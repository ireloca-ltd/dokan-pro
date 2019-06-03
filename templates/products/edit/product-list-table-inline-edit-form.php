<tr class="dokan-product-list-inline-edit-form dokan-hide">
    <td colspan="11">
        <fieldset>
            <div class="dokan-clearfix">
                <div class="dokan-w3 dokan-inline-edit-column">
                    <strong class="dokan-inline-edit-section-title"><?php esc_html_e( 'Quick Edit', 'dokan' ); ?></strong>

                    <div class="inline-edit-col dokan-clearfix">
                        <label class="dokan-w3">
                            <?php esc_html_e( 'Title', 'dokan' ); ?>
                        </label>
                        <div class="dokan-w9">
                            <input type="text" class="dokan-form-control" data-field-name="post_title" value="<?php echo esc_html( $post_title ); ?>">
                        </div>
                    </div>
                </div>
                <div class="dokan-w6 dokan-inline-edit-column">
                    <label>
                        <?php esc_html_e( 'Product Tags', 'dokan' ); ?>
                    </label>

                    <select data-field-name="product_tag" class="dokan-form-control" multiple>
                        <?php foreach ( $options['tags'] as $tag ): ?>
                            <option value="<?php echo esc_attr( $tag->term_id ); ?>"<?php echo in_array( $tag->term_id, $product_tag ) ? ' selected' : ''; ?>>
                                <?php echo esc_html( $tag->name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>
                        <input type="checkbox" data-field-name="reviews_allowed" value="open" <?php checked( $reviews_allowed, true ); ?>> &nbsp;
                        <?php esc_html_e( 'Enable Reviews', 'dokan' ); ?>
                    </label>

                    <label>
                        <?php esc_html_e( 'Status', 'dokan' ); ?> &nbsp;

                        <?php if ( 'pending' === $post_status ) : ?>
                            <span class="dokan-label dokan-label-danger">
                                <?php esc_html_e( 'Pending Review', 'dokan' ); ?>
                                <input type="hidden" data-field-name="post_status" value="<?php echo esc_attr( 'pending' ); ?>">
                            </span>
                        <?php else: ?>
                            <select data-field-name="post_status" style="min-width: 100px;">
                                <?php foreach ( $options['post_statuses'] as $post_status_slug => $post_status_label ): ?>
                                    <option value="<?php echo esc_attr( $post_status_slug ); ?>"<?php selected( $post_status, $post_status_slug ); ?>>
                                        <?php echo esc_html( $post_status_label ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </label>

                    <hr>

                    <strong class="dokan-inline-edit-section-title"><?php esc_html_e( 'Product Data', 'dokan' ); ?></strong>

                    <?php if ( $options['is_sku_enabled'] ): ?>
                        <div class="dokan-inline-edit-field-row dokan-clearfix">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'SKU', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <input type="text" class="dokan-form-control" data-field-name="sku" value="<?php echo esc_html( $sku ); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( 'simple' === $product_type ): ?>
                        <div class="dokan-inline-edit-field-row dokan-clearfix">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'Price', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <input type="text" class="dokan-form-control" data-field-name="_regular_price" value="<?php echo esc_html( $_regular_price ); ?>">
                            </div>
                        </div>

                        <div class="dokan-inline-edit-field-row dokan-clearfix">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'Sale', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <input type="text" class="dokan-form-control" data-field-name="_sale_price" value="<?php echo esc_html( $_sale_price ); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $options['is_weight_enabled'] ): ?>
                        <div class="dokan-inline-edit-field-row dokan-clearfix">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'Weight', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <input type="text" class="dokan-form-control" data-field-name="weight" value="<?php echo esc_html( $weight ); ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $options['is_dimensions_enabled'] ): ?>
                        <div class="dokan-inline-edit-field-row dokan-clearfix">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'L/W/H', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9 dokan-clearfix">
                                <div class="dokan-w4">
                                    <input type="text" class="dokan-form-control" data-field-name="length" value="<?php echo esc_html( $length ); ?>" placeholder="<?php esc_html_e( 'Length', 'dokan' ); ?>">
                                </div>
                                <div class="dokan-w4">
                                    <input type="text" class="dokan-form-control" data-field-name="width" value="<?php echo esc_html( $width ); ?>" placeholder="<?php esc_html_e( 'Width', 'dokan' ); ?>">
                                </div>
                                <div class="dokan-w4">
                                    <input type="text" class="dokan-form-control" data-field-name="height" value="<?php echo esc_html( $height ); ?>" placeholder="<?php esc_html_e( 'Height', 'dokan' ); ?>">
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( ( 'simple' === $product_type && ! $is_virtual ) || 'variable' === $product_type ): ?>
                        <div class="dokan-inline-edit-field-row dokan-clearfix">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'Shipping class', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <select data-field-name="shipping_class_id" class="dokan-form-control">
                                    <option value="_no_shipping_class"><?php esc_html_e( 'No shipping class', 'dokan' ); ?></option>

                                    <?php foreach ( $options['shipping_classes'] as $shipping_class_obj ): ?>
                                        <option value="<?php echo esc_attr( $shipping_class_obj->term_id ); ?>"<?php selected( $shipping_class_id, $shipping_class_obj->term_id ); ?>>
                                            <?php echo esc_html( $shipping_class_obj->name ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="dokan-inline-edit-field-row dokan-clearfix">
                        <label class="dokan-w3">
                            <?php esc_html_e( 'Visibility', 'dokan' ); ?>
                        </label>
                        <div class="dokan-w9">
                            <select data-field-name="_visibility" class="dokan-form-control">
                                <?php foreach ( $options['visibilities'] as $visibility_slug => $visibility_name ): ?>
                                    <option value="<?php echo esc_attr( $visibility_slug ); ?>"<?php selected( $_visibility, $visibility_slug ); ?>>
                                        <?php echo esc_html( $visibility_name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <?php if ( ( 'simple' === $product_type || 'variable' === $product_type ) && $options['can_manage_stock'] ): ?>
                        <label>
                            <input type="checkbox" data-field-name="manage_stock" value="open" data-field-toggler <?php checked( $manage_stock, true ); ?>> &nbsp;
                            <?php esc_html_e( 'Manage Stock', 'dokan' ); ?>
                        </label>

                        <div class="dokan-inline-edit-field-row dokan-clearfix<?php echo $manage_stock ? '' : ' dokan-hide'; ?>" data-field-toggle="manage_stock" data-field-show-on="true">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'Stock Qty', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <input type="text" class="dokan-form-control" data-field-name="stock_quantity" value="<?php echo esc_html( $stock_quantity ); ?>">
                            </div>
                        </div>

                        <div class="dokan-inline-edit-field-row dokan-clearfix<?php echo $manage_stock ? ' dokan-hide' : ''; ?>" data-field-toggle="manage_stock" data-field-show-on="false">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'In Stock?', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <select data-field-name="stock_status" class="dokan-form-control">
                                    <?php foreach ( $options['stock_statuses'] as $stock_status_slug => $stock_status_name ): ?>
                                        <option value="<?php echo esc_attr( $stock_status_slug ); ?>"<?php selected( $stock_status, $stock_status_slug ); ?>>
                                            <?php echo esc_html( $stock_status_name ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="dokan-inline-edit-field-row dokan-clearfix<?php echo $manage_stock ? '' : ' dokan-hide'; ?>" data-field-toggle="manage_stock" data-field-show-on="true">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'Backorders?', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <select data-field-name="backorders" class="dokan-form-control" style="width: 100%;">
                                    <?php foreach ( $options['backorder_options'] as $backorders_slug => $backorders_name ): ?>
                                        <option value="<?php echo esc_attr( $backorders_slug ); ?>"<?php selected( $backorders, $backorders_slug ); ?>>
                                            <?php echo esc_html( $backorders_name ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php elseif ( 'grouped' === $product_type ): ?>
                        <div class="dokan-inline-edit-field-row dokan-clearfix">
                            <label class="dokan-w3">
                                <?php esc_html_e( 'In Stock?', 'dokan' ); ?>
                            </label>
                            <div class="dokan-w9">
                                <select data-field-name="stock_status" class="dokan-form-control">
                                    <?php foreach ( $options['stock_statuses'] as $stock_status_slug => $stock_status_name ): ?>
                                        <option value="<?php echo esc_attr( $stock_status_slug ); ?>"<?php selected( $stock_status, $stock_status_slug ); ?>>
                                            <?php echo esc_html( $stock_status_name ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dokan-w3 dokan-inline-edit-column">
                    <label>
                        <?php esc_html_e( 'Product categories', 'dokan' ); ?>
                    </label>

                    <?php if ( ! $options['using_single_category_style'] ): ?>
                        <select data-field-name="product_cat" class="dokan-form-control" multiple>
                            <?php foreach ( $options['categories'] as $category ): ?>
                                <option value="<?php echo esc_attr( $category->term_id ); ?>"<?php echo in_array( $category->term_id, $product_cat ) ? ' selected' : ''; ?>>
                                    <?php echo esc_html( $category->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <select data-field-name="product_cat" class="dokan-form-control">
                            <?php foreach ( $options['categories'] as $category ): ?>
                                <option value="<?php echo esc_attr( $category->term_id ); ?>"<?php echo selected( $product_cat, $category->term_id ); ?>>
                                    <?php echo esc_html( $category->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php endif; ?>
                </div>
            </div>

            <div class="dokan-clearfix">
                <button type="button" class="dokan-btn dokan-btn-default inline-edit-cancel">
                    <?php _e( 'Cancel', 'dokan' ); ?>
                </button>

                <div class="dokan-right inline-edit-submit-button">
                    <div class="dokan-spinner"></div>
                    <button type="button" class="dokan-btn dokan-btn-default dokan-btn-theme dokan-right inline-edit-update">
                        <?php _e( 'Update', 'dokan' ); ?>
                    </button>
                </div>
            </div>

            <input type="hidden" data-field-name="ID" value="<?php echo esc_attr( $product_id ); ?>">
            <input type="hidden" data-field-name="product_type" value="<?php echo esc_attr( $product_type ); ?>">
        </fieldset>
    </td>
</tr>
