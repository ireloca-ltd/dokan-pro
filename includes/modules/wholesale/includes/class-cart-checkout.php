<?php

/**
* Cart and checkout handler Class
*
* @since 2.9.5
*/
class Dokan_Wholesale_Cart_Checkout {

    /**
     * Load automatically when class initiate
     *
     * @since 2.9.5
     */
    public function __construct() {
        add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'show_wholesale_price' ], 10 );
        add_filter( 'woocommerce_available_variation', [ $this, 'add_variation_data' ], 10, 3 );
        add_action( 'woocommerce_before_calculate_totals', [ $this, 'calculate_cart' ], 12, 1 );
        add_action( 'woocommerce_before_mini_cart' , [ $this , 'recalculate_cart_totals' ] );
        add_action( 'woocommerce_after_cart_item_name', [ $this, 'show_wholesale_info' ], 10, 2 );
        add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'order_item_meta' ], 10, 3 );
        add_filter( 'woocommerce_order_item_display_meta_key', [ $this, 'change_wholesale_item_meta_title' ], 20, 3 );
        add_action( 'woocommerce_get_price_html', [ $this, 'show_loop_wholesale_price' ], 11, 2 );
    }

    /**
     * Get formatter data for wholesale
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function change_wholesale_item_meta_title( $key, $meta, $item ) {
        if ( '_dokan_item_wholesale' === $meta->key ) {
            $key = __( 'Wholesale Item' , 'dokan' );
        }

        return $key;
    }

    /**
     * Add warranty data to all variations
     *
     * @param $data
     * @param $product
     * @param $variation
     *
     * @return mixed
     */
    function add_variation_data( $data, $product, $variation ) {
        $variation_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $variation->variation_id ) ) ? $variation->variation_id : $variation->get_id();
        $wholesale     = get_post_meta( $variation_id, '_dokan_wholesale_meta', true );

        $data['_enable_wholesale']   = ! empty( $wholesale['enable_wholesale'] ) ? $wholesale['enable_wholesale'] : 'no';
        $data['_wholesale_price']    = ! empty( $wholesale['price'] ) ? $wholesale['price'] : '';
        $data['_wholesale_quantity'] = ! empty( $wholesale['quantity'] ) ? $wholesale['quantity'] : '';

        return $data;
    }

    /**
     * Show wholesale price
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function show_wholesale_price() {
        global $product;

        if ( ! dokan_wholesale_can_see_price() ) {
            return;
        }

        if ( ! $product->is_type( 'simple' ) ) {
            return;
        }

        $wholesale = get_post_meta( $product->get_id(), '_dokan_wholesale_meta', true );

        if ( ! isset( $wholesale['enable_wholesale'] ) ) {
            return;
        }

        if ( 'no' == $wholesale['enable_wholesale'] ) {
            return;
        }

        if ( empty( $wholesale['price'] ) ) {
            return;
        }

        dokan_get_template_part( 'wholesale/single-product', '', [
            'is_wholesale'       => true,
            'user_id'            => dokan_get_current_user_id(),
            'product'            => $product,
            'enable_wholesale'   => ! empty( $wholesale['enable_wholesale'] ) ? $wholesale['enable_wholesale'] : 'no',
            'wholesale_price'    => ! empty( $wholesale['price'] ) ? $wholesale['price'] : '',
            'wholesale_quantity' => ! empty( $wholesale['quantity'] ) ? $wholesale['quantity'] : ''
        ] );
    }

    /**
     * Calculate cart item for wholesales
     *
     * @since 2.9.5
     *
     * @return void
     */
    public function calculate_cart( $cart_obj ) {
        if ( ! dokan_wholesale_can_see_price() ) {
            return;
        }

        foreach ( $cart_obj->get_cart() as $cart_key => $cart ) {
            $product_id = ! empty( $cart['variation_id'] ) ? $cart['variation_id'] : $cart['product_id'];
            $wholesale  = get_post_meta( $product_id, '_dokan_wholesale_meta', true );

            WC()->cart->cart_contents[$cart_key]['wholesale'] = $wholesale;

            if ( ! isset( $wholesale['enable_wholesale'] ) ) {
                continue;
            }

            if ( 'no' == $wholesale['enable_wholesale'] ) {
                continue;
            }

            if ( empty( $wholesale['price'] ) ) {
                continue;
            }

            if ( $wholesale['quantity'] <= 0 ) {
                continue;
            }

            if (  $wholesale['quantity'] <= $cart['quantity'] ) {
                $cart['data']->set_price( $wholesale['price'] );
            }
        }
    }

    /**
     * Recalculate mini cart when applied wholesale
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function recalculate_cart_totals() {
        WC()->cart->calculate_totals();
    }

    /**
     * Display wholesale info
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function show_wholesale_info( $cart_item, $cart_item_key ) {
        if ( ! dokan_wholesale_can_see_price() ) {
            return;
        }

        if ( isset( $cart_item['wholesale'] ) ) {
            if ( isset( $cart_item['wholesale']['enable_wholesale'] ) && 'yes' == $cart_item['wholesale']['enable_wholesale'] ) {
                $remaining_qty = absint( $cart_item['wholesale']['quantity'] - $cart_item['quantity'] );
                if ( $remaining_qty > 0 ) {
                    echo '<br>';
                    echo sprintf( __( 'For wholesale price buy <strong>%d</strong> more units' ), $remaining_qty );
                }
            }
        }
    }

    /**
     * Order item meta added for wholesale product
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function order_item_meta( $item, $cart_item_key, $values ) {
        $cart_contents  = WC()->cart->get_cart();

        if ( ! dokan_wholesale_can_see_price() ) {
            return;
        }

        $cart_item = $cart_contents[$cart_item_key];

        if ( isset( $cart_item['wholesale'] ) ) {
            if ( isset( $cart_item['wholesale']['enable_wholesale'] ) && 'yes' == $cart_item['wholesale']['enable_wholesale'] ) {
                if ( $cart_item['wholesale']['quantity'] <= $cart_item['quantity'] ) {
                    $item_id = $item->save();
                    wc_add_order_item_meta( $item_id, '_dokan_item_wholesale', 'yes' );
                }
            }
        }
    }

    /**
     * Show wholesale price in shop loop
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function show_loop_wholesale_price( $price, $product ) {
        $is_diplay_shop_archive = dokan_get_option( 'display_price_in_shop_archieve', 'dokan_wholesale', 'no' );

        if ( 'off' == $is_diplay_shop_archive ) {
            return $price;
        }

        if ( is_shop() ) {
            if ( dokan_wholesale_can_see_price() ) {
                if ( $product->is_type( 'simple' ) ) {
                    $wholesale = get_post_meta( $product->get_id(), '_dokan_wholesale_meta', true );
                    if ( isset( $wholesale['enable_wholesale'] ) && 'yes' == $wholesale['enable_wholesale'] ) {
                        $wholesale_price = apply_filters( 'dokan_wholesale_price_loop', sprintf( '<span class="dokan-wholesale-price">( %s: %s )</span>', __( 'Wholesale', 'dokan'), wc_price( $wholesale['price'] ) ) );
                        return $price . $wholesale_price;
                    }
                }
            }
        }

        return $price;
    }
}
