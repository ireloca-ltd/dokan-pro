<?php

class Dokan_Follow_Store_Vendor_Dashboard {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'init', array( $this, 'add_endpoint' ) );
        add_action( 'dokan_get_dashboard_nav', array( $this, 'add_dashboard_nav' ) );
        add_action( 'dokan_load_custom_template', array( $this, 'load_dashboard_template' ) );
    }

    /**
     * Register new endpoint for Vendor Dashbaord page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_endpoint() {
        add_rewrite_endpoint( 'followers', EP_PAGES );
    }

    /**
     * Add settings nav in settings page
     *
     * @since 1.0.0
     *
     * @param array $settings
     */
    public function add_dashboard_nav( $settings ) {
        $settings['followers'] = array(
            'title'      => __( 'Followers', 'dokan' ),
            'icon'       => '<i class="fa fa-heart"></i>',
            'url'        => dokan_get_navigation_url( 'followers' ),
            'pos'        => 175,
            'permission' => 'dokan_view_overview_menu'
        );

        return $settings;
    }

    /**
     * Load dashboard page template
     *
     * @since 1.0.0
     *
     * @param array $query_vars
     *
     * @return void
     */
    public function load_dashboard_template( $query_vars ) {
        global $wpdb;

        if ( empty( $query_vars ) || ! array_key_exists( 'followers' , $query_vars ) ) {
            return;
        }

        $vendor_id = dokan_get_current_user_id();

        $followers = $wpdb->get_results(
            $wpdb->prepare(
                  "select follower_id, vendor_id, followed_at"
                . " from {$wpdb->prefix}dokan_follow_store_followers"
                . " where vendor_id = %d"
                . "     and unfollowed_at is null",
                $vendor_id
            ),
            OBJECT_K
        );

        if ( empty( $followers ) ) {
            $customers = array();

        } else {
            $customer_ids = array_keys( $followers );

            $query = new WP_User_Query( array(
                'include' => $customer_ids,
                'number'  => -1,
            ) );

            $customers = $query->get_results();
        }

        $args = array(
            'vendor_id' => $vendor_id,
            'followers' => $followers,
            'customers' => $customers,
        );

        dokan_follow_store_get_template( 'vendor-dashboard', $args );
    }
}
