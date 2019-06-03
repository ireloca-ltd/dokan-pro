<?php
/*
Plugin Name: Store Support
Plugin URI: https://wedevs.com/products/plugins/dokan/store-support/
Description: Enable vendors to provide support to customers from store page.
Version: 1.3.6
Author: weDevs
Author URI: http://wedevs.com/
Thumbnail Name: store-support.png
License: GPL2
*/

/**
 * Copyright (c) 2015 weDevs Team (email: info@wedevs.com). All rights reserved.
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
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DOKAN_STORE_SUPPORT_PLUGIN_VERSION', '1.3.6' );
define( 'DOKAN_STORE_SUPPORT_DIR', dirname( __FILE__ ) );
define( 'DOKAN_STORE_SUPPORT_PLUGIN_ASSEST', plugins_url( 'assets', __FILE__ ) );

/**
 * Dokan_Store_Support class
 *
 * @class Dokan_Store_Support The class that holds the entire Dokan_Store_Support plugin
 */
class Dokan_Store_Support {

    private $post_type = 'dokan_store_support';
    private $per_page = 15;

    /**
     * Constructor for the Dokan_Store_Support class
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
        add_filter( 'dokan_get_all_cap', array( $this, 'add_capabilities' ), 10 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        $this->init_hooks();

        require_once DOKAN_STORE_SUPPORT_DIR . '/support-widget.php';
    }

    /**
     * Initializes the Dokan_Store_Support() class
     *
     * Checks for an existing Dokan_Store_Support() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Store_Support();
        }

        return $instance;
    }

    /**
     * Initialize all hooks and filters
     *
     * @since  1.0.0
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'init', array( $this, 'register_dokan_store_support' ) ,50 );
        add_action( 'init', array( $this, 'register_dokan_support_topic_status' ), 50 );

        add_action( 'template_redirect', array( $this, 'change_topic_status' ) );

        add_action( 'dokan_after_store_tabs', array( $this, 'generate_support_button' ) );
        add_action( 'dokan_after_load_script', array( $this, 'include_scripts' ) );
        add_action( 'dokan_enqueue_scripts', array( $this, 'include_scripts' ) );

        add_action( 'wp_ajax_dokan_support_ajax_handler', array( $this, 'ajax_handler' ) );
        add_action( 'wp_ajax_nopriv_dokan_support_ajax_handler', array( $this, 'ajax_handler' ) );

        add_filter( 'dokan_query_var_filter', array( $this, 'register_support_queryvar' ), 20 );
        add_filter( 'dokan_get_dashboard_nav', array( $this, 'add_store_support_page' ), 20, 1 );
        add_filter( 'dokan_set_template_path', array( $this, 'load_store_support_templates' ), 11, 3 );
        add_action( 'dokan_load_custom_template', array( $this, 'load_template_from_plugin' ), 20 );
        add_action( 'dokan_rewrite_rules_loaded', array( $this, 'add_rewrite_rules' ) );

        add_filter( 'comment_post_redirect', array( $this, 'redirect_after_comment' ), 15, 2 );
        add_filter( 'edit_comment_link', array( $this, 'remove_comment_edit_link' ), 15, 3 );

        add_action( 'wp_insert_comment', array( $this, 'change_topic_status_on_comment' ), 13, 2 );
        add_action( 'woocommerce_account_menu_items', array( $this, 'place_support_menu' ) );

        add_action( 'dokan_settings_form_bottom', array( $this, 'add_support_btn_title_input' ), 13, 2 );
        add_action( 'dokan_store_profile_saved', array( $this, 'save_supoort_btn_title' ), 13 );

        add_filter( 'woocommerce_locate_template', array( $this, 'customer_topic_list' ),15 );

        add_action( 'widgets_init', array( $this, 'register_widgets' ) );
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

        $all_cap = array(
            'dokan_manage_support_tickets',
        );

        foreach ( $all_cap as $key => $cap ) {
            $wp_roles->add_cap( 'seller', $cap );
            $wp_roles->add_cap( 'administrator', $cap );
            $wp_roles->add_cap( 'shop_manager', $cap );
        }

        $support_url = dokan_get_page_url( 'myaccount', 'woocommerce' ). 'support-tickets/';
        add_option( 'dokan-customer-support' , $support_url );
        set_transient( 'dokan-store-support-activated', 1 );
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
        wp_enqueue_style( 'dokan-store-support-styles', plugins_url( 'assets/css/style.css', __FILE__ ), false, date( 'Ymd' ) );
        wp_enqueue_script( 'dokan-store-support-scripts', plugins_url( 'assets/js/script.js', __FILE__ ), array( 'jquery' ), false, true );
        wp_localize_script( 'dokan-store-support-scripts', 'wait_string', array( 'wait' => __( 'wait...', 'dokan' ) ) );
    }

    /**
     * Initialize scripts after dokan script loaded
     *
     * @since  1.3.4
     *
     * @return void
     */
    function include_scripts() {
        wp_enqueue_style( 'dokan-magnific-popup' );
        wp_enqueue_script( 'dokan-popup' );
    }

    /**
     * Set per page value
     *
     * @since 1.3.5
     *
     * @param type $val
     */
    public function set_per_page( $val ){
        $this->per_page = $val;
    }

    /**
     * Register Custom Post type for support
     * @since 1.0
     * @return void
     */
    function register_dokan_store_support() {

        $labels = array(
            'name'               => __( 'Topics', 'Post Type General Name', 'dokan' ),
            'singular_name'      => __( 'Topic', 'Post Type Singular Name', 'dokan' ),
            'menu_name'          => __( 'Support', 'dokan' ),
            'name_admin_bar'     => __( 'Support', 'dokan' ),
            'parent_item_colon'  => __( 'Parent Item', 'dokan' ),
            'all_items'          => __( 'All Topics', 'dokan' ),
            'add_new_item'       => __( 'Add New Topic', 'dokan' ),
            'add_new'            => __( 'Add New', 'dokan' ),
            'new_item'           => __( 'New Topic', 'dokan' ),
            'edit_item'          => __( 'Edit Topic', 'dokan' ),
            'update_item'        => __( 'Update Topic', 'dokan' ),
            'view_item'          => __( 'View Topic', 'dokan' ),
            'search_items'       => __( 'Search Topic', 'dokan' ),
            'not_found'          => __( 'Not found', 'dokan' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'dokan' ),
        );
        $args   = array(
            'label'             => __( 'Store Support', 'dokan' ),
            'description'       => __( 'Support Topics by customer', 'dokan' ),
            'labels'            => $labels,
            'supports'          => array( 'title', 'author', 'comments', 'editor' ),
            'hierarchical'      => false,
            'public'            => false,
            'show_ui'           => false,
            'show_in_menu'      => false,
            'menu_position'     => 5,
            'show_in_admin_bar' => false,
            'show_in_nav_menus' => false,
            'rewrite'           => array( 'slug' => '' ),
            'can_export'        => true,
            'has_archive'       => true,
        );
        register_post_type( $this->post_type, $args );
    }

