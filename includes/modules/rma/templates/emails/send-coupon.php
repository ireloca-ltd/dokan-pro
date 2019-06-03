<?php
/**
 * New Coupon Email
 *
 * An email is sent to admin and customer when a vendor generate a coupon
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! $coupon ) {
    return;
}

$coupon_link = wc_get_page_permalink( 'myaccount' );
$coupon_id   = $data['coupon_id'];
$coupon_link = $coupon_link . "/view-rma-requests/{$coupon_id}/";

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php _e( 'Hello,', 'dokan' ); ?></p>

<p> <?php _e( 'A new coupon is generated for you.', 'dokan' ); ?> </p>

<p><?php _e( 'Summary of the Coupon:', 'dokan' ); ?></p>
<hr>

<p><?php printf( __( 'Coupon Code: %s', 'dokan' ), $coupon->get_code() ); ?></p>
<p><?php printf( __( 'Coupon Amount: %s', 'dokan' ), $coupon->get_amount() ); ?></p>

<?php if ( ! current_user_can( 'manage_woocommerce' ) ) : ?>
    <p><?php printf( __( '<a target="_blank" href="%s">Click Here to See Details</a>', 'dokan' ), $coupon_link ); ?></p>
<?php endif; ?>

<?php do_action( 'woocommerce_email_footer', $email );
