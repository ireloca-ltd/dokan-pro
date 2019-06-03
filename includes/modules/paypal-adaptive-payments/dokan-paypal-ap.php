<?php
/*
Plugin Name: PayPal Adaptive Payment
Plugin URI: https://wedevs.com/products/dokan/paypal-adaptive-payment/
Description: Allows to send split payments to vendor via PayPal Adaptive Payment gateway
Version: 1.1.0
Author: weDevs
Author URI: https://wedevs.com/
Thumbnail Name: paypal-adaptive.png
License: GPL2
*/

/**
 * Copyright (c) 2015 wedevs (email: info@wedevs.com). All rights reserved.
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
// don't call the file directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

define( 'DOKAN_PAYPAL_ADAPTIVE_PLUGIN_PATH', plugins_url( '', __FILE__ ) );

/**
 * Dokan_Paypal_AP class
 *
 * @class Dokan_Paypal_AP The class that holds the entire Dokan_Paypal_AP plugin
 */
class Dokan_Paypal_AP {

    function __construct() {
        add_action( 'init', array( $this, 'init' ) );

        add_filter( 'woocommerce_payment_gateways', array( $this, 'register_gateway' ) );
        add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_cart' ), 10, 3 );

        add_filter( 'dokan_get_dashboard_nav', array( $this, 'unset_withdraw_page' ) );
        add_filter( 'woocommerce_available_payment_gateways', array( $this, 'checkout_filter_gateway' ), 1 );

    }

    function init() {
        if ( !class_exists( 'WC_Payment_Gateway' ) ) {
            return;
        }

        require_once dirname( __FILE__ ) . '/classes/class-dokan-paypal-ap-gateway.php';
        require_once dirname( __FILE__ ) . '/lib/vendor/autoload.php';
    }

    function register_gateway( $gateways ) {
        $gateways[] = 'WC_Dokan_Paypal_Ap_Gateway';

        return $gateways;
    }

    function checkout_filter_gateway( $gateways ){

        $settings = get_option( 'woocommerce_dokan_paypal_adaptive_settings' );

        // check if Dokan Paypal Adaptive Payments is Enabled or not
        if ( ! isset( $settings['enabled'] ) || $settings['enabled'] != 'yes' ) {
            return $gateways;
        }

        foreach ( WC()->cart->cart_contents as $key => $values ) {

            if ( $values['data']->get_type() == 'product_pack' ) {
                unset( $gateways['dokan_paypal_adaptive'] );
                break;
            } else {
                unset($gateways['paypal']);
                break;
            }
        }

        return $gateways;
    }
    /**
     * Don't permit to add products more than payee limit of PayPal
     *
     * @param boolean $valid
     * @param int     $product_id
     * @param type    $quantity
     * @return boolean
     */
    function validate_cart( $valid, $product_id, $quantity ) {

        $products = WC()->cart->get_cart();

        // emulate add-to-cart by pushing the new content to the array
        $products[$product_id] = array( 'product_id' => $product_id );

        if ( $products ) {
            $settings = get_option( 'woocommerce_dokan_paypal_adaptive_settings' );

            if ( ! isset( $settings['enabled'] ) || $settings['enabled'] != 'yes' ) {
                return $valid;
            }

            $payees      = array();
            $single_mode = ( isset( $settings['single_mode'] ) && $settings['single_mode'] == 'yes' ) ? true : false;

            foreach ( $products as $key => $data ) {
                $product_id = $data['product_id'];
                $seller_id  = get_post_field( 'post_author', $product_id );

                if ( ! array_key_exists( $seller_id, $payees ) ) {
                    $payees[$seller_id] = $seller_id;
                }
            }

            // single seller mode
            if ( $single_mode && count( $payees ) > 1 ) {
                $error_message = __( 'You can not add more than one vendors product in the cart', 'dokan' );
                wc_add_notice( $error_message, 'error' );
                return false;
            }

            // PayPal doesn't allow more than 6 payees in adaptive
            // so 5 + site_owner = 6
            if ( count( $payees ) > 5 ) {
                $error_message = isset( $settings['max_error'] ) ? $settings['max_error'] : '';

                wc_add_notice( $error_message, 'error' );

                return false;
            }
        }

        return $valid;
    }

    /**
     * Unset Seller dashboard withdraw page
     *
     * @param array   $urls
     * @return array
     */
    function unset_withdraw_page( $urls ) {
        $withdraw_settings = get_option( 'dokan_withdraw' );
        $hide_withdraw_option = isset( $withdraw_settings['hide_withdraw_option'] ) ? $withdraw_settings['hide_withdraw_option'] : 'off';

        if ( $hide_withdraw_option == 'on' ) {
            $enable = get_option( 'woocommerce_dokan_paypal_adaptive_settings' );
            // bailout if the gateway is not enabled
            if ( isset( $enable['enabled'] ) && $enable['enabled'] !== 'yes' ) {
                return $urls;
            }

            if ( array_key_exists( 'withdraw', $urls ) ) {
                unset( $urls['withdraw'] );
            }

            return $urls;
        }

        return $urls;
    }


}

new Dokan_Paypal_AP();
