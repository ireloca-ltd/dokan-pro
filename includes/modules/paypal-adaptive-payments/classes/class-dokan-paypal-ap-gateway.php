<?php
if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Provides a PayPal Adaptive Payment Gateway.
 *
 * @author weDevs
 */
class WC_Dokan_Paypal_Ap_Gateway extends WC_Payment_Gateway {

    var $notify_url;

    /**
     * Constructor for the gateway.
     *
     * @access public
     * @return void
     */
    public function __construct() {

        $this->id                 = 'dokan_paypal_adaptive';
        $this->icon               = false;
        $this->has_fields         = true;
        $this->method_title       = __( 'Dokan PayPal Adaptive Payments', 'dokan' );
        $this->method_description = __( 'Pay via paypal adaptive payment', 'dokan' );
        $this->icon               = apply_filters( 'woocommerce_paypal_icon', DOKAN_PAYPAL_ADAPTIVE_PLUGIN_PATH . '/assets/images/paypal-adaptive.png' );

        $title                    = $this->get_option( 'title' );
        $this->title              = empty( $title ) ? __( 'PayPal Adaptive Payments', 'dokan' ) : $title;
        $this->testmode           = $this->get_option( 'testmode' );
        $this->send_shipping      = $this->get_option( 'send_shipping' );
        $this->single_mode        = $this->get_option( 'single_mode' );
        $this->fees_payer         = $this->get_option( 'fees_payer' );
        $this->appuser            = $this->get_option( 'appuser' );
        $this->apppass            = $this->get_option( 'apppass' );
        $this->appsig             = $this->get_option( 'appsig' );
        $this->appid              = $this->get_option( 'appid' );
        $this->test_appuser       = $this->get_option( 'test_appuser' );
        $this->test_apppass       = $this->get_option( 'test_apppass' );
        $this->test_appsig        = $this->get_option( 'test_appsig' );
        $this->debug              = $this->get_option( 'debug' );
        $this->pa_admin_email     = $this->get_option( 'pa_admin_email' );
        $this->payment_process    = $this->get_option( 'payment_process' );
        $this->notify_url         = str_replace( 'https:', 'http:', add_query_arg( 'wc-api', 'WC_Dokan_Paypal_Ap_Gateway', home_url( '/' ) ) );

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        // Logs
        if ( 'yes' == $this->debug ) {
            $this->log = new WC_Logger();
        }

        // Payment listener/API hook
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );

        add_action( 'woocommerce_api_wc_dokan_paypal_ap_gateway', array( $this, 'check_ipn_response' ) );
        add_action( 'dokan-valid-paypal-adaptive-request', array( $this, 'successful_request' ) );
        add_action( 'admin_footer', array( $this, 'admin_script' ) );

