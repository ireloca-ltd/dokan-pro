<?php
/**
 * Dokan Pro Ajax class
 *
 * @since 2.4
 *
 * @package dokan
 */
class Dokan_Pro_Ajax {

    /**
     * Loading automatically when class initiate
     *
     * @since 2.4
     *
     * @uses action hook
     * @uses filter hook
     */
    function __construct() {

        // Shipping ajax hanlding
        add_action( 'wp_ajax_dps_select_state_by_country', array( $this, 'load_state_by_country' ) );
        add_action( 'wp_ajax_nopriv_dps_select_state_by_country', array( $this, 'load_state_by_country' ) );

        // Announcement ajax handling
        add_action( 'wp_ajax_dokan_announcement_remove_row', array( $this, 'remove_announcement') );
        add_action( 'wp_ajax_nopriv_dokan_announcement_remove_row', array( $this, 'remove_announcement') );

        // shipping state ajax
        add_action( 'wp_ajax_nopriv_dokan_shipping_country_select', array( $this, 'get_state_by_shipping_country') );
        add_action( 'wp_ajax_dokan_shipping_country_select', array( $this, 'get_state_by_shipping_country') );

        // shipping calculation ajax
        add_action( 'wp_ajax_nopriv_dokan_shipping_calculator', array( $this, 'get_calculated_shipping_cost') );
        add_action( 'wp_ajax_dokan_shipping_calculator', array( $this, 'get_calculated_shipping_cost') );

        // Variation Handle in old Design
        add_action( 'wp_ajax_dokan_add_variation', array( $this, 'add_variation' ) );
        add_action( 'wp_ajax_dokan_link_all_variations', array( $this, 'link_all_variations' ) );
        add_action( 'wp_ajax_dokan_pre_define_attribute', array( $this, 'dokan_pre_define_attribute' ) );
        add_action( 'wp_ajax_dokan_save_attributes', array( $this, 'save_attributes' ) );
        add_action( 'wp_ajax_dokan_remove_variation', array( $this, 'remove_variations' ) );
        add_action( 'wp_ajax_dokan_load_variations', array( $this, 'load_variations' ) );
        add_action( 'wp_ajax_dokan_save_variations', array( $this, 'save_variations' ) );
        add_action( 'wp_ajax_dokan_bulk_edit_variations', array( $this, 'bulk_edit_variations' ) );

        // Single product Design ajax
        add_action( 'wp_ajax_dokan_save_attributes_options', array( $this, 'save_attributes_options') );
        add_action( 'wp_ajax_nopriv_dokan_save_attributes_options', array( $this, 'save_attributes_options') );
        add_action( 'wp_ajax_dokan_save_variations_options', array( $this, 'save_variations_options') );
        add_action( 'wp_ajax_nopriv_dokan_save_variations_options', array( $this, 'save_variations_options') );
        add_action( 'wp_ajax_dokan_add_new_variations_options', array( $this, 'add_new_variations_options') );
        add_action( 'wp_ajax_nopriv_dokan_add_new_variations_options', array( $this, 'add_new_variations_options') );
        add_action( 'wp_ajax_dokan_remove_single_variation_item', array( $this, 'remove_single_variation_item') );
        add_action( 'wp_ajax_nopriv_dokan_remove_single_variation_item', array( $this, 'remove_single_variation_item') );
        // add_action( 'wp_ajax_dokan_get_pre_attribute', array( $this, 'add_predefined_attribute') );
        // add_action( 'wp_ajax_nopriv_dokan_get_pre_attribute', array( $this, 'add_predefined_attribute') );
        add_action( 'wp_ajax_dokan_get_pre_attribute', array( $this, 'add_attr_predefined_attribute') );
        add_action( 'wp_ajax_nopriv_dokan_get_pre_attribute', array( $this, 'add_attr_predefined_attribute') );
        add_action( 'wp_ajax_dokan_add_new_attribute', array( $this, 'add_new_attribute') );
        add_action( 'wp_ajax_nopriv_dokan_add_new_attribute', array( $this, 'add_new_attribute') );
        add_action( 'wp_ajax_dokan_load_order_items', array( $this, 'load_order_items') );
        add_action( 'wp_ajax_nopriv_dokan_load_order_items', array( $this, 'load_order_items') );

        // Refund request
        add_action( 'wp_ajax_dokan_refund_request', array( $this, 'dokan_refund_request') );
        add_action( 'wp_ajax_nopriv_dokan_refund_request', array( $this, 'dokan_refund_request') );

        add_action( 'wp_ajax_dokan_toggle_seller', array( $this, 'toggle_seller_status' ) );

        // Shipping Zone
        add_action( 'wp_ajax_dokan-get-shipping-zone', array( $this, 'get_shipping_zone' ) );
        add_action( 'wp_ajax_dokan-update-shipping-method-settings', array( $this, 'update_shipping_methods_settings' ) );
        add_action( 'wp_ajax_dokan-toggle-shipping-method-enabled', array( $this, 'toggle_shipping_method' ) );
        add_action( 'wp_ajax_dokan-save-zone-settings', array( $this, 'save_zone_settings' ) );
        add_action( 'wp_ajax_dokan-add-shipping-method', array( $this, 'add_shipping_method' ) );
        add_action( 'wp_ajax_dokan-delete-shipping-method', array( $this, 'delete_shipping_method' ) );
        add_action( 'wp_ajax_dokan-save-shipping-settings', array( $this, 'save_shipping_settings' ) );
        add_action( 'wp_ajax_dokan-get-shipping-settings', array( $this, 'get_shipping_settings' ) );
    }

    /**
     * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {

        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Ajax();
        }

        return $instance;
    }

    /**
     * Get shipping zone
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_shipping_zone() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        if ( isset( $_POST['zoneID'] ) ) {
            $zones = Dokan_Shipping_Zone::get_zone( $_POST['zoneID'] );
        } else {
            $zones = Dokan_Shipping_Zone::get_zones();
        }

        wp_send_json_success( $zones );
    }

    /**
     * Get shipping methods
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_shipping_methods() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        if ( !isset( $_POST['zoneID'] ) ) {
            wp_send_json_error( __( 'Zone not found', 'dokan' ) );
        }

        $methods = Dokan_Shipping_Zone::get_shipping_methods( $_POST['zoneID'] );

        wp_send_json_success( $methods );
    }

    /**
     * Update shipping methods settings
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function update_shipping_methods_settings() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $zone_id = isset( $_POST['zoneID'] ) ? $_POST['zoneID'] : '';

        if ( $zone_id == '' ) {
            wp_send_json_error( __( 'Shipping zone not found', 'dokan' ) );
        }

        $defaults = array(
            'instance_id' => '',
            'method_id'   => '',
            'zone_id'     => $zone_id,
            'settings'    => array()
        );

        $args = dokan_parse_args( $_POST['data'], $defaults );

        if ( empty( $args['settings']['title'] ) ) {
            wp_send_json_error( __( 'Shipping title must be required', 'dokan' ) );
        }

        $result = Dokan_Shipping_Zone::update_shipping_method( $args );

        wp_send_json_success( $args );
    }

    /**
     * Toggle shipping method
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function toggle_shipping_method() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $zone_id = isset( $_POST['zoneID'] ) ? $_POST['zoneID'] : '';

        if ( $zone_id == '' ) {
            wp_send_json_error( __( 'Shipping zone not found', 'dokan' ) );
        }

        $instance_id = ! empty( $_POST['instance_id'] ) ? $_POST['instance_id'] : 0;

        if ( ! $instance_id ) {
            wp_send_json_error( __( 'Shipping method not found', 'dokan' ) );
        }

        $data = array(
            'instance_id' => $instance_id,
            'zone_id'     => $zone_id,
            'checked'     => ( $_POST['checked'] == 'true' ) ? 1 : 0
        );

        $result = Dokan_Shipping_Zone::toggle_shipping_method( $data );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() );
        }

        $message = $data['checked'] ? __( 'Shipping method enabled successfully', 'dokan' ) : __( 'Shipping method disabled successfully', 'dokan' );
        wp_send_json_success( $message );
    }

    /**
     * Add new shipping method for a zone
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function add_shipping_method() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $zone_id = isset( $_POST['zoneID'] ) ? $_POST['zoneID'] : '';

        if ( $zone_id == '' ) {
            wp_send_json_error( __( 'Shipping zone not found', 'dokan' ) );
        }

        if ( empty( $_POST['method'] ) ) {
            wp_send_json_error( __( 'Please select a shipping method', 'dokan' ) );
        }

        $data = array(
            'zone_id'   => $zone_id,
            'method_id' => $_POST['method']
        );

        $result = Dokan_Shipping_Zone::add_shipping_methods( $data );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() , 'dokan' );
        }

        wp_send_json_success( __( 'Shipping method added successfully', 'dokan' ) );
    }

    /**
     * Delete shipping method
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function delete_shipping_method() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $zone_id = isset( $_POST['zoneID'] ) ? $_POST['zoneID'] : '';

        if ( $zone_id == '' ) {
            wp_send_json_error( __( 'Shipping zone not found', 'dokan' ) );
        }

        if ( empty( $_POST['instance_id'] ) ) {
            wp_send_json_error( __( 'Shipping method not found', 'dokan' ) );
        }

        $data = array(
            'zone_id'     => $zone_id,
            'instance_id' => $_POST['instance_id']
        );

        $result = Dokan_Shipping_Zone::delete_shipping_methods( $data );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( $result->get_error_message() , 'dokan' );
        }

        wp_send_json_success( __( 'Shipping method deleted', 'dokan' ) );
    }

    /**
     * Save shipping Settings
     *
     * @since 2.9.2
     *
     * @return void
     */
    public function save_shipping_settings() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $settings = $_POST['settings'];
        $user_id = dokan_get_current_user_id();

        if ( isset( $settings['processing_time'] ) ) {
            update_user_meta( $user_id, '_dps_pt', $settings['processing_time'] );
        }

