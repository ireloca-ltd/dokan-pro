<?php

/**
 * Geolocation Module Shortcodes
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Shortcode {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_shortcode( 'dokan-geolocation-filter-form', array( $this, 'shortcode' ) );
        add_filter( 'dokan_button_shortcodes', array( $this, 'add_to_dokan_shortcode_menu' ) );
    }

    /**
     * Geolocation Shortcode
     *
     * @since 1.0.0
     *
     * @param array $attrs
     *
     * @return string
     */
    public function shortcode( $attrs ) {
        $defaults = array(
            'scope' => '',
            'display' => 'inline',
        );

        $attrs = shortcode_atts( $defaults, $attrs );

        ob_start();
        dokan_geo_filter_form( $attrs['scope'], $attrs['display'] );
        return ob_get_clean();
    }

    /**
     * Add Geolocation shortocde to Dokan shortcode menu
     *
     * @since 1.0.0
     *
     * @param array $shortcodes
     *
     * @return array
     */
    public function add_to_dokan_shortcode_menu( $shortcodes ) {
        $shortcodes['dokan-geolocation-filter-form'] = array(
            'title'   => __( 'Geolocation Filter Form', 'dokan' ),
            'content' => '[dokan-geolocation-filter-form]'
        );

        return $shortcodes;
    }
}
