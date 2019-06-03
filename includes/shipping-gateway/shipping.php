<?php

/**
 *  Dokan regular Shipping Class
 *
 *  Register WooCommerce gateway as
 *  Dokan Shipping
 *
 *  @author weDevs <info@wedevs.com>
 */
class Dokan_WC_Shipping extends WC_Shipping_Method {
    /**
     * Constructor for your shipping class
     *
     * @access public
     *
     * @return void
     */
    public function __construct() {
        $this->id                 = 'dokan_product_shipping';
        $this->method_title       = __( 'Dokan Shipping' );
        $this->method_description = __( 'Enable vendors to set shipping cost per product and per country' );

        $this->enabled      = $this->get_option( 'enabled' );
        $this->title        = $this->get_option( 'title' );
        $this->tax_status   = $this->get_option( 'tax_status' );

        $this->init();
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    function init() {
        // Load the settings API
        $this->init_form_fields();
        $this->init_settings();

        // Save settings in admin if you have any defined
        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Checking is gateway enabled or not
     *
     * @return boolean [description]
     */
    public function is_method_enabled() {
        return $this->enabled == 'yes';
    }

    /**
     * Initialise Gateway Settings Form Fields
     *
     * @access public
     * @return void
     */
    function init_form_fields() {

        $this->form_fields = array(
            'enabled' => array(
                'title'         => __( 'Enable/Disable', 'dokan' ),
                'type'          => 'checkbox',
                'label'         => __( 'Enable Shipping', 'dokan' ),
                'default'       => 'yes'
            ),
            'title' => array(
                'title'         => __( 'Method Title', 'dokan' ),
                'type'          => 'text',
                'description'   => __( 'This controls the title which the user sees during checkout.', 'dokan' ),
                'default'       => __( 'Regular Shipping', 'dokan' ),
                'desc_tip'      => true,
            ),
            'tax_status' => array(
                'title'         => __( 'Tax Status', 'dokan' ),
                'type'          => 'select',
                'default'       => 'taxable',
                'options'       => array(
                    'taxable'   => __( 'Taxable', 'dokan' ),
                    'none'      => _x( 'None', 'Tax status', 'dokan' )
                ),
            ),

        );
    }

    /**
     * calculate_shipping function.
     *
     * @access public
     *
     * @param mixed $package
     *
     * @return void
     */
    public function calculate_shipping( $package = array() ) {
        $products = $package['contents'];
        $destination_country = isset( $package['destination']['country'] ) ? $package['destination']['country'] : '';
        $destination_state = isset( $package['destination']['state'] ) ? $package['destination']['state'] : '';

        $amount = 0.0;

        if ( ! $this->is_method_enabled() ) {
            return;
        }

        if ( !self::is_shipping_enabled_for_seller( $package['seller_id'] ) ) {
            return;
        }

        if ( $products ) {
            $amount = $this->calculate_per_seller( $products, $destination_country, $destination_state );
        }

        $tax_rate = ( $this->tax_status == 'none' ) ? false : '';

        $rate = array(
            'id'    => $this->id,
            'label' => $this->title,
            'cost'  => $amount,
            'taxes' => $tax_rate
        );

        // Register the rate
        $this->add_rate( $rate );
    }

    /**
     * Check if shipping for this product is enabled
     *
     * @param integet $product_id
     *
     * @return boolean
     */
    public static function is_product_disable_shipping( $product_id ) {
        $enabled = get_post_meta( $product_id, '_disable_shipping', true );

        if ( $enabled == 'yes' ) {
            return true;
        }

        return false;
    }

    /**
     * Check if shipping for this product is enabled
     *
     * @param  integet  $product_id
     *
     * @return boolean
     */
    public static function is_shipping_enabled_for_seller( $seller_id ) {
        $enabled = get_user_meta( $seller_id, '_dps_shipping_enable', true );

        if ( $enabled == 'yes' ) {
            return true;
        }

        return false;
    }

    /**
     * Check if seller has any shipping enable product in this order
     *
     * @since  2.4.11
     *
     * @param  array $products
     *
     * @return boolean
     */
    public function has_shipping_enabled_product( $products ) {

        foreach ( $products as $product ) {
            if ( !self::is_product_disable_shipping( $product['product_id'] ) ) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get product shipping costs
     *
     * @param  integer $product_id
     *
     * @return array
     */
    public static function get_seller_country_shipping_costs( $seller_id ) {
        $country_cost = get_user_meta( $seller_id, '_dps_country_rates', true );
        $country_cost = is_array( $country_cost ) ? $country_cost : array();

        return $country_cost;
    }


    /**
     * Calculate shipping per seller
     *
     * @param  array $products
     * @param  array $destination
     *
     * @return float
     */
    public function calculate_per_seller( $products, $destination_country, $destination_state  ) {
        $amount = 0.0;
        $price = array();

        $seller_products = array();

        foreach ( $products as $product ) {
            $seller_id                     = get_post_field( 'post_author', $product['product_id'] );
            $seller_products[$seller_id][] = $product;
        }

        if ( $seller_products ) {

            foreach ( $seller_products as $seller_id => $products ) {

                if ( !self::is_shipping_enabled_for_seller( $seller_id ) ) {
                    continue;
                }

                if ( ! $this->has_shipping_enabled_product( $products ) ) {
                    continue;
                }

                $default_shipping_price     = get_user_meta( $seller_id, '_dps_shipping_type_price', true );
                $default_shipping_add_price = get_user_meta( $seller_id, '_dps_additional_product', true );

                $downloadable_count = 0;
                foreach ( $products as $product ) {

                    if ( self::is_product_disable_shipping( $product['product_id'] ) ) {
                        continue;
                    }

                    if ( isset( $product['variation_id'] ) ) {
                        $is_virtual      = get_post_meta( $product['variation_id'], '_virtual', true );
                        $is_downloadable = get_post_meta( $product['variation_id'], '_downloadable', true );
                    } else {
                        $is_virtual      = get_post_meta( $product['product_id'], '_virtual', true );
                        $is_downloadable = get_post_meta( $product['product_id'], '_downloadable', true );
                    }

                    if ( ( $is_virtual == 'yes' ) || ( $is_downloadable == 'yes' ) ) {
                        $downloadable_count++;
                        continue;
                    }

                    if ( get_post_meta( $product['product_id'], '_overwrite_shipping', true ) == 'yes' ) {
                        $default_shipping_qty_price = get_post_meta( $product['product_id'], '_additional_qty', true );
                        $price[ $seller_id ]['addition_price'][] = get_post_meta( $product['product_id'], '_additional_price', true );
                    } else {
                        $default_shipping_qty_price = get_user_meta( $seller_id, '_dps_additional_qty', true );
                        $price[ $seller_id ]['addition_price'][] = 0;
                    }

                    $price[ $seller_id ]['default'] = floatval( $default_shipping_price );

                    if ( $product['quantity'] > 1 ) {
                        $price[ $seller_id ]['qty'][] = ( ( $product['quantity'] - 1 ) * floatval( $default_shipping_qty_price ) );
                    } else {
                        $price[ $seller_id ]['qty'][] = 0;
                    }

                }

                if ( count( $products ) > 1 ) {
                    $price[ $seller_id ]['add_product'] =  floatval( $default_shipping_add_price ) * ( count( $products) - ( 1 + $downloadable_count ) );
                } else {
                    $price[ $seller_id ]['add_product'] = 0;
                }

                $dps_country_rates = get_user_meta( $seller_id, '_dps_country_rates', true );
                $dps_state_rates   = get_user_meta( $seller_id, '_dps_state_rates', true );

                if ( isset( $dps_state_rates[$destination_country] ) ) {

                    if ( array_key_exists( $destination_state, $dps_state_rates[$destination_country] ) ) {
                        if ( isset( $dps_state_rates[$destination_country][$destination_state] ) ) {
                            $price[$seller_id]['state_rates'] = floatval( $dps_state_rates[$destination_country][$destination_state] );
                        } else {
                            $price[$seller_id]['state_rates'] = ( isset( $dps_country_rates[$destination_country] ) ) ? floatval( $dps_country_rates[$destination_country] ) : 0;
                        }

                    } elseif ( array_key_exists( 'everywhere', $dps_state_rates[$destination_country] ) ) {
                        $price[$seller_id]['state_rates'] = ( isset( $dps_state_rates[$destination_country]['everywhere'] ) ) ? floatval( $dps_state_rates[$destination_country]['everywhere'] ) : 0;
                    } else {
                        $price[$seller_id]['state_rates'] = 0;
                    }

                } else {

                    if ( !array_key_exists( $destination_country, $dps_country_rates ) ) {
                        $price[$seller_id]['state_rates'] = isset( $dps_country_rates['everywhere'] ) ? floatval( $dps_country_rates['everywhere'] ) : 0;
                    } else {
                        $price[$seller_id]['state_rates'] = ( isset( $dps_country_rates[$destination_country] ) ) ? floatval( $dps_country_rates[$destination_country] ) : 0;
                    }
                }
            }
        }

        if ( !empty( $price ) ) {
            foreach ( $price as $s_id => $value ) {
                $amount = $amount + ( ( isset( $value['addition_price'] ) ? array_sum( $value['addition_price'] ) : 0 ) +  ( isset($value['default'] ) ? $value['default'] : 0 ) + ( isset( $value['qty'] ) ? array_sum( $value['qty'] ) : 0 ) + $value['add_product'] + ( isset( $value['state_rates'] ) ? $value['state_rates'] : 0 ) );
            }
        }

        return apply_filters( 'dokan_shipping_calculate_amount', $amount, $price, $products, $destination_country, $destination_state );
    }
}




