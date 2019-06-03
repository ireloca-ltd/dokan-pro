(function($){

    var Dokan_RMA = {

        init: function() {
            $( 'input#dokan_rma_product_override' ).on( 'change', this.toggleProductrmaOption );

            $( 'select#dokan-warranty-type' ).on( 'change', this.toggleTypeContent );
            $( 'select#dokan-warranty-length' ).on( 'change', this.toggleLenghtContent );

            $( 'table.dokan-rma-addon-warranty-table').on( 'click', 'a.add-item', this.addRow );
            $( 'table.dokan-rma-addon-warranty-table').on( 'click', 'a.remove-item', this.removeRow );

            $( 'form#dokan-update-request-status' ).on( 'submit', this.changeRequestStatus );

            $( 'a.dokan-send-refund-request' ).on( 'click', this.openRefundPopup );
            $( 'a.dokan-send-coupon-request' ).on( 'click', this.openCouponPopup );

            $( 'body' ).on( 'submit', 'form#dokan-send-refund-popup-form', this.submitRefundRequest );
            $( 'body' ).on( 'submit', 'form#dokan-send-coupon-popup-form', this.sendCouponRequest );

            this.initialize();


        },

        initialize: function() {
            $( 'select#dokan-warranty-type' ).trigger( 'change' );
            $( 'input#dokan_rma_product_override' ).trigger( 'change' );
        },

        addRow: function(e){
            e.preventDefault();
            var row = $(this).closest('tr').first().clone().appendTo($(this).closest('tbody'));
            row.find('input').val('');
            row.find('select').val('days');
        },

        removeRow: function(e) {
            e.preventDefault();

            if( $(this).closest('tbody').find( 'tr' ).length == 1 ){
                return;
            }

            $(this).closest('tr').remove();
        },

        toggleProductrmaOption: function(e) {
            e.preventDefault();
            var self = $(this);

            if ( self.is( ':checked' ) ) {
                $('.dokan-product-rma-option-wrapper').slideDown();
            } else {
                $('.dokan-product-rma-option-wrapper').slideUp( 300, function() {
                    $(this).hide();
                });
            }

        },

        toggleTypeContent: function(e) {
            e.preventDefault();

            var self = $(this),
                hide_classes = '.hide_if_no_warranty',
                show_classes = '.show_if_no_warranty',
                val  = self.val();

            $.each( [ 'included_warranty', 'addon_warranty' ], function( index, value ) {
                hide_classes = hide_classes + ', .hide_if_' + value;
                show_classes = show_classes + ', .show_if_' + value;
            });

            $(hide_classes).show();
            $(show_classes).hide();

            $('.show_if_' + val ).show();
            $('.hide_if_' + val ).hide();

            if ( val === 'included_warranty' ) {
                $( 'select#dokan-warranty-length' ).trigger( 'change' );
            }
        },

        toggleLenghtContent: function(e) {
            e.preventDefault();

            var self = $(this),
                hide_classes = '.hide_if_lifetime, .hide_if_limited',
                show_classes = '.show_if_lifetime, .show_if_limited',
                val = self.val();

            $(hide_classes).show();
            $(show_classes).hide();

            $('.show_if_' + val ).show();
            $('.hide_if_' + val ).hide();
        },

        changeRequestStatus: function(e) {
            e.preventDefault();

            var self = $(this),
                data = {
                    action: 'dokan-update-return-request',
                    nonce: DokanRMA.nonce,
                    formData: self.serialize()
                }

            jQuery( '.dokan-status-update-panel' ).block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            $.post( DokanRMA.ajaxurl, data, function(resp){
                if ( resp.success ) {
                    jQuery( '.dokan-status-update-panel' ).unblock();
                    window.location.reload();
                } else {
                    jQuery( '.dokan-status-update-panel' ).unblock();
                    alert( resp.data );
                }
            });
        },

        openRefundPopup: function(e) {
            e.preventDefault();
            var self = $(this),
                refundTemplate = wp.template( 'dokan-send-refund' );

            $.magnificPopup.open({
                fixedContentPos: true,
                items: {
                    src: refundTemplate().trim(),
                    type: 'inline'
                },
                callbacks: {
                    open: function() {
                        $(this.content).closest('.mfp-wrap').removeAttr('tabindex');
                        var data = {
                            action: 'dokan-get-refund-order-data',
                            nonce: DokanRMA.nonce,
                            request_id: self.data( 'request_id' )
                        };

                        $( '#dokan-send-refund-popup' ).block( { message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } } );

                        $.post( DokanRMA.ajaxurl, data, function(resp) {
                            $( '#dokan-send-refund-popup' ).find( '.refund-content' ).html( resp.data );
                            $( '#dokan-send-refund-popup' ).unblock();

                            $('table.dokan-refund-item-list-table').find( 'input.refund_item_amount' ).on( 'keyup', function(){
                                total = 0.0;
                                $('table.dokan-refund-item-list-table').find( 'input.refund_item_amount' ).each( function( item, key ) {
                                    total += parseFloat( accounting.unformat( $(key).val(), dokan_refund.mon_decimal_point ) );
                                });

                                $('.dokan-popup-total-refund-amount').find('span.amount').text( accounting.formatNumber( total, dokan_refund.currency_format_num_decimals, '', dokan_refund.mon_decimal_point ) );
                                $('input[name="refund_total_amount"]').val(accounting.formatNumber( total, dokan_refund.currency_format_num_decimals, '', dokan_refund.mon_decimal_point ));
                            });
                        } );

                        $( 'body' ).trigger( 'dokan-refund-popup-opened', Dokan_RMA );
                    }
                }
            });
        },

        openCouponPopup: function(e) {
            e.preventDefault();
            var self = $(this),
                couponTemplate = wp.template( 'dokan-send-coupon' );

            $.magnificPopup.open({
                fixedContentPos: true,
                items: {
                    src: couponTemplate().trim(),
                    type: 'inline'
                },
                callbacks: {
                    open: function() {
                        $(this.content).closest('.mfp-wrap').removeAttr('tabindex');
                        var data = {
                            action: 'dokan-get-coupon-order-data',
                            nonce: DokanRMA.nonce,
                            request_id: self.data( 'request_id' )
                        };

                        $( '#dokan-send-coupon-popup' ).block( { message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } } );

                        $.post( DokanRMA.ajaxurl, data, function(resp) {
                            $( '#dokan-send-coupon-popup' ).find( '.coupon-content' ).html( resp.data );
                            $( '#dokan-send-coupon-popup' ).unblock();

                            $('table.dokan-refund-item-list-table').find( 'input.refund_item_amount' ).on( 'keyup', function(){
                                total = 0.0;
                                $('table.dokan-refund-item-list-table').find( 'input.refund_item_amount' ).each( function( item, key ) {
                                    total += parseFloat( accounting.unformat( $(key).val(), dokan_refund.mon_decimal_point ) );
                                });

                                $('.dokan-popup-total-refund-amount').find('span.amount').text( accounting.formatNumber( total, dokan_refund.currency_format_num_decimals, '', dokan_refund.mon_decimal_point ) );
                                $('input[name="refund_total_amount"]').val(accounting.formatNumber( total, dokan_refund.currency_format_num_decimals, '', dokan_refund.mon_decimal_point ));
                            });
                        } );

                        $( 'body' ).trigger( 'dokan-coupon-popup-opened', Dokan_RMA );
                    }
                }
            });
        },

        submitRefundRequest: function(e) {
            e.preventDefault();

            var self = $(this),
                data = {
                    action: 'dokan-send-refund-request',
                    nonce: DokanRMA.nonce,
                    formData: self.serialize()
                };

            $( '#dokan-send-refund-popup' ).block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            $.post( DokanRMA.ajaxurl, data, function(resp) {
                if ( resp.success ) {
                    $( '#dokan-send-refund-popup' ).unblock()
                    window.location.reload();
                } else {
                    $( '#dokan-send-refund-popup' ).unblock()
                    alert( resp.data );
                }
            } )
        },

        sendCouponRequest: function(e) {
            e.preventDefault();
            var self = $(this),
                data = {
                    action: 'dokan-send-coupon-request',
                    nonce: DokanRMA.nonce,
                    formData: self.serialize()
                };

            $( '#dokan-send-coupon-popup' ).block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

            $.post( DokanRMA.ajaxurl, data, function(resp) {
                if ( resp.success ) {
                    $( '#dokan-send-coupon-popup' ).unblock()
                    window.location.reload();
                } else {
                    $( '#dokan-send-coupon-popup' ).unblock()
                    alert( resp.data );
                }
            } )

        }

    }

    $(document).ready(function(){
        Dokan_RMA.init();
    });

})(jQuery);
