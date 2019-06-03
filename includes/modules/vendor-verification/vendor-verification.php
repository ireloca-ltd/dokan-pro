<?php
/*
  Plugin Name: Vendor Verification
  Plugin URI: https://wedevs.com/products/dokan/seller-verification/
  Description: Dokan add-on to verify sellers
  Version: 1.3.0
  Author: weDevs
  Author URI: http://wedevs.com/
  Thumbnail Name: vendor-verification.png
  License: GPL2
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
// don't call the file directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

define( 'DOKAN_VERIFICATION_PLUGIN_VERSION', '1.2.0' );
define( 'DOKAN_VERFICATION_DIR', dirname( __FILE__ ) );
define( 'DOKAN_VERFICATION_INC_DIR', dirname( __FILE__ ) . '/includes/' );
define( 'DOKAN_VERFICATION_LIB_DIR', dirname( __FILE__ ) . '/lib/' );
define( 'DOKAN_VERFICATION_PLUGIN_ASSEST', plugins_url( 'assets', __FILE__ ) );
// give a way to turn off loading styles and scripts from parent theme

if ( !defined( 'DOKAN_VERFICATION_LOAD_STYLE' ) ) {
    define( 'DOKAN_VERFICATION_LOAD_STYLE', true );
}

if ( !defined( 'DOKAN_VERFICATION_LOAD_SCRIPTS' ) ) {
    define( 'DOKAN_VERFICATION_LOAD_SCRIPTS', true );
}

use Hybridauth\Exception\Exception;
use Hybridauth\Hybridauth;
use Hybridauth\HttpClient;
use Hybridauth\Storage\Session;

/**
 * Dokan_Seller_Verification class
 *
 * @class Dokan_Seller_Verification The class that holds the entire Dokan_Seller_Verification plugin
 */
class Dokan_Seller_Verification {

    public static $plugin_prefix;
    public static $plugin_url;
    public static $plugin_path;
    public static $plugin_basename;
    private $config;
    private $base_url;

    public $e_msg = false;

