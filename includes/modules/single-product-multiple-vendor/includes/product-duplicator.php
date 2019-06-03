<?php

use Dokan\Traits\Singleton;

/**
* Product related functionality
*
* @package Dokan
*/
class Dokan_SPMV_Product_Duplicator {

    use Singleton;

    const PUBLISHED_STATUS = 0;
    const TRASHED_STATUS   = 1;
    const PENDING_STATUS   = 2;
    const DRAFT_STATUS     = 3;

    protected $product_id = 0;
    protected $vendor_id  = 0;

    /**
     * Clone a product
     *
     * @since 2.9.8
     *
     * @return int|WP_Error
     */
    public function clone_product( $product_id, $vendor_id ) {
        $product_id = absint( $product_id );
        $vendor_id  = absint( $vendor_id );

        $this->product_id = $product_id;
        $this->vendor_id  = $vendor_id;

        $product = wc_get_product( $product_id );

        if ( ! $product instanceof WC_Product ) {
            return new WP_Error( 'product_not_found', __( 'Product not found.', 'dokan' ) );
        }

        $vendor = dokan()->vendor->get( $vendor_id );

        if ( ! $vendor->get_id() ) {
            return new WP_Error( 'vendor_not_found', __( 'Vendor not found.', 'dokan' ) );
        }

        if ( ! $this->is_valid_user( $product_id, $vendor_id ) ) {
            return new WP_Error( 'invalid_vendor', __( 'Vendor is not not eligible to clone this product.', 'dokan' ) );
        }

        $wc_duplicator      = new WC_Admin_Duplicate_Product();
        $update_product_ids = [];

        // Compatibility for WC 3.0+
        if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
            // For latest version 3.0+
            add_filter( 'woocommerce_new_product_data', [ $this, 'set_product_author' ] );

            $product           =  wc_get_product( $product_id );
            $clone_product     =  $wc_duplicator->product_duplicate( $product );
            $cloned_product_id =  $clone_product->get_id();

            remove_filter( 'woocommerce_new_product_data', [ $this, 'set_product_author' ] );
        } else {
            // For older version < 3.0+
            $post              = get_post( $product_id );
            $product           = wc_get_product( $product_id );
            $cloned_product_id =  $wc_duplicator->wc_duplicator( $post );
        }

        $product_status  = apply_filters( 'dokan_cloned_product_status', dokan_get_new_post_status() );
        $has_multivendor = get_post_meta( $product_id, '_has_multi_vendor', true );

        if ( ! $has_multivendor ) {
            $has_multivendor      = $this->get_next_map_id();
            $update_product_ids[] = $product_id;
        }

        $update_product_ids[] = $cloned_product_id;

        if ( $this->set_map_id( $has_multivendor, $update_product_ids ) ){
            update_post_meta( $product_id, '_has_multi_vendor', $has_multivendor );
            update_post_meta( $cloned_product_id, '_has_multi_vendor', $has_multivendor );
        }

        wp_update_post( [
            'ID'          => absint( $cloned_product_id ),
            'post_title'  => $product->get_title(),
            'post_status' => $product_status,
            'post_author' => $vendor_id
        ] );

        $this->update_product_visibility( $cloned_product_id, $product_status );

        do_action( 'dokan_spmv_create_clone_product', $cloned_product_id, $product_id );

        return $cloned_product_id;
    }

    /**
     * Set product author
     *
     * @since 2.9.8
     *
     * @param array $args
     */
    public function set_product_author( $args ) {
        $args['post_author'] = $this->vendor_id;

        return $args;
    }

    /**
     * Check is seller is elligible for sell this item
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function is_valid_user( $product_id, $vendor_id ) {
        if ( ! dokan_is_user_seller( $vendor_id ) ) {
            return false;
        }

        $product_author = get_post_field( 'post_author', $product_id );

        if ( $vendor_id === absint( $product_author ) ) {
            return false;
        }

        $is_cloned = $this->check_already_cloned( $product_id, $vendor_id );

        if ( $is_cloned ) {
            return false;
        }

        return true;
    }

    /**
     * Check already cloned this product
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function check_already_cloned( $product_id, $vendor_id ) {
        global $wpdb;

        $map_id = get_post_meta( $product_id, '_has_multi_vendor', true );

        if ( empty( $map_id ) ) {
            return false;
        }

        $sql     = "SELECT * FROM `{$wpdb->prefix}dokan_product_map` WHERE `map_id`= '$map_id' AND `seller_id` = '$vendor_id' AND `is_trash` IN (0,2,3)";
        $results = $wpdb->get_row( $sql );

        if ( $results ) {
            return true;
        }

        return false;
    }

    /**
     * Get mapping ID for next execution
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function get_next_map_id() {
        global $wpdb;

        $sql = "SELECT MAX(`map_id`) as max_id FROM `{$wpdb->prefix}dokan_product_map`";
        $current_id = $wpdb->get_var( $sql );

        if ( ! $current_id ) {
            return 1;
        }

        return $current_id + 1;
    }

    /**
     * Set mapping ids for product
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function set_map_id( $map_id, $product_ids ) {
        global $wpdb;

        $values = array();
        foreach ( $product_ids as $product_id ) {
            $seller_id = get_post_field( 'post_author', $product_id );
            $values[] = '(' . $map_id . ',' . $product_id . ',' . $seller_id . ')';
        }

        $values = implode( ',', $values );

        $result = $wpdb->query( "INSERT INTO `{$wpdb->prefix}dokan_product_map`
            ( map_id, product_id, seller_id )
            VALUES $values"
        );

        if ( $result ) {
            return true;
        }

        return false;
    }

    /**
     * Udpate product visibility in product mapping table
     *
     * @since 2.9.8
     *
     * @param integer $product_id
     * @param string $visibility [ 0 -> publish | 1 -> trash | 2 -> pending | 3 -> draft ]
     *
     * @return void
     */
    public function update_product_visibility( $product_id, $visibility ) {
        global $wpdb;

        $is_trash = $this->get_product_map_status_code( $visibility );

        $table = $wpdb->prefix . 'dokan_product_map';
        $wpdb->update(
            $table,
            [
                'is_trash'   => $is_trash
            ],
            [
                'product_id' => $product_id
            ],
            [ '%d' ],
            [ '%d' ]
        );
    }

    /**
     * Get mapping status
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function get_product_map_status_code( $status = '' ) {
        $statuses = [
            'publish' => self::PUBLISHED_STATUS,
            'trash'   => self::TRASHED_STATUS,
            'pending' => self::PENDING_STATUS,
            'draft'   => self::DRAFT_STATUS
        ];

        if ( ! empty( $status ) ) {
            return isset( $statuses[$status] ) ? $statuses[$status] : '';
        }

        return $statuses;
    }
}
