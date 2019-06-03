<?php

/**
 * Vendor dashboard functionalities
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Vendor_Dashboard {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'dokan_store_profile_saved', array( $this, 'save_vendor_geodata' ), 10, 2 );
        add_action( 'dokan_product_edit_after_main', array( $this, 'add_product_editor_options' ) );
        add_action( 'dokan_new_product_added', array( $this, 'update_product_settings' ) );
        add_action( 'dokan_product_updated', array( $this, 'update_product_settings' ) );
        add_action( 'dokan_update_auction_product', array( $this, 'update_product_settings' ) );
    }

    /**
     * Use store settings option
     *
     * @since 1.0.0
     *
     * @param int $post_id
     *
     * @return string
     */
    public function use_store_settings( $post_id ) {
        $use_store_settings = get_post_meta( $post_id, '_dokan_geolocation_use_store_settings', true );

        if ( empty( $use_store_settings ) || 'yes' === $use_store_settings ) {
            return 'yes';
        } else {
            return 'no';
        }
    }

    /**
     * Save vendor geodata
     *
     * @since 1.0.0
     *
     * @param int   $store_id
     * @param array $dokan_settings
     *
     * @return void
     */
    public function save_vendor_geodata( $store_id, $dokan_settings ) {
        if ( isset( $dokan_settings['location'] ) && isset( $dokan_settings['find_address'] ) ) {
            $location = explode( ',', $dokan_settings['location'] );

            if ( 2 !== count( $location ) ) {
                return;
            }

            $old_latitude  = get_user_meta( $store_id, 'dokan_geo_latitude', true );
            $old_longitude = get_user_meta( $store_id, 'dokan_geo_longitude', true );

            $new_latitude  = $location[0];
            $new_longitude = $location[1];

            update_user_meta( $store_id, 'dokan_geo_latitude', $new_latitude );
            update_user_meta( $store_id, 'dokan_geo_longitude', $new_longitude );
            update_user_meta( $store_id, 'dokan_geo_public', 1 );
            update_user_meta( $store_id, 'dokan_geo_address', $dokan_settings['find_address'] );

            if ( ( $old_latitude == $new_latitude ) && ( $old_longitude == $new_longitude ) ) {
                return;
            }

            $updater_file = DOKAN_GEOLOCATION_PATH . '/class-dokan-geolocation-update-product-location-data.php';
            include_once $updater_file;

            $processor = new Dokan_Geolocation_Update_Product_Location_Data();

            $item = array(
                'vendor_id' => $store_id,
                'paged'     => 1,
            );

            $processor->push_to_queue( $item );
            $processor->save()->dispatch();

            $processes = get_option( 'dokan_background_processes', array() );
            $processes['Dokan_Geolocation_Update_Product_Location_Data'] = $updater_file;

            update_option( 'dokan_background_processes', $processes, 'no' );
        }
    }

    /**
     * Add product editor options/settings
     *
     * @since 1.0.0
     *
     * @param int $post_id
     *
     * @return void
     */
    public function add_product_editor_options( $post_id ) {
        $store_id            = dokan_get_current_user_id();
        $store_geo_latitude  = get_user_meta( $store_id, 'dokan_geo_latitude', true );

        if ( $post_id instanceof WP_Post ) {
            $post_id = $post_id->ID;
        }

        $use_store_settings  = $this->use_store_settings( $post_id );

        if ( 'yes' === $use_store_settings ) {
            $dokan_geo_latitude  = get_user_meta( $store_id, 'dokan_geo_latitude', true );
            $dokan_geo_longitude = get_user_meta( $store_id, 'dokan_geo_longitude', true );
            $dokan_geo_public    = get_user_meta( $store_id, 'dokan_geo_public', true );
            $dokan_geo_address   = get_user_meta( $store_id, 'dokan_geo_address', true );

        } else {
            $dokan_geo_latitude  = get_post_meta( $post_id, 'dokan_geo_latitude', true );
            $dokan_geo_longitude = get_post_meta( $post_id, 'dokan_geo_longitude', true );
            $dokan_geo_public    = get_post_meta( $post_id, 'dokan_geo_public', true );
            $dokan_geo_address   = get_post_meta( $post_id, 'dokan_geo_address', true );
        }

        if ( ! $dokan_geo_latitude || ! $dokan_geo_longitude ) {
            $default_locations = dokan_geo_get_default_location();
            $dokan_geo_latitude  = $default_locations['latitude'];
            $dokan_geo_longitude = $default_locations['longitude'];
        }

        $args = array(
            'post_id'             => $post_id,
            'use_store_settings'  => $use_store_settings,
            'dokan_geo_latitude'  => $dokan_geo_latitude,
            'dokan_geo_longitude' => $dokan_geo_longitude,
            'dokan_geo_public'    => $dokan_geo_public,
            'dokan_geo_address'   => $dokan_geo_address,
            'store_has_settings'  => ! empty( $store_geo_latitude ),
            'store_settings_url'  => dokan_get_navigation_url( 'settings/store' ),
        );

        dokan_geo_get_template( 'product-editor-options', $args );
    }

    /**
     * Update product settings
     *
     * @since 1.0.0
     *
     * @param int $post_id
     *
     * @return void
     */
    public function update_product_settings( $post_id ) {
        $store_id            = dokan_get_current_user_id();
        $dokan_geo_latitude  = get_user_meta( $store_id, 'dokan_geo_latitude', true );
        $dokan_geo_longitude = get_user_meta( $store_id, 'dokan_geo_longitude', true );
        $dokan_geo_public    = get_user_meta( $store_id, 'dokan_geo_public', true );
        $dokan_geo_address   = get_user_meta( $store_id, 'dokan_geo_address', true );

        $use_store_settings = 'yes';

        if ( isset( $_POST['_dokan_geolocation_use_store_settings'] ) && 'yes' !== $_POST['_dokan_geolocation_use_store_settings'] ) {
            $use_store_settings = 'no';
        }

        update_post_meta( $post_id, '_dokan_geolocation_use_store_settings', $use_store_settings );

        if ( 'yes' !== $use_store_settings ) {
            $dokan_geo_latitude  = ! empty( $_POST['_dokan_geolocation_product_dokan_geo_latitude'] ) ? $_POST['_dokan_geolocation_product_dokan_geo_latitude'] : null;
            $dokan_geo_longitude = ! empty( $_POST['_dokan_geolocation_product_dokan_geo_longitude'] ) ? $_POST['_dokan_geolocation_product_dokan_geo_longitude'] : null;
            $dokan_geo_address   = ! empty( $_POST['_dokan_geolocation_product_dokan_geo_address'] ) ? $_POST['_dokan_geolocation_product_dokan_geo_address'] : null;
        }

        update_post_meta( $post_id, 'dokan_geo_latitude', $dokan_geo_latitude );
        update_post_meta( $post_id, 'dokan_geo_longitude', $dokan_geo_longitude );
        update_post_meta( $post_id, 'dokan_geo_public', $dokan_geo_public );
        update_post_meta( $post_id, 'dokan_geo_address', $dokan_geo_address );
    }
}
