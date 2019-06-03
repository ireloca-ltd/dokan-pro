<?php

namespace DokanPro\ReportAbuse;

use WC_Email;

class AdminEmail extends WC_Email {

    /**
     * The report to be emailed
     *
     * @var null|object
     */
    protected $report = null;

    /**
     * Class constructor
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function __construct() {
        $this->id             = 'dokan_report_abuse_admin_email';
        $this->title          = esc_html__( 'Dokan Report Abuse', 'dokan' );
        $this->description    = esc_html__( 'Send abuse report notification to admin.', 'dokan' );
        $this->template_html  = 'report-abuse-admin-email-html.php';
        $this->template_base  = DOKAN_REPORT_ABUSE_VIEWS . '/';
        $this->placeholders   = [
            '{site_title}'    => $this->get_blogname(),
        ];

        // Call parent constructor
        parent::__construct();

        // Set recipient
        $this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );

        // Set the email content type text/html
        $this->email_type = 'html';

        // Hook the trigger method
        add_action( 'dokan_report_abuse_send_admin_email', [ $this, 'trigger' ] );
    }

    /**
     * Email settings
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function init_form_fields() {
        $available_placeholders = '{site_title}';

        $this->form_fields = [
            'enabled' => [
                'title'         => esc_html__( 'Enable/Disable', 'dokan' ),
                'type'          => 'checkbox',
                'label'         => esc_html__( 'Enable this email', 'dokan' ),
                'default'       => 'yes',
            ],

            'subject' => [
                'title'         => esc_html__( 'Subject', 'dokan' ),
                'type'          => 'text',
                'desc_tip'      => true,
                'description'   => sprintf( esc_html__( 'Available placeholders: %s', 'dokan' ), $available_placeholders ),
                'placeholder'   => $this->get_default_subject(),
                'default'       => $this->get_default_subject(),
            ],

            'heading' => [
                'title'         => esc_html__( 'Email heading', 'dokan' ),
                'type'          => 'text',
                'desc_tip'      => true,
                'description'   => sprintf( esc_html__( 'Available placeholders: %s', 'dokan' ), $available_placeholders ),
                'placeholder'   => $this->get_default_heading(),
                'default'       => $this->get_default_heading(),
            ],
        ];
    }

    /**
     * Email default subject
     *
     * @since 2.9.8
     *
     * @return string
     */
    public function get_default_subject() {
        return sprintf(
            esc_html__( '%s A new abuse report has been submitted', 'dokan' ),
            '[{site_title}]'
        );
    }

    /**
     * Email default heading
     *
     * @since 2.9.8
     *
     * @return string
     */
    public function get_default_heading() {
        return esc_html__( 'Product Abuse Report', 'dokan' );
    }

    /**
     * Send email
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function trigger( $report ) {
        $this->setup_locale();

        if ( ! $this->is_enabled() && ! $this->get_recipient() ) {
            return;
        }

        $this->report = $report;

        $sent = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

        $this->restore_locale();
    }

    /**
     * Email content
     *
     * @since 2.9.8
     *
     * @return string
     */
    public function get_content() {
        $product = wc_get_product( $this->report->product_id );
        $vendor  = dokan_get_vendor( $this->report->vendor_id );

        ob_start();
        wc_get_template( $this->template_html, [
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'         => $this,
            'data'          => [
                'product_title'  => $product->get_title(),
                'product_link'   => admin_url( sprintf( 'post.php?post=%d&action=edit', $product->get_id() ) ),
                'vendor_name'    => $vendor->get_shop_name(),
                'vendor_link'    => admin_url( sprintf( 'user-edit.php?user_id=%d', $vendor->get_id() ) ),
                'reason'         => $this->report->reason,
                'description'    => $this->report->description,
                'customer'       => $this->report->customer_id ? new \WC_Customer( $this->report->customer_id ) : 0,
                'customer_name'  => $this->report->customer_name,
                'customer_email' => $this->report->customer_email,
                'reported_at'    => strtotime( $this->report->reported_at ),
                'report'         => $this->report,
            ],
        ], 'dokan/', $this->template_base );
        return ob_get_clean();
    }
}
