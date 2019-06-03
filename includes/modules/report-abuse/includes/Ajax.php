<?php

namespace DokanPro\ReportAbuse;

class Ajax {

    /**
     * Class constructor
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function __construct() {
        add_filter( 'dokan_ajax_login_user_response', [ self::class, 'add_nonce_to_ajax_reponse' ] );

        add_action( 'wp_ajax_nopriv_dokan_report_abuse_get_form', [ self::class, 'get_form' ] );
        add_action( 'wp_ajax_dokan_report_abuse_get_form', [ self::class, 'get_form' ] );
        add_action( 'wp_ajax_nopriv_dokan_report_abuse_submit_form', [ self::class, 'submit_form' ] );
        add_action( 'wp_ajax_dokan_report_abuse_submit_form', [ self::class, 'submit_form' ] );
    }

    /**
     * Add nonce to login form popup response
     *
     * @since 2.9.8
     *
     * @param array $response
     *
     * @return array
     */
    public static function add_nonce_to_ajax_reponse( $response ) {
        $response['dokan_report_abuse_nonce'] = wp_create_nonce( 'dokan_report_abuse' );
        return $response;
    }

    /**
     * Get report form
     *
     * @since 2.9.8
     *
     * @return void
     */
    public static function get_form() {
        check_ajax_referer( 'dokan_report_abuse' );

        ob_start();
        dokan_report_abuse_template( 'report-form-popup' );
        $popup_html = ob_get_clean();

        wp_send_json_success( $popup_html );
    }

    /**
     * Submit report form
     *
     * @since 2.9.8
     *
     * @return void
     */
    public static function submit_form() {
        check_ajax_referer( 'dokan_report_abuse' );

        if ( empty( $_POST['form_data'] ) ) {
            wp_send_json_error( [
                'message' => esc_html__( 'Missing form_data.', 'dokan' ),
            ], 400 );
        }

        $args = wp_parse_args( $_POST['form_data'], [
            'reason'         => '',
            'product_id'     => 0,
            'customer_name'  => '',
            'customer_email' => '',
            'description'    => '',
        ] );

        $customer_id = get_current_user_id();

        if ( $customer_id ) {
            $args['customer_id'] = $customer_id;
        }

        $report = dokan_report_abuse_create_report( $args );

        if ( is_wp_error( $report ) ) {
            wp_send_json_error( [
                'message' => $report->get_error_message(),
            ], 400 );
        }

        // Call WC_Emails once
        wc()->mailer();

        do_action( 'dokan_report_abuse_send_admin_email', $report );

        $response = [
            'message' => esc_html__( 'Your report has been submitted. Thank you for your response.', 'dokan' ),
            'report'  => $report,
        ];

        wp_send_json_success( $response, 200 );
    }
}
