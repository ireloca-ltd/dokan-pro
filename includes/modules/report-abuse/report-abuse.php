<?php
/*
* Plugin Name: Report Abuse
* Plugin URI: https://wedevs.com/products/plugins/dokan/
* Description: Let customers report fraudulent or fake products.
* Version: 2.9.8
* Author: weDevs
* Author URI: https://wedevs.com/
* Thumbnail Name: report-abuse.png
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

use Dokan\Traits\Singleton;

final class DokanReportAbuse {

    use Singleton;

    /**
     * Exec after first instance has been created
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function boot() {
        $this->define_constants();
        $this->includes();
        $this->instances();

        dokan_register_activation_hook( __FILE__, [ self::class, 'activate' ] );
    }

    /**
     * Module constants
     *
     * @since 2.9.8
     *
     * @return void
     */
    private function define_constants() {
        define( 'DOKAN_REPORT_ABUSE_FILE' , __FILE__ );
        define( 'DOKAN_REPORT_ABUSE_PATH' , dirname( DOKAN_REPORT_ABUSE_FILE ) );
        define( 'DOKAN_REPORT_ABUSE_INCLUDES' , DOKAN_REPORT_ABUSE_PATH . '/includes' );
        define( 'DOKAN_REPORT_ABUSE_URL' , plugins_url( '', DOKAN_REPORT_ABUSE_FILE ) );
        define( 'DOKAN_REPORT_ABUSE_ASSETS' , DOKAN_REPORT_ABUSE_URL . '/assets' );
        define( 'DOKAN_REPORT_ABUSE_VIEWS', DOKAN_REPORT_ABUSE_PATH . '/views' );
    }

    /**
     * Include module related files
     *
     * @since 2.9.8
     *
     * @return void
     */
    private function includes() {
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/functions.php';
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/AdminSettings.php';
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/Ajax.php';
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/SingleProduct.php';
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/EmailLoader.php';
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/Admin.php';
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/Rest.php';
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/AdminSingleProduct.php';
    }

    /**
     * Create module related class instances
     *
     * @since 2.9.8
     *
     * @return void
     */
    private function instances() {
        new \DokanPro\ReportAbuse\AdminSettings();
        new \DokanPro\ReportAbuse\Ajax();
        new \DokanPro\ReportAbuse\SingleProduct();
        new \DokanPro\ReportAbuse\EmailLoader();
        new \DokanPro\ReportAbuse\Admin();
        new \DokanPro\ReportAbuse\Rest();
        new \DokanPro\ReportAbuse\AdminSingleProduct();
    }

    /**
     * Executes on module activation
     *
     * @since 2.9.8
     *
     * @return void
     */
    public static function activate() {
        $option = get_option( 'dokan_report_abuse', [] );

        if ( empty( $option['abuse_reasons'] ) ) {
            $option['abuse_reasons'] = [
                [
                    'id'    => 'report_as_spam',
                    'value' => esc_html__( 'This content is spam', 'dokan' ),
                ],
                [
                    'id'    => 'report_as_adult',
                    'value' => esc_html__( 'This content should marked as adult', 'dokan' ),
                ],
                [
                    'id'    => 'report_as_abusive',
                    'value' => esc_html__( 'This content is abusive', 'dokan' ),
                ],
                [
                    'id'    => 'report_as_violent',
                    'value' => esc_html__( 'This content is violent', 'dokan' ),
                ],
                [
                    'id'    => 'report_as_risk_of_hurting',
                    'value' => esc_html__( 'This content suggests the author might be risk of hurting themselves', 'dokan' ),
                ],
                [
                    'id'    => 'report_as_infringes_copyright',
                    'value' => esc_html__( 'This content infringes upon my copyright', 'dokan' ),
                ],
                [
                    'id'    => 'report_as_contains_private_info',
                    'value' => esc_html__( 'This content contains my private information', 'dokan' ),
                ],
                [
                    'id' => 'other',
                    'value' => esc_html__( 'Other', 'dokan' )
                ],
            ];

            update_option( 'dokan_report_abuse', $option, false );
        }

        self::create_tables();
    }

    /**
     * Create module related tables
     *
     * @since 2.9.8
     *
     * @return void
     */
    private static function create_tables() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            if ( ! empty($wpdb->charset ) ) {
                $collate .= "AUTO_INCREMENT=1 DEFAULT CHARACTER SET $wpdb->charset";
            }

            if ( ! empty($wpdb->collate ) ) {
                $collate .= " AUTO_INCREMENT=1 COLLATE $wpdb->collate";
            }
        }

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $request_table = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}dokan_report_abuse_reports` (
          `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
          `reason` VARCHAR(191) NOT NULL,
          `product_id` BIGINT(20) NOT NULL,
          `vendor_id` BIGINT(20) NOT NULL,
          `customer_id` BIGINT(20) DEFAULT NULL,
          `customer_name` VARCHAR(191) DEFAULT NULL,
          `customer_email` VARCHAR(100) DEFAULT NULL,
          `description` TEXT DEFAULT NULL,
          `reported_at` DATETIME NOT NULL,
          INDEX `reason` (`reason`),
          INDEX `product_id` (`product_id`),
          INDEX `vendor_id` (`vendor_id`)
        ) $collate";

        dbDelta( $request_table );
    }
}

/**
 * Load Dokan Plugin when all plugins loaded
 *
 * @return \DokanReportAbuse
 */
function dokan_report_abuse() {
    return DokanReportAbuse::instance();
}

dokan_report_abuse();
