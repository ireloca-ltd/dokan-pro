<?php

/**
 * Include module templates
 *
 * @since 2.9.8
 *
 * @param string $name
 * @param array  $args
 *
 * @return void
 */
function dokan_spmv_get_template( $name, $args = [] ) {
    dokan_get_template( "$name.php", $args, DOKAN_SPMV_VIEWS, trailingslashit( DOKAN_SPMV_VIEWS ) );
}

/**
 * Get other reseller vendors
 *
 * @since 2.9.8
 *
 * @param int $product_id
 *
 * @return array
 */
function dokan_spmv_get_product_clones( $product ) {
    global $wpdb;

    $clones  = [];
    $product = wc_get_product( $product );

    if ( $product->get_id() ) {
        $product_id      = $product->get_id();
        $has_multivendor = get_post_meta( $product_id, '_has_multi_vendor', true );

        if ( ! empty( $has_multivendor ) ) {
            $clones = $wpdb->get_col( $wpdb->prepare(
                  "select product_id from {$wpdb->prefix}dokan_product_map"
                . " where map_id = %d and product_id != %d",
                $has_multivendor,
                $product_id
            ) );
        }
    }

    return $clones;
}
