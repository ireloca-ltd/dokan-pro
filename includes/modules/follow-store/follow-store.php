<?php
/*
* Plugin Name: Follow Store
* Plugin URI: https://wedevs.com/products/plugins/dokan/
* Description: Send emails to customers when their favorite store updates.
* Version: 1.0.0
* Author: weDevs
* Author URI: https://wedevs.com/
* Thumbnail Name: follow-store.png
* License: GPL2
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

final class Dokan_Follow_Store {

    /**
     * Module version
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.0.0';

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->instances();
    }

    /**
     * Module constants
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function define_constants() {
        define( 'DOKAN_FOLLOW_STORE_VERSION' , $this->version );
        define( 'DOKAN_FOLLOW_STORE_FILE' , __FILE__ );
        define( 'DOKAN_FOLLOW_STORE_PATH' , dirname( DOKAN_FOLLOW_STORE_FILE ) );
        define( 'DOKAN_FOLLOW_STORE_INCLUDES' , DOKAN_FOLLOW_STORE_PATH . '/includes' );
        define( 'DOKAN_FOLLOW_STORE_URL' , plugins_url( '', DOKAN_FOLLOW_STORE_FILE ) );
        define( 'DOKAN_FOLLOW_STORE_ASSETS' , DOKAN_FOLLOW_STORE_URL . '/assets' );
        define( 'DOKAN_FOLLOW_STORE_VIEWS', DOKAN_FOLLOW_STORE_PATH . '/views' );
    }

    /**
     * Include module related files
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function includes() {
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/functions.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-install.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-scripts.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-ajax.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-follow-button.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-my-account.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-vendor-dashboard.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-cron.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-email-loader.php';
    }

    /**
     * Create module related class instances
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function instances() {
        new Dokan_Follow_Store_Install();
        new Dokan_Follow_Store_Scripts();
        new Dokan_Follow_Store_Ajax();
        new Dokan_Follow_Store_Follow_Button();
        new Dokan_Follow_Store_My_Account();
        new Dokan_Follow_Store_Vendor_Dashboard();
        new Dokan_Follow_Store_Cron();
        new Dokan_Follow_Store_Email_Loader();
    }
}

new Dokan_Follow_Store();
