;(function($) {

	var pricingPane = $('#woocommerce-product-data');

	if ( pricingPane.length ) {
		pricingPane.find('.pricing').addClass('show_if_product_pack').end()
			.find('.inventory_tab').addClass('hide_if_product_pack').end()
			.find('.shipping_tab').addClass('hide_if_product_pack').end()
			.find('.linked_product_tab').addClass('hide_if_product_pack').end()
			.find('.attributes_tab').addClass('hide_if_product_pack').end()
			.find('._no_of_product_field').hide().end()
			.find('._pack_validity_field').hide()
	}

	$('body').on('woocommerce-product-type-change',function(event, select_val){

		$('._no_of_product_field').hide();
		$('._pack_validity_field').hide();
		$('._enable_recurring_payment_field').hide();
		$('.subscription_pricing').hide();
	    $('._sale_price_field').show();
	    $('.dokan_subscription_trial_period').hide();

		if ( select_val == 'product_pack' ) {
			$('._no_of_product_field').show();
			$('._pack_validity_field').show();
			$('._enable_recurring_payment_field').show();
			$('._sale_price_field').hide();
		}

		if ( $('#dokan_subscription_enable_trial').is(':checked') ) {
			$('.dokan_subscription_trial_period').show();
		}

		if( $( '#_enable_recurring_payment' ).is( ":checked" ) ) {
			$('.subscription_pricing').show();
			$('._pack_validity_field').hide();
		}
	});

	$('.woocommerce_options_panel').on('change', '#dokan_subscription_enable_trial', function() {
		$('.dokan_subscription_trial_period').hide();

		if ( $(this).is(':checked') ) {
			$('.dokan_subscription_trial_period').fadeIn();
		}
	});

	$('.woocommerce_options_panel').on('change', '#_enable_recurring_payment', function() {

		$('.subscription_pricing').hide();
		$('._pack_validity_field').show();

		if ( $(this).is(':checked') ) {
			$('.subscription_pricing').fadeIn();
			$('._pack_validity_field').hide();
		}
	});

})(jQuery);