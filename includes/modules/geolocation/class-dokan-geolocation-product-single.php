<?php

/**
 * Geolocation Module Product Tab in product single page
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Product_Single {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_filter( 'woocommerce_product_tabs', array( $this, 'add_tab' ) );
        add_action( 'dokan_geo_product_location_tab_data', array( $this, 'add_product_location_data' ), 30 );
    }

    /**
     * Add Product Location tab in product single page
     *
     * @since 1.0.0
     *
     * @param array $tabs
     *
     * @return array
     */
    public function add_tab( $tabs ) {
        $show_tab = dokan_get_option( 'show_product_location_in_wc_tab', 'dokan_geolocation', 'on' );

        if ( 'on' !== $show_tab ) {
            return $tabs;
        }

        $tabs['geolocation'] = array(
            'title'    => __( 'Location', 'dokan' ),
            'priority' => 90,
            'callback' => array( $this, 'location_tab' )
        );

        return $tabs;
    }

    /**
     * Location tab callback
     *
     * Prints google map with product location
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function location_tab() {
        printf( '<h2>%s</h2>', __( 'Product Location', 'dokan' ) );

        dokan_geo_product_location();

        do_action( 'dokan_geo_product_location_tab_data' );
    }

    /**
     * Add product location data in product single page
     *
     * @todo This should be a reusable function
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_product_location_data() {
        global $product;

        $latitude  = $product->get_meta( 'dokan_geo_latitude', true );
        $longitude = $product->get_meta( 'dokan_geo_longitude', true );
        $address   = $product->get_meta( 'dokan_geo_address', true );

        $args = array(
            'id'                  => $product->get_id(),
            'dokan_geo_latitude'  => $latitude,
            'dokan_geo_longitude' => $longitude,
            'dokan_geo_address'   => $address,
            'info'                => null,
        );

        dokan_geo_get_template( 'item-geolocation-data', $args );
    }
}
