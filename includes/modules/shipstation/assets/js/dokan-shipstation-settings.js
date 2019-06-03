( function( $ ) {
    $( '#dokan-shipstation-settings-form' ).on( 'submit', function ( e ) {
        e.preventDefault();

        var form = $( this ),
            form_data = form.serialize() + '&action=dokan_shipstation_settings';

        $.post( dokan.ajaxurl, form_data, function( response ) {
            if ( ! response.success ) {
                alert( response.data );
                return;
            }
        } );
    } );
} )( jQuery );
