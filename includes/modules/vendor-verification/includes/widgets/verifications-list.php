<?php

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class Dokan_Verification_list extends WP_Widget {

    private $seller_info;

    /**
     * Constructor
     *
     * @return void
     * */
    public function __construct() {
        $widget_ops = array( 'classname' => 'dokan-verification-list', 'description' => __( 'Dokan Seller Verifications', 'dokan' ) );
        parent::__construct( 'dokan-verification-list', __( 'Dokan: Verification', 'dokan' ), $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     * */
    function widget( $args, $instance ) {
        if ( dokan_is_store_page() || is_product() ) {

            if ( is_product() ) {
                global $post;
                $seller_id = get_post_field( 'post_author', $post->ID );
            }

            if ( dokan_is_store_page() ) {
                $seller_id  = (int) get_query_var( 'author' );
            }

            if ( empty( $seller_id ) ) {
                return;
            }

            $store_info = dokan_get_store_info( $seller_id );

            $this->seller_info = $store_info;

            if ( ! isset( $store_info['dokan_verification']['verified_info'] ) || empty( $store_info['dokan_verification']['verified_info'] ) ) {
                return;
            }
            extract( $args, EXTR_SKIP );

            $title = apply_filters( 'widget_title', $instance['title'] );

            echo $before_widget;

            if ( !empty( $title ) ) {
                echo $args['before_title'] . $title . $args['after_title'];
            }
            if ( !isset( $store_info['dokan_verification']['info'] ) ) {
                return;
            }

            if ( $store_info['dokan_verification']['info'] == '' || sizeof( $store_info['dokan_verification']['info'] ) < 0 ) {
                return;
            }
            ?>

            <div id="dokan-verification-list">
                <ul class="fa-ul">
                    <?php
                    foreach ( $store_info['dokan_verification'] as $key => $item ) {
                        $this->print_item( $key, $item );
                    }
                    ?>
                </ul>
            </div>

            <?php
            echo $after_widget;
        }
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     * */
    function update( $new_instance, $old_instance ) {

        // update logic goes here
        $updated_instance = $new_instance;
        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     * */
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array(
            'title' => __( 'ID Verification', 'dokan' ),
        ) );

        $title = $instance['title'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'dokan' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    /*
     * Prints out list items after checking
     */

    function print_item( $key, $item ) {

        switch ( $key ) {
            case 'info' :
                $this->print_info_items( $key, $item );
                break;
            case 'verified_info' :

                break;
            default :
                $this->print_social_item( $key, $item );
                break;
        }
    }

    function print_social_item( $key, $item ) {

        if ( $item === '' || sizeof( $item ) < 0 ) {
            return;
        }
        ?>

        <li class="clearfix">
            <i class="fa fa-<?php echo $key ?>-square"></i><span><?php echo ucfirst( $key ) ?></span><i class="fa fa-check-circle-o verification-icon dokan-right"></i>
        </li>

    <?php
    }

    function print_info_items( $key, $item ) {

        if ( isset( $item['dokan_v_id_status'] ) ) {
            if ( $item['dokan_v_id_status'] === 'approved' ) {
                ?>
                <li class="clearfix">
                    <i class="fa fa-user"></i><span><?php _e( 'Photo ID', 'dokan' ); ?></span><i class="fa fa-check-circle-o verification-icon dokan-right"></i>
                </li>
            <?php
            }
        }

        if ( isset( $item['store_address']['v_status'] ) && $item['store_address']['v_status'] === 'approved' ) {
            if ( sizeof( $this->verify_address( $item['store_address'] ) ) == 0 ) {
                ?>
                <li class="clearfix">
                    <i class="fa fa-map-marker"></i><span><?php _e( 'Postal Address', 'dokan' ); ?></span><i class="fa fa-check-circle-o verification-icon dokan-right"></i>
                </li>
            <?php
            }
        }

        if ( isset( $item['phone_status'] ) ) {
            if ( $item['phone_status'] === 'verified' ) {
                ?>
                <li class="clearfix">
                    <i class="fa fa-phone-square"></i><span><?php _e( 'Phone', 'dokan' ); ?></span><i class="fa fa-check-circle-o verification-icon dokan-right"></i>
                </li>
            <?php
            }
        }
    }

    private function verify_address( $verified_address ) {
        $store_address = $this->seller_info['address'];
        return array_diff( $store_address, $verified_address );
    }

}

add_action( 'widgets_init', 'dokan_register_verification_widget' );

function dokan_register_verification_widget() {
    register_widget( 'Dokan_Verification_list' );
}

add_action( 'dokan_sidebar_store_after', 'dokan_show_verification_widget');

function dokan_show_verification_widget() {
    if ( !is_active_sidebar( 'sidebar-store' ) ) {
        $args = array(
            'before_widget' => '<aside class="widget">',
            'after_widget'  => '</aside>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        );
        the_widget( 'Dokan_Verification_list', array( 'title' => __( 'Verifications', 'dokan' ) ), $args );
    }
}
