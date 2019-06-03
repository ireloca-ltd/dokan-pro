<?php
/**
  Plugin Name: Dokan Pro - business
  Plugin URI: https://wedevs.com/dokan/
  Description: An e-commerce marketplace plugin for WordPress. Powered by WooCommerce and weDevs.
  Version: 2.9.9
  Author: weDevs
  Author URI: https://wedevs.com/
  WC requires at least: 3.0
  WC tested up to: 3.6.2
  License: GPL2
  TextDomain: dokan
 */

/**
 * Dokan Pro Feature Loader
 *
 * Load all pro functionality in this class
 * if pro folder exist then automatically load this class file
 *
 * @since 2.4
 *
 * @author weDevs <info@wedevs.com>
 */
class Dokan_Pro {

    /**
     * Plan type
     *
     * @var string
     */
    private $plan = 'dokan-business';

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '2.9.9';

    /**
     * Constructor for the Dokan_Pro class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @return void
     */
    public function __construct() {
        $this->define_constants();

        add_action( 'plugins_loaded', array( $this, 'check_dokan_lite_exist' ), 10 );
        add_action( 'dokan_loaded', array( $this, 'init_plugin' ), 10 );
    }

    /**
     * Check is dokan lite active or not
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function check_dokan_lite_exist() {
        if ( ! class_exists( 'WeDevs_Dokan' ) ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            add_action( 'admin_notices', array( $this, 'activation_notice' ) );
            add_action( 'wp_ajax_dokan_pro_install_dokan_lite', array( $this, 'install_dokan_lite' ) );
        }
    }

    /**
     * Load all things
     *
     * @since 2.7.3
     *
     * @return void
     */
    public function init_plugin() {
        spl_autoload_register( array( $this, 'dokan_pro_autoload' ) );

        $this->includes();
        $this->load_actions();
        $this->load_filters();
    }

    /**
     * Initializes the WeDevs_Dokan() class
     *
     * Checks for an existing WeDevs_WeDevs_Dokan() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro();
        }

        return $instance;
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public static function activate() {
        require_once dirname( __FILE__ ) . '/includes/installer.php';
        $installer = new Dokan_Pro_Installer();
        $installer->do_install();
    }


    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'dokan', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Get plan id
     *
     * @since 2.8.4
     *
     * @return void
     */
    public function get_plan() {
        return $this->plan;
    }

