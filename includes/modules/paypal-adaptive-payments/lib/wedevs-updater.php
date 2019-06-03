<?php

if ( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'WeDevs_Plugin_Update_Checker' ) ):

/**
 * WeDevs Plugin update checker
 *
 * @author Tareq Hasan
 * @version 0.1
 */
class WeDevs_Plugin_Update_Checker {

    const base_url = 'http://wedevs.com/';
    // const base_url = 'http://localhost/wedevs/';

    private $file;
    private $product_id;

    function __construct( $file, $product_id = '' ) {
        $this->file = $file;
        $this->product_id = $product_id;

        // Check For Updates
        add_filter( 'pre_set_site_transient_update_plugins', array($this, 'update_check') );

        // Check For Plugin Information
        add_filter( 'plugins_api', array($this, 'plugin_information'), 10, 3 );
    }

    /**
     * Retrieve the license key
     *
     * @return array
     */
    function get_license_key() {
        return get_option( 'wedevs_' . dirname( $this->file ) . '_license', array() );
    }

    /**
     * Check for updates against the remote server.
     *
     * @access public
     * @param  object $transient
     * @return object $transient
     */
    public function update_check( $transient ) {

        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        $response = $this->request( $transient );

        if ( !$response ) {
            return $transient;
        }

        if ( version_compare( $transient->checked[$this->file], $response->latest, '<' ) ) {

            $obj = new stdClass();
            $obj->slug = dirname( $this->file );
            $obj->new_version = $response->latest;
            $obj->url = self::base_url;

            if ( isset( $response->latest_url ) ) {
                $obj->package = $response->latest_url;
            }

            $transient->response[$this->file] = $obj;
        }

        return $transient;
    }

    /**
     * Plugin changelog information popup
     *
     * @param type $false
     * @param type $action
     * @param type $args
     * @return \stdClass|boolean
     */
    function plugin_information( $false, $action, $args ) {

        if ( $action != 'plugin_information' ) {
            return $false;
        }

        if ( isset( $args->slug ) && $args->slug != dirname( $this->file ) ) {
            return $false;
        }

        $remote_info = $this->request();

        $obj = new stdClass();
        $obj->slug = dirname( $this->file );
        $obj->version = $remote_info->latest;
        $obj->homepage = self::base_url;
        $obj->external = self::base_url;

        if ( isset( $remote_info->last_updated ) ) {
            $obj->last_updated = $remote_info->last_updated;
        }

        if ( isset( $remote_info->tested ) ) {
            $obj->tested = $remote_info->tested;
        }

        if ( isset( $remote_info->requires ) ) {
            $obj->requires = $remote_info->requires;
        }

        if ( isset( $remote_info->latest_url ) ) {
            $obj->download_link = $remote_info->latest_url;
        }

        $obj->sections = array(
            'changelog' => '<pre>' . $remote_info->msg . '</pre>'
        );

        return $obj;
    }

    function request( $transient = false ) {
        global $wp_version;

        if ( !$transient ) {
            $transient = get_site_transient( 'update_plugins' );
        }

        $license = $this->get_license_key();

        $params = array(
            'timeout' => 30,
            'headers' => array(),
            'cookies' => array(),
            'sslverify' => false,
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
            'body' => array(
                'name' => $this->file,
                'slug' => dirname( $this->file ),
                'product_id' => $this->product_id,
                'type' => 'plugin',
                'version' => $transient->checked[$this->file],
                'site_url' => home_url( '/' ),
                'license' => isset( $license['key'] ) ? $license['key'] : '',
                'license_email' => isset( $license['email'] ) ? $license['email'] : '',
            )
        );

        $request = wp_remote_post( self::base_url . '?action=wedevs_update_check', $params );
        $response = wp_remote_retrieve_body( $request );

        if ( is_wp_error( $request ) || $request['response']['code'] != 200 ) {
            return false;
        }

        return json_decode( $response );
    }

}

endif;