    /**
     * Constructor for the Dokan_Seller_Verification class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {
        self::$plugin_prefix   = 'Dokan_verification_';
        self::$plugin_basename = plugin_basename( __FILE__ );
        self::$plugin_url      = plugin_dir_url( self::$plugin_basename );
        self::$plugin_path     = trailingslashit( dirname( __FILE__ ) );

        add_action( 'init', array( $this, 'init_hooks' ) );

        //includes file
        $this->includes_file();
    }

    public function init_hooks() {
        $installed_version = get_option( 'dokan_theme_version' );

        $this->base_url = dokan_get_navigation_url( 'settings/verification' );
        $this->config = $this->get_provider_config();

        add_action( 'init', array( $this, 'init_session' ) );
        add_action( 'template_redirect', array( $this, 'monitor_autheticate_requests' ), 99 );

        // widget
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );

        // Loads frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        //filters
        add_filter( 'dokan_get_all_cap', array( $this, 'add_capabilities' ), 10 );
        add_filter( 'dokan_get_dashboard_settings_nav', array( $this, 'register_dashboard_menu' ) );
        add_filter( 'dokan_query_var_filter', array( $this, 'dokan_verification_template_endpoint' ) );

        //ajax hooks
        add_action( 'wp_ajax_dokan_update_verify_info', array( $this, 'dokan_update_verify_info' ) );
        add_action( 'wp_ajax_dokan_id_verification_cancel', array( $this, 'dokan_id_verification_cancel' ) );
        add_action( 'wp_ajax_dokan_address_verification_cancel', array( $this, 'dokan_address_verification_cancel' ) );
        add_action( 'wp_ajax_dokan_sv_form_action', array( $this, 'dokan_sv_form_action' ) );
        add_action( 'wp_ajax_dokan_v_load_state_by_country', array( $this, 'dokan_v_load_state_by_country' ) );
        add_action( 'wp_ajax_dokan_update_verify_info_insert_address', array( $this, 'dokan_update_verify_info_insert_address' ) );
        add_action( 'wp_ajax_dokan_v_send_sms', array( $this, 'dokan_v_send_sms' ) );
        add_action( 'wp_ajax_dokan_v_verify_sms_code', array( $this, 'dokan_v_verify_sms_code' ) );

        if ( $installed_version >= 2.4 ) {
            add_filter( 'dokan_dashboard_settings_heading_title', array( $this, 'load_verification_template_header' ), 15, 2 );
            add_action( 'dokan_render_settings_content', array( $this, 'load_verification_content' ) );
        } else {
            add_action( 'dokan_settings_template', array( $this, 'dokan_verification_set_templates' ), 10, 2 );
        }

        add_action( 'dokan_admin_menu', array( $this, 'load_verfication_admin_template' ), 15 );

        // usermeta update hook
        add_action( 'updated_user_meta', array( $this, 'dokan_v_recheck_verification_status_meta' ), 10, 4 );

        // Custom dir for vendor uploaded file
        add_filter( 'upload_dir', array( $this, 'dokan_customize_upload_dir' ), 10 );
    }

    public function get_provider_config() {
        $config = array(
            'callback'   => $this->base_url,
            'debug_mode' => false,

            'providers' => array(
                "Facebook" => array(
                    "enabled" => true,
                    "keys"    => array( "id" => "", "secret" => "" ),
                    "scope"   => "email, public_profile"
                ),
                "Google"   => array(
                    "enabled"         => true,
                    "keys"            => array( "id" => "", "secret" => "" ),
                    "scope"           => "https://www.googleapis.com/auth/userinfo.profile ". // optional
                        "https://www.googleapis.com/auth/userinfo.email"   , // optional
                    "access_type"     => "offline",
                    "approval_prompt" => "force",
                    "hd"              => home_url()
                ),
                "LinkedIn" => array(
                    "enabled" => true,
                    "keys"    => array( "id" => "", "secret" => "" ),
                ),
                "Twitter"  => array(
                    "enabled" => true,
                    "keys"    => array( "key" => "", "secret" => "" ),
                ),
            )
        );

        //facebook config from admin
        $fb_id     = dokan_get_option( 'fb_app_id', 'dokan_verification' );
        $fb_secret = dokan_get_option( 'fb_app_secret', 'dokan_verification' );
        if ( $fb_id != '' && $fb_secret != '' ) {
            $config['providers']['Facebook']['keys']['id']     = $fb_id;
            $config['providers']['Facebook']['keys']['secret'] = $fb_secret;
        }
        //google config from admin
        $g_id     = dokan_get_option( 'google_app_id', 'dokan_verification' );
        $g_secret = dokan_get_option( 'google_app_secret', 'dokan_verification' );
        if ( $g_id != '' && $g_secret != '' ) {
            $config['providers']['Google']['keys']['id']     = $g_id;
            $config['providers']['Google']['keys']['secret'] = $g_secret;
        }
        //linkedin config from admin
        $l_id     = dokan_get_option( 'linkedin_app_id', 'dokan_verification' );
        $l_secret = dokan_get_option( 'linkedin_app_secret', 'dokan_verification' );
        if ( $l_id != '' && $l_secret != '' ) {
            $config['providers']['LinkedIn']['keys']['id']     = $l_id;
            $config['providers']['LinkedIn']['keys']['secret'] = $l_secret;
        }
        //Twitter config from admin
        $twitter_id     = dokan_get_option( 'twitter_app_id', 'dokan_verification' );
        $twitter_secret = dokan_get_option( 'twitter_app_secret', 'dokan_verification' );
        if ( $twitter_id != '' && $twitter_secret != '' ) {
            $config['providers']['Twitter']['keys']['key']    = $twitter_id;
            $config['providers']['Twitter']['keys']['secret'] = $twitter_secret;
        }

        /**
         * Filter the Config array of Hybridauth
         *
         * @since 1.0.0
         *
         * @param array $config
         */
        $config = apply_filters( 'dokan_verify_providers_config', $config );

