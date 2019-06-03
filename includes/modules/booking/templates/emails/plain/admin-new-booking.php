<?php
/**
 * Admin new booking email
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " . $email_heading . " =\n\n";

if ( wc_booking_order_requires_confirmation( $booking->get_order() ) && $booking->get_status() == 'pending-confirmation' ) {
	$opening_paragraph = __( 'A booking has been made by %s and is awaiting your approval. The details of this booking are as follows:', 'dokan' );
} else {
	$opening_paragraph = __( 'A new booking has been made by %s. The details of this booking are as follows:', 'dokan' );
}

if ( $booking->get_order() && $booking->get_order()->get_billing_first_name() && $booking->get_order()->get_billing_last_name() ) {
	echo sprintf( $opening_paragraph, $booking->get_order()->get_billing_first_name() . ' ' . $booking->get_order()->get_billing_last_name() ) . "\n\n";
}

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo sprintf( __( 'Booked: %s', 'dokan' ), $booking->get_product()->get_title() ) . "\n";
echo sprintf( __( 'Booking ID: %s', 'dokan' ), $booking->get_id() ) . "\n";

if ( $booking->has_resources() && ( $resource = $booking->get_resource() ) ) {
	echo sprintf( __( 'Booking Type: %s', 'dokan'), $resource->post_title ) . "\n";
}

echo sprintf( __( 'Booking Start Date: %s', 'dokan' ), $booking->get_start_date() ) . "\n";
echo sprintf( __( 'Booking End Date: %s', 'dokan' ), $booking->get_end_date() ) . "\n";

if ( $booking->has_persons() ) {
	foreach ( $booking->get_persons() as $id => $qty ) {
		if ( 0 === $qty ) {
			continue;
		}

		$person_type = ( 0 < $id ) ? get_the_title( $id ) : __( 'Person(s)', 'dokan' );
		echo sprintf( __( '%s: %d', 'dokan'), $person_type, $qty ) . "\n";
	}
}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

if ( wc_booking_order_requires_confirmation( $booking->get_order() ) && $booking->get_status() == 'pending-confirmation' ) {
	echo __( 'This booking is awaiting your approval. Please check it and inform the customer if the date is available or not.', 'dokan' ) . "\n\n";
}

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
