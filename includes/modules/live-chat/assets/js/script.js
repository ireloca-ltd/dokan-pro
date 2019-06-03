(function($){
    var wrapper = $( '.dokan-store-tabs' ),
        login_btn = $( '.dokan-live-chat-login' ),
        custom_login_btn = login_btn.html();

    var Dokan_Live_Chat = {

        init : function() {
            $('.dokan-live-chat-login').click( this.popUp.show );
            $('body').on( 'submit', '#dokan-chat-login', this.popUp.submitLogin );
            // $('body').on( 'submit', '#dokan-support-form', this.popUp.submitSupportMsg );
        },
        popUp : {
            show : function(e){
                e.preventDefault();
                login_btn.html( dokan_live_chat.wait );
                Dokan_Live_Chat.popUp.getForm( 'login_form' );
            },
            getForm : function( data ){

                var s_data = {
                    action: 'dokan_live_chat_login',
                    data: data,
                    store_id : login_btn.data( 'store_id' )
                };

                $.post( dokan.ajaxurl, s_data, function ( resp ) {
                    if ( resp.success == true ) {
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup dokan-support-login-wrapper"><div id="ds-error-msg" ></div>' + resp.data + '</div>',
                                type: 'inline'
                           }
                        });
                        login_btn.html(custom_login_btn);
                    } else {
                        alert('failed');
                        login_btn.html(custom_login_btn);
                    }
                } )
            },

            submitLogin : function(e){
                e.preventDefault();
                console.log('yes');
                var self = $(this);
                var s_data = {
                    action : 'dokan_live_chat_login',
                    data : 'login_data_submit',
                    form_data : self.serialize(),
                };

                var $e_msg = $('#ds-error-msg');
                $e_msg.addClass('dokan-hide');
                $.post( dokan.ajaxurl, s_data, function ( resp ) {
                    if ( resp.success == true ) {
                        $.magnificPopup.close();
                        location.reload()
                    }
                    else {
                        alert('failed');
                        login_btn.html(custom_login_btn);
                    }
                } )
            },
        },
    };

    $(function() {
        Dokan_Live_Chat.init();
    });
})(jQuery);
