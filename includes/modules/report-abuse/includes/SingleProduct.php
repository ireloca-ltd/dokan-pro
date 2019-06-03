<?php

namespace DokanPro\ReportAbuse;

class SingleProduct {

    /**
     * Class constructor
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function __construct() {
        add_action( 'woocommerce_single_product_summary', [ self::class, 'add_report_button' ], 100 );
        add_action( 'wp_enqueue_scripts', [ self::class, 'enqueue_scripts' ] );
    }

    /**
     * Add report button
     *
     * @since 2.9.8
     *
     * @return void
     */
    public static function add_report_button() {
        $label = apply_filters( 'dokan_report_abuse_button_label', esc_html__( 'Report Abuse', 'dokan' ) );

        $args = [
            'label' => $label,
        ];

        dokan_report_abuse_template( 'report-button', $args );
    }

    /**
     * Enqueue scripts
     *
     * @since 2.9.8
     *
     * @return void
     */
    public static function enqueue_scripts() {
        if ( is_product() ) {
            $product = wc_get_product();

            wp_enqueue_script( 'dokan-report-abuse', DOKAN_REPORT_ABUSE_ASSETS . '/js/dokan-report-abuse.js', [ 'jquery', 'dokan-login-form-popup' ], DOKAN_PRO_PLUGIN_VERSION, true );

            $options = get_option( 'dokan_report_abuse', [] );

            wp_localize_script( 'dokan-report-abuse', 'dokanReportAbuse', array_merge( $options, [
                'is_user_logged_in' => is_user_logged_in(),
                'nonce'             => wp_create_nonce( 'dokan_report_abuse' ),
                'product_id'        => $product->get_id(),
            ] ) );
        }
    }
}
