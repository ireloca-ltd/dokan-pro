<?php
/**
 * Dokan Announcement Listing Template
 *
 * @since 2.2
 *
 * @package dokan
 */
?>
<div class="dokan-announcement-wrapper">
    <?php
    if( $notices ) {
        $i = 0;
        foreach ( $notices as $notice ) {
            $notice_url =  trailingslashit( dokan_get_navigation_url( 'single-announcement' ).''.$notice->ID );
            ?>
            <div class="dokan-announcement-wrapper-item <?php echo ( $notice->post_status == 'unread' ) ? 'dokan-announcement-uread' : '' ?>">
                <div class="announcement-action">
                    <a href="#" class="remove_announcement" data-notice_row = <?php echo $notice->ID; ?>><i class="fa fa-times"></i></a>
                </div>
                <div class="dokan-annnouncement-date dokan-left">
                    <div class="announcement-day"><?php echo date_i18n( 'd', strtotime( $notice->post_date ) ); ?></div>
                    <div class="announcement-month"><?php echo date_i18n( 'l', strtotime( $notice->post_date ) ); ?></div>
                    <div class="announcement-year"><?php echo date_i18n( 'Y', strtotime( $notice->post_date ) ); ?></div>
                </div>
                <div class="dokan-announcement-content-wrap dokan-left">
                    <div class="dokan-announcement-heading">
                        <a href="<?php echo $notice_url; ?>">
                            <h3><?php echo $notice->post_title; ?></h3>
                        </a>
                    </div>

                    <div class="dokan-announcement-content">
                        <?php echo wp_trim_words( $notice->post_content, '15', sprintf('<p><a href="%s">%s</a></p>', $notice_url , __( ' See More', 'dokan' ) ) );  ?>
                    </div>
                </div>
                <div class="dokan-clearfix"></div>
            </div>
            <?php
            $i++;
        }
    } else {
        ?>
        <div class="dokan-no-announcement">
            <div class="annoument-no-wrapper">
                <i class="fa fa-bell dokan-announcement-icon"></i>
                <p><?php _e( 'No announcement found', 'dokan' ) ?></p>
            </div>
        </div>

        <?php
    }
    ?>
    </div>
