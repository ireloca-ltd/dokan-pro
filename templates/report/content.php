<?php
/**
 * Dokan Review Content Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<div class="dokan-report-wrap">
    <ul class="dokan_tabs">
    <?php
    foreach ( $charts['charts'] as $key => $value ) {
        if ( isset( $value['permission'] ) && ! current_user_can( $value['permission'] ) ) {
            continue;
        }

        $class = ( $current == $key ) ? ' class="active"' : '';
        printf( '<li%s><a href="%s">%s</a></li>', $class, add_query_arg( array( 'chart' => $key ), $link ), $value['title'] );
    }
    ?>
    </ul>

    <?php if ( isset( $charts['charts'][$current] ) ) { ?>
        <?php if ( isset( $charts['charts'][$current]['permission'] ) && ! current_user_can( $charts['charts'][$current]['permission'] ) ): ?>
            <?php
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this report', 'dokan' ) ) );
            ?>
        <?php else: ?>
            <div id="dokan_tabs_container">
                <div class="tab-pane active" id="home">
                    <?php
                    $func = $charts['charts'][$current]['function'];
                    if ( $func && ( is_callable( $func ) ) ) {
                        call_user_func( $func );
                    }
                    ?>
                </div>
            </div>
        <?php endif ?>

    <?php } ?>
</div>
