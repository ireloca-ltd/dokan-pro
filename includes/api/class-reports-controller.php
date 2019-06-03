<?php

/**
 * REST API Reports controller
 *
 * Handles requests to the /reports endpoint.
 *
 * @author   Dokan
 * @category API
 * @package  Dokan/API
 * @since    2.8
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Dokan_REST_Reports_Controller extends WP_REST_Controller {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'dokan/v1';

    /**
     * Route name
     *
     * @var string
     */
    protected $base = 'reports';

    /**
     * Register all routes related with reports
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->base . '/sales_overview', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_sales_overview' ),
                'permission_callback' => array( $this, 'check_sales_overview_permission' ),
                'args'                => $this->get_collection_params()
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/top_selling', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_top_selling' ),
                'permission_callback' => array( $this, 'check_top_selling_permission' ),
                'args'                => $this->get_collection_params()
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/top_earners', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_top_earners' ),
                'permission_callback' => array( $this, 'check_top_earners_permission' ),
                'args'                => $this->get_collection_params()
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/top_earners', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_top_earners' ),
                'permission_callback' => array( $this, 'check_top_earners_permission' ),
                'args'                => $this->get_collection_params()
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/summary', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_report_summary' ),
                'permission_callback' => array( $this, 'check_report_summary_permission' ),
                'args'                => $this->get_collection_params()
            ),
        ) );
    }

    /**
     * Check permission to view this report
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function check_sales_overview_permission() {
        return current_user_can( 'dokan_view_overview_report' );
    }

    /**
     * Check permission to view this top_selling
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function check_top_selling_permission() {
        return current_user_can( 'dokan_view_top_selling_report' );
    }

    /**
     * Check permission to view this top_selling
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function check_top_earners_permission() {
        return current_user_can( 'dokan_view_top_earning_report' );
    }

    /**
     * Check permission to view this top_selling
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function check_report_summary_permission() {
        return current_user_can( 'dokan_view_sales_overview' );
    }

    /**
     * Get reports summary
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_report_summary( $request ) {
        $seller_id = dokan_get_current_user_id();

        $data = array(
            'pageviews'      => (int) dokan_author_pageviews( $seller_id ),
            'orders_count'   => dokan_count_orders( $seller_id ),
            'sales'          => dokan_author_total_sales( $seller_id ),
            'seller_balance' => dokan_get_seller_earnings( $seller_id )
        );

        return rest_ensure_response( $data );
    }

    /**
     * Get report data for Sales Overview
     *
     * @param type $request
     *
     * @return array
     */
    public function get_sales_overview( $request ) {
        $seller_id  = dokan_get_current_user_id();
        $start_date = $request['start_date'];
        $end_date   = $request['end_date'];

        $order_totals = dokan_get_order_report_data( array(
            'data'         => array(
                '_order_total'    => array(
                    'type'     => 'meta',
                    'function' => 'SUM',
                    'name'     => 'total_sales'
                ),
                '_order_shipping' => array(
                    'type'     => 'meta',
                    'function' => 'SUM',
                    'name'     => 'total_shipping'
                ),
                'ID'              => array(
                    'type'     => 'post_data',
                    'function' => 'COUNT',
                    'name'     => 'total_orders'
                )
            ),
            'filter_range' => true,
        ), $start_date, $end_date, $seller_id );

        $total_items = absint( dokan_get_order_report_data( array(
            'data'         => array(
                '_qty' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => 'SUM',
                    'name'            => 'order_item_qty'
                )
            ),
            'query_type'   => 'get_var',
            'filter_range' => true
        ), $start_date, $end_date, $seller_id ) );

        // Get discount amounts in range
        $total_coupons = dokan_get_order_report_data( array(
            'data'         => array(
                'discount_amount' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'coupon',
                    'function'        => 'SUM',
                    'name'            => 'discount_amount'
                )
            ),
            'where'        => array(
                array(
                    'key'      => 'order_item_type',
                    'value'    => 'coupon',
                    'operator' => '='
                )
            ),
            'query_type'   => 'get_var',
            'filter_range' => true
        ), $start_date, $end_date, $seller_id );

        $average_sales = $order_totals->total_sales / ( 30 + 1 );

        $data = array(
            'total_sales'    => $order_totals->total_sales,
            'total_shipping' => $order_totals->total_shipping,
            'total_orders'   => absint( $order_totals->total_orders ),
            'total_items'    => $total_items,
            'total_coupons'  => $total_coupons,
            'average_sales'  => $average_sales,
        );

        return $data;
    }

    /**
     * Get report data for Top Selling products
     *
     * @param type $request
     *
     * @return array
     */
    public function get_top_selling( $request ) {

        global $wpdb;
        $seller_id  = dokan_get_current_user_id();
        $start_date = $request['start_date'];
        $end_date   = $request['end_date'];

        $start_date = strtotime( $start_date );
        $end_date   = strtotime( $end_date );

        // Get order ids and dates in range
        $order_items = apply_filters( 'woocommerce_reports_top_sellers_order_items', $wpdb->get_results( "
            SELECT order_item_meta_2.meta_value as product_id, SUM( order_item_meta.meta_value ) as item_quantity FROM {$wpdb->prefix}woocommerce_order_items as order_items

            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
            LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
            LEFT JOIN {$wpdb->prefix}dokan_orders AS do ON posts.ID = do.order_id

            WHERE   posts.post_type     = 'shop_order'
            AND     posts.post_status   != 'trash'
            AND     do.seller_id = {$seller_id}
            AND     do.order_status IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) . "')
            AND     post_date > '" . date( 'Y-m-d', $start_date ) . "'
            AND     post_date < '" . date( 'Y-m-d', strtotime( '+1 day', $end_date ) ) . "'
            AND     order_items.order_item_type = 'line_item'
            AND     order_item_meta.meta_key = '_qty'
            AND     order_item_meta_2.meta_key = '_product_id'
            GROUP BY order_item_meta_2.meta_value
        " ), $start_date, $end_date );

        $found_products = array();

        if ( $order_items ) {
            foreach ( $order_items as $order_item ) {
                $found_products[$order_item->product_id] = $order_item->item_quantity;
            }
        }

        asort( $found_products );
        $found_products = array_reverse( $found_products, true );
        $found_products = array_slice( $found_products, 0, 25, true );
        reset( $found_products );

        $data = array();

        foreach ( $found_products as $product_id => $sales ) {
            $product = wc_get_product( $product_id );

            $data[] = array(
                'id'       => $product->get_id(),
                'title'    => $product->get_title(),
                'url'      => $product->get_permalink(),
                'edit_url' => dokan_edit_product_url( $product_id ),
                'sold_qty' => $sales,
            );
        }

        return rest_ensure_response( $data );
    }

    /**
     * Get report data for Top Earning products
     *
     * @param type $request
     *
     * @return array
     */
    public function get_top_earners( $request ) {
        global $wpdb;

        $seller_id  = dokan_get_current_user_id();
        $start_date = $request['start_date'];
        $end_date   = $request['end_date'];

        $start_date = strtotime( $start_date );
        $end_date   = strtotime( $end_date );

        // Get order ids and dates in range
        $order_items = apply_filters( 'woocommerce_reports_top_earners_order_items', $wpdb->get_results( "
            SELECT order_item_meta_2.meta_value as product_id, SUM( order_item_meta.meta_value ) as line_total,SUM( do.net_amount ) as total_earning FROM {$wpdb->prefix}woocommerce_order_items as order_items

            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
            LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
            LEFT JOIN {$wpdb->prefix}dokan_orders AS do ON posts.ID = do.order_id

            WHERE   posts.post_type     = 'shop_order'
            AND     posts.post_status   != 'trash'
            AND     do.seller_id = {$seller_id}
            AND     do.order_status           IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) . "')
            AND     post_date > '" . date( 'Y-m-d', $start_date ) . "'
            AND     post_date < '" . date( 'Y-m-d', strtotime( '+1 day', $end_date ) ) . "'
            AND     order_items.order_item_type = 'line_item'
            AND     order_item_meta.meta_key = '_line_total'
            AND     order_item_meta_2.meta_key = '_product_id'
            GROUP BY order_item_meta_2.meta_value
        " ), $start_date, $end_date );

            $found_products = array();
            $total_earnings = array();
            if ( $order_items ) {
                foreach ( $order_items as $order_item ) {
                    $found_products[$order_item->product_id] = $order_item->line_total;
                    $total_earnings[$order_item->product_id] = $order_item->total_earning;
                }
            }

            asort( $found_products );
            $found_products = array_reverse( $found_products, true );
            $found_products = array_slice( $found_products, 0, 25, true );
            reset( $found_products );

            $data = array();
            foreach ( $found_products as $product_id => $sales ) {
                $product = wc_get_product( $product_id );

                $data[] = array(
                    'id'       => $product->get_id(),
                    'title'    => $product->get_title(),
                    'url'      => $product->get_permalink(),
                    'edit_url' => dokan_edit_product_url( $product_id ),
                    'sales'    => $sales,
                );
            }

            return $data;
    }

    /**
     * Get collection params
     *
     * @return array
     */
    public function get_collection_params() {
        return array(
            'start_date' => array(
                'default'           => date( 'Y-m-01', current_time( 'timestamp' ) ),
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'end_date'   => array(
                'default'           => date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) ),
                'sanitize_callback' => 'sanitize_text_field',
            )
        );
    }
}
