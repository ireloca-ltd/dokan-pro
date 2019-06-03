<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Productpack Product Class
 *
 * The default product type kinda product.
 *
 * @class       WC_Product_Productpack
 * @version     2.0.0
 * @package     Dokan
 * @subpackage  Subscription
 */
class WC_Product_Product_pack extends WC_Product {

    /**
     * __construct function.
     *
     * @access public
     * @param mixed $product
     */
    public function __construct( $product ) {
        $this->product_type = 'product_pack';
        parent::__construct( $product );
    }

    public function managing_stock() {
        return false;
    }

    /**
     * Get the add to url used mainly in loops.
     *
     * @access public
     * @return string
     */
    public function add_to_cart_url() {
        $url = $this->is_purchasable() && $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->id ) ) : get_permalink( $this->id );

        return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
    }

}
