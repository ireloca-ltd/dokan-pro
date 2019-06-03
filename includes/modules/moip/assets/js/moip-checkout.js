jQuery( function($) {

    jQuery(document).on( 'click', '#place_order', function() {

        if ( jQuery('input#payment_method_dokan-moip-connect').prop('checked') !== true ){
            return;
        }

        var $card_number = $('#dokan-moip-connect-card-number').val();
        var $card_cvc    = $('#dokan-moip-connect-card-cvc').val();
        var $card_expiry = $('#dokan-moip-connect-card-expiry').val();
        var $card_expiry_month = $card_expiry.substr(0, 2);
        var $card_expiry_year = $card_expiry.substr(5);

        if ( ! MoipSdkJs.MoipValidator.isValidNumber( $card_number ) ) {
            $('#dokan-moip-connect-card-number').closest('p').before( '<ul class="woocommerce_error woocommerce-error"><li>' + moip_params.card_error + '</li></ul>' );
            return false;
        }

        if ( ! MoipSdkJs.MoipValidator.isSecurityCodeValid( $card_number, $card_cvc ) ) {
            $('#dokan-moip-connect-card-cvc').closest('p').before( '<ul class="woocommerce_error woocommerce-error"><li>' + moip_params.cvc_error + '</li></ul>' );
            return false;
        }

        if ( ! MoipSdkJs.MoipValidator.isExpiryDateValid( $card_expiry_month, $card_expiry_year ) ) {
            $('#dokan-moip-connect-card-expiry').closest('p').before( '<ul class="woocommerce_error woocommerce-error"><li>' + moip_params.expriy_error + '</li></ul>' );
            return false;
        }

        var $form = jQuery("form.checkout, form#order_review");

        var hashed = $form.find('input.moip_hash');

        hashed.val('');

        MoipSdkJs.MoipCreditCard
            .setPubKey( moip_params.public_key )
            .setCreditCard({
                number: $card_number,
                cvc: $card_cvc,
                expirationMonth: $card_expiry_month,
                expirationYear: $card_expiry_year
            })
            .hash()
            .then( function( hash ) {
                $form.find('input.moip_hash').remove();
                $form.append("<input type='hidden' class='moip_hash' name='moip_hash' value='" + hash + "'/>");
                $form.submit();
            });

        return false;
    });

    jQuery(document).ready(function() {
        var $billing_cpf_span = $('#billing_cpf_field span.optional');
        var $billing_cpf = $('#billing_cpf_field');

        if ( $('.payment_box.payment_method_dokan-moip-connect').css('display') !== 'block' ) {
            $billing_cpf.hide();
        } else {
            $billing_cpf_span.html('<span style="color:red">*</span>');
        }
    });

    jQuery(document).on('click','.wc_payment_methods.payment_methods li', function() {
        var $billing_cpf_span = $('#billing_cpf_field span.optional');
        var $billing_cpf = $('#billing_cpf_field');

        setTimeout(function() {
            if ( $('.payment_box.payment_method_dokan-moip-connect').css('display') == 'block' ) {
                $billing_cpf.show();
                $billing_cpf_span.html('<span style="color:red">*</span>');
            } else {
                $billing_cpf.hide();
            }
        }, 1000);
    });

} );
