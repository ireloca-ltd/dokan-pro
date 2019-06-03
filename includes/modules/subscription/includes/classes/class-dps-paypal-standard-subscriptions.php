<?php

use DokanPro\Modules\Subscription\Helper;
use DokanPro\Modules\Subscription\SubscriptionPack;

/**
 * PayPal Standard Subscription Class.
 *
 * Filters necessary functions in the WC_Paypal class to allow for subscriptions.
 *
 * @package     Dokan Product Subscription
 * @subpackage  WC_PayPal_Standard_Subscriptions
 * @category    Class
 * @author      Sabbir Ahmed
 * @since       1.0
 */

class DPS_PayPal_Standard_Subscriptions {

    protected static $log;
    protected static $debug;
    public static $api_username;
    public static $api_password;
    public static $api_signature;
    public static $api_endpoint;

    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     * @since 1.0
     */
    public static function init() {

        $paypal_settings = self::get_wc_paypal_settings();

        // logs
        self::subscription_paypal_credential_verify();

        // Set creds
        self::$api_username = ( isset( $paypal_settings['api_username'] ) ) ? $paypal_settings['api_username'] : '';
        self::$api_password = ( isset( $paypal_settings['api_password'] ) ) ? $paypal_settings['api_password'] : '';
        self::$api_signature = ( isset( $paypal_settings['api_signature'] ) ) ? $paypal_settings['api_signature'] : '';

        add_action( 'valid-paypal-standard-ipn-request', __CLASS__ . '::process_paypal_ipn_request', 11 );

        add_filter( 'woocommerce_paypal_args', __CLASS__ . '::paypal_standard_subscription_args' );

        add_action( 'woocommerce_settings_api_form_fields_paypal', __CLASS__ . '::paypal_settings_args' );

        add_action( 'woocommerce_update_options_payment_gateways_paypal', __CLASS__ . '::save_subscription_form_fields', 11 );

    }

    private static function log( $message = '' ) {
        $paypal_settings = self::get_wc_paypal_settings();

        if ( isset( $paypal_settings['debug'] ) && $paypal_settings['debug'] == 'yes' ) {
            $logger = new WC_Logger();
            $logger->add( 'paypal', $message );
        }
    }

    /**
     * Return the default WC PayPal gateway's settings.
     */
    private static function get_wc_paypal_settings() {
        $paypal_settings = get_option( 'woocommerce_paypal_settings' );

        return $paypal_settings;
    }

    /**
     * Returns a payment gateway object by gateway's ID, or false if it could not find the gateway.
     */
    public static function get_payment_gateway( $gateway_id ) {

        $found_gateway = false;

        if ( WC()->payment_gateways ) {
            foreach (WC()->payment_gateways->payment_gateways() as $gateway) {
                if ( $gateway_id == $gateway->id )
                    $found_gateway = $gateway;
            }
        }

        return $found_gateway;
    }

    /**
     * Adds extra PayPal credential fields required to manage subscriptions.
     *
     */
    public static function paypal_settings_args( $form_fields ) {
        // Warn store managers not to change their PayPal Email address as it can break existing Subscriptions in WC2.0+
        $form_fields['email']['desc_tip'] = false;
        $form_fields['email']['description'] .= ' </p><p class="description">' . __( 'It is <strong>strongly recommended you do not change this email address</strong> if you have active subscriptions with PayPal. Doing so can break existing subscriptions.', 'dokan' );

        $form_fields += array(
            'api_credentials' => array(
                'title'       => __( 'API Credentials', 'dokan' ),
                'type'        => 'title',
                'description' => sprintf( __( 'Enter your PayPal API credentials to unlock subscription suspension and cancellation features. %sLearn More &raquo;%s', 'dokan' ), '<a href="http://docs.woothemes.com/document/store-manager-guide/#extrapaypalconfigurationsteps" target="_blank" tabindex="-1">', '</a>' ),
                'default'     => ''
                ),
            'api_username' => array(
                'title'       => __( 'API Username', 'dokan' ),
                'type'        => 'text',
                'description' => '',
                'default'     => ''
                ),
            'api_password' => array(
                'title'       => __( 'API Password', 'dokan' ),
                'type'        => 'text',
                'description' => '',
                'default'     => ''
                ),
            'api_signature' => array(
                'title'       => __( 'API Signature', 'dokan' ),
                'type'        => 'text',
                'description' => '',
                'default'     => ''
                )
            );

        return $form_fields;
    }

