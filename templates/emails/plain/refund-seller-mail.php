<?php
/**
 * Refund processed Email.
 *
 * An email sent to the vendor when a refund request is processed by admin.
 *
 * @class       Dokan_Email_Refund_Vendor
 * @version     2.6.6
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . $email_heading . " =\n\n";
?>
<?php _e( "Hi ".$data['seller_name'], 'dokan' );  echo " \n";?>
<?php _e( 'Your refund request is '.$data['status'] , 'dokan' );  echo " \n";?>
<?php _e( 'Order ID : '.$data['order_id'], 'dokan' );  echo " \n";?>
<?php _e( 'Refund Amount : '.$data['order_id'], 'dokan' );  echo " \n";?>
<?php _e( 'Refund Reason : '.$data['order_id'], 'dokan' );  echo " \n";?>
<?php echo sprintf( __( 'You can view the order details by clicking <a href="%s">here</a>', 'dokan' ), $data['order_link'] );  echo " \n";?>

<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
