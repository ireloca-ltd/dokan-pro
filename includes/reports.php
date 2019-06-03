<?php

/**
 * Returns the definitions for the reports and charts
 *
 * @since 1.0
 * @return array
 */
function dokan_get_reports_charts() {
    $charts = array(
        'title'  => __( 'Sales', 'dokan' ),
        'charts' => array(
            "overview"          => array(
                'title'       => __( 'Overview', 'dokan' ),
                'description' => '',
                'hide_title'  => true,
                'function'    => 'dokan_sales_overview',
                'permission'  => 'dokan_view_overview_report'
            ),
            "sales_by_day"      => array(
                'title'       => __( 'Sales by day', 'dokan' ),
                'description' => '',
                'function'    => 'dokan_daily_sales',
                'permission'  => 'dokan_view_daily_sale_report'
            ),
            "top_sellers"       => array(
                'title'       => __( 'Top selling', 'dokan' ),
                'description' => '',
                'function'    => 'dokan_top_sellers',
                'permission'  => 'dokan_view_top_selling_report'
            ),
            "top_earners"       => array(
                'title'       => __( 'Top earning', 'dokan' ),
                'description' => '',
                'function'    => 'dokan_top_earners',
                'permission'  => 'dokan_view_top_earning_report'
            ),
             "sales_statement"       => array(
                'title'       => __( 'Statement', 'dokan' ),
                'description' => '',
                'function'    => 'dokan_seller_sales_statement',
                'permission'  => 'dokan_view_statement_report'
            )
        )
    );

    return apply_filters( 'dokan_reports_charts', $charts );
}


/**
 * Seller sales statement
 *
 * @since
 *
 * @return
 */
