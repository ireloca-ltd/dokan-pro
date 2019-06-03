<?php
/**
 * No cheating please
 */
if ( ! defined( 'WPINC' ) ) exit;

require_once MOIP_LIB . '/vendor/autoload.php';

use  Moip\Moip ;
use  Moip\Auth\Connect ;
use  Moip\Auth\BasicAuth;
use  Moip\Auth\OAuth;

/**
 * Dokan Moip Withdraw Class
 */
class Dokan_Moip_Withdraw {
    /**
     * Hold moip settings
     * @var array
     */
    protected $settings;

    /**
     * Hold app name
     * @var string
     */
    protected $app_id;

    /**
     * Hold app secret
     * @var string
     */
    protected $secret;

    /**
     * Hold access token
     * @var string
     */
    protected $access_token;

    /**
     * Construcotr method
     */
    public function __construct() {
        $this->app_id       = get_option( 'moip_app_id' );
        $this->secret       = get_option( 'moip_secret' );
        $this->access_token = get_option( 'moip_access_token' );
        $this->settings     = get_option('woocommerce_dokan-moip-connect_settings');
        $this->init_hooks();
    }

    /**
     * Init all the hooks
     *
     * @return void
     */
    public function init_hooks() {
        $this->init_filters();
        $this->init_actions();
    }

    /**
     * Init all the action hooks
     *
     * @return void
     */
    public function init_actions() {
        add_action( 'template_redirect', array( $this, 'moip_connect' ) );
        add_action( 'template_redirect', array( $this, 'delete_moip_account' ) );
    }

    /**
     * Init all the filter hooks
     *
     * @return void
     */
    public function init_filters() {
        add_filter( 'dokan_withdraw_methods', array( $this, 'register_withdraw_method' ) );
    }

    /**
     * Connect dokan vendor to moip
     *
     * @return void
     */
    public function moip_connect() {
        $redirect_uri = dokan_get_navigation_url( 'settings/payment' ) . '?moip=yes';

        if ( ! isset( $_GET['moip'] ) || $_GET['moip'] !== 'yes' ) {
            return;
        }

        if ( ! isset( $_GET['code'] ) || empty( $_GET['code'] ) ) {
            return;
        }

        if ( isset( $this->settings['testmode'] ) && $this->settings['testmode'] == 'yes' ) {
            $connect = new Connect( $redirect_uri, $this->app_id, true, Connect::ENDPOINT_SANDBOX );
        } else {
            $connect = new Connect( $redirect_uri, $this->app_id, true, Connect::ENDPOINT_PRODUCTION );
        }

        $connect->setClientSecret( $this->secret );
        $connect->setCode($_GET['code']);

        /*
         * After the user authorize your app, you must generate an OAuth token
         * to make transactions in his name.
         */
        $authorize = $connect->authorize();

        $vendor_moip_token = $authorize->access_token;
        $vendor_moip_account = $authorize->moipAccount->id;

        update_user_meta( get_current_user_id(), 'vendor_moip_token', $vendor_moip_token );
        update_user_meta( get_current_user_id(), 'vendor_moip_account', $vendor_moip_account );
    }

