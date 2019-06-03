<?php
/**
 * Dokan Account Migration Button Template
 *
 * @since 2.4
 *
 * @package dokan
 */

?>

<p>&nbsp;</p>

<ul class="dokan-account-migration-lists">
    <li>
        <div class="dokan-w8 left-content">
            <p><strong><?php _e( 'Become a Vendor', 'dokan' ) ?></strong></p>
            <p><?php _e( 'Vendor can sell products and manage a store with a beautiful dashboard', 'dokan' ) ?></p>
        </div>
        <div class="dokan-w4 right-content">
            <a href="<?php echo dokan_get_page_url( 'myaccount', 'woocommerce' ); ?>account-migration/seller/" class="btn btn-primary"><?php _e( 'Become a Vendor', 'dokan' ); ?></a>
        </div>
        <div class="dokan-clearfix"></div>
    </li>

    <?php do_action( 'dokan_customer_account_migration_list' ); ?>
</ul>
