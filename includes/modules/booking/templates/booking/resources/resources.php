<?php

?>
<header class="dokan-dashboard-header">
    <h1 class="entry-title"><?php _e( $title , 'dokan' ); ?>
        <span class="dokan-add-resource-link">
            <button href="#" class="dokan-btn dokan-btn-theme dokan-right"><i class="fa fa-briefcase">&nbsp;</i><?php _e( 'Add new Resource', 'dokan' ) ?></button>
        </span>
    </h1>
</header><!-- .dokan-dashboard-header -->

<?php
global $post;

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_before' );

            ?>
            <div class="dokan-product-listing">

                <article class="dokan-product-listing-area">

                    <table class="dokan-table dokan-table-striped product-listing-table">
                        <thead>
                            <tr>
                                <th><?php _e( 'Name', 'dokan' ); ?></th>
                                <th><?php _e( 'Parent', 'dokan' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

                            $post_statuses = array('publish', 'draft', 'pending');
                            $args          = array(
                                'post_type'      => 'bookable_resource',
                                'post_status'    => $post_statuses,
                                'posts_per_page' => 10,
                                'author'         => dokan_get_current_user_id(),
                                'orderby'        => 'post_date',
                                'order'          => 'DESC',
                                'paged'          => $pagenum,
                                );

                            if ( isset( $_GET['post_status']) && in_array( $_GET['post_status'], $post_statuses ) ) {
                                $args['post_status'] = $_GET['post_status'];
                            }

                            if( isset( $_GET['date'] ) && $_GET['date'] != 0 ) {
                                $args['m'] = $_GET['date'];
                            }

                            if( isset( $_GET['product_cat'] ) && $_GET['product_cat'] != -1 ) {
                                $args['tax_query']= array(
                                    'relation' => 'and',
                                    array(
                                        'taxonomy' => 'product_cat',
                                        'field' => 'id',
                                        'terms' => (int)  $_GET['product_cat'],
                                        'include_children' => false,
                                        ),
                                    array(
                                        'taxonomy' => 'product_type',
                                        'field'    => 'slug',
                                        'terms'    => 'booking',
                                        ),
                                    );
                            }

                            if ( isset( $_GET['product_search_name']) && !empty( $_GET['product_search_name'] ) ) {
                                $args['s'] = $_GET['product_search_name'];
                            }

                            $original_post = $post;
                            $product_query = new WP_Query( $args );

                            if ( $product_query->have_posts() ) {
                                while ($product_query->have_posts()) {
                                    $product_query->the_post();

                                    $tr_class = ($post->post_status == 'pending' ) ? ' class="danger"' : '';

                                    $edit_url = dokan_get_navigation_url('booking').'resources/edit/?id=' . $post->ID;
                                    ?>
                                    <tr<?php echo $tr_class; ?>>
                                    <td>
                                        <a href="<?php echo $edit_url; ?>"><?php echo $post->post_title; ?></a>
                                    </td>
                                    <td>
                                        <?php
                                        global $wpdb;
                                        $parents      = $wpdb->get_col( $wpdb->prepare( "SELECT product_id FROM {$wpdb->prefix}wc_booking_relationships WHERE resource_id = %d ORDER BY sort_order;", $post->ID ) );
                                        $parent_posts = array();
                                        foreach ( $parents as $parent_id ) {
                                            if( $parent_id == 0 ){
                                                continue;
                                            }
                                            $edit_link = dokan_get_navigation_url('booking').'edit/?product_id='.$parent_id;
                                            $parent_posts[] = "<a href='$edit_link'>" . get_the_title( $parent_id ) . '</a>';
                                        }
                                        echo $parent_posts ? implode( ', ', $parent_posts ) : __( 'N/A', 'dokan' );
                                        ?>
                                    </td>
                                    <td>
                                        <a class="dokan-btn dokan-btn-sm dokan-btn-theme" href ="<?php echo $edit_url  ?>"><?php _e( 'Edit', 'dokan' );  ?></a>
                                        <button class="dokan-btn dokan-btn-theme dokan-btn-sm btn-remove" data-id="<?php echo $post->ID ?>"><?php _e( 'Remove', 'dokan' );  ?></button>
                                    </td>
                                </tr>

                                <?php } ?>

                                <?php } else { ?>
                                <tr>
                                    <td colspan="7"><?php _e( 'No Resource found', 'dokan' ); ?></td>
                                </tr>
                                <?php } ?>

                            </tbody>

                        </table>

                        <?php
                        wp_reset_postdata();

                        $pagenum      = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                        $base_url = dokan_get_navigation_url('booking').'resources/';

                        if ( $product_query->max_num_pages > 1 ) {
                            echo '<div class="pagination-wrap">';
                            $page_links = paginate_links( array(
                                'current'   => $pagenum,
                                'total'     => $product_query->max_num_pages,
                                'base'      => $base_url. '%_%',
                                'format'    => '?pagenum=%#%',
                                'add_args'  => false,
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
                </div>

                <?php

            /**
             *  dokan_dashboard_content_before hook
             *
             *  @hooked get_dashboard_side_navigation
             *
             *  @since 2.4
             */
            do_action( 'dokan_dashboard_content_inside_after' );
            do_action( 'dokan_after_listing_product' );
            ?>

            <?php

        /**
         *  dokan_dashboard_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        ?>
        <script type="text/javascript">
            ( function ( $ ) {

                $( document ).ready( function () {
                    $('.dokan-dashboard-header').on('click', '.dokan-btn', function(){
                      add_resource_name = prompt( wc_bookings_writepanel_js_params.i18n_new_resource_name );

                      var data = {
                         action:            'add_new_resource',
                         add_resource_name: add_resource_name,
                     };

                     $.post( dokan.ajaxurl, data, function( response ) {
                        console.log(response);
                        if ( response.error ) {
                            alert( response.error );
                        } else if( response.success ) {
                            $('tbody').append(response.data);
                        }
                    });
                 });
                    $('.dokan-product-listing').on('click', '.btn-remove', function(){

                        res_id = $(this).data( 'id' );
                        row = $(this).closest('tr');
                        var data = {
                            action : 'delete_resource',
                            resource_id : res_id
                        }
                        $.post( dokan.ajaxurl, data, function( response ){
                            if( response.success ){
                                row.hide();
                            }
                        });



                    });

                } );

            } )( jQuery );

        </script>