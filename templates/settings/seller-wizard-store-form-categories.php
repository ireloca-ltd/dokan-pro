<tr>
    <th scope="row"><label for="dokan_store_categories[]"><?php echo esc_html( $label ); ?></label></th>
    <td>
        <select
            class="wc-enhanced-select"
            name="dokan_store_categories[]"
            id="dokan_store_categories[]"
            data-placeholder="<?php esc_html_e( 'Select categories', 'dokan' ); ?>"
            <?php echo $is_multiple ? 'multiple': ''; ?>
        >
            <?php foreach ( $categories as $category ): ?>
                <option value="<?php echo esc_attr( $category->term_id ); ?>" <?php echo in_array( $category->term_id, $store_categories ) ? 'selected' : ''; ?>>
                    <?php echo esc_html( $category->name ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
</tr>
