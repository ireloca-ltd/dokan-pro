<?php

/**
 * Dokan Store Social Share class
 *
 * Integrates Social sharing buttons inside store page
 * Settings for single stores
 *
 * @since 2.6.6
 */

class Dokan_Pro_Store_Share {
    
    private $share_text;
    /**
     * Loads automatically when class initiate
     *
     * @uses actions hook
     * @uses filter hook
     */
    public function __construct() {
        $this->init_hooks();
        $this->share_text = apply_filters( 'dokan_share_text', __( 'Share', 'dokan' ) );
    }

    /**
     * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return object self
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Store_Share();
        }

        return $instance;
    }

    /**
     * Init hooks and filters
     *
     * @return void
     */
    function init_hooks() {
        //register scripts
        add_action( 'dokan_register_scripts', array( $this, 'register_scripts' ), 30 );
        //render 
        add_action( 'dokan_enqueue_scripts', array( $this, 'enqueue_scripts' ), 30 );
//        add_action( 'woocommerce_after_main_content', array( $this, 'render_html' ), 30 );
        add_action( 'dokan_after_store_tabs', array( $this, 'render_share_button' ), 1 );
        add_action( 'wp_footer', array( $this, 'render_script' ), 30 );
    }
    
    /**
    * Register all scripts
    *
    * @return void
    **/
    function register_scripts() {
        // register styles
        wp_register_style( 'dokan-social-style', DOKAN_PRO_PLUGIN_ASSEST . '/css/jssocials.css', false, time(), 'all' );
        wp_register_style( 'dokan-social-theme-minimal', DOKAN_PRO_PLUGIN_ASSEST . '/css/jssocials-theme-minima.css', false, time(), 'all' );
        wp_register_style( 'dokan-social-theme-flat', DOKAN_PRO_PLUGIN_ASSEST . '/css/jssocials-theme-flat.css', false, time(), 'all' );

        // register scripts
        wp_register_script( 'dokan-social-script', DOKAN_PRO_PLUGIN_ASSEST . '/js/jssocials.min.js', array( 'jquery', 'dokan-script' ), null, true );
    }
    
    /**
    * Enqueue all scripts
    *
    * @return void
    **/
    function enqueue_scripts() {
        if ( dokan_is_store_page() ) {
            wp_enqueue_script( 'dokan-social-script' );
            wp_enqueue_style( 'dokan-social-style' );
            wp_enqueue_style( 'dokan-magnific-popup' );
            wp_enqueue_script( 'dokan-popup' );
            wp_enqueue_style( 'dokan-social-theme-minimal' );
        }
        
        if ( is_account_page() ) {
            wp_enqueue_style( 'dokan-social-style' );
            wp_enqueue_style( 'dokan-social-theme-flat' );
        }
    }
    
    /**
     * Render Share Buttons HTML
     * 
     * @return string 
     */
    function render_html() {
        ob_start();
        ?>
        <div class="dokan-share-wrap">
            <?php echo $this->share_text; ?>
            <div class="dokan-share">
                
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render Share pop up button
     * 
     * @return void
     */
    function render_share_button(){
        ?>
        <li class="dokan-share-btn-wrap dokan-right">
            <button class="dokan-share-btn dokan-btn dokan-btn-theme dokan-btn-sm"><?php echo esc_html( $this->share_text ); ?>  <i class="fa fa-external-link"></i></button>
        </li>
        <?php
    }
    
    /**
     * Render JS
     * 
     * @return void
     */
    function render_script(){
        if ( !dokan_is_store_page() ) {
            return;
        }
        ?>
        <script>
            (function($){
                
                var Dokan_share = {
                    
                    init : function(){
                        this.init_share();
                        $('.dokan-share-btn').click( this.showPopup );
                    },
                    
                    init_share : function(){
                        $(".dokan-share").jsSocials({
                        showCount: false,
                        showLabel: false,
                           shares: ["facebook", "twitter", "googleplus", "linkedin", "pinterest", "email"]
                        });
                    },
                    
                    showPopup : function(){
                        var content = <?php echo json_encode( $this->render_html() ) ?>;
                        $.magnificPopup.open({
                            items: {
                                src: '<div class="white-popup dokan-share-popup-wrapper"><div id="ds-error-msg" ></div>'+ content +'</div>',
                                type: 'inline'
                           }
                        });
                        
                        Dokan_share.init_share();
                    }
                }
                $(function() {
                    Dokan_share.init();
                });
            })(jQuery);
        </script>
        <?php
    }

}

$dokan_social = Dokan_Pro_Store_Share::init();