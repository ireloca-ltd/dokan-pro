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
            do_action( 'dokan_rma_reqeusts_before' );

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
                do_action( 'dokan_rma_request_content_inside_before' );
            ?>


            <article class="dokan-rma-request-area">
                <?php  dokan_warranty_request_listing_status_filter(); ?>

                <table class="table table-striped rma-request-listing-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'Details', 'dokan' ) ?></th>
                            <th><?php _e( 'Products', 'dokan' ) ?></th>
                            <th><?php _e( 'Type', 'dokan' ) ?></th>
                            <th><?php _e( 'Status', 'dokan' ); ?></th>
                            <th><?php _e( 'Last Updated', 'dokan' ); ?></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ( $requests ): ?>
                            <?php foreach ( $requests as $request ): ?>
                                <tr>
                                    <td class="details">
                                        <?php
                                        if ( current_user_can( 'dokan_view_order' ) ) {
                                            $order_link = '<a href="' . wp_nonce_url( add_query_arg( array( 'order_id' => $request['order_id'] ), dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' ) . '"><strong>' . sprintf( __( 'Order %s', 'dokan-lite' ), esc_attr( $request['order_id'] ) ) . '</strong></a>';
                                        } else {
                                            $order_link = '<strong>' . sprintf( __( 'Order %s', 'dokan-lite' ), esc_attr( $request['order_id'] ) ) . '</strong>';
                                        }

                                        echo sprintf( '<a href="%s">#%d</a> by %s on %s', add_query_arg( [ 'request' => $request['id'] ], dokan_get_navigation_url( 'return-request' ) ), $request['id'], $request['customer']['name'], $order_link );
                                        ?>

                                        <div class="row-actions">
                                            <span class="edit">
                                                <a href="<?php echo add_query_arg( [ 'request' => $request['id'] ], dokan_get_navigation_url( 'return-request' ) ); ?>" class="request-manage"><?php _e( 'Manage', 'dokan' ); ?></a>
                                            </span> |
                                            <span class="delete">
                                                <a href="#" class="request-delete"><?php _e( 'Delete', 'dokan' ); ?></a>
                                            </span>
                                        </div>

                                    </td>
                                    <td>
                                        <?php
                                            $product_list = [];
                                            foreach ( $request['items'] as $item ) {
                                                $product_list[] = '<a href="' . $item['url'] . '">' . $item['title'] . '</a> <strong>x ' . $item['quantity'] . '</strong>';
                                            }

                                            echo implode( ', ', $product_list );
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo dokan_warranty_request_type( $request['type'] ); ?>
                                    </td>
                                    <td>
                                        <?php echo dokan_warranty_request_status( $request['status'] ); ?>
                                    </td>
                                    <td>
                                        <?php echo date_i18n( get_option( 'date_format' ), strtotime( $request['created_at'] ) ); ?>
                                    </td>
                                    <td>
                                        <a class="dokan-btn dokan-btn-default dokan-btn-sm tips" href="<?php echo add_query_arg( [ 'request' => $request['id'] ], dokan_get_navigation_url( 'return-request' ) ); ?>" data-toggle="tooltip" title="<?php _e( 'View', 'dokan' ); ?>" data-placement="top" title="%s"><i class="fa fa-eye">&nbsp;</i></a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <?php _e( 'No request found', 'dokan' ); ?>
                                </td>
                            </tr>
                        <?php endif ?>

                    </tbody>
                </table>

                <?php echo $pagination_html; ?>
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
