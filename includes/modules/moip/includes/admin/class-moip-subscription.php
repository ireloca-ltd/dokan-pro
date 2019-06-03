<?php
/**
 * No cheating please
 */
if ( ! defined( 'WPINC' ) ) exit;

require_once MOIP_INC . '/admin/interface-moip-subscription.php';

/**
 * Moip subscripton class
 */
class Dokan_Moip_Subscription implements Moip_Subscription_Interface {
    /**
     * Hold app key
     * @var string
     */
    protected $key;

    /**
     * Hold app token
     *
     * @var string
     */
    protected $token;

    /**
     * Hold app settings
     *
     * @var object
     */
    protected $settings;

    /**
     * Hold base_url
     *
     * @var string
     */
    protected $base_url;

    /**
     * Constructor method
     */
    public function __construct() {
        $this->settings = get_option('woocommerce_dokan-moip-connect_settings');
        $this->key      = $this->settings['testmode'] == 'no' ? $this->settings['production_key'] : $this->settings['test_key'];
        $this->token    = $this->settings['testmode'] == 'no' ? $this->settings['production_token'] : $this->settings['test_token'];
        $this->base_url = $this->settings['testmode'] == 'no' ? 'https://api.moip.com.br/assinaturas/v1' : 'https://sandbox.moip.com.br/assinaturas/v1';
    }

    /**
     * Create moip plan
     *
     * @param  object $product
     * @param  int $subscription_interval
     * @param  string $subscription_period
     * @param  int $subscription_length
     *
     * @return void
     */
    public function create_plan( $product, $subscription_interval, $subscription_period, $subscription_length, $trail_details = [] ) {

        $base_url = $this->base_url . '/plans';

        // if dokan billing cycle stop sets to never, set subscription_length to 999 (unlimited)
        if ( empty( $subscription_length ) ) {
            $subscription_length = 999;
        }

        $plan_body = array(
            'code'           => $product->get_id(),
            'name'           => $product->get_name(),
            'description'    => $product->get_description(),
            'amount'         => $product->get_price() * 100,
            'interval'       => array(
                'length'     => $subscription_interval,
                'unit'       => $subscription_period
            ),
            'billing_cycles' => $subscription_length,
            'trial'          => array(
                'days'       => ! empty( $trail_details['days'] ) ? $trail_details['days'] : 0,
                'enabled'    => ! empty( $trail_details['is_enabled'] ) ? $trail_details['is_enabled'] : false
            )
        );

        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
            'body'        => json_encode( $plan_body ),
            'cookies'     => array()
        );

        $response = wp_remote_post( $base_url, $args );

