<?php

class Dokan_ShipStation_Hooks {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'woocommerce_api_wc_shipstation', array( $this, 'init_shipstation_api' ) );
    }

    /**
     * Init ShipStation API
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_shipstation_api() {
        require_once DOKAN_SHIPSTATION_INCLUDES . '/abstract-class-dokan-shipstation-api-request.php';
        require_once DOKAN_SHIPSTATION_INCLUDES . '/class-dokan-shipstation-api.php';

        new Dokan_ShipStation_Api();
    }
}
