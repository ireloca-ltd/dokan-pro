// For Announcement scripts;
(function($){

    $(document).ready(function(){
        $( '.dokan-announcement-wrapper' ).on( 'click', 'a.remove_announcement', function(e) {
            e.preventDefault();

            if( confirm( dokan.delete_confirm ) ) {

                var self = $(this),
                    data = {
                        'action' : 'dokan_announcement_remove_row',
                        'row_id' : self.data('notice_row'),
                        '_wpnonce' : dokan.nonce
                    };
                self.closest('.dokan-announcement-wrapper-item').append('<span class="dokan-loading" style="position:absolute;top:2px; right:15px"> </span>');
                var row_count = $('.dokan-announcement-wrapper-item').length;
                $.post( dokan.ajaxurl, data, function(response) {
                    if( response.success ) {
                        self.closest('.dokan-announcement-wrapper-item').find( 'span.dokan-loading' ).remove();
                        self.closest('.dokan-announcement-wrapper-item').fadeOut(function(){
                            $(this).remove();
                            if( row_count == 1 ) {
                                $( '.dokan-announcement-wrapper' ).html( response.data );
                            }
                        });
                    } else {
                        alert( dokan.wrong_message );
                    }
                });
            }

        });
    });

})(jQuery);
// Coupon
(function($) {

    $.validator.setDefaults({ ignore: ":hidden" });

    var validatorError = function(error, element) {
        var form_group = $(element).closest('.form-group');
        form_group.addClass('has-error').append(error);
    };

    var validatorSuccess = function(label, element) {
        $(element).closest('.form-group').removeClass('has-error');
    };

    var Dokan_Coupons = {
        init: function() {
            var self = this;
            this.couponsValidation(self);
        },

        couponsValidation: function(self) {
            $("form.coupons").validate({
                errorElement: 'span',
                errorClass: 'error',
                errorPlacement: validatorError,
                success: validatorSuccess
            });
        }
    };

    Dokan_Coupons.init();

})(jQuery);

jQuery( document ).ready( function ( $ ) {
    'use strict';

    function InlineEditableTable ( table ) {
        this.$table = $( table );
        this.current_tr = null;
        this.current_tr_posX = 0;
        this.edit_form = null;
        this.current_product_id = 0;

        this.bindElements();
    }

    InlineEditableTable.prototype.bindElements = function () {
        var self = this;

        self.$table.on( 'click', '.item-inline-edit', function ( e ) {
            e.preventDefault();

            if ( ! dokan.is_vendor_enabled ) {
                return alert( dokan.not_enable_message );
            }

            self.$table.find( 'tr.dokan-product-list-inline-edit-form' ).addClass( 'dokan-hide' );

            var anchor = $( this ).children( 'a' );

            self.current_product_id = anchor.data( 'product-id' );
            self.current_tr = anchor.parents( 'tr' );
            self.current_tr_posX = self.current_tr.offset().top;

            self.current_tr.addClass( 'dokan-hide' );

            self.edit_form = self.current_tr.next( '.dokan-product-list-inline-edit-form' );

            self.edit_form.removeClass( 'dokan-hide' );

            self.bindSelect2();

            self.scrollTop();
        } );

        self.$table.on( 'click', '.inline-edit-cancel', function ( e ) {
            e.preventDefault();

            self.current_tr
                .removeClass( 'dokan-hide' )
                .next( '.dokan-product-list-inline-edit-form' )
                .addClass( 'dokan-hide' );

            self.scrollTop();
        } );

        self.$table.on( 'click', '.inline-edit-update', function ( e ) {
            e.preventDefault();

            var btn = $( this ),
                wrapper = btn.parent(),
                fieldset = btn.parents( 'fieldset' );

            wrapper.addClass( 'show-loading-animation' );
            fieldset.attr( 'disabled', 'disabled' );

            var data = {};

            self.edit_form.find( '[data-field-name]' ).each( function () {
                if ( $( this ).attr( 'type' ) === 'checkbox' ) {
                    data[ $( this ).data( 'field-name' ) ] = $( this ).is( ':checked' );
                } else {
                    data[ $( this ).data( 'field-name' ) ] = $( this ).val();
                }
            } );

            $.ajax( {
                url: dokan.ajaxurl,
                method: 'post',
                dataType: 'json',
                data: {
                    action: 'dokan_product_inline_edit',
                    security: dokan.product_inline_edit_nonce,
                    data: data
                }
            } ).done( function ( response ) {
                if ( response.data && response.data.row ) {
                    self.scrollTop( self.current_tr );

                    self.edit_form
                        .addClass( 'dokan-hide' )
                        .prev()
                        .replaceWith( response.data.row );
                }

            } ).fail( function ( response ) {
                if ( response.responseJSON && typeof response.responseJSON.data === 'string' ) {
                    alert( response.responseJSON.data );
                }

            } ).always( function () {
                wrapper.removeClass( 'show-loading-animation' );
                fieldset.removeAttr( 'disabled' );
            } );
        } );

        self.$table.on( 'click', '[data-field-toggler]', function ( e ) {
            var name = $( this ).data( 'field-name' ),
                is_checked = $( this ).is( ':checked' );

            self.edit_form.find( '[data-field-toggle="' + name + '"]' ).each( function () {
                var el = $( this );
                var compare_value = el.data( 'field-show-on' );

                if ( is_checked == compare_value ) { // not comparing data type
                    el.removeClass( 'dokan-hide' );
                } else {
                    el.addClass( 'dokan-hide' );
                }

                el.find( '.dokan-select2' )
                    .select2( 'destroy' )
                    .removeClass( 'dokan-select2' );

                self.bindSelect2();
            } );
        } );
    }

    InlineEditableTable.prototype.bindSelect2 = function () {
        this.edit_form
            .find( 'select:not(.dokan-select2)' )
            .addClass( 'dokan-select2' )
            .select2();
    }

    InlineEditableTable.prototype.scrollTop = function () {
        $('html, body').scrollTop( this.current_tr_posX - 50 );
    }

    $( '.dokan-inline-editable-table' ).each( function () {
        new InlineEditableTable( this );
    } );
} );

