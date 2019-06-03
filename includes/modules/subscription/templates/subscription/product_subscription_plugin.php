<?php
/**
 * Template Name: product Subscription
 */

?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">
	<?php dokan_get_template( 'dashboard-nav.php', array( 'active_menu' => 'subscription' ) ); ?>

	<div class="dokan-dashboard-content dokan-subscription-content">

	        <?php

	        if ( dokan_is_seller_enabled( get_current_user_id() ) ) {
	            echo do_shortcode( '[dps_product_pack]' );
	        } else {
	            dokan_seller_not_enabled_notice();
	        }

	        ?>

	</div><!-- #primary .content-area -->
</div>

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>