        if ( ! $this->is_valid_for_use() ) {
            $this->enabled = false;
        }
    }

    /**
     * Check if this gateway is enabled and available in the user's country
     *
     * @access public
     * @return bool
     */
    function is_valid_for_use() {
        if ( !in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_paypal_supported_currencies', array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB', 'RUB' ) ) ) ) {
            return false;
        }

        return true;
    }

    /**
     * Admin Panel Options
     * - Options for bits like 'title' and availability on a country-by-country basis
     *
     * @since 1.0.0
     */
    public function admin_options() {
        ?>
        <h3><?php _e( 'Dokan PayPal Adaptive Payments', 'dokan' ); ?></h3>
        <p><?php _e( 'PayPal Adaptive works by sending payment to multiple user email', 'dokan' ); ?></p>
        <div style="display: none;" id="seller-percentage"><?php echo dokan_get_option( 'seller_percentage', 'dokan_selling', '90' ); ?></div>

        <?php if ( $this->is_valid_for_use() ) : ?>

            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table><!--/.form-table-->

        <?php else : ?>
            <div class="inline error"><p><strong><?php _e( 'Gateway Disabled', 'dokan' ); ?></strong>: <?php _e( 'PayPal Adaptive does not support your store currency.', 'dokan' ); ?></p></div>
        <?php
        endif;
    }

    /**
     * Display information in frontend
     * after checkout process button
     */
    public function payment_fields() {
        echo $this->get_option( 'description' );
    }

    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title'   => __( 'Enable/Disable', 'dokan' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable PayPal Adaptive', 'dokan' ),
                'default' => 'no'
            ),
            'title' => array(
                'title'       => __( 'Title', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'dokan' ),
                'default'     => __( 'PayPal Adaptive', 'dokan' ),
                'desc_tip'    => true,
            ),
            'description' => array(
                'title'       => __( 'Description', 'dokan' ),
                'type'        => 'textarea',
                'description' => __( 'This controls the description which the user sees during checkout.', 'dokan' ),
                'default'     => __( 'Pay via PayPal Adaptive; you can pay with your credit card if you don\'t have a PayPal account', 'dokan' )
            ),
            'pa_admin_email' => array(
                'title'       => __( 'PayPal Email', 'dokan' ),
                'type'        => 'email',
                'description' => __( 'Please enter your PayPal email address; this is needed in order to take payment.', 'dokan' ),
                'default'     => '',
                'desc_tip'    => true,
                'placeholder' => 'you@youremail.com'
            ),
            'payment_process' => array(
                'title'       => __( 'Payment System', 'dokan' ),
                'type'        => 'select',
                'description' => __( 'Please choose your payment system', 'dokan' ),
                'desc_tip'    => true,
                'default'     => 'chained',
                'options'     => array(
                    'chained'  => __( 'Chained Payment', 'dokan' ),
                    'parallel' => __( 'Parallel Payment', 'dokan' )
                )
            ),
            'single_mode' => array(
                'title'       => __( 'Mode', 'dokan' ),
                'label'       => __( 'Single Vendor Mode (Recommended)', 'dokan' ),
                'type'        => 'checkbox',
                'default'     => 'yes',
                'description' => sprintf( __( 'When using this mode, items from <strong>only one</strong> vendor can be purchased in a single order. Works only with <strong>chained payments</strong>. It\'s a good solution for marketplace as disputes/chargebacks will be handled by the vendor, not by admin.<br>Warning : <i>Do not use <b>Chained Payment </b> while <a href="%s" target="_blank">Vendor commission</a> is set to 100%%</i>', 'dokan' ), admin_url().'/admin.php?page=dokan-settings')
            ),
            'fees_payer' => array(
                'title'       => __( 'Fees Payer', 'dokan' ),
                'type'        => 'select',
                'description' => __( 'Please choose who will pay the fee', 'dokan' ),
                'desc_tip'    => true,
                'default'     => 'each',
                'options'     => array(
                    'each'  => __( 'Each receiver', 'dokan' ),
                    'admin' => __( 'Admin', 'dokan' ),
                    'seller' => __( 'Vendor', 'dokan' )
                )
            ),
            'send_shipping' => array(
                'title'       => __( 'Shipping details', 'dokan' ),
                'label'       => __( 'Send shipping details to PayPal instead of billing.', 'dokan' ),
                'type'        => 'checkbox'
            ),
            'testmode' => array(
                'title'       => __( 'PayPal sandbox', 'dokan' ),
                'type'        => 'checkbox',
                'label'       => __( 'Enable PayPal sandbox', 'dokan' ),
                'default'     => 'no',
                'description' => sprintf( __( 'PayPal sandbox can be used to test payments. Sign up for a developer account <a href="%s">here</a>.', 'dokan' ), 'https://developer.paypal.com/' ),
            ),
            'appuser' => array(
                'title'       => __( 'API Username', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'For this payment method your need an application credential', 'dokan' ),
                'default'     => '',
                'desc_tip'    => true,
                'placeholder' => 'username'
            ),
            'apppass' => array(
                'title'       => __( 'API Password', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'For this payment method your need an application credential', 'dokan' ),
                'default'     => '',
                'desc_tip'    => true,
                'placeholder' => 'password'
            ),
            'appsig' => array(
                'title'       => __( 'API Signature', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'For this payment method your need an application credential', 'dokan' ),
                'default'     => '',
                'desc_tip'    => true,
                'placeholder' => 'signature'
            ),
            'appid' => array(
                'title'       => __( 'App ID', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'For this payment method your need an application credential', 'dokan' ),
                'default'     => '',
                'desc_tip'    => true,
                'placeholder' => 'api appID'
            ),
            'test_appuser' => array(
                'title'       => __( 'Sandbox App Username', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'For this system please sign up in developer account and get your  application credential', 'dokan' ),
                'default'     => '',
                'desc_tip'    => true,
                'placeholder' => 'username'
            ),
            'test_apppass' => array(
                'title'       => __( 'Sandbox App Password', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'For this system please sign up in developer account and get your  application credential', 'dokan' ),
                'default'     => '',
                'desc_tip'    => true,
                'placeholder' => 'password'
            ),
            'test_appsig' => array(
                'title'       => __( 'Sandbox App Signature', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'For this system please sign up in developer account and get your  application credential', 'dokan' ),
                'default'     => '',
                'desc_tip'    => true,
                'placeholder' => 'signature'
            ),
            'max_error' => array(
                'title'       => __( 'Error Message', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'This is the error message displayed to a shopper when attempting to add too many vendor items to the cart due to PayPal limitation.', 'dokan' ),
                'default'     => __( 'Cart item quantity total exceeded - item not added to cart. Please checkout to purchase the items in your cart.', 'dokan' ),
                'desc_tip'    => true,
            ),
            'debug' => array(
                'title'       => __( 'Debug Log', 'woocommerce' ),
                'type'        => 'checkbox',
                'label'       => __( 'Enable logging', 'woocommerce' ),
                'default'     => 'no',
                'description' => sprintf( __( 'Log PayPal events, such as IPN requests, inside <code>woocommerce/logs/paypal-%s.txt</code>', 'woocommerce' ), sanitize_file_name( wp_hash( 'paypal' ) ) ),
            )
        );
    }

    public function admin_script( $script ) {
        ?>
        <script type="text/javascript">
            jQuery(function($) {

                $select_process_payment = $('#woocommerce_dokan_paypal_adaptive_payment_process');
                $check_single_mode = $('#woocommerce_dokan_paypal_adaptive_single_mode');
                $select_fees_payer = $('#woocommerce_dokan_paypal_adaptive_fees_payer');
                $seller_percentage = parseInt( $('#seller-percentage').text() );

                if( $select_process_payment.val() === 'parallel' ){
                    $check_single_mode.closest('tr').hide();
                    $select_fees_payer.closest('tr').hide();
                } else if ( 100 == $seller_percentage ) {
                    $select_fees_payer.closest('tr').find('option[value="admin"]').hide();
                }

                $select_process_payment.on('change', function() {
                    var self = $(this);

                    if ( self.val() === 'chained' ) {
                        self.closest('tr').next('tr').show();
                        $check_single_mode.prop('checked', true);
                        $select_fees_payer.closest('tr').find('option[value="seller"]').show();
                        $select_fees_payer.closest('tr').show();
                        if ( 100 > $seller_percentage ) {
                            $select_fees_payer.closest('tr').find('option[value="admin"]').show();
                        } else {
                            $select_fees_payer.closest('tr').find('option[value="admin"]').hide();
                        }

                    } else {
                        self.closest('tr').next('tr').hide();
                        $check_single_mode.prop('checked', false);
                        $select_fees_payer.closest('tr').hide();
                    }

                });

                $check_single_mode.on('change', function() {
                    if ( this.checked ) {
                        $(this).closest('tr').next('tr').find('option[value="seller"]').show();
                        $select_fees_payer.val('seller');
                    } else {
                        $(this).closest('tr').next('tr').find('option[value="seller"]').hide();
                        $select_fees_payer.val('each');
                    }
                });

            });
        </script>
        <?php
    }

    /**
     * Process the payment and return the result
     *
     * @access public
     * @param int     $order_id
     * @return array
     */
    function process_payment( $order_id ) {

        $receiver   = array();
        $payRequest = new \PayPal\Types\AP\PayRequest();
        $order      = new \WC_Order( $order_id );

        $sub_orders = get_children( array( 'post_parent' => $order_id, 'post_type' => 'shop_order' ) );

        if ( $sub_orders ) {

            $sum = 0;
            foreach ( $sub_orders as $key => $order_post ) {
                $seller_id        = dokan_get_seller_id_by_order( $order_post->ID );
                $seller_balance   = $this->get_seller_net_balance( $order_post->ID, $seller_id );
                $seller_pay_email = dokan_get_seller_withdraw_mail( $seller_id );

                if ( false === $seller_pay_email ) {
                    $seller_pay_email = get_user_by( 'id', $seller_id )->user_email;
                }

                $receiver[$key] = new \PayPal\Types\AP\Receiver();
                $receiver[$key]->amount = round( $seller_balance, wc_get_price_decimals() );
                $receiver[$key]->email  = $seller_pay_email;

                $sum += (float) $seller_balance;
            }

            if ( $this->payment_process == 'chained' ) {
                // if single seller mode enabled then we need to exit as we have multiple orders here
                if ( $this->single_mode == 'yes' ) {
                    throw new Exception( sprintf( __( 'You have products from multiple-vendor please choose products from Single vendor only', 'dokan' ) ) );
                }
                $admin_amount = (string) round( $order->get_total(), wc_get_price_decimals() );
            } else {
                $admin_amount = (string) ( round( (float) $order->get_total() - $sum, wc_get_price_decimals() ) );
            }
        } else {

            $seller_id        = dokan_get_seller_id_by_order( $order_id );
            $seller_balance   = $this->get_seller_net_balance( $order_id, $seller_id );
            $seller_pay_email = dokan_get_seller_withdraw_mail( $seller_id );

            if ( false === $seller_pay_email ) {
                $seller_pay_email = get_user_by( 'id', $seller_id )->user_email;
            }

            $receiver[0]         = new PayPal\Types\AP\Receiver();
            $receiver[0]->amount = round( $seller_balance, wc_get_price_decimals() );
            $receiver[0]->email  = $seller_pay_email;

            if ( $this->payment_process == 'chained' ) {

                if ( $this->single_mode == 'yes' ) {
                    $admin_amount        = (string) ( round( (float) $order->get_total() - (float) $seller_balance, wc_get_price_decimals() ) );
                    $receiver[0]->amount = (string) $order->get_total();
                } else {
                    $admin_amount = (string) round( $order->get_total(), wc_get_price_decimals() );
                }

            } else {
                $admin_amount = (string) ( round( (float) $order->get_total() - (float) $seller_balance, wc_get_price_decimals() ) );
            }
        }

        $count = count( $receiver );

        if ( $admin_amount > 0 ) {
            $receiver[$count]         = new PayPal\Types\AP\Receiver();
            $receiver[$count]->amount = $admin_amount;
            $receiver[$count]->email  = $this->pa_admin_email;
        }

        if ( $this->payment_process == 'chained' ) {

            if ( $this->single_mode == 'yes' ) {
                $receiver[0]->primary = 'true';
                if ( 'seller' == $this->fees_payer ) {
                    $payRequest->feesPayer = 'PRIMARYRECEIVER';
                }else if ( 'admin' == $this->fees_payer ) {
                    if ( $admin_amount > 0 ) {
                        $payRequest->feesPayer = 'SECONDARYONLY';
                    }
                }
            } elseif ( $admin_amount > 0 ) {
                //make admin the primary receiver
                $receiver[$count]->primary = 'true';
                if ( 'admin' == $this->fees_payer ) {
                    $payRequest->feesPayer = 'PRIMARYRECEIVER';
                }
                // else defaults to EACH receiver
            }
        }

        $this->add_log( 'Payment Process: ' . $this->payment_process . ' ------ Reciever list' . print_r( $receiver, true ) . '...' );

        $receiverList                   = new PayPal\Types\AP\ReceiverList( $receiver );
        $payRequest->receiverList       = $receiverList;

        $requestEnvelope                = new PayPal\Types\Common\RequestEnvelope( "en_US" );
        $payRequest->requestEnvelope    = $requestEnvelope;
        $payRequest->actionType         = "PAY";
        $payRequest->reverseAllParallelPaymentsOnError         = true;
        $payRequest->cancelUrl          = esc_url( $order->get_cancel_order_url() );
        $payRequest->returnUrl          = esc_url( $this->get_return_url( $order ) );
        $payRequest->currencyCode       = get_woocommerce_currency();
        $payRequest->ipnNotificationUrl = $this->notify_url;

        if ( 'yes' == $this->testmode ) {
            $sdkConfig = array(
                "mode"            => "sandbox",
                "acct1.UserName"  => $this->test_appuser,
                "acct1.Password"  => $this->test_apppass,
                "acct1.Signature" => $this->test_appsig,
                "acct1.AppId"     => "APP-80W284485P519543T"
            );
        } else {
            $sdkConfig = array(
                "mode"            => "live",
                "acct1.UserName"  => $this->appuser,
                "acct1.Password"  => $this->apppass,
                "acct1.Signature" => $this->appsig,
                "acct1.AppId"     => $this->appid
            );
        }

        $adaptivePaymentsService = new PayPal\Service\AdaptivePaymentsService( $sdkConfig );
        $payResponse             = $adaptivePaymentsService->Pay( $payRequest );

        if ( $payResponse->payKey ) {

            $shippingAddressInfo = new PayPal\Types\AP\ShippingAddressInfo();

            $shippingAddressInfo->addresseeName = dokan_get_prop( $order, 'billing_first_name' ) . ' ' . dokan_get_prop( $order, 'billing_last_name' );
            $shippingAddressInfo->street1       = dokan_get_prop( $order, 'billing_address_1' );
            $shippingAddressInfo->street2       = dokan_get_prop( $order, 'billing_address_2' );
            $shippingAddressInfo->city          = dokan_get_prop( $order, 'billing_city' );
            $shippingAddressInfo->zip           = dokan_get_prop( $order, 'billing_postcode' );
            $shippingAddressInfo->state         = $this->get_paypal_state( dokan_get_prop( $order, 'billing_country' ), dokan_get_prop( $order, 'billing_state' ) );
            $shippingAddressInfo->country       = dokan_get_prop( $order, 'billing_country' );

            if ( 'yes' == $this->send_shipping ) {
                $shippingAddressInfo->addresseeName = dokan_get_prop( $order, 'shipping_first_name' ) . ' ' . dokan_get_prop( $order, 'shipping_last_name' );
                $shippingAddressInfo->street1       = dokan_get_prop( $order, 'shipping_address_1' );
                $shippingAddressInfo->street2       = dokan_get_prop( $order, 'shipping_address_2' );
                $shippingAddressInfo->city          = dokan_get_prop( $order, 'shipping_city' );
                $shippingAddressInfo->zip           = dokan_get_prop( $order, 'shipping_postcode' );
                $shippingAddressInfo->state         = $this->get_paypal_state( dokan_get_prop( $order, 'shipping_country' ), dokan_get_prop( $order, 'shipping_state' ) );
                $shippingAddressInfo->country       = dokan_get_prop( $order, 'shipping_country' );
            }

            $so                                      = new PayPal\Types\AP\SenderOptions();
            $so->shippingAddress                     = $shippingAddressInfo;

            $re                                      = new PayPal\Types\Common\RequestEnvelope( 'en_US' );
            $setPaymentOptionsRequest                = new PayPal\Types\AP\SetPaymentOptionsRequest( $re, $payResponse->payKey );
            $setPaymentOptionsRequest->senderOptions = $so;
            $paymentOptionRequest                    = $adaptivePaymentsService->SetPaymentOptions( $setPaymentOptionsRequest );
        }

        $this->add_log( 'Payment Response: ' . print_r( $payResponse, true ) );

        if ( 'Failure' == $payResponse->responseEnvelope->ack ) {
            throw new Exception( sprintf( __( 'Paypal Adaptive Error : %s , Error Code : %d', 'dokan' ), $payResponse->error[0]->message, $payResponse->error[0]->errorId ) );
        }
        // update paykey reference to find out
        update_post_meta( $order->id, '_dokan_pap_key', $payResponse->payKey );

        if ( 'yes' == $this->testmode ) {
            $paypal_url = "https://www.sandbox.paypal.com/webscr?cmd=_ap-payment&paykey=" . $payResponse->payKey;
        } else {
            $paypal_url = "https://www.paypal.com/webscr?cmd=_ap-payment&paykey=" . $payResponse->payKey;
        }

        // Return thankyou redirect
        return array(
            'result'   => 'success',
            'redirect' => $paypal_url
        );
    }

    /**
     * Get the state to send to paypal
     * @param  string $cc
     * @param  string $state
     * @return string
     */
    public function get_paypal_state( $cc, $state ) {
        if ( 'US' === $cc ) {
            return $state;
        }

        $states = WC()->countries->get_states( $cc );

        if ( isset( $states[ $state ] ) ) {
            return $states[ $state ];
        }

        return $state;
    }

    /**
     *  Check if ipn request is valid or not
     *
     *  @return boolean true/false
     */
    function check_ipn_response() {

        if ( 'yes' == $this->testmode ) {
            $config = array( 'mode' => 'sandbox' );
        } else {
            $config = array( 'mode' => 'live' );
        }

        $ipnMessage = new PayPal\IPN\PPIPNMessage( '', $config );

        if ( $ipnMessage->validate() ) {

            $this->add_log( 'IPN Response: ' . print_r( $ipnMessage->getRawData(), true ) );

            do_action( "dokan-valid-paypal-adaptive-request" );
        } else {

            $this->add_log( 'Received invalid response from PayPal Adaptive Payment' );

            if ( is_wp_error( $ipnMessage ) ) {
                $this->add_log( 'Error response: ' . $ipnMessage->get_error_message() );
            }
        }
    }

    /**
     * Do succesfull request...
     *
     * @return void
     */
    function successful_request() {

        $posted = $_POST;
        $paykey = isset( $posted['pay_key'] ) ? $posted['pay_key'] : '';

        if ( !$paykey ) {
            $this->add_log( __( 'No pay key found, abort.', 'dokan' ) );
            exit;
        }

        $order_id = $this->get_order_by_paykey( $paykey );

        if ( !$order_id ) {
            $this->add_log( sprintf( __( 'No order id found by pay key: %s', 'dokan' ), $paykey ) );
            exit;
        }

        $order = new WC_Order( $order_id );

        // yet another check to make sure
        if ( !isset( $order->id ) || empty( $order->id ) || absint( $order->id ) <= 0 ) {
            $this->add_log( sprintf( __( 'No order found by pay key: %s', 'dokan' ), $paykey ) );
            exit;
        }

        $this->add_log( "Payment post data: \n" . print_r( $posted, true ) . "\n-----------\n" );

        $status = strtolower( $posted['status'] );

        switch ( $status ) {
        case 'completed' :

            // Check order not already completed
            if ( $order->status == 'completed' ) {
                $this->add_log( 'Aborting, Order #' . $order->id . ' is already complete.' );
                exit;
            }

            $order->add_order_note( __( 'IPN payment completed', 'dokan' ) );
            $order->payment_complete();

            if ( !empty( $posted['sender_email'] ) ) {
                update_post_meta( $order_id, "Payer's PayPal address", $posted['sender_email'] );
            }

            if ( !empty( $posted['fees_payer'] ) ) {
                update_post_meta( $order_id, 'Fee Payer', $posted['fees_payer'] );
            }

            if ( !empty( $posted['payment_request_date'] ) ) {
                update_post_meta( $order_id, "PayPal's Date Stamp", $posted['payment_request_date'] );
            }

            break;

        case 'error' :
        case 'reversalerror':
        case 'denied' :
        case 'expired' :
        case 'failed' :
        case 'voided' :
            // Order failed
            $order->update_status( 'failed', sprintf( __( 'Payment %s via IPN.', 'dokan' ), strtolower( $posted['status'] ) ) );

            break;

        case 'incomplete':
        case 'processing':
        case 'pending':
        case 'created' :
            // order pending
            $order->update_status( 'on-hold', sprintf( __( 'Payment %s via IPN. Order is pending completion. PayPal will notify this site of further status updates. Or check your PayPal account for further information', 'dokan' ), strtolower( $_POST['status'] ) ) );

            break;

        case "refunded" :

            // Handle full refunds, not partial refunds
            if ( $order->get_total() == ( $posted['mc_gross'] * -1 ) ) {

                // Mark order as refunded
                $order->update_status( 'refunded', sprintf( __( 'Payment %s via IPN.', 'dokan' ), strtolower( $posted['status'] ) ) );

                $mailer  = WC()->mailer();
                $message = $mailer->wrap_message( __( 'Order refunded/reversed', 'dokan' ), sprintf( __( 'Order %s has been marked as refunded - PayPal reason code: %s', 'dokan' ), $order->get_order_number(), $posted['reason_code'] ) );

                $mailer->send( get_option( 'admin_email' ), sprintf( __( 'Payment for order %s refunded/reversed', 'dokan' ), $order->get_order_number() ), $message );
            }

            break;

        case "reversed" :
        case "chargeback" :

            // Mark order as refunded
            $order->update_status( 'refunded', sprintf( __( 'Payment %s via IPN.', 'dokan' ), strtolower( $posted['status'] ) ) );

            $mailer  = WC()->mailer();
            $message = $mailer->wrap_message( __( 'Order refunded/reversed', 'dokan' ), sprintf( __( 'Order %s has been marked as refunded - PayPal reason code: %s', 'dokan' ), $order->get_order_number(), $posted['reason_code'] ) );
            $mailer->send( get_option( 'admin_email' ), sprintf( __( 'Payment for order %s refunded/reversed', 'dokan' ), $order->get_order_number() ), $message );

            break;

        default :
            // No action
            break;
        }
    }


    /**
     * Get seller net Balance from dokan_order_table
     *
     * @global $wpdb
     * @param integer $order_id
     * @param integer $seller_id
     * @return array $results
     */
    function get_seller_net_balance( $order_id, $seller_id ) {
        global $wpdb;

        $table  = $wpdb->prefix . "dokan_orders";
        $result = $wpdb->get_var( $wpdb->prepare( "SELECT `net_amount` FROM $table WHERE `order_id` = %d AND `seller_id` = %d", $order_id, $seller_id ) );

        return $result;
    }

    /**
     * Get order by PayPal adaptive pay key
     *
     * @global $wpdb $wpdb
     * @param string  $paykey
     * @return boolean|int
     */
    function get_order_by_paykey( $paykey ) {
        global $wpdb;

        $sql      = $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_dokan_pap_key' AND meta_value = %s", $paykey );
        $order_id = $wpdb->get_var( $sql );

        if ( !$order_id ) {
            return false;
        }

        return $order_id;
    }

    /**
     * Add to log file if debug enabled
     *
     * @param string  $message
     */
    function add_log( $message ) {
        if ( 'yes' == $this->debug ) {
            $this->log->add( 'dokan-paypalap', $message );
        }
    }

}
