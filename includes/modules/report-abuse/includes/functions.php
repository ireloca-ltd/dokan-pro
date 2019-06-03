<?php

/**
 * Include Dokan Report Abuse template
 *
 * @since 1.0.0
 *
 * @param string $name
 * @param array  $args
 *
 * @return void
 */
function dokan_report_abuse_template( $name, $args = [] ) {
    dokan_get_template( "$name.php", $args, DOKAN_REPORT_ABUSE_VIEWS, trailingslashit( DOKAN_REPORT_ABUSE_VIEWS ) );
}

/**
 * Create abuse report
 *
 * @since 2.9.8
 *
 * @param array $args
 *
 * @return array
 */
function dokan_report_abuse_create_report( $args ) {
    global $wpdb;

    $defaults = [
        'reason'        => '',
        'product_id'     => 0,
        'customer_id'    => 0,
        'customer_name'  => '',
        'customer_email' => '',
        'description'    => '',
    ];

    $args = wp_parse_args( $args, $defaults );

    $report       = [];
    $placeholders = [];

    if ( empty( $args['reason'] ) ) {
        return new WP_Error( 'missing_reason', esc_html__( 'Missing reason param.', 'dokan' ) );
    }

    $args['reason'] = wp_trim_words( $args['reason'], 191 );

    $report['reason'] = $args['reason'];
    $placeholders[]   = '%s';

    if ( empty( $args['product_id'] ) ) {
        return new WP_Error( 'missing_product_id', esc_html__( 'Missing product_id param.', 'dokan' ) );
    }

    $product = wc_get_product( $args['product_id'] );

    if ( ! $product instanceof WC_Product ) {
        return new WP_Error( 'invalid_product_id', esc_html__( 'Product not found.', 'dokan' ) );
    }

    $report['product_id'] = $args['product_id'];
    $placeholders[]       = '%d';

    $vendor = dokan_get_vendor_by_product( $product );

    $report['vendor_id'] = $vendor->get_id();
    $placeholders[]      = '%d';

    $customer = null;

    if ( ! empty( $args['customer_id'] ) ) {
        $customer    = new WC_Customer( $args['customer_id'] );
        $customer_id = $customer->get_id();

        if ( ! $customer_id ) {
            return new WP_Error( 'invalid_customer_id', esc_html__( 'Customer not found.', 'dokan' ) );
        }
    }

    $option = get_option( 'dokan_report_abuse', [] );

    if ( isset( $option['reported_by_logged_in_users_only'] ) && 'on' === $option['reported_by_logged_in_users_only'] ) {
        if ( empty( $customer ) ) {
            return new WP_Error( 'user_must_logged_in', esc_html__( 'User must login to report an abuse.', 'dokan' ) );
        }

        $report['customer_id'] = $customer_id;
        $placeholders[]        = '%d';
    } else if ( $customer ) {
        $report['customer_id'] = $customer_id;
        $placeholders[]        = '%d';
    } else {
        if ( empty( $args['customer_name'] ) ) {
            return new WP_Error( 'missing_field', esc_html__( 'customer_name is required.', 'dokan' ) );
        } else if ( empty( $args['customer_email'] ) ) {
            return new WP_Error( 'missing_field', esc_html__( 'customer_email is required.', 'dokan' ) );
        } else if ( ! is_email( $args['customer_email'] ) ) {
            return new WP_Error( 'missing_field', esc_html__( 'Invalid customer_email.', 'dokan' ) );
        }

        $report['customer_name'] = wp_trim_words( $args['customer_name'], 191 );
        $placeholders[]          = '%s';

        $report['customer_email'] = wp_trim_words( $args['customer_email'], 100 );
        $placeholders[]           = '%s';
    }

    if ( ! empty( $args['description'] ) ) {
        $report['description'] = $args['description'];
        $placeholders[]        = '%s';
    }

    $report['reported_at'] = current_time( 'mysql' );
    $placeholders[]       = '%s';

    $inserted = $wpdb->insert(
        $wpdb->prefix . 'dokan_report_abuse_reports',
        $report,
        $placeholders
    );

    if ( ! $inserted ) {
        return new WP_Error( 'unable_to_create_report', esc_html__( 'Unable to create abuse report.', 'dokan' ) );
    }

    $report = $wpdb->get_row(
        "select * from {$wpdb->prefix}dokan_report_abuse_reports where id = {$wpdb->insert_id}"
    );

    /**
     * Fires after created an abuse report
     *
     * @since 2.9.8
     *
     * @param object            $report
     * @param \WC_Product       $product
     * @param \Dokan_Vendor     $vendor
     * @param null|\WC_Customer $customer
     */
    do_action( 'dokan_report_abuse_created_report', $report, $product, $vendor, $customer );

    return $report;
}

/**
 * Get module admin settings
 *
 * @since 2.9.8
 *
 * @return array
 */
function dokan_report_abuse_get_option() {
    $option = get_option( 'dokan_report_abuse', [] );

    if ( empty( $option['reported_by_logged_in_users_only'] ) || 'on' !== $option['reported_by_logged_in_users_only'] ) {
        $option['reported_by_logged_in_users_only'] = 'off';
    }

    if ( empty( $option['abuse_reasons'] ) ) {
        $option['abuse_reasons'] = [];

        $option['abuse_reasons'][] = [
            'id'    => 'other',
            'value' => esc_html__( 'Other', 'dokan' ),
        ];
    }

    return $option;
}

/**
 * Report Abuse Form
 *
 * @since 2.9.8
 *
 * @param array $args
 * @param bool  $echo
 *
 * @return void|string
 */
