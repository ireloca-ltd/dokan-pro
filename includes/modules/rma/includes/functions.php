<?php

/**
 * Warranty Type
 *
 * @since 1.0.0
 *
 * @return array|string
 */
function dokan_rma_warranty_type( $type = '' ) {
    $warranty_type = apply_filters( 'dokan_rma_warranty_type', [
        'no_warranty'       => __( 'No Warranty', 'dokan' ),
        'included_warranty' => __( 'Warranty Included', 'dokan' ),
        'addon_warranty'    => __( 'Warranty as Add-On', 'dokan' )
    ] );

    if ( ! empty( $type ) ) {
        return isset( $warranty_type[$type] ) ? $warranty_type[$type] : '';
    }

    return $warranty_type;
}

/**
 * Warranty Length if included warranty
 *
 * @since 1.0.0
 *
 * @return string | Array
 */
function dokan_rma_warranty_length( $length = '' ) {
    $warranty_length = apply_filters( 'dokan_rma_warranty_length', [
        'limited'  => __( 'Limited', 'dokan' ),
        'lifetime' => __( 'Lifetime', 'dokan' )
    ] );

    if ( ! empty( $length ) ) {
        return isset( $warranty_length[$length] ) ? $warranty_length[$length] : '';
    }

    return $warranty_length;
}

/**
 * Warranty Length duration if included warranty
 *
 * @since 1.0.0
 *
 * @return string | Array
 */
function dokan_rma_warranty_length_duration( $duration = '' ) {
    $warranty_length_duration = [
        'days'   => __( 'Days', 'dokan' ),
        'weeks'  => __( 'Weeks', 'dokan' ),
        'months' => __( 'Months', 'dokan' ),
        'years'  => __( 'Years', 'dokan' )
    ];

    if ( ! empty( $duration ) ) {
        return isset( $warranty_length_duration[$duration] ) ? $warranty_length_duration[$duration] : '';
    }

    return $warranty_length_duration;
}

/**
 * Get refund Reasons formatted
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_rma_refund_reasons( $reason = '' ) {
    $reasons = dokan_get_option( 'rma_reasons', 'dokan_rma', [] );

    if ( ! empty( $reasons ) ) {
        $reasons = wp_list_pluck( $reasons, 'value', 'id' );
    }

    if ( $reason ) {
        return isset( $reasons[$reason] ) ? $reasons[$reason] : '';
    }

    return $reasons;
}

/**
 * Get duration value
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_rma_get_duration_value( $duration, $value = 0 ) {
    $unit = dokan_rma_warranty_length_duration( $duration );

    if ( 1 == $value ) {
        $unit = rtrim( $unit, 's' );
    }

    return $unit;
}

/**
 * Get the warranty validity date based on the order date and warranty duration
 *
 * @since 1.0.0
 *
 * @param string $order_date
 * @param int $warranty_duration
 * @param string $warranty_unit
 *
 * @return string
 */
function dokan_rma_get_date( $order_date, $warranty_duration, $warranty_unit ) {
    $order_time     = strtotime( $order_date );
    $expired_date   = false;

    $order_date = array(
        'month'     => date( 'n', $order_time ),
        'day'       => date( 'j', $order_time ),
        'year'      => date( 'Y', $order_time )
    );

    if ( $warranty_unit == 'days' ) {

        $expired_time = $order_time + $warranty_duration*86400;
        $expired_date = date( 'Y-m-d', $expired_time )." 23:59:59";
        $expired_time = strtotime( $expired_date );

    } elseif ( $warranty_unit == 'weeks' ) {

        $add = (86400 * 7) * $warranty_duration;
        $expired_time = $order_time + $add;
        $expired_date = date( 'Y-m-d', $expired_time )." 23:59:59";
        $expired_time = strtotime( $expired_date );

    } elseif ( $warranty_unit == 'months' ) {
        $warranty_day   = $order_date['day'];
        $warranty_month = $order_date['month'] + $warranty_duration;
        $warranty_year  = $order_date['year'] + ( $warranty_month / 12 );
        $warranty_month = $warranty_month % 12;

        if ( ( $warranty_month == 2 ) && ( $warranty_day > 28 ) ) $warranty_day = 29;

        if ( checkdate( $warranty_month, $warranty_day, $warranty_year ) ) {
            $expired_time = mktime( 23, 59, 59, $warranty_month, $warranty_day, $warranty_year );
        } else {
            $expired_time = mktime( 23, 59, 59, $warranty_month, ( $warranty_day - 1 ) , $warranty_year );
        }
    } elseif ( $warranty_unit == 'years' ) {
        $warranty_year = $order_date['year'] + $warranty_duration;

        if ( checkdate( $order_date['month'], $order_date['day'], $warranty_year ) ) {
            $expired_time = mktime( 23, 59, 59, $order_date['month'], $order_date['day'], $warranty_year );
        } else {
            $expired_time = mktime( 23, 59, 59, $order_date['month'], ($order_date['day'] - 1) , $warranty_year );
        }
    }

    if ( $expired_time ) {
        return date_i18n( get_option( 'date_format' ), $expired_time );
    }

    return '-';
}

