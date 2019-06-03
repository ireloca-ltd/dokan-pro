<?php
/**
 * Dokan Shipping Class
 *
 * @author weDevs
 */
class Dokan_Pro_Shipping {

    /**
     * Load automatically when class inistantiate
     *
     * @since 2.4
     *
     * @uses actions|filter hooks
     */
    public function __construct() {
        if ( defined( 'WCML_VERSION' ) ) {
            require_once DOKAN_PRO_INC . '/shipping-gateway/shipping.php';
        }

        add_action( 'init', array( $this, 'include_shipping' ), 5 );
        add_action( 'woocommerce_shipping_methods', array( $this, 'register_shipping' ) );
        add_action( 'woocommerce_product_tabs', array( $this, 'register_product_tab' ) );
        add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_country' ) );
        add_action( 'template_redirect', array( $this, 'handle_shipping' ) );
    }

    /**
     * Inistantiate the Dokan_Pro_Shipping class
     *
     * @since 2.4
     *
     * @return object
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Shipping();
        }

        return $instance;
    }

    /**
     * Include main shipping integration
     *
     * @since 2.0
     *
     * @return void
     */
    function include_shipping() {
        require_once DOKAN_PRO_INC . '/shipping-gateway/shipping.php';
    }

    /**
     * Register shipping method
     *
     * @since 2.0
     *
     * @param array $methods
     *
     * @return array
     */
    function register_shipping( $methods ) {
        $methods['dokan_product_shipping'] = 'Dokan_WC_Shipping';
        $methods['dokan_vendor_shipping'] = 'Dokan_Vendor_Shipping';

        return $methods;
    }

    /**
     * Validate the shipping area
     *
     * @since 2.0
     *
     * @param  array $posted
     *
     * @return void
     */
    function validate_country( $posted ) {
        $shipping_method = WC()->session->get( 'chosen_shipping_methods' );

        // per product shipping was not chosen
        if ( ! is_array( $shipping_method ) || !in_array( 'dokan_product_shipping', $shipping_method ) ) {
            return;
        }

        if ( isset( $posted['ship_to_different_address'] ) && $posted['ship_to_different_address'] == '1' ) {
            $shipping_country = $posted['shipping_country'];
        } else {
            $shipping_country = $posted['billing_country'];
        }

        // echo $shipping_country;
        $packages = WC()->shipping->get_packages();

        reset( $packages );

        if ( !isset( $packages[0]['contents'] ) ) {
            return;
        }

        $products = array();

        foreach ( $packages as $package ) {
            array_push( $products, $package['contents'] );
        }

        $destination_country = isset( $packages[0]['destination']['country'] ) ? $packages[0]['destination']['country'] : '';
        $destination_state   = isset( $packages[0]['destination']['state'] ) ? $packages[0]['destination']['state'] : '';

        // hold all the errors
        $errors = array();

        foreach ( $products as $key => $product ) {
            $dokan_regular_shipping = new Dokan_WC_Shipping();

            foreach ( $product as $product_obj ) {
                $seller_id = get_post_field( 'post_author', $product_obj['product_id'] );

                if ( ! $dokan_regular_shipping->is_method_enabled() ) {
                    continue;
                }

                if ( ! Dokan_WC_Shipping::is_shipping_enabled_for_seller( $seller_id ) ) {
                    continue;
                }

                if ( Dokan_WC_Shipping::is_product_disable_shipping( $product_obj['product_id'] ) ) {
                    continue;
                }

                $dps_country_rates = get_user_meta( $seller_id, '_dps_country_rates', true );
                $dps_state_rates   = get_user_meta( $seller_id, '_dps_state_rates', true );

                $has_found   = false;
                $dps_country = ( isset( $dps_country_rates ) ) ? $dps_country_rates : array();
                $dps_state   = ( isset( $dps_state_rates[$destination_country] ) ) ? $dps_state_rates[$destination_country] : array();

                if ( array_key_exists( $destination_country, $dps_country ) ) {

                    if ( $dps_state ) {
                        if ( array_key_exists( $destination_state, $dps_state ) ) {
                            $has_found = true;
                        } elseif ( array_key_exists( 'everywhere', $dps_state ) ) {
                            $has_found = true;
                        }
                    } else {
                        $has_found = true;
                    }
                } else {
                    if ( array_key_exists( 'everywhere', $dps_country ) ) {
                        $has_found = true;
                    }
                }

                if ( ! $has_found ) {
                    $errors[] = sprintf( '<a href="%s">%s</a>', get_permalink( $product_obj['product_id'] ), get_the_title( $product_obj['product_id'] ) );
                }

            }
        }

        if ( $errors ) {
            if ( count( $errors ) == 1 ) {
                $message = sprintf( __( 'This product does not ship to your chosen location: %s', 'dokan' ), implode( ', ', $errors ) );
            } else {
                $message = sprintf( __( 'These products do not ship to your chosen location.: %s', 'dokan' ), implode( ', ', $errors ) );
            }

            wc_add_notice( $message, 'error' );
        }
    }

