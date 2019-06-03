<?php

/**
 * Dokan Pro Product Class
 *
 * @since 2.4
 *
 * @package dokan
 */
class Dokan_Pro_Products {

    /**
     * Holds the inline edit options
     *
     * @var array
     */
    private $inline_edit_options = array();

    /**
     * Load automatically when class initiate
     *
     * @since 2.4
     *
     * @uses actions
     * @uses filters
     */
    public function __construct() {
        add_action( 'dokan_product_edit_after_inventory_variants', array( $this, 'load_shipping_tax_content' ), 10, 2 );
        add_action( 'dokan_product_edit_after_inventory_variants', array( $this, 'load_linked_product_content' ), 15, 2 );
        add_action( 'dokan_product_edit_after_inventory_variants', array( $this, 'load_variations_content' ), 20, 2 );
        add_action( 'dokan_product_edit_after_inventory_variants', array( $this, 'load_lot_discount_content' ), 25, 2 );
        add_action( 'dokan_dashboard_wrap_after', array( $this, 'load_variations_js_template' ), 10, 2 );
        add_action( 'dokan_render_new_product_template', array( $this, 'render_new_product_template' ), 10 );
        add_action( 'woocommerce_product_options_advanced', array( $this,'add_per_product_commission_options' ), 15 );
        add_action( 'woocommerce_process_product_meta_simple', array( $this,'save_per_product_commission_options' ), 15 );
        add_action( 'woocommerce_process_product_meta_variable', array( $this,'save_per_product_commission_options' ), 15 );
        add_action( 'dokan_new_product_added', array( $this, 'set_product_tags' ), 10, 2 );
        add_action( 'dokan_product_updated', array( $this, 'set_product_tags' ) );
        add_action( 'dokan_product_updated', array( $this, 'set_product_type' ), 11 );
        add_action( 'dokan_product_updated', array( $this, 'save_pro_product_data' ), 12 );
        add_action( 'dokan_product_updated', array( $this, 'updated_product_email' ), 20 );
        add_action( 'template_redirect', array( $this, 'handle_duplicate_product' ), 10 );
        add_action( 'dokan_product_dashboard_errors', array( $this, 'display_duplicate_message' ), 10 );
        add_action( 'dokan_product_list_table_after_row', array( $this, 'add_product_inline_edit_form' ), 10, 2 );
        add_action( 'wp_ajax_dokan_product_inline_edit', array( $this, 'product_inline_edit' ) );

        add_filter( 'dokan_product_row_actions', array( $this, 'product_row_action' ), 10, 2 );
        add_filter( 'dokan_update_product_post_data', array( $this, 'save_product_post_data' ), 10 );
        add_filter( 'dokan_product_types', array( $this, 'set_default_product_types' ), 10 );

        add_action( 'dokan_after_linked_product_fields', array( $this, 'group_product_content' ), 10, 2 );
        add_filter( 'woocommerce_duplicate_product_exclude_meta', array( $this, 'remove_unwanted_meta' ) );

        add_filter( 'dokan_localized_args', array( $this, 'dokan_pro_localized_args' ) );
    }

    /**
     * Inistantiate the Dokan_Pro_Products class
     *
     * @since 2.4
     *
     * @return object
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Products();
        }

        return $instance;
    }

    /**
     * Render New Product Template
     *
     * @since 2.4
     *
     * @param  array $query_vars
     *
     * @return void
     */
    public function render_new_product_template( $query_vars ) {
        if ( isset( $query_vars['new-product'] ) ) {
            dokan_get_template_part( 'products/new-product' );
        }
    }

    /**
     * Load Variation Content
     *
     * @since 2.4
     *
     * @param  object $post
     * @param  integer $post_id
     *
     * @return void
     */
    public function load_variations_content( $post, $post_id ) {
        $_has_attribute       = get_post_meta( $post_id, '_has_attribute', true );
        $_create_variations   = get_post_meta( $post_id, '_create_variation', true );
        $product_attributes   = get_post_meta( $post_id, '_product_attributes', true );
        $attribute_taxonomies = wc_get_attribute_taxonomies();

        dokan_get_template_part( 'products/product-variation', '', array(
            'pro'                  => true,
            'post_id'              => $post_id,
            '_has_attribute'       => $_has_attribute,
            '_create_variations'   => $_create_variations,
            'product_attributes'   => $product_attributes,
            'attribute_taxonomies' => $attribute_taxonomies,
        ) );
    }

