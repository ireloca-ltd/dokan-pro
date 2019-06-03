<?php
/**
 * Dokan_Live_Chat_Seller_Settings Class
 */
class Dokan_Live_Chat_Seller_Settings {

    /**
     * Hold class instance
     *
     * @var object
     */
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
        if ( ! self::$instance ) {
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
        add_action( 'dokan_settings_form_bottom', array( $this, 'dokan_live_chat_seller_settings' ), 15, 2 );
        add_action( 'dokan_store_profile_saved', array( $this, 'dokan_live_chat_save_seller_settings' ), 15 );
    }

    /**
     * Register live caht seller settings on seller dashboard
     *
     * @param  int $user_id
     *
     * @param  object $profile
     *
     * @since 1.0
     *
     * @return object
     */
    public function dokan_live_chat_seller_settings( $user_id, $profile ) {
        if ( dokan_get_option( 'enable', 'dokan_live_chat' ) !== 'on' ) {
            return;
        }

        $enable_chat = isset( $profile['live_chat'] ) ? $profile['live_chat'] : 'no';
        ?>
        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label"><?php _e( 'Enable Live Chat' , 'dokan' ) ?></label>
            <div class="dokan-w5 dokan-text-left">
                <div class="checkbox">
                    <label>
                        <input type="hidden" name="live_chat" value="no">
                        <input type="checkbox" id="live_chat" name="live_chat" value="yes" <?php checked( $enable_chat, 'yes' ); ?>><?php  _e( 'Enable Live Chat', 'dokan'); ?>
                    </label>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Save dokan live chat seller settings
     *
     * @param  string $user_id
     *
     * @return void
     */
    public function dokan_live_chat_save_seller_settings( $user_id ) {
        if ( ! isset( $_POST['live_chat'] ) ) {
            return;
        }

        $store_info = dokan_get_store_info( $user_id );
        $store_info['live_chat'] = wc_clean( $_POST['live_chat'] );

        update_user_meta( $user_id, 'dokan_profile_settings', $store_info );
    }
}

Dokan_Live_Chat_Seller_Settings::init();
