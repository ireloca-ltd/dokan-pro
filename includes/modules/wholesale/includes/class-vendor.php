<?php

/**
* Vendor related functionality
*
* @since 2.9.5
*/
class Dokan_Wholesale_Vendor {

    /**
     * Load automatically when class initiate
     *
     * @since 2.9.5
     */
    public function __construct() {
        add_action( 'dokan_product_edit_after_inventory_variants', [ $this, 'load_wholesale_content' ], 30, 2 );
        add_action( 'dokan_product_after_variable_attributes', [ $this, 'load_wholesale_content_variation' ], 30, 3 );

        add_action( 'dokan_product_updated', [ $this, 'save_wholesale_data' ], 12 );
        add_action( 'dokan_process_product_meta', [ $this, 'save_variation_wholesale_data' ], 12 );
        add_action( 'dokan_ajax_save_product_variations', [ $this, 'save_variation_wholesale_data' ], 12 );
    }

    /**
    * Render product rma options
    *
    * @since 2.9.5
    *
    * @return void
    **/
    public function load_wholesale_content( $post, $post_id ) {
        $user_id            = dokan_get_current_user_id();
        $wholesale          = get_post_meta( $post_id, '_dokan_wholesale_meta', true );

        dokan_get_template_part( 'wholesale/product', '', [
            'is_wholesale'       => true,
            'user_id'            => $user_id,
            'enable_wholesale'   => ! empty( $wholesale['enable_wholesale'] ) ? $wholesale['enable_wholesale'] : 'no',
            'wholesale_price'    => ! empty( $wholesale['price'] ) ? $wholesale['price'] : '',
            'wholesale_quantity' => ! empty( $wholesale['quantity'] ) ? $wholesale['quantity'] : ''
        ] );
    }

    /**
     * Show wholesale settings in variations
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function load_wholesale_content_variation( $loop, $variation_data, $variation ) {
        $user_id            = dokan_get_current_user_id();
        $wholesale_data     = get_post_meta( $variation->ID, '_dokan_wholesale_meta', true );

        dokan_get_template_part( 'wholesale/product-variation', '', [
            'is_wholesale'     => true,
            'user_id'          => $user_id,
            'loop'             => $loop,
            'enable_wholesale' => ! empty( $wholesale_data['enable_wholesale'] ) ? $wholesale_data['enable_wholesale'] : 'no',
            'price'            => ! empty( $wholesale_data['price'] ) ? $wholesale_data['price'] : '',
            'quantity'         => ! empty( $wholesale_data['quantity'] ) ? $wholesale_data['quantity'] : ''
        ] );
    }

    /**
     * Save RMA data
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function save_wholesale_data( $post_id ) {
        if ( ! $post_id ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! isset( $_POST['wholesale'] ) ) {
            return;
        }

        update_post_meta( $post_id, '_dokan_wholesale_meta', $_POST['wholesale'] );
    }

    /**
     * Save wholesale variation data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function save_variation_wholesale_data( $post_id ) {
        if ( ! $post_id ) {
            return;
        }

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! isset( $_POST['variable_wholesale_enable'] ) ) {
            return;
        }

        $data = [];
        foreach ( $_POST['variable_wholesale_price'] as $loop => $price ) {
            $data[$loop] = [
                'enable_wholesale' => ! empty( $_POST['variable_wholesale_enable'][$loop] ) ? sanitize_text_field( $_POST['variable_wholesale_enable'][$loop] ) : 'no',
                'price'            => $price,
                'quantity'         => ! empty( $_POST['variable_wholesale_quantity'][$loop] ) ? sanitize_text_field( $_POST['variable_wholesale_quantity'][$loop] ) : 0
            ];
        }

        foreach ( $data as $key => $wholesale_data ) {
            update_post_meta( $_POST['variable_post_id'][$key], '_dokan_wholesale_meta', $wholesale_data );
        }
    }

}
