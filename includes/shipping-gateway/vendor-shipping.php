<?php
/*
 * Table Rate Shipping Method Extender Class
 */

if ( ! defined( 'ABSPATH' ) )
    exit;

// Check if WooCommerce is active
if ( class_exists( 'WooCommerce' ) ) {

    if ( ! class_exists('WC_Shipping_Method')) {
        return;
    }

    if ( class_exists( 'Dokan_Vendor_Shipping' ) ) {
        return;
    }

    class Dokan_Vendor_Shipping extends WC_Shipping_Method {

        /*
         * Table Rates from Database
         */
        protected $options_save_name;

        /*
         * Table Rates from Database
         */
        public $default_option;

        /**
         * Cloning is forbidden. Will deactivate prior 'instances' users are running
         *
         * @since 4.0
         */
        public function __clone() {
            _doing_it_wrong( __FUNCTION__, __( 'Cloning this class could cause catastrophic disasters!', 'dokan' ), '4.0' );
        }

        /**
         * Unserializing instances of this class is forbidden.
         *
         * @since 4.0
         */
        public function __wakeup() {
            _doing_it_wrong( __FUNCTION__, __( 'Unserializing is forbidden!', 'dokan' ), '4.0' );
        }

        /**
         * __construct function.
         *
         * @access public
         * @return void
         */
        function __construct( $instance_id = 0 ) {
            $this->id                   = 'dokan_vendor_shipping';
            $this->instance_id          = absint( $instance_id );
            $this->method_title         = __( 'Vendor Shipping', 'be-table-ship' );
            $this->method_description   = __( 'Charge varying rates based on user defined conditions', 'be-table-ship' );
            $this->supports             = array( 'shipping-zones', 'instance-settings', 'instance-settings-modal' );
            $this->default              = "";

            // Initialize settings
            $this->init();

            // additional hooks for post-calculations settings
            add_filter( 'woocommerce_shipping_chosen_method', array( $this, 'select_default_rate' ), 10, 2 );
            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

        }


        /**
         * Evaluate a cost from a sum/string.
         * @param  string $sum
         * @param  array  $args
         * @return string
         */
        protected function evaluate_cost( $sum, $args = array() ) {
            include_once( WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php' );

            // Allow 3rd parties to process shipping cost arguments
            $args           = apply_filters( 'woocommerce_evaluate_shipping_cost_args', $args, $sum, $this );
            $locale         = localeconv();
            $decimals       = array( wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' );
            $this->fee_cost = $args['cost'];

            // Expand shortcodes
            add_shortcode( 'fee', array( $this, 'fee' ) );

            $sum = do_shortcode( str_replace(
                array(
                    '[qty]',
                    '[cost]',
                ),
                array(
                    $args['qty'],
                    $args['cost'],
                ),
                $sum
            ) );

            remove_shortcode( 'fee', array( $this, 'fee' ) );

            // Remove whitespace from string
            $sum = preg_replace( '/\s+/', '', $sum );

            // Remove locale from string
            $sum = str_replace( $decimals, '.', $sum );

            // Trim invalid start/end characters
            $sum = rtrim( ltrim( $sum, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

            // Do the math
            return $sum ? WC_Eval_Math::evaluate( $sum ) : 0;
        }

        /**
         * Work out fee (shortcode).
         * @param  array $atts
         * @return string
         */
        public function fee( $atts ) {
            $atts = shortcode_atts( array(
                'percent' => '',
                'min_fee' => '',
                'max_fee' => '',
            ), $atts, 'fee' );

            $calculated_fee = 0;

            if ( $atts['percent'] ) {
                $calculated_fee = $this->fee_cost * ( floatval( $atts['percent'] ) / 100 );
            }

            if ( $atts['min_fee'] && $calculated_fee < $atts['min_fee'] ) {
                $calculated_fee = $atts['min_fee'];
            }

            if ( $atts['max_fee'] && $calculated_fee > $atts['max_fee'] ) {
                $calculated_fee = $atts['max_fee'];
            }

            return $calculated_fee;
        }

        /**
         * Get items in package.
         * @param  array $package
         * @return int
         */
        public function get_package_item_qty( $package ) {
            $total_quantity = 0;
            foreach ( $package['contents'] as $item_id => $values ) {
                if ( $values['quantity'] > 0 && $values['data']->needs_shipping() ) {
                    $total_quantity += $values['quantity'];
                }
            }
            return $total_quantity;
        }

        /**
         * Finds and returns shipping classes and the products with said class.
         *
         * @param mixed $package
         *
         * @return array
         */
        public function find_shipping_classes( $package ) {
            $found_shipping_classes = array();

            foreach ( $package['contents'] as $item_id => $values ) {
                if ( $values['data']->needs_shipping() ) {
                    $found_class = $values['data']->get_shipping_class();

                    if ( ! isset( $found_shipping_classes[ $found_class ] ) ) {
                        $found_shipping_classes[ $found_class ] = array();
                    }

                    $found_shipping_classes[ $found_class ][ $item_id ] = $values;
                }
            }

            return $found_shipping_classes;
        }

        /**
        * init function.
        * initialize variables to be used
        *
        * @access public
        * @return void
        */
        function init() {
            $this->instance_form_fields = array(
                'title' => array(
                    'title'         => __( 'Method title', 'dokan' ),
                    'type'          => 'text',
                    'description'   => __( 'This controls the title which the user sees during checkout.', 'dokan' ),
                    'default'       => __( 'Vendor Shipping', 'dokan' ),
                    'desc_tip'      => true,
                ),
                'tax_status' => array(
                    'title'         => __( 'Tax status', 'dokan' ),
                    'type'          => 'select',
                    'class'         => 'wc-enhanced-select',
                    'default'       => 'taxable',
                    'options'       => array(
                        'taxable'   => __( 'Taxable', 'dokan' ),
                        'none'      => _x( 'None', 'Tax status', 'dokan' ),
                    ),
                )
            );

            $this->title                = $this->get_option( 'title' );
            $this->tax_status           = $this->get_option( 'tax_status' );
        }

        /**
         * calculate_shipping function.
         *
         * @access public
         * @param array $package (default: array())
         * @return void
         */
        function calculate_shipping( $package = array() ) {
            $rates = array();
            $zone = Dokan_Shipping_Zone::get_zone_matching_package( $package );
            $seller_id = $package['seller_id'];

            if ( empty( $seller_id ) ) {
                return;
            }

            $shipping_methods = Dokan_Shipping_Zone::get_shipping_methods( $zone->get_id(), $seller_id );

            if ( empty( $shipping_methods ) ) {
                return;
            }

            foreach ( $shipping_methods as $key => $method ) {
                $tax_rate = ( $method['settings']['tax_status'] == 'none' ) ? false : '';
                $has_costs     = false;

                if ( 'yes' != $method['enabled'] ) {
                    continue;
                }

                if ( $method['id'] == 'flat_rate' ) {
                    $setting_cost = isset( $method['settings']['cost'] ) ? stripslashes_deep( $method['settings']['cost'] ) : '';

                    if ( '' !== $setting_cost ) {
                        $has_costs = true;
                        $cost = $this->evaluate_cost( $setting_cost, array(
                            'qty'  => $this->get_package_item_qty( $package ),
                            'cost' => $package['contents_cost'],
                        ) );
                    }

                    // Add shipping class costs.
                    $shipping_classes = WC()->shipping->get_shipping_classes();

                    if ( ! empty( $shipping_classes ) ) {
                        $found_shipping_classes = $this->find_shipping_classes( $package );
                        $highest_class_cost     = 0;
                        $calculation_type = ! empty( $method['settings']['calculation_type'] ) ? $method['settings']['calculation_type'] : 'class';
                        foreach ( $found_shipping_classes as $shipping_class => $products ) {
                            // Also handles BW compatibility when slugs were used instead of ids
                            $shipping_class_term = get_term_by( 'slug', $shipping_class, 'product_shipping_class' );
                            $class_cost_string   = $shipping_class_term && $shipping_class_term->term_id
                                                    ? ( ! empty( $method['settings']['class_cost_' . $shipping_class_term->term_id ] ) ? stripslashes_deep( $method['settings']['class_cost_' . $shipping_class_term->term_id] ) : '' )
                                                    : ( ! empty( $method['settings']['no_class_cost'] ) ? $method['settings']['no_class_cost'] : '' );

                            if ( '' === $class_cost_string ) {
                                continue;
                            }

                            $has_costs  = true;

                            $class_cost = $this->evaluate_cost( $class_cost_string, array(
                                'qty'  => array_sum( wp_list_pluck( $products, 'quantity' ) ),
                                'cost' => array_sum( wp_list_pluck( $products, 'line_total' ) ),
                            ) );

                            if ( 'class' === $calculation_type ) {
                                $cost += $class_cost;
                            } else {
                                $highest_class_cost = $class_cost > $highest_class_cost ? $class_cost : $highest_class_cost;
                            }
                        }

                        if ( 'order' === $calculation_type && $highest_class_cost ) {
                            $cost += $highest_class_cost;
                        }
                    }

                } elseif ( 'free_shipping' == $method['id'] ) {
                    $is_available = $this->free_shipping_is_available( $package, $method );

                    if ( $is_available ) {
                        $cost = '0';
                        $has_costs = true;
                    }
                } else {
                    if ( ! empty( $method['settings']['cost'] ) ) {
                        $has_costs = true;
                        $cost = $method['settings']['cost'];
                    } else {
                        $has_costs = true;
                        $cost = '0';
                    }
                }


                if ( ! $has_costs ) {
                    continue;
                }

                $rates[] = array(
                    'id'          => $this->get_method_rate_id( $method ),
                    'label'       => $method['title'],
                    'cost'        => $cost,
                    'description' => ! empty( $method['settings']['description'] ) ? $method['settings']['description'] : '',
                    'taxes'       => $tax_rate,
                    'default'     => 'off'
                );
            }

            // send shipping rates to WooCommerce
            if( is_array( $rates ) && count( $rates ) > 0 ) {

                // cycle through rates to send and alter post-add settings
                foreach( $rates as $key => $rate ) {

                    $this->add_rate( array(
                        'id'        => $rate['id'],
                        'label'     => apply_filters( 'dokan_vendor_shipping_rate_label', $rate['label'], $rate ),
                        'cost'      => $rate['cost'],
                        'meta_data' => array( 'description' => $rate['description'] ),
                        'package'   => $package,
                    ));

                    if( $rate['default'] == 'on' ) {
                        $this->default = $rate['id'];
                    }
                }
            }
        }


        /**
         * See if free shipping is available based on the package and cart.
         *
         * @param array $package Shipping package.
         *
         * @return bool
         */
        public function free_shipping_is_available( $package, $method ) {
            $has_met_min_amount = false;
            $min_amount = ! empty( $method['settings']['min_amount'] ) ? $method['settings']['min_amount'] : 0;

            $line_subtotal      = wp_list_pluck( $package['contents'], 'line_subtotal', null );
            $line_total         = wp_list_pluck( $package['contents'], 'line_total', null );
            $discount_total     = array_sum( $line_subtotal ) - array_sum( $line_total );
            $line_subtotal_tax  = wp_list_pluck( $package['contents'], 'line_subtotal_tax', null );
            $line_total_tax     = wp_list_pluck( $package['contents'], 'line_tax', null );
            $discount_tax_total = array_sum( $line_subtotal_tax ) - array_sum( $line_total_tax );

            $total = array_sum( $line_subtotal ) + array_sum( $line_subtotal_tax );

            if ( WC()->cart->display_prices_including_tax() ) {
                $total = round( $total - ( $discount_total + $discount_tax_total ), wc_get_price_decimals() );
            } else {
                $total = round( $total - $discount_total, wc_get_price_decimals() );
            }

            if ( $total >= $min_amount ) {
                $has_met_min_amount = true;
            }

            return apply_filters( 'dokaan_shipping_free_shipping_is_available', $has_met_min_amount, $package, $method );
        }


        /**
         * Is available in specific zone locations
         *
         * @since 2.8.0
         *
         * @return void
         */
        public function is_available( $package ) {
            $seller_id = $package['seller_id'];

            $destination_country  = isset( $package['destination']['country'] ) ? $package['destination']['country'] : '';
            $destination_state    = isset( $package['destination']['state'] ) ? $package['destination']['state'] : '';
            $destination_postcode = isset( $package['destination']['postcode'] ) ? $package['destination']['postcode'] : '';

            if ( empty( $seller_id ) ) {
                return false;
            }

            $zone      = Dokan_Shipping_Zone::get_zone_matching_package( $package );
            $locations = Dokan_Shipping_Zone::get_locations( $zone->get_id(), $seller_id );

            if ( empty( $locations ) ) {
                return true;
            }

            $location_group = array();

            foreach ( $locations as $location ) {
                $location_group[$location['type']][] = $location;
            }

            $is_available = false;

            if ( isset( $location_group['country'] ) ) {
                $country_array = wp_list_pluck( $location_group['country'], 'code' );

                if ( ! in_array( $destination_country, $country_array ) ) {
                    return false;
                }

                $is_available = true;
            }

            if ( isset( $location_group['state'] ) ) {
                $states       = wp_list_pluck( $location_group['state'], 'code' );
                $state_array  = array_map( array( $this, 'split_state_code' ), $states );
                $is_available = false;

                if ( in_array( $destination_state, $state_array ) ) {
                    $is_available = true;
                }
            }

            if ( isset( $location_group['postcode'] ) ) {
                $is_available   = false;
                $postcode_array = wp_list_pluck( $location_group['postcode'], 'code' );

                // if postcode is set as ranges (e.g. 10001...10010)
                if ( strstr( $postcode_array[0], '...' ) ) {
                    $range = array_map( 'trim', explode( '...', $postcode_array[0] ) );

                    if ( 2 !== count( $range ) ) {
                        return $is_available;
                    }

                    list( $min, $max ) = $range;

                    // If the postcode is non-numeric, make it numeric.
                    if ( ! is_numeric( $min ) || ! is_numeric( $max ) ) {
                        $min = str_pad( wc_make_numeric_postcode( $min ), strlen( $compare ), '0' );
                        $max = str_pad( wc_make_numeric_postcode( $max ), strlen( $compare ), '0' );
                    }

                    $postcode_array = range( $min, $max );
                } else {
                    $postcode_array = array_map( 'trim', $postcode_array );
                }

                if ( in_array( $destination_postcode, $postcode_array ) ) {
                    $is_available = true;
                }

                // if postcode is set as wilecard range (e.g 1000*)
                if ( strstr( $postcode_array[0], '*' ) ) {
                    $is_available = true;
                }
            }

            return apply_filters( $this->id . '_is_available', $is_available, $package, $this );
        }

        /**
         * Split state code from country:state string
         *
         * @param string $value [like: BD:DHA]
         *
         * @return string [like: DHA ]
         */
        public function split_state_code( $value ) {
            $state_code = explode( ':', $value );
            return $state_code[1];
        }

        /**
         * alter the default rate if one is chosen in settings.
         *
         * @access public
         *
         *  @param mixed $package
         *
         * @return bool
         */
        function select_default_rate( $chosen_method, $_available_methods ) {

            //Select the 'Default' method from WooCommerce settings
            if( array_key_exists( $this->default, $_available_methods ) ) {

                return $this->default;
            }

            return $chosen_method;
        }


        /**
         * Hide shipping rates when free shipping is available.
         * Updated to support WooCommerce 2.6 Shipping Zones.
         *
         * @access public
         *
         * @param array $rates Array of rates found for the package.
         *
         * @return array
         */
        function hide_shipping_when_free_is_available( $rates ) {
            if( $this->hide_method !== 'yes' ) return $rates;

            // determine if free shipping is available
            $free_shipping = false;
            foreach ( $rates as $rate_id => $rate ) {
                if ( 'free_shipping' === $rate->method_id ) {
                    $free_shipping = true;
                    break;
                }
            }
            // if available, remove all options from this method
            if( $free_shipping ) {
                foreach ( $rates as $rate_id => $rate ) {
                    if ( $this->id === $rate->method_id && strpos( $rate_id, $this->id . ':' . $this->instance_id . '-') !== false ) {
                        unset( $rates[ $rate_id ] );
                    }
                }
            }

            return $rates;
        }


        /**
         * Hide shipping rates when one has option enabled.
         *
         * @access public
         *
         * @param array $rates Array of rates found for the package.
         *
         * @return array
         */
        function hide_other_options( $rates ) {
            $hide_key = false;

            // return if no rates have been added
            if( ! isset( $rates ) || empty( $rates ) )
                return $rates;

            // cycle through available rates
            foreach( $rates as $key => $rate ) {
                if( $rate['hide_ops'] === 'on' ) {
                    $hide_key = $key;
                }
            }

            if( $hide_key ) {
                return array( $hide_key => $rates[ $hide_key ] );
            }

            return $rates;
        }

        /**
         * Get shpping method id
         *
         * @since 2.8.0
         *
         * @return void
         */
        public function get_method_rate_id( $method ) {
            return apply_filters( 'dokan_get_vendor_shipping_method_id', $method['id'] . ':' . $method['instance_id'] );
        }

    }

}
