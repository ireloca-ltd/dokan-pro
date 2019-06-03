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

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<hr>
<p>
    <?php echo $data['message'] ?>
</p>
<hr>
<p>
    <?php echo sprintf( __( 'You can check this announcement in dashboard by clicking <a href="%s">here<a/>', 'dokan' ), $data['announcement_url'] ) ?> .
</p>
<?php
do_action( 'woocommerce_email_footer', $email );