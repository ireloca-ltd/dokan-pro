<?php
/*
Plugin Name: ShipStation Integration
Plugin URI: https://wedevs.com/products/plugins/dokan/
Description: Adds ShipStation label printing support to Dokan. Requires server DomDocument support.
Version: 1.0.0
Author: weDevs
Author URI: https://wedevs.com/
Thumbnail Name: shipstation.png
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

class Dokan_ShipStation {

    /**
     * Module version
     *
     * @var string
     *
     * @since 1.0.0
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
        define( 'DOKAN_SHIPSTATION_VERSION' , $this->version );
        define( 'DOKAN_SHIPSTATION_PATH' , dirname( __FILE__ ) );
        define( 'DOKAN_SHIPSTATION_INCLUDES' , DOKAN_SHIPSTATION_PATH . '/includes' );
        define( 'DOKAN_SHIPSTATION_URL' , plugins_url( '', __FILE__ ) );
        define( 'DOKAN_SHIPSTATION_ASSETS' , DOKAN_SHIPSTATION_URL . '/assets' );
        define( 'DOKAN_SHIPSTATION_VIEWS', DOKAN_SHIPSTATION_PATH . '/views' );
        define( 'DOKAN_SHIPSTATION_EXPORT_LIMIT', 100 );
    }

    /**
     * Include module related PHP files
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function includes() {
        require_once DOKAN_SHIPSTATION_INCLUDES . '/functions.php';
        require_once DOKAN_SHIPSTATION_INCLUDES . '/class-dokan-shipstation-hooks.php';
        require_once DOKAN_SHIPSTATION_INCLUDES . '/class-dokan-shipstation-settings.php';
    }

    /**
     * Create module related class instances
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function instances() {
        new Dokan_ShipStation_Hooks();
        new Dokan_ShipStation_Settings();
    }
}

new Dokan_ShipStation();