function dokan_report_abuse_report_form( $args = [], $echo = false ) {
    $defaults = [
        'text'                 => esc_html__( 'Why are you reporting this?', 'dokan' ),
        'id'                   => 'dokan-report-abuse-form',
        'option_list_classes'  => '',
        'option_label_classes' => '',
    ];

    $args   = wp_parse_args( $args, $defaults );
    $option = dokan_report_abuse_get_option();

    $args = array_merge( $args, $option );

    if ( $echo ) {
        dokan_report_abuse_template( 'report-form', $args );
    } else {
        ob_start();
        dokan_report_abuse_template( 'report-form', $args );
        return ob_get_clean();
    }
}

/**
 * Get abuse reports
 *
 * @since 2.9.8
 *
 * @param array $args
 *
 * @return array
 */
function dokan_report_abuse_get_reports( $args = [] ) {
    global $wpdb;

    $defaults = [
        'ids'        => [],
        'id'         => 0,
        'reason'     => '',
        'product_id' => 0,
        'vendor_id'  => 0,
        'order_by'   => 'id',
        'order'      => 'desc',
        'per_page'   => 20,
        'page'       => 1,
        'count'      => false,
    ];

    $args = wp_parse_args( $args, $defaults );

    if ( ! $args['count'] ) {
        $sql = 'select * from ' . $wpdb->prefix . 'dokan_report_abuse_reports where 1=1';
    } else {
        $sql = 'select count(*) from ' . $wpdb->prefix . 'dokan_report_abuse_reports where 1=1';
    }

    if ( ! empty( $args['ids'] ) && is_array( $args['ids'] ) ) {
        $ids = array_filter( $args['ids'], 'absint' );

        $sql .= ' and id in (' . implode( ',', $ids ) . ')';
    } else if ( ! empty( $args['id'] ) ) {
        $sql .= $wpdb->prepare(
            ' and id = %d', $args['id']
        );
    }

    if ( ! empty( $args['reason'] ) ) {
        $option        = dokan_report_abuse_get_option();
        $abuse_reasons = $option['abuse_reasons'];
        $no_of_reasons = count( $abuse_reasons );

        for ( $i = 0; $i < $no_of_reasons; $i++ ) {
            if ( $abuse_reasons[$i]['value'] === $args['reason'] ) {
                $sql .= $wpdb->prepare(
                    ' and reason = %s', $args['reason']
                );

                break;
            }
        }
    }

    if ( ! empty( $args['vendor_id'] ) ) {
        $sql .= $wpdb->prepare(
            ' and vendor_id = %d', $args['vendor_id']
        );
    }

    if ( ! empty( $args['product_id'] ) ) {
        $sql .= $wpdb->prepare(
            ' and product_id = %d',
            absint( $args['product_id'] )
        );
    }

    if ( ! $args['count'] ) {
        if ( in_array( $args['order_by'], [ 'id', 'reason', 'product_id', 'vendor_id', 'reported_at' ] ) && in_array( strtolower( $args['order'] ) , [ 'asc', 'desc' ] ) ) {
            $sql .= ' order by ' . $args['order_by'] . ' ' . $args['order'];
        }

        $offset = $args['per_page'] * ( $args['page'] - 1 );

        $sql .= $wpdb->prepare(
            ' limit %d offset %d',
            $args['per_page'], $offset
        );
    }

    if ( $args['count'] ) {
        return $wpdb->get_var($sql);
    }

    $results = $wpdb->get_results( $sql );

    $reports = [];

    foreach ( $results as $i => $result ) {
        $reports[ $i ]['id']     = absint( $result->id );
        $reports[ $i ]['reason'] = $result->reason;

        $product = wc_get_product( $result->product_id );
        $reports[ $i ]['product'] = [
            'id'        => $product->get_id(),
            'title'     => $product->get_title(),
            'admin_url' => admin_url( sprintf( 'post.php?post=%d&action=edit', $product->get_id() ) ),
        ];

        $vendor = dokan_get_vendor( $result->vendor_id );
        $reports[ $i ]['vendor'] = [
            'id'        => $vendor->get_id(),
            'name'      => $vendor->get_shop_name(),
            'admin_url' => admin_url( sprintf( 'user-edit.php?user_id=%d', $vendor->get_id() ) ),
        ];

        if ( $result->customer_id ) {
            $customer       = new WC_Customer( $result->customer_id );
            $customer_name  = $customer->get_username();
            $customer_email = $customer->get_email();
            $admin_url      = admin_url( sprintf( 'user-edit.php?user_id=%d', $customer->get_id() ) );
        } else {
            $customer_name  = $result->customer_name;
            $customer_email = $result->customer_email;
            $admin_url      = null;
        }

        $reports[ $i ]['reported_by'] = [
            'id'        => absint( $result->customer_id ),
            'name'      => $customer_name,
            'email'     => $customer_email,
            'admin_url' => $admin_url,
        ];

        $reports[ $i ]['description'] = $result->description;
        $reports[ $i ]['reported_at'] = mysql_to_rfc3339( $result->reported_at );
    }

    if ( ! empty( $args['id'] ) && ! empty( $reports ) ) {
        $reports = $reports[0];
    }

    return $reports;
}

/**
 * Delete abuse reports
 *
 * @since 2.9.8
 *
 * @param array $ids
 *
 * @return int
 */
function dokan_report_abuse_delete_reports( $ids ) {
    global $wpdb;

    $ids = implode( ',', $ids );

    return $wpdb->query(
        "delete from {$wpdb->prefix}dokan_report_abuse_reports where id in ({$ids})"
    );
}
