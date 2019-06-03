<?php
/**
 * New RMA Warranty Request Email.
 *
 * An email sent to the vendor when a request is sent to vendor.
 *
 */

defined( 'ABSPATH' ) || exit;

if ( ! $data ) {
    return;
}

$details    = isset( $data['details'] ) ? $data['details'] : '';
$type       = isset( $data['type'] ) ? ucwords( $data['type'] ) : '';
$order_id   = isset( $data['order_id'] ) ? $data['order_id'] : '';
$reason     = isset( $data['reasons'] ) ? ucwords( $data['reasons'] ) : '';
$order_link = wp_nonce_url( add_query_arg( [ 'order_id' => $order_id ], dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' );
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php _e( 'Hello,', 'dokan' ); ?></p>

<p> <?php printf( __( 'A new refund request is made by %s', 'dokan' ), $replace['customer_name'] ); ?> </p>

<p><?php _e( 'Summary of the Refund Request:', 'dokan' ); ?></p>
<hr>

<p><?php printf( __( 'Request Type: %s', 'dokan' ), $type ); ?></p>
<p><?php printf( __( 'Request Details: %s', 'dokan' ), $details ); ?></p>
<p><?php printf( __( 'Order ID: <a target="_blank" href="%s">%s</a>', 'dokan' ), $order_link, $order_id ); ?></p>
<p><?php printf( __( 'Reason: %s', 'dokan' ), $reason ); ?></p>

<?php

do_action( 'woocommerce_email_footer', $email );
