<?php

abstract class Dokan_ShipStation_Api_Request {

    /**
     * Is logging enabled or not
     *
     * @since 1.0.0
     *
     * @var null
     */
    private $logging_enabled = null;

    /**
     * Stores logger class
     *
     * @since 1.0.0
     *
     * @var WC_Logger
     */
    private $log = null;

    /**
     * Log something
     *
     * @since 1.0.0
     *
     * @param string $message
     */
    public function log( $message ) {
        if ( is_null ( $this->logging_enabled ) ) {
            $this->logging_enabled = dokan_get_option( 'enable_shipstation_logging', 'dokan_selling', 'off' );
        }

        if ( 'off' === $this->logging_enabled ) {
            return;
        }

        if ( is_null( $this->log ) ) {
            $this->log = new WC_Logger();
        }

        $this->log->add( 'dokan-shipstation', $message );
    }

    /**
     * Run the request
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function request() {}

    /**
     * Validate data
     *
     * @since 1.0.0
     *
     * @param array $required_fields fields to look for
     *
     * @return void
     */
    public function validate_input( $required_fields ) {
        foreach ( $required_fields as $required ) {
            if ( empty( $_GET[ $required ] ) ) {
                $this->trigger_error( sprintf( __( 'Missing required param: %s', 'dokan' ), $required ) );
            }
        }
    }

    /**
     * Trigger and log an error
     *
     * @since 1.0.0
     *
     * @param string $message
     *
     * @return void
     */
    public function trigger_error( $message ) {
        $this->log( $message );
        wp_send_json_error( $message );
    }
}
