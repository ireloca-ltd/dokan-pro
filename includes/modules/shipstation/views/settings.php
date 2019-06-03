<form method="post" id="dokan-shipstation-settings-form"  action="" class="dokan-form-horizontal">
    <div class="dokan-form-group">
        <label class="dokan-w4 dokan-control-label" for="dokan-shipstation-auth-key"><?php _e( 'Authentication Key', 'dokan' ); ?>
            <span class="dokan-tooltips-help tips" title="" data-placement="bottom" data-original-title="<?php _e( 'This is the Auth Key you set in ShipStation and allows ShipStation to communicate with your store.', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
        </label>
        <div class="dokan-w8 dokan-text-left">
            <code style="padding: 8px 10px; font-size: 13px;"><?php echo esc_html( $auth_key ); ?></code>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w4 dokan-control-label" for="dokan-shipstation-export-statuses"><?php _e( 'Export Order Statuses', 'dokan' ); ?>
            <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Define the order statuses you wish to export to ShipStation.', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
        </label>
        <div class="dokan-w8 dokan-text-left">
            <select name="dokan_shipstation_export_statuses[]" id="" class="dokan-select2 dokan-form-control" multiple>
                <?php foreach( $statuses as $status => $label ): ?>
                    <option value="<?php echo esc_attr( $status ); ?>"<?php echo in_array( $status, $export_statuses ) ? ' selected' : ''; ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="dokan-form-group">
        <label class="dokan-w4 dokan-control-label" for="dokan-shipstation-order-status"><?php _e( 'Shipped Order Status', 'dokan' ); ?>
            <span class="dokan-tooltips-help tips" data-placement="bottom" data-original-title="<?php _e( 'Define the order status you wish to update to once an order has been shipping via ShipStation. By default this is Completed.', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
        </label>
        <div class="dokan-w8 dokan-text-left">
            <select name="dokan_shipstation_shipped_status" id="" class="dokan-select2 dokan-form-control">
                <?php foreach( $statuses as $status => $label ): ?>
                    <option value="<?php echo esc_attr( $status ); ?>" <?php selected( $shipped_status, $status ); ?>><?php echo esc_html( $label ); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="dokan-form-group">
        <div class="dokan-w4">
            <?php wp_nonce_field( 'dokan_store_shipstation_form_action', 'dokan_store_shipstation_form_nonce' ); ?>
        </div>
        <div class="dokan-w8 dokan-text-left">
            <input
                type="submit"
                id='dokan-store-shipstation-form-submit'
                class="dokan-left dokan-btn dokan-btn-theme"
                value="<?php esc_attr_e( 'Save Changes', 'dokan' ); ?>"
            >
        </div>
    </div>
</form>
