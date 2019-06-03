<?php
/*
Plugin Name: Stripe Connect
Plugin URI: https://wedevs.com/products/plugins/dokan/stripe-connect/
Description: Accept credit card payments and allow your sellers to get automatic split payment in Dokan via Stripe.
Version: 1.3.2
Author: weDevs
Author URI: https://wedevs.com
Thumbnail Name: stripe.png
License: GNU General Public License v3.0
*/


/**
 * Copyright (c) 2015 weDevs (email: info@wedevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DOKAN_STRIPE_FILE', __FILE__ );
define( 'DOKAN_STRIPE_PATH', dirname( __FILE__ ) );
define( 'DOKAN_STRIPE_TEMPLATE_PATH', dirname( __FILE__ ) . '/templates/' );

/**
 * Dokan Stripe Main class
 *
 * @author weDevs<info@wedevs.com>
 */
class Dokan_Stripe {

    /**
     * Constructor
     */
    public function __construct() {

        /** All actions */
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'template_redirect', array( $this, 'stripe_check_connect' ), 20 );

        add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );

        add_filter( 'dokan_withdraw_methods', array( $this, 'register_dokan_withdraw_gateway' ) );
        add_filter( 'dokan_get_dashboard_nav', array( $this, 'remove_withdraw_page' ) );
        add_filter( 'dokan_query_var_filter', array( $this, 'remove_withdraw_query_var' ), 80 );

        // Handle recurring subscription cancel
        add_action( 'dps_cancel_recurring_subscription', array( $this, 'cancel_recurring_subscription' ), 10, 2 );

        add_action( 'edit_user_profile', array( $this, 'stripe_admin_menu') , 50 );
        add_action( 'show_user_profile', array( $this, 'stripe_admin_menu') , 50 );
        add_action( 'personal_options_update', array( $this, 'stripe_admin_functions') , 50 );
        add_action( 'edit_user_profile_update', array( $this, 'stripe_admin_functions') , 50 );
        add_action( 'template_redirect', array( $this, 'delete_stripe_account') , 50 );
        add_action( 'init', array( $this, 'handle_stripe_webhook') , 10 );

        add_action( 'dokan_store_profile_saved', array( $this, 'save_stripe_progress' ), 8, 2 );
        // add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 15, 3 );

        add_action( 'woocommerce_after_checkout_validation', array( $this, 'check_vendor_configure_stripe' ), 15, 2 );

        // approve refund request automatically such as stripe connect
        add_action( 'dokan_after_refund_request', [ $this, 'process_refund_request' ], 10, 2 );
    }

    function filter_gateways(  $gateways ){
        if ( !empty( WC()->cart->cart_contents ) ) {
            foreach ( WC()->cart->cart_contents as $key => $values ) {
                if ( dokan_get_prop( $values['data'], 'product_type', 'get_type') == 'product_pack' ) {
                    unset( $gateways['dokan-stripe-connect'] );
                    break;
                } else {
                    unset( $gateways['stripe'] );
                    break;
                }
            }
        }
        return $gateways;
    }

    /**
     * Validate checkout if vendor has configured stripe account
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function check_vendor_configure_stripe( $data, $errors ) {
        $settings = get_option('woocommerce_dokan-stripe-connect_settings');

        // bailout if the gateway is not enabled
        if ( isset( $settings['enabled'] ) && $settings['enabled'] == 'yes' ) {
            if ( 'dokan-stripe-connect' == $data['payment_method'] ) {
                if ( isset( $settings['allow_non_connected_sellers'] ) && 'yes' === $settings['allow_non_connected_sellers'] ) {
                    return;
                }

                foreach ( WC()->cart->get_cart() as $item ) {
                    $product_id = $item['data']->get_id();
                    $available_vendors[get_post_field( 'post_author', $product_id )][] = $item['data'];
                }

                // if it's subscription product return early
                $subscription_product = wc_get_product( $product_id );

                if ( $subscription_product && 'product_pack' === $subscription_product->get_type() ) {
                    return;
                }

                $vendor_names = array();

                foreach ( array_keys( $available_vendors ) as $vendor_id ) {
                    $vendor = dokan()->vendor->get( $vendor_id );
                    $access_token = get_user_meta( $vendor_id, '_stripe_connect_access_key', true );

                    if ( empty( $access_token ) ) {
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
                    $errors->add( 'stipe-not-configured', sprintf(__('<strong>Error!</strong> The <strong>%s</strong> does not allowes the Stipe gateway. You can not purchase this products %s using Stripe Gateway', 'dokan'), $data['name'], $data['products'] ) );
                }
            }
        }

    }

    /**
     * Init localisations and files
     */
    public function init() {
        if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
            return;
        }

        include_once dirname( __FILE__ ) . '/classes/class-dokan-stripe-connect.php';
        include_once dirname( __FILE__ ) . '/classes/class-dokan-stripe-connect-saved-cards.php';
    }

    /**
     * Register the gateway for use
     */
    public function register_gateway( $methods ) {
        $methods[] = 'Dokan_Stripe_Connect';

        return $methods;
    }

    /**
     * Check to connect with stripe
     *
     * @return void
     */
    function stripe_check_connect() {
        if ( !empty( $_GET['state'] ) && 'wepay' == $_GET['state'] ) {
            return;
        }

        if ( empty( $_GET['scope'] ) || empty( $_GET['code'] ) ) {
            return;
        }

        $settings   = get_option('woocommerce_dokan-stripe-connect_settings');
        $client_id  = $settings['testmode'] == 'yes' ? $settings['test_client_id'] : $settings['client_id'];
        $secret_key = $settings['testmode'] == 'yes' ? $settings['test_secret_key'] : $settings['secret_key'];

        require_once DOKAN_STRIPE_PATH . '/classes/lib/oauth/OAuth2Exception.php';
        require_once DOKAN_STRIPE_PATH . '/classes/lib/oauth/OAuth2Client.php';
        require_once DOKAN_STRIPE_PATH . '/classes/lib/StripeOAuth.class.php';

        $oauth = new StripeOAuth( $client_id, $secret_key );
        $token = $oauth->getAccessToken( $_GET['code'] );
        $key   = $oauth->getPublishableKey( $_GET['code'] );

        update_user_meta( get_current_user_id(), '_stripe_connect_access_key', $token );

        wp_redirect( dokan_get_navigation_url( 'settings/payment' ) );
        exit;
    }

    /**
     * Add to cart validation error
     *
     * Customers shouldn't be able to buy product if the sellers stripe account
     * is not connected.
     *
     * @param bool  $validation
     * @param int  $product_id
     *
     * @return bool
     */
    function add_to_cart_validation( $validation, $product_id, $qty ) {
        $settings = get_option('woocommerce_dokan-stripe-connect_settings');

        // bailout if the gateway is not enabled
        if ( isset( $settings['enabled'] ) && $settings['enabled'] !== 'yes' ) {
            return $validation;
        }

        $seller_id    = get_post_field( 'post_author', $product_id );
        $access_token = get_user_meta( $seller_id, '_stripe_connect_access_key', true );

        if ( empty( $access_token ) ) {
            wc_add_notice( __( 'This vendor has not configured his Stripe payment gateway and the product can not be purchased!', 'dokan' ), 'error' );

            return false;
        }

        return $validation;
    }

    /**
     * Register the stripe gateway for withdraw
     *
     * @param  array  $methods
     *
     * @return array
     */
    function register_dokan_withdraw_gateway( $methods ) {
        $settings = get_option('woocommerce_dokan-stripe-connect_settings');

        if ( isset( $settings['enabled'] ) && $settings['enabled'] != 'yes' ) {
            return $methods;
        }

        $methods['dokan-stripe-connect'] = array(
            'title'    => __( 'Stripe', 'dokan' ),
            'callback' => array( $this, 'stripe_authorize_button' )
        );

        return $methods;
    }

    /**
     * This enables dokan vendors to connect their stripe account to the site stripe gateway account
     *
     * @param array $store_settings
     */
    function stripe_authorize_button( $store_settings ) {
        $store_user = wp_get_current_user();
        $settings   = get_option('woocommerce_dokan-stripe-connect_settings');

        if ( ! $settings ) {
            _e( 'Stripe gateway is not configured. Please contact admin.', 'dokan' );
            return;
        }

        if ( ! isset( $settings['enabled'] ) || $settings['enabled'] == 'no' ) {
            return;
        }

        $client_id  = $settings['testmode'] == 'yes' ? $settings['test_client_id'] : $settings['client_id'];
        $secret_key = $settings['testmode'] == 'yes' ? $settings['test_secret_key'] : $settings['secret_key'];
        $key        = get_user_meta( $store_user->ID, '_stripe_connect_access_key', true );
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
            <input type="hidden" name="settings[stripe]" value="<?php echo empty( $key ) ? 0 : 1; ?>">
            <?php
                if ( empty( $key ) ) {

                    echo '<div class="dokan-alert dokan-alert-danger">';
                        _e( 'Your account is not yet connected with Stripe. Connect with Stripe to receive your commissions.', 'dokan' );
                    echo '</div>';

                    require_once DOKAN_STRIPE_PATH . '/classes/lib/oauth/OAuth2Exception.php';
                    require_once DOKAN_STRIPE_PATH . '/classes/lib/oauth/OAuth2Client.php';
                    require_once DOKAN_STRIPE_PATH . '/classes/lib/StripeOAuth.class.php';

                    $oauth = new StripeOAuth( $client_id, $secret_key );
                    $url   = $oauth->getAuthorizeUri();
                    ?>
                    <br/>
                    <a class="clear" href="<?php echo $url; ?>" target="_TOP">
                        <img src="<?php echo plugins_url( '/assets/images/blue.png', DOKAN_STRIPE_FILE ); ?>" width="190" height="33" data-hires="true">
                    </a>
                    <?php

                } else {
                    ?>
                    <div class="dokan-alert dokan-alert-success">
                        <?php _e( 'Your account is connected with Stripe.', 'dokan' ); ?>
                        <a  class="dokan-btn dokan-btn-danger dokan-btn-theme" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'dokan-disconnect-stripe' ), dokan_get_navigation_url( 'settings/payment' ) ), 'dokan-disconnect-stripe' ); ?>"><?php _e( 'Disconnect', 'dokan' ); ?></a>
                    </div>
                    <?php
                }
            ?>
        </div>
        <?php
    }

    /**
     * Remove withdraw page if stripe is enabled
     *
     * @param  array  $urls
     *
     * @return array
     */
    public function remove_withdraw_page( $urls ) {
        $withdraw_settings = get_option( 'dokan_withdraw' );
        $hide_withdraw_option = isset( $withdraw_settings['hide_withdraw_option'] ) ? $withdraw_settings['hide_withdraw_option'] : 'off';

        if ( $hide_withdraw_option == 'on' ) {
            $settings = get_option( 'woocommerce_dokan-stripe-connect_settings' );
            // bailout if the gateway is not enabled
            if ( isset( $settings['enabled'] ) && $settings['enabled'] !== 'yes' ) {
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
     * Remove withdraw query var disable access to withdraw template
     *
     * @since 1.3
     *
     * @param array $query_vars
     *
     * @return array $query_vars
     */
    public function remove_withdraw_query_var( $query_vars ) {
        $withdraw_settings = get_option( 'dokan_withdraw' );
        $hide_withdraw_option = isset( $withdraw_settings['hide_withdraw_option'] ) ? $withdraw_settings['hide_withdraw_option'] : 'off';

        if ( $hide_withdraw_option == 'on' ) {
            $key = array_search( 'withdraw', $query_vars );

            if ( $key != FALSE ) {
                unset( $query_vars[$key] );
                $query_vars = array_values( $query_vars );
            }

            return $query_vars;
        }

        return $query_vars;
    }

    public function delete_stripe_account() {
        $user_id = get_current_user_id();

        if ( !is_user_logged_in() ) {
            return;
        }

        if ( !dokan_is_user_seller( $user_id ) ) {
            return;
        }

        if ( isset( $_GET['action'] ) && $_GET['action'] == 'dokan-disconnect-stripe' ) {

            if ( !wp_verify_nonce( $_GET['_wpnonce'], 'dokan-disconnect-stripe' ) ) {
                return;
            }

            delete_user_meta( $user_id, '_stripe_connect_access_key');
            wp_redirect( dokan_get_navigation_url( 'settings/payment' ) );
            exit;
        }
    }

    /**
    * Handle webhook for recurring
    *
    * @since 1.3.3.
    *
    * @return void
    **/
    public function handle_stripe_webhook() {

        if ( isset( $_GET['webhook'] ) && $_GET['webhook'] == 'dokan' ) {
            global $wpdb;

            require_once 'classes/lib/init.php';

            $stripe_options = get_option('woocommerce_dokan-stripe-connect_settings');

            $secret_key = $stripe_options['testmode'] == 'yes' ? $stripe_options['test_secret_key'] : $stripe_options['secret_key'];

            \Stripe\Stripe::setApiKey( $secret_key );

            // retrieve the request's body and parse it as JSON
            $body = @file_get_contents( 'php://input' );

            // grab the event information
            $event_json = json_decode($body);

            // this will be used to retrieve the event from Stripe
            $event_id = $event_json->id;

            if( isset( $event_json->id ) ) {
                try {

                    // to verify this is a real event, we re-retrieve the event from Stripe
                    $event = \Stripe\Event::retrieve( $event_id );
                    $invoice = $event->data->object;

                    // successful payment, both one time and recurring payments
                    if ( 'invoice.payment_succeeded' == $event->type ) {
                        $user_id = $wpdb->get_var( "SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = '_stripe_subscription_id' AND `meta_value`='$invoice->subscription'" );
                        $period_start = date( 'Y-m-d H:i:s', $invoice->period_start );
                        $period_end = date( 'Y-m-d H:i:s', $invoice->period_end );
                        $order_id = get_user_meta( $user_id, 'product_order_id', true );

                        if ( $invoice->paid ) {
                            update_user_meta( $user_id, 'product_pack_startdate', $period_start );
                            update_user_meta( $user_id, 'product_pack_enddate', $period_end );
                            update_user_meta( $user_id, 'can_post_product', '1' );

                            if ( !empty( $invoice->charge ) ) {
                                update_post_meta( $order_id, '_stripe_subscription_charge_id', $invoice->charge );
                            }
                        }
                    }

                    // failed payment
                    if ( 'invoice.payment_failed' == $event->type ) {
                        $user_id = $wpdb->get_var( "SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = '_stripe_subscription_id' AND `meta_value`='$invoice->subscription'" );

                        // Terminate user to update product
                        update_user_meta( $user_id, 'can_post_product', '0' );

                        // Make sure this is final attempt
                        if ( isset( $invoice->next_payment_attempt ) && $invoice->next_payment_attempt == null ) {
                            delete_user_meta( $user_id, 'product_package_id' );
                            delete_user_meta( $user_id, '_stripe_subscription_id' );
                            delete_user_meta( $user_id, 'product_order_id' );
                            delete_user_meta( $user_id, 'product_no_with_pack' );
                            delete_user_meta( $user_id, 'product_pack_startdate' );
                            delete_user_meta( $user_id, 'product_pack_enddate' );
                            delete_user_meta( $user_id, 'can_post_product' );
                            delete_user_meta( $user_id, '_customer_recurring_subscription' );
                            delete_user_meta( $user_id, 'dokan_seller_percentage' );
                        }
                    }

                    if ( 'charge.dispute.created' == $event->type ) {
                        $charge_id = $invoice->charge;
                        $charge  = \Stripe\Charge::retrieve( $charge_id );
                        $charge_invoice  = \Stripe\Invoice::retrieve( $charge->invoice );
                        $settings = get_option('woocommerce_dokan-stripe-connect_settings');

                        $user_id = $wpdb->get_var( "SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = '_stripe_subscription_id' AND `meta_value`='$charge_invoice->subscription'" );
                        $order_id = get_user_meta( $user_id, 'product_order_id', true );
                        $order = wc_get_order( $order_id );

                        $order->set_status( 'on-hold' );
                        $order->save();

                        update_user_meta( $user_id, 'can_post_product', '0' );

                        $order->add_order_note( sprintf( __( 'Order %s status is now on-hold due to dispute via %s on (Charge IDs: %s)', 'dokan' ), $order->get_order_number(), $settings['title'], $charge_id ) );
                    }

                    if ( 'charge.dispute.closed' == $event->type ) {

                        if ( 'won' == $invoice->status ) {
                            $charge_id = $invoice->charge;
                            $charge  = \Stripe\Charge::retrieve( $charge_id );
                            $charge_invoice  = \Stripe\Invoice::retrieve( $charge->invoice );
                            $settings = get_option('woocommerce_dokan-stripe-connect_settings');

                            $user_id = $wpdb->get_var( "SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = '_stripe_subscription_id' AND `meta_value`='$charge_invoice->subscription'" );
                            $order_id = get_user_meta( $user_id, 'product_order_id', true );
                            $order = wc_get_order( $order_id );

                            $order->set_status( 'completed' );
                            $order->save();

                            update_user_meta( $user_id, 'can_post_product', '1' );

                            $order->add_order_note( sprintf( __( 'Order %s status is now completed due to dispute resolved in your favour via %s on (Charge IDs: %s)', 'dokan' ), $order->get_order_number(), $settings['title'], $charge_id ) );
                        }
                    }

                    if ( 'customer.subscription.trial_will_end' == $event->type ) {
                        // it will trigger 3 days before an trail ends
                        do_action( 'dokan_vendor_subscription_will_end' );
                    }

                    // update pack end date
                    if ( 'customer.subscription.created' == $event->type ) {
                        $invoice = $event->data->object;
                        $user_id = $wpdb->get_var( "SELECT `user_id` FROM $wpdb->usermeta WHERE `meta_key` = '_stripe_subscription_id' AND `meta_value`='$invoice->id'" );

                        update_user_meta( $user_id, 'product_pack_enddate', date( 'Y-m-d H:i:s', $invoice->current_period_end ) );
                    }

                } catch ( Exception $e ) {
                    // something failed, perhaps log a notice or email the site admin
                }
            }
        }
    }

    /**
    * Handle recurring subscription cancelation
    *
    * @since 1.3.3
    *
    * @return void
    **/
    public function cancel_recurring_subscription( $order_id, $user_id ) {
        if ( ! $order_id ) {
            return;
        }

        if ( $order_id != get_user_meta( $user_id, 'product_order_id', true ) ) {
            return;
        }

        $order = wc_get_order( $order_id );

        if ( 'dokan-stripe-connect' == $order->get_payment_method() ) {

            try {
                Dokan_Stripe_Connect::cancel_recurring_subscription( $user_id );
            } catch ( Exception $e ) {

            }

            delete_user_meta( $user_id, 'product_package_id' );
            delete_user_meta( $user_id, '_stripe_subscription_id' );
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
     * Admin functions for controlling user Stripe Accounts
     *
     * @param array $store_settings
     */
    function stripe_admin_functions( $user_id ) {

        if ( !dokan_is_user_seller( $user_id ) || ! current_user_can( 'manage_options' )  ) {
            return $user_id;
        }

        $stripe_settings   = get_option('woocommerce_dokan-stripe-connect_settings');

        if ( ! $stripe_settings ) {
            return $user_id;
        }

        $stripe_key = get_user_meta( $user_id, '_stripe_connect_access_key', true );

        if ( isset($_POST['disconnect_user_stripe'] ) ) {
            delete_user_meta( $user_id, '_stripe_connect_access_key');
        }
        return $user_id;
    }

    /**
    * This is admin menu for controlling Seller Stripe status
    *
    * @param array $store_settings
    */
    function stripe_admin_menu( $user ) {

        if ( !dokan_is_user_seller( $user->ID ) || ! current_user_can( 'manage_options' )  ) {
            return $user;
        }

        $stripe_key = get_user_meta( $user->ID, '_stripe_connect_access_key', true );
        ?>
        <h3><?php _e('Dokan Stripe Settings','dokan');?></h3>
        <?php
        if ( !empty( $stripe_key ) ) : ?>
            <?php submit_button( __( 'Disconnect User Stripe Account', 'dokan' ) ,'delete', 'disconnect_user_stripe'); ?>
        <?php else : ?>
            <h4><?php _e("User account not connected to Stripe",'dokan');?></h4>
        <?php
        endif;
    }

    /**
    * Save stripe progress settings data
    *
    * @since 2.8
    *
    * @return void
    **/
    public function save_stripe_progress( $store_id, $dokan_settings ) {
        if ( ! $store_id ) {
            return;
        }

        if ( isset( $_POST['settings']['stripe'] ) ) {
            $dokan_settings['payment']['stripe'] = $_POST['settings']['stripe'];
        }

        update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );
    }

    /**
     * Process refund request
     *
     * @param  int $refund_id
     * @param  array $data
     *
     * @return void
     */
    public function process_refund_request( $refund_id, $data ) {

        if ( ! $data['order_id'] ) {
            return wp_send_json( __( 'No refund data to be processed', 'dokan' ) );
        }

        $order_id         = $data['order_id'];
        $order            = wc_get_order( $order_id );
        $vendor_id        = dokan_get_seller_id_by_order( $order_id );
        $vendor_token     = get_user_meta( $vendor_id, '_stripe_connect_access_key', true );
        $vendor_charge_id = $order->get_meta( "_dokan_stripe_charge_id_{$vendor_id}" );

        // if vendor charge id is not found, meaning it's a not purcahsed with sitripe so return early
        if ( ! $vendor_charge_id ) {
            return true;
        }

        require_once 'classes/lib/init.php';

        $stripe_options = get_option('woocommerce_dokan-stripe-connect_settings');
        $secret_key     = $stripe_options['testmode'] == 'yes' ? $stripe_options['test_secret_key'] : $stripe_options['secret_key'];

        \Stripe\Stripe::setApiKey( $secret_key );

        try {
            $refund = \Stripe\Refund::create( [
                'charge'                 => $vendor_charge_id,
                'amount'                 => $data['refund_amount'] * 100, // in cents
                'reason'                 => __( 'requested_by_customer', 'dokan' ),
                'refund_application_fee' => true
            ], $vendor_token );
        } catch( Exception $e ) {
            return wp_send_json_error( $e->getMessage() );
        }

        if ( ! $refund->id ) {
            return wp_send_json_error( __( 'Refund ID is not found', 'dokan' ) );
        }

        $order->add_order_note( sprintf( __( 'Refund Processed Via Stripe ( Refund ID: %s )', 'dokan' ), $refund->id ) );

        if ( ! class_exists( 'Dokan_REST_Refund_Controller' ) ) {
            require_once DOKAN_PRO_INC . '/api/class-refund-controller.php';
        }

        global $wpdb;

        $refund_data = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}dokan_refund` WHERE `id`= %d", $refund_id )
        );

        $refund_api     = new Dokan_REST_Refund_Controller;
        $approve_refund = $refund_api->approve_refund_request( $refund_data );

        if ( ! $approve_refund ) {
            return wp_send_json_error( __( 'Refund request has been sent but not approved', 'dokan' ) );
        }

        if ( ! class_exists( 'Dokan_Pro_Admin_Refund' ) ) {
            require_once DOKAN_PRO_CLASS . '/admin-refund.php';
        }

        $refund = new Dokan_Pro_Admin_Refund();
        $refund->update_status( $refund_id, 1 );

        return wp_send_json_success( __( 'Your refund request has been processed.', 'dokan' ) );
    }
}

new Dokan_Stripe();
