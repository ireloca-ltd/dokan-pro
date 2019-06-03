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