    /**
     * In WC 2.0, settings are saved on a new instance of the PayPalpayment gateway, not
     * the global instance, so our admin fields are not set (nor saved). As a result, we
     * need to run the save routine @see WC_Settings_API::process_admin_options() again
     * to save our fields.
     *
     */
    public static function save_subscription_form_fields() {
        $paypal_gateway = self::get_payment_gateway( 'paypal' );

        $paypal_gateway->process_admin_options();
    }

    /**
     * Overwrite paypal arguments
     * @param  array $paypal_args
     * @return [type]              [description]
     */
    public static function paypal_standard_subscription_args( $paypal_args ) {

        $custom      = (array)json_decode( $paypal_args['custom'] );
        $order_id    = $custom['order_id'];
        $order_key   = $custom['order_key'];

        $order       = new WC_Order( $order_id );
        $order_items = $order->get_items();

        // Only one subscription allowed in the cart when PayPal Standard is active
        $product = $order->get_product_from_item( array_pop( $order_items ) );

        if ( ! Helper::is_subscription_product( $product->get_id() ) ) {
            return $paypal_args;
        }

        $subscription = dokan()->subscription->get( $product->get_id() );

        $paypal_args['cmd']       = '_xclick-subscriptions';
        $paypal_args['item_name'] = $subscription->get_package_title();

        $unconverted_periods = array(
            'billing_period' => $subscription->get_period_type(),
            'trial_period'   => $subscription->get_trial_period_types(),
        );

        $converted_periods = array();

        foreach ($unconverted_periods as $key => $period) {
            switch (strtolower( $period )) {
                case 'day':
                $converted_periods[$key] = 'D';
                break;
                case 'week':
                $converted_periods[$key] = 'W';
                break;
                case 'year':
                $converted_periods[$key] = 'Y';
                break;
                case 'month':
                default:
                $converted_periods[$key] = 'M';
                break;
            }
        }

        // max trial period length in days for paypal
        $max_trial_length_in_days  = 720;
        $initial_payment           = $order->get_total();
        $subscription_interval     = $subscription->get_recurring_interval();
        $subscription_installments = $subscription->get_period_length();
        $trial_end_timestamp       = $subscription->get_trial_end_time();
        $subscription_trial_lenth  = $subscription->get_trial_period_length();

        // We have a recurring payment
        if ( $subscription->is_recurring() ) {
            // if vendor has already used a trial pack, then make the tiral to a normal recurring pack
            if ( ! Helper::has_used_trial_pack( get_current_user_id() ) && $subscription->is_trial() ) {
                // Trial period 1 price. For a free trial period, specify 0.
                $paypal_args['a1'] = 0;

                if ( $subscription_trial_lenth > $max_trial_length_in_days ) {
                    throw new Exception( __( 'Trial subscription can\'t be more than 720 days for PayPal', 'dokan' ) );
                }

                // trail subscription
                $paypal_args['p1'] = $subscription->get_trial_range();;

                // trail period
                $paypal_args['t1'] = $converted_periods['trial_period'];

                // Subscription price
                $paypal_args['a3'] = $initial_payment;

                // Subscription duration
                $paypal_args['p3'] = $subscription_interval;

                // Subscription period
                $paypal_args['t3'] = $converted_periods['billing_period'];
            } else {
                // Subscription price
                $paypal_args['a3'] = $initial_payment;

                // Subscription duration
                $paypal_args['p3'] = $subscription_interval;

                // Subscription period
                $paypal_args['t3'] = $converted_periods['billing_period'];
            }

            if ( $subscription_installments === 1 ) {
                // Non-recurring payments
                $paypal_args['src'] = 0;
            } else {
                $paypal_args['src'] = 1;

                if ( $subscription_installments < 2 || $subscription_installments > 52 ) {
                    throw new Exception( __( 'Billing cycle can\'t be less than 2 or greater than 52 for PayPal', 'dokan' ) );
                }

                $paypal_args['srt'] = $subscription_installments;
            }
        }

        // if non-recurring pack
        if ( ! $subscription->is_recurring() ) {
            $paypal_args['src'] = 0;

            // Subscription price
            $paypal_args['a3'] = $initial_payment;

            // Subscription duration
            $paypal_args['p3'] = $subscription->get_pack_valid_days();

            // Subscription period
            $paypal_args['t3'] = 'D';
        }

        // Force return URL so that order description & instructions display
        $paypal_args['rm'] = 2;

        return $paypal_args;
    }