/**
 * Strip slashes from array value recursively
 *
 * @param string $value
 *
 * @return string
 */
function dokan_stripslashes( $value ) {
    return stripslashes( $value );
}

/**
 * Get warranty details from item
 *
 * @param array $item
 *
 * @return array
 */
function dokan_get_order_item_warranty( $item ) {
    $meta       = $item['item_meta'];
    $warranty   = array('type' => 'no_warranty');

    foreach ( $meta as $key => $value ) {
        $value = version_compare( WC_VERSION, '3.0', '<' ) ? stripslashes( $value[0] ) : $value;

        if ( $key == '_dokan_item_warranty' ) {
            $warranty = array_merge( $warranty, (array) maybe_unserialize( $value ) );
        } elseif ( $key == '_dokan_item_warranty_selected' ) {
            $warranty['dokan_warranty_idx'] = $value;
        }
    }

    return $warranty;
}

/**
 * Get warranty expiry time in order items
 *
 * @param array $warranty
 * @param object $order
 *
 * @return string
 */
function dokan_get_warranty_duration_string( $warranty, $order ) {
    if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
        $completed = get_post_meta( $order->id, '_completed_date', true );
    } else {
        $completed = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;
    }

    if ( empty( $completed ) ) {
        $completed = false;
    }

    $order_paid_date = $order->get_date_paid();
    $order_paid_date = $order_paid_date ? $order_paid_date->date( 'Y-m-d H:i:s' ) : '';

    if ( $warranty['type'] == 'no_warranty' ) {
        $warranty_string = __( 'Product has no warranty', 'dokan' );
    } elseif ( $warranty['type'] == 'included_warranty' ) {
        if ( $warranty['length'] == 'lifetime' ) {
            $warranty_string = __( 'Lifetime', 'dokan' );
        } else {
            $order_date         = $completed ? $completed : $order_paid_date;
            $warranty_string    = __( 'Expiry Date: ', 'dokan' ) . dokan_rma_get_date( $order_date, $warranty['length_value'], $warranty['length_duration'] );
        }
    } elseif ( $warranty['type'] == 'addon_warranty' ) {
        $idx                = $warranty['dokan_warranty_idx'];
        $warranty_string    = '';

        if ( isset( $warranty['addon_settings'][$idx] ) ) {
            $addon           = $warranty['addon_settings'][$idx];
            $order_date      = $completed ? $completed : $order_paid_date;
            $warranty_string = __( 'Expiry Date: ', 'dokan' ) . dokan_rma_get_date( $order_date, $addon['length'], $addon['duration'] );
        }
    }

    return $warranty_string;
}


/**
 * Get the formatted warranty string of the given product
 *
 * @param int   $product_id
 * @param array $warranty
 *
 * @return string
 */
function dokan_get_warranty_string( $product_id = 0, $warranty = null ) {

    if ( empty( $warranty ) || $warranty['type'] == 'no_warranty' ) {
        $string = __( 'No warranty', 'dokan' );
    } elseif ( $warranty['type'] == 'included_warranty' ) {
        if ( $warranty['length'] == 'lifetime' ) {
            $string = __( 'Lifetime Warranty Included', 'dokan' );
        } else {
            $duration = dokan_rma_get_duration_value( $warranty['length_duration'], $warranty['length_value'] );
            $string   = sprintf( __( 'Warranty Included (%d %s)', 'dokan' ), $warranty['length_value'], $duration );
        }
    } else {
        $string = 'Add-on Warranty: ';
        foreach ( (array)$warranty['addon_settings'] as $addon ) {
            $duration   = dokan_rma_get_duration_value( $addon['duration'], $addon['length'] );
            $amount     = wc_price( $addon['price'] );
            $string     .= sprintf( __( '%d %s for %s; ', 'dokan' ), $addon['length'], $duration, $amount );
        }
    }

    return apply_filters( 'dokan_get_warranty_string', $string, $product_id, $warranty );
}

