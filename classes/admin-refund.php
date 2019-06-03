<?php

/**
*  Dokan Pro Admin refund class
*
*  Manupulate all refund functionality
*  in admin backend
*
*  @author weDevs <info@wedevs>
*
*  @since 2.4.11
*
*  @package dokan
*/
class Dokan_Pro_Admin_Refund extends Dokan_Pro_Refund {

    /**
     * Initializes the Dokan_Admin_Refund class
     *
     * Checks for an existing Dokan_Admin_Refund instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Pro_Admin_Refund();
        }

        return $instance;
    }

    /**
     * Bulk action handler
     *
     * @return void admin
     */
    function bulk_action_handler() {
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            return;
        }

        if ( ! isset( $_POST['dokan_refund_bulk'] ) ) {
            return;
        }

        $bulk_action = $_POST['dokan_refund_bulk'];

        if ( ! isset( $_POST['id'] )  ) {
            return;
        }

        //if id empty then empty value return
        if ( ! is_array( $_POST['id'] ) && ! count( $_POST['id'] ) ) {
            return;
        }

        $refund_ids = implode( "','", $_POST['id'] );
        $status = $_POST['status_page'];

        switch ( $bulk_action ) {

        case 'delete':

            foreach ( $_POST['id'] as $refund_id ) {
                $this->delete_refund( $refund_id );
            }

            wp_redirect( admin_url( 'admin.php?page=dokan-refund&message=trashed&status=' . $status ) );
            die();

            break;

        case 'cancel':

            foreach ( $_POST['id'] as $key => $refund_id ) {
                $order_id = $_POST['order_id'][$key];
                $refund_amount  = $_POST['refund_amount'][$key];

                // Dokan_Email::init()->refund_request_cancel( $user_id, $refund_amount, $method, $note );
                $this->update_status( $refund_id, $order_id, 2 );
            }

            wp_redirect( admin_url( 'admin.php?page=dokan-refund&message=cancelled&status=' . $status ) );
            die();

            break;

        case 'approve':

            foreach ( $_POST['id'] as $key => $refund_id ) {
                $order_id = $_POST['order_id'][$key];
                $refund_amount  = $_POST['refund_amount'][$key];
                $method  = $_POST['method'][$key];

                // Dokan_Email::init()->refund_request_approve( $order_id, $refund_amount, $method );
                $this->update_status( $refund_id, $order_id, 1 );
            }

            wp_redirect( admin_url( 'admin.php?page=dokan-refund&message=approved&status=' . $status ) );

            break;

        case 'pending':

            foreach ( $_POST['id'] as $key => $refund_id ) {
                $this->update_status( $refund_id, $_POST['user_id'][$key], 0 );
            }

            wp_redirect( admin_url( 'admin.php?page=dokan-refund&message=pending&status=' . $status ) );

            break;
        }


    }

    /**
     * Refund listing for admin
     *
     * @param  string  $status
     *
     * @return void admin
     */
    function admin_refund_list( $status ) {
        $pagenum = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
        $limit   = 20;
        $offset  = ( $pagenum - 1 ) * $limit;
        $result  = $this->get_refund_requests( $this->get_status_code( $status ), $limit, $offset );
        ?>

        <?php if ( isset( $_GET['message'] ) ) {
            $message = '';

            switch ( $_GET['message'] ) {
            case 'trashed':
                $message = __( 'Request Deleted!', 'dokan' );
                break;

            case 'cancelled':
                $message = __( 'Request Cancelled!', 'dokan' );
                break;

            case 'approved':
                $message = __( 'Request Approved!', 'dokan' );
                break;
            }

            if ( ! empty( $message ) ) {
                ?>
                <div class="updated">
                    <p><strong><?php echo $message; ?></strong></p>
                </div>
                <?php
            }
        } ?>
        <form method="post" action="" id="dokan-admin-refund-action">
            <?php wp_nonce_field( 'dokan_refund_admin_bulk_action', 'dokan_refund_admin_bulk_action_nonce' ); ?>

            <table class="widefat refund-table">
                <thead>
                    <tr>
                        <td class="check-column">
                            <input type="checkbox" class="dokan-refund-allcheck">
                        </td>
                        <th><?php _e( 'Order ID', 'dokan' ); ?></th>
                        <th><?php _e( 'Vendor', 'dokan' ); ?></th>
                        <th><?php _e( 'Refund Amount', 'dokan' ); ?></th>
                        <th><?php _e( 'Refund Reason', 'dokan' ); ?></th>
                        <th><?php _e( 'Payment Gateway', 'dokan' ); ?></th>
                        <th><?php _e( 'Date', 'dokan' ); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td class="check-column">
                            <input type="checkbox" class="dokan-refund-allcheck">
                        </td>
                        <th><?php _e( 'Order ID', 'dokan' ); ?></th>
                        <th><?php _e( 'Vendor', 'dokan' ); ?></th>
                        <th><?php _e( 'Refund Amount', 'dokan' ); ?></th>
                        <th><?php _e( 'Refund Reason', 'dokan' ); ?></th>
                        <th><?php _e( 'Payment Gateway', 'dokan' ); ?></th>
                        <th><?php _e( 'Date', 'dokan' ); ?></th>
                    </tr>
                </tfoot>

            <?php
        if ( $result ) {
            $count = 0;
            foreach ( $result as $key => $row ) {
                try {
                    $order = new WC_Order( $row->order_id );
                    $payment_gateway = wc_get_payment_gateway_by_order( $order );
                    $gateway_supports_refunds = false !== $payment_gateway && $payment_gateway->supports( 'refunds' );
                    $gateway_name             = false !== $payment_gateway ? ( ! empty( $payment_gateway->method_title ) ? $payment_gateway->method_title : $payment_gateway->get_title() ) : __( 'Payment Gateway', 'dokan' );
                    ?>
                    <tr class="<?php echo ( $count % 2 ) == 0 ? 'alternate': 'odd'; ?>">

                        <th class="check-column">
                            <input type="checkbox" name="id[<?php echo $row->id;?>]" value="<?php echo $row->id;?>">
                            <input type="hidden" name="order_id[<?php echo $row->id;?>]" value="<?php echo $row->order_id; ?>">
                            <input type="hidden" name="seller_id[<?php echo $row->id;?>]" value="<?php echo $row->seller_id; ?>">
                            <input type="hidden" name="refund_amount[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->refund_amount ); ?>">
                            <input type="hidden" name="refund_reason[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->refund_reason ); ?>">
                            <input type="hidden" name="item_qtys[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->item_qtys ); ?>">
                            <input type="hidden" name="item_totals[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->item_totals ); ?>">
                            <input type="hidden" name="item_tax_totals[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->item_tax_totals ); ?>">
                            <input type="hidden" name="restock_items[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->restock_items ); ?>">
                        </th>
                        <td>
                            <strong><a href="<?php echo admin_url( 'post.php?post=' . $row->order_id . '&action=edit' ); ?>"><?php echo '#' . $row->order_id; ?></a></strong>
                            <div class="row-actions">
                                <?php if ( $status == 'pending' ) {

                                    if ( $gateway_supports_refunds ) { ?>
                                        <span class="edit"><a href="#" class="dokan-refund-action do-api-refund" data-status="approve" data-refund_id = "<?php echo $row->id; ?>"><?php printf( _x( 'Refund via %s', 'Refund', 'dokan' ), $gateway_name ); ?></a> | </span>
                                    <?php } ?>

                                    <span class="edit"><a href="#" class="dokan-refund-action" data-status="approve" data-refund_id = "<?php echo $row->id; ?>"><?php _e( 'Approve Refund', 'dokan' ); ?></a> | </span>
                                    <span class="edit"><a href="#" class="dokan-refund-action" data-status="cancel" data-refund_id = "<?php echo $row->id; ?>"><?php _e( 'Cancel', 'dokan' ); ?></a></span>

                                <?php } elseif ( false ) { //$status == 'completed'?>

                                    <span class="edit"><a href="#" class="dokan-refund-action" data-status="cancel" data-refund_id = "<?php echo $row->id; ?>"><?php _e( 'Cancel', 'dokan' ); ?></a> | </span>
                                    <span class="edit"><a href="#" class="dokan-refund-action" data-status="pending" data-refund_id = "<?php echo $row->id; ?>"><?php _e( 'Pending', 'dokan' ); ?></a></span>

                                <?php } elseif ( false ) { //$status == 'cancelled'?>

                                    <span class="edit"><a href="#" class="dokan-refund-action" data-status="approve" data-refund_id = "<?php echo $row->id; ?>"><?php _e( 'Approve', 'dokan' ); ?></a> | </span>
                                    <span class="edit"><a href="#" class="dokan-refund-action" data-status="pending" data-refund_id = "<?php echo $row->id; ?>"><?php _e( 'Pending', 'dokan' ); ?></a></span>

                                <?php } ?>

                                <?php if ( $result && $status == 'completed' ) { ?>
                                    <span class="trash"><!-- | --><a href="#" class="dokan-refund-action" data-status="delete" data-refund_id = "<?php echo $row->id; ?>"><?php _e( 'Delete', 'dokan' ); ?></a></span>

                                <?php } ?>
                            </div>
                        </td>
                        <td><?php echo the_author_meta( 'display_name', $row->seller_id ); ?></td>
                        <td><?php echo wc_price( $row->refund_amount ); ?></td>
                        <td><?php echo $row->refund_reason; ?></td>
                        <td><?php echo get_post_meta( $row->order_id, '_payment_method_title', true ); ?></td>
                        <td><?php echo date_i18n( 'M j, Y g:ia', strtotime( $row->date ) ); ?></td>
                    </tr>
                    <?php
                } catch (Exception $e) {
                    ?>
                    <th class="check-column">
                        <input type="checkbox" disabled="true" name="id[<?php echo $row->id;?>]" value="<?php echo $row->id;?>">
                        <input type="hidden" name="order_id[<?php echo $row->id;?>]" value="<?php echo $row->order_id; ?>">
                        <input type="hidden" name="seller_id[<?php echo $row->id;?>]" value="<?php echo $row->seller_id; ?>">
                        <input type="hidden" name="refund_amount[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->refund_amount ); ?>">
                        <input type="hidden" name="refund_reason[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->refund_reason ); ?>">
                        <input type="hidden" name="item_qtys[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->item_qtys ); ?>">
                        <input type="hidden" name="item_totals[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->item_totals ); ?>">
                        <input type="hidden" name="item_tax_totals[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->item_tax_totals ); ?>">
                        <input type="hidden" name="restock_items[<?php echo $row->id;?>]" value="<?php echo esc_attr( $row->restock_items ); ?>">
                    </th>
                    <td>
                        <strong><?php echo '#' . $row->order_id; ?> <?php _e( '( Invalid order )', 'dokan' ); ?></strong>
                        <div class="row-actions">
                            <span class="trash"><!-- | --><a href="#" class="dokan-refund-action" data-status="delete" data-refund_id = "<?php echo $row->id; ?>"><?php _e( 'Delete', 'dokan' ); ?></a></span>
                        </div>
                    </td>
                    <td><?php echo the_author_meta( 'display_name', $row->seller_id ); ?></td>
                    <td><?php echo wc_price( $row->refund_amount ); ?></td>
                    <td><?php echo $row->refund_reason; ?></td>
                    <td><?php echo get_post_meta( $row->order_id, '_payment_method_title', true ); ?></td>
                    <td><?php echo date_i18n( 'M j, Y g:ia', strtotime( $row->date ) ); ?></td>
                    <?php

                }
                $count++;
            }
        } else {
            ?>
                <tr>
                    <td colspan="8">
                        <?php _e( 'No results found', 'dokan' ) ?>
                    </td>
                </tr>
                <?php
            }
            ?>
            </table>

            <div class="tablenav bottom">

                <div class="alignleft actions bulkactions">
                    <select name="dokan_refund_bulk">
                        <option value="-1" selected="selected"><?php _e( 'Bulk Actions', 'dokan' ); ?></option>

                        <?php if ( $status == 'pending' ) { ?>

                            <option value="approve"><?php _e( 'Approve Requests', 'dokan' ); ?></option>
                            <option value="cancel"><?php _e( 'Mark as Cancelled', 'dokan' ); ?></option>

                        <?php } elseif ( $status == 'completed' ) { ?>

                            <option value="cancel"><?php _e( 'Mark as Cancelled', 'dokan' ); ?></option>
                            <option value="pending"><?php _e( 'Mark Pending', 'dokan' ); ?></option>

                        <?php } elseif ( $status == 'cancelled' ) { ?>

                            <option value="approve"><?php _e( 'Approve Requests', 'dokan' ); ?></option>
                            <option value="pending"><?php _e( 'Mark Pending', 'dokan' ); ?></option>

                        <?php } ?>

                        <?php if ( $result ) { ?>
                            <option value="delete"><?php _e( 'Delete', 'dokan' ); ?></option>
                            <option value="paypal"><?php _e( 'Download PayPal mass payment file', 'dokan' ); ?></option>
                        <?php } ?>
                    </select>

                    <input type="hidden" name="status_page" value="<?php echo $status; ?>">
                    <input type="submit" name="" id="doaction2" class="button button-primary" value="<?php esc_attr_e( 'Apply', 'dokan' ); ?>">
                </div>

                <?php if ( $result ) {
                    $counts = dokan_get_refund_count();
                    $num_of_pages = ceil( $counts[$status] / $limit );
                    $page_links = paginate_links( array(
                        'base'      => add_query_arg( 'paged', '%#%' ),
                        'format'    => '',
                        'prev_text' => __( '&laquo;', 'dokan' ),
                        'next_text' => __( '&raquo;', 'dokan' ),
                        'total'     => $num_of_pages,
                        'current'   => $pagenum
                    ) );

                    if ( $page_links ) {
                        echo '<div class="tablenav-pages">' . $page_links . '</div>';
                    }
                } ?>
            </div>

        </form>
        <?php $ajax_url = admin_url('admin-ajax.php'); ?>
        <style type="text/css">
            .refund-table {
                margin-top: 10px;
            }

            .refund-table td, .refund-table th {
                vertical-align: top;
            }

            .custom-spinner {
                background: url('images/spinner-2x.gif') no-repeat;
                background-position: 43% 9px;
                background-size: 20px 20px;
                opacity: .4;
                filter: alpha(opacity=40);
                width: 20px;
                height: 20px;
            }
        </style>
        <script>
            (function($){
                $(document).ready(function(){
                    var url = "<?php echo $ajax_url; ?>";

                    $('#dokan-admin-refund-action').on('click', 'a.dokan-refund-action', function(e) {
                        e.preventDefault();
                        var self = $(this);

                        self.closest( 'tr' ).addClass('custom-spinner');
                        data = {
                            action: 'dokan_refund_form_action',
                            formData : $('#dokan-admin-refund-action').serialize(),
                            status: self.data('status') ,
                            refund_id : self.data( 'refund_id' )
                        }

                        $.post(url, data, function( resp ) {

                            if( resp.success ) {
                                if ( self.data('status') == 'approve' ) {
                                    rdata = resp.data.data;
                                    rdata.action = 'woocommerce_refund_line_items';
                                    rdata.api_refund = self.is( '.do-api-refund' );
                                    rdata.security = dokan_refund.order_item_nonce;
                                    $.post(url, rdata, function( res ) {
                                        if( res.success ) {
                                            self.closest( 'tr' ).removeClass('custom-spinner');
                                        }
                                    });
                                } else if ( self.data('status') == 'delete' ) {
                                    var rdata = {
                                        action:    'woocommerce_delete_refund',
                                        refund_id: resp.data.refund_processing_id,
                                        security:  dokan_refund.order_item_nonce,
                                    };
                                    $.post( url, rdata, function( res ) {
                                        self.closest( 'tr' ).removeClass('custom-spinner');
                                    });
                                }
                                window.location = resp.data.url;
                            } else {
                                self.closest( 'tr' ).removeClass('custom-spinner');
                                alert( 'Something wrong' );
                            }
                        });

                    });
                });
            })(jQuery)
        </script>
        <?php
    }
}
