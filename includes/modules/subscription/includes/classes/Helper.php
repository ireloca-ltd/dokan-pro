<?php

namespace DokanPro\Modules\Subscription;

use Dokan\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * DPS Helper Class
 */
class Helper {
    use Singleton;

    /**
     * Get a sellers remaining product count
     *
     * @param  int $vendor_id
     *
     * @return int|0 on failure
     */
    public static function get_vendor_remaining_products( $vendor_id ) {
        $vendor = dokan()->vendor->get( $vendor_id )->subscription;

        if ( ! $vendor ) {
            return 0;
        }

        return $vendor->get_remaining_products();
    }

    /**
     * Check if its vendor subscribed pack
     *
     * @param integer $product_id
     *
     * @return boolean
     */
    public static function is_vendor_subscribed_pack( $product_id ) {
        $user_id              = get_current_user_id();
        $date                 = date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );
        $product_pack_enddate = self::get_pack_end_date( $user_id );
        $validation_date      = date( 'Y-m-d', strtotime( $product_pack_enddate ) );
        $product_package_id   = get_user_meta( $user_id, 'product_package_id', true );

        if ( $product_pack_enddate == 'unlimited' && $product_package_id == $product_id ) {
            return true;
        }

        if ( $date < $validation_date && $product_package_id == $product_id ) {
            return true;
        }

