<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

// Get the payment gateway
$payment_gateway = wc_get_payment_gateway_by_order( $order );

// Get line items
$line_items          = $order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'line_item' ) );
$line_items_fee      = $order->get_items( 'fee' );
$line_items_shipping = $order->get_items( 'shipping' );

if ( wc_tax_enabled() ) {
    $order_taxes         = $order->get_taxes();
    $tax_classes         = WC_Tax::get_tax_classes();
    $classes_options     = array();
    $classes_options[''] = __( 'Standard', 'dokan' );

    if ( $tax_classes ) {
        foreach ( $tax_classes as $class ) {
            $classes_options[ sanitize_title( $class ) ] = $class;
        }
    }

    // Older orders won't have line taxes so we need to handle them differently :(
    $tax_data = '';
    if ( $line_items ) {
        $check_item = current( $line_items );
        $tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
    } elseif ( $line_items_shipping ) {
        $check_item = current( $line_items_shipping );
        $tax_data = maybe_unserialize( isset( $check_item['taxes'] ) ? $check_item['taxes'] : '' );
    } elseif ( $line_items_fee ) {
        $check_item = current( $line_items_fee );
        $tax_data   = maybe_unserialize( isset( $check_item['line_tax_data'] ) ? $check_item['line_tax_data'] : '' );
    }

    $legacy_order     = ! empty( $order_taxes ) && empty( $tax_data ) && ! is_array( $tax_data );
    $show_tax_columns = ! $legacy_order || sizeof( $order_taxes ) === 1;
} else {
    $legacy_order = false;
    $order_taxes  = false;
}
?>
<div class="woocommerce_order_items_wrapper wc-order-items-editable">
    <table cellpadding="0" cellspacing="0" class="woocommerce_order_items dokan-table dokan-table-strip">
        <thead>
            <tr>
                <!-- <th><input type="checkbox" class="check-column" /></th> -->
                <th class="item sortable" colspan="2" data-sort="string-ins"><?php _e( 'Item', 'dokan' ); ?></th>

                <?php do_action( 'woocommerce_admin_order_item_headers' ); ?>

                <th class="item_cost sortable" data-sort="float"><?php _e( 'Cost', 'dokan' ); ?></th>
                <th class="quantity sortable" data-sort="int"><?php _e( 'Qty', 'dokan' ); ?></th>
                <th class="line_cost sortable" data-sort="float"><?php _e( 'Total', 'dokan' ); ?></th>

                <?php
                    if ( empty( $legacy_order ) && ! empty( $order_taxes ) ) :
                        foreach ( $order_taxes as $tax_id => $tax_item ) :
                            $tax_class      = wc_get_tax_class_by_tax_id( $tax_item['rate_id'] );
                            $tax_class_name = isset( $classes_options[ $tax_class ] ) ? $classes_options[ $tax_class ] : __( 'Tax', 'dokan' );
                            $column_label   = ! empty( $tax_item['label'] ) ? $tax_item['label'] : __( 'Tax', 'dokan' );
                            ?>
                                <th class="line_tax tips" data-tip="<?php
                                        echo esc_attr( $tax_item['name'] . ' (' . $tax_class_name . ')' );
                                    ?>">
                                    <?php echo esc_attr( $column_label ); ?>
                                    <input type="hidden" class="order-tax-id" name="order_taxes[<?php echo $tax_id; ?>]" value="<?php echo esc_attr( $tax_item['rate_id'] ); ?>">
                                    <a class="delete-order-tax" href="#" data-rate_id="<?php echo $tax_id; ?>"></a>
                                </th>
                            <?php
                        endforeach;
                    endif;
                ?>
                <th class="wc-order-edit-line-item" width="1%">&nbsp;</th>
            </tr>
        </thead>
        <tbody id="order_line_items">
        <?php
            foreach ( $line_items as $item_id => $item ) {
                $_product = $order->get_product_from_item( $item );

                dokan_get_template_part( 'orders/views/html-order-item', '', array(
                    'pro'          => true,
                    'order'        => $order,
                    'legacy_order' => $legacy_order,
                    'order_taxes'  => $order_taxes,
                    'item'         => $item,
                    'item_id'      => $item_id,
                    '_product'     => $_product
                ) );

                do_action( 'woocommerce_order_item_' . $item['type'] . '_html', $item_id, $item );
            }
        ?>
        </tbody>
        <tbody id="order_shipping_line_items">
        <?php
            $shipping_methods = WC()->shipping() ? WC()->shipping->load_shipping_methods() : array();
            foreach ( $line_items_shipping as $item_id => $item ) {
                dokan_get_template_part( 'orders/views/html-order-shipping', '', array(
                    'pro'              => true,
                    'item'             => $item,
                    'item_id'          => $item_id,
                    'order'            => $order,
                    'legacy_order'     => $legacy_order,
                    'order_taxes'      => $order_taxes,
                    'shipping_methods' => $shipping_methods
                ) );
            }
        ?>
        </tbody>
        <tbody id="order_fee_line_items">
        <?php
            foreach ( $line_items_fee as $item_id => $item ) {
                dokan_get_template_part( 'orders/views/html-order-fee', '', array(
                    'pro'          => true,
                    'item'         => $item,
                    'item_id'      => $item_id,
                    'order'        => $order,
                    'order_taxes'  => $order_taxes,
                    'legacy_order' => $legacy_order,
                ) );
            }
        ?>
        </tbody>
        <tbody id="order_refunds">
        <?php
            if ( $refunds = $order->get_refunds() ) {
                foreach ( $refunds as $refund ) {
                    dokan_get_template_part( 'orders/views/html-order-refund', '', array(
                        'pro'          => true,
                        'refund'       => $refund,
                        'order_taxes'  => $order_taxes,
                        'legacy_order' => $legacy_order
                    ) );
                }
            }
        ?>
        </tbody>
    </table>
