<?php

/**
* Admin class
*
* @package Dokan Pro
*/
class Dokan_SPMV_Admin {

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        // settings section
        add_filter( 'dokan_settings_sections', array( $this, 'add_new_section_admin_panel' ) );
        add_filter( 'dokan_settings_fields', array( $this, 'add_new_setting_field_admin_panel' ), 12, 1 );
    }

    /**
     * Add new Section in admin dokan settings
     *
     * @param array  $sections
     *
     * @return array
     */
    function add_new_section_admin_panel( $sections ) {
        $sections['dokan_spmv'] = array(
            'id'    => 'dokan_spmv',
            'title' => __( 'Single Product MultiVendor', 'dokan' ),
            'icon'  => 'dashicons-store'
        );

        return $sections;
    }

    /**
     * Add new Settings field in admin settings area
     *
     * @param array  $settings_fields
     *
     * @return array
     */
    function add_new_setting_field_admin_panel( $settings_fields ) {

        $settings_fields['dokan_spmv'] = array(
            'enable_pricing' => array(
                'name'  => 'enable_pricing',
                'label' => __( 'Enable Single Product Multiple Vendor', 'dokan' ),
                'desc'  => __( 'Enable Single Product Multiple Vendor functionality', 'dokan' ),
                'type'  => 'checkbox'
            ),

            'sell_item_btn' => array(
                'name'    => 'sell_item_btn',
                'label'   => __( 'Sell Item Button Text', 'dokan' ),
                'desc'    => __( 'Change your sell this item button text', 'dokan' ),
                'type'    => 'text',
                'default' => __( 'Sell This Item', 'dokan' ),
            ),

            'available_vendor_list_title' => array(
                'name'    => 'available_vendor_list_title',
                'label'   => __( 'Available Vendor Display area title', 'dokan' ),
                'desc'    => __( 'Set your heading for available vendor section in single product page', 'dokan' ),
                'type'    => 'text',
                'default' => __( 'Other Available Vendor', 'dokan' ),
            ),

            'available_vendor_list_position' => array(
                'name'    => 'available_vendor_list_position',
                'label'   => __( 'Available Vendor Section Display Position', 'dokan' ),
                'desc'    => __( 'Set your displaying position for diplaying available vendor section in single product page', 'dokan' ),
                'type'    => 'select',
                'options' => array(
                    'below_tabs'  => __( 'Above Single Product Tabs', 'dokan' ),
                    'inside_tabs' => __( 'Display inside Product Tabs', 'dokan' ),
                    'after_tabs'  => __( 'After Single Product Tabs', 'dokan' ),
                ),
                'default' => 'below_tabs',
            ),
        );

        return $settings_fields;
    }

}