function dokan_seller_sales_statement() {
    $start_date = date( 'Y-m-01', current_time('timestamp') );
    $end_date = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );

    if ( isset( $_GET['dokan_report_filter'] ) ) {
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];
    }
    ?>

    <form method="get" class="dokan-form-inline report-filter dokan-clearfix" action="">
        <div class="dokan-form-group">
            <label for="from"><?php _e( 'From:', 'dokan' ); ?></label> <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo date_i18n( get_option( 'date_format' ), strtotime( $start_date ) ); ?>" />
        </div>

        <div class="dokan-form-group">
            <label for="to"><?php _e( 'To:', 'dokan' ); ?></label>
            <input type="text" name="end_date" id="to" class="datepicker" readonly="readonly" value="<?php echo date_i18n( get_option( 'date_format' ), strtotime( $end_date ) ); ?>" />

            <input type="hidden" name="chart" value="sales_statement">
            <input type="submit" name="dokan_report_filter" class="dokan-btn dokan-btn-success dokan-btn-sm dokan-theme" value="<?php _e( 'Show', 'dokan' ); ?>" />
        </div>
        <input type="submit" name="dokan_statement_export_all"  class="dokan-btn dokan-right dokan-btn-sm dokan-btn-danger dokan-btn-theme" value="<?php esc_attr_e( 'Export All', 'dokan' ); ?>">
    </form>
    <?php
    global $wpdb;
    $vendor = dokan()->vendor->get( dokan_get_current_user_id() );
    $opening_balance = $vendor->get_balance( false, date( 'Y-m-d', strtotime( $start_date . ' -1 days' ) ) );
    $status = implode( "', '", dokan_withdraw_get_active_order_status() );

    $sql = "SELECT * from {$wpdb->prefix}dokan_vendor_balance WHERE vendor_id = %d AND DATE(balance_date) >= %s AND DATE(balance_date) <= %s AND ( ( trn_type = 'dokan_orders' AND status IN ('{$status}') ) OR trn_type IN ( 'dokan_withdraw', 'dokan_refund' ) ) ORDER BY balance_date";
    $statements = $wpdb->get_results( $wpdb->prepare( $sql, $vendor->id, $start_date, $end_date ) );
    ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php _e( 'Balance Date', 'dokan' ); ?></th>
                <th><?php _e( 'Trn Date', 'dokan' ); ?></th>
                <th><?php _e( 'ID', 'dokan' ); ?></th>
                <th><?php _e( 'Type', 'dokan' ); ?></th>
                <th><?php _e( 'Debit', 'dokan' ); ?></th>
                <th><?php _e( 'Credit', 'dokan' ); ?></th>
                <th><?php _e( 'Balance', 'dokan' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if( $opening_balance ) { ?>
                <tr>
                    <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $start_date ) ); ?></td>
                    <td><?php echo '--'; ?></td>
                    <td><?php echo '--'; ?></td>
                    <td><?php _e( 'Opening Balance' , 'dokan' ); ?></td>
                    <td><?php echo '--'; ?></td>
                    <td><?php echo '--'; ?></td>
                    <td><?php echo wc_price( $opening_balance ); ?></td>
                </tr>
            <?php } if( count( $statements ) ) {
                $total_debit = 0;
                $total_credit = 0;
                $balance = $opening_balance;
                foreach ( $statements as $statement ) {
                    $total_debit += $statement->debit;
                    $total_credit += $statement->credit;
                    $balance += $statement->debit - $statement->credit;

                    switch ( $statement->trn_type ) {
                        case 'dokan_orders':
                            $type = __( 'Order', 'dokan' );
                            $url  = wp_nonce_url( add_query_arg( array( 'order_id' => $statement->trn_id ), dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' );
                            break;

                        case 'dokan_withdraw':
                            $type = __( 'Withdraw', 'dokan' );
                            $url  = add_query_arg( array( 'type' => 'approved' ), dokan_get_navigation_url('withdraw') );
                            break;

                        case 'dokan_refund':
                            $type = __( 'Refund', 'dokan' );
                            $url  = wp_nonce_url( add_query_arg( array( 'order_id' => $statement->trn_id ), dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' );
                            break;
                    }
                    ?>
                    <tr>
                        <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $statement->balance_date ) ); ?></td>
                        <td><?php echo date_i18n( get_option( 'date_format' ), strtotime( $statement->trn_date ) ); ?></td>
                        <td><a href="<?php echo $url; ?>">#<?php echo $statement->trn_id; ?></a></td>
                        <td><?php echo $type; ?></td>
                        <td><?php echo wc_price( $statement->debit ); ?></td>
                        <td><?php echo wc_price( $statement->credit ); ?></td>
                        <td><?php echo wc_price( $balance ); ?></td>
                    </tr>
                    <?php

                }
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><b><?php _e( 'Total :', 'dokan' ); ?></b></td>
                    <td><b><?php echo wc_price( $total_debit ); ?></b></td>
                    <td><b><?php echo wc_price( $total_credit ); ?></b></td>
                    <td><b><?php echo wc_price( $balance ); ?></b></td>
                </tr>
            <?php
            }
            else {
                ?>
                <tr>
                    <td colspan="6"><?php _e( 'No Result found!', 'dokan' ); ?></td>
                </tr>
                <?php
            }

            ?>

        </tbody>
    </table>

    <?php
}


/**
 * Generate SQL query and fetch the report data based on the arguments passed
 *
 * This function was cloned from WC_Admin_Report class.
 *
 * @since 1.0
 *
 * @global WPDB $wpdb
 * @global WP_User $current_user
 * @param array $args
 * @param string $start_date
 * @param string $end_date
 * @return obj
 */
function dokan_get_order_report_data( $args = array(), $start_date, $end_date, $current_user = false ) {
    global $wpdb;

    if ( !$current_user ) {
        $current_user = dokan_get_current_user_id();
    }

    $defaults = array(
        'data'         => array(),
        'where'        => array(),
        'where_meta'   => array(),
        'query_type'   => 'get_row',
        'group_by'     => '',
        'order_by'     => '',
        'limit'        => '',
        'filter_range' => false,
        'nocache'      => false,
        'debug'        => false
    );

    $args = wp_parse_args( $args, $defaults );

    extract( $args );

    if ( empty( $data ) )
        return false;

    $select = array();

    foreach ( $data as $key => $value ) {
        $distinct = '';

        if ( isset( $value['distinct'] ) )
            $distinct = 'DISTINCT';

        if ( $value['type'] == 'meta' )
            $get_key = "meta_{$key}.meta_value";
        elseif( $value['type'] == 'post_data' )
            $get_key = "posts.{$key}";
        elseif( $value['type'] == 'order_item_meta' )
            $get_key = "order_item_meta_{$key}.meta_value";
        elseif( $value['type'] == 'order_item' )
            $get_key = "order_items.{$key}";

        if ( $value['function'] )
            $get = "{$value['function']}({$distinct} {$get_key})";
        else
            $get = "{$distinct} {$get_key}";

        $select[] = "{$get} as {$value['name']}";
    }

    $query['select'] = "SELECT " . implode( ',', $select );
    $query['from']   = "FROM {$wpdb->posts} AS posts";

    // Joins
    $joins         = array();
    $joins['do']  = "LEFT JOIN {$wpdb->prefix}dokan_orders AS do ON posts.ID = do.order_id";

    foreach ( $data as $key => $value ) {
        if ( $value['type'] == 'meta' ) {

            $joins["meta_{$key}"] = "LEFT JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";

        } elseif ( $value['type'] == 'order_item_meta' ) {

            $joins["order_items"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_items.order_id";
            $joins["order_item_meta_{$key}"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";

        } elseif ( $value['type'] == 'order_item' ) {

            $joins["order_items"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id";

        }
    }

    if ( ! empty( $where_meta ) ) {
        foreach ( $where_meta as $value ) {
            if ( ! is_array( $value ) )
                continue;

            $key = is_array( $value['meta_key'] ) ? $value['meta_key'][0] : $value['meta_key'];

            if ( isset( $value['type'] ) && $value['type'] == 'order_item_meta' ) {

                $joins["order_items"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id";
                $joins["order_item_meta_{$key}"] = "LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_{$key} ON order_items.order_item_id = order_item_meta_{$key}.order_item_id";

            } else {
                // If we have a where clause for meta, join the postmeta table
                $joins["meta_{$key}"] = "LEFT JOIN {$wpdb->postmeta} AS meta_{$key} ON posts.ID = meta_{$key}.post_id";
            }
        }
    }

    $query['join'] = implode( ' ', $joins );

    $query['where']  = "
        WHERE   posts.post_type     = 'shop_order'
        AND     posts.post_status   != 'trash'
        AND     do.seller_id = {$current_user}
        AND     do.order_status IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) . "')
        ";

    if ( $filter_range ) {
        $query['where'] .= "
            AND     DATE(post_date) >= '" . $start_date . "'
            AND     DATE(post_date) <= '" . $end_date . "'
        ";
    }

    foreach ( $data as $key => $value ) {
        if ( $value['type'] == 'meta' ) {

            $query['where'] .= " AND meta_{$key}.meta_key = '{$key}'";

        } elseif ( $value['type'] == 'order_item_meta' ) {

            $query['where'] .= " AND order_items.order_item_type = '{$value['order_item_type']}'";
            $query['where'] .= " AND order_item_meta_{$key}.meta_key = '{$key}'";

        }
    }

    if ( ! empty( $where_meta ) ) {
        $relation = isset( $where_meta['relation'] ) ? $where_meta['relation'] : 'AND';

        $query['where'] .= " AND (";

        foreach ( $where_meta as $index => $value ) {
            if ( ! is_array( $value ) )
                continue;

            $key = is_array( $value['meta_key'] ) ? $value['meta_key'][0] : $value['meta_key'];

            if ( strtolower( $value['operator'] ) == 'in' ) {
                if ( is_array( $value['meta_value'] ) )
                    $value['meta_value'] = implode( "','", $value['meta_value'] );
                if ( ! empty( $value['meta_value'] ) )
                    $where_value = "IN ('{$value['meta_value']}')";
            } else {
                $where_value = "{$value['operator']} '{$value['meta_value']}'";
            }

            if ( ! empty( $where_value ) ) {
                if ( $index > 0 )
                    $query['where'] .= ' ' . $relation;

                if ( isset( $value['type'] ) && $value['type'] == 'order_item_meta' ) {
                    if ( is_array( $value['meta_key'] ) )
                        $query['where'] .= " ( order_item_meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
                    else
                        $query['where'] .= " ( order_item_meta_{$key}.meta_key   = '{$value['meta_key']}'";

                    $query['where'] .= " AND order_item_meta_{$key}.meta_value {$where_value} )";
                } else {
                    if ( is_array( $value['meta_key'] ) )
                        $query['where'] .= " ( meta_{$key}.meta_key   IN ('" . implode( "','", $value['meta_key'] ) . "')";
                    else
                        $query['where'] .= " ( meta_{$key}.meta_key   = '{$value['meta_key']}'";

                    $query['where'] .= " AND meta_{$key}.meta_value {$where_value} )";
                }
            }
        }

        $query['where'] .= ")";
    }

    if ( ! empty( $where ) ) {
        foreach ( $where as $value ) {
            if ( strtolower( $value['operator'] ) == 'in' ) {
                if ( is_array( $value['value'] ) )
                    $value['value'] = implode( "','", $value['value'] );
                if ( ! empty( $value['value'] ) )
                    $where_value = "IN ('{$value['value']}')";
            } else {
                $where_value = "{$value['operator']} '{$value['value']}'";
            }

            if ( ! empty( $where_value ) )
                $query['where'] .= " AND {$value['key']} {$where_value}";
        }
    }

    if ( $group_by ) {
        $query['group_by'] = "GROUP BY {$group_by}";
    }

    if ( $order_by ) {
        $query['order_by'] = "ORDER BY {$order_by}";
    }

    if ( $limit ) {
        $query['limit'] = "LIMIT {$limit}";
    }

    $query      = apply_filters( 'dokan_reports_get_order_report_query', $query );
    $query      = implode( ' ', $query );
    $query_hash = md5( $query_type . $query );

    if ( $debug ) {
        printf( '<pre>%s</pre>', print_r( $query, true ) );
    }

    if ( $debug || $nocache || ( false === ( $result = get_transient( 'dokan_wc_report_' . $query_hash ) ) ) ) {
        $result = apply_filters( 'dokan_reports_get_order_report_data', $wpdb->$query_type( $query ), $data );

        if ( $filter_range ) {
            if ( $end_date == date('Y-m-d', current_time( 'timestamp' ) ) ) {
                $expiration = 60 * 60 * 1; // 1 hour
            } else {
                $expiration = 60 * 60 * 24; // 24 hour
            }
        } else {
            $expiration = 60 * 60 * 24; // 24 hour
        }

        set_transient( 'dokan_wc_report_' . $query_hash, $result, $expiration );
    }

    return $result;
}


/**
 * Generate sales overview report chart in report area
 *
 * @since 1.0
 *
 * @return void
 */
function dokan_sales_overview() {
    $start_date = date( 'Y-m-01', current_time('timestamp') );
    $end_date = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );

    dokan_report_sales_overview( $start_date, $end_date, __( 'This month\'s sales', 'dokan' ) );
}


/**
 * Generate seller dashboard overview chart
 *
 * @since 1.0
 * @return void
 */
function dokan_dashboard_sales_overview() {
    $start_date = date( 'Y-m-01', current_time('timestamp') );
    $end_date = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );

    dokan_sales_overview_chart_data( $start_date, $end_date, 'day' );
}


/**
 * Generates daily sales report
 *
 * @since 1.0
 * @global WPDB $wpdb
 */
function dokan_daily_sales() {
    global $wpdb;

    $start_date = date( 'Y-m-01', current_time('timestamp') );
    $end_date = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );

    if ( isset( $_POST['dokan_report_filter'] ) ) {
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
    }
    ?>

    <form method="post" class="dokan-form-inline report-filter dokan-clearfix" action="">
        <div class="dokan-form-group">
            <label for="from"><?php _e( 'From:', 'dokan' ); ?></label> <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo date_i18n( get_option( 'date_format' ), strtotime( $start_date ) ); ?>" />
        </div>

        <div class="dokan-form-group">
            <label for="to"><?php _e( 'To:', 'dokan' ); ?></label>
            <input type="text" name="end_date" id="to" class="datepicker" readonly="readonly" value="<?php echo date_i18n( get_option( 'date_format' ), strtotime( $end_date ) ); ?>" />

            <input type="submit" name="dokan_report_filter" class="dokan-btn dokan-btn-success dokan-btn-sm dokan-theme" value="<?php _e( 'Show', 'dokan' ); ?>" />
        </div>
    </form>
    <?php

    dokan_report_sales_overview( $start_date, $end_date, __( 'Sales in this period', 'dokan' ) );
}



/**
 * Sales overview factory function
 *
 * @since 1.0
 *
 * @global type $woocommerce
 * @global WPDB $wpdb
 * @global type $wp_locale
 * @global WP_User $current_user
 * @param type $start_date
 * @param type $end_date
 * @param type $heading
 */
function dokan_report_sales_overview( $start_date, $end_date, $heading = '' ) {
    global $woocommerce, $wpdb, $wp_locale;

    $total_sales = $total_orders = $order_items = $discount_total = $shipping_total = 0;

    $order_totals = dokan_get_order_report_data( array(
        'data' => array(
            '_order_total' => array(
                'type'     => 'meta',
                'function' => 'SUM',
                'name'     => 'total_sales'
            ),
            '_order_shipping' => array(
                'type'     => 'meta',
                'function' => 'SUM',
                'name'     => 'total_shipping'
            ),
            'ID' => array(
                'type'     => 'post_data',
                'function' => 'COUNT',
                'name'     => 'total_orders'
            )
        ),
        'filter_range' => true,
        // 'debug' => true
    ), $start_date, $end_date );

    $total_sales    = $order_totals->total_sales;
    $total_shipping = $order_totals->total_shipping;
    $total_orders   = absint( $order_totals->total_orders );
    $total_items    = absint( dokan_get_order_report_data( array(
        'data' => array(
            '_qty' => array(
                'type'            => 'order_item_meta',
                'order_item_type' => 'line_item',
                'function'        => 'SUM',
                'name'            => 'order_item_qty'
            )
        ),
        'query_type' => 'get_var',
        'filter_range' => true
    ), $start_date, $end_date ) );

    // Get discount amounts in range
    $total_coupons = dokan_get_order_report_data( array(
        'data' => array(
            'discount_amount' => array(
                'type'            => 'order_item_meta',
                'order_item_type' => 'coupon',
                'function'        => 'SUM',
                'name'            => 'discount_amount'
            )
        ),
        'where' => array(
            array(
                'key'      => 'order_item_type',
                'value'    => 'coupon',
                'operator' => '='
            )
        ),
        'query_type' => 'get_var',
        'filter_range' => true
    ), $start_date, $end_date );

    $average_sales = $total_sales / ( 30 + 1 );

    $legend = apply_filters( 'dokan-seller-dashboard-reports-left-sidebar', array(
        'sales_in_this_period' => array(
            'title' => sprintf( __( '%s sales in this period', 'dokan' ), '<strong>' . wc_price( $total_sales ) . '</strong>' ),
        ),

        'average_daily_sales' => array(
            'title' => sprintf( __( '%s average daily sales', 'dokan' ), '<strong>' . wc_price( $average_sales ) . '</strong>' ),
        ),

        'orders_placed' => array(
            'title' => sprintf( __( '%s orders placed', 'dokan' ), '<strong>' . $total_orders . '</strong>' ),
        ),

        'items_purchased' => array(
            'title' => sprintf( __( '%s items purchased', 'dokan' ), '<strong>' . $total_items . '</strong>' ),
        ),

        'charged_for_shipping' => array(
            'title' => sprintf( __( '%s charged for shipping', 'dokan' ), '<strong>' . wc_price( $total_shipping ) . '</strong>' ),
        ),

        'worth_of_coupons_used' => array(
            'title' => sprintf( __( '%s worth of coupons used', 'dokan' ), '<strong>' . wc_price( $total_coupons ) . '</strong>' ),
        ),
    ) );
    ?>
    <div id="poststuff" class="dokan-reports-wrap">
        <div class="dokan-reports-sidebar report-left dokan-left">
            <ul class="chart-legend">
                <?php foreach ($legend as $item) {
                    printf( '<li>%s</li>', $item['title'] );
                } ?>
            </ul>
        </div>

        <div class="dokan-reports-main report-right dokan-right">
            <div class="postbox">
                <h3><span><?php echo $heading; ?></span></h3>

                <?php dokan_sales_overview_chart_data( $start_date, $end_date, 'day' ); ?>
            </div>
        </div>
    </div>
    <?php
}



/**
 * Prepares chart data for sales overview
 *
 * @since 1.0
 *
 * @global type $wp_locale
 * @param type $start_date
 * @param type $end_date
 * @param type $group_by
 */
function dokan_sales_overview_chart_data( $start_date, $end_date, $group_by ) {
    global $wp_locale;

    $start_date_to_time = strtotime( $start_date );
    $end_date_to_time = strtotime( $end_date );

    if ( $group_by == 'day' ) {
        $group_by_query       = 'YEAR(post_date), MONTH(post_date), DAY(post_date)';
        $chart_interval       = ceil( max( 0, ( $end_date_to_time - $start_date_to_time ) / ( 60 * 60 * 24 ) ) );
        $barwidth             = 60 * 60 * 24 * 1000;
    } else {
        $group_by_query = 'YEAR(post_date), MONTH(post_date)';
        $chart_interval = 0;
        $min_date             = $start_date_to_time;
        while ( ( $min_date   = strtotime( "+1 MONTH", $min_date ) ) <= $end_date_to_time ) {
            $chart_interval ++;
        }
        $barwidth             = 60 * 60 * 24 * 7 * 4 * 1000;
    }

    // Get orders and dates in range - we want the SUM of order totals, COUNT of order items, COUNT of orders, and the date
    $orders = dokan_get_order_report_data( array(
        'data' => array(
            '_order_total' => array(
                'type'     => 'meta',
                'function' => 'SUM',
                'name'     => 'total_sales'
            ),
            'ID' => array(
                'type'     => 'post_data',
                'function' => 'COUNT',
                'name'     => 'total_orders',
                'distinct' => true,
            ),
            'post_date' => array(
                'type'     => 'post_data',
                'function' => '',
                'name'     => 'post_date'
            ),
        ),
        'group_by'     => $group_by_query,
        'order_by'     => 'post_date ASC',
        'query_type'   => 'get_results',
        'filter_range' => true,
        'debug' => false
    ), $start_date, $end_date );

    // Prepare data for report
    $order_counts      = dokan_prepare_chart_data( $orders, 'post_date', 'total_orders', $chart_interval, $start_date_to_time, $group_by );
    $order_amounts     = dokan_prepare_chart_data( $orders, 'post_date', 'total_sales', $chart_interval, $start_date_to_time, $group_by );

    // Encode in json format
    $chart_data = json_encode( array(
        'order_counts'      => array_values( $order_counts ),
        'order_amounts'     => array_values( $order_amounts )
    ) );

    $chart_colours = array(
        'order_counts'  => '#3498db',
        'order_amounts'   => '#1abc9c'
    );

    ?>
    <div class="chart-container">
        <div class="chart-placeholder main" style="width: 100%; height: 350px;"></div>
    </div>

    <script type="text/javascript">
        jQuery(function($) {

            var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );
            var isRtl = '<?php echo is_rtl() ? "1" : "0"; ?>'

            var series = [
                {
                    label: "<?php echo esc_js( __( 'Sales total', 'dokan' ) ) ?>",
                    data: order_data.order_amounts,
                    shadowSize: 0,
                    hoverable: true,
                    points: { show: true, radius: 5, lineWidth: 1, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 2, fill: false },
                    shadowSize: 0,
                    prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>"
                },
                {
                    label: "<?php echo esc_js( __( 'Number of orders', 'dokan' ) ) ?>",
                    data: order_data.order_counts,
                    shadowSize: 0,
                    hoverable: true,
                    points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
                    lines: { show: true, lineWidth: 3, fill: false },
                    shadowSize: 0,
                    append_tooltip: " <?php echo __( 'sales', 'dokan' ); ?>"
                },
            ];

            var main_chart = jQuery.plot(
                jQuery('.chart-placeholder.main'),
                series,
                {
                    legend: {
                        show: true,
                        position: 'nw'
                    },
                    series: {
                        lines: { show: true, lineWidth: 4, fill: false },
                        points: { show: true }
                    },
                    grid: {
                        borderColor: '#eee',
                        color: '#aaa',
                        borderWidth: 1,
                        hoverable: true,
                        show: true,
                        aboveData: false,
                    },
                    xaxis: {
                        color: '#aaa',
                        position: "bottom",
                        tickColor: 'transparent',
                        mode: "time",
                        timeformat: "<?php if ( $group_by == 'day' ) echo '%d %b'; else echo '%b'; ?>",
                        monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
                        tickLength: 1,
                        minTickSize: [1, "<?php echo $group_by; ?>"],
                        font: {
                            color: "#aaa"
                        },
                        transform: function (v) { return ( isRtl == '1' ) ? -v : v; },
                        inverseTransform: function (v) { return ( isRtl == '1' ) ? -v : v; }
                    },
                    yaxes: [
                        {
                            position: ( isRtl == '1' ) ? "right" : "left",
                            min: 0,
                            minTickSize: 1,
                            tickDecimals: 0,
                            color: '#d4d9dc',
                            font: { color: "#aaa" }
                        },
                        {
                            position: ( isRtl == '1' ) ? "right" : "left",
                            min: 0,
                            tickDecimals: 2,
                            alignTicksWithAxis: 1,
                            color: 'transparent',
                            font: { color: "#aaa" }
                        }
                    ],
                    colors: ["<?php echo $chart_colours['order_counts']; ?>", "<?php echo $chart_colours['order_amounts']; ?>"]
                }
            );

            jQuery('.chart-placeholder').resize();
        });

    </script>
    <?php
}

