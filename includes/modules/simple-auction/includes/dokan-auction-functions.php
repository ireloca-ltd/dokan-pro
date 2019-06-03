<?php

/**
 *  Common function for dokan auction
 *  pluigin.
 *
 *  Loaded all all function in whole plugins
 *  @author Sabbir <sabbir.081070@gmail.com>
 */

/**
 * Check if the seller is enabled
 *
 * @since 1.0.0
 *
 * @param int $user_id
 *
 * @return boolean
 */
function dokan_is_seller_auction_disabled( $user_id ) {
    $auction = get_user_meta( $user_id, 'dokan_disable_auction', true );

    if ( $auction == 'yes' ) {
        return true;
    }

    return false;
}

/**
 * Auction post status counting
 *
 * @since  1.0.0
 *
 * @param  string $post_type
 * @param  integer $user_id
 *
 * @return array
 */
function dokan_count_auction_posts( $post_type, $user_id ) {
    global $wpdb;

    $cache_key = 'dokan-count-auction' . $post_type . '-' . $user_id;
    $counts = wp_cache_get( $cache_key, 'dokan' );
    $term = get_term_by( 'slug', 'auction', 'product_type' );
    if ( false === $counts ) {
        // $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} WHERE post_type = %s AND post_author = %d GROUP BY post_status";
        $query = "SELECT post_status, COUNT( * ) AS num_posts FROM {$wpdb->posts} INNER JOIN {$wpdb->prefix}term_relationships ON ({$wpdb->prefix}posts.ID = {$wpdb->prefix}term_relationships.object_id) AND ( {$wpdb->prefix}term_relationships.term_taxonomy_id IN ( %s ) ) AND {$wpdb->prefix}posts.post_author = %s AND {$wpdb->prefix}posts.post_type = %s GROUP BY post_status";
        $results = $wpdb->get_results( $wpdb->prepare( $query, $term->term_id, $user_id, $post_type ), ARRAY_A );
        $counts = array_fill_keys( get_post_stati(), 0 );

        $total = 0;
        foreach ( $results as $row ) {
            $counts[ $row['post_status'] ] = (int) $row['num_posts'];
            $total += (int) $row['num_posts'];
        }

        $counts['total'] = $total;
        $counts = (object) $counts;
        wp_cache_set( $cache_key, $counts, 'dokan' );
    }

    return $counts;
}

/**
 * Auction listing status filter
 *
 * @since  1.0.0
 *
 * @return void
 */
function dokan_auction_product_listing_status_filter() {
    $permalink = dokan_get_navigation_url( 'auction' );
    $status_class = isset( $_GET['post_status'] ) ? $_GET['post_status'] : 'all';
    $post_counts = dokan_count_auction_posts( 'product', dokan_get_current_user_id() );
    ?>
    <ul class="dokan-listing-filter dokan-left subsubsub">
        <li<?php echo $status_class == 'all' ? ' class="active"' : ''; ?>>
            <a href="<?php echo $permalink; ?>"><?php printf( __( 'All (%d)', 'dokan' ), $post_counts->total-$post_counts->trash ); ?></a>
        </li>
        <li<?php echo $status_class == 'publish' ? ' class="active"' : ''; ?>>
            <a href="<?php echo add_query_arg( array( 'post_status' => 'publish' ), $permalink ); ?>"><?php printf( __( 'Online (%d)', 'dokan' ), $post_counts->publish ); ?></a>
        </li>
        <li<?php echo $status_class == 'pending' ? ' class="active"' : ''; ?>>
            <a href="<?php echo add_query_arg( array( 'post_status' => 'pending' ), $permalink ); ?>"><?php printf( __( 'Pending Review (%d)', 'dokan' ), $post_counts->pending ); ?></a>
        </li>
        <li<?php echo $status_class == 'draft' ? ' class="active"' : ''; ?>>
            <a href="<?php echo add_query_arg( array( 'post_status' => 'draft' ), $permalink ); ?>"><?php printf( __( 'Draft (%d)', 'dokan' ), $post_counts->draft ); ?></a>
        </li>
    </ul> <!-- .post-statuses-filter -->
    <?php
}