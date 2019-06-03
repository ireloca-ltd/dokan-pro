<?php

/**
* Wholesale related API's
*/
class Dokan_REST_Wholesale_Controller extends Dokan_REST_Controller {

    /**
     * Endpoint namespace
     *
     * @var string
     */
    protected $namespace = 'dokan/v1';

    /**
     * Route name
     *
     * @var string
     */
    protected $base = 'wholesale';

    /**
     * Register all routes releated with stores
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->base . '/register', array(
            array(
                'methods'  => WP_REST_Server::CREATABLE,
                'callback' => [ $this, 'create_wholesale_customer' ],
                'args'     => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), [
                    'id' => [
                        'description' => __( 'User ID.', 'dokan' ),
                        'required'    => true,
                        'type'        => 'string',
                    ],
                ] ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/customers', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_wholesale_customers' ],
                'args'                => $this->get_collection_params(),
                'permission_callback' => [ $this, 'permission_for_get_all' ]
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/customer/(?P<id>[\d]+)', array(
            'args' => [
                'id' => [
                    'description' => __( 'Unique identifier for the object.', 'dokan' ),
                    'type'        => 'integer',
                ],
                'status' => [
                    'description' => __( 'Status for wholesale customer', 'dokan' ),
                    'type'        => 'string',
                ]
            ],
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => [ $this, 'update_wholesale_customer_status' ],
                'permission_callback' => [ $this, 'permission_for_change_status' ]
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/customers/batch', array(
            array(
                'methods'  => WP_REST_Server::EDITABLE,
                'callback' => array( $this, 'batch_update' ),
                'permission_callback' => array( $this, 'permission_check_for_manageable_part' ),
            ),
        ) );
    }

    /**
     * Change wholesale customer status
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function permission_for_change_status() {
        return current_user_can( 'manage_woocommerce' );
    }

    /**
     * Change wholesale customer status
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function permission_for_get_all() {
        return current_user_can( 'manage_woocommerce' );
    }

    /**
     * Change wholesale customer status
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function permission_check_for_manageable_part() {
        return current_user_can( 'manage_woocommerce' );
    }

    /**
     * Get all wholesale customers
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_wholesale_customers( $request ) {
        $params    = $request->get_params();
        $customers = array();
        $defaults  = array(
            'role__in'   => array( 'seller', 'customer' ),
            'number'     => 10,
            'offset'     => 0,
            'orderby'    => 'registered',
            'order'      => 'ASC',
            'meta_query' => [
                [
                    'key'     => '_is_dokan_wholesale_customer',
                    'compare' => 'EXISTS'
                ]
            ],
        );

        $args = array(
            'number' => (int) $params['per_page'],
            'offset' => (int) ( $params['page'] - 1 ) * $params['per_page']
        );

        if ( ! empty( $params['search'] ) ) {
            $args['search']         = '*' . sanitize_text_field( ( $params['search'] ) ) . '*';
            $args['search_columns'] = array( 'user_login', 'user_email', 'display_name' );
        }

        if ( ! empty( $params['status'] ) ) {
            $args['status'] = sanitize_text_field( $params['status'] );
        }

        if ( ! empty( $params['orderby'] ) ) {
            $args['orderby'] = sanitize_sql_orderby( $params['orderby'] );
        }

        if ( ! empty( $params['order'] ) ) {
            $args['order'] = sanitize_text_field( $params['order'] );
        }

        $args   = dokan_parse_args( $args, $defaults );
        $status = ! empty( $args['status'] ) ? $args['status'] : '';

        if ( in_array( $status, array( 'active', 'deactive' ) ) ) {
            $args['meta_query'][] = [
                'key'     => '_dokan_wholesale_customer_status',
                'value'   => $status,
                'compare' => '='
            ];
        }

        unset( $args['status'] );

        $user_query  = new WP_User_Query( $args );
        $results     = $user_query->get_results();
        $total_users = $user_query->total_users;

        foreach ( $results as $key => $customer ) {
            $formatted_customer = $this->prepare_item_for_response( $customer, $request );
            $customers[]        = $this->prepare_response_for_collection( $formatted_customer );
        }

        $response = rest_ensure_response( $customers );
        $response = $this->format_collection_response( $response, $request, $total_users );

        return $response;
    }

    /**
     * Create wholesale customer
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function create_wholesale_customer( $request ) {
        if ( empty( $request['id'] ) ) {
            return new WP_Error( 'no-user', __( 'No user found', 'dokan' ), [ 'status' => 401 ] );
        }

        $user = new WP_User( $request['id'] );

        if ( ! $user->ID ) {
            return new WP_Error( 'no-user', __( 'No user found', 'dokan' ), [ 'status' => 401 ] );
        }

        if ( ! ( in_array( 'seller', $user->roles ) || in_array( 'customer', $user->roles ) ) ) {
            return new WP_Error( 'no-valid-role', __( 'Not a valid user for wholesale', 'dokan' ), [ 'status' => 401 ] );
        }

        $need_approval = dokan_get_option( 'need_approval_for_wholesale_customer', 'dokan_wholesale', 'yes' );

        if ( 'yes' == $need_approval ) {
            update_user_meta( $user->ID, '_is_dokan_wholesale_customer', 'yes' );
            update_user_meta( $user->ID, '_dokan_wholesale_customer_status', 'deactive' );
            $user->remove_cap( 'dokan_wholesale_customer' );
        } else {
            update_user_meta( $user->ID, '_is_dokan_wholesale_customer', 'yes' );
            update_user_meta( $user->ID, '_dokan_wholesale_customer_status', 'active' );
            $user->add_cap( 'dokan_wholesale_customer' );
        }

        return $this->prepare_item_for_response( $user, $request );
    }

    /**
     * Update status for wholesale customer
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update_wholesale_customer_status( $request ) {
        if ( empty( $request['id'] ) ) {
            return new WP_Error( 'no-user', __( 'No user found', 'dokan' ), [ 'status' => 401 ] );
        }

        if ( empty( $request['status'] ) ) {
            return new WP_Error( 'no-status', __( 'No status found for update', 'dokan' ), [ 'status' => 401 ] );
        }

        if ( ! in_array( $request['status'], [ 'activate', 'deactivate', 'delete' ] ) ) {
            return new WP_Error( 'invalid-status', __( 'Status are not valid', 'dokan' ), [ 'status' => 401 ] );
        }

        $user = new WP_User( $request['id'] );

        if ( ! $user->ID ) {
            return new WP_Error( 'no-user', __( 'No user found', 'dokan' ), [ 'status' => 401 ] );
        }

        if ( 'activate' == $request['status'] ) {
            update_user_meta( $user->ID, '_is_dokan_wholesale_customer', 'yes' );
            update_user_meta( $user->ID, '_dokan_wholesale_customer_status', 'active' );
            $user->add_cap( 'dokan_wholesale_customer' );
        }

        if ( 'deactivate' == $request['status'] ) {
            update_user_meta( $user->ID, '_is_dokan_wholesale_customer', 'no' );
            update_user_meta( $user->ID, '_dokan_wholesale_customer_status', 'deactive' );
            $user->remove_cap( 'dokan_wholesale_customer' );
        }

        if ( 'delete' == $request['status'] ) {
            delete_user_meta( $user->ID, '_is_dokan_wholesale_customer' );
            delete_user_meta( $user->ID, '_dokan_wholesale_customer_status' );
            $user->remove_cap( 'dokan_wholesale_customer' );
        }

        return $this->prepare_item_for_response( $user, $request );
    }

    /**
     * Batch udpate for vendor listing
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function batch_update( $request ) {
        $params = $request->get_params();

        if ( empty( $params ) ) {
            return new WP_Error( 'no_item_found', __( 'No data found for bulk updating', 'dokan' ), array( 'status' => 404 ) );
        }

        $allowed_status = array( 'activate', 'deactivate', 'delete' );
        $response       = array();

        foreach ( $params as $status => $value ) {
            if ( in_array( $status, $allowed_status ) ) {
                switch ( $status ) {
                    case 'activate':
                        foreach ( $value as $customer_id ) {
                            $user = get_user_by( 'id', $customer_id );
                            update_user_meta( $user->ID, '_is_dokan_wholesale_customer', 'yes' );
                            update_user_meta( $user->ID, '_dokan_wholesale_customer_status', 'active' );
                            $user->add_cap( 'dokan_wholesale_customer' );

                            $response['approved'][] = $customer_id;
                        }
                        break;

                    case 'deactivate':

                        foreach ( $value as $customer_id ) {
                            $user = get_user_by( 'id', $customer_id );
                            update_user_meta( $user->ID, '_is_dokan_wholesale_customer', 'no' );
                            update_user_meta( $user->ID, '_dokan_wholesale_customer_status', 'deactive' );
                            $user->remove_cap( 'dokan_wholesale_customer' );
                            $response['pending'][] = $customer_id;
                        }
                        break;

                    case 'delete':

                        foreach ( $value as $customer_id ) {
                            $user = get_user_by( 'id', $customer_id );
                            delete_user_meta( $user->ID, '_is_dokan_wholesale_customer' );
                            delete_user_meta( $user->ID, '_dokan_wholesale_customer_status' );
                            $user->remove_cap( 'dokan_wholesale_customer' );
                            $response['delete'][] = $customer_id;
                        }
                        break;
                }
            }
        }

        return $response;
    }

    /**
     * Transform Wholesale customer userdata
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function format_user_collections( $request ) {
        return apply_filters( 'dokan_wholesale_customer_data', [
            'id'               => $request->ID,
            'first_name'       => $request->first_name,
            'last_name'        => $request->last_name,
            'username'         => $request->user_login,
            'name'             => $request->display_name,
            'email'            => $request->user_email,
            'avatar'           => get_avatar_url( $request->user_email ),
            'url'              => $request->user_url,
            'role'             => implode( ', ', $request->roles ),
            'registerd_date'   => mysql_to_rfc3339( $request->user_registered ),
            'wholesale_status' => $request->has_cap( 'dokan_wholesale_customer' ) ? 'active' : 'deactive'
        ] );
    }

    /**
     * Prepare a single user output for response
     *
     * @param object $item
     * @param WP_REST_Request $request Request object.
     * @param array $additional_fields (optional)
     *
     * @return WP_REST_Response $response Response data.
     */
    public function prepare_item_for_response( $data, $request, $additional_fields = [] ) {
        $data     = $this->format_user_collections( $data );
        $all_data = array_merge( $data, apply_filters( 'dokan_rest_wholesale_customer_additional_fields', $additional_fields, $data, $request ) );
        $response = rest_ensure_response( $all_data );
        $response->add_links( $this->prepare_links( $all_data, $request ) );

        return apply_filters( 'dokan_rest_prepare_wholesale_customer_for_response', $response );
    }

