<?php
/**
 * Customer inbox class
 */
class Dokan_Customer_Inbox extends Dokan_Live_Chat_Start {

    /**
     * Constructor of this class
     *
     * @since 1.1
     */
    public function __construct() {
        $this->init_hooks();
        parent::__construct();
    }

    /**
     * Init all the hooks
     *
     * @since  1.1
     *
     * @return void
     */
    public function init_hooks() {
        add_action( 'woocommerce_account_menu_items', array( $this, 'add_customer_inbox' ) );
        add_filter( 'dokan_query_var_filter', array( $this, 'register_customer_inbox_query_var' ), 10, 2 );
        add_filter( 'woocommerce_locate_template', array( $this, 'load_customer_inbox' ), 20 );
    }

    /**
     * Add customer inbox menu
     *
     * @param array $menus
     *
     * @since 1.1
     *
     * @return array
     */
    public function add_customer_inbox( $menus ) {

        if ( $this->enabled !== 'on' ) {
            return $menus;
        }

        if ( ! empty( $this->app_id ) || ! empty( $this->app_secret ) ) {
            $menus['customer-inbox'] = __( 'Inbox', 'dokan' );
        }

        return $menus;
    }

    /**
     * Register customer inbox query vars
     *
     * @param array $vars
     *
     * @since 1.1
     *
     * @return array
     */
    function register_customer_inbox_query_var( $vars ) {
        $vars['customer-inbox'] = 'customer-inbox';

        return $vars;
    }

    /**
     * Load customer inbox menu content
     *
     * @param  string $file
     *
     * @since 1.1
     *
     * @return string
     */
    public function load_customer_inbox( $file ) {
        if ( $this->enabled !== 'on' ) {
            return $file;
        }

        if ( empty( $this->app_id ) || empty( $this->app_secret ) ) {
            return $file;
        }

        global $wp;

        if ( isset( $wp->query_vars['customer-inbox'] ) && basename( $file ) == 'my-account.php' ) {
            return DOKAN_LIVE_CHAT . '/templates/customer-inbox.php';
        }

        return $file;
    }

    /**
     * Initialize this class
     *
     * @return instance
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Customer_Inbox();
        }

        return $instance;
    }
}

Dokan_Customer_Inbox::init();
