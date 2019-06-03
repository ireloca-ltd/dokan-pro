<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Dokan_Stripe_Connect_Saved_Cards class.
 *
 * @package Dokan Stripe
 */
class Dokan_Stripe_Connect_Saved_Cards {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp', array( $this, 'delete_card' ) );
        add_action( 'woocommerce_after_my_account', array( $this, 'output' ) );
    }

    /**
     * Display saved cards
     */
    public function output() {

        if ( ! is_user_logged_in() ) {
            return;
        }

        $cards = get_user_meta( get_current_user_id(), '_stripe_customer_id', false );

        if ( $cards ) {
            wc_get_template( 'saved-cards.php', array( 'cards' => $cards ), 'woocommerce-gateway-stripe/', DOKAN_STRIPE_TEMPLATE_PATH );
        }
    }

    /**
     * Delete a card
     */
    public function delete_card() {

        if ( ! isset( $_POST['stripe_delete_card'] ) || !is_account_page() ) {
            return;
        }

        $credit_cards = get_user_meta( get_current_user_id(), '_stripe_customer_id', false );

        if ( empty( $credit_cards ) ) {
            return;
        }

        if ( isset( $_POST['stripe_delete_card'] ) && wp_verify_nonce( $_POST['_wpnonce'], "stripe_del_card" ) ) {

            for( $i = 0; $i < count( $credit_cards ); $i++ ) {
                if ( $credit_cards[ $i ]['customer_id'] == $_POST['stripe_delete_card'] )
                    delete_user_meta( get_current_user_id(), '_stripe_customer_id', $credit_cards[ $i ] );
            }
        }
    }
}

new Dokan_Stripe_Connect_Saved_Cards();
