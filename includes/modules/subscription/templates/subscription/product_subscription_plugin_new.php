<?php
/**
 * Template Name: product Subscription
 */

?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_subcription_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_subcription_content_before' );
    ?>

	<div class="dokan-dashboard-content">

		<?php

            /**
             *  dokan_subscription_content_inside_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_subscription_content_inside_before' );


	        if ( dokan_is_seller_enabled( get_current_user_id() ) ) {
	            echo do_shortcode( '[dps_product_pack]' );
	        } else {
	            dokan_seller_not_enabled_notice();
	        }

            /**
             *  dokan_subscription_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_subscription_content_inside_after' );
        ?>


	</div><!-- #primary .content-area -->

	<?php
        /**
         *  dokan_dashboard_content_after hook
         *  dokan_subscription_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_subscription_content_after' );
    ?>

</div>

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>
