(function($){

    $(function(){

        $('#dokan-product-enquiry').on('submit', function(e) {
            e.preventDefault();

            var self = $(this),
                data = self.serialize(),
                button = self.find('input[type=submit]');

            var message = $('textarea#dokan-enq-message').val();

            if ( $.trim(message) === '' ) {
                return;
            }

            button.attr('disabled', true);
            self.append('<i class="fa fa-refresh fa-spin"></i>');

            $.post(DokanEnquiry.ajaxurl, data, function(resp) {

                if ( typeof resp.data !== 'undefined' ) {

                    if ( resp.success === true ) {

                        $(resp.data).insertBefore(self);
                        self.find('textarea').val('');

                    } else {
                        alert(resp.data);
                    }
                }

                button.removeAttr('disabled');
                self.find('i.fa-refresh').remove();
            });

        });

    });

})(jQuery);