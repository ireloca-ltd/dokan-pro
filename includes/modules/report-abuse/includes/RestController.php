<?php

namespace DokanPro\ReportAbuse;

use WP_REST_Controller;
use WP_REST_Server;

class RestController extends WP_REST_Controller {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'dokan/v1';

    /**
     * Route base.
     *
     * @var string
     */
    protected $rest_base = 'abuse-reports';

    /**
     * Register routes
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_items' ],
                'permission_callback' => [ $this, 'is_dokandar' ]
            ]
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/(?P<id>[\d]+)', [
            'args' => [
                'id' => [
                    'description' => __( 'Abuse report id', 'dokan' ),
                    'type'        => 'integer',
                ],
            ],
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [ $this, 'delete_item' ],
                'permission_callback' => [ $this, 'is_dokandar' ],
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/batch', [
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [ $this, 'delete_items' ],
                'permission_callback' => [ $this, 'is_dokandar' ]
            ]
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/abuse-reasons', [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_abuse_reasons' ],
                'permission_callback' => [ $this, 'is_dokandar' ]
            ]
        ] );
    }

    /**
     * Permission callback
     *
     * @since 2.9.8
     *
     * @return bool
     */
    public function is_dokandar() {
        return current_user_can( 'dokandar' );
    }

    /**
     * Get reports
     *
     * @since 2.9.8
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function get_items( $request ) {
        global $wpdb;

        // These defaults should be replaced by schema
        $per_page   = 20;
        $page       = ! empty( $request['page'] ) ? $request['page'] : 1;
        $reason     = ! empty( $request['reason'] ) ? $request['reason'] : '';
        $product_id = ! empty( $request['product_id'] ) ? $request['product_id'] : 0;
        $vendor_id  = ! empty( $request['vendor_id'] ) ? $request['vendor_id'] : 0;

        $args =  [
            'page'       => $page,
            'reason'     => $reason,
            'product_id' => $product_id,
            'vendor_id'  => $vendor_id,
        ];

        $data     = dokan_report_abuse_get_reports( $args );
        $response = rest_ensure_response( $data );

        $args['count'] = true;
        $total         = dokan_report_abuse_get_reports( $args );
        $response->header( 'X-Dokan-AbuseReports-Total', $total );

        $max_pages = ceil( $total / $per_page );
        $response->header( 'X-Dokan-AbuseReports-TotalPages', (int) $max_pages );

        return $response;
    }

    /**
     * Get abuse reasons
     *
     * @since 2.9.8
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function get_abuse_reasons( $request ) {
        $option = dokan_report_abuse_get_option();

        $response = rest_ensure_response( $option['abuse_reasons'] );

        return $response;
    }

    /**
     * Delete report
     *
     * @since 2.9.8
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function delete_item( $request ) {
        $report = dokan_report_abuse_get_reports( [ 'id' => $request['id'] ] );

        if ( empty( $report ) ) {
            return new \WP_Error( 'report_not_found', __( 'Report not found', 'dokan' ) );
        }

        dokan_report_abuse_delete_reports( [ $report['id'] ] );

        return rest_ensure_response( $report );
    }

    /**
     * Delete reports in bulk
     *
     * @since 2.9.8
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function delete_items( $request ) {
        $ids = $request['items'];

        if ( ! is_array( $ids ) ) {
            return new \WP_Error( 'invalid_data', __( 'items must be an array of report ids', 'dokan' ) );
        }

        $reports = dokan_report_abuse_get_reports( [ 'ids' => $ids ] );

        $ids = array_map( function ( $report ) {
            return $report['id'];
        }, $reports );

        dokan_report_abuse_delete_reports( $ids );

        return rest_ensure_response( $reports );
    }
}
