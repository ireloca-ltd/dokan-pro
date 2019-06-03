<?php

use DokanPro\Modules\Subscription\Helper;

/**
* Subscription API controller
*
* @since 2.8.0
*
* @package dokan
*/
class Dokan_REST_Subscription_Controller extends Dokan_REST_Controller {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'dokan/v1';

    /**
     * Route name
     *
     * @var string
     */
    protected $base = 'subscription';



    /**
     * Register all routes related with coupons
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [ $this, 'get_subscription' ],
                'permission_callback' => [ $this, 'check_permission' ],
                'args'                => $this->get_collection_params(),
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/', [
            [
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => [ $this, 'cancel_subscription' ],
                'permission_callback' => [ $this, 'check_permission' ],
                'args'                => $this->get_collection_params(),
            ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->base . '/batch', [
            [
                'methods'  => WP_REST_Server::DELETABLE,
                'callback' => [ $this, 'batch_cancel' ],
                'permission_callback' => [ $this, 'check_permission' ],
            ],
        ] );
    }

    /**
     * Check permission
     *
     * @since 2.9.3
     *
     * @return boolean
     */
    public function check_permission() {
        return current_user_can( 'manage_options' );
    }

    /**
     * Get all subscription
     *
     * @since 2.9.3
     *
     * @return object
     */
    public function get_subscription( $request ) {
        $params = $request->get_params();

        $args = apply_filters( 'dokan_get_subscription_args', [
            'role' => 'seller',
            'meta_query' => [
                [
                    'key'   => 'can_post_product',
                    'value' => '1'
                ],
                [
                    'key'   => 'dokan_enable_selling',
                    'value' => 'yes'
                ],
            ]
        ] );

        if ( ! empty( $params['per_page'] ) ) {
            $args['number'] = $params['per_page'];
        }

        if ( ! empty( $params['offset'] ) ) {
            $args['offset'] = $params['offset'];
        }

        if ( ! empty( $params['orderby'] ) ) {
            $args['orderby'] = $params['orderby'];
        }

        if ( ! empty( $params['order'] ) ) {
            $args['order'] = $params['order'];
        }

        if ( ! empty( $params['search'] ) ) {
            $args['search'] = $params['search'];
        }

        if ( ! empty( $params['paged'] ) ) {
            $args['paged'] = $params['paged'];
        }

        $user_query  = new WP_User_Query( $args );
        $users       = $user_query->get_results();
        $total_users = $user_query->get_total();

        if ( ! $users ) {
            return new WP_Error( 'no_subscription', __( 'No subscription found.', 'dokan' ), [ 'status' => 200 ] );
        }

        $data = [];

        foreach ( $users as $user ) {
            $data[] = $this->prepare_item_for_response( $user, $request );
        }

        $response = rest_ensure_response( $data );
        $response = $this->format_collection_response( $response, $request, $total_users );

        return $response;
    }

    /**
     * Cancel vendor subscription
     *
     * @param  array $request
     *
     * @return object
     */
    public function cancel_subscription( $request ) {
        $user_id = (int) $request->get_param( 'id' );
        $user    = new WP_User( $user_id );
        $status  = get_terms( 'shop_order_status' );

        if ( is_wp_error( $status ) ) {
            register_taxonomy( 'shop_order_status', array( 'shop_order' ), array( 'rewrite' => false ) );
        }

        $order_id = get_user_meta( $user_id, 'product_order_id', true );

        if ( ! $order_id ) {
            return new WP_Error( 'no_subscription', __( 'No subscription is found to be deleted.', 'dokan' ), [ 'status' => 200 ] );
        }

        if ( get_user_meta( $user_id, '_customer_recurring_subscription', true ) == 'active' ) {
            Helper::log( 'Subscription cancel check: Admin has canceled Subscription of User #' . $user_id . ' on order #' . $order_id );

            do_action( 'dps_cancel_recurring_subscription', $order_id, $user_id );
        } else {
            Helper::log( 'Subscription cancel check: Admin has canceled Subscription of User #' . $user_id . ' on order #' . $order_id );

            Helper::delete_subscription_pack( $user_id, $order_id );
        }

        $response = $this->prepare_item_for_response( $user, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }

    public function batch_cancel( $request ) {
        $params = $request->get_params();

        if ( ! empty( $params['cancel'] ) ) {
            $user_ids = $params['cancel'];
        }

        if ( ! $user_ids ) {
            return new WP_Error( 'no_subscription', __( 'No subscription is found to be deleted.', 'dokan' ), [ 'status' => 200 ] );
        }

        $status = get_terms( 'shop_order_status' );

        if ( is_wp_error( $status ) ) {
            register_taxonomy( 'shop_order_status', array( 'shop_order' ), array( 'rewrite' => false ) );
        }

        foreach ( $user_ids as $user_id ) {
            $order_id = get_user_meta( $user_id, 'product_order_id', true );

            if ( ! $order_id ) {
                return new WP_Error( 'no_subscription', __( 'No subscription is found to be deleted.', 'dokan' ), [ 'status' => 200 ] );
            }

            if ( get_user_meta( $user_id, '_customer_recurring_subscription', true ) == 'active' ) {
                Helper::log( 'Subscription cancel check: Admin has canceled Subscription of User #' . $user_id . ' on order #' . $order_id );

                do_action( 'dps_cancel_recurring_subscription', $order_id, $user_id );
            } else {
                Helper::log( 'Subscription cancel check: Admin has canceled Subscription of User #' . $user_id . ' on order #' . $order_id );

                Helper::delete_subscription_pack( $user_id, $order_id );
            }
        }

        $response = rest_ensure_response( $user_ids );

        return $response;
    }

    /**
     * Prepare a single sinle subscription output for response.
     *
     * @param Object $user
     * @param WP_REST_Request $request Request object.
     *
     * @return WP_REST_Response $response Response data.
     */
    public function prepare_item_for_response( $user, $request ) {
        $subscription = dokan()->vendor->get( $user->ID )->subscription;

        if ( ! $subscription ) {
            return new WP_Error( 'no_subscription', __( 'No subscription is found to be deleted.', 'dokan' ), [ 'status' => 200 ] );
        }

        $end_date = $subscription->get_pack_end_date();

        $data = [
            'id'                 => $user->ID,
            'user_link'          => get_edit_user_link( $user->ID ),
            'user_name'          => $user->data->user_nicename,
            'subscription_id'    => $subscription->get_id(),
            'subscription_title' => $subscription->get_package_title(),
            'start_date'         => date_i18n( get_option( 'date_format' ), strtotime( $subscription->get_pack_start_date() ) ),
            'end_date'           => 'unlimited' === $end_date ? __( 'Unlimited', 'dokan' ) : date_i18n( get_option( 'date_format' ), strtotime( $end_date ) ),
            'status'             => $subscription->has_subscription()
        ];

        $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
        $data    = $this->add_additional_fields_to_object( $data, $request );
        $data    = $this->filter_response_by_context( $data, $context );

        return apply_filters( 'dokan_rest_prepare_subscription', $data, $user, $request );
    }
}