<?php

/**
* Vendor product releate functions
*
* @since 1.0.0
*
* @package dokan
*/
class Dokan_RMA_Product {

    use Dokan_RMA_Common;

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'dokan_product_edit_after_inventory_variants', [ $this, 'load_rma_content' ], 30, 2 );
        add_action( 'dokan_product_updated', [ $this, 'save_rma_data' ], 12 );
    }

    /**
    * Render product rma options
    *
    * @since 1.0.0
    *
    * @return void
    **/
    public function load_rma_content( $post, $post_id ) {
        $user_id          = dokan_get_current_user_id();
        $override_default = get_post_meta( $post_id, '_dokan_rma_override_product', true );
        $reasons          = dokan_rma_refund_reasons();
        $rma_settings     = $this->get_settings( $post_id );

        dokan_get_template_part( 'rma/product', '', array(
            'is_rma'           => true,
            'reasons'          => $reasons,
            'rma_settings'     => $rma_settings,
            'override_default' => $override_default
        ) );
    }

    /**
     * Save RMA data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function save_rma_data( $post_id ) {
        if ( ! $post_id ) {
            return;
        }

        if ( ! isset( $_POST['dokan_rma_product_override'] ) ) {
            return;
        }

        $override_default = $_POST['dokan_rma_product_override'];

        if ( ! empty( $_POST['dokan_rma_product_override'] ) ) {
            update_post_meta( $post_id, '_dokan_rma_override_product', $override_default );
        }

        if ( 'yes' == $override_default ) {
            $product_rma_settings = $this->transform_rma_settings( $_POST );
            update_post_meta( $post_id, '_dokan_rma_settings', $product_rma_settings );
        } else {
            delete_post_meta( $post_id, '_dokan_rma_settings' );
        }
    }

}
