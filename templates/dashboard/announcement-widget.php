<?php
/**
 *  Dokan Dashboard Template
 *
 *  Dokan Dahsboard Announcement widget template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>
 <div class="dashboard-widget dokan-announcement-widget">
    <div class="widget-title">
        <i class="fa fa-bullhorn" aria-hidden="true"></i> <?php _e( 'Latest Announcement', 'dokan' ); ?>

        <span class="pull-right">
            <a href="<?php echo $announcement_url; ?>"><?php _e( 'See All', 'dokan' ); ?></a>
        </span>
    </div>
    <?php

    ?>
    <?php if ( $notices ): ?>
        <ul class="list-unstyled">
            <?php foreach ( $notices as $notice ): ?>
                <?php
                    $notice_url =  trailingslashit( dokan_get_navigation_url( 'single-announcement' ).''.$notice->ID );
                 ?>
                <li>
                    <div class="dokan-dashboard-announce-content dokan-left">
                        <a href="<?php echo $notice_url; ?>"><h3><?php echo $notice->post_title; ?></h3></a>
                        <?php echo wp_trim_words( $notice->post_content, 6, '...' ); ?>
                    </div>
                    <div class="dokan-dashboard-announce-date dokan-right <?php echo ( $notice->status == 'unread' ) ? 'dokan-dashboard-announce-unread' : 'dokan-dashboard-announce-read'; ?>">
                        <div class="announce-day"><?php echo date_i18n( 'd', strtotime( $notice->post_date ) ); ?></div>
                        <div class="announce-month"><?php echo date_i18n( 'l', strtotime( $notice->post_date ) ); ?></div>
                        <div class="announce-year"><?php echo date_i18n( 'Y', strtotime( $notice->post_date ) ); ?></div>
                    </div>
                    <div class="dokan-clearfix"></div>
                </li>
            <?php endforeach ?>
        </ul>
    <?php else: ?>
        <div class="dokan-no-announcement">
            <div class="annoument-no-wrapper">
                <i class="fa fa-bell dokan-announcement-icon"></i>
                <p><?php _e( 'No announcement found', 'dokan' ) ?></p>
            </div>
        </div>
    <?php endif ?>
</div> <!-- .products -->