    /**
     * Handle payapl IPN response
     * @param  array $transaction_details [description]
     * @return [type]                      [description]
     */
    public static function process_paypal_ipn_request( $transaction_details ) {
        global $wpdb;

        $transaction_details = stripslashes_deep( $transaction_details );

        Helper::log( 'Transaction details check: ' .print_r( $transaction_details, true ) );

        if ( !in_array( $transaction_details['txn_type'], array('subscr_signup', 'subscr_payment', 'subscr_cancel', 'subscr_eot', 'subscr_failed', 'subscr_modify') ) ) {
            return;
        }

        if ( empty( $transaction_details['custom'] ) || empty( $transaction_details['invoice'] ) ) {
            return;
        }

        $custom      = (array)json_decode( $transaction_details['custom'] );
        $order_id    = $custom['order_id'];
        $order_key   = $custom['order_key'];

        $transaction_details['txn_type'] = strtolower( $transaction_details['txn_type'] );

        $order              = new WC_Order( $order_id );
        $subs_product       = $order->get_items();
        $product            = reset( $subs_product );

        $customer_id        = get_post_meta( $order_id, '_customer_user', true );
        $no_of_product_pack = get_post_meta( $product['product_id'], '_no_of_product', true );

        if ( dokan_get_prop( $order, 'order_key' ) !== $order_key ) {
            self::log( 'Subscription IPN Error: Order Key does not match invoice.' );
            return false;
        }

        if ( isset( $transaction_details['subscr_id'] ) ) {
            update_post_meta( $order_id, '_paypal_subscriber_ID', $transaction_details['subscr_id'] );
        }

        switch ($transaction_details['txn_type'] ) {
            case 'subscr_signup':
                // Store PayPal Details
                update_post_meta( $order_id, 'Payer PayPal address', $transaction_details['payer_email'] );
                update_post_meta( $order_id, 'Payer PayPal first name', $transaction_details['first_name'] );
                update_post_meta( $order_id, 'Payer PayPal last name', $transaction_details['last_name'] );

                $order->payment_complete();

                $subs_interval = get_post_meta( $product['product_id'], '_subscription_period_interval', true );
                $subs_period   = get_post_meta( $product['product_id'], '_subscription_period', true );
                $add_s         = ($subs_interval != 1 ) ? 's' : '';

                update_user_meta( $customer_id, 'product_package_id', $product['product_id'] );
                update_user_meta( $customer_id, 'product_order_id', $order_id );
                update_user_meta( $customer_id, 'product_no_with_pack', get_post_meta( $product['product_id'], '_no_of_product', true ) );
                update_user_meta( $customer_id, 'product_pack_startdate', date( 'Y-m-d H:i:s' ) );
                update_user_meta( $customer_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', strtotime( "+" . $subs_interval . " " . $subs_period . "" . $add_s ) ) );
                update_user_meta( $customer_id, 'can_post_product', '1' );
                update_user_meta( $customer_id, '_customer_recurring_subscription', 'active' );

                // make all the existing product publish
                Helper::make_product_publish( $customer_user_id );

                $admin_commission      = get_post_meta( $product['product_id'], '_subscription_product_admin_commission', true );
                $admin_commission_type = get_post_meta( $product['product_id'], '_subscription_product_admin_commission_type', true );

                if ( ! empty( $admin_commission ) && ! empty( $admin_commission_type ) ) {
                    update_user_meta( $customer_id, 'dokan_admin_percentage', $admin_commission );
                    update_user_meta( $customer_id, 'dokan_admin_percentage_type', $admin_commission_type );
                } else {
                    update_user_meta( $customer_id, 'dokan_admin_percentage', '' );
                }

                $order->add_order_note( __( 'IPN subscription sign up completed.', 'dokan' ) );
                self::log( 'IPN subscription sign up completed for order ' );

            break;

            case 'subscr_payment':
                if ( 'completed' == strtolower( $transaction_details['payment_status'] ) ) {
                    $subs_interval = get_post_meta( $product['product_id'], '_subscription_period_interval', true );
                    $subs_period   = get_post_meta( $product['product_id'], '_subscription_period', true );
                    $add_s         = ($subs_interval != 1 ) ? 's' : '';

                    // Store PayPal Details
                    update_post_meta( $order_id, '_payer_first_name', $transaction_details['first_name'] );
                    update_post_meta( $order_id, '_payer_last_name', $transaction_details['last_name'] );

                    update_user_meta( $customer_id, 'product_package_id', $product['product_id'] );
                    update_user_meta( $customer_id, 'product_order_id', $order_id );
                    update_user_meta( $customer_id, 'product_no_with_pack', get_post_meta( $product['product_id'], '_no_of_product', true ) );
                    update_user_meta( $customer_id, 'product_pack_startdate', date( 'Y-m-d H:i:s' ) );
                    update_user_meta( $customer_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', strtotime( "+" . $subs_interval . " " . $subs_period . "" . $add_s ) ) );
                    update_user_meta( $customer_id, 'can_post_product', '1' );
                    update_user_meta( $customer_id, '_customer_recurring_subscription', 'active' );

                    // make all the existing product publish
                    Helper::make_product_publish( $customer_user_id );

                    $admin_commission      = get_post_meta( $product['product_id'], '_subscription_product_admin_commission', true );
                    $admin_commission_type = get_post_meta( $product['product_id'], '_subscription_product_admin_commission_type', true );

                    if ( ! empty( $admin_commission ) && ! empty( $admin_commission_type ) ) {
                        update_user_meta( $customer_id, 'dokan_admin_percentage', $admin_commission );
                        update_user_meta( $customer_id, 'dokan_admin_percentage_type', $admin_commission_type );
                    } else {
                        update_user_meta( $customer_id, 'dokan_admin_percentage', '' );
                    }

                    if ( dokan_get_prop( $order, 'status' ) != 'completed' ) {
                        $order->payment_complete();
                    }

                                        // Subscription Payment completed
                    $order->add_order_note( sprintf( __( 'IPN subscription payment completed. txn_id#%s', 'dokan' ), $transaction_details['txn_id'] ) );

                } elseif ( 'failed' == strtolower( $transaction_details['payment_status'] ) ) {

                    // Subscription Payment completed
                    $order->add_order_note( __( 'IPN subscription payment failed.', 'dokan' ) );

                    self::log( 'IPN subscription payment failed for order ' . $order_id );

                // First payment on order, don't generate a renewal order
                } else {

                    self::log( 'IPN subscription payment notification received for order ' . $order_id . ' with status ' . $transaction_details['payment_status'] );
                }
                break;

            case 'subscr_cancel':

                self::log( 'IPN subscription cancelled for order ' . $order_id );

                // Subscription Payment completed
                $order->add_order_note( __( 'IPN subscription cancelled for order.', 'dokan' ) );

                if ( get_user_meta( $customer_id, 'product_order_id', true ) == $order_id ) {
                    Helper::log( 'Subscription cancel check: PayPal ( subscr_cancel ) has canceled Subscription of User #' . $customer_id . ' on order #' . $order_id );
                    Helper::delete_subscription_pack( $customer_id, $order_id );
                }

                break;

            case 'subscr_eot': // Subscription ended, either due to failed payments or expiration

                $subscription_length = get_post_meta( $product['product_id'], '_subscription_length', true );

                // PayPal fires the 'subscr_eot' notice immediately if a subscription is only for one billing period, so ignore the request when we only have one billing period
                if ( 1 != $subscription_length && $subscription_length != get_post_meta( $product['product_id'], '_subscription_period_interval', true ) ) {

                    self::log( 'IPN subscription end-of-term for order ' . $order_id );

                    // Record subscription ended
                    $order->add_order_note( __( 'IPN subscription end-of-term for order.', 'dokan' ) );

                    // Ended due to failed payments so cancel the subscription
                    Helper::log( 'Subscription cancel check: PayPal ( subscr_eot ) has canceled Subscription of User #' . $customer_id . ' on order #' . $order_id );
                    Helper::delete_subscription_pack( $customer_id, $order_id );
                }

                break;

            case 'subscr_failed': // Subscription sign up failed

                self::log( 'IPN subscription payment failure for order ' . $order_id );

                // Subscription Payment completed
                $order->add_order_note( __( 'IPN subscription payment failure.', 'dokan' ) );

                // First payment on order, don't generate a renewal order
                Helper::log( 'Subscription cancel check: PayPal ( subscr_failed ) has canceled Subscription of User #' . $customer_id . ' on order #' . $order_id );
                Helper::delete_subscription_pack( $customer_id, $order_id );

                break;
        }

        // Prevent default IPN handling for subscription txn_types
         exit;
    }

    /**
     * When a store manager or user cancels a subscription in the store, also cancel the subscription with PayPal.
     */
    public static function cancel_subscription_with_paypal( $order_id, $user_id ) {

        $order        = new WC_Order( $order_id );
        $profile_id   = get_post_meta( $order->get_id(), '_paypal_subscriber_ID', true );

        // Make sure a subscriptions status is active with PayPal
        $response = self::change_subscription_status( $profile_id, 'Cancel' );

        if ( $response ) {
            update_user_meta( $user_id, '_dps_user_subscription_status', 'cancelled' );
            Helper::delete_subscription_pack( $user_id, $order_id );

            $order->add_order_note( __( 'Subscription cancelled with PayPal', 'dokan' ) );
        }
    }

    /**
     * Performs an Express Checkout NVP API operation as passed in $api_method.
     *
     * Although the PayPal Standard API provides no facility for cancelling a subscription, the PayPal
     * Express Checkout  NVP API can be used.
     */
    public static function change_subscription_status( $profile_id, $new_status ) {

        switch ($new_status) {
            case 'Cancel' :
            $new_status_string = __( 'cancelled', 'dokan' );
            break;
            case 'Suspend' :
            $new_status_string = __( 'suspended', 'dokan' );
            break;
            case 'Reactivate' :
            $new_status_string = __( 'reactivated', 'dokan' );
            break;
        }

        $paypal_settings = self::get_wc_paypal_settings();

        // Set creds
        self::$api_endpoint  = ( $paypal_settings['testmode'] == 'no' ) ? 'https://api-3t.paypal.com/nvp' : 'https://api-3t.sandbox.paypal.com/nvp';
        self::$api_username  = ( isset( $paypal_settings['api_username'] ) ) ? $paypal_settings['api_username'] : '';
        self::$api_password  = ( isset( $paypal_settings['api_password'] ) ) ? $paypal_settings['api_password'] : '';
        self::$api_signature = ( isset( $paypal_settings['api_signature'] ) ) ? $paypal_settings['api_signature'] : '';

        $request = wp_remote_post( self::$api_endpoint, array(
            'timeout'   => 15,
            'sslverify' => false,
            'body'      => array(
                'USER'      => self::$api_username,
                'PWD'       => self::$api_password,
                'SIGNATURE' => self::$api_signature,
                'VERSION'   => '76.0',
                'METHOD'    => 'ManageRecurringPaymentsProfileStatus',
                'PROFILEID' => $profile_id,
                'ACTION'    => $new_status,
                'NOTE'      => sprintf( __( 'Subscription %s at %s', 'dokan' ), $new_status_string, get_bloginfo( 'name' ) )
                )
            ) );

        if ( is_wp_error( $request ) || $request['response']['code'] != 200 ) {
            self::log( 'Subscription Cancel - HTTP error' );
            return false;
        }

        $response = wp_remote_retrieve_body( $request );
        parse_str( $response, $parsed_response );

        if ( isset( $parsed_response['ACK'] ) && $parsed_response['ACK'] == 'Failure' ) {
            self::log( $parsed_response['L_LONGMESSAGE0'] );
            return false;
        }

        if ( isset( $parsed_response['ACK'] ) && $parsed_response['ACK'] == 'Success' ) {
            return true;
        }

        return false;
    }

    /**
    * Check for paypal information and save logs
    *
    * @since  [1.1.4]
    * @return set $debug,$log and $endpoint variables
    */
    public static function subscription_paypal_credential_verify(){
        $paypal_settings = self::get_wc_paypal_settings();

        if ( $paypal_settings && ! isset( $paypal_settings['debug'] ) ) {

            function dokan_paypal_credential_error() {
                ?>
                <div id="message" class="error notice is-dismissible">
                    <p><?php _e( 'Your Paypal Credentials are not complete', 'dokan' ); ?>.</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
                <?php
            }
            add_action( 'admin_notices', 'dokan_paypal_credential_error' );
        }
        else{
            self::$debug = ( $paypal_settings['debug'] == 'yes' ) ? true : false;
            self::$log = ( self::$debug ) ? new WC_Logger() : '';
            self::$api_endpoint = ( $paypal_settings['testmode'] == 'no' ) ? 'https://api-3t.paypal.com/nvp' : 'https://api-3t.sandbox.paypal.com/nvp';
        }
    }

}

DPS_PayPal_Standard_Subscriptions::init();