    /**
     * Dokan main plugin activation notice
     *
     * @since 2.5.2
     *
     * @return void
     * */
    public function activation_notice() {
        ?>
        <div class="updated" id="dokan-pro-installer-notice" style="padding: 1em; position: relative;">
            <h2><?php _e( 'Your Dokan Pro is almost ready!', 'dokan' ); ?></h2>

            <?php
            $plugin_file      = basename( dirname( __FILE__ ) ) . '/dokan-pro.php';
            $core_plugin_file = 'dokan-lite/dokan.php';
            ?>
            <a href="<?php echo wp_nonce_url( 'plugins.php?action=deactivate&amp;plugin=' . $plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'deactivate-plugin_' . $plugin_file ); ?>" class="notice-dismiss" style="text-decoration: none;" title="<?php _e( 'Dismiss this notice', 'dokan' ); ?>"></a>

            <?php if ( file_exists( WP_PLUGIN_DIR . '/' . $core_plugin_file ) && is_plugin_inactive( 'dokan-lite' ) ): ?>
                <p><?php echo sprintf( __( 'You just need to activate the <strong>%s</strong> to make it functional.', 'dokan' ), 'Dokan (Lite) - Multi-vendor Marketplace plugin' ); ?></p>
                <p>
                    <a class="button button-primary" href="<?php echo wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $core_plugin_file . '&amp;plugin_status=all&amp;paged=1&amp;s=', 'activate-plugin_' . $core_plugin_file ); ?>"  title="<?php _e( 'Activate this plugin', 'dokan' ); ?>"><?php _e( 'Activate', 'dokan' ); ?></a>
                </p>
            <?php else: ?>
                <p><?php echo sprintf( __( "You just need to install the %sCore Plugin%s to make it functional.", "dokan" ), '<a target="_blank" href="https://wordpress.org/plugins/dokan-lite/">', '</a>' ); ?></p>

                <p>
                    <button id="dokan-pro-installer" class="button"><?php _e( 'Install Now', 'dokan' ); ?></button>
                </p>
            <?php endif ?>
        </div>

        <script type="text/javascript">
            ( function ( $ ) {
                $( '#dokan-pro-installer-notice #dokan-pro-installer' ).click( function ( e ) {
                    e.preventDefault();
                    $( this ).addClass( 'install-now updating-message' );
                    $( this ).text( '<?php echo esc_js( 'Installing...', 'dokan' ); ?>' );

                    var data = {
                        action: 'dokan_pro_install_dokan_lite',
                        _wpnonce: '<?php echo wp_create_nonce( 'dokan-pro-installer-nonce' ); ?>'
                    };

                    $.post( ajaxurl, data, function ( response ) {
                        if ( response.success ) {
                            $( '#dokan-pro-installer-notice #dokan-pro-installer' ).attr( 'disabled', 'disabled' );
                            $( '#dokan-pro-installer-notice #dokan-pro-installer' ).removeClass( 'install-now updating-message' );
                            $( '#dokan-pro-installer-notice #dokan-pro-installer' ).text( '<?php echo esc_js( 'Installed', 'dokan' ); ?>' );
                            window.location.reload();
                        }
                    } );
                } );
            } )( jQuery );
        </script>
        <?php
    }

