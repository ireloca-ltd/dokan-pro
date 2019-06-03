<?php

/**
 * Dokan Best Seller Widget Class
 *
 * @since 1.0
 *
 * @package dokan
 */
class Dokan_Best_Seller_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        $widget_ops = array( 'classname' => 'dokan-best-seller-widget', 'description' => 'Dokan best vendor widget' );
        parent::__construct( 'dokan-best-seller-widget', 'Dokan: Best Vendors', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     *
     * @return void Echoes it's output
     */
    function widget( $args, $instance ) {

        extract( $args, EXTR_SKIP );

        $title = apply_filters( 'widget_title', $instance['title'] );
        $limit = absint( $instance['count'] ) ? absint( $instance['count'] ) : 10;

        $seller = dokan_get_best_sellers( $limit );

        echo $before_widget;

        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        dokan_get_template_part( 'widgets/best-seller', '', array(
            'pro' => true,
            'seller' => $seller,
        ) );

        echo $after_widget;
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     *
     * @return array The validated and (if necessary) amended settings
     */
    function update( $new_instance, $old_instance ) {

        $updated_instance = $new_instance;
        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     *
     * @return void Echoes it's output
     */
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __( 'Best Vendor', 'dokan' ),
            'count' => __( '3', 'dokan' )
        ) );

        $title = $instance['title'];
        $count = $instance['count'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'dokan' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'No. of Vendor:', 'dokan' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" />
        </p>
        <?php
    }
}
