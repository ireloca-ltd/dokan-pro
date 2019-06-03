<?php
/**
 * Settings discount template
 *
 * @since 2.6
 */
if ( isset( $is_enable_op_discount['order-discount'] ) && $is_enable_op_discount['order-discount'] == 'order-discount' ) {
    ?>
    <div class="dokan-form-group">
        <label class="dokan-w3 dokan-control-label"><?php _e( 'Discount ', 'dokan' ); ?></label>
        <div class="dokan-w5 dokan-text-left">
            <div class="checkbox">
                <label class="dokan-control-label" for="lbl_setting_minimum_quantity">
                    <input type="hidden" name="setting_show_minimum_discount_option" value="no">
                    <input id="lbl_setting_minimum_quantity" type="checkbox" name="setting_show_minimum_order_discount_option" value="yes"<?php checked( $is_enable_order_discount, 'yes' ); ?>>
                    <?php _e( 'Enable storewide discount', 'dokan' ); ?>
                </label>
            </div>
            <div class="show_if_needs_sw_discount <?php echo ($is_enable_order_discount=='yes') ? '' : 'hide_if_order_discount' ;?>">
                <div class="dokan-text-left dokan-form-group">
                    <input id="setting_minimum_order_amount" value="<?php echo $setting_minimum_order_amount; ?>" name="setting_minimum_order_amount" placeholder="<?php _e( 'Minimum Order Amount', 'dokan' ); ?>" class="dokan-form-control input-md" type="number">
                </div>
                <div class="dokan-text-left dokan-form-group">
                    <input id="setting_order_percentage" value="<?php echo $setting_order_percentage; ?>" name="setting_order_percentage" placeholder="<?php _e( 'Percentage', 'dokan' ); ?>" class="dokan-form-control input-md" type="number" min="1" max="100">
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>