    /**
     * Install dokan lite
     *
     * @since 2.5.2
     *
     * @return void
     * */
    public function install_dokan_lite() {
        if ( !isset( $_REQUEST['_wpnonce'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce'], 'dokan-pro-installer-nonce' ) ) {
            wp_send_json_error( __( 'Error: Nonce verification failed', 'dokan' ) );
        }

        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $plugin = 'dokan-lite';
        $api    = plugins_api( 'plugin_information', array( 'slug' => $plugin, 'fields' => array( 'sections' => false ) ) );

        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result   = $upgrader->install( $api->download_link );
        activate_plugin( 'dokan-lite/dokan.php' );

        wp_send_json_success();
    }

    /**
     * Define all pro module constant
     *
     * @since  2.6
     *
     * @return void
     */
    public function define_constants() {
        define( 'DOKAN_PRO_PLUGIN_VERSION', $this->version );
        define( 'DOKAN_PRO_FILE', __FILE__ );
        define( 'DOKAN_PRO_DIR', dirname( __FILE__ ) );
        define( 'DOKAN_PRO_INC', dirname( __FILE__ ) . '/includes' );
        define( 'DOKAN_PRO_ADMIN_DIR', DOKAN_PRO_INC . '/admin' );
        define( 'DOKAN_PRO_CLASS', dirname( __FILE__ ) . '/classes' );
        define( 'DOKAN_PRO_PLUGIN_ASSEST', plugins_url( 'assets', __FILE__ ) );
    }

    /**
     * Load all includes file for pro
     *
     * @since 2.4
     *
     * @return void
     */
    public function includes() {

        if ( is_admin() ) {
            require_once DOKAN_PRO_ADMIN_DIR . '/admin.php';
            require_once DOKAN_PRO_ADMIN_DIR . '/ajax.php';
            require_once DOKAN_PRO_ADMIN_DIR . '/admin-pointers.php';
            require_once DOKAN_PRO_ADMIN_DIR . '/shortcode-button.php';
            require_once DOKAN_PRO_ADMIN_DIR . '/promotion.php';
        }

        require_once DOKAN_PRO_ADMIN_DIR . '/announcement.php';
        require_once DOKAN_PRO_INC . '/class-shipping-zone.php';
        require_once DOKAN_PRO_INC . '/shipping-gateway/shipping.php';
        require_once DOKAN_PRO_INC . '/shipping-gateway/vendor-shipping.php';
        require_once DOKAN_PRO_CLASS . '/update.php';
        require_once DOKAN_PRO_INC . '/functions.php';
        require_once DOKAN_PRO_INC . '/orders.php';
        require_once DOKAN_PRO_INC . '/reports.php';
        require_once DOKAN_PRO_INC . '/wc-functions.php';
        require_once DOKAN_PRO_INC . '/class-dokan-store-category.php';

        require_once DOKAN_PRO_INC . '/widgets/best-seller.php';
        require_once DOKAN_PRO_INC . '/widgets/feature-seller.php';

        require_once DOKAN_PRO_CLASS . '/store-seo.php';
        require_once DOKAN_PRO_CLASS . '/store-share.php';
        require_once DOKAN_PRO_CLASS . '/social-login.php';
        require_once DOKAN_PRO_CLASS . '/email-verification.php';

        require_once DOKAN_PRO_INC . '/class-assets.php';

        if ( !function_exists( 'dokan_pro_get_active_modules' ) ) {
            require_once dirname( __FILE__ ) . '/includes/modules.php';
        }
        // load all the active modules
        $modules = dokan_pro_get_active_modules();

        if ( $modules ) {
            foreach ( $modules as $module_file ) {
                $module_path = dirname( __FILE__ ) . '/includes/modules/' . $module_file;
                if ( file_exists( $module_path ) ) {
                    include_once $module_path;
                }
            }
        }

        if ( ! class_exists( 'Hybridauth' ) ) {
            require_once DOKAN_PRO_INC . '/lib/hybridauth/autoload.php';
        }

        require_once DOKAN_PRO_INC . '/brands/class-dokan-brands.php';
    }

    /**
     * Instantiate all classes
     *
     * @since 2.4
     *
     * @return void
     */
    public function inistantiate() {
        new Dokan_Store_Category();

        if ( is_admin() ) {
            Dokan_Pro_Admin_Ajax::init();
            new Dokan_Pro_Admin_Settings();
            new Dokan_Pro_Promotion();
        }

        new Dokan_Announcement();

        Dokan_Pro_Ajax::init();
        Dokan_Pro_Shipping::init();
        new Dokan_Shipping_Zone();
        new Dokan_Update( $this->plan );
        Dokan_Email_Verification::init();
        Dokan_Social_Login::init();

        if ( is_user_logged_in() ) {
            Dokan_Pro_Dashboard::init();
            Dokan_Pro_Products::init();
            Dokan_Pro_Coupons::init();
            Dokan_Pro_Reviews::init();
            Dokan_Pro_Reports::init();
            Dokan_Pro_Withdraws::init();
            Dokan_Pro_Settings::init();
            Dokan_Pro_Notice::init();
            Dokan_Pro_Refund::init();
        }

        Dokan_Pro_Store::init();
        new Dokan_Pro_Assets();
    }

    /**
     * Load all necessary Actions hooks
     *
     * @since 2.4
     *
     * @return void [description]
     */
    public function load_actions() {
         // init the classes
        add_action( 'init', array( $this, 'localization_setup' ) );

        add_action( 'init', array( $this, 'inistantiate' ), 10 );
        add_action( 'init', array( $this, 'register_scripts' ), 10 );

        add_action( 'widgets_init', array( $this, 'register_widgets' ) );

        add_action( 'woocommerce_after_my_account', array( $this, 'dokan_account_migration_button' ) );

        add_action( 'dokan_enqueue_scripts', array( $this, 'enqueue_scripts' ), 11 );
        add_action( 'dokan_enqueue_admin_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'dokan_enqueue_admin_dashboard_script', array( $this, 'admin_dashboad_enqueue_scripts' ) );
    }

    /**
     * Load all Filters Hook
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_filters() {
        add_filter( 'dokan_rest_api_class_map', array( $this, 'rest_api_class_map' ) );
        add_filter( 'dokan_is_pro_exists', array( $this, 'set_as_pro' ), 99 );
        add_filter( 'dokan_query_var_filter', array( $this, 'load_query_var' ), 10 );
        add_filter( 'woocommerce_locate_template', array( $this, 'account_migration_template' ) );
        add_filter( 'woocommerce_locate_template', array( $this, 'dokan_registration_template' ) );
        add_filter( 'dokan_set_template_path', array( $this, 'load_pro_templates' ), 10, 3 );

        //Dokan Email filters for WC Email
        add_filter( 'woocommerce_email_classes', array( $this, 'load_dokan_emails' ), 36 );
        add_filter( 'dokan_email_list', array( $this, 'set_email_template_directory' ), 15 );
        add_filter( 'dokan_email_actions', array( $this, 'register_email_actions' ) );
    }

    /**
     * Get plugin path
     *
     * @since 2.5.2
     *
     * @return void
     * */
    public function plugin_path() {
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Required all class files inside Pro
     *
     * @since 2.4
     *
     * @param  string $class
     *
     * @return void
     */
    public function dokan_pro_autoload( $class ) {
        if ( stripos( $class, 'Dokan_Pro_' ) !== false ) {
            $class_name = str_replace( array( 'Dokan_Pro_', '_' ), array( '', '-' ), $class );
            $file_path  = DOKAN_PRO_CLASS . '/' . strtolower( $class_name ) . '.php';

            if ( file_exists( $file_path ) ) {
                require_once $file_path;
            }
        }
    }

    /**
     * Register all scripts
     *
     * @since 2.6
     *
     * @return void
     * */
    public function register_scripts() {
        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        // Register all js
        wp_register_script( 'serializejson', WC()->plugin_url() . '/assets/js/jquery-serializejson/jquery.serializejson' . $suffix . '.js', array( 'jquery' ), '2.6.1' );
        wp_register_script( 'dokan-product-shipping', plugins_url( 'assets/js/single-product-shipping.js', __FILE__ ), false, null, true );
    }

    /**
     * Enqueue scripts
     *
     * @since 2.6
     *
     * @return void
     * */
    public function enqueue_scripts() {

        if ( ( dokan_is_seller_dashboard() || ( get_query_var( 'edit' ) && is_singular( 'product' ) ) ) || dokan_is_store_page() || dokan_is_store_review_page() || is_account_page() || apply_filters( 'dokan_forced_load_scripts', false ) ) {
            // wp_enqueue_style( 'dokan-pro-style' );
            wp_enqueue_style( 'dokan-pro-style', DOKAN_PRO_PLUGIN_ASSEST . '/css/style.css', false, time(), 'all' );

            // Load accounting scripts
            wp_enqueue_script( 'serializejson' );
            wp_enqueue_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js', array( 'jquery' ), null, true );

            //localize script for refund and dashboard image options
            $dokan_refund = dokan_get_refund_localize_data();
            wp_localize_script( 'dokan-script', 'dokan_refund', $dokan_refund );
            wp_enqueue_script( 'dokan-pro-script', DOKAN_PRO_PLUGIN_ASSEST . '/js/dokan-pro.js', array( 'jquery', 'dokan-script' ), null, true );
        }

        // Load in Single product pages only
        if ( is_singular( 'product' ) && !get_query_var( 'edit' ) ) {
            wp_enqueue_script( 'dokan-product-shipping' );
        }

        if ( get_query_var( 'account-migration' ) ) {
            wp_enqueue_script( 'dokan-vendor-registration' );
        }
    }

    /**
     * Admin scripts
     *
     * @since 2.6
     *
     * @return void
     * */
    public function admin_enqueue_scripts() {
        wp_enqueue_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI.min.js', array( 'jquery' ), null, true );
        wp_enqueue_script( 'dokan_pro_admin', DOKAN_PRO_PLUGIN_ASSEST . '/js/dokan-pro-admin.js', array( 'jquery', 'jquery-blockui' ) );

        $dokan_refund = dokan_get_refund_localize_data();
        $dokan_admin  = apply_filters( 'dokan_admin_localize_param', array(
            'ajaxurl'      => admin_url( 'admin-ajax.php' ),
            'nonce'        => wp_create_nonce( 'dokan-admin-nonce' ),
            'activating'   => __( 'Activating', 'dokan' ),
            'deactivating' => __( 'Deactivating', 'dokan' )
        ) );

        wp_localize_script( 'dokan_slider_admin', 'dokan_refund', $dokan_refund );
        wp_localize_script( 'dokan_pro_admin', 'dokan_admin', $dokan_admin );
    }

    /**
     * Load admin dashboard scripts
     *
     * @since 2.6
     *
     * @return void
     * */
    public function admin_dashboad_enqueue_scripts() {
        wp_enqueue_style( 'dokan-pro-admin-dash', DOKAN_PRO_PLUGIN_ASSEST . '/css/admin.css' );
    }

    /**
     * Load Pro rewrite query vars
     *
     * @since 2.4
     *
     * @param  array $query_vars
     *
     * @return array
     */
    public function load_query_var( $query_vars ) {
        $query_vars[] = 'coupons';
        $query_vars[] = 'reports';
        $query_vars[] = 'reviews';
        $query_vars[] = 'announcement';
        $query_vars[] = 'single-announcement';
        $query_vars[] = 'account-migration';
        $query_vars[] = 'dokan-registration';

        return $query_vars;
    }

    /**
     * Dokan Account Migration Button render
     *
     * @since 2.4
     *
     * @return void
     */
    function dokan_account_migration_button() {
        $user = wp_get_current_user();

        if ( dokan_is_user_customer( $user->ID ) ) {
            dokan_get_template_part( 'global/account-migration-btn', '', array( 'pro' => true ) );
        }
    }

    /**
     * Account migration template on my account
     *
     * @param string  $file path of the template
     *
     * @return string
     */
    function account_migration_template( $file ) {
        if ( get_query_var( 'account-migration' ) && dokan_is_user_customer( get_current_user_id() ) && basename( $file ) == 'my-account.php' ) {
            $file = dokan_locate_template( 'global/update-account.php', '', DOKAN_PRO_DIR . '/templates/', true );
        }

        return $file;
    }

    /**
     *
     * @param type $file
     * @return type
     */
    function dokan_registration_template( $file ) {
        if ( get_query_var( 'dokan-registration' ) && dokan_is_user_customer( get_current_user_id() ) && basename( $file ) == 'my-account.php' ) {
            $file = dokan_locate_template( 'global/dokan-registration.php', '', DOKAN_PRO_DIR . '/templates/', true );
        }
        return $file;
    }

    /**
     * Load dokan pro templates
     *
     * @since 2.5.2
     *
     * @return void
     * */
    public function load_pro_templates( $template_path, $template, $args ) {
        if ( isset( $args['pro'] ) && $args['pro'] ) {
            return $this->plugin_path() . '/templates';
        }

        return $template_path;
    }

    /**
     * Set plugin in pro mode
     *
     * @since 2.6
     *
     * @param boolean $is_pro
     *
     * @return boolean
     */
    function set_as_pro( $is_pro ) {
        return true;
    }

    /**
     * Add Dokan Email classes in WC Email
     *
     * @since 2.6.6
     *
     * @param array $wc_emails
     *
     * @return $wc_emails
     */
    function load_dokan_emails( $wc_emails ) {
        $wc_emails['Dokan_Email_Announcement']    = include( DOKAN_PRO_INC . '/emails/class-dokan-email-announcement.php' );
        $wc_emails['Dokan_Email_Updated_Product'] = include( DOKAN_PRO_INC . '/emails/class-dokan-email-updated-product.php' );
        $wc_emails['Dokan_Email_Refund_Request']  = include( DOKAN_PRO_INC . '/emails/class-dokan-refund-request.php' );
        $wc_emails['Dokan_Email_Refund_Vendor']   = include( DOKAN_PRO_INC . '/emails/class-dokan-email-refund-vendor.php' );
        $wc_emails['Dokan_Email_Vendor_Enable']   = include( DOKAN_PRO_INC . '/emails/class-dokan-email-vendor-enable.php' );
        $wc_emails['Dokan_Email_Vendor_Disable']  = include( DOKAN_PRO_INC . '/emails/class-dokan-email-vendor-disable.php' );

        return $wc_emails;
    }

    /**
     * Register Dokan Email actions for WC
     *
     * @since 2.6.6
     *
     * @param array $actions
     *
     * @return $actions
     */
    function register_email_actions( $actions ) {
        $actions[] = 'dokan_edited_product_pending_notification';
        $actions[] = 'dokan_after_announcement_saved';
        $actions[] = 'dokan_refund_request_notification';
        $actions[] = 'dokan_refund_processed_notification';

        return $actions;
    }

    /**
     * Set template override directory for Dokan Emails
     *
     * @since 2.6.6
     *
     * @param array $dokan_emails
     *
     * @return $dokan_emails
     */
    function set_email_template_directory( $dokan_emails ) {
        $dokan_pro_emails = array(
            'product-updated-pending',
            'announcement',
            'refund-seller-mail',
            'refund_request',
        );

        return array_merge( $dokan_pro_emails, $dokan_emails );
    }

    /**
     * Initialize pro rest api class
     *
     * @param array $class_map
     *
     * @return array
     */
    function rest_api_class_map( $class_map ) {
        $classes = array(
            dirname( __FILE__ ) . '/includes/api/class-store-category-controller.php'    => 'Dokan_REST_Store_Category_Controller',
            dirname( __FILE__ ) . '/includes/api/class-coupon-controller.php'            => 'Dokan_REST_Coupon_Controller',
            dirname( __FILE__ ) . '/includes/api/class-reports-controller.php'           => 'Dokan_REST_Reports_Controller',
            dirname( __FILE__ ) . '/includes/api/class-reviews-controller.php'           => 'Dokan_REST_Reviews_Controller',
            dirname( __FILE__ ) . '/includes/api/class-product-variation-controller.php' => 'Dokan_REST_Product_Variation_Controller',
            dirname( __FILE__ ) . '/includes/api/class-store-controller.php'             => 'Dokan_Pro_REST_Store_Controller',
            dirname( __FILE__ ) . '/includes/api/class-modules-controller.php'           => 'Dokan_REST_Modules_Controller',
            dirname( __FILE__ ) . '/includes/api/class-announcement-controller.php'      => 'Dokan_REST_Announcement_Controller',
            dirname( __FILE__ ) . '/includes/api/class-refund-controller.php'            => 'Dokan_REST_Refund_Controller',
            dirname( __FILE__ ) . '/includes/api/class-logs-controller.php'              => 'Dokan_REST_Logs_Controller',
        );

        return array_merge( $class_map, $classes );
    }

    /**
     * Register widgets
     *
     * @since 2.8
     *
     * @return void
     */
    public function register_widgets() {
        register_widget( 'Dokan_Best_Seller_Widget' );
        register_widget( 'Dokan_Feature_Seller_Widget' );
    }
}

/**
 * Load pro plugin for dokan
 *
 * @since 2.5.3
 *
 * @return void
 * */
function dokan_pro() {
    return Dokan_Pro::init();
}

dokan_pro();

register_activation_hook( __FILE__, array( 'Dokan_Pro', 'activate' ) );