/**
 * Warranty request status
 *
 * @since 1.0.0
 *
 * @param string $status
 *
 * @return array|String
 */
function dokan_warranty_request_status( $status = '' ) {
    $statuses = apply_filters( 'dokan_warranty_request_status', [
        'new'        => __( 'New', 'dokan' ),
        'processing' => __( 'Processing', 'dokan' ),
        'completed'  => __( 'Completed', 'dokan' ),
        'rejected'   => __( 'Rejected', 'dokan' ),
        'reviewing'  => __( 'Reviewing', 'dokan' ),
    ] );

    if ( ! empty( $status ) ) {
        return ! empty( $statuses[$status] ) ? $statuses[$status] : '';
    }

    return $statuses;
}

/**
 * Get return request type
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_warranty_request_type( $type = '' ) {
    $types = apply_filters( 'dokan_warranty_request_type', [
        'replace' => __( 'Replace', 'dokan' ),
        'refund'  => __( 'Refund', 'dokan' ),
        'coupon'  => __( 'Refund as store credit', 'dokan' )
    ] );

    if ( 'no' == dokan_get_option( 'rma_enable_refund_request', 'dokan_rma', 'no' ) ) {
        unset( $types['refund'] );
    }

    if ( 'no' == dokan_get_option( 'rma_enable_coupon_request', 'dokan_rma', 'no' ) ) {
        unset( $types['coupon'] );
    }

    if ( ! empty( $type ) ) {
        return ! empty( $types[$type] ) ? $types[$type] : '';
    }

    return $types;
}

/**
 * Save warranty request
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_save_warranty_request( $data = [] ) {
    global $wpdb;

    $default = [
        'items'       => [],
        'order_id'    => 0,
        'vendor_id'   => 0,
        'customer_id' => get_current_user_id(),
        'type'        => 'replace',
        'reasons'     => '',
        'status'      => 'new',
        'details'     => '',
        'note'        => '',
        'created_at'  => current_time( 'mysql' )
    ];

    $data = dokan_parse_args( $data, $default );

    // Check if not order id passed
    if ( empty( $data['order_id'] ) ) {
        return new WP_Error( 'no-order-id', __( 'No order found', 'dokan' ) );
    }

    // Check if not have any vendor
    if ( empty( $data['vendor_id'] ) ) {
        return new WP_Error( 'no-vendor-id', __( 'No vendor found', 'dokan' ) );
    }

    // Check if customer select any product or not. If not select any product we do not proceed to create request
    if ( empty( $data['items'] ) ) {
        return new WP_Error( 'no-product-id', __( 'Please select some item for sending request', 'dokan' ) );
    }

    if ( empty( $data['type'] ) ) {
        return new WP_Error( 'no-type', __( 'Request type must be required', 'dokan' ) );
    }

    $request_table      = $wpdb->prefix . 'dokan_rma_request';
    $request_item_table = $wpdb->prefix . 'dokan_rma_request_product';

    $wpdb->insert(
        $request_table,
        [
            'order_id'    => $data['order_id'],
            'vendor_id'   => $data['vendor_id'],
            'customer_id' => $data['customer_id'],
            'type'        => $data['type'],
            'status'      => $data['status'],
            'reasons'     => $data['reasons'],
            'details'     => wp_kses_post( $data['details'] ),
            'note'        => $data['note'],
            'created_at'  => $data['created_at'],
        ],
        [ '%d', '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' ]
    );

    $request_id = $wpdb->insert_id;

    foreach ( $data['items'] as $item ) {
        $wpdb->insert(
            $request_item_table,
            [
                'request_id' => $request_id,
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'item_id'    => $item['item_id']
            ],
            [ '%d', '%d', '%d' ]
        );
    }

    if ( $request_id ) {
        do_action( 'dokan_rma_save_warranty_request', $data );

        return true;
    }

    return false;
}

/**
 * Update warranty requests
 *
 * @param array $data
 *
 * @return WP_Error | true
 */
