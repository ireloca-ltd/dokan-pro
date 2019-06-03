// Remove a person type
jQuery( '#bookings_persons' ).on( 'click', 'button.remove_booking_person', function ( e ) {
    e.preventDefault();
    var answer = confirm( wc_bookings_writepanel_js_params.i18n_remove_person );
    if ( answer ) {

        var el = jQuery( this ).parent().parent();

        var person = jQuery( this ).attr( 'rel' );

        if ( person > 0 ) {

            jQuery( el ).block( { message: null } );

            var data = {
                action: 'woocommerce_remove_bookable_person',
                person_id: person,
                security: wc_bookings_writepanel_js_params.nonce_delete_person
            };

            jQuery.post( wc_bookings_writepanel_js_params.ajax_url, data, function ( response ) {
                jQuery( el ).fadeOut( '300', function () {
                    jQuery( el ).remove();
                } );
            } );

        } else {
            jQuery( el ).fadeOut( '300', function () {
                jQuery( el ).remove();
            } );
        }

    }
    return false;
} );

jQuery(function($) {

    $('ul.booking-status').on('click', 'a.dokan-edit-status', function(e) {
        $(this).addClass('dokan-hide').closest('li').next('li').removeClass('dokan-hide');

        return false;
    });

    $('ul.booking-status').on('click', 'a.dokan-cancel-status', function(e) {
        $(this).closest('li').addClass('dokan-hide').prev('li').find('a.dokan-edit-status').removeClass('dokan-hide');

        return false;
    });

    $('form#dokan-booking-status-form').on('submit', function(e) {
        e.preventDefault();

        var self = $(this),
            li = self.closest('li');

        li.block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });

        $.post( dokan.ajaxurl, self.serialize(), function(response) {
            li.unblock();

            if ( response.success ) {
                var prev_li = li.prev();

                li.addClass('dokan-hide');
                prev_li.find('label').replaceWith(response.data);
                prev_li.find('a.dokan-edit-status').removeClass('dokan-hide');
            } else {
                console.log( response );
                alert( response.data );
            }
        });
    });

});


