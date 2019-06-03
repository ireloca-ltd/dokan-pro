<?php

namespace DokanPro\ReportAbuse;

class EmailLoader {

    /**
     * Class constructor
     *
     * @since 2.9.8
     *
     * @return array
     */
    public function __construct() {
        add_filter( 'dokan_email_classes', [ self::class, 'add_email_class' ] );
        add_filter( 'dokan_email_list', [ self::class, 'add_email_template_file' ] );
        add_filter( 'dokan_email_actions', [ self::class, 'add_email_action' ] );
    }

    /**
     * Add email class
     *
     * @since 2.9.8
     *
     * @param array $wc_emails
     *
     * @return array
     */
    public static function add_email_class( $wc_emails ) {
        require_once DOKAN_REPORT_ABUSE_INCLUDES . '/AdminEmail.php';

        $wc_emails['Dokan_Report_Abuse_Admin_Email'] = new AdminEmail();

        return $wc_emails;
    }

    /**
     * Add email template
     *
     * @since 2.9.8
     *
     * @param array $template_files
     *
     * @return array
     */
    public static function add_email_template_file( $template_files ) {
        $template_files[] = 'report-abuse-admin-email-html.php';

        return $template_files;
    }

    /**
     * Add email action
     *
     * @since 2.9.8
     *
     * @param array $actions
     *
     * @return array
     */
    public static function add_email_action( $actions ) {
        $actions[] = 'dokan_report_abuse_send_admin_email';

        return $actions;
    }
}
