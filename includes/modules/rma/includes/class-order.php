<?php

/**
* Order Manage
*/
class Dokan_RMA_Order {

    use Dokan_RMA_Common;

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'order_item_meta' ], 10, 3 );
        add_action( 'woocommerce_order_status_changed', [ $this, 'order_status_changed' ], 10, 4 );

        if ( is_admin() ) {
            add_action( 'woocommerce_before_order_itemmeta', [ $this, 'render_order_item_warranty' ], 10, 3 );
            add_action( 'woocommerce_order_item_meta_end', [ $this, 'render_order_item_warranty' ], 10, 3 );
        }

        // My account page custom rewrite for order
        add_action( 'init', [ $this, 'rewrite_endpoint' ] );
        add_filter( 'query_vars', [ $this, 'add_query_vars' ] );
        add_filter( 'the_title', [ $this, 'endpoint_title' ] );
        add_filter( 'woocommerce_account_menu_items', [ $this, 'dokan_rma_requests_link' ], 50 );
        add_action( 'woocommerce_account_request-warranty_endpoint', [ $this, 'content_request_warranty' ] );
        add_action( 'woocommerce_account_rma-requests_endpoint', [ $this, 'content_rma_requests' ] );
        add_action( 'woocommerce_account_view-rma-requests_endpoint', [ $this, 'content_rma_requests_view' ] );

        // My order list table actions
        add_filter( 'woocommerce_my_account_my_orders_actions', [ $this, 'request_button' ], 10, 2 );
        add_filter( 'dokan_my_account_my_sub_orders_actions', [ $this, 'request_button' ], 10, 2 );
    }

    /**
     * Register rewrite endpoint for license upgrade pages
     *
     * @return void
     */
    public function rewrite_endpoint() {
        add_rewrite_endpoint( 'request-warranty', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'rma-requests', EP_ROOT | EP_PAGES );
        add_rewrite_endpoint( 'view-rma-requests', EP_ROOT | EP_PAGES );
    }

    /**
     * Register the query vars
     *
     * @param array
     *
     * @return array
     */
    public function add_query_vars( $vars ) {
        $vars[] = 'request-warranty';
        $vars[] = 'rma-requests';
        $vars[] = 'view-rma-requests';

        return $vars;
    }

    /**
     * Set endpoint title.
     *
     * @since 1.0.0
     *
     * @param string $title
     *
     * @return string
     */
    public function endpoint_title( $title ) {
        global $wp_query;
        $is_endpoint = isset( $wp_query->query_vars[ 'request-warranty' ] );

        if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
            $title = __( 'Request a Warranty', 'dokan' );
            remove_filter( 'the_title', array( $this, 'endpoint_title' ) );
        }

        return $title;
    }

    /**
     * Request warrany template for customer end
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function content_request_warranty() {
        dokan_get_template_part( 'rma/customer-order', '', array(
            'is_rma'   => true,
        ) );
    }

    /**
     * View details RMA in customer END
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function content_rma_requests_view() {
        $warrnty_requests = new Dokan_RMA_Warranty_Request();
        $conversation_request = new Dokan_RMA_Conversation();

        $request_id   = get_query_var( 'view-rma-requests' );
        $request      = $warrnty_requests->get( $request_id );
        $conversations = $conversation_request->get( [ 'request_id' => $request_id ] );

        dokan_get_template_part( 'rma/customer-rma-single-request', '', array(
            'is_rma'        => true,
            'request'       => $request,
            'conversations' => $conversations
        ) );
    }

    /**
     * Load content for all RMA requests
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function content_rma_requests() {
        $warrnty_requests = new Dokan_RMA_Warranty_Request();

        $data           = [];
        $pagination_html = '';
        $item_per_page  = 20;
        $total_count    = dokan_get_warranty_request( [ 'count' => true ] );
        $page           = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
        $offset         = ( $page * $item_per_page ) - $item_per_page;
        $total_page     = ceil( $total_count['total_count']/$item_per_page );

        if ( ! empty( $_GET['status'] ) ) {
            $data['status'] = $_GET['status'];
        }

        $data['number']      = $item_per_page;
        $data['offset']      = $offset;
        $data['customer_id'] = dokan_get_current_user_id();

        if( $total_page > 1 ){
            $pagination_html = '<div class="pagination-wrap">';
            $page_links = paginate_links( array(
                'base'      => add_query_arg( 'cpage', '%#%' ),
                'format'    => '',
                'type'      => 'array',
                'prev_text' => __( '&laquo; Previous', 'dokan-lite' ),
                'next_text' => __( 'Next &raquo;', 'dokan-lite' ),
                'total'     => $total_page,
                'current'   => $page
            ) );
            $pagination_html .= '<ul class="pagination"><li>';
            $pagination_html .= join( "</li>\n\t<li>", $page_links );
            $pagination_html .= "</li>\n</ul>\n";
            $pagination_html .= '</div>';
        };

        dokan_get_template_part( 'rma/customer-rma-requests', '', array(
            'is_rma'          => true,
            'requests'        => $warrnty_requests->all( $data ),
            'total_count'     => $total_count,
            'pagination_html' => $pagination_html
        ) );
    }

    /**
     * List of all RMA request for a customer
     *
     * @param array $menu_links
     *
     * @return array
     */
    public function dokan_rma_requests_link( $menu_links ){
        $menu_links = array_slice( $menu_links, 0, 5, true )
        + array( 'rma-requests' => __( 'RMA Requests', 'dokan' ) )
        + array_slice( $menu_links, 5, NULL, true );

        return $menu_links;
    }

    /**
     * Show request warranty button
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function request_button( $actions, $order ) {
        $allowed_status = dokan_get_option( 'rma_order_status', 'dokan_rma', 'wc-completed' );

        if ( $allowed_status != 'wc-' . $order->get_status() ) {
            return $actions;
        }

        $url = esc_url_raw( wc_get_account_endpoint_url( 'request-warranty' ) . $order->get_id() ) ;
        $actions['request_warranty'] = array( 'url' => $url, 'name' => __( 'Request Warranty', 'dokan' ) );
        return $actions;
    }

    /**
     * Listens to order status changes and sets the completed date if the current
     * order status matches the start status of the warranty period
     *
     * @param int       $order_id
     * @param string    $old_status
     * @param string    $new_status
     * @param WC_Order  Actual order
     */
    public function order_status_changed( $order_id, $old_status, $new_status, $order ) {
        // update order's date of completion
        $handler = function () use ( $order ) {
            $order->set_date_completed( current_time( 'mysql' ) );
            $order->save();
        };

        $this->handle_status_change( $order_id, $new_status, $handler );
    }

    /**
     * Handler for the order status change.
     *
     * @param int       $order_id
     * @param string    $new_status
     * @param callable  $handler
     */
    private function handle_status_change( $order_id, $new_status, $handler ) {
        $order = wc_get_order( $order_id );

        if ( 'completed' !== $new_status ) {
            return;
        }

        $items          = $order->get_items();
        $has_warranty   = false;

        foreach ( $items as $item ) {
            $warranty       = false;
            $addon_index    = false;
            $metas          = (isset($item['item_meta'])) ? $item['item_meta'] : array();

            foreach ( $metas as $key => $value ) {
                $value = version_compare( WC_VERSION, '3.0', '<' ) ? $value[0] : $value;

                if ( $key == '_item_warranty' ) {
                    $warranty = maybe_unserialize( $value );
                }
            }

            if ( $warranty ) {
                $handler( $order );
                break; // only need to update once per order
            }
        }
    }

    /**
     * Include add-ons line item meta.
     *
     * @since 1.0.0
     *
     * @param  WC_Order_Item_Product $item          Order item data.
     * @param  string                $cart_item_key Cart item key.
     * @param  array                 $values        Order item values.
     *
     * @return  void
     */
    public function order_item_meta( $item, $cart_item_key, $values ) {
        $_product       = $values['data'];
        $_product_id    = ( version_compare( WC_VERSION, '3.0', '<' ) && isset( $_product->variation_id ) ) ? $_product->variation_id : $_product->get_id();
        $warranty       = $this->get_settings( $_product_id );
        $warranty_label = $warranty['label'];

        if ( $warranty && 'no_warranty' !== $warranty['type'] ) {
            $item_id = $item->save();

            if ( $warranty['type'] == 'addon_warranty' ) {
                $warranty_index = isset( $values['dokan_warranty_index'] ) ? $values['dokan_warranty_index'] : false;
                wc_add_order_item_meta( $item_id, '_dokan_item_warranty_selected', $warranty_index );
            }

            if ( 'no_warranty' !== $warranty['type'] ) {
                wc_add_order_item_meta( $item_id, '_dokan_item_warranty', $warranty );
            }
        }
    }

    /**
     * Display an order item's warranty data
     *
     * @param int           $item_id
     * @param array         $item
     * @param WC_Product    $product
     */
    public function render_order_item_warranty( $item_id, $item, $product ) {
        global $post, $wp;

        if ( $item['type'] != 'line_item' ) {
            return;
        }

        $warranty = wc_get_order_item_meta( $item_id, '_dokan_item_warranty', true );

        if ( isset( $_GET['order_id'] ) ) {
            $order_id = $_GET['order_id'];
        } elseif( isset( $wp->query_vars['view-order'] ) && ! empty( $wp->query_vars['view-order'] ) ) {
            $order_id = $wp->query_vars['view-order'];
        } elseif ( $post ) {
            $order_id = $post->ID;
        }

        if ( $warranty && ! empty( $order_id ) ) {
            $name = $value = $expiry = false;

            $order = wc_get_order( $order_id );
            $order_date = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;

            if ( empty( $warranty['label'] ) ) {
                $product_warranty = $this->get_settings( $item['product_id'] );
                $warranty['label'] = $product_warranty['label'];
            }
            if ( $warranty['type'] == 'addon_warranty' ) {
                $addons         = $warranty['addon_settings'];
                $warranty_index = wc_get_order_item_meta( $item_id, '_dokan_item_warranty_selected', true );

                if ( $warranty_index !== false && isset( $addons[$warranty_index] ) && !empty( $addons[$warranty_index] ) ) {
                    $addon  = $addons[$warranty_index];
                    $name   = $warranty['label'];
                    $unit  = dokan_rma_get_duration_value( $addon['duration'], $addon['length'] );
                    $value = $addon['length'] . ' ' . $unit;

                    if ( $order_date ) {
                        $expiry = dokan_rma_get_date( $order_date, $addon['length'], $addon['duration'] );
                    }

                }
            } elseif ( $warranty['type'] == 'included_warranty' ) {
                if ( $warranty['length'] == 'limited' ) {
                    $name   = $warranty['label'];
                    $unit  = dokan_rma_get_duration_value( $warranty['length_duration'], $warranty['length_value'] );
                    $value = $warranty['length_value'] . ' ' . $unit;

                    if ( $order_date ) {
                        $expiry = dokan_rma_get_date( $order_date, $warranty['length_value'], $warranty['length_duration'] );
                    }
                }
            }

            if ( !$name || ! $value ) {
                return;
            }

            ?>
            <div class="view">
                <table cellspacing="0" class="display_meta">
                    <tr>
                        <th style="width: 39%;"><?php echo wp_kses_post( $name ); ?>:</th>
                        <td>
                        <?php
                            echo wp_kses_post( $value );

                            if ( $expiry ) {
                                if ( current_time('timestamp') > strtotime( $expiry ) ) {
                                    echo ' <small>(expired on '. $expiry .')</small>';
                                } else {
                                    echo ' <small>(expires '. $expiry .')</small>';
                                }
                            }
                        ?>
                        </td>
                    </tr>
                </table>
            </div>
            <?php
        }
    }

}
