<?php
/**
Plugin Name: WooCommerce Booking Integration
Plugin URI: https://wedevs.com/products/plugins/dokan/woocommerce-booking-integration/
Description: This Plugin Integrates WooCommerce Booking with Dokan.
Version: 1.4.1
Author: weDevs
Author URI: https://wedevs.com
Thumbnail Name: booking.png
License: GPL2
 */

/**
 * Copyright (c) 2016 weDevs (email: info@wedevs.com). All rights reserved.
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

// Define all constant
define( 'DOKAN_WC_BOOKING_PLUGIN_VERSION', '1.4.1' );
define( 'DOKAN_WC_BOOKING_DIR', dirname( __FILE__ ) );
define( 'DOKAN_WC_BOOKING_PLUGIN_ASSEST', plugins_url( 'assets', __FILE__ ) );
define( 'DOKAN_WC_BOOKING_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
define( 'DOKAN_WC_BOOKING_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

/**
 * Dokan_WC_Booking class
 *
 * @class Dokan_WC_Booking The class that holds the entire Dokan_WC_Booking plugin
 */
class Dokan_WC_Booking {

    private $depends_on           = array();
    private $dependency_error     = array();
    private $dependency_not_found = false;

    /**
     * Constructor for the Dokan_WC_Booking class
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
        $this->depends_on['wc_bookings'] = array(
            'name'   => 'WC_Bookings',
            'notice' => sprintf( __( '<b>Dokan WC Booking </b> requires %sWooCommerce Bookings plugin%s to be installed & activated!', 'dokan' ), '<a target="_blank" href="https://woocommerce.com/products/woocommerce-bookings/">', '</a>' ),
        );

        if ( ! $this->check_if_has_dependency() ) {
            add_action( 'admin_notices', array ( $this, 'dependency_notice' ) );
            return;
        }

        add_filter( 'dokan_get_all_cap', array( $this, 'add_capabilities' ), 10 );

        // Loads frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_action( 'init', array( $this, 'init_hooks' ) );

        // insert booking order into dokan_order_table if it's created by admin
        if ( is_admin() ) {
            add_action( 'woocommerce_bookings_create_booking_page_add_order_item', 'dokan_sync_insert_order' );
        }

        add_action( 'dokan_new_product_added', array( $this, 'save_booking_data' ), 10 );
        add_action( 'dokan_product_updated', array( $this, 'save_booking_data' ), 10 );

        // save per product commission for bookable product
        add_action( 'woocommerce_process_product_meta_booking', array( 'Dokan_Pro_Products', 'save_per_product_commission_options' ), 20 );

        //ajax
        add_action( 'wp_ajax_add_new_resource', array( $this, 'add_new_resource' ) );
        add_action( 'wp_ajax_nopriv_add_new_resource', array( $this, 'add_new_resource' ) );
        add_action( 'wp_ajax_delete_resource', array( $this, 'delete_resource' ) );
        add_action( 'wp_ajax_nopriv_delete_resource', array( $this, 'delete_resource' ) );
        add_action( 'wp_ajax_dokan_wc_booking_change_status', array( $this, 'change_booking_status' ) );
        add_action( 'wp_ajax_noprivdokan_wc_booking_change_status', array( $this, 'change_booking_status' ) );

        add_action( 'template_redirect', array( $this, 'update_resource_data' ) );

        //booking modification
        add_action( 'woocommerce_new_booking', array( $this, 'add_seller_id_meta' ) );
        add_action( 'shutdown', array( $this, 'add_seller_manage_cap' ) );
        add_action( 'wp_ajax_dokan-wc-booking-confirm', array( $this, 'mark_booking_confirmed' ) );
        add_action( 'woocommerce_after_order_itemmeta', array( $this, 'booking_display' ), 10, 3 );
        // booking person type delete
        add_action( 'wp_ajax_woocommerce_remove_bookable_person', array( $this, 'dokan_remove_bookable_person' ) );

        // booking page filters
        add_filter( 'dokan_booking_menu', array( $this, 'dokan_get_bookings_menu' ) );
        add_filter( 'dokan_booking_menu_title', array( $this, 'dokan_get_bookings_menu_title' ) );

        add_filter( 'dokan_set_template_path', array( $this, 'load_booking_templates' ), 10, 3 );

        // insert bookable porduct type
        add_filter( 'dokan_get_product_types', array( $this, 'insert_bookable_product_type' ) );
    }

    /**
     * Check whether is their has any dependency or not
     *
     * @return boolean
     */
    function check_if_has_dependency() {
        $res = true;

        foreach ( $this->depends_on as $class ) {
            if ( ! class_exists( $class['name'] ) ) {
                $this->dependency_error[] = $class['notice'];
                $res = false;
                $this->dependency_not_found = true;
            }
        }

        return $res;
    }

    /**
    * Get plugin path
    *
    * @since 2.0
    *
    * @return void
    **/
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
    * Load Dokan Booking templates
    *
    * @since 2.0
    *
    * @return void
    **/
    public function load_booking_templates( $template_path, $template, $args ) {
        if ( isset( $args['is_booking'] ) && $args['is_booking'] ) {
            return $this->plugin_path() . '/templates';
        }

        return $template_path;
    }

    /**
     * Insert bookable product type
     *
     * @param  array $types
     *
     * @return array
     */
    public function insert_bookable_product_type( $types ) {
        $types['booking'] = __( 'Bookable Product', 'dokan' );

        return $types;
    }

