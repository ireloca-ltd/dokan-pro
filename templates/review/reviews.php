<?php
/**
 * Dokan Dahsbarod Review Main Template
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
         *  dokan_dashboard_review_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_dashboard_review_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-reviews-content">

        <?php

            /**
             *  dokan_review_content_inside_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_review_content_inside_before' );
        ?>


        <article class="dokan-reviews-area">

            <?php
                /**
                 * dokan_review_content_area_header hook
                 *
                 * @hooked dokan_review_header_render
                 *
                 * @since 2.4
                 */
                do_action( 'dokan_review_content_area_header' );


                /**
                 * dokan_review_content hook
                 *
                 * @since 2.4
                 */
                do_action( 'dokan_review_content' );

            ?>

        </article>

        <?php

            /**
             *  dokan_review_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_review_content_inside_after' );
        ?>

    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_dashboard_review_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_dashboard_review_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>