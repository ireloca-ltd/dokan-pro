<?php

/**
* Dokan Shipping Zone Class
*
* @package dokan
*/
class Dokan_Shipping_Zone {

    /**
     * Get All Zone
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function get_zones() {
        $data_store = WC_Data_Store::load( 'shipping-zone' );
        $raw_zones  = $data_store->get_zones();
        $zones      = array();
        $seller_id  = dokan_get_current_user_id();

        foreach ( $raw_zones as $raw_zone ) {
            $zone             = new WC_Shipping_Zone( $raw_zone );
            $enabled_methods  = $zone->get_shipping_methods( true );
            $methods_id = wp_list_pluck( $enabled_methods, 'id' );

            if ( in_array( 'dokan_vendor_shipping', $methods_id ) ) {
                $zones[ $zone->get_id() ]                            = $zone->get_data();
                $zones[ $zone->get_id() ]['zone_id']                 = $zone->get_id();
                $zones[ $zone->get_id() ]['formatted_zone_location'] = $zone->get_formatted_location();
                $zones[ $zone->get_id() ]['shipping_methods']        = self::get_shipping_methods( $zone->get_id(), $seller_id );
            }
        }

        // Everywhere zone if has method called vendor shipping
        $overall_zone    = new WC_Shipping_Zone(0);
        $enabled_methods = $overall_zone->get_shipping_methods( true );
        $methods_id      = wp_list_pluck( $enabled_methods, 'id' );

        if ( in_array( 'dokan_vendor_shipping', $methods_id ) ) {
            $zones[ $overall_zone->get_id() ]                            = $overall_zone->get_data();
            $zones[ $overall_zone->get_id() ]['zone_id']                 = $overall_zone->get_id();
            $zones[ $overall_zone->get_id() ]['formatted_zone_location'] = $overall_zone->get_formatted_location();
            $zones[ $overall_zone->get_id() ]['shipping_methods']        = self::get_shipping_methods( $overall_zone->get_id(), $seller_id );
        }

        return $zones;
    }

    /**
     * Get single zone info
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function get_zone( $zone_id ) {
        $zone = array();
        $seller_id = dokan_get_current_user_id();

        $zone_obj = WC_Shipping_Zones::get_zone_by( 'zone_id', $zone_id );
        $zone['data']                    = $zone_obj->get_data();
        $zone['formatted_zone_location'] = $zone_obj->get_formatted_location();
        $zone['shipping_methods']        = self::get_shipping_methods( $zone_id, $seller_id );
        $zone['locations']               = self::get_locations( $zone_id );

        return $zone;
    }

    /**
     * Add Shipping Method for a zone
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function add_shipping_methods( $data ) {
        global $wpdb;

        $table_name = "{$wpdb->prefix}dokan_shipping_zone_methods";

        if ( empty( $data['method_id'] ) ) {
            return new WP_Error( 'no-method-id', __( 'No shipping method found for adding', 'dokan' ) );
        }

        $result = $wpdb->insert(
            $table_name,
            array(
                'method_id' => $data['method_id'],
                'zone_id'   => $data['zone_id'],
                'seller_id' => dokan_get_current_user_id()
            ),
            array(
                '%s',
                '%d',
                '%d'
            )
        );

        if ( ! $result ) {
            return new WP_Error( 'method-not-added', __( 'Shipping method not added successfully', 'dokan' ) );
        }

        return $wpdb->insert_id;
    }

    /**
     * Delete shipping method
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function delete_shipping_methods( $data ) {
        global $wpdb;

        $table_name = "{$wpdb->prefix}dokan_shipping_zone_methods";

        $result = $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE zone_id=%d AND seller_id=%d AND instance_id=%d", $data['zone_id'], dokan_get_current_user_id(), $data['instance_id'] ) );

        if ( ! $result ) {
            return new WP_Error( 'method-not-deleted', __( 'Shipping method not deleted', 'dokan' ) );
        }

        return $result;
    }

    /**
     * Get Shipping Methods for a zone
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function get_shipping_methods( $zone_id, $seller_id ) {
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}dokan_shipping_zone_methods WHERE `zone_id`={$zone_id} AND `seller_id`={$seller_id}";
        $results = $wpdb->get_results( $sql );

        $method = array();

        foreach ( $results as $key => $result ) {
            $default_settings = array(
                'title'       => self::get_method_label( $result->method_id ),
                'description' => __( 'Lets you charge a rate for shipping', 'dokan' ),
                'cost'        => '0',
                'tax_status'  => 'none'
            );

            $method_id = $result->method_id .':'. $result->instance_id;
            $settings = ! empty( $result->settings ) ? maybe_unserialize( $result->settings ) : array();
            $settings = wp_parse_args( $settings, $default_settings );

            $method[$method_id]['instance_id'] = $result->instance_id;
            $method[$method_id]['id']          = $result->method_id;
            $method[$method_id]['enabled']     = ( $result->is_enabled ) ? 'yes' : 'no';
            $method[$method_id]['title']       = $settings['title'];
            $method[$method_id]['settings']    = array_map( 'stripslashes_deep', maybe_unserialize( $settings ) );
        }

        return $method;
    }

    /**
     * Update shipping method settings
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function update_shipping_method( $args ) {
        global $wpdb;

        $data = array(
            'method_id' => $args['method_id'],
            'zone_id'   => $args['zone_id'],
            'seller_id' => empty( $args['seller_id'] ) ? dokan_get_current_user_id() : $args['seller_id'],
            'settings'  => maybe_serialize( $args['settings'] )
        );

        $table_name = "{$wpdb->prefix}dokan_shipping_zone_methods";
        $updated = $wpdb->update( $table_name, $data, array( 'instance_id' => $args['instance_id' ] ), array( '%s', '%d', '%d', '%s' ) );

        if ( $updated ) {
            return $data;
        }

        return false;
    }

    /**
     * Toggle shipping method
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function toggle_shipping_method( $data ) {
        global $wpdb;
        $table_name = "{$wpdb->prefix}dokan_shipping_zone_methods";
        $updated    = $wpdb->update( $table_name, array( 'is_enabled' => $data['checked']  ), array( 'instance_id' => $data['instance_id' ], 'zone_id' => $data['zone_id'], 'seller_id' => dokan_get_current_user_id() ), array( '%d' ) );

        if ( ! $updated ) {
            return new WP_Error( 'method-not-toggled', __( 'Method enable or disable not working', 'dokan' ) );
        }

        return true;
    }

    /**
     * Get zone locations
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function get_locations( $zone_id, $seller_id = null ) {
        global $wpdb;

        $table_name = "{$wpdb->prefix}dokan_shipping_zone_locations";

        if ( ! $seller_id ) {
            $seller_id  = dokan_get_current_user_id();
        }

        $sql = "SELECT * FROM {$table_name} WHERE zone_id=$zone_id AND seller_id=$seller_id";

        $results = $wpdb->get_results( $sql );

        $locations = array();

        if ( $results ) {
            foreach ( $results as $key => $result ) {
                $locations[] = array(
                    'code' => $result->location_code,
                    'type'  => $result->location_type
                );
            }
        }

        return $locations;
    }

    /**
     * Save zone location for seller
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function save_location( $location, $zone_id ) {
        global $wpdb;

        // Setup arrays for Actual Values, and Placeholders
        $values        = array();
        $place_holders = array();
        $seller_id     = dokan_get_current_user_id();
        $table_name    = "{$wpdb->prefix}dokan_shipping_zone_locations";

        $query = "INSERT INTO {$table_name} (seller_id, zone_id, location_code, location_type) VALUES ";

        if ( ! empty( $location ) ) {
            foreach( $location as $key => $value ) {
                array_push( $values, $seller_id, $zone_id, $value['code'], $value['type'] );
                $place_holders[] = "('%d', '%d', '%s', '%s')";
            }

            $query .= implode(', ', $place_holders);

            $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE zone_id=%d AND seller_id=%d", $zone_id, $seller_id ) );

            if ( $wpdb->query( $wpdb->prepare( "$query ", $values ) ) ) {
                return true;
            }
        } else {
            if( $wpdb->query( $wpdb->prepare( "DELETE FROM {$table_name} WHERE zone_id=%d AND seller_id=%d", $zone_id, $seller_id ) ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * get Shipping method label
     *
     * @since 2.8.0
     *
     * @return void
     */
    public static function get_method_label( $method_id ) {
        if ( 'flat_rate' == $method_id ) {
            return __( 'Flat Rate', 'dokan' );
        } elseif ( 'local_pickup' == $method_id ) {
            return __( 'Local Pickup', 'dokan' );
        } elseif( 'free_shipping' == $method_id ) {
            return __( 'Free Shipping', 'dokan' );
        } else {
            return __( 'Custom Shipping', 'dokan' );
        }
    }