/**
 * Output the top sellers chart.
 *
 * @access public
 * @return void
 */
function dokan_top_sellers() {

    global $start_date, $end_date, $woocommerce, $wpdb;
    $current_user = dokan_get_current_user_id();

    $start_date = isset( $_POST['start_date'] ) ? $_POST['start_date'] : '';
    $end_date   = isset( $_POST['end_date'] ) ? $_POST['end_date'] : '';

    if ( ! $start_date )
        $start_date = date( 'Ymd', strtotime( date( 'Ym', current_time( 'timestamp' ) ) . '01' ) );
    if ( ! $end_date )
         $end_date = date( 'Ymd', current_time( 'timestamp' ) );

    $start_date = strtotime( $start_date );
    $end_date = strtotime( $end_date );

    // Get order ids and dates in range
    $order_items = apply_filters( 'woocommerce_reports_top_sellers_order_items', $wpdb->get_results( "
        SELECT order_item_meta_2.meta_value as product_id, SUM( order_item_meta.meta_value ) as item_quantity FROM {$wpdb->prefix}woocommerce_order_items as order_items

        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        LEFT JOIN {$wpdb->prefix}dokan_orders AS do ON posts.ID = do.order_id

        WHERE   posts.post_type     = 'shop_order'
        AND     posts.post_status   != 'trash'
        AND     do.seller_id = {$current_user}
        AND     do.order_status IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) . "')
        AND     post_date > '" . date('Y-m-d', $start_date ) . "'
        AND     post_date < '" . date('Y-m-d', strtotime('+1 day', $end_date ) ) . "'
        AND     order_items.order_item_type = 'line_item'
        AND     order_item_meta.meta_key = '_qty'
        AND     order_item_meta_2.meta_key = '_product_id'
        GROUP BY order_item_meta_2.meta_value
    " ), $start_date, $end_date );

    $found_products = array();

    if ( $order_items ) {
        foreach ( $order_items as $order_item ) {
            $found_products[ $order_item->product_id ] = $order_item->item_quantity;
        }
    }

    asort( $found_products );
    $found_products = array_reverse( $found_products, true );
    $found_products = array_slice( $found_products, 0, 25, true );
    reset( $found_products );
    ?>
    <form method="post" action="" class="report-filter dokan-form-inline dokan-clearfix">
        <div class="dokan-form-group">
            <label for="from"><?php _e( 'From:', 'dokan' ); ?></label>
            <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo date_i18n( get_option( 'date_format' ), strtotime( $start_date ) ); ?>" />
        </div>

        <div class="dokan-form-group">
            <label for="to"><?php _e( 'To:', 'dokan' ); ?></label>
            <input type="text" class="datepicker" name="end_date" id="to" readonly="readonly" value="<?php echo date_i18n( get_option( 'date_format' ), strtotime( $end_date ) ); ?>" />
        </div>

        <input type="submit" class="dokan-btn dokan-btn-success dokan-btn-sm dokan-theme" value="<?php _e( 'Show', 'dokan' ); ?>" />
    </form>


    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php _e( 'Product', 'dokan' ); ?></th>
                <th><?php _e( 'Sales', 'dokan' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $max_sales = current( $found_products );
                foreach ( $found_products as $product_id => $sales ) {
                    $width = $sales > 0 ? ( $sales / $max_sales ) * 100 : 0;
                    $product_title = get_the_title( $product_id );

                    if ( $product_title ) {
                        $product_name = '<a href="' . get_permalink( $product_id ) . '">'. __( $product_title ) .'</a>';
                        $orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=' . urlencode( $product_title ) . '&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) );
                    } else {
                        $product_name = __( 'Product does not exist', 'dokan' );
                        $orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) );
                    }

                    $orders_link = apply_filters( 'dokan_reports_order_link', $orders_link, $product_id, $product_title );
                    $orders_link = ''; //FIXME : order link

                    echo '<tr><th class="60%">' . $product_name . '</th><td width="1%"><span>' . esc_html( $sales ) . '</span></td><td width="30%"><div class="progress"><a class="progress-bar" href="' . esc_url( $orders_link ) . '" style="width:' . esc_attr( $width ) . '%">&nbsp;</a></div></td></tr>';
                }
            ?>
        </tbody>
    </table>
    <?php
}