    /**
     * print error notice if dependency not active
     *
     * @since 1.0.0
     */
    function dependency_notice() {
        $errors = '';
        $error  = '';
        foreach ( $this->dependency_error as $error ) {
            $errors .= '<p>' . $error . '</p>';
        }
        $message = '<div class="error">' . $errors . '</div>';

        echo $message;
    }

    /**
     * Initializes the Dokan_WC_Booking() class
     *
     * Checks for an existing Dokan_WC_Booking() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_WC_Booking();
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

        $all_cap = array(
            'dokan_view_booking_menu',
            'dokan_add_booking_product',
            'dokan_edit_booking_product',
            'dokan_delete_booking_product',
            'dokan_manage_booking_products',
            'dokan_manage_booking_calendar',
            'dokan_manage_bookings',
            'dokan_manage_booking_resource'
        );

        foreach ( $all_cap as $key => $cap ) {
            $wp_roles->add_cap( 'seller', $cap );
            $wp_roles->add_cap( 'administrator', $cap );
            $wp_roles->add_cap( 'shop_manager', $cap );
        }

        set_transient( 'dokan-wc-booking', 1 );
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

        if ( !is_admin() && isset( $wp->query_vars['booking'] ) ) {
            global $post, $wp_scripts;

            /**
             * All styles goes here
             */
            wp_enqueue_style( 'dokan_wc_booking-styles', plugins_url( 'assets/css/style.css', __FILE__ ), false, date( 'Ymd' ) );
            /**
             * All scripts goes here
             */
            wp_enqueue_script( 'dokan_wc_booking-scripts', plugins_url( 'assets/js/script.js', __FILE__ ), array( 'jquery' ), false, true );

            $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

            $suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_register_script( 'wc_bookings_writepanel_js', DOKAN_WC_BOOKING_PLUGIN_ASSEST . '/js/writepanel.min.js', array( 'jquery', 'jquery-ui-datepicker' ), DOKAN_WC_BOOKING_PLUGIN_VERSION, true );
            wp_register_script( 'wc_bookings_settings_js', WC_BOOKINGS_PLUGIN_URL . '/assets/js/settings' . $suffix . '.js', array( 'jquery' ), WC_BOOKINGS_VERSION, true );
            wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            $post_id = isset( $post->ID ) ? $post->ID : '';

            if ( dokan_is_seller_dashboard() ) {
                if ( isset( $_GET['product_id'] ) ) {
                    $post_id = $_GET['product_id'];
                } else {
                    $post_id = '';
                }
            }

            $params = array(
                'i18n_remove_person'  => esc_js( __( 'Are you sure you want to remove this person type?', 'dokan' ) ),
                'nonce_delete_person' => wp_create_nonce( 'delete-bookable-person' ),
                'nonce_add_person'    => wp_create_nonce( 'add-bookable-person' ),
                'nonce_unlink_person' => wp_create_nonce( 'unlink-bookable-person' ),
                'i18n_remove_resource'  => esc_js( __( 'Are you sure you want to remove this resource?', 'dokan' ) ),
                'nonce_delete_resource' => wp_create_nonce( 'delete-bookable-resource' ),
                'nonce_add_resource'    => wp_create_nonce( 'add-bookable-resource' ),
                'i18n_minutes' => esc_js( __( 'minutes', 'dokan' ) ),
                'i18n_days'    => esc_js( __( 'days', 'dokan' ) ),
                'i18n_new_resource_name' => esc_js( __( 'Enter a name for the new resource', 'dokan' ) ),
                'post'                   => $post_id,
                'plugin_url'             => WC()->plugin_url(),
                'ajax_url'               => admin_url( 'admin-ajax.php' ),
                'calendar_image'         => WC()->plugin_url() . '/assets/images/calendar.png',
            );

