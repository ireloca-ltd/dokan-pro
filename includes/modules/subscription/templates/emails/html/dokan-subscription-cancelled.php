<?php
/**
 * Subscription Cancelled Email
 *
 * An email is sent to admin when a subscription is get cancelled by the vendor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! $vendor ) {
    return;
}

do_action( 'woocommerce_email_header', $email_heading, $email );

printf( '<p>%s</p>', __( 'Hello there', 'dokan' ) );

printf( "<p>%s</p>", __( "A subscription has been cancelled by {$vendor->get_store_name()}", "dokan" ) );

if ( $subscription ) {
    printf( '<p>%s</p>', __( 'Subscription Details:', 'dokan' ) );

    printf( '<p>%s</p>', __( 'Subscription Pack: ' . $subscription->get_package_title(), 'dokan' ) );
    printf( '<p>%s</p>', __( 'Price : ' . wc_price( $subscription->get_price() ) , 'dokan' ) );
}

echo '<hr>';

do_action( 'woocommerce_email_footer', $email );
