<tr>
	<td class="file_name">
        <input type="text" class="dokan-form-control wc_variation_file_name" placeholder="<?php esc_attr_e( 'File Name', 'dokan' ); ?>" name="_wc_variation_file_names[<?php echo $variation_id; ?>][]" value="<?php echo esc_attr( $file['name'] ); ?>" />
        <input type="hidden" name="_wc_variation_file_hashes[<?php echo $variation_id; ?>][]" value="<?php echo esc_attr( $key ); ?>" />
    </td>
    <td class="file_url"><input type="text" class="dokan-form-control wc_variation_file_url" placeholder="<?php esc_attr_e( "http://", 'dokan' ); ?>" name="_wc_variation_file_urls[<?php echo $variation_id; ?>][]" value="<?php echo esc_attr( $file['file'] ); ?>" /></td>
	<td class="file_url_choose" width="1%"><a href="#" class="dokan-btn dokan-btn-default upload_file_button" data-choose="<?php esc_attr_e( 'Choose file', 'dokan' ); ?>" data-update="<?php esc_attr_e( 'Insert file URL', 'dokan' ); ?>"><?php echo str_replace( ' ', '&nbsp;', __( 'Choose file', 'dokan' ) ); ?></a></td>
	<td width="1%"><a href="#" class="dokan-btn dokan-btn-theme delete"><?php _e( 'Delete', 'dokan' ); ?></a></td>
</tr>