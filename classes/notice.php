<?php
/**
 * Dokan Notice Class
 *
 * @since  2.1
 *
 * @author weDevs  <info@wedevs.com>
 */
class Dokan_Pro_Notice {

    private $perpage = 10;
    private $total_query_result;
    public $notice_id;

    /**
     * Load automatically when class initiate
     *
     * @since 2.4
     *
     * @uses action hook
     * @uses filter hook
     */
    function __construct() {
        add_action( 'dokan_load_custom_template', array( $this, 'load_announcement_template' ), 10 );
        add_action( 'dokan_announcement_content_area_header', array( $this, 'load_header_template' ) );
        add_action( 'dokan_announcement_content', array( $this, 'load_announcement_content' ), 10 );
        add_action( 'dokan_single_announcement_content', array( $this, 'load_single_announcement_content' ), 10 );
    }

    /**
     * Initializes the Dokan_Pro_Notice() class
     *
     * Checks for an existing Dokan_Pro_Notice() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Notice();
        }

        return $instance;
    }

    /**
     * Render announcement template
     *
     * @since  2.2
     *
     * @param  array $query_vars
     *
     * @return void
     */
    public function load_announcement_template( $query_vars ) {
        if ( isset( $query_vars['announcement'] ) ) {
            dokan_get_template_part( 'announcement/announcement', '', array( 'pro' => true, 'announcement' => $this ) );
            return;
        }
        if ( isset( $query_vars['single-announcement'] ) ) {
            dokan_get_template_part( 'announcement/single-announcement', '', array( 'pro' => true ) );
            return;
        }
    }

    /**
     * Render Announcement listing template header
     *
     * @since 2.2
     *
     * @return void
     */
    public function load_header_template() {
        dokan_get_template_part( 'announcement/header', '', array( 'pro' => true ) );
    }

    /**
     * Load announcement Content
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_announcement_content() {
        $this->show_announcement_template();
    }

    /**
     * Load Single announcement content
     *
     * @since 2.4
     *
     * @return void
     */
    public function load_single_announcement_content() {
        $this->notice_id =  get_query_var( 'single-announcement' );

        if ( is_numeric( $this->notice_id ) ) {
            $notice = $this->get_single_announcement( $this->notice_id );
        }

        if ( $notice ) {
            $notice_data = reset( $notice );
            if( $notice_data->status == 'unread' ) {
                $this->update_notice_status( $this->notice_id, 'read' );
            }
            dokan_get_template_part( 'announcement/single-notice', '', array( 'pro' => true, 'notice_data' => $notice_data ) );

        } else {
            dokan_get_template_part( 'announcement/no-announcement', '', array( 'pro' => true ) );
        }
    }

    /**
     * Get Announcement via teble and announcement post type
     *
     * @since  2.1
     *
     * @param  integer $per_page
     *
     * @return object
     */
    function get_announcement_by_users( $per_page = NULL ) {

        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

        $args = array(
            'post_type'      => 'dokan_announcement',
            'post_status'    => 'publish',
            'posts_per_page' => ($per_page) ? $per_page : $this->perpage,
            'orderby'        => 'post_date',
            'order'          => 'DESC',
            'meta_key'       => '_announcement_type',
            'meta_value'     => 'selected_seller',
            'paged'          => $pagenum
        );

        $this->add_query_filter();

        $query = new WP_Query( $args );

        $this->remove_query_filter();

        return $query;
    }

    /**
     * Show announcement template
     *
     * @since  2.1
     *
     * @return void
     */
    function show_announcement_template() {
        $query = $this->get_announcement_by_users();

        $args = array(
            'post_type'      => 'dokan_announcement',
            'post_status'    => 'publish',
            'orderby'        => 'post_date',
            'order'          => 'DESC',
            'meta_key'       => '_announcement_type',
            'meta_value'     => 'all_seller',
        );

        $this->add_query_filter();

        $all_seller_posts = new WP_Query( $args );

        $this->remove_query_filter();

        $notices = array_merge( $all_seller_posts->posts, $query->posts );

        dokan_get_template_part( 'announcement/listing-announcement', '', array( 'pro' => true, 'notices' => $notices ) );

        wp_reset_postdata();
        $this->get_pagination( $query );
    }

