<?php
/**
 * Outputs a variation
 *
 * @var int $variation_id
 * @var WP_POST $variation
 * @var array $variation_data array of variation data
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

extract( $variation_data );
?>
<div class="dokan-product-variation-itmes">
    <h3 class="variation-topbar-heading">

        <strong>#<?php echo esc_html( $variation_id ); ?> </strong>
        <?php
            foreach ( $parent_data['attributes'] as $attribute ) {

                // Only deal with attributes that are variations
                if ( ! $attribute['is_variation'] || 'false' === $attribute['is_variation'] ) {
                    continue;
                }

                // Get current value for variation (if set)
                $variation_selected_value = isset( $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ] ) ? $variation_data[ 'attribute_' . sanitize_title( $attribute['name'] ) ] : '';

                // Name will be something like attribute_pa_color
                echo '<select class="dokan-form-control" name="attribute_' . sanitize_title( $attribute['name'] ) . '[' . $loop . ']"><option value="">' . __( 'Any', 'dokan' ) . ' ' . esc_html( wc_attribute_label( $attribute['name'] ) ) . '&hellip;</option>';

                // Get terms for attribute taxonomy or value if its a custom attribute
                if ( $attribute['is_taxonomy'] ) {

                    $post_terms = wp_get_post_terms( $parent_data['id'], $attribute['name'] );

                    foreach ( $post_terms as $term ) {
                        echo '<option ' . selected( $variation_selected_value, $term->slug, false ) . ' value="' . esc_attr( $term->slug ) . '">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) ) . '</option>';
                    }

                } else {

                    $options = wc_get_text_attributes( $attribute['value'] );

                    foreach ( $options as $option ) {
                        $selected = sanitize_title( $variation_selected_value ) === $variation_selected_value ? selected( $variation_selected_value, sanitize_title( $option ), false ) : selected( $variation_selected_value, $option, false );
                        echo '<option ' . $selected . ' value="' . esc_attr( $option ) . '">' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ) . '</option>';
                    }

                }

                echo '</select>';
            }
        ?>

        <input type="hidden" name="variable_post_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $variation_id ); ?>" />
        <input type="hidden" class="variation_menu_order" name="variation_menu_order[<?php echo $loop; ?>]" value="<?php echo isset( $menu_order ) ? absint( $menu_order ) : 0; ?>" />
        <div class="dokan-clearfix"></div>
    </h3>
    <div class="actions">
        <i class="fa fa-bars sort tips" data-title="<?php _e( 'Drag and drop, or click to set admin variation order', 'dokan' ); ?>" aria-hidden="true" ></i>
        <i class="fa fa-sort-desc fa-flip-horizointal toggle-variation-content" aria-hidden="true"></i>
        <a href="#" class="remove_variation delete" rel="<?php echo esc_attr( $variation_id ); ?>"><?php _e( 'Remove', 'dokan' ); ?></a>
    </div>

    <div class="dokan-variable-attributes woocommerce_variable_attributes wc-metabox-content" style="display: none;">
        <div class="data">
            <div class="content-half-part thumbnail-checkbox-options">
                <div class="upload_image">
                    <a href="#" class="upload_image_button tips <?php if ( $_thumbnail_id > 0 ) echo 'dokan-img-remove'; ?>" title="<?php if ( $_thumbnail_id > 0 ) { echo _e( 'Remove this image', 'dokan' ); } else { echo _e( 'Upload an image', 'dokan' ); } ?>" rel="<?php echo esc_attr( $variation_id ); ?>">
                        <img src="<?php if ( ! empty( $image ) ) echo esc_attr( $image ); else echo esc_attr( wc_placeholder_img_src() ); ?>" width="130px" height="130px"/>
                        <input type="hidden" name="upload_image_id[<?php echo $loop; ?>]" class="upload_image_id" value="<?php echo esc_attr( $_thumbnail_id ); ?>" />
                    </a>
                </div>
                <div class="dokan-form-group options">
                    <label><input type="checkbox" class="" name="variable_enabled[<?php echo $loop; ?>]" <?php checked( $variation->post_status, 'publish' ); ?> /> <?php _e( 'Enabled', 'dokan' ); ?></label>
                    <label><input type="checkbox" class="variable_is_downloadable" name="variable_is_downloadable[<?php echo $loop; ?>]" <?php checked( isset( $_downloadable ) ? $_downloadable : '', 'yes' ); ?> /> <?php _e( 'Downloadable', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" title="<?php _e( 'Enable this option if access is given to a downloadable file upon purchase of a product', 'dokan' ); ?>"></i></label>
                    <label><input type="checkbox" class="variable_is_virtual" name="variable_is_virtual[<?php echo $loop; ?>]" <?php checked( isset( $_virtual ) ? $_virtual : '', 'yes' ); ?> /> <?php _e( 'Virtual', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" title="<?php _e( 'Enable this option if a product is not shipped or there is no shipping cost', 'dokan' ); ?>"></i></label>

                    <?php if ( get_option( 'woocommerce_manage_stock' ) == 'yes' ) : ?>
                        <label><input type="checkbox" class="variable_manage_stock" name="variable_manage_stock[<?php echo $loop; ?>]" <?php checked( isset( $_manage_stock ) ? $_manage_stock : '', 'yes' ); ?> /> <?php _e( 'Manage stock?', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Enable this option to enable stock management at variation level', 'dokan' ); ?>"></i></label>
                    <?php endif; ?>

                    <?php do_action( 'dokan_variation_options', $loop, $variation_data, $variation ); ?>

                </div>
                <div class="dokan-clearfix"></div>
            </div>

            <div class="content-half-part">
                <?php if ( wc_product_sku_enabled() ) : ?>
                    <div class="sku">
                        <label><?php _e( 'SKU', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Enter a SKU for this variation or leave blank to use the parent product SKU.', 'dokan' ); ?>"></i></label>
                        <input type="text" class="dokan-form-control" size="5" name="variable_sku[<?php echo $loop; ?>]" value="<?php if ( isset( $_sku ) ) echo esc_attr( $_sku ); ?>" placeholder="<?php echo esc_attr( $parent_data['sku'] ); ?>" />
                    </div>
                <?php else : ?>
                    <input type="hidden" name="variable_sku[<?php echo $loop; ?>]" value="<?php if ( isset( $_sku ) ) echo esc_attr( $_sku ); ?>" />
                <?php endif; ?>

                <div class="stock-status">
                    <label><?php _e( 'Stock status', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Controls whether or not the product is listed as "in stock" or "out of stock" on the frontend.', 'dokan' ); ?>"></i></label>
                    <select name="variable_stock_status[<?php echo $loop; ?>]" class="dokan-form-control">
                        <?php
                            foreach ( $parent_data['stock_status_options'] as $key => $value ) {
                                echo '<option value="' . esc_attr( $key === $_stock_status ? '' : $key ) . '" ' . selected( $key === $_stock_status, true, false ) . '>' . esc_html( $value ) . '</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>

            <div class="dokan-clearfix"></div>
            <div class="variable_pricing">
                <div class="content-half-part">
                    <label><?php echo __( 'Regular price', 'dokan' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?></label>
                    <span class="vendor-earning">( <?php _e( ' You Earn : ', 'dokan' ) ?><?php echo get_woocommerce_currency_symbol() ?><span class="vendor-price">0.00</span> )</span>
                    <input type="text" size="5" name="variable_regular_price[<?php echo $loop; ?>]" value="<?php if ( isset( $_regular_price ) ) echo esc_attr( $_regular_price ); ?>" class="dokan-form-control dokan-product-regular-price-variable" placeholder="<?php esc_attr_e( 'Variation price (required)', 'dokan' ); ?>" />
                </div>
                <div class="content-half-part">
                    <label><?php echo __( 'Sale price', 'dokan' ) . ' (' . get_woocommerce_currency_symbol() . ')'; ?> <a href="#" class="sale_schedule"><?php _e( 'Schedule', 'dokan' ); ?></a><a href="#" class="cancel_sale_schedule" style="display:none"><?php _e( 'Cancel schedule', 'dokan' ); ?></a></label>
                    <input type="text" size="5" name="variable_sale_price[<?php echo $loop; ?>]" value="<?php if ( isset( $_sale_price ) ) echo esc_attr( $_sale_price ); ?>" class="dokan-form-control dokan-product-sales-price-variable" />
                </div>
                <div class="dokan-form-group dokan-clearfix product-edit-container">
                    <div class="dokan-product-less-price-alert dokan-hide">
                        <?php _e('Product price can\'t be less than the vendor fee!', 'dokan' ); ?>
                    </div>
                </div>
                <div class="dokan-clearfix"></div>
                <div class="sale_price_dates_fields dokan-form-group" style="display: none">
                    <div class="content-half-part">
                        <label><?php _e( 'Sale start date', 'dokan' ); ?></label>
                        <input type="text" class="dokan-form-control sale_price_dates_from" name="variable_sale_price_dates_from[<?php echo $loop; ?>]" value="<?php echo ! empty( $_sale_price_dates_from ) ? date_i18n( 'Y-m-d', $_sale_price_dates_from ) : ''; ?>" placeholder="<?php echo esc_attr_x( 'From&hellip;', 'placeholder', 'dokan' ) ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                    </div>
                    <div class="content-half-part">
                        <label><?php _e( 'Sale end date', 'dokan' ); ?></label>
                        <input type="text" class="dokan-form-control sale_price_dates_to" name="variable_sale_price_dates_to[<?php echo $loop; ?>]" value="<?php echo ! empty( $_sale_price_dates_to ) ? date_i18n( 'Y-m-d', $_sale_price_dates_to ) : ''; ?>" placeholder="<?php echo esc_attr_x('To&hellip;', 'placeholder', 'dokan') ?> YYYY-MM-DD" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
                    </div>
                    <div class="dokan-clearfix"></div>
                </div>
                <?php
                    /**
                     * dokan_variation_options_pricing action.
                     *
                     * @since 2.6
                     *
                     * @param int     $loop
                     * @param array   $variation_data
                     * @param WP_Post $variation
                     */
                    do_action( 'dokan_variation_options_pricing', $loop, $variation_data, $variation );
                ?>
            </div>

            <?php if ( 'yes' == get_option( 'woocommerce_manage_stock' ) ) : ?>

                <div class="dokan-form-group show_if_variation_manage_stock" style="display: none;">
                    <div class="content-half-part">
                        <label><?php _e( 'Stock quantity', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Enter a quantity to enable stock management at variation level, or leave blank to use the parent product\'s options.', 'dokan' ); ?>"></i></label>
                        <input type="number" class="dokan-form-control" size="5" name="variable_stock[<?php echo $loop; ?>]" value="<?php if ( isset( $_stock ) ) echo esc_attr( wc_stock_amount( $_stock ) ); ?>" step="any" />
                    </div>
                    <div class="content-half-part">
                        <label><?php _e( 'Allow backorders?', 'dokan' ); ?></label>
                        <select name="variable_backorders[<?php echo $loop; ?>]" class="dokan-form-control">
                            <?php
                                foreach ( $parent_data['backorder_options'] as $key => $value ) {
                                    echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key === $_backorders, true, false ) . '>' . esc_html( $value ) . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="dokan-clearfix"></div>
                    <?php
                        /**
                         * woocommerce_variation_options_inventory action.
                         *
                         * @since 2.5.0
                         *
                         * @param int     $loop
                         * @param array   $variation_data
                         * @param WP_Post $variation
                         */
                        do_action( 'dokan_variation_options_inventory', $loop, $variation_data, $variation );
                    ?>
                </div>

            <?php endif; ?>

            <?php if ( wc_product_weight_enabled() || wc_product_dimensions_enabled() ) : ?>

                <div class="weight-dimension">
                    <?php if ( wc_product_weight_enabled() ) : ?>
                        <div class="content-half-part hide_if_variation_virtual">
                            <label><?php echo __( 'Weight', 'dokan' ) . ' (' . esc_html( get_option( 'woocommerce_weight_unit' ) ) . ')'; ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Enter a weight for this variation or leave blank to use the parent product weight.', 'dokan' ); ?>"></i></label>
                            <input type="text" size="5" name="variable_weight[<?php echo $loop; ?>]" value="<?php if ( isset( $_weight ) ) echo esc_attr( $_weight ); ?>" placeholder="<?php echo esc_attr( $parent_data['weight'] ); ?>" class="dokan-form-control" />
                        </div>
                    <?php else : ?>
                        <div>&nbsp;</div>
                    <?php endif; ?>

                    <?php if ( wc_product_dimensions_enabled() ) : ?>
                        <div class="content-half-part dimensions_field hide_if_variation_virtual">
                            <label for="product_length"><?php echo __( 'Dimensions (L&times;W&times;H)', 'dokan' ) . ' (' . esc_html( get_option( 'woocommerce_dimension_unit' ) ) . ')'; ?></label>
                            <div class="dokan-form-group">
                                <input id="product_length" class="dokan-w3 dokan-form-control wc_input_decimal" size="6" type="text" name="variable_length[<?php echo $loop; ?>]" value="<?php if ( isset( $_length ) ) echo esc_attr( $_length ); ?>" placeholder="<?php echo esc_attr( $parent_data['length'] ); ?>" />
                                <input class="dokan-w3 dokan-form-control wc_input_decimal" size="6" type="text" name="variable_width[<?php echo $loop; ?>]" value="<?php if ( isset( $_width ) ) echo esc_attr( $_width ); ?>" placeholder="<?php echo esc_attr( $parent_data['width'] ); ?>" />
                                <input class="dokan-w3 dokan-form-control wc_input_decimal last" size="6" type="text" name="variable_height[<?php echo $loop; ?>]" value="<?php if ( isset( $_height ) ) echo esc_attr( $_height ); ?>" placeholder="<?php echo esc_attr( $parent_data['height'] ); ?>" />
                            </div>
                        </div>
                    <?php else : ?>
                        <div>&nbsp;</div>
                    <?php endif; ?>

                    <div class="dokan-clearfix"></div>
                </div>
            <?php endif; ?>

            <div>
                <div class="dokan-form-group hide_if_variation_virtual">
                    <label><?php _e( 'Shipping class', 'dokan' ); ?></label>
                    <?php
                        $args = array(
                            'taxonomy'          => 'product_shipping_class',
                            'hide_empty'        => 0,
                            'show_option_none'  => __( 'Same as parent', 'dokan' ),
                            'name'              => 'variable_shipping_class[' . $loop . ']',
                            'id'                => '',
                            'class'             => 'dokan-form-control',
                            'selected'          => isset( $shipping_class ) ? esc_attr( $shipping_class ) : '',
                            'echo'              => 0
                        );

                        echo wp_dropdown_categories( $args );
                    ?>
                </div>
                <?php if ( wc_tax_enabled() ) : ?>


                <div class="dokan-form-group form-row-full">
                    <label><?php _e( 'Tax class', 'dokan' ); ?></label>
                    <select class="dokan-form-control" name="variable_tax_class[<?php echo $loop; ?>]">
                        <option value="parent" <?php selected( is_null( $_tax_class ), true ); ?>><?php _e( 'Same as parent', 'dokan' ); ?></option>
                        <?php
                        foreach ( $parent_data['tax_class_options'] as $key => $value ) {
                            echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key === $_tax_class, true, false ) . '>' . esc_html( $value ) . '</option>';
                        }
                        ?>
                    </select>

                </div>

                <?php
                    /**
                     * dokan_variation_options_tax action.
                     *
                     * @since 2.6
                     *
                     * @param int     $loop
                     * @param array   $variation_data
                     * @param WP_Post $variation
                     */
                    do_action( 'dokan_variation_options_tax', $loop, $variation_data, $variation );
                ?>
                <?php endif; ?>

            </div>

            <div>
                <p class="dokan-form-group">
                    <label><?php _e( 'Variation description', 'dokan' ); ?></label>
                    <textarea class="dokan-form-control" name="variable_description[<?php echo $loop; ?>]" rows="3" style="width:100%;"><?php echo isset( $variation_data['_variation_description'] ) ? esc_textarea( $variation_data['_variation_description'] ) : ''; ?></textarea>
                </p>
            </div>

            <div class="show_if_variation_downloadable" style="display: none;">
                <div class="dokan-form-group downloadable_files">
                    <label><?php _e( 'Downloadable files', 'dokan' ); ?></label>
                    <table class="dokan-table dokan-table-striped">
                        <thead>
                            <div>
                                <th><?php _e( 'Name', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'This is the name of the download shown to the customer.', 'dokan' ); ?>"></i></th>
                                <th colspan="2"><?php _e( 'File URL', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'This is the URL or absolute path to the file which customers will get access to. URLs entered here should already be encoded.', 'dokan' ); ?>"></i></th>
                                <th>&nbsp;</th>
                            </div>
                        </thead>
                        <tbody>
                            <?php
                            if ( $_downloadable_files ) {
                                foreach ( $_downloadable_files as $key => $file ) {
                                    if ( ! is_array( $file ) ) {
                                        $file = array(
                                            'file' => $file,
                                            'name' => ''
                                        );
                                    }
                                    dokan_get_template_part( 'products/edit/html-product-variation-download', '', array(
                                        'pro'          => true,
                                        'key'          => $key,
                                        'file'         => $file,
                                        'variation_id' => $variation_id
                                    ) );
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <div>
                                <th colspan="4">
                                    <a href="#" class="dokan-btn dokan-btn-default insert-file-row" data-row="<?php
                                        $key  = '';
                                        $file = array(
                                            'file' => '',
                                            'name' => ''
                                        );
                                        ob_start();
                                        dokan_get_template_part( 'products/edit/html-product-variation-download', '', array(
                                            'pro'          => true,
                                            'key'          => $key,
                                            'file'         => $file,
                                            'variation_id' => $variation_id
                                        ) );
                                        echo esc_attr( ob_get_clean() );
                                    ?>"><?php _e( 'Add File', 'dokan' ); ?></a>
                                </th>
                            </div>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="dokan-form-group show_if_variation_downloadable" style="display: none;">
                <div class="content-half-part">
                    <label><?php _e( 'Download limit', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Leave blank for unlimited re-downloads.', 'dokan' ); ?>"></i></label>
                    <input type="text" class="dokan-form-control" name="variable_download_limit[<?php echo $loop; ?>]" value="<?php if ( isset( $_download_limit ) ) echo esc_attr( $_download_limit ); ?>" placeholder="<?php esc_attr_e( 'Unlimited', 'dokan' ); ?>" />
                </div>
                <div class="content-half-part">
                    <label><?php _e( 'Download expiry', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Enter the number of days before a download link expires, or leave blank.', 'dokan' ); ?>"></i></label>
                    <input type="text" class="dokan-form-control" name="variable_download_expiry[<?php echo $loop; ?>]" value="<?php if ( isset( $_download_expiry ) ) echo esc_attr( $_download_expiry ); ?>" placeholder="<?php esc_attr_e( 'Unlimited', 'dokan' ); ?>" />
                </div>

                <?php
                    /**
                     * dokan_variation_options_download action.
                     *
                     * @since 2.6
                     *
                     * @param int     $loop
                     * @param array   $variation_data
                     * @param WP_Post $variation
                     */
                    do_action( 'dokan_variation_options_download', $loop, $variation_data, $variation );
                ?>
                <div class="dokan-clearfix"></div>
            </div>

            <?php do_action( 'dokan_product_after_variable_attributes', $loop, $variation_data, $variation ); ?>
        </div>
    </div>
</div>
