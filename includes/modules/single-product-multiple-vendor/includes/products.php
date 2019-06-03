<?php

/**
* Product related functionality
*
* @package Dokan
*/
class Dokan_SPMV_Products {

    /**
     * Load automatically when class initiate
     *
     * @since 1.0.0
     */
    public function __construct() {
        $enable_option = dokan_get_option( 'enable_pricing', 'dokan_spmv', 'off' );
        $display_position = dokan_get_option( 'available_vendor_list_position', 'dokan_spmv', 'below_tabs' );

        if ( ! filter_var( $enable_option, FILTER_VALIDATE_BOOLEAN ) ) {
            return;
        }

        add_action( 'template_redirect', array( $this, 'handle_sell_item_action' ), 10 );
        add_action( 'woocommerce_single_product_summary', array( $this, 'show_sell_now_btn' ), 32 );
        add_action( 'dokan_product_deleted', array( $this, 'delete_product_meta' ), 20 );
        add_action( 'delete_post', array( $this, 'delete_product' ) );
        add_action( 'wp_trash_post', array( $this, 'trash_product' ) );
        add_action( 'untrashed_post', array( $this, 'untrash_product' ) );
        add_action( 'transition_post_status',  array( $this, 'on_product_status_changes' ), 10, 3 );
        add_action( 'dokan_product_updated',  array( $this, 'update_product_status' ), 10, 3 );

        if ( 'below_tabs' == $display_position ) {

            add_action( 'woocommerce_after_single_product_summary', array( $this, 'show_vendor_comparison' ), 1 );

        } else if ( 'inside_tabs' == $display_position ) {

            add_filter( 'woocommerce_product_tabs', array( $this, 'show_vendor_comparison_inside_tab' ) );

        } else if ( 'after_tabs' == $display_position  ) {
            add_action( 'woocommerce_after_single_product_summary', array( $this, 'show_vendor_comparison' ), 12 );
        }
    }

