<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="woocommerce_booking_person wc-metabox closed">
	<h3>
		<button type="button" class="remove_booking_person button dokan-btn dokan-btn-theme" rel="<?php echo esc_attr( $person_type_id ); ?>"><?php _e( 'Remove', 'dokan' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'dokan' ); ?>"></div>

		<strong>#<?php echo esc_html( $person_type_id ); ?> &mdash; <span class="person_name"><?php echo $person_type->post_title; ?></span></strong>

		<input type="hidden" name="person_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $person_type_id ); ?>" />
		<input type="hidden" class="person_menu_order" name="person_menu_order[<?php echo $loop; ?>]" value="<?php echo $loop; ?>" />
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content dokan-booking-persons-table">
		<tbody>
			<tr>
				<td>
					<label><?php _e( 'Person Type Name', 'dokan' ); ?>:</label>
					<input type="text" class="person_name" name="person_name[<?php echo $loop; ?>]" min="0" value="<?php echo esc_attr( $person_type->post_title ); ?>" placeholder="<?php _e( 'Name', 'dokan' ) . $loop; ?>" />
				</td>
				<td>
					<label><?php _e( 'Base Cost', 'dokan' ); ?>:</label>
					<input type="number" name="person_cost[<?php echo $loop; ?>]" min="0" value="<?php echo esc_attr( $person_type->cost ); ?>" placeholder="0.00" step="0.01" />
                    <?php do_action( 'woocommerce_bookings_after_person_cost', $person_type_id ); ?>
				</td>
				<td>
					<label><?php _e( 'Block Cost', 'dokan' ); ?>:</label>
					<input type="number" name="person_block_cost[<?php echo $loop; ?>]" min="0" value="<?php echo $person_type->block_cost ? esc_attr( $person_type->block_cost ) : ''; ?>" placeholder="0.00" step="0.01" />
                    <?php do_action( 'woocommerce_bookings_after_person_block_cost', $person_type_id ); ?>
				</td>
			</tr>
			<tr>
				<td>
					<label><?php _e( 'Description', 'dokan' ); ?>:</label>
					<input type="text" class="person_description" name="person_description[<?php echo $loop; ?>]" value="<?php echo esc_attr( $person_type->post_excerpt ); ?>" />
				</td>
				<td>
					<label><?php _e( 'Min', 'dokan' ); ?>:</label>
					<input type="number" name="person_min[<?php echo $loop; ?>]" value="<?php echo esc_attr( $person_type->min ); ?>" min="0" />
				</td>
				<td>
					<label><?php _e( 'Max', 'dokan' ); ?>:</label>
					<input type="number" name="person_max[<?php echo $loop; ?>]" value="<?php echo esc_attr( $person_type->max ); ?>" min="0" />
				</td>
			</tr>
		</tbody>
	</table>
</div>
