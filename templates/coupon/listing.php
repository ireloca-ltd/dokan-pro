<?php
/**
 *  Dashboard Coupon listing template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>

<table class="dokan-table">
    <thead>
        <tr>
            <th><?php _e('Code', 'dokan'); ?></th>
            <th><?php _e('Coupon type', 'dokan'); ?></th>
            <th><?php _e('Coupon amount', 'dokan'); ?></th>
            <th><?php _e('Product IDs', 'dokan'); ?></th>
            <th><?php _e('Usage / Limit', 'dokan'); ?></th>
            <th><?php _e('Expiry date', 'dokan'); ?></th>
        </tr>
    </thead>

    <?php
        foreach( $coupons as $key => $post ) {
            ?>
            <tr>
                <td class="coupon-code" data-title="<?php _e('Code', 'dokan'); ?>">
                    <?php $edit_url =  wp_nonce_url( add_query_arg( array('post' => $post->ID, 'action' => 'edit', 'view' => 'add_coupons'), dokan_get_navigation_url( 'coupons' ) ), '_coupon_nonce', 'coupon_nonce_url' ); ?>
                    <div class="code">
                        <?php if ( current_user_can( 'dokan_edit_coupon' ) ): ?>
                            <a href="<?php echo $edit_url; ?>"><span><?php echo esc_attr( $post->post_title ); ?></span></a>
                        <?php else: ?>
                            <a href=""><span><?php echo esc_attr( $post->post_title ); ?></span></a>
                        <?php endif ?>
                    </div>

                    <div class="row-actions">
                        <?php $del_url = wp_nonce_url( add_query_arg( array('post' => $post->ID, 'action' => 'delete'), dokan_get_navigation_url( 'coupons' ) ) ,'_coupon_del_nonce', 'coupon_del_nonce'); ?>

                        <?php if ( current_user_can( 'dokan_edit_coupon' ) ): ?>
                            <span class="edit"><a href="<?php echo $edit_url; ?>"><?php _e( 'Edit', 'dokan' ); ?></a> | </span>
                        <?php endif; ?>

                        <?php if ( current_user_can( 'dokan_delete_coupon' ) ): ?>
                            <span class="delete"><a  href="<?php echo $del_url; ?>"  onclick="return confirm('<?php esc_attr_e( 'Are you sure want to delete', 'dokan' ); ?>');"><?php _e('delete', 'dokan'); ?></a></span>
                        <?php endif ?>
                    </div>
                </td>

                <td data-title="<?php _e('Coupon type', 'dokan'); ?>">
                    <?php
                    $discount_type = get_post_meta( $post->ID, 'discount_type', true );
                    $types         = Dokan_Pro_Coupons::get_coupon_types();

                    printf( __( '%s', 'dokan' ), $types[$discount_type] );
                    ?>
                </td>

                <td data-title="<?php _e('Coupon amount', 'dokan'); ?>">
                    <?php echo esc_attr( get_post_meta( $post->ID, 'coupon_amount', true ) ); ?>
                </td>

                <td data-title="<?php _e('Product IDs', 'dokan'); ?>">
                    <?php
                        $product_ids = get_post_meta( $post->ID, 'product_ids', true );
                        $product_ids = $product_ids ? array_map( 'absint', explode( ',', $product_ids ) ) : array();

                        if ( sizeof( $product_ids ) > 0 ) {
                            if ( count( $product_ids ) > 12 ) {
                                $product_ids = array_slice( $product_ids, 0, 12 );
                                echo sprintf( '%s... <a href="%s">%s</a>', esc_html( implode( ', ', $product_ids ) ), esc_url( $edit_url ), __( 'See all', 'dokan' ) );
                            } else {
                                echo esc_html( implode( ', ', $product_ids ) );
                            }
                        } else {
                            echo '&ndash;';
                        }
                    ?>
                </td>

                <td data-title="<?php _e('Usage / Limit', 'dokan'); ?>">
                    <?php

                        $usage_count = absint( get_post_meta( $post->ID, 'usage_count', true ) );
                        $usage_limit = esc_html( get_post_meta($post->ID, 'usage_limit', true) );

                        if ( $usage_limit )
                            printf( __( '%s / %s', 'dokan' ), $usage_count, $usage_limit );
                        else
                            printf( __( '%s / &infin;', 'dokan' ), $usage_count );
                     ?>
                </td>

                <td data-title="<?php _e('Expiry date', 'dokan'); ?>">
                    <?php
                        $expiry_date = get_post_meta( $post->ID, 'date_expires', true );

                        if ( $expiry_date && ( (string) (int) $expiry_date === $expiry_date )
                            && ( $expiry_date <= PHP_INT_MAX )
                            && ( $expiry_date >= ~PHP_INT_MAX ) ) {

                            echo esc_html( date_i18n( 'F j, Y', $expiry_date ) );
                        } else {
                            echo $expiry_date ? esc_html( date_i18n( 'F j, Y', strtotime( $expiry_date ) ) ) : '&ndash;';
                        }
                    ?>
                </td>
                <td class="diviader"></td>
            </tr>
            <?php
        }
    ?>
</table>
