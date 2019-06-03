<?php
/**
 * Pending Updated Product Email. (plain text)
 *
 * An email sent to the admin when a new Product is updated by vendor and in pending status.
 *
 * @class       Dokan_Email_New_Product_Pending
 * @version     2.6.6
 * 
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
echo "= " . $email_heading . " =\n\n";
?>

<?php _e( 'Hello there,', 'dokan' );  echo " \n\n";?>

<?php _e( 'A product has been updated in your site.', 'dokan' ); echo " \n\n"; ?>

<?php _e( 'Summary of the product:', 'dokan' );  ?>

<?php echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n"; ?>

<?php _e( 'Title: '. $data['title'], 'dokan' ); echo " \n"; ?> 
<?php _e( 'Price: '. wc_price( $data['price'] ), 'dokan' ); echo " \n"; ?>
<?php _e( 'Vendor: '. $data['seller_name'], 'dokan' ); echo " \n"; ?>
<?php _e( 'Category: '. $data['category'], 'dokan' ); echo " \n"; ?>

<?php _e( 'The product is currently in "pending" state.', 'dokan' ); echo " \n";?>

<?php

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