/* global wp, dokan, dokan_refund, accounting */
jQuery( function( $ ) {

    /**
     * Variations actions
     */
    var Dokan_Product_Variation_Actions = {

        /**
         * Initialize variations actions
         */
        init: function() {
            $( '#dokan-variable-product-options' )
                .on( 'change', 'input.variable_is_downloadable', this.variable_is_downloadable )
                .on( 'change', 'input.variable_is_virtual', this.variable_is_virtual )
                .on( 'change', 'input.variable_manage_stock', this.variable_manage_stock )
                .on( 'click', '.expand_all', this.expand_all )
                .on( 'click', '.close_all', this.close_all )
                // .on( 'click', 'button.notice-dismiss', this.notice_dismiss )
                .on( 'click', '.dokan-product-variation-itmes .sort', this.set_menu_order )
                .on( 'reload', this.reload );

            $( 'input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock' ).change();
            $( '.dokan-product-variation-wrapper' ).on( 'dokan_variations_loaded', this.variations_loaded );
            $( document.body ).on( 'dokan_variations_added', this.variation_added );
        },

        /**
         * Reload UI
         *
         * @param {Object} event
         * @param {Int} qty
         */
        reload: function() {
            Dokan_Product_Variation_Ajax.load_variations( 1 );
            Dokan_Product_Variation_PageNav.set_paginav( 0 );
        },

        /**
         * Check if variation is downloadable and show/hide elements
         */
        variable_is_downloadable: function() {
            $( this ).closest( '.dokan-product-variation-itmes' ).find( '.show_if_variation_downloadable' ).hide();

            if ( $( this ).is( ':checked' ) ) {
                $( this ).closest( '.dokan-product-variation-itmes' ).find( '.show_if_variation_downloadable' ).show();
            }
        },

        /**
         * Check if variation is virtual and show/hide elements
         */
        variable_is_virtual: function() {
            $( this ).closest( '.dokan-product-variation-itmes' ).find( '.hide_if_variation_virtual' ).show();

            if ( $( this ).is( ':checked' ) ) {
                $( this ).closest( '.dokan-product-variation-itmes' ).find( '.hide_if_variation_virtual' ).hide();
            }
        },

        /**
         * Check if variation manage stock and show/hide elements
         */
        variable_manage_stock: function() {
            $( this ).closest( '.dokan-product-variation-itmes' ).find( '.show_if_variation_manage_stock' ).hide();

            if ( $( this ).is( ':checked' ) ) {
                $( this ).closest( '.dokan-product-variation-itmes' ).find( '.show_if_variation_manage_stock' ).show();
            }
        },

        expand_all: function(e) {
            $(this).closest( '#dokan-variable-product-options-inner' ).find( '.dokan-product-variation-itmes > .dokan-variable-attributes' ).show();
            return false;
        },

        close_all: function(e) {
            $(this).closest( '#dokan-variable-product-options-inner' ).find( '.dokan-product-variation-itmes > .dokan-variable-attributes' ).hide();
            return false;
        },

        /**
         * Notice dismiss
         */
        // notice_dismiss: function() {
        //     $( this ).closest( 'div.notice' ).remove();
        // },

        /**
         * Run actions when variations is loaded
         *
         * @param {Object} event
         * @param {Int} needsUpdate
         */
        variations_loaded: function( event, needsUpdate ) {
            needsUpdate = needsUpdate || false;

            var wrapper = $( '.dokan-product-variation-wrapper' );

            if ( ! needsUpdate ) {
                // Show/hide downloadable, virtual and stock fields
                $( 'input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock', wrapper ).change();

                // Open sale schedule fields when have some sale price date
                $( '.dokan-product-variation-itmes', wrapper ).each( function( index, el ) {
                    var $el       = $( el ),
                        date_from = $( '.sale_price_dates_from', $el ).val(),
                        date_to   = $( '.sale_price_dates_to', $el ).val();

                    if ( '' !== date_from || '' !== date_to ) {
                        $( 'a.sale_schedule', $el ).click();
                    }
                });

                // Remove variation-needs-update classes
                $( '.dokan-variations-container .variation-needs-update', wrapper ).removeClass( 'variation-needs-update' );

                // Disable cancel and save buttons
                $( 'button.cancel-variation-changes, button.save-variation-changes', wrapper ).attr( 'disabled', 'disabled' );
            }


            $( 'h3.variation-topbar-heading', wrapper ).on( 'click', function(e) {
                e.preventDefault();

                var self = $(this);

                self.closest('.dokan-product-variation-itmes').find('.dokan-variable-attributes').slideToggle( 300, function() {
                    if ( $(this).is( ':visible' ) ) {
                        self.closest('.dokan-product-variation-itmes').find( 'i.fa-sort-desc' ).removeClass( 'fa-flip-horizointal' ).addClass( 'fa-flip-vertical' );
                    } else {
                        self.closest('.dokan-product-variation-itmes').find( 'i.fa-sort-desc' ).removeClass( 'fa-flip-vertical' ).addClass( 'fa-flip-horizointal' );
                    }
                });

            });

            $( '.toggle-variation-content', wrapper ).on( 'click', function(e) {
                e.preventDefault();

                var self = $(this);

                self.closest('.dokan-product-variation-itmes').find('.dokan-variable-attributes').slideToggle( 300, function() {
                    if ( $(this).is( ':visible' ) ) {
                        self.removeClass( 'fa-flip-horizointal' ).addClass( 'fa-flip-vertical' );
                    } else {
                        self.removeClass( 'fa-flip-vertical' ).addClass( 'fa-flip-horizointal' );
                    }
                } );

                return false;
            } );

            $('.tips').tooltip();

            // disable saving button for variable product
            function dokan_disable_variable_saving_button( $element ) {
                if ( Number( $element.closest('.variable_pricing').find('span.vendor-price').text() ) < 0 ) {
                    $element.closest( '.variable_pricing' ).find( '.save-variation-changes' ).attr( 'disabled', 'disabled' );
                    $element.closest( '.variable_pricing' ).find( '.dokan-product-less-price-alert' ).removeClass( 'dokan-hide' );
                    $( 'input[type=submit]' ).attr( 'disabled', 'disabled' );
                } else {
                    $element.closest( '.variable_pricing' ).find( '.dokan-product-less-price-alert' ).addClass( 'dokan-hide' );
                    $element.closest( '.variable_pricing' ).find( '.save-variation-changes' ).removeAttr( 'disabled' );
                    $( 'input[type=submit]' ).removeAttr( 'disabled' );
                }
            }

            // variable product price error checking
            function dokan_show_variable_product_earning_suggestion() {
                var selectedCategoryWrapper = $('select#product_cat').find('option:selected');

                if ( selectedCategoryWrapper.data( 'commission' ) != '' ) {
                    var vendor_percentage = selectedCategoryWrapper.data( 'commission' );
                    var commission_type = selectedCategoryWrapper.data( 'commission_type' );
                } else {
                    var commission_type = $('span.vendor-earning').attr( 'data-commission_type' );
                    var vendor_percentage = $('span.vendor-earning').attr( 'data-commission' );
                }

                if ( commission_type == 'percentage' ) {
                    if ( $('input.dokan-product-sales-price-variable' ).val() == '' ) {
                        $( 'input.dokan-product-regular-price-variable').each( function( i, elm ) {
                            var $element = $(elm);

                            $element.closest('.content-half-part').find('span.vendor-price').html(
                                parseFloat( accounting.formatNumber( ( ( $(this).closest( elm ).val() * vendor_percentage ) / 100 ), dokan.rounding_precision, '' ) )
                                .toString()
                                .replace( '.', dokan.mon_decimal_point )
                            )

                            dokan_disable_variable_saving_button( $element );
                        } );
                    } else {
                        $( 'input.dokan-product-sales-price-variable').each( function( i, elm ) {
                            var $element = $( elm );

                            $element.closest('.variable_pricing').find('span.vendor-price').html(
                                parseFloat( accounting.formatNumber( ( ( $(this).closest( elm ).val() * vendor_percentage ) / 100 ), dokan.rounding_precision, '' ) )
                                .toString()
                                .replace( '.', dokan.mon_decimal_point )
                            )

                            dokan_disable_variable_saving_button( $element );
                        } );
                    }
                } else {
                    if ( $('input.dokan-product-sales-price-variable' ).val() == '' ) {
                        $( 'input.dokan-product-regular-price-variable').each( function( i, elm ) {
                            var $element = $(elm);

                            $element.closest('.variable_pricing').find('span.vendor-price').html(
                                parseFloat( accounting.formatNumber( (  $(this).closest( elm ).val() - vendor_percentage ), dokan.rounding_precision, '' ) )
                                .toString()
                                .replace( '.', dokan.mon_decimal_point )
                            );

                            dokan_disable_variable_saving_button( $element );

                        } );
                    } else {
                        $( 'input.dokan-product-sales-price-variable' ).each( function( i, elm ) {
                            var $element = $( elm );

                            $element.closest('.variable_pricing').find('span.vendor-price').html(
                                parseFloat( accounting.formatNumber( (  $(this).closest( elm ).val() - vendor_percentage ), dokan.rounding_precision, '' ) )
                                .toString()
                                .replace( '.', dokan.mon_decimal_point )
                            );

                            dokan_disable_variable_saving_button( $element );
                        } );
                    }
                }
            }

            $( "input.dokan-product-regular-price-variable, input.dokan-product-sales-price-variable" ).on( 'keyup', function () {
                dokan_show_variable_product_earning_suggestion()
            } ).trigger( 'keyup' );

            // Datepicker fields
            $( '.sale_price_dates_fields', wrapper ).each( function() {
                var dates = $( this ).find( 'input' ).datepicker({
                    defaultDate:     '',
                    dateFormat:      'yy-mm-dd',
                    numberOfMonths:  1,
                    showButtonPanel: true,
                    onSelect:        function( selectedDate ) {
                        var option   = $( this ).is( '.sale_price_dates_from' ) ? 'minDate' : 'maxDate',
                            instance = $( this ).data( 'datepicker' ),
                            date     = $.datepicker.parseDate( instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings );

                        dates.not( this ).datepicker( 'option', option, date );
                        $( this ).change();
                    }
                });
            });

            // Allow sorting
            $( '.dokan-variations-container', wrapper ).sortable({
                items:                '.dokan-product-variation-itmes',
                cursor:               'move',
                axis:                 'y',
                handle:               '.sort',
                scrollSensitivity:    40,
                forcePlaceholderSize: true,
                helper:               'clone',
                opacity:              0.65,
                stop:                 function() {
                    Dokan_Product_Variation_Actions.variation_row_indexes();
                }
            });
        },

        /**
         * Run actions when added a variation
         *
         * @param {Object} event
         * @param {Int} qty
         */
        variation_added: function( event, qty ) {
            if ( 1 === qty ) {
                Dokan_Product_Variation_Actions.variations_loaded( null, true );
            }
        },

        /**
         * Lets the user manually input menu order to move items around pages
         */
        set_menu_order: function( event ) {
            event.preventDefault();
            var $menu_order  = $( this ).closest( '.dokan-product-variation-itmes' ).find('.variation_menu_order');
            var value        = window.prompt( dokan.i18n_enter_menu_order, $menu_order.val() );

            if ( value != null ) {
                // Set value, save changes and reload view
                $menu_order.val( parseInt( value, 10 ) ).change();
                Dokan_Product_Variation_Ajax.save_variations();
            }
        },

        /**
         * Set menu order
         */
        variation_row_indexes: function() {
            var wrapper      = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ),
                current_page = parseInt( wrapper.attr( 'data-page' ), 10 ),
                offset       = parseInt( ( current_page - 1 ) * dokan.variations_per_page, 10 );

            $( '.dokan-variations-container .dokan-product-variation-itmes' ).each( function ( index, el ) {
                $( '.variation_menu_order', el ).val( parseInt( $( el ).index( '.dokan-variations-container .dokan-product-variation-itmes' ), 10 ) + 1 + offset ).change();
            });
        }
    };

    /**
     * Product variations metabox ajax methods
     */
    var Dokan_Product_Variation_Ajax = {

        /**
         * Initialize variations ajax methods
         */
        init: function() {
            this.load_variations();
            this.initial_load();

            $( '#dokan-variable-product-options' )
                .on( 'click', 'button.save-variation-changes', this.save_variations )
                .on( 'click', 'button.cancel-variation-changes', this.cancel_variations )
                .on( 'click', '.remove_variation', this.remove_variation );

            $( document.body )
                .on( 'change', '#dokan-variable-product-options .dokan-variations-container :input', this.input_changed )
                .on( 'change', '.dokan-variations-defaults select', this.defaults_changed );

            $( 'form.dokan-product-edit-form' ).on( 'submit', this.save_on_submit );

            $( '#dokan-variable-product-options' ).on( 'click', 'a.do_variation_action', this.do_variation_action );
        },

        /**
         * Check if have some changes before leave the page
         *
         * @return {Bool}
         */
        check_for_changes: function() {
            var need_update = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container .variation-needs-update' );

            if ( 0 < need_update.length ) {
                if ( window.confirm( dokan.i18n_edited_variations ) ) {
                    Dokan_Product_Variation_Ajax.save_changes();
                } else {
                    need_update.removeClass( 'variation-needs-update' );
                    return false;
                }
            }

            return true;
        },

        /**
         * Block edit screen
         */
        block: function() {
            $( '.dokan-product-variation-wrapper' ).block({
                message: null,
                fadeIn: 100,
                fadeOut: 2000,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
        },

        /**
         * Unblock edit screen
         */
        unblock: function() {
            $( '.dokan-product-variation-wrapper' ).unblock();
        },

        /**
         * Initial load variations
         *
         * @return {Bool}
         */
        initial_load: function() {
            if ( 0 === $( '#dokan-variable-product-options' ).find( '.dokan-variations-container .dokan-product-variation-itmes' ).length ) {
                Dokan_Product_Variation_PageNav.go_to_page();
            }
        },

        /**
         * Load variations via Ajax
         *
         * @param {Int} page (default: 1)
         * @param {Int} per_page (default: 10)
         */
        load_variations: function( page, per_page ) {
            page     = page || 1;
            per_page = per_page || dokan.variations_per_page;

            var wrapper = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' );

            Dokan_Product_Variation_Ajax.block();

            $.ajax({
                url: dokan.ajaxurl,
                data: {
                    action:     'dokan_load_variations',
                    security:   dokan.load_variations_nonce,
                    product_id: $('#dokan-edit-product-id').val(),
                    attributes: wrapper.data( 'attributes' ),
                    page:       page,
                    per_page:   per_page
                },
                type: 'POST',
                success: function( response ) {
                    wrapper.empty().append( response ).attr( 'data-page', page );

                    $( '.dokan-product-variation-wrapper' ).trigger( 'dokan_variations_loaded' );

                    Dokan_Product_Variation_Ajax.unblock();
                }
            });
        },

        /**
         * Ger variations fields and convert to object
         *
         * @param  {Object} fields
         *
         * @return {Object}
         */
        get_variations_fields: function( fields ) {
            var data = $( ':input', fields ).serializeJSON();

            $( '.dokan-variations-defaults select' ).each( function( index, element ) {
                var select = $( element );
                data[ select.attr( 'name' ) ] = select.val();
            });

            return data;
        },

        /**
         * Save variations changes
         *
         * @param {Function} callback Called once saving is complete
         */
        save_changes: function( callback ) {
            var wrapper     = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ),
                need_update = $( '.variation-needs-update', wrapper ),
                data        = {};

            // Save only with products need update.
            if ( 0 < need_update.length ) {
                Dokan_Product_Variation_Ajax.block();

                data                 = Dokan_Product_Variation_Ajax.get_variations_fields( need_update );
                data.action          = 'dokan_save_variations';
                data.security        = dokan.save_variations_nonce;
                data.product_id      = $( '#dokan-edit-product-id' ).val();
                data['product-type'] = $( '#product_type' ).val();

                $.ajax({
                    url: dokan.ajaxurl,
                    data: data,
                    type: 'POST',
                    success: function( response ) {
                        // Allow change page, delete and add new variations
                        need_update.removeClass( 'variation-needs-update' );
                        $( 'button.cancel-variation-changes, button.save-variation-changes' ).attr( 'disabled', 'disabled' );

                        $( '.dokan-product-variation-wrapper' ).trigger( 'dokan_variations_saved' );

                        if ( typeof callback === 'function' ) {
                            callback( response );
                        }

                        Dokan_Product_Variation_Ajax.unblock();
                    }
                });
            }
        },

        /**
         * Save variations
         *
         * @return {Bool}
         */
        save_variations: function() {
            $( '#dokan-variable-product-options' ).trigger( 'dokan_variations_save_variations_button' );

            Dokan_Product_Variation_Ajax.save_changes( function( error ) {
                var wrapper = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ),
                    current = wrapper.attr( 'data-page' );

                $( '#dokan-variable-product-options' ).find( '#dokan_errors' ).remove();

                if ( error ) {
                    wrapper.before( error );
                }

                $( '.dokan-variations-defaults select' ).each( function() {
                    $( this ).attr( 'data-current', $( this ).val() );
                });

                Dokan_Product_Variation_PageNav.go_to_page( current );
            });

            return false;
        },

        /**
         * Save on post form submit
         */
        save_on_submit: function( e ) {
            var need_update = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container .variation-needs-update' );

            if ( 0 < need_update.length ) {
                e.preventDefault();
                $( '#dokan-variable-product-options' ).trigger( 'dokan_variations_save_variations_on_submit' );
                Dokan_Product_Variation_Ajax.save_changes( Dokan_Product_Variation_Ajax.save_on_submit_done );
            }
        },

        /**
         * After saved, continue with form submission
         */
        save_on_submit_done: function() {
            $( 'form.dokan-product-edit-form' ).submit();
        },

        /**
         * Discart changes.
         *
         * @return {Bool}
         */
        cancel_variations: function() {
            var current = parseInt( $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ).attr( 'data-page' ), 10 );

            $( '#dokan-variable-product-options' ).find( '.dokan-variations-container .variation-needs-update' ).removeClass( 'variation-needs-update' );
            $( '.dokan-variations-defaults select' ).each( function() {
                $( this ).val( $( this ).attr( 'data-current' ) );
            });

            Dokan_Product_Variation_PageNav.go_to_page( current );

            return false;
        },

        /**
         * Add variation
         *
         * @return {Bool}
         */
        add_variation: function() {
            Dokan_Product_Variation_Ajax.block();

            var data = {
                action: 'dokan_add_variation',
                post_id: $( '#dokan-edit-product-id' ).val(),
                loop: $( '.dokan-product-variation-itmes' ).length,
                security: dokan.add_variation_nonce
            };

            $.post( dokan.ajaxurl, data, function( response ) {
                var variation = $( response );
                variation.addClass( 'variation-needs-update' );

                $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ).prepend( variation );
                $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
                $( '.dokan-product-variation-wrapper' ).trigger( 'dokan_variations_added', 1 );
                Dokan_Product_Variation_Ajax.unblock();
            });

            return false;
        },

        /**
         * Remove variation
         *
         * @return {Bool}
         */
        remove_variation: function(e) {
            e.preventDefault();

            Dokan_Product_Variation_Ajax.check_for_changes();

            if ( window.confirm( dokan.i18n_remove_variation ) ) {
                var variation     = $( this ).attr( 'rel' ),
                    variation_ids = [],
                    data          = {
                        action: 'dokan_remove_variation'
                    };

                Dokan_Product_Variation_Ajax.block();

                if ( 0 < variation ) {
                    variation_ids.push( variation );

                    data.variation_ids = variation_ids;
                    data.security      = dokan.delete_variations_nonce;

                    $.post( dokan.ajaxurl, data, function() {
                        var wrapper      = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ),
                            current_page = parseInt( wrapper.attr( 'data-page' ), 10 ),
                            total_pages  = Math.ceil( ( parseInt( wrapper.attr( 'data-total' ), 10 ) - 1 ) / dokan.variations_per_page ),
                            page         = 1;

                        $( '.dokan-product-variation-wrapper' ).trigger( 'dokan_variations_removed' );

                        if ( current_page === total_pages || current_page <= total_pages ) {
                            page = current_page;
                        } else if ( current_page > total_pages && 0 !== total_pages ) {
                            page = total_pages;
                        }

                        Dokan_Product_Variation_PageNav.go_to_page( page, -1 );
                    });

                } else {
                    Dokan_Product_Variation_Ajax.unblock();
                }
            }

            return false;
        },

        /**
         * Link all variations (or at least try :p)
         *
         * @return {Bool}
         */
        link_all_variations: function() {
            Dokan_Product_Variation_Ajax.check_for_changes();

            if ( window.confirm( dokan.i18n_link_all_variations ) ) {
                Dokan_Product_Variation_Ajax.block();

                var data = {
                    action: 'dokan_link_all_variations',
                    post_id: $('#dokan-edit-product-id').val(),
                    security: dokan.link_variation_nonce
                };

                $.post( dokan.ajaxurl, data, function( response ) {
                    var count = parseInt( response, 10 );

                    if ( 1 === count ) {
                        window.alert( count + ' ' + dokan.i18n_variation_added );
                    } else if ( 0 === count || count > 1 ) {
                        window.alert( count + ' ' + dokan.i18n_variations_added );
                    } else {
                        window.alert( dokan.i18n_no_variations_added );
                    }

                    if ( count > 0 ) {
                        Dokan_Product_Variation_PageNav.go_to_page( 1, count );
                        $( '.dokan-product-variation-wrapper' ).trigger( 'dokan_variations_added', count );
                    } else {
                        Dokan_Product_Variation_Ajax.unblock();
                    }
                });
            }

            return false;
        },

        /**
         * Add new class when have changes in some input
         */
        input_changed: function() {
            $( this )
                .closest( '.dokan-product-variation-itmes' )
                .addClass( 'variation-needs-update' );

            $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );

            $( '.dokan-product-variation-wrapper' ).trigger( 'dokan_variations_input_changed' );
        },

        /**
         * Added new .variation-needs-update class when defaults is changed
         */
        defaults_changed: function() {
            $( this )
                .closest( '#dokan-variable-product-options' )
                .find( '.dokan-product-variation-itmes:first' )
                .addClass( 'variation-needs-update' );

            $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );

            $( '#dokan-variable-product-options' ).trigger( 'dokan_variations_defaults_changed' );
        },

        /**
         * Actions
         */
        do_variation_action: function() {
            var do_variation_action = $( 'select.variation-actions' ).val(),
                data       = {},
                changes    = 0,
                value;

            switch ( do_variation_action ) {
                case 'add_variation' :
                    Dokan_Product_Variation_Ajax.add_variation();
                    return;
                case 'link_all_variations' :
                    Dokan_Product_Variation_Ajax.link_all_variations();
                    return;
                case 'delete_all' :
                    if ( window.confirm( dokan.i18n_delete_all_variations ) ) {
                        if ( window.confirm( dokan.i18n_last_warning ) ) {
                            data.allowed = true;
                            changes      = parseInt( $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ).attr( 'data-total' ), 10 ) * -1;
                        }
                    }
                    break;
                case 'variable_regular_price_increase' :
                case 'variable_regular_price_decrease' :
                case 'variable_sale_price_increase' :
                case 'variable_sale_price_decrease' :
                    value = window.prompt( dokan.i18n_enter_a_value_fixed_or_percent );

                    if ( value != null ) {
                        if ( value.indexOf( '%' ) >= 0 ) {
                            data.value = accounting.unformat( value.replace( /\%/, '' ), dokan_refund.mon_decimal_point ) + '%';
                        } else {
                            data.value = accounting.unformat( value, dokan_refund.mon_decimal_point );
                        }
                    }
                    break;
                case 'variable_regular_price' :
                case 'variable_sale_price' :
                case 'variable_stock' :
                case 'variable_weight' :
                case 'variable_length' :
                case 'variable_width' :
                case 'variable_height' :
                case 'variable_download_limit' :
                case 'variable_download_expiry' :
                    value = window.prompt( dokan.i18n_enter_a_value );

                    if ( value != null ) {
                        data.value = value;
                    }
                    break;
                case 'variable_sale_schedule' :
                    data.date_from = window.prompt( dokan.i18n_scheduled_sale_start );
                    data.date_to   = window.prompt( dokan.i18n_scheduled_sale_end );

                    if ( null === data.date_from ) {
                        data.date_from = false;
                    }

                    if ( null === data.date_to ) {
                        data.date_to = false;
                    }
                    break;
                default :
                    $( 'select.variation-actions' ).trigger( do_variation_action );
                    data = $( 'select.variation-actions' ).triggerHandler( do_variation_action + '_ajax_data', data );
                    break;
            }

            if ( 'delete_all' === do_variation_action && data.allowed ) {
                $( '#dokan-variable-product-options' ).find( '.variation-needs-update' ).removeClass( 'variation-needs-update' );
            } else {
                Dokan_Product_Variation_Ajax.check_for_changes();
            }

            Dokan_Product_Variation_Ajax.block();

            $.ajax({
                url: dokan.ajaxurl,
                data: {
                    action:       'dokan_bulk_edit_variations',
                    security:     dokan.bulk_edit_variations_nonce,
                    product_id:   $( '#dokan-edit-product-id' ).val(),
                    product_type: $( '#product_type' ).val(),
                    bulk_action:  do_variation_action,
                    data:         data
                },
                type: 'POST',
                success: function() {
                    Dokan_Product_Variation_PageNav.go_to_page( 1, changes );
                }
            });
        }
    };

    /**
     * Product variations pagenav
     */
    var Dokan_Product_Variation_PageNav = {

        /**
         * Initialize products variations meta box
         */
        init: function() {
            $( document.body )
                .on( 'dokan_variations_added', this.update_single_quantity )
                .on( 'change', '.dokan-variations-pagenav .page-selector', this.page_selector )
                .on( 'click', '.dokan-variations-pagenav .first-page', this.first_page )
                .on( 'click', '.dokan-variations-pagenav .prev-page', this.prev_page )
                .on( 'click', '.dokan-variations-pagenav .next-page', this.next_page )
                .on( 'click', '.dokan-variations-pagenav .last-page', this.last_page );
        },

        /**
         * Set variations count
         *
         * @param {Int} qty
         *
         * @return {Int}
         */
        update_variations_count: function( qty ) {
            var wrapper        = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ),
                total          = parseInt( wrapper.attr( 'data-total' ), 10 ) + qty,
                displaying_num = $( '.dokan-variations-pagenav .displaying-num' );

            // Set the new total of variations
            wrapper.attr( 'data-total', total );

            if ( 1 === total ) {
                displaying_num.text( dokan.i18n_variation_count_single.replace( '%qty%', total ) );
            } else {
                displaying_num.text( dokan.i18n_variation_count_plural.replace( '%qty%', total ) );
            }

            return total;
        },

        /**
         * Update variations quantity when add a new variation
         *
         * @param {Object} event
         * @param {Int} qty
         */
        update_single_quantity: function( event, qty ) {
            if ( 1 === qty ) {
                var page_nav = $( '.dokan-variations-pagenav' );

                Dokan_Product_Variation_PageNav.update_variations_count( qty );

                if ( page_nav.is( ':hidden' ) ) {
                    $( 'option, optgroup', 'select.variation-actions' ).show();
                    $( 'select.variation-actions' ).val( 'add_variation' );
                    $( '#dokan-variable-product-options' ).find( '.dokan-variation-action-toolbar' ).show();
                    page_nav.show();
                    $( '.pagination-links', page_nav ).hide();
                }
            }
        },

        /**
         * Set the pagenav fields
         *
         * @param {Int} qty
         */
        set_paginav: function( qty ) {
            var wrapper          = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ),
                new_qty          = Dokan_Product_Variation_PageNav.update_variations_count( qty ),
                toolbar          = $( '#dokan-variable-product-options' ).find( '.dokan-variation-action-toolbar' ),
                variation_action = $( 'select.variation-actions' ),
                page_nav         = $( '.dokan-variations-pagenav' ),
                displaying_links = $( '.pagination-links', page_nav ),
                total_pages      = Math.ceil( new_qty / dokan.variations_per_page ),
                options          = '';

            // Set the new total of pages
            wrapper.attr( 'data-total_pages', total_pages );

            $( '.total-pages', page_nav ).text( total_pages );

            // Set the new pagenav options
            for ( var i = 1; i <= total_pages; i++ ) {
                options += '<option value="' + i + '">' + i + '</option>';
            }

            $( '.page-selector', page_nav ).empty().html( options );

            // Show/hide pagenav
            if ( 0 === new_qty ) {
                toolbar.not( '.toolbar-top, .toolbar-buttons' ).hide();
                page_nav.hide();
                $( 'option, optgroup', variation_action ).hide();
                $( 'select.variation-actions' ).val( 'add_variation' );
                $( 'option[data-global="true"]', variation_action ).show();

            } else {
                toolbar.show();
                page_nav.show();
                $( 'option, optgroup', variation_action ).show();
                $( 'select.variation-actions' ).val( 'add_variation' );

                // Show/hide links
                if ( 1 === total_pages ) {
                    displaying_links.hide();
                } else {
                    displaying_links.show();
                }
            }
        },

        /**
         * Check button if enabled and if don't have changes
         *
         * @return {Bool}
         */
        check_is_enabled: function( current ) {
            return ! $( current ).hasClass( 'disabled' );
        },

        /**
         * Change "disabled" class on pagenav
         */
        change_classes: function( selected, total ) {
            var first_page = $( '.dokan-variations-pagenav .first-page' ),
                prev_page  = $( '.dokan-variations-pagenav .prev-page' ),
                next_page  = $( '.dokan-variations-pagenav .next-page' ),
                last_page  = $( '.dokan-variations-pagenav .last-page' );

            if ( 1 === selected ) {
                first_page.addClass( 'disabled' );
                prev_page.addClass( 'disabled' );
            } else {
                first_page.removeClass( 'disabled' );
                prev_page.removeClass( 'disabled' );
            }

            if ( total === selected ) {
                next_page.addClass( 'disabled' );
                last_page.addClass( 'disabled' );
            } else {
                next_page.removeClass( 'disabled' );
                last_page.removeClass( 'disabled' );
            }
        },

        /**
         * Set page
         */
        set_page: function( page ) {
            $( '.dokan-variations-pagenav .page-selector' ).val( page ).first().change();
        },

        /**
         * Navigate on variations pages
         *
         * @param {Int} page
         * @param {Int} qty
         */
        go_to_page: function( page, qty ) {
            page = page || 1;
            qty  = qty || 0;

            Dokan_Product_Variation_PageNav.set_paginav( qty );
            Dokan_Product_Variation_PageNav.set_page( page );
        },

        /**
         * Paginav pagination selector
         */
        page_selector: function() {
            var selected = parseInt( $( this ).val(), 10 ),
                wrapper  = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' );

            $( '.dokan-variations-pagenav .page-selector' ).val( selected );

            Dokan_Product_Variation_Ajax.check_for_changes();
            Dokan_Product_Variation_PageNav.change_classes( selected, parseInt( wrapper.attr( 'data-total_pages' ), 10 ) );
            Dokan_Product_Variation_Ajax.load_variations( selected );
        },

        /**
         * Go to first page
         *
         * @return {Bool}
         */
        first_page: function() {
            if ( Dokan_Product_Variation_PageNav.check_is_enabled( this ) ) {
                Dokan_Product_Variation_PageNav.set_page( 1 );
            }

            return false;
        },

        /**
         * Go to previous page
         *
         * @return {Bool}
         */
        prev_page: function() {
            if ( Dokan_Product_Variation_PageNav.check_is_enabled( this ) ) {
                var wrapper   = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ),
                    prev_page = parseInt( wrapper.attr( 'data-page' ), 10 ) - 1,
                    new_page  = ( 0 < prev_page ) ? prev_page : 1;

                Dokan_Product_Variation_PageNav.set_page( new_page );
            }

            return false;
        },

        /**
         * Go to next page
         *
         * @return {Bool}
         */
        next_page: function() {
            if ( Dokan_Product_Variation_PageNav.check_is_enabled( this ) ) {
                var wrapper     = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ),
                    total_pages = parseInt( wrapper.attr( 'data-total_pages' ), 10 ),
                    next_page   = parseInt( wrapper.attr( 'data-page' ), 10 ) + 1,
                    new_page    = ( total_pages >= next_page ) ? next_page : total_pages;

                Dokan_Product_Variation_PageNav.set_page( new_page );
            }

            return false;
        },

        /**
         * Go to last page
         *
         * @return {Bool}
         */
        last_page: function() {
            if ( Dokan_Product_Variation_PageNav.check_is_enabled( this ) ) {
                var last_page = $( '#dokan-variable-product-options' ).find( '.dokan-variations-container' ).attr( 'data-total_pages' );

                Dokan_Product_Variation_PageNav.set_page( last_page );
            }

            return false;
        }
    };

    // On DOM ready
    $(function() {
        if ( $( '#dokan-variable-product-options' ).length ) {
            Dokan_Product_Variation_Actions.init();
            Dokan_Product_Variation_Ajax.init();
            Dokan_Product_Variation_PageNav.init();
        }
    });


});

