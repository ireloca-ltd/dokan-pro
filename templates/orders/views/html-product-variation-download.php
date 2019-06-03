<tr>
	<td class="file_name"><input type="text" class="input_text" placeholder="<?php _e( 'File Name', 'dokan' ); ?>" name="_wc_variation_file_names[<?php echo $variation_id; ?>][]" value="<?php echo esc_attr( $file['name'] ); ?>" /></td>
	<td class="file_url"><input type="text" class="input_text" placeholder="<?php _e( "http://", 'dokan' ); ?>" name="_wc_variation_file_urls[<?php echo $variation_id; ?>][]" value="<?php echo esc_attr( $file['file'] ); ?>" /></td>
	<td class="file_url_choose" width="1%"><a href="#" class="button upload_file_button" data-choose="<?php _e( 'Choose file', 'dokan' ); ?>" data-update="<?php _e( 'Insert file URL', 'dokan' ); ?>"><?php echo str_replace( ' ', '&nbsp;', __( 'Choose file', 'dokan' ) ); ?></a></td>
	<td width="1%"><a href="#" class="delete"><?php _e( 'Delete', 'dokan' ); ?></a></td>
</tr>