    /**
     * Find a matching zone for a given package.
     *
     * @param  array $package Shipping package.
     *
     * @return WC_Shipping_Zone
     */
    public static function get_zone_matching_package( $package ) {
        $country          = strtoupper( wc_clean( $package['destination']['country'] ) );
        $state            = strtoupper( wc_clean( $package['destination']['state'] ) );
        $postcode         = wc_normalize_postcode( wc_clean( $package['destination']['postcode'] ) );
        $cache_key        = WC_Cache_Helper::get_cache_prefix( 'shipping_zones' ) . 'dokan_shipping_zone_' . md5( sprintf( '%s+%s+%s', $country, $state, $postcode ) );
        $matching_zone_id = wp_cache_get( $cache_key, 'shipping_zones' );

        if ( false === $matching_zone_id ) {
            $matching_zone_id = self::get_zone_id_from_package( $package );
            wp_cache_set( $cache_key, $matching_zone_id, 'shipping_zones' );
        }

        return new WC_Shipping_Zone( $matching_zone_id ? $matching_zone_id : 0 );
    }

    /**
     * Find a matching zone ID for a given package.
     *
     * @param  object $package Package information.
     *
     * @return int
     */
    public static function get_zone_id_from_package( $package ) {
        global $wpdb;

        $country   = strtoupper( wc_clean( $package['destination']['country'] ) );
        $state     = strtoupper( wc_clean( $package['destination']['state'] ) );
        $continent = strtoupper( wc_clean( WC()->countries->get_continent_code_for_country( $country ) ) );
        $postcode  = wc_normalize_postcode( wc_clean( $package['destination']['postcode'] ) );
        $vendor_id = self::get_vendor_id_from_package( $package );

        // Work out criteria for our zone search.
        $criteria   = array();
        $criteria[] = $wpdb->prepare( "( ( location_type = 'country' AND location_code = %s )", $country );
        $criteria[] = $wpdb->prepare( "OR ( location_type = 'state' AND location_code = %s )", $country . ':' . $state );
        $criteria[] = $wpdb->prepare( "OR ( location_type = 'continent' AND location_code = %s )", $continent );
        $criteria[] = 'OR ( location_type IS NULL ) )';

        // Postcode range and wildcard matching.
        $postcode_locations = $wpdb->get_results( "SELECT zone_id, location_code FROM {$wpdb->prefix}dokan_shipping_zone_locations WHERE location_type = 'postcode' AND seller_id = {$vendor_id};" );

        if ( $postcode_locations ) {
            $zone_ids_with_postcode_rules = array_map( 'absint', wp_list_pluck( $postcode_locations, 'zone_id' ) );
            $matches                      = wc_postcode_location_matcher( $postcode, $postcode_locations, 'zone_id', 'location_code', $country );
            $do_not_match                 = array_unique( array_diff( $zone_ids_with_postcode_rules, array_keys( $matches ) ) );
            if ( ! empty( $do_not_match ) ) {
                $criteria[] = 'AND zones.zone_id NOT IN (' . implode( ',', $do_not_match ) . ')';
            }
        }

        // Get matching zones.
        $zone_id = $wpdb->get_var(
            "SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones
            LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'
            WHERE " . implode( ' ', $criteria ) // phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared
            . ' ORDER BY zone_order ASC, zone_id ASC LIMIT 1'
        );

        // if zone id is not found in vendor's available zone id, assume it falls under `Locations not covered by your other zones`.
        if ( ! in_array( $zone_id, self::get_vendor_all_zone_ids( $package ) ) ) {
            return 0;
        }

        return $zone_id;
    }

    /**
     * Get vendor id from package
     *
     * @param  int $package
     *
     * @return int
     */
    public static function get_vendor_id_from_package( $package ) {
        if ( ! $package ) {
            return 0;
        }

        $vendor_id = isset( $package['seller_id'] ) ? $package['seller_id'] : '';

        if ( ! $vendor_id ) {
            return 0;
        }

        return $vendor_id;
    }

    /**
     * Get all the zone ids of a vendor
     *
     * @param  object $package
     *
     * @return array
     */
    public static function get_vendor_all_zone_ids( $package ) {
        global $wpdb;
        $vendor_id = isset( $package['seller_id'] ) ? $package['seller_id'] : '';

        if ( ! $vendor_id ) {
            return 0;
        }

        $table_name = "{$wpdb->prefix}dokan_shipping_zone_methods";
        $results    = $wpdb->get_results( $wpdb->prepare( "SELECT zone_id FROM {$table_name} WHERE seller_id=%d", $vendor_id ) );

        $zone_ids = array_map( function( $zone ) {
            return (int) $zone->zone_id;
        }, $results );

        return $zone_ids;
    }
}
