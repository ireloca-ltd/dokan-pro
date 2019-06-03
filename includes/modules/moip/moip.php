<?php
/*
Plugin Name: Wirecard
Plugin URI: https://wedevs.com
Description: Wirecard payment gateway for dokan
Version: 1.0
Author: weDevs, LLC
Author URI: https://wedevs.com/
Thumbnail Name: wirecard-connect.png
License: GPL2
*/

if ( ! defined( 'WPINC' ) ) exit;

/**
 * Dokan Moip Class
 */
class Dokan_Moip {
    /**
     * Hold plugin version
     * @var string
     */
    private $version;

    /**
     * Constructor method of this class
     */
    public function __construct() {
        $this->version = '1.0';
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define all the constants
     *
     * @since  1.0
     *
     * @return void;
     */
    public function define_constants() {
        define( 'MOIP_INC', dirname( __FILE__ ) . '/includes' );
        define( 'MOIP_ASSETS', plugin_dir_url( __FILE__ ) . 'assets' );
        define( 'MOIP_LIB', MOIP_INC . '/libs' );
        define( 'MOIP_TEMPLATE', dirname( __FILE__ ) . '/templates' );
    }

    /**
     * Get single instance
     *
     * @return object
     */
    public static function init() {
        $instance = false;

        if ( ! $instance ) {
            $instance = new static;
        }

        return $instance;
    }

    /**
     * Includes all the files
     *
     * @since  1.0
     *
     * @return string
     */
    public function includes() {
        require_once MOIP_INC . '/admin/class-moip-gateway.php';
        require_once MOIP_INC . '/public/class-withdraw-methods.php';
    }

    /**
     * Init all the hooks
     *
     * @return void
     */
    public function init_hooks() {
        $this->init_actions();
        $this->init_filters();
    }

    /**
     * Init all the actions
     *
     * @return void
     */
    public function init_actions() {
        add_action( 'dokan_store_profile_saved', array( $this, 'save_moip_progress' ), 10, 2 );
        add_action( 'woocommerce_after_checkout_validation', array( $this, 'check_vendor_configure_moip' ), 15, 2 );

        // admin control over vendor moip account
        add_action( 'edit_user_profile', array( $this, 'moip_admin_menu') , 50 );
        add_action( 'show_user_profile', array( $this, 'moip_admin_menu') , 50 );

        add_action( 'personal_options_update', array( $this, 'moip_admin_functions') , 50 );
        add_action( 'edit_user_profile_update', array( $this, 'moip_admin_functions') , 50 );

        //handle webhook
        add_action( 'init', array( $this, 'handle_moip_webhook') , 10 );
        // cancel recurring subscription
        add_action( 'dps_cancel_recurring_subscription', array( $this, 'cancel_recurring_subscription' ), 10, 2 );

        // retry to make payment of due invoice
        add_action( 'template_redirect', array( $this, 'retry_delayd_payment' ) );

        // register webhook
        add_action( 'template_redirect', array( $this, 'register_webhook' ) );
    }

    /**
     * Register webhook
     *
     * @return void
     */
    public function register_webhook() {
        if ( get_option( 'dokan-moip-webhook-registered' ) == 'yes' ) {
            return;
        }

        $settings   = get_option( 'woocommerce_dokan-moip-connect_settings' );

        $key        = $settings['testmode'] == 'no' ? $settings['production_key'] : $settings['test_key'];
        $token      = $settings['testmode'] == 'no' ? $settings['production_token'] : $settings['test_token'];
        $public_key = $settings['testmode'] == 'no' ? $settings['production_public_key'] : $settings['test_public_key'];

        if ( empty( $key ) || empty( $token ) || empty( $public_key ) ) {
            return;
        }

        $base_url = $settings['testmode'] == 'no' ? 'https://api.moip.com.br/assinaturas/v1/users/preferences' : 'https://sandbox.moip.com.br/assinaturas/v1/users/preferences';

        $body = array(
            'notification' => array(
                'webhook'  => array(
                    'url'  => get_site_url() . '?webhook=dokan-moip'
                )
            )
        );

        $args = array(
            'timeout'       => 45,
            'redirection'   => 5,
            'headers'       => array(
                'cache-control' => 'no-cache',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Basic ' . base64_encode( $token . ':' . $key ),
            ),
            'body'          => json_encode( $body )
        );

        $response = wp_remote_post( $base_url, $args );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( 'Error', 'Something went wrong' );
        }

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
            update_option( 'dokan-moip-webhook-registered', 'yes' );
        }
    }

    /**
     * Cancel recurring subscription pack
     *
     * @param  int $order_id
     * @param  int $user_id
     *
     * @return void
     */
    public function cancel_recurring_subscription( $order_id, $user_id ) {
        if ( ! $order_id ) {
            return;
        }

        if ( $order_id != get_user_meta( $user_id, 'product_order_id', true ) ) {
            return;
        }

        $order = wc_get_order( $order_id );

        if ( $order->get_payment_method() !== 'dokan-moip-connect' ) {
            return;
        }

        $subscription_code = get_user_meta( $user_id, 'subscription_code', true );

        require_once MOIP_INC . '/admin/class-moip-subscription.php';

        $dms = new Dokan_Moip_Subscription();
        $cancel = $dms->cancel_subscription( $user_id, $subscription_code );

        if ( ! $cancel ) {
            return;
        }

        delete_user_meta( $user_id, 'product_package_id' );
        delete_user_meta( $user_id, 'subscription_code' );
        delete_user_meta( $user_id, 'product_order_id' );
        delete_user_meta( $user_id, 'product_no_with_pack' );
        delete_user_meta( $user_id, 'product_pack_startdate' );
        delete_user_meta( $user_id, 'product_pack_enddate' );
        delete_user_meta( $user_id, 'can_post_product' );
        delete_user_meta( $user_id, '_customer_recurring_subscription' );
        delete_user_meta( $user_id, 'dokan_seller_percentage' );
    }

    /**
     * Handle moip webhook
     *
     * @return void
     */
    public function handle_moip_webhook() {
        if ( ! isset( $_GET['webhook'] ) || $_GET['webhook'] !== 'dokan-moip' ) {
            return;
        }

        $body = @file_get_contents( 'php://input' );
        $response = json_decode( $body );

        if ( ! isset( $response->env ) ) {
            return;
        }

        // if invoice created successfully
        if ( isset( $response->event ) && $response->event == 'invoice.created' ) {
            global $wpdb;

            $subscription_code = $response->resource->subscription_code;

            $user_id = $wpdb->get_var( "SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = 'subscription_code' AND `meta_value`='$subscription_code'" );

            require_once MOIP_INC . '/admin/class-moip-subscription.php';

            $dms = new Dokan_Moip_Subscription();
            $invoice = $dms->get_invoice( $response->resource->id );

            if ( in_array( $response->resource->status->code, array( 2, 3 ) ) ) {
                $product_id = $invoice->plan->code;

                $subscription_interval = get_post_meta( $product_id, '_subscription_period_interval', true );
                $subscription_period   = get_post_meta( $product_id, '_subscription_period', true );
                $subscription_length   = get_post_meta( $product_id, '_subscription_length', true );
                $add_s = ( $subscription_interval != 1 ) ? 's' : '';

                update_user_meta( $user_id, 'product_pack_startdate', date( 'Y-m-d H:i:s' ) );
                update_user_meta( $user_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', strtotime( "+" . $subscription_interval . " " . $subscription_period . "" . $add_s ) ) );
                update_user_meta( $user_id, 'can_post_product', '1' );
            } elseif ( $response->resource->status->code == 5 ) {
                // failed payment but still have chance to pay the bill using auto retry payment
                update_user_meta( $user_id, 'can_post_product', '0' );
            } else {
                // faild payment
                update_user_meta( $user_id, 'can_post_product', '0' );
                delete_user_meta( $user_id, 'product_package_id' );
                delete_user_meta( $user_id, 'subscription_code' );
                delete_user_meta( $user_id, 'product_order_id' );
                delete_user_meta( $user_id, 'product_no_with_pack' );
                delete_user_meta( $user_id, 'product_pack_startdate' );
                delete_user_meta( $user_id, 'product_pack_enddate' );
                delete_user_meta( $user_id, 'can_post_product' );
                delete_user_meta( $user_id, '_customer_recurring_subscription' );
                delete_user_meta( $user_id, 'dokan_seller_percentage' );
            }
        }

        if ( isset( $response->event ) && $response->event == 'subscription.expired' ) {
            global $wpdb;

            $subscription_code = $response->resource->code;
            $user_id           = $wpdb->get_var( "SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = 'subscription_code' AND `meta_value`='$subscription_code'" );

            // subscriptoin expired
            update_user_meta( $user_id, 'can_post_product', '0' );
            delete_user_meta( $user_id, 'product_package_id' );
            delete_user_meta( $user_id, 'subscription_code' );
            delete_user_meta( $user_id, 'product_order_id' );
            delete_user_meta( $user_id, 'product_no_with_pack' );
            delete_user_meta( $user_id, 'product_pack_startdate' );
            delete_user_meta( $user_id, 'product_pack_enddate' );
            delete_user_meta( $user_id, 'can_post_product' );
            delete_user_meta( $user_id, '_customer_recurring_subscription' );
            delete_user_meta( $user_id, 'dokan_seller_percentage' );
        }
    }

    /**
     * Retry delayd payment
     *
     * @return void
     */
    public function retry_delayd_payment() {
        require_once MOIP_INC . '/admin/class-moip-subscription.php';

        $dms = new Dokan_Moip_Subscription();
        $dms->retry_payment();
    }

    /**
     * Check if vendor has configured the moip settings
     *
     * @param  array $data
     * @param  array $errors
     *
     * @return void
     */
    public function check_vendor_configure_moip( $data, $errors ) {
        $settings = get_option('woocommerce_dokan-moip-connect_settings');

        // bailout if the gateway is not enabled
        if ( isset( $settings['enabled'] ) && $settings['enabled'] == 'yes' ) {
            if ( 'dokan-moip-connect' == $data['payment_method'] ) {
                foreach ( WC()->cart->get_cart() as $item ) {
                    $product_id = $item['data']->get_id();
                    $available_vendors[get_post_field( 'post_author', $product_id )][] = $item['data'];
                }

                $vendor_names = array();

                foreach ( array_keys( $available_vendors ) as $vendor_id ) {
                    $vendor = dokan()->vendor->get( $vendor_id );
                    $vendor_moip_account = get_user_meta( $vendor_id, 'vendor_moip_account', true );

                    if ( empty( $vendor_moip_account ) ) {
                        $vendor_products = array();

                        foreach ( $available_vendors[$vendor_id] as $product ) {
                            $vendor_products[] = sprintf( '<a href="%s">%s</a>', $product->get_permalink(), $product->get_name() );
                        }
                        $vendor_names[$vendor_id] = array(
                            'name' => sprintf( '<a href="%s">%s</a>', esc_url( $vendor->get_shop_url() ), $vendor->get_shop_name() ),
                            'products' => implode( ', ', $vendor_products )
                        );
                    }
                }

                foreach ( $vendor_names as $vendor_id => $data ) {
                    if ( user_can( $vendor_id, 'manage_options' ) ) {
                        return;
                    }

                    $errors->add( 'moip-not-configured', sprintf( __( '<strong>Error!</strong> The <strong>%s</strong> does not allowes the Moip gateway. You can not purchase this products %s using Moip Gateway', 'dokan' ), $data['name'], $data['products'] ) );
                }
            }
        }
    }

    /**
    * Save moip progress settings data
    *
    * @return void
    **/
    public function save_moip_progress( $store_id, $dokan_settings ) {
        if ( ! $store_id ) {
            return;
        }

        if ( isset( $_POST['settings']['moip'] ) ) {
            $dokan_settings['payment']['moip'] = wc_clean( $_POST['settings']['moip'] );
        }

        update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );
    }

    /**
     * Init all the filters
     *
     * @return void
     */
    public function init_filters() {
        // register the moip gateway
        add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );

        // prevent non moip vendor to add product
        add_filter( 'dokan_can_add_product', array( $this, 'can_seller_add_product' ) );

        // hide withdraw page
        add_filter( 'dokan_get_dashboard_nav', array( $this, 'remove_withdraw_page' ) );
    }

    /**
     * Register the gateway for use
     *
     * @param  array $methods
     *
     * @return array $methods
     */
    public function register_gateway( $methods ) {
        $methods[] = 'Dokan_Moip_Connect';

        return $methods;
    }

    /**
     * Prevents non-moip connected users from creating new product posts
     *
     * @param  array  $errors
     *
     * @return array
     */
    function can_seller_add_product( $errors ) {

        $payment_gateways = new WC_Payment_Gateways;
        $available_gateways = $payment_gateways->get_available_payment_gateways();
        $settings = get_option('woocommerce_dokan-moip-connect_settings');
        // bailout if the gateway is not enabled
        if ( isset( $settings['enabled'] ) && $settings['enabled'] !== 'yes' ) {
            return $errors;
        }

        $user_id   = get_current_user_id();
        $vendor_moip_account = get_user_meta( $user_id, 'vendor_moip_account', true );

        if ( empty( $vendor_moip_account ) && count( $available_gateways ) < 2 ) {
            $errors[] = sprintf( '%s <a href="%s">%s</a>', __( 'Your Moip account isn\'t active yet. Please connect to Moip first!', 'dokan' ), dokan_get_navigation_url('settings/payment'), __( 'Connect to Moip', 'dokan' ) );
        }

        return $errors;
    }

    /**
     * Remove withdraw page if moip is enabled
     *
     * @param  array  $urls
     *
     * @return array
     */
    public function remove_withdraw_page( $urls ) {
        $withdraw_settings    = get_option( 'dokan_withdraw' );
        $hide_withdraw_option = isset( $withdraw_settings['hide_withdraw_option'] ) ? $withdraw_settings['hide_withdraw_option'] : 'off';

        if ( $hide_withdraw_option == 'on' ) {
            $settings = get_option( 'woocommerce_dokan-moip-connect_settings' );
            // bailout if the gateway is not enabled
            if ( isset( $settings['enabled'] ) && $settings['enabled'] != 'yes' ) {
                return $urls;
            }

            if ( array_key_exists( 'withdraw', $urls ) ) {
                unset( $urls['withdraw'] );
            }

            return $urls;
        }

        return $urls;
    }

    /**
    * This is admin menu for controlling Seller moip status
    *
    * @param array $store_settings
    */
    function moip_admin_menu( $user ) {

        if ( ! dokan_is_user_seller( $user->ID ) || ! current_user_can( 'manage_woocommerce' )  ) {
            return $user;
        }

        $vendor_moip_account = get_user_meta( $user->ID, 'vendor_moip_account', true );
        ?>
        <h3><?php _e( 'Dokan Moip Settings', 'dokan' );?></h3>
        <?php
        if ( ! empty( $vendor_moip_account ) ) : ?>
            <?php submit_button( __( 'Disconnect User Moip Account', 'dokan' ) ,'delete', 'disconnect_user_moip'); ?>
        <?php else : ?>
            <h4><?php _e( 'User account not connected to Moip', 'dokan' );?></h4>
        <?php
        endif;
    }

    /**
     * Admin functions for controlling user Moip Accounts
     *
     * @param array $store_settings
     */
    function moip_admin_functions( $user_id ) {

        if ( ! dokan_is_user_seller( $user_id ) || ! current_user_can( 'manage_woocommerce' )  ) {
            return $user_id;
        }

        $moip_settings = get_option( 'woocommerce_dokan-moip-connect_settings' );

        if ( ! $moip_settings ) {
            return $user_id;
        }

        $vendor_moip_account = get_user_meta( $user_id, 'vendor_moip_account', true );

        if ( isset( $_POST['disconnect_user_moip'] ) ) {
            delete_user_meta( $user_id, 'vendor_moip_account' );
        }

        return $user_id;
    }


}

Dokan_Moip::init();
