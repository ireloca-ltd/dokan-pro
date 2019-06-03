<?php

/**
* Ajax handling class
*/
class Dokan_RMA_Ajax {

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'wp_ajax_dokan-update-return-request', [ $this, 'update_status' ], 10 );
        add_action( 'wp_ajax_dokan-get-refund-order-data', [ $this, 'get_order_data' ], 10 );
        add_action( 'wp_ajax_dokan-get-coupon-order-data', [ $this, 'get_order_data' ], 10 );
        add_action( 'wp_ajax_dokan-send-refund-request', [ $this, 'send_refund_request' ], 10 );
        add_action( 'wp_ajax_dokan-send-coupon-request', [ $this, 'send_coupon_request' ], 10 );
    }

    /**
     * Update request status
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update_status() {
        parse_str( $_POST['formData'], $data );

        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_rma_nonce' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $request = new Dokan_RMA_Warranty_Request();
        $updated = $request->update( $data );

        if ( is_wp_error( $updated ) ) {
            wp_send_json_error( $updated->get_error_message() );
        }

        wp_send_json_success( [ 'message' => __( 'Status changed successfully', 'dokan' ) ] );
    }

    /**
     * Get order data for refund
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_order_data() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_rma_nonce' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        if ( empty( $_POST['request_id'] ) ) {
            wp_send_json_error( __( 'Order id not found', 'dokan' ) );
        }

        $warranty_request = new Dokan_RMA_Warranty_Request();
        $request = $warranty_request->get( $_POST['request_id'] );

        if ( ! $request ) {
            wp_send_json_error( __( 'Invalid Request', 'dokan' ) );
        }

        $wc_tax_enabled = get_option( 'woocommerce_calc_taxes' ) === 'yes' ? true : false;

        $order_id        = $request['order_id'];
        $order           = wc_get_order( $order_id );
        $order_tax_total = $order->get_total_tax();

        if ( ! empty( $request['items'] ) ) {
            ob_start();
            ?>
                <table class="dokan-table dokan-refund-item-list-table">
                    <thead>
                        <tr>
                            <th width="50%"><?php _e( 'Product', 'dokan' ); ?></th>
                            <th width="20%"><?php _e( 'Qty', 'dokan' ); ?></th>

                            <?php if ( $wc_tax_enabled ) : ?>
                                <th width="20%"><?php _e( 'Tax', 'dokan' ); ?></th>
                            <?php endif; ?>

                            <th width="20%"><?php _e( 'Total', 'dokan' ); ?></th>

                            <?php if ( $wc_tax_enabled ) : ?>
                                <th width="20%"><?php _e( 'Tax Refund', 'dokan' ); ?></th>
                            <?php endif; ?>

                            <th width="20%"><?php _e( 'Total Refund', 'dokan' ); ?></th>
                            <th width="15%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $request['items'] as $key => $item ): ?>
                            <tr>
                                <td><a href="<?php echo $item['url']; ?>"><?php echo $item['title'] ?></a></td>
                                <td><?php echo $item['quantity'] ?></td>

                                <?php if ( $wc_tax_enabled ) : ?>
                                   <td><?php echo $order_tax_total; ?></td>
                                <?php endif; ?>

                                <td><?php echo wc_price( $item['price'] * $item['quantity'] ); ?></td>

                                <?php if ( $wc_tax_enabled ) : ?>
                                    <td>
                                        <input type="number" min="0" step="any" max="<?php echo $item['price'] * $item['quantity']; ?>" name="refund_tax" class="refund_item_amount">
                                    </td>
                                <?php endif; ?>
                                <td>
                                    <input type="hidden" name="item_id[]" value="<?php echo $item['item_id']; ?>">
                                    <input type="number" min="0" step="any" max="<?php echo $item['price'] * $item['quantity']; ?>" name="refund_amount[]" class="refund_item_amount">
                                    <input type="hidden" name="line_item_qtys" value="<?php echo $item['quantity'] ?>">
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <input type="hidden" name="refund_total_amount" value="0">
                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                <input type="hidden" name="refund_order_id" value="<?php echo $request['order_id'] ?>">
                <input type="hidden" name="refund_vendor_id" value="<?php echo $request['vendor']['store_id']; ?>">
                <div class="dokan-popup-total-refund-amount dokan-right"><?php _e( 'Total Amount : ', 'dokan' ) ?> <strong><span><?php echo get_woocommerce_currency_symbol(); ?></span><span class="amount">0.00</span></strong></div>
                <div class="dokan-clearfix"></div>
            <?php
            $data = ob_get_clean();
            wp_send_json_success( $data );
        }

        wp_send_json_error( __( 'No Item found', 'dokan' ) );
    }

    /**
     * Send refund request
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function send_refund_request() {
        parse_str( $_POST['formData'], $data );

        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_rma_nonce' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        if ( empty( $data['refund_order_id'] ) ) {
            wp_send_json_error( __( 'No order found', 'dokan' ) );
        }

        $order = wc_get_order( $data['refund_order_id'] );

        if ( ! $order ) {
            wp_send_json_error( __( 'No order found', 'dokan' ) );
        }

        if ( empty( $data['refund_vendor_id'] ) ) {
            wp_send_json_error( __( 'No vendor found', 'dokan' ) );
        }

        if ( $data['refund_vendor_id'] != dokan_get_current_user_id() ) {
            wp_send_json_error( __( 'Error! This is not your request.', 'dokan' ) );
        }

        if ( $data['refund_total_amount'] <= 0 ) {
            wp_send_json_error( __( 'Refund amount must be greater than 0', 'dokan' ) );
        }

        $warranty_request = new Dokan_RMA_Warranty_Request();
        $request_data     = $warranty_request->get( $data['request_id'] );

        $refund_max_amount = [];
        foreach ( $request_data['items'] as $request_item ) {
            $refund_max_amount[] = $request_item['price']*$request_item['quantity'];
        }

        $total_amount = wc_format_decimal( array_sum( $refund_max_amount ), wc_get_price_decimals() );

        // Validate that the refund can occur
        $refund_amount = wc_format_decimal( sanitize_text_field( $data['refund_total_amount'] ), wc_get_price_decimals() );
        $max_refund    = wc_format_decimal( $total_amount - $order->get_total_refunded(), wc_get_price_decimals() );

        $refund = new Dokan_Pro_Refund;

        if ( ! $refund_amount || $max_refund < $refund_amount || 0 > $refund_amount ) {
            wp_send_json_error( __( 'Invalid refund amount', 'dokan' ) );
        } else if ( $refund->has_pending_refund_request( $data['refund_order_id'] ) ) {
            wp_send_json_error( __( 'You have already a processing refund request for this order.', 'dokan' ) );
        } else {

            $refund_obj_qty   = [ $data['item_id'][0] => $data['line_item_qtys'] ];
            $refund_obj_total = [ $data['item_id'][0] => $data['refund_amount'][0] ];
            $refund_obj_tax   = ! empty( $data['refund_tax'] ) ? [ $data['item_id'][0] => [ $data['line_item_qtys'] => $data['refund_tax'] ] ] : [];

            $postdata = [
                'order_id'               => $data['refund_order_id'],
                'seller_id'              => $data['refund_vendor_id'],
                'refund_amount'          => $data['refund_total_amount'],
                'refund_reason'          => __( 'Warranty Request from Customer', 'dokan' ),
                'line_item_qtys'         => json_encode( $refund_obj_qty ),
                'line_item_totals'       => json_encode( $refund_obj_total ),
                'line_item_tax_totals'   => json_encode( $refund_obj_tax ),
                'api_refund'             => false,
                'restock_refunded_items' => null,
                'status'                 => 0
            ];

            $refund = new Dokan_Pro_Refund;

            $refund->insert_refund( $postdata );

            do_action( 'dokan_refund_request_notification',  $data['refund_order_id'] );

            wp_send_json_success( $data );
        }

        wp_send_json_error( __( 'Something is wrong, Please try again', 'dokan' ) );
    }

    /**
     * Send coupon to customer
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function send_coupon_request() {
        parse_str( $_POST['formData'], $data );

        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_rma_nonce' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        if ( empty( $data['refund_order_id'] ) ) {
            wp_send_json_error( __( 'No order found', 'dokan' ) );
        }

        $order = wc_get_order( $data['refund_order_id'] );

        if ( ! $order ) {
            wp_send_json_error( __( 'No order found', 'dokan' ) );
        }

        if ( empty( $data['refund_vendor_id'] ) ) {
            wp_send_json_error( __( 'No vendor found', 'dokan' ) );
        }

        if ( $data['refund_vendor_id'] != dokan_get_current_user_id() ) {
            wp_send_json_error( __( 'Error! This is not your request.', 'dokan' ) );
        }

        if ( $data['refund_total_amount'] <= 0 ) {
            wp_send_json_error( __( 'Refund amount must be greater than 0', 'dokan' ) );
        }

        $warranty_request  = new Dokan_RMA_Warranty_Request();
        $request_data      = $warranty_request->get( $data['request_id'] );
        $refund_max_amount = [];

        foreach ( $request_data['items'] as $request_item ) {
            $refund_max_amount[] = $request_item['price']*$request_item['quantity'];
        }

        $refund_amount = wc_format_decimal( sanitize_text_field( $data['refund_total_amount'] ), wc_get_price_decimals() );
        $total_amount = wc_format_decimal( array_sum( $refund_max_amount ), wc_get_price_decimals() );

        $coupon = new WC_Coupon( 319 );

        $coupon->set_code( dokan_rma_generate_coupon_code() );
        $coupon->set_amount( $refund_amount );
        $coupon->set_date_created( date("Y-m-d H:i:s") );
        $coupon->set_date_expires( null );
        $coupon->set_discount_type( 'fixed_cart' );
        $coupon->set_description( '' );
        $coupon->set_usage_count( 0 );
        $coupon->set_individual_use( false );
        $coupon->set_product_ids( [] );
        $coupon->set_excluded_product_ids( [] );
        $coupon->set_usage_limit( '1' );
        $coupon->set_usage_limit_per_user( '1' );
        $coupon->set_limit_usage_to_x_items( null );
        $coupon->set_free_shipping( false );
        $coupon->set_product_categories( [] );
        $coupon->set_excluded_product_categories( [] );
        $coupon->set_exclude_sale_items( false );
        $coupon->set_minimum_amount( '' );
        $coupon->set_maximum_amount( '' );
        $coupon->set_email_restrictions( [ $order->get_billing_email() ] );
        $coupon->set_used_by( [] );
        $coupon->set_virtual( false );

        $coupon->save();
        $coupon_id = $coupon->get_id();

        wp_update_post( [
            'ID' => $coupon_id,
            'post_author' => dokan_get_current_user_id()
        ] );

        do_action( 'dokan_send_coupon_to_customer', $coupon, $data );

        wp_send_json_success( __( 'Coupon has been created successfully and send to customer email', 'dokan' ) );
    }
}
