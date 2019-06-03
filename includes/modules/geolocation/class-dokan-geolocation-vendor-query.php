<?php

/**
 * Module related WP_User_Query filters
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Vendor_Query {

    /**
     * Holds the WP_User_Query object
     *
     * @since 1.0.0
     *
     * @var null|WP_User_Query
     */
    private $user_query = null;

    /**
     * Latitude query value
     *
     * @since 1.0.0
     *
     * @var float
     */
    private $latitude = 0.00;

    /**
     * Longitude query value
     *
     * @since 1.0.0
     *
     * @var float
     */
    private $longitude = 0.00;

    /**
     * Distance/Radius query value
     *
     * @since 1.0.0
     *
     * @var int
     */
    private $distance = 0;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_filter( 'dokan_seller_listing_args', array( $this, 'filter_pre_user_query' ) );
    }

    /**
     * Add action to filter vendor sql query
     *
     * @since 1.0.0
     *
     * @param array $seller_args
     *
     * @return array
     */
    public function filter_pre_user_query( $seller_args ) {
        add_action( 'pre_user_query', array( $this, 'add_user_query' ) );

        return $seller_args;
    }

    /**
     * WP_User_Query hook
     *
     * @since 1.0.0
     *
     * @param WP_User_Query $user_query
     */
    public function add_user_query( $user_query ) {
        if ( version_compare( DOKAN_PLUGIN_VERSION, '2.9.6', '>' ) ) {
            if ( empty( $user_query->query_vars['role__in'] ) || $user_query->query_vars['role__in'] !== ['seller', 'administrator'] ) {
                return;
            }
        } else {
            if ( empty( $user_query->query_vars['role'] ) || 'seller' !== $user_query->query_vars['role'] ) {
                return;
            }
        }

        $this->user_query = $user_query;

        $this->latitude  = get_query_var( 'latitude' );
        $this->longitude = get_query_var( 'longitude' );
        $this->distance  = get_query_var( 'distance', 0 );

        $this->filter_query_fields();
        $this->filter_query_from();
        $this->filter_query_orderby();
    }

    /**
     * Add extra select statement to WP_User_Query
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function filter_query_fields() {
        $this->user_query->query_fields .= ', metalat.meta_value as dokan_geo_latitude, metalong.meta_value as dokan_geo_longitude, metaaddr.meta_value as dokan_geo_address';

        if ( $this->latitude && $this->longitude ) {
            // unit in kilometers or miles
            $distance_unit = dokan_get_option( 'distance_unit', 'dokan_geolocation', 'km' );

            $distance_earth_center_to_surface = ( 'km' === $distance_unit ) ? 6371 : 3959;

            $this->user_query->query_fields .= ", (
                {$distance_earth_center_to_surface} * acos(
                    cos( radians( {$this->latitude} ) ) *
                    cos( radians( metalat.meta_value ) ) *
                    cos(
                        radians( metalong.meta_value ) - radians( {$this->longitude} )
                    ) +
                    sin( radians( {$this->latitude} ) ) *
                    sin( radians( metalat.meta_value ) )
                )
            ) as geo_distance";
        }
    }

    /**
     * Add inner join clauses to WP_User_Query
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function filter_query_from() {
        global $wpdb;

        $this->user_query->query_from .= " inner join {$wpdb->usermeta} as metalat on {$wpdb->users}.ID = metalat.user_id and metalat.meta_key = 'dokan_geo_latitude'";
        $this->user_query->query_from .= " inner join {$wpdb->usermeta} as metalong on {$wpdb->users}.ID = metalong.user_id and metalong.meta_key = 'dokan_geo_longitude'";
        $this->user_query->query_from .= " inner join {$wpdb->usermeta} as metaaddr on {$wpdb->users}.ID = metaaddr.user_id and metaaddr.meta_key = 'dokan_geo_address'";
    }

    /**
     * Add having clause to WP_User_Query
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function filter_query_orderby() {
        if ( $this->latitude && $this->longitude && $this->distance ) {
            $distance = absint( $this->distance );
            $this->user_query->query_orderby = "having geo_distance < {$distance} " . $this->user_query->query_orderby;
        }
    }
}
