<?php

/**
* Dokan Refund API Controller
*
* @since 2.8.2
*
* @package dokan
*/
class Dokan_REST_Refund_Controller extends Dokan_REST_Controller {

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
    protected $base = 'refund';

    /**
     * Register all routes releated with stores
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->base, array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.', 'dokan' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_refunds' ),
                'args'                => array_merge( $this->get_collection_params(),  array(
                    'status' => array(
                        'type'        => 'string',
                        'description' => __( 'Refund status', 'dokan' ),
                        'required'    => false,
                    ),
                ) ),
                'permission_callback' => array( $this, 'refund_permissions_check' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_refund' ),
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                'permission_callback' => array( $this, 'create_refund_permissions_check' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/', array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.', 'dokan' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'change_refund_status' ),
                'args'                => array(
                    'status' => array(
                        'type'        => 'string',
                        'description' => __( 'Refund status', 'dokan' ),
                        'required'    => false,
                    )
                ),
                'permission_callback' => array( $this, 'refund_permissions_check' ),
            ),

            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_refund' ),
                'permission_callback' => array( $this, 'refund_permissions_check' ),
            ),

        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/batch', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'batch_items' ),
                'permission_callback' => array( $this, 'refund_permissions_check' ),
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
            ),
        ) );
    }

    /**
     * Map refund status
     *
     * @since 2.8.2
     *
     * @return array
     */
    protected function get_status( $status ) {
        $statuses = array(
            0 => 'pending',
            1 => 'approved',
            2 => 'cancelled'
        );

        if ( is_string( $status ) ) {
            return array_search( $status, $statuses );
        } else{
            return isset( $statuses[$status] ) ? $statuses[$status] : '';
        }

        return $statuses;
    }

    /**
     * Get refunds
     *
     * @since 2.8.2
     *
     * @return object
     */
    public function get_refunds( $request ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return new WP_Error( 'access_denied', __( 'You do not have permission', 'dokan' ), array( 'status' => 404 ) );
        }

        $status = ! empty( $request['status'] ) ? $request['status'] : '';
        $refund = new Dokan_Pro_Admin_Refund();

        $limit = $request['per_page'];
        $offset = ( $request['page'] - 1 ) * $request['per_page'];

        $refund_count = dokan_get_refund_count();

