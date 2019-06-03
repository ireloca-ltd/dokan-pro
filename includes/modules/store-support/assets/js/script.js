
//dokan store support form submit
(function($){

    var wrapper = $( '.dokan-store-tabs' );

    var support_btn = $( '.dokan-store-support-btn' );
    var custom_support_btn = support_btn.html();
    //console.log(custom_support_btn);
    var Dokan_Store_Support = {

        init : function() {
            if ( !support_btn.length ) {
               // return;
            }
            $('.dokan-store-support-btn').click( this.popUp.show );
            $('body').on( 'submit', '#dokan-support-login', this.popUp.submitLogin );
            $('body').on( 'submit', '#dokan-support-form', this.popUp.submitSupportMsg );
        },
        popUp : {
            show : function(e){
                e.preventDefault();
                support_btn.html( wait_string.wait );
                if (support_btn.hasClass('user_logged_out')){
                    Dokan_Store_Support.popUp.getForm( 'login_form' );
                } else {
                    Dokan_Store_Support.popUp.getForm( 'get_support_form' );
                }
            },
            getForm : function( data ){

                var s_data = {
                    action: 'dokan_support_ajax_handler',
                    data: data,
                    store_id : support_btn.data( 'store_id' )
                };
                $.post( dokan.ajaxurl, s_data, function ( resp ) {
                    if ( resp.success == true ) {
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup dokan-support-login-wrapper"><div id="ds-error-msg" ></div>' + resp.data + '</div>',
                                type: 'inline'
                           }
                        });
                        support_btn.html(custom_support_btn);
                    } else {
                        alert('failed');
                        support_btn.html(custom_support_btn);
                    }
                } )
            },
            submitLogin : function(e){
                e.preventDefault();
                var self = $(this);
                var s_data = {
                    action : 'dokan_support_ajax_handler',
                    data : 'login_data_submit',
                    form_data : self.serialize(),
                };
                var $e_msg = $('#ds-error-msg');
                $e_msg.addClass('dokan-hide');
                $.post( dokan.ajaxurl, s_data, function ( resp ) {
                    if ( resp.success == true ) {
                        $.magnificPopup.close();
                        Dokan_Store_Support.popUp.getForm( 'get_support_form' );
                        support_btn.html(custom_support_btn);
                    }else if (resp.success == false){
                        $e_msg.removeClass('dokan-hide');
                        $e_msg.html(resp.msg);
                        $e_msg.addClass('dokan-alert dokan-alert-danger');
                    }
                    else {
                        alert('failed');
                        support_btn.html(custom_support_btn);
                    }
                } )
            },
            submitSupportMsg : function(e){
                e.preventDefault();
                //prevent multiple submission
                $( '#support-submit-btn' ).prop('disabled', true);

                var self = $(this);
                var s_data = {
                    action : 'dokan_support_ajax_handler',
                    data : 'support_msg_submit',
                    form_data : self.serialize(),
                };
                var $e_msg = $('#ds-error-msg');

                $.post( dokan.ajaxurl, s_data, function ( resp ) {
                    if ( resp.success == true ) {
                        self.trigger( 'reset' );
                        $.magnificPopup.close();
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup dokan-support-login-wrapper dokan-alert dokan-alert-success">' + resp.msg + '</div>',
                                type: 'inline'
                           }
                        });

                    } else if ( resp.success == false ) {
                        $e_msg.removeClass('dokan-hide');
                        $e_msg.html(resp.msg);
                        $e_msg.addClass('dokan-alert dokan-alert-danger');
                    }
                    else {
                        alert('failed');
                        $( '#support-submit-btn' ).prop('disabled', false );
                    }
                } )
            }
        },
    };

    $(function() {
        Dokan_Store_Support.init();
    });

})(jQuery);

//dokan support comments
(function($){

    var wrapper = $( '.dokan-support-topic-wrapper' );
    var Dokan_support_comment = {

        init : function() {
            $('body').on( 'submit', '#dokan-support-commentform', this.submitComment );
        },

        submitComment : function(e){
            e.preventDefault();
            var self = $(this);
            var s_data = {
                action : 'dokan_support_ajax_handler',
                data : 'support_msg_submit',
                form_data : self.serialize(),
            };

            if( $('#comment').val() === '' ){
                alert('Comment box is empty');
                return;
            }


            var formurl = self.attr('action');

            $('.dokan-support-topic-wrapper').block({ message: null, overlayCSS: { background: '#fff url(' + dokan.ajax_loader + ') no-repeat center', opacity: 0.6 } });
            $.post( formurl, s_data.form_data, function ( resp ) {

                if(resp){
                    $('.dokan-support-topic-wrapper').unblock();
                    $('.dokan-support-topic-wrapper').html($(resp).find('.dokan-support-topic-wrapper').html());
                }
            } )
        }

    };

    $(function() {
        Dokan_support_comment.init();
    });

})(jQuery);

//dokan support settings
(function($){

    var Dokan_support_settings = {

        init : function() {
            $('body').on( 'change', '#support_checkbox', this.toggle_name_input );
        },
        toggle_name_input : function() {
            if ( $( '#support_checkbox' ).is( ':checked' ) ) {
                $( '.support-enable-check' ).show();
            } else {
                $( '.support-enable-check' ).hide();
            }
        }
    };

    $(function() {
        Dokan_support_settings.init();
        Dokan_support_settings.toggle_name_input();
    });

})(jQuery);