        if ( isset( $settings['shipping_policy'] ) ) {
            update_user_meta( $user_id, '_dps_ship_policy', $settings['shipping_policy'] );
        }

        if ( isset( $settings['refund_policy'] ) ) {
            update_user_meta( $user_id, '_dps_refund_policy', $settings['refund_policy'] );
        }

        update_user_meta( $user_id, '_dps_zone_wise_settings', 'yes' );

        wp_send_json_success( __( 'Settings save successfully', 'dokan' ) );
    }

    /**
     * Get shipping settings
     *
     * @since 2.9.2
     *
     * @return void
     */
    public function get_shipping_settings() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $user_id = dokan_get_current_user_id();

        $dps_processing  = get_user_meta( $user_id, '_dps_pt', true );
        $shipping_policy = get_user_meta( $user_id, '_dps_ship_policy', true );
        $refund_policy   = get_user_meta( $user_id, '_dps_refund_policy', true );

        $shipping_data = array(
            'processing_time' => $dps_processing,
            'shipping_policy' => $shipping_policy,
            'refund_policy' => $refund_policy,
        );

        wp_send_json_success( $shipping_data );
    }

    /**
     * Save zone settings
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function save_zone_settings() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dokan_reviews' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        $zone_id = isset( $_POST['zoneID'] ) ? $_POST['zoneID'] : '';

        if ( $zone_id == '' ) {
            wp_send_json_error( __( 'Shipping zone not found', 'dokan' ) );
        }

        $location = array();

        if ( ! empty( $_POST['country'] ) && is_array( $_POST['country'] ) ) {
            $country_array = array();

            foreach ( $_POST['country'] as $country ) {
                $country_array[] = array(
                    'code' => $country['code'],
                    'type'  => 'country'
                );
            }

            $location = array_merge( $location, $country_array );
        }

        if ( ! empty( $_POST['state'] ) && is_array( $_POST['state'] ) ) {
            $state_array = array();

            foreach ( $_POST['state'] as $state ) {
                $state_array[] = array(
                    'code' => $state['code'],
                    'type'  => 'state'
                );
            }

            $location = array_merge( $location, $state_array );
        }

        if ( ! empty( $_POST['postcode'] ) ) {
            $postcodes = explode( ',', $_POST['postcode'] );
            $postcode_array = array();

            foreach ( $postcodes as $postcode ) {
                $postcode_array[] = array(
                    'code' => $postcode,
                    'type' => 'postcode'
                );
            }

            $location = array_merge( $location, $postcode_array );
        }

        $result = Dokan_Shipping_Zone::save_location( $location, $zone_id );

        wp_send_json_success( __( 'Zone settings save successfully', 'dokan' ) );
    }

    /**
     * Load variations
     *
     * @return void
     */
    public function load_variations() {
        ob_start();

        check_ajax_referer( 'load-variations', 'security' );

        // Check permissions again and make sure we have what we need
        if ( ! current_user_can( 'dokandar' ) || empty( $_POST['product_id'] ) || empty( $_POST['attributes'] ) ) {
            die( -1 );
        }

        global $post;

        $product_id = absint( $_POST['product_id'] );
        $post       = get_post( $product_id ); // Set $post global so its available like within the admin screens
        $per_page   = ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
        $page       = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;

        // Get attributes
        $attributes        = array();
        $posted_attributes = json_decode( wp_unslash( $_POST['attributes'] ) );

        foreach ( $posted_attributes as $key => $value ) {
            $attributes[ $key ] = array_map( 'wc_clean', (array)$value );
        }

        // Get tax classes
        $tax_classes           = WC_Tax::get_tax_classes();
        $tax_class_options     = array();
        $tax_class_options[''] = __( 'Standard', 'dokan' );

        if ( ! empty( $tax_classes ) ) {
            foreach ( $tax_classes as $class ) {
                $tax_class_options[ sanitize_title( $class ) ] = esc_attr( $class );
            }
        }

        // Set backorder options
        $backorder_options = array(
            'no'     => __( 'Do not allow', 'dokan' ),
            'notify' => __( 'Allow, but notify customer', 'dokan' ),
            'yes'    => __( 'Allow', 'dokan' )
        );

        // set stock status options
        $stock_status_options = array(
            'instock'    => __( 'In stock', 'dokan' ),
            'outofstock' => __( 'Out of stock', 'dokan' )
        );

        $parent_data = array(
            'id'                   => $product_id,
            'attributes'           => $attributes,
            'tax_class_options'    => $tax_class_options,
            'sku'                  => get_post_meta( $product_id, '_sku', true ),
            'weight'               => wc_format_localized_decimal( get_post_meta( $product_id, '_weight', true ) ),
            'length'               => wc_format_localized_decimal( get_post_meta( $product_id, '_length', true ) ),
            'width'                => wc_format_localized_decimal( get_post_meta( $product_id, '_width', true ) ),
            'height'               => wc_format_localized_decimal( get_post_meta( $product_id, '_height', true ) ),
            'tax_class'            => get_post_meta( $product_id, '_tax_class', true ),
            'backorder_options'    => $backorder_options,
            'stock_status_options' => $stock_status_options
        );

        if ( ! $parent_data['weight'] ) {
            $parent_data['weight'] = wc_format_localized_decimal( 0 );
        }

        if ( ! $parent_data['length'] ) {
            $parent_data['length'] = wc_format_localized_decimal( 0 );
        }

        if ( ! $parent_data['width'] ) {
            $parent_data['width'] = wc_format_localized_decimal( 0 );
        }

        if ( ! $parent_data['height'] ) {
            $parent_data['height'] = wc_format_localized_decimal( 0 );
        }

        // Get variations
        $args = apply_filters( 'woocommerce_ajax_admin_get_variations_args', array(
            'post_type'      => 'product_variation',
            'post_status'    => array( 'private', 'publish' ),
            'posts_per_page' => $per_page,
            'paged'          => $page,
            'orderby'        => array( 'menu_order' => 'ASC', 'ID' => 'DESC' ),
            'post_parent'    => $product_id
        ), $product_id );

        $variations = get_posts( $args );
        $loop = 0;

        if ( $variations ) {

            foreach ( $variations as $variation ) {
                $variation_id     = absint( $variation->ID );
                $variation_meta   = get_post_meta( $variation_id );
                $variation_data   = array();
                $shipping_classes = get_the_terms( $variation_id, 'product_shipping_class' );
                $variation_fields = array(
                    '_sku'                   => '',
                    '_stock'                 => '',
                    '_regular_price'         => '',
                    '_sale_price'            => '',
                    '_weight'                => '',
                    '_length'                => '',
                    '_width'                 => '',
                    '_height'                => '',
                    '_download_limit'        => '',
                    '_download_expiry'       => '',
                    '_downloadable_files'    => '',
                    '_downloadable'          => '',
                    '_virtual'               => '',
                    '_thumbnail_id'          => '',
                    '_sale_price_dates_from' => '',
                    '_sale_price_dates_to'   => '',
                    '_manage_stock'          => '',
                    '_stock_status'          => '',
                    '_backorders'            => null,
                    '_tax_class'             => null,
                    '_variation_description' => ''
                );

                foreach ( $variation_fields as $field => $value ) {
                    $variation_data[ $field ] = isset( $variation_meta[ $field ][0] ) ? maybe_unserialize( $variation_meta[ $field ][0] ) : $value;
                }

                // Add the variation attributes
                $variation_data = array_merge( $variation_data, wc_get_product_variation_attributes( $variation_id ) );

                // Formatting
                $variation_data['_regular_price'] = wc_format_localized_price( $variation_data['_regular_price'] );
                $variation_data['_sale_price']    = wc_format_localized_price( $variation_data['_sale_price'] );
                $variation_data['_weight']        = wc_format_localized_decimal( $variation_data['_weight'] );
                $variation_data['_length']        = wc_format_localized_decimal( $variation_data['_length'] );
                $variation_data['_width']         = wc_format_localized_decimal( $variation_data['_width'] );
                $variation_data['_height']        = wc_format_localized_decimal( $variation_data['_height'] );
                $variation_data['_thumbnail_id']  = absint( $variation_data['_thumbnail_id'] );
                $variation_data['image']          = $variation_data['_thumbnail_id'] ? wp_get_attachment_thumb_url( $variation_data['_thumbnail_id'] ) : '';
                $variation_data['shipping_class'] = $shipping_classes && ! is_wp_error( $shipping_classes ) ? current( $shipping_classes )->term_id : '';
                $variation_data['menu_order']     = $variation->menu_order;
                $variation_data['_stock']         = '' === $variation_data['_stock'] ? '' : wc_stock_amount( $variation_data['_stock'] );

                dokan_get_template_part( 'products/edit/html-product-variation', '', array(
                    'pro'                => true,
                    'loop'               => $loop,
                    'variation_id'       => $variation_id,
                    'parent_data'        => $parent_data,
                    'variation_data'     => $variation_data,
                    'variation'          => $variation
                ) );

                $loop++;
            }
        }

        die();
    }

    /**
     * Save variations via AJAX.
     */
    public static function save_variations() {
        ob_start();

        check_ajax_referer( 'save-variations', 'security' );

        // Check permissions again and make sure we have what we need
        if ( ! current_user_can( 'dokandar' ) || empty( $_POST ) || empty( $_POST['product_id'] ) ) {
            die( -1 );
        }

        $product_id   = absint( $_POST['product_id'] );
        $product_type = empty( $_POST['product_type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product_type'] ) );

        $product_type_terms = wp_get_object_terms( $product_id, 'product_type' );

        // If the product type hasn't been set or it has changed, update it before saving variations
        if ( empty( $product_type_terms ) || $product_type !== sanitize_title( current( $product_type_terms )->name ) ) {
            wp_set_object_terms( $product_id, $product_type, 'product_type' );
        }

        WC_Meta_Box_Product_Data::save_variations( $product_id, get_post( $product_id ) );

        do_action( 'dokan_ajax_save_product_variations', $product_id );

        // Clear cache/transients
        wc_delete_product_transients( $product_id );
        die();
    }


    /**
     * Bulk action - Toggle Enabled.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_toggle_enabled( $variations, $data ) {
        global $wpdb;

        foreach ( $variations as $variation_id ) {
            $post_status = get_post_status( $variation_id );
            $new_status  = 'private' === $post_status ? 'publish' : 'private';
            $wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $variation_id ) );
        }
    }

    /**
     * Bulk action - Toggle Downloadable Checkbox.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_toggle_downloadable( $variations, $data ) {
        foreach ( $variations as $variation_id ) {
            $_downloadable   = get_post_meta( $variation_id, '_downloadable', true );
            $is_downloadable = 'no' === $_downloadable ? 'yes' : 'no';
            update_post_meta( $variation_id, '_downloadable', $is_downloadable );
        }
    }

    /**
     * Bulk action - Toggle Virtual Checkbox.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_toggle_virtual( $variations, $data ) {
        foreach ( $variations as $variation_id ) {
            $_virtual   = get_post_meta( $variation_id, '_virtual', true );
            $is_virtual = 'no' === $_virtual ? 'yes' : 'no';
            update_post_meta( $variation_id, '_virtual', $is_virtual );
        }
    }

    /**
     * Bulk action - Toggle Manage Stock Checkbox.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_toggle_manage_stock( $variations, $data ) {
        foreach ( $variations as $variation_id ) {
            $_manage_stock   = get_post_meta( $variation_id, '_manage_stock', true );
            $is_manage_stock = 'no' === $_manage_stock || '' === $_manage_stock ? 'yes' : 'no';
            update_post_meta( $variation_id, '_manage_stock', $is_manage_stock );
        }
    }

    /**
     * Bulk action - Set Regular Prices.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_regular_price( $variations, $data ) {
        if ( ! isset( $data['value'] ) ) {
            return;
        }

        foreach ( $variations as $variation_id ) {
            // Price fields
            $regular_price = wc_clean( $data['value'] );
            $sale_price    = get_post_meta( $variation_id, '_sale_price', true );

            // Date fields
            $date_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
            $date_to   = get_post_meta( $variation_id, '_sale_price_dates_to', true );
            $date_from = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
            $date_to   = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';

            dokan_save_product_price( $variation_id, $regular_price, $sale_price, $date_from, $date_to );
        }
    }

    /**
     * Bulk action - Set Sale Prices.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_sale_price( $variations, $data ) {
        if ( ! isset( $data['value'] ) ) {
            return;
        }

        foreach ( $variations as $variation_id ) {
            // Price fields
            $regular_price = get_post_meta( $variation_id, '_regular_price', true );
            $sale_price    = wc_clean( $data['value'] );

            // Date fields
            $date_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
            $date_to   = get_post_meta( $variation_id, '_sale_price_dates_to', true );
            $date_from = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
            $date_to   = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';

            dokan_save_product_price( $variation_id, $regular_price, $sale_price, $date_from, $date_to );
        }
    }

    /**
     * Bulk action - Set Stock.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_stock( $variations, $data ) {
        if ( ! isset( $data['value'] ) ) {
            return;
        }

        $value = wc_clean( $data['value'] );

        foreach ( $variations as $variation_id ) {
            if ( 'yes' === get_post_meta( $variation_id, '_manage_stock', true ) ) {
                wc_update_product_stock( $variation_id, wc_stock_amount( $value ) );
            } else {
                delete_post_meta( $variation_id, '_stock' );
            }
        }
    }

    /**
     * Bulk action - Set Weight.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_weight( $variations, $data ) {
        self::variation_bulk_set_meta( $variations, '_weight', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Set Length.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_length( $variations, $data ) {
        self::variation_bulk_set_meta( $variations, '_length', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Set Width.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_width( $variations, $data ) {
        self::variation_bulk_set_meta( $variations, '_width', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Set Height.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_height( $variations, $data ) {
        self::variation_bulk_set_meta( $variations, '_height', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Set Download Limit.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_download_limit( $variations, $data ) {
        self::variation_bulk_set_meta( $variations, '_download_limit', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Set Download Expiry.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_download_expiry( $variations, $data ) {
        self::variation_bulk_set_meta( $variations, '_download_expiry', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Delete all.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_delete_all( $variations, $data ) {
        if ( isset( $data['allowed'] ) && 'true' === $data['allowed'] ) {
            foreach ( $variations as $variation_id ) {
                wp_delete_post( $variation_id );
            }
        }
    }

    /**
     * Bulk action - Sale Schedule.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_sale_schedule( $variations, $data ) {
        if ( ! isset( $data['date_from'] ) && ! isset( $data['date_to'] ) ) {
            return;
        }

        foreach ( $variations as $variation_id ) {
            // Price fields
            $regular_price = get_post_meta( $variation_id, '_regular_price', true );
            $sale_price    = get_post_meta( $variation_id, '_sale_price', true );

            // Date fields
            $date_from = get_post_meta( $variation_id, '_sale_price_dates_from', true );
            $date_to   = get_post_meta( $variation_id, '_sale_price_dates_to', true );

            if ( 'false' === $data['date_from'] ) {
                $date_from = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
            } else {
                $date_from = $data['date_from'];
            }

            if ( 'false' === $data['date_to'] ) {
                $date_to = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';
            } else {
                $date_to = $data['date_to'];
            }

            dokan_save_product_price( $variation_id, $regular_price, $sale_price, $date_from, $date_to );
        }
    }

    /**
     * Bulk action - Increase Regular Prices.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_regular_price_increase( $variations, $data ) {
        self::variation_bulk_adjust_price( $variations, '_regular_price', '+', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Decrease Regular Prices.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_regular_price_decrease( $variations, $data ) {
        self::variation_bulk_adjust_price( $variations, '_regular_price', '-', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Increase Sale Prices.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_sale_price_increase( $variations, $data ) {
        self::variation_bulk_adjust_price( $variations, '_sale_price', '+', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Decrease Sale Prices.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param  array $data
     */
    private static function variation_bulk_action_variable_sale_price_decrease( $variations, $data ) {
        self::variation_bulk_adjust_price( $variations, '_sale_price', '-', wc_clean( $data['value'] ) );
    }

    /**
     * Bulk action - Set Price.
     *
     * @since 2.6
     *
     * @used-by bulk_edit_variations
     *
     * @param  array $variations
     * @param string $operator + or -
     * @param string $field price being adjusted
     * @param string $value Price or Percent
     */
    private static function variation_bulk_adjust_price( $variations, $field, $operator, $value ) {
        foreach ( $variations as $variation_id ) {
            // Get existing data
            $_regular_price = get_post_meta( $variation_id, '_regular_price', true );
            $_sale_price    = get_post_meta( $variation_id, '_sale_price', true );
            $date_from      = get_post_meta( $variation_id, '_sale_price_dates_from', true );
            $date_to        = get_post_meta( $variation_id, '_sale_price_dates_to', true );
            $date_from      = ! empty( $date_from ) ? date( 'Y-m-d', $date_from ) : '';
            $date_to        = ! empty( $date_to ) ? date( 'Y-m-d', $date_to ) : '';

            if ( '%' === substr( $value, -1 ) ) {
                $percent = wc_format_decimal( substr( $value, 0, -1 ) );
                $$field  += ( ( $$field / 100 ) * $percent ) * "{$operator}1";
            } else {
                $$field  += $value * "{$operator}1";
            }
            dokan_save_product_price( $variation_id, $_regular_price, $_sale_price, $date_from, $date_to );
        }
    }

    /**
     * Bulk action - Set Meta.
     *
     * @since 2.6
     *
     * @param array $variations
     * @param string $field
     * @param string $value
     */
    private static function variation_bulk_set_meta( $variations, $field, $value ) {
        foreach ( $variations as $variation_id ) {
            update_post_meta( $variation_id, $field, $value );
        }
    }

    public static function bulk_edit_variations() {
        ob_start();

        check_ajax_referer( 'bulk-edit-variations', 'security' );

        // Check permissions again and make sure we have what we need
        if ( ! current_user_can( 'dokandar' ) || empty( $_POST['product_id'] ) || empty( $_POST['bulk_action'] ) ) {
            die( -1 );
        }

        $product_id  = absint( $_POST['product_id'] );
        $bulk_action = wc_clean( $_POST['bulk_action'] );
        $data        = ! empty( $_POST['data'] ) ? array_map( 'wc_clean', $_POST['data'] ) : array();
        $variations  = array();

        if ( apply_filters( 'dokan_bulk_edit_variations_need_children', true ) ) {
            $variations = get_posts( array(
                'post_parent'    => $product_id,
                'posts_per_page' => -1,
                'post_type'      => 'product_variation',
                'fields'         => 'ids',
                'post_status'    => array( 'publish', 'private' )
            ) );
        }

        if ( method_exists( __CLASS__, "variation_bulk_action_$bulk_action" ) ) {
            call_user_func( array( __CLASS__, "variation_bulk_action_$bulk_action" ), $variations, $data );
        } else {
            do_action( 'dokan_bulk_edit_variations_default', $bulk_action, $data, $product_id, $variations );
        }

        do_action( 'dokan_bulk_edit_variations', $bulk_action, $data, $product_id, $variations );

        // Sync and update transients
        WC_Product_Variable::sync( $product_id );
        wc_delete_product_transients( $product_id );
        die();
    }

    /**
     * Delete variations via ajax function.
     */
    public static function remove_variations() {
        check_ajax_referer( 'delete-variations', 'security' );

        if ( ! current_user_can( 'dokandar' ) ) {
            die(-1);
        }

        $variation_ids = (array) $_POST['variation_ids'];

        foreach ( $variation_ids as $variation_id ) {
            $variation = get_post( $variation_id );

            if ( $variation && 'product_variation' == $variation->post_type ) {
                wp_delete_post( $variation_id );
            }
        }

        die();
    }

    /**
     * insert refund request via ajax
     *
     * @since 2.4.11
     *
     */
    public function dokan_refund_request() {

        check_ajax_referer( 'order-item', 'security' );

        $seller_id = dokan_get_seller_id_by_order($_POST['order_id']);
        $_POST['seller_id'] = $seller_id;
        $_POST['status'] = 0;

        // Validate that the refund can occur
        $refund_amount = wc_format_decimal( sanitize_text_field( $_POST['refund_amount'] ), wc_get_price_decimals() );
        $order         = wc_get_order( $_POST['order_id'] );

        $max_refund  = wc_format_decimal( $order->get_total() - $order->get_total_refunded(), wc_get_price_decimals() );

        $refund = new Dokan_Pro_Refund;

        if ( ! $refund_amount || $max_refund < $refund_amount || 0 > $refund_amount ) {
            $data = __( 'Invalid refund amount', 'dokan' );
            wp_send_json_error( $data );
        } else if ( $refund->has_pending_refund_request( $_POST['order_id'] ) ) {
            $data = __( 'You have already a processing refund request for this order.', 'dokan' );
            wp_send_json_error( $data );
        } else{
            $refund    = new Dokan_Pro_Refund;
            $refund_id = $refund->insert_refund( $_POST );

            do_action( 'dokan_after_refund_request', $refund_id, $_POST );
            do_action( 'dokan_refund_request_notification', $_POST['order_id'] );

            $data = __( 'Refund request sent successfully', 'dokan' );
            wp_send_json_success( $data );
        }
    }

    /**
     * Enable/disable seller selling capability from admin seller listing page
     *
     * @return type
     */
    function toggle_seller_status() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_POST['nonce'] ) && ! wp_verify_nonce( $_POST['nonce'], 'dokan-admin-nonce' ) ) {
            return;
        }

        $user_id = isset( $_POST['user_id'] ) ? intval( $_POST['user_id'] ) : 0;
        $status = sanitize_text_field( $_POST['type'] );

        if ( in_array( $status, array( 'yes', 'no' ) ) ) {
            if ( 'yes' == $status ) {
                $user = dokan()->vendor->get( $user_id )->make_active();
            } else {
                $user = dokan()->vendor->get( $user_id )->make_inactive();
            }
        }

        wp_send_json_success($user);
        exit;
    }

    /**
     * Load State via ajax for refund
     *
     * @since 2.4.11
     *
     * @return html Set of states
     */
    public function load_order_items() {

        check_ajax_referer( 'order-item', 'security' );

        if ( ! current_user_can( 'edit_shop_orders' ) ) {
            die(-1);
        }


        // Return HTML items
        $order_id = absint( $_POST['order_id'] );
        $order    = wc_get_order( $order_id );
        $data     = get_post_meta( $order_id );
        include( DOKAN_PRO_DIR . '/templates/orders/views/html-order-items.php' );

        die();
    }

    /**
     * Load State via ajax for shipping
     *
     * @since 2.4
     *
     * @return html Set of states
     */
    function load_state_by_country() {

        $country_id  = $_POST['country_id'];
        $country_obj = new WC_Countries();
        $states      = $country_obj->states;

        ob_start();
        if ( !empty( $states[$country_id] ) ) {
            ?>
             <tr>
                <td>
                    <label for=""><?php _e( 'State', 'dokan' ); ?></label>
                    <select name="dps_state_to[<?php echo $country_id ?>][]" class="dokan-form-control dps_state_selection" id="dps_state_selection">
                        <?php dokan_state_dropdown( $states[$country_id], '', true ); ?>
                    </select>
                </td>
                <td>
                    <label for=""><?php _e( 'Cost', 'dokan' ); ?></label>
                    <div class="dokan-input-group">
                        <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                        <input type="text" placeholder="0.00" class="dokan-form-control" name="dps_state_to_price[<?php echo $country_id; ?>][]">
                    </div>
                </td>
                <td width="15%">
                    <label for=""></label>
                    <div>
                        <a class="dps-add" href="#"><i class="fa fa-plus"></i></a>
                        <a class="dps-remove" href="#"><i class="fa fa-minus"></i></a>
                    </div>
                </td>
            </tr>
            <?php
            // }
        } else {
            ?>
            <tr>
                <td>
                    <label for=""><?php _e( 'State', 'dokan' ); ?></label>
                    <input type="text" name="dps_state_to[<?php echo $country_id ?>][]" class="dokan-form-control dps_state_selection" placeholder="State name">
                </td>
                <td>
                    <label for=""><?php _e( 'Cost', 'dokan' ); ?></label>
                    <div class="dokan-input-group">
                        <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                        <input type="text" placeholder="0.00" class="dokan-form-control" name="dps_state_to_price[<?php echo $country_id; ?>][]">
                    </div>
                </td>
                <td width="15%">
                    <label for=""></label>
                    <div>
                        <a class="dps-add" href="#"><i class="fa fa-plus"></i></a>
                        <a class="dps-remove" href="#"><i class="fa fa-minus"></i></a>
                    </div>
                </td>
            </tr>
            <?php
        }

        $data = ob_get_clean();

        wp_send_json_success( $data );
    }

    /**
     * Remove Announcement ajax
     *
     * @since 2.4
     *
     * @return josn
     */
    function remove_announcement() {
        global $wpdb;

        check_ajax_referer( 'dokan_reviews' );

        $table_name = $wpdb->prefix. 'dokan_announcement';
        $row_id     = $_POST['row_id'];

        $result = $wpdb->update(
            $table_name,
            array(
                'status' => 'trash',
            ),
            array( 'post_id' => $row_id, 'user_id' => dokan_get_current_user_id() )
        );

        ob_start();
        ?>
        <div class="dokan-no-announcement">
            <div class="annoument-no-wrapper">
                <i class="fa fa-bell dokan-announcement-icon"></i>
                <p><?php _e( 'No Announcement found', 'dokan' ) ?></p>
            </div>
        </div>
        <?php
        $content = ob_get_clean();

        if ( $result ) {
            wp_send_json_success( $content );
        } else {
            wp_send_json_error();
        }
    }

    /**
     * get state by shipping country
     *
     * @since 2.4
     *
     * @return json
     */
    function get_state_by_shipping_country() {
        global $post;
        $dps_state_rates   = get_user_meta( $_POST['author_id'], '_dps_state_rates', true );
        $country_obj = new WC_Countries();
        $states      = $country_obj->states;

        $country = $_POST['country_id'];
        ob_start(); ?>
        <?php
        if ( isset( $dps_state_rates[$country] ) && count( $dps_state_rates[$country] ) ) { ?>
            <label for="dokan-shipping-state" class="dokan-control-label"><?php _e( 'State', 'dokan' ); ?></label>
            <select name="dokan-shipping-state" class="dokan-shipping-state dokan-form-control" id="dokan-shipping-state">
                <option value=""><?php _e( '--Select State--', 'dokan' ); ?></option>
                <?php foreach ($dps_state_rates[$country] as $state_code => $state_cost ): ?>
                    <option value="<?php echo $state_code ?>"><?php
                        if ( $state_code == 'everywhere' ) {
                            _e( 'Other States', 'dokan' );
                        } else {
                            if( isset( $states[$country][$state_code] ) ) {
                                echo $states[$country][$state_code];
                            } else {
                                echo $state_code;
                            }
                        }
                    ?></option>
                <?php endforeach ?>
            </select>
        <?php
        }
        $content = ob_get_clean();

        wp_send_json_success( $content );
    }

    /**
     * calculate shipping rate in single product page
     *
     * @since 2.4
     *
     * @return json
     */
    function get_calculated_shipping_cost() {
        global $post;

        $_overwrite_shipping      = get_post_meta( $_POST['product_id'], '_overwrite_shipping', true );

        $dps_country_rates = get_user_meta( $_POST['author_id'], '_dps_country_rates', true );
        $dps_state_rates   = get_user_meta( $_POST['author_id'], '_dps_state_rates', true );

        $store_shipping_type_price    = (float)get_user_meta( $_POST['author_id'], '_dps_shipping_type_price', true );
        $additional_product_cost      = (float)get_post_meta( $_POST['product_id'], '_additional_price', true );
        $base_shipping_type_price     = (float)$store_shipping_type_price;
        $additional_qty_product_price = get_post_meta( $_POST['product_id'], '_additional_qty', true );
        $dps_additional_qty           = get_user_meta( $_POST['author_id'], '_dps_additional_qty', true );
        $additional_qty_price         = $dps_additional_qty;

        if ( $_overwrite_shipping == 'yes' ) {
            $base_shipping_type_price     = ( (float)$store_shipping_type_price + ( ($additional_product_cost) ? (float)$additional_product_cost : 0 ) );
            $additional_qty_price         = ( $additional_qty_product_price ) ? $additional_qty_product_price : $dps_additional_qty;
        }

        if ( isset( $_POST['country_id'] ) || !empty( $_POST['country_id'] ) ) {
            $country = $_POST['country_id'];
        } else {
            $country = '';
        }

        if ( isset( $_POST['quantity'] ) && $_POST['quantity'] > 0 ) {
            $quantity = $_POST['quantity'];
        } else {
            $quantity = 1;
        }

        $additional_quantity_cost = ( $quantity - 1 ) * $additional_qty_price;
        $flag = '';
        ob_start(); ?>

        <?php
        if ( $country != '' ) {
            if ( isset( $dps_state_rates[$country] ) && count( $dps_state_rates[$country] ) && empty( $_POST['state'] ) ) {
                _e( 'Please select a State from the dropdown', 'dokan' );
            } else if ( !isset( $dps_state_rates[$country] ) && empty( $_POST['state'] ) ) {
                echo __( 'Shipping Cost : ', 'dokan' ) . '<h4>' . wc_price( $dps_country_rates[$country] + $base_shipping_type_price + $additional_quantity_cost ) . '</h4>';
            } else if ( isset( $_POST['state'] ) && !empty( $_POST['state'] ) ) {
                $state = $_POST['state'];
                echo __( 'Shipping Cost : ', 'dokan' ) . '<strong>' . wc_price( $dps_state_rates[$country][$state] + $base_shipping_type_price + $additional_quantity_cost ) . '</strong>';
            }
        } else {
            _e( 'Please select a country from the dropdown', 'dokan' );
        }
        $content = ob_get_clean();

        wp_send_json_success( $content );
    }

     /**
     * Save attributes from edit product page
     *
     * @return void
     */
    function save_attributes() {
        // Get post data
        parse_str( $_POST['data'], $data );
        $post_id = absint( $_POST['post_id'] );

        // Save Attributes
        $attributes = array();

        if ( isset( $data['attribute_names'] ) ) {

            $attribute_names  = array_map( 'stripslashes', $data['attribute_names'] );
            $attribute_values = isset( $data['attribute_values'] ) ? $data['attribute_values'] : array();

            if ( isset( $data['attribute_visibility'] ) ) {
                $attribute_visibility = $data['attribute_visibility'];
            }

            if ( isset( $data['attribute_variation'] ) ) {
                $attribute_variation = $data['attribute_variation'];
            }

            $attribute_is_taxonomy   = $data['attribute_is_taxonomy'];
            $attribute_position      = $data['attribute_position'];
            $attribute_names_max_key = max( array_keys( $attribute_names ) );

            for ( $i = 0; $i <= $attribute_names_max_key; $i++ ) {
                if ( empty( $attribute_names[ $i ] ) ) {
                    continue;
                }

                $is_visible   = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
                $is_variation = isset( $attribute_variation[ $i ] ) ? 1 : 0;
                $is_taxonomy  = $attribute_is_taxonomy[ $i ] ? 1 : 0;


                if ( $is_taxonomy ) {

                    if ( isset( $attribute_values[ $i ] ) ) {

                        // Select based attributes - Format values (posted values are slugs)
                        if ( is_array( $attribute_values[ $i ] ) ) {
                            $values = $attribute_values[ $i ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                        // Text based attributes - Posted values are term names, wp_set_object_terms wants ids or slugs.
                        } else {
                            $values     = array();
                            $raw_values = explode( WC_DELIMITER, $attribute_values[ $i ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
                            foreach ( $raw_values as $value ) {
                                $term = get_term_by( 'name', $value, $attribute_names[ $i ] );
                                if ( ! $term ) {
                                    $term = wp_insert_term( $value, $attribute_names[ $i ] );

                                    if ( $term && ! is_wp_error( $term ) ) {
                                        $values[] = $term['term_id'];
                                    }
                                } else {
                                    $values[] = $term->term_id;
                                }
                            }
                        }

                        // Remove empty items in the array
                        $values = array_filter( $values, 'strlen' );

                    } else {
                        $values = array();
                    }

                    $values = array_map( 'strval', $values );

                    // Update post terms
                    if ( taxonomy_exists( $attribute_names[ $i ] ) ) {
                        wp_set_object_terms( $post_id, $values, $attribute_names[ $i ] );
                    }

                    if ( ! empty( $values ) ) {
                        // Add attribute to array, but don't set values
                        $attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
                            'name'          => wc_clean( $attribute_names[ $i ] ),
                            'value'         => '',
                            'position'      => $attribute_position[ $i ],
                            'is_visible'    => $is_visible,
                            'is_variation'  => $is_variation,
                            'is_taxonomy'   => $is_taxonomy
                        );
                    }

                } elseif ( isset( $attribute_values[ $i ] ) ) {

                    // Text based, possibly separated by pipes (WC_DELIMITER). Preserve line breaks in non-variation attributes.
                    $values = implode( ' ' . WC_DELIMITER . ' ', array_map( 'wc_clean', array_map( 'stripslashes', $attribute_values[ $i ] ) ) );

                    // Custom attribute - Add attribute to array and set the values
                    $attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
                        'name'          => wc_clean( $attribute_names[ $i ] ),
                        'value'         => $values,
                        'position'      => $attribute_position[ $i ],
                        'is_visible'    => $is_visible,
                        'is_variation'  => $is_variation,
                        'is_taxonomy'   => $is_taxonomy
                    );
                }
            }
        }

        uasort( $attributes, 'wc_product_attribute_uasort_comparison' );
        update_post_meta( $post_id, '_product_attributes', $attributes );
        die();
    }


    /**
     * Save attributes from edit product page
     *
     * @since 2.3
     *
     * @return void
     */
    function save_attributes_options() {
        // Get post data
        parse_str( $_POST['formdata'], $data );
        $post_id = absint( $data['product_id'] );

        // Save Attributes
        $attributes = array();

        if ( isset( $data['attribute_names'] ) ) {

            $attribute_names = array_map( 'stripslashes', $data['attribute_names'] );
            $attr_values = isset( $data['attribute_values'] ) ? $data['attribute_values'] : array();

            $attr_tax = $attr_pos = $attr_visible = $attr_variation = array();

            foreach ( $attribute_names as $key => $value ) {
                $attr_pos[$key]       = $key;
                $attr_visible[$key]   = 1;
                $attr_variation[$key] = 1;
                $attribute_values[$key] = explode(',', $attr_values[$key] );
            }

            $attribute_visibility = $attr_visible;
            $attribute_variation = $attr_variation;
            $attribute_is_taxonomy = $data['attribute_is_taxonomy'];
            $attribute_position = $attr_pos;
            $attribute_names_count = sizeof( $attribute_names );

            for ( $i=0; $i < $attribute_names_count; $i++ ) {
                if ( ! $attribute_names[ $i ] )
                    continue;

                $is_visible     = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
                $is_variation   = isset( $attribute_variation[ $i ] ) ? 1 : 0;
                $is_taxonomy    = $attribute_is_taxonomy[ $i ] ? 1 : 0;

                if ( $is_taxonomy ) {

                    if ( isset( $attribute_values[ $i ] ) ) {

                        // Select based attributes - Format values (posted values are slugs)
                        if ( is_array( $attribute_values[ $i ] ) ) {
                            $values = $attribute_values[ $i ];

                        // Text based attributes - Posted values are term names - don't change to slugs
                        } else {
                            $values = array_map( 'stripslashes', array_map( 'strip_tags', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) );
                        }

                        // Remove empty items in the array
                        $values = array_filter( $values, 'strlen' );

                    } else {
                        $values = array();
                    }

                    // Update post terms
                    if ( taxonomy_exists( $attribute_names[ $i ] ) )
                        wp_set_object_terms( $post_id, $values, $attribute_names[ $i ] );

                    if ( $values ) {
                        // Add attribute to array, but don't set values
                        $attributes[ $attribute_names[ $i ] ] = array(
                            'name'          => wc_clean( $attribute_names[ $i ] ),
                            'value'         => '',
                            'position'      => $attribute_position[ $i ],
                            'is_visible'    => $is_visible,
                            'is_variation'  => $is_variation,
                            'is_taxonomy'   => $is_taxonomy
                        );
                    }

                } elseif ( isset( $attribute_values[ $i ] ) ) {

                    // Text based, separate by pipe
                    $values = implode( ' ' . WC_DELIMITER . ' ', array_map( 'wc_clean', array_map( 'stripslashes', $attribute_values[ $i ] ) ) );

                    // Custom attribute - Add attribute to array and set the values
                    $attributes[ $attribute_names[ $i ] ] = array(
                        'name'          => wc_clean( $attribute_names[ $i ] ),
                        'value'         => $values,
                        'position'      => $attribute_position[ $i ],
                        'is_visible'    => $is_visible,
                        'is_variation'  => $is_variation,
                        'is_taxonomy'   => $is_taxonomy
                    );
                }
            }
        }

        if ( ! function_exists( 'attributes_cmp' ) ) {
            function attributes_cmp( $a, $b ) {
                if ( $a['position'] == $b['position'] ) return 0;
                return ( $a['position'] < $b['position'] ) ? -1 : 1;
            }
        }

        uasort( $attributes, 'attributes_cmp' );
        update_post_meta( $post_id, '_product_attributes', $attributes );

        wp_send_json_success();
        die();
    }

    /**
     * Delete variations via ajax function
     */
    public function remove_variation() {
        if ( ! current_user_can( 'dokandar' ) ) {
            die(-1);
        }

        $variation_ids = (array) $_POST['variation_ids'];

        foreach ( $variation_ids as $variation_id ) {
            $variation = get_post( $variation_id );

            if ( $variation && 'product_variation' == $variation->post_type ) {
                wp_delete_post( $variation_id );
            }
        }

        die();
    }

    /**
     * Save Variation Options
     *
     * @since 2.3
     *
     * @return json
     */
    function save_variations_options() {
        global $woocommerce, $wpdb;

        parse_str( $_POST['formdata'], $postdata );
        $variation_ids = array();
        $attributes    = (array) maybe_unserialize( get_post_meta( $postdata['post_id'], '_product_attributes', true ) );

        if ( isset( $postdata['variable_sku'] ) ) {

            $variable_post_id               = isset( $postdata['variable_post_id'] ) ? $postdata['variable_post_id'] : $variation_ids ;
            $variable_sku                   = isset( $postdata['variable_sku'] ) ? $postdata['variable_sku'] : array();
            $variable_regular_price         = isset( $postdata['variable_regular_price'] ) ? $postdata['variable_regular_price'] : array();
            $variable_sale_price            = isset( $postdata['variable_sale_price'] ) ? $postdata['variable_sale_price'] : array();
            $upload_image_id                = isset( $postdata['upload_image_id'] ) ? $postdata['upload_image_id'] : array();
            $variable_download_limit        = isset( $postdata['variable_download_limit'] ) ? $postdata['variable_download_limit'] : array();
            $variable_download_expiry       = isset( $postdata['variable_download_expiry'] ) ? $postdata['variable_download_expiry'] : array();
            $variable_shipping_class        = isset( $postdata['variable_shipping_class'] ) ? $postdata['variable_shipping_class'] : array();
            $variable_tax_class             = isset( $postdata['variable_tax_class'] ) ? $postdata['variable_tax_class'] : array();
            $variable_menu_order            = isset( $postdata['variation_menu_order'] ) ? $postdata['variation_menu_order'] : array();
            $variable_sale_price_dates_from = isset( $postdata['variable_sale_price_dates_from'] ) ? $postdata['variable_sale_price_dates_from'] : array();
            $variable_sale_price_dates_to   = isset( $postdata['variable_sale_price_dates_to'] ) ? $postdata['variable_sale_price_dates_to'] : array();

            $variable_weight                = isset( $postdata['variable_weight'] ) ? $postdata['variable_weight'] : array();
            $variable_length                = isset( $postdata['variable_length'] ) ? $postdata['variable_length'] : array();
            $variable_width                 = isset( $postdata['variable_width'] ) ? $postdata['variable_width'] : array();
            $variable_height                = isset( $postdata['variable_height'] ) ? $postdata['variable_height'] : array();
            $variable_stock                 = isset( $postdata['variable_stock'] ) ? $postdata['variable_stock'] : array();
            $variable_manage_stock          = isset( $postdata['variable_manage_stock'] ) ? $postdata['variable_manage_stock'] : array();
            $variable_stock_status          = isset( $postdata['variable_stock_status'] ) ? $postdata['variable_stock_status'] : array();
            $variable_backorders            = isset( $postdata['variable_backorders'] ) ? $postdata['variable_backorders'] : array();

            $variable_enabled               = isset( $postdata['variable_enabled'] ) ? $postdata['variable_enabled'] : array();
            $variable_is_virtual            = isset( $postdata['variable_is_virtual'] ) ? $postdata['variable_is_virtual'] : array();
            $variable_is_downloadable       = isset( $postdata['variable_is_downloadable'] ) ? $postdata['variable_is_downloadable'] : array();

            $max_loop = max( array_keys( $variable_post_id ) );

            for ( $i = 0; $i <= $max_loop; $i ++ ) {

                if ( ! isset( $variable_post_id[ $i ] ) )
                    continue;

                $variation_id = absint( $variable_post_id[ $i ] );

                // Virtal/Downloadable
                $is_downloadable = isset( $variable_is_downloadable[ $i ] ) ? 'yes' : 'no';

                if ( isset( $variable_is_virtual[ $i ] ) ) {
                    $is_virtual = 'yes';
                } else {

                    if ( $is_downloadable == 'yes' ) {
                        $is_virtual = 'yes';
                    } else {
                        $is_virtual = 'no';
                    }
                }
                // $is_virtual = isset(  ) ? 'yes' : 'no';

                // Enabled or disabled
                $post_status = isset( $variable_enabled[ $i ] ) ? 'publish' : 'private';
                $parent_manage_stock = isset( $postdata['_manage_stock'] ) ? 'yes' : 'no';
                $manage_stock        = isset( $variable_manage_stock[ $i ] ) ? 'yes' : 'no';

                // Generate a useful post title
                $variation_post_title = sprintf( __( 'Variation #%s of %s', 'dokan' ), absint( $variation_id ), esc_html( get_the_title( $postdata['post_id'] ) ) );

                // Update or Add post
                if ( ! $variation_id ) {

                    $variation = array(
                        'post_title'    => $variation_post_title,
                        'post_content'  => '',
                        'post_status'   => $post_status,
                        'post_author'   => dokan_get_current_user_id(),
                        'post_parent'   => $postdata['post_id'],
                        'post_type'     => 'product_variation',
                        'menu_order'    => $variable_menu_order[ $i ]
                    );

                    $variation_id = wp_insert_post( $variation );

                    do_action( 'woocommerce_create_product_variation', $variation_id );
                } else {
                    $wpdb->update( $wpdb->posts, array( 'post_status' => $post_status, 'post_title' => $variation_post_title ), array( 'ID' => $variation_id ) );

                    do_action( 'woocommerce_update_product_variation', $variation_id );
                }

                // Only continue if we have a variation ID
                if ( ! $variation_id ) {
                    continue;
                }

                // Update post meta
                update_post_meta( $variation_id, '_sku', wc_clean( $variable_sku[ $i ] ) );
                //update_post_meta( $variation_id, '_stock', wc_clean( $variable_stock[ $i ] ) );
                update_post_meta( $variation_id, '_thumbnail_id', absint( $upload_image_id[ $i ] ) );
                update_post_meta( $variation_id, '_virtual', wc_clean( $is_virtual ) );
                update_post_meta( $variation_id, '_downloadable', wc_clean( $is_downloadable ) );
                update_post_meta( $variation_id, '_manage_stock', $manage_stock );

                // Only update stock status to user setting if changed by the user, but do so before looking at stock levels at variation level
                if ( ! empty( $variable_stock_status[ $i ] ) ) {
                    wc_update_product_stock_status( $variation_id, $variable_stock_status[ $i ] );
                }

                if ( 'yes' === $manage_stock ) {
                    update_post_meta( $variation_id, '_backorders', wc_clean( $variable_backorders[ $i ] ) );
                    wc_update_product_stock( $variation_id, wc_stock_amount( $variable_stock[ $i ] ) );
                } else {
                    delete_post_meta( $variation_id, '_backorders' );
                    delete_post_meta( $variation_id, '_stock' );
                }

                if ( isset( $variable_weight[ $i ] ) )
                    update_post_meta( $variation_id, '_weight', ( $variable_weight[ $i ] === '' ) ? '' : wc_format_decimal( $variable_weight[ $i ] ) );
                if ( isset( $variable_length[ $i ] ) )
                    update_post_meta( $variation_id, '_length', ( $variable_length[ $i ] === '' ) ? '' : wc_format_decimal( $variable_length[ $i ] ) );
                if ( isset( $variable_width[ $i ] ) )
                    update_post_meta( $variation_id, '_width', ( $variable_width[ $i ] === '' ) ? '' : wc_format_decimal( $variable_width[ $i ] ) );
                if ( isset( $variable_height[ $i ] ) )
                    update_post_meta( $variation_id, '_height', ( $variable_height[ $i ] === '' ) ? '' : wc_format_decimal( $variable_height[ $i ] ) );


                // Price handling
                $regular_price  = wc_format_decimal( $variable_regular_price[ $i ] );
                $sale_price     = ( $variable_sale_price[ $i ] === '' ? '' : wc_format_decimal( $variable_sale_price[ $i ] ) );
                $date_from      = wc_clean( $variable_sale_price_dates_from[ $i ] );
                $date_to        = wc_clean( $variable_sale_price_dates_to[ $i ] );

                update_post_meta( $variation_id, '_regular_price', $regular_price );
                update_post_meta( $variation_id, '_sale_price', $sale_price );

                // Save Dates
                if ( $date_from )
                    update_post_meta( $variation_id, '_sale_price_dates_from', strtotime( $date_from ) );
                else
                    update_post_meta( $variation_id, '_sale_price_dates_from', '' );

                if ( $date_to )
                    update_post_meta( $variation_id, '_sale_price_dates_to', strtotime( $date_to ) );
                else
                    update_post_meta( $variation_id, '_sale_price_dates_to', '' );

                if ( $date_to && ! $date_from )
                    update_post_meta( $variation_id, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );

                // Update price if on sale
                if ( $sale_price != '' && $date_to == '' && $date_from == '' )
                    update_post_meta( $variation_id, '_price', $sale_price );
                else
                    update_post_meta( $variation_id, '_price', $regular_price );

                if ( $sale_price != '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) )
                    update_post_meta( $variation_id, '_price', $sale_price );

                if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
                    update_post_meta( $variation_id, '_price', $regular_price );
                    update_post_meta( $variation_id, '_sale_price_dates_from', '' );
                    update_post_meta( $variation_id, '_sale_price_dates_to', '' );
                }

                if ( isset( $variable_tax_class[ $i ] ) && $variable_tax_class[ $i ] !== 'parent' )
                    update_post_meta( $variation_id, '_tax_class', wc_clean( $variable_tax_class[ $i ] ) );
                else
                    delete_post_meta( $variation_id, '_tax_class' );

                if ( $is_downloadable == 'yes' ) {
                    update_post_meta( $variation_id, '_download_limit', wc_clean( $variable_download_limit[ $i ] ) );
                    update_post_meta( $variation_id, '_download_expiry', wc_clean( $variable_download_expiry[ $i ] ) );

                    $files         = array();
                    $file_names    = isset( $postdata['_wc_variation_file_names'][ $variation_id ] ) ? array_map( 'wc_clean', $postdata['_wc_variation_file_names'][ $variation_id ] ) : array();
                    $file_urls     = isset( $postdata['_wc_variation_file_urls'][ $variation_id ] ) ? array_map( 'esc_url_raw', array_map( 'trim', $postdata['_wc_variation_file_urls'][ $variation_id ] ) ) : array();
                    $file_url_size = sizeof( $file_urls );

                    for ( $ii = 0; $ii < $file_url_size; $ii ++ ) {
                        if ( ! empty( $file_urls[ $ii ] ) )
                            $files[ md5( $file_urls[ $ii ] ) ] = array(
                                'name' => $file_names[ $ii ],
                                'file' => $file_urls[ $ii ]
                            );
                    }

                    // grant permission to any newly added files on any existing orders for this product prior to saving
                    do_action( 'woocommerce_process_product_file_download_paths', $postdata['post_id'], $variation_id, $files );

                    update_post_meta( $variation_id, '_downloadable_files', $files );
                } else {
                    update_post_meta( $variation_id, '_download_limit', '' );
                    update_post_meta( $variation_id, '_download_expiry', '' );
                    update_post_meta( $variation_id, '_downloadable_files', '' );
                }

                // Save shipping class
                $variable_shipping_class[ $i ] = ! empty( $variable_shipping_class[ $i ] ) ? (int) $variable_shipping_class[ $i ] : '';
                wp_set_object_terms( $variation_id, $variable_shipping_class[ $i ], 'product_shipping_class');

                // Update taxonomies - don't use wc_clean as it destroys sanitized characters
                $updated_attribute_keys = array();

                foreach ( $attributes as $attribute ) {

                    if ( $attribute['is_variation'] ) {
                        $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
                        $updated_attribute_keys[] = $attribute_key;

                        if ( $attribute['is_taxonomy'] ) {
                            // Don't use wc_clean as it destroys sanitized characters
                            $value = isset( $postdata[$attribute_key][$i] ) ? sanitize_title( stripslashes( $postdata[$attribute_key][$i] ) ) : '';
                        } else {
                            $value = isset( $postdata[$attribute_key][$i] ) ? wc_clean( stripslashes( $postdata[$attribute_key][$i] ) ) : '';
                        }

                        update_post_meta( $variation_id, $attribute_key, $value );
                    }
                }

                // Remove old taxonomies attributes so data is kept up to date - first get attribute key names
                $delete_attribute_keys = $wpdb->get_col( $wpdb->prepare( "SELECT meta_key FROM {$wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND meta_key NOT IN ( '" . implode( "','", $updated_attribute_keys ) . "' ) AND post_id = %d;", $variation_id ) );

                foreach ( $delete_attribute_keys as $key ) {
                    delete_post_meta( $variation_id, $key );
                }

                do_action( 'woocommerce_save_product_variation', $variation_id, $i );
            }
        }

        // Update parent if variable so price sorting works and stays in sync with the cheapest child
        WC_Product_Variable::sync( $postdata['post_id'] );

        // Update default attribute options setting
        $default_attributes = array();

        foreach ( $attributes as $attribute ) {
            if ( $attribute['is_variation'] ) {
                $value = '';

                if ( isset( $postdata[ 'default_attribute_' . sanitize_title( $attribute['name'] ) ] ) ) {
                    if ( $attribute['is_taxonomy'] ) {
                        // Don't use wc_clean as it destroys sanitized characters
                        $value = sanitize_title( trim( stripslashes( $postdata[ 'default_attribute_' . sanitize_title( $attribute['name'] ) ] ) ) );
                    } else {
                        $value = wc_clean( trim( stripslashes( $postdata[ 'default_attribute_' . sanitize_title( $attribute['name'] ) ] ) ) );
                    }
                }

                if ( $value ) {
                    $default_attributes[ sanitize_title( $attribute['name'] ) ] = $value;
                }
            }
        }

        update_post_meta( $postdata['post_id'], '_default_attributes', $default_attributes );

        wp_send_json_success();
    }

    /**
     * Add New Variations Options
     *
     * @since 2.3
     *
     * @return josn success
     */
    function add_new_variations_options() {

        $post_id = intval( $_POST['post_id'] );
        $menu_order = intval( $_POST['menu_order'] );

        $variation = array(
            'post_title'   => 'Product #' . $post_id . ' Variation',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_author'  => dokan_get_current_user_id(),
            'post_parent'  => $post_id,
            'post_type'    => 'product_variation',
            'menu_order'    => $menu_order

        );

        $variation_id = wp_insert_post( $variation );

        wp_send_json_success();
    }

    /**
     * Remove Single Variation Item
     *
     * @since 2.3
     *
     * @return json
     */
    function remove_single_variation_item() {

        $variation_id = (int)$_POST['variation_id'];

        $variation = get_post( $variation_id );

        if ( $variation && 'product_variation' == $variation->post_type ) {
            wp_delete_post( $variation_id );
        }

        wp_send_json_success();
    }

    function add_attr_predefined_attribute() {
        check_ajax_referer( 'dokan_reviews' );

        global $wc_product_attributes;

        $thepostid     = 0;
        $taxonomy      = sanitize_text_field( $_POST['taxonomy'] );
        $i             = absint( $_POST['i'] );
        $attribute     = array(
            'name'         => $taxonomy,
            'value'        => '',
            'is_visible'   => apply_filters( 'dokan_attribute_default_visibility', 1 ),
            'is_variation' => apply_filters( 'dokan_attribute_default_is_variation', 0 ),
            'is_taxonomy'  => $taxonomy ? 1 : 0
        );

        if ( $taxonomy ) {
            $attribute_taxonomy = $wc_product_attributes[ $taxonomy ];
            $metabox_class[]    = 'taxonomy';
            $metabox_class[]    = $taxonomy;
            $attribute_label    = wc_attribute_label( $taxonomy );
        } else {
            $attribute_label = '';
            $attribute_taxonomy = array();
            $metabox_class[]    = '';
        }
        ob_start();
        dokan_get_template_part( 'products/edit/html-product-attribute', '', array(
            'pro'                => true,
            'i'                  => $i,
            'thepostid'          => $thepostid,
            'taxonomy'           => $taxonomy,
            'attribute_taxonomy' => $attribute_taxonomy,
            'attribute_label'    => $attribute_label,
            'attribute'          => $attribute,
            'metabox_class'      => $metabox_class,
            'position'           => 0
        ) );
        $content = ob_get_clean();
        wp_send_json_success( $content );
    }

    /**
     * Add new attribute from predifined attribute
     *
     * @since 2.5
     *
     * @return void
     */
    function add_new_attribute() {
        check_ajax_referer( 'dokan_reviews' );

        if ( ! current_user_can( 'dokandar' ) ) {
            die(-1);
        }

        $taxonomy = esc_attr( $_POST['taxonomy'] );
        $term     = wc_clean( $_POST['term'] );

        if ( taxonomy_exists( $taxonomy ) ) {

            $result = wp_insert_term( $term, $taxonomy );

            if ( is_wp_error( $result ) ) {
                wp_send_json( array(
                    'error' => $result->get_error_message()
                ) );
            } else {
                $term = get_term_by( 'id', $result['term_id'], $taxonomy );
                wp_send_json( array(
                    'term_id' => $term->term_id,
                    'name'    => $term->name,
                    'slug'    => $term->slug
                ) );
            }
        }
    }

    /**
     * Add Predefined Attribute
     *
     * @since 2.3
     *
     * @return json success|$content (array)
     */
    function add_predefined_attribute() {
        $attr_name               = $_POST['name'];
        $single                  = ( isset( $_POST['from'] ) && $_POST['from'] == 'popup' ) ? 'single-':'';
        $remove_btn              = ( isset( $_POST['from'] ) && $_POST['from'] == 'popup' ) ? 'single_':'';
        $attribute_taxonomy_name = wc_attribute_taxonomy_name( $attr_name );
        $tax                     = get_taxonomy( $attribute_taxonomy_name );
        $options                 = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );
        $att_val                 = wp_list_pluck( $options, 'name');
        ob_start();
        ?>
        <tr class="dokan-<?php echo $single; ?>attribute-options">
            <td width="20%">
                <input type="text" disabled="disabled" value="<?php echo $attr_name; ?>" class="dokan-form-control dokan-<?php echo $single; ?>attribute-option-name-label" data-attribute_name="<?php echo wc_sanitize_taxonomy_name( str_replace( 'pa_', '', $attribute_taxonomy_name ) ); ?>">
                <input type="hidden" name="attribute_names[]" value="<?php echo esc_attr( $attribute_taxonomy_name ); ?>" class="dokan-<?php echo $single; ?>attribute-option-name">
                <input type="hidden" name="attribute_is_taxonomy[]" value="1">
            </td>
            <td colspan="3"><input type="text" name="attribute_values[]" value="<?php echo implode( ',', $att_val ); ?>" data-preset_attr="<?php echo implode( ',', $att_val ); ?>" class="dokan-form-control dokan-<?php echo $single; ?>attribute-option-values"></td>
            <td><button title="<?php _e( 'Clear All' , 'dokan' ) ?>"class="dokan-btn dokan-btn-theme clear_attributes"><?php _e( 'Clear' , 'dokan' ) ?></button>
                <button title="Delete" class="dokan-btn dokan-btn-theme remove_<?php echo $remove_btn; ?>attribute"><i class="fa fa-trash-o"></i></button>
            </td>
        </tr>
        <?php
        $content = ob_get_clean();
        wp_send_json_success( $content );
    }

    /**
     * Add variation via ajax function
     *
     * @since 2.3
     *
     * @return void
     */
    public static function add_variation() {
        check_ajax_referer( 'add-variation', 'security' );

        if ( ! current_user_can( 'dokandar' ) ) {
            die(-1);
        }

        global $post;

        $post_id = intval( $_POST['post_id'] );
        $post    = get_post( $post_id ); // Set $post global so its available like within the admin screens
        $loop    = intval( $_POST['loop'] );

        $variation = array(
            'post_title'   => 'Product #' . $post_id . ' Variation',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_author'  => dokan_get_current_user_id(),
            'post_parent'  => $post_id,
            'post_type'    => 'product_variation',
            'menu_order'   => -1
        );

        $variation_id = wp_insert_post( $variation );

        do_action( 'dokan_create_product_variation', $variation_id );

        if ( $variation_id ) {
            $variation        = get_post( $variation_id );
            $variation_meta   = get_post_meta( $variation_id );
            $variation_data   = array();
            $shipping_classes = get_the_terms( $variation_id, 'product_shipping_class' );
            $variation_fields = array(
                '_sku'                   => '',
                '_stock'                 => '',
                '_regular_price'         => '',
                '_sale_price'            => '',
                '_weight'                => '',
                '_length'                => '',
                '_width'                 => '',
                '_height'                => '',
                '_download_limit'        => '',
                '_download_expiry'       => '',
                '_downloadable_files'    => '',
                '_downloadable'          => '',
                '_virtual'               => '',
                '_thumbnail_id'          => '',
                '_sale_price_dates_from' => '',
                '_sale_price_dates_to'   => '',
                '_manage_stock'          => '',
                '_stock_status'          => '',
                '_backorders'            => null,
                '_tax_class'             => null,
                '_variation_description' => ''
            );

            foreach ( $variation_fields as $field => $value ) {
                $variation_data[ $field ] = isset( $variation_meta[ $field ][0] ) ? maybe_unserialize( $variation_meta[ $field ][0] ) : $value;
            }

            // Add the variation attributes
            $variation_data = array_merge( $variation_data, wc_get_product_variation_attributes( $variation_id ) );

            // Formatting
            $variation_data['_regular_price'] = wc_format_localized_price( $variation_data['_regular_price'] );
            $variation_data['_sale_price']    = wc_format_localized_price( $variation_data['_sale_price'] );
            $variation_data['_weight']        = wc_format_localized_decimal( $variation_data['_weight'] );
            $variation_data['_length']        = wc_format_localized_decimal( $variation_data['_length'] );
            $variation_data['_width']         = wc_format_localized_decimal( $variation_data['_width'] );
            $variation_data['_height']        = wc_format_localized_decimal( $variation_data['_height'] );
            $variation_data['_thumbnail_id']  = absint( $variation_data['_thumbnail_id'] );
            $variation_data['image']          = $variation_data['_thumbnail_id'] ? wp_get_attachment_thumb_url( $variation_data['_thumbnail_id'] ) : '';
            $variation_data['shipping_class'] = $shipping_classes && ! is_wp_error( $shipping_classes ) ? current( $shipping_classes )->term_id : '';
            $variation_data['menu_order']     = $variation->menu_order;
            $variation_data['_stock']         = wc_stock_amount( $variation_data['_stock'] );

            // Get tax classes
            $tax_classes           = WC_Tax::get_tax_classes();
            $tax_class_options     = array();
            $tax_class_options[''] = __( 'Standard', 'dokan' );

            if ( ! empty( $tax_classes ) ) {
                foreach ( $tax_classes as $class ) {
                    $tax_class_options[ sanitize_title( $class ) ] = esc_attr( $class );
                }
            }

            // Set backorder options
            $backorder_options = array(
                'no'     => __( 'Do not allow', 'dokan' ),
                'notify' => __( 'Allow, but notify customer', 'dokan' ),
                'yes'    => __( 'Allow', 'dokan' )
            );

            // set stock status options
            $stock_status_options = array(
                'instock'    => __( 'In stock', 'dokan' ),
                'outofstock' => __( 'Out of stock', 'dokan' )
            );

            // Get attributes
            $attributes = (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

            $parent_data = array(
                'id'                   => $post_id,
                'attributes'           => $attributes,
                'tax_class_options'    => $tax_class_options,
                'sku'                  => get_post_meta( $post_id, '_sku', true ),
                'weight'               => wc_format_localized_decimal( get_post_meta( $post_id, '_weight', true ) ),
                'length'               => wc_format_localized_decimal( get_post_meta( $post_id, '_length', true ) ),
                'width'                => wc_format_localized_decimal( get_post_meta( $post_id, '_width', true ) ),
                'height'               => wc_format_localized_decimal( get_post_meta( $post_id, '_height', true ) ),
                'tax_class'            => get_post_meta( $post_id, '_tax_class', true ),
                'backorder_options'    => $backorder_options,
                'stock_status_options' => $stock_status_options
            );

            if ( ! $parent_data['weight'] ) {
                $parent_data['weight'] = wc_format_localized_decimal( 0 );
            }

            if ( ! $parent_data['length'] ) {
                $parent_data['length'] = wc_format_localized_decimal( 0 );
            }

            if ( ! $parent_data['width'] ) {
                $parent_data['width'] = wc_format_localized_decimal( 0 );
            }

            if ( ! $parent_data['height'] ) {
                $parent_data['height'] = wc_format_localized_decimal( 0 );
            }

            dokan_get_template_part( 'products/edit/html-product-variation', '', array(
                'pro'                => true,
                'loop'               => $loop,
                'variation_id'       => $variation_id,
                'parent_data'        => $parent_data,
                'variation_data'     => $variation_data,
                'variation'          => $variation
            ) );
        }

        die();
    }

    /**
     * Link all variations via ajax function
     *
     * @since 2.3
     *
     * @return void
     */
    function link_all_variations() {
        if ( ! defined( 'WC_MAX_LINKED_VARIATIONS' ) ) {
            define( 'WC_MAX_LINKED_VARIATIONS', 49 );
        }

        check_ajax_referer( 'link-variations', 'security' );

        wc_set_time_limit( 0 );

        $post_id = intval( $_POST['post_id'] );

        if ( ! $post_id ) {
            die();
        }

        $variations = array();

        $_product = wc_get_product( $post_id, array( 'product_type' => 'variable' ) );

        // Put variation attributes into an array
        foreach ( $_product->get_attributes() as $attribute ) {
            if ( ! $attribute['is_variation'] ) {
                continue;
            }

            $attribute_field_name = 'attribute_' . sanitize_title( $attribute['name'] );

            if ( $attribute['is_taxonomy'] ) {
                $options = wc_get_product_terms( $post_id, $attribute['name'], array( 'fields' => 'slugs' ) );
            } else {
                $options = explode( WC_DELIMITER, $attribute['value'] );
            }

            $options = array_map( 'trim', $options );

            $variations[ $attribute_field_name ] = $options;
        }

        // Quit out if none were found
        if ( sizeof( $variations ) == 0 ) {
            die();
        }

        // Get existing variations so we don't create duplicates
        $available_variations = array();

        foreach( $_product->get_children() as $child_id ) {
            $child = $_product->get_child( $child_id );

            if ( ! empty( $child->variation_id ) ) {
                $available_variations[] = $child->get_variation_attributes();
            }
        }

        // Created posts will all have the following data
        $variation_post_data = array(
            'post_title'   => 'Product #' . $post_id . ' Variation',
            'post_content' => '',
            'post_status'  => 'publish',
            'post_author'  => dokan_get_current_user_id(),
            'post_parent'  => $post_id,
            'post_type'    => 'product_variation'
        );

        $variation_ids       = array();
        $added               = 0;
        $possible_variations = wc_array_cartesian( $variations );

        foreach ( $possible_variations as $variation ) {

            // Check if variation already exists
            if ( in_array( $variation, $available_variations ) ) {
                continue;
            }

            $variation_id = wp_insert_post( $variation_post_data );

            $variation_ids[] = $variation_id;

            foreach ( $variation as $key => $value ) {
                update_post_meta( $variation_id, $key, $value );
            }

            // Save stock status
            update_post_meta( $variation_id, '_stock_status', 'instock' );

            $added++;

            do_action( 'dokan_product_variation_linked', $variation_id );

            if ( $added > WC_MAX_LINKED_VARIATIONS )
                break;
        }

        delete_transient( 'wc_product_children_' . $post_id );

        echo $added;

        die();
    }

    /**
     * Dokan Pre Define Attribute Render
     *
     * @since 2.0
     *
     * @return void
     */
    function dokan_pre_define_attribute() {

        $attribute = $_POST;
        $attribute_taxonomy_name = wc_attribute_taxonomy_name( $attribute['name'] );
        $tax = get_taxonomy( $attribute_taxonomy_name );
        $options = get_terms( $attribute_taxonomy_name, 'orderby=name&hide_empty=0' );
        $i = $_POST['row'];
        ob_start();
        ?>
        <div class="inputs-box woocommerce_attribute" data-count="<?php echo $i; ?>">
            <div class="box-header">
                <input type="text" disabled="disabled" value="<?php echo $attribute['name']; ?>">
                <input type="hidden" name="attribute_names[<?php echo $i; ?>]" value="<?php echo esc_attr( $attribute_taxonomy_name ); ?>">
                <input type="hidden" name="attribute_is_taxonomy[<?php echo $i; ?>]" value="1">
                <input type="hidden" name="attribute_position[<?php echo $i; ?>]" class="attribute_position" value="<?php echo esc_attr( $i ); ?>" />
                <span class="actions">
                    <button class="row-remove btn pull-right btn-danger btn-sm"><?php _e( 'Remove', 'dokan' ); ?></button>
                </span>
            </div>
            <div class="box-inside clearfix">
                <div class="attribute-config">
                    <ul class="list-unstyled ">
                        <li>
                            <label class="checkbox-inline">
                                <input type="checkbox" class="checkbox" <?php
                                $tax = '';
                                checked( apply_filters( 'default_attribute_visibility', false, $tax ), true );
                                ?> name="attribute_visibility[<?php echo $i; ?>]" value="1" /> <?php _e( 'Visible on the product page', 'dokan' ); ?>
                            </label>
                        </li>
                        <li class="enable_variation" <?php echo ( $_POST['type'] === 'simple' )? 'style="display:none;"' : ""; ?>>
                            <label class="checkbox-inline">
                            <input type="checkbox" class="checkbox" <?php
                            checked( apply_filters( 'default_attribute_variation', false, $tax ), true );
                        ?> name="attribute_variation[<?php echo $i; ?>]" value="1" /> <?php _e( 'Used for variations', 'dokan' ); ?></label>
                        </li>
                    </ul>
                </div>
                <div class="attribute-options">
                    <ul class="option-couplet list-unstyled ">
                        <?php
                        if ($options) {
                            foreach ($options as $count => $option) {
                                ?>
                                <li>
                                    <input type="text" class="option" placeholder="<?php _e( 'Option...', 'dokan' ); ?>" name="attribute_values[<?php echo $i; ?>][<?php echo $count; ?>]" value="<?php echo esc_attr( $option->name ); ?>">
                                    <span class="item-action actions">
                                        <a href="#" class="row-add">+</a>
                                        <a href="#" class="row-remove">-</a>
                                    </span>
                                </li>
                                <?php
                            }
                        } else {
                            ?>
                            <li>
                                <input type="text" class="option" name="attribute_values[<?php echo $i; ?>][0]" placeholder="<?php _e( 'Option...', 'dokan' ); ?>">
                                <span class="item-action actions">
                                    <a href="#" class="row-add">+</a>
                                    <a href="#" class="row-remove">-</a>
                                </span>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div> <!-- .attribute-options -->
            </div> <!-- .box-inside -->
        </div> <!-- .input-box -->
        <?php
        $response = ob_get_clean();
        return wp_send_json_success( $response );
    }

}
