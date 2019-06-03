<?php
global $post;

$_downloadable   = get_post_meta( $post->ID, '_downloadable', true );
$_virtual   = get_post_meta( $post->ID, '_virtual', true );

$_is_lot_discount       = get_post_meta( $post->ID, '_is_lot_discount', true );
$_lot_discount_quantity = get_post_meta( $post->ID, '_lot_discount_quantity', true );
$_lot_discount_amount   = get_post_meta( $post->ID, '_lot_discount_amount', true );
$is_enable_op_discount  = dokan_get_option( 'discount_edit', 'dokan_selling', '' ) ? dokan_get_option( 'discount_edit', 'dokan_selling', '' ) : array();

?>
<div class="update-button-wrap">
    <input type="submit" name="dokan_update_product" class="dokan-btn dokan-btn-theme dokan-btn-lg" value="<?php esc_attr_e( 'Update Product', 'dokan' ); ?>"/>
</div>

<div class="toggle-sidebar-container">

    <div class="dokan-post-status dokan-toggle-sidebar">
        <label for="post_status"><?php _e( 'Product Status:', 'dokan' ); ?></label>

        <?php $pending_class = $post->post_status == 'pending' ? '  dokan-label dokan-label-warning': ''; ?>
        <span class="dokan-toggle-selected-display<?php echo $pending_class; ?>"><?php echo dokan_get_post_status( $post->post_status ); ?></span>

        <?php if ( $post->post_status != 'pending' ) { ?>
            <a class="dokan-toggle-edit dokan-label dokan-label-success" href="#"><?php _e( 'Edit', 'dokan' ); ?></a>

            <div class="dokan-toggle-select-container dokan-hide">

                <?php $post_statuses = apply_filters( 'dokan_post_status', array(
                    'publish' => __( 'Online', 'dokan' ),
                    'draft'   => __( 'Draft', 'dokan' )
                ), $post ); ?>

                <select id="post_status" class="dokan-toggle-select" name="post_status">
                    <?php foreach ($post_statuses as $status => $label) { ?>
                        <option value="<?php echo $status; ?>"<?php selected( $post->post_status, $status ); ?>><?php echo $label; ?></option>
                    <?php } ?>
                </select>

                <a class="dokan-toggle-save dokan-btn dokan-btn-default dokan-btn-sm" href="#"><?php _e( 'OK', 'dokan' ); ?></a>
                <a class="dokan-toggle-cacnel" href="#"><?php _e( 'Cancel', 'dokan' ); ?></a>
            </div> <!-- #dokan-toggle-select -->
        <?php } ?>
    </div>

    <div class="product-type dokan-toggle-sidebar">
        <label for="product_type"><?php _e( 'Product Type:', 'dokan' ); ?></label>

        <?php
        $supported_types = apply_filters( 'dokan_product_type_selector', array(
            'simple'    => __( 'Simple product', 'dokan' ),
            'variable'  => __( 'Variable product', 'dokan' )
        ) );

        if ( $terms = wp_get_object_terms( $post->ID, 'product_type' ) ) {
            $product_type = sanitize_title( current( $terms )->name );
        } else {
            $product_type = 'simple';
        }


        if ( !array_key_exists( $product_type, $supported_types) ) {
            $product_type = 'simple';
        }
        ?>

        <span class="dokan-toggle-selected-display"><?php echo dokan_get_product_types( $product_type ); ?></span>
        <a class="dokan-toggle-edit dokan-label dokan-label-success" href="#"><?php _e( 'Edit', 'dokan' ); ?></a>

            <div class="dokan-toggle-select-container dokan-hide">
                <select name="product_type" id="product_type" class="dokan-toggle-select">
                    <?php
                    foreach ( $supported_types as $value => $label ) {
                        echo '<option value="' . esc_attr( $value ) . '" ' . selected( $product_type, $value, false ) .'>' . esc_html( $label ) . '</option>';
                    }
                    ?>

                </select>

                <a class="dokan-toggle-save dokan-btn dokan-btn-default dokan-btn-sm" href="#"><?php _e( 'OK', 'dokan' ); ?></a>
                <a class="dokan-toggle-cacnel" href="#"><?php _e( 'Cancel', 'dokan' ); ?></a>
            </div> <!-- #dokan-toggle-select -->

    </div> <!-- .product-type -->
</div>

<?php do_action( 'dokan_product_edit_before_sidebar' ); ?>