    /**
     * Check is seller is elligible for sell this item
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function is_valid_user( $product_id ) {
        if ( ! is_user_logged_in() ) {
            return false;
        }

        $user_id = get_current_user_id();

        if ( ! dokan_is_user_seller( $user_id ) ) {
            return false;
        }

        $product_author = get_post_field( 'post_author', $product_id );

        if ( $user_id == $product_author ) {
            return false;
        }

        if ( $this->check_already_cloned( $product_id ) ) {
            return false;
        }

        return true;
    }

    /**
     * Check already cloned this product
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function check_already_cloned( $product_id ) {
        global $wpdb;

        $map_id = get_post_meta( $product_id, '_has_multi_vendor', true );
        $user_id = get_current_user_id();

        if ( empty( $map_id ) ) {
            return false;
        }

        $sql     = "SELECT * FROM `{$wpdb->prefix}dokan_product_map` WHERE `map_id`= '$map_id' AND `seller_id` = '$user_id' AND `is_trash` IN (0,2,3)";
        $results = $wpdb->get_row( $sql );

        if ( $results ) {
            return true;
        }

        return false;
    }

    /**
     * Handle sell item form submission
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function handle_sell_item_action() {

        if ( ! isset( $_POST['dokan_sell_this_item'] ) ) {
            return;
        }

        $user_id = ! empty( $_POST['user_id'] ) ? $_POST['user_id'] : 0;
        $current_product_author = ! empty( $_POST['current_product_author'] ) ? $_POST['current_product_author'] : 0;
        $product_id = ! empty( $_POST['product_id'] ) ? $_POST['product_id'] : 0;

        if ( ! $user_id || ! $product_id || $current_product_author ) {
            return;
        }

        if ( ! $this->is_valid_user( $product_id ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['dokan-sell-item-nonce'], 'dokan-sell-item-action' ) ) {
            return;
        }

        $wo_dup = new WC_Admin_Duplicate_Product();
        $update_product_ids = array();

        // Compatibility for WC 3.0+
        if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
            // For latest version 3.0+
            $product = wc_get_product( $product_id );
            $clone_product =  $wo_dup->product_duplicate( $product );
            $clone_product_id =  $clone_product->get_id();
        } else {
            // For older version < 3.0+
            $post = get_post( $product_id );
            $product = wc_get_product( $product_id );
            $clone_product_id =  $wo_dup->duplicate_product( $post );
        }

        $product_status = apply_filters( 'dokan_cloned_product_status', dokan_get_new_post_status() );

        $has_multivendor = get_post_meta( $product_id, '_has_multi_vendor', true );

        if ( ! $has_multivendor ) {
            $has_multivendor = $this->get_next_map_id();
            $update_product_ids[] = $product_id;
        }

        $update_product_ids[] = $clone_product_id;

        if ( $this->set_map_id( $has_multivendor, $update_product_ids ) ){
            update_post_meta( $product_id, '_has_multi_vendor', $has_multivendor );
            update_post_meta( $clone_product_id, '_has_multi_vendor', $has_multivendor );
        }

        wp_update_post(
            array(
                'ID' => intval( $clone_product_id ),
                'post_title' => $product->get_title(),
                'post_status' => $product_status,
                'post_author' => $user_id
            )
        );

        $this->update_product_visibility( $clone_product_id, $product_status );

        do_action( 'dokan_spmv_create_clone_product', $clone_product_id, $product_id );
        wp_redirect( dokan_edit_product_url( $clone_product_id ) );
        exit();
    }

    /**
     * Show vendor comparison inside product tabs
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function show_vendor_comparison_inside_tab( $tabs ) {
        $title = dokan_get_option( 'available_vendor_list_title', 'dokan_spmv', __( 'Other Available Vendor', 'dokan' ) );

        $tabs['vendor_comaprison'] = array(
            'title'    => $title,
            'priority' => 100,
            'callback' => array( $this, 'show_vendor_comparison' )
        );

        return $tabs;
    }

    /**
     * Added Sell this item btn
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function show_sell_now_btn() {
        global $product;

        if ( $product->get_type() == 'product_pack' ) {
            return;
        }

        if ( ! $this->is_valid_user( $product->get_id() ) ) {
            return;
        }

        $sell_item_btn_txt = dokan_get_option( 'sell_item_btn', 'dokan_spmv', __( 'Sell This Item', 'dokan' ) );
        ?>
        <form method="post">
            <?php wp_nonce_field( 'dokan-sell-item-action', 'dokan-sell-item-nonce' ); ?>
            <button name="dokan_sell_this_item" class="dokan-btn dokan-btn-theme"><?php echo $sell_item_btn_txt; ?></button>
            <input type="hidden" name="product_id" value="<?php echo $product->get_id(); ?>">
            <input type="hidden" name="user_id" value="<?php echo get_current_user_id(); ?>">
        </form>
        <?php
    }

    /**
     * Get mapping status
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_product_map_status_code( $status = '' ) {
        $statuses = array(
            'publish' => 0,
            'trash'   => 1,
            'pending' => 2,
            'draft'   => 3
        );

        if ( ! empty( $status ) ) {
            return isset( $statuses[$status] ) ? $statuses[$status] : '';
        }

        return $statuses;
    }

    /**
     * Delete map and meta data for product
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function delete_product_meta( $product_id ) {
        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! $product_id ) {
            return;
        }

        $this->delete_map_id( intval( $product_id ) );
        delete_post_meta( $product_id, '_has_multi_vendor' );
    }

    /**
     * Delete product form admin area
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function delete_product( $product_id ) {
        if ( ! current_user_can( 'delete_posts' ) || ! $product_id ) {
            return;
        }

        $post_type = get_post_type( $product_id );

        if ( 'product' == $post_type ) {
            $this->delete_map_id( intval( $product_id ) );
            delete_post_meta( $product_id, '_has_multi_vendor' );
        }
    }
    /**
     * Update trash flag during product trash
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function trash_product( $product_id ) {
        global $wpdb;

        if ( ! is_user_logged_in() || ! is_admin() ) {
            return;
        }

        if ( ! $product_id ) {
            return;
        }

        $post_type = get_post_type( $product_id );

        if ( 'product' == $post_type ) {
            $this->update_product_visibility( $product_id, 'trash' );
        }

    }

    /**
     * Untrash product and restore
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function untrash_product( $product_id ) {
        global $wpdb;

        if ( ! is_user_logged_in() || ! is_admin() ) {
            return;
        }

        if ( ! $product_id ) {
            return;
        }

        $post_type = get_post_type( $product_id );

        if ( 'product' == $post_type ) {
            $product = wc_get_product( $product_id );
            if ( 'publish' == $product->get_status() ) {
                $this->update_product_visibility( $product_id, 'publish' );
            }
        }
    }

    /**
     * Trigger product status changes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function on_product_status_changes( $new_status, $old_status, $post ) {
        if ( ! is_user_logged_in() || ! is_admin() ) {
            return;
        }

        if ( ! $post->ID ) {
            return;
        }

        if ( ! empty( $post->post_type ) && 'product' == $post->post_type ) {

            if ( 'pending' == $new_status ) {
                $this->update_product_visibility( $post->ID, 'pending' );
            }

            if ( 'draft' == $new_status  ) {
                $this->update_product_visibility( $post->ID, 'draft' );
            }

            if ( 'publish' == $new_status ) {
                $this->update_product_visibility( $post->ID, 'publish' );
            }
        }
    }

    /**
     * Udpate product visibility status from seller end
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update_product_status( $product_id ) {
        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! $product_id ) {
            return;
        }

        $product = wc_get_product( $product_id );

        if ( 'draft' == $product->get_status() ) {
            $this->update_product_visibility( $product_id, 'draft' );
        }
    }

    /**
     * Udpate product visibility in product mapping table
     *
     * @since 1.0.0
     *
     * @param integer $product_id
     * @param string $visibility [ 0 -> publish | 1 -> trash | 2 -> pending | 3 -> draft ]
     *
     * @return void
     */
    public function update_product_visibility( $product_id, $visibility ) {
        global $wpdb;

        $is_trash = $this->get_product_map_status_code( $visibility );

        $table = $wpdb->prefix . 'dokan_product_map';
        $wpdb->update(
            $table,
            array(
                'is_trash' => $is_trash
            ),
            array( 'product_id' => $product_id ),
            array( '%d' ),
            array( '%d' )
        );
    }
    /**
     * Show Vendor comparison
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function show_vendor_comparison() {
        global $product;

        if ( ! $product ) {
            return;
        }

        $lists = $this->get_other_reseller_vendors( $product->get_id() );

        if ( $lists ) {
            ?>
            <div class="dokan-other-vendor-camparison">

                <h3>
                    <?php echo dokan_get_option( 'available_vendor_list_title', 'dokan_spmv', __( 'Other Available Vendor', 'dokan' ) ); ?>
                </h3>

                <div class="table dokan-table dokan-other-vendor-camparison-table">

                    <?php foreach ( $lists as $key => $list ): ?>
                        <?php
                            $product_obj    = wc_get_product( $list->product_id );
                            $post_author_id = get_post_field( 'post_author', $product_obj->get_id() );
                            $seller_info    = dokan_get_store_info( $post_author_id );
                            $rating_count   = $product_obj->get_rating_count();
                            $review_count   = $product_obj->get_review_count();
                            $average        = $product_obj->get_average_rating();

                            if ( ! $product_obj->is_visible() ) {
                                continue;
                            }
                        ?>

                    <div class="table-row <?php echo ( $list->product_id == $product->get_id() ) ? 'active' : ''; ?>">
                        <div class="table-cell vendor">
                            <?php echo get_avatar( $post_author_id, 52 ); ?>
                            <a href="<?php echo dokan_get_store_url( $post_author_id ); ?>"><?php echo $seller_info['store_name'] ?></a>
                        </div>
                        <div class="table-cell price">
                            <span class="cell-title"><?php _e( 'Price', 'dokan' ); ?></span>
                            <?php echo $product_obj->get_price_html(); ?>
                        </div>
                        <div class="table-cell rating">
                            <span class="cell-title"><?php _e( 'Rating', 'dokan' ); ?></span>
                            <div class="woocommerce-product-rating">
                                <?php echo wc_get_rating_html( $average, $rating_count ); ?>
                                <?php if ( comments_open() ) : ?><a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'dokan' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>)</a><?php endif ?>
                            </div>
                        </div>
                        <div class="table-cell action-area">
                            <a href="<?php echo dokan_get_store_url( $post_author_id ); ?>" class="dokan-btn tips link" title="<?php _e( 'View Store', 'dokan' ); ?>">
                                <i class="fa fa-external-link"></i>
                            </a>
                            <a href="<?php echo $product_obj->get_permalink(); ?>" class="dokan-btn tips view" title="<?php _e( 'View Product', 'dokan' ); ?>">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                            <?php if ( 'simple' == $product_obj->get_type() ): ?>
                                <?php
                                echo sprintf( '<a href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s" title="%s">%s</a>',
                                    esc_url( $product_obj->add_to_cart_url() ),
                                    1,
                                    esc_attr( $product_obj->get_id() ),
                                    esc_attr( $product_obj->get_sku() ),
                                    'dokan-btn tips cart',
                                    __( 'Add to cart', 'dokan' ),
                                    '<i class="fa fa-shopping-cart"></i>'
                                );
                                ?>
                            <?php elseif ( 'variable' == $product_obj->get_type() ) : ?>
                                <a href="<?php echo $product_obj->get_permalink(); ?>" class="dokan-btn tips bars" title="<?php _e( 'Select Options', 'dokan' ); ?>"><i class="fa fa-bars"></i></a>
                            <?php endif ?>
                        </div>
                    </div>

                    <?php endforeach ?>

                </div>
            </div>

            <style>
                .dokan-other-vendor-camparison {
                    clear: both;
                    margin: 10px 0px 20px;
                }

                .dokan-other-vendor-camparison h3 {
                    margin-bottom: 15px;
                }

                .dokan-other-vendor-camparison-table {
                    margin:50px 0;
                }
                .table-row {
                    display: table;
                    background: white;
                    border-radius: 5px;
                    border: 1px solid #edf2f7;
                    padding: 20px;
                    width: 100%;
                    margin-bottom: 15px;
                    box-shadow: 1.21px 4.851px 27px 0px rgba(202, 210, 240, 0.2);
                }

                .table-row.active {
                    border: 1px solid #e3e3e3;
                }

                .table-cell {
                    display: table-cell;
                    vertical-align: middle;
                }
                .table-cell.vendor {
                    width: 45%;
                }
                .table-cell.price {
                    width: 15%;
                }
                .table-cell.rating {
                    width: 20%;
                }
                .table-cell.action-area {
                    width: 20%;
                    text-align: center;
                }

                .table-cell.vendor img{
                    display: inline-block;
                    vertical-align: middle;
                    border-radius: 3px;
                }
                .table-cell.vendor a{
                    display: inline-block;
                    vertical-align: middle;
                    text-decoration: none;
                    color: black;
                    font-size: 20px;
                    line-height: 1.2em;
                    margin-left: 15px;
                }
                .table-cell .woocommerce-product-rating{
                    margin-bottom:0 !important;
                }
                span.cell-title {
                    display: block;
                    font-size: 16px;
                    margin-bottom: 10px;
                    color: #82959b;
                }
                .table-cell .woocommerce-Price-amount{
                    color: #e74c3c;
                    font-size: 20px;
                    line-height: 1.2em;
                }

                .table-cell .dokan-btn {
                    padding: 5px 8px;
                    font-size: 16px;
                }
                .table-cell .dokan-btn.link {
                    color: #8e44ad;
                }
                .table-cell .dokan-btn.view {
                    color: #008fd5;
                }
                .table-cell .dokan-btn.cart {
                    color: #d35400;
                }
                .table-cell .dokan-btn:hover {
                    background-color: #f5f7fa;
                    color: inherit;
                }

                @media screen and (max-width: 767px){
                    .table-row {
                        display: block;
                        padding:0;
                        width: 100%;
                    }
                    .table-cell {
                        display: block;
                        width: 100% !important;
                        text-align: center;
                    }
                    .table-cell.vendor img{
                        display: block;
                        margin: 30px auto;
                    }
                    .table-cell.vendor a{
                        display: block;
                        margin: 0 20px;
                    }
                    .table-cell.price{
                        padding: 20px 0;
                    }
                    span.cell-title{
                        display: none;
                    }

                    .action-area{
                        border-top: 1px solid #e5edf0;
                        margin-top: 20px;
                        padding: 10px 0;
                    }
                }
            </style>

            <script>
                ;(function($) {
                    $(document).ready( function() {
                        $('.tips').tooltip();
                    })
                })(jQuery);
            </script>
            <?php
        }
    }

    /**
     * Get mapping ID for next execution
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_next_map_id() {
        global $wpdb;

        $sql = "SELECT MAX(`map_id`) as max_id FROM `{$wpdb->prefix}dokan_product_map`";
        $current_id = $wpdb->get_var( $sql );

        if ( ! $current_id ) {
            return 1;
        }

        return $current_id+1;
    }

    /**
     * Set mapping ids for product
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function set_map_id( $map_id, $product_ids ) {
        global $wpdb;

        $values = array();
        foreach ( $product_ids as $product_id ) {
            $seller_id = get_post_field( 'post_author', $product_id );
            $values[] = '(' . $map_id . ',' . $product_id . ',' . $seller_id . ')';
        }

        $values = implode( ',', $values );

        $result = $wpdb->query( "INSERT INTO `{$wpdb->prefix}dokan_product_map`
            ( map_id, product_id, seller_id )
            VALUES $values"
        );

        if ( $result ) {
            return true;
        }

        return false;
    }

    /**
     * Delete product map data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function delete_map_id( $product_id ) {
        global $wpdb;

        if ( ! $product_id ) {
            return false;
        }

        $table = $wpdb->prefix . 'dokan_product_map';
        $wpdb->delete( $table, array( 'product_id' => $product_id ), array( '%d' ) );
    }

    /**
     * Get other reseller vendors
     *
     * @since 1.0.0
     *
     * @param integer $product_id
     *
     * @return void
     */
    public function get_other_reseller_vendors( $product_id ) {
        global $wpdb;

        if ( ! $product_id ) {
            return false;
        }

        $has_multivendor = get_post_meta( $product_id, '_has_multi_vendor', true );

        if ( empty( $has_multivendor ) ) {
            return false;
        }

        $sql     = "SELECT `product_id` FROM `{$wpdb->prefix}dokan_product_map` WHERE `map_id`= '$has_multivendor' AND `product_id` != $product_id AND `is_trash` = 0";
        $results = $wpdb->get_results( $sql );

        if ( $results ) {
            return $results;
        }

        return false;
    }
}
