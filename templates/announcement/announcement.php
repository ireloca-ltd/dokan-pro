<?php
/**
 * Dokan Announcement Template
 *
 * @since 2.2
 *
 * @package dokan
 */
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_dashboard_announcement_content_before
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_dashboard_announcement_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-notice-listing">

        <?php

            /**
             *  dokan_before_listing_notice hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_before_listing_notice' );
        ?>

        <article class="dokan-notice-listing-area">

            <?php

                /**
                 * dokan_announcement_content_area_header hook
                 *
                 * @since 2.4
                 */
                do_action( 'dokan_announcement_content_area_header' );

                /**
                 * dokan_announcement_content hook
                 *
                 * @since 2.4
                 */
                do_action( 'dokan_announcement_content' );

            ?>

        </article>

        <?php

            /**
             *  dokan_after_listing_notice hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_after_listing_notice' );
        ?>
    </div><!-- #primary .content-area -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_dashboard_announcement_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_dashboard_announcement_content_after' );
    ?>
</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>