    /**
    * Render product lot dicount options
    *
    * @since 2.6
    *
    * @return void
    **/
    public function load_lot_discount_content( $post, $post_id ) {
        $_is_lot_discount       = get_post_meta( $post_id, '_is_lot_discount', true );
        $_lot_discount_quantity = get_post_meta( $post_id, '_lot_discount_quantity', true );
        $_lot_discount_amount   = get_post_meta( $post_id, '_lot_discount_amount', true );
        $is_enable_op_discount  = dokan_get_option( 'discount_edit', 'dokan_selling' );
        $is_enable_op_discount  = $is_enable_op_discount ? $is_enable_op_discount : array();

        dokan_get_template_part( 'products/product-lot-discount', '', array(
            'pro'                    => true,
            'post_id'                => $post_id,
            '_is_lot_discount'       => $_is_lot_discount,
            '_lot_discount_quantity' => $_lot_discount_quantity,
            '_lot_discount_amount'   => $_lot_discount_amount,
            'is_enable_op_discount'  => $is_enable_op_discount,
        ) );

    }


    /**
     * Load Variation popup content when edit product
     *
     * @since 2.4
     *
     * @param  object $post
     * @param  integer $post_id
     *
     * @return void
     */
    public function load_variations_js_template( $post, $post_id ) {
        dokan_get_template_part( 'products/edit/tmpl-add-attribute', '', array( 'pro' => true, 'post_id' => $post_id ) );
    }

    /**
     * Load Shipping and tax content
     *
     * @since 2.4
     *
     * @param  object $post
     * @param  integer $post_id
     *
     * @return void
     */
    public function load_shipping_tax_content( $post, $post_id ) {

        $user_id                 = dokan_get_current_user_id();
        $processing_time         = dokan_get_shipping_processing_times();
        $_required_tax           = get_post_meta( $post_id, '_required_tax', true );
        $_disable_shipping       = ( get_post_meta( $post_id, '_disable_shipping', true ) ) ? get_post_meta( $post_id, '_disable_shipping', true ) : 'no';
        $_additional_price       = get_post_meta( $post_id, '_additional_price', true );
        $_additional_qty         = get_post_meta( $post_id, '_additional_qty', true );
        $_processing_time        = get_post_meta( $post_id, '_dps_processing_time', true );
        $dps_shipping_type_price = get_user_meta( $user_id, '_dps_shipping_type_price', true );
        $dps_additional_qty      = get_user_meta( $user_id, '_dps_additional_qty', true );
        $dps_pt                  = get_user_meta( $user_id, '_dps_pt', true );
        $classes_options         = $this->get_tax_class_option();
        $porduct_shipping_pt     = ( $_processing_time ) ? $_processing_time : $dps_pt;

        dokan_get_template_part( 'products/product-shipping-content', '', array(
            'pro'                     => true,
            'post'                    => $post,
            'post_id'                 => $post_id,
            'user_id'                 => $user_id,
            'processing_time'         => $processing_time,
            '_required_tax'           => $_required_tax,
            '_disable_shipping'       => $_disable_shipping,
            '_additional_price'       => $_additional_price,
            '_additional_qty'         => $_additional_qty,
            '_processing_time'        => $_processing_time,
            'dps_shipping_type_price' => $dps_shipping_type_price,
            'dps_additional_qty'      => $dps_additional_qty,
            'dps_pt'                  => $dps_pt,
            'classes_options'         => $classes_options,
            'porduct_shipping_pt'     => $porduct_shipping_pt,
        ) );
    }

