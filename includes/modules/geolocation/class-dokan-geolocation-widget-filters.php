<?php

/**
 * Geolocation Module Widget Filter Form
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Widget_Filters extends WP_Widget {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $widget_ops = array(
            'classname'   => 'dokan-geolocation-widget-filters',
            'description' => __( 'Show Dokan Geolocation Filter Form', 'dokan' ),
        );

        parent::__construct( 'dokan-geolocation-widget-filters', __( 'Dokan: Geolocation Filter Form', 'dokan' ), $widget_ops );
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
            'scope'   => 'both',
            'display' => 'inline',
        ) );

        $args = array(
            'scope_id'     => $this->get_field_id( 'scope' ),
            'scope_name'   => $this->get_field_name( 'scope' ),
            'scope'        => $instance['scope'],
            'display_id'   => $this->get_field_id( 'display' ),
            'display_name' => $this->get_field_name( 'display' ),
            'display'      => $instance['display'],
        );

        dokan_geo_get_template( 'widget-form', $args );
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
        $scope   = in_array( $new_instance['scope'], array( 'both', 'product', 'vendor' ) ) ? $new_instance['scope'] : 'product';
        $display = ( 'inline' !== $new_instance['display'] ) ? '' : 'inline';

        return array(
            'scope'   => $scope,
            'display' => $display,
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
        extract( $args, EXTR_SKIP );

        echo $before_widget;

        $scope = in_array( $instance['scope'] , array( 'product', 'vendor' ) ) ? $instance['scope'] : '';
        $display = ( 'inline' !== $instance['display'] ) ? '' : 'inline';

        dokan_geo_filter_form( $scope, $display );

        echo $after_widget;
    }
}
