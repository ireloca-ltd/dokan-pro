<?php
/**
 *  Dokan Dashboard Orders Template
 *
 *  Load order related template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

    <div class="dokan-dashboard-wrap">

        <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_before' );
            do_action( 'dokan_rma_single_request_before' );

        ?>

        <div class="dokan-dashboard-content dokan-orders-content">

            <?php

                /**
                 *  dokan_rma_request_content_inside_before hook
                 *
                 *  @hooked show_seller_enable_message
                 *
                 *  @since 2.4
                 */
                do_action( 'dokan_rma_single_request_content_inside_before' );
            ?>


            <article class="dokan-rma-single-request-area">
                <header class="dokan-dashboard-header">
                    <div class="left-header-content">
                        <h1 class="entry-title"><?php _e( 'Return Request', 'dokan' ) ?>
                            <span class="left-header-content dokan-right">
                                <a href="<?php echo dokan_get_navigation_url( 'return-request' ); ?>"><?php _e( '&larr; Back to List', 'dokan' ); ?></a>
                            </span>
                        </h1>
                    </div>
                    <div class="dokan-clearfix"></div>
                </header>

                <?php if ( ! $request ): ?>
                    <?php dokan_get_template_part( 'global/dokan-error', '', [ 'deleted' => false, 'message' => __( 'Invalid request id. Not found', 'dokan' ) ] ); ?>
                <?php elseif( dokan_get_current_user_id() != $request['vendor']['store_id'] ): ?>
                    <?php dokan_get_template_part( 'global/dokan-error', '', [ 'deleted' => false, 'message' => __( 'Error! this is not your request.', 'dokan' ) ] ); ?>
                <?php else: ?>
                    <div class="dokan-w8 dokan-rma-single-request-left-content">
                        <div class="dokan-clearfix">
                            <div class="dokan-panel dokan-panel-default">
                                <div class="dokan-panel-heading">
                                    <?php _e( 'Details', 'dokan' ); ?>
                                </div>
                                <div class="dokan-panel-body">
                                    <table class="dokan-table">
                                        <tbody>
                                            <tr>
                                                <td><strong><?php _e( 'Order ID :', 'dokan' ) ?></strong></td>
                                                <td>
                                                    <?php
                                                        if ( current_user_can( 'dokan_view_order' ) ) {
                                                            $order_link = '<a href="' . wp_nonce_url( add_query_arg( array( 'order_id' => $request['order_id'] ), dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' ) . '"><strong>' . sprintf( __( 'Order %s', 'dokan-lite' ), esc_attr( $request['order_id'] ) ) . '</strong></a>';
                                                        } else {
                                                            $order_link = '<strong>' . sprintf( __( 'Order %s', 'dokan-lite' ), esc_attr( $request['order_id'] ) ) . '</strong>';
                                                        }
                                                        echo $order_link;
                                                     ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><strong><?php _e( 'Customer Name :', 'dokan' ) ?></strong></td>
                                                <td><?php echo $request['customer']['name']; ?></td>
                                            </tr>

                                            <tr>
                                                <td><strong><?php _e( 'Request Type :', 'dokan' ) ?></strong></td>
                                                <td><?php echo dokan_warranty_request_type( $request['type'] ); ?></td>
                                            </tr>
                                            <tr>
                                                <td><strong><?php _e( 'Products :', 'dokan' ); ?></strong></td>
                                                <td>
                                                    <?php
                                                        $product_list = [];
                                                        foreach ( $request['items'] as $item ) {
                                                            $product_list[] = '<a href="' . $item['url'] . '">' . $item['title'] . '</a> <strong>x ' . $item['quantity'] . '</strong>';
                                                        }

                                                        echo implode( ', ', $product_list );
                                                    ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="additional-details">
                                        <strong><?php _e( 'Additional Details', 'dokan' ); ?></strong>
                                        <hr>
                                        <div class="details-row">
                                            <p class="details-label"><?php _e( 'Reason', 'dokan' ) ?></p>
                                            <p class="details-value"><?php echo dokan_rma_refund_reasons( $request['reasons'] ); ?></p>
                                        </div>
                                        <div class="details-row">
                                            <p class="details-label"><?php _e( 'Reason Details', 'dokan' ) ?></p>
                                            <p class="details-value">
                                                <?php
                                                    echo ! empty( $request['details'] ) ? $request['details'] : __( 'No details found', 'dokan' );
                                                ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dokan-w4 dokan-rma-single-request-right-content">
                        <div class="dokan-clearfix">
                            <div class="dokan-panel dokan-panel-default dokan-status-update-panel">
                                <div class="dokan-panel-heading">
                                    <?php _e( 'Status', 'dokan' ); ?>
                                </div>
                                <div class="dokan-panel-body">
                                    <p>
                                        <?php echo sprintf( '<strong>%s</strong>: %s', __( 'Last Updated', 'dokan' ), date_i18n( get_option( 'date_format' ), strtotime( $request['created_at'] ) ) ) ?>
                                    </p>
                                    <form method="post" action="" id="dokan-update-request-status">
                                        <div class="dokan-form-group">
                                            <label for="request-status"><?php _e( 'Change Status', 'dokan' ) ?></label>
                                            <select name="status" id="status" class="dokan-form-control">
                                                <?php foreach ( dokan_warranty_request_status() as $status_key => $status_value ): ?>
                                                    <option value="<?php echo $status_key ?>" <?php selected( $request['status'], $status_key ); ?>><?php echo $status_value ?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>

                                        <div class="dokan-form-group dokan-clearfix">
                                            <input type="hidden" name="id" value="<?php echo $request['id']; ?>">
                                            <input type="hidden" name="vendor_id" value="<?php echo $request['vendor']['store_id']; ?>">
                                            <input type="submit" class="dokan-right dokan-btn dokan-btn-default" value="<?php _e( 'Update', 'dokan' ) ?>">

                                            <?php if ( 'yes' == dokan_get_option( 'rma_enable_refund_request', 'dokan_rma', 'no' ) && 'refund' == $request['type'] && 'completed' == $request['status'] ): ?>
                                                <?php
                                                    $refund                     = new Dokan_Pro_Refund;
                                                    $has_already_refund_request = $refund->has_pending_refund_request( $request['order_id'] );
                                                ?>
                                                <?php if ( $has_already_refund_request ): ?>
                                                    <a href="#" class="dokan-btn dokan-btn-default" disabled><?php _e( 'Send Refund', 'dokan' ) ?></a>
                                                <?php else: ?>
                                                    <a href="#" class="dokan-btn dokan-btn-default dokan-send-refund-request" data-request_id="<?php echo $request['id']; ?>"><?php _e( 'Send Refund', 'dokan' ) ?></a>
                                                <?php endif ?>
                                            <?php endif ?>

                                            <?php if ( 'yes' == dokan_get_option( 'rma_enable_coupon_request', 'dokan_rma', 'no' ) && 'coupon' == $request['type'] && 'completed' == $request['status'] ): ?>
                                                <a href="#" class="dokan-btn dokan-btn-default dokan-send-coupon-request" data-request_id="<?php echo $request['id']; ?>"><?php _e( 'Send Coupon', 'dokan' ) ?></a>
                                            <?php endif ?>
                                                <?php
                                                    if ( isset( $has_already_refund_request ) && $has_already_refund_request ) {
                                                        ?>
                                                        <p class="dokan-alert dokan-alert-info" style="margin-top: 10px;">
                                                            <?php _e( 'Already send refund request. Wait for admin approval', 'dokan' ); ?>
                                                        </p>
                                                        <?php
                                                    };
                                                ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dokan-clearfix"></div>

                    <div class="dokan-w12">
                        <div class="dokan-clearfix">
                            <div class="dokan-panel dokan-panel-default">
                                <div class="dokan-panel-heading">
                                    <?php _e( 'Conversations', 'dokan' ) ?>
                                </div>

                                <div class="dokan-panel-body">
                                    <?php
                                        dokan_get_template_part( 'rma/conversations', '', array(
                                            'is_rma'        => true,
                                            'request'       => $request,
                                            'conversations' => $conversations,
                                            'from'          => 'vendor',
                                        ) );
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif ?>
            </article>


            <?php

                /**
                 *  dokan_order_content_inside_after hook
                 *
                 *  @since 2.4
                 */
                do_action( 'dokan_rma_request_content_inside_after' );
            ?>

        </div> <!-- #primary .content-area -->

        <?php

            /**
             *  dokan_dashboard_content_after hook
             *  dokan_rma_reqeusts_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_after' );
            do_action( 'dokan_rma_reqeusts_after' );

        ?>

    </div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>
