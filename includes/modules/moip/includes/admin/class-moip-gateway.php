<?php
/**
 * No cheating please
 */
if ( ! defined( 'WPINC' ) ) exit;

require_once MOIP_LIB . '/vendor/autoload.php';

use Moip\Moip;
use Moip\Auth\OAuth;
use Moip\Auth\Connect;
use DokanPro\Modules\Subscription\Helper;
use DokanPro\Modules\Subscription\SubscriptionPack;

/**
 * Dokan Moip Gateway
 */
class Dokan_Moip_Connect extends WC_Payment_Gateway {
    /**
     * Constructor method
     */
    public function __construct() {
        $this->set_settings();
        $this->init_hooks();
    }

    /**
     * Set settings
     *
     * @since 1.0
     */
    protected function set_settings() {
        $this->id                 = 'dokan-moip-connect';
        $this->method_title       = __( 'Dokan Wirecard Connect', 'dokan' );
        $this->method_description = __( 'Have your customers pay with credit card.', 'dokan' );
        $this->has_fields         = true;
        $this->supports           = array( 'products' );

        // load form fields
        $this->init_form_fields();

        // load settings
        $this->init_settings();

        // get settings value
        $this->title           = $this->get_option( 'title' );
        $this->description     = $this->get_option( 'description' );
        $this->enabled         = $this->get_option( 'enabled' );
        $this->testmode        = $this->get_option( 'testmode' );
        $this->moip_fee        = $this->get_option( 'moip_fee' );
        $this->base_url        = $this->testmode == 'no' ? 'https://api.moip.com.br' : 'https://sandbox.moip.com.br';
        $this->token           = $this->testmode == 'no' ? $this->get_option( 'production_token' ) : $this->get_option( 'test_token' );
        $this->key             = $this->testmode == 'no' ? $this->get_option( 'production_key' ) : $this->get_option( 'test_key' );
        $this->public_key      = $this->testmode == 'no' ? $this->get_option( 'production_public_key' ) : $this->get_option( 'test_public_key' );
    }

    /**
     * Set form fields
     *
     * @return void;
     */
    public function init_form_fields() {
        $this->form_fields = $this->load_form_fields();
    }

    /**
     * Get form filds
     *
     * @return array
     */
    public function load_form_fields() {
        $test_url       = 'https://conta-sandbox.moip.com.br/configurations/api_credentials';
        $production_url = 'https://conta.moip.com.br/configurations/api_credentials';

        return array(
            'enabled' => array(
                'title'       => __( 'Enable/Disable', 'dokan' ),
                'label'       => __( 'Enable Wirecard', 'dokan' ),
                'type'        => 'checkbox',
                'description' => '',
                'default'     => 'no'
            ),
            'title' => array(
                'title'       => __( 'Title', 'dokan' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'dokan' ),
                'default'     => __( 'Wirecard Credit Card', 'dokan' )
            ),
            'description' => array(
                'title'       => __( 'Description', 'dokan' ),
                'type'        => 'textarea',
                'description' => __( 'This controls the description which the user sees during checkout.', 'dokan' ),
                'default'     => 'Pay with your credit card via Wirecard.'
            ),
            'moip_fee' => array(
                'title'       => __( 'Wirecard Fee', 'dokan' ),
                'type'        => 'select',
                'options'     => array(
                    'admin'   => __( 'Admin', 'dokan' ),
                    'vendor'  => __( 'Vendor', 'dokan' ),
                ),
                'description' => __( 'Select who will bear the Wirecard transection fee.', 'dokan' ),
                'default'     => 'vendor'
            ),
            'testmode' => array(
                'title'       => __( 'Test mode', 'dokan' ),
                'label'       => __( 'Enable Test Mode', 'dokan' ),
                'type'        => 'checkbox',
                'description' => __( 'Place the payment gateway in test mode using test API keys.', 'dokan' ),
                'default'     => 'yes'
            ),
            'production-credentials-title' => array(
                'title' => __( 'Production credentials', 'dokan' ),
                'type'  => 'title',
            ),
            'production_token' => array(
                'title'       => __( 'Production Token', 'dokan' ),
                'type'        => 'text',
                'description' => sprintf( '<a href="%s" target="_blank">%s</a>', $production_url, __( 'Get your producttion API token from your wirecard account. (Required**)', 'dokan' ) ),
                'default'     => ''
            ),
            'production_key' => array(
                'title'       => __( 'Production Key', 'dokan' ),
                'type'        => 'text',
                'description' => sprintf( '<a href="%s" target="_blank">%s</a>', $production_url, __( 'Get your producttion API keys from your wirecard account. (Required**)', 'dokan' ) ),
                'default'     => ''
            ),
            'production_public_key' => array(
                'title'       => __( 'Production Public Key', 'dokan' ),
                'type'        => 'textarea',
                'description' => sprintf( '<a href="%s" target="_blank">%s</a>', $production_url, __( 'Get your producttion public API keys from your wirecard account. (Required**)', 'dokan' ) ),
                'default'     => ''
            ),
            'test-credentials-title' => array(
                'title' => __( 'Test credentials', 'dokan' ),
                'type'  => 'title',
            ),
            'test_token' => array(
                'title'       => __( 'Test Token', 'dokan' ),
                'type'        => 'text',
                'description' => sprintf( '<a href="%s" target="_blank">%s</a>', $test_url, __( 'Get your test API token from your wirecard account. (Required**)', 'dokan' ) ),
                'default'     => ''
            ),
            'test_key' => array(
                'title'       => __( 'Test Key', 'dokan' ),
                'type'        => 'text',
                'description' => sprintf( '<a href="%s" target="_blank">%s</a>', $test_url, __( 'Get your test API keys from your wirecard account. (Required**)', 'dokan' ) ),
                'default'     => ''
            ),
            'test_public_key' => array(
                'title'       => __( 'Test Public Key', 'dokan' ),
                'type'        => 'textarea',
                'description' => sprintf( '<a href="%s" target="_blank">%s</a>', $test_url, __( 'Get your test public API keys from your wirecard account. (Required**)', 'dokan' ) ),
                'default'     => ''
            ),
        );
    }

