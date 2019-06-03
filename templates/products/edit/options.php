<?php $_sold_individually = get_post_meta( $post_id, '_sold_individually', true ); ?>
<div class="dokan-form-horizontal">
    <div class="dokan-form-group">
        <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Purchase Note', 'dokan' ); ?></label>
        <div class="dokan-w6 dokan-text-left">
            <?php dokan_post_input_box( $post->ID, '_purchase_note', array(), 'textarea' ); ?>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w4 dokan-control-label" for="_enable_reviews"><?php _e( 'Reviews', 'dokan' ); ?></label>
        <div class="dokan-w4 dokan-text-left">
            <?php $_enable_reviews = ( $post->comment_status == 'open' ) ? 'yes' : 'no'; ?>
            <?php dokan_post_input_box( $post->ID, '_enable_reviews', array('value' => $_enable_reviews, 'label' => __( 'Enable Reviews', 'dokan' ) ), 'checkbox' ); ?>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w4 dokan-control-label" for="_purchase_note"><?php _e( 'Visibility', 'dokan' ); ?></label>
        <div class="dokan-w6 dokan-text-left">
            <select name="_visibility" id="_visibility" class="dokan-form-control">
                <?php foreach ( $visibility_options = dokan_get_product_visibility_options() as $name => $label ): ?>
                    <option value="<?php echo $name; ?>" <?php selected( $_visibility, $name ); ?>><?php echo $label; ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w4 dokan-control-label" for="_sold_individually"><?php _e( 'Sold Individually', 'dokan' ); ?></label>
        <div class="dokan-w7 dokan-text-left">
            <input name="_sold_individually" id="_sold_individually" value="yes" type="checkbox" <?php checked( $_sold_individually, 'yes' ); ?>>
            <?php _e( 'Allow only one quantity of this product to be bought in a single order', 'dokan' ) ?>
        </div>
    </div>

</div> <!-- .form-horizontal -->