    /**
     *  Handle Shipping post submit
     *
     *  @since  2.0
     *
     *  @return void
     */
    function handle_shipping() {

        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        if ( isset( $_POST['dokan_update_shipping_options'] ) && wp_verify_nonce( $_POST['dokan_shipping_form_field_nonce'], 'dokan_shipping_form_field' ) ) {

            if ( ! current_user_can( 'dokan_view_store_shipping_menu' ) ) {
                wp_die( __( 'You have no access to save this shipping options', 'dokan' ) );
            }

            $user_id = dokan_get_current_user_id();
            $s_rates = array();
            $rates   = array();

            // Additional extra code

            if ( isset( $_POST['dps_enable_shipping'] ) ) {
                update_user_meta( $user_id, '_dps_shipping_enable', $_POST['dps_enable_shipping'] );
            }

            if ( isset( $_POST['dokan_shipping_type'] ) ) {
                update_user_meta( $user_id, '_dokan_shipping_type', $_POST['dokan_shipping_type'] );
            }

            if ( isset( $_POST['dps_shipping_type_price'] ) ) {
                update_user_meta( $user_id, '_dps_shipping_type_price', $_POST['dps_shipping_type_price'] );
            }

            if ( isset( $_POST['dps_additional_product'] ) ) {
                update_user_meta( $user_id, '_dps_additional_product', $_POST['dps_additional_product'] );
            }

            if ( isset( $_POST['dps_additional_qty'] ) ) {
                update_user_meta( $user_id, '_dps_additional_qty', $_POST['dps_additional_qty'] );
            }

            if ( isset( $_POST['dps_pt'] ) ) {
                update_user_meta( $user_id, '_dps_pt', $_POST['dps_pt'] );
            }

            if ( isset( $_POST['dps_ship_policy'] ) ) {
                update_user_meta( $user_id, '_dps_ship_policy', $_POST['dps_ship_policy'] );
            }

            if ( isset( $_POST['dps_refund_policy'] ) ) {
                update_user_meta( $user_id, '_dps_refund_policy', $_POST['dps_refund_policy'] );
            }

            if ( isset( $_POST['dps_form_location'] ) ) {
                update_user_meta( $user_id, '_dps_form_location', $_POST['dps_form_location'] );
            }

            if ( isset( $_POST['dps_country_to'] ) ) {

                foreach ($_POST['dps_country_to'] as $key => $value) {
                    $country = $value;
                    $c_price = floatval( $_POST['dps_country_to_price'][$key] );

                    if( !$c_price && empty( $c_price ) ) {
                        $c_price = 0;
                    }

                    if ( !empty( $value ) ) {
                        $rates[$country] = $c_price;
                    }
                }
            }

            update_user_meta( $user_id, '_dps_country_rates', $rates );

            if ( isset( $_POST['dps_state_to'] ) ) {
                foreach ( $_POST['dps_state_to'] as $country_code => $states ) {

                    foreach ( $states as $key_val => $name ) {
                        $country_c = $country_code;
                        $state_code = $name;
                        $s_price = floatval( $_POST['dps_state_to_price'][$country_c][$key_val] );

                        if ( !$s_price || empty( $s_price ) ) {
                            $s_price = 0;
                        }

                        if ( !empty( $name ) ) {
                            $s_rates[$country_c][$state_code] = $s_price;
                        }
                    }
                }
            }

            update_user_meta( $user_id, '_dps_state_rates', $s_rates );

            do_action( 'dokan_after_shipping_options_updated' ,$rates, $s_rates );

            $shipping_url = dokan_get_navigation_url( 'settings/regular-shipping' );
            wp_redirect( add_query_arg( array( 'message' => 'shipping_saved' ), $shipping_url ) );
            exit();
        }
    }

