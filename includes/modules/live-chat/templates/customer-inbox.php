<?php
/**
 * Customer inbox templage
 *
 * @since 1.1
 */

do_action( 'woocommerce_account_navigation' ); ?>

<div class="woocommerce-MyAccount-content">
    <div id="customer-inbox">
        <?php echo do_shortcode( '[dokan-chat-inbox]' ); ?>
    </div>
</div>
