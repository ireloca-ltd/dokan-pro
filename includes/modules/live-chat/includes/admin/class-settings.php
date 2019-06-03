<?php
/**
 * Dokan_Live_Chat_Settings Class
 */
class Dokan_Live_Chat_Settings {

    public static $instance;

    /**
     * Constructor method for this class
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Return single instance of this class
     *
     * @since 1.0
     *
     * @return object;
     */
    public static function init() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Initialize all the hooks
     *
     * @since 1.0
     *
     * @return void
     */
    public function init_hooks() {
        add_filter( 'dokan_settings_sections', array( $this, 'dokan_live_chat_sections' ), 20, 1 );
        add_filter( 'dokan_settings_fields', array( $this, 'dokan_live_chat_settings' ), 20, 1 );
    }

    /**
     * Add dokan live caht secitons in doakn admin settings
     *
     * @param  array $sections
     *
     * @since 1.0
     *
     * @return array $sections;
     */
    public function dokan_live_chat_sections( $sections ) {
        $sections[] = array(
            'id'    => 'dokan_live_chat',
            'title' => __( 'Live Chat', 'dokan' ),
            'icon'  => 'dashicons-format-chat'
        );

        return $sections;
    }

    /**
     * Register dokan live chat settings
     *
     * @param  array $settings
     *
     * @since 1.0
     *
     * @return array $settings
     */
    public function dokan_live_chat_settings( $settings ) {
        $settings['dokan_live_chat'] = array(
            'enable' => array(
                'name'=> 'enable',
                'label' => __( 'Enable Live Chat', 'dokan' ),
                'desc'  => __( 'Enable live chat between vendor and customer', 'dokan' ),
                'type'  => 'checkbox',
                'default' => 'on',
            ),
            'app_id' => array(
                'name'  => 'app_id',
                'label' => __( 'App ID', 'dokan' ),
                'desc'  => sprintf( '%s <a target="_blank" href="%s">%s</a>', __( 'Insert App ID', 'dokan' ), esc_url( 'https://talkjs.com/dashboard/signup/standard/'), __( '( Get your App ID )', 'dokan' ) ),
                'type'  => 'text',
            ),
            'app_secret' => array(
                'name'  => 'app_secret',
                'label' => __( 'App Secret', 'dokan' ),
                'desc'  => sprintf( '%s <a target="_blank" href="%s">%s</a>', __( 'Insert App Secret', 'dokan' ), esc_url( 'https://talkjs.com/dashboard/signup/standard/'), __( '( Get your App Secret )', 'dokan' ) ),
                'type'  => 'text',
            ),
            'chat_button_seller_page' => array(
                'name'  => 'chat_button_seller_page',
                'label' => __( 'Chat Button on Vendor Page', 'dokan' ),
                'desc'  => __( 'Show chat button on vendor page', 'dokan' ),
                'type'  => 'checkbox',
            ),
            'chat_button_product_page' => array(
                'name'  => 'chat_button_product_page',
                'label' => __( 'Chat Button on Product Page', 'dokan' ),
                'desc'  => __( 'Show chat button on product page', 'dokan' ),
                'type'  => 'select',
                'options' => array(
                    'above_tab' => __( 'Above Product Tab', 'dokan' ),
                    'inside_tab' => __( 'Inside Product Tab', 'dokan' ),
                    'dont_show' => __( 'Don\'t Show', 'dokan' ),
                )
            ),
        );

        return $settings;
    }

}

Dokan_Live_Chat_Settings::init();
