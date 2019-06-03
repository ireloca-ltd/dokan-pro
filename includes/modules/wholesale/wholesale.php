<?php
/*
Plugin Name: Wholesale
Plugin URI: http://wedevs.com/
Description: Offer any customer to buy product as a wholesale price from any vendors
Version: 2.9.5
Author: weDevs
Author URI: http://wedevs.com/
Thumbnail Name: wholesale.png
License: GPL2
*/

/**
 * Copyright (c) 2014 weDevs (email: info@wedevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
* className
*/
class Dokan_Wholesale {

    /**
     * Load automatically when class initiate
     *
     * @since 2.9.5
     */
    public function __construct() {

        $this->define();

        $this->includes();

        $this->initiate();

        $this->hooks();
    }

    /**
     * Initializes the Dokan_Wholesale() class
     *
     * Checks for an existing Dokan_Wholesale() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Wholesale();
        }

        return $instance;
    }

    /**
     * hooks
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function define() {
        define( 'DOKAN_WHOLESALE_DIR', dirname( __FILE__ ) );
        define( 'DOKAN_WHOLESALE_INC_DIR', DOKAN_WHOLESALE_DIR . '/includes' );
        define( 'DOKAN_WHOLESALE_ASSETS_DIR', plugins_url( 'assets', __FILE__ ) );
    }

    /**
    * Get plugin path
    *
    * @since 2.9.5
    *
    * @return void
    **/
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * includes all necessary class a functions file
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function includes() {
        require_once DOKAN_WHOLESALE_INC_DIR . '/functions.php';

        if ( is_admin() ) {
            require_once DOKAN_WHOLESALE_INC_DIR . '/clas-admin.php';
        }

        // Load all helper functions
        require_once DOKAN_WHOLESALE_INC_DIR . '/class-customer.php';
        require_once DOKAN_WHOLESALE_INC_DIR . '/class-vendor.php';
        require_once DOKAN_WHOLESALE_INC_DIR . '/class-cart-checkout.php';
    }

    /**
     * Initiate all classes
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function initiate() {
        if ( is_admin() ) {
            new Dokan_Wholesale_Admin();
        }

        new Dokan_Wholesale_Customer();
        new Dokan_Wholesale_Vendor();
        new Dokan_Wholesale_Cart_Checkout();
    }

    /**
     * Init all hooks
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function hooks() {
        add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
        add_filter( 'dokan_set_template_path', [ $this, 'load_wholesale_templates' ], 10, 3 );
        add_filter( 'dokan_rest_api_class_map', [ $this, 'rest_api_class_map' ] );
        add_filter( 'dokan_frontend_localize_script', [ $this, 'add_localize_data' ] );
    }

    /**
     * Load scripts
     *
     * @since 2.9.5
     *
     * @return void
    */
    public function load_scripts() {
        global $wp, $post;

        if ( is_account_page() ) {
            wp_enqueue_script( 'dokan-wholesale-script', DOKAN_WHOLESALE_ASSETS_DIR . '/js/scripts.js', array( 'jquery' ), DOKAN_PLUGIN_VERSION, true );
        }

        if ( $post ) {
            $product = wc_get_product( $post->ID );

            if ( $product && $product->is_type( 'variable' ) ) {
                wp_enqueue_script( 'dokan-wholesale-script', DOKAN_WHOLESALE_ASSETS_DIR . '/js/scripts.js', array( 'jquery' ), DOKAN_PLUGIN_VERSION, true );
                wp_localize_script( 'dokan-wholesale-script', 'DokanWholesale', [
                        'currency_symbol'   => get_woocommerce_currency_symbol(),
                        'check_permission'  => dokan_wholesale_can_see_price(),
                        'variation_wholesale_string' => apply_filters( 'dokan_variable_product_wholesale_string', [
                            'wholesale_price' => __( 'Wholesale Price', 'dokan' ),
                            'minimum_quantity' => __( 'Minimum Quantity', 'dokan' )
                        ] )
                    ]
                );
            }
        }
    }

    /**
     * Set template path for Wholesale
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function load_wholesale_templates( $template_path, $template, $args ) {
        if ( isset( $args['is_wholesale'] ) && $args['is_wholesale'] ) {
            return Dokan_Wholesale::init()->plugin_path() . '/templates';
        }

        return $template_path;
    }

    /**
     * Create Mapping table for product and vendor
     *
     * @since 2.9.5
     *
     * @return void
     */
    public static function activate() {

    }

    /**
     * REST API classes Mapping
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function rest_api_class_map( $class_map ) {
        $class_map[ DOKAN_WHOLESALE_INC_DIR . '/api/class-wholesale-controller.php'] = 'Dokan_REST_Wholesale_Controller';

        return $class_map;
    }

    /**
     * Set some localize data for wholesales
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_localize_data( $data ) {
        $data['wholesale'] = [
            'activeStatusMessage' => __( 'You are succefully converted as a wholesale customer', 'dokan' ),
            'deactiveStatusMessage' => __( 'Your wholesale customer request send to the admin. Please wait for approval', 'dokan' ),
        ];

        return $data;
    }


}

Dokan_Wholesale::init();

dokan_register_activation_hook( __FILE__, array( 'Dokan_Wholesale', 'activate' ) );

