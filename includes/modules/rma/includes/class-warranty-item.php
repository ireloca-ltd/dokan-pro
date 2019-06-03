<?php

/**
* className
*/
class Dokan_Warranty_Item {

    public $item_id;
    public $type;
    public $label;
    public $addons;
    public $addon_selected;
    public $addon_default;
    public $length;
    public $duration_value;
    public $duration_type;
    public $no_warranty_option;
    public $order_id;


    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     *
     * @param integer $item_id
     */
    public function __construct( $item_id ) {
        $warranty   = wc_get_order_item_meta( $item_id, '_dokan_item_warranty', true );
        $selected   = wc_get_order_item_meta( $item_id, '_dokan_item_warranty_selected', true );

        $this->item_id = $item_id;
        $this->addon_selected = ( $selected ) ? $selected : false;

        if ( !$warranty ) {
            $this->type = 'no_warranty';
            return;
        }

        foreach ( $warranty as $key => $value ) {
            switch ( $key ) {
                case 'value':
                    $this->duration_value = $value;
                    break;

                case 'duration':
                    $this->duration_type = $value;
                    break;

                case 'default':
                    $this->addon_default = $value;
                    break;

                default:
                    $this->$key = $value;
                    break;
            }
        }
    }

    /**
     * Get order id
     *
     * @return integer $order_id
     */
    public function get_order_id() {
        global $wpdb;

        if ( ! $this->order_id ) {
            $this->order_id = $wpdb->get_var( $wpdb->prepare(
                "SELECT order_id
                FROM {$wpdb->prefix}woocommerce_order_items
                WHERE order_item_id = %d",
                $this->item_id
            ));
        }

        return $this->order_id;
    }

    /**
     * Get quantity remaining
     *
     * @return integer
     */
    public function get_quantity_remaining() {
        global $wpdb;

        $request_table      = $wpdb->prefix . 'dokan_rma_request';
        $request_item_table = $wpdb->prefix . 'dokan_rma_request_product';
        $qty                = wc_get_order_item_meta( $this->item_id, '_qty', true );;
        $product_id         = wc_get_order_item_meta( $this->item_id, '_product_id', true );
        $order_id           = $this->get_order_id();

        $sql    = "SELECT SUM( rit.quantity ) as total FROM {$request_item_table} as rit LEFT JOIN {$request_table} as rt ON rt.id=rit.request_id WHERE rt.order_id='$order_id' AND rit.product_id='$product_id'";
        $result = $wpdb->get_row( $sql, ARRAY_A );

        return (int)( $qty - $result['total'] );
    }

    /**
     * Check if has warranty
     *
     * @return boolean
     */
    public function has_warranty() {
        $has_warranty   = false;
        $remaining      = $this->get_quantity_remaining();

        if ( $remaining < 1 ) {
            return $has_warranty;
        }

        if ( $this->type == 'included_warranty' ) {
            if ( 'lifetime' === $this->length_value ) {
                $has_warranty = true;
            } else {
                $now    = current_time( 'timestamp' );
                $expiry = $this->get_expiry();

                if ( ! $expiry || $now < $expiry ) {
                    $has_warranty = true;
                }
            }
        } elseif ( $this->type == 'addon_warranty' ) {
            if ( isset( $this->addon_settings[ $this->addon_selected ] ) ) {
                $addon  = $this->addon_settings[ $this->addon_selected ];
                $now    = current_time( 'timestamp' );
                $expiry = $this->get_expiry( $addon['length'], $addon['duration'] );

                if ( !$expiry || $now < $expiry ) {
                    $has_warranty = true;
                }
            }
        }

        return $has_warranty;
    }

    /**
     * Get the warranty's expiration date.
     *
     * @param string $duration_value
     * @param string $duration_type
     *
     * @return bool|int
     */
    public function get_expiry( $duration_value = '', $duration_type = '' ) {
        $expiry         = false;
        $order          = wc_get_order( $this->get_order_id() );

        if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
            $completed_date = get_post_meta( $order->id, '_completed_date', true);
        } else {
            $completed_date = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;
        }

        if ( empty( $duration_value ) ) {
            $duration_value = $this->duration_value;
        }

        if ( empty( $duration_type ) ) {
            $duration_type = $this->duration_type;
        }

        if ( $completed_date ) {
            $expiry = strtotime( $completed_date . ' +'. $duration_value .' '. $duration_type );
        }

        return $expiry;
    }

}
