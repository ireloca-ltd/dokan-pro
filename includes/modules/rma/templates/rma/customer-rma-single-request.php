<?php
/**
 * Customer Single request template
 *
 * @package dokan
 *
 * @since 1.0.0
 */
?>
<header>
    <h2><?php _e( 'Return Request', 'dokan' ); ?></h2>
</header>

<br>

<?php wc_print_notices(); ?>

<?php if ( empty( $request ) ): ?>
    <?php wc_print_notice( __( 'Request not found by this ID', 'dokan' ), 'error' ); ?>
<?php elseif( $request['customer']['id'] != get_current_user_id() ): ?>
    <?php wc_print_notice( __( 'Error! This is not your request.', 'dokan' ), 'error' ); ?>
<?php else: ?>
    <p><?php echo sprintf( __( 'Request ID is #<mark>%s</mark> on Order #<mark>%s</mark> is currently <mark>%s</mark>', 'dokan' ), $request['id'], $request['order_id'], dokan_warranty_request_status( $request['status'] ) ); ?></p>

    <table class="woocommerce-table shop_table" style="margin-top: 15px;">
        <tbody>
            <tr>
                <td><strong><?php _e( 'Last Updated :', 'dokan' ) ?></strong></td>
                <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $request['created_at'] ) ); ?></td>
            </tr>
            <tr>
                <td><strong><?php _e( 'Type :', 'dokan' ) ?></strong></td>
                <td><?php echo dokan_warranty_request_type( $request['type'] ); ?></td>
            </tr>
            <tr>
                <td><strong><?php _e( 'Products :', 'dokan' ) ?></strong></td>
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
            <tr>
                <td><strong><?php _e( 'Status :', 'dokan' ) ?></strong></td>
                <td><?php echo dokan_warranty_request_status( $request['status'] ); ?></td>
            </tr>
        </tbody>
    </table>

    <h3><?php _e( 'Additional Details', 'dokan' ) ?></h3>
    <hr>

    <p>
        <div class="details-label"><strong><?php _e( 'Reason : ', 'dokan' ) ?></strong></div>
        <div class="details">
            <?php echo dokan_rma_refund_reasons( $request['reasons'] ); ?>
        </div>
    </p>
    <p>
        <div class="details-label"><strong><?php _e( 'Reason Details : ', 'dokan' ) ?></strong></div>
        <div class="details">
            <?php
                echo ! empty( $request['details'] ) ? $request['details'] : __( 'No details found', 'dokan' );
            ?>
        </div>
    <p>

    <?php
        dokan_get_template_part( 'rma/conversations', '', array(
            'is_rma'        => true,
            'request'       => $request,
            'conversations' => $conversations,
            'from'          => 'customer'
        ) );
    ?>
<?php endif ?>

