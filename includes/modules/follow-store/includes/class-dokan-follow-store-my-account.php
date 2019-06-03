<?php

class Dokan_Follow_Store_My_Account {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'init', array( $this, 'add_endpoint' ) );

        add_filter( 'woocommerce_account_menu_items', array( $this, 'add_wc_account_menu_items' ) );
        add_action( 'woocommerce_account_following_endpoint', array( $this, 'add_wc_account_content' ) );
    }

    /**
     * Register new endpoint for My Account page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_endpoint() {
        add_rewrite_endpoint( 'following', EP_PAGES );
    }

    /**
     * Add Vendor tab in customer dashboard
     *
     * @since 1.0.0
     *
     * @param array $items
     *
     * @return array
     */
    public function add_wc_account_menu_items( $items ) {
        unset( $items['customer-logout'] );

        $items['following']       = __( 'Vendors', 'dokan' );
        $items['customer-logout'] = __( 'Logout', 'dokan' );

        return $items;
    }

    /**
     * Add content to the new endpoint
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_wc_account_content() {
        global $wpdb;

        $customer_id = get_current_user_id();

        $following_stores = $wpdb->get_col(
            $wpdb->prepare(
                  "select `vendor_id`"
                . " from {$wpdb->prefix}dokan_follow_store_followers"
                . " where `follower_id` = %d"
                . " and `unfollowed_at` is null",
                $customer_id
            )
        );

        if ( empty( $following_stores ) ) {
            $vendors = array(
                'users' => null,
                'count' => 0,
            );

        } else {
            $args = array(
                'include' => $following_stores,
            );

            $vendors = dokan_get_sellers( $args );
        }

        $template_args = array(
            'sellers'         => $vendors,
            'limit'           => $vendors['count'],
            'offset'          => 0,
            'paged'           => false,
            'search_query'    => null,
            'pagination_base' => null,
            'per_row'         => 3,
            'search_enabled'  => false,
            'image_size'      => 'full',
        );

        if ( function_exists( 'dokan_geo_remove_seller_listing_footer_content_hook' ) ) {
            dokan_geo_remove_seller_listing_footer_content_hook();
        }

        dokan_get_template_part( 'store-lists-loop', false, $template_args );
    }
}
