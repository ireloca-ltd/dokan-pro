<?php

/**
 * @see https://help.shipstation.com/hc/en-us/articles/205928478#2 Documentation on ShipStation request endpoints
 */
class Dokan_ShipStation_Api extends Dokan_ShipStation_Api_Request {

    /**
     * Stores whether or not shipstation has been authenticated
     *
     * @since 1.0.0
     *
     * @var bool
     */
    private static $authenticated = false;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        nocache_headers();

        if ( ! defined( 'DONOTCACHEPAGE' ) ) {
            define( "DONOTCACHEPAGE", "true" );
        }

        if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
            define( "DONOTCACHEOBJECT", "true" );
        }

        if ( ! defined( 'DONOTCACHEDB' ) ) {
            define( "DONOTCACHEDB", "true" );
        }

        self::$authenticated = false;

        $this->request();
    }

    /**
     * Has API been authenticated?
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public static function authenticated() {
        return self::$authenticated;
    }

    /**
     * Handle the request
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function request() {
        if ( empty( $_GET['auth_key'] ) ) {
            $this->trigger_error( __( 'Authentication key is required!', 'dokan' ) );
        }

        $auth_key = sanitize_text_field( $_GET['auth_key'] );

        $args = array(
            'role'       => 'seller',
            'meta_key'   => 'shipstation_auth_key',
            'meta_value' => $auth_key,
        );

        $user_query = new WP_User_Query( $args );
        $sellers = $user_query->get_results();

        if ( empty( $sellers ) ) {
            $this->trigger_error( __( 'Invalid authentication key', 'dokan' ) );
        }

        $seller = array_pop( $sellers );

        $request = $_GET;

        if ( isset( $request['action'] ) ) {
            $this->request = array_map( 'sanitize_text_field', $request );
        } else {
            $this->trigger_error( __( 'Invalid request', 'dokan' ) );
        }

        self::$authenticated = true;

        if ( in_array( $this->request['action'], array( 'export', 'shipnotify' ) ) ) {
            $this->log( sprintf( __( 'Input params: %s', 'dokan' ), http_build_query( $this->request ) ) );

            if ( 'export' === $this->request['action'] ) {
                require_once DOKAN_SHIPSTATION_INCLUDES . '/class-dokan-shipstation-api-export.php';
                $request_class = new Dokan_ShipStation_Api_Export( self::$authenticated, $seller );

            } else {
                require_once DOKAN_SHIPSTATION_INCLUDES . '/class-dokan-shipstation-api-shipnotify.php';
                $request_class = new Dokan_ShipStation_Api_ShipNotify( self::$authenticated, $seller );
            }

            $request_class->request();

        } else {
            $this->trigger_error( __( 'Invalid request', 'dokan' ) );
        }

        exit;
    }
}