        return false;
    }

    /**
     * Check package renew for seller
     *
     * @param integer $product_id
     *
     * @return boolean
     */
    public static function pack_renew_seller( $product_id ) {

        $date = date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );
        $validation_date = date( 'Y-m-d', strtotime( self::get_pack_end_date( dokan_get_current_user_id() ) ) );

        $datetime1 = new \DateTime( $date );
        $datetime2 = new \DateTime( $validation_date );

        $interval = $datetime1->diff( $datetime2 );

        $interval = $interval->format( '%r%d' );

        if ( (int) $interval <= 3 && (int) $interval >= 0 && ( get_user_meta( get_current_user_id(), 'product_package_id', true ) == $product_id ) ) {
            return true;
        }

        return false;
    }



    /**
     * Returns a readable recurring period
     *
     * @param  string $period
     *
     * @return string
     */
    public static function recurring_period( $period ) {
        switch ($period) {
            case 'day':
                return __( 'day', 'dokan' );

            case 'week':
                return __( 'week', 'dokan' );

            case 'month':
                return __( 'month', 'dokan' );

            case 'year':
                return __( 'year', 'dokan' );

            default:
                return apply_filters( 'dps_recurring_text', $period );
        }
    }

    /**
     * Get a list of options of all the product types
     *
     * @return strings
     */
    public static function get_product_types_options() {
        $selected      = dokan()->subscription->get( get_the_ID() )->get_allowed_product_types();
        $product_types = dokan_get_product_types();
        $output        = '';

        if ( ! $product_types ) {
            return;
        }

        foreach ( $product_types as $value => $label ) {
            $output .= '<option value="' . esc_attr( $value ) . '" ';
            $output .= in_array( $value, $selected ) ? ' selected="selected"' : '';
            $output .= '>' . esc_html( $label ) . '</option>';
        }

        echo $output;
    }

    /**
     * Get a list of options for trail period
     *
     * @return string
     */
    public static function get_trial_period_options() {
        $subscription   = dokan()->subscription->get( get_the_ID() );
        $selected_range = $subscription->get_trial_range();
        $range_output   = '';
        $period_range   = range( 1, 30 );
        $range_output   .= '<select name="dokan_subscription_trail_range" class="dokan-subscription-range" style="margin-right: 10px">';

        foreach ( $period_range as $range ) {
            $range_output .= '<option value="' . esc_attr( $range ) . '"';
            $range_output .= selected( $selected_range, $range, false );
            $range_output .= '>' . __( $range, 'dokan' ) . '</option>';
        }

        $range_output .= '</select>';
        echo $range_output;

        $selected_period_types = $subscription->get_trial_period_types();
        $period_types_output   = '';
        $period_types          = apply_filters( 'dokan_subscription_trial_period_types', [
            'day'   => __( 'Day(s)', 'dokan' ),
            'week'  => __( 'Week(s)', 'dokan' ),
            'month' => __( 'Month(s)', 'dokan' ),
            'year'  => __( 'Year(s)', 'dokan' )
        ] );

        $period_types_output .= '<select name="dokan_subscription_trial_period_types">';

        foreach ( $period_types as $key => $value ) {
            $period_types_output .= '<option value="' . esc_attr( $key ) . '"';
            $period_types_output .= selected( $selected_period_types, $key, false );
            $period_types_output .= '>' . $value . '</option>';
        }

        $period_types_output .= '</select>';
        echo $period_types_output;
    }

    /**
     * Get vendor subscription pack id
     *
     * @return int|null on failure
     */
    public static function get_subscription_pack_id() {
        $user_id = dokan_get_current_user_id();

        if ( ! $user_id || ! dokan_is_user_seller( $user_id ) ) {
            return null;
        }

        $subscription_pack_id = get_user_meta( $user_id, 'product_package_id', true );

        if ( ! $subscription_pack_id ) {
            return null;
        }

        return $subscription_pack_id;
    }

    /**
     * Is gallary image upload restricted
     *
     * @return boolean
     */
    public static function is_gallery_image_upload_restricted() {
        return get_post_meta( self::get_subscription_pack_id(), '_enable_gallery_restriction', true );
    }

    /**
     * Get allowed product types of a vendor
     *
     * @return array|empty array on failure
     */
    public static function get_vendor_allowed_product_types() {
        $types  = [];
        $vendor = dokan()->vendor->get( dokan_get_current_user_id() )->subscription;

        if ( $vendor ) {
            $types = $vendor->get_allowed_product_types();
        }

        return $types ? $types : [];
    }

    /**
     * Get allowed product cateogories of a vendor
     *
     * @return array|empty array on failure
     */
    public static function get_vendor_allowed_product_categories() {
        $categories = [];

        $vendor = dokan()->vendor->get( dokan_get_current_user_id() )->subscription;

        if ( $vendor ) {
            $categories = $vendor->get_allowed_product_categories();
        }

        return $categories;
    }

    /**
     * Get subscription recurring interval strings
     *
     * @return string
     */
    public static function get_subscription_period_interval_strings() {
        $intervals = array();

        for ( $i = 1; $i <= 30; $i++ ) {
            $intervals[$i] = $i;
        }

        return $intervals;
    }


    /**
     * Return an i18n'ified associative array of all possible subscription periods.
     *
     * @since 1.1
     */
    public static function get_subscription_period_strings( $number = 1, $period = '' ) {

        $translated_periods = array(
            'day'   => sprintf( _n( 'day(s)', '%s days', $number, 'dokan' ), $number ),
            'week'  => sprintf( _n( 'week(s)', '%s weeks', $number, 'dokan' ), $number ),
            'month' => sprintf( _n( 'month(s)', '%s months', $number, 'dokan' ), $number ),
            'year'  => sprintf( _n( 'year(s)', '%s years', $number, 'dokan' ), $number )
        );

        return $translated_periods;
    }


    /**
     * Returns an array of subscription lengths.
     *
     * PayPal Standard Allowable Ranges
     * D – for days; allowable range is 1 to 90
     * W – for weeks; allowable range is 1 to 52
     * M – for months; allowable range is 1 to 24
     * Y – for years; allowable range is 1 to 5
     *
     * @param subscription_period string (optional) One of day, week, month or year. If empty, all subscription ranges are returned.
     * @since 1.0
     */
    public static function get_subscription_ranges( $subscription_period = '' ) {
        $subscription_ranges = array();
        $subscription_ranges[] = __( 'Never', 'dokan' );

        for ( $i = 1; $i <= 30; $i++ ) {
            $subscription_ranges[$i] = $i;
        }

        return $subscription_ranges;
    }

    /**
     * Is subscription module is enabled
     *
     * @return boolean
     */
    public static function is_subscription_module_enabled() {
        $is_enabled = dokan_get_option( 'enable_pricing', 'dokan_product_subscription' );

        return 'on' === $is_enabled ? true : false;
    }

    /**
     * Is subscription is enalbed on registration
     *
     * @return boolean
     */
    public static function is_subscription_enabled_on_registration() {
        $is_enabled = dokan_get_option( 'enable_subscription_pack_in_reg', 'dokan_product_subscription' );

        return 'on' === $is_enabled ? true : false;
    }

    /**
     * Check is product is subscription or not
     *
     * @param integer $product_id
     * @return boolean
     */
    public static function is_subscription_product( $product_id ) {
        $product = wc_get_product( $product_id );

        if ( $product && 'product_pack' === $product->get_type() ) {
            return true;
        }

        return false;
    }

    /**
     * Checks the cart to see if it contains a subscription product renewal.
     * Returns the cart_item containing the product renewal, else false.
     *
     * @return boolean
     */
    public static function cart_contains_subscription_renewal( $role = '' ) {
        $contains_renewal = false;

        if ( !empty( WC()->cart->cart_contents ) ) {
            foreach ( WC()->cart->cart_contents as $cart_item ) {
                if ( isset( $cart_item['subscription_renewal'] ) && ( empty( $role ) || $role === $cart_item['subscription_renewal']['role'] ) ) {
                    $contains_renewal = $cart_item;
                    break;
                }
            }
        }

        return $contains_renewal;
    }

    /**
     * Checks the cart to see if it contains a subscription product
     *
     * @return void
     */
    public static function cart_contains_subscription() {
        global $woocommerce;

        $contains_subscription = false;

        if ( self::cart_contains_subscription_renewal( 'child' ) ) {

            $contains_subscription = false;

        } else if ( ! empty( WC()->cart->cart_contents ) ) {
            foreach ( WC()->cart->cart_contents as $cart_item ) {
                if ( self::is_subscription_product( $cart_item['product_id'] ) ) {
                    $contains_subscription = true;
                    break;
                }
            }
        }

        return $contains_subscription;
    }

    /**
     * Removes all subscription products from the shopping cart.
     *
     * @return void
     */
    public static function remove_subscriptions_from_cart() {

        foreach ( WC()->cart->cart_contents as $cart_item_key => $cart_item ) {
            if ( self::is_subscription_product( $cart_item['product_id'] ) ) {
                WC()->cart->set_quantity( $cart_item_key, 0 );
            }
        }
    }

    /**
     * Helper function for loggin
     *
     * @param string $message
     */
    public static function log( $message ) {
        $message = sprintf( "[%s] %s\n", date( 'd.m.Y h:i:s' ), $message );
        error_log( $message, 3, dirname( __FILE__ ) . '/debug.log' );
    }

    /**
     * Delete Subscription pack
     *
     * @param integer $customer_id
     *
     * @return void
     */
    public static function delete_subscription_pack( $customer_id, $order_id ) {
        if ( $order_id != get_user_meta( $customer_id, 'product_order_id', true ) ) {
            return;
        }

        do_action( 'dokan_subscription_cancelled', $customer_id, get_user_meta( $customer_id, 'product_package_id', true ) );

        delete_user_meta( $customer_id, 'product_package_id' );
        delete_user_meta( $customer_id, 'product_order_id' );
        delete_user_meta( $customer_id, 'product_no_with_pack' );
        delete_user_meta( $customer_id, 'product_pack_startdate' );
        delete_user_meta( $customer_id, 'product_pack_enddate' );
        delete_user_meta( $customer_id, 'can_post_product' );
        delete_user_meta( $customer_id, '_customer_recurring_subscription' );
        delete_user_meta( $customer_id, 'dokan_admin_percentage' );
    }

    /**
     * Check if a vendor has existing product
     *
     * @param int  $user_id
     *
     * @return boolean
     */
    public static function check_vendor_has_existing_product( $user_id ) {
        $query = get_posts( "post_type=product&author=$user_id&post_status=any" );

        self::log( 'Product exist check: As the package has expired of user #' . $user_id . ' we are checking if he has any product' );

        if ( $query ) {
            return true;
        }

        return false;
    }

    /**
     * Upadate Product Status
     *
     * @param int $user_id
     *
     * @return void
     */
    public static function update_product_status( $user_id ) {
        global $wpdb;

        $status = dokan_get_option( 'product_status_after_end', 'dokan_product_subscription', 'draft' );
        self::log ( 'Product status check: As the package has expired of user #' . $user_id . ', we are changing his existing product status to ' . $status );
        $wpdb->query( "UPDATE $wpdb->posts SET post_status = '$status' WHERE post_author = '$user_id' AND post_type = 'product' AND post_status='publish'" );
    }

    /**
     * Alert before 2 days end of subscription
     *
     * @return boolean
     */
    public static function alert_before_two_days( $user_id ) {
        if ( 'unlimited' === self::get_pack_end_date( $user_id ) ) {
            return false;
        }

        $alert_days = dokan_get_option( 'no_of_days_before_mail', 'dokan_product_subscription' );

        if ( $alert_days == 0 ) {
            $alert_days = 2;
        }

        $date = new \DateTime( date( 'Y-m-d h:i:s', strtotime( current_time( 'mysql', 1 ) . '+' . $alert_days . ' days' ) ) );
        $prv_two_date = $date->format( 'Y-m-d H:i:s' );

        // return $prv_two_date;
        if ( $prv_two_date == self::get_pack_end_date( $user_id ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get pack end date
     *
     * @return string
     */
    public static function get_pack_end_date( $vendor_id ) {
        return get_user_meta( $vendor_id, 'product_pack_enddate', true );
    }

    /**
     * Update can_post_product flag on subscripton expire
     *
     * @return boolean
     */
    public static function maybe_cancel_subscription( $vendor_id ) {
        if ( 'unlimited' === self::get_pack_end_date( $vendor_id ) ) {
            return false;
        }

        $date            = date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );
        $validation_date = date( 'Y-m-d', strtotime( self::get_pack_end_date( $vendor_id ) ) );

        if ( $date > $validation_date ) {
            self::log( 'Subscription validity check ( ' . $date . ' ): checking subscription pack validity of user #' . $vendor_id . '. This users subscription pack will expire on ' . $validation_date );
            return true;
        }

        return false;
    }

    /**
     * Determine if the user has used a free pack before
     *
     * @param int $user_id
     *
     * @return boolean
     */
    public static function has_used_trial_pack( $user_id ) {
        $has_used = get_user_meta( $user_id, 'dokan_used_trial_pack', true );

        if ( ! $has_used ) {
            return false;
        }

        return true;
    }

    /**
     * Make product status publish
     *
     * @param int $user_id
     *
     * @return void
     */
    public static function make_product_publish( $user_id ) {
        global $wpdb;

        $wpdb->query( "UPDATE $wpdb->posts SET post_status = 'publish' WHERE post_author = '$user_id' AND post_type = 'product' AND post_status != 'publish'" );
    }

    /**
     * Add used trial pack
     *
     * @param int $user_id
     * @param int $pack_id
     *
     * @return void
     */
    public static function add_used_trial_pack( $user_id, $pack_id ) {
        $subscription = dokan()->subscription->get( $pack_id );

        if ( empty( $subscription->pack_id ) ) {
            return false;
        }

        if ( ! $subscription->is_trial() ) {
            return false;
        }

        update_user_meta( $user_id, 'dokan_used_trial_pack', true );
    }
}

Helper::instance();