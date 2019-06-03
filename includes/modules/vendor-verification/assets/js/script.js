;
( function ( $ ) {
        
    var feedback = $('#feedback');
    var phoneFeedback = $('#d_v_phone_feedback');
    // country to state select generate
    var dokan_address_wrapper = $( '.dokan-address-fields' );
                    var dokan_address_select = {
                        init: function () {
                            
                            dokan_address_wrapper.on( 'change', 'select.country_to_state', this.state_select );
                        },
                        state_select: function () {
                            var states_json = wc_country_select_params.countries.replace( /&quot;/g, '"' ),
                                states = $.parseJSON( states_json ),
                                $statebox = $( '#dokan_address_state' ),
                                input_name = $statebox.attr( 'name' ),
                                input_id = $statebox.attr( 'id' ),
                                input_class = $statebox.attr( 'class' ),
                                value = $statebox.val(),
                                selected_state = $( '#dokan_selected_state' ).val(),
                                input_selected_state = $( '#dokan_selected_state' ).val(),
                                country = $( this ).val();

                            if ( states[ country ] ) {
                                
                                if ( $.isEmptyObject( states[ country ] ) ) {

                                    $( 'div#dokan-states-box' ).slideUp( 2 );
                                    if ( $statebox.is( 'select' ) ) {
                                        $( 'select#dokan_address_state' ).replaceWith( '<input type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required />' );
                                    }
                                    
                                    $( '#dokan_address_state' ).val( 'N/A' );
                                    
                                } else {
                                    input_selected_state = '';
                                    
                                    var options = '',
                                        state = states[ country ];

                                    for ( var index in state ) {
                                        if ( state.hasOwnProperty( index ) ) {
                                            if ( selected_state ) {
                                                if ( selected_state == index ) {
                                                    var selected_value = 'selected="selected"';
                                                } else {
                                                    var selected_value = '';
                                                }
                                            }
                                            options = options + '<option value="' + index + '"' + selected_value + '>' + state[ index ] + '</option>';
                                        }
                                    }

                                    if ( $statebox.is( 'select' ) ) {
                                        $( 'select#dokan_address_state' ).html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );
                                    }
                                    if ( $statebox.is( 'input' ) ) {
                                        $( 'input#dokan_address_state' ).replaceWith( '<select type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required ></select>' );
                                        $( 'select#dokan_address_state' ).html( '<option value="">' + wc_country_select_params.i18n_select_state_text + '</option>' + options );
                                    }
                                    $( '#dokan_address_state' ).removeClass( 'dokan-hide' );
                                    $( 'div#dokan-states-box' ).slideDown();
                                    
                                }
                            } else {
                                
                                
                                if ( $statebox.is( 'select' ) ) {
                                    input_selected_state = '';
                                    $( 'select#dokan_address_state' ).replaceWith( '<input type="text" class="' + input_class + '" name="' + input_name + '" id="' + input_id + '" required="required"/>' );
                                }
                                $( '#dokan_address_state' ).val(input_selected_state);
                                
                                if ( $( '#dokan_address_state' ).val() == 'N/A' ){
                                    $( '#dokan_address_state' ).val('');
                                }
                                $( '#dokan_address_state' ).removeClass( 'dokan-hide' );
                                $( 'div#dokan-states-box' ).slideDown();
                            }
                        }
                    }

    $( document ).ready( function () {
        
        dokan_address_select.init();
    
    //ID verification
        // show verification panel on start click
        $( 'button#dokan_v_id_click' ).click( function () {
            $(this).slideUp( 'fast',function(){
                $( '.dokan_v_id_info_box' ).slideDown('fast');
            });
        } );
        
        // close verification panel on cancel click
        $( 'input#dokan_v_id_cancel_form' ).click( function () {
            $( '.dokan_v_id_info_box' ).slideUp( 'fast',function(){
                $( 'button#dokan_v_id_click' ).slideDown('fast');
            });
        } );
        
        // submit ID verification request
        $( '.dokan-verification-content' ).on( 'click', 'input#dokan_v_id_submit', function ( e ) {
            e.preventDefault();

            if ( $( "input[name='dokan_gravatar']" ).val() == 0 ) {
                alert( 'Upload a Photo' );
                return;
            }

            var self = $( this ),
                data = {
                    action: 'dokan_update_verify_info',
                    data: self.closest( '#dokan-verify-id-form' ).serialize(),
                };

            feedback.fadeOut();

            $.post( dokan.ajaxurl, data, function ( resp ) {
                if ( resp.success == true ) {
                    $( '#dokan_v_id_feedback' ).addClass( 'hidden' );
                    feedback.addClass( 'dokan-alert dokan-alert-success' );
                    feedback.html( resp.data );
                    feedback.fadeIn();
                    $( 'div.dokan_v_id_info_box' ).hide();
                    $( 'button#dokan_v_id_cancel' ).show();                    
                } else {
                    feedback.addClass( 'dokan-alert dokan-alert-danger' );
                    feedback.html( 'failed' );
                    feedback.fadeIn();
                }
            } )
        } );
        
        // cancel Verification ID request
        $( 'button#dokan_v_id_cancel' ).click( function () {
            data = {
                action: 'dokan_id_verification_cancel',
                data: 'cancel',
            };

            feedback.fadeOut();
            $.post( dokan.ajaxurl, data, function ( resp ) {
                if ( resp.success == true ) {
                    $( '#dokan_v_id_feedback' ).addClass( 'hidden' );
                    feedback.addClass( 'dokan-alert dokan-alert-success' );
                    feedback.html( resp.data );
                    feedback.fadeIn();
                    $( 'button#dokan_v_id_cancel' ).hide();
                    $( 'button#dokan_v_id_click' ).removeClass('dokan-hide');
                    $( 'button#dokan_v_id_click' ).show();
                                        
                } else {
                    feedback.addClass( 'dokan-alert dokan-alert-danger' );
                    feedback.html( 'failed' );
                    feedback.fadeIn();
                }
            } )
        } );
        
    //Phone verification    
        // send sms on submit
        $('.dokan_v_phone_box').on('submit', 'form#dokan-verify-phone-form', function(e) {
            e.preventDefault();

            if ( $( "input[name = 'phone']" ).val() == '' ) {
                alert( 'Insert Phone No.' );
                return;
            }

            var self = $(this),
                data  = {
                    action : 'dokan_v_send_sms',
                    data : self.serialize(),
                };

            $.post( dokan.ajaxurl, data, function( resp ) {

                if ( resp.success == true ) {
                    if(resp.data.success == true){
                      phoneFeedback.removeClass();
                      phoneFeedback.addClass('dokan-alert dokan-alert-success');
                      phoneFeedback.html(resp.data.message);

                      $( 'div.dokan_v_phone_box' ).slideUp();
                      $( 'div.dokan_v_phone_code_box' ).slideDown();
                    }else{
                      phoneFeedback.removeClass();
                      phoneFeedback.addClass('dokan-alert dokan-alert-danger');
                      phoneFeedback.html(resp.data.message);
                    }

                }else{
                    $('#feedback').addClass('dokan-alert dokan-alert-danger');
                    $('#feedback').html('failed');
                }

            })
        });
        
        // Allow Numeric values only on phone verification field
        $( '#phone' ).keydown( function ( e ) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ( $.inArray( e.keyCode, [ 46, 8, 9, 27, 13, 91, 107, 109, 110, 187, 189, 190 ] ) !== -1 ||
                // Allow: Ctrl+A
                    ( e.keyCode == 65 && e.ctrlKey === true ) ||
                    // Allow: home, end, left, right
                        ( e.keyCode >= 35 && e.keyCode <= 39 ) ) {
                    // let it happen, don't do anything
                    return;
                }

                // Ensure that it is a number and stop the keypress
                if ( ( e.shiftKey || ( e.keyCode < 48 || e.keyCode > 57 ) ) && ( e.keyCode < 96 || e.keyCode > 105 ) ) {
                    e.preventDefault();
                }
        } );
        
        // submit verification code
        $('.dokan_v_phone_code_box').on('submit', 'form#dokan-v-phone-code-form', function(e) {
            e.preventDefault();

            if ( $( "input[name = 'sms_code']" ).val() == '' ) {
                alert( 'Insert SMS code' );
                return;
            }

            var self = $(this),
                data  = {
                    action : 'dokan_v_verify_sms_code',
                    data : self.serialize(),
                };

            $.post( dokan.ajaxurl, data, function( resp ) {

                if ( resp.success == true ) {

                    if ( resp.data.success == true ) {

                        phoneFeedback.removeClass();
                        phoneFeedback.addClass('dokan-alert dokan-alert-success');
                        phoneFeedback.html(resp.data.message);
                        $('.dokan_v_phone_code_box').fadeOut();

                    } else {
                        phoneFeedback.removeClass();
                        phoneFeedback.addClass('dokan-alert dokan-alert-danger');
                        phoneFeedback.html(resp.data.message);
                    }

                } else {
                    $('#feedback').addClass('dokan-alert dokan-alert-danger');
                    $('#feedback').html('failed');
                }
            });
        });

    //Address verification    
        // show address verification panel on start click
        $( 'button#dokan_v_address_click' ).click( function () {
            $( 'button#dokan_v_address_click' ).slideUp('fast',function(){
                $( '.dokan_v_address_box' ).slideDown('fast');
            });
        } );
        
        // close address verification panel on cancel click
        $( 'input#dokan_v_address_cancel' ).click( function () {
            $( '.dokan_v_address_box' ).slideUp('fast',function(){
                $( 'button#dokan_v_address_click' ).slideDown('fast');
                var address_feedback = $('div#d_v_address_feedback');
                address_feedback.addClass('dokan-hide');
                address_feedback.html('');
            });
        } );
        
        // submit Address verification request
        $( '.dokan-verification-content' ).on( 'submit', 'form#dokan-verify-address-form', function ( e ) {
            e.preventDefault();

            var self = $( this );
           
            var address_feedback = $('div#d_v_address_feedback');
            feedback.fadeOut();
            address_feedback.addClass( 'dokan-hide' );
            
            

            $.post( dokan.ajaxurl, self.serialize(), function ( resp ) {

                if ( resp.success == true ) {
                    
                    feedback.addClass( 'dokan-alert dokan-alert-success' );
                    $( 'html,body' ).animate( { scrollTop: 100 } );
                    feedback.html( resp.data );
                    feedback.fadeIn();
                    $( 'div.dokan_v_address_box' ).slideUp( 'fast' );
                    $( 'button#dokan_v_address_cancel' ).removeClass( 'dokan-hide' );
                    $( '#dokan_v_address_cancel' ).show();
                   

                } else {
                    address_feedback.addClass( 'dokan-alert dokan-alert-danger' );
                    address_feedback.html( resp.data );
                    address_feedback.removeClass( 'dokan-hide' );
                    address_feedback.fadeIn();
                }
            } );
        } );
        
        //cancel Address verification request
        $( 'button#dokan_v_address_cancel' ).click( function () {
            var data = {
                action: 'dokan_address_verification_cancel',
                data: 'cancel',
            };

            feedback.fadeOut();

            $.post( dokan.ajaxurl, data, function ( resp ) {
                if ( resp.success == true ) {
                    $( '#dokan_v_address_feedback' ).addClass( 'dokan-hide' );
                    feedback.addClass( 'dokan-alert dokan-alert-success' );
                    feedback.html( resp.data );
                    feedback.fadeIn(); 
                    $( 'button#dokan_v_address_cancel' ).addClass('dokan-hide');
                    $( 'button#dokan_v_address_click' ).removeClass('dokan-hide');
                    $( 'button#dokan_v_address_click' ).show();
                    $('div#d_v_address_feedback').addClass('dokan-hide');
                    
                } else {
                    feedback.addClass( 'dokan-alert dokan-alert-danger' );
                    feedback.html( 'failed' );
                    feedback.fadeIn();
                }
            } )
        } );//        
    } );

} )( jQuery );
