
// For single page shipping calculation scripts;
(function($){

    $(document).ready(function(){
        $( '.dokan-shipping-calculate-wrapper' ).on( 'change', 'select#dokan-shipping-country', function(e) {
            e.preventDefault();
            
            var self = $(this),
                data = {
                    'action' : 'dokan_shipping_country_select',
                    'country_id' : self.val(),
                    'author_id' : self.data('author_id'),
                };

            if( self.val() != '' ) {
                $.post( dokan.ajaxurl, data, function( resp ) {
                    
                    if( resp.success ) {
                        self.closest('.dokan-shipping-calculate-wrapper').find('.dokan-shipping-state-wrapper').html( resp.data );
                        self.closest('.dokan-shipping-calculate-wrapper').find('.dokan-shipping-price-wrapper').html('');
                    }
                });
            } else {
                self.closest('.dokan-shipping-calculate-wrapper').find('.dokan-shipping-price-wrapper').html('');
                self.closest('.dokan-shipping-calculate-wrapper').find('.dokan-shipping-state-wrapper').html('');
            }   
        });

        $('.dokan-shipping-calculate-wrapper').on( 'keydown', '#dokan-shipping-qty', function(e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 91, 107, 109, 110, 187, 189, 190]) !== -1 ||
                 // Allow: Ctrl+A
                (e.keyCode == 65 && e.ctrlKey === true) ||
                 // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                     // let it happen, don't do anything
                    return;
            }

            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        $( '.dokan-shipping-calculate-wrapper' ).on( 'click', 'button.dokan-shipping-calculator', function(e) {
            e.preventDefault();
            
            var self = $(this),
                data = {
                    'action' : 'dokan_shipping_calculator',
                    'country_id' : self.closest('.dokan-shipping-calculate-wrapper').find('select.dokan-shipping-country').val(),
                    'product_id' : self.closest('.dokan-shipping-calculate-wrapper').find('select.dokan-shipping-country').data('product_id'),
                    'author_id' : self.closest('.dokan-shipping-calculate-wrapper').find('select.dokan-shipping-country').data('author_id'),
                    'quantity' : self.closest('.dokan-shipping-calculate-wrapper').find('input.dokan-shipping-qty').val(),
                    'state' : self.closest('.dokan-shipping-calculate-wrapper').find('select.dokan-shipping-state').val(),
                };
                
            $.post( dokan.ajaxurl, data, function( resp ) {
                if( resp.success ) {
                    self.closest('.dokan-shipping-calculate-wrapper').find('.dokan-shipping-price-wrapper').html( resp.data );
                }
            });  
        });
    });

})(jQuery);