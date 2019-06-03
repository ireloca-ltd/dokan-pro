<?php
/**
 * New Refund request Email.
 *
 * An email sent to the admin when a new refund request is created by vendor.
 *
 * @class       Dokan_Email_Refund_Request
 * @version     2.6.6
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p>
    <?php _e( 'Hi', 'dokan' ); ?>,
</p>
<p>
    <?php _e( 'New refund request for order #'.$data['order_id'], 'dokan' ); ?>
</p>
<p>
    <?php echo sprintf( __( 'You can process the request by going <a href="%s">here</a>', 'dokan' ), $data['refund_url'] ) ; ?>
</p>

<?php

do_action( 'woocommerce_email_footer', $email );
