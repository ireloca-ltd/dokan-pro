;(function($){

    var DokanWholesaleFrontend = {

        init: function() {
            $('body').on( 'click', 'a#dokan-become-wholesale-customer-btn', this.makeWholesaleCustomer )
            $("input[name=variation_id]").on( 'change', this.triggerVariationWholesale );
        },

        triggerVariationWholesale: function(e) {
            e.preventDefault();

            var variations      = $(".variations_form").data("product_variations");
            var variation_id    = $("input[name=variation_id]").val();

            for ( var x = 0; x < variations.length; x++ ) {
                if ( variations[x].variation_id == variation_id ) {
                    var variation = variations[x];
                    if ( DokanWholesale.check_permission ) {
                        if ( variation._enable_wholesale == 'yes' ) {
                            var wholesale_string = DokanWholesale.variation_wholesale_string.wholesale_price
                                                    + ': '
                                                    + '<strong>'
                                                    + DokanWholesale.currency_symbol
                                                    + variation._wholesale_price
                                                    + '</strong>'
                                                    + ' ('
                                                    + DokanWholesale.variation_wholesale_string.minimum_quantity
                                                    + ': '
                                                    + '<strong>'
                                                    + variation._wholesale_quantity
                                                    + '</strong>'
                                                    + ')';

                            $('.single_variation').append( '<div class="woocommerce-variation-wholesale">' + wholesale_string + '</div>' );
                        } else {
                            $('.single_variation').find( '.woocommerce-variation-wholesale' ).remove();
                        }
                    }
                }
            }
        },

        makeWholesaleCustomer: function(e) {
            e.preventDefault();

            var self = $(this),
                url = dokan.rest.root + dokan.rest.version + '/wholesale/register',
                data = {
                    id : self.data('id')
                };

            jQuery( '.dokan-wholesale-migration-wrapper' ).block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            $.post( url, data, function( resp ) {
                if ( resp.wholesale_status == 'active' ) {
                    self.closest('li').html( '<div class="woocommerce-message" style="margin-bottom:0px">' + dokan.wholesale.activeStatusMessage + '</div>' );
                } else {
                    self.closest('li').html( '<div class="woocommerce-info" style="margin-bottom:0px">' + dokan.wholesale.deactiveStatusMessage + '</div>' );
                }
                jQuery( '.dokan-wholesale-migration-wrapper' ).unblock();
            } );
        }
    };

    $(document).ready(function(){
        DokanWholesaleFrontend.init();
    });

})(jQuery);
