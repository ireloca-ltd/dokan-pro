<?php

class Dokan_Follow_Store_Ajax {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'wp_ajax_dokan_follow_store_toggle_status', array( $this, 'toggle_follow_status' ) );
        add_filter( 'wp_ajax_dokan_follow_store_get_current_status', array( $this, 'get_current_status' ) );
    }

    /**
     * Toggle follow store status
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function toggle_follow_status() {
        if ( empty( $_POST ) || ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'dokan_follow_store' ) ) {
            wp_send_json_error( new WP_Error( 'invalid_nonce', __( 'Nonce is invalid', 'dokan' ) ), 403 );
        }

        if ( empty( $_POST['vendor_id'] ) ) {
            wp_send_json_error( new WP_Error( 'missing_required_field', __( 'vendor_id field is required', 'dokan' ) ), 422 );
        }

        $customer_id = get_current_user_id();
        $vendor      = dokan()->vendor->get( $_POST['vendor_id'] );

        if ( ! $vendor->id ) {
            wp_send_json_error( new WP_Error( 'invalid_vendor', __( 'Invalid vendor_id', 'dokan' ) ), 422 );
        }

        $status = dokan_follow_store_toggle_status( $vendor->id, $customer_id );

        if ( is_wp_error( $status ) ) {
            wp_send_json_error( $status, 422 );
        }

        wp_send_json_success( array( 'status' => $status ), 200 );
    }

    /**
     * Get current follow status
     *
     * @since 2.9.7
     *
     * @return void
     */
    public function get_current_status() {
        if ( empty( $_GET['vendor_id'] ) ) {
            wp_send_json_error( array( 'message' => __( 'vendor_id is required.' ) ), 400 );
        }

        $vendor_id   = absint( $_GET['vendor_id'] );
        $customer_id = get_current_user_id();

        if ( ! $customer_id ) {
            wp_send_json_error( array( 'message' => __( 'You have to logged in to get your status.' ) ), 400 );
        }

        $is_following = dokan_follow_store_is_following_store( $vendor_id, $customer_id );

        wp_send_json_success( array(
            'is_following' => $is_following,
            'nonce'        => wp_create_nonce( 'dokan_follow_store' ),
        ) );
    }
}
