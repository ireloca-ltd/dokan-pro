<?php
/*
Plugin Name: Geolocation
Plugin URI: https://wedevs.com/products/plugins/dokan/
Description: Search Products and Vendors by geolocation.
Version: 1.0.0
Author: weDevs
Author URI: https://wedevs.com/
Thumbnail Name: geolocation.png
License: GPL2
*/

/**
 * Copyright (c) 2016 weDevs (email: info@wedevs.com ). All rights reserved.
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
if ( ! defined( 'ABSPATH' ) ) exit;

class Dokan_Geolocation {

    /**
     * Module version
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = null;

    /**
     * Checks admin has set google map api key
     *
     * @since 1.0.0
     *
     * @var bool
     */
    public $has_gmap_api_key = false;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->version          = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : DOKAN_PRO_PLUGIN_VERSION;
        $this->has_gmap_api_key = dokan_get_option( 'gmap_api_key', 'dokan_general', false );

        $this->define_constants();
        $this->includes();
        $this->hooks();
        $this->instances();

        dokan_register_activation_hook( __FILE__, array( $this, 'activate' ) );
    }

    /**
     * Module constants
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function define_constants() {
        define( 'DOKAN_GEOLOCATION_VERSION' , $this->version );
        define( 'DOKAN_GEOLOCATION_PATH' , dirname( __FILE__ ) );
        define( 'DOKAN_GEOLOCATION_URL' , plugins_url( '', __FILE__ ) );
        define( 'DOKAN_GEOLOCATION_ASSETS' , DOKAN_GEOLOCATION_URL . '/assets' );
        define( 'DOKAN_GEOLOCATION_VIEWS', DOKAN_GEOLOCATION_PATH . '/views' );
    }

    /**
     * Add action and filter hooks
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function hooks() {
        if ( $this->has_gmap_api_key ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
            add_action( 'widgets_init', array( $this, 'register_widget' ) );
        } else {
            add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        }
    }

    /**
     * Include module related files
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function includes() {
        require_once DOKAN_GEOLOCATION_PATH . '/functions.php';
        require_once DOKAN_GEOLOCATION_PATH . '/class-geolocation-admin-settings.php';

        if ( $this->has_gmap_api_key ) {
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-scripts.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-shortcode.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-widget-filters.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-widget-product-location.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-vendor-dashboard.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-vendor-query.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-vendor-view.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-product-query.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-product-view.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-product-single.php';
            require_once DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-product-import.php';
        }
    }

    /**
     * Create module related class instances
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function instances() {
        new Dokan_Geolocation_Admin_Settings();

        if ( $this->has_gmap_api_key ) {
            new Dokan_Geolocation_Scripts();
            new Dokan_Geolocation_Shortcode();
            new Dokan_Geolocation_Vendor_Dashboard();
            new Dokan_Geolocation_Vendor_Query();
            new Dokan_Geolocation_Vendor_View();
            new Dokan_Geolocation_Product_Query();
            new Dokan_Geolocation_Product_View();
            new Dokan_Geolocation_Product_Single();
            new Dokan_Geolocation_Product_Import();
        }
    }

    /**
     * Run upon module activation
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function activate() {
        $updater_file = DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-update-location-data.php';

        include_once $updater_file;
        $processor = new Dokan_Geolocation_Update_Location_Data();

        $processor->cancel_process();

        $item = array(
            'updating' => 'vendors',
            'paged'    => 1,
        );

        $processor->push_to_queue( $item );
        $processor->save()->dispatch();

        $processes = get_option( 'dokan_background_processes', array() );
        $processes['Dokan_Geolocation_Update_Location_Data'] = $updater_file;

        update_option( 'dokan_background_processes', $processes, 'no' );
    }

    /**
     * Enqueue module scripts
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'dokan-geolocation', DOKAN_GEOLOCATION_ASSETS . '/css/geolocation.css', array(), $this->version );
        wp_enqueue_script( 'dokan-geolocation', DOKAN_GEOLOCATION_ASSETS . '/js/geolocation.js', array( 'jquery', 'google-maps' ), $this->version, true );
    }

    /**
     * Register module widgets
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_widget() {
        register_widget( 'Dokan_Geolocation_Widget_Filters' );
        register_widget( 'Dokan_Geolocation_Widget_Product_Location' );
    }

    /**
     * Show admin notices
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_notices() {
        dokan_geo_get_template( 'admin-notices' );
    }
}

new Dokan_Geolocation();
