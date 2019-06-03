<?php
/**
 * Dokan Featured Seller Widget Content Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>

<?php if ( $sellers ) : ?>
    <ul class="dokan-feature-sellers">
        <?php
            foreach ( $sellers as $key => $seller ) {
                $store_info = dokan_get_store_info( $seller->ID );
                $rating = dokan_get_seller_rating( $seller->ID );
                $display_rating = $rating['rating'];

                if ( ! $rating['count'] ) {
                    $display_rating = __( 'No ratings found yet!', 'dokan' );
                }
                ?>
                <li>
                    <a href="<?php echo dokan_get_store_url( $seller->ID ); ?>">
                        <?php echo esc_html( $store_info['store_name'] ); ?>
                    </a><br />
                    <i class='fa fa-star'></i>
                    <?php echo $display_rating; ?>
                </li>

                <?php
            }
        ?>
    </ul>
<?php else : ?>
    <p><?php _e( 'No vendor found', 'dokan' ); ?></p>
<?php endif; ?>
