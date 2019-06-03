<?php
/*
Plugin Name: Auction Integration
Plugin URI: https://wedevs.com/products/dokan/dokan-simple-auctions/
Description: A plugin that combined WooCommerce simple auction and Dokan plugin
Version: 1.5.2
Author: Sabbir Ahmed
Author URI: https://wedevs.com/
Thumbnail Name: auction.png
*/

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

define( 'DOKAN_AUCTION_DIR', dirname( __FILE__ ) );

/**
 * Dokan_Auction class
 *
 * @class Dokan_Auction The class that holds the entire Dokan_Auction plugin
 */
class Dokan_Auction {

    private $depends_on = array();
    private $dependency_error = array();

    /**
     * Constructor for the Dokan_Auction class
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

        $this->depends_on['WooCommerce_simple_auction'] = array(
            'name'   => 'WooCommerce_simple_auction',
            'notice' => sprintf( __( '<b>Auction Integration </b> requires %sWooCommerce Simple Auctions plugin%s to be installed & activated first !' , 'dokan' ), '<a target="_blank" href="https://codecanyon.net/item/woocommerce-simple-auctions-wordpress-auctions/6811382">', '</a>' ),
        );

        if ( ! $this->check_if_has_dependency() ) {
            add_action( 'admin_notices', array ( $this, 'dependency_notice' ) );
            return;
        }

        $this->includes();

        // Hooking all caps
        add_filter( 'dokan_get_all_cap', array( $this, 'add_capabilities' ), 10 );

        // insert auction porduct type
        add_filter( 'dokan_get_product_types', array( $this, 'insert_auction_product_type' ) );

        // Loads frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'dokan_seller_meta_fields', array( $this, 'add_admin_user_options' ) );
        add_action( 'dokan_process_seller_meta_fields', array( $this, 'save_admin_user_option' ) );
        add_filter( 'dokan_get_dashboard_nav', array( $this, 'add_auction_dashboad_menu' ), 20, 1 );
        add_filter( 'dokan_settings_fields', array( $this, 'add_auction_dokan_settings' ) );
        add_filter( 'dokan_query_var_filter', array( $this, 'add_dokan_auction_endpoint' ) );
        add_filter( 'dokan_set_template_path', array( $this, 'load_auction_templates' ), 10, 3 );
        add_action( 'dokan_load_custom_template', array( $this, 'load_dokan_auction_template'), 10, 1 );
        add_action( 'user_register', array( $this, 'dokan_admin_user_register_enable_auction' ), 16 );
        add_action( 'dokan_product_listing_exclude_type', array( $this, 'product_listing_exclude_auction' ), 11 );
        add_action( 'dokan_rewrite_rules_loaded', array( $this, 'add_rewrite_rules' ) );

        add_filter( 'dokan_dashboard_nav_active', array( $this, 'dashboard_auction_active_menu' ) );
        // dokan simple auciton email
        add_filter( 'woocommerce_email_classes', array( $this, 'load_auction_email_class' ) );
        add_filter( 'dokan_email_actions', array( $this, 'register_auction_email_action') );

        // send bid email to admin and vendor
        add_filter( 'woocommerce_email_recipient_bid_note', array( $this, 'send_bid_email' ), 99, 2 );

        add_filter( 'dokan_localized_args', array( $this, 'set_localized_args' ) );
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
            }
        }

        return $res;
    }

     /*
     * print error notice if dependency not active
     * @since 1.5.0
     */
    function dependency_notice(){
        $errors = '';
        $error = '';
        foreach ( $this->dependency_error as $error ) {
            $errors .= '<p>' . $error . '</p>';
        }
        $message = '<div class="error">' . $errors . '</div>';

        echo $message;
    }