function dokan_update_warranty_request( $data = [] ) {
    global $wpdb;

    if ( empty( $data['id'] ) ) {
        return new WP_Error( 'no-request-id', __( 'No request id found', 'dokan' ) );
    }

    $statuses      = dokan_warranty_request_status();
    $request_table = $wpdb->prefix . 'dokan_rma_request';

    $request = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$request_table} WHERE `id`=%d", $data['id'] ), ARRAY_A );
    $data    = dokan_parse_args( $data, $request );

    if ( ! in_array( $data['status'], array_keys( $statuses ) ) ) {
        return new WP_Error( 'no-valid-status', __( 'Your status is not valid', 'dokan' ) );
    }

    $result = $wpdb->update( $request_table, $data, [ 'id' => $data['id'] ] );

    if ( ! $result ) {
        return new WP_Error( 'status-not-updated', __( 'Status not updated, Please try again', 'dokan' ) );
    }

    return $result;
}

/**
 * Dokan get warranty request
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_get_warranty_request( $data = [] ) {
    global $wpdb;

    $default = [
        'id'      => 0,
        'number'  => 20,
        'offset'  => 0,
        'orderby' => 'created_at',
        'order'   => 'desc',
        'count'   => false
    ];

    $data = dokan_parse_args( $data, $default );

    $request_table      = $wpdb->prefix . 'dokan_rma_request';
    $request_item_table = $wpdb->prefix . 'dokan_rma_request_product';

    if ( $data['count'] ) {
        $sql = "SELECT count('id') as total_count FROM {$request_table} as rt WHERE 1=1";
    } else {
        $sql = "SELECT rt.*, GROUP_CONCAT( rit.product_id SEPARATOR ',') AS 'products', GROUP_CONCAT( rit.quantity SEPARATOR ', ') AS 'quantity', GROUP_CONCAT( rit.item_id SEPARATOR ', ') AS 'item_id' FROM {$request_table} as rt INNER JOIN {$request_item_table} as rit ON rt.id=rit.request_id WHERE 1=1";
    }

    if ( ! empty( $data['type'] ) ) {
        $sql .= " AND rt.type='{$data['type']}'";
    }

    if ( ! empty( $data['customer_id'] ) ) {
        $sql .= " AND rt.customer_id='{$data['customer_id']}'";
    }

    if ( ! empty( $data['order_id'] ) ) {
        $sql .= " AND rt.order_id='{$data['order_id']}'";
    }

    if ( ! empty( $data['vendor_id'] ) ) {
        $sql .= " AND rt.vendor_id='{$data['vendor_id']}'";
    }

    if ( ! empty( $data['reasons'] ) ) {
        $sql .= " AND rt.reasons='{$data['reasons']}'";
    }

    if ( ! empty( $data['status'] ) ) {
        $sql .= " AND rt.status='{$data['status']}'";
    }

    if ( $data['id'] ) {
        $sql .= " AND rt.id='{$data['id']}'";
    }

    if ( ! $data['count'] ) {
        $sql .= " GROUP BY rt.id ORDER BY {$data['orderby']} {$data['order']} LIMIT {$data['offset']}, {$data['number']}";
    }

    if ( $data['count'] || $data['id'] ) {
        return $wpdb->get_row( $sql, ARRAY_A );
    }

    return $wpdb->get_results( $sql, ARRAY_A );
}

/**
 * undocumented function
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_update_warranty_request_status( $id, $status ) {
    global $wpdb;

    $request_table = $wpdb->prefix . 'dokan_rma_request';
    $statuses      = dokan_warranty_request_status();

    if ( in_array( $status, array_keys( $statuses ) ) ) {
        return new WP_Error( 'no-valid-status', __( 'Your status is not valid', 'dokan' ) );
    }

    $result = $wpdb->update( $request_table,
        [
            'status' => $status
        ],
        [ 'id' => $id ],
        [ '%s' ], [ '%d' ] );

    if ( ! $result ) {
        return new WP_Error( 'status-not-updated', __( 'Status not updated, Please try again', 'dokan' ) );
    }

    return $result;
}

/**
 * Warranty request status count
 *
 * @since 1.0.0
 *
 * @return array
 */
