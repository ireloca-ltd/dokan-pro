<?php

use DokanPro\Modules\Subscription\Helper;
use DokanPro\Modules\Subscription\SubscriptionPack;

/**
 * Dokan_Stripe_Connect class.
 *
 * @extends WC_Payment_Gateway
 */
class Dokan_Stripe_Connect extends WC_Payment_Gateway {

    public function __construct() {

        $this->id           = 'dokan-stripe-connect';
        $this->method_title = __( 'Dokan Stripe Connect', 'dokan' );
        $this->icon         = plugins_url( '/assets/images/cards.png', dirname( __FILE__ ) );
        $this->has_fields   = true;
        $this->api_endpoint = 'https://api.stripe.com/';
        $this->supports     = array( 'products' );

        $this->init_form_fields();
        $this->init_settings();

        $this->title           = $this->settings['title'];
        $this->description     = $this->settings['description'];
        $this->enabled         = $this->settings['enabled'];
        $this->testmode        = $this->settings['testmode'];
        $this->stripe_checkout = isset( $this->settings['stripe_checkout'] ) && $this->settings['stripe_checkout'] == 'yes' ? true : false;
        $this->secret_key      = $this->testmode == 'no' ? $this->settings['secret_key'] : $this->settings['test_secret_key'];
        $this->publishable_key = $this->testmode == 'no' ? $this->settings['publishable_key'] : $this->settings['test_publishable_key'];
        $this->saved_cards     = $this->settings['saved_cards'] === "yes" ? true : false;
        $this->checkout_locale = $this->get_option( 'stripe_checkout_locale' );
        $this->accept_bitcoin  = ( 'USD' === strtoupper( get_woocommerce_currency() ) && 'yes' === $this->get_option( 'stripe_bitcoin' ) ) ? true : false;
        $this->checkout_image  = $this->get_option( 'stripe_checkout_image' );
        $this->checkout_label  = $this->get_option( 'stripe_checkout_label' );
        $this->stripe_meta_key = '_dokan_stripe_charge_id_';

        /** All actions */
        add_action( 'wp_enqueue_scripts', array( &$this, 'payment_scripts' ) );
        add_action( 'admin_notices', array( &$this, 'checks' ) );
        add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Check if SSL is enabled and notify the user
     */
    public function checks() {

        global $woocommerce;

        if ( $this->enabled == 'no' ) {
            return;
        }

        if ( $woocommerce->version < '1.5.8' ) {
            echo '<div class="error"><p>' . __( 'Stripe now uses stripe.js for security and requires WooCommerce 1.5.8. Please update WooCommerce to continue using Stripe.', 'dokan' ) . '</p></div>';
            return;
        }

        if ( ! $this->secret_key ) {
            echo '<div class="error"><p>' . sprintf( __( 'Stripe error: Please enter your secret key <a href="%s">here</a>', 'dokan' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=dokan_stripe_connect' ) ) . '</p></div>';
            return;

        } elseif ( ! $this->publishable_key ) {
            echo '<div class="error"><p>' . sprintf( __( 'Stripe error: Please enter your publishable key <a href="%s">here</a>', 'dokan' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=dokan_stripe_connect' ) ) . '</p></div>';
            return;
        }

        if ( $this->secret_key == $this->publishable_key ) {
            echo '<div class="error"><p>' . sprintf( __( 'Stripe error: Your secret and publishable keys match. Please check and re-enter.', 'dokan' ), admin_url( 'admin.php?page=wc-settings&tab=checkout&section=dokan_stripe_connect' ) ) . '</p></div>';
            return;
        }

        if ( ! wc_site_is_https() && get_option( 'woocommerce_force_ssl_checkout' ) == 'no' && ! class_exists( 'WordPressHTTPS' ) ) {
            echo '<div class="error"><p>' . sprintf( __( 'Stripe is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout may not be secure! Please enable SSL and ensure your server has a valid SSL certificate - Stripe will only work in test mode.', 'dokan' ), admin_url( 'admin.php?page=wc-settings&tab=advanced' ) ) . '</p></div>';
        }
    }

    /**
     * Check if this gateway is enabled and available in the user's country
     */
    public function is_available() {
        global $woocommerce;

        if ( $this->enabled == "yes" ) {

            if ( $woocommerce->version < '1.5.8' ) {
                return false;
            }

            if ( ! is_ssl() && $this->testmode != 'yes' ) {
                return false;
            }

            if ( ! in_array( get_option( 'woocommerce_currency' ), array( 'AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BIF','BMD','BND','BOB','BRL','BSD','BWP','BZD','CAD','CDF','CHF','CLP','CNY','COP','CRC','CVE','CZK','DJF','DKK','DOP','DZD','EEK','EGP','ETB','EUR','FJD','FKP','GBP','GEL','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','ISK','JMD','JPY','KES','KGS','KHR','KMF','KRW','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','LVL','MAD','MDL','MGA','MKD','MNT','MOP','MRO','MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SEK','SGD','SHP','SLL','SOS','SRD','STD','SVC','SZL','THB','TJS','TOP','TRY','TTD','TWD','TZS','UAH','UGX','USD','UYU','UZS','VEF','VND','VUV','WST','XAF','XCD','XOF','XPF','YER','ZAR','ZMW' ) ) ) {
                return false;
            }

            if ( ! $this->secret_key ) return false;
            if ( ! $this->publishable_key ) return false;

            return true;
        }

        return false;
    }

    /**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'       => __( 'Enable/Disable', 'dokan' ),
                'label'       => __( 'Enable Stripe', 'dokan' ),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => __( 'Title', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'dokan' ),
                'default'     => __( 'Dokan Credit card (Stripe)', 'dokan' )
            ),
            'description' => array(
                'title'       => __( 'Description', 'dokan' ),
                'type'        => 'textarea',
                'description' => __( 'This controls the description which the user sees during checkout.', 'dokan' ),
                'default'     => 'Pay with your credit card via Stripe.'
            ),
            'allow_non_connected_sellers' => array(
                'title'       => __( 'Non-connected sellers', 'dokan' ),
                'label'       => __( 'Allow ordering products from non-connected sellers', 'dokan' ),
                'type'        => 'checkbox',
                'description' => __( 'If this is enable, customers can order products from non-connected sellers. The payment will send to admin Stripe account.', 'dokan' ),
                'default'     => 'no'
            ),
            'testmode' => array(
                'title'       => __( 'Test mode', 'dokan' ),
                'label'       => __( 'Enable Test Mode', 'dokan' ),
                'type'        => 'checkbox',
                'description' => __( 'Place the payment gateway in test mode using test API keys.', 'dokan' ),
                'default'     => 'yes'
            ),
            'stripe_checkout' => array(
                'title'       => __( 'Stripe Checkout', 'dokan' ),
                'label'       => __( 'Enable Stripe Checkout', 'dokan' ),
                'type'        => 'checkbox',
                'description' => __( 'If enabled, this option shows a "pay" button and modal credit card form on the checkout, instead of credit card fields directly on the page.', 'dokan' ),
                'default'     => 'no'
            ),
            'stripe_checkout_locale' => array(
                'title'       => __( 'Stripe Checkout locale', 'dokan' ),
                'type'        => 'select',
                'class'       => 'wc-enhanced-select',
                'description' => __( 'Language to display in Stripe Checkout modal. Specify Auto to display Checkout in the user\'s preferred language, if available. English will be used by default.', 'dokan' ),
                'default'     => 'en',
                'desc_tip'    => true,
                'options'     => array(
                    'auto' => __( 'Auto', 'dokan' ),
                    'zh'   => __( 'Simplified Chinese', 'dokan' ),
                    'da'   => __( 'Danish', 'dokan' ),
                    'nl'   => __( 'Dutch', 'dokan' ),
                    'en'   => __( 'English', 'dokan' ),
                    'fi'   => __( 'Finnish', 'dokan' ),
                    'fr'   => __( 'French', 'dokan' ),
                    'de'   => __( 'German', 'dokan' ),
                    'it'   => __( 'Italian', 'dokan' ),
                    'ja'   => __( 'Japanese', 'dokan' ),
                    'no'   => __( 'Norwegian', 'dokan' ),
                    'es'   => __( 'Spanish', 'dokan' ),
                    'sv'   => __( 'Swedish', 'dokan' ),
                ),
            ),
            'stripe_bitcoin' => array(
                'title'       => __( 'Bitcoin Currency', 'dokan' ),
                'label'       => __( 'Enable Bitcoin Currency in Stripe Checkout', 'dokan' ),
                'type'        => 'checkbox',
                'description' => __( 'If enabled, an option to accept bitcoin will show on the checkout modal. Note: Stripe Checkout needs to be enabled and store currency must be set to USD.', 'dokan' ),
                'default'     => 'no',
                'desc_tip'    => true,
            ),
            'stripe_checkout_image' => array(
                'title'       => __( 'Checkout Image', 'dokan' ),
                'description' => __( 'Optionally enter the URL to a 128x128px image of your brand or product. e.g. <code>https://yoursite.com/wp-content/uploads/2013/09/yourimage.jpg</code>', 'dokan' ),
                'type'        => 'text',
                'default'     => '',
                'desc_tip'    => true,
            ),
            'stripe_checkout_label' => array(
                'title'       => __( 'Checkout Button Label', 'dokan' ),
                'description' => __( 'Optionally enter a Label for PAY button', 'dokan' ),
                'type'        => 'text',
                'default'     => '',
                'desc_tip'    => true,
            ),
            'saved_cards' => array(
                'title'       => __( 'Saved cards', 'dokan' ),
                'label'       => __( 'Enable saved cards', 'dokan' ),
                'type'        => 'checkbox',
                'description' => __( 'If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Stripe servers, not on your store.', 'dokan' ),
                'default'     => 'no'
            ),
            'live-credentials-title' => array(
                'title' => __( 'Live credentials', 'dokan' ),
                'type'  => 'title',
            ),
            'secret_key' => array(
                'title'       => __( 'Secret Key', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'Get your API keys from your stripe account.', 'dokan' ),
                'default'     => ''
            ),
            'publishable_key' => array(
                'title'       => __( 'Publishable Key', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'Get your API keys from your stripe account.', 'dokan' ),
                'default'     => ''
            ),
            'client_id' => array(
                'title'       => __( 'Client ID', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'Get your client ID from your stripe account, the Apps menu.', 'dokan' ),
                'default'     => ''
            ),
            'test-credentials-title' => array(
                'title' => __( 'Test credentials', 'dokan' ),
                'type'  => 'title',
            ),
            'test_secret_key' => array(
                'title'       => __( 'Test Secret Key', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'Get your API keys from your stripe account.', 'dokan' ),
                'default'     => ''
            ),
            'test_publishable_key' => array(
                'title'       => __( 'Test Publishable Key', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'Get your API keys from your stripe account.', 'dokan' ),
                'default'     => ''
            ),
            'test_client_id' => array(
                'title'       => __( 'Test Client ID', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'Get your client ID from your stripe account, the Apps menu.', 'dokan' ),
                'default'     => ''
            ),
        );
    }

    /**
     * Admin Panel Options
     * - Options for bits like 'title' and availability on a country-by-country basis
     */
    public function admin_options() {
        ?>
        <h3><?php _e( 'Stripe Connect', 'dokan' ); ?></h3>
        <p><?php _e( 'Stripe works by adding credit card fields on the checkout and then sending the details to Stripe for verification.', 'dokan' ); ?></p>
        <?php
            echo '<p>' . sprintf( __( 'Recurring subscription requires webhooks to be configured. Go to <a href="%s">webhook</a> and set your webhook url <code>%s</code>. Otherwise recurring payment not working automatically', 'dokan' ), 'https://dashboard.stripe.com/account/webhooks', add_query_arg( array( 'webhook' => 'dokan' ), home_url('/') ) ) . '</p>';
         ?>
        <?php if ( in_array( get_option( 'woocommerce_currency' ), array( 'AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BIF','BMD','BND','BOB','BRL','BSD','BWP','BZD','CAD','CDF','CHF','CLP','CNY','COP','CRC','CVE','CZK','DJF','DKK','DOP','DZD','EEK','EGP','ETB','EUR','FJD','FKP','GBP','GEL','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','INR','ISK','JMD','JPY','KES','KGS','KHR','KMF','KRW','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','LVL','MAD','MDL','MGA','MKD','MNT','MOP','MRO','MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','RON','RSD','RUB','RWF','SAR','SBD','SCR','SEK','SGD','SHP','SLL','SOS','SRD','STD','SVC','SZL','THB','TJS','TOP','TRY','TTD','TWD','TZS','UAH','UGX','USD','UYU','UZS','VEF','VND','VUV','WST','XAF','XCD','XOF','XPF','YER','ZAR','ZMW' ) ) ) { ?>
        <table class="form-table">
            <?php $this->generate_settings_html(); ?>
        </table><!--/.form-table-->

        <?php } else { ?>

        <div class="inline error">
            <p>
                <strong><?php _e( 'Gateway Disabled', 'dokan' ); ?></strong>
                <?php echo __( 'Choose a currency supported by Stripe as your store currency to enable Stripe Connect.', 'dokan' ); ?>
            </p>
        </div>
        <?php }
    }

    /**
     * Get Stripe amount to pay
     * @return float
     */
    public function get_stripe_amount( $total ) {

        switch ( get_woocommerce_currency() ) {
            /* Zero decimal currencies*/
            case 'BIF' :
            case 'CLP' :
            case 'DJF' :
            case 'GNF' :
            case 'JPY' :
            case 'KMF' :
            case 'KRW' :
            case 'MGA' :
            case 'PYG' :
            case 'RWF' :
            case 'VND' :
            case 'VUV' :
            case 'XAF' :
            case 'XOF' :
            case 'XPF' :
            $total = absint( $total );
            break;
            default :
            $total = $total * 100; /* In cents*/
            break;
        }

        return $total;
    }

    /**
     * Payment form on checkout page
     */
    public function payment_fields() {
        $checked = 1;
        ?>
        <fieldset>
            <?php
            if ( $this->description ) {
                echo wpautop( esc_html( $this->description ) );
            }
            if ( $this->testmode == 'yes' ) {
                echo '<p>' . __( 'TEST MODE ENABLED. In test mode, you can use the card number 4242424242424242 with any CVC and a valid expiration date.', 'dokan' ) . '</p>';
            }
            ?>

            <?php if ( is_user_logged_in() && ( $credit_cards = get_user_meta( get_current_user_id(), '_stripe_customer_id', false ) ) ) : ?>
                <p class="form-row form-row-wide">

                    <a class="button" style="float:right;" href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>#saved-cards"><?php _e( 'Manage cards', 'dokan' ); ?></a>

                    <?php foreach ( $credit_cards as $i => $credit_card ) : if ( empty($credit_card['active_card']) ) continue; ?>
                        <input type="radio" id="stripe_card_<?php echo $i; ?>" name="stripe_customer_id" style="width:auto;" value="<?php echo $i; ?>" />
                        <label style="display:inline;" for="stripe_customer_<?php echo $i; ?>"><?php _e( 'Card ending with', 'dokan' ); ?> <?php echo $credit_card['active_card']; ?> (<?php echo $credit_card['exp_month'] . '/' . $credit_card['exp_year'] ?>)</label><br />
                    <?php endforeach; ?>

                    <input type="radio" id="new" name="stripe_customer_id" style="width:auto;" <?php checked( 1, 1 ) ?> value="new" /> <label style="display:inline;" for="new"><?php _e( 'Use a new credit card', 'dokan' ); ?></label>

                </p>
                <div class="clear"></div>
            <?php endif; ?>

            <div class="stripe_new_card" <?php if ( $checked === 0 ) : ?> style="display:none;"<?php endif; ?>
                data-amount="<?php echo esc_attr( $this->get_stripe_amount( WC()->cart->total ) ); ?>"
                data-currency="<?php echo esc_attr( strtolower( get_woocommerce_currency() ) ); ?>"
                >
                <?php
                if ( ! $this->stripe_checkout ) :
                    $cc_form = new WC_Payment_Gateway_CC;
                    $cc_form->id       = $this->id;
                    $cc_form->supports = $this->supports;
                    $cc_form->form();
                endif;
                ?>
            </div>
        </fieldset>
        <?php
    }

    /**
     * Saved for previous card
     * @param  integer $customer_id
     * @return object
     */
    public function get_saved_cards( $customer_id ) {

        if ( false === ( $cards = get_transient( 'stripe_cards_' . $customer_id ) ) ) {
            $response = $this->stripe_request( array(
                'limit'       => 100
                ), 'customers/' . $customer_id . '/cards', 'GET' );

            if ( is_wp_error( $response ) ) {
                return false;
            }

            $cards = $response->data;

            set_transient( 'stripe_cards_' . $customer_id, $cards, HOUR_IN_SECONDS * 48 );
        }

        return $cards;
    }

    /**
     * payment_scripts function.
     *
     * Outputs scripts used for stripe payment
     *
     * @access public
     */
    public function payment_scripts() {

        if ( ! is_checkout() ) {
            return;
        }

        if ( $this->stripe_checkout ) {
            wp_enqueue_script( 'stripe', 'https://checkout.stripe.com/v2/checkout.js', '', '2.0', true );
            wp_enqueue_script( 'dokan_stripe', plugins_url( 'assets/js/stripe_checkout.js', dirname( __FILE__ ) ), array( 'stripe' ), false, true );
        } else {
            wp_enqueue_script( 'stripe', 'https://js.stripe.com/v1/', '', '1.0', true );
            wp_enqueue_script( 'dokan_stripe', plugins_url( 'assets/js/stripe.js', dirname( __FILE__ ) ), array( 'jquery','stripe' ), false, false );
        }

        $stripe_params = array(
            'key'                  => $this->publishable_key,
            'name'                 => get_bloginfo( 'name' ),
            'description'          => get_bloginfo ( 'description' ),
            'label'                => sprintf( __( '%s', 'dokan') , $this->checkout_label ),
            'locale'               => $this->checkout_locale,
            'bitcoin'              => $this->accept_bitcoin ? 'true' : 'false',
            'image'                => $this->checkout_image,
            'i18n_terms'           => __( 'Please accept the terms and conditions first', 'dokan' ),
            'i18n_required_fields' => __( 'Please fill in required checkout fields first', 'dokan' ),
            );

        if ( is_checkout_pay_page() && isset( $_GET['order'] ) && isset( $_GET['order_id'] ) ) {
            $order_key = urldecode( $_GET['order'] );
            $order_id  = absint( $_GET['order_id'] );
            $order     = wc_get_order( $order_id );

            if ( dokan_get_prop( $order, 'id') == $order_id && dokan_get_prop( $order, 'order_key') == $order_key ) {
                $stripe_params['billing_first_name'] = dokan_get_prop( $order , 'billing_first_name');
                $stripe_params['billing_last_name']  = dokan_get_prop( $order , 'billing_last_name');
                $stripe_params['billing_address_1']  = dokan_get_prop( $order , 'billing_address_1');
                $stripe_params['billing_address_2']  = dokan_get_prop( $order , 'billing_address_2');
                $stripe_params['billing_state']      = dokan_get_prop( $order , 'billing_state');
                $stripe_params['billing_city']       = dokan_get_prop( $order , 'billing_city');
                $stripe_params['billing_postcode']   = dokan_get_prop( $order , 'billing_postcode');
                $stripe_params['billing_country']    = dokan_get_prop( $order , 'billing_country');
            }
        }

        wp_localize_script( 'dokan_stripe', 'wc_stripe_connect_params', apply_filters( 'dokan_stripe_js_params', $stripe_params) );
    }

    /**
     * Get order details
     *
     * @param  int  $order_id
     * @param  int  $seller_id
     *
     * @return array
     */
    public function get_dokan_order( $order_id, $seller_id ) {
        global $wpdb;

        $sql = "SELECT *
        FROM {$wpdb->prefix}dokan_orders AS do
        WHERE
        do.seller_id = %d AND
        do.order_id = %d";

        return $wpdb->get_row( $wpdb->prepare( $sql, $seller_id, $order_id ) );
    }

    /**
     * Process the payment
     */
    public function process_payment( $order_id ) {
        global $woocommerce, $wpdb;

        $customer_id  = 0;
        $stripe_token = isset( $_POST['stripe_token'] ) ? wc_clean( $_POST['stripe_token'] ) : '';

        $order        = wc_get_order( $order_id );
        $all_withdraws = array();

        try {

            // if ( ! method_exists( 'Stripe', 'setApiKey' ) ) {
            require_once 'lib/init.php';
            // }

            \Stripe\Stripe::setApiKey( $this->secret_key );

            /* Check if paying via customer ID (saved credit card?)*/
            if ( isset( $_POST['stripe_customer_id'] ) && $_POST['stripe_customer_id'] !== 'new' && is_user_logged_in() ) {

                $customer_ids = get_user_meta( get_current_user_id(), '_stripe_customer_id', false );

                if ( isset( $customer_ids[ $_POST['stripe_customer_id'] ]['customer_id'] ) ) {
                    $customer_id = $customer_ids[ $_POST['stripe_customer_id'] ]['customer_id'];
                } else {
                    throw new Exception( __( 'Invalid card.', 'dokan' ) );
                }
            }

            /* Else, Check token*/
            else if ( empty( $stripe_token ) ) {
                throw new Exception( __( 'Please make sure your card details have been entered correctly and that your browser supports JavaScript.', 'dokan' ) );
            }

            /* Check amount*/
            $order_total = dokan_get_prop( $order, 'order_total', 'get_total' );
            if ( $order_total * 100 < 50 ) {
                throw new Exception( __( 'Minimum order total is 0.50', 'dokan' ) );
            }

            if ( is_user_logged_in() && ! $customer_id && $stripe_token ) {

                $customer_id = $this->add_customer( $order, $stripe_token );

            } else if ( !is_user_logged_in() ) {

                if ( !empty( $woocommerce->session->stripe_guest_user_token ) ) {

                    $customer_id = $woocommerce->session->stripe_guest_user_token;

                } else {

                    $customer_id = $this->add_customer( $order, $stripe_token );

                    $woocommerce->session->set( 'stripe_guest_user_token', $customer_id );
                }
            }

            if ( $this->is_subscription_order( $order ) ) {
                $this->process_subscription_payment( $order_id, $order, $customer_id, $stripe_token );
            } else {
                $this->process_seller_payment( $order_id, $order, $customer_id, $stripe_token );
            }

        } catch( Exception $e ) {
            /* Add order note*/
            $order->add_order_note( sprintf( __( 'Stripe Payment Error: %s', 'dokan' ), $e->getMessage() ) );
            update_post_meta( $order_id, '_dwh_stripe_charge_error', $e->getMessage());

            wc_add_notice( __( 'Error: ', 'dokan' ) . $e->getMessage() );
            return;
        }

        /* Return redirect URL to thank you page*/
        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }

    /**
    * Process subscription payment
    *
    * @since 1.3.3
    *
    * @return void
    **/
    public function process_subscription_payment( $order_id, $order, $customer_id, $stripe_token ) {
        $currency     = strtolower( get_woocommerce_currency() );
        $order_desc   = sprintf( __( '%s - Order %s', 'dokan' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );
        $charge_ids   = array();

        if ( ! $order ) {
            throw new Exception( __( 'No orders found to process!', 'dokan' ) );
        }

        // We assume that if a subscription product added into a cart then no other product doesn't exist in cart so we get only one product
        $order_items        = $order->get_items();
        $product_pack_item  = reset( $order_items );
        $product_pack       = wc_get_product( $product_pack_item->get_product_id() );

        $product_pack_name  = $product_pack->get_title() . ' #' . $product_pack->get_id();
        $product_pack_id    = $product_pack->get_slug() . '-' . $product_pack->get_id();
        $customer_user_id   = $order->get_customer_id();
        $order_total        = round( $order->get_total(), 2 );

        $dokan_subscription = dokan()->subscription->get( $product_pack->get_id() );

        if ( $dokan_subscription->is_recurring() ) {
            // If reccuring pack
            $subscription_interval = $dokan_subscription->get_recurring_interval();
            $subscription_period   = $dokan_subscription->get_period_type();
            $subscription_length   = $dokan_subscription->get_period_length();
            $trial_period_days     = $dokan_subscription->is_trial() ? $dokan_subscription->get_trial_period_length() : 0;

            // if vendor already has used a trial pack, create a new plan without trial period
            if ( Helper::has_used_trial_pack( get_current_user_id() ) ) {
                $trial_period_days = 0;
                $product_pack_id   = $product_pack_id . '-' . random_int( 1, 99999 );
            }

            try {
                $stripe_plan = \Stripe\Plan::retrieve( $product_pack_id );
            } catch ( Exception $e ) {
                $stripe_product = \Stripe\Product::create( array(
                   'name' => $product_pack_name,
                   'type' => 'service'
                ) );

                \Stripe\Plan::create( array(
                    'amount'            => $order_total * 100,
                    'interval'          => $subscription_period,
                    'interval_count'    => $subscription_interval,
                    'currency'          => $currency,
                    'id'                => $product_pack_id,
                    'product'           => $stripe_product->id,
                    'trial_period_days' => $trial_period_days
                ) );
            }

            $already_has_subscription = get_user_meta( $customer_user_id, '_stripe_subscription_id', true );

            if ( ! empty( $already_has_subscription ) ) {
                $existing = \Stripe\Subscription::retrieve( $already_has_subscription );
                $existing->cancel();
            }

            $customer     = \Stripe\Customer::retrieve( $customer_id );
            $subscription = $customer->subscriptions->create( [
                'plan'            => $product_pack_id,
                'trial_from_plan' => true
            ] );

            $transaction_id                = $subscription->id;
            $add_s                         = ( $subscription_interval != 1 ) ? 's' : '';
            $charge_ids[$customer_user_id] = $transaction_id;

            update_user_meta( $customer_user_id, '_stripe_subscription_id', $transaction_id );
            update_user_meta( $customer_user_id, 'product_package_id', $product_pack->get_id() );
            update_user_meta( $customer_user_id, 'product_order_id', $order_id );
            update_user_meta( $customer_user_id, 'product_no_with_pack', get_post_meta( $product_pack->get_id(), '_no_of_product', true ) );
            update_user_meta( $customer_user_id, 'product_pack_startdate', date( 'Y-m-d H:i:s' ) );
            update_user_meta( $customer_user_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', strtotime( "+" . $subscription_interval . " " . $subscription_period . "" . $add_s ) ) );
            update_user_meta( $customer_user_id, 'can_post_product', '1' );
            update_user_meta( $customer_user_id, '_customer_recurring_subscription', 'active' );

            Helper::make_product_publish( $customer_user_id );

            $admin_commission      = get_post_meta( $product_pack->get_id(), '_subscription_product_admin_commission', true );
            $admin_commission_type = get_post_meta( $product_pack->get_id(), '_subscription_product_admin_commission_type', true );

            if ( ! empty( $admin_commission ) && ! empty( $admin_commission_type ) ) {
                update_user_meta( $customer_user_id, 'dokan_admin_percentage', $admin_commission );
                update_user_meta( $customer_user_id, 'dokan_admin_percentage_type', $admin_commission_type );
            } else {
                update_user_meta( $customer_user_id, 'dokan_admin_percentage', '' );
            }

            $order->payment_complete();
            $order->add_order_note( sprintf( __( 'Order %s payment <a href="#">completed</a> via %s on (Charge IDs: %s)', 'dokan' ), $order->get_order_number(), $this->method_title, $transaction_id ) );

        } else {
            // If non-rcurring pack
            try {
                $charge = \Stripe\Charge::create( array( 'customer' => $customer_id, 'amount' => $order_total * 100, 'currency' => $currency, 'description' => $order_desc ) );
                $charge_ids[ $customer_user_id ] = $charge->id;

                $pack_validity = get_post_meta( $product_pack->get_id(), '_pack_validity', true );
                update_user_meta( $customer_user_id, 'product_package_id', $product_pack->get_id() );
                update_user_meta( $customer_user_id, 'product_order_id', $order_id );
                update_user_meta( $customer_user_id, 'product_no_with_pack', get_post_meta( $product_pack->get_id(), '_no_of_product', true ) );
                update_user_meta( $customer_user_id, 'product_pack_startdate', date( 'Y-m-d H:i:s' ) );
                update_user_meta( $customer_user_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', strtotime( "+$pack_validity days" ) ) );
                update_user_meta( $customer_user_id, 'can_post_product', '1' );
                update_user_meta( $customer_user_id, '_customer_recurring_subscription', '' );

                Helper::make_product_publish( $customer_user_id );

                $admin_commission      = get_post_meta( $product_pack->get_id(), '_subscription_product_admin_commission', true );
                $admin_commission_type = get_post_meta( $product_pack->get_id(), '_subscription_product_admin_commission_type', true );

                if ( ! empty( $admin_commission ) && ! empty( $admin_commission_type ) ) {
                    update_user_meta( $customer_user_id, 'dokan_admin_percentage', $admin_commission );
                    update_user_meta( $customer_user_id, 'dokan_admin_percentage_type', $admin_commission_type );
                } else {
                    update_user_meta( $customer_user_id, 'dokan_admin_percentage', '' );
                }

                $order->payment_complete();
                $order->add_order_note( sprintf( __( 'Order %s payment <a href="#">completed</a> via Dokan Stripe on (Charge IDs: %s)', 'dokan' ), $order->get_order_number(), implode( ', ', $charge_ids ) ) );

            } catch ( Exception $e ) {
                $order->add_order_note( sprintf( __( 'Stripe Payment Error: %s', 'dokan' ), $e->getMessage() ) );

                throw new Exception( $e->getMessage() );
            }
        }
    }

    /**
    * Process Seller payment
    *
    * @since 1.3.3
    *
    * @return void
    **/
    public function process_seller_payment( $order_id, $order, $customer_id, $stripe_token ) {
        $currency     = strtolower( get_woocommerce_currency() );
        $order_desc   = sprintf( __( '%s - Order %s', 'dokan' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );
        $charge_ids   = array();
        $has_suborder = get_post_meta( $order_id, 'has_sub_order', true );
        $all_orders   = array();

        // put orders in an array
        // if has sub-orders, pick only sub-orders
        // if it's a single order, get the single order only
        if ( $has_suborder == '1' ) {
            $sub_orders = get_children( array( 'post_parent' => $order_id, 'post_type' => 'shop_order' ) );

            foreach ($sub_orders as $order_post) {
                $sub_order    = wc_get_order( $order_post->ID  );
                $all_orders[] = $sub_order;
            }

        } else {
            $all_orders[] = $order;
        }

        if ( ! $all_orders ) {
            throw new Exception( __( 'No orders found to process!', 'dokan' ) );
        }

        // seems like we have some orders to process
        // iterate through orders and fetch the net amount and fees
        foreach ( $all_orders as $tmp_order ) {
            $tmp_order_id = dokan_get_prop( $tmp_order, 'id' );
            $seller_id    = dokan_get_seller_id_by_order( $tmp_order_id );
            $do_order     = $this->get_dokan_order( $tmp_order_id, $seller_id );

            // in-case we can't find the order
            if ( ! $do_order ) {
                throw new Exception( __( 'Something went wrong and the order can not be processed!', 'dokan' ) );
            }

            $fee          = floatval( $do_order->order_total ) - floatval( $do_order->net_amount );
            $access_token = get_user_meta( $seller_id, '_stripe_connect_access_key', true );

            $settings = get_option('woocommerce_dokan-stripe-connect_settings');

            if ( isset( $settings['allow_non_connected_sellers'] ) && 'yes' === $settings['allow_non_connected_sellers'] ) {
                $allow_non_connected_sellers = true;
            } else {
                $allow_non_connected_sellers = false;
            }

            if ( ! empty( $access_token ) ) {
                $token = \Stripe\Token::create( array( 'customer' => $customer_id ), $access_token );
            } else if ( $allow_non_connected_sellers ) {
                $token = null;
            } else {
                throw new Exception( __( 'Unable to process with Stripe gateway', 'dokan' ) );
            }

            $order_total     = round( $do_order->order_total, 2 );
            $application_fee = ( $allow_non_connected_sellers && ! $token ) ? $order_total : round( $fee, 2 );

            if ( $do_order->order_total == 0 ) {
                $tmp_order->add_order_note( sprintf( __( 'Order %s payment completed', 'dokan' ), $tmp_order->get_order_number() ) );
                continue;
            }
            //data array for withdraw
            $withdraw_data = array(
                'user_id'  => $seller_id,
                'amount'   => $order_total - $application_fee,
                'order_id' => $tmp_order_id,
            );

            $all_withdraws[] = $withdraw_data;

            if ( $token ) {
                $charge = \Stripe\Charge::create( array(
                    'amount'          => $order_total * 100,
                    'currency'        => $currency,
                    'application_fee' => $application_fee * 100,
                    'description'     => $order_desc,
                    'card'            => ! empty( $token->id ) ? $token->id : $stripe_token
                    ), $access_token );
            } else {
                $order_desc   = sprintf( __( '%s - Order %s, suborder of %s', 'dokan' ), esc_html( get_bloginfo( 'name' ) ), $tmp_order_id, $order->get_order_number() );

                $charge = \Stripe\Charge::create( [
                    'amount'      => $order_total * 100,
                    'currency'    => $currency,
                    'description' => $order_desc,
                    'customer'    => $customer_id
                ] );

                $tmp_order->add_order_note( sprintf( __( 'Vendor payment transferred to admin account since the vendor had not connected to Stripe', 'dokan' ) ) );
            }

            $charge_ids[ $seller_id ] = $charge->id;
            update_post_meta( $tmp_order_id, $this->stripe_meta_key . $seller_id, $charge->id );

            if ( $order_id !== $tmp_order_id ) {
                $tmp_order->add_order_note( sprintf( __( 'Order %s payment completed via Dokan Stripe on Charge ID: %s', 'dokan' ), $tmp_order->get_order_number(), $charge->id ) );
            }
        }

        /* Add order note*/
        $order->add_order_note( sprintf( __( 'Order %s payment <a href="#">completed</a> via Dokan Stripe on (Charge IDs: %s)', 'dokan' ), $order->get_order_number(), implode( ', ', $charge_ids ) ) );

        /* Payment complete*/
        $order->update_status( 'completed' );
        $order->payment_complete();

        $this->insert_into_vendor_balance( $all_withdraws );
        $this->process_seller_withdraws( $all_withdraws );

        foreach ( $charge_ids as $seller_id => $charge_id ) {
            $meta_key = $this->stripe_meta_key . $seller_id;
            update_post_meta( $order_id, $meta_key, $charge_id );
        }

        return $charge_ids;
    }

    /**
    * Check is subscription order
    *
    * @since 1.3.3
    *
    * @return void
    **/
    public function is_subscription_order( $order ) {
        if ( ! $order ) {
            return false;
        }

        foreach ( $order->get_items() as $item ) {
            $product = wc_get_product( $item->get_product_id() );

            if ( class_exists( 'Dokan_Product_Subscription' ) && 'product_pack' == $product->get_type() ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Insert withdraw data into vendor balnace table
     *
     * @param  array $all_withdraw
     *
     * @return void
     */
    public function insert_into_vendor_balance( $all_withdraws ) {
        if ( ! $all_withdraws ) {
            return;
        }

        global $wpdb;

        foreach ( $all_withdraws as $withdraw ) {
            $wpdb->insert( $wpdb->prefix . 'dokan_vendor_balance',
                array(
                    'vendor_id'     => $withdraw['user_id'],
                    'trn_id'        => $withdraw['order_id'],
                    'trn_type'      => 'dokan_withdraw',
                    'perticulars'   => 'Paid Via Stripe',
                    'debit'         => 0,
                    'credit'        => $withdraw['amount'],
                    'status'        => 'approved',
                    'trn_date'      => current_time( 'mysql' ),
                    'balance_date'  => current_time( 'mysql' ),
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%f',
                    '%f',
                    '%s',
                    '%s',
                    '%s',
                )
            );
        }
    }

    /**
     * Automatically process withdrwal for sellers per order
     *
     * @since 1.3.2
     *
     * @param array $all_withdraws
     *
     * @return void
     */
    public function process_seller_withdraws( $all_withdraws ){
        $IP =  dokan_get_client_ip();
        $withdraw = new Dokan_Withdraw();
        foreach ( $all_withdraws as $withdraw_data ) {

            $data = array(
                'date'    => current_time( 'mysql' ),
                'status'  => 1,
                'method'  => 'dokan-stripe-connect',
                'notes'   => sprintf( __( 'Order %d payment Auto paid via Dokan Stripe', 'dokan' ), $withdraw_data['order_id'] ),
                'ip'      => $IP
            );

            $data = array_merge( $data, $withdraw_data );
            $withdraw->insert_withdraw( $data );
        }
    }

    /**
     * add_customer function.
     *
     * @param mixed   $stripe_token
     *
     * @return void
     */
    public function add_customer( $order, $stripe_token ) {

        if ( ! $stripe_token ) {
            return;
        }

        require_once 'lib/init.php';

        \Stripe\Stripe::setApiKey( $this->secret_key );

        $customer = \Stripe\Customer::create( array(
            'email'       => dokan_get_prop( $order , 'billing_email' ),
            'description' => 'Customer: ' . dokan_get_prop( $order , 'shipping_first_name') . ' ' . dokan_get_prop( $order, 'shipping_last_name' ),
            "card"        => $stripe_token,
            'expand[]'    => 'default_card'
            )
        );

        if ( empty( $customer->id ) ) {
            return;
        }

        if ( !is_user_logged_in() ) {
            return $customer->id;
        }

        if ( $this->saved_cards && !empty( $customer->sources->data[0]->last4 ) ) {
            add_user_meta( get_current_user_id(), '_stripe_customer_id', array(
                'customer_id' => $customer->id,
                'active_card' => !empty( $customer->sources->data[0]->last4 ) ? $customer->sources->data[0]->last4 : '',
                'exp_year'    => !empty( $customer->sources->data[0]->exp_year ) ? $customer->sources->data[0]->exp_year : '',
                'exp_month'   => !empty( $customer->sources->data[0]->exp_month ) ? $customer->sources->data[0]->exp_month : '',
            ) );
        }

        return $customer->id;
    }

    /**
    * Cancel stripe subscription
    *
    * @since 1.3.3
    *
    * @return void
    **/
    public static function cancel_recurring_subscription( $user_id ) {
        $user_subscription = get_user_meta( $user_id, '_stripe_subscription_id', true );

        require_once 'lib/init.php';

        $stripe_option = get_option( 'woocommerce_dokan-stripe-connect_settings' );

        if ( $stripe_option['testmode'] == 'yes' ) {
            $secret_key = $stripe_option['test_secret_key'];
        } else {
            $secret_key = $stripe_option['secret_key'];
        }

        \Stripe\Stripe::setApiKey( $secret_key );
        $existing_subscription = \Stripe\Subscription::retrieve( $user_subscription );
        $existing_subscription->cancel();
    }


    /**
     * Maintain Stripe request
     *
     * @param  array $request
     * @param  string $api
     *
     * @return array
     */
    public function stripe_request( $request, $api = 'charges' ) {
        global $woocommerce;

        $response = wp_remote_post( $this->api_endpoint . 'v1/' . $api, array(
            'method'        => 'POST',
            'headers'       => array(
                'Authorization' => 'Basic ' . base64_encode( $this->secret_key . ':' )
                ),
            'body'          => $request,
            'timeout'       => 70,
            'sslverify'     => false,
            'user-agent'    => 'WooCommerce ' . $woocommerce->version
            ));

        if ( is_wp_error($response) ) {
            return new WP_Error( 'stripe_error', __('There was a problem connecting to the payment gateway.', 'dokan') );
        }

        if ( empty($response['body']) ) {
            return new WP_Error( 'stripe_error', __('Empty response.', 'dokan') );
        }

        $parsed_response = json_decode( $response['body'] );

        /* Handle response */
        if ( ! empty( $parsed_response->error ) ) {

            return new WP_Error( 'stripe_error', $parsed_response->error->message );

        } elseif ( empty( $parsed_response->id ) ) {

            return new WP_Error( 'stripe_error', __('Invalid response.', 'dokan') );

        }

        return $parsed_response;
    }
 }