</div>
<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
    <?php
        $coupons = $order->get_items( array( 'coupon' ) );
        if ( $coupons ) {
            ?>
            <div class="wc-used-coupons">
                <ul class="wc_coupon_list"><?php
                    echo '<li><strong>' . __( 'Coupon(s) Used', 'dokan' ) . '</strong></li>';
                    foreach ( $coupons as $item_id => $item ) {
                        $post_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'shop_coupon' AND post_status = 'publish' LIMIT 1;", $item['name'] ) );

                        $link = $post_id ? add_query_arg( array( 'post' => $post_id, 'view' => 'add_coupons', 'action' => 'edit' ), dokan_get_navigation_url( 'coupons' ) ) : dokan_get_navigation_url( 'coupons' );

                        echo '<li class="code"><a href="' . esc_url( $link ) . '" class="tips" data-tip="' . esc_attr( wc_price( $item['discount_amount'], array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ) ) . '"><span>' . esc_html( $item['name'] ). '</span></a></li>';
                    }
                ?></ul>
            </div>
            <?php
        }
    ?>
    <table class="wc-order-totals">
        <tr>
            <td><?php _e( 'Discount', 'dokan' ); ?> <span class="tips" data-tip="<?php _e( 'This is the total discount. Discounts are defined per line item.', 'dokan' ); ?>">[?]</span>:</td>
            <td class="total">
                <?php echo wc_price( $order->get_total_discount(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?>
            </td>
            <td width="1%"></td>
        </tr>

        <?php do_action( 'woocommerce_admin_order_totals_after_discount', dokan_get_prop( $order, 'id' ) ); ?>

        <tr>
            <td><?php _e( 'Shipping', 'dokan' ); ?> <span class="tips" data-tip="<?php _e( 'This is the shipping and handling total costs for the order.', 'dokan' ); ?>">[?]</span>:</td>
            <td class="total"><?php echo wc_price( $order->get_total_shipping(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
            <td width="1%"></td>
        </tr>

        <?php do_action( 'woocommerce_admin_order_totals_after_shipping', dokan_get_prop( $order, 'id' ) ); ?>

        <?php if ( wc_tax_enabled() ) : ?>
            <?php foreach ( $order->get_tax_totals() as $code => $tax ) : ?>
                <tr>
                    <td><?php echo $tax->label; ?>:</td>
                    <td class="total"><?php echo $tax->formatted_amount; ?></td>
                    <td width="1%"></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php do_action( 'woocommerce_admin_order_totals_after_tax', dokan_get_prop( $order, 'id' ) ); ?>

        <tr>
            <td><?php _e( 'Order Total', 'dokan' ); ?>:</td>
            <td class="total">
                <div class="view"><?php echo $order->get_formatted_order_total(); ?></div>
                <div class="edit" style="display: none;">
                    <input type="text" class="wc_input_price" id="_order_total" name="_order_total" placeholder="<?php echo wc_format_localized_price( 0 ); ?>" value="<?php echo ( isset( $data['_order_total'][0] ) ) ? esc_attr( wc_format_localized_price( $data['_order_total'][0] ) ) : ''; ?>" />
                    <div class="clear"></div>
                </div>
            </td>
            <td><?php if ( $order->is_editable() ) : ?><div class="wc-order-edit-line-item-actions"><a class="edit-order-item" href="#"></a></div><?php endif; ?></td>
        </tr>

        <?php do_action( 'woocommerce_admin_order_totals_after_total', dokan_get_prop( $order, 'id' ) ); ?>

        <tr>
            <td class="refunded-total"><?php _e( 'Refunded', 'dokan' ); ?>:</td>
            <td class="total refunded-total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
            <td width="1%"></td>
        </tr>

        <?php do_action( 'woocommerce_admin_order_totals_after_refunded', dokan_get_prop( $order, 'id' ) ); ?>

    </table>
    <div class="clear"></div>
</div>

<?php if ( current_user_can( 'dokan_manage_refund' ) && dokan_is_refund_allowed_to_approve( $order->get_id() ) ): ?>
    <div class="wc-order-data-row wc-order-bulk-actions">
        <p class="add-items">

            <?php if ( ( $order->get_total() - $order->get_total_refunded() ) > 0 ) : ?>
                <button type="button" class="dokan-btn dokan-btn-default refund-items"><?php _e( 'Request Refund', 'dokan' ); ?></button>
            <?php endif; ?>
        </p>
        <div class="clear"></div>
    </div>

    <?php if ( ( $order->get_total() - $order->get_total_refunded() ) > 0 ) : ?>
        <div class="wc-order-data-row wc-order-refund-items" style="display: none;">
            <table class="wc-order-totals dokan-table dokan-table-strip">

                <?php if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) : ?>
                    <tr>
                        <td><?php _e( 'Restock refunded items', 'dokan' ); ?>:</td>
                        <td class="total"><input type="checkbox" id="restock_refunded_items" name="restock_refunded_items" <?php checked( apply_filters( 'dokan_restock_refunded_items', true ) ); ?> /></td>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td><?php _e( 'Amount already refunded', 'dokan' ); ?>:</td>
                    <td class="total">-<?php echo wc_price( $order->get_total_refunded(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
                </tr>
                <tr>
                    <td><?php _e( 'Total available to refund', 'dokan' ); ?>:</td>
                    <td class="total"><?php echo wc_price( $order->get_total() - $order->get_total_refunded(), array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ); ?></td>
                </tr>
                <tr>
                    <td><label for="refund_amount"><?php _e( 'Refund amount', 'dokan' ); ?>:</label></td>
                    <td class="total">
                        <input type="text" class="text" id="refund_amount" name="refund_amount" class="wc_input_price" disabled="disabled" />
                        <div class="clear"></div>
                    </td>
                </tr>
                <tr>
                    <td><label for="refund_reason"><?php _e( 'Reason for refund (optional)', 'dokan' ); ?>:</label></td>
                    <td class="total">
                        <input type="text" class="text" id="refund_reason" name="refund_reason" />
                        <div class="clear"></div>
                    </td>
                </tr>
            </table>
            <div class="clear"></div>
            <div class="refund-actions">
                <?php
                $refund_amount = '<span class="wc-order-refund-amount">' . wc_price( 0, array( 'currency' => dokan_replace_func( 'get_order_currency', 'get_currency', $order ) ) ) . '</span>'; ?>

                <button type="button" class="dokan-btn dokan-btn-default do-manual-refund tips" data-tip="<?php esc_attr_e( 'You will need to manually issue a refund through your payment gateway after using this.', 'dokan' ); ?>"><?php printf( _x( 'Submit Refund Request %s', 'Submit Refund Request $amount', 'dokan' ), $refund_amount ); ?></button>
                <button type="button" class="dokan-btn dokan-btn-default cancel-action"><?php _e( 'Cancel', 'dokan' ); ?></button>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
        </div>
    <?php endif; ?>

<?php endif ?>
