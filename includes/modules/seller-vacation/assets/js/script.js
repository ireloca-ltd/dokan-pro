;(function($) {

$(document).ready(function(){

    var vacation_message = $('.vacation_message'),
        datewise_area = $('.datewise_close_store'),
        go_vacation = $('.show_if_go_vacation');
        
        datewise_area.hide();
        go_vacation.hide();
        vacation_message.hide();

    $('.sotre_vacation').on('change', '#settings_closing_style', function(){
        var self = $(this);
        if( self.val() == 'datewise' ) {
            datewise_area.hide().fadeIn();
            vacation_message.fadeIn();
        } else if( self.val() == 'instantly' ) {
            datewise_area.fadeOut();
            vacation_message.fadeIn();
        } else {
            datewise_area.fadeOut();
            vacation_message.fadeOut();
        }    
    });

    $('.goto_vacation_settings').on('change', '#setting_go_vacation', function(){
        var self = $(this);
        if( self.prop('checked') ) {
            go_vacation.hide().fadeIn();
        } else {
            go_vacation.fadeOut();
        }
    })

    $('#setting_go_vacation').trigger('change');

    $('#settings_closing_style').trigger( 'change' );


    $( "#settings_close_from" ).datepicker({
        defaultDate: "+1w",
        dateFormat: 'yy-mm-dd',
        changeYear: true,
        changeMonth: true,
        numberOfMonths: 1,
        minDate: 0,
        onClose: function( selectedDate ) {
            $( "#settings_close_to" ).datepicker( "option", "minDate", selectedDate );
        }
    });
    $( "#settings_close_to" ).datepicker({
        defaultDate: "+1w",
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        numberOfMonths: 1,
        minDate: 0,
        onClose: function( selectedDate ) {
            //$( "#settings_close_from" ).datepicker( "option", "maxDate", selectedDate );
        }
    });

});

})(jQuery);