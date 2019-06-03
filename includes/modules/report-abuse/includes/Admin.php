<?php

namespace DokanPro\ReportAbuse;

class Admin {

    /**
     * Class constructor
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function __construct() {
        add_action( 'dokan_admin_menu', [ self::class, 'add_admin_menu' ], 10, 2 );
        add_filter( 'dokan-admin-routes', [ self::class, 'add_admin_route' ] );
        add_action( 'dokan-vue-admin-scripts', [ self::class, 'enqueue_admin_script' ] );
    }

    /**
     * Add Dokan submenu
     *
     * @since 2.9.8
     *
     * @param string $capability
     * @param int    $menu_position
     *
     * @return void
     */
    public static function add_admin_menu( $capability, $menu_position ) {
        global $submenu;

        $title = esc_html__( 'Abuse Reports', 'dokan' );
        $slug  = 'dokan';

        $submenu[ $slug ][] = [ $title, $capability, 'admin.php?page=' . $slug . '#/abuse-reports' ];
    }

    /**
     * Add admin page Route
     *
     * @since 2.9.8
     *
     * @param array $routes
     *
     * @return array
     */
    public static function add_admin_route( $routes ) {
        $routes[] = [
            'path'      => '/abuse-reports',
            'name'      => 'AbuseReports',
            'component' => 'AbuseReports'
        ];

        $routes[] = [
            'path'      => '/abuse-reports/:id',
            'name'      => 'AbuseReportsSingle',
            'component' => 'AbuseReportsSingle'
        ];

        return $routes;
    }

    /**
     * Enqueue admin script
     *
     * @since 2.9.8
     *
     * @return void
     */
    public static function enqueue_admin_script() {
        wp_enqueue_style( 'woocommerce_select2', WC()->plugin_url() . '/assets/css/select2.css', [], WC_VERSION );
        wp_enqueue_script(
            'dokan-report-abuse-admin-vue',
            DOKAN_REPORT_ABUSE_ASSETS . '/js/dokan-report-abuse-admin.js',
            [ 'jquery', 'dokan-vue-vendor', 'dokan-vue-bootstrap', 'selectWoo' ],
            DOKAN_PRO_PLUGIN_VERSION,
            true
        );
    }
}
