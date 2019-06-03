<?php

namespace DokanPro\ReportAbuse;

class AdminSettings {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_filter( 'dokan_settings_sections', [ $this, 'add_settings_section' ] );
        add_filter( 'dokan_settings_fields', [ $this, 'add_settings_fields'] );
    }

    /**
     * Add admin settings section
     *
     * @since 1.0.0
     *
     * @param array $sections
     *
     * @return array
     */
    public function add_settings_section( $sections ) {
        $sections['dokan_report_abuse'] = [
            'id'    => 'dokan_report_abuse',
            'title' => __( 'Product Report Abuse', 'dokan' ),
            'icon'  => 'dashicons-flag'
        ];

        return $sections;
    }

    /**
     * Add admin settings fields
     *
     * @since 1.0.0
     *
     * @param array $settings_fields
     *
     * @return array
     */
    public function add_settings_fields( $settings_fields ) {
        $settings_fields['dokan_report_abuse'] = [
            'reported_by_logged_in_users_only' => [
                'name'    => 'reported_by_logged_in_users_only',
                'label'   => __( 'Reported by', 'dokan' ),
                'desc'    => __( 'Only logged-in users can report', 'dokan' ),
                'type'    => 'checkbox',
                'default' => 'off',
            ],

            'abuse_reasons' => [
                'name'    => 'abuse_reasons',
                'label'   => __( 'Reasons for Abuse Report', 'dokan' ),
                'type'    => 'repeatable',
                'desc'  => __( 'Add your reasons whatever you want.', 'dokan' )
            ],
        ];

        return $settings_fields;
    }
}