    function register_dokan_support_topic_status(){
    	register_post_status( 'open', array(
    		'label'                     => __( 'Open', 'dokan'),
    		'public'                    => true,
    		'exclude_from_search'       => false,
    		'show_in_admin_all_list'    => true,
    		'show_in_admin_status_list' => true,
    		'label_count'               => _n_noop( 'Open <span class="count">(%s)</span>', 'Open <span class="count">(%s)</span>' ),
    	) );

        register_post_status( 'closed', array(
    		'label'                     => __( 'Closed', 'dokan'),
    		'public'                    => true,
    		'exclude_from_search'       => false,
    		'show_in_admin_all_list'    => true,
    		'show_in_admin_status_list' => true,
    		'label_count'               => _n_noop( 'Closed <span class="count">(%s)</span>', 'Closed <span class="count">(%s)</span>' ),
    	) );
    }

    /**
     * Get store support button
     *
     * @since 2.9.7
     *
     * @param int $store_id
     *
     * @return array
     */
    public function get_support_button( $store_id ) {
        $button = array(
            'show'  => false,
            'class' => 'user_logged_out',
            'text'  => '',
        );

        if ( is_user_logged_in() ) {
            $button['class'] = 'user_logged';
        }

        $store_info = dokan_get_store_info( $store_id );

        if ( isset( $store_info['show_support_btn'] ) && 'yes' === $store_info['show_support_btn'] ) {
            $button['show'] = true;
        }

        $button['text'] = isset( $store_info['support_btn_name'] ) && !empty( $store_info['support_btn_name'] ) ? $store_info['support_btn_name'] : __( 'Get Support', 'dokan' );

        return $button;
    }

    /**
     * prints Get support button on store page
     *
     * @since 1.0
     * @param int store_id
     */
    function generate_support_button( $store_id ) {
        $button = $this->get_support_button( $store_id );

        if ( ! $button['show'] ) {
            return;
        }

        ?>
        <li class="dokan-store-support-btn-wrap dokan-right">
            <button data-store_id="<?php echo $store_id; ?>" class="dokan-store-support-btn dokan-btn dokan-btn-theme dokan-btn-sm <?php echo $button['class']; ?>"><?php echo esc_html( $button['text'] ); ?></button>
        </li>
        <?php
    }

    /**
     * Ajax handler for all frontend Ajax submits
     *
     * @since 1.0
     *
     * @return void
     */
    function ajax_handler() {

        switch ( $_POST['data'] ) {
            case 'login_form':
                wp_send_json_success( $this->login_form() );
                break;

            case 'get_support_form':
                wp_send_json_success( $this->get_support_form() );
                break;

            case 'login_data_submit':
                $this->login_data_submit();
                break;

            case 'support_msg_submit':
                $this->support_msg_submit();
                break;

            default:
                wp_send_json_success( '<div>Error!! try again!</div>' );
                break;
        }
    }

    /**
     * generate login form
     * @since 1.0
     * @return string Login form Html
     */
    function login_form() {

        ob_start();
        ?>

        <h2><?php _e( 'Please Login to Continue', 'dokan' ); ?></h2>

        <form class="dokan-form-container" id="dokan-support-login">
            <div class="dokan-form-group">
                <label class="dokan-form-label" for="login-name"><?php _e( 'Username :', 'dokan' ) ?></label>
                <input required class="dokan-form-control" type="text" name='login-name' id='login-name'/>
            </div>
            <div class="dokan-form-group">
                <label class="dokan-form-label" for="login-password"><?php _e( 'Password :', 'dokan' ) ?></label>
                <input required class="dokan-form-control" type="password" name='login-password' id='login-password'/>
            </div>
            <?php wp_nonce_field( 'dokan-support-login-action', 'dokan-support-login-nonce' ); ?>
            <div class="dokan-form-group">
                <input id='support-submit-btn' type="submit" value="<?php _e( 'Login', 'dokan' ) ?>" class="dokan-w5 dokan-btn dokan-btn-theme"/>
            </div>
        </form>
        <div class="dokan-clearfix"></div>
        <?php
        return ob_get_clean();
    }

    /**
     * Generate Support form
     *
     * @since 1.0
     *
     * @return string support form html
     */
    function get_support_form( $seller_id = "" ) {
        global $user_login;

        $seller_id = $seller_id == "" ? ( ( isset( $_POST['store_id'] ) ) ? $_POST['store_id'] : 0 ) : $seller_id;
        wp_get_current_user();

        $customer_orders = apply_filters( 'dokan_store_support_order_id_select_in_form', dokan_get_customer_orders_by_seller( dokan_get_current_user_id() , $seller_id ) );

        ob_start();
        ?>
        <div class="dokan-support-intro-user"><strong><?php printf( __( 'Hi, %s', 'dokan' ), $user_login ) ?></strong></div>
        <div class="dokan-support-intro-text"><?php _e( 'Create a new support topic', 'dokan' ) ?></div>
        <form class="dokan-form-container" id="dokan-support-form">
            <div class="dokan-form-group">
                <label class="dokan-form-label" for="dokan-support-subject"><?php _e( 'Subject :', 'dokan' ) ?></label>
                <input required class="dokan-form-control" type="text" name='dokan-support-subject' id='dokan-support-subject'/>
            </div>
            <div class="dokan-form-group">
                <?php if ( !empty( $customer_orders ) ) :?>
                            <select class="dokan-form-control dokan-select" name="order_id">
                                <option><?php _e( 'Select Order ID', 'dokan' ); ?></option>
                                <?php foreach ( $customer_orders as $order ) {
                                    echo "<option value='$order'>Order #$order</option>";
                                } ?>
                            </select>
                <?php endif; ?>
            </div>

            <div class="dokan-form-group">
                <label class="dokan-form-label" for="dokan-support-msg"><?php _e( 'Message :', 'dokan' ) ?></label>
                <textarea required class="dokan-form-control" name='dokan-support-msg' rows="5" id='dokan-support-msg'></textarea>
            </div>
            <input type="hidden" name='store_id' value="<?php echo $seller_id; ?>" />

            <?php wp_nonce_field( 'dokan-support-form-action', 'dokan-support-form-nonce' ); ?>
            <div class="dokan-form-group">
                <input id='support-submit-btn' type="submit" value="<?php _e( 'Submit', 'dokan' ) ?>" class="dokan-w5 dokan-btn dokan-btn-theme"/>
            </div>
        </form>
        <div class="dokan-clearfix"></div>
        <?php
        return ob_get_clean();
    }