;(function($){
    var variantsHolder = $('#variants-holder');

    var Dokan_Editor = {

        init: function() {
            product_type = 'simple';

            // post status change
            $('.dokan-toggle-sidebar').on('click', 'a.dokan-toggle-edit', this.sidebarToggle.showStatus );
            $('.dokan-toggle-sidebar').on('click', 'a.dokan-toggle-save', this.sidebarToggle.saveStatus );
            $('.dokan-toggle-sidebar').on('click', 'a.dokan-toggle-cacnel', this.sidebarToggle.cancel );

            // Tab view variants
            $('#product-attributes').on('click', '.add-variant-category', this.variants.addCategory );
            $('#variants-holder').on('click', '.box-header .row-remove', this.variants.removeCategory );
            $('#variants-holder').on('click', '.item-action a.row-add', this.variants.addItem );
            $('#variants-holder').on('click', '.item-action a.row-remove', this.variants.removeItem );

            $('body, #variable_product_options').on( 'click', '.sale_schedule', this.variants.saleSchedule );
            $('body, #variable_product_options').on( 'click', '.cancel_sale_schedule', this.variants.cancelSchedule );
            $('#variable_product_options').on('woocommerce_variations_added', this.variants.onVariantAdded );
            $('.save_attributes').on('click', this.variants.save );

            this.variants.dates();
            this.variants.initSaleSchedule();

            // shipping
            $('.product-edit-new-container, #product-shipping').on('change', 'input[type=checkbox]#_overwrite_shipping', this.editProduct.shipping.showHideOverride );
            $('.product-edit-new-container').on('change', 'input[type=checkbox]#_disable_shipping', this.editProduct.shipping.disableOverride );
            $('#product-shipping').on('click', '#_disable_shipping', this.shipping.disableOverride );
            $('.product-edit-new-container, .product_lot_discount').on('change', 'input[type=checkbox]#_is_lot_discount', this.editProduct.showLotDiscountWrapper );
            $('body').on( 'click', '.upload_image_button', this.editProduct.loadVariationImage );


            this.editProduct.shipping.showHideOverride();
            this.editProduct.shipping.disableOverride();
            this.shipping.disableOverride();
            $('#_disable_shipping').trigger('change');
            $('#_overwrite_shipping').trigger('change');

            $( '.hide_if_lot_discount' ).hide();
            $( '.hide_if_order_discount' ).hide();

            // save attributes

            $( 'body' ).on( 'dokan-product-editor-loaded', this.bindProductTagDropdown );
+           $( 'body' ).on( 'dokan-product-editor-popup-opened', this.bindProductTagDropdown );
        },

        editProduct: {
            showLotDiscountWrapper: function(){
                if ( $( this ).is(':checked') ) {
                    $( '.show_if_needs_lot_discount' ).slideDown('fast');
                } else {
                    $( '.show_if_needs_lot_discount' ).slideUp('fast');
                }
            },

            loadVariationImage: function(e) {
                e.preventDefault();
                var variable_image_frame;
                var $button                = $(this);
                var post_id                = $button.attr('rel');
                var $parent                = $button.closest('.upload_image');
                setting_variation_image    = $parent;
                placeholder_iamge          = dokan.dokan_placeholder_img_src;
                setting_variation_image_id = post_id;

                e.preventDefault();

                if ( $button.is('.dokan-img-remove') ) {

                    setting_variation_image.find( '.upload_image_id' ).val( '' );
                    setting_variation_image.find( 'img' ).attr( 'src', placeholder_iamge );
                    setting_variation_image.find( '.upload_image_button' ).removeClass( 'dokan-img-remove' );
                    $button.closest( '.dokan-product-variation-itmes' ).addClass( 'variation-needs-update' );
                    $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
                    $( '.dokan-product-variation-wrapper' ).trigger( 'dokan_variations_input_changed' );

                } else {

                    // If the media frame already exists, reopen it.
                    if ( variable_image_frame ) {
                        variable_image_frame.uploader.uploader.param( 'post_id', setting_variation_image_id );
                        variable_image_frame.open();
                        return;
                    } else {
                        wp.media.model.settings.post.id = setting_variation_image_id;
                        wp.media.model.settings.type = 'dokan';
                    }

                    // Create the media frame.
                    variable_image_frame = wp.media.frames.variable_image = wp.media({
                        // Set the title of the modal.
                        title: dokan.i18n_choose_image,
                        button: {
                            text: dokan.i18n_set_image
                        }
                    });

                    // When an image is selected, run a callback.
                    variable_image_frame.on( 'select', function() {

                        attachment = variable_image_frame.state().get('selection').first().toJSON();

                        setting_variation_image.find( '.upload_image_id' ).val( attachment.id );
                        setting_variation_image.find( '.upload_image_button' ).addClass( 'dokan-img-remove' );
                        setting_variation_image.find( 'img' ).attr( 'src', attachment.url );
                        $button.closest( '.dokan-product-variation-itmes' ).addClass( 'variation-needs-update' );
                        $( 'button.cancel-variation-changes, button.save-variation-changes' ).removeAttr( 'disabled' );
                        $( '.dokan-product-variation-wrapper' ).trigger( 'dokan_variations_input_changed' );

                        wp.media.model.settings.post.id = setting_variation_image_id;
                    });

                    // Finally, open the modal.
                    variable_image_frame.open();
                }
            },

            shipping: {
                showHideOverride: function() {
                    if ( $('#_overwrite_shipping').is(':checked') ) {
                        $('.show_if_override').show();
                    } else {
                        $('.show_if_override').hide();
                    }
                },

                disableOverride: function() {
                    if ( $('#_disable_shipping').is(':checked') ) {
                        $('.show_if_needs_shipping').show();
                        $( '#_overwrite_shipping').trigger('change')
                    } else {
                        $('.show_if_needs_shipping').hide();
                    }
                }
            }
        },

        variants: {
            addCategory: function (e) {
                e.preventDefault();

                var product_types = $('#product_type').val();
                var check = $(this).closest('p.toolbar').find('select.select-attribute').val();
                var row = $('.inputs-box').length;

                if ( check == '' ) {
                    var category = wp.template('sc-category');
                    variantsHolder.append( category( { row:row } ) ).children(':last').hide().fadeIn();
                } else {
                    var data = {
                        row: row,
                        name: check,
                        type: product_types,
                        action: 'dokan_pre_define_attribute',
                    };

                    $('#product-attributes .toolbar').block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });

                    $.post( dokan.ajaxurl, data, function(resp) {
                        if ( resp.success ) {
                            variantsHolder.append(resp.data).children(':last').hide().fadeIn();
                        }
                        $('#product-attributes .toolbar').unblock();

                    });
                }

                if ( product_type === 'simple' ) {
                    variantsHolder.find('.show_if_variable').hide();
                }

            },

            removeCategory: function (e) {
                e.preventDefault();

                if ( confirm('Sure?') ) {
                    $(this).parents('.inputs-box').fadeOut(function() {
                        $(this).remove();
                    });
                }
            },

            addItem: function (e) {
                e.preventDefault();

                var self = $(this),
                    wrap = self.closest('.inputs-box'),
                    list = self.closest('ul.option-couplet');

                var col = list.find('li').length,
                    row = wrap.data('count');


                var template = _.template( $('#tmpl-sc-category-item').html() );
                self.closest('li').after(template({'row': row, 'col': col}));
            },

            removeItem: function (e) {
                e.preventDefault();

                var options = $(this).parents('ul').find('li');

                // don't remove if only one option is there
                if ( options.length > 1 ) {
                    $(this).parents('li').fadeOut(function() {
                        $(this).remove();
                    });
                }
            },

            save: function() {

                var data = {
                    post_id: $(this).data('id'),
                    data:  $('.woocommerce_attributes').find('input, select, textarea').serialize(),
                    action:  'dokan_save_attributes'
                };

                var this_page = window.location.toString();

                $('#variants-holder').block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
                $.post(ajaxurl, data, function(resp) {

                    $('#variable_product_options').block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
                    $('#variable_product_options').load( this_page + ' #variable_product_options_inner', function() {
                        $('#variable_product_options').unblock();
                    } );

                    // fire change events for varaiations
                    $('input.variable_is_downloadable, input.variable_is_virtual, input.variable_manage_stock').trigger('change');

                    $('#variants-holder').unblock();
                });
            },

            initSaleSchedule: function() {
                // Sale price schedule
                $('.sale_price_dates_fields').each(function() {

                    var $these_sale_dates = $(this);
                    var sale_schedule_set = false;
                    var $wrap = $these_sale_dates.closest( 'div, table' );

                    $these_sale_dates.find('input').each(function(){
                        if ( $(this).val() != '' )
                            sale_schedule_set = true;
                    });

                    if ( sale_schedule_set ) {
                        $wrap.find('.sale_schedule').hide();
                        $wrap.find('.cancel_sale_schedule').show();
                        $wrap.find('.sale_price_dates_fields').show();
                    } else {
                        $wrap.find('.sale_schedule').show();
                        $wrap.find('.cancel_sale_schedule').hide();
                        $wrap.find('.sale_price_dates_fields').hide();
                    }
                });
            },

            saleSchedule: function() {
                var $wrap = $(this).closest( 'div, table' );

                $(this).hide();
                $wrap.find('.cancel_sale_schedule').show();
                $wrap.find('.sale_price_dates_fields').show();

                return false;
            },

            cancelSchedule: function() {
                var $wrap = $(this).closest( 'div, table' );

                $(this).hide();
                $wrap.find('.sale_schedule').show();
                $wrap.find('.sale_price_dates_fields').hide();
                $wrap.find('.sale_price_dates_fields').find('input').val('');

                return false;
            },

            dates: function() {
                var dates = $( ".sale_price_dates_fields input" ).datepicker({
                    defaultDate: "",
                    dateFormat: "yy-mm-dd",
                    numberOfMonths: 1
                });
            },

            onVariantAdded: function() {
                Dokan_Editor.variants.dates();
            }
        },

        sidebarToggle: {
            showStatus: function(e) {
                var container = $(this).siblings('.dokan-toggle-select-container');

                if (container.is(':hidden')) {
                    container.slideDown('fast');

                    $(this).hide();
                }

                return false;
            },

            saveStatus: function(e) {
                var container = $(this).closest('.dokan-toggle-select-container');

                container.slideUp('fast');
                container.siblings('a.dokan-toggle-edit').show();

                // update the text
                var text = $('option:selected', container.find('select.dokan-toggle-select')).text();
                container.siblings('.dokan-toggle-selected-display').html(text);

                return false;
            },

            cancel: function(e) {
                var container = $(this).closest('.dokan-toggle-select-container');

                container.slideUp('fast');
                container.siblings('a.dokan-toggle-edit').show();

                return false;
            }
        },

        shipping: {
            disableOverride: function() {
                if ( $('#_disable_shipping').is(':checked') ) {
                    $('.hide_if_disable').hide();
                } else {
                    $('.hide_if_disable').show();
                    Dokan_Editor.editProduct.shipping.showHideOverride();
                }
            }
        },

        bindProductTagDropdown: function () {
            if ( ! ( dokan.product_vendors_can_create_tags && 'on' === dokan.product_vendors_can_create_tags ) ) {
                return;
            }

            $( '#product_tag' ).select2( {
                tags: true,
                language: {
                    noResults: function () {
                        return dokan.i18n_no_result_found;
                    }
                }
            } );
        }
    }

    Dokan_Editor.init();

})(jQuery);

