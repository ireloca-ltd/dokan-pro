<?php
/**
* Vendor enable email to vendors.
*
* An email sent to the vendor(s) when a he or she is enabled by the admin
*
* @class    Dokan_Email_Vendor_Enable
* @version  2.7.6
*
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p>
    <?php printf( __( 'Congratulations %s!', 'dokan' ), $data['display_name'] ); ?>
</p>
<p>
    <?php _e( 'Your vendor account is activated', 'dokan' ); ?>
</p>
<p>
    <?php echo sprintf( __( 'You can <a href="%s" target="_blank">login here</a> ', 'dokan' ), wc_get_page_permalink( 'myaccount' ) ) ; ?>
</p>
<?php
do_action( 'woocommerce_email_footer', $email );
