(function($) {
    function follow_store( button, vendor_id, _wpnonce ) {
        button.toggleClass( 'dokan-follow-store-button-working' );

        $.ajax( {
            url: dokan.ajaxurl,
            method: 'post',
            dataType: 'json',
            data: {
                action: 'dokan_follow_store_toggle_status',
                _nonce: _wpnonce || dokanFollowStore._nonce,
                vendor_id: vendor_id
            }

        } ).fail( function ( e ) {
            var response = e.responseJSON.data.pop();

            alert(response.message);

        } ).always( function () {
            button.toggleClass( 'dokan-follow-store-button-working' );

        } ).done( function ( response ) {
            if ( response.data && response.data.status ) {
                if ( response.data.status === 'following' ) {
                    button
                        .attr( 'data-status', 'following' )
                        .children( '.dokan-follow-store-button-label-current' )
                        .html( dokanFollowStore.button_labels.following );
                } else {
                    button
                        .attr( 'data-status', '' )
                        .children( '.dokan-follow-store-button-label-current' )
                        .html( dokanFollowStore.button_labels.follow );
                }
            }

            $( 'body' ).trigger( 'dokan:follow_store:changed_follow_status', {
                vendor_id: vendor_id,
                button: button,
                status: response.data.status
            } );
        } );
    }

    function get_current_status( vendor_id ) {
        $.ajax( {
            url: dokan.ajaxurl,
            method: 'get',
            dataType: 'json',
            data: {
                action: 'dokan_follow_store_get_current_status',
                vendor_id: vendor_id
            }
        } ).done( function ( response ) {
            $( 'body' ).trigger( 'dokan:follow_store:current_status', {
                vendor_id: vendor_id,
                is_following: response.data.is_following,
                nonce: response.data.nonce
            } );
        } );
    }

    $( '.dokan-follow-store-button', 'body' ).on( 'click', function (e) {
        e.preventDefault();

        var button = $( this ),
            vendor_id = parseInt( button.data( 'vendor-id' ) ),
            is_logged_in = parseInt( button.data('is-logged-in') );

        if ( ! is_logged_in ) {
            $('body').on( 'dokan:login_form_popup:fetching_form dokan:login_form_popup:fetched_form', function () {
                button.toggleClass( 'dokan-follow-store-button-working' );
            } );

            $('body').on( 'dokan:login_form_popup:logged_in', function () {
                get_current_status( vendor_id );
            } );

            $('body').on( 'dokan:follow_store:current_status', function ( e, data ) {
                if ( ! data.is_following ) {
                    follow_store( button, vendor_id, data.nonce );
                } else {
                    window.location.href = window.location.href;
                }
            } );

            $('body').on( 'dokan:follow_store:changed_follow_status', function () {
                window.location.href = window.location.href;
            } );

            $('body').trigger('dokan:login_form_popup:show');

            return;
        }

        follow_store( button, vendor_id );
    } );
})(jQuery);
