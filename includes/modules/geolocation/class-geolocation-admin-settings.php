<?php

/**
 * Dokan Geolocation Admin Settings
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Admin_Settings {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_filter( 'dokan_settings_sections', array( $this, 'add_settings_section' ) );
        add_filter( 'dokan_settings_fields', array( $this, 'add_settings_fields' ) );
    }

    /**
     * Add admin settings section
     *
     * @since 1.0.0
     *
     * @param array $sections
     *
     * @return array
     */
    public function add_settings_section( $sections ) {
        $sections['dokan_geolocation'] = array(
            'id'    => 'dokan_geolocation',
            'title' => __( 'Geolocation', 'dokan' ),
            'icon'  => 'dashicons-location'
        );

        return $sections;
    }

    /**
     * Add admin settings fields
     *
     * @since 1.0.0
     *
     * @param array $settings_fields
     *
     * @return array
     */
    public function add_settings_fields( $settings_fields ) {
        $settings_fields['dokan_geolocation'] = array(
            'show_locations_map' => array(
                'name'    => 'show_locations_map',
                'label'   => __( 'Location Map Position', 'dokan' ),
                'type'    => 'select',
                'default' => 'top',
                'options' => array(
                    'top'   => __( 'Top', 'dokan' ),
                    'left'  => __( 'Left', 'dokan' ),
                    'right' => __( 'Right', 'dokan' ),
                ),
            ),

            'show_filters_before_locations_map' => array(
                'name'    => 'show_filters_before_locations_map',
                'label'   => __( 'Show filters before location map', 'dokan' ),
                'desc'    => __( 'Yes', 'dokan' ),
                'type'    => 'checkbox',
                'default' => 'on',
            ),

            'show_product_location_in_wc_tab' => array(
                'name'    => 'show_product_location_in_wc_tab',
                'label'   => __( 'Product Location tab', 'dokan' ),
                'desc'    => __( 'Show Location tab in single product page', 'dokan' ),
                'type'    => 'checkbox',
                'default' => 'on',
            ),

            'distance_unit' => array(
                'name'    => 'distance_unit',
                'label'   => __( 'Radius Search - Unit', 'dokan' ),
                'type'    => 'select',
                'default' => 'km',
                'options' => array(
                    'km'    => __( 'Kilometers', 'dokan' ),
                    'miles' => __( 'Miles', 'dokan' ),
                ),
            ),

            'distance_min' => array(
                'name'    => 'distance_min',
                'label'   => __( 'Radius Search - Minimum Distance', 'dokan' ),
                'type'    => 'number',
                'min'     => 0,
                'default' => 0,
            ),

            'distance_max' => array(
                'name'    => 'distance_max',
                'label'   => __( 'Radius Search - Maximum Distance', 'dokan' ),
                'type'    => 'number',
                'min'     => 1,
                'default' => 10,
            ),

            'map_zoom'     => array(
                'name'    => 'map_zoom',
                'label'   => __( 'Map Zoom Level', 'dokan' ),
                'desc'    => __( 'To zoom out increase the number, to zoom in decrease the number.', 'dokan' ),
                'type'    => 'number',
                'min'     => 1,
                'max'     => 18,
                'default' => 11,
            )
        );

        return $settings_fields;
    }
}
