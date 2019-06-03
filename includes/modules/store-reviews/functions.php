<?php

/**
 * Create or update a store review
 *
 * @since 2.9.5
 *
 * @param int   $store_id
 * @param array $data
 *
 * @return int|\WP_Error
 */
function dsr_save_store_review( $store_id, $data ) {
    $postarr = array(
        'post_title'     => $data['title'],
        'post_content'   => $data['content'],
        'author'         => $data['reviewer_id'],
        'post_type'      => 'dokan_store_reviews',
        'post_status'    => 'publish'
    );

    if ( ! empty( $data[ 'id' ] ) ) {
        $post                    = get_post( $data['id'] );
        $current_user_id         = get_current_user_id();
        $user_can_manage_reviews = current_user_can( 'dokan_manage_reviews' );

        if ( $user_can_manage_reviews || $current_user_id === absint( $post->post_author ) ) {
            $postarr[ 'ID' ] = $post->ID;
            $post_id         = wp_update_post( $postarr );
        } else {
            $post_id = 0;
        }
    } else {
        $post_id = wp_insert_post( $postarr );
    }

    if ( ! is_wp_error( $post_id ) ) {
        update_post_meta( $post_id, 'store_id', $store_id );

        $rating = isset( $data['rating'] ) ? absint( $data['rating'] ) : 0;
        update_post_meta( $post_id, 'rating', $rating );
    }

    return $post_id;
}
