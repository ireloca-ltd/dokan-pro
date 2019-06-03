<?php
/**
 * Dashboard Coupon template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">
    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_dashboard_coupon_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_dashboard_coupon_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-coupon-content">

        <?php

            /**
             *  dokan_coupon_content_inside_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_coupon_content_inside_before' );
        ?>

        <article class="dashboard-coupons-area">

            <?php
                /**
                 * dokan_coupon_content_header hook
                 *
                 * @hooked dokan_coupon_header_render
                 *
                 * @since 2.4
                 */
                do_action( 'dokan_coupon_content_area_header' );


                /**
                 * dokan_coupon_content hook
                 *
                 * @since 2.4
                 */
                do_action( 'dokan_coupon_content' );

            ?>

        </article>

        <?php

            /**
             *  dokan_coupon_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_coupon_content_inside_after' );
        ?>

    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_dashboard_coupon_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_dashboard_coupon_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>