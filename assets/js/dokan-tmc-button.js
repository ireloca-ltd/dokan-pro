jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.dokan_button', {
        init : function(editor, url) {
                var menuItem = [];
                var ds_img = dokan_assets_url +'/images/D.png';
                $.each( dokan_shortcodes, function( i, val ){
                    var tempObj = {
                            text : val.title,
                            onclick: function() {
                                editor.insertContent(val.content)
                            }
                        };
                        
                    menuItem.push( tempObj );
                } );
                // Register buttons - trigger above command when clickeditor
                editor.addButton('dokan_button', {
                    title : 'Dokan shortcodes', 
                    classes : 'dokan-ss',
                    type  : 'menubutton',
//                    text  : 'Dokan',
//                    image : dokan_assets_url +'/images/D.png',
                    menu  : menuItem,
                    style : ' background-size : 18px; background-repeat : no-repeat; background-image: url( '+ ds_img +' );'
                });
        },   
    });

    // Register our TinyMCE plugin
    
    tinymce.PluginManager.add('dokan_button', tinymce.plugins.dokan_button);
});