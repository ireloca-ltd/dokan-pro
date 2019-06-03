<p>
    <label for="<?php echo esc_attr( $scope_id ); ?>"><?php _e( 'Type', 'dokan' ); ?></label>
    <select name="<?php echo esc_attr( $scope_name ); ?>" id="<?php echo esc_attr( $scope_id ); ?>" class="widefat">
        <option value="both" <?php selected( $scope, 'both' ); ?>>
            <?php _e( 'Both', 'dokan' ); ?>
        </option>
        <option value="product" <?php selected( $scope, 'product' ); ?>>
            <?php _e( 'Product', 'dokan' ); ?>
        </option>
        <option value="vendor" <?php selected( $scope, 'vendor' ); ?>>
            <?php _e( 'Vendor', 'dokan' ); ?>
        </option>
    </select>
</p>

<p>
    <input
        type="checkbox"
        class="checkbox"
        id="<?php echo esc_attr( $display_id ); ?>"
        name="<?php echo esc_attr( $display_name ); ?>"
        value="inline"
        <?php checked( $display, 'inline' ); ?>
    >
    <label for="<?php echo esc_attr( $display_id ); ?>"><?php _e( 'Display Inline', 'dokan' ); ?></label>
</p>
