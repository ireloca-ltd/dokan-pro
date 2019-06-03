<?php

/**
 * Dokan tinyMce Shortcode Button class
 *
 * @since 2.4.12
 */
class Dokan_shortcodes_button {

    /**
     * Constructor for shortcode class
     */
    public function __construct() {

        add_filter( 'mce_external_plugins',  array( $this, 'enqueue_plugin_scripts' ) );
        add_filter( 'mce_buttons',  array( $this, 'register_buttons_editor' ) );

        add_action( 'admin_enqueue_scripts', array( $this, 'localize_shortcodes' ) , 90  );
    }

    /**
     * Generate shortcode array
     *
     * @since 2.4.12
     *
     */
    function localize_shortcodes() {
        $screen = get_current_screen();

        $shortcodes = array(
            'dokan-dashboard'            => array(
                'title'   => __( 'Vendor Dasboard', 'dokan' ),
                'content' => '[dokan-dashboard]'
            ),
            'vendor-registration' => array(
                'title'   => __( 'Vendor Registration Form', 'dokan' ),
                'content' => '[dokan-vendor-registration]'
            ),
            'dokan-stores'               => array(
                'title'   => __( 'Stores List', 'dokan' ),
                'content' => '[dokan-stores]'
            ),
            'dokan-best-selling-product' => array(
                'title'   => __( 'Best Selling Product', 'dokan' ),
                'content' => '[dokan-best-selling-product no_of_product="5" seller_id="" ]'
            ),
            'dokan-top-rated-product'    => array(
                'title'   => __( 'Top Rated Products', 'dokan' ),
                'content' => '[dokan-top-rated-product]'
            ),
            'dokan-my-orders' => array(
                'title'   => __( 'Dokan My Orders', 'dokan' ),
                'content' => '[dokan-my-orders]'
            ),
            'dokan-customer-migration' => array(
                'title'   => __( 'Customer Migration Form', 'dokan' ),
                'content' => '[dokan-customer-migration]'
            ),
        );

        $assets_url = DOKAN_PRO_PLUGIN_ASSEST;

        if ( 'page' == $screen->post_type || 'product' == $screen->post_type ) {
            wp_localize_script( 'dokan_pro_admin', 'dokan_shortcodes', apply_filters( 'dokan_button_shortcodes', $shortcodes ) );
            wp_localize_script( 'dokan_pro_admin', 'dokan_assets_url', $assets_url );
        }
    }

    /**
     * * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_shortcodes_button();
        }

        return $instance;
    }

    /**
     * Add button on Post Editor
     *
     * @since 2.4.12
     *
     * @param array $plugin_array
     *
     * @return array
     */
    function enqueue_plugin_scripts( $plugin_array ) {
        //enqueue TinyMCE plugin script with its ID.
        $screen = get_current_screen();

        if ( 'page' == $screen->post_type || 'product' == $screen->post_type ) {
            $plugin_array["dokan_button"] =  DOKAN_PRO_PLUGIN_ASSEST . "/js/dokan-tmc-button.js";
        }

        return $plugin_array;
    }

    /**
     * Register tinyMce button
     *
     * @since 2.4.12
     *
     * @param array $buttons
     *
     * @return array
     */
    function register_buttons_editor( $buttons ) {
        //register buttons with their id.
        array_push( $buttons, "dokan_button" );

        return $buttons;
    }

}

Dokan_shortcodes_button::init();
