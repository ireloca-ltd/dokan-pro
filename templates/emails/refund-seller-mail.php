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
?>
<p>
    <?php _e( "Hi ".$data['seller_name'], 'dokan' ); ?>
</p>
<p>
    <?php _e( 'Your refund request is '.$data['status'] , 'dokan' ); ?>
</p>
<hr>
<p>
    <?php _e( 'Order ID : '.$data['order_id'], 'dokan' ); ?>
    <?php _e( 'Refund Amount : '.$data['amount'], 'dokan' ); ?>
    <?php _e( 'Refund Reason : '.$data['reason'], 'dokan' ); ?>
</p>
<p>
    <?php echo sprintf( __( 'You can view the order details by clicking <a href="%s">here</a>', 'dokan' ), $data['order_link'] ); ?>
</p>

<?php
do_action( 'woocommerce_email_footer', $email );
