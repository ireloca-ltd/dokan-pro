<button
    type="button"
    class="<?php echo esc_html( $button_classes ) ?>"
    data-vendor-id="<?php echo esc_attr( $vendor_id ); ?>"
    data-status="<?php echo esc_attr( $status ); ?>"
    data-is-logged-in="<?php echo esc_attr( $is_logged_in ); ?>"
>
    <span class="dokan-follow-store-button-label-current"><?php echo $label_current; ?></span>
    <span class="dokan-follow-store-button-label-unfollow"><?php echo $label_unfollow; ?></span>
</button>
