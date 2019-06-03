//dokan store seo form submit
(function($){

    var wrapper = $( '.dokan-store-seo-wrapper' );
    var Dokan_Store_SEO = {

        init : function() {
            wrapper.on( 'submit', 'form#dokan-store-seo-form', this.form.validate );
        },

        form : {
            validate : function(e){
                e.preventDefault();

                var self = $( this ),
                data = {
                    action: 'dokan_seo_form_handler',
                    data: self.serialize(),
                };

                Dokan_Store_SEO.form.submit( data );

                return false;
            },

            submit : function( data ){
                var feedback = $('#dokan-seo-feedback');
                feedback.fadeOut();

                $.post( dokan.ajaxurl, data, function ( resp ) {
                    if ( resp.success ) {
                        feedback.html(resp.data);
                        feedback.removeClass('dokan-hide');
                        feedback.addClass('dokan-alert-success');
                        feedback.fadeIn();
                    } else {
                        feedback.html(resp.data);
                        feedback.addClass('dokan-alert-danger');
                        feedback.removeClass('dokan-hide');
                        feedback.fadeIn();
                    }
                } )
            }
        },
    };

    $(function() {
        Dokan_Store_SEO.init();
    });

})(jQuery);
