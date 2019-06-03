<div id="dokan-spmv-products-admin">
    <hr>
    <label><?php esc_html_e( 'Other Available Vendor', 'dokan' ); ?></label>
    <?php if ( empty( $clones ) ): ?>
        <em><?php esc_html_e( 'No vendor own this same product.', 'dokan' ); ?></em>
    <?php else: ?>
        <ul class="dokan-spmv-products-admin-vendors">
            <?php foreach ( $clones as $product_id ): ?>
                <?php
                    $product = wc_get_product( $product_id );
                    if ( $product ):
                        $vendor = dokan_get_vendor_by_product( $product_id  );
                        $status = $product->get_status();
                        $status = get_post_status_object( $status );
                        ?>
                            <li>
                                <img src="<?php echo esc_url( $vendor->get_avatar() ); ?>" alt="<?php echo esc_attr( $vendor->get_shop_name() ); ?>">
                                <a class="shop-name" href="<?php echo esc_url( get_edit_post_link( $product_id, false ) ); ?>" target="_blank">
                                    <?php echo esc_html( $vendor->get_shop_name() ); ?>
                                    <span class="product-status">
                                        - <?php echo $status->label; ?>
                                    </span>
                                </a>
                                <span class="delete-product" data-product-id="<?php echo $product->get_id(); ?>">&times;</span>
                            </li>
                        <?php
                    endif;
                ?>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p><strong><?php esc_html_e( 'Assign product to vendors', 'dokan' ); ?></strong></p>
    <select id="dokan-spmv-products-admin-assign-vendors" data-placeholder="Select vendors" multiple></select>
    <button type="button" class="button" id="dokan-spmv-products-admin-assign-vendors-btn">
        <?php esc_html_e( 'Assign Products', 'dokan' ); ?>
    </button>
</div>
