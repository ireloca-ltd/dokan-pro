<?php
/**
 *  Dokan Dashboard staffs Template
 *
 *  Load staffs related template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>
<?php
    $is_edit = ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && ! empty( $_GET['staff_id'] ) ) ? $_GET['staff_id'] : 0;
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
        do_action( 'dokan_staffs_content_before' );

    ?>

    <div class="dokan-dashboard-content dokan-staffs-content">

        <?php

            /**
             *  dokan_staffs_content_inside_before hook
             *
             *  @hooked show_seller_enable_message
             *
             *  @since 2.4
             */
            do_action( 'dokan_add_staffs_content_inside_before' );
        ?>

        <header class="dokan-dashboard-header">
            <span class="left-header-content">
                <h1 class="entry-title">
                    <?php
                        if ( !$is_edit ) {
                            _e( 'Add New Staff', 'dokan' );
                        } else {
                            _e( 'Edit Staff', 'dokan' );
                        }
                    ?>
                </h1>
            </span>
            <div class="dokan-clearfix"></div>
        </header><!-- .entry-header -->

        <article class="dokan-staffs-area">
            <?php
                do_action( 'dokan_add_staff_content' );
            ?>
        </article>


        <?php

            /**
             *  dokan_staffs_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_staffs_content_inside_after' );
        ?>

    </div> <!-- #primary .content-area -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_staffs_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_staffs_content_after' );

    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>