    /**
     * handles login data and signs in user
     * @since 1.0
     * @return string success|failed
     */
    function login_data_submit() {

        parse_str( $_POST['form_data'], $postdata );

        if ( !wp_verify_nonce( $postdata['dokan-support-login-nonce'], 'dokan-support-login-action' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }
        $info                  = array();
        $info['user_login']    = $postdata['login-name'];
        $info['user_password'] = $postdata['login-password'];
        $user_signon           = wp_signon( $info, false );

        if ( is_wp_error( $user_signon ) ) {
            wp_send_json( array(
                'success' => false,
                'msg'     => __( 'Invalid Username or Password', 'dokan' ),
            ) );
        } else {
            wp_send_json( array(
                'success' => true,
                'msg'     => __( 'Logged in', 'dokan' ),
            ) );
        }
    }

    /**
     * Create post from fronend AJAX data
     *
     * @since 1.0
     *
     * @return string success | failed
     */
    function support_msg_submit( $postdata = array() ) {

        if ( empty( $postdata ) ) {

            parse_str( $_POST['form_data'], $postdata );

            if ( !wp_verify_nonce( $postdata['dokan-support-form-nonce'], 'dokan-support-form-action' ) ) {
                wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
            }
        }

        $my_post = array(
            'post_title'     => sanitize_text_field( $postdata['dokan-support-subject'] ),
            'post_content'   => wp_kses_post( $postdata['dokan-support-msg'] ),
            'post_status'    => 'open',
            'post_author'    => dokan_get_current_user_id(),
            'post_type'      => 'dokan_store_support',
            'comment_status' => 'open'
        );

        $post_id = wp_insert_post( apply_filters( 'dss_new_ticket_insert_args', $my_post ) );

        if ( $post_id ) {
            update_post_meta( $post_id, 'store_id', $postdata['store_id'] );
            update_post_meta( $post_id, 'order_id', $postdata['order_id'] );

            $this->send_email_to_seller( $postdata['store_id'], $post_id );

            $success_msg = __( 'Thank you. Your ticket has been submitted!', 'dokan' );

            do_action( 'dss_new_ticket_created', $post_id, $postdata['store_id'] );

            wp_send_json( array(
                'success' => true,
                'msg'     => apply_filters( 'dss_ticket_submission_msg' , $success_msg),
            ) );

        } else {
            $error_msg = __( 'Sorry, something went wrong! Couldn\'t create the ticket.', 'dokan' );
            wp_send_json( array(
                'success' => false,
                'msg'     => apply_filters( 'dss_ticket_submission_error_msg', $error_msg ),
            ) );
        }
    }

    /**
     * Register dokan query vars
     *
     * @since 1.0
     *
     * @param array $vars
     *
     * @return array new $vars
     */
    function register_support_queryvar( $vars ) {
        $vars[] = 'support';
        $vars[] = 'support-tickets';

        return $vars;
    }

    /**
     * Auto flush rewrite
     *
     * @since 1.3.5
     *
     */
    function add_rewrite_rules() {
        if ( get_transient( 'dokan-store-support-activated' ) ) {
            flush_rewrite_rules( true );
            delete_transient( 'dokan-store-support-activated' );
        }
    }

    /**
     * Add menu on seller dashboard
     * @since 1.0
     * @param array $urls
     * @return array $urls
     */
    function add_store_support_page( $urls ) {

        if ( !current_user_can( 'dokan_manage_support_tickets' ) ) {
            return $urls;
        }

        if ( dokan_is_seller_enabled( get_current_user_id() ) ) {

            $counts = $this->topic_count( dokan_get_current_user_id() );
            $count  = 0;
            if ( $counts ) {
                $count = wp_list_pluck( $counts, 'count', 'post_status' );
            }

            $defaults = array(
                'open'   => 0,
                'closed' => 0,
            );

            $count = wp_parse_args( $count, $defaults );

            $open   = $count['open'];
            $closed = $count['closed'];
            $count_text = $open ? ' (' . $open . ')' : '';

            $urls['support'] = array(
                'title' => __( 'Support', 'dokan' ) . $count_text,
                'icon'  => '<i class="fa fa-life-ring"></i>',
                'url'   => dokan_get_navigation_url( 'support' ),
                'pos'   => 199,
            );
        }

        return $urls;
    }

    /**
    * Get plugin path
    *
    * @since 2.8
    *
    * @return void
    **/
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
    * Load Dokan Store support templates
    *
    * @since 2.8
    *
    * @return void
    **/
    public function load_store_support_templates( $template_path, $template, $args ) {
        if ( isset( $args['is_store_support'] ) && $args['is_store_support'] ) {
            return $this->plugin_path() . '/templates';
        }

        return $template_path;
    }

    /**
     * Register page templates
     *
     * @since 1.0
     *
     * @param array $query_vars
     *
     * @return array $query_vars
     */
    function load_template_from_plugin( $query_vars ) {
        if ( isset( $query_vars['support'] ) ) {
            if ( !current_user_can( 'dokan_manage_support_tickets' ) ) {
                dokan_get_template_part( 'global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan' ) ) );
            } else {
                dokan_get_template_part( 'store-support/support', '', array( 'is_store_support' => true ) );
            }
        }

