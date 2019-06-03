<?php

/**
 * Get Seller status counts, used in admin area
 *
 * @since 2.6.6
 *
 * @global WPDB $wpdb
 * @return array
 */
function dokan_wholesale_get_customer_status_count() {
    $args = [
        'role__in'   => [ 'seller', 'customer' ],
        'fields'     => 'ID',
        'meta_query' => [
            [
                'key'     => '_is_dokan_wholesale_customer',
                'compare' => 'EXISTS'
            ]
        ],
    ];

    $total_users = new WP_User_Query( $args );

    $args['meta_query'][] = [
        'key'     => '_dokan_wholesale_customer_status',
        'value'   => 'active',
        'compare' => '='
    ];

    $active_users   = new WP_User_Query( $args );
    $total_count    = $total_users->get_total();
    $inactive_count = $total_count - $active_users->get_total();

    $counts =  array(
        'total'    => $total_count,
        'active'   => $active_users->get_total(),
        'deactive' => $inactive_count,
    );

    return $counts;
}

/**
 * Check if customer has permission to see wholesale
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_wholesale_can_see_price() {
    $wholesale_price_display = dokan_get_option( 'wholesale_price_display', 'dokan_wholesale', 'wholesale_customer' );

    if ( 'all_user' == $wholesale_price_display ) {
        return true;
    }

    return current_user_can( 'dokan_wholesale_customer' ) ;
}
