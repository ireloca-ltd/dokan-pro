<?php
/**
 * Customer booking confirmed email
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

echo "= " . $email_heading . " =\n\n";

echo __( 'The following booking has been cancelled by the customer. The details of the cancelled booking can be found below.', 'dokan' ) . "\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo sprintf( __( 'Booked: %s', 'dokan'), $booking->get_product()->get_title() ) . "\n";
echo sprintf( __( 'Booking ID: %s', 'dokan'), $booking->get_id() ) . "\n";

if ( $booking->has_resources() && ( $resource = $booking->get_resource() ) ) {
	echo sprintf( __( 'Booking Type: %s', 'dokan'), $resource->post_title ) . "\n";
}

echo sprintf( __( 'Booking Start Date: %s', 'dokan'), $booking->get_start_date() ) . "\n";
echo sprintf( __( 'Booking End Date: %s', 'dokan'), $booking->get_end_date() ) . "\n";

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

echo make_clickable( sprintf( __( 'You can view and edit this booking in the dashboard here: %s', 'dokan' ), admin_url( 'post.php?post=' . $booking->get_id() . '&action=edit' ) ) );

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
