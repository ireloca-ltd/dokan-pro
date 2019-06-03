<?php

namespace DokanPro\Modules\Subscription\Abstracts;

defined( 'ABSPATH' ) || exit;

/**
 * Dokan Abstract Vendor Subscription Class
 */
abstract class VendorSubscription {

    /**
     * Hold vendor id
     *
     * @var integer
     */
    protected $vendor_id = 0;

    /**
     * Get vendor id
     *
     * @return integer
     */
    public abstract function get_vendor();

    /**
     * Get all the info of a vendor regarding subscription
     *
     * @return array
     */
    public function get_info() {
        if ( ! $this->get_id() ) {
            return null;
        }

        return [
            'subscription_id'    => $this->get_id(),
            'has_subscription'   => $this->has_subscription(),
            'expiry_date'        => $this->get_pack_end_date(),
            'published_products' => $this->get_published_product_count(),
            'remaining_products' => $this->get_remaining_products(),
        ];
    }

    /**
     * Check if vendor has a subscription
     *
     * @return boolean
     */
    public function has_subscription() {
        $pack_id = get_user_meta( $this->get_vendor(), 'product_package_id', true );

        return $pack_id ? true : false;
    }

    /**
     * Get pack end date
     *
     * @return string
     */
    public function get_pack_end_date() {
        return get_user_meta( $this->get_vendor(), 'product_pack_enddate', true );
    }

    /**
     * Can post product
     *
     * @return boolean
     */
    public function can_post_product() {
        return get_user_meta( $this->get_vendor(), 'can_post_product', true );
    }

    /**
     * Get pack starting date
     *
     * @return string
     */
    public function get_pack_start_date() {
        return get_user_meta( $this->get_vendor(), 'product_pack_startdate', true );
    }

    /**
     * Check package validity for seller
     *
     * @param integer $product_id
     *
     * @return boolean
     */
    public function check_pack_validity_for_vendor( $pack_id ) {
        $date                 = date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );
        $product_pack_enddate = $this->get_pack_end_date();
        $validation_date      = date( 'Y-m-d', strtotime( $product_pack_enddate ) );
        $product_package_id   = $this->get_id();

        if ( $product_pack_enddate == 'unlimited' && $product_package_id == $pack_id ) {
            return true;
        }

        if ( $date < $validation_date && $product_package_id == $pack_id ) {
            return true;
        }

        return false;
    }

    /**
     * Get number of product has publisedh by seller
     *
     * @param integer
     *
     * @return integer
     */
    function get_published_product_count() {
        global $wpdb;

        $allowed_status = apply_filters( 'dps_get_product_by_seller_allowed_statuses', array( 'publish', 'pending' ) );

        $query = "SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = {$this->get_vendor()} AND post_type = 'product' AND post_status IN ( '" . implode( "','", $allowed_status ). "' )";
        $count = $wpdb->get_var( $query );

        return $count;
    }

    /**
     * Get a vendor remaining product count against a subscription pack
     *
     * @return int
     */
    public function get_remaining_products() {
        $pack_product_no = $this->get_number_of_products();

        if ( '-1' === $pack_product_no ) {
            return '-1';
        }

        $remaining_product = $pack_product_no - $this->get_published_product_count();
        $remaining_product = $remaining_product < 0 ? 0 : $remaining_product;

        return $remaining_product;
    }

    /**
     * Check if vendor can renew a package
     *
     * @param integer $pack_id
     *
     * @return boolean
     */
    public function can_renew_package( $pack_id ) {
        $date            = date( 'Y-m-d', strtotime( current_time( 'mysql' ) ) );
        $validation_date = date( 'Y-m-d', strtotime( $this->get_pack_end_date() ) );

        $datetime1 = new DateTime( $date );
        $datetime2 = new DateTime( $validation_date );

        $interval = $datetime1->diff( $datetime2 );
        $interval = $interval->format( '%r%d' );

        if ( (int) $interval <= 3 && (int) $interval >= 0 && ( $this->get_id() == $product_id ) ) {
            return true;
        }

        return false;
    }

    /**
     * Vendor has recurring subscription pack
     *
     * @return boolean
     */
    public function has_recurring_pack() {
        $status = get_user_meta( $this->get_vendor(), '_customer_recurring_subscription', true );

        if ( 'active' === $status ) {
            return true;
        }

        return false;
    }
}