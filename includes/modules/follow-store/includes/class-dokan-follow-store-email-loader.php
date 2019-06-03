<?php

class Dokan_Follow_Store_Email_Loader {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function __construct() {
        add_filter( 'dokan_email_classes', array( $this, 'add_email_class' ) );
        add_filter( 'dokan_email_list', array( $this, 'add_email_template_file' ) );
        add_filter( 'dokan_email_actions', array( $this, 'add_email_action' ) );
    }

    /**
     * Add email class
     *
     * @since 1.0.0
     *
     * @param array $wc_emails
     *
     * @return array
     */
    public function add_email_class( $wc_emails ) {
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-email.php';
        require_once DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-vendor-email.php';

        $wc_emails['Dokan_Follow_Store_Email']        = new Dokan_Follow_Store_Email();
        $wc_emails['Dokan_Follow_Store_Vendor_Email'] = new Dokan_Follow_Store_Vendor_Email();

        return $wc_emails;
    }

    /**
     * Add email template
     *
     * @since 1.0.0
     *
     * @param array $template_files
     *
     * @return array
     */
    public function add_email_template_file( $template_files ) {
        $template_files[] = 'follow-store-updates-email-html.php';
        $template_files[] = 'follow-store-vendor-email-html.php';

        return $template_files;
    }

    /**
     * Add email action
     *
     * @since 1.0.0
     *
     * @param array $actions
     *
     * @return array
     */
    public function add_email_action( $actions ) {
        $actions[] = 'dokan_follow_store_send_update_email';
        $actions[] = 'dokan_follow_store_toggle_status';

        return $actions;
    }
}
