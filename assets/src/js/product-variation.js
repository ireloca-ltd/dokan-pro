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
