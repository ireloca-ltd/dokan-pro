<?php
/**
 * Vendor enable email to vendors.
 *
 * An email sent to the vendor(s) when a he or she is enabled by the admin
 *
 * @class       Dokan_Email_Vendor_Enable
 * @version     2.7.6
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . $email_heading . " =\n\n";
?>

------------------------------------------------------------

<?php printf( __( 'Hello %s', 'dokan' ), $data['display_name'] ); echo " \n\n";  ?>

------------------------------------------------------------

<p>
    <?php _e( 'Sorry, your vendor account is deactivated.', 'dokan' ); ?>
</p>
<p>
    <?php _e( 'You can\'t sell or upload product. To activate your account please contact with the admin.', 'dokan' ); ?>
</p>

<?php

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