        if ( ! empty( $status ) ) {
            if ( $status == 'pending' ) {
                $total_count = $refund_count['pending'];
            } elseif( $status == 'approved' ) {
                $total_count = $refund_count['completed'];
            } else {
                $total_count = $refund_count['cancelled'];
            }

            $refunds = $refund->get_refund_requests( $this->get_status( $status ), $limit, $offset );
        } else {
            global $wpdb;
            $sql    = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}dokan_refund LIMIT %d, %d", $offset, $limit );
            $refunds = $wpdb->get_results( $sql );
            $total_count = array_sum( $refund_count );
        }

        $data = array();
        foreach ( $refunds as $key => $value ) {
            $resp   = $this->prepare_response_for_object( $value, $request );
            $data[] = $this->prepare_response_for_collection( $resp );
        }

        $response       = rest_ensure_response( $data );
        $refund_count   = dokan_get_refund_count();

        if (  current_user_can( 'manage_options' ) ) {
            $response->header( 'X-Status-Pending', $refund_count['pending'] );
            $response->header( 'X-Status-Completed', $refund_count['completed'] );
            $response->header( 'X-Status-Cancelled', $refund_count['cancelled'] );
        }

        $response = $this->format_collection_response( $response, $request, $total_count );
        return $response;
    }

    /**
     * Cancel refund status
     *
     * @since 2.8.2
     *
     * @return void
     */
    public function change_refund_status( $request ) {
        global $wpdb;

        $store_id = dokan_get_current_user_id();

        if ( empty( $request['id'] ) ) {
            return new WP_Error( 'no_id', __( 'Invalid Refund ID', 'dokan' ), array( 'status' => 404 ) );
        }

        $status = ! empty( $request['status'] ) ? $request['status'] : 'pending';

        if ( ! current_user_can( 'manage_options' ) ) {
            return new WP_Error( 'cancel_request', __( 'Vendor can only create refund request', 'dokan' ), array( 'status' => 400 ) );
        }

        $sql    = "SELECT * FROM `{$wpdb->prefix}dokan_refund` WHERE `id`={$request['id']}";
        $result = $wpdb->get_row( $sql );

        if ( $result->status != '0' ) {
            return new WP_Error( 'not_cancel_request', __( 'This refund is not pending. Only pending request status can be changed', 'dokan' ), array( 'status' => 400 ) );
        }

        $refund      = new Dokan_Pro_Admin_Refund();

        $status_code = $this->get_status( $status );
        $order_id    = isset( $request['order_id'] ) ? $request['order_id'] : '';

        if ( 1 == $status_code ) {
            if ( ! dokan_is_refund_allowed_to_approve( $order_id ) ) {
                return new WP_Error( 'unable_to_approve', __( 'This refund request is not allowed to approve.', 'dokan' ), array( 'status' => 200 ) );
            }

            $this->approve_refund_request( $result );
        }

        $refund->update_status( $request['id'], $status_code );
        $response = $wpdb->get_row( $sql );

        return rest_ensure_response( $this->prepare_response_for_object( $response, $request ) );
    }

    /**
     * Delete a refund
     *
     * @since 2.8.2
     *
     * @return void
     */
    public function delete_refund( $request ) {
        global $wpdb;

        $refund_id = !empty( $request['id'] ) ? intval( $request['id'] ) : 0;

        if ( !$refund_id ) {
            return new WP_Error( 'no_id', __( 'Invalid Refund ID', 'dokan' ), array( 'status' => 404 ) );
        }

        $sql    = "SELECT * FROM `{$wpdb->prefix}dokan_refund` WHERE `id`={$refund_id}";
        $result = $wpdb->get_row( $sql );

        if ( empty( $result->id ) ) {
            return new WP_Error( 'no_refund', __( 'No refund found for deleting', 'dokan' ), array( 'status' => 404 ) );
        }

        $refund = new Dokan_Pro_Admin_Refund();

        $refund->delete_refund( $request['id'] );

        return rest_ensure_response( $this->prepare_response_for_object( $result, $request ) );
    }

    /**
     * Make a refund request
     *
     * @since 2.8.2
     *
     * @return void
     */
    public function create_refund( $request ) {
        global $wpdb;

        $data                            = array();
        $store_id                        = dokan_get_current_user_id();
        $data['order_id']                = $request['order_id'] ;
        $data['seller_id']               = dokan_get_seller_id_by_order($request['order_id']);
        $data['refund_amount']           = $request['amount'];
        $data['refund_reason']           = $request['reason'] ? $request['reason'] : '';
        $data['line_item_qtys']          = $request['line_item_qtys'] ? $request['line_item_qtys'] : '';
        $data['line_item_totals']        = $request['line_item_totals'] ? $request['line_item_totals'] : '';
        $data['line_item_tax_totals']    = $request['line_item_tax_totals'] ? $request['line_item_tax_totals'] : '';
        $data['restock_refunded_items']  = $request['restock_refunded_items'] ? 'true' : 'false';
        $data['status']                  = 0;
        $data['api_refund']              = $request['method'] ? $request['method'] : 'false';

        // Validate that the refund can occur
        $amount     = wc_format_decimal( sanitize_text_field( $data['refund_amount'] ), wc_get_price_decimals() );
        $order      = wc_get_order( $data['order_id'] );
        $max_refund = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

        $refund = new Dokan_Pro_Admin_Refund;

        if ( $store_id != $data['seller_id'] && ! $this->refund_permissions_check() ) {
            return new WP_Error( 'cheating', __( 'Cheating uh!', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( empty( $store_id ) ) {
            return new WP_Error( 'no_store_found', __( 'No vendor found', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( empty( $data['order_id'] ) ) {
            return new WP_Error( 'no_order_found', __( 'No Order found', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( empty( $data['refund_amount'] ) ) {
            return new WP_Error( 'no_amount_found', __( 'No Amount found', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( ! $amount || $max_refund < $amount || 0 > $amount ) {
            return new WP_Error( 'invalid_amount', __( 'Invalid refund amount', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( $refund->has_pending_refund_request( $data['order_id'] ) ) {
            return new WP_Error( 'duplicate', __( 'You have already a processing refund request for this order.', 'dokan' ), array( 'status' => 404 ) );
        }

        $update = $refund->insert_refund( $data );

        $data_info['id']      = $wpdb->insert_id;
        $data_info['user']    = $this->get_user_data( $data['seller_id'] );
        $data_info['created'] = mysql_to_rfc3339( date( 'Y-m-d h:i:s' ) );

        return rest_ensure_response( $data_info );
    }

    /**
     * Approve, Pending and cancel bulk action
     *
     * JSON data format for sending to API
     *     {
     *         "approved" : [
     *             "1", "9", "7"
     *         ],
     *         "pending" : [
     *             "2"
     *         ],
     *         "cancelled" : [
     *             "5"
     *         ],
     *         "delete" : [
     *             "9", "11"
     *         ]
     *     }
     *
     * @since 2.8.2
     *
     * @return void
     */
    public function batch_items( $request ) {
        global $wpdb;

        $params = $request->get_params();

        if ( empty( $params ) ) {
            return new WP_Error( 'no_item_found', __( 'No items found for bulk updating', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( ! $this->refund_permissions_check() ) {
            return new WP_Error( 'no_permission', __( 'You do not have permission for bulk status change', 'dokan' ), array( 'status' => 404 ) );
        }

        $allowed_status = array( 'approved', 'cancelled', 'pending', 'delete' );

        foreach ( $params as $status => $value ) {
            if ( ! in_array( $status, $allowed_status ) ) {
                return false;
            }

            $refund = new Dokan_Pro_Admin_Refund();

            if ( 'delete' === $status ) {
                foreach ( $value as $refund_id ) {
                    $refund->delete_refund( $refund_id );
                }
            }

            if ( 'approved' === $status && $this->get_status( $status ) == 1 ) {
                foreach ( $value as $refund_id ) {
                    $sql    = "SELECT * FROM `{$wpdb->prefix}dokan_refund` WHERE `id`={$refund_id}";
                    $result = $wpdb->get_row( $sql );

                    $this->approve_refund_request( $result );
                }
            }

            if ( $status !== 'delete' ) {
                foreach ( $value as $refund_id ) {
                    $status_code = $this->get_status( $status );

                    $wpdb->query( $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}dokan_refund
                        SET status = %d WHERE id = %d",
                        $status_code, $refund_id
                    ) );
                }
            }
        }

        return true;
    }

    /**
     * Create refund post on approve refund request.
     *
     * @since 2.8.3
     */
    public function approve_refund_request( $data ) {
        global $wpdb;

        $order_id               = absint( $data->order_id );
        $vendor_id              = dokan_get_seller_id_by_order( $order_id );
        $refund_amount          = wc_format_decimal( sanitize_text_field( wp_unslash( $data->refund_amount ) ), wc_get_price_decimals() );
        $refund_reason          = sanitize_text_field( $data->refund_reason );
        $line_item_qtys         = json_decode( sanitize_text_field( wp_unslash( $data->item_qtys ) ), true );
        $line_item_totals       = json_decode( sanitize_text_field( wp_unslash( $data->item_totals ) ), true );
        $line_item_tax_totals   = json_decode( sanitize_text_field( wp_unslash( $data->item_tax_totals ) ), true );
        $api_refund             = 'true' === $data->method;
        $restock_refunded_items = 'true' === $data->restock_items;
        $refund                 = false;

        $vendor_refund          = $fee_refund = 0;
        $shipping_fee_recipient = dokan_get_option( 'shipping_fee_recipient', 'dokan_general', 'seller' );

        // Prepare line items which we are refunding.
        $line_items = array();
        $item_ids   = array_unique( array_merge( array_keys( $line_item_qtys, $line_item_totals ) ) );

        foreach ( $item_ids as $item_id ) {
            $line_items[ $item_id ] = array(
                'qty'          => 0,
                'refund_total' => 0,
                'refund_tax'   => array(),
            );
        }

        foreach ( $line_item_qtys as $item_id => $qty ) {
            $line_items[ $item_id ]['qty'] = max( $qty, 0 );
        }

        $order = new WC_Order();

        foreach ( $line_item_totals as $item_id => $total ) {

            $item = $order->get_item( $item_id );

            if ( 'line_item' == $item['type'] ) {
                $percentage_type    = dokan_get_commission_type( $vendor_id, $item['product_id'] );
                $vendor_percentage  = dokan_get_seller_percentage( $vendor_id, $item['product_id'] );
                $vendor_refund      += $percentage_type == 'percentage' ? (float) ( $total * $vendor_percentage ) / 100 : (float) ( $total * ( ( $item['subtotal'] - $vendor_percentage ) / $item['subtotal'] ) );
            } else {
                $fee_refund += $total;
            }

            $line_items[ $item_id ]['refund_total'] = wc_format_decimal( $total );
        }

        foreach ( $line_item_tax_totals as $item_id => $tax_totals ) {
            foreach ( $tax_totals as $total_tax ) {
                $fee_refund += $total_tax;
                $line_items[ $item_id ]['refund_tax'] = wc_format_decimal( $total_tax );
            }
        }

        if ( 'seller' == $shipping_fee_recipient ) {
            $vendor_refund += $fee_refund;
        }

        // if paid via automatic payment such as stripe
        $order = wc_get_order( $order_id );

        if ( $order->get_payment_method() === 'dokan-stripe-connect' ) {
            $wpdb->insert( $wpdb->prefix . 'dokan_vendor_balance',
                array(
                    'vendor_id'     => $vendor_id,
                    'trn_id'        => $order_id,
                    'trn_type'      => 'dokan_refund',
                    'perticulars'   => __( 'Paid Via Stripe', 'dokan' ),
                    'debit'         => $vendor_refund,
                    'credit'        => 0,
                    'status'        => 'wc-completed', // see: Dokan_Vendor->get_balance() method
                    'trn_date'      => current_time( 'mysql' ),
                    'balance_date'  => current_time( 'mysql' ),
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%f',
                    '%f',
                    '%s',
                    '%s',
                    '%s',
                )
            );

        }

        $wpdb->insert( $wpdb->prefix . 'dokan_vendor_balance',
            array(
                'vendor_id'     => $vendor_id,
                'trn_id'        => $order_id,
                'trn_type'      => 'dokan_refund',
                'perticulars'   => $refund_reason,
                'debit'         => 0,
                'credit'        => $vendor_refund,
                'status'        => 'approved',
                'trn_date'      => current_time( 'mysql' ),
                'balance_date'  => current_time( 'mysql' ),
            ),
            array(
                '%d',
                '%d',
                '%s',
                '%s',
                '%f',
                '%f',
                '%s',
                '%s',
                '%s',
            )
        );

        // Create the refund object.
        $refund = wc_create_refund(
            array(
                'amount'         => $refund_amount,
                'reason'         => $refund_reason,
                'order_id'       => $order_id,
                'line_items'     => $line_items,
                'refund_payment' => $api_refund,
                'restock_items'  => $restock_refunded_items,
            )
        );

        // update the order table with new refund amount
        $order_data = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}dokan_orders WHERE order_id = %d", $order_id )
        );

        if ( isset( $order_data->order_total, $order_data->net_amount ) ) {
            $new_total_amount = $order_data->order_total - $refund_amount;
            $new_net_amount   = $order_data->net_amount - $vendor_refund;

            // insert on dokan sync table
            $wpdb->update( $wpdb->prefix . 'dokan_orders',
                array( 'order_total' => $new_total_amount, 'net_amount' => $new_net_amount ),
                array( 'order_id' => $order_id ),
                array( '%d', '%d' ),
                array( '%d' )
            );
        }


        if ( dokan_is_sub_order( $order_id ) ) {
            $parent_order_id = wp_get_post_parent_id( $order_id );

            // Create the refund object for parent order.
            $refund = wc_create_refund(
                array(
                    'amount'         => $refund_amount,
                    'reason'         => $refund_reason,
                    'order_id'       => $parent_order_id,
                    'line_items'     => '{}',
                    'refund_payment' => $api_refund,
                    'restock_items'  => false,
                )
            );
        }

        $vendor       = dokan()->vendor->get( $vendor_id );
        $vendor_email = $vendor->get_email();

        do_action( 'dokan_refund_processed_notification', $vendor_email, $order_id, 'approved', $refund_amount, $refund_reason );

        return true;
    }

    /**
     * Prepare data for response
     *
     * @since 2.8.2
     *
     * @return data
     */
    public function prepare_response_for_object( $object, $request ) {

        $data = array(
            'id'           => $object->id,
            'order_id'     => $object->order_id,
            'vendor'       => $this->get_user_data( $object->seller_id ),
            'amount'       => floatval( $object->refund_amount ),
            'reason'       => $object->refund_reason,
            'item_qtys'    => $object->item_qtys,
            'item_totals'  => $object->item_totals,
            'tax_totals'   => $object->item_tax_totals,
            'restock_items'=> $object->restock_items,
            'created'      => mysql_to_rfc3339( $object->date ),
            'status'       => $this->get_status( (int) $object->status ),
            'method'       => get_post_meta( $object->order_id, '_payment_method_title', true )
        );

        $response      = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $object, $request ) );

        return apply_filters( "dokan_rest_prepare_refund_object", $response, $object, $request );
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
                'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->base, $object->id ) ),
            ),
            'collection' => array(
                'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->base ) ),
            ),
        );

        return $links;
    }

    /**
     * Get user data
     *
     * @since 2.8.2
     *
     * @return return object
     */
    public function get_user_data( $user_id ) {
        $vendor = dokan()->vendor->get( $user_id );

        return array(
            'id'         => $vendor->get_id(),
            'store_name' => $vendor->get_shop_name(),
            'email'      => $vendor->get_email(),
            'first_name' => $vendor->get_first_name(),
            'last_name'  => $vendor->get_last_name()
        );
    }

    /**
     * Check permission for getting refund
     *
     * @since 2.8.2
     *
     * @return void
     */
    public function create_refund_permissions_check() {
        return current_user_can( 'dokandar' );
    }

    /**
     * Check permission for getting refund
     *
     * @since 2.8.2
     *
     * @return void
     */
    public function refund_permissions_check() {
        return current_user_can( 'manage_options' ) || current_user_can( 'dokandar' );
    }

    /**
     * Get the Coupon's schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'Refund',
            'type'       => 'object',
            'properties' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.', 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view' ),
                    'readonly'    => true,
                ),
                'order' => array(
                    'description' => __( 'Order ID', 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view' ),
                ),
                'vendor' => array(
                    'description' => __( 'Vendor ID', 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view' ),
                ),
                'amount' => array(
                    'description' => __( 'The amount requested for refund. Should always be numeric', 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view' ),
                ),
                'reason' => array(
                    'description' => __( "Refund Reason", 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view' ),
                ),
                'item_qty' => array(
                    'description' => __( "Item Quantity", 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view' ),
                ),
                'item_total' => array(
                    'description' => __( "Item Total", 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view' ),
                ),
                'tax_total' => array(
                    'description' => __( "Tax Total", 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view' ),
                ),
                'restock' => array(
                    'description' => __( "Restock Items", 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view' ),
                ),
                'created_data' => array(
                    'description' => __( "The date the Refund request has beed created in the site's timezone.", 'dokan' ),
                    'type'        => 'date-time',
                    'context'     => array( 'view' ),
                ),
                'status' => array(
                    'description' => __( "Refund status", 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view' ),
                ),
                'method' => array(
                    'description' => __( "Refund Method", 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view' ),
                ),
            ),
        );

        return $this->add_additional_fields_schema( $schema );
    }

}