        if ( isset( $query_vars['support-tickets'] ) ) {
            dokan_get_template_part( 'store-support/support-tickets', '', array( 'is_store_support' => true ) );
        }
    }

    /**
     * Query for support topics using seller id
     * @since 1.0
     * @param int $seller_id
     * @return WP_Query $query
     */
    function get_support_topics_by_seller( $seller_id ) {


        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
        $offset  = ( $pagenum - 1 ) * $this->per_page;
        $paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;
        // WP_Query arguments

        $args = array(
            'post_type'  => 'dokan_store_support',
            'posts_per_page' => $this->per_page,
            'offset'         => $offset,
            'paged'          => $paged,
            'meta_query' => array(
                array(
                    'key'     => 'store_id',
                    'value'   => $seller_id,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            ),
        );

        $args = apply_filters( 'dokan_get_topic_by_seller_qry_args', $args );

        $args['post_status'] = 'open';
        if ( isset( $_GET['ticket_status'] ) ) {
            $args['post_status'] = $_GET['ticket_status'];
        }

        // The Query
        $query = new WP_Query( $args );

        $this->total_query_result = $query->found_posts;

        return $query;
    }

    /**
     * Print html of all topics for given seller
     * @since 1.0
     * @param int $seller_id
     * @return void
     */
    function print_support_topics_by_seller( $seller_id ) {

        $query = $this->get_support_topics_by_seller( $seller_id );
        ?>
        <div class="dokan-support-topics-list">
             <?php
                if ( $query->posts ) : ?>
            <table class="dokan-table dokan-support-table">
                <thead>
                    <tr>
                        <th><?php _e( 'Topic', 'dokan' ) ?></th>
                        <th><?php _e( 'Title', 'dokan' ) ?></th>
                        <th><?php _e( 'Customer', 'dokan' ) ?></th>
                        <th><?php _e( 'Status', 'dokan' ) ?></th>
                        <th><?php _e( 'Date', 'dokan' ) ?></th>
                        <th><?php _e( 'Action', 'dokan' ) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    foreach ( $query->posts as $topic ) :
                        $topic_url = trailingslashit( dokan_get_navigation_url( 'support' ) . '' . $topic->ID );
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo $topic_url; ?>"
                                   <strong>
                                       <?php echo '#'.$topic->ID ?>
                                    </strong>
                                </a>
                            </td>
                            <td>
                                <a href="<?php echo $topic_url ?>">
                                    <?php echo $topic->post_title; ?>
                                </a>
                            </td>
                            <td>
                                <div class="dokan-support-customer-name">
                                    <?php echo get_avatar( $topic->post_author, 50 ) ?>
                                    <strong><?php  echo get_user_meta( $topic->post_author, 'nickname', true ); ?></strong>
                                </div>
                            </td>
                             <?php
                                switch ( $topic->post_status ) {
                                    case 'open':
                                        $c_status = __( 'closed', 'dokan' );
                                        $btn_icon = 'fa-close';
                                        $topic_status = 'dokan-label-success';
                                        $btn_title = __( 'close topic' , 'dokan' );
                                        break;
                                    case 'closed':
                                        $c_status = __( 'open', 'dokan' );
                                        $btn_icon = 'fa-file-o';
                                        $topic_status = 'dokan-label-danger';
                                        $btn_title = __( 're-open topic' , 'dokan' );
                                        break;

                                    default:
                                        $c_status = __( 'closed', 'dokan' );
                                        $btn_icon = 'fa-close';
                                        $topic_status = 'dokan-label-success';
                                        $btn_title = __( 'close topic' , 'dokan' );
                                        break;
                                }
                                ?>
                            <td><span class="dokan-label <?php echo $topic_status ?>"><?php echo $topic->post_status; ?></span></td>
                            <td class="dokan-order-date"><span><?php echo get_the_date( 'F j, Y \a\t g:i a' , $topic->ID )?></span></td>
                            <td>
                                <a class="dokan-btn dokan-btn-default dokan-btn-sm tips dokan-support-status-change" onclick="return confirm('Are you sure?');" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'dokan-support-topic-status', 'topic_id' => $topic->ID, 'ticket_status' => $c_status ), dokan_get_navigation_url('support') ), 'dokan-change-topic-status' ); ?>" title="" data-changing_post_id="<?php echo $topic->ID ?>" data-original-title="<?php echo $btn_title ?>"><i class="fa <?php echo $btn_icon ?>">&nbsp;</i></a>
                            </td>
                        </tr>
              <?php endforeach; ?>
          <?php else :?>
                    <div class="dokan-error">
                        <?php _e( 'No tickets found!' , 'dokan' ) ?>
                    </div>
          <?php endif;?>
                </tbody>
            </table>
        </div>
        <?php
        $this->topics_pagination( 'support' );
        wp_reset_postdata();
    }

    /**
     * Query single topic for given seller id
     * @since 1.0
     * @param int $topic_id
     * @param int $seller_id
     * @return WP_Query $query_dss
     */
    function get_single_topic( $topic_id, $seller_id ) {

        $args_t = array(
            'p'          => $topic_id,
            'post_type'  => $this->post_type,
            'meta_query' => array(
                array(
                    'key'     => 'store_id',
                    'value'   => $seller_id,
                    'compare' => '=',
                    'type'    => 'NUMERIC',
                ),
            ),
        );

        $args_t = apply_filters( 'dokan_support_get_single_topic_args', $args_t );

        $query_dss = new WP_Query( $args_t );
        return $query_dss;
    }

    /**
     * Query single topic for given customer id
     *
     * @since 1.0
     * @param int $topic_id
     * @param int $customer_id
     * @return WP_Query $query_dss
     */
    function get_single_topic_by_customer( $topic_id, $customer_id ) {

        $args_t = array(
            'p'         => $topic_id,
            'post_type' => $this->post_type,
            'author'    => $customer_id,
        );

        $query_dss = new WP_Query( $args_t );

        return $query_dss;
    }

    /**
     * Print Html for single topic with given topic object
     * @since 1.0
     * @param object $topic Custom post type 'dokan_store_support' object
     * @return void
     */
    function print_single_topic( $topic ) {
        global $wp;

        $is_customer = 0;
        $back_url    = dokan_get_navigation_url( 'support' );

        if ( isset($wp->query_vars['support-tickets']) ) {
           $is_customer = 1;
           $back_url = trailingslashit( dokan_get_page_url( 'myaccount', 'woocommerce' ). 'support-tickets/' );
        }

        if ( $topic->have_posts() ) {
            while ( $topic->have_posts() ) : $topic->the_post();
            ?>
        <a href="<?php echo $back_url ?>">&larr; <?php _e( 'Back to Tickets' , 'dokan' ); ?></a>
            <div class="dokan-support-single-title">
                <h1><?php the_title() ?></h1>
                <?php
                    $Order_id = get_post_meta( get_the_ID(), 'order_id', true );

                    if ( $Order_id ) {
                    ?>
                    <span class="order-reference" >
                        <h3>
                            <?php echo '<a href="' . wp_nonce_url( add_query_arg( array( 'order_id' => $Order_id ), dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' ) . '"><strong>' . sprintf( __( 'Referenced Order #%s', 'dokan' ), esc_attr( $Order_id) ). '</strong></a>'; ?>
                        </h3>
                    </span>
                    <?php
                    }
                ?>
            </div>
            <div class="dokan-suppport-topic-body dokan-clearfix">
                <div class="dokan-support-user-image dokan-w3">
                    <?php echo get_avatar( get_the_author_meta( 'ID' ), 90 ); ?>

                    <div class="dokan-support-user-name">
                        <h4><?php the_author() ?></h4>
                        <p class="dokan-support-date-time"><?php the_date( 'F j, Y \a\t g:i a' ); ?></p>
                    </div>
                </div>
                <div class="dokan-support-reply dokan-w9">
                    <p><?php the_content(); ?></p>
                </div>
            </div>

            <ul class="dokan-support-commentlist">
                <?php
                $ticket_status = get_post_status( get_the_ID());
                //Gather comments for a specific page/post
                $comments = get_comments( array(
                    'post_id' => get_the_ID(),
                    'status'  => 'approve' //Change this to the type of comments to be displayed
                ) );

                //Display the list of comments
                wp_list_comments( array(
                        'max_depth'         => 0,
                        'page'              => 1,
                        'per_page'          => 5, //Allow comment pagination
                        'reverse_top_level' => true, //Show the latest comments at the top of the list
                        'format'            => 'html5',
                        'callback'          => array( $this, 'support_comment_format' ),
                    ), $comments );
                    ?>
            </ul>
                <?php
            endwhile;
            ?>

            <div class="dokan-panel dokan-panel-default">
                <div class="dokan-panel-heading">
                    <?php
                    $heading = $ticket_status == 'open' ? __( 'Add Reply' , 'dokan' ) : __( 'Ticket Closed' , 'dokan' );
                    ?>
                    <strong><?php echo $heading ?></strong>

                    <?php if ( ! $is_customer && $ticket_status == 'closed' ) {
                        echo '<em>' . __( '(Adding reply will re-open the ticket)', 'dokan' ) . '</em>';
                    } ?>
                </div>

                <div class="dokan-panel-body dokan-support-reply-form">
                    <?php
                    if ( $ticket_status === 'open' || ! $is_customer ) {
                        $comment_textarea = '<p class="comment-form-comment"><label for="Reply ">' . //_x( 'Give Reply ', 'dokan' ) .
                                            '</label><textarea class="comment-textarea" required="required" id="comment" name="comment" rows="3" aria-required="true">' .' '.
                                            '</textarea></p>';
                        $select_topic_status = '<select class="dokan-support-topic-select dokan-form-control dokan-w3" name="dokan-topic-status-change">
                                                    <option value="0">'.  __( '-- Change Status --', 'dokan' ).'</option>
                                                    <option value="1">'. __( 'Close Ticket', 'dokan' ).'</option>
                                                </select>';

                        $comment_field = $comment_textarea;

                        if ( ! $is_customer ) {
                            $comment_field = $comment_textarea . $select_topic_status.'<div class="clearfix"></div>';
                        }

                        $comment_args = array(
                            'id_form'              => 'dokan-support-commentform',
                            'id_submit'            => 'submit',
                            'class_submit'         => 'submit dokan-btn dokan-btn-theme',
                            'name_submit'          => 'submit',
                            'title_reply'          => __( 'Leave a Reply', 'dokan' ),
                            'title_reply_to'       => '',
                            'cancel_reply_link'    => __( 'Cancel Reply', 'dokan' ),
                            'label_submit'         => __( 'Submit Reply', 'dokan' ),
                            'format'               => 'html5',
                            'comment_field'        => $comment_field,
                            'must_log_in'          => '<p class="must-log-in">' .
                                sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink() ) ) ) . '</p>',
                            'logged_in_as'         => '',
                            'comment_notes_before' => '',
                            'comment_notes_after'  => '',
                        );
                        comment_form( $comment_args, get_the_ID() );
                    } else {
                        ?>
                        <div class="dokan-alert dokan-alert-warning">
                            <?php _e( 'This ticket has been closed. Open a new support ticket if you have any further query.', 'dokan' ); ?>
                        </div>
                        <?php
                    }
                    wp_reset_query();
                    ?>
                </div>
            </div>
        <?php
        } else { ?>
            <div class="dokan-error">
                <?php _e( 'Topic not found' , 'dokan' ) ?>
            </div>
        <?php
        }
    }

    /**
     * Redirect users to same topic after a comment is submitted if it is dokan_store_support post type
     * @since 1.0
     * @param string $location
     * @param object $comment
     * @return string location
     */
    function redirect_after_comment( $location, $comment ) {

        if ( get_post_type( $comment->comment_post_ID ) == $this->post_type ) {
            return $_SERVER["HTTP_REFERER"];
        }
        return $location;
    }

    /**
     * Print support topics on customer my account page
     * @since 1.0
     * @return void
     */
    function my_account_support_topics() {
        ?>
        <h2><?php _e( 'Support Tickets', 'dokan' ) ?></h2>
        <div class="dokan-support-topics">
            <a href="<?php echo dokan_get_page_url( 'myaccount', 'woocommerce' ). 'support-tickets/' ; ?>"><button class="dokan-btn dokan-btn-theme"><?php _e( 'View Support Tickets' , 'dokan' ) ?></button></a>
        </div>
        <?php
    }

    /**
     * Prints html of all topics for given customer
     *
     * @since 1.0
     *
     * @param int $customer_id
     *
     * @return void
     */
    function print_support_topics_by_customer( $customer_id ) {

        $query = $this->get_topics_by_customer( $customer_id );
        ?>
        <div class="dokan-support-topics-list">
            <?php if ( $query->posts ) : ?>
            <table class="dokan-table dokan-support-table">
                <thead>
                    <tr>
                        <th><?php _e( 'Topic', 'dokan' ) ?></th>
                        <th><?php _e( 'Store Name', 'dokan' ) ?></th>
                        <th><?php _e( 'Title', 'dokan' ) ?></th>
                        <th><?php _e( 'Status', 'dokan' ) ?></th>
                        <th><?php _e( 'Date', 'dokan' ) ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                foreach ( $query->posts as $topic ) :
                    $topic_url = trailingslashit( dokan_get_page_url( 'myaccount', 'woocommerce' ). 'support-tickets/'  . '' . $topic->ID );

                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo $topic_url; ?>"
                               <strong>
                                   <?php echo '#'.$topic->ID ?>
                                </strong>
                            </a>
                        </td>
                        <td>
                            <div class="dokan-support-customer-name">
                                <?php
                                    $store_info = dokan_get_store_info( $topic->store_id );
                                    $store_name = isset( $store_info['store_name'] ) ? $store_info['store_name'] : get_user_meta( $topic->store_id, 'nickname', true );
                                    $store_url = dokan_get_store_url( $topic->store_id );
                                ?>
                                <strong><a href="<?php echo $store_url ?>" target="_blank"><?php  echo $store_name ?></a></strong>
                            </div>
                        </td>
                        <td>
                            <a href="<?php echo $topic_url ?>">
                                <?php echo $topic->post_title; ?>
                            </a>
                        </td>
                        <?php
                            switch ( $topic->post_status ) {
                                case 'open':
                                    $c_status = __( 'closed', 'dokan' );
                                    $btn_icon = 'fa-close';
                                    $topic_status = 'dokan-label-success';
                                    $btn_title = __( 'close topic' , 'dokan' );
                                    break;
                                case 'closed':
                                    $c_status = __( 'open', 'dokan' );
                                    $btn_icon = 'fa-file-o';
                                    $topic_status = 'dokan-label-danger';
                                    $btn_title = __( 're-open topic' , 'dokan' );
                                    break;

                                default:
                                    $c_status = __( 'closed', 'dokan' );
                                    $btn_icon = 'fa-close';
                                    $topic_status = 'dokan-label-success';
                                    $btn_title = __( 'close topic' , 'dokan' );
                                    break;
                            }
                            ?>
                        <td><span class="dokan-label <?php echo $topic_status ?>"><?php echo $topic->post_status; ?></span></td>

                        <td class="dokan-order-date"> <span><?php echo $topic->post_date?></span></td>
                    </tr>
                <?php endforeach; ?>
                <?php else :?>
                    <div class="dokan-error">
                        <?php _e( 'No tickets found!' , 'dokan' ) ?>
                    </div>
                <?php endif;?>
                </tbody>
            </table>

        </div>
        <?php
        $this->topics_pagination('support-tickets');
        wp_reset_postdata();
    }

    /**
     * Query all topics by given customer
     *
     * @since 1.0
     *
     * @param int $customer_id
     *
     * @return WP_Query $topic_query
     */
    function get_topics_by_customer( $customer_id ) {

        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
        $offset  = ( $pagenum - 1 ) * $this->per_page;
        $paged = (get_query_var( 'paged' )) ? get_query_var( 'paged' ) : 1;

        $args_c = array(
            'author'         => $customer_id,
            'post_type'      => 'dokan_store_support',
            'posts_per_page' => $this->per_page,
            'offset'         => $offset,
            'paged'          => $paged,
            'orderby'        => 'post_date',
            'order'          => 'DESC',

        );
        $args_c['post_status'] = 'open';

        if ( isset( $_GET['ticket_status'] ) ) {
            $args_c['post_status'] = $_GET['ticket_status'];
        }

        $topic_query = new WP_Query( $args_c );

        $this->total_query_result = $topic_query->found_posts;

        return $topic_query;
    }

    /**
     * Disable edit of comments on comment listing
     * @since 1.0
     * @return string
     */
    function remove_comment_edit_link( $link, $comment_id, $text ){
        $comment = get_comment( $comment_id );
        if ( get_post_type( $comment->comment_post_ID ) == $this->post_type ) {
            $link = '';
            return $link;
        }
        return $link;
    }

    /**
     * show pagination on support listing
     * @since 1.0
     *
     * @param array $query_var
     * @return void
     */
    function topics_pagination( $query_var ) {

        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
        $num_of_pages = ceil( $this->total_query_result / $this->per_page );

        if ( is_account_page() ) {
            $base_url = home_url( 'my-account/support-tickets/' );
        } else {
            $base_url = dokan_get_navigation_url( $query_var );
        }

        $page_links = paginate_links( array(
            'base'      => $base_url. '%_%',
            'format'    => '?pagenum=%#%',
            'add_args'  => false,
            'prev_text' => __( '&laquo;', 'aag' ),
            'next_text' => __( '&raquo;', 'aag' ),
            'total'     => $num_of_pages,
            'current'   => $pagenum,
            'type'      => 'array'
        ) );

        if ( $page_links ) {
            echo "<ul class='pagination'>\n\t<li>";
            echo join("</li>\n\t<li>", $page_links);
            echo "</li>\n</ul>\n";
        }
    }

    /**
     * Print comments into formatted html, callback for wp_comment_list function
     *
     * @since 1.0
     */
    function support_comment_format( $comment, $args, $depth ) {

        $GLOBALS['comment'] = $comment; ?>

            <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">

                <div class="dokan-suppport-topic-body">
                    <div class="dokan-support-user-image dokan-w3">
                        <?php echo get_avatar( $comment, 50 ) ?>
                        <div class="dokan-support-user-name">
                            <h4> <?php comment_author() ?> </h4>
                            <p class="dokan-support-date-time">
                                <time>
                                    <?php  echo get_comment_time(). '<span class="human-diff"> ( '. human_time_diff( time(), get_comment_time( 'U' ,true ) ) . ' )</span>' ?>
                                </time>
                            </p>
                        </div>
                    </div>
                    <div class="dokan-support-reply dokan-w8">
                        <p><?php comment_text(); ?></p>
                    </div>
                    <div class="dokan-clearfix"></div>
                </div>
            <?php
        }
    /**
     * Change status of topic from support list action
     * @since 1.0
     * @return void
     */
    function change_topic_status(){

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! dokan_is_user_seller( dokan_get_current_user_id() ) ) {
            return;
        }

        $defaults = array(
            'topic_id' => 0,
            'ticket_status' => '',
        );

        $defaults = wp_parse_args( $_GET, $defaults );

        if ( $defaults['topic_id'] != 0 && $defaults['ticket_status'] != '' ) {
            $post_id = $defaults['topic_id'];
            $status  = $defaults['ticket_status'];

            $my_post = array(
                'ID'          => $post_id,
                'post_status' => $status,
            );
            wp_update_post( $my_post );

            $status = $status == 'open' ? 'closed' : 'open';

            wp_redirect( dokan_get_navigation_url( 'support' )."?ticket_status=$status" );
        }
    }

    /**
     * Change topic status from comment section
     *
     * @param int $comment_id
     * @param obj $comment
     *
     * @return void
     */
    function change_topic_status_on_comment( $comment_id, $comment ) {

        $post_id     = (int) $comment->comment_post_ID;
        $parent_post = get_post( $post_id );

        if ( $parent_post->post_type != $this->post_type){
            return;
        }

        $store_id = get_post_meta( $post_id, 'store_id', true );

        do_action( 'dss_new_comment_inserted', $post_id, $store_id );

        if ( ! isset($_POST['dokan-topic-status-change'] ) ) {
            $this->notify_ticket_author( $comment, $parent_post, true );
            return;
        }

        // override default comment notification
        add_filter( 'comment_notification_recipients', '__return_empty_array', PHP_INT_MAX );
        $this->notify_ticket_author( $comment, $parent_post );

        // if a new comment is on a closed topic by seller, it should re-open
        if ( $parent_post->post_status == 'closed' ) {
            wp_update_post( array(
                'ID'     => $post_id,
                'post_status' => 'open'
            ) );

            return;
        }

        if ( $_POST['dokan-topic-status-change'] == 1 || $_POST['dokan-topic-status-change'] == 2 ) {

            $status  = $_POST['dokan-topic-status-change'] == 1 ? 'closed' : 'open';

            $my_post = array(
                'ID'          => $post_id,
                'post_status' => $status,
            );

            wp_update_post( $my_post );
        }
    }

    /**
     * Send email notification on a new reply
     *
     * @param  object   $comment
     * @param  \WP_Post   $ticket
     * @param  boolean  $to_store
     *
     * @return void
     */
    public function notify_ticket_author( $comment, $ticket, $to_store = false ) {

        $email              = Dokan_Email::init();
        $store_id           = get_post_meta( $ticket->ID, 'store_id', true );
        $store              = dokan_get_store_info( $store_id );
        $store_name         = $store['store_name'];
        $store_email        = get_userdata( $store_id )->user_email;
        $url                = dokan_get_navigation_url( 'support' ) . $ticket->ID;
        $account_ticket_url = trailingslashit( dokan_get_page_url( 'myaccount', 'woocommerce' ) ) . 'support-tickets/' . $ticket->ID;
        $subject            = sprintf( __( '[%s][%d] A New Reply on Your Ticket', 'dokan' ), $email->get_from_name(), $ticket->ID );

        ob_start();

        if ( $to_store ) {
            include dirname( __FILE__ ) . '/templates/email/new-reply-to-store.php';
        } else {
            include dirname( __FILE__ ) . '/templates/email/new-reply-to-user.php';
        }

        $message  = ob_get_clean();
        $search   = array('[ticket-title]', '[account-ticket-url]', '[store-name]', '[store-url]', '[site-name]', '[site-url]', '[dashboard-ticket-url]' );
        $replace  = array( $ticket->post_title, $account_ticket_url, $store_name, dokan_get_store_url( $store_id ), $email->get_from_name(), home_url(), $url );
        $message  = str_replace( $search, $replace, $message );
        $to_email = $to_store ? $store_email : get_userdata( $ticket->post_author )->user_email;

        $email->send( $to_email, $subject, $message );
    }

    /**
     * Support button text input field generator
     *
     * @param int $current_user
     * @param array $profile_info
     *
     * @return void
     */
    function add_support_btn_title_input( $current_user, $profile_info ){

        $support_text = isset( $profile_info['support_btn_name'] ) ? $profile_info['support_btn_name'] : '';
        $enable_support = isset( $profile_info['show_support_btn'] ) ? $profile_info['show_support_btn'] : 'yes';
        ?>
            <div class="dokan-form-group">
                <label class="dokan-w3 dokan-control-label"><?php _e( 'Enable Support' , 'dokan' ) ?></label>
                <div class="dokan-w5 dokan-text-left">
                    <div class="checkbox">
                        <label>
                            <input type="hidden" name="support_checkbox" value="no">
                            <input type="checkbox" id="support_checkbox" name="support_checkbox" value="yes" <?php checked( $enable_support, 'yes' ); ?>><?php  _e( 'Show support button in store', 'dokan'); ?>
                        </label>
                    </div>
                </div>
            </div>

            <div class="dokan-form-group support-enable-check">
                <label class="dokan-w3 dokan-control-label" for="dokan_support_btn_name"><?php _e( 'Support Button text', 'dokan' ); ?></label>

                <div class="dokan-w5 dokan-text-left">
                    <input id="dokan_support_btn_name" value="<?php echo $support_text; ?>" name="dokan_support_btn_name" placeholder="<?php _e( 'Get Support', 'dokan'); ?>" class="dokan-form-control" type="text">
                </div>
            </div>
        <?php
    }

    /**
     * Save support button text on store settings
     *
     * @param int $user_id
     * @param array $profile_info
     */
    function save_supoort_btn_title( $user_id ){

        $profile_info = dokan_get_store_info( $user_id );
        if ( isset( $_POST['dokan_support_btn_name'] ) && isset( $_POST['support_checkbox'] ) ) {
            $profile_info['support_btn_name'] = $_POST['dokan_support_btn_name'];
            $profile_info['show_support_btn'] = $_POST['support_checkbox'];

            update_user_meta( $user_id, 'dokan_profile_settings', $profile_info );
        }
    }

    /**
     * Link My Support Topics for customers under My Account
     *
     * @global object $wp
     *
     * @param object $file
     *
     * @return object
     */
    function customer_topic_list( $file ) {
        global $wp;
        if ( isset($wp->query_vars['support-tickets']) && basename( $file ) == 'my-account.php' ) {
            return DOKAN_STORE_SUPPORT_DIR . '/templates/store-support/support-tickets.php';
        }
        return $file;
    }

    /**
     * Return counts for all topic status count
     *
     * @since 1.0
     *
     * @global $wpdb
     *
     * @param int $store_id
     *
     * @return object|boolean $result
     */
    function topic_count( $store_id ){

        global $wpdb;

        $where = apply_filters( 'dss_topic_count_where_clause',
                                "WHERE tpm.meta_key ='store_id'
                                AND tpm.meta_value = $store_id" );

        $sql = "SELECT `post_status`, count(`ID`) as count
                FROM {$wpdb->posts} as tp
                LEFT JOIN {$wpdb->postmeta} as tpm
                ON tp.ID = tpm.post_id
                $where
                GROUP BY tp.post_status";
        $results = $wpdb->get_results( $sql );

        if ( $results ) {
            return $results;
        }

        return false;
    }

    /**
     * Return counts for all ticket status count for customer
     *
     * @since 1.0
     *
     * @global $wpdb
     *
     * @param int $customer_id
     *
     * @return object|boolean $result
     */
    function topic_count_by_customer( $customer_id ){

        global $wpdb;

        $sql = "SELECT tp.post_status, count( tp.ID ) AS count FROM {$wpdb->posts} AS tp
                WHERE tp.post_author = '$customer_id' AND tp.post_type = '$this->post_type'
                GROUP BY tp.post_status";
        $results = $wpdb->get_results( $sql );

        if ( $results ) {
            return $results;
        }

        return false;
    }

    /**
     * generate support topic status list with count
     * @since 1.0
     * @return void
     */
    function support_topic_status_list( $seller = true ){

        if ( $seller ){
            $counts = $this->topic_count( dokan_get_current_user_id() );
            $redir_url = dokan_get_navigation_url( 'support' );
        } else {
            $counts = $this->topic_count_by_customer( dokan_get_current_user_id() );
            $redir_url = get_permalink( get_option('woocommerce_myaccount_page_id') ).'support-tickets';
        }

        $count = 0;
        if( $counts ){
            $count = wp_list_pluck( $counts, 'count', 'post_status' );
        }

        $defaults = array(
            'open' => 0,
            'closed' => 0,
        );

        $count  = wp_parse_args( $count, $defaults );

        $open   = $count['open'];
        $closed = $count['closed'];
        $all    = $open + $closed;

        $current_status = isset($_GET['ticket_status']) ? $_GET['ticket_status'] : 'open';
        ?>
        <ul class="dokan-support-topic-counts subsubsub">
            <li <?php echo $current_status == 'all' ? 'class = "active"' : ''; ?>>
                <a href="<?php echo $redir_url.'?ticket_status=all' ?>"><?php echo __( 'All Tickets', 'dokan').' ('. $all.') |' ?></a>
            </li>
            <li <?php echo $current_status == 'open' ? 'class = "active"' : ''; ?>>
                <a href="<?php echo $redir_url.'?ticket_status=open' ?>"><?php echo __( 'Open Tickets', 'dokan').' ('. $open.') |' ?></a>
            </li>
            <li <?php echo $current_status == 'closed' ? 'class = "active"' : ''; ?>>
                <a href="<?php echo $redir_url.'?ticket_status=closed' ?>"><?php echo __( 'Closed Tickets', 'dokan').' ('. $closed.')' ?></a>
            </li>
        </ul>
    <?php
    }

    function send_email_to_seller( $store_id, $topic_id ) {

        $email       = Dokan_Email::init();
        $user        = $store_id;
        $store       = dokan_get_store_info( $user );
        $store_name  = $store['store_name'];
        $store_email = get_userdata( $user )->user_email;
        $url         = dokan_get_navigation_url( 'support' ) . $topic_id;
        $subject     = sprintf( __( '[%d] A New Support Ticket', 'dokan' ), $topic_id );

        ob_start();
        include dirname( __FILE__ ) . '/templates/email/new-ticket.php';
        $message = ob_get_clean();

        $search  = array('[store-name]', '[support-dashboard]', '[site-name]', '[site-url]');
        $replace = array( $store_name, $url, $email->get_from_name(), home_url() );
        $message = str_replace( $search, $replace, $message );

        $email->send( $store_email, $subject, $message );

        $sender = get_userdata( dokan_get_current_user_id() );

        do_action( 'dss_ticket_mail_sent', array(
            'to'           => $store_email,
            'subject'      => $subject,
            'message'      => $message,
            'sender_email' => $sender->user_email,
            'sender_name'  => $sender->first_name. ' ' .$sender->last_name,
        ) );
    }

    /**
     * Add Support ticket in My Account Menu
     *
     * @since 1.3.3
     *
     * @param arrat $items
     *
     * @return $items
     */
    function place_support_menu( $items ) {

        unset( $items['customer-logout'] );
        $items['support-tickets']   = __( "Seller Support Tickets", 'dokan' );
        $items['customer-logout']   = __( 'Logout', 'dokan' );

        return $items;
    }

     /**
     * Add capabilities
     *
     * @return void
     */
    public function add_capabilities( $capabilities ) {
        $capabilities['store_support'] = array(
            'dokan_manage_support_tickets' => __( 'Manage support ticket', 'dokan' ),
        );

        return $capabilities;
    }

    /**
     * Register widgets
     *
     * @since 2.8
     *
     * @return void
     */
    public function register_widgets() {
        register_widget( 'Dokan_Store_Support_Widget' );
    }

}

// Dokan_Store_Support
Dokan_Store_Support::init();

dokan_register_activation_hook( __FILE__, array( 'Dokan_Store_Support', 'activate' ) );