function dokan_warranty_request_status_count() {
    global $wpdb;

    $vendor_id = dokan_get_current_user_id();
    $request_table      = $wpdb->prefix . 'dokan_rma_request';
    $cache_key = 'dokan-count-warranty-request-' . $vendor_id;
    $counts = wp_cache_get( $cache_key, 'dokan' );

    if ( false === $counts ) {
        // $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d GROUP BY post_status";
        $query = "SELECT status, COUNT( * ) AS num_request FROM {$request_table} WHERE `vendor_id` = '%s' GROUP BY status";
        $results = $wpdb->get_results( $wpdb->prepare( $query, $vendor_id ), ARRAY_A );

        $total = 0;
        foreach ( $results as $row ) {
            $counts[ $row['status'] ] = (int) $row['num_request'];
            $total += (int) $row['num_request'];
        }

        $counts['total'] = $total;
        wp_cache_set( $cache_key, $counts, 'dokan' );
    }

    return $counts;
}

/**
 * List filter for warranty request
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_warranty_request_listing_status_filter() {
    $permalink    = dokan_get_navigation_url( 'return-request' );
    $status_class = isset( $_GET['status'] ) ? $_GET['status'] : 'all';
    $post_counts  = dokan_warranty_request_status_count();
    $statuses     = dokan_warranty_request_status();

    // var_dump( $post_counts ); die();
    ?>
    <ul class="list-inline request-statuses-filter">
        <li<?php echo $status_class == 'all' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $permalink; ?>"><?php printf( __( 'All (%d)', 'dokan' ), $post_counts['total'] ); ?></a>
        </li>

        <?php if ( $post_counts ) {
            foreach ( $post_counts as $status_key => $count ) {
                if ( 'total' == $status_key ) {
                    continue;
                }
                ?>
                <li<?php echo $status_key == $status_class ? ' class="active"' : ''; ?>>
                    <a href="<?php echo add_query_arg( array( 'status' => $status_key ), $permalink ); ?>"><?php printf( __( '%s (%d)', 'dokan' ), $statuses[$status_key], $count ); ?></a>
                </li>
                <?php
            }
        }
        ?>
    </ul> <!-- .post-statuses-filter -->
    <?php
}

/**
 * Get quantity remainting
 *
 * @since 1.0.0
 *
 * @return void
 */
function dokan_get_product_qty_remaining( $order_id, $item_id ) {
    global $wpdb;

    $request_table      = $wpdb->prefix . 'dokan_rma_request';
    $request_item_table = $wpdb->prefix . 'dokan_rma_request_product';
    $qty                = wc_get_order_item_meta( $item_id, '_qty', true );;
    $product_id         = wc_get_order_item_meta( $item_id, '_product_id', true );

    // Can check if variation exists

    $sql    = "SELECT SUM( rit.quantity ) as total FROM {$request_item_table} as rit LEFT JOIN {$request_table} as rt ON rt.id=rit.request_id WHERE rt.order_id='$order_id' AND rit.product_id='$product_id'";
    $result = $wpdb->get_row( $sql, ARRAY_A );

    return (int)( $qty - $result['total'] );
}

/**
 * Check item has warranty or not
 *
 * @since 1.0.0
 *
 * @param integer $order_id
 * @param array $item
 *
 * @return void
 */
function dokan_check_item_has_warranty( $order_id, $item ) {
    $has_warranty   = false;
    $remaining      = dokan_get_product_qty_remaining( $order_id, $item->get_id() );


    if ( $remaining < 1 ) {
        return $has_warranty;
    }
}

function dokan_get_item_warranty_expiry( $duration_value = '', $duration_type = '' ) {
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

/**
 * Check and generate an unique random copuon code
 *
 * @since 1.0.0
 *
 * @return string
 */
function dokan_rma_generate_coupon_code() {
    global $wpdb;

    $chars = 'abcdefghijklmnopqrstuvwxyz01234567890';
    do {

        $code = '';
        for ( $x = 0; $x < 8; $x++ ) {
            $code .= $chars[ rand( 0, strlen( $chars )-1 ) ];
        }

        $check = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_title = %s AND post_type = 'shop_coupon'", $code ) );
        if ( $check == 0 ) {
            break;
        }

    } while ( true );

    return $code;
}