        if ( is_wp_error( $response ) ) {
            throw new Exception( __( 'Something went wrong', 'dokan' ) );
        }

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '400' ) {

            $error = json_decode( wp_remote_retrieve_body( $response ) );

            if ( isset( $error->errors[0]->code ) && $error->errors[0]->code == 'MA6' ) {
                $plan_id = $this->edit_plan( $product->get_id(), $product, $subscription_interval, $subscription_period, $subscription_length, $trail_details = [] );

                if ( $plan_id ) {
                    return $plan_id;
                }

            }
        }

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '201' ) {
            dokan_log( 'plan created' );
            return $product->get_id();
        }
    }

    /**
     * Edit a plan
     *
     * @param  int $plan_id
     * @param  object product
     * @param  int $subscription_interval
     * @param  string $subscription_period
     * @param  int $subscription_length
     *
     * @return int $plan_id
     */
    public function edit_plan( $plan_id, $product, $subscription_interval, $subscription_period, $subscription_length, $trail_details = [] ) {
        if ( empty( $plan_id ) || empty( $product ) ) {
            return false;
        }

        if ( empty( $subscription_interval ) || empty( $subscription_period ) || empty( $subscription_length ) ) {
            return false;
        }

        $base_url = $this->base_url . '/plans/' . $plan_id;

        $plan_body = array(
            'code'          => $plan_id,
            'name'          => $product->get_name(),
            'amount'        => $product->get_price() * 100,
            'interval'      => array(
                'length'    => $subscription_interval,
                'unit'      => $subscription_period
            ),
            'billing_cycles' => $subscription_length,
            'trial'          => array(
                'days'       => ! empty( $trail_details['days'] ) ? $trail_details['days'] : 0,
                'enabled'    => ! empty( $trail_details['is_enabled'] ) ? $trail_details['is_enabled'] : false
            )
        );

        $args = array(
            'method'      => 'PUT',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
            'body'        => json_encode( $plan_body ),
            'cookies'     => array()
        );

        $response = wp_remote_request( $base_url, $args );

        if ( is_wp_error( $response ) ) {
            throw new Exception( __( 'Something went wrong', 'dokan' ) );
        }

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
            return $product->get_id();
        }
    }

    /**
     * Create moip subscription
     *
     * @param  object $order
     * @param  int $plan_id
     *
     * @return int subscriptoin_id
     */
    public function create_subscription( $order, $plan_id ) {
        $base_url = $this->base_url . '/subscriptions?new_customer=true';

        $customer_info                      = array();
        $customer_info['full_name']         = $order->get_formatted_billing_full_name();
        $customer_info['email']             = $order->get_billing_email();
        $customer_info['birthdate']         = '';
        $customer_info['tax_document']      = wc_clean( $_POST['billing_cpf'] );
        $customer_info['phone_prefix']      = substr( $order->get_billing_phone(), 0, 3 );
        $customer_info['phone_sufix']       = substr( $order->get_billing_phone(), 3 );
        $customer_info['card_number']       = wc_clean( str_replace( ' ', '', $_POST['dokan-moip-connect-card-number'] ) );
        $customer_info['card_expiry_month'] = wc_clean( substr( $_POST['dokan-moip-connect-card-expiry'], 0, 2 ) );
        $customer_info['card_expiry_year']  = wc_clean( substr( $_POST['dokan-moip-connect-card-expiry'], 5 ) );

        $subscription_code = rand();
        $subs_body = array(
          'code' => $subscription_code,
          'plan' => array(
            'code' => $plan_id,
          ),
          'payment_method' => 'CREDIT_CARD',
          'customer' => array(
            'code'            => $subscription_code,
            'email'           => $customer_info['email'],
            'fullname'        => $customer_info['full_name'],
            'cpf'             => $customer_info['tax_document'],
            'phone_number'    => $customer_info['phone_sufix'],
            'phone_area_code' => $customer_info['phone_prefix'],
            'birthdate_day'   => '26',
            'birthdate_month' => '04',
            'birthdate_year'  => '1986',
            'address' => array(
              'street'     => 'Rua nome da Rua',
              'number'     => '170',
              'complement' => 'Casa',
              'district'   => 'Bairro',
              'city'       => 'São Paulo',
              'state'      => 'SP',
              'country'    => 'BRA',
              'zipcode'    => '00000000'
            ),
            'billing_info' => array(
              'credit_card' => array(
                'holder_name'      => $customer_info['full_name'],
                'number'           => $customer_info['card_number'],
                'expiration_month' => $customer_info['card_expiry_month'],
                'expiration_year'  => $customer_info['card_expiry_year']
              )
            )
          )
        );

        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
            'body'        => json_encode( $subs_body ),
            'cookies'     => array()
        );

        $response = wp_remote_post( $base_url, $args );

        if ( is_wp_error( $response ) ) {
            throw new Exception( __('Subscripton can\'t be created', 'dokan' ) );
        }

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '400' ) {
            $error = json_decode( wp_remote_retrieve_body( $response ) );

            if ( isset( $error->errors[0]->code ) && $error->errors[0]->code == 'MA33' ) {
                update_user_meta( get_current_user_id(), 'subscription_code', $subscription_code );
                return $subscription_code;
            } else {
                throw new Exception( __( 'Subscripton not created', 'dokan' ) );
            }
        }

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '201' ) {
            dokan_log( 'subscription created' );
            update_user_meta( get_current_user_id(), 'subscription_code', $subscription_code );

            return $subscription_code;
        }
    }

    /**
     * Cancel a subscription
     *
     * @param  int $user_id
     * @param  string $subcription_code
     *
     * @return boolean
     */
    public function cancel_subscription( $user_id, $subscription_code ) {
        if ( empty( $user_id ) || empty( $subscription_code ) ) {
            return false;
        }

        $base_url = $this->base_url . '/subscriptions/' . $subscription_code .  '/cancel';

        $args = array(
            'method'      => 'PUT',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
        );

        $response = wp_remote_request( $base_url, $args );

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
            return true;
        }

        return false;
    }

    /**
     * Activate a suspended subscription
     *
     * @param  int $user_id
     * @param  string $subcription_code
     *
     * @return boolean
     */
    public function activate_subscription( $user_id, $subscription_code ) {
        if ( empty( $user_id ) || empty( $subscription_code ) ) {
            return false;
        }

        $base_url = $this->base_url . '/subscriptions/' . $subscription_code .  '/activate';

        $args = array(
            'method'      => 'PUT',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
        );

        $response = wp_remote_request( $base_url, $args );

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
            update_user_meta( $user_id, 'subscription_code', $subscription_code );
            return true;
        }

        return false;
    }

    /**
     * Suspend a subscription
     *
     * @param  int $user_id
     * @param  string $subcription_code
     *
     * @return boolean
     */
    public function suspend_subscription( $user_id, $subscription_code ) {
        if ( empty( $user_id ) || empty( $subscription_code ) ) {
            return false;
        }

        $base_url = $this->base_url . '/subscriptions/' . $subscription_code .  '/suspend';

        $args = array(
            'method'      => 'PUT',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
        );

        $response = wp_remote_request( $base_url, $args );

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
            dokan_log( 'subscription suspended' );
            delete_user_meta( $user_id, 'subscription_code' );
            delete_user_meta( $user_id, 'can_post_product' );
            return true;
        }

        return false;
    }

    /**
     * Edit a subscription
     *
     * @param  string $subcription_code
     * @param  string $amount
     * @param  string $day
     * @param  string $month
     * @param  string $year
     *
     * @return boolean
     */
    public function edit_subscription( $subscription_code, $amount, $day, $month, $year ) {
        if ( empty( $subscription_code ) ) {
            return false;
        }

        if ( empty( $amount ) || empty( $day ) || empty( $month ) || empty( $year ) ) {
            return false;
        }

        $base_url = $this->base_url . '/subscriptions/' . $subscription_code;

        $body = array(
            'plan' => array(
                'code' => $subscription_code
            ),
            'amount' => $amount * 100,
            'next_invoice_date' => array(
                'day'   => $day,
                'month' => $month,
                'year'  => $year
            )
        );
        $args = array(
            'method'      => 'PUT',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
            'body' => json_encode( $body ),
        );

        $response = wp_remote_request( $base_url, $args );

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
            return true;
        }

        return false;
    }

    /**
     * Get a invoice
     *
     * @param  int $invoice_id
     *
     * @return array
     */
    public function get_invoice( $invoice_id ) {
        if ( ! $invoice_id ) {
            return;
        }

        $base_url = $this->base_url . '/invoices/' . $invoice_id;

        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
            'cookies'     => array()
        );

        $response = wp_remote_get( $base_url, $args );

        if ( is_wp_error( $response ) ) {
            throw new Exception( __( 'Invoice not found', 'dokan' ) );
        }

        if ( ! isset( $response['response']['code'] ) || $response['response']['code'] != '200' ) {
            throw new Exception( __( 'Invoice not found', 'dokan' ) );
        }

        $response_body = wp_remote_retrieve_body( $response );

        return json_decode( $response_body );
    }

    /**
     * Retry to pay a delayed invoice payment
     *
     * @return void
     */
    public function retry_payment() {
        $base_url = $this->base_url . '/users/preferences/retry';

        if ( empty( $this->key ) || empty( $this->token ) ) {
            return;
        }

        if ( get_option( 'retry_moip_payment_sandbox' ) == 'no' ) {
            return;
        }

        if ( strpos( $base_url, 'sandbox' ) ) {
            if ( get_option( 'retry_moip_payment_sandbox' ) == 'yes' ) {
                return;
            }
        }

        $body = array(
            'first_try'  => 1,
            'second_try' => 3,
            'third_try'  => 5,
            'finally'    => 'cancel'
        );

        $args = array(
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(
                'Authorization' => 'Basic ' . base64_encode( $this->token . ':' . $this->key ),
                'Content-Type'  => 'application/json'
            ),
            'body'        => json_encode( $body ),
            'cookies'     => array()
        );

        $response = wp_remote_post( $base_url, $args );

        if ( is_wp_error( $response ) ) {
            throw new Exception( __( 'Something went wrong', 'dokan' ) );
        }

        if ( isset( $response['response']['code'] ) && $response['response']['code'] == '200' ) {
            if ( strpos( $base_url, 'sandbox' ) ) {
                update_option( 'retry_moip_payment_sandbox', 'yes' );
            } else {
                update_option( 'retry_moip_payment_sandbox', 'no' );
            }
        }
    }
}
