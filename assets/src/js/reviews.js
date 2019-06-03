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