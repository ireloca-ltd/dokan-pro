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