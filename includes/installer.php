<?php

/**
* Dokan Pro Installer file
*/
class Dokan_Pro_Installer {

    /**
     * Load automatically when class initiate
     *
     * @since 2.8.0
     */
    public function do_install() {
        $this->create_shipping_tables();
        $this->maybe_activate_modules();
    }

    /**
     * Maybe Activate modules
     *
     * @since 2.8.0
     *
     * @return void
     * */
    public function maybe_activate_modules() {
        global $wpdb;

        $has_installed = $wpdb->get_row( "SELECT option_id FROM {$wpdb->options} WHERE option_name = 'dokan_pro_active_modules'" );

        if ( $has_installed ) {
            return;
        }

        if ( ! function_exists( 'dokan_pro_get_modules' ) ) {
            require_once dirname( __FILE__ ) . '/modules.php';
        }

        $modules = dokan_pro_get_modules();

        if ( $modules ) {
            foreach ( $modules as $module_file => $data ) {
                dokan_pro_activate_module( $module_file );
            }
        }
    }

    /**
     * Create Shipping Tables
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function create_shipping_tables() {
        global $wpdb;

        $sqls = array(
            "CREATE TABLE `{$wpdb->prefix}dokan_shipping_zone_methods` (
              `instance_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `method_id` varchar(255) NOT NULL DEFAULT '',
              `zone_id` int(11) unsigned NOT NULL,
              `seller_id` int(11) NOT NULL,
              `is_enabled` tinyint(1) NOT NULL DEFAULT '1',
              `settings` longtext,
              PRIMARY KEY (`instance_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;",

            "CREATE TABLE `{$wpdb->prefix}dokan_shipping_zone_locations` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `seller_id` int(11) DEFAULT NULL,
              `zone_id` int(11) DEFAULT NULL,
              `location_code` varchar(255) DEFAULT NULL,
              `location_type` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;"
        );

        include_once ABSPATH . 'wp-admin/includes/upgrade.php';

        foreach ( $sqls as $sql ) {
            dbDelta( $sql );
        }
    }

}