    /**
    * Render linked product content
    *
    * @since 2.6.6
    *
    * @return void
    **/
    public function load_linked_product_content( $post, $post_id ) {
        $upsells_ids = get_post_meta( $post_id, '_upsell_ids', true );
        $crosssells_ids = get_post_meta( $post_id, '_crosssell_ids', true );

        dokan_get_template_part( 'products/linked-product-content', '', array(
            'pro'            => true,
            'post'           => $post,
            'post_id'        => $post_id,
            'upsells_ids'    => $upsells_ids,
            'crosssells_ids' => $crosssells_ids
        ) );
    }

    /**
     * Get taxes options value
     *
     * @since 2.4
     *
     * @return array
     */
    function get_tax_class_option() {
        $tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );
        $classes_options = array();
        $classes_options[''] = __( 'Standard', 'dokan' );

        if ( $tax_classes ) {

            foreach ( $tax_classes as $class ) {
                $classes_options[ sanitize_title( $class ) ] = esc_html( $class );
            }
        }

        return $classes_options;
    }

    /**
     * add per product commission options
     *
     * @since 2.4.12
     *
     * @return void
     */
    function add_per_product_commission_options() {

        if ( !current_user_can( 'manage_woocommerce' ) ) {
            return;
        }
        woocommerce_wp_select( array(
            'id'            => '_per_product_admin_commission_type',
            'label'         => __( 'Admin Commission type', 'dokan' ),
            'options'       => array(
                'percentage'  => __( 'Percentage', 'dokan' ),
                'flat'        => __( 'Flat', 'dokan' ),
            ),
            'wrapper_class' => 'per-product-commission-type show_if_simple show_if_variable show_if_booking',
            'description'   => __( 'Set the commission type admin will get from this product', 'dokan' ),
            'data_type'     => 'price'
        ) );
        woocommerce_wp_text_input(
            array(
                'id'            => '_per_product_admin_commission',
                'label'         => __( 'Admin Commission', 'dokan' ),
                'wrapper_class' => 'per-product-commission show_if_simple show_if_variable show_if_booking',
                'description'   => __( 'Override the default commission admin will get from this product', 'dokan' ),
                'data_type'     => 'price'
            )
        );
    }

    /**
     * save per product commission options
     *
     * @since 2.4.12
     *
     * @param  integer $post_id
     *
     * @return void
     */
    public static function save_per_product_commission_options( $post_id ) {

        if ( !current_user_can( 'manage_woocommerce' ) ) {
            return;
        }
        if ( isset( $_POST['_per_product_admin_commission_type'] ) ) {
            $value = empty( $_POST['_per_product_admin_commission_type'] ) ? 'percentage' : $_POST['_per_product_admin_commission_type'];
            update_post_meta( $post_id, '_per_product_admin_commission_type', $value );
        }
        if ( isset( $_POST['_per_product_admin_commission'] ) ) {
            $value = '' === $_POST['_per_product_admin_commission'] ? '' : (float) $_POST['_per_product_admin_commission'];
            update_post_meta( $post_id, '_per_product_admin_commission', $value );
        }
    }

    /**
     * Save extra product data
     *
     * @since  2.5.3
     *
     * @param  integer $post_id
     *
     * @return void
     */
    public function save_pro_product_data( $post_id ) {
        if ( ! $post_id ) {
            return;
        }

        $is_virtual   = isset( $_POST['_virtual'] ) ? 'yes' : 'no';
        $product_type = empty( $_POST['product_type'] ) ? 'simple' : stripslashes( $_POST['product_type'] );

        // Save lot discount options
        $is_lot_discount = isset( $_POST['_is_lot_discount'] ) ? $_POST['_is_lot_discount'] : 'no';
        if ( $is_lot_discount == 'yes' ) {
            $lot_discount_quantity = isset( $_POST['_lot_discount_quantity'] ) ? $_POST['_lot_discount_quantity'] : 0;
            $lot_discount_amount   = isset( $_POST['_lot_discount_amount'] ) ? $_POST['_lot_discount_amount'] : 0;
            if ( $lot_discount_quantity == '0' || $lot_discount_amount == '0' ) {
                update_post_meta( $post_id, '_lot_discount_quantity', $lot_discount_quantity );
                update_post_meta( $post_id, '_lot_discount_amount', $lot_discount_amount );
                update_post_meta( $post_id, '_is_lot_discount', 'no' );
            } else {
                update_post_meta( $post_id, '_lot_discount_quantity', $lot_discount_quantity );
                update_post_meta( $post_id, '_lot_discount_amount', $lot_discount_amount );
                update_post_meta( $post_id, '_is_lot_discount', $is_lot_discount );
            }
        } else if ( $is_lot_discount == 'no' ) {
            update_post_meta( $post_id, '_lot_discount_quantity', 0 );
            update_post_meta( $post_id, '_lot_discount_amount', 0 );
            update_post_meta( $post_id, '_is_lot_discount', 'no' );
        }

        // Dimensions
        if ( 'no' == $is_virtual ) {

            if ( isset( $_POST['_weight'] ) ) {
                update_post_meta( $post_id, '_weight', ( '' === $_POST['_weight'] ) ? '' : wc_format_decimal( $_POST['_weight'] )  );
            }

            if ( isset( $_POST['_length'] ) ) {
                update_post_meta( $post_id, '_length', ( '' === $_POST['_length'] ) ? '' : wc_format_decimal( $_POST['_length'] )  );
            }

            if ( isset( $_POST['_width'] ) ) {
                update_post_meta( $post_id, '_width', ( '' === $_POST['_width'] ) ? '' : wc_format_decimal( $_POST['_width'] )  );
            }

            if ( isset( $_POST['_height'] ) ) {
                update_post_meta( $post_id, '_height', ( '' === $_POST['_height'] ) ? '' : wc_format_decimal( $_POST['_height'] )  );
            }
        } else {
            update_post_meta( $post_id, '_weight', '' );
            update_post_meta( $post_id, '_length', '' );
            update_post_meta( $post_id, '_width', '' );
            update_post_meta( $post_id, '_height', '' );
        }

        //Save shipping meta data
        update_post_meta( $post_id, '_disable_shipping', stripslashes( isset( $_POST['_disable_shipping'] ) ? $_POST['_disable_shipping'] : 'no' ) );

        if ( isset( $_POST['_overwrite_shipping'] ) && $_POST['_overwrite_shipping'] == 'yes' ) {
            update_post_meta( $post_id, '_overwrite_shipping', stripslashes( $_POST['_overwrite_shipping'] ) );
        } else {
            update_post_meta( $post_id, '_overwrite_shipping', 'no' );
        }

        update_post_meta( $post_id, '_additional_price', stripslashes( isset( $_POST['_additional_price'] ) ? $_POST['_additional_price'] : ''  ) );
        update_post_meta( $post_id, '_additional_qty', stripslashes( isset( $_POST['_additional_qty'] ) ? $_POST['_additional_qty'] : ''  ) );
        update_post_meta( $post_id, '_dps_processing_time', stripslashes( isset( $_POST['_dps_processing_time'] ) ? $_POST['_dps_processing_time'] : ''  ) );

        // Save shipping class
        $product_shipping_class = ( isset( $_POST['product_shipping_class'] ) && $_POST['product_shipping_class'] > 0 && 'external' !== $product_type ) ? absint( $_POST['product_shipping_class'] ) : '';
        wp_set_object_terms( $post_id, $product_shipping_class, 'product_shipping_class' );

        // Cross sells and upsells
        $upsells    = isset( $_POST['upsell_ids'] ) ? array_map( 'intval', $_POST['upsell_ids'] ) : array();
        $crosssells = isset( $_POST['crosssell_ids'] ) ? array_map( 'intval', $_POST['crosssell_ids'] ) : array();

        update_post_meta( $post_id, '_upsell_ids', $upsells );
        update_post_meta( $post_id, '_crosssell_ids', $crosssells );

        // Save variations
        if ( 'variable' == $product_type ) {
            dokan_save_variations( $post_id );
        }

        if ( 'grouped' == $product_type && version_compare( WC_VERSION, '2.7', '>' ) ) {
            $product = wc_get_product( $post_id );
            $goroup_product_ids = isset( $_POST['grouped_products'] ) ? array_filter( array_map( 'intval', (array) $_POST['grouped_products'] ) ) : array();
            $product->set_props( array( 'children' => $goroup_product_ids ) );
            $product->save();
        }
    }

    /**
     * Set new product tags
     *
     * @since 2.8.4
     *
     * @param int   $product_id
     * @param array $posted_data
     *
     * @return void
     */
    public function set_product_tags( $product_id, $posted_data = [] ) {
        if ( empty( $posted_data ) && ! empty( $_POST ) ) {
            $posted_data = $_POST;
        }

        if ( ! isset( $posted_data['product_tag'] ) || ! is_array( $posted_data['product_tag'] ) ) {
            return;
        }

        // Newly added tags will be string typed data
        $tags = array_filter( $posted_data['product_tag'], function ( $tag ) {
            return ! absint( $tag );
        } );

        if ( ! empty( $tags ) ) {
            $tags_ids = array();

            foreach ( $tags as $tag ) {
                $new_tag = wp_insert_term( $tag, 'product_tag' );

                if ( ! is_wp_error( $new_tag ) ) {
                    $tags_ids[] = $new_tag['term_id'];
                }
            }

            if ( ! empty( $tags_ids ) ) {
                wp_set_object_terms( $product_id, $tags_ids, 'product_tag', true );
            }
        }
    }

    /**
    * Added duplicate row action
    *
    * @since 2.6.3
    *
    * @return void
    **/
    public function product_row_action( $row_action, $post ) {
        if ( empty( $post->ID ) ) {
            return $row_action;
        }

        if ( current_user_can( 'dokan_edit_product' ) ) {
            $row_action['quick-edit'] = array(
                'title' => __( 'Quick Edit', 'dokan' ),
                'url'   => '#quick-edit',
                'class' => 'item-inline-edit',
                'other' => 'data-product-id="' . $post->ID . '"',
            );
        }

        $can_duplicate_product = current_user_can( 'dokan_duplicate_product' );
        $vendor_can_duplicate_product = dokan_get_option( 'vendor_duplicate_product', 'dokan_selling', 'on' );

        if ( $can_duplicate_product && 'on' === $vendor_can_duplicate_product ) {
            $row_action['duplicate'] = array(
                'title' => __( 'Duplicate', 'dokan' ),
                'url'   => wp_nonce_url( add_query_arg( array( 'action' => 'dokan-duplicate-product', 'product_id' => $post->ID ), dokan_get_navigation_url('products') ), 'dokan-duplicate-product' ),
                'class' => 'duplicate',
            );
        }

        return $row_action;
    }

    /**
    * Handle duplicate product action
    *
    * @since 2.6.3
    *
    * @return void
    **/
    public function handle_duplicate_product() {

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( dokan_get_option( 'vendor_duplicate_product', 'dokan_selling', 'on' ) == 'off' ) {
            return;
        }

        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        if ( ! apply_filters( 'dokan_vendor_can_duplicate_product', true ) ) {
            return;
        }

        if ( isset( $_GET['action'] ) && $_GET['action'] == 'dokan-duplicate-product' ) {
            $product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0;

            if ( !$product_id ) {
                wp_redirect( add_query_arg( array( 'message' => 'error' ), dokan_get_navigation_url( 'products' ) ) );
                return;
            }

            if ( !wp_verify_nonce( $_GET['_wpnonce'], 'dokan-duplicate-product' ) ) {
                wp_redirect( add_query_arg( array( 'message' => 'error' ), dokan_get_navigation_url( 'products' ) ) );
                return;
            }

            if ( !dokan_is_product_author( $product_id ) ) {
                wp_redirect( add_query_arg( array( 'message' => 'error' ), dokan_get_navigation_url( 'products' ) ) );
                return;
            }

            $wo_dup = new WC_Admin_Duplicate_Product();

            // Compatibility for WC 3.0+
            if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
                $product = wc_get_product( $product_id );
                $clone_product =  $wo_dup->product_duplicate( $product );
                $clone_product_id =  $clone_product->get_id();
            } else {
                $post = get_post( $product_id );
                $clone_product_id =  $wo_dup->duplicate_product( $post );
            }

            $product_status = dokan_get_new_post_status();
            wp_update_post( array( 'ID' => intval( $clone_product_id ), 'post_status' => $product_status ) );

            $redirect = apply_filters( 'dokan_redirect_after_product_duplicating', dokan_get_navigation_url( 'products' ), $product_id, $clone_product_id );
            wp_redirect( add_query_arg( array( 'message' => 'product_duplicated' ),  $redirect ) );
            exit;
        }
    }

    /**
    * Show duplicate success message
    *
    * @since 2.6.3
    *
    * @return void
    **/
    public function display_duplicate_message( $type ) {
        if ( 'product_duplicated' == $type ) {
            dokan_get_template_part( 'global/dokan-success', '', array( 'deleted' => true, 'message' => __( 'Product succesfully duplicated', 'dokan' ) ) );
        }
    }

    /**
     * Set product type
     *
     * @since 2.5.3
     *
     * @param integer $post_id
     */
    public function set_product_type( $post_id ) {
        if ( isset( $_POST['product_type'] ) ) {
            wp_set_object_terms( $post_id, $_POST['product_type'], 'product_type' );
        }
    }

    /**
     * Set Additional product Post Data
     *
     * @since 2.6.3
     *
     * @param Object $product
     *
     * @return $product
     */
    public function save_product_post_data( $product ) {
        //update product status to pending-review if set by admin
        if ( 'publish' !== $product['post_status'] || 'on' !== dokan_get_option( 'edited_product_status', 'dokan_selling' ) ) {
            return $product;
        }

        $vendor_id = dokan_get_current_user_id();
        // return early if vendor can publish product directly
        if ( 'yes' === get_user_meta( $vendor_id, 'dokan_publishing', true ) ) {
            return $product;
        }

        $product['post_status'] = 'pending';

        return $product;
    }

    /**
     * Set default product types
     *
     * @since 2.6
     *
     * @param array $product_types
     *
     * @return $product_types
     */
    function set_default_product_types( $product_types ) {

        $product_types = array(
            'simple' => __( 'Simple', 'dokan' ),
            'variable' => __( 'Variable', 'dokan' ),
        );

        if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
            $product_types['grouped'] = __( 'Group Product', 'dokan' );
        }

        return $product_types;
    }

    /**
     * Send email to admin once a product is updated
     *
     * @since 2.6.5
     *
     * @param int $product_id
     *
     * @param string $status
     */
    function updated_product_email( $product_id ) {

        if ( dokan_get_option( 'edited_product_status', 'dokan_selling', 'off' ) != 'on' ) {
            return;
        }

        $product       = wc_get_product( $product_id );
        $seller_id     = get_post_field( 'post_author', $product_id );
        $seller        = get_user_by( 'id', $seller_id );
        $category      = wp_get_post_terms( dokan_get_prop( $product, 'id' ), 'product_cat', array( 'fields' => 'names' ) );

        do_action( 'dokan_edited_product_pending_notification', $product, $seller, $category );
    }

    /**
    * Group product content
    *
    * @since 2.6.6
    *
    * @return void
    **/
    public function group_product_content( $post, $post_id ) {
        dokan_get_template_part( 'products/group-product', '', array(
            'pro'            => true,
            'post'           => $post,
            'post_id'        => $post_id,
            'product'        => wc_get_product( $post_id )
        ) );
    }

    /**
     * Remove unwanted meta_keys while duplicating product
     *
     * @param  array $meta_keys
     *
     * @since 2.7.6
     *
     * @return array $meta_keys
     */
    public function remove_unwanted_meta( $meta_keys ) {
        $meta_keys[] = 'pageview';

        return $meta_keys;
    }

    /**
     * Add Dokan Pro localized vars
     *
     * @since 2.8.4
     *
     * @param array $args
     *
     * @return array
     */
    public function dokan_pro_localized_args( $args ) {
        $dokan_pro_args = array(
            'product_vendors_can_create_tags' => dokan_get_option( 'product_vendors_can_create_tags', 'dokan_selling' ),
            'product_inline_edit_nonce'       => wp_create_nonce( 'product-inline-edit' ),
            'is_vendor_enabled'               => dokan_is_seller_enabled( dokan_get_current_user_id() ),
            'not_enable_message'              => __( 'Error! Your account is not enabled for selling, please contact the admin', 'dokan' ),
        );

        return array_merge( $args, $dokan_pro_args );
    }

    /**
     * Inline edit form
     *
     * @since 2.9.0
     *
     * @param WC_Product $product
     * @param WP_Post    $post
     *
     * @return void
     */
    public function add_product_inline_edit_form( $product, $post ) {
        $options = $this->get_inline_edit_options();

        $wp_cats = get_the_terms( $post, 'product_cat' );
        $cats    = wp_list_pluck( $wp_cats, 'term_id' );

        if ( $options['using_single_category_style'] && ! empty( $cats ) ) {
            $cats = array_pop( $cats );
        }

        $wp_tags = get_the_terms( $post, 'product_tag' );

        if ( $wp_tags ) {
            $tags = wp_list_pluck( $wp_tags, 'term_id' );
        } else {
            $tags = array();
        }

        $args = array(
            'pro'     => true,
            'id'      => 'dokan-product-list-table',
            'options' => $this->get_inline_edit_options(),

            // product informations
            'product_id'        => $product->get_id(),
            'post_title'        => $product->get_title(),
            'product_cat'       => $cats,
            'product_tag'       => $tags,
            'product_type'      => $product->get_type(),
            'is_virtual'        => $product->is_virtual(),
            'reviews_allowed'   => $product->get_reviews_allowed(),
            'post_status'       => $post->post_status,
            'sku'               => $product->get_sku(),
            '_regular_price'    => $product->get_regular_price(),
            '_sale_price'       => $product->get_sale_price(),
            'weight'            => $product->get_weight(),
            'length'            => $product->get_length(),
            'width'             => $product->get_width(),
            'height'            => $product->get_height(),
            'shipping_class_id' => $product->get_shipping_class_id(),
            '_visibility'       => ( version_compare( WC_VERSION, '2.7', '>' ) ) ? $product->get_catalog_visibility() : get_post_meta( $post->ID, '_visibility', true ),
            'manage_stock'      => $product->get_manage_stock(),
            'stock_quantity'    => $product->get_stock_quantity(),
            'stock_status'      => $product->get_stock_status(),
            'backorders'        => $product->get_backorders(),
        );

        dokan_get_template_part( 'products/edit/product-list-table-inline-edit-form', '', $args );
    }

    /**
     * Reusable inline edit options
     *
     * @since 2.9.0
     *
     * @return array
     */
    private function get_inline_edit_options() {
        if ( ! empty( $this->inline_edit_options ) ) {
            return $this->inline_edit_options;
        }

        $args = array(
            'taxonomy'   => 'product_cat',
            'number'     => false,
            'orderby'    => 'name',
            'order'      => 'asc',
            'hide_empty' => false,
        );

        $categories = get_terms( $args );

        $using_single_category_style = ( 'single' === dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) ) || false;

        $args = array(
            'taxonomy'   => 'product_tag',
            'number'     => false,
            'orderby'    => 'name',
            'order'      => 'asc',
            'hide_empty' => false,
        );

        $tags = get_terms( $args );

        $this->inline_edit_options = array(
            'using_single_category_style' => $using_single_category_style,
            'categories' => $categories,
            'tags'  => $tags,
            'post_statuses' => array(
                'publish' => __( 'Online', 'dokan' ),
                'draft'   => __( 'Draft', 'dokan' ),
            ),
            'is_sku_enabled' => wc_product_sku_enabled(),
            'is_weight_enabled' => wc_product_weight_enabled(),
            'is_dimensions_enabled' => wc_product_dimensions_enabled(),
            'shipping_classes' => WC()->shipping->get_shipping_classes(),
            'visibilities' => dokan_get_product_visibility_options(),
            'can_manage_stock' => get_option( 'woocommerce_manage_stock' ),
            'stock_statuses' => array(
                'instock'    => __( 'In Stock', 'dokan' ),
                'outofstock' => __( 'Out of Stock', 'dokan' ),
            ),
            'backorder_options' => array(
                'no'     => __( 'Do not allow', 'dokan' ),
                'notify' => __( 'Allow but notify customer', 'dokan' ),
                'yes'    => __( 'Allow', 'dokan' ),
            ),
        );

        return $this->inline_edit_options;
    }

    /**
     * Save quick edit product data
     *
     * @since 2.9.0
     *
     * @return void
     */
    public function product_inline_edit() {
        if ( ! wp_verify_nonce( $_POST['security'], 'product-inline-edit' ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'dokan' ) );
        }

        if ( empty( $_POST['data'] ) ) {
            wp_send_json_error( __( 'data is empty' ), 422 );
        }

        $posted_data = $_POST['data'];

        if ( empty( $posted_data['ID'] ) ) {
            wp_send_json_error( __( 'Product ID field is required', 'dokan' ), 422 );
        }

        $args = array(
            'ID'             => 0,
            'post_title'     => '',
            'post_status'    => '',
            'product_cat'    => array(),
            'product_tag'    => array(),
            'product_type'   => 'simple',
            '_regular_price' => '',
            '_sale_price'    => '',
            '_visibility'    => 'visible',
        );

        $data = array();

        foreach ( $args as $field => $default_val ) {
            $data[ $field ] = isset( $posted_data[ $field ] ) ? $posted_data[ $field ] : $default_val;
        }

        // save post content & excerpt; (ei: see `dokan_save_product` function)
        $saved_post           = get_post( $data['ID'] );
        $data['post_content'] = $saved_post ? $saved_post->post_content : '';
        $data['post_excerpt'] = $saved_post ? $saved_post->post_excerpt : '';
        $data['post_status']  = $saved_post && 'pending' === $saved_post->post_status ? 'pending' : $data['post_status'];

        $data = apply_filters( 'dokan_update_product_post_data', $data );

        $product_id = dokan_save_product( $data );

        if ( is_wp_error( $product_id ) ) {
            wp_send_json_error( __( 'Error updating product data', 'dokan' ), 422 );
        }

        $product = wc_get_product( $product_id );

        $product_props = array(
            'reviews_allowed',
            'sku',
            'weight',
            'length',
            'width',
            'height',
            'shipping_class_id',
            'manage_stock',
            'stock_quantity',
            'stock_status',
            'backorders',
        );

        foreach ( $product_props as $prop ) {
            $method_name = "set_{$prop}";

            if ( isset( $posted_data[ $prop ] ) && method_exists( $product, $method_name ) ) {
                $product->$method_name( $posted_data[ $prop ] );
            }
        }

        if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
            $product->set_catalog_visibility( $data['_visibility'] );
        }

        $product_id = $product->save();

        if ( is_wp_error( $product_id ) ) {
            wp_send_json_error( __( 'Error updating product data', 'dokan' ), 422 );
        }

        $post = get_post( $product_id );
        $row_actions = dokan_product_get_row_action( $post );
        $tr_class = ( $post->post_status == 'pending' ) ? 'danger' : '';

        $row_args = array(
            'post' => $post,
            'product' => $product,
            'tr_class' => $tr_class,
            'row_actions' => $row_actions,
        );

        ob_start();
        dokan_get_template_part( 'products/products-listing-row', '', $row_args );
        $html = ob_get_clean();

        $message = array(
            'message' => __( 'Product updated successfully', 'dokan' ),
            'row' => $html,
        );

        wp_send_json_success( $message );
    }
}
