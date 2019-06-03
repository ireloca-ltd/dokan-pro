<div class="dokan-w4">
    <select
        class="dokan-select2 dokan-form-control"
        name="dokan_seller_category"
    >
        <option value=""><?php echo esc_html( __( 'Store Category', 'dokan' ) ); ?></option>
        <?php foreach ( $categories as $category ): ?>
            <option value="<?php echo esc_attr( $category->slug ); ?>" <?php echo ( $category->slug === $category_query ) ? 'selected' : ''; ?>>
                <?php echo esc_html( $category->name ); ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<script>
    jQuery( document ).ready( function ( $ ) {
        var form = $( '.dokan-seller-search-form' ),
            category = form.find( '[name="dokan_seller_category"]' );

        form.on( 'dokan_seller_search_populate_data', function ( e, data ) {
            data.store_category = category.val();
        } );

        category.on( 'change', function () {
            form.trigger( 'dokan_seller_search' );
        } );
    } );
</script>