            wp_localize_script( 'wc_bookings_writepanel_js', 'wc_bookings_writepanel_js_params', $params );

            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'wc_bookings_writepanel_js' );
            wp_enqueue_script( 'jquery-tiptip' );

            wp_enqueue_style( 'wc_bookings_admin_styles', DOKAN_WC_BOOKING_PLUGIN_ASSEST . '/css/admin.css', null, DOKAN_WC_BOOKING_PLUGIN_VERSION );
            wp_enqueue_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', null, WC_VERSION );
            wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );

            add_filter( 'dokan_dashboard_nav_active', array( $this, 'set_booking_menu_as_active' ) );
        }
    }

    /**
     * Loaded all dependency hooks
     *
     * @return void
     */
    function init_hooks() {
        if ( $this->dependency_not_found ) {
            return;
        }

        add_filter( 'dokan_get_dashboard_nav', array( $this, 'add_booking_page' ), 11, 1 );
        add_action( 'dokan_load_custom_template', array( $this, 'load_template_from_plugin' ) );
        add_filter( 'dokan_query_var_filter', array( $this, 'register_booking_queryvar' ) );
        add_filter( 'dokan_add_new_product_redirect', array( $this, 'set_redirect_url' ), 10, 2 );
        add_filter( 'dokan_product_listing_exclude_type', array( $this, 'exclude_booking_type_from_product_listing' ) );

        add_action( 'dokan_rewrite_rules_loaded', array( $this, 'add_rewrite_rules' ) );

        if ( !class_exists( 'WC_Product_Booking' ) && defined( 'WC_BOOKINGS_MAIN_FILE' ) ) {
            $wcb_path = preg_replace( '(woocommmerce-bookings.php)', '', WC_BOOKINGS_MAIN_FILE );
            include_once $wcb_path . 'includes/class-wc-product-booking.php';
        }

        if ( !is_admin() ) {
            include_once DOKAN_WC_BOOKING_DIR . '/includes/class-wc-booking-calendar.php';
        }

        //override emails
        add_filter( 'woocommerce_email_classes', array( $this, 'setup_emails' ), 12 );
        add_filter( 'woocommerce_email_classes', array( $this, 'load_dokan_booking_cancelled_emails' ), 13 );
        add_filter( 'woocommerce_email_actions', array( $this, 'register_dokan_booking_cancelled_actions' ) );

        //override email receipents
        add_filter( 'woocommerce_email_recipient_new_booking', array( $this, 'set_seller_as_email_recipient' ), 10, 2 );
        //add_filter( 'woocommerce_email_recipient_booking_cancelled', array( $this, 'set_seller_as_email_recipient' ), 10, 2 );

    }
    public function load_dokan_booking_cancelled_emails( $wc_emails ) {
        $wc_emails['Dokan_Email_Booking_Cancelled'] = include( DOKAN_WC_BOOKING_DIR. '/includes/emails/class-dokan-booking-email-cancelled.php' );

        return $wc_emails;
    }
    public function register_dokan_booking_cancelled_actions( $actions ) {
        $actions[] = 'woocommerce_bookings_cancelled_booking';

        return $actions;
    }

    /**
     * Filter template for New Booking Email template path
     *
     * @since 1.1.2
     *
     * @param array $emails
     *
     * @return $emails
     */
    function setup_emails( $emails ) {
        if ( isset( $emails['WC_Email_New_Booking'] ) ) {
            $email = $emails['WC_Email_New_Booking'];

            $email->title       = __( 'Dokan New Booking', 'dokan' );
            $email->description = __( 'New booking emails are sent to the admin when a new booking is created and paid. This email is also received when a Pending confirmation booking is created.', 'dokan' );

            $email->template_base = DOKAN_WC_BOOKING_TEMPLATE_PATH;
            $email->recipient     = "vendor@ofthe.product";
        }

        // if ( isset( $emails['WC_Email_Admin_Booking_Cancelled'] ) ) {
        //     $email = $emails['WC_Email_Admin_Booking_Cancelled'];

        //     $email->title       = __( 'Dokan Booking Cancelled by Admin', 'dokan' );
        //     $email->description = __( 'Booking cancelled Admin emails are sent to vendor when the status of a booking goes to cancelled.', 'dokan' );

        //     $email->template_base = DOKAN_WC_BOOKING_TEMPLATE_PATH;
        //     $email->recipient     = "vendor@ofthe.product";
        // }
        return $emails;
    }

    /**
     * Filter Email recipient for New booking orders
     *
     * @since 1.1.2
     *
     * @param string $recipient
     *
     * @param WC_Booking $booking
     *
     * @return $recipient
     */
    function set_seller_as_email_recipient( $recipient, $booking ) {

        if ( !$booking ) {
            return $recipient;
        }

        $seller     = get_post_field( 'post_author', $booking->product_id );
        $sellerdata = get_userdata( $seller );

        return apply_filters( 'dokan_booking_new_email_recipient', $sellerdata->user_email, $booking );
    }

    /**
     * Add menu on seller dashboard
     * @since 1.0
     * @param array $urls
     * @return array $urls
     */
    function add_booking_page( $urls ) {

        if ( ! current_user_can( 'dokan_view_booking_menu' ) ) {
            return $urls;
        }

        $urls['booking'] = array(
            'title' => __( 'Booking', 'dokan' ),
            'icon'  => '<i class="fa fa-calendar"></i>',
            'url'   => dokan_get_navigation_url( 'booking' ),
            'pos'   => 180,
        );

        return $urls;
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


        if ( isset( $query_vars['booking'] ) ) {
            if ( !current_user_can( 'dokan_view_booking_menu' ) ) {
                dokan_get_template_part( 'global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this booking page', 'dokan' ) ) );
            } else {
                dokan_get_template_part( 'booking/booking', '', array( 'is_booking' => true ) );
            }
            return;
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
    function register_booking_queryvar( $vars ) {
        $vars[] = 'booking';
        return $vars;
    }

    /**
     * Rewrite flush when reactive this plugin
     */
    function add_rewrite_rules() {
        if ( get_transient( 'dokan-wc-booking' ) ) {
            flush_rewrite_rules( true );
            delete_transient( 'dokan-wc-booking' );
        }
    }

    /**
     * Save Booking meta data
     *
     * @since 1.0
     *
     * @global Array $wpdb
     *
     * @param int $post_id
     *
     * @return void
     */
    function save_booking_data( $post_id ) {
        global $wpdb;

        $product_type = empty( $_POST['product_type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product_type'] ) );

        if ( 'booking' !== $product_type ) {
            return;
        }

        $product = new WC_Product_Booking( $post_id );

        if ( !is_a( $product, 'WC_Product_Booking' ) ) {
            return;
        }

        // Save shipping class
        $product_shipping_class = isset( $_POST['product_shipping_class'] ) && $_POST['product_shipping_class'] > 0 ? absint( $_POST['product_shipping_class'] ) : '';
        wp_set_object_terms( $post_id, $product_shipping_class, 'product_shipping_class' );

        $resources = $this->get_posted_resources();
        $posted_props = array(
            'apply_adjacent_buffer'      => isset( $_POST['_wc_booking_apply_adjacent_buffer'] ),
            'availability'               => $this->get_posted_availability(),
            'block_cost'                 => wc_clean( $_POST['_wc_booking_block_cost'] ),
            'buffer_period'              => wc_clean( $_POST['_wc_booking_buffer_period'] ),
            'calendar_display_mode'      => wc_clean( $_POST['_wc_booking_calendar_display_mode'] ),
            'cancel_limit_unit'          => wc_clean( $_POST['_wc_booking_cancel_limit_unit'] ),
            'cancel_limit'               => wc_clean( $_POST['_wc_booking_cancel_limit'] ),
            'check_start_block_only'     => 'start' === $_POST['_wc_booking_check_availability_against'],
            'cost'                       => wc_clean( $_POST['_wc_booking_cost'] ),
            'default_date_availability'  => wc_clean( $_POST['_wc_booking_default_date_availability'] ),
            'display_cost'               => wc_clean( $_POST['_wc_display_cost'] ),
            'duration_type'              => wc_clean( $_POST['_wc_booking_duration_type'] ),
            'duration_unit'              => wc_clean( $_POST['_wc_booking_duration_unit'] ),
            'duration'                   => wc_clean( $_POST['_wc_booking_duration'] ),
            'enable_range_picker'        => isset( $_POST['_wc_booking_enable_range_picker'] ),
            'first_block_time'           => wc_clean( $_POST['_wc_booking_first_block_time'] ),
            'has_person_cost_multiplier' => isset( $_POST['_wc_booking_person_cost_multiplier'] ),
            'has_person_qty_multiplier'  => isset( $_POST['_wc_booking_person_qty_multiplier'] ),
            'has_person_types'           => isset( $_POST['_wc_booking_has_person_types'] ),
            'has_persons'                => isset( $_POST['_wc_booking_has_persons'] ),
            'has_resources'              => isset( $_POST['_wc_booking_has_resources'] ),
            'max_date_unit'              => wc_clean( $_POST['_wc_booking_max_date_unit'] ),
            'max_date_value'             => wc_clean( $_POST['_wc_booking_max_date'] ),
            'max_duration'               => wc_clean( $_POST['_wc_booking_max_duration'] ),
            'max_persons'                => isset( $_POST['_wc_booking_max_persons_group'] ) ? wc_clean( $_POST['_wc_booking_max_persons_group'] ) : '',
            'min_date_unit'              => wc_clean( $_POST['_wc_booking_min_date_unit'] ),
            'min_date_value'             => wc_clean( $_POST['_wc_booking_min_date'] ),
            'min_duration'               => wc_clean( $_POST['_wc_booking_min_duration'] ),
            'min_persons'                => isset( $_POST['_wc_booking_min_persons_group'] ) ? wc_clean( $_POST['_wc_booking_min_persons_group'] ) : '' ,
            'person_types'               => $this->get_posted_person_types( $product ),
            'pricing'                    => $this->get_posted_pricing(),
            'qty'                        => wc_clean( $_POST['_wc_booking_qty'] ),
            'requires_confirmation'      => isset( $_POST['_wc_booking_requires_confirmation'] ),
            'resource_label'             => isset( $_POST['_wc_booking_resource_label'] ) ? wc_clean( $_POST['_wc_booking_resource_label'] ) : '',
            'resource_base_costs'        => wp_list_pluck( $resources, 'base_cost' ),
            'resource_block_costs'       => wp_list_pluck( $resources, 'block_cost' ),
            'resource_ids'               => array_keys( $resources ),
            'resources_assignment'       => isset( $_POST['_wc_booking_resources_assignment'] ) ? wc_clean( $_POST['_wc_booking_resources_assignment'] ) : '',
            'user_can_cancel'            => isset( $_POST['_wc_booking_user_can_cancel'] ),
            'has_restricted_days'        => isset( $_POST['_wc_booking_has_restricted_days' ] ) ? wc_clean( $_POST['_wc_booking_has_restricted_days'] ) : '',
            'restricted_days'            => isset( $_POST['_wc_booking_restricted_days'] ) ? wc_clean( $_POST['_wc_booking_restricted_days'] ) : ''
        );

        $product->set_props( $posted_props );

        $product->save();

        do_action( 'dokan_booking_after_product_data_saved' );
    }

    /**
     * Filter Redirect url after new booking product added
     *
     * @since 1.0
     *
     * @param string $url
     *
     * @param int $product_id
     *
     * @return $url
     */
    function set_redirect_url( $url, $product_id ) {

        $product_type = isset( $_POST['product_type'] ) ? $_POST['product_type'] : '';
        $tab          = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

        if ( 'booking' == $product_type ) {
            $url = add_query_arg( array( 'product_id' => $product_id ), dokan_get_navigation_url( 'booking' ) . 'edit/' );
            return $url;
        }

        if ( 'booking' == $tab ) {
            $url = add_query_arg( array(), dokan_get_navigation_url( 'booking' ) );
            return $url;
        }

        return $url;
    }

    /**
     * Add new resource via ajax
     *
     * @since 1.0
     *
     * @return void
     *
     */
    function add_new_resource() {

        $add_resource_name = wc_clean( $_POST['add_resource_name'] );

        if ( empty( $add_resource_name ) ) {
            wp_send_json_error();
        }

        $resource    = array(
            'post_title'   => $add_resource_name,
            'post_content' => '',
            'post_status'  => 'publish',
            'post_author'  => dokan_get_current_user_id(),
            'post_type'    => 'bookable_resource'
        );
        $resource_id = wp_insert_post( $resource );
        $edit_url    = dokan_get_navigation_url( 'booking' ) . 'resources/edit/?id=' . $resource_id;
        ob_start();
        ?>
        <tr>
            <td><a href="<?php echo $edit_url ?>"><?php echo $add_resource_name ?></a></td>
            <td><?php _e( 'N/A', 'dokan' ); ?></td>
            <td>
                <a class="dokan-btn dokan-btn-sm dokan-btn-theme" href ="<?php echo $edit_url  ?>"><?php _e( 'Edit', 'dokan' );  ?></a>
                <button class="dokan-btn dokan-btn-theme dokan-btn-sm btn-remove" data-id="<?php echo $resource_id ?>"><?php _e( 'Remove', 'dokan' ); ?></button>
            </td>
        </tr>

        <?php
        $output      = ob_get_clean();
        wp_send_json_success( $output );
    }

    /**
     * Update Resource Data via ajax
     *
     * @since 1.0
     *
     * @return void
     */
    function update_resource_data() {
        if ( !isset( $_POST['dokan_booking_resource_update'] ) ) {
            return;
        }

        $post_id = $_POST['resource_id'];

        $post             = get_post( $post_id );
        $post->post_title = $_POST['post_title'];

        wp_update_post( $post );

        // Qty field
        update_post_meta( $post_id, 'qty', wc_clean( $_POST['_wc_booking_qty'] ) );
        // Availability
        $availability = array();
        $row_size     = isset( $_POST["wc_booking_availability_type"] ) ? sizeof( $_POST["wc_booking_availability_type"] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
            $availability[$i]['type']     = wc_clean( $_POST["wc_booking_availability_type"][$i] );
            $availability[$i]['bookable'] = wc_clean( $_POST["wc_booking_availability_bookable"][$i] );
            $availability[$i]['priority'] = intval( $_POST['wc_booking_availability_priority'][$i] );

            switch ( $availability[$i]['type'] ) {
                case 'custom' :
                    $availability[$i]['from'] = wc_clean( $_POST["wc_booking_availability_from_date"][$i] );
                    $availability[$i]['to']   = wc_clean( $_POST["wc_booking_availability_to_date"][$i] );
                    break;
                case 'months' :
                    $availability[$i]['from'] = wc_clean( $_POST["wc_booking_availability_from_month"][$i] );
                    $availability[$i]['to']   = wc_clean( $_POST["wc_booking_availability_to_month"][$i] );
                    break;
                case 'weeks' :
                    $availability[$i]['from'] = wc_clean( $_POST["wc_booking_availability_from_week"][$i] );
                    $availability[$i]['to']   = wc_clean( $_POST["wc_booking_availability_to_week"][$i] );
                    break;
                case 'days' :
                    $availability[$i]['from'] = wc_clean( $_POST["wc_booking_availability_from_day_of_week"][$i] );
                    $availability[$i]['to']   = wc_clean( $_POST["wc_booking_availability_to_day_of_week"][$i] );
                    break;
                case 'time' :
                case 'time:1' :
                case 'time:2' :
                case 'time:3' :
                case 'time:4' :
                case 'time:5' :
                case 'time:6' :
                case 'time:7' :
                    $availability[$i]['from'] = wc_booking_sanitize_time( $_POST["wc_booking_availability_from_time"][$i] );
                    $availability[$i]['to']   = wc_booking_sanitize_time( $_POST["wc_booking_availability_to_time"][$i] );
                    break;
                case 'time:range' :
                    $availability[$i]['from'] = wc_booking_sanitize_time( $_POST["wc_booking_availability_from_time"][$i] );
                    $availability[$i]['to']   = wc_booking_sanitize_time( $_POST["wc_booking_availability_to_time"][$i] );

                    $availability[$i]['from_date'] = wc_clean( $_POST['wc_booking_availability_from_date'][$i] );
                    $availability[$i]['to_date']   = wc_clean( $_POST['wc_booking_availability_to_date'][$i] );
                    break;
            }
        }
        update_post_meta( $post_id, '_wc_booking_availability', $availability );

        $redirect_url = dokan_get_navigation_url( 'booking' ) . 'resources/edit/?id=' . $post_id;
        wp_redirect( add_query_arg( array( 'message' => 'success' ), $redirect_url ) );
    }

    /**
     * Delete Booking resource
     *
     * @since 1.0
     *
     * @return JSON Success | Error
     */
    function delete_resource() {
        $post_id = wc_clean( $_POST['resource_id'] );

        if ( wp_delete_post( $post_id ) ) {
            wp_send_json_success();
        } else {
            wp_send_json_error();
        }
    }

    /**
     * Highlight Booking menu as active on Dokan Dashboard
     *
     * @since 1.0
     *
     * @param string $active_menu
     *
     * @return string
     */
    function set_booking_menu_as_active( $active_menu ) {
        return 'booking';
    }

    /**
     * Add Seller meta to newly created Booking
     *
     * @since 1.0
     *
     * @param int $booking_id Newly created booking id
     *
     * @return void
     */
    function add_seller_id_meta( $booking_id ) {
        $product_id = get_post_meta( $booking_id, '_booking_product_id', true );
        $seller_id  = get_post_field( 'post_author', $product_id );
        update_post_meta( $booking_id, '_booking_seller_id', $seller_id );
    }

    /**
     * Exclude Booking type products from dokan product listing
     *
     * @since 1.0
     *
     * @param array $product_types
     *
     * @return array $product_types
     */
    function exclude_booking_type_from_product_listing( $product_types ) {
        $product_types[] = 'booking';
        return $product_types;
    }

    /**
     * Add Booking Manage capability to seller
     *
     * @since 1.0
     *
     * @global type $wp_roles
     *
     * @return void
     */
    function add_seller_manage_cap() {
        global $wp_roles;

        if ( is_object( $wp_roles ) ) {
            $wp_roles->add_cap( 'seller', 'manage_bookings' );
        }
    }

    /**
     * Confirm bookings from seller dashboard with additional security checks
     *
     * @since 1.0
     *
     * @return void
     *
     */
    function mark_booking_confirmed() {

        if ( !current_user_can( 'manage_bookings' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'dokan' ) );
        }
        if ( !check_admin_referer( 'wc-booking-confirm' ) ) {
            wp_die( __( 'You have taken too long. Please go back and retry.', 'dokan' ) );
        }
        $booking_id = isset( $_GET['booking_id'] ) && (int) $_GET['booking_id'] ? (int) $_GET['booking_id'] : '';
        if ( !$booking_id ) {
            die;
        }

        // Additional check to see if Seller id is same as current user
        $seller = get_post_meta( $booking_id, '_booking_seller_id', true );

        if ( $seller != dokan_get_current_user_id() ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'dokan' ) );
        }

        $booking = get_wc_booking( $booking_id );

        if ( $booking->get_status() !== 'confirmed' ) {
            $booking->update_status( 'confirmed' );
        }

        wp_safe_redirect( wp_get_referer() );
    }

    public static function get_booking_status_counts_by( $seller_id ) {

        global $wpdb;

        $cache_key = 'dokan-wc-booking-status-count-' . $seller_id;
        $counts    = wp_cache_get( $cache_key );

        if ( 1 ) {

            $statuses = array_unique( array_merge( get_wc_booking_statuses(), get_wc_booking_statuses( 'user' ), get_wc_booking_statuses( 'cancel' ) ) );

            $statuses = array_fill_keys( array_keys( array_flip( $statuses ) ), 0 );

            $counts   = $statuses + array( 'total' => 0 );
            $meta_key = '_booking_seller_id';

            $sql = "Select post_status
            From $wpdb->posts as p
            LEFT JOIN $wpdb->postmeta as pm ON p.ID = pm.post_id
            WHERE
            pm.meta_key = %s AND
            pm.meta_value = %d AND
            p.post_status != 'trash' ";

            $results = $wpdb->get_results( $wpdb->prepare( $sql, $meta_key, $seller_id ) );

            if ( $results ) {
                $total = 0;

                foreach ( $results as $status ) {
                    if ( isset( $counts[$status->post_status] ) ) {
                        $counts[$status->post_status] += 1;
                        $counts['total']              += 1;
                    }
                }
            }

            $counts = (object) $counts;

            wp_cache_set( $cache_key, $counts, 'dokan' );
        }

        return $counts;
    }

    function change_booking_status() {

        check_ajax_referer( 'dokan_wc_booking_change_status' );

        $booking_id = intval( $_POST['booking_id'] );
        $booking    = get_wc_booking( $booking_id );

        $status = wc_clean( $_POST['booking_order_status'] );

        if ( $booking->update_status( $status ) ) {
            $html = '<label class="dokan-label dokan-booking-label-' . esc_attr( $status ) . ' ">' . get_post_status_object( $status )->label .  '</label>';

            wp_send_json_success( $html );

        } else {
            echo _e( "Error Occured", 'dokan' );
        }

        exit();
    }

    /**
     * Returns the Booking menu Items
     *
     * @since 1.1
     * @return array
     */
    function dokan_get_bookings_menu( $bookings ) {
        $bookings = array(
            "" => array(
                'title' => __( 'All Booking Product', 'dokan' ),
                'tabs'  => true
            ),
            "new-product"     => array(
                'title' => __( 'Add Booking Product', 'dokan' ),
                'tabs'  => false
            ),
            "my-bookings"     => array(
                'title' => __( 'Manage Bookings', 'dokan' ),
                'tabs'  => current_user_can( 'dokan_manage_bookings' )
            ),
            "calendar"        => array(
                'title' => __( 'Calendar', 'dokan' ),
                'tabs'  => current_user_can( 'dokan_manage_booking_calendar' )
            ),
            "resources"       => array(
                'title' => __( 'Manage Resources', 'dokan' ),
                'tabs'  => current_user_can( 'dokan_manage_booking_resource' )
            ),
            "edit"            => array(
                'title' => __( 'Edit Booking Product', 'dokan' ),
                'tabs'  => false
            ),
            "resources/edit"  => array(
                'title' => __( 'Edit Resource', 'dokan' ),
                'tabs'  => false
            ),
            "booking-details" => array(
                'title' => __( 'Edit Booking', 'dokan' ),
                'tabs'  => false
            )
        );

        return apply_filters( 'dokan_booking_nav_titles', $bookings );
    }

    /**
     * Returns the Booking menu Items Title
     *
     * @since 1.1
     * @return array
     */
    function dokan_get_bookings_menu_title( $current_page ) {

        $menus = apply_filters( 'dokan_booking_menu', '' );

        foreach ( $menus as $key => $value ) {

            if ( $current_page == $key ) {
                $title = $value['title'];
            }
        }
        return $title;
    }


    private function get_posted_availability(){
        $availability = array();
        $row_size     = isset( $_POST['wc_booking_availability_type'] ) ? sizeof( $_POST['wc_booking_availability_type'] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
                $availability[ $i ]['type']     = wc_clean( $_POST['wc_booking_availability_type'][ $i ] );
                $availability[ $i ]['bookable'] = wc_clean( $_POST['wc_booking_availability_bookable'][ $i ] );
                $availability[ $i ]['priority'] = intval( $_POST['wc_booking_availability_priority'][ $i ] );

                switch ( $availability[ $i ]['type'] ) {
                        case 'custom' :
                                $availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_date'][ $i ] );
                                $availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_date'][ $i ] );
                        break;
                        case 'months' :
                                $availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_month'][ $i ] );
                                $availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_month'][ $i ] );
                        break;
                        case 'weeks' :
                                $availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_week'][ $i ] );
                                $availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_week'][ $i ] );
                        break;
                        case 'days' :
                                $availability[ $i ]['from'] = wc_clean( $_POST['wc_booking_availability_from_day_of_week'][ $i ] );
                                $availability[ $i ]['to']   = wc_clean( $_POST['wc_booking_availability_to_day_of_week'][ $i ] );
                        break;
                        case 'time' :
                        case 'time:1' :
                        case 'time:2' :
                        case 'time:3' :
                        case 'time:4' :
                        case 'time:5' :
                        case 'time:6' :
                        case 'time:7' :
                                $availability[ $i ]['from'] = wc_booking_sanitize_time( $_POST['wc_booking_availability_from_time'][ $i ] );
                                $availability[ $i ]['to']   = wc_booking_sanitize_time( $_POST['wc_booking_availability_to_time'][ $i ] );
                        break;
                        case 'time:range' :
                                $availability[ $i ]['from'] = wc_booking_sanitize_time( $_POST['wc_booking_availability_from_time'][ $i ] );
                                $availability[ $i ]['to']   = wc_booking_sanitize_time( $_POST['wc_booking_availability_to_time'][ $i ] );

                                $availability[ $i ]['from_date'] = wc_clean( $_POST['wc_booking_availability_from_date'][ $i ] );
                                $availability[ $i ]['to_date']   = wc_clean( $_POST['wc_booking_availability_to_date'][ $i ] );
                        break;
                }
        }
        return $availability;

    }

    /**
     * Get posted pricing fields and format.
     *
     * @return array
     */
    private function get_posted_pricing() {
        $pricing = array();
        $row_size     = isset( $_POST['wc_booking_pricing_type'] ) ? sizeof( $_POST['wc_booking_pricing_type'] ) : 0;
        for ( $i = 0; $i < $row_size; $i ++ ) {
                $pricing[ $i ]['type']          = wc_clean( $_POST['wc_booking_pricing_type'][ $i ] );
                $pricing[ $i ]['cost']          = wc_clean( $_POST['wc_booking_pricing_cost'][ $i ] );
                $pricing[ $i ]['modifier']      = wc_clean( $_POST['wc_booking_pricing_cost_modifier'][ $i ] );
                $pricing[ $i ]['base_cost']     = wc_clean( $_POST['wc_booking_pricing_base_cost'][ $i ] );
                $pricing[ $i ]['base_modifier'] = wc_clean( $_POST['wc_booking_pricing_base_cost_modifier'][ $i ] );

                switch ( $pricing[ $i ]['type'] ) {
                        case 'custom' :
                                $pricing[ $i ]['from'] = wc_clean( $_POST['wc_booking_pricing_from_date'][ $i ] );
                                $pricing[ $i ]['to']   = wc_clean( $_POST['wc_booking_pricing_to_date'][ $i ] );
                        break;
                        case 'months' :
                                $pricing[ $i ]['from'] = wc_clean( $_POST['wc_booking_pricing_from_month'][ $i ] );
                                $pricing[ $i ]['to']   = wc_clean( $_POST['wc_booking_pricing_to_month'][ $i ] );
                        break;
                        case 'weeks' :
                                $pricing[ $i ]['from'] = wc_clean( $_POST['wc_booking_pricing_from_week'][ $i ] );
                                $pricing[ $i ]['to']   = wc_clean( $_POST['wc_booking_pricing_to_week'][ $i ] );
                        break;
                        case 'days' :
                                $pricing[ $i ]['from'] = wc_clean( $_POST['wc_booking_pricing_from_day_of_week'][ $i ] );
                                $pricing[ $i ]['to']   = wc_clean( $_POST['wc_booking_pricing_to_day_of_week'][ $i ] );
                        break;
                        case 'time' :
                        case 'time:1' :
                        case 'time:2' :
                        case 'time:3' :
                        case 'time:4' :
                        case 'time:5' :
                        case 'time:6' :
                        case 'time:7' :
                                $pricing[ $i ]['from'] = wc_booking_sanitize_time( $_POST['wc_booking_pricing_from_time'][ $i ] );
                                $pricing[ $i ]['to']   = wc_booking_sanitize_time( $_POST['wc_booking_pricing_to_time'][ $i ] );
                        break;
                        case 'time:range' :
                                $pricing[ $i ]['from'] = wc_booking_sanitize_time( $_POST['wc_booking_pricing_from_time'][ $i ] );
                                $pricing[ $i ]['to']   = wc_booking_sanitize_time( $_POST['wc_booking_pricing_to_time'][ $i ] );

                                $pricing[ $i ]['from_date'] = wc_clean( $_POST['wc_booking_pricing_from_date'][ $i ] );
                                $pricing[ $i ]['to_date']   = wc_clean( $_POST['wc_booking_pricing_to_date'][ $i ] );
                        break;
                        default :
                                $pricing[ $i ]['from'] = wc_clean( $_POST['wc_booking_pricing_from'][ $i ] );
                                $pricing[ $i ]['to']   = wc_clean( $_POST['wc_booking_pricing_to'][ $i ] );
                        break;
                }
        }
        return $pricing;
    }

    /**
     * Get posted person types.
     *
     * @return array
     */
    private function get_posted_person_types( $product ) {
        $person_types = array();

        if ( isset( $_POST['person_id'] ) && isset( $_POST['_wc_booking_has_persons'] ) ) {
                $person_ids         = $_POST['person_id'];
                $person_menu_order  = $_POST['person_menu_order'];
                $person_name        = $_POST['person_name'];
                $person_cost        = $_POST['person_cost'];
                $person_block_cost  = $_POST['person_block_cost'];
                $person_description = $_POST['person_description'];
                $person_min         = $_POST['person_min'];
                $person_max         = $_POST['person_max'];
                $max_loop           = max( array_keys( $_POST['person_id'] ) );

                for ( $i = 0; $i <= $max_loop; $i ++ ) {
                        if ( ! isset( $person_ids[ $i ] ) ) {
                                continue;
                        }
                        $person_id   = absint( $person_ids[ $i ] );
                        $person_type = new WC_Product_Booking_Person_Type( $person_id );
                        $person_type->set_props( array(
                                'name'        => wc_clean( stripslashes( $person_name[ $i ] ) ),
                                'description' => wc_clean( stripslashes( $person_description[ $i ] ) ),
                                'sort_order'  => absint( $person_menu_order[ $i ] ),
                                'cost'        => wc_clean( $person_cost[ $i ] ),
                                'block_cost'  => wc_clean( $person_block_cost[ $i ] ),
                                'min'         => wc_clean( $person_min[ $i ] ),
                                'max'         => wc_clean( $person_max[ $i ] ),
                                'parent_id'   => $product->get_id(),
                        ) );
                        $person_types[] = $person_type;
                }
        }
        return $person_types;
    }

    /**
     * Get posted resources. Resources are global, but booking products store information about the relationship.
     *
     * @return array
     */
    private function get_posted_resources( ) {
        $resources = array();

        if ( isset( $_POST['resource_id'] ) && isset( $_POST['_wc_booking_has_resources'] ) ) {
                $resource_ids         = $_POST['resource_id'];
                $resource_menu_order  = $_POST['resource_menu_order'];
                $resource_base_cost   = $_POST['resource_cost'];
                $resource_block_cost  = $_POST['resource_block_cost'];
                $max_loop             = max( array_keys( $_POST['resource_id'] ) );
                $resource_base_costs  = array();
                $resource_block_costs = array();

                foreach ( $resource_menu_order as $key => $value ) {
                        $resources[ absint( $resource_ids[ $key ] ) ] = array(
                                'base_cost'  => wc_clean( $resource_base_cost[ $key ] ),
                                'block_cost' => wc_clean( $resource_block_cost[ $key ] ),
                        );
                }
        }

        return $resources;
    }

    /**
     * Show booking data if a line item is linked to a booking ID.
     */
    public function booking_display( $item_id, $item, $product ) {
        $booking_ids = WC_Booking_Data_Store::get_booking_ids_from_order_item_id( $item_id );
        $booking_details_url = dokan_get_navigation_url( 'booking/booking-details' );
        if ( $booking_ids ) {
            foreach ( $booking_ids as $booking_id ) {
                    $booking = new WC_Booking( $booking_id );
                    ?>
                    <div class="wc-booking-summary" style="width:160%">
                            <strong class="wc-booking-summary-number">
                                    <?php printf( __( 'Booking #%s', 'woocommerce-bookings' ), esc_html( $booking->get_id() ) ); ?>
                                <div>
                                    <span class="status-<?php echo esc_attr( $booking->get_status() ); ?>">
                                            <?php echo esc_html( wc_bookings_get_status_label( $booking->get_status() ) ) ?>
                                    </span>
                                </div>
                            </strong>
                            <?php wc_bookings_get_summary_list( $booking ); ?>
                            <div class="wc-booking-summary-actions">
                                    <?php if ( in_array( $booking->get_status(), array( 'pending-confirmation' ) ) ) : ?>
                                            <a href="<?php echo  wp_nonce_url( admin_url( 'admin-ajax.php?action=dokan-wc-booking-confirm&booking_id=' . $booking->get_id() ), 'wc-booking-confirm' ); ?>"><?php _e( 'Confirm booking', 'woocommerce-bookings' ); ?></a>
                                    <?php endif; ?>

                                    <?php if ( $booking_id ) : ?>
                                            <a href="<?php echo add_query_arg( 'booking_id', $booking_id, $booking_details_url ) ?>"><?php _e( 'View booking &rarr;', 'woocommerce-bookings' ); ?></a>
                                    <?php endif; ?>
                            </div>
                    </div>
                    <?php
            }
        }
    }

    /**
     * Delete bookable person type
     * @since 2.7.3
     */
    public function dokan_remove_bookable_person() {
        if ( ! isset( $_POST['action'] ) && $_POST['action'] != 'woocommerce_remove_bookable_person' ) {
            return;
        }
        if ( ! wp_verify_nonce( $_POST['security'], 'delete-bookable-person' ) ) {
            return;
        }

        wp_delete_post( $_POST['person_id'] );
        exit;
    }

    /**
     * Add capabilities
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_capabilities( $capabilities ) {
        $capabilities['menu']['dokan_view_booking_menu'] = __( 'View booking menu', 'dokan' );

        $capabilities['booking'] = array(
            'dokan_manage_booking_products' => __( 'Manage booking products', 'dokan' ),
            'dokan_manage_booking_calendar' => __( 'Manage booking calendar', 'dokan' ),
            'dokan_manage_bookings'         => __( 'Manage bookings', 'dokan' ),
            'dokan_manage_booking_resource' => __( 'Manage booking resource', 'dokan' ),
            'dokan_add_booking_product'     => __( 'Add booking product', 'dokan' ),
            'dokan_edit_booking_product'    => __( 'Edit booking product', 'dokan' ),
            'dokan_delete_booking_product'  => __( 'Delete booking product', 'dokan' ),
        );

        return $capabilities;
    }
}

// Dokan_WC_Booking
$dokan_wc_booking = Dokan_WC_Booking::init();
dokan_register_activation_hook( __FILE__, array( 'Dokan_WC_Booking', 'activate' ) );
