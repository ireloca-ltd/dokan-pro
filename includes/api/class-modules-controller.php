<?php

/**
 * REST API Modules controller
 *
 * Handles requests to the /admin/modules endpoint.
 *
 * @author   Dokan
 * @category API
 * @package  Dokan/API
 * @since    2.8
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Dokan_REST_Modules_Controller extends Dokan_REST_Admin_Controller {

    /**
     * Route name
     *
     * @var string
     */
    protected $base = 'modules';

    /**
     * Register all routes related with modules
     *
     * @return void
     */
    public function register_routes() {

        register_rest_route( $this->namespace, '/' . $this->base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_modules' ),
                'permission_callback' => array( $this, 'check_permission' ),
                'args'                =>  array(
                    'status' => array(
                        'description' => __( 'Module Status', 'dokan' ),
                        'required'    => false,
                        'type'        => 'string',
                        'default'     => 'all',
                        'enum'        => array(
                            'all', 'active', 'inactive'
                        )
                    ),
                ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/activate', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'activate_modules' ),
                'permission_callback' => array( $this, 'check_permission' ),
                'args'                =>  $this->activation_request_args(),
            )
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/deactivate', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'deactivate_modules' ),
                'permission_callback' => array( $this, 'check_permission' ),
                'args'                =>  $this->activation_request_args(),
            )
        ) );
    }

    /**
     * Activation/deactivation request args
     *
     * @return array
     */
    public function activation_request_args() {
        return array(
            'module' => array(
                'description' => __( 'Basename of the module as array', 'dokan' ),
                'required'    => true,
                'type'        => 'array',
                'items'       => array(
                    'type' => 'string'
                )
            ),
        );
    }

    /**
     * Fetch all modules
     *
     * @param WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function get_modules( $request ) {
        $modules      = dokan_pro_get_modules();
        $actives      = dokan_pro_get_active_modules();
        $result       = array();
        $active_count = 0;
        $status       = $request['status'];

        foreach ( $modules as $key => $module ) {
            $is_active = in_array( $key, $actives );
            $item      = array_merge( $module, array( 'slug' => $key, 'id'=> $key, 'active' => $is_active ) );

            if ( $is_active ) {
                $active_count++;
            }

            switch ($status) {
                case 'active':
                    if ( $is_active ) {
                        $result[] = $item;
                    }
                    break;

                case 'inactive':
                    if ( ! $is_active ) {
                        $result[] = $item;
                    }
                    break;

                default:
                    $result[] = $item;
                    break;
            }
        }

        if (  ! empty( $request['orderby'] ) ) {
            if ( 'asc' == $request['order'] ) {
                usort( $result, 'dokan_module_short_by_name_asc' );
            } else {
                usort( $result, 'dokan_module_short_by_name_desc' );
            }
        }

        $response = rest_ensure_response( $result );

        $response->header( 'X-WP-Total', count( $modules ) );
        $response->header( 'X-WP-Active', $active_count );

        return $response;
    }

    /**
     * Check if module file exists
     *
     * Blindly passing an invalid file causes warning
     *
     * @param  string $module
     *
     * @return boolean|WP_Error
     */
    private function module_file_exists( $module ) {
        $module_root = DOKAN_PRO_INC . '/modules';

        if ( ! file_exists( $module_root . '/' . $module ) ) {
            return new WP_Error( 'not-exists', __( 'Module doesn\'t exists.', 'dokan' ) );
        }

        return true;
    }

    /**
     * Activate modules
     *
     * @param  WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function activate_modules( $request ) {
        $modules        = $request['module'];
        $activate_count = 0;

        foreach ( $modules as $module ) {
            $file_exists = $this->module_file_exists( $module );

            if ( true != $file_exists ) {
                continue;
            }

            $activated = dokan_pro_activate_module( $module );

            if ( true === $activated ) {
                $activate_count++;
            }
        }

        return rest_ensure_response( array(
            'success'   => true,
            'activated' => $activate_count
        ) );
    }

    /**
     * Deactivate modules
     *
     * @param  WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function deactivate_modules( $request ) {
        $modules        = $request['module'];
        $deactivate_count = 0;

        foreach ( $modules as $module ) {
            $file_exists = $this->module_file_exists( $module );

            if ( true != $file_exists ) {
                continue;
            }

            $deactivated = dokan_pro_deactivate_module( $module );

            if ( true === $deactivated ) {
                $deactivate_count++;
            }
        }

        return rest_ensure_response( array(
            'success'     => true,
            'deactivated' => $deactivate_count
        ) );
    }
}
