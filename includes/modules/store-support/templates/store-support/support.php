<?php
$dss = Dokan_Store_Support::init();

$topic_id = get_query_var( 'support' );

if( is_numeric( $topic_id ) ) {
    $topic = $dss->get_single_topic( $topic_id, dokan_get_current_user_id() );
}
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *  dokan_dashboard_support_content_before
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_dashboard_support_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-support-listing dokan-support-topic-wrapper">

        <?php
        if ( empty( $topic ) || isset( $_GET['ticket_status'] ) ) {
            ?>
        <header class="dokan-dashboard-header">
            <?php $title =  __( 'Support Tickets', 'dokan' ); ?>
            <h1 class="entry-title"><?php echo apply_filters( 'dss_vendor_support_title', $title ) ?></h1>
        </header><!-- .dokan-dashboard-header -->

        <?php $dss->support_topic_status_list() ?>
        <?php
            $dss->print_support_topics_by_seller( dokan_get_current_user_id() );
        } else {
            $dss->print_single_topic( $topic );
        }
        ?>

    </div><!-- .dokan-dashboard-content -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_dashboard_support_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_dashboard_support_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>
