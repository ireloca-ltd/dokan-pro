<?php
/*
Plugin Name: Product Enquiry
Plugin URI: http://wedevs.com/
Description: Enquiry for a specific product to a seller
Version: 1.0.0
Author: Tareq Hasan
Author URI: http://tareq.wedevs.com/
Thumbnail Name: product-enquiry.png
License: GPL2
*/

/**
 * Copyright (c) 2014 Tareq Hasan (email: tareq@wedevs.com). All rights reserved.
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
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Dokan_Product_Enquiry class
 *
 * @class Dokan_Product_Enquiry The class that holds the entire Dokan_Product_Enquiry plugin
 */
class Dokan_Product_Enquiry {

    /**
     * Constructor for the Dokan_Product_Enquiry class
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
        $this->define_constants();

        add_action( 'wp_ajax_dokan_product_enquiry', array( $this, 'send_email' ) );
        add_action( 'wp_ajax_nopriv_dokan_product_enquiry', array( $this, 'send_email' ) );

        add_filter( 'woocommerce_product_tabs', array( $this, 'register_tab' ), 99 );
        add_filter( 'dokan_settings_fields', array( $this, 'guest_user_settings' ), 10 );

        add_filter( 'dokan_email_classes', array( $this, 'add_email_class' ) );
        add_filter( 'dokan_email_list', array( $this, 'add_email_template_file' ) );
        add_filter( 'dokan_email_actions', array( $this, 'add_email_action' ) );

        // Loads frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Define constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'DOKAN_ENQUIRY_INC', __DIR__ . '/includes' );
        define( 'DOKAN_ENQUIRY_VIEWS', __DIR__ . '/views' );
    }

    /**
     * Initializes the Dokan_Product_Enquiry() class
     *
     * Checks for an existing Dokan_Product_Enquiry() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Product_Enquiry();
        }

        return $instance;
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
        wp_enqueue_script( 'dpe-scripts', plugins_url( 'assets/js/enquiry.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_localize_script( 'dpe-scripts', 'DokanEnquiry', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' )
        ) );

    }

    /**
     * Get user agent string
     *
     * @return string
     */
    function get_user_agent() {
        return substr( $_SERVER['HTTP_USER_AGENT'], 0, 150 );
    }

    /**
     * Get from name for email.
     *
     * @access public
     * @return string
     */
    function get_from_name() {
        return wp_specialchars_decode( esc_html( get_option( 'woocommerce_email_from_name' ) ), ENT_QUOTES );
    }

    /**
     * Get from email address.
     *
     * @access public
     * @return string
     */
    function get_from_address() {
        return sanitize_email( get_option( 'woocommerce_email_from_address' ) );
    }

    /**
     * Send email
     *
     * @since  0.1
     *
     * @return void
     */
    function send_email() {
        check_ajax_referer( 'dokan_product_enquiry' );

        $posted = $_POST;
        $url    = isset( $_POST['url'] ) ? $_POST['url'] : '';

        if ( ! empty( $url ) ) {
            wp_send_json_error( __( 'Boo ya!', 'dokan' ) );
        }

        if ( is_user_logged_in() ) {
            $sender     = wp_get_current_user();
            $customer_name  = $sender->display_name;
            $customer_email = $sender->user_email;
        } else {
            $customer_name  = trim( strip_tags( $posted['author'] ) );
            $customer_email = trim( strip_tags( $posted['email'] ) );
        }

        $message = esc_attr( trim( $posted['enq_message'] ) );

        if ( $message == '' ) {
            wp_send_json_error( 'oops' );
        }

        $product_id = (int) $posted['enquiry_id'];
        $vendor_id  = (int) $posted['seller_id'];
        $vendor     = dokan()->vendor->get( $vendor_id );

        // no seller found
        if ( ! $vendor || is_wp_error( $vendor ) ) {
            $message = sprintf( '<div class="alert alert-success">%s</div>', __( 'Something went wrong!', 'dokan' ) );
            wp_send_json_error( $message );
        }

        // no product found
        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            $message = sprintf( '<div class="alert alert-success">%s</div>', __( 'Something went wrong!', 'dokan' ) );
            wp_send_json_error( $message );
        }

        $email_args = array(
            $vendor,
            $product,
            dokan_get_client_ip(),
            $this->get_user_agent(),
            $customer_name,
            $customer_email,
            $message
        );

        do_action_ref_array( 'dokan_send_enquiry_email', $email_args );

