<?php if ( $is_wholesale_customer ): ?>
    <?php if ( ! $is_active_wholesale ): ?>
        <li class="dokan-wholesale-migration-wrapper">
            <div class="woocommerce-info" style="margin-bottom:0px"><?php _e( 'Your wholesale customer request send to the admin. Please wait for approval', 'dokan' ); ?></div>
        </li>
    <?php endif ?>
<?php else: ?>
    <li class="dokan-wholesale-migration-wrapper">
        <div class="dokan-w8 left-content">
            <p><strong><?php _e( 'Become a Wholesale Customer', 'dokan' ) ?></strong></p>
            <p><?php _e( 'Vendor can sell products and manage a store with a beautiful dashboard', 'dokan' ) ?></p>
        </div>
        <div class="dokan-w4 right-content">
            <a href="#" class="btn btn-primary" id="dokan-become-wholesale-customer-btn" data-id="<?php echo get_current_user_id(); ?>"><?php _e( 'Become a wholesale customer', 'dokan' ); ?></a>
        </div>
        <div class="dokan-clearfix"></div>
    </li>
<?php endif ?>