    /**
     *  Add Query filter for select, join and where
     *  with dokan_announcement post type
     *
     *  @since  2.1
     */
    function add_query_filter() {
        add_filter( 'posts_fields', array( $this, 'select_dokan_announcement_table' ), 10, 2 );
        add_filter( 'posts_join', array( $this, 'join_dokan_announcement_table' ) );
        add_filter( 'posts_where', array( $this, 'where_dokan_announcement_table' ), 10, 2 );
    }

    /**
     * Remove query filters
     *
     * @since  2.1
     *
     * @return void
     */
    function remove_query_filter() {
        remove_filter( 'posts_fields', array( $this, 'select_dokan_announcement_table' ), 10, 2 );
        remove_filter( 'posts_join', array( $this, 'join_dokan_announcement_table' ) );
        remove_filter( 'posts_where', array( $this, 'where_dokan_announcement_table' ), 10, 2 );
    }

    /**
     * Render pagination for seller announcement
     *
     * @since  2.1
     *
     * @param  object $query
     *
     * @return void
     */
    function get_pagination( $query ) {
        $pagenum  = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
        $base_url = dokan_get_navigation_url('notice');

        if ( $query->max_num_pages > 1 ) {
            echo '<div class="pagination-wrap">';
            $page_links = paginate_links( array(
                'current'   => $pagenum,
                'total'     => $query->max_num_pages,
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
    }

    /**
     * Get single announcement
     *
     * @since  2.1
     *
     * @param  integer $notice_id
     *
     * @return object
     */
    function get_single_announcement( $notice_id ) {
        $args = array(
            'p'         => $notice_id,
            'post_type' => 'dokan_announcement'
        );

        $this->add_query_filter();

        $query = new WP_Query( $args );
        $notice = (array)$query->posts;

        $this->remove_query_filter();
        return $notice;
    }

    /**
     * Update notice status in dokan_announcement table
     *
     * @since  2.1
     *
     * @param  integer $notice_id
     * @param  string $status
     *
     * @return void
     */
    function update_notice_status( $notice_id, $status ) {
        global $wpdb;
        $table_name = $wpdb->prefix.'dokan_announcement';

        $wpdb->update(
            $table_name,
            array(
                'status' => $status,
            ),
            array( 'post_id' => $notice_id, 'user_id' => dokan_get_current_user_id() )
        );
    }

    /**
     * Select query filter
     *
     * @since  2.1
     *
     * @param  string $fields
     * @param  object $query
     *
     * @return string
     */
    function select_dokan_announcement_table( $fields, $query ) {
        global $wpdb;

        $table_name = $wpdb->prefix.'dokan_announcement';
        $fields .= " ,da.id, da.status";

        return $fields;
    }

    /**
     * Join query filter
     *
     * @since  2.1
     *
     * @param  string $join
     *
     * @return string
     */
    function join_dokan_announcement_table( $join ) {
        global $wpdb;

        $table_name = $wpdb->prefix .'dokan_announcement';
        $join .= " LEFT JOIN $table_name AS da ON $wpdb->posts.ID = da.post_id";

        return $join;
    }

    /**
     * Where query filter
     *
     * @since  2.1
     *
     * @param  integer $where
     * @param  object $query
     *
     * @return string
     */
    function where_dokan_announcement_table( $where, $query ) {
        global $wpdb;

        $table_name = $wpdb->prefix .'dokan_announcement';
        $current_user_id = dokan_get_current_user_id();

        $where .= " AND da.user_id = $current_user_id AND ( da.status = 'read' OR da.status = 'unread' )";

        return $where;
    }
}