;(function($){

    var Dokan_Comments = {

        init: function() {
            $('#dokan-comments-table').on('click', '.dokan-cmt-action', this.setCommentStatus);
            $('.dokan-check-all').on('click', this.toggleCheckbox);
        },

        toggleCheckbox: function() {
            $(".dokan-check-col").prop('checked', $(this).prop('checked'));
        },

        setCommentStatus: function(e) {
            e.preventDefault();

            var self = $(this),
                comment_id = self.data('comment_id'),
                comment_status = self.data('cmt_status'),
				page_status = self.data('page_status'),
				post_type = self.data('post_type'),
				curr_page = self.data('curr_page'),
                tr = self.closest('tr'),
                data = {
                    'action': 'dokan_comment_status',
                    'comment_id': comment_id,
                    'comment_status': comment_status,
					'page_status': page_status,
					'post_type': post_type,
					'curr_page': curr_page,
					'nonce': dokan.nonce
                };

            $.post(dokan.ajaxurl, data, function(resp){

                if ( resp.success == false ) {
                    alert( resp.data );
                } else {
                    if( page_status === 1 ) {
                        if ( comment_status === 1 || comment_status === 0) {
                            tr.fadeOut(function() {
                                tr.replaceWith(resp.data['content']).fadeIn();
                            });

                        } else {
                            tr.fadeOut(function() {
                                $(this).remove();
                            });
                        }
                    } else {
                        tr.fadeOut(function() {
                            $(this).remove();
                        });
                    }

                    if(resp.data['pending'] == null) resp.data['pending'] = 0;
                    if(resp.data['spam'] == null) resp.data['spam'] = 0;
                    if(resp.data['trash'] == null) resp.data['trash'] = 0;
    				if(resp.data['approved'] == null) resp.data['approved'] = 0;

                    $('.comments-menu-approved').text(resp.data['approved']);
                    $('.comments-menu-pending').text(resp.data['pending']);
                    $('.comments-menu-spam').text(resp.data['spam']);
    				$('.comments-menu-trash').text(resp.data['trash']);
                }
            });
        }

    };

    $(function(){
        Dokan_Comments.init();
    });

})(jQuery);
!function(a){var b={init:function(){a("#dokan-comments-table").on("click",".dokan-cmt-action",this.setCommentStatus),a("#dokan-comments-table").on("click","button.dokan-cmt-close-form",this.closeForm),a("#dokan-comments-table").on("click","button.dokan-cmt-submit-form",this.submitForm),a("#dokan-comments-table").on("click",".dokan-cmt-edit",this.populateForm),a(".dokan-check-all").on("click",this.toggleCheckbox)},toggleCheckbox:function(){a(".dokan-check-col").prop("checked",a(this).prop("checked"))},setCommentStatus:function(b){b.preventDefault();var c=a(this),d=c.data("comment_id"),e=c.data("cmt_status"),f=c.data("page_status"),g=c.data("post_type"),h=c.data("curr_page"),i=c.closest("tr"),j={action:"dokan_comment_status",comment_id:d,comment_status:e,page_status:f,post_type:g,curr_page:h,nonce:dokan.nonce};a.post(dokan.ajaxurl,j,function(b){1===f&&(1===e||0===e)?i.fadeOut(function(){i.replaceWith(b.data.content).fadeIn()}):i.fadeOut(function(){a(this).remove()}),null==b.data.pending&&(b.data.pending=0),null==b.data.spam&&(b.data.spam=0),null==b.data.trash&&(b.data.trash=0),a(".comments-menu-pending").text(b.data.pending),a(".comments-menu-spam").text(b.data.spam),a(".comments-menu-trash").text(b.data.trash)})},populateForm:function(b){b.preventDefault();var c=a(this).closest("tr");if(c.next().hasClass("dokan-comment-edit-row"))return void c.next().remove();var d=a("#dokan-edit-comment-row").html(),e={author:c.find(".dokan-cmt-hid-author").text(),email:c.find(".dokan-cmt-hid-email").text(),url:c.find(".dokan-cmt-hid-url").text(),body:c.find(".dokan-cmt-hid-body").text(),id:c.find(".dokan-cmt-hid-id").text(),status:c.find(".dokan-cmt-hid-status").text()};c.after(_.template(d,e))},closeForm:function(b){b.preventDefault(),a(this).closest("tr.dokan-comment-edit-row").remove()},submitForm:function(b){b.preventDefault();var c=a(this),d=c.closest("tr.dokan-comment-edit-row"),e={action:"dokan_update_comment",comment_id:d.find("input.dokan-cmt-id").val(),content:d.find("textarea.dokan-cmt-body").val(),author:d.find("input.dokan-cmt-author").val(),email:d.find("input.dokan-cmt-author-email").val(),url:d.find("input.dokan-cmt-author-url").val(),status:d.find("input.dokan-cmt-status").val(),nonce:dokan.nonce,post_type:d.find("input.dokan-cmt-post-type").val()};a.post(dokan.ajaxurl,e,function(a){a.success===!0?(d.prev().replaceWith(a.data),d.remove()):alert(a.data)})}};a(function(){b.init()})}(jQuery);
// Shipping tab js
(function($){

    $(document).ready(function(){

        $('.dokan-shipping-location-wrapper').on('change', '.dps_country_selection', function() {
            var self = $(this),
                data = {
                    country_id : self.find(':selected').val(),
                    action  : 'dps_select_state_by_country'
                };

            if ( self.val() == '' || self.val() == 'everywhere' ) {
                self.closest('.dps-shipping-location-content').find('table.dps-shipping-states tbody').html('');
            } else {
                $.post( dokan.ajaxurl, data, function(response) {
                    if( response.success ) {
                        self.closest('.dps-shipping-location-content').find('table.dps-shipping-states tbody').html(response.data);
                    }
                });
            }
        });

        $('.dps-main-wrapper').on('click', 'a.dps-shipping-add', function(e) {
            e.preventDefault();

            html = $('#dps-shipping-hidden-lcoation-content');
            var row = $(html).first().clone().appendTo($('.dokan-shipping-location-wrapper')).show();
            $('.dokan-shipping-location-wrapper').find('.dps-shipping-location-content').first().find('a.dps-shipping-remove').show();

            $('.tips').tooltip();

            row.removeAttr('id');
            row.find('input,select').val('');
            row.find('a.dps-shipping-remove').show();
        });

        $('.dokan-shipping-location-wrapper').on('click', 'a.dps-shipping-remove', function(e) {
            e.preventDefault();
            $(this).closest('.dps-shipping-location-content').remove();
            $dpsElm = $('.dokan-shipping-location-wrapper').find('.dps-shipping-location-content');

            if( $dpsElm.length == 1) {
                $dpsElm.first().find('a.dps-shipping-remove').hide();
            }
        });

        $('.dokan-shipping-location-wrapper').on('click', 'a.dps-add', function(e) {
            e.preventDefault();

            var row = $(this).closest('tr').first().clone().appendTo($(this).closest('table.dps-shipping-states'));
            row.find('input,select').val('');
            row.find('a.dps-remove').show();
            $('.tips').tooltip();
        });

        $('.dokan-shipping-location-wrapper').on('click', 'a.dps-remove', function(e) {
            e.preventDefault();

            if( $(this).closest('table.dps-shipping-states').find( 'tr' ).length == 1 ){
                $(this).closest('.dps-shipping-location-content').find('td.dps_shipping_location_cost').show();
            }

            $(this).closest('tr').remove();


        });

        $('.dokan-shipping-location-wrapper').on('change keyup', '.dps_state_selection', function() {
            var self = $(this);

            if( self.val() == '' || self.val() == '-1' ) {
                self.closest('.dps-shipping-location-content').find('td.dps_shipping_location_cost').show();
            } else {
                self.closest('.dps-shipping-location-content').find('td.dps_shipping_location_cost').hide();
            }
        });

        $('.dokan-shipping-location-wrapper .dps_state_selection').trigger('change');
        $('.dokan-shipping-location-wrapper .dps_state_selection').trigger('keyup');

        $wrap = $('.dokan-shipping-location-wrapper').find('.dps-shipping-location-content');

        if( $wrap.length == 1) {
            $wrap.first().find('a.dps-shipping-remove').hide();
        }

    });

})(jQuery);

//dokan store seo form submit
(function($){

    var wrapper = $( '.dokan-store-seo-wrapper' );
    var Dokan_Store_SEO = {

        init : function() {
            wrapper.on( 'submit', 'form#dokan-store-seo-form', this.form.validate );
        },

        form : {
            validate : function(e){
                e.preventDefault();

                var self = $( this ),
                data = {
                    action: 'dokan_seo_form_handler',
                    data: self.serialize(),
                };

                Dokan_Store_SEO.form.submit( data );

                return false;
            },

            submit : function( data ){
                var feedback = $('#dokan-seo-feedback');
                feedback.fadeOut();

                $.post( dokan.ajaxurl, data, function ( resp ) {
                    if ( resp.success ) {
                        feedback.html(resp.data);
                        feedback.removeClass('dokan-hide');
                        feedback.addClass('dokan-alert-success');
                        feedback.fadeIn();
                    } else {
                        feedback.html(resp.data);
                        feedback.addClass('dokan-alert-danger');
                        feedback.removeClass('dokan-hide');
                        feedback.fadeIn();
                    }
                } )
            }
        },
    };

    $(function() {
        Dokan_Store_SEO.init();
    });

})(jQuery);
