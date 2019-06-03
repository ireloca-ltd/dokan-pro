<div class="dokan-form-group">
    <label class="dokan-w3 dokan-control-label" for="dokan_store_categories"><?php echo esc_html( $label ); ?></label>

    <div class="dokan-w5 dokan-text-left">
        <select
            class="dokan-select2 dokan-form-control"
            name="dokan_store_categories[]"
            id="dokan_store_categories"
            data-placeholder="<?php echo esc_html( $label ); ?>"
            <?php echo $is_multiple ? 'multiple': ''; ?>
        >
            <?php foreach ( $categories as $category ): ?>
                <option value="<?php echo esc_attr( $category->term_id ); ?>" <?php echo in_array( $category->term_id, $store_categories ) ? 'selected' : ''; ?>>
                    <?php echo esc_html( $category->name ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>