    /**
     * Init all the hooks
     *
     * @return void
     */
    public function init_hooks() {
        $this->init_actions();
    }

    /**
     * Init all the action hooks
     *
     * @return void
     */
    public function init_actions() {
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'get_moip_access_token' ) );
        add_filter( 'woocommerce_credit_card_form_fields', array( $this, 'add_cpf_field' ), 10, 2 );
        // include js
        add_action( 'wp_enqueue_scripts', array( $this, 'include_moip_js' ) );
    }

    /**
     * Get moip access token
     *
     * @return void
     */
    public function get_moip_access_token() {
        $post_data = wp_unslash( $_POST );
        $field_key = "woocommerce_{$this->id}_";

        if ( ! isset( $post_data["{$field_key}enabled"] ) || $post_data["{$field_key}enabled"] != 1 ) {
            return;
        }

        $key        = $post_data["{$field_key}testmode"] == 1 ? wc_clean( $post_data["{$field_key}test_key"] ) : wc_clean( $post_data["{$field_key}production_key"] );
        $token      = $post_data["{$field_key}testmode"] == 1 ? wc_clean( $post_data["{$field_key}test_token"] ) : wc_clean( $post_data["{$field_key}production_token"] );
        $public_key = $post_data["{$field_key}testmode"] == 1 ? wc_clean( $post_data["{$field_key}test_public_key"] ) : wc_clean( $post_data["{$field_key}production_public_key"] );
        $base_url   = $post_data["{$field_key}testmode"] == 1 ? esc_url( 'https://sandbox.moip.com.br/v2/channels' ) : esc_url( 'https://api.moip.com.br/v2/channels' );

        if ( empty( $key ) || empty( $token ) || empty( $public_key ) ) {
            return;
        }

        $body = array(
            'name'        => get_bloginfo( 'name' ),
            'description' => get_bloginfo( 'description' ),
            'site'        => get_site_url(),
            'redirectUri' => dokan_get_navigation_url( 'settings/payment' ) . '?moip=yes'
        );

        $headers = array(
            'Content-Type: application/json',
            'Cache-Control: no-cache',
            'Authorization: Basic ' . base64_encode( $token . ':' . $key ),
        );

        $curl = curl_init();

        curl_setopt_array( $curl, array(
          CURLOPT_URL => $base_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => json_encode( $body ),
          CURLOPT_HTTPHEADER => $headers,
        ) );

        $response = curl_exec( $curl );
        $error    = curl_error( $curl );

        curl_close( $curl );

        if ( $error ) {
            new WP_Error( __( 'Something went wrong:', 'dokan' ) . $error );
        }

        $response = json_decode( $response );

        if ( isset( $response->ERROR ) ) {
            return wp_send_json_error( $response->ERROR );
        }

        if ( ! isset( $response->id, $response->secret, $response->accessToken ) ) {
            return;
        }

        update_option( 'moip_app_id', $response->id );
        update_option( 'moip_secret', $response->secret );
        update_option( 'moip_access_token', $response->accessToken );
    }

    /**
     * Add cpf field in the checkout page
     *
     * @param array $fileds
     * @param string $id
     *
     * @return array
     */
    public function add_cpf_field( $fields, $id ) {
        if ( $this->id == $id ) {
            $fields['cpf_field'] = '<p class="form-row form-row-wide">
                <label for="billing_cpf" class="">' . esc_html__( 'CPF Number', 'dokan' ) . '&nbsp;<span class="optional"><span style="color:red">*</span></span></label>

                <input type="number" style="padding: 10px; font-size:16px" class="input-text" name="billing_cpf" id="billing_cpf" placeholder="' . esc_html__( 'CPF Number', 'dokan' ) . '">
            </p>';
        }

        return $fields;
    }

    /**
     * Include all the scripts
     *
     * @return void
     */
    public function include_moip_js() {
        if ( ! is_checkout() ) {
            return;
        }

        wp_enqueue_script( 'dokan-moip', MOIP_ASSETS . '/js/moip.js' , array(), false, false );

        $moip_params = array(
            'public_key'   => $this->public_key,
            'card_error'   => __( 'Card number is not valid', 'dokan' ),
            'expriy_error' => __( 'Card expriy date is not valid', 'dokan' ),
            'cvc_error'    => __( 'Card CVC number is not valid', 'dokan' ),
        );

        wp_enqueue_script( 'dokan-moip-checkout', MOIP_ASSETS . '/js/moip-checkout.js' , array( 'jquery' ), false, false );
        wp_localize_script( 'dokan-moip', 'moip_params', $moip_params );
    }

    /**
     * Payment form on checkout page
     *
     * @return void
     */
    public function payment_fields() {
        ?>
        <fieldset>
            <?php
            if ( $this->description ) {
                echo wpautop( esc_html( $this->description ) );
            }
            if ( $this->testmode == 'yes' ) {
                echo '<p>' . __( 'TEST MODE ENABLED. In test mode, you can use the card number 4012001037141112 with any CVC and a valid expiration date.', 'dokan' ) . '</p>';
            }
            ?>
            <p class="form-row form-row-wide">

                <?php
                    $cc_form = new WC_Payment_Gateway_CC;
                    $cc_form->id       = $this->id;
                    $cc_form->supports = $this->supports;
                    $cc_form->form();
                ?>

            </p>
            <div class="clear"></div>
        </fieldset>
        <?php
    }

    /**
     * Process payment
     *
     * @param  int $order_id
     *
     * @return array
     */
    public function process_payment( $order_id ) {
        global $woocommerce, $wpdb;

        $access_token = get_option( 'moip_access_token' );

        $order         = wc_get_order( $order_id );
        $all_withdraws = array();

        try {
            /* Check amount*/
            $order_total = dokan_get_prop( $order, 'order_total', 'get_total' );

            if ( $order_total * 100 < 50 ) {
                throw new Exception( __( 'Minimum order total is 0.50', 'dokan' ) );
            }

            if ( $this->is_subscription_order( $order ) ) {
                $this->process_seller_subscriptoin_payment( $order_id, $order, $access_token );
            } else {
                $this->process_seller_payment( $order_id, $order, $access_token );
            }

        } catch( Exception $e ) {
            throw new Exception( __( 'Something went wrong:', 'dokan' ) . $e->getMessage() );
        }

        return array(
            'result'   => 'success',
            'redirect' => $this->get_return_url( $order )
        );
    }

    /**
     * Process seller subscription payment
     *
     * @return void
     */
    public function process_seller_subscriptoin_payment( $order_id, $order, $access_token ) {

        $currency     = strtolower( get_woocommerce_currency() );
        $order_desc   = sprintf( __( '%s - Order %s', 'dokan' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );

        if ( ! $order ) {
            throw new Exception( __( 'No orders found to process!', 'dokan' ) );
        }

        // We assume that if a subscription product added into a cart then no other product doesn't exist in cart so we get only one product
        $order_items        = $order->get_items();
        $product_pack_item  = reset( $order_items );
        $product_pack       = wc_get_product( $product_pack_item->get_product_id() );
        $customer_user_id   = $order->get_customer_id();
        $order_total        = round( $order->get_total(), 2 );
        $dokan_subscription = dokan()->subscription->get( $product_pack->get_id() );

        if ( $dokan_subscription->is_recurring() ) {
            require_once MOIP_INC . '/admin/class-moip-subscription.php';
            // If reccuring pack
            $subscription_interval = $dokan_subscription->get_recurring_interval();
            $subscription_period   = $dokan_subscription->get_period_type();
            $subscription_length   = $dokan_subscription->get_period_length();
            $trial_details         = array(
                'days'             => $dokan_subscription->is_trial() ? $dokan_subscription->get_trial_period_length() : 0,
                'is_enabled'       => $dokan_subscription->is_trial()
            );

            // if vendor has already used a trial pack, then make trial to a normal recurring pack
            if ( Helper::has_used_trial_pack( get_current_user_id() ) ) {
                $trial_details['days']       = 0;
                $trial_details['is_enabled'] = false;
            }

            $moip_subscriptoin = new Dokan_Moip_Subscription();

            $plan_id = $moip_subscriptoin->create_plan( $product_pack, $subscription_interval, strtoupper( $subscription_period ), $subscription_length, $trial_details );

            if ( $plan_id ) {
                $subscription_code = $moip_subscriptoin->create_subscription( $order, $plan_id );
            }

            if ( empty( $subscription_code ) ) {
                throw new Exception( __( 'Subscripton can\'t be created', 'dokan' ) );
            }

            $add_s = ( $subscription_interval != 1 ) ? 's' : '';

            update_user_meta( $customer_user_id, 'product_package_id', $product_pack->get_id() );
            update_user_meta( $customer_user_id, 'product_order_id', $order_id );
            update_user_meta( $customer_user_id, 'product_no_with_pack', get_post_meta( $product_pack->get_id(), '_no_of_product', true ) );
            update_user_meta( $customer_user_id, 'product_pack_startdate', date( 'Y-m-d H:i:s' ) );
            update_user_meta( $customer_user_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', strtotime( "+" . $subscription_interval . " " . $subscription_period . "" . $add_s ) ) );
            update_user_meta( $customer_user_id, 'can_post_product', '1' );
            update_user_meta( $customer_user_id, '_customer_recurring_subscription', 'active' );

            // make all the existing product publish if not
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

        } else {
            try {
                // get access token
                if ( $this->testmode == 'yes' ) {
                    $moip = new Moip( new OAuth( $access_token ), Moip::ENDPOINT_SANDBOX );
                } else {
                    $moip = new Moip( new OAuth( $access_token ), Moip::ENDPOINT_PRODUCTION );
                }

                // get the moip customer holder data
                $moip_data = $this->set_moip_customer_holder( $moip, $order );

                if ( empty( $moip_data ) ) {
                    throw new Exception( __( 'Moip data is not found', 'dokan' ) );
                }

                // get all the order items and add to moip_order item
                $moip_order = $moip->orders()->setOwnId( uniqid() );
                $items = $order->get_items();

                // we always need to pass the quantity as 1
                $quantity = 1;

                foreach ( $items as $item ) {
                    $moip_order->addItem( $item->get_product_id(), $quantity, 'sku1', (int) ( $item->get_total() + $item->get_total_tax() ) * 100 );
                }

                // Creating an order and splitting payment using 'addReceiver' method
                // Here we're setting a secondary account to receive vendor commission
                $moip_order->setCustomer( $moip_data['customer'] )
                ->create();

                if ( ! isset( $_POST['moip_hash'] ) || empty( $_POST['moip_hash'] ) ) {
                    throw new Exception( __( 'Credit card nout found', 'dokan' ) );
                }

                // customer hashed credit card number
                $card_number = wc_clean( $_POST['moip_hash'] );

                $payment = $moip_order->payments()
                ->setCreditCardHash( $card_number, $moip_data['holder'] )
                ->execute();

                $payment_id = $payment->getId();

                if ( ! $payment_id ) {
                    throw new Exception( __( 'Payment id not found', 'dokan' ) );
                }

                $pack_validity = get_post_meta( $product_pack->get_id(), '_pack_validity', true );
                update_user_meta( $customer_user_id, 'product_package_id', $product_pack->get_id() );
                update_user_meta( $customer_user_id, 'product_order_id', $order_id );
                update_user_meta( $customer_user_id, 'product_no_with_pack', get_post_meta( $product_pack->get_id(), '_no_of_product', true ) );
                update_user_meta( $customer_user_id, 'product_pack_startdate', date( 'Y-m-d H:i:s' ) );
                update_user_meta( $customer_user_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', strtotime( "+$pack_validity days" ) ) );
                update_user_meta( $customer_user_id, 'can_post_product', '1' );
                update_user_meta( $customer_user_id, '_customer_recurring_subscription', '' );

                // make all the existing product publish if not
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

            } catch ( Exception $e ) {
                $order->add_order_note( sprintf( __( 'Moip Payment Error: %s', 'dokan' ), $e->getMessage() ) );

                throw new Exception( $e->getMessage() );
            }
        }
    }

    /**
     * Process seller payment
     *
     * @param  int $order_id
     * @param  object $order
     * @param  string $access_token
     * @return void
     */
    public function process_seller_payment( $order_id, $order, $access_token ) {
        $currency     = strtolower( get_woocommerce_currency() );
        $order_desc   = sprintf( __( '%s - Order %s', 'dokan' ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() );
        $has_suborder = get_post_meta( $order_id, 'has_sub_order', true );
        $all_orders   = array();

        // put orders in an array
        // if has sub-orders, pick only sub-orders
        // if it's a single order, get the single order only
        if ( $has_suborder == '1' ) {
            $sub_orders = get_children( array( 'post_parent' => $order_id, 'post_type' => 'shop_order' ) );

            foreach ( $sub_orders as $order_post ) {
                $sub_order    = wc_get_order( $order_post->ID  );
                $all_orders[] = $sub_order;
            }

        } else {
            $all_orders[] = $order;
        }

        if ( ! $all_orders ) {
            throw new Exception( __( 'No orders found to process!', 'dokan' ) );
        }

        // get access token
        if ( $this->testmode == 'yes' ) {
            $moip = new Moip( new OAuth( $access_token ), Moip::ENDPOINT_SANDBOX );
        } else {
            $moip = new Moip( new OAuth( $access_token ), Moip::ENDPOINT_PRODUCTION );
        }

        // get moip customer holder data
        $moip_data = $this->set_moip_customer_holder( $moip, $order );

        if ( empty( $moip_data ) ) {
            throw new Exception( __( 'Moip data is not found', 'dokan' ) );
        }

        foreach ( $all_orders as $tmp_order ) {
            $tmp_order_id = dokan_get_prop( $tmp_order, 'id' );
            $seller_id    = dokan_get_seller_id_by_order( $tmp_order_id );
            $do_order     = $this->get_dokan_order( $tmp_order_id, $seller_id );

            // in-case we can't find the order
            if ( ! $do_order ) {
                throw new Exception( __( 'Something went wrong and the order can not be processed!', 'dokan' ) );
            }

            $fee               = floatval( $do_order->order_total ) - floatval( $do_order->net_amount );
            $order_total       = round( $do_order->order_total, 2 );
            $application_fee   = round( $fee, 2 );
            $vendor_commission = ( $order_total - $application_fee ) * 100;

            // get all the order items and add to moip_order item
            $moip_order = $moip->orders()->setOwnId( uniqid() );
            $items = $tmp_order->get_items();

            // we always need to pass the quantity as 1
            $quantity = 1;

            foreach ( $items as $item ) {
                $moip_order->addItem( $item->get_product_id(), $quantity, 'sku1', (int) $order_total * 100 );
            }

            $vendor_id           = dokan_get_seller_id_by_order( $tmp_order->get_id() );
            $moip_vendor_account = get_user_meta( $vendor_id, 'vendor_moip_account', true );
            $moip_fee_bearer     = $this->moip_fee == 'vendor' ? true : false;

            // Creating an order and splitting payment using 'addReceiver' method
            // Here we're setting a secondary account to receive vendor commission
            $moip_order->setCustomer( $moip_data['customer'] )
            ->addReceiver( $moip_vendor_account, 'SECONDARY', $vendor_commission, null, $moip_fee_bearer )
            ->create();

            if ( ! isset( $_POST['moip_hash'] ) || empty( $_POST['moip_hash'] ) ) {
                throw new Exception( __( 'Credit card nout found', 'dokan' ) );
            }

            // customer hashed credit card number
            $card_number = wc_clean( $_POST['moip_hash'] );

            $payment = $moip_order->payments()
            ->setCreditCardHash( $card_number, $moip_data['holder'] )
            ->execute();

            $payment_id = $payment->getId();

            if ( ! $payment_id ) {
                throw new Exception( __( 'Payment id not found', 'dokan' ) );
            }

            $order->payment_complete( $payment_id );

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
        }

        $this->process_seller_withdraws( $all_withdraws );
    }

    /**
     * Format moip customer and holder data
     *
     * @param object $moip
     * @param object $order
     *
     * @return array
     */
    public function set_moip_customer_holder( $moip, $order ) {
        // create customer info
        $customer_info = array();
        $customer_info['full_name']    = $order->get_formatted_billing_full_name();
        $customer_info['email']        = $order->get_billing_email();
        $customer_info['birthdate']    = '';
        $customer_info['tax_document'] = wc_clean( $_POST['billing_cpf'] );
        $customer_info['phone_prefix'] = substr( $order->get_billing_phone(), 0, 2 );
        $customer_info['phone_sufix']  = substr( $order->get_billing_phone(), 2 );

        // todo
        // add mandatory phone, tax, birthday fields in the checkout form
        $customer = $moip->customers()->setOwnId( uniqid() )
                ->setFullname( $customer_info['full_name'] )
                ->setEmail( $customer_info['email'] )
                ->setBirthDate( '1988-12-30' )
                ->setTaxDocument( $customer_info['tax_document'] )
                ->setPhone( $customer_info['phone_prefix'], $customer_info['phone_sufix'] )
                ->create();

        $holder = $moip->holders()
                ->setFullname( $customer_info['full_name'] )
                ->setBirthDate( '1988-12-30' )
                ->setTaxDocument( $customer_info['tax_document'] )
                ->setPhone( $customer_info['phone_prefix'], $customer_info['phone_sufix'] );

        $moip_data = array();
        $moip_data['customer'] = $customer;
        $moip_data['holder']   = $holder;

        return $moip_data;
    }

    /**
     * Process seller withdraws
     *
     * @param  array $all_withdraws
     *
     * @return void
     */
    public function process_seller_withdraws( $all_withdraws ) {
        $IP       =  dokan_get_client_ip();
        $withdraw = new Dokan_Withdraw();

        foreach ( $all_withdraws as $withdraw_data ) {
            $data = array(
                'date'    => current_time( 'mysql' ),
                'status'  => 1,
                'method'  => 'dokan-moip-connect',
                'notes'   => sprintf( __( 'Order %d payment auto paid via Dokan Wirecard', 'dokan' ), $withdraw_data['order_id'] ),
                'ip'      => $IP
            );

            $data = array_merge( $data, $withdraw_data );
            $withdraw->insert_withdraw( $data );
        }
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
     * Validate the form
     *
     * @return boolean
     */
    public function validate_fields() {
        if ( empty( $_POST['dokan-moip-connect-card-number'] ) || empty( $_POST['dokan-moip-connect-card-cvc'] ) || empty( $_POST['dokan-moip-connect-card-expiry'] ) ) {
            throw new Exception( __( 'Please enter card details', 'dokan' ) );
        }

        if ( empty( $_POST['billing_phone'] ) ) {
            throw new Exception( __( 'Phone number is required', 'dokan' ) );
        }

        if ( empty( $_POST['billing_cpf'] ) ) {
            throw new Exception( __( 'CPF number is required', 'dokan' ) );
        }

        if ( strlen( $_POST['billing_cpf'] ) !== 11 ) {
            throw new Exception( __( 'CPF number must be 11 character long', 'dokan' ) );
        }
    }

    /**
    * Check is subscription order
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
}