        return $config;
    }

    function load_verification_template_header( $heading, $query_vars ) {
        if ( isset( $query_vars ) && $query_vars == 'verification' ) {
            $heading = __( 'Verification', 'dokan' );
        }

        return $heading;
    }

    function load_verification_content( $query_vars ) {
        if ( isset( $query_vars['settings'] ) && $query_vars['settings'] == 'verification' ) {
            if (  current_user_can( 'dokan_view_store_verification_menu' ) ) {
                require_once DOKAN_VERFICATION_DIR . '/templates/verification-new.php';
            } else {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this verification page', 'dokan' ) ) );
            }
            return;
        }
    }

    function load_verfication_admin_template() {
        add_submenu_page( 'dokan', __( 'Vendor Verifications', 'dokan' ), __( 'Verifications' ), 'manage_options', 'dokan-seller-verifications', array( $this, 'seller_verfications_page' ) );
    }

    function seller_verfications_page() {
        require_once dirname( __FILE__ ) . '/templates/admin-verifications.php';
    }

    /**
     * Initializes the Dokan_Seller_Verification() class
     *
     * Checks for an existing Dokan_Seller_Verification() instance
     * and if it doesn't find one, creates it.
     *
     * @since 1.0.0
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Seller_Verification();
        }

        return $instance;
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public static function activate() {
        global $wp_roles;

        if ( class_exists( 'WP_Roles' ) && !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        $wp_roles->add_cap( 'seller', 'dokan_view_store_verification_menu' );
        $wp_roles->add_cap( 'administrator', 'dokan_view_store_verification_menu' );
        $wp_roles->add_cap( 'shop_manager', 'dokan_view_store_verification_menu' );
    }

    /**
     * Include all the required files
     *@since 1.0.0
     *
     * @return void
     */
    function includes_file() {
        $inc_dir = DOKAN_VERFICATION_INC_DIR;
        $lib_dir = DOKAN_VERFICATION_LIB_DIR;

        require_once $lib_dir . 'sms-verification/gateways.php';
        require_once $inc_dir . 'theme-functions.php';

        //widgets
        require_once $inc_dir . '/widgets/verifications-list.php';

        if ( is_admin() ) {
            require_once $inc_dir . 'admin/admin.php';
        }
    }

    /**
     * Register widgets
     *
     * @since 2.8
     *
     * @return void
     */
    public function register_widgets() {
        register_widget( 'Dokan_Verification_list' );
    }

    public function init_session() {
        if ( session_id() == '' ) {
            session_start();
        }
    }

    /**
     * Monitors Url for Hauth Request and process Hauth for authentication
     *
     * @global type $current_user
     *
     * @return void
     */
    public function monitor_autheticate_requests() {
        $vendor_id = dokan_get_current_user_id();

        if ( ! $vendor_id ) {
            return;
        }

        if ( isset( $_GET['dokan_auth_dc'] ) ) {
            $seller_profile = dokan_get_store_info( $vendor_id );
            $provider_dc    = sanitize_text_field( $_GET['dokan_auth_dc'] );

            $seller_profile['dokan_verification'][$provider_dc] = '';

            update_user_meta( $vendor_id, 'dokan_profile_settings', $seller_profile );
            return;
        }

        try {
            /**
             * Feed the config array to Hybridauth
             *
             * @var Hybridauth
             */
            $hybridauth = new Hybridauth( $this->config );

            /**
             * Initialize session storage.
             *
             * @var Session
             */
            $storage = new Session();

            /**
             * Hold information about provider when user clicks on Sign In.
             */
            $provider = ! empty( $_GET['dokan_auth'] ) ? $_GET['dokan_auth'] : '';

            if ( $provider ) {
                $storage->set( 'provider', $provider );
            }

            if ( $provider = $storage->get( 'provider' ) ) {
                $adapter = $hybridauth->authenticate( $provider );

                $storage->clear();
            }

            if ( ! isset( $adapter ) ) {
                return;
            }

            $user_profile = $adapter->getUserProfile();

            if ( ! $user_profile ) {
                wc_add_notice( __( 'Something went wrong! please try again', 'dokan' ), 'success' );
                wp_redirect( $this->callback );
            }

            $seller_profile = dokan_get_store_info( $vendor_id );
            $seller_profile['dokan_verification'][$provider] = (array) $user_profile;

            update_user_meta( $vendor_id, 'dokan_profile_settings', $seller_profile );

        } catch ( Exception $e ) {
            $this->e_msg = $e->getMessage();
        }
    }

    /**
     * Enqueue admin scripts
     *
     * Allows plugin assets to be loaded.
     *
     * @uses wp_enqueue_script()
     * @uses wp_localize_script()
     * @uses wp_enqueue_style
     */
    public function enqueue_scripts() {
        global $wp;

        wp_enqueue_style( 'dokan-verification-styles', plugins_url( 'assets/css/style.css', __FILE__ ), true, date( 'Ymd' ) );
        wp_enqueue_script( 'dokan-verification-scripts', plugins_url( 'assets/js/script.js', __FILE__ ), array( 'jquery' ), false, true );

        if ( isset( $wp->query_vars['settings'] ) == 'verification' ) {
            wp_enqueue_script( 'wc-country-select' );
        }
    }

    /**
     * Add capabilities
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_capabilities( $capabilities ) {
        $capabilities['menu']['dokan_view_store_verification_menu'] = __( 'View verification settings menu', 'dokan' );

        return $capabilities;
    }

    /**
     * Adds Verification menu on Dokan Seller Dashboard
     *
     * @since 1.0.0
     * @param array() $urls
     * @return array() $urls
     */
    public function register_dashboard_menu( $urls ) {
        $urls['verification'] = array(
            'title'      => __( 'Verification', 'dokan' ),
            'icon'       => '<i class="fa fa-check"></i>',
            'url'        => dokan_get_navigation_url( 'settings/verification' ),
            'pos'        => 55,
            'permission' => 'dokan_view_store_verification_menu'
        );

        return $urls;
    }

    function dokan_verification_set_templates( $path, $part ) {

        if ( $part == 'verification' ) {
            return DOKAN_VERFICATION_DIR . '/templates/verification.php';
        }

        return $path;
    }

    function dokan_verification_template_endpoint( $query_var ) {
        $query_var[] = 'verification';
        return $query_var;
    }

    /** Updates photo Id for verification
     *
     * @since 1.0.0
     * @return void
     */
    public function dokan_update_verify_info() {
        parse_str( $_POST['data'], $postdata );

        if ( !wp_verify_nonce( $postdata['dokan_verify_action_nonce'], 'dokan_verify_action' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }

        $user_id        = get_current_user_id();
        $seller_profile = dokan_get_store_info( $user_id );

        if ( isset( $postdata['dokan_v_id_type'] ) && isset( $postdata['dokan_gravatar'] ) ) {
            $seller_profile['dokan_verification']['info']['photo_id']          = $postdata['dokan_gravatar'];
            $seller_profile['dokan_verification']['info']['dokan_v_id_type']   = $postdata['dokan_v_id_type'];
            $seller_profile['dokan_verification']['info']['dokan_v_id_status'] = 'pending';

            $msg = sprintf( __( 'Your ID verification request is Sent and %s approval', 'dokan' ), $seller_profile['dokan_verification']['info']['dokan_v_id_status'] );
            dokan_verification_request_submit_email();
        }

        update_user_meta( $user_id, 'dokan_profile_settings', $seller_profile );

        wp_send_json_success( $msg );
    }

    /*
     * Clears Verify Info value for ID verification via AJAX
     *
     * @since 1.0.0
     *
     * @return AJAX Success/fail
     */

    public function dokan_id_verification_cancel() {
        $user_id        = get_current_user_id();
        $seller_profile = dokan_get_store_info( $user_id );

        unset( $seller_profile['dokan_verification']['info']['photo_id'] );
        unset( $seller_profile['dokan_verification']['info']['dokan_v_id_type'] );
        unset( $seller_profile['dokan_verification']['info']['dokan_v_id_status'] );
        //update user meta pending here
        update_user_meta( $user_id, 'dokan_profile_settings', $seller_profile );
        // update_user_meta( $user_id, 'dokan_verification_status', 'cancelled' );

        $msg = __( 'Your ID Verification request is cancelled', 'dokan' );

        wp_send_json_success( $msg );
    }

    /*
     * Clears Verify Info value for Address verification via AJAX
     *
     * @since 1.0.0
     *
     * @return AJAX Success/fail
     */

    public function dokan_address_verification_cancel() {
        $user_id        = get_current_user_id();
        $seller_profile = dokan_get_store_info( $user_id );

        unset( $seller_profile['dokan_verification']['info']['store_address'] );
        //update user meta pending here
        update_user_meta( $user_id, 'dokan_profile_settings', $seller_profile );

        $msg = __( 'Your Address Verification request is cancelled', 'dokan' );

        wp_send_json_success( $msg );
    }

    /* Admin panel verification actions managed here
     * @since 1.0.0
     *
     * @return Ajax Success/fail
     */

    public function dokan_sv_form_action() {

        parse_str( $_POST['formData'], $postdata );
        if ( !wp_verify_nonce( $postdata['dokan_sv_nonce'], 'dokan_sv_nonce_action' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }
        $postdata['status']    = $_POST['status'];
        $postdata['seller_id'] = $_POST['seller_id'];
        $postdata['type']      = $_POST['type'];


        $user_id        = $postdata['seller_id'];
        $seller_profile = dokan_get_store_info( $user_id );

        switch ( $postdata['status'] ) {
            case 'approved':
                if ( $postdata['type'] === 'id' ) {

                    $seller_profile['dokan_verification']['verified_info']['photo'] = array(
                        'photo_id'        => $postdata['dokan_gravatar'],
                        'dokan_v_id_type' => $postdata['dokan_v_id_type'],
                    );

                    $seller_profile['dokan_verification']['info']['dokan_v_id_status'] = 'approved';
                } elseif ( $postdata['type'] === 'address' ) {

                    $seller_profile['dokan_verification']['verified_info']['store_address'] = array(
                        'street_1' => $postdata['street_1'],
                        'street_2' => $postdata['street_2'],
                        'city'     => $postdata['store_city'],
                        'zip'      => $postdata['store_zip'],
                        'country'  => $postdata['store_country'],
                        'state'    => $postdata['store_state'],
                    );
                    $seller_profile['address'] = array(
                        'street_1' => $postdata['street_1'],
                        'street_2' => $postdata['street_2'],
                        'city'     => $postdata['store_city'],
                        'zip'      => $postdata['store_zip'],
                        'country'  => $postdata['store_country'],
                        'state'    => $postdata['store_state'],
                    );

                    $seller_profile['dokan_verification']['info']['store_address']['v_status'] = 'approved';
                }

                update_user_meta( $user_id, 'dokan_profile_settings', $seller_profile );

                break;

            case 'pending' :

                if ( $postdata['type'] === 'id' ) {

                    $seller_profile['dokan_verification']['info']['dokan_v_id_status'] = 'pending';
                } elseif ( $postdata['type'] === 'address' ) {

                    $seller_profile['dokan_verification']['info']['store_address']['v_status'] = 'pending';
                }

                update_user_meta( $user_id, 'dokan_profile_settings', $seller_profile );

                break;

            case 'rejected' :
                if ( $postdata['type'] === 'id' ) {

                    $seller_profile['dokan_verification']['info']['dokan_v_id_status'] = 'rejected';
                } elseif ( $postdata['type'] === 'address' ) {
                    $seller_profile['dokan_verification']['info']['store_address']['v_status'] = 'rejected';
                }

                update_user_meta( $user_id, 'dokan_profile_settings', $seller_profile );

                break;

            case 'disapproved' :

                if ( $postdata['type'] === 'id' ) {

                    unset( $seller_profile['dokan_verification']['verified_info']['photo'] );
                    $seller_profile['dokan_verification']['info']['dokan_v_id_status'] = 'pending';
                } elseif ( $postdata['type'] === 'address' ) {

                    unset( $seller_profile['dokan_verification']['verified_info']['store_address'] );

                    $seller_profile['dokan_verification']['info']['store_address']['v_status'] = 'pending';
                }

                update_user_meta( $user_id, 'dokan_profile_settings', $seller_profile );

                break;
        }

        $msg = __( 'Information updated', 'dokan' );
        wp_send_json_success( $msg );
    }

    /*
     * Insert Verification page Address fields into Verify info via AJAX
     *
     * @since 1.0.0
     *
     * @return Ajax Success/fail
     */

    public function dokan_update_verify_info_insert_address() {

        $address_field = $_POST['dokan_address'];

        if ( !wp_verify_nonce( $_POST['dokan_verify_action_address_form_nonce'], 'dokan_verify_action_address_form' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }

        $current_user   = get_current_user_id();
        $seller_profile = dokan_get_store_info( $current_user );

        $default = array(
            'street_1' => '',
            'street_2' => '',
            'city'     => '',
            'zip'      => '',
            'country'  => '',
            'state'    => '',
            'v_status' => 'pending',
        );

        if ( $address_field['state'] === 'N/A' ) {
            $address_field['state'] = '';
        }

        $store_address = wp_parse_args( $address_field, $default );

        $msg = __('Please fill all the required fields', 'dokan' );

        $seller_profile['dokan_verification']['info']['store_address'] = $store_address;

        update_user_meta( $current_user, 'dokan_profile_settings', $seller_profile );

        $msg = __( 'Your Address verification request is Sent and Pending approval', 'dokan' );

        dokan_verification_request_submit_email();
        wp_send_json_success( $msg );
    }

    /*
     * Sets the value of main verification status meta automatically
     *
     * @since 1.0.0
     *
     * @return void
     *
     */

    public function dokan_v_recheck_verification_status_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {

        if ( $meta_key != 'dokan_profile_settings' ) {
            return;
        }
        $current_user = $object_id;

        $seller_profile = dokan_get_store_info( $current_user );

        if ( !isset( $seller_profile['dokan_verification']['info'] ) ) {
            return;
        }

        $id_status      = isset( $seller_profile['dokan_verification']['info']['dokan_v_id_status'] ) ? $seller_profile['dokan_verification']['info']['dokan_v_id_status'] : '';
        $address_status = isset( $seller_profile['dokan_verification']['info']['store_address']['v_status'] ) ? $seller_profile['dokan_verification']['info']['store_address']['v_status'] : '';

        if ( $id_status === $address_status ) {
            update_user_meta( $current_user, 'dokan_verification_status', $id_status );
        } elseif ( ($id_status === 'pending' || $id_status === 'approved' || $id_status === 'rejected' ) && ( $address_status === 'approved' || $address_status === 'pending' || $address_status === 'rejected' ) ) {
            $st = $id_status . ',' . $address_status;
            update_user_meta( $current_user, 'dokan_verification_status', $st );
        } elseif ( ($id_status === 'pending' || $id_status === 'approved' || $id_status === 'rejected') && $address_status === '' ) {
            update_user_meta( $current_user, 'dokan_verification_status', $id_status );
        } elseif ( $id_status === '' && ( $address_status === 'approved' || $address_status === 'pending' || $address_status === 'rejected' ) ) {
            update_user_meta( $current_user, 'dokan_verification_status', $address_status );
        }


        //clear info meta if empty
        if ( empty( $seller_profile['dokan_verification']['info'] ) ) {
            unset( $seller_profile['dokan_verification']['info'] );
            update_user_meta( $current_user, 'dokan_profile_settings', $seller_profile );
        }

        return;
    }

    /*
     * Sends SMS from verification template
     *
     * @since 1.0.0
     *
     * @return Ajax Success/fail
     *
     */

    public function dokan_v_send_sms() {
        parse_str( $_POST['data'], $postdata );

        if ( !wp_verify_nonce( $postdata['dokan_verify_action_nonce'], 'dokan_verify_action' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }
        $info['success'] = false;

        $sms  = WeDevs_dokan_SMS_Gateways::instance();
        $info = $sms->send( $postdata['phone'] );

        if ( $info['success'] == true ) {

            $current_user   = get_current_user_id();
            $seller_profile = dokan_get_store_info( $current_user );

            $seller_profile['dokan_verification']['info']['phone_no']        = $postdata['phone'];
            $seller_profile['dokan_verification']['info']['phone_code']   = $info['code'];
            $seller_profile['dokan_verification']['info']['phone_status'] = 'pending';

            update_user_meta( $current_user, 'dokan_profile_settings', $seller_profile );
        }
        wp_send_json_success( $info );
    }

    /*
     * Verify sent SMS code and update corresponding meta
     *
     * @since 1.0.0
     *
     * @return Ajax Success/fail
     *
     */
    public function dokan_v_verify_sms_code() {

        parse_str( $_POST['data'], $postdata );

        if ( !wp_verify_nonce( $postdata['dokan_verify_action_nonce'], 'dokan_verify_action' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }

        $current_user   = get_current_user_id();
        $seller_profile = dokan_get_store_info( $current_user );

        $saved_code = $seller_profile['dokan_verification']['info']['phone_code'];

        if ( $saved_code == $postdata['sms_code'] ) {

            $seller_profile['dokan_verification']['info']['phone_status'] = 'verified';
            $seller_profile['dokan_verification']['info']['phone_no'] = $seller_profile['dokan_verification']['info']['phone_no'];
            update_user_meta( $current_user, 'dokan_profile_settings', $seller_profile );

            $resp = array(
                'success' => true,
                'message' => 'Your Phone is verified now',
            );
            wp_send_json_success( $resp );
        } else {
            $resp = array(
                'success' => false,
                'message' => 'Your SMS code is not valid, please try again',
            );
            wp_send_json_success( $resp );
        }
    }

    /*
     * Custom dir for vendor uploaded file
     *
     * @since 2.9.0
     *
     * @return array
     *
     */
    public function dokan_customize_upload_dir( $upload ) {
        global $wp;

        if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
            return $upload;
        }

        if ( strpos( $_SERVER['HTTP_REFERER'], 'settings/verification' ) != false ) {
            $user_id = get_current_user_id();
            $user = get_user_by( 'id', $user_id );
            $dirname = $user_id . '-' . $user->user_login;
            $upload['subdir'] = '/verification/' . $dirname;
            $upload['path']   = $upload['basedir'] . $upload['subdir'];
            $upload['url']    = $upload['baseurl'] . $upload['subdir'];
        }

        return $upload;
    }

}

$dokan_Seller_Verification = Dokan_Seller_Verification::init();

dokan_register_activation_hook( __FILE__, array( 'Dokan_Seller_Verification', 'activate' ) );
