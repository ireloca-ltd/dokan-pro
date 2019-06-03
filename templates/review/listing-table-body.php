<?php
/**
 * Dokan Review Listing Table body Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<?php

if ( count( $comments ) == 0 ) {
    ?>
        <tr><td colspan="5"><?php _e( 'No Results Found', 'dokan' ); ?></td></tr>
    <?php
} else {

    foreach ( $comments as $comment ) {
        Dokan_Pro_Reviews::init()->render_row( $comment, $post_type );
    }

}
