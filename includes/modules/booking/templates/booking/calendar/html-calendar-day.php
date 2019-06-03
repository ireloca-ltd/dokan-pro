<script>
printDivCSS = new String ()
function printDiv(divId) {
window.frames["print_frame"].document.body.innerHTML=printDivCSS + document.getElementById(divId).innerHTML;
window.frames["print_frame"].window.focus();
window.frames["print_frame"].window.print();
}
</script>
<div class="wrap woocommerce">
	<div class="icon32 icon32-woocommerce-settings" id="icon-woocommerce"><br /></div>
	<h2><?php _e( 'Bookings by day', 'dokan' ); ?></h2>

	<form method="get" id="mainform" enctype="multipart/form-data" class="wc_bookings_calendar_form">
		<!--<input type="hidden" name="post_type" value="wc_booking" />-->
		<!--<input type="hidden" name="page" value="booking_calendar" />-->
		<input type="hidden" name="view" value="<?php echo esc_attr( $view ); ?>" />
		<input type="hidden" name="tab" value="calendar" />
		<div class="tablenav">
			<div class="filters">
				<select id="calendar-bookings-filter" name="filter_bookings" class="wc-enhanced-select" style="width:200px">
					<option value=""><?php _e( 'Filter Bookings', 'dokan' ); ?></option>
					<?php if ( $product_filters = $this->product_filters() ) : ?>
						<optgroup label="<?php _e( 'By bookable product', 'dokan' ); ?>">
							<?php foreach ( $product_filters as $filter_id => $filter_name ) : ?>
								<option value="<?php echo $filter_id; ?>" <?php selected( $product_filter, $filter_id ); ?>><?php echo $filter_name; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endif; ?>
					<?php if ( $resources_filters = $this->resources_filters() ) : ?>
						<optgroup label="<?php _e( 'By resource', 'dokan' ); ?>">
							<?php foreach ( $resources_filters as $filter_id => $filter_name ) : ?>
								<option value="<?php echo $filter_id; ?>" <?php selected( $product_filter, $filter_id ); ?>><?php echo $filter_name; ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endif; ?>
				</select>
			</div>
			<div class="date_selector">
				<a class="prev" href="<?php echo esc_url( add_query_arg( 'calendar_day', date_i18n( 'Y-m-d', strtotime( '-1 day', strtotime( $day ) ) ) ) ); ?>">&larr;</a>
				<div>
					<input type="text" name="calendar_day" class="calendar_day" placeholder="yyyy-mm-dd" value="<?php echo esc_attr( $day ); ?>" />
				</div>
				<a class="next" href="<?php echo esc_url( add_query_arg( 'calendar_day', date_i18n( 'Y-m-d', strtotime( '+1 day', strtotime( $day ) ) ) ) ); ?>">&rarr;</a>
			</div>
			<div class="views">
				<a class="month" href="<?php echo esc_url( add_query_arg( 'view', 'month' ) ); ?>"><?php _e( 'Month View', 'dokan' ); ?></a>
			</div>
			<div class="print">
				<a href="javascript:printDiv('calendar_days_print')"><i class="fa fa-print"></i>Print</a>
			</div>
			<script type="text/javascript">
				jQuery(function() {
					jQuery(".tablenav select, .tablenav input").change(function() {
		     			jQuery("#mainform").submit();
		   			});
		   			jQuery( '.calendar_day' ).datepicker({
						dateFormat: 'yy-mm-dd',
						numberOfMonths: 1,
					});
					// Tooltips
					jQuery(".bookings li").tipTip({
				    	'attribute' : 'data-tip',
				    	'fadeIn' : 50,
				    	'fadeOut' : 50,
				    	'delay' : 200
				    });
		   		});
			</script>
		</div>

		<div class="calendar_days">
			<ul class="hours">
				<?php for ( $i = 0; $i < 24; $i ++ ) : ?>
					<li><label><?php if ( $i != 0 && $i != 24 ) echo date_i18n( 'g:ia', strtotime( "midnight +{$i} hour" ) ); ?></label></li>
				<?php endfor; ?>
			</ul>
			<ul class="bookings">
				<?php $this->list_bookings_for_day(); ?>
			</ul>
		</div>
	</form>

<div id="calendar_days_print" class="dokan_print_bookings" style="display:none;">
<img alt="Image" src="https://storage.googleapis.com/gcs.ireloca.com/2018/10/72fc5d29-irelocalogologin.png">
<h3><?php _e( 'Bookings for', 'dokan-wc-booking' ); ?>&nbsp;<?php echo date_i18n( 'd-m-Y', strtotime( $day )); ?></h3>
<ul><?php $this->dokan_print_bookings(); ?></ul>
</div>
<iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>
</div>
