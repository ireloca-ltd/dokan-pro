<?php

/**
 * Dokan seller inbox class
 *
 * @since 1.1
 */
class Dokan_Seller_Inbox extends Dokan_Live_Chat_Start {
    /**
     * Constructor method of this class
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
     * @since 1.1
     *
     * @return void
     */
    public function init_hooks() {
        // add inbox menu
        add_filter( 'dokan_get_dashboard_nav', array( $this, 'dokan_add_inbox_menu' ), 22, 1 );
        add_filter( 'dokan_query_var_filter', array( $this, 'dokan_add_endpoint' ) );
        add_action( 'dokan_load_custom_template', array( $this, 'dokan_load_inbox_template' ), 22 );
        add_action( 'dokan_rewrite_rules_loaded', array( $this, 'flush_rewrite_rules' ) );
    }

    /**
     * Register inbox menu on seller dashboard
     *
     * @param array $urls
     *
     * @since 1.0
     *
     * @return array
     */
    public function dokan_add_inbox_menu( $urls ) {
        if ( $this->enabled !== 'on' ) {
            return $urls;
        }

        if ( empty( $this->app_id ) || empty( $this->app_secret ) ) {
            return $urls;
        }

        if ( dokan_is_seller_enabled( get_current_user_id() ) ) {
            $urls['inbox'] = array(
                'title' => __( 'Inbox', 'dokan' ),
                'icon'  => '<i class="fa fa-comment"></i>',
                'url'   => dokan_get_navigation_url( 'inbox' ),
                'pos'   => 195,
                'permission' => 'dokan_view_inbox_menu'
            );
        }

        return $urls;
    }

    /**
     * Add inbox endpoint to Dashboard
     *
     * @param array $query_var
     *
     * @since 1.0
     *
     * @return array
     */
    public function dokan_add_endpoint( $query_var ) {
        $query_var['inbox'] = 'inbox';

        return $query_var;
    }

    /**
     * Dokan Load inbox template
     *
     * @param  array $query_vars
     *
     * @since 1.0
     *
     * @return string
     */
    public function dokan_load_inbox_template( $query_vars ) {
        if ( ! isset( $query_vars['inbox'] ) ) {
            return;
        }

        if ( $this->enabled !== 'on' ) {
            return;
        }

        if ( empty( $this->app_id ) || empty( $this->app_secret ) ) {
            return;
        }

        require_once DOKAN_LIVE_CHAT . '/templates/inbox.php';
    }

    /**
     * Flush rewrite rules
     *
     * @since 1.1
     *
     * @return void;
     */
    public function flush_rewrite_rules() {
        if ( get_transient( 'dokan-live-chat' ) ) {
            flush_rewrite_rules( true );
            delete_transient( 'dokan-live-chat' );
        }
    }

    /**
     * Initialize this class
     *
     * @since 1.1
     *
     * @return instance
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            return $instance = new Dokan_Seller_Inbox();
        }

        return $instance;
    }
}

Dokan_Seller_Inbox::init();
