<?php
/**
 *  Dokan Inbox Template
 *
 *  @since 2.6.0
 *
 *  @package dokan
 */
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">
    <?php

        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
    ?>

    <div class="dokan-dashboard-content">

        <?php

            /**
             *  dokan_chat_content_inside_before hook
             *
             *  @hooked show_seller_dashboard_notice
             *
             *  @since 2.4
             */
            do_action( 'dokan_chat_content_inside_before' );
        ?>

        <?php echo do_shortcode( '[dokan-chat-inbox]' ); ?>

         <?php

            /**
             *  dokan_chat_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_chat_content_inside_after' );
        ?>


    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>