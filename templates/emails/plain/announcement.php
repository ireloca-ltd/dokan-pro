<?php
/**
 * Announcement email to vendors.
 *
 * An email sent to the vendor(s) when a announcement is created by admin.
 *
 * @class       Dokan_Email_Announcement
 * @version     2.6.8
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . $email_heading . " =\n\n";
?>

------------------------------------------------------------

<?php echo $data['message']; echo " \n\n";  ?>

------------------------------------------------------------


<?php _e( 'You can check this announcement in your dashboard ', 'dokan' );  echo " \n\n";?> .

<?php

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );