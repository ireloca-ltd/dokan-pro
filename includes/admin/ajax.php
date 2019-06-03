<?php

/**
 * Ajax handling for Dokan in Admin area
 *
 * @since 2.2
 *
 * @author weDevs <info@wedevs.com>
 */
class Dokan_Pro_Admin_Ajax {

    /**
     * Load automatically all actions
     */
    function __construct() {
        add_action( 'wp_ajax_regen_sync_table', array( $this, 'regen_sync_order_table' ) );
        add_action( 'wp_ajax_check_duplicate_suborders', array( $this, 'check_duplicate_suborders' ) );
        add_action( 'wp_ajax_print_duplicate_suborders', array( $this, 'print_duplicate_suborders' ) );
        add_action( 'wp_ajax_dokan_duplicate_order_delete', array( $this, 'dokan_duplicate_order_delete' ) );
        add_action( 'wp_ajax_dokan_duplicate_orders_bulk_delete', array( $this, 'dokan_duplicate_orders_bulk_delete' ) );
        add_action( 'wp_ajax_dokan_refund_form_action', array( $this, 'handle_refund_action' ) );
        add_action( 'wp_ajax_dokan-toggle-module', array( $this, 'toggle_module' ), 10 );
    }

    /**
     * Initializes the Dokan_Template_Withdraw class
     *
     * Checks for an existing Dokan_Template_Withdraw instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Pro_Admin_Ajax();
        }

        return $instance;
    }

    /**
     * Handle sync order table via ajax
     *
     * @return json success|error|data
     */
    function regen_sync_order_table() {

        if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'regen_sync_table' ) {
            return wp_send_json_error( __( 'You don\'t have enough permission', 'dokan', '403' ) );
        }

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return wp_send_json_error( __( 'You don\'t have enough permission', 'dokan', '403' ) );
        }

        global $wpdb;

        $limit        = isset( $_POST['limit'] ) ? $_POST['limit'] : 0;
        $offset       = isset( $_POST['offset'] ) ? $_POST['offset'] : 0;
        $total_orders = isset( $_POST['total_orders'] ) ? $_POST['total_orders'] : 0;

        if ( $offset == 0 ) {
            $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->dokan_orders );

            $total_orders = $wpdb->get_var( "SELECT count(ID)
                FROM $wpdb->posts
                WHERE post_type = 'shop_order'" );

            $parent_orders = $wpdb->get_var( "SELECT count(ID)
                FROM {$wpdb->posts} as p
                LEFT JOIN {$wpdb->postmeta} as m ON p.ID = m.post_id
                WHERE m.meta_key = 'has_sub_order' and p.post_type = 'shop_order' " );
            $total_orders = $total_orders - $parent_orders;
        }

        $sql = "SELECT ID FROM $wpdb->posts
                WHERE post_type = 'shop_order'
                LIMIT %d,%d";

        $orders = $wpdb->get_results( $wpdb->prepare($sql, $offset * $limit, $limit ) );

        if ( $orders ) {
            foreach ( $orders as $order) {
                dokan_sync_order_table( $order->ID );
            }

            $sql       = "SELECT * FROM " . $wpdb->dokan_orders;
            $generated = $wpdb->get_results( $sql );
            $done      = count( $generated );

            wp_send_json_success( array(
                'offset'       => $offset + 1,
                'total_orders' => $total_orders,
                'done'         => $done,
                'message'      => sprintf( __( '%d orders sync completed out of %d', 'dokan' ), $done, $total_orders )
            ) );
        } else {
            $dashboard_link = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=dokan' ), __( 'Go to Dashboard &rarr;', 'dokan' ) );
            wp_send_json_success( array(
                'offset'  => 0,
                'done'    => 'All',
                'message' => sprintf( __( 'All orders has been synchronized. %s', 'dokan' ), $dashboard_link )
            ) );
        }
    }

    /**
     * Remove duplicate sub-orders if found
     *
     * @since 2.4.4
     *
     * @return json success|error|data
     */
    function check_duplicate_suborders(){

        if ( ! isset( $_POST['action'] ) || $_POST['action'] !== 'check_duplicate_suborders' ) {
            return wp_send_json_error( __( 'You don\'t have enough permission', 'dokan', '403' ) );
        }

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return wp_send_json_error( __( 'You don\'t have enough permission', 'dokan', '403' ) );
        }

        if ( session_id() == '' ) {
            session_start();
        }

        global $wpdb;

        $limit        = isset( $_POST['limit'] ) ? $_POST['limit'] : 0;
        $offset       = isset( $_POST['offset'] ) ? $_POST['offset'] : 0;
        $prev_done    = isset( $_POST['done'] ) ? $_POST['done'] : 0;
        $total_orders = isset( $_POST['total_orders'] ) ? $_POST['total_orders'] : 0;

        if ( $offset == 0 ) {
            unset( $_SESSION['dokan_duplicate_order_ids'] );
            $total_orders = $wpdb->get_var( "SELECT count(ID) FROM $wpdb->posts AS p
                LEFT JOIN $wpdb->postmeta AS m ON p.ID = m.post_id
                WHERE post_type = 'shop_order' AND m.meta_key = 'has_sub_order'" );
        }

        $sql = "SELECT ID FROM $wpdb->posts AS p
        LEFT JOIN $wpdb->postmeta AS m ON p.ID = m.post_id
        WHERE post_type = 'shop_order' AND m.meta_key = 'has_sub_order'
        LIMIT %d,%d";

        $orders           = $wpdb->get_results( $wpdb->prepare( $sql, $offset * $limit, $limit ) );
        $duplicate_orders = isset( $_SESSION['dokan_duplicate_order_ids'] ) ? $_SESSION['dokan_duplicate_order_ids'] : array();

        if ( $orders ) {
            foreach ( $orders as $order ) {

                $sellers_count = count( dokan_get_sellers_by( $order->ID ) );
                $sub_order_ids = dokan_get_suborder_ids_by( $order->ID );

                if ( $sellers_count < count( $sub_order_ids ) ) {
                    $duplicate_orders = array_merge( array_slice( $sub_order_ids, $sellers_count ), $duplicate_orders );
                }
            }

            if ( count( $duplicate_orders ) ) {
                $_SESSION['dokan_duplicate_order_ids'] = $duplicate_orders;
            }

            $done = $prev_done + count($orders);

            wp_send_json_success( array(
                'offset'       => $offset + 1,
                'total_orders' => $total_orders,
                'done'         => $done,
                'message'      => sprintf( __( '%d orders checked out of %d', 'dokan' ), $done, $total_orders )
            ) );

        } else {

            if( count( $duplicate_orders ) ) {
               wp_send_json_success( array(
                    'offset'  => 0,
                    'done'    => 'All',
                    'message' => sprintf( __( 'All orders are checked and we found some duplicate orders', 'dokan' ) ),
                    'duplicate' => true
                ) );
            }

            $dashboard_link = sprintf( '<a href="%s">%s</a>', admin_url( 'admin.php?page=dokan' ), __( 'Go to Dashboard &rarr;', 'dokan' ) );

            wp_send_json_success( array(
                    'offset'  => 0,
                    'done'    => 'All',
                    'message' => sprintf( __( 'All orders are checked and no duplicate was found. %s', 'dokan' ), $dashboard_link )
            ) );
        }
    }

    /**
     * Print Duplicate Suborder table
     *
     * @since 2.4.4
     *
     * @return json success|error|data
     *
     */
    function print_duplicate_suborders() {
        if(session_id() == ''){
            session_start();
        }
        $duplicate_orders = isset( $_SESSION['dokan_duplicate_order_ids'] ) ? $_SESSION['dokan_duplicate_order_ids'] : array();

        ob_start();

        require_once DOKAN_PRO_INC.'/admin/duplicate-order-list.php';

        $html = ob_get_clean();

        wp_send_json_success( array(
            'html'  => $html,
        ) );
    }

    /**
     * Delete Duplicate orders
     *
     * @since 2.4.4
     *
     * @return json success|error|data
     */
    function dokan_duplicate_order_delete() {

        parse_str( $_POST['formData'], $data );
        if ( ! wp_verify_nonce( $data['dokan_duplicate_orders_bulk_action_nonce'], 'dokan_duplicate_orders_bulk_action' ) ) {
            wp_send_json_error();
        }

        $duplicate_order_id = (int) $_POST['order_id'];

        if ( !$duplicate_order_id ) {
            wp_send_json_error();
        }

        if ( wp_delete_post( $duplicate_order_id ) ) {
            wp_send_json_success( array(
                'status' => 'deleted',
            ) );
        }
    }

    /**
     * Delete orders in Bulk
     *
     * @since 2.4.4
     *
     * @return json success|error|data
     */
    function dokan_duplicate_orders_bulk_delete() {

        parse_str( $_POST['formData'], $data );
        if ( !wp_verify_nonce( $data['dokan_duplicate_orders_bulk_action_nonce'], 'dokan_duplicate_orders_bulk_action' ) ) {
            wp_send_json_error();
        }

        if ( isset( $data['id'] ) ) {
            foreach ( $data['id'] as $order_id ) {
                wp_delete_post( $order_id );
                $deleted_orders[] = (int) $order_id;
            }
            wp_send_json_success( array(
                'status'  => 1,
                'deleted' => json_encode($deleted_orders),
                'msg'     => 'Selected Orders Deleted Successfully'
            ) );
        } else {
            wp_send_json_success( array(
                'status' => 0,
                'msg'    => 'Select Orders to Delete'
            ) );
        }
    }

    /**
     *  Handle refund action via ajax
     *
     * @since 2.4.11
     *
     *  @return json success|error|data
     */
    function handle_refund_action() {

        parse_str( $_POST['formData'], $postdata );

        if( !wp_verify_nonce( $postdata['dokan_refund_admin_bulk_action_nonce'], 'dokan_refund_admin_bulk_action' ) ) {
            wp_send_json_error();
        }

        $refund = Dokan_Pro_Refund::init();

        $bulk_action = $_POST['status'];
        $status      = $postdata['status_page'];
        $refund_id = $_POST['refund_id'];

        switch ( $bulk_action ) {

            case 'delete':

                $order_id       = $postdata['order_id'][$refund_id];
                $seller_id      = $postdata['seller_id'][$refund_id];
                $refund_amount  = $postdata['refund_amount'][$refund_id];
                $refund_reason  = $postdata['refund_reason'][$refund_id];
                $seller = get_userdata( $seller_id );
                $seller_mail = $seller->user_email;

                do_action( 'dokan_refund_processed_notification' , $seller_mail, $order_id, 'deleted', $refund_amount, $refund_reason );

                $refund->delete_refund( $refund_id );
                $refund_processing_id = get_post_meta( $order_id, 'dokan_refund_processing_id', true );
                $url = admin_url( 'admin.php?page=dokan-refund&message=trashed&status=' . $status );
                wp_send_json_success( array( 'url'=> $url, 'refund_processing_id' => $refund_processing_id ) );

                break;

            case 'cancel':

                $order_id       = $postdata['order_id'][$refund_id];
                $seller_id      = $postdata['seller_id'][$refund_id];
                $refund_amount  = $postdata['refund_amount'][$refund_id];
                $refund_reason  = $postdata['refund_reason'][$refund_id];
                $seller = get_userdata( $seller_id );
                $seller_mail = $seller->user_email;
                do_action( 'dokan_refund_processed_notification' , $seller_mail, $order_id, 'canceled', $refund_amount, $refund_reason );
                //Dokan_Email::init()->dokan_refund_seller_mail( $seller_mail, $order_id, 'canceled', $refund_amount, $refund_reason );
                $refund->update_status( $refund_id, $order_id, 2 );
                delete_post_meta( $order_id, 'dokan_refund_processing_id' );
                $url = admin_url( 'admin.php?page=dokan-refund&message=cancelled&status=' . $status );
                wp_send_json_success( array( 'url'=> $url ) );

                break;

            case 'approve':

                $data['order_id']               = $postdata['order_id'][$refund_id];
                $seller_id                      = $postdata['seller_id'][$refund_id];
                $data['refund_amount']          = $postdata['refund_amount'][$refund_id];
                $data['refund_reason']          = $postdata['refund_reason'][$refund_id];
                $data['line_item_qtys']         = $postdata['item_qtys'][$refund_id];
                $data['line_item_totals']       = $postdata['item_totals'][$refund_id];
                $data['line_item_tax_totals']   = $postdata['item_tax_totals'][$refund_id];
                $data['restock_refunded_items'] = $postdata['restock_items'][$refund_id];

                $seller = get_userdata( $seller_id );
                $seller_mail = $seller->user_email;
                do_action( 'dokan_refund_processed_notification' , $seller_mail, $data['order_id'], 'approved', $data['refund_amount'], $data['refund_reason'] );
//                Dokan_Email::init()->dokan_refund_seller_mail( $seller_mail, $data['order_id'], 'approved', $data['refund_amount'], $data['refund_reason'] );
                $refund->update_status( $refund_id, $data['order_id'], 1 );
                delete_post_meta( $data['order_id'], 'dokan_refund_processing_id' );
                $url = admin_url( 'admin.php?page=dokan-refund&message=approved&status=' . $status );
                wp_send_json_success( array( 'url'=> $url, 'data' => $data ) );

                break;

            case 'pending':

                $refund->update_status( $refund_id, $postdata['order_id'][$refund_id], 0 );

                $url = admin_url( 'admin.php?page=dokan-refund&message=pending&status=' . $status );
                wp_send_json_success( array( 'url'=> $url ) );

                break;
        }
    }

    /**
    * Toggle module
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function toggle_module() {
        if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'], 'dokan-admin-nonce' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $module = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : '';
        $type   = isset( $_POST['type'] ) ? $_POST['type'] : '';

        if ( ! $module ) {
            wp_send_json_error( __( 'Invalid module provided', 'dokan' ) );
        }

        if ( ! in_array( $type, array( 'activate', 'deactivate' ) ) ) {
            wp_send_json_error( __( 'Invalid request type', 'dokan' ) );
        }

        $module_data = dokan_pro_get_module( $module );

        if ( 'activate' == $type ) {
            $status = dokan_pro_activate_module( $module );

            if ( is_wp_error( $status ) ) {
                wp_send_json_error( array(
                    'error' => $status->get_error_code(),
                    'message' => $status->get_error_message()
                ) );
            }

            $message = __( 'Activated', 'dokan' );
        } else {
            dokan_pro_deactivate_module( $module );
            $message = __( 'Deactivated', 'dokan' );
        }

        wp_send_json_success( $message );
    }

}
