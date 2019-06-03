<h2 id="saved-cards" style="margin-top:40px;"><?php _e( 'Saved cards', 'dokan' ); ?></h2>
<table class="shop_table">
    <thead>
        <tr>
            <th><?php _e( 'Card', 'dokan' ); ?></th>
            <th><?php _e( 'Expires', 'dokan' ); ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $cards as $card ) : ?>


        <tr>
            <td><?php printf( __( 'Card ending in %s', 'dokan' ), $card['active_card'] ); ?></td>
            <td><?php printf( __( 'Expires %s/%s', 'dokan' ), $card['exp_month'], $card['exp_year'] ); ?></td>
            <td>
                <form action="" method="POST">
                    <?php wp_nonce_field ( 'stripe_del_card' ); ?>
                    <input type="hidden" name="stripe_delete_card" value="<?php echo esc_attr( $card['customer_id'] ); ?>">
                    <input type="submit" class="button" value="<?php _e( 'Delete card', 'dokan' ); ?>">
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
