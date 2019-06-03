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
