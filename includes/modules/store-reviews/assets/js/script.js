( function ( $ ) {

    var wrapper = $( '.dokan-review-wrapper' );
    var ajax_action = 'dokan_store_rating_ajax_handler';
    var Dokan_Store_Rating = {
        init: function () {
            wrapper.on( 'click', 'button.add-review-btn', this.popUp.show );
            wrapper.on( 'click', 'button.edit-review-btn', this.popUp.showEdit );
            $( 'body' ).on( 'submit', '#dokan-add-review-form', this.popUp.submitReview );
        },
        popUp: {
            show: function ( e ) {

                var s_data = {
                    action: ajax_action,
                    data: 'review_form',
                    store_id: $( 'button.add-review-btn' ).data( 'store_id' )
                };
                $.post( dokan.ajaxurl, s_data, function ( resp ) {
                    if ( resp.success == true ) {
                        $.magnificPopup.open( {
                            items: {
                                src: '<div class="white-popup dokan-seller-rating-add-wrapper"><div id="ds-error-msg" ></div>' + resp.data + '</div>',
                                type: 'inline'
                            }
                        } );

                    } else {
                        alert( 'failed' );
                    }
                } );
            },
            showEdit: function ( e ) {
                
                var s_data = {
                    action: ajax_action,
                    data: 'edit_review_form',
                    store_id: $( 'button.edit-review-btn' ).data( 'store_id' ),
                    post_id:  $( 'button.edit-review-btn' ).data( 'post_id' ),
                };
                $.post( dokan.ajaxurl, s_data, function ( resp ) {
                    if ( resp.success == true ) {
                        $.magnificPopup.open( {
                            items: {
                                src: '<div class="white-popup dokan-seller-rating-add-wrapper"><div id="ds-error-msg" ></div>' + resp.data + '</div>',
                                type: 'inline'
                            }
                        } );

                    } else {
                        alert( 'failed' );
                    }
                } );
            },
            submitReview: function ( e ) {
                e.preventDefault();

                var self = $( this );
                var s_data = {
                    action: ajax_action,
                    data: 'submit_review',
                    store_id: $( 'button.add-review-btn' ).data( 'store_id' ),
                    rating: $( "#dokan-seller-rating" ).rateYo( 'rating' ),
                    form_data: self.serialize()
                };

                var $e_msg = $( '#ds-error-msg' );

                $.post( dokan.ajaxurl, s_data, function ( resp ) {
                    if ( resp.success == true ) {
                        $.magnificPopup.close();
                        $.magnificPopup.open( {
                            items: {
                                src: '<div class="white-popup dokan-seller-rating-add-wrapper dokan-alert dokan-alert-success">' + resp.msg + '</div>',
                                type: 'inline'
                            }
                        } );
                        
                        location.reload();

                    } else if ( resp.success == false ) {
                        $e_msg.removeClass( 'dokan-hide' );
                        $e_msg.html( resp.msg );
                        $e_msg.addClass( 'dokan-alert dokan-alert-danger' );
                    }
                    else {
                        alert( 'failed' );
                    }
                } );
            }
        }
    };


    $( function () {
        Dokan_Store_Rating.init();
    } );

} )( jQuery );
