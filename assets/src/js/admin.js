;(function($){

    var Dokan_Admin = {

        init: function() {
            $('.dokan-modules').on( 'change', 'input.dokan-toggle-module', this.toggleModule );
        },

        toggleModule: function(e) {
            var self = $(this);

            if ( self.is(':checked') ) {
                // Enabled
                var mesg = dokan_admin.activating,
                    data = {
                        action: 'dokan-toggle-module',
                        type: 'activate',
                        module: self.closest( 'li' ).data( 'module' ),
                        nonce: dokan_admin.nonce
                    };
            } else {
                // Disbaled
                var mesg = dokan_admin.deactivating,
                    data = {
                        action: 'dokan-toggle-module',
                        type: 'deactivate',
                        module: self.closest( 'li' ).data( 'module' ),
                        nonce: dokan_admin.nonce
                    };
            }

            self.closest('.plugin-card').block({
                message: mesg,
                overlayCSS: { background: '#222', opacity: 0.7 },
                css: {
                    fontSize: '19px',
                    color:      '#fff',
                    border:     'none',
                    backgroundColor:'none',
                    cursor:     'wait'
                },
            });

            wp.ajax.send( 'dokan-toggle-module', {
                data: data,
                success: function(response) {

                },

                error: function(error) {
                    if ( error.error === 'plugin-exists' ) {
                        wp.ajax.send( 'dokan-toggle-module', {
                            data: data
                        });
                    }
                },

                complete: function(resp) {
                    $('.blockMsg').text(resp.data);
                    setTimeout( function() {
                        self.closest('.plugin-card').unblock();
                    }, 1000)
                }
            });
        }
    };

    $(document).ready(function(){
        Dokan_Admin.init();
    });
})(jQuery);