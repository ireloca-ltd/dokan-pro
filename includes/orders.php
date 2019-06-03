<?php

/**
 * Get seller refund by date range
 *
 * @param  string  $start_date
 * @param  string  $end_date
 * @param  int $seller_id
 *
 * @return object
 */
function dokan_get_seller_refund_by_date( $start_date, $end_date, $seller_id = false ) {
    global $wpdb;

    $seller_id           = ! $seller_id ? get_current_user_id() : intval( $seller_id );
    $refund_status_where = $wpdb->prepare( ' AND status = %d', 1 );
    $refund_date_query   = $wpdb->prepare( ' AND DATE( date ) >= %s AND DATE( date ) <= %s', $start_date, $end_date );

    $refund_sql = "SELECT *
            FROM {$wpdb->prefix}dokan_refund
            WHERE
                seller_id = %d
                $refund_date_query
                $refund_status_where
            ORDER BY date ASC";

    return $wpdb->get_results( $wpdb->prepare( $refund_sql, $seller_id ) );
}

/**
 * Insert a order in sync table once a refund is created
 *
 * @since 2.4.11
 *
 * @global object $wpdb
 *
 * @param int $order_id
 */
function dokan_sync_refund_order( $order_id, $refund_id ) {
    global $wpdb;

    $order          = new WC_Order( $order_id );
    $seller_id      = dokan_get_seller_id_by_order( $order_id );
    $order_total    = $order->get_total();

    if ( $order->get_total_refunded() ) {
        $order_total = $order_total - $order->get_total_refunded();
    }

    $order_status   = dokan_get_prop( $order, 'status' );
    $admin_commission   = dokan_get_admin_commission_by( $order, $seller_id );
    $net_amount         = $order_total - $admin_commission;
    $net_amount     = apply_filters( 'dokan_order_refunded_net_amount', $net_amount, $order );

    // make sure order status contains "wc-" prefix
    if ( stripos( $order_status, 'wc-' ) === false ) {
        $order_status = 'wc-' . $order_status;
    }

    $wpdb->update( $wpdb->prefix . 'dokan_orders',
        array(
            'order_total'  => $order_total,
            'net_amount'   => $net_amount,
            'order_status' => $order_status,
        ),
        array(
            'order_id'     => $order_id
        ),
        array(
            '%f',
            '%f',
            '%s',
        )
    );
    update_post_meta( $order_id, 'dokan_refund_processing_id', $refund_id );
}
// add_action( 'woocommerce_order_refunded', 'dokan_sync_refund_order', 10, 2 );

/**
 * Insert a order in sync table once a refund is deleted
 *
 * @since 2.4.11
 *
 * @global object $wpdb
 *
 * @param int $order_id
 */
function dokan_delete_refund_order( $refund_id, $order_id ) {
    dokan_sync_refund_order( $order_id, $refund_id );
    delete_post_meta( $order_id, 'dokan_refund_processing_id' );
}
// add_action( 'woocommerce_refund_deleted', 'dokan_delete_refund_order', 10, 2 );

if ( !function_exists( 'dokan_sync_order_table' ) ) :

/**
 * Insert a order in sync table once a order is created
 *
 * @global object $wpdb
 * @param int $order_id
 * @since 2.4
 */
function dokan_sync_order_table( $order_id ) {
    global $wpdb;

    if ( get_post_meta( $order_id, 'has_sub_order', true ) == '1' ) {
        return;
    }

    $order          = wc_get_order( $order_id );
    $seller_id      = dokan_get_seller_id_by_order_id( $order_id );
    $order_total    = $order->get_total();

    if ( $order->get_total_refunded() ) {
        $order_total = $order_total - $order->get_total_refunded();
    }

    $order_status       = dokan_get_prop( $order, 'status' );
    $admin_commission   = dokan_get_admin_commission_by( $order, $seller_id );
    $net_amount         = $order_total - $admin_commission;
    $net_amount         = apply_filters( 'dokan_sync_order_net_amount', $net_amount, $order );

    // make sure order status contains "wc-" prefix
    if ( stripos( $order_status, 'wc-' ) === false ) {
        $order_status = 'wc-' . $order_status;
    }

    $seller_id = ! is_array( $seller_id ) ? $seller_id : 0;

    $wpdb->insert( $wpdb->prefix . 'dokan_orders',
        array(
            'order_id'     => $order_id,
            'seller_id'    => $seller_id,
            'order_total'  => $order_total,
            'net_amount'   => $net_amount,
            'order_status' => $order_status,
        ),
        array(
            '%d',
            '%d',
            '%f',
            '%f',
            '%s',
        )
    );
}

endif;

