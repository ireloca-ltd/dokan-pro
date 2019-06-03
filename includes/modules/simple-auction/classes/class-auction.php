<?php

/**
 * Tempalte shortcode class file
 *
 * @load all shortcode for template  rendering
 */
class Dokan_Template_Auction {

    public static $errors;
    public static $product_cat;
    public static $post_content;
    public static $validated;
    public static $validate;

    /**
     * __construct function
     *
     * @since 1.0.0
     */
    function __construct() {
        add_action( 'template_redirect', array( $this, 'auction_handle_all_submit' ), 11 );
        add_action( 'template_redirect', array( $this, 'handle_auction_product_delete' ) );
        add_action( 'dokan_auction_after_general_options', array( $this, 'load_attribute_options' ), 12 );
        add_action( 'dokan_auction_after_general_options', array( $this, 'load_shipping_options' ), 13 );
    }

    /**
     * Initializes the Dokan_Template_Auction() class
     *
     * Checks for an existing Dokan_Template_Auction() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Template_Auction();
        }

        return $instance;
    }

    /**
    * Load attribute templates
    *
    * @since 1.5.2
    *
    * @return void
    **/
    public function load_attribute_options( $post_id ) {
        $product_attributes   = get_post_meta( $post_id, '_product_attributes', true );
        $attribute_taxonomies = wc_get_attribute_taxonomies();

        dokan_get_template_part( 'auction/html-auction-attribute', '', array(
            'is_auction'           => true,
            'post_id'              => $post_id,
            'product_attributes'   => $product_attributes,
            'attribute_taxonomies' => $attribute_taxonomies,
        ) );
    }

    /**
    * Load Shipping templates
    *
    * @since 1.5.2
    *
    * @return void
    **/
    public function load_shipping_options( $post_id ) {
        dokan_get_template_part( 'auction/auction-shipping', '', array(
            'is_auction'           => true,
            'post_id'              => $post_id,
        ) );
    }

