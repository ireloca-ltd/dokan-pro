<form class="dokan-form-container" id="<?php echo esc_attr( $id ); ?>">
    <div><?php echo $text; ?></div>

    <fieldset>
        <ul class="<?php echo $option_list_classes; ?>">
            <?php foreach( $abuse_reasons as $abuse_reason ): ?>
                <li>
                    <label class="<?php echo $option_label_classes; ?>">
                        <input required type="radio" name="reason" value="<?php echo esc_attr( $abuse_reason['value'] ); ?>"> <?php echo esc_html( $abuse_reason['value'] ); ?>
                    </label>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ( ! is_user_logged_in() && 'off' === $reported_by_logged_in_users_only ): ?>
            <div class="dokan-form-group">
                <label><?php esc_html_e( 'Your Name', 'dokan' ); ?></label>
                <input type="text" required class="dokan-form-control" name="customer_name">
            </div>

            <div class="dokan-form-group">
                <label><?php esc_html_e( 'Your Email', 'dokan' ); ?></label>
                <input type="email" required class="dokan-form-control" name="customer_email">
            </div>
        <?php endif; ?>

        <div class="dokan-form-group">
            <label><?php esc_html_e( 'Description', 'dokan' ); ?></label>
            <textarea name="description" class="dokan-form-control" rows="4"></textarea>
        </div>

        <div class="dokan-form-group">
            <p class="dokan-popup-error"></p>

            <button type="submit" class="dokan-w4 dokan-btn dokan-btn-theme" id="dokan-report-abuse-form-submit-btn">
                <?php esc_html_e( 'Report Abuse', 'dokan' ); ?>
            </button>

            <button type="button" class="dokan-w4 dokan-btn dokan-btn-theme dokan-hide" id="dokan-report-abuse-form-working-btn">
                <i class="fa fa-refresh fa-spin"></i>&nbsp;&nbsp;<?php esc_html_e( 'Reporting', 'dokan-lite' ); ?>...
            </button>
        </div>
    </fieldset>
</form>
<div class="dokan-clearfix"></div>
