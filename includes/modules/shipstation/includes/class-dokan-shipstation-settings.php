<?php

class Dokan_ShipStation_Settings {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_filter( 'dokan_settings_fields', array( $this, 'add_admin_settings_fields' ), 11 );
        add_action( 'dokan_dashboard_content_before', array( $this, 'enqueue_scripts' ) );
        add_action( 'dokan_get_dashboard_settings_nav', array( $this, 'add_settings_nav' ) );
        add_filter( 'dokan_dashboard_settings_heading_title', array( $this, 'add_heading_title' ), 10, 2 );
        add_action( 'dokan_dashboard_settings_helper_text', array( $this, 'add_helper_text' ), 10, 2 );
        add_action( 'dokan_settings_content', array( $this, 'add_settings_content' ) );
        add_action( 'wp_ajax_dokan_shipstation_settings', array( $this, 'form_handler' ) );
    }

    /**
     * Add admin settings fields
     *
     * @since 1.0.0
     *
     * @param array $settings_fields
     */
    public function add_admin_settings_fields( $settings_fields ) {
        $settings_fields['dokan_selling']['enable_shipstation_logging'] = array(
            'name'  => 'enable_shipstation_logging',
            'label' => __( 'Log ShipStation API Request', 'dokan' ),
            'desc'  => __( 'Log all ShipStation API interactions.', 'dokan' ),
            'type'  => 'checkbox',
        );

        return $settings_fields;
    }

    /**
     * Enqueue ShipStation scripts in vendor settings page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_scripts() {
        $settings = get_query_var( 'settings' );

        if ( 'shipstation' !== $settings ) {
            return;
        }

        wp_enqueue_script( 'dokan-shipstation-settings', DOKAN_SHIPSTATION_ASSETS . '/js/dokan-shipstation-settings.js', array( 'jquery' ), DOKAN_SHIPSTATION_VERSION, true );
    }

    /**
     * Add settings nav in settings page
     *
     * @since 1.0.0
     *
     * @param array $settings
     */
    public function add_settings_nav( $settings ) {
        $settings['shipstation'] = array(
            'title'      => __( 'ShipStation', 'dokan' ),
            'icon'       => '<i class="fa fa-gear"></i>',
            'url'        => dokan_get_navigation_url( 'settings/shipstation' ),
            'pos'        => 71,
            'permission' => 'dokan_view_store_shipping_menu'
        );

        return $settings;
    }

    /**
     * Add heading title in settings page
     *
     * @since 1.0.0
     *
     * @param string $header
     * @param string $query_vars
     */
    public function add_heading_title( $header, $query_vars ) {
        if ( 'shipstation' === $query_vars ) {
            $header = __( 'ShipStation', 'dokan' );
        }

        return $header;
    }

    /**
     * Add helper text in settings page
     *
     * @since 1.0.0
     *
     * @param string $help_text
     * @param string $query_vars
     */
    public function add_helper_text( $help_text, $query_vars ) {
        if ( 'shipstation' === $query_vars ) {
            $help_text = __( 'ShipStation allows you to retrieve & manage orders, then print labels & packing slips with ease.', 'dokan' );
        }

        return $help_text;
    }

    /**
     * Add settings form in settings page
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function add_settings_content() {
        $settings = get_query_var( 'settings' );

        if ( 'shipstation' !== $settings ) {
            return;
        }

        $seller_id = dokan_get_current_user_id();

        $auth_key = get_user_meta( $seller_id, 'shipstation_auth_key', true );

        if ( ! $auth_key ) {
            $auth_key = $this->generate_key( $seller_id );

            update_user_meta( $seller_id, 'shipstation_auth_key', $auth_key );
        }

        $statuses = wc_get_order_statuses();

        $export_statuses = get_user_meta( $seller_id, 'shipstation_export_statuses', true );
        $export_statuses = ! empty( $export_statuses ) && is_array( $export_statuses ) ? $export_statuses : array();

        $shipped_status = get_user_meta( $seller_id, 'shipstation_shipped_status', true );
        $shipped_status = ! empty( $shipped_status ) ? $shipped_status : 'wc-completed';

        $args = array(
            'auth_key'        => $auth_key,
            'statuses'        => $statuses,
            'export_statuses' => $export_statuses,
            'shipped_status'  => $shipped_status,
        );

        dokan_shipstation_get_template( 'settings', $args );
    }

    /**
     * Generate read-only auth key for ShipStation
     *
     * @since 1.0.0
     *
     * @param int $seller_id
     *
     * @return string
     */
    public function generate_key( $seller_id ) {
        $to_hash = $seller_id . date( 'U' ) . mt_rand();
        return 'DOKANSS-' . hash_hmac( 'md5', $to_hash, wp_hash( $to_hash ) );
    }

    /**
     * Ajax form submission handler
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function form_handler() {
        if ( ! wp_verify_nonce( $_POST['dokan_store_shipstation_form_nonce'], 'dokan_store_shipstation_form_action' ) ) {
            wp_send_json_error( __( 'Are you cheating?', 'dokan' ) );
        }

        if ( ! current_user_can( 'dokan_view_store_shipping_menu' ) ) {
            wp_send_json_error( __( 'Pemission denied', 'dokan' ) );
        }

        if ( empty( $_POST['dokan_shipstation_export_statuses'] ) ) {
            wp_send_json_error( __( 'Export Order Statuse is empty', 'dokan' ) );
        }

        if ( empty( $_POST['dokan_shipstation_shipped_status'] ) ) {
            wp_send_json_error( __( 'Shipped Order Statuse is empty', 'dokan' ) );
        }

        $seller_id = dokan_get_current_user_id();

        $data = array(
            'export_statuses' => $_POST['dokan_shipstation_export_statuses'],
            'order_status'    => $_POST['dokan_shipstation_shipped_status'],
        );

        $saved = $this->save_vendor_settings( $seller_id, $data );

        wp_send_json_success( __( 'Your changes has been updated!', 'dokan' ) );
    }

    /**
     * Save vendor settings
     *
     * @since 1.0.0
     *
     * @param int   $seller_id
     * @param array $data
     *
     * @return void
     */
    public function save_vendor_settings( $seller_id, $data ) {
        update_user_meta( $seller_id, 'shipstation_export_statuses', $data['export_statuses'] );
        update_user_meta( $seller_id, 'shipstation_shipped_status', $data['order_status'] );
    }
}
