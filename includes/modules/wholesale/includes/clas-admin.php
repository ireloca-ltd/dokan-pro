<?php

/**
* Admin class
*/
class Dokan_Wholesale_Admin {

    /**
     * Load automatically when class initiate
     *
     * @since 2.9.5
     */
    public function __construct() {
        add_filter( 'dokan_settings_sections', [ $this, 'load_settings_section' ], 20 );
        add_filter( 'dokan_settings_fields', [ $this, 'load_settings_fields' ], 20 );

        add_action( 'dokan-vue-admin-scripts', [ $this, 'admin_enqueue_scripts' ] );
        add_action( 'dokan_admin_menu', [ $this, 'add_submenu' ], 17 );
        add_filter( 'dokan-admin-routes', [ $this, 'admin_routes' ] );
    }

    /**
     * Load admin settings section
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function load_settings_section( $section ) {
        $section[] = array(
            'id'    => 'dokan_wholesale',
            'title' => __( 'Wholesale', 'dokan' ),
            'icon'  => 'dashicons-admin-multisite'
        );

        return $section;
    }

    /**
     * Load all settings fields
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function load_settings_fields( $fields ) {
        $fields['dokan_wholesale'] = array(
            'wholesale_price_display' => array(
                'name'    => 'wholesale_price_display',
                'label'   => __( 'Who can see wholesale price', 'dokan' ),
                'type'    => 'radio',
                'desc'    => __( 'Who can actually see the wholesale price in product page', 'dokan' ),
                'default' => 'wholesale_customer',
                'options' => [
                    'all_user'           => __( 'Display wholesale price to all users' ),
                    'wholesale_customer' => __( 'Display wholesale price to Wholesale customer only' ),
                ]
            ),

            'display_price_in_shop_archieve' => array(
                'name'    => 'display_price_in_shop_archieve',
                'label'   => __( 'Show wholesale price on shop archive', 'dokan' ),
                'type'    => 'checkbox',
                'desc'    => __( 'Show in price column', 'dokan' ),
                'default' => 'no',
            ),

            'need_approval_for_wholesale_customer' => array(
                'name'    => 'need_approval_for_wholesale_customer',
                'label'   => __( 'Need approval for customer', 'dokan' ),
                'type'    => 'select',
                'desc'    => __( 'Customer need admin approval for becoming a wholesale customer', 'dokan' ),
                'default' => 'yes',
                'options' => [
                    'yes' => __( 'Yes', 'dokan' ),
                    'no'  => __( 'No', 'dokan' )
                ]
            ),
        );

        return $fields;
    }

    /**
     * Enqueue vue component js
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_style( 'dokan-wholesale-admin-style', DOKAN_WHOLESALE_ASSETS_DIR . '/css/admin.css', false, DOKAN_PLUGIN_VERSION, 'all' );
        wp_enqueue_script( 'dokan-wholesale-admin', DOKAN_WHOLESALE_ASSETS_DIR . '/js/admin.js', array(  'jquery', 'dokan-vue-vendor', 'dokan-vue-bootstrap' ), false, true );
    }

    /**
     * Add submenu page in dokan Dashboard
     *
     * @since 2.9.5
     *
     * @return void
     */
    function add_submenu( $capability ) {
        global $submenu;
        $slug = 'dokan';

        if ( current_user_can( 'manage_options' ) ) {
            $submenu[ $slug ][] = array( __( 'Wholesale Customer', 'dokan' ), $capability, 'admin.php?page=' . $slug . '#/wholesale-customer' );
        }
    }

    /**
     * Add subscripton route
     *
     * @param  array $routes
     *
     * @return array
     */
    public function admin_routes( $routes ) {
        $routes[] = [
            'path'      => '/wholesale-customer',
            'name'      => 'WholesaleCustomer',
            'component' => 'WholesaleCustomer'
        ];

        return $routes;
    }


}
