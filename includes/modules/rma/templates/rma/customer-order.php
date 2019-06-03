<?php
/**
 * Template for customer warranty request
 *
 * @since 1.0.0
 *
 * @package dokan
 */
?>

<div class="dokan-warranty-request-wrap">
    <?php
        try {
            $order_id = get_query_var( 'request-warranty' );

            if ( ! $order_id ) {
                throw new Exception( __( 'No order found', 'dokan' ), 403 );
            }

            $order = wc_get_order( $order_id );

            if ( ! $order  ) {
                throw new Exception( __( 'Invalid order', 'dokan' ), 403 );
            }

            if ( $order->get_meta( 'has_sub_order' ) ) {
                dokan_order_show_suborders( $order );
            } else {
                dokan_get_template_part( 'rma/request-warranty', '', array(
                    'is_rma'    => true,
                    'order'     => $order,
                    'vendor_id' => dokan_get_seller_id_by_order( $order->get_id() )
                ) );
            }

        } catch ( Exception $e ) {
            wc_print_notice( $e->getMessage(), 'error' );
        }

    ?>
</div>
