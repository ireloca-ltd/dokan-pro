<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="woocommerce_booking_resource wc-metabox closed">
	<h3>
		<button type="button" class="dokan-btn dokan-btn-theme remove_booking_resource button" rel="<?php echo esc_attr( absint( $resource_id ) ); ?>"><?php _e( 'Remove', 'dokan' ); ?></button>

		<!--<a href="<?php //echo admin_url( 'post.php?post=' . $resource_id . '&action=edit' ); ?>" target="_blank" class="edit_resource"><?php //_e( 'Edit resource', 'dokan' ); ?> &rarr;</a>-->

		<div class="handlediv" title="<?php _e( 'Click to toggle', 'dokan' ); ?>"></div>

		<strong>#<?php echo esc_html( $resource_id ); ?> &mdash; <span class="resource_name"><?php echo $resource->post_title; ?></span></strong>

		<input type="hidden" name="resource_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $resource_id ); ?>" />
		<input type="hidden" class="resource_menu_order" name="resource_menu_order[<?php echo $loop; ?>]" value="<?php echo $loop; ?>" />
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
		<tbody>
			<tr>
				<td>
					<label><?php _e( 'Base Cost', 'dokan' ); ?>:</label>
					<input type="number" class="" name="resource_cost[<?php echo $loop; ?>]" min="0" value="<?php if ( ! empty( $resource_base_cost ) ) echo esc_attr( $resource_base_cost ); ?>" placeholder="0.00" step="0.01" />
                    <?php do_action( 'woocommerce_bookings_after_resource_cost', $resource_id, $post_id ); ?>
				</td>
				<td>
					<label><?php _e( 'Block Cost', 'dokan' ); ?>:</label>
					<input type="number" class="" name="resource_block_cost[<?php echo $loop; ?>]" min="0" value="<?php if ( ! empty( $resource_block_cost ) ) echo esc_attr( $resource_block_cost ); ?>" placeholder="0.00" step="0.01" />
                    <?php do_action( 'woocommerce_bookings_after_resource_block_cost', $resource_id, $post_id ); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
