<?php
/**
 * Refund base class
 *
 * @author wedDevs <info@wedevs.com>
 *
 * @since 2.4.11
 *
 * @package dokan
 */

class Dokan_Pro_Refund {

    /**
     * Initializes the Dokan_Template_Refund class
     *
     * Checks for an existing Dokan_Template_Refund instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Pro_Refund();
        }

        return $instance;
    }

    /**
     * Update refund status
     *
     * @since 2.4.11
     *
     * @param  integer $row_id
     * @param  integer $order_id
     * @param  string $status
     *
     * @return void
     */
    function update_status( $row_id, $status ) {
        global $wpdb;

        // 0 -> pending
        // 1 -> approve
        // 2 -> cancelled
        $wpdb->query( $wpdb->prepare(
            "UPDATE {$wpdb->prefix}dokan_refund
            SET status = %d WHERE id = %d",
            $status, $row_id
        ) );
    }

    /**
     * Insert an refund request
     *
     * @since 2.4.11
     *
     * @param  array  $data
     *
     * @return boolean
     */
    function insert_refund( $data = array() ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'dokan_refund';

        $postdata = array(
            'order_id'        => (int)$data['order_id'],
            'seller_id'       => (int)$data['seller_id'],
            'refund_amount'   => floatval( $data['refund_amount'] ),
            'refund_reason'   => $data['refund_reason'],
            'item_qtys'       => $data['line_item_qtys'],
            'item_totals'     => $data['line_item_totals'],
            'item_tax_totals' => $data['line_item_tax_totals'],
            'restock_items'   => $data['restock_refunded_items'],
            'date'            => current_time( 'mysql' ),
            'status'          => (int)$data['status'],
            'method'          => $data['api_refund'],
        );

        $format = array( '%d', '%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' );

        $wpdb->insert( $table_name, $postdata, $format );

        return $wpdb->insert_id;
    }

    /**
     * Check if has already pending refund request
     *
     * @since 2.4.11
     *
     * @return boolean
     */
    function has_pending_refund_request( $order_id ) {
        global $wpdb;

        $wpdb->dokan_refund = $wpdb->prefix . 'dokan_refund';

        $sql    = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}dokan_refund WHERE status = %d AND order_id = %d", 0, $order_id );
        $status = $wpdb->get_results( $sql );

        if ( $status ) {
            return true;
        }

        return false;
    }

    /**
     * Get refund request
     *
     * @since 2.4.11
     *
     * @param  integer   $status
     * @param  integer   $limit
     * @param  integer   $offset
     *
     * @return array
     */
    function get_refund_requests( $status = 0, $limit = 10, $offset = 0 ) {
        global $wpdb;

        $sql    = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}dokan_refund WHERE status = %d LIMIT %d, %d", $status, $offset, $limit );
        $result = $wpdb->get_results( $sql );

        return $result;
    }

    /**
     * Delete a refund request
     *
     * @since 2.4.11
     *
     * @param  integer
     *
     * @return void
     */
    function delete_refund( $id ) {
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}dokan_refund WHERE id = %d", $id ) );
    }

    /**
     * Get status code by status type
     *
     * @since 2.4.11
     *
     * @param  string
     *
     * @return integer
     */
    function get_status_code( $status ) {
        switch ( $status ) {
            case 'pending':
                return 0;
                break;

            case 'completed':
                return 1;
                break;

            case 'cancelled':
                return 2;
                break;
        }
    }

    /**
     * Print status messages
     *
     * @since 2.4.11
     *
     * @param  string  $status
     *
     * @return void
     */
    function request_status( $status ) {
        switch ( $status ) {
            case 0:
                return '<span class="label label-danger">' . __( 'Pending Reivew', 'dokan' ) . '</span>';
                break;

            case 1:
                return '<span class="label label-warning">' . __( 'Accepted', 'dokan' ) . '</span>';
                break;
        }
    }
}