/**
 * Output the top earners chart.
 *
 * @access public
 * @return void
 */
function dokan_top_earners() {

    global $wpdb;
    $current_user = dokan_get_current_user_id();
    $withdraw_order_status = dokan_get_option( 'withdraw_order_status', 'dokan_withdraw' );

    $start_date = isset( $_POST['start_date'] ) ? $_POST['start_date'] : '';
    $end_date   = isset( $_POST['end_date'] ) ? $_POST['end_date'] : '';

    if ( ! $start_date )
        $start_date = date( 'Ymd', strtotime( date('Ym', current_time( 'timestamp' ) ) . '01' ) );
    if ( ! $end_date )
        $end_date = date( 'Ymd', current_time( 'timestamp' ) );

    $start_date = strtotime( $start_date );
    $end_date = strtotime( $end_date );

    // Get order ids and dates in range
    $order_items = apply_filters( 'woocommerce_reports_top_earners_order_items', $wpdb->get_results( "
        SELECT order_item_meta_2.meta_value as product_id, SUM( order_item_meta.meta_value ) as line_total,SUM( do.net_amount ) as total_earning FROM {$wpdb->prefix}woocommerce_order_items as order_items

        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        LEFT JOIN {$wpdb->prefix}dokan_orders AS do ON posts.ID = do.order_id

        WHERE   posts.post_type     = 'shop_order'
        AND     posts.post_status   != 'trash'
        AND     do.seller_id = {$current_user}
        AND     do.order_status           IN ('" . implode( "','", apply_filters( 'woocommerce_reports_order_statuses', $withdraw_order_status ) ) . "')
        AND     post_date > '" . date('Y-m-d', $start_date ) . "'
        AND     post_date < '" . date('Y-m-d', strtotime('+1 day', $end_date ) ) . "'
        AND     order_items.order_item_type = 'line_item'
        AND     order_item_meta.meta_key = '_line_total'
        AND     order_item_meta_2.meta_key = '_product_id'
        GROUP BY order_item_meta_2.meta_value
    " ), $start_date, $end_date );

    $found_products = array();
    $total_earnings = array();
    if ( $order_items ) {
        foreach ( $order_items as $order_item ) {
            $found_products[ $order_item->product_id ] = $order_item->line_total;
            $total_earnings[ $order_item->product_id ] = $order_item->total_earning;
        }
    }

    asort( $found_products );
    $found_products = array_reverse( $found_products, true );
    $found_products = array_slice( $found_products, 0, 25, true );
    reset( $found_products );
    ?>
    <form method="post" action="" class="report-filter dokan-form-inline dokan-clearfix">
        <div class="dokan-form-group">
            <label for="from"><?php _e( 'From:', 'dokan' ); ?></label>
            <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo date_i18n( get_option( 'date_format' ), strtotime( $start_date ) ); ?>" />
        </div>

        <div class="dokan-form-group">
            <label for="to"><?php _e( 'To:', 'dokan' ); ?></label>
            <input type="text" class="datepicker" name="end_date" id="to" readonly="readonly" value="<?php echo date_i18n( get_option( 'date_format' ), strtotime( $end_date ) ); ?>" />
        </div>

        <input type="submit" class="dokan-btn dokan-btn-success dokan-btn-sm dokan-theme" value="<?php _e( 'Show', 'dokan' ); ?>" />
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th><?php _e( 'Product', 'dokan' ); ?></th>
                <th colspan="2"><?php _e( 'Sales', 'dokan' ); ?></th>
                <th><?php _e( 'Earning', 'dokan' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                $max_sales = current( $found_products );
                foreach ( $found_products as $product_id => $sales ) {
                    $width = $sales > 0 ? ( round( $sales ) / round( $max_sales ) ) * 100 : 0;

                    $product_title = get_the_title( $product_id );

                    if ( $product_title ) {
                        $product_name = '<a href="'.get_permalink( $product_id ).'">'. __( $product_title ) .'</a>';
                        $orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=' . urlencode( $product_title ) . '&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) );
                    } else {
                        $product_name = __( 'Product no longer exists', 'dokan' );
                        $orders_link = admin_url( 'edit.php?s&post_status=all&post_type=shop_order&action=-1&s=&shop_order_status=' . implode( ",", apply_filters( 'woocommerce_reports_order_statuses', array( 'wc-completed', 'wc-processing', 'wc-on-hold' ) ) ) );
                    }

                    $orders_link = apply_filters( 'woocommerce_reports_order_link', $orders_link, $product_id, $product_title );

                    echo '<tr>
                                <th>' . $product_name . '</th>
                                <td colspan="2"><span>' . wc_price( $sales ) . '</span></td>
                                <td width="1%"><span>' . wc_price( $total_earnings[ $product_id ] ) . '</span></td>
                                <td class="bars"><a href="' . esc_url( $orders_link ) . '" style="width:' . esc_attr( $width ) . '%">&nbsp;</a></td>
                          </tr>';
                }
            ?>
        </tbody>
    </table>
    <?php
}
