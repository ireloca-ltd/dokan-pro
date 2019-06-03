<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Add generic mappings.
 *
 * @since 3.1.0
 * @param array $mappings
 * @return array
 */
function wc_importer_generic_mappings( $mappings ) {
    $generic_mappings = array(
        __( 'Title', 'dokan' )         => 'name',
        __( 'Product Title', 'dokan' ) => 'name',
        __( 'Price', 'dokan' )         => 'regular_price',
        __( 'Parent SKU', 'dokan' )    => 'parent_id',
        __( 'Quantity', 'dokan' )      => 'stock_quantity',
    );

    return array_merge( $mappings, $generic_mappings );
}

add_filter( 'woocommerce_csv_product_import_mapping_default_columns', 'wc_importer_generic_mappings' );
