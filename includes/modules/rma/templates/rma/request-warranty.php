<?php
/**
 * Request Warranty Template for Customer
 *
 * @package dokan
 *
 * @since 1.0.0
 */
?>

<?php
$vendor          = dokan()->vendor->get( $vendor_id );
$store_name      = $vendor->get_store_name();
$default_reasons = dokan_rma_refund_reasons();
$store_warranty  = get_user_meta( $vendor->get_id(), '_dokan_rma_settings', true );
$count_warranty_product = 0;
?>

<?php wc_print_notices(); ?>

<h3>
    <?php _e( 'Store Name: ', 'dokan' ) ?>
    <?php echo $store_name; ?>
</h3>

<form method="post">
    <table class="woocommerce-orders-table shop_table shop_table_responsive dokan-rma-order-item-table">
        <thead>
            <tr>
                <th></th>
                <th><?php _e( 'Product Name', 'dokan' ) ?></th>
                <th><?php _e( 'Price', 'dokan' ) ?></th>
                <th><?php _e( 'Vendor', 'dokan' ) ?></th>
                <th><?php _e( 'Qty', 'dokan' ) ?></th>
                <th><?php _e( 'Warranty Details', 'dokan' ) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $order->get_items('line_item') as $item ): ?>
                <?php
                    $_product = $order->get_product_from_item( $item );
                    $vendor_id = get_post_field( 'post_author', $_product->get_id() );

                    $warranty_item = new Dokan_Warranty_Item( $item->get_id() );
                    $has_warranty  = $warranty_item->has_warranty();

                    if ( $has_warranty ) {
                        $count_warranty_product++;
                    }
                ?>
                <tr>
                    <th>
                        <?php if ( $has_warranty ): ?>
                            <input type="checkbox" name="request_item[]" value="<?php echo $_product->get_id() ?>">
                            <input type="hidden" name="request_item_id[]" value="<?php echo $item->get_id() ?>">
                        <?php endif ?>
                    </th>
                    <td><a href="<?php echo $_product->get_permalink(); ?>"><?php echo $_product->get_title(); ?></a></td>
                    <td><?php echo $_product->get_price_html(); ?></td>
                    <td>
                        <?php
                            $author = get_post_field( 'post_author', $_product->get_id() );
                            $vendor = dokan()->vendor->get( $author );
                            echo '<a href="' . $vendor->get_shop_url() . '">' . $vendor->get_shop_name() . '</a>';
                        ?>
                    </td>
                    <td>
                        <?php if ( $warranty_item->get_quantity_remaining() > 0 ): ?>
                            <select name="request_item_qty[]" id="request_item_qty[]">
                                <?php
                                    for ( $i=1; $i <= $item->get_quantity(); $i++ ) {
                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                    }
                                ?>
                            </select>

                        <?php else: ?>
                            <p><?php _e( 'Quantity Unavailable', 'dokan' ); ?></p>
                        <?php endif ?>
                    </td>

                    <td>
                        <?php
                            $warranty = dokan_get_order_item_warranty( $item );
                            echo dokan_get_warranty_duration_string( $warranty, $order );
                        ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <?php if ( $count_warranty_product ): ?>
        <div class="warranty-form-row">
            <label for=""><?php _e( 'I want to request for a', 'dokan' ) ?></label>
            <select name="type" id="type">
                <?php foreach ( dokan_warranty_request_type() as $type_key => $type_value ): ?>
                    <option value="<?php echo $type_key; ?>"><?php echo $type_value; ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <?php if ( ! empty( $store_warranty['reasons'] ) ): ?>
            <div class="warranty-form-row">
                <label for=""><?php _e( 'Select reason to request for warranty', 'dokan' ) ?></label>
                <select name="reasons" id="reasons">
                    <?php foreach ( $default_reasons as $key => $reason ): ?>
                        <?php if ( in_array( $key, $store_warranty['reasons'] ) ): ?>
                            <option value="<?php echo $key; ?>"><?php echo $reason; ?></option>
                        <?php endif ?>
                    <?php endforeach ?>
                </select>
            </div>

        <?php endif ?>

        <div class="warranty-form-row">
            <label for=""><?php _e( 'Tell details about yout problem', 'dokan' ) ?></label>
            <textarea name="details" id="warranty_request_details" rows="6"></textarea>
        </div>

        <div class="warranty-form-row">
            <input type="hidden" name="order_id" value="<?php echo $order->get_id(); ?>">
            <input type="hidden" name="vendor_id" value="<?php echo $vendor->get_id(); ?>">
            <?php wp_nonce_field( 'dokan_save_warranty_request', 'dokan_save_warranty_request_nonce' ); ?>
            <input type="submit" name="warranty_submit_request" class="dokan-btn dokan-btn-theme" value="<?php _e( 'Submit Request', 'dokan' ); ?>">
        </div>

    <?php endif ?>

</form>
