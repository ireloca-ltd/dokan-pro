<?php

/**
 * Group product template
 *
 * @since 2.6.6
 *
 * @package Dokan
 */
?>

<div class="dokan-group-product-content show_if_grouped">
    <label for="crosssell_ids" class="form-label"><?php _e( 'Grouped products', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'This lets you choose which products are part of this group.', 'dokan' ); ?>"></i></label>
    <select class="dokan-form-control dokan-product-search" multiple="multiple" style="width: 100%;" id="grouped_products" name="grouped_products[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'dokan' ); ?>" data-action="dokan_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>" data-user_ids="<?php echo dokan_get_current_user_id(); ?>">
        <?php
            $product_ids = $product->get_children( 'edit' );

            foreach ( $product_ids as $product_id ) {
                $product = wc_get_product( $product_id );
                if ( is_object( $product ) ) {
                    echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                }
            }

        ?>
    </select>
</div>