<?php

/**
* Store API Controller
*
* @package dokan
*
* @author weDevs <info@wedevs.com>
*/
class Dokan_Pro_REST_Store_Controller extends Dokan_REST_Store_Controller {

    /**
     * Register all routes releated with stores
     *
     * @return void
     */
    public function register_routes() {

        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/status', array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                    'required'    => true
                ),
                'status' => array(
                    'description' => __( 'Status for the store object.' ),
                    'type'        => 'string',
                    'required'    => true
                ),
            ),
            array(
                'methods'  => WP_REST_Server::EDITABLE,
                'callback' => array( $this, 'update_vendor_status' ),
                'permission_callback' => array( $this, 'permission_check_for_manageable_part' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/batch', array(
            array(
                'methods'  => WP_REST_Server::EDITABLE,
                'callback' => array( $this, 'batch_update' ),
                'permission_callback' => array( $this, 'permission_check_for_manageable_part' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/stats' , array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_store_stats' ),
                'args'                => $this->get_collection_params(),
                'permission_callback' => array( $this, 'permission_check_for_stats' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/email' , array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                ),
                'subject' => array(
                    'description' => __( 'Subject of the email.' ),
                    'type'        => 'string',
                    'required'    => true,
                ),
                'body' => array(
                    'description' => __( 'Body of the email.' ),
                    'type'        => 'string',
                    'required'    => true,
                ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'send_email' ),
                'args'                => $this->get_collection_params(),
                'permission_callback' => array( $this, 'permission_check_for_manageable_part' ),
            ),
        ) );
    }

    /**
     * update_vendor_status
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update_vendor_status( $request ) {
        if ( ! in_array( $request['status'], array( 'active', 'inactive' ) ) ) {
            return new WP_Error( 'no_valid_status', __( 'Status parameter must be active or inactive' ), array( 'status' => 400 ) );
        }

        $store_id = ! empty( $request['id'] ) ? $request['id'] : 0;

        if ( empty( $store_id ) ) {
            return new WP_Error( 'no_vendor_found', __( 'No vendor found for updating status' ), array( 'status' => 400 ) );
        }

        if ( 'active' == $request['status'] ) {
            $user = dokan()->vendor->get( $store_id )->make_active();
        } else {
            $user = dokan()->vendor->get( $store_id )->make_inactive();
        }

        $response = rest_ensure_response( $user );
        $response->add_links( $this->prepare_links( $user, $request ) );
        return $response;
    }

    /**
     * Batch udpate for vendor listing
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function batch_update( $request ) {
        $params = $request->get_params();

        if ( empty( $params ) ) {
            return new WP_Error( 'no_item_found', __( 'No items found for bulk updating', 'dokan' ), array( 'status' => 404 ) );
        }

        $allowed_status = array( 'approved', 'pending', 'delete' );

        $response = array();

        foreach ( $params as $status => $value ) {
            if ( in_array( $status, $allowed_status ) ) {

                switch ( $status ) {
                    case 'approved':

                        foreach ( $value as $vendor_id ) {
                            $response['approved'][] = dokan()->vendor->get( $vendor_id )->make_active();
                        }
                        break;

                    case 'pending':

                        foreach ( $value as $vendor_id ) {
                            $response['pending'][] = dokan()->vendor->get( $vendor_id )->make_inactive();
                        }
                        break;

                    case 'delete':

                        foreach ( $value as $vendor_id ) {
                            $user = dokan()->vendor->get( $vendor_id )->delete();
                            $response['delete'][] = $user;
                        }
                        break;
                }
            }
        }

        return $response;
    }

    /**
     * undocumented function
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function permission_check_for_manageable_part() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Get stats for the vendor
     *
     * @param  WP_REST_Request $request
     *
     * @return boolean
     */
    public function permission_check_for_stats( $request ) {
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        if ( $request['id'] == dokan_get_current_user_id() ) {
            return true;
        }

        return false;
    }

    /**
     * Fetch stats for the store
     *
     * @param  WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function get_store_stats( $request ) {
        $store_id    = (int) $request['id'];
        $vendor      = dokan()->vendor->get( $store_id );

        $products    = dokan_count_posts( 'product', $store_id );
        $orders      = dokan_count_orders( $store_id );
        $reviews     = dokan_count_comments( 'product', $store_id );
        $total_items = absint( dokan_get_order_report_data( array(
            'data' => array(
                '_qty' => array(
                    'type'            => 'order_item_meta',
                    'order_item_type' => 'line_item',
                    'function'        => 'SUM',
                    'name'            => 'order_item_qty'
                )
            ),
            'query_type'   => 'get_var',
            'filter_range' => false
        ), false, false, $store_id ) );

        $response = array(
            'products' => array(
                'total'   => $products->publish,
                'sold'    => $total_items,
                'visitor' => $vendor->get_product_views()
            ),
            'revenue'  => array(
                'orders'  => $orders->{'wc-processing'} + $orders->{'wc-completed'},
                'sales'   => $vendor->get_total_sales(),
                'earning' => dokan_get_seller_earnings( $store_id, false )
            ),
            'others'   => array(
                'commission_rate' => $vendor->get_percentage(),
                'commission_type' => dokan_get_commission_type( $store_id ),
                'balance'         => $vendor->get_balance(false),
                'reviews'         => $reviews->{'approved'}
            )
        );

        return rest_ensure_response( $response );
    }

    /**
     * Send email to the vendor
     *
     * @param  WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function send_email( $request ) {
        $response  = array( 'success' => true );
        $vendor_id = $request['id'];
        $vendor    = dokan()->vendor->get( $vendor_id );

        $subject   = $request['subject'];
        $body      = $request['body'];

        $response['success'] = wp_mail( $vendor->get_email(), $subject, $body );

        return rest_ensure_response( $response );
    }
}