    /**
     * Register moip withdraw method
     *
     * @param  array $methods
     *
     * @return array
     */
    public function register_withdraw_method( $methods ) {

        if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] != 'yes' ) {
            return $methods;
        }

        $methods['dokan-moip-connect'] = array(
            'title'    => __( 'Wirecard', 'dokan' ),
            'callback' => array( $this, 'moip_authorize_button' )
        );

        return $methods;
    }

    /**
     * This enables dokan vendors to connect their moip account to the site moip gateway account
     *
     * @param array $store_settings
     *
     * @return void
     */
    public function moip_authorize_button( $store_settings ) {
        $store_user = wp_get_current_user();
        $redirect_uri = dokan_get_navigation_url( 'settings/payment' ) . '?moip=yes';

        if ( ! $this->settings ) {
            _e( 'Moip gateway is not configured. Please contact admin.', 'dokan' );
            return;
        }

        $vendor_moip_account = get_user_meta( $store_user->ID, 'vendor_moip_account', true );
        ?>

        <style type="text/css" media="screen">
            .dokan-stripe-connect-container {
                border: 1px solid #eee;
                padding: 15px;
            }

            .dokan-stripe-connect-container .dokan-alert {
                margin-bottom: 0;
            }
        </style>

        <div class="dokan-stripe-connect-container">
            <input type="hidden" name="settings[moip]" value="<?php echo empty( $vendor_moip_account ) ? 0 : 1; ?>">
            <?php
                if ( empty( $vendor_moip_account ) ) {

                    echo '<div class="dokan-alert dokan-alert-danger">';
                        _e( 'Your account is not yet connected with Wirecard. Connect with Wirecard to receive your commissions.', 'dokan' );
                    echo '</div>';

                    $token  = $this->settings['testmode'] == 'yes' ? $this->settings['test_token'] : $this->settings['production_token'];
                    $key    = $this->settings['testmode'] == 'yes' ? $this->settings['test_key'] : $this->settings['production_key'];

                    if ( $this->settings['testmode'] == 'yes' ) {
                        $moip = new Moip( new BasicAuth( $token, $key ), Moip::ENDPOINT_SANDBOX );
                    } else {
                        $moip = new Moip( new BasicAuth( $token, $key ), Moip::ENDPOINT_PRODUCTION );
                    }

                    // Now it's time to create a URL then redirect your user to ask him permissions to create projects in his name
                    if ( $this->settings['testmode'] == 'yes' ) {
                        $connect = new Connect( $redirect_uri, $this->app_id, true, Connect::ENDPOINT_SANDBOX );
                    } else {
                        $connect = new Connect( $redirect_uri, $this->app_id, true, Connect::ENDPOINT_PRODUCTION );
                    }

                    $connect->setScope(Connect::RECEIVE_FUNDS)
                        ->setScope(Connect::REFUND)
                        ->setScope(Connect::MANAGE_ACCOUNT_INFO)
                        ->setScope(Connect::RETRIEVE_FINANCIAL_INFO);

                       $url = $connect->getAuthUrl();

                    ?>
                    <br/>
                    <a class="clear dokan-btn dokan-btn-theme" href="<?php echo $url; ?>" target="_TOP">
                        <?php _e( 'Connect With Wirecard', 'dokan' ); ?>
                    </a>
                    <?php
                } else {
                    ?>
                    <div class="dokan-alert dokan-alert-success">
                        <?php _e( 'Your account is connected with Wirecard.', 'dokan' ); ?>
                        <a  class="dokan-btn dokan-btn-danger dokan-btn-theme" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'dokan-disconnect-moip' ), dokan_get_navigation_url( 'settings/payment' ) ), 'dokan-disconnect-moip' ); ?>"><?php _e( 'Disconnect', 'dokan' ); ?></a>
                    </div>
                    <?php
                }
            ?>
        </div>
        <?php
    }

    /**
     * Delect vendor moip account
     *
     * @return void
     */
    public function delete_moip_account() {

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! isset( $_GET['action'] ) || $_GET['action'] !== 'dokan-disconnect-moip' ) {
            return;
        }

        if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'dokan-disconnect-moip' ) ) {
            return;
        }

        $user_id = get_current_user_id();

        if ( ! dokan_is_user_seller( $user_id ) ) {
            return;
        }

        delete_user_meta( $user_id, 'vendor_moip_token' );
        delete_user_meta( $user_id, 'vendor_moip_account' );

        wp_redirect( dokan_get_navigation_url( 'settings/payment' ) );
        exit;
    }

    /**
     * Get single instance of this class
     *
     * @return object
     */
    public static function init() {
        $instance = false;

        if ( ! $instance ) {
            return $instance = new static;
        }
    }
}

Dokan_Moip_Withdraw::init();
