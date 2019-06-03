<?php
/**
 *  Dahboard Coupon Header Template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>
<header class="dokan-dashboard-header">
    <span class="left-header-content">
        <h1 class="entry-title">
            <?php _e( 'Coupon', 'dokan' ); ?>
        </h1>

        <?php if ( ! $is_edit_page && current_user_can( 'dokan_add_coupon' ) ) { ?>
            <span class="left-header-content dokan-left">
                <a href="<?php echo add_query_arg( array( 'view' => 'add_coupons' ), dokan_get_navigation_url( 'coupons' ) ); ?>" class="dokan-btn dokan-btn-theme dokan-left"><i class="fa fa-gift">&nbsp;</i> <?php _e( 'Add new Coupon', 'dokan' ); ?></a>
            </span>
        <?php } ?>
    </span>
    <div class="dokan-clearfix"></div>
</header><!-- .entry-header -->
