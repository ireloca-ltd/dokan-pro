<?php

/**
 * Geolocation Module Product Location Widget
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Widget_Product_Location extends WP_Widget {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $widget_ops = array(
            'classname'   => 'dokan-geolocation-widget-product-location',
            'description' => __( 'Show product geolocation informations in single page', 'dokan' ),
        );

        parent::__construct( 'dokan-geolocation-widget-product-location', __( 'Dokan: Product Location', 'dokan' ), $widget_ops );
    }

    /**
     * Widget settings form in widget settings
     *
     * @since 1.0.0
     *
     * @param array $instance
     *
     * @return void
     */
    public function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __( 'Product Location', 'dokan' ),
        ) );

        $args = array(
            'title_id'   => $this->get_field_id( 'title' ),
            'title_name' => $this->get_field_name( 'title' ),
            'title'      => $instance['title'],
        );

        dokan_geo_get_template( 'widget-product-location', $args );
    }

    /**
     * Update or save widget settings
     *
     * @since 1.0.0
     *
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array
     */
    public function update( $new_instance, $old_instance ) {
        $title = empty( $new_instance['title'] ) ? __( 'Product Location', 'dokan' ) : $new_instance['title'];

        return array(
            'title' => $title,
        );
    }

    /**
     * Display widget in frontend
     *
     * @since 1.0.0
     *
     * @param array $args
     * @param array $instance
     *
     * @return void
     */
    public function widget( $args, $instance ) {
        if ( ! is_product() ) {
            return;
        }

        $show_tab = dokan_get_option( 'show_product_location_in_wc_tab', 'dokan_geolocation', 'on' );

        if ( 'on' === $show_tab ) {
            return;
        }

        extract( $args, EXTR_SKIP );

        echo $before_widget;

        $title = empty( $instance['title'] ) ? __( 'Product Location', 'dokan' ) : $instance['title'];

        echo $args['before_title'] . $title . $args['after_title'];

        dokan_geo_product_location();

        $this->add_product_location_data();

        echo $after_widget;
    }

    /**
     * Add product location data in product single page
     *
     * @todo This should be a reusable function
     *
     * @since 2.9.5
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
