<?php

/**
 * Scripts and Styles Class
 */
class Dokan_Pro_Assets {

    function __construct() {

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', [ $this, 'register' ], 5 );
            add_action( 'dokan-vue-admin-scripts', [ $this, 'enqueue_admin_scripts' ] );
            add_filter( 'dokan_admin_localize_script', [ $this, 'admin_localize_script' ] );
        } else {
            add_action( 'wp_enqueue_scripts', [ $this, 'register' ], 5 );
            add_action( 'dokan_enqueue_scripts', [ $this, 'enqueue_frontend_scripts' ], 5 );
        }
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_script( 'dokan-pro-vue-admin' );
        wp_enqueue_style( 'dokan-pro-vue-admin' );
    }

    /**
     * Enqueue forntend scripts
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function enqueue_frontend_scripts() {
        global $wp;

        if ( isset( $wp->query_vars['settings'] ) && $wp->query_vars['settings'] == 'shipping' ) {
            wp_enqueue_style( 'dokan-vue-bootstrap' );
            wp_enqueue_style( 'dokan-pro-vue-frontend-shipping' );
            wp_enqueue_script( 'dokan-pro-vue-frontend-shipping' );

            $localize_array = array(
                'nonce'             => wp_create_nonce( 'dokan_shipping_nonce' ),
                'allowed_countries' => WC()->countries->get_allowed_countries(),
                'continents'        => WC()->countries->get_continents(),
                'states'            => WC()->countries->get_states(),
                'shipping_class'    => WC()->shipping->get_shipping_classes(),
                'i18n'              => array( 'dokan' => dokan_get_jed_locale_data( 'dokan' ) ),
                'processing_time'   => dokan_get_shipping_processing_times()
            );

            wp_localize_script( 'dokan-pro-vue-frontend-shipping', 'dokanShipping', $localize_array );
        }


        // wp_enqueue_script( 'dokan-pro-vue-frontend' );
        // wp_enqueue_style( 'dokan-pro-vue-frontend' );
    }

    /**
     * Register our app scripts and styles
     *
     * @return void
     */
    public function register() {
        $this->register_scripts( $this->get_scripts() );
        $this->register_styles( $this->get_styles() );
    }

    /**
     * Register scripts
     *
     * @param  array $scripts
     *
     * @return void
     */
    private function register_scripts( $scripts ) {
        foreach ( $scripts as $handle => $script ) {
            $deps      = isset( $script['deps'] ) ? $script['deps'] : false;
            $in_footer = isset( $script['in_footer'] ) ? $script['in_footer'] : false;
            $version   = isset( $script['version'] ) ? $script['version'] : DOKAN_PRO_PLUGIN_VERSION;

            wp_register_script( $handle, $script['src'], $deps, $version, $in_footer );
        }
    }

    /**
     * Register styles
     *
     * @param  array $styles
     *
     * @return void
     */
    public function register_styles( $styles ) {
        foreach ( $styles as $handle => $style ) {
            $deps    = isset( $style['deps'] ) ? $style['deps'] : false;
            $version = isset( $style['version'] ) ? $style['version'] : DOKAN_PRO_PLUGIN_VERSION;

            wp_register_style( $handle, $style['src'], $deps, $version );
        }
    }

    /**
     * Get all registered scripts
     *
     * @return array
     */
    public function get_scripts() {
        $prefix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : '';

        $scripts = [
            'dokan-pro-vue-admin' => [
                'src'       => DOKAN_PRO_PLUGIN_ASSEST . '/js/vue-pro-admin.js',
                'deps'      => [ 'jquery', 'dokan-vue-vendor', 'dokan-vue-bootstrap' ],
                'version'   => filemtime( DOKAN_PRO_DIR . '/assets/js/vue-pro-admin.js' ),
                'in_footer' => true
            ],

            // 'dokan-pro-vue-frontend' => [
            //     'src'       => DOKAN_PRO_PLUGIN_ASSEST . '/js/vue-pro-frontend.js',
            //     'deps'      => [ 'jquery', 'dokan-vue-vendor', 'dokan-vue-bootstrap' ],
            //     'version'   => filemtime( DOKAN_PRO_DIR . '/assets/js/vue-pro-frontend.js' ),
            //     'in_footer' => true
            // ],

            'dokan-pro-vue-frontend-shipping' => [
                'src'       => DOKAN_PRO_PLUGIN_ASSEST . '/js/vue-pro-frontend-shipping.js',
                'deps'      => [ 'jquery', 'dokan-vue-vendor', 'dokan-vue-bootstrap' ],
                'version'   => filemtime( DOKAN_PRO_DIR . '/assets/js/vue-pro-frontend-shipping.js' ),
                'in_footer' => true
            ],
        ];

        return $scripts;
    }

    /**
     * Get registered styles
     *
     * @return array
     */
    public function get_styles() {

        $styles = [
            'dokan-pro-vue-admin' => [
                'src'     =>  DOKAN_PRO_PLUGIN_ASSEST . '/css/vue-pro-admin.css',
                'version' => filemtime( DOKAN_PRO_DIR . '/assets/css/vue-pro-admin.css' ),
            ],
            'dokan-pro-vue-frontend' => [
                'src'     =>  DOKAN_PRO_PLUGIN_ASSEST . '/css/vue-pro-frontend.css',
                'version' => time() //filemtime( DOKAN_PRO_DIR . '/assets/css/vue-pro-frontend.css' ),
            ],
            'dokan-pro-vue-frontend-shipping' => [
                'src'     =>  DOKAN_PRO_PLUGIN_ASSEST . '/css/vue-pro-frontend-shipping.css',
                'version' => filemtime( DOKAN_PRO_DIR . '/assets/css/vue-pro-frontend-shipping.css' ),
            ],
        ];

        return $styles;
    }

    /**
     * Admin localize Script
     *
     * @param  array $scripts
     *
     * @since 2.9.8
     *
     * @return array
     */
    public function admin_localize_script( $scripts ) {
        $general_settings                  = get_option( 'dokan_general', [] );
        $banner_width                      = ! empty( $general_settings['store_banner_width'] ) ? $general_settings['store_banner_width'] : 625;
        $banner_height                     = ! empty( $general_settings['store_banner_height'] ) ? $general_settings['store_banner_height'] : 300;
        $has_flex_width                    = ! empty( $general_settings['store_banner_flex_width'] ) ? $general_settings['store_banner_flex_width'] : true;
        $has_flex_height                   = ! empty( $general_settings['store_banner_flex_height'] ) ? $general_settings['store_banner_flex_height'] : true;

        $scripts['states']                 = WC()->countries->get_allowed_country_states();
        $scripts['countries']              = WC()->countries->get_allowed_countries();
        $scripts['urls']['proAssetsUrl']   = DOKAN_PRO_PLUGIN_ASSEST;
        $scripts['store_banner_dimension'] = [ 'width' => $banner_width, 'height' => $banner_height, 'flex-width'  => $has_flex_width, 'flex-height' => $has_flex_height ];

        return $scripts;
    }
}
