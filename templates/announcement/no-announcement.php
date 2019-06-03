<?php
/**
 * Dokan No Announcement Found Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<article class="dokan-notice-single-notice-area">
    <header class="dokan-dashboard-header dokan-clearfix">
        <span class="left-header-content">
            <h1 class="entry-title"><?php _e( 'Notice', 'dokan' ); ?></h1>
        </span>
    </header>
    <div class="dokan-error">
        <?php echo sprintf( "<p>%s <a href='%s'>%s</a></p", __( 'No Notice found; ', 'dokan' ), dokan_get_navigation_url('announcement'), __( 'Back to all Notice', 'dokan' ) ) ?>
    </div>
</article>
