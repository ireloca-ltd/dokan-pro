Stripe.setPublishableKey( wc_stripe_connect_params.key );

function stripe_pad( num, size ) {
    var s = num+"";
    while ( s.length < size )
        s = "0" + s;
    return s;
}

jQuery( function() {

    /* Checkout Form */
    jQuery('form.checkout').on('checkout_place_order_dokan-stripe-connect', function( event ) {

        return stripeFormHandler();
    });

    /* Pay Page Form */
    jQuery('form#order_review').submit(function(){
        return stripeFormHandler();
    });

    /* Both Forms */
    jQuery("form.checkout, form#order_review").on('change', '.card-number, .card-cvc, .card-expiry-month, .card-expiry-year, input[name=stripe_customer_id], #dokan-stripe-connect-card-number, #dokan-stripe-connect-card-cvc, #dokan-stripe-connect-card-expiry', function( event ) {

        jQuery('.woocommerce_error, .woocommerce-error, .woocommerce-message, .woocommerce_message, .stripe_token').remove();

        jQuery('.stripe_token').remove();

        wc_stripe_connect_params.token_done = false;
    });

    /* Open and close */
    jQuery("form.checkout, form#order_review").on('change', 'input[name=stripe_customer_id]', function() {

        if ( jQuery('input[name=stripe_customer_id]:checked').val() == 'new' ) {

            jQuery('div.stripe_new_card').slideDown( 200 );

        } else {

            jQuery('div.stripe_new_card').slideUp( 200 );

        }

    } );

} );

function stripeFormHandler() {

    if ( jQuery('#payment_method_dokan-stripe-connect').is(':checked') && ( jQuery('input[name=stripe_customer_id]:checked').size() == 0 || jQuery('input[name=stripe_customer_id]:checked').val() == 'new' ) ) {

        if ( jQuery( 'input.stripe_token' ).size() == 0 ) {

            var card    = jQuery('#dokan-stripe-connect-card-number').val();

            var cvc     = jQuery('#dokan-stripe-connect-card-cvc').val();

            var $form = jQuery("form.checkout, form#order_review");

            var expires = jQuery('#dokan-stripe-connect-card-expiry').payment( 'cardExpiryVal' );

            var month = parseInt( expires['month'] ) || 0;

            var year = parseInt( expires['year'] ) || 0;

            month = stripe_pad( month, 2 );

            year = stripe_pad( year, 2 );

            $form.block({message: null, overlayCSS: {background: '#fff url(' + woocommerce_params.plugin_url + '/assets/images/ajax-loader.gif) no-repeat center', opacity: 0.6}});

            if ( jQuery('#billing_first_name').size() == 0 ) {
                name            = wc_stripe_connect_params.billing_first_name + ' ' + wc_stripe_connect_params.billing_last_name;
                address_line1   = wc_stripe_connect_params.billing_address_1;
                address_line2   = wc_stripe_connect_params.billing_address_2;
                address_state   = wc_stripe_connect_params.billing_state;
                address_city    = wc_stripe_connect_params.billing_city;
                address_zip     = wc_stripe_connect_params.billing_postcode;
                address_country = wc_stripe_connect_params.billing_country;
            } else {
                name            = jQuery('#billing_first_name').val() + ' ' + jQuery('#billing_last_name').val();
                address_line1   = jQuery('#billing_address_1').val();
                address_line2   = jQuery('#billing_address_2').val();
                address_state   = jQuery('#billing_state').val();
                address_city    = jQuery('#billing_city').val();
                address_zip     = jQuery('#billing_postcode').val();
                address_country = jQuery('#billing_country').val();
            }

            Stripe.createToken( {
                number:     card,
                cvc:        cvc,
                exp_month:  month,
                exp_year:   year,
                name:       name,
                address_line1: address_line1,
                address_line2: address_line2,
                address_state: address_state,
                address_city: address_city,
                address_zip: address_zip,
                address_country: address_country
            }, stripeResponseHandler );

            // Prevent form submitting
            return false;

        }

    }

    return true;

}

function stripeResponseHandler( status, response ) {

    var $form = jQuery("form.checkout, form#order_review");

    if ( response.error ) {

        jQuery('.woocommerce_error, .woocommerce-error, .woocommerce-message, .woocommerce_message, .stripe_token').remove();

        jQuery('#dokan-stripe-connect-card-number').closest('p').before( '<ul class="woocommerce_error woocommerce-error"><li>' + response.error.message + '</li></ul>' );

        $form.unblock();

    } else {

        // token contains id, last4, and card type
        var token = response['id'];

        wc_stripe_connect_params.token_done = true;

        jQuery( '.stripe_token').remove();

        // insert the token into the form so it gets submitted to the server
        $form.append("<input type='hidden' class='stripe_token' name='stripe_token' value='" + token + "'/>");

        $form.submit();
    }
}