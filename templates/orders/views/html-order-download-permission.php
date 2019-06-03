<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="wc-metabox closed">
	<h3 class="fixed">
		<button type="button" rel="<?php echo absint( $download->product_id ) . ',' . esc_attr( $download->download_id ); ?>" class="revoke_access button"><?php _e( 'Revoke Access', 'dokan' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'dokan' ); ?>"></div>
		<strong>
			<?php echo '#' . absint( dokan_get_prop( $product, 'id' ) ) . ' &mdash; ' . apply_filters( 'woocommerce_admin_download_permissions_title', $product->get_title(), $download->product_id, $download->order_id, $download->order_key, $download->download_id ) . ' &mdash; ' . sprintf( __( '%s: %s', 'dokan' ), $file_count, wc_get_filename_from_url( $product->get_file_download_path( $download->download_id ) ) ) . ' &mdash; ' . sprintf( _n('Downloaded %s time', 'Downloaded %s times', absint( $download->download_count ), 'dokan'), absint( $download->download_count ) ); ?>
		</strong>
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content dokan-table dokan-table-strip">
		<tbody>
			<tr>
				<td>
					<label><?php _e( 'Downloads Remaining', 'dokan' ); ?>:</label>
					<input type="hidden" name="product_id[<?php echo $loop; ?>]" value="<?php echo absint( $download->product_id ); ?>" />
					<input type="hidden" name="download_id[<?php echo $loop; ?>]" value="<?php echo esc_attr( $download->download_id ); ?>" />
					<input type="number" step="1" min="0" class="short" name="downloads_remaining[<?php echo $loop; ?>]" value="<?php echo esc_attr( $download->downloads_remaining ); ?>" placeholder="<?php _e( 'Unlimited', 'dokan' ); ?>" />
				</td>
				<td>
					<label><?php _e( 'Access Expires', 'dokan' ); ?>:</label>
					<input type="text" class="short date-picker" name="access_expires[<?php echo $loop; ?>]" value="<?php echo $download->access_expires > 0 ? date_i18n( 'Y-m-d', strtotime( $download->access_expires ) ) : ''; ?>" maxlength="10" placeholder="<?php _e( 'Never', 'dokan' ); ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" />
				</td>
			</tr>
		</tbody>
	</table>
</div>