    /**
     * Prepare links for the request.
     *
     * @param WC_Data         $object  Object data.
     * @param WP_REST_Request $request Request object.
     *
     * @return array                   Links for the given post.
     */
    protected function prepare_links( $object, $request ) {
        $links = array(
            'self' => array(
                'href' => rest_url( sprintf( '/%s/%s/customers/%d', $this->namespace, $this->base, $object['id'] ) ),
            ),
            'collection' => array(
                'href' => rest_url( sprintf( '/%s/%s/customers', $this->namespace, $this->base ) ),
            ),
        );

        return $links;
    }

    /**
     * Format item's collection for response
     *
     * @param  object $response
     * @param  object $request
     * @param  array $items
     * @param  int $total_items
     *
     * @return object
     */
    public function format_collection_response( $response, $request, $total_items ) {
        if ( $total_items === 0 ) {
            return $response;
        }

        // Store pagation values for headers then unset for count query.
        $per_page = (int) ( ! empty( $request['per_page'] ) ? $request['per_page'] : 20 );
        $page     = (int) ( ! empty( $request['page'] ) ? $request['page'] : 1 );

        if ( function_exists( 'dokan_wholesale_get_customer_status_count' ) && current_user_can( 'manage_options' ) ) {
            $counts = dokan_wholesale_get_customer_status_count();
            $response->header( 'X-Status-Deactive', (int) $counts['deactive'] );
            $response->header( 'X-Status-Active', (int) $counts['active'] );
            $response->header( 'X-Status-All', (int) $counts['total'] );
        }

        $response->header( 'X-WP-Total', (int) $total_items );

        $max_pages = ceil( $total_items / $per_page );

        $response->header( 'X-WP-TotalPages', (int) $max_pages );
        $base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s/customers', $this->namespace, $this->base ) ) );

        if ( $page > 1 ) {
            $prev_page = $page - 1;
            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }
            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }
        if ( $max_pages > $page ) {

            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }


}
