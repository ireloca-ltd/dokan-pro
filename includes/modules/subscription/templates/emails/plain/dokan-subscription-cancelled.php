<?php
/**
 * Subscription Cancelled Email
 *
 * An email is sent to admin when a subscription is get cancalled by the vendor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! $vendor ) {
    return;
}

echo "= " . $email_heading . " =\n\n";
?>

<?php _e( 'Hello there,', 'dokan' );  echo " \n\n";?>

<?php _e( "A subscription has been cancelled by {$vendor->get_store_name()}", "dokan" ); echo " \n\n"; ?>

<?php _e( 'Subscription Details:', 'dokan' );  ?>

<?php echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n"; ?>

<?php
    if ( $subscription ) {
        printf( __( 'Subscription Details:', 'dokan' ) ); echo " \n";

        printf( '<p>%s</p>', __( 'Subscription Pack: ' . $subscription->get_package_title(), 'dokan' ) ); echo " \n";
        printf( '<p>%s</p>', __( 'Price : ' . wc_price( $subscription->get_price() ) , 'dokan' ) ); echo " \n";
    }
?>

<?php

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
