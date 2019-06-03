<?php
/*
Plugin Name: Store Reviews
Plugin URI: http://wedevs.com/
Description: A plugin that allows customers to rate the sellers.
Version: 1.1.0
Author: weDevs
Author URI: http://wedevs.com/
Thumbnail Name: vendor-review.png
License: GPL2
*/

/**
 * Copyright (c) YEAR weDevs (email: info@wedevs.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

define( 'DOKAN_SELLER_RATINGS_PLUGIN_VERSION', '1.1.0' );
define( 'DOKAN_SELLER_RATINGS_DIR', dirname( __FILE__ ) );
define( 'DOKAN_SELLER_RATINGS_PLUGIN_ASSEST', plugins_url( 'assets', __FILE__ ) );

/**
 * Dokan_Store_Reviews class
 *
 * @class Dokan_Store_Reviews The class that holds the entire Dokan_Store_Reviews plugin
 */
class Dokan_Store_Reviews {

    /**
     * Constructor for the Dokan_Store_Reviews class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {
        //hooks
        add_action( 'init', array( $this, 'register_dokan_store_review_type' ) );
        add_action( 'dokan_seller_rating_value', array( $this, 'replace_rating_value' ), 10, 2 );
        add_filter( 'dokan_seller_tab_reviews_list', array( $this, 'replace_ratings_list' ),10 ,2 );

        $this->includes();

        // Loads frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

    }

    /**
     * Initializes the Dokan_Store_Reviews() class
     *
     * Checks for an existing Dokan_Store_Reviews() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Dokan_Store_Reviews();
        }

        return $instance;
    }

    /**
     * Enqueue admin scripts
     *
     * Allows plugin assets to be loaded.
     *
     * @uses wp_enqueue_script()
     * @uses wp_localize_script()
     * @uses wp_enqueue_style
     */
    public function enqueue_scripts() {

        //only load the scripts on store page for optimization
        if ( dokan_is_store_page() ) {
            wp_enqueue_style( 'dokan-magnific-popup' );
            wp_enqueue_style( 'dsr-styles', plugins_url( 'assets/css/style.css', __FILE__ ), false, date( 'Ymd' ) );
            wp_enqueue_style( 'dokan-rateyo-styles', plugins_url( 'assets/css/rateyo.min.css', __FILE__ ) );

            wp_enqueue_script( 'dsr-scripts', plugins_url( 'assets/js/script.js', __FILE__ ), array( 'jquery', 'dokan-popup' ), false, true );
            wp_enqueue_script( 'dokan-rateyo', plugins_url( 'assets/js/rateyo.min.js', __FILE__ ) );
        }
    }

    /**
     * Include files
     *
     * @return void
     */
    function includes() {
        require_once DOKAN_SELLER_RATINGS_DIR.'/classes/DSR_View.php';
        require_once DOKAN_SELLER_RATINGS_DIR . '/functions.php';
    }

     /**
     * Register Custom Post type for Store Reviews
     *
     * @since 1.0
     *
     * @return void
     */
    function register_dokan_store_review_type() {

        $labels = array(
            'name'               => __( 'Store Reviews', 'Post Type General Name', 'dokan' ),
            'singular_name'      => __( 'Store Review', 'Post Type Singular Name', 'dokan' ),
            'menu_name'          => __( 'Store Reviews', 'dokan' ),
            'name_admin_bar'     => __( 'Store Reviews', 'dokan' ),
            'parent_item_colon'  => __( 'Parent Item', 'dokan' ),
            'all_items'          => __( 'All Reviews', 'dokan' ),
            'add_new_item'       => __( 'Add New review', 'dokan' ),
            'add_new'            => __( 'Add New', 'dokan' ),
            'new_item'           => __( 'New review', 'dokan' ),
            'edit_item'          => __( 'Edit review', 'dokan' ),
            'update_item'        => __( 'Update review', 'dokan' ),
            'view_item'          => __( 'View review', 'dokan' ),
            'search_items'       => __( 'Search review', 'dokan' ),
            'not_found'          => __( 'Not found', 'dokan' ),
            'not_found_in_trash' => __( 'Not found in Trash', 'dokan' ),
        );
        $args   = array(
            'label'             => __( 'Store Reviews', 'dokan' ),
            'description'       => __( 'Store Reviews by customer', 'dokan' ),
            'labels'            => $labels,
            'supports'          => array( 'title', 'author', 'editor' ),
            'hierarchical'      => false,
            'public'            => false,
            'publicly_queryable' => true,
            'show_in_menu'      => false,
            'show_in_rest'      => true,
            'menu_position'     => 5,
            'show_in_admin_bar' => false,
            'rewrite'           => array( 'slug' => '' ),
            'can_export'        => true,
            'has_archive'       => true,
        );
        register_post_type( 'dokan_store_reviews', $args );
    }

    /**
     * Filter Dokan Core rating calculation value
     *
     * @since 1.0
     *
     * @param array $rating
     * @param int $store_id
     *
     * @return array calculated Rating
     */
    function replace_rating_value( $rating, $store_id ) {

        $args = array(
            'post_type'      => 'dokan_store_reviews',
            'meta_key'       => 'store_id',
            'meta_value'     => $store_id,
            'post_status'    => 'publish',
        );

        $query = new WP_Query( $args );

        $review_count = $query->post_count;

        if ( $review_count ) {

            $rating = 0;
            foreach ( $query->posts as $review ) {
                $rating += intval( get_post_meta( $review->ID, 'rating', true ) );
            }

            $rating = number_format( $rating / $review_count, 2 );
        } else {
            $rating = __( 'No Ratings found yet', 'dokan' );
        }

        return array(
            'rating' => $rating,
            'count'  => $review_count
        );
    }

    /**
     * Filter the Review list shown on review tab by default core
     *
     * @since 1.0
     *
     * @param string $review_list
     * @param int $store_id
     *
     * @return string Review List HTML
     */
    function replace_ratings_list( $review_list, $store_id ) {

        $args = array(
            'post_type'      => 'dokan_store_reviews',
            'meta_key'       => 'store_id',
            'meta_value'     => $store_id,
            'post_status'    => 'publish',
            'author__not_in' => array( get_current_user_id(), $store_id )
        );

        $query = new WP_Query( $args );
        $no_review_msg = apply_filters( 'dsr_no_review_found_msg', 'No Reviews found' );
        ob_start();

        DSR_View::init()->render_review_list( $query->posts, $no_review_msg );

        wp_reset_postdata();

        return ob_get_clean();;
    }

} // Dokan_Store_Reviews

$dsr = Dokan_Store_Reviews::init();