    /**
     * Initializes the Dokan_Auction() class
     *
     * Checks for an existing Dokan_Auction() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Auction();
        }

        return $instance;
    }

    /**
     * Register activation hook
     *
     * @since  1.5.2
     *
     * @return void
     */
    public static function activate() {
        global $wp_roles;
        set_transient( 'dokan-auction', 1 );

        if ( class_exists( 'WP_Roles' ) && !isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        $all_cap = array(
            'dokan_view_auction_menu',
            'dokan_add_auction_product',
            'dokan_edit_auction_product',
            'dokan_delete_auction_product'
        );

        foreach ( $all_cap as $key => $cap ) {
            $wp_roles->add_cap( 'seller', $cap );
            $wp_roles->add_cap( 'administrator', $cap );
            $wp_roles->add_cap( 'shop_manager', $cap );
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
        $capabilities['menu']['dokan_view_auction_menu'] = __( 'View auction menu');

        $capabilities['auction'] = array(
            'dokan_add_auction_product'    => __( 'Add auction product', 'dokan' ),
            'dokan_edit_auction_product'   => __( 'Edit auction product', 'dokan' ),
            'dokan_delete_auction_product' => __( 'Delete auction product', 'dokan' ),
        );

        return $capabilities;
    }

    /**
     * Insert auction product type
     *
     * @param  array $types
     *
     * @return array
     */
    public function insert_auction_product_type( $types ) {
        $types['auction'] = __( 'Auction Product', 'dokan' );

        return $types;
    }

    /**
    * Include files
    *
    * @since 1.5.0
    *
    * @return void
    **/
    public function includes() {
        require_once dirname(__FILE__) . '/classes/class-auction.php';
        require_once dirname(__FILE__) . '/includes/dokan-auction-functions.php';
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

        wp_enqueue_style( 'dokan-auction-styles', plugins_url( 'assets/css/dokan-auction-style.css', __FILE__ ), false, date( 'Ymd' ) );

        if( isset( $wp->query_vars['new-auction-product'] ) ) {
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'dokan-auctiondasd-timepicker', plugins_url( 'assets/js/jquery-ui-timepicker.js', __FILE__ ), array( 'jquery' ), false, true );
        }

        if( isset( $wp->query_vars['auction'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'dokan-auctiondasd-timepicker', plugins_url( 'assets/js/jquery-ui-timepicker.js', __FILE__ ), array( 'jquery' ), false, true );
            wp_enqueue_media();
            wp_enqueue_script( 'dokan-script' );
        }
    }

    /**
    * Get plugin path
    *
    * @since 1.5.1
    *
    * @return void
    **/
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Show auction action in user profile
     *
     * @since 1.0.0
     *
     * @param object $user
     */
    function add_admin_user_options( $user ) {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        if ( !user_can( $user, 'dokandar' ) ) {
            return;
        }

        $auction = get_user_meta( $user->ID, 'dokan_disable_auction', true );
        ?>
        <tr>
            <th><?php _e( 'Auction', 'dokan' ); ?></th>
            <td>
                <label for="dokan_disable_auction">
                    <input type="hidden" name="dokan_disable_auction" value="no">
                    <input name="dokan_disable_auction" type="checkbox" id="dokan_disable_auction" value="yes" <?php checked( $auction, 'yes' ); ?> />
                    <?php _e( 'Disable Auction', 'dokan' ); ?>
                </label>

                <p class="description"><?php _e( 'Disable auction capability for this vendor', 'dokan' ) ?></p>
            </td>
        </tr>
        <?php
    }

    /**
     * Save admin user profile options
     *
     * @since  1.0.0
     *
     * @param  integer $user_id
     *
     * @return void
     */
    function save_admin_user_option( $user_id ) {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        if ( ! isset( $_POST['dokan_enable_selling'] ) ) {
            return;
        }

        $selling = sanitize_text_field( $_POST['dokan_disable_auction'] );
        update_user_meta( $user_id, 'dokan_disable_auction', $selling );
    }

    /**
     * Add auction settings in dokan settings
     *
     * @since 1.0.0
     *
     * @param array $settings_fields
     */
    function add_auction_dokan_settings( $settings_fields ) {
        $settings_fields['dokan_selling']['new_seller_enable_auction'] = array(
            'name'    => 'new_seller_enable_auction',
            'label'   => __( 'New vendor Enable Auction', 'dokan' ),
            'desc'    => __( 'Make auction status enable for new registred vendor', 'dokan' ),
            'type'    => 'checkbox',
            'default' => 'on'
        );

        return $settings_fields;
    }

    /**
     * Show dashboard auction menu
     *
     * @since 1.0.0
     *
     * @param array $urls
     */
    function add_auction_dashboad_menu( $urls ) {

        if ( dokan_is_seller_enabled( get_current_user_id() ) && !dokan_is_seller_auction_disabled( get_current_user_id() ) ) {
            $urls['auction'] = array(
                'title' => __( 'Auction', 'dokan' ),
                'icon'  => '<i class="fa fa-gavel"></i>',
                'url'   => dokan_get_navigation_url('auction'),
                'pos'   => 185,
                'permission' => 'dokan_view_auction_menu'
            );
        }

        return $urls;
    }

    /**
     * Register endpoint for auction
     *
     * @since 1.0.0
     *
     * @param array $query_var
     */
    function add_dokan_auction_endpoint( $query_var ) {
        if ( !dokan_is_seller_auction_disabled( get_current_user_id() ) ){
            $query_var[] = 'auction';
            $query_var[] = 'new-auction-product';
        }

        return $query_var;
    }

    /**
     * Flush rewrite endpoind after activation
     *
     * @since 1.5.2
     *
     * @return void
     */
    function add_rewrite_rules() {
        if ( get_transient( 'dokan-auction' ) ) {
            flush_rewrite_rules( true );
            delete_transient( 'dokan-auction' );
        }
    }

    /**
    * Load dokan pro templates
    *
    * @since 1.5.1
    *
    * @return void
    **/
    public function load_auction_templates( $template_path, $template, $args ) {
        if ( isset( $args['is_auction'] ) && $args['is_auction'] ) {
            return $this->plugin_path() . '/templates';
        }

        return $template_path;
    }

    /**
     * Render auction dashboard template
     *
     * @since  1.0.0
     *
     * @param  array $query_vars
     *
     * @return void
     */
    function load_dokan_auction_template( $query_vars ) {

        if ( isset( $query_vars['auction'] ) ) {
            if ( ! current_user_can( 'dokan_view_auction_menu' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this auction page', 'dokan' ) ) );
            } else {
                dokan_get_template_part( 'auction/template-auction', '', array( 'is_auction' => true ) );
            }
            return;
        }

        if ( isset( $query_vars['new-auction-product'] ) ) {
            if ( ! current_user_can( 'dokan_add_auction_product' ) ) {
                dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to view this page', 'dokan' ) ) );
            } else {
                dokan_get_template_part( 'auction/new-auction-product', '', array( 'is_auction' => true ) );
            }
            return;
        }
    }

    /**
     * Disable selling capability by default once a seller is registered
     *
     * @since 1.0.0
     *
     * @param int $user_id
     */
    function dokan_admin_user_register_enable_auction( $user_id ) {
        $user = new WP_User( $user_id );
        $role = reset( $user->roles );

        if ( $role == 'seller' ) {
            if ( dokan_get_option( 'new_seller_enable_auction', 'dokan_selling' ) == 'off' ) {
                update_user_meta( $user_id, 'dokan_disable_auction', 'yes' );
            } else {
                update_user_meta( $user_id, 'dokan_disable_auction', 'no' );
            }
        }
    }

    /**
    * Exclude auction product from product listing
    *
    * @since 1.5.1
    *
    * @return void
    **/
    public function product_listing_exclude_auction( $product_type ) {
        $product_type[] = 'auction';
        return $product_type;
    }

    /**
     * Set auction active menu in dokan dashboard
     *
     * @since  1.0.0
     *
     * @param  string $active_menu
     *
     * @return string
     */
    function dashboard_auction_active_menu( $active_menu ) {
        if ( $active_menu == 'new-auction-product' ) {
            $active_menu = 'auction';
        }
        return $active_menu;
    }

    /**
     * Load auction email class
     *
     * @since  2.7.1
     *
     * @param  array $wc_emails
     *
     * @return array
     */
    public function load_auction_email_class( $wc_emails ) {
        $wc_emails['Dokan_Auction_Email'] = include( DOKAN_AUCTION_DIR. '/includes/emails/class-dokan-auction-email.php' );

        return $wc_emails;
    }

    /**
     * Register auction email action hook
     *
     * @since  2.7.1
     *
     * @param  array $actions
     *
     * @return array
     */
    public function register_auction_email_action( $actions ) {
        $actions[] = 'dokan_new_auction_product_added';

        return $actions;
    }

    /**
     * Send bid email to seller and amdin
     *
     * @param $recipient
     *
     * @param $object
     *
     * @since 2.8.2
     *
     * @return string
     */
    public function send_bid_email( $recipient, $object ) {
        if ( ! $object ) {
            return;
        }

        $product_id = $object->get_id();

        if ( empty( $product_id ) ) {
            return $recipient;
        }

        $vendor_id    = get_post_field( 'post_author', $product_id );
        $vendor_email = dokan()->vendor->get( $vendor_id )->get_email();

        return $recipient . ',' . $vendor_email;
    }

    /**
     * Set localized args
     *
     * @param array $args
     *
     * @since DOKAN_PLUGIN_SINCE
     *
     * @return array
     */
    public function set_localized_args( $args ) {
        $auction_args = [
            'datepicker' => [
                'now'         => __( 'Now', 'dokan' ),
                'done'        => __( 'Done', 'dokan' ),
                'time'        => __( 'Time', 'dokan' ),
                'hour'        => __( 'Hour', 'dokan' ),
                'minute'      => __( 'Minute', 'dokan' ),
                'second'      => __( 'Second', 'dokan' ),
                'time-zone'   => __( 'Time Zone', 'dokan' ),
                'choose-time' => __( 'Choose Time', 'dokan' ),
            ]
        ];

        return array_merge( $args, $auction_args );
    }

} // Dokan_Auction

$dokan_auction = Dokan_Auction::init();
dokan_register_activation_hook( __FILE__, array( 'Dokan_Auction', 'activate' ) );
