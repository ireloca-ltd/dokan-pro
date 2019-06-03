<?php
/**
 * Conversation template
 *
 * @since 1.0.0
 *
 * @package dokan
 */
?>

<?php if ( 'vendor' != $from ): ?>
    <h3><?php _e( 'Conversations', 'dokan' ) ?></h3>
    <hr>
<?php endif ?>

<div class="dokan-rma-request-conversations">
    <?php if ( $conversations ): ?>
        <ul>
            <?php foreach ( $conversations as $conversation ): ?>
                <li class="<?php echo dokan_get_current_user_id() == $conversation['to'] ? 'left' : 'right'; ?>">
                    <div class="message">
                        <?php echo $conversation['message']; ?>
                    </div>
                </li>
            <?php endforeach ?>

            <div class="dokan-clearfix"></div>
        </ul>
    <?php else: ?>
        <?php _e( 'No conversations found', 'dokan' ); ?>
    <?php endif ?>
</div>

<form method="post">
    <p class="form-row">
        <textarea name="message" id="message" rows="10"></textarea>
    </p>

    <?php wp_nonce_field( 'dokan_rma_send_message', 'dokan_rma_send_message_nonce' ); ?>
    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
    <?php if ( 'vendor' == $from ): ?>
        <input type="hidden" name="from" value="<?php echo dokan_get_current_user_id(); ?>">
        <input type="hidden" name="to" value="<?php echo $request['customer']['id']; ?>">
    <?php elseif ( 'customer' == $from ): ?>
        <input type="hidden" name="from" value="<?php echo dokan_get_current_user_id(); ?>">
        <input type="hidden" name="to" value="<?php echo $request['vendor']['store_id']; ?>">
    <?php endif ?>
    <input type="submit" name="dokan_rma_send_message" class="woocommerce-button button" value="<?php _e( 'Send Message', 'dokan' ); ?>">
</form>
