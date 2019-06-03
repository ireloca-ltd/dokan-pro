<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">
    <?php
    do_action( 'dokan_dashboard_content_before' );
    do_action( 'dokan_auction_product_listing_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-product-listing">
        <?php

            /**
             *  dokan_auction_product_listing_inside_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_auction_product_listing_inside_before' );
            do_action( 'dokan_before_listing_product' );
        ?>

        <?php do_action( 'dokan_before_listing_auction_product' ); ?>

            <article class="dokan-product-listing-area">

                <div class="product-listing-top dokan-clearfix">
                    <?php  dokan_auction_product_listing_status_filter(); ?>

                    <?php if ( current_user_can( 'dokan_add_auction_product' ) ): ?>
                        <span class="dokan-add-product-link">
                            <a href="<?php echo dokan_get_navigation_url( 'new-auction-product' ); ?>" class="dokan-btn dokan-btn-theme dokan-right"><i class="fa fa-briefcase">&nbsp;</i> <?php _e( 'Add New Auction Product', 'dokan' ); ?></a>
                        </span>
                    <?php endif ?>
                </div>

                <?php dokan_product_dashboard_errors(); ?>

                <table class="table table-striped product-listing-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'Image', 'dokan' ); ?></th>
                            <th><?php _e( 'Name', 'dokan' ); ?></th>
                            <th><?php _e( 'Status', 'dokan' ); ?></th>
                            <th><?php _e( 'SKU', 'dokan' ); ?></th>
                            <th><?php _e( 'Stock', 'dokan' ); ?></th>
                            <th><?php _e( 'Price', 'dokan' ); ?></th>
                            <th><?php _e( 'Type', 'dokan' ); ?></th>
                            <th><?php _e( 'Views', 'dokan' ); ?></th>
                            <th><?php _e( 'Date', 'dokan' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        global $post;
                        $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

                        $post_statuses = array( 'publish', 'draft', 'pending');

                        $args = array(
                            'post_status'         => $post_statuses,
                            'ignore_sticky_posts' => 1,
                            'orderby'             => 'post_date',
                            'author'              => dokan_get_current_user_id(),
                            'order'               => 'DESC',
                            'posts_per_page'      => -1,
                            'tax_query'           => array( array( 'taxonomy' => 'product_type', 'field' => 'slug', 'terms' => 'auction' ) ),
                            'auction_archive'     => TRUE,
                            'show_past_auctions'  => TRUE,
                            'paged'               => $pagenum
                        );

                        if ( isset( $_GET['post_status'] ) && in_array( $_GET['post_status'], $post_statuses ) ) {
                            $args['post_status'] = $_GET['post_status'];
                        }

                        // $original_post = $post;
                        $product_query = new WP_Query( $args );

                        if ( $product_query->have_posts() ) {
                            while ($product_query->have_posts()) {
                                $product_query->the_post();

                                $tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';
                                $product = dokan_wc_get_product( $post->ID );
                                $edit_url = add_query_arg( array('product_id' => $post->ID, 'action' => 'edit' ), dokan_get_navigation_url('auction') );

                                ?>
                                <tr<?php echo $tr_class; ?>>
                                    <td>
                                        <?php if ( current_user_can( 'dokan_edit_auction_product' ) ): ?>
                                            <a href="<?php echo $edit_url ?>"><?php echo $product->get_image(); ?></a>
                                        <?php else: ?>
                                            <a href="#"><?php echo $product->get_image(); ?></a>
                                        <?php endif ?>
                                    </td>
                                    <td>
                                        <?php if ( current_user_can( 'dokan_edit_auction_product' ) ): ?>
                                            <p><a href="<?php echo $edit_url ?>"><?php echo $product->get_title(); ?></a></p>
                                        <?php else: ?>
                                            <p><a href=""><?php echo $product->get_title(); ?></a></p>
                                        <?php endif ?>

                                        <div class="row-actions">
                                            <?php if ( current_user_can( 'dokan_edit_auction_product' ) ): ?>
                                                <span class="edit"><a href="<?php echo $edit_url; ?>"><?php _e( 'Edit', 'dokan' ); ?></a> | </span>
                                            <?php endif ?>

                                            <?php if ( current_user_can( 'dokan_delete_auction_product' ) ): ?>
                                                <span class="delete"><a onclick="return confirm('Are you sure?');" href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'dokan-delete-auction-product', 'product_id' => $post->ID ), dokan_get_navigation_url('auction') ), 'dokan-delete-auction-product' ); ?>"><?php _e( 'Delete Permanently', 'dokan' ); ?></a> | </span>
                                            <?php endif ?>

                                            <span class="view"><a href="<?php echo get_permalink( $product->get_id() ); ?>" rel="permalink"><?php _e( 'View', 'dokan' ); ?></a></span>
                                        </div>
                                    </td>
                                    <td class="post-status">
                                        <label class="dokan-label <?php echo $post->post_status; ?>"><?php echo dokan_get_post_status( $post->post_status ); ?></label>
                                    </td>
                                    <td>
                                        <?php
                                        if ( $product->get_sku() ) {
                                            echo $product->get_sku();
                                        } else {
                                            echo '<span class="na">&ndash;</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ( $product->is_in_stock() ) {
                                            echo '<mark class="instock">' . __( 'In stock', 'dokan' ) . '</mark>';
                                        } else {
                                            echo '<mark class="outofstock">' . __( 'Out of stock', 'dokan' ) . '</mark>';
                                        }

                                        if ( $product->managing_stock() ) :
                                            if ( version_compare( WC_VERSION, '2.7', '>' ) ) {
                                                echo ' &times; ' . $product->get_stock_quantity();
                                            } else {
                                                echo ' &times; ' . $product->get_total_stock();
                                            }
                                        endif;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ( $product->get_price_html() ) {
                                            echo $product->get_price_html();
                                        } else {
                                            echo '<span class="na">&ndash;</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $class = '';
                                        if( $product->get_type() == 'auction' ){
                                            if($product->is_closed())
                                                $class .= ' finished ';
                                            if($product->get_auction_fail_reason() == '1')
                                                $class .= ' no_bid fail ';
                                            if($product->get_auction_fail_reason() == '2')
                                                $class .= ' no_reserve fail';
                                            if($product->get_auction_closed() == '3')
                                                $class .= ' sold ';
                                            if($product->get_auction_payed())
                                                $class .= ' payed ';
                                            echo "<span class='tips' title='Auction'><i class='fa fa-gavel ".$class."'></i></span>";
                                        }
                                        if ( get_post_meta( $product->get_id() , '_auction' , TRUE)){
                                            echo "<span class='product-type tips auction' title='Auction'><i class='fa fa-gavel order'></i><span>";
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo (int) get_post_meta( $post->ID, 'pageview', true ); ?>
                                    </td>
                                    <td class="post-date">
                                        <?php
                                        if ( '0000-00-00 00:00:00' == $post->post_date ) {
                                            $t_time = $h_time = __( 'Unpublished', 'dokan' );
                                            $time_diff = 0;
                                        } else {
                                            $t_time = get_the_time( __( 'Y/m/d g:i:s A', 'dokan' ) );
                                            $m_time = $post->post_date;
                                            $time = get_post_time( 'G', true, $post );

                                            $time_diff = time() - $time;

                                            if ( $time_diff > 0 && $time_diff < 24 * 60 * 60 ) {
                                                $h_time = sprintf( __( '%s ago', 'dokan' ), human_time_diff( $time ) );
                                            } else {
                                                $h_time = mysql2date( __( 'Y/m/d', 'dokan' ), $m_time );
                                            }
                                        }

                                        echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, 'date', 'all' ) . '</abbr>';
                                        echo '<br />';
                                        if ( 'publish' == $post->post_status ) {
                                            _e( 'Published', 'dokan' );
                                        } elseif ( 'future' == $post->post_status ) {
                                            if ( $time_diff > 0 ) {
                                                echo '<strong class="attention">' . __( 'Missed schedule', 'dokan' ) . '</strong>';
                                            } else {
                                                _e( 'Scheduled', 'dokan' );
                                            }
                                        } else {
                                            _e( 'Last Modified', 'dokan' );
                                        }
                                        ?>
                                    </td>
                                </tr>

                            <?php } ?>

                        <?php } else { ?>
                            <tr>
                                <td colspan="7"><?php _e( 'No product found', 'dokan' ); ?></td>
                            </tr>
                        <?php } ?>

                    </tbody>

                </table>

                <?php
                wp_reset_postdata();

                $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

                if ( $product_query->max_num_pages > 1 ) {
                    echo '<div class="pagination-wrap">';
                    $page_links = paginate_links( array(
                        'current'   => $pagenum,
                        'total'     => $product_query->max_num_pages,
                        'base'      => add_query_arg( 'pagenum', '%#%' ),
                        'format'    => '',
                        'type'      => 'array',
                        'prev_text' => __( '&laquo; Previous', 'dokan' ),
                        'next_text' => __( 'Next &raquo;', 'dokan' )
                    ) );

                    echo '<ul class="pagination"><li>';
                    echo join("</li>\n\t<li>", $page_links);
                    echo "</li>\n</ul>\n";
                    echo '</div>';
                }
                ?>
            </article>

        <?php do_action( 'dokan_after_listing_auction_product' ); ?>
        <?php

            /**
             *  dokan_auction_product_listing_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_auction_product_listing_inside_after' );
        ?>
    </div><!-- #primary .content-area -->

     <?php
        /**
         *  dokan_dashboard_content_after hook
         *  dokan_withdraw_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_auction_product_listing_content_after' );
    ?>
</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>