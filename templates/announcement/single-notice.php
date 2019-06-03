<?php
/**
 * Dokan Single Announement Content Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<article class="dokan-notice-single-notice-area">
    <header class="dokan-dashboard-header dokan-clearfix">
        <span class="left-header-content">
            <h1 class="entry-title"><?php echo $notice_data->post_title; ?></h1>
        </span>
    </header>
    <span class="dokan-single-announcement-date"><i class="fa fa-calendar"></i> <?php echo date('F j, Y ', strtotime( $notice_data->post_date ) ); ?></span>

    <div class="entry-content">
        <?php echo wpautop( $notice_data->post_content ); ?>
    </div>

    <div class="dokan-announcement-link">
        <a href="<?php echo dokan_get_navigation_url( 'announcement' ) ?>" class="dokan-btn dokan-btn-theme"><?php _e( 'Back to all Notice', 'dokan' ); ?></a>
    </div>

</article>
