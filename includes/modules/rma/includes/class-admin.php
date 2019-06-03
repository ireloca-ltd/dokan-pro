<?php

/**
* Admin class
*/
class Dokan_RMA_Admin {

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_filter( 'dokan_settings_sections', array( $this, 'load_settings_section' ), 20 );
        add_filter( 'dokan_settings_fields', array( $this, 'load_settings_fields' ), 20 );
    }

    /**
     * Load admin settings section
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_settings_section( $section ) {
        $section[] = array(
            'id'    => 'dokan_rma',
            'title' => __( 'RMA', 'dokan' ),
            'icon'  => 'dashicons-image-rotate'
        );

        return $section;
    }

    /**
     * Load all settings fields
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_settings_fields( $fields ) {
        $fields['dokan_rma'] = array(
            'rma_order_status' => array(
                'name'    => 'rma_order_status',
                'label'   => __( 'Order Status', 'dokan' ),
                'type'    => 'select',
                'desc'  => __( 'Which order status you want to give return and warranty facility', 'dokan' ),
                'default' => 'seller',
                'options' => wc_get_order_statuses(),
            ),
            'rma_enable_refund_request' => array(
                'name'    => 'rma_enable_refund_request',
                'label'   => __( 'Enable Refund Requests', 'dokan' ),
                'type'    => 'select',
                'desc'    => __( 'Allow customers to request for refunds', 'dokan' ),
                'default' => 'no',
                'options' => [
                    'yes' => __( 'Yes', 'dokan' ),
                    'no' => __( 'No', 'dokan' )
                ],
            ),
            'rma_enable_coupon_request' => array(
                'name'    => 'rma_enable_coupon_request',
                'label'   => __( 'Enable Coupon Requests', 'dokan' ),
                'type'    => 'select',
                'desc'    => __( 'Allow customers to request for coupons as store credit', 'dokan' ),
                'default' => 'no',
                'options' => [
                    'yes' => __( 'Yes', 'dokan' ),
                    'no' => __( 'No', 'dokan' )
                ],
            ),

            'rma_reasons' => array(
                'name'    => 'rma_reasons',
                'label'   => __( 'Reasons for RMA', 'dokan' ),
                'type'    => 'repeatable',
                'desc'  => __( 'Add your reasons whatever you want', 'dokan' )
            ),

            'rma_policy' => array(
                'name'    => 'rma_policy',
                'label'   => __( 'Refund Policy', 'dokan' ),
                'type'    => 'wpeditor',
                'desc'  => __( 'Refund policy for all stores. Vendor can overwride this policy', 'dokan' )
            )
        );

        return $fields;
    }
}
