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

echo "= " . $email_heading . " =\n\n";
?>

<?php _e( 'Hello there,', 'dokan' );  echo " \n\n";?>

<?php printf( __( 'A new refund request is made by %s', 'dokan' ), $replace['customer_name'] ); echo " \n\n"; ?>

<?php _e( 'Summary of the Refund Request:', 'dokan' ); ?>

<?php echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n"; ?>

<?php printf( __( 'Request Type: %s', 'dokan' ), $type ); echo " \n"; ?>
<?php printf( __( 'Request Details: %s', 'dokan' ), $details ); echo " \n"; ?>
<?php printf( __( 'Order ID: <a target="_blank" href="%s">%s</a>', 'dokan' ), $order_link, $order_id ); echo " \n"; ?>
<?php printf( __( 'Reason: %s', 'dokan' ), $reason ); echo " \n"; ?>

<?php

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
