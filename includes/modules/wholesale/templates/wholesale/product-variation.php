<div class="dokan-form-group">
    <label for="variable_wholesale_enable[<?php echo $loop; ?>]">
        <input name="variable_wholesale_enable[<?php echo $loop; ?>]" value="no" type="hidden">
        <input name="variable_wholesale_enable[<?php echo $loop; ?>]" id="variable_wholesale_enable[<?php echo $loop; ?>]" <?php checked( $enable_wholesale, 'yes' ); ?> value="yes" type="checkbox">
        <?php _e( 'Enable wholesale for this product', 'dokan' ); ?>
    </label>
</div>
<div class="dokan-form-group dokan-clearfix">
    <div class="content-half-part">
        <label><?php echo __( 'Wholesale price', 'dokan' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></label>
        <input type="text" size="5" name="variable_wholesale_price[<?php echo $loop; ?>]" value="<?php echo esc_attr( $price ); ?>" class="dokan-form-control" placeholder="<?php esc_attr_e( 'Variation wholesale price', 'dokan' ); ?>" />
    </div>
    <div class="content-half-part">
        <label><?php echo __( 'Wholesale Quantity', 'dokan' ); ?></label>
        <input type="number" size="5" min="0" step="1" name="variable_wholesale_quantity[<?php echo $loop; ?>]" value="<?php echo esc_attr( $quantity ); ?>" class="dokan-form-control" />
    </div>
    <div class="dokan-clearfix"></div>
</div>
