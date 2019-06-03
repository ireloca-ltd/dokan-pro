<?php
/**
 * Dokan Best Seller Widget Content Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<ul class="dokan-best-sellers">
    <?php

    if ( $seller ) {

        foreach ( $seller as $key => $value ) {
            $store_info = dokan_get_store_info( $value->seller_id );
            $rating = dokan_get_seller_rating( $value->seller_id );
            $display_rating = $rating['rating'];

            if ( ! $rating['count'] ) {
                $display_rating = __( 'No ratings found yet!', 'dokan' );
            }
            ?>
            <li>
                <a href="<?php echo dokan_get_store_url( $value->seller_id ); ?>">
                    <?php echo esc_html( $store_info['store_name'] ); ?>
                </a><br />
                <i class='fa fa-star'></i>
                <?php echo $display_rating; ?>
            </li>
            <?php
        }
    } else {
        ?>
        <p><?php _e( 'No vendor found', 'dokan' ); ?></p>
        <?php
    }
    ?>
</ul>
