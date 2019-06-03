<?php if ( isset( $is_enable_op_discount['product-discount'] ) && $is_enable_op_discount['product-discount'] == 'product-discount' ) : ?>
    <div class="dokan-discount-options dokan-edit-row dokan-clearfix">
        <div class="dokan-section-heading" data-togglehandler="dokan_discount_options">
            <h2><i class="fa fa-gift"></i> <?php _e( 'Discount Options', 'dokan' ); ?></h2>
            <p><?php _e( 'Set your discount for this product', 'dokan' ) ?></p>
            <a href="#" class="dokan-section-toggle">
                <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
            </a>
            <div class="dokan-clearfix"></div>
        </div>

        <div class="dokan-section-content">
            <label class="dokan-form-label" for="_is_lot_discount">
                <input type="checkbox" id="_is_lot_discount" name="_is_lot_discount" value="yes" <?php checked( $_is_lot_discount, 'yes' ); ?>>
                <?php _e( 'Enable bulk discount', 'dokan' ); ?>
            </label>

            <div class="show_if_needs_lot_discount <?php echo ( $_is_lot_discount=='yes') ? '' : 'dokan-hide'; ?>">
                <div class="content-half-part">
                    <label class="dokan-form-label" for="_lot_discount_quantity"><?php _e('Minimum quantity', 'dokan');?></label>
                    <?php dokan_post_input_box( $post_id, '_lot_discount_quantity', array( 'class'=>'dokan-form-control', 'placeholder' => __( '0', 'dokan' ), 'min' => 0, 'value' => $_lot_discount_quantity ), 'number' ); ?>
                </div>
                <div class="dokan-form-group content-half-part">
                    <label class="dokan-form-label" for="_lot_discount_amount"><?php _e('Discount %', 'dokan');?></label>
                    <div class="dokan-input-group">
                        <input id="_lot_discount_amount" value="<?php echo $_lot_discount_amount; ?>" name="_lot_discount_amount" placeholder="<?php _e( 'Percentage', 'dokan' ); ?>" class="dokan-form-control" type="number" min="0" max="100">
                        <span class="dokan-input-group-addon"><?php echo '%'; ?></span>
                    </div>
                </div>
                <div class="dokan-clearfix"></div>
            </div>
        </div>
    </div>
<?php endif;?>