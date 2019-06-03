<?php
/*
Plugin Name: Single Product Multiple Vendor
Plugin URI: https://wedevs.com/products/dokan/dokan-simple-auctions/
Description: A module that offers multiple vendor to sell a single product
Version: 1.0.0
Author: weDevs
Author URI: https://wedevs.com/
Thumbnail Name: single-product-multivendor.png
*/

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
* className
*/
class Dokan_Single_Product_Multi_Vendor {

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
            $instance = new Dokan_Single_Product_Multi_Vendor();
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
        define( 'DOKAN_SPMV_DIR', dirname( __FILE__ ) );
        define( 'DOKAN_SPMV_INC_DIR', DOKAN_SPMV_DIR . '/includes' );
        define( 'DOKAN_SPMV_ASSETS_DIR', plugins_url( 'assets', __FILE__ ) );
        define( 'DOKAN_SPMV_VIEWS', DOKAN_SPMV_DIR . '/views' );
    }

    /**
     * includes all necessary class a functions file
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function includes() {
        require_once DOKAN_SPMV_INC_DIR . '/functions.php';
        require_once DOKAN_SPMV_INC_DIR . '/product-duplicator.php';

        if ( is_admin() ) {
            require_once DOKAN_SPMV_INC_DIR . '/admin.php';
            require_once DOKAN_SPMV_INC_DIR . '/products-admin.php';
        }

        require_once DOKAN_SPMV_INC_DIR . '/products.php';
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
            new Dokan_SPMV_Admin();
            new Dokan_SPMV_Products_Admin();
        }

        new Dokan_SPMV_Products();
    }

    /**
     * Init all hooks
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function hooks() {
        $enable_option = dokan_get_option( 'enable_pricing', 'dokan_spmv', 'off' );

        if ( 'off' == $enable_option ) {
            return;
        }

        //tinysort.min.js
        add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
    }

    /**
     * Load scripts
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_scripts() {
        wp_enqueue_script( 'dokan-spvm-tinysort', DOKAN_SPMV_ASSETS_DIR. '/js/tinysort.min.js', array( 'jquery' ) );
    }

    /**
     * Create Mapping table for product and vendor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function activate() {
        global $wpdb;

        $sql = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}dokan_product_map` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `map_id` bigint(20) DEFAULT NULL,
                `product_id` bigint(20) DEFAULT NULL,
                `seller_id` bigint(20) DEFAULT NULL,
                `is_trash` tinyint(4) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

}

Dokan_Single_Product_Multi_Vendor::init();
dokan_register_activation_hook( __FILE__, array( 'Dokan_Single_Product_Multi_Vendor', 'activate' ) );

