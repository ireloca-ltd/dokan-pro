<?php

/**
 * Dashboard Template Class
 *
 * A template for frontend dashboard rendering items
 *
 * @since 2.4
 *
 * @author weDevs <info@wedevs.com>
 */
class Dokan_Pro_Dashboard extends Dokan_Template_Dashboard {

    /**
     * Constructor for the WeDevs_Dokan class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses add_action()
     *
     */
    public function __construct() {
        $this->user_id        = dokan_get_current_user_id();
        $this->comment_counts = $this->get_comment_counts();

        add_action( 'dokan_dashboard_before_widgets', array( $this, 'show_profile_progressbar' ), 10 );
        add_action( 'dokan_dashboard_left_widgets', array( $this, 'get_review_widget' ), 16 );
        add_action( 'dokan_dashboard_right_widgets', array( $this, 'get_announcement_widget' ), 12 );
    }

    /**
     * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {

        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Dashboard();
        }

        return $instance;
    }

    /**
     * Show Profile progressbar
     *
     * @return void
     */
    public function show_profile_progressbar() {
        if ( current_user_can( 'dokan_view_overview_menu' ) ) {
            echo dokan_get_profile_progressbar();
        }
    }

    /**
     * Get Review Widget
     *
     * @return void
     */
    public function get_review_widget() {
        if ( !current_user_can( 'dokan_view_overview_menu' ) ) {
            return;
        }

        if ( !current_user_can( 'dokan_view_review_reports' ) ) {
            return;
        }

        dokan_get_template_part( 'dashboard/review-widget', '', array(
            'pro'            => true,
            'comment_counts' => $this->comment_counts,
            'reviews_url'    => dokan_get_navigation_url( 'reviews' ),
        )
        );
    }

    /**
     * Get announcement widget
     *
     * @return void
     */
    public function get_announcement_widget() {
        if ( !current_user_can( 'dokan_view_overview_menu' ) ) {
            return;
        }

        if ( !current_user_can( 'dokan_view_announcement' ) ) {
            return;
        }

        $template_notice = Dokan_Pro_Notice::init();
        $query           = $template_notice->get_announcement_by_users( apply_filters( 'dokan_announcement_list_number', 3 ) );

        $args = array(
            'post_type'   => 'dokan_announcement',
            'post_status' => 'publish',
            'orderby'     => 'post_date',
            'order'       => 'DESC',
            'meta_key'    => '_announcement_type',
            'meta_value'  => 'all_seller',
        );

        $template_notice->add_query_filter();

        $all_seller_posts = new WP_Query( $args );

        $template_notice->remove_query_filter();

        $notices = array_merge( $all_seller_posts->posts, $query->posts );

        dokan_get_template_part( 'dashboard/announcement-widget', '', array(
            'pro'              => true,
            'notices'          => $notices,
            'announcement_url' => dokan_get_navigation_url( 'announcement' ),
        )
        );
    }

}
