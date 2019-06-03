<?php

/**
 * SMS Gateway handler class
 *
 * @author weDevs
 */
class WeDevs_dokan_SMS_Gateways {

    private static $_instance;

    /**
     * Gateway slug
     *
     * @param string $provider name of the gateway
     */
    function __construct() {
        add_filter( 'wedevs_sms_via_smsglobal', array( $this, 'smsGlobalAPI' ) );
        add_filter( 'wedevs_sms_via_clickatell', array( $this, 'clickatellAPI' ) );
        add_filter( 'wedevs_sms_via_twilio', array( $this, 'twilio_api' ) );
        add_filter( 'wedevs_sms_via_nexmo', array( $this, 'nexmo_api' ) );
    }

    public static function instance() {
        if ( !self::$_instance ) {
            self::$_instance = new WeDevs_dokan_SMS_Gateways();
        }

        return self::$_instance;
    }

    /**
     * Get all sms gateways
     *
     * @return array
     */
    function get_gateways() {
        $gateways = array(
            'nexmo'  => array( 'label' => 'Nexmo' ),
            'twilio' => array( 'label' => 'Twilio' ),
        );

        return apply_filters( 'wedevs_dokan_sms_gateways', $gateways );
    }

    /**
     * Check for sms send throttleing
     * Users should not request for sms frquently
     *
     * @return bool false means not send sms now
     */
    function check_throttle() {
        $offset       = (int) wedevs_sms_get_option( 'sms_throttle_offset' ); //minutes
        $sms_throttle = wedevs_sms_get_option( 'sms_throttle' );

        //not enabled? bail out
        if ( $sms_throttle != 'on' ) {
            return true;
        }

        //check users
        if ( is_user_logged_in() ) {
            $last_sent = get_user_meta( get_current_user_id(), 'sms_last_sent', true );
        } else {
            $last_sent = isset( $_COOKIE['sms_last_sent'] ) ? $_COOKIE['sms_last_sent'] : 1;
        }

        if ( $last_sent ) {
            $last_sent = strtotime( $last_sent ) + $offset * 60;
            if ( (time() - $last_sent) > 0 ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set last sms sent time
     */
    function set_last_sent() {
        $last_sent = current_time( 'mysql' );

        if ( is_user_logged_in() ) {
            update_user_meta( get_current_user_id(), 'sms_last_sent', $last_sent );
        } else {
            setcookie( 'sms_last_sent', $last_sent, time() + 86400, '/' );
        }
    }

    function send( $to ) {

        $active_gateway = dokan_get_option( 'active_gateway', 'dokan_verification_sms_gateways' );

        if ( empty( $active_gateway ) ) {
            $response = array(
                'success' => false,
                'message' => 'No active gateway found'
            );

            return $response;
        }

        $code     = rand( 1000, 9999 );
//        $sms_text = wedevs_sms_get_option( 'sms_text' );
        $sms_text = dokan_get_option( 'sms_text', 'dokan_verification_sms_gateways' );
        $sms_text = str_replace( '%CODE%', $code, $sms_text );
        $sms_data = array( 'text' => $sms_text, 'to' => $to, 'code' => $code );

        $status = apply_filters( 'wedevs_sms_via_' . $active_gateway, $sms_data );

        //set last sms sent time
        if ( $status['success'] == true ) {
            $this->set_last_sent( $status );
        }
        
        if ( !isset( $status['success'] ) ) {
            $response = array(
                'success' => false,
                'message' => 'Gateway Not found!!!'
            );
            return $response;
        }

        return $status;
    }

   

    /**
     * Sends SMS via Twillo api
     *
     * @uses `wedevs_sms_via_twilio` filter to fire
     *
     * @param type $sms_data
     * @return boolean
     */
    function twilio_api( $sms_data ) {
        $response = array(
            'success' => false,
            'message' => dokan_get_option( 'sms_sent_error', 'dokan_verification_sms_gateways' ),
        );

        $sid   = dokan_get_option( 'twilio_username', 'dokan_verification_sms_gateways' );
        $token = dokan_get_option( 'twilio_pass', 'dokan_verification_sms_gateways' );
        $from  = dokan_get_option( 'twilio_number', 'dokan_verification_sms_gateways' );

        require_once dirname( __FILE__ ) . '/lib/twilio/Twilio.php';

        $client = new Services_Twilio( $sid, $token );
        try {

            $message = $client->account->messages->sendMessage(
            $from, '+' . $sms_data['to'], $sms_data['text']
            );

            if ( $message->status != 'failed' ) {
                $response = array(
                    'success' => true,
                    'code'    => $sms_data['code'],
                    'message' => dokan_get_option( 'sms_sent_msg', 'dokan_verification_sms_gateways' ),
                );
            }
        } catch ( Exception $exc ) {
            $response['message'] = $exc->getMessage();
        }

        return $response;
    }

    
    /**
    * Sends SMS via Nexmo api
    *
    * @uses `wedevs_sms_via_nexmo` filter to fire
    *
    * @param type $sms_data
    * @return boolean
    */
    function nexmo_api( $sms_data ) {

        $response = array(
            'success' => false,
            'message' => dokan_get_option( 'sms_sent_error', 'dokan_verification_sms_gateways' ),
        );

        $sms_data['number']   = $sms_data['to'];
        $sms_data['sms_body'] = $sms_data['text'];
        //var_dump($sms_data);

        $username = dokan_get_option( 'nexmo_username', 'dokan_verification_sms_gateways' );
        $password = dokan_get_option( 'nexmo_pass', 'dokan_verification_sms_gateways' );
        $from     = dokan_get_option( 'sender_name', 'dokan_verification_sms_gateways' );

        $api_key    = $username;
        $api_secret = $password;

        require_once dirname( __FILE__ ) . '/lib/NexmoMessage.php';

        $nexmo_sms = new NexmoMessage( $api_key, $api_secret );
        $info      = $nexmo_sms->sendText( $sms_data['number'], $from, $sms_data['sms_body'] );


        //  var_dump( 'INFO FROM NEXMO', $info );

        if ( $info->messages[0]->status == '0' ) {
            $response = array(
                'success' => true,
                'code'    => $sms_data['code'],
                'message' => dokan_get_option( 'sms_sent_msg', 'dokan_verification_sms_gateways' ),
            );
        }

        return $response;
    }

}