    /**
     * Adds a seller tab in product single page
     *
     * @since 2.0
     *
     * @param array $tabs
     *
     * @return array
     */
    function register_product_tab( $tabs ) {
        global $post;

        if( get_post_meta( $post->ID, '_disable_shipping', true ) == 'yes' ) {
            return $tabs;
        }

        if( get_post_meta( $post->ID, '_downloadable', true ) == 'yes' ) {
            return $tabs;
        }

        if ( 'yes' != get_option( 'woocommerce_calc_shipping' ) ) {
            return $tabs;
        }

        $tabs['shipping'] = array(
            'title' => __( 'Shipping', 'dokan' ),
            'priority' => 12,
            'callback' => array( $this, 'shipping_tab' )
        );

        return $tabs;
    }

    /**
     * Callback for Register_prouduct_tab function
     *
     * @since 2.0
     *
     * @return void
     */
    function shipping_tab() {
        global $post;

        $_overwrite_shipping     = get_post_meta( $post->ID, '_overwrite_shipping', true );
        $dps_processing          = get_user_meta( $post->post_author, '_dps_pt', true );
        $from                    = get_user_meta( $post->post_author, '_dps_form_location', true );
        $dps_country_rates       = get_user_meta( $post->post_author, '_dps_country_rates', true );
        $shipping_policy         = get_user_meta( $post->post_author, '_dps_ship_policy', true );
        $refund_policy           = get_user_meta( $post->post_author, '_dps_refund_policy', true );
        $product_processing_time = get_post_meta( $post->ID, '_dps_processing_time', true );
        $processing_time         = $dps_processing;

        if ( $_overwrite_shipping == 'yes' ) {
            $processing_time  = ( $product_processing_time ) ? $product_processing_time : $dps_processing;
        }

        $country_obj = new WC_Countries();
        $countries   = $country_obj->countries;
        ?>

        <?php if ( $processing_time ) { ?>
            <p>
                <strong>
                    <?php _e( 'Ready to ship in', 'dokan' ); ?> <?php echo dokan_get_processing_time_value( $processing_time ); ?>

                    <?php
                    if ( $from ) {
                        echo __( 'from', 'dokan' ) . ' ' . $countries[$from];
                    }
                    ?>
                </strong>
            </p>
            <hr>
        <?php } ?>

        <?php if ( $shipping_policy ) { ?>
            <p>&nbsp;</p>
            <strong><?php _e( 'Shipping Policy', 'dokan' ); ?></strong>
            <hr>
            <?php echo wpautop( $shipping_policy ); ?>
        <?php } ?>

        <?php if ( $refund_policy ) { ?>
            <hr>
            <p>&nbsp;</p>
            <strong><?php _e( 'Refund Policy', 'dokan' ); ?></strong>
            <hr>
            <?php echo wpautop( $refund_policy ); ?>
        <?php } ?>
        <?php
    }
}