    /**
     * Saving handle for auction data
     *
     * @since  1.0.0
     *
     * @return void
     */
    function auction_handle_all_submit() {
        if ( !is_user_logged_in() ) {
            return;
        }

        if ( !dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        $errors = array();
        self::$product_cat = -1;
        self::$post_content = '';

        if ( ! $_POST ) {
            return;
        }

        global $woocommerce_auctions;

        if ( isset( $_POST['add_auction_product'] ) && wp_verify_nonce( $_POST['dokan_add_new_auction_product_nonce'], 'dokan_add_new_auction_product' ) ) {
            if ( ! current_user_can( 'dokan_add_auction_product' ) ) {
                return;
            }

            $post_title     = isset( $_POST['post_title'] ) ? trim( $_POST['post_title'] ) : '';
            $post_content   = isset( $_POST['post_content'] ) ? trim( $_POST['post_content'] ) : '';
            $post_excerpt   = isset( $_POST['post_excerpt'] ) ? trim( $_POST['post_excerpt'] ) : '';
            $product_cat    = isset( $_POST['product_cat'] ) ? absint( $_POST['product_cat'] ) : '';
            $featured_image = isset( $_POST['feat_image_id'] ) ? absint( $_POST['feat_image_id'] ) : '';

            if ( empty( $post_title ) ) {
                $errors[] = __( 'Please enter product title', 'dokan' );
            }

            if ( $product_cat < 0 ) {
                $errors[] = __( 'Please select a category', 'dokan' );
            }

            self::$errors = apply_filters( 'dokan_can_add_product', $errors );

            if ( !self::$errors ) {
                $product_status = dokan_get_new_post_status();
                $post_data = apply_filters( 'dokan_insert_auction_product_post_data', array(
                        'post_type'    => 'product',
                        'post_status'  => $product_status,
                        'post_title'   => $post_title,
                        'post_content' => $post_content,
                        'post_excerpt' => $post_excerpt,
                        'post_author'  => dokan_get_current_user_id()
                    ) );

                $product_id = wp_insert_post( $post_data );

                if ( $product_id ) {

                    // Set featured images
                    if ( $featured_image ) {
                        set_post_thumbnail( $product_id, $featured_image );
                    }

                    // Set Gallery Images
                    if ( !empty( $_POST['product_image_gallery'] ) ) {
                        $attachment_ids = array_filter( explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) );
                        update_post_meta( $product_id, '_product_image_gallery', implode( ',', $attachment_ids ) );
                    }

                     /** set product category * */
                    if( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
                        wp_set_object_terms( $product_id, (int) $_POST['product_cat'], 'product_cat' );
                    } else {
                        if( isset( $_POST['product_cat'] ) && !empty( $_POST['product_cat'] ) ) {
                            $cat_ids = array_map( 'intval', (array)$_POST['product_cat'] );
                            wp_set_object_terms( $product_id, $cat_ids, 'product_cat' );
                        }
                    }

                    // Set Product tags
                    if( isset( $_POST['product_tag'] ) ) {
                        $tags_ids = array_map( 'intval', (array)$_POST['product_tag'] );
                    } else {
                        $tags_ids = array();
                    }
                    wp_set_object_terms( $product_id, $tags_ids, 'product_tag' );

                    // Set product type
                    wp_set_object_terms( $product_id, 'auction', 'product_type' );

                    $woocommerce_auctions->product_save_data( $product_id, get_post( $product_id ) );

                    do_action( 'dokan_new_auction_product_added', $product_id, $post_data );

                    // Dokan_Email::init()->new_product_added( $product_id, $product_status );

                    if ( current_user_can( 'dokan_edit_auction_product' ) ) {
                        $redirect_url = add_query_arg( array('product_id' => $product_id, 'action' => 'edit', 'message' => 'success' ), dokan_get_navigation_url('auction') );
                    } else {
                        $redirect_url = dokan_get_navigation_url('auction');
                    }

                    wp_redirect( $redirect_url );
                    exit;
                }
            }
        }

        // Edit handle in auction product
        if ( isset( $_GET['product_id'] ) ) {
            $post_id = intval( $_GET['product_id'] );
        } else {
            global $post, $product;
            if ( !empty( $post ) ) {
                $post_id = $post->ID;
            }
        }

        if ( isset( $_POST['update_auction_product'] ) && wp_verify_nonce( $_POST['dokan_edit_auction_product_nonce'], 'dokan_edit_auction_product' ) ) {
            if ( ! current_user_can( 'dokan_edit_auction_product' ) ) {
                return;
            }

            $product_info = array(
                'ID'             => $post_id,
                'post_title'     => sanitize_text_field( $_POST['post_title'] ),
                'post_content'   => $_POST['post_content'],
                'post_excerpt'   => $_POST['post_excerpt'],
                'post_status'    => isset( $_POST['post_status'] ) ? $_POST['post_status'] : 'pending',
                'comment_status' => isset( $_POST['_enable_reviews'] ) ? 'open' : 'closed'
            );

            wp_update_post( $product_info );

            /** set product category * */
            if( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ) {
                wp_set_object_terms( $post_id, (int) $_POST['product_cat'], 'product_cat' );
            } else {
                if( isset( $_POST['product_cat'] ) && !empty( $_POST['product_cat'] ) ) {
                    $cat_ids = array_map( 'intval', (array)$_POST['product_cat'] );
                    wp_set_object_terms( $post_id, $cat_ids, 'product_cat' );
                }
            }

            wp_set_object_terms( $post_id, 'auction', 'product_type' );

            /** Set Product tags */
            if( isset( $_POST['product_tag'] ) ) {
                $tags_ids = array_map( 'intval', (array)$_POST['product_tag'] );
            } else {
                $tags_ids = array();
            }
            wp_set_object_terms( $post_id, $tags_ids, 'product_tag' );

            // Handle visibility ( with WC 3.0.0+ compatibility )
            $terms = array();
            $_visibility = isset( $_POST['_visibility'] ) ? $_POST['_visibility'] : '';
            switch ( $_visibility ) {
                case 'hidden' :
                    $terms[] = 'exclude-from-search';
                    $terms[] = 'exclude-from-catalog';
                    break;
                case 'catalog' :
                    $terms[] = 'exclude-from-search';
                    break;
                case 'search' :
                    $terms[] = 'exclude-from-catalog';
                    break;
            }

            wp_set_post_terms( $post_id, $terms, 'product_visibility', false );
            update_post_meta( $post_id, '_visibility', $_visibility );

            /** set images **/
            $featured_image = absint( $_POST['feat_image_id'] );
            if ( $featured_image ) {
                set_post_thumbnail( $post_id, $featured_image );
            } else {
                delete_post_thumbnail( $post_id );
            }

            // Gallery Images
            $attachment_ids = array_filter( explode( ',', wc_clean( $_POST['product_image_gallery'] ) ) );
            update_post_meta( $post_id, '_product_image_gallery', implode( ',', $attachment_ids ) );

            $woocommerce_auctions->product_save_data( $post_id, get_post( $post_id ) );

            // Save Attributes
            $attributes = array();

            if ( isset( $_POST['attribute_names'] ) && isset( $_POST['attribute_values'] ) ) {

                $attribute_names  = $_POST['attribute_names'];
                $attribute_values = $_POST['attribute_values'];

                if ( isset( $_POST['attribute_visibility'] ) ) {
                    $attribute_visibility = $_POST['attribute_visibility'];
                }

                if ( isset( $_POST['attribute_variation'] ) ) {
                    $attribute_variation = $_POST['attribute_variation'];
                }

                $attribute_is_taxonomy   = $_POST['attribute_is_taxonomy'];
                $attribute_position      = $_POST['attribute_position'];
                $attribute_names_max_key = max( array_keys( $attribute_names ) );

                for ( $i = 0; $i <= $attribute_names_max_key; $i++ ) {
                    if ( empty( $attribute_names[ $i ] ) ) {
                        continue;
                    }

                    $is_visible   = isset( $attribute_visibility[ $i ] ) ? 1 : 0;
                    $is_variation = isset( $attribute_variation[ $i ] ) ? 1 : 0;
                    $is_taxonomy  = $attribute_is_taxonomy[ $i ] ? 1 : 0;

                    if ( $is_taxonomy ) {

                        $values_are_slugs = false;

                        if ( isset( $attribute_values[ $i ] ) ) {

                            // Select based attributes - Format values (posted values are slugs)
                            if ( is_array( $attribute_values[ $i ] ) ) {
                                $values           = array_map( 'sanitize_title', $attribute_values[ $i ] );
                                $values_are_slugs = true;

                            // Text based attributes - Posted values are term names - don't change to slugs
                            } else {
                                $values = array_map( 'stripslashes', array_map( 'strip_tags', explode( WC_DELIMITER, $attribute_values[ $i ] ) ) );
                            }

                            // Remove empty items in the array
                            $values = array_filter( $values, 'strlen' );

                        } else {
                            $values = array();
                        }

                        // Update post terms
                        if ( taxonomy_exists( $attribute_names[ $i ] ) ) {

                            foreach ( $values as $key => $value ) {
                                $term = get_term_by( $values_are_slugs ? 'slug' : 'name', trim( $value ), $attribute_names[ $i ] );

                                if ( $term ) {
                                    $values[ $key ] = intval( $term->term_id );
                                } else {
                                    $term = wp_insert_term( trim( $value ), $attribute_names[ $i ] );
                                    if ( isset( $term->term_id ) ) {
                                        $values[ $key ] = intval( $term->term_id );
                                    }
                                }
                            }

                            wp_set_object_terms( $post_id, $values, $attribute_names[ $i ] );
                        }

                        if ( ! empty( $values ) ) {
                            // Add attribute to array, but don't set values
                            $attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
                                'name'         => wc_clean( $attribute_names[ $i ] ),
                                'value'        => '',
                                'position'     => $attribute_position[ $i ],
                                'is_visible'   => $is_visible,
                                'is_variation' => $is_variation,
                                'is_taxonomy'  => $is_taxonomy,
                            );
                        }
                    } elseif ( isset( $attribute_values[ $i ] ) ) {

                        // Text based, possibly separated by pipes (WC_DELIMITER). Preserve line breaks in non-variation attributes.
                        $values = implode( ' ' . WC_DELIMITER . ' ', array_map( 'wc_clean', array_map( 'stripslashes', $attribute_values[ $i ] ) ) );

                        // Custom attribute - Add attribute to array and set the values
                        $attributes[ sanitize_title( $attribute_names[ $i ] ) ] = array(
                            'name'         => wc_clean( $attribute_names[ $i ] ),
                            'value'        => $values,
                            'position'     => $attribute_position[ $i ],
                            'is_visible'   => $is_visible,
                            'is_variation' => $is_variation,
                            'is_taxonomy'  => $is_taxonomy,
                        );
                    }
                }
            }
            uasort( $attributes, 'wc_product_attribute_uasort_comparison' );

            /**
             * Unset removed attributes by looping over previous values and
             * unsetting the terms.
             */
            $old_attributes = array_filter( (array) maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) ) );

            if ( ! empty( $old_attributes ) ) {
                foreach ( $old_attributes as $key => $value ) {
                    if ( empty( $attributes[ $key ] ) && ! empty( $value['is_taxonomy'] ) && taxonomy_exists( $key ) ) {
                        wp_set_object_terms( $post_id, array(), $key );
                    }
                }
            }

            update_post_meta( $post_id, '_product_attributes', $attributes );

           // Dimensions
            if ( isset( $_POST['_weight'] ) ) {
                update_post_meta( $post_id, '_weight', ( '' === $_POST['_weight'] ) ? '' : wc_format_decimal( $_POST['_weight'] )  );
            }

            if ( isset( $_POST['_length'] ) ) {
                update_post_meta( $post_id, '_length', ( '' === $_POST['_length'] ) ? '' : wc_format_decimal( $_POST['_length'] )  );
            }

            if ( isset( $_POST['_width'] ) ) {
                update_post_meta( $post_id, '_width', ( '' === $_POST['_width'] ) ? '' : wc_format_decimal( $_POST['_width'] )  );
            }

            if ( isset( $_POST['_height'] ) ) {
                update_post_meta( $post_id, '_height', ( '' === $_POST['_height'] ) ? '' : wc_format_decimal( $_POST['_height'] )  );
            }

            //Save shipping meta data
            update_post_meta( $post_id, '_disable_shipping', stripslashes( isset( $_POST['_disable_shipping'] ) ? $_POST['_disable_shipping'] : 'no' ) );

            if ( isset( $_POST['_overwrite_shipping'] ) && $_POST['_overwrite_shipping'] == 'yes' ) {
                update_post_meta( $post_id, '_overwrite_shipping', stripslashes( $_POST['_overwrite_shipping'] ) );
            } else {
                update_post_meta( $post_id, '_overwrite_shipping', 'no' );
            }

            update_post_meta( $post_id, '_additional_price', stripslashes( isset( $_POST['_additional_price'] ) ? $_POST['_additional_price'] : ''  ) );
            update_post_meta( $post_id, '_additional_qty', stripslashes( isset( $_POST['_additional_qty'] ) ? $_POST['_additional_qty'] : ''  ) );
            update_post_meta( $post_id, '_dps_processing_time', stripslashes( isset( $_POST['_dps_processing_time'] ) ? $_POST['_dps_processing_time'] : ''  ) );

            // Save shipping class
            $product_shipping_class = ( isset( $_POST['product_shipping_class'] ) && $_POST['product_shipping_class'] > 0 && 'external' !== $product_type ) ? absint( $_POST['product_shipping_class'] ) : '';
            wp_set_object_terms( $post_id, $product_shipping_class, 'product_shipping_class' );

            do_action( 'dokan_update_auction_product', $post_id );

            $edit_url = add_query_arg( array('product_id' => $post_id, 'action' => 'edit' ), dokan_get_navigation_url('auction') );
            wp_redirect( add_query_arg( array( 'message' => 'success' ), $edit_url ) );
            exit;
        }
    }

    /**
     * Handle auction product delete
     *
     * @since  1.0.0
     *
     * @return void
     */
    function handle_auction_product_delete() {
        if ( !is_user_logged_in() ) {
            return;
        }

        if ( !dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        if ( ! current_user_can( 'dokan_delete_auction_product' ) ) {
            return;
        }

        if ( isset( $_GET['action'] ) && $_GET['action'] == 'dokan-delete-auction-product' ) {
            $product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : 0;

            if ( !$product_id ) {
                wp_redirect( add_query_arg( array( 'message' => 'error' ), dokan_get_navigation_url( 'auction' ) ) );
                return;
            }

            if ( !wp_verify_nonce( $_GET['_wpnonce'], 'dokan-delete-auction-product' ) ) {
                wp_redirect( add_query_arg( array( 'message' => 'error' ), dokan_get_navigation_url( 'auction' ) ) );
                return;
            }

            if ( !dokan_is_product_author( $product_id ) ) {
                wp_redirect( add_query_arg( array( 'message' => 'error' ), dokan_get_navigation_url( 'auction' ) ) );
                return;
            }

            wp_delete_post( $product_id );
            wp_redirect( add_query_arg( array( 'message' => 'product_deleted' ), dokan_get_navigation_url( 'auction' ) ) );
            exit;
        }

    }

}

Dokan_Template_Auction::init();
