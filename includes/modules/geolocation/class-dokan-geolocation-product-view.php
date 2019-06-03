<?php

/**
 * Shows location maps for WC Products
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Product_View {

    /**
     * Map location
     *
     * Possible values: top, left, right
     *
     * @since 1.0.0
     *
     * @var string
     */
    private $map_location = 'top';

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->map_location = dokan_get_option( 'show_locations_map', 'dokan_geolocation', 'top' );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'woocommerce_before_shop_loop', array( $this, 'start_column_layout' ), 1 );
        add_action( 'woocommerce_no_products_found', array( $this, 'start_column_layout' ), 1 );
        add_action( 'woocommerce_after_main_content', array( $this, 'end_column_layout' ), 1 );
        add_action( 'woocommerce_before_shop_loop', array( $this, 'before_shop_loop' ) );
        add_action( 'woocommerce_no_products_found', array( $this, 'before_shop_loop' ), 9 );
        add_action( 'woocommerce_after_shop_loop_item', array( $this, 'after_shop_loop_item' ) );
    }

    /**
     * Enqueue locations map scripts in WC shop page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_scripts() {
        if ( ! is_shop() && ! is_product_taxonomy() ) {
            return;
        }

        dokan_geo_enqueue_locations_map();
    }

    /**
     * Include locations map template in WC shop page when layout is set to left or right
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function start_column_layout() {
        if ( ! is_shop() && ! is_product_taxonomy() ) {
            return;
        }

        if ( 'right' === $this->map_location ) {
            echo '<div class="dokan-geolocation-row dokan-geolocation-map-right"><div class="dokan-geolocation-col-7">';

        } else if ( 'left' === $this->map_location ) {
            echo '<div class="dokan-geolocation-row dokan-geolocation-map-left"><div class="dokan-geolocation-col-5">';

            dokan_geo_get_template( 'map', array( 'layout' => 'left' ) );

            echo '</div><div class="dokan-geolocation-col-7">';
        }
    }

    /**
     * Include locations map template in WC shop page when layout is set to left or right
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function end_column_layout() {
        if ( ! is_shop() && ! is_product_taxonomy() ) {
            return;
        }

        if ( 'right' === $this->map_location ) {
            echo '</div><div class="dokan-geolocation-col-5">';

            dokan_geo_get_template( 'map', array( 'layout' => 'right' ) );

            echo '</div>'; // .row

        } else if ( 'left' === $this->map_location ) {
            echo '</div>';  // .row
        }
    }

    /**
     * Include locations map template in WC shop page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function before_shop_loop() {
        if ( ! is_shop() && ! is_product_taxonomy() ) {
            return;
        }

        $show_filters = dokan_get_option( 'show_filters_before_locations_map', 'dokan_geolocation', 'on' );

        if ( 'on' === $show_filters ) {
            dokan_geo_filter_form( 'product' );
        }

        if ( 'top' === $this->map_location ) {
            dokan_geo_get_template( 'map', array( 'layout' => 'top' ) );
        }
    }

    /**
     * Include geolocation data for every product
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function after_shop_loop_item() {
        if ( ! is_shop() && ! is_product_taxonomy() ) {
            return;
        }

        global $post, $product;

        if ( empty( $post->dokan_geo_latitude ) || empty( $post->dokan_geo_longitude ) ) {
            return;
        }

        $image_src = wp_get_attachment_image_src( $product->get_image_id() );

        if ( ! empty( $image_src[0] ) ) {
            $image = $image_src[0];
        } else {
            $image = wc_placeholder_img_src();
        }

        $info_window_data = array(
            'title'   => $post->post_title,
            'link'    => get_permalink( $post->ID ),
            'image'   => $image,
            'address' => $post->dokan_geo_address,
        );

        /**
         * Filter to modify product data for map marker info window
         *
         * @since 1.0.0
         *
         * @param array      $info_window_data
         * @param WP_Post    $post
         * @param WC_Product $product
         */
        $info = apply_filters( 'dokan_geolocation_info_product', $info_window_data, $post, $product );

        $args = array(
            'id'                  => $post->ID,
            'dokan_geo_latitude'  => $post->dokan_geo_latitude,
            'dokan_geo_longitude' => $post->dokan_geo_longitude,
            'dokan_geo_address'   => $post->dokan_geo_address,
            'info'                => json_encode( $info ),
        );

        dokan_geo_get_template( 'item-geolocation-data', $args );
    }
}
