<?php
/**
 * Dokan Admin Dahsboard Report Template
 *
 * @since 1.0
 *
 * @package dokan
 */
?>

<div class="wrap">
    <?php
    $tab  = isset( $_GET['tab'] ) ? $_GET['tab'] : 'report';
    $type = isset( $_GET['type'] ) ? $_GET['type'] : 'day';

    $all_tabs = apply_filters( 'dokan_admin_report_tabs', array(
        'report' => __( 'Reports', 'dokan' ),
        'logs'   => __( 'All Logs', 'dokan' ),
    ) );
    ?>

    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <?php foreach ( $all_tabs as $tab_id => $label ) { ?>
            <a href="admin.php?page=dokan-reports&amp;tab=<?php echo esc_attr( $tab_id ); ?>" class="nav-tab<?php if ( $tab == $tab_id ) echo ' nav-tab-active'; ?>"><?php echo $label; ?></a>
        <?php } ?>
    </h2>

    <?php
    if ( $tab == 'report' ) {
        $report_sub_head = apply_filters( 'dokan_admin_report_sub', array(
            'day'    => __( 'By Day', 'dokan' ),
            'year'   => __( 'By Year', 'dokan' ),
            'seller' => __( 'By Vendor', 'dokan' ),
        ) );

        $head_count = count( $report_sub_head );
        $loop_count = 1;
        ?>
        <ul class="subsubsub dokan-report-sub" style="float: none;">
            <?php foreach ( $report_sub_head as $sub_id => $sub_label ) { ?>
                <li>
                    <a href="admin.php?page=dokan-reports&amp;tab=report&amp;type=<?php echo esc_attr( $sub_id ); ?>" <?php if ( $type == $sub_id ) echo 'class="current"'; ?>><?php echo $sub_label; ?></a>

                    <?php
                    echo ( $loop_count != $head_count ) ? '|' : '';
                    $loop_count++;
                    ?>
                </li>
            <?php } ?>
        </ul>

        <?php
        $start_date       = date( 'Y-m-01', current_time( 'timestamp' ) );
        $end_date         = date( 'Y-m-d', strtotime( 'midnight', current_time( 'timestamp' ) ) );
        $current_year     = $selected_year = date( 'Y' );
        $chosen_seller_id ='';

        if ( isset( $_POST['dokan_report_filter_date'] ) ) {
            $start_date = $_POST['start_date'];
            $end_date   = $_POST['end_date'];
        }

        if ( isset( $_POST['dokan_report_filter_year'] ) ) {
            $selected_year = $_POST['report_year'];
        }

        if ( isset( $_POST['chosen_store_name'] ) ) {
            $chosen_seller_id = $_POST['chosen_store_name'];
        }


        if ( $type == 'day' ) {
            ?>
            <form method="post" class="form-inline report-filter" action="">
                <span class="form-group">
                    <label for="from"><?php _e( 'From:', 'dokan' ); ?></label> <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo esc_attr( $start_date ); ?>" />
                </span>

                <span class="form-group">
                    <label for="to"><?php _e( 'To:', 'dokan' ); ?></label>
                    <input type="text" name="end_date" id="to" class="datepicker" readonly="readonly" value="<?php echo esc_attr( $end_date ); ?>" />

                    <input type="submit" name="dokan_report_filter_date" class="button button-primary" value="<?php _e( 'Show', 'dokan' ); ?>" />
                </span>
            </form>

            <?php
        }
        if ( $type == 'seller' ) {

            $user_search        = new WP_User_Query( array( 'role' => 'seller' ) );
            $sellers            = $user_search->get_results();
            $chosen_placeholder = __( 'Select a Store...', 'dokan' );
            $seller_id          = isset( $_POST['chosen_store_name'] ) ? intval( $_POST['chosen_store_name'] ) : 0;
            ?>
            <form method="post" class="form-inline report-filter" action="" >

                <span class="form-group">
                    <label for='chosen_store_name'<b><?php _e( 'Store Name : ', 'dokan' ); ?></b></label>
                    <select id='dokan-select2' name='chosen_store_name' data-placeholder= '<?php echo __( 'Select a Store...', 'dokan' ); ?>' style="width:350px;"  class="dokan-select2">
                        <option></option>
                        <?php
                        foreach ( $sellers as $user ) {
                            $vendor = dokan()->vendor->get( $user->ID );
                            if ( !empty( $vendor->get_shop_name() ) ) {
                                ?>
                                <option <?php selected( $seller_id, $vendor->get_id() ); ?> value='<?php echo $vendor->get_id(); ?>'><?php echo esc_html( $vendor->get_shop_name() ) ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>

                </span>

                <span class="form-group">
                    <label for="from"><?php _e( 'From:', 'dokan' ); ?></label>
                    <input type="text" class="datepicker" name="start_date" id="from" readonly="readonly" value="<?php echo esc_attr( $start_date ); ?>" />
                </span>

                <span class="form-group">
                    <label for="to"><?php _e( 'To:', 'dokan' ); ?></label>
                    <input type="text" name="end_date" id="to" class="datepicker" readonly="readonly" value="<?php echo esc_attr( $end_date ); ?>" />

                    <input type="submit" name="dokan_report_filter_date" class="button button-primary" value="<?php _e( 'Show', 'dokan' ); ?>" />
                </span>
            </form>

        <?php } elseif ( $type == 'year' ) { ?>
            <form method="post" class="form-inline report-filter" action="">
                <span class="form-group">
                    <label for="from"><?php _e( 'Year:', 'dokan' ); ?></label>
                    <select name="report_year">
                        <?php for ( $i = ($current_year - 5); $i < ($current_year + 5); $i++ ) { ?>
                            <option value="<?php echo $i; ?>" <?php selected( $selected_year, $i ); ?>><?php echo $i; ?></option>
                        <?php } ?>
                    </select>
                </span>

                <input type="submit" name="dokan_report_filter_year" class="button button-primary" value="<?php _e( 'Show', 'dokan' ); ?>" />
            </form>
            <?php
        } else {
            do_action( 'dokan_report_sub_' . $type, $tab, $start_date, $end_date, $selected_year, $current_year );
        }
        ?>

        <div class="admin-report-container">
            <?php
            $order_total = $earning_total = $total_orders = 0;

            if ( $type == 'year' ) {
                $report_data = dokan_admin_report( 'year', $selected_year );
            } elseif ( $type == 'seller' ) {
                $report_data = dokan_admin_report_by_seller( $chosen_seller_id );
            } else {
                $report_data = dokan_admin_report();
            }

            if ( $report_data ) {
                foreach ( $report_data as $row ) {
                    $order_total   += $row->order_total;
                    $earning_total += $row->earning;
                    $total_orders  += $row->total_orders;
                }
            }
            ?>

            <div class="dokan-reports-sidebar">
                <ul class="chart-legend">
                    <li>
                        <strong><?php echo wc_price( $earning_total ); ?></strong>
                        <?php _e( 'Total Earning in this period', 'dokan' ); ?>
                    </li>
                    <li>
                        <strong><?php echo wc_price( $order_total ); ?></strong>
                        <?php _e( 'Order total in this period', 'dokan' ); ?>
                    </li>
                    <li>
                        <strong><?php echo $total_orders; ?></strong>
                        <?php _e( 'orders placed in this period', 'dokan' ); ?>
                    </li>
                </ul>
            </div>

            <div class="chart-container">
                <div class="chart-placeholder main"></div>
            </div>
        </div>


    <?php } else if ( $tab == 'logs' ) { ?>
        <?php
        $headers = array(
            'order_id'     => __( 'Order', 'dokan' ),
            'seller_id'    => __( 'Vendor', 'dokan' ),
            'order_total'  => __( 'Order Total', 'dokan' ),
            'net_amount'   => __( 'Vendor Earning', 'dokan' ),
            'commision'    => __( 'Commision', 'dokan' ),
            'order_status' => __( 'Status', 'dokan' ),
        );
        $headers = apply_filters( 'dokan_earning_report_header', $headers );
        $statuses = wc_get_order_statuses();

        $args = $_GET;
        $args['action'] = 'dokan-export';
        $export_url = add_query_arg( $args , admin_url( 'admin.php' ) );
        ?>

        <?php do_action( 'dokan_prev_report_form', $_GET ); ?>

        <a href="<?php echo $export_url ?>">
            <button class="button button-primary" style="float:right; margin: 10px;"><?php _e( ' Export CSV', 'dokan' ); ?></button>
        </a>
        <table class="widefat withdraw-table" style="margin-top: 15px;">
            <thead>
                <tr>
                    <?php
                    foreach ( (array) $headers as $key => $label ) {
                        ?>
                        <th><?php echo $label; ?></th>
                        <?php
                    }
                    ?>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <?php
                    foreach ( (array) $headers as $key => $label ) {
                        ?>
                        <th><?php echo $label; ?></th>
                        <?php
                    }
                    ?>
                </tr>
            </tfoot>
            <tbody>
                <?php
                $count        = 0;
                $pagenum      = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
                $limit        = 20;
                $offset       = ( $pagenum - 1 ) * $limit;

                $seller_where = '';

                if ( isset( $_GET['seller_id'] ) ) {
                    $seller_where = $wpdb->prepare( 'AND seller_id = %d', $_GET['seller_id'] );
                }

                $sql = "SELECT do.*, p.post_date FROM {$wpdb->prefix}dokan_orders do
                        LEFT JOIN $wpdb->posts p ON do.order_id = p.ID
                        WHERE seller_id != 0 AND p.post_status != 'trash' $seller_where
                        ORDER BY do.order_id DESC LIMIT $limit OFFSET $offset";

                $all_logs = $wpdb->get_results( $sql );
                foreach ( $all_logs as $log ) {
                    $seller = get_user_by( 'id', $log->seller_id );
                    if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
                        $order_status = $log->order_status;
                    } else {
                        $order_status = 'wc-' . $log->order_status;
                    }

                    $result = array(
                        'order_id'     => '<a href="' . admin_url( 'post.php?action=edit&amp;post=' . $log->order_id ) . '">#' . $log->order_id . '</a>',
                        'seller_id'    => $seller ? '<a href="' . add_query_arg( array( 'seller_id' => $log->seller_id ) ) . '">' . $seller->display_name . '</a> (<a href="' . admin_url( 'user-edit.php?user_id=' . $log->seller_id ) . '">' . __( 'edit', 'dokan' ) . '</a>)' : __( 'Vendor not exist', 'dokan' ),
                        'order_total'  => $log->order_total,
                        'net_amount'   => $log->net_amount,
                        'commision'    => $log->order_total - $log->net_amount,
                        'order_status' => $statuses[$order_status]
                    );

                    $result                = apply_filters( 'dokan_report_table_value', $result, $log->order_id, $log->seller_id );
                    $result['order_total'] = !empty( $result['order_total'] ) ? wc_price( $result['order_total'] ) : '';
                    $result['net_amount']  = !empty( $result['net_amount'] ) ? wc_price( $result['net_amount'] ) : '';
                    $result['commision']   = !empty( $result['commision'] ) ? wc_price( $result['commision'] ) : '';
                    ?>
                    <tr<?php echo $count % 2 == 0 ? ' class="alternate"' : ''; ?>>

                        <?php
                        foreach ( (array) $headers as $key => $content ) {
                            ?>
                            <th><?php echo isset( $result[$key] ) ? $result[$key] : ''; ?></th>

                            <?php
                        }
                        ?>
                    </tr>
                    <?php
                    $count++;
                }
                ?>
            </tbody>
        </table>

        <div class="tablenav bottom">
            <?php
            if ( $all_logs ) {
                $count_where = 'seller_id != 0';

                if ( isset( $_GET['seller_id'] ) ) {
                    $count_where = $wpdb->prepare( 'seller_id = %d', $_GET['seller_id'] );
                }

                $count        = $wpdb->get_var( "SELECT COUNT(id) FROM {$wpdb->prefix}dokan_orders WHERE $count_where" );
                $num_of_pages = ceil( $count / $limit );

                $page_links = paginate_links( array(
                    'base'      => add_query_arg( 'paged', '%#%' ),
                    'format'    => '',
                    'prev_text' => __( '&laquo;', 'dokan' ),
                    'next_text' => __( '&raquo;', 'dokan' ),
                    'total'     => $num_of_pages,
                    'current'   => $pagenum,
                    'add_args'  => false
                ) );

                if ( $page_links ) {
                    echo '<div class="tablenav-pages">' . $page_links . '</div>';
                }
            }
            ?>
        </div>

    <?php } else { ?>
        <?php do_action( 'dokan_admin_report_tab_' . $tab ); ?>
    <?php } ?>

    <script type="text/javascript">
        jQuery(function ($) {
            $('.datepicker').datepicker({
                dateFormat: 'yy-mm-dd'
            });

            $('#dokan-select2').select2();
        });
    </script>
</div>
