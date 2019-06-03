<?php
/*
Plugin Name: Return and Warranty Request
Plugin URI: http://wedevs.com/
Description: Manage return and warranty from vendor end
Version: 1.0.0
Author: weDevs
Author URI: http://wedevs.com/
Thumbnail Name: rma.png
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
class Dokan_RMA {

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {

        $this->define();

        $this->includes();

        $this->initiate();

        $this->hooks();
    }

    /**
     * Initializes the Dokan_Auction() class
     *
     * Checks for an existing Dokan_Auction() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_RMA();
        }

        return $instance;
    }

    /**
     * hooks
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function define() {
        define( 'DOKAN_RMA_DIR', dirname( __FILE__ ) );
        define( 'DOKAN_RMA_INC_DIR', DOKAN_RMA_DIR . '/includes' );
        define( 'DOKAN_RMA_ASSETS_DIR', plugins_url( 'assets', __FILE__ ) );
    }

    /**
    * Get plugin path
    *
    * @since 1.5.1
    *
    * @return void
    **/
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * includes all necessary class a functions file
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function includes() {
        if ( is_admin() ) {
            require_once DOKAN_RMA_INC_DIR . '/class-admin.php';
        }

        require_once DOKAN_RMA_INC_DIR . '/class-trait-rma.php';
        require_once DOKAN_RMA_INC_DIR . '/class-ajax.php';
        require_once DOKAN_RMA_INC_DIR . '/class-vendor.php';
        require_once DOKAN_RMA_INC_DIR . '/class-product.php';
        require_once DOKAN_RMA_INC_DIR . '/class-order.php';
        require_once DOKAN_RMA_INC_DIR . '/class-frontend.php';
        require_once DOKAN_RMA_INC_DIR . '/class-warranty-request.php';
        require_once DOKAN_RMA_INC_DIR . '/class-warranty-item.php';
        require_once DOKAN_RMA_INC_DIR . '/class-warranty-request-conversation.php';

        // Load all helper functions
        require_once DOKAN_RMA_INC_DIR . '/functions.php';
    }

    /**
     * Initiate all classes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function initiate() {
        if ( is_admin() ) {
            new Dokan_RMA_Admin();
        }

        new Dokan_RMA_Ajax();
        new Dokan_RMA_Vendor();
        new Dokan_RMA_Frontend();
        new Dokan_RMA_Product();
        new Dokan_RMA_Order();
    }

    /**
     * Init all hooks
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function hooks() {
        //tinysort.min.js
        add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );

        add_action( 'dokan_loaded', [ $this, 'load_emails' ], 20 );
        // dokan simple auciton email

    }

    /**
     * Load emails
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_emails() {
        add_filter( 'woocommerce_email_classes', [ $this, 'load_rma_email_classes' ], 99 );
        add_filter( 'dokan_email_actions', [ $this, 'register_rma_email_actions' ] );
    }

    /**
     * Load all email class related with RMA
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_rma_email_classes( $wc_emails ) {
        $wc_emails['Dokan_Send_Coupon_Email']         = include( DOKAN_RMA_INC_DIR. '/emails/class-dokan-rma-send-coupin-email.php' );
        $wc_emails['Dokan_Rma_Send_Warranty_Request'] = include( DOKAN_RMA_INC_DIR. '/emails/class-dokan-rma-send-warranty-request.php' );

        return $wc_emails;
    }

    /**
     * Register all email actions
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_rma_email_actions( $actions ) {
        $actions[] = 'dokan_send_coupon_to_customer';
        $actions[] = 'dokan_rma_send_warranty_request';

        return $actions;
    }

    /**
     * Load scripts
     *
     * @since 1.0.0
     *
     * @return void
    */
    public function load_scripts() {
        global $wp;

        if ( ( isset( $wp->query_vars['settings'] ) && $wp->query_vars['settings'] == 'rma' )
            || ( get_query_var( 'edit' ) && is_singular( 'product' ) ) ) {
            wp_enqueue_script( 'dokan-rma-script', DOKAN_RMA_ASSETS_DIR . '/js/scripts.js', array( 'jquery' ), DOKAN_PLUGIN_VERSION, true );
            wp_enqueue_style( 'dokan-rma-style', DOKAN_RMA_ASSETS_DIR . '/css/style.css', false , DOKAN_PLUGIN_VERSION, 'all' );
        }


        if ( is_account_page() && ( isset( $wp->query_vars[ 'request-warranty' ] ) || isset( $wp->query_vars['view-rma-requests'] ) ) ) {
            wp_enqueue_style( 'dokan-rma-style', DOKAN_RMA_ASSETS_DIR . '/css/style.css', false , DOKAN_PLUGIN_VERSION, 'all' );
        }

        if ( isset( $wp->query_vars[ 'return-request' ] ) ) {
            wp_enqueue_style( 'dokan-rma-style', DOKAN_RMA_ASSETS_DIR . '/css/style.css', false , DOKAN_PLUGIN_VERSION, 'all' );
            wp_enqueue_script( 'dokan-rma-script', DOKAN_RMA_ASSETS_DIR . '/js/scripts.js', array( 'jquery' ), DOKAN_PLUGIN_VERSION, true );

            wp_localize_script( 'dokan-rma-script', 'DokanRMA', [
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'dokan_rma_nonce' )
            ] );
        }

        if ( is_account_page() ) {
            $custom_css = '
            body.woocommerce-account ul li.woocommerce-MyAccount-navigation-link--rma-requests a:before{
                content: "\f0e2"
            }';
            wp_add_inline_style( 'woocommerce-layout', $custom_css );
        }
    }

    /**
     * Create Mapping table for product and vendor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function activate() {
        global $wp_roles;

        if ( class_exists( 'WP_Roles' ) && !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        $wp_roles->add_cap( 'seller', 'dokan_view_store_rma_menu' );
        $wp_roles->add_cap( 'administrator', 'dokan_view_store_rma_menu' );
        $wp_roles->add_cap( 'shop_manager', 'dokan_view_store_rma_menu' );

        $wp_roles->add_cap( 'seller', 'dokan_view_store_rma_settings_menu' );
        $wp_roles->add_cap( 'administrator', 'dokan_view_store_rma_settings_menu' );
        $wp_roles->add_cap( 'shop_manager', 'dokan_view_store_rma_settings_menu' );

        self::create_tables();
    }

    /**
     * Create all tables related with RMA
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function create_tables() {
        global $wpdb;

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $request_table = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}dokan_rma_request` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `order_id` int(11) NOT NULL,
          `vendor_id` int(11) NOT NULL,
          `customer_id` int(11) NOT NULL,
          `type` varchar(25) NOT NULL DEFAULT '',
          `status` varchar(25) NOT NULL DEFAULT '',
          `reasons` text NOT NULL,
          `details` longtext,
          `note` longtext,
          `created_at` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

        $request_product_map = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}dokan_rma_request_product` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `request_id` int(11) NOT NULL,
          `product_id` int(11) NOT NULL,
          `quantity` int(11) NOT NULL,
          `item_id` int(11) NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

        $conversation_table = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}dokan_rma_conversations` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `request_id` int(11) NOT NULL,
          `from` int(11) NOT NULL,
          `to` int(11) NOT NULL,
          `message` longtext,
          `created_at` datetime NOT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

        dbDelta( $request_table );
        dbDelta( $request_product_map );
        dbDelta( $conversation_table );
    }

}

Dokan_RMA::init();

dokan_register_activation_hook( __FILE__, array( 'Dokan_RMA', 'activate' ) );

