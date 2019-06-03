<?php

class Dokan_Follow_Store_Scripts {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Enqueue module scripts
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'dokan-follow-store', DOKAN_FOLLOW_STORE_ASSETS . '/css/follow-store.css', array( 'dokan-style', 'dokan-fontawesome' ), DOKAN_FOLLOW_STORE_VERSION );

        $dokan_follow_store = array(
            '_nonce'        => wp_create_nonce( 'dokan_follow_store' ),
            'button_labels' => dokan_follow_store_button_labels(),
        );

        wp_enqueue_script( 'dokan-follow-store', DOKAN_FOLLOW_STORE_ASSETS . '/js/follow-store.js', array( 'jquery' ), DOKAN_FOLLOW_STORE_VERSION, true );
        wp_localize_script( 'dokan-follow-store', 'dokanFollowStore', $dokan_follow_store );
    }
}
