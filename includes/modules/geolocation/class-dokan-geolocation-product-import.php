<?php

/**
 * Dokan_Geolocation_Product_Import
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Product_Import {
    /**
     * Constructor method
     */
    public function __construct() {
        add_filter( 'woocommerce_product_importer_pre_expand_data', [ __CLASS__, 'format_geo_data' ] );
    }

    /**
     * Dokan format geo data
     *
     * @since 2.9.5
     *
     * @param array $data
     *
     * @return array
     */
    public static function format_geo_data( $data ) {
        if ( ! empty( $data['meta:dokan_geo_latitude'] ) ) {
            $data['meta:dokan_geo_latitude'] = dokan_geo_float_val( $data['meta:dokan_geo_latitude'] );
        }

        if ( ! empty( $data['meta:dokan_geo_longitude'] ) ) {
            $data['meta:dokan_geo_longitude'] = dokan_geo_float_val( $data['meta:dokan_geo_longitude'] );
        }

        return $data;
    }
}