        $success = sprintf( '<div class="alert alert-success">%s</div>', __( 'Email sent successfully!', 'dokan' ) );
        wp_send_json_success( $success );
    }

    /**
     * Register product enquiry tab
     *
     * @since  0.1
     *
     * @param  array $tabs
     *
     * @return array
     */
    function register_tab( $tabs ) {
        global $product, $post;

        $tabs['seller_enquiry_form'] = array(
            'title'    => __( 'Product Enquiry', 'dokan' ),
            'priority' => 29,
            'callback' => array( $this, 'show_form' )
        );

        return $tabs;
    }

    /**
    * Settings for guest users
    *
    * @since 0.2
    *
    * @return void
    **/
    public function guest_user_settings( $settings_fields ) {
        $settings_fields['dokan_selling']['enable_guest_user_enquiry'] = array(
            'name'    => 'enable_guest_user_enquiry',
            'label'   => __( 'Guest Product Enquiry', 'dokan' ),
            'desc'    => __( 'Enable/Disable product enquiry for guest user', 'dokan' ),
            'type'    => 'checkbox',
            'default' => 'on'
        );

        return $settings_fields;
    }

    /**
     * Show enquiry form in single product page tab
     *
     * @since  0.1
     *
     * @return void
     */
    function show_form() {
        global $post;
        $guest_enquiry = dokan_get_option( 'enable_guest_user_enquiry', 'dokan_selling', 'on' );
        ?>

        <h3 style="margin-bottom: 25px;"><?php _e( 'Product Enquiry', 'dokan' ); ?></h3>

        <div class="row">
            <div class="col-md-10">
                <form id="dokan-product-enquiry" method="post" class="form" role="form">
                    <?php if ( ! is_user_logged_in() ) { ?>
                        <div class="row">
                            <?php if ( $guest_enquiry == 'off' ): ?>
                                <div class="col-xs-12 col-md-12 form-group">
                                    <?php _e( 'Please Login to make enquiry about this product', 'dokan' ); ?>
                                </div>
                                <div class="col-xs-12 col-md-12 form-group">
                                    <a class="btn btn-success btn-green btn-flat btn-lg " href="<?php echo add_query_arg( array( 'redirect_to' => get_permalink( $post->ID ) ), wc_get_page_permalink( 'myaccount' ) ); ?>"><?php _e( 'Login', 'dokan' ); ?></a>
                                </div>
                            <?php else: ?>
                                <div class="col-xs-6 col-md-6 form-group">
                                    <input class="form-control" id="name" name="author" placeholder="<?php _e( 'Your Name', 'dokan' ); ?>" type="text" required/>
                                </div>

                                <div class="col-xs-6 col-md-6 form-group">
                                    <input class="form-control" id="email" name="email" placeholder="you@example.com" type="email" required />
                                </div>

                                <input type="url" name="url" value="" style="display:none">
                            <?php endif ?>
                        </div>
                    <?php } ?>
                    <?php if ( $guest_enquiry == 'on' || is_user_logged_in() ): ?>
                        <div class="form-group">
                            <textarea class="form-control" id="dokan-enq-message" name="enq_message" placeholder="<?php _e( 'Details about your enquiry...', 'dokan' ); ?>" rows="5" required></textarea>
                        </div>

                        <?php do_action( 'dokan_product_enquiry_after_form' ); ?>

                        <?php wp_nonce_field( 'dokan_product_enquiry' ); ?>
                        <input type="hidden" name="enquiry_id" value="<?php echo esc_attr( $post->ID ); ?>">
                        <input type="hidden" name="seller_id" value="<?php echo esc_attr( $post->post_author ); ?>">
                        <input type="hidden" name="action" value="dokan_product_enquiry">

                        <input class="dokan-btn dokan-btn-theme" type="submit" value="<?php _e( 'Submit Enquiry', 'dokan' ); ?>">
                    <?php endif ?>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Add email class
     *
     * @param array $classes
     *
     * @return array
     */
    public function add_email_class( $classes ) {
        require_once DOKAN_ENQUIRY_INC . '/dokan-product-enquiry-email.php';

        $classes['Dokan_Product_Enquiry_Email'] = new Dokan_Product_Enquiry_Email();

        return $classes;
    }

    /**
     * Add email template file
     *
     * @param array $templare_files
     *
     * @return array
     */
    public function add_email_template_file( $template_files ) {
        $template_files[] = 'product-enquery-email-html.php';

        return $template_files;
    }

    /**
     * Add eamil aciton
     *
     * @param array $actions
     *
     * @return array
     */
    public function add_email_action( $actions ) {
        $actions[] = 'dokan_send_enquiry_email';

        return $actions;
    }

} // Dokan_Product_Enquiry

Dokan_Product_Enquiry::init();
