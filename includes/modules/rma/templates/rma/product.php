<div class="dokan-rma-options dokan-edit-row dokan-clearfix">
    <div class="dokan-section-heading" data-togglehandler="dokan_rma_options">
        <h2><i class="fa fa-undo" aria-hidden="true"></i> <?php _e( 'RMA Options', 'dokan' ); ?></h2>
        <p><?php _e( 'Set your return and warranty settigs for overwride global settings', 'dokan' ) ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>

    <div class="dokan-section-content">
        <div class="dokan-form-group">
            <label for="dokan_rma_product_override">
                <input type="hidden" name="dokan_rma_product_override" value="no">
                <input name="dokan_rma_product_override" id="dokan_rma_product_override" <?php checked( $override_default, 'yes' ); ?> value="yes" type="checkbox">
                <?php _e( 'Override your default RMA settings for this product', 'dokan' ); ?>
            </label>
        </div>

        <div class="dokan-product-rma-option-wrapper">
            <div class="dokan-form-group">
                <label class="form-label" for="dokan-rma-label"><?php _e( 'Label: ', 'dokan' ); ?>
                    <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Warrany label what customer will be see', 'dokan' ); ?>">
                        <i class="fa fa-question-circle"></i>
                    </span>
                </label>

                <input id="dokan-rma-label" value="<?php echo $rma_settings['label']; ?>" name="warranty_label" placeholder="<?php _e( 'Label', 'dokan' ); ?>" class="dokan-form-control input-md" type="text">
            </div>

            <div class="dokan-form-group">
                <label class="form-label" for="dokan-rma-type"><?php _e( 'Type: ', 'dokan' ); ?>
                    <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Warranty and Return Type', 'dokan' ); ?>">
                        <i class="fa fa-question-circle"></i>
                    </span>
                </label>
                <select name="warranty_type" id="dokan-warranty-type" class="dokan-form-control">
                    <?php foreach ( dokan_rma_warranty_type() as $warranty_key => $warranty_value ): ?>
                        <option value="<?php echo $warranty_key; ?>" <?php selected( $rma_settings['type'], $warranty_key ); ?>><?php echo $warranty_value; ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="dokan-form-group show_if_included_warranty">
                <label class="form-label" for="dokan-rma-type"><?php _e( 'Length: ', 'dokan' ); ?>
                    <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Warranty length, How many times( day, weeks month, years ) you want to give warranty ', 'dokan' ); ?>">
                        <i class="fa fa-question-circle"></i>
                    </span>
                </label>
                <select name="warranty_length" id="dokan-warranty-length" class="dokan-form-control">
                    <?php foreach ( dokan_rma_warranty_length() as $length_key => $length_value ): ?>
                        <option value="<?php echo $length_key; ?>" <?php selected( $rma_settings['length'], $length_key ); ?>><?php echo $length_value; ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="dokan-form-group content-half-part hide_if_lifetime show_if_included_warranty" style="padding-right: 10px;">
                <label class="form-label" for="dokan-rma-type"><?php _e( 'Length Value: ', 'dokan' ); ?>
                    <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Warranty length value', 'dokan' ); ?>">
                        <i class="fa fa-question-circle"></i>
                    </span>
                </label>
                <input type="number" class="dokan-form-control" min="0" step="1" name="warranty_length_value" value="<?php echo $rma_settings['length_value']; ?>">
            </div>

            <div class="dokan-form-group content-half-part hide_if_lifetime show_if_included_warranty">
                <label class="form-label" for="dokan-warranty-length-duration"><?php _e( 'Length Duration: ', 'dokan' ); ?>
                    <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Warranty length, How many times( day, weeks month, years ) you want to give warranty ', 'dokan' ); ?>">
                        <i class="fa fa-question-circle"></i>
                    </span>
                </label>
                <select name="warranty_length_duration" id="dokan-warranty-length-duration" class="dokan-form-control">
                    <?php foreach ( dokan_rma_warranty_length_duration() as $length_duration_key => $length_duration_value ): ?>
                        <option value="<?php echo $length_duration_key; ?>" <?php selected( $rma_settings['length_duration'], $length_duration_key ); ?>><?php echo $length_duration_value; ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="dokan-clearfix"></div>

            <?php if ( ! empty( $reasons ) ): ?>

                <div class="dokan-form-group">
                    <label class="form-label" for="dokan-warranty-length-duration"><?php _e( 'Refund Reasons: ', 'dokan' ); ?>
                        <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Select your return reasonas which will be displayed in customer end', 'dokan' ); ?>">
                            <i class="fa fa-question-circle"></i>
                        </span>
                    </label>
                    <?php foreach ( $reasons as $reason_key => $reason_value ): ?>
                        <div class="dokan-form-group">
                            <div class="checkbox">
                                <label for="warranty_reason[<?php echo $reason_key ?>]">
                                    <input name="warranty_reason[]" <?php echo in_array( $reason_key, $rma_settings['reasons'] ) ? 'checked' : '' ?> id="warranty_reason[<?php echo $reason_key ?>]" value="<?php echo $reason_key; ?>" type="checkbox"> <?php echo $reason_value; ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>

            <?php endif ?>

            <div class="dokan-form-group show_if_addon_warranty">
                <label class="form-label" for="dokan-warranty-length-duration"><?php _e( 'Add on Warranty settings: ', 'dokan' ); ?>
                    <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Warranty length, How many times( day, weeks month, years ) you want to give warranty ', 'dokan' ); ?>">
                        <i class="fa fa-question-circle"></i>
                    </span>
                </label>
                <table class="dokan-table dokan-rma-addon-warranty-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'Cost', 'dokan' ) ?></th>
                            <th><?php _e( 'Duration', 'dokan' ) ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ( ! empty( $rma_settings['addon_settings'] ) ): ?>
                            <?php foreach ( $rma_settings['addon_settings'] as $addon_setting ): ?>
                                <tr>
                                    <td>
                                        <div class="dokan-input-group">
                                            <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                            <input type="number" min="0" step="any" name="warranty_addon_price[]" class="dokan-form-control" id="warranty_addon_price[]" value="<?php echo $addon_setting['price']; ?>">
                                        </div>
                                    </td>

                                    <td width="45%">
                                        <input type="number" min="0" step="any" name="warranty_addon_length[]" class="dokan-form-control" id="warranty_addon_length[]" value="<?php echo $addon_setting['length']; ?>">
                                        <select name="warranty_addon_duration[]" id="warranty_addon_duration[]" class="dokan-form-control">
                                            <?php foreach ( dokan_rma_warranty_length_duration() as $length_duration_key => $length_duration_value ): ?>
                                                <option value="<?php echo $length_duration_key; ?>" <?php selected( $addon_setting['duration'], $length_duration_key ); ?>><?php echo $length_duration_value; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>

                                    <td width="20%">
                                        <a href="#" class="dokan-btn dokan-btn-default add-item"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        <a href="#" class="dokan-btn dokan-btn-default remove-item"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php else: ?>
                            <tr>
                                <td>
                                    <div class="dokan-input-group">
                                        <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                        <input type="number" min="0" step="any" name="warranty_addon_price[]" class="dokan-form-control" id="warranty_addon_price[]">
                                    </div>
                                </td>

                                <td width="45%">
                                    <input type="number" min="0" step="any" name="warranty_addon_length[]" class="dokan-form-control" id="warranty_addon_length[]">
                                    <select name="warranty_addon_duration[]" id="warranty_addon_duration[]" class="dokan-form-control">
                                        <?php foreach ( dokan_rma_warranty_length_duration() as $length_duration_key => $length_duration_value ): ?>
                                            <option value="<?php echo $length_duration_key; ?>"><?php echo $length_duration_value; ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </td>

                                <td width="20%">
                                    <a href="#" class="dokan-btn dokan-btn-default add-item"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                    <a href="#" class="dokan-btn dokan-btn-default remove-item"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>

            <div class="dokan-form-group">
                <label class="form-label" for="dokan-warranty-length-duration"><?php _e( 'RMA Policy: ', 'dokan' ); ?>
                    <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Your store return and warranty policy', 'dokan' ); ?>">
                        <i class="fa fa-question-circle"></i>
                    </span>
                </label>
                <?php wp_editor( $rma_settings['policy'], 'warranty_policy', [ 'editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'dokan_warranty_policy' ] ); ?>
            </div>

        </div>

    </div>
</div>
