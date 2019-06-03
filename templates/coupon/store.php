<div class="code">
    <span class="outside">
        <span class="inside">
            <div class="coupon-title"><?php printf( __( '%s Discount', 'dokan' ), $coupon_amount_formatted ); ?></div>
            <div class="coupon-body">
                <?php if ( !empty( $coupon->post_content ) ) { ?>
                    <span class="coupon-details"><?php echo esc_html( $coupon->post_content ); ?></span>
                <?php } ?>
                <span class="coupon-code"><?php printf( __( 'Coupon Code: <strong>%s</strong>', 'dokan' ), $coupon->post_title ); ?></span>

                <?php if ( $expiry_date ) {
                    $expiry_date = is_object( $expiry_date ) ? $expiry_date->getTimestamp() : $expiry_date; ?>
                    <span class="expiring-in">(<?php printf( __( 'Expiring in %s', 'dokan' ), human_time_diff( $current_time, $expiry_date ) ); ?>)</span>
                <?php } ?>
            </div>
        </span>
    </span>
</div>
