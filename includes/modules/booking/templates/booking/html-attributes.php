<div class="dokan-attribute-variation-options dokan-edit-row dokan-clearfix">
    <div class="dokan-section-heading" data-togglehandler="dokan_attribute_variation_options">
        <h2><i class="fa fa-search" aria-hidden="true"></i> <?php _e( 'Search Attribute', 'dokan' ); ?></h2>
        <p><?php _e( 'Manage attributes', 'dokan' ); ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
        </a>

        <div class="dokan-clearfix"></div>
    </div>
    <div class="dokan-section-content">
        <div class="dokan-product-attribute-wrapper">

            <ul class="dokan-attribute-option-list">
                <?php
                global $wc_product_attributes;

                // Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
                $attributes           = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );
                // Output All Set Attributes
                if ( ! empty( $attributes ) ) {
                    $attribute_keys  = array_keys( $attributes );
                    $attribute_total = sizeof( $attribute_keys );

                    for ( $i = 0; $i < $attribute_total; $i ++ ) {
                        $attribute     = $attributes[ $attribute_keys[ $i ] ];
                        $position      = empty( $attribute['position'] ) ? 0 : absint( $attribute['position'] );
                        $taxonomy      = '';
                        $metabox_class = array();

                        if ( $attribute['is_taxonomy'] ) {
                            $taxonomy = $attribute['name'];

                            if ( ! taxonomy_exists( $taxonomy ) ) {
                                continue;
                            }

                            $attribute_taxonomy = $wc_product_attributes[ $taxonomy ];
                            $metabox_class[]    = 'taxonomy';
                            $metabox_class[]    = $taxonomy;
                            $attribute_label    = wc_attribute_label( $taxonomy );
                        } else {
                            $attribute_label    = apply_filters( 'woocommerce_attribute_label', $attribute['name'], $attribute['name'], false );
                        }

                        dokan_get_template_part( 'products/edit/html-product-attribute', '', array(
                            'pro'                => true,
                            'thepostid'          => $post_id,
                            'taxonomy'           => $taxonomy,
                            'attribute_taxonomy' => isset( $attribute_taxonomy ) ? $attribute_taxonomy : null,
                            'attribute_label'    => $attribute_label,
                            'attribute'          => $attribute,
                            'metabox_class'      => $metabox_class,
                            'position'           => $position,
                            'i'                  => $i
                        ) );

                    }
                }
                ?>
            </ul>

          <!-- We Removed the selector -->
          
        </div>
        <div class="dokan-product-variation-wrapper show_if_variable">
            <div id="dokan-variable-product-options">
                <div id="dokan-variable-product-options-inner"></div>
            </div>
        </div>
</div></div>
