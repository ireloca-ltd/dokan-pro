<?php

class Dokan_SPMV_Products_Admin {

    /**
     * Class constructor
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ], 11 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_ajax_dokan_spmv_products_admin_search_vendors', [ $this, 'search_vendors' ] );
        add_action( 'wp_ajax_dokan_spmv_products_admin_assign_vendors', [ $this, 'assign_vendors' ] );
        add_action( 'wp_ajax_dokan_spmv_products_admin_delete_clone_product', [ $this, 'delete_clone_product' ] );
    }

    /**
     * Add metabox
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function add_meta_box() {
        remove_meta_box( 'sellerdiv', 'product', 'normal' );
        add_meta_box( 'dokansellerdiv', __( 'Vendor', 'dokan' ), [ $this, 'dokan_seller_meta_box' ], 'product', 'normal', 'core' );
    }

    /**
     * Metabox
     *
     * @since 2.9.8
     *
     * @param WP_Post $post
     *
     * @return void
     */
    public function dokan_seller_meta_box( $post ) {
        dokan_seller_meta_box( $post );

        if ( 'auto-draft' === $post->post_status ) {
            return;
        }

        $args = [
            'clones' => dokan_spmv_get_product_clones( $post->ID )
        ];

        dokan_spmv_get_template( 'dokan-spmv-products-admin', $args );
    }

    /**
     * Enqueue scripts in product editing admin page
     *
     * @since 2.9.8
     *
     * @param string $hook
     *
     * @return void
     */
    public function enqueue_scripts( $hook ) {
        global $post;

        if ( $hook == 'post-new.php' || $hook == 'post.php' ) {
            if ( 'product' === $post->post_type ) {
                wp_enqueue_style( 'dokan-spmv-products-admin', DOKAN_SPMV_ASSETS_DIR . '/css/dokan-spmv-products-admin.css', [], DOKAN_PRO_PLUGIN_VERSION );
                wp_enqueue_script( 'dokan-spmv-products-admin', DOKAN_SPMV_ASSETS_DIR . '/js/dokan-spmv-products-admin.js', [ 'jquery', 'dokan_pro_admin' ], DOKAN_PRO_PLUGIN_VERSION, true );
                add_filter( 'dokan_admin_localize_param', [ $this, 'add_localize_params' ]);
            }
        }
    }

    /**
     * Add additional localized vars
     *
     * @since 2.9.8
     *
     * @param array $params
     *
     * @return array
     */
    public function add_localize_params( $params ) {
        global $post;

        return array_merge( $params, [
            'dokanSPMVAdmin' => [
                'product_id' => $post->ID,
                'i18n'       => [
                    'error_loading'   => esc_html__( 'Could not find any vendor.', 'dokan' ),
                    'searching'       => esc_html__( 'Searching vendors', 'dokan' ),
                    'input_too_short' => esc_html__( 'Search vendors', 'dokan' ),
                    'confirm_delete'  => esc_html__( 'Are you sure you want to delete this product? This will permanently delete the product and you will not be able to recover this product.', 'dokan' ),
                ]
            ]
        ] );
    }

    /**
     * Ajax method to search vendors
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function search_vendors() {
        check_ajax_referer( 'dokan-admin-nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized operation', 'dokan' ) ], 403 );
        }

        $vendors = [];

        $post_data = wp_unslash( $_GET );

        if ( ! empty( $post_data['s'] ) && ! empty( $post_data['product_id'] ) ) {
            $s          = sanitize_text_field( $post_data['s'] );
            $product_id = absint( $post_data['product_id'] );

            $product_vendor = dokan_get_vendor_by_product( $product_id );

            $exlcude_vendors = [ $product_vendor->get_id() ];

            $clones = dokan_spmv_get_product_clones( $product_id );

            if ( ! empty( $clones ) ) {
                foreach ( $clones as $clone_id ) {
                    $vendor = dokan_get_vendor_by_product( $clone_id );
                    $exlcude_vendors[] = $vendor->get_id();
                }
            }

            $results = dokan()->vendor->all( [
                'search'  => '*' . $s . '*',
                'exclude' => $exlcude_vendors,
                'number'  => -1,
            ] );

            if ( ! empty( $results ) ) {
                foreach ( $results as $vendor ) {
                    $vendors[] = [
                        'id'     => $vendor->get_id(),
                        'name'   => $vendor->get_shop_name(),
                        'avatar' => $vendor->get_avatar(),
                    ];
                }
            }
        }

        wp_send_json_success( [
            'vendors' => $vendors
        ] );
    }

    /**
     * Clone products for vendors
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function assign_vendors() {
        check_ajax_referer( 'dokan-admin-nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized operation', 'dokan' ) ], 403 );
        }

        $post_data = wp_unslash( $_POST );

        if ( empty( $post_data['product_id'] ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'product_id is empty.', 'dokan' ) ], 400 );
        }

        if ( empty( $post_data['vendors'] ) || ! is_array( $post_data['vendors'] ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'vendors param is empty.', 'dokan' ) ], 400 );
        }

        $product = wc_get_product( $post_data['product_id'] );

        if ( ! $product->get_id() ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Product not found.', 'dokan' ) ], 400 );
        }

        $vendors = array_map( 'absint', $post_data['vendors'] );

        $duplicator = Dokan_SPMV_Product_Duplicator::instance();

        foreach ( $vendors as $vendor_id ) {
            $id = $duplicator->clone_product( $product->get_id(), $vendor_id );

            if ( is_wp_error( $id ) ) {
                wp_send_json_error( [
                    'message' => sprintf( esc_html__( 'Unable to clone product for vendor id: %d', 'dokan' ), $vendor_id )
                ], 400 );
            }
        }

        wp_send_json_success( [ 'message' => esc_html__( 'Vendors assigned successfully', 'dokan' ) ] );
    }

    /**
     * Delete cloned product
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function delete_clone_product() {
        check_ajax_referer( 'dokan-admin-nonce' );

        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized operation.', 'dokan' ) ], 403 );
        }

        $post_data = wp_unslash( $_POST );

        if ( empty( $post_data['product_id'] ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'product_id is empty.', 'dokan' ) ], 400 );
        }

        $product = wc_get_product( $post_data['product_id'] );

        if ( empty( $product ) ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Product not found', 'dokan' ) ], 400 );
        }

        $product = wp_delete_post( $product->get_id(), true );

        if ( ! $product ) {
            wp_send_json_error( [ 'message' => esc_html__( 'Could not delete product.', 'dokan' ) ], 400 );
        }

        wp_send_json_success( [ 'message' => esc_html__( 'Product deleted successfully.', 'dokan' ) ] );
    }
}
