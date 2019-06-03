<?php

/**
* Frontend product and cart management
*/
class Dokan_RMA_Frontend {

    use Dokan_RMA_Common;

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'show_product_warranty' ] );
        add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 10, 2 );
        add_filter( 'woocommerce_add_cart_item', [ $this, 'add_cart_item' ], 10, 1 );
        add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_cart_validation' ], 10, 2 );

        add_filter( 'woocommerce_get_cart_item_from_session', [ $this, 'get_cart_item_from_session' ], 10, 2 );
        add_filter( 'woocommerce_get_item_data', [ $this, 'get_item_data' ], 10, 2 );
        add_action( 'woocommerce_add_to_cart', [ $this, 'add_warranty_index' ], 10, 6 );

        add_filter( 'add_to_cart_text', [ $this, 'add_to_cart_text' ], 15 );
        add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'add_to_cart_text' ], 15, 2 );

        add_action( 'template_redirect', [ $this, 'handle_warranty_submit_request' ], 10 );
        add_action( 'template_redirect', [ $this, 'handle_warranty_conversation' ], 10 );
    }

    /**
     * Show a product's warranty information
     *
     * @since 1.0.0
     */
    function show_product_warranty() {
        global $post, $product;

        if ( $product->is_type( 'external' ) ) {
            return;
        }

        $product_id     = $product->get_id();
        $warranty       = $this->get_settings( $product_id );
        $warranty_label = $warranty['label'];

        if ( $warranty['type'] == 'included_warranty' ) {
            if ( $warranty['length'] == 'limited' ) {
                $value      = $warranty['length_value'];
                $duration   = dokan_rma_get_duration_value( $warranty['length_duration'], $value );

                echo '<p class="warranty_info"><b>'. $warranty_label .':</b> '. $value .' '. $duration .'</p>';
            } else {
                echo '<p class="warranty_info"><b>'. $warranty_label .':</b> '. __('Lifetime', 'dokan') .'</p>';
            }
        } elseif ( $warranty['type'] == 'addon_warranty' ) {
            $addons = $warranty['addon_settings'];

            if ( is_array( $addons ) && !empty( $addons ) ) {
                echo '<p class="warranty_info"><b>'. $warranty_label .'</b> <select name="dokan_warranty">';
                echo '<option value="-1">'. __( 'No warranty', 'dokan' ) .'</option>';

                foreach ( $addons as $x => $addon ) {
                    $amount     = $addon['price'];
                    $value      = $addon['length'];
                    $duration   = dokan_rma_get_duration_value( $addon['duration'], $value );

                    if ( $value == 0 && $amount == 0 ) {
                        // no warranty option
                        echo '<option value="-1">'. __( 'No warranty', 'dokan' ) .'</option>';
                    } else {
                        if ( $amount == 0 ) {
                            $amount = __( 'Free', 'dokan' );
                        } else {
                            $amount = wc_price( $amount );
                        }
                        echo '<option value="'. $x .'">'. $value .' '. $duration . ' &mdash; '. $amount .'</option>';
                    }
                }

                echo '</select></p>';
            }
        } else {
            echo '<p class="warranty_info"></p>';
        }
    }

    /**
     * Adds a dokan_warranty_index to a cart item.
     * Used in tracking the selected warranty options
     *
     * @since 1.0.0
     *
     * @param array $item_data
     * @param int $product_id
     *
     * @return array $item_data
     */
    function add_cart_item_data( $item_data, $product_id ) {
        if ( isset( $_POST['dokan_warranty']) && $_POST['dokan_warranty'] !== '' ) {
            $item_data['dokan_warranty_index'] = $_POST['dokan_warranty'];
        }

        return $item_data;
    }

    /**
     * Add custom data to a cart item based on the selected warranty type
     *
     * @since 1.0.0
     *
     * @param array $item_data
     *
     * @return array $item_data
     */
    function add_cart_item( $item_data ) {
        $_product       = $item_data['data'];
        $warranty_index = false;

        if ( isset( $item_data['dokan_warranty_index'] ) ) {
            $warranty_index = $item_data['dokan_warranty_index'];
        }

        $product_id = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->get_id();
        $warranty   = $this->get_settings( $product_id );

        if ( $warranty ) {
            if ( $warranty['type'] == 'addon_warranty' && $warranty_index !== false ) {
                $addons                            = $warranty['addon_settings'];
                $item_data['dokan_warranty_index'] = $warranty_index;
                $add_cost                          = 0;

                if ( isset( $addons[$warranty_index] ) && !empty( $addons[$warranty_index] ) ) {
                    $addon = $addons[$warranty_index];
                    if ( $addon['price'] > 0 ) {
                        $add_cost += $addon['price'];

                        $_product->set_price( $_product->get_price() + $add_cost );
                    }
                }
            }
        }

        return $item_data;
    }

    /**
     * Make sure an add-to-cart request is valid
     *
     * @param bool $valid
     * @param int $product_id
     * @return bool $valid
     */
    function add_cart_validation( $valid = '', $product_id = '' ) {
        $warranty       = $this->get_settings( $product_id );
        $warranty_label = $warranty['label'];

        if ( $warranty['type'] == 'addon_warranty' && ! isset( $_REQUEST['dokan_warranty'] ) ) {
            $error = sprintf( __( 'Please select your %s first.', 'dokan' ), $warranty_label );

            if ( function_exists( 'wc_add_notice' ) ) {
                wc_add_notice( $error, 'error' );
            } else {
                WC()->add_error( $error );
            }

            return false;
        }

        return $valid;
    }

    /**
     * Get warranty index and add it to the cart item
     *
     * @since 1.0.0
     *
     * @param array $cart_item
     * @param array $values
     *
     * @return array $cart_item
     */
    function get_cart_item_from_session( $cart_item, $values ) {

        if ( isset( $values['dokan_warranty_index'] ) ) {
            $cart_item['dokan_warranty_index'] = $values['dokan_warranty_index'];
            $cart_item = $this->add_cart_item( $cart_item );
        }

        return $cart_item;
    }

    /**
     * Returns warranty data about a cart item
     *
     * @since 1.0.0
     *
     * @param array $other_data
     * @param array $cart_item
     *
     * @return array $other_data
     */
    function get_item_data( $other_data, $cart_item ) {
        $_product   = $cart_item['data'];
        $product_id = $_product->get_id();

        $warranty       = $this->get_settings( $product_id );
        $warranty_label = $warranty['label'];

        if ( $warranty ) {
            if ( $warranty['type'] == 'addon_warranty' && isset( $cart_item['dokan_warranty_index'] ) ) {
                $addons         = $warranty['addon_settings'];
                $warranty_index = $cart_item['dokan_warranty_index'];

                if ( isset( $addons[$warranty_index] ) && ! empty( $addons[$warranty_index] ) ) {
                    $addon         = $addons[$warranty_index];
                    $name          = $warranty_label;
                    $duration_unit = dokan_rma_get_duration_value( $addon['duration'], $addon['length'] );
                    $value         = $addon['length'] . ' ' . $duration_unit;

                    if ( $addon['price'] > 0 ) {
                        $value .= ' (' . wc_price( $addon['price'] ) . ')';
                    }

                    $other_data[] = array(
                        'name'      => $name,
                        'value'     => $value,
                        'display'   => ''
                    );
                }
            } elseif ( $warranty['type'] == 'included_warranty' ) {
                if ( $warranty['length'] == 'lifetime' ) {
                    $other_data[] = array(
                        'name'      => $warranty_label,
                        'value'     => __( 'Lifetime', 'dokan' ),
                        'display'   => ''
                    );
                } elseif ( $warranty['length'] == 'limited' ) {
                    $duration_unit = dokan_rma_get_duration_value( $warranty['length_duration'], $warranty['length_value'] );
                    $string = $warranty['length_value'] . ' ' . $duration_unit;
                    $other_data[] = array(
                        'name'      => $warranty_label,
                        'value'     => $string,
                        'display'   => ''
                    );
                }
            }
        }

        return $other_data;
    }

    /**
     * Add warranty index to the cart items from POST
     *
     * @since 1.0.0
     *
     * @param string $cart_key
     * @param int $product_Id
     * @param int $quantity
     * @param int $variation_id
     * @param object $variation
     * @param array $cart_item_data
     *
     * @return void
     */
    function add_warranty_index( $cart_key, $product_id, $quantity, $variation_id = null, $variation = null, $cart_item_data = null ) {
        if ( isset( $_POST['dokan_warranty'] ) && $_POST['dokan_warranty'] !== '' ) {
            WC()->cart->cart_contents[$cart_key]['dokan_warranty_index'] = $_POST['dokan_warranty'];
        }
    }

    /**
     * Add to cart text.
     *
     * @since 1.0.0
     * @version 2.9.0
     * @param string $text Add to cart text.
     * @param object $product
     * @return string
     */
    public function add_to_cart_text( $text, $product = null ) {
        if ( ! is_object( $product ) ) {
            $product = wc_get_product( get_the_ID() );
        }

        if ( ! is_a( $product, 'WC_Product' ) ) {
            return $text;
        }

        if ( ! is_single( $product->get_id() ) && $this->check_required_warranty( $product->get_id() ) ) {
            $text = apply_filters( 'dokan_rma_addons_add_to_cart_text', __( 'Select options', 'dokan' ) );
        }

        return $text;
    }

    /**
     * Handle customer submit request
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function handle_warranty_submit_request() {
        if ( !isset( $_POST['warranty_submit_request'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['dokan_save_warranty_request_nonce'], 'dokan_save_warranty_request' ) ) {
            return;
        }

        if ( ! isset( $_POST['request_item'] ) ) {
            wc_add_notice( __( 'Please select some item for sending request', 'dokan' ), 'error' );
            return;
        }

        $product_map = [];

        // Mapping all product with quantity
        foreach ( $_POST['request_item'] as $key => $product_id ) {
            $product_map[] = [
                'product_id' => $product_id,
                'quantity'   => ! empty( $_POST['request_item_qty'][$key] ) ? $_POST['request_item_qty'][$key] : 1,
                'item_id'    => ! empty( $_POST['request_item_id'][$key] ) ? $_POST['request_item_id'][$key] : 0
            ];
        }

        $data          = $_POST;
        $data['items'] = $product_map;

        $result = dokan_save_warranty_request( $data );

        if ( is_wp_error( $result ) ) {
            wc_add_notice( $result->get_error_message(), 'error' );
            return;
        }

        do_action( 'dokan_rma_send_warranty_request', $data );

        wc_add_notice( __( 'Request has been successfully submitted', 'dokan' ), 'success' );

        wp_redirect( wc_get_account_endpoint_url( 'rma-requests' ) );
        exit();
    }

    /**
     * undocumented function
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function handle_warranty_conversation() {
        if ( ! isset( $_POST['dokan_rma_send_message'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['dokan_rma_send_message_nonce'], 'dokan_rma_send_message' ) ) {
            return;
        }

        if ( empty( $_POST['message'] ) ) {
            wc_add_notice( __( 'Please enter some text for messaging', 'dokan' ), 'error' );
            return;
        }

        if ( empty( $_POST['request_id'] ) ) {
            wc_add_notice( __( 'No request found for conversation', 'dokan' ), 'error' );
            return;
        }

        $data = [
            'request_id' => $_POST['request_id'],
            'from'       => $_POST['from'],
            'to'         => $_POST['to'],
            'message'    => sanitize_textarea_field( $_POST['message'] ),
            'created_at' => current_time( 'mysql' )
        ];

        $conversation = new Dokan_RMA_Conversation();
        $result       =  $conversation->insert( $data );

        if ( is_wp_error( $result ) ) {
            wc_add_notice( $result->get_error_message(), 'error' );
            return;
        }

        wc_add_notice( __( 'Message send successfully', 'dokan' ), 'success' );

        wp_redirect( $_POST['_wp_http_referer'] );
        exit();
    }

}
