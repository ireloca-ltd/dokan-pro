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

echo "= " . $email_heading . " =\n\n";
?>

<?php _e( 'Hello there,', 'dokan' );  echo " \n\n";?>

<?php _e( 'A new coupon is generated for you.', 'dokan' ); echo " \n\n"; ?>

<?php _e( 'Summary of the Coupon:', 'dokan' );  ?>

<?php echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n"; ?>

<?php printf( __( 'Coupon Code: %s', 'dokan' ), $coupon->get_code() ); echo " \n"; ?>
<?php printf( __( 'Coupon Amount: %s', 'dokan' ), $coupon->get_amount() ); echo " \n"; ?>

<?php if ( ! current_user_can( 'manage_woocommerce' ) ) : ?>
    <?php printf( __( '<a target="_blank" href="%s">Click Here to See Details</a>', 'dokan' ), $coupon_link ); ?>
<?php endif; ?>

<?php

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
