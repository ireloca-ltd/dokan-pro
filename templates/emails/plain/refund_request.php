<?php
/**
 * New Refund request Email. (plain text)
 *
 * An email sent to the admin when a new refund request is created by vendor.
 *
 * @class       Dokan_Email_Refund_Request
 * @version     2.6.6
 * 
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
echo "= " . $email_heading . " =\n\n";
?>

<?php _e( 'Hi', 'dokan' ); ?>,

<?php _e( 'New refund request for order #'.$data['order_id'], 'dokan' );  echo " \n";?>

<?php _e( 'You can process the request by going here: '.$data['refund_url'], 'dokan' ); echo " \n"; ?>

<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