<aside class="downloadable downloadable_files show_if_simple">
    <div class="dokan-side-head">
        <label class="checkbox-inline">
            <input type="checkbox" id="_downloadable" name="_downloadable" value="yes"<?php checked( $_downloadable, 'yes' ); ?>>
            <?php _e( 'Downloadable Product', 'dokan' ); ?>
        </label>
    </div> <!-- .dokan-side-head -->

    <div class="dokan-side-body<?php echo ($_downloadable == 'yes' ) ? '' : ' dokan-hide'; ?>">
        <ul class="list-unstyled ">
            <li class="dokan-form-group">

                <table class="dokan-table dokan-table-condensed">
                    <tfoot>
                        <tr>
                            <th>
                                <a href="#" class="insert-file-row dokan-btn dokan-btn-sm dokan-btn-success" data-row="<?php
                                    $file = array(
                                        'file' => '',
                                        'name' => ''
                                    );
                                    ob_start();
                                    include DOKAN_INC_DIR . '/woo-views/html-product-download.php';
                                    echo esc_attr( ob_get_clean() );
                                ?>"><?php _e( 'Add File', 'dokan' ); ?></a>
                            </th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php
                        $downloadable_files = get_post_meta( $post->ID, '_downloadable_files', true );

                        if ( $downloadable_files ) {
                            foreach ( $downloadable_files as $key => $file ) {
                                include DOKAN_INC_DIR . '/woo-views/html-product-download.php';
                            }
                        }
                        ?>
                    </tbody>
                </table>

            </li>
            <li class="dokan-form-group">
                <div class="dokan-input-group">
                    <span class="dokan-input-group-addon"><?php _e( 'Limit', 'dokan' ); ?></span>
                    <?php dokan_post_input_box( $post->ID, '_download_limit', array( 'placeholder' => __( 'Number of times', 'dokan' ) ) ); ?>
                </div>
            </li>
            <li class="dokan-form-group">
                <div class="dokan-input-group">
                    <span class="dokan-input-group-addon">Expiry</span>
                    <?php dokan_post_input_box( $post->ID, '_download_expiry', array( 'placeholder' => __( 'Number of days', 'dokan' ) ) ); ?>
                </div>
            </li>
        </ul>
    </div> <!-- .dokan-side-body -->
</aside> <!-- .downloadable -->

<aside class="virtual virtual_product show_if_simple">
    <div class="dokan-side-head">
        <label class="checkbox-inline">
            <input type="checkbox" id="_virtual" name="_virtual" value="yes"<?php checked( $_virtual, 'yes' ); ?>>
            <?php _e( 'Virtual Product', 'dokan' ); ?>
        </label>
    </div> <!-- .dokan-side-head -->
</aside>

<?php do_action( 'dokan_product_edit_after_downloadable' ); ?>

<?php if ( ! is_int( key( $is_enable_op_discount ) ) && array_key_exists( 'product-discount', $is_enable_op_discount ) == "product-discount" ) : ?>
    <aside class="product_lot_discount">
        <div class="dokan-side-head">
            <label class="dokan-checkbox-inline dokan-form-label" for="_is_lot_discount">
                <input type="checkbox" id="_is_lot_discount" name="_is_lot_discount" value="yes" <?php checked( $_is_lot_discount, 'yes' ); ?>>
                <?php _e( 'Enable bulk discount', 'dokan' );?>
            </label>
        </div> <!-- .dokan-side-head -->


            <div class="dokan-side-body show_if_needs_lot_discount <?php echo ($_is_lot_discount=='yes') ? '' : 'hide_if_lot_discount' ;?>">
                <div class="dokan-form-group">
                    <div class="dokan-input-group">
                        <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                        <?php dokan_post_input_box( $post->ID, '_lot_discount_quantity', array( 'placeholder' => __( 'Minimum quantity', 'dokan' ), 'min' => 0, 'value' => $_lot_discount_quantity ), 'number' ); ?>
                    </div>
                </div>
                <div class="dokan-form-group">
                    <div class="dokan-input-group">
                        <?php dokan_post_input_box( $post->ID, '_lot_discount_amount', array( 'placeholder' => __( 'Discount %', 'dokan' ), 'min' => 0, 'value' => $_lot_discount_amount ), 'number' ); ?>
                        <span class="dokan-input-group-addon" style="position: relative;left: -9%;"><?php echo '%'; ?></span>
                    </div>
                </div>
                </ul>
            </div> <!-- .dokan-side-body -->

    </aside> <!-- .downloadable -->
<?php endif; ?>

<?php do_action( 'dokan_product_edit_after_discount' ); ?>

<aside class="product-gallery">
    <div class="dokan-side-head">
        <?php _e( 'Image Gallery', 'dokan' ); ?>
    </div>

    <div class="dokan-side-body" id="dokan-product-images">
        <div class="dokan-product-gallery">
            <div id="product_images_container">
                <ul class="product_images dokan-clearfix">
                    <?php
                    $product_images = get_post_meta( $post_id, '_product_image_gallery', true );
                    $gallery = explode( ',', $product_images );

                    if ( $gallery ) {
                        foreach ($gallery as $image_id) {
                            if ( empty( $image_id ) ) {
                                continue;
                            }

                            $attachment_image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                            ?>
                            <li class="image" data-attachment_id="<?php echo $image_id; ?>">
                                <img src="<?php echo $attachment_image[0]; ?>" alt="">
                                <a href="#" class="action-delete" title="<?php esc_attr_e( 'Delete image', 'dokan' ); ?>">&times;</a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                    <li class="add-image add-product-images tips" data-title="<?php _e( 'Add gallery image', 'dokan' ); ?>">
                        <a href="#" class="add-product-images"><i class="fa fa-plus" aria-hidden="true"></i></a>
                    </li>
                </ul>

                <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_images ); ?>">
            </div>
        </div>
    </div>

        <!-- <a href="#" class="add-product-images dokan-btn dokan-btn-success"><?php _e( '+ Add product images', 'dokan' ); ?></a> -->
    </div>
</aside> <!-- .product-gallery -->

<?php do_action( 'dokan_product_edit_after_sidebar' ); ?>
