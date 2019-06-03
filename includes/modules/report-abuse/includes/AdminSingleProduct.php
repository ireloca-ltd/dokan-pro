<?php

namespace DokanPro\ReportAbuse;

class AdminSingleProduct {

    /**
     * Class constructor
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function __construct() {
        add_action( 'add_meta_boxes', [ self::class, 'add_abuse_report_meta_box' ] );
    }

    /**
     * Add metabox in product editing page
     *
     * @since 2.9.8
     *
     * @return void
     */
    public static function add_abuse_report_meta_box() {
        add_meta_box( 'dokan_report_abuse_reports', __( 'Abuse Reports', 'dokan' ), [ self::class, 'meta_box' ], 'product', 'normal', 'core' );
    }

    /**
     * Abuse Reports metabox
     *
     * @since 2.9.8
     *
     * @param \WP_Post $post
     *
     * @return void
     */
    public static function meta_box( $post ) {
        $reports = dokan_report_abuse_get_reports( [
            'product_id' => $post->ID,
        ] );

        dokan_report_abuse_template( 'report-abuse-admin-single-product', [
            'reports'     => $reports,
            'date_format' => get_option( 'date_format', 'F j, Y' ),
            'time_format' => get_option( 'time_format', 'g:i a' ),
        ] );

        wp_enqueue_style( 'dokan-report-abuse-admin-single-product', DOKAN_REPORT_ABUSE_ASSETS . '/css/dokan-report-abuse-admin-single-product.css', ['dokan-fontawesome'], DOKAN_PRO_PLUGIN_VERSION );
        wp_enqueue_script( 'dokan-report-abuse-admin-single-product', DOKAN_REPORT_ABUSE_ASSETS . '/js/dokan-report-abuse-admin-single-product.js', ['jquery'], DOKAN_PRO_PLUGIN_VERSION, true );

        wp_localize_script( 'dokan-report-abuse-admin-single-product', 'dokanReportAbuse', [
            'rest' => [
                'root'    => esc_url_raw( get_rest_url() ),
                'nonce'   => wp_create_nonce( 'wp_rest' ),
            ],
            'i18n' => [
                'delete'              => esc_html__( 'Delete', 'dokan' ),
                'deleting'            => esc_html__( 'Deleting', 'dokan' ),
                'deletedSuccessfully' => esc_html__( 'Report successfully deleted.', 'dokan' ),
                'confirmDelete'       => esc_html__( 'Are you sure you want to delete this report', 'dokan' ),
            ]
        ] );
    }
}
