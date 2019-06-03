<?php
/**
 * Dokan Coupons Class
 *
 * @author weDevs
 */
class Dokan_Pro_Coupons {

    private $perpage = 10;
    private $total_query_result;
    public static $validated;
    public static $validate;
    public $is_edit_page;

    /**
     * Load automatically when class inistantiate
     *
     * @since 2.4
     *
     * @uses actions|filter hooks
     */
    public function __construct() {

        $this->is_edit_page =  isset( $_GET['view'] ) && $_GET['view'] == 'add_coupons';

        add_filter( 'dokan_get_dashboard_nav', array( $this, 'add_coupon_menu' ) );
        add_action( 'dokan_load_custom_template', array( $this, 'load_coupon_template' ) );
        add_action( 'template_redirect', array( $this, 'handle_coupons' ) );

        add_action( 'dokan_coupon_content_area_header', array( $this, 'dokan_coupon_header_render' ), 10 );
        add_action( 'dokan_coupon_content', array( $this, 'dokan_coupon_content_render' ), 10 );
    }

    /**
     * Inistantiate the Dokan_Pro_Coupons class
     *
     * @since 2.4
     *
     * @return object
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Coupons();
        }

        return $instance;
    }

    /**
     * Add Coupon menu
     *
     * @param array $urls
     *
     * @since 2.4
     *
     * @return array $urls
     */
    public function add_coupon_menu( $urls ) {
        $urls['coupons'] = array(
            'title'      => __( 'Coupons', 'dokan' ),
            'icon'       => '<i class="fa fa-gift"></i>',
            'url'        => dokan_get_navigation_url( 'coupons' ),
            'pos'        => 55,
            'permission' => 'dokan_view_coupon_menu'
        );

        return $urls;
    }

    /**
     * Load Coupon template
     *
     * @since 2.4
     *
     * @param  array $query_vars
     *
     * @return void [require once template]
     */
    public function load_coupon_template( $query_vars ) {
        if ( isset( $query_vars['coupons'] ) ) {
            dokan_get_template_part( 'coupon/coupons', '', array( 'pro'=>true ) );
            return;
        }
    }

    /**
     * Render Coupon Header template
     *
     * @since 2.4
     *
     * @return void
     */
    public function dokan_coupon_header_render() {
        $is_edit = ( ! empty( $_GET['post'] ) && $this->is_edit_page ) ? true : false;
        dokan_get_template_part( 'coupon/header', '', array( 'pro' => true, 'is_edit_page' => $this->is_edit_page, 'is_edit' => $is_edit ) );
    }

    /**
     * Render Coupon Content
     *
     * @since 2.4
     *
     * @return void
     */
    public function dokan_coupon_content_render() {
        if ( ! dokan_is_seller_enabled( get_current_user_id() ) ) {
            echo dokan_seller_not_enabled_notice();
        } else {

            $this->list_user_coupons();

            if ( is_wp_error( self::$validated )) {
                $messages = self::$validated->get_error_messages();

                foreach ( $messages as $message ) {
                    dokan_get_template_part('global/dokan-error', '', array( 'deleted' => true, 'message' => $message ) );
                }
            }

            $this->add_coupons_form( self::$validated );
        }
    }

    /**
     * Handle the coupons submission
     *
     * @return void
     */
    function handle_coupons() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        if ( ! dokan_is_user_seller( get_current_user_id() ) ) {
            return;
        }

        // Coupon functionality
        self::$validated = $this->validate();

        if ( !is_wp_error( self::$validated ) ) {
            $this->coupons_create();
        }

        $this->coupun_delete();
    }

    /**
     * Coupon Delete Functionality
     *
     * @since 2.4
     *
     * @return void
     */
    function coupun_delete() {
        if ( !isset( $_GET['post'] ) || !isset( $_GET['action'] ) ) {
            return;
        } else if ( $_GET['action'] != 'delete' ) {
            return;
        }

        if ( ! current_user_can( 'dokan_delete_coupon' ) ) {
            wp_die( __( 'You have not permission to delete this coupon', 'dokan' ) );
        }

        if ( !wp_verify_nonce( $_GET['coupon_del_nonce'], '_coupon_del_nonce' ) ) {
            wp_die( __( 'Are you cheating?', 'dokan' ) );
        }

        wp_delete_post( $_GET['post'], true );
        wp_redirect( add_query_arg( array('message' => 'delete_succefully'), dokan_get_navigation_url( 'coupons' ) ) );
    }

    /**
     * Validate Coupon handler form
     *
     * @since 2.4
     *
     * @return object WP_Error|error
     */
    function validate() {

        if ( !isset( $_POST['coupon_creation'] ) ) {
            return;
        }

        if ( !wp_verify_nonce( $_POST['coupon_nonce_field'], 'coupon_nonce' ) ) {
            wp_die( __( 'Are you cheating?', 'dokan' ) );
        }

        $errors = new WP_Error();

        // Checking permissions for adding and editing
        if ( empty( $_POST['post_id'] ) ) {
            if ( ! current_user_can( 'dokan_add_coupon' ) ) {
                $errors->add( 'title', __( 'You have no permission to add this coupon', 'dokan' ) );
            }
        } else {
            if ( ! current_user_can( 'dokan_edit_coupon' ) ) {
                $errors->add( 'title', __( 'You have no permission to edit this coupon', 'dokan' ) );
            }
        }

        if ( empty( $_POST['title'] ) ) {
            $errors->add( 'title', __( 'Please enter the coupon title', 'dokan' ) );
        }

        if ( empty( $_POST['amount'] ) ) {
            $errors->add( 'amount', __( 'Please enter the amount', 'dokan' ) );
        }

        if ( !isset( $_POST['product_drop_down'] ) || !count( $_POST['product_drop_down'] ) ) {
            $errors->add( 'products', __( 'Please specify any products', 'dokan' ) );
        }

        $this->is_coupon_exist( $_POST['title'], $errors );

        if ( $errors->get_error_codes() ) {
            return $errors;
        }

        return true;
    }

    /**
    * Get the orders total from a specific seller
    *
    * @since version 3
    *
    * @param string $title
    * @param object $error
    *
    * @return object $error
    */
    function is_coupon_exist( $title, $errors ) {
        $args = array( 'post_type' => 'shop_coupon', 'name' => $title );
        $query = get_posts( $args );

        if ( $title ) {
            if ( !empty( $query ) ) {
                if ( empty( $_POST['post_id'] ) || $_POST['post_id'] != $query[0]->ID ) {
                    return $errors->add( 'duplicate', __( 'Coupon title already exists', 'dokan' ) );
                }
            }
        }
    }

    /**
     * Create Coupon hanlder function
     *
     * @since 2.4
     *
     * @return void
     */
    function coupons_create() {
        if ( !isset( $_POST['coupon_creation'] ) ) {
            return;
        }

        if ( !wp_verify_nonce( $_POST['coupon_nonce_field'], 'coupon_nonce' ) ) {
            wp_die( __( 'Are you cheating?', 'dokan' ) );
        }

        if ( empty( $_POST['post_id'] ) ) {

            $post = array(
                'post_title'   => $_POST['title'],
                'post_content' => $_POST['description'],
                'post_status'  => 'publish',
                'post_type'    => 'shop_coupon',
                'post_author'  => dokan_get_current_user_id()
            );

            $post_id = wp_insert_post( $post );
            $message = 'coupon_saved';

        } else {

            $post = array(
                'ID'           => $_POST['post_id'],
                'post_title'   => $_POST['title'],
                'post_content' => $_POST['description'],
                'post_status'  => 'publish',
                'post_type'    => 'shop_coupon',
                'post_author'  => dokan_get_current_user_id()
            );
            $post_id = wp_update_post( $post );
            $message = 'coupon_update';
        }

        if ( !$post_id ) {
            return;
        }

        $customer_email     = array_filter( array_map( 'trim', explode( ',', sanitize_text_field( $_POST['email_restrictions'] ) ) ) );
        $type               = sanitize_text_field( $_POST['discount_type'] );
        $amount             = sanitize_text_field( $_POST['amount'] );
        $usage_limit        = empty( $_POST['usage_limit'] ) ? '' : absint( $_POST['usage_limit'] );
        $expiry_date        = strtotime( sanitize_text_field( $_POST['expire'] ) );
        $apply_before_tax   = isset( $_POST['apply_before_tax'] ) ? 'yes' : 'no';
        $exclude_sale_items = isset( $_POST['exclude_sale_items'] ) ? 'yes' : 'no';
        $show_on_store      = isset( $_POST['show_on_store'] ) ? 'yes' : 'no';
        $minimum_amount     = sanitize_text_field( $_POST['minium_ammount'] );

        if ( isset( $_POST['product_drop_down'] ) ) {
            $product_ids = implode( ',', array_filter( array_map( 'intval', (array) $_POST['product_drop_down'] ) ) );
        } else {
            $product_ids = '';
        }

        if ( isset( $_POST['exclude_product_ids'] ) ) {
            $exclude_product_ids = implode( ',', array_filter( array_map( 'intval', (array) $_POST['exclude_product_ids'] ) ) );
        } else {
            $exclude_product_ids = '';
        }

        if ( isset( $_POST['product_categories'] ) ) {
            $product_categories = array_filter( array_map( 'intval', (array) $_POST['product_categories'] ) );
        } else {
            $product_categories = array();
        }

        if ( isset( $_POST['exclude_product_categories'] ) ) {
            $exclude_product_categories = array_filter( array_map( 'intval', (array) $_POST['exclude_product_categories'] ) );
        } else {
            $exclude_product_categories = array();
        }

        update_post_meta( $post_id, 'discount_type', $type );
        update_post_meta( $post_id, 'coupon_amount', $amount );
        update_post_meta( $post_id, 'product_ids', $product_ids );
        update_post_meta( $post_id, 'exclude_product_ids', $exclude_product_ids );
        update_post_meta( $post_id, 'product_categories', $product_categories );
        update_post_meta( $post_id, 'exclude_product_categories', $exclude_product_categories );
        update_post_meta( $post_id, 'usage_limit', $usage_limit );
        // update_post_meta( $post_id, 'expiry_date', $expiry_date );
        update_post_meta( $post_id, 'date_expires', $expiry_date );
        update_post_meta( $post_id, 'apply_before_tax', $apply_before_tax );
        update_post_meta( $post_id, 'free_shipping', 'no' );
        update_post_meta( $post_id, 'exclude_sale_items', $exclude_sale_items );
        update_post_meta( $post_id, 'show_on_store', $show_on_store );
        update_post_meta( $post_id, 'minimum_amount', $minimum_amount );
        update_post_meta( $post_id, 'customer_email', $customer_email );

        do_action( 'dokan_after_coupon_create', $post_id );

        if ( !defined( 'DOING_AJAX' ) ) {
            wp_redirect( add_query_arg( array('message' => $message), dokan_get_navigation_url( 'coupons' ) ) );
        }
    }

    /**
     * Render coupon Message
     *
     * @return void
     */
    function message() {
        if ( isset( $_GET['message'] ) && $_GET['message'] == 'delete_succefully' ) {
            dokan_get_template_part( 'global/dokan-message', '', array( 'message'=> __( 'Coupon has been deleted successfully!', 'dokan' ) ) );
        }

        if ( isset( $_GET['message'] ) && $_GET['message'] == 'coupon_saved' ) {
            dokan_get_template_part( 'global/dokan-message', '', array( 'message'=> __( 'Coupon has been saved successfully!', 'dokan' ) ) );
        }

        if ( isset( $_GET['message'] ) && $_GET['message'] == 'coupon_update' ) {
            dokan_get_template_part( 'global/dokan-message', '', array( 'message'=> __( 'Coupon has been updated successfully!', 'dokan' ) ) );
        }
    }

    /**
     * Render listing of coupon
     *
     * @since 2.4
     *
     * @return void
     */
    function list_user_coupons() {
        //click add coupon then hide this function
        if( isset( $_GET['view'] ) && $_GET['view'] == 'add_coupons'  ) {
            return;
        }

        if( isset($_GET['post']) &&  $_GET['action'] == 'edit' ) {
            return;
        }

        $perpage = $this->perpage;
        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
        $offset  = ( $pagenum - 1 ) * $perpage;

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $args = array(
            'post_type'      => 'shop_coupon',
            'post_status'    => array('publish'),
            'posts_per_page' => $this->perpage,
            'offset'         => $offset,
            'author'         => dokan_get_current_user_id(),
            'paged'          => $paged
        );

        $coupon_query = new WP_Query( $args );
        $all_coupons  = $coupon_query->get_posts();

        if ( $all_coupons ) {
            $this->total_query_result = $coupon_query->found_posts;
            $this->message();
            dokan_get_template_part( 'coupon/listing', '', array( 'pro' => true, 'coupons' => $coupon_query->posts ) );
            echo $this->pagination();
        } else {
            dokan_get_template_part( 'coupon/no-coupon', '', array( 'pro' => true, 'message' => __( 'No coupons found!', 'dokan' ) ) );
        }
    }

    /**
     * Coupon Pagination
     *
     * @return void
     */
    function pagination() {
        $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
        $num_of_pages = ceil( $this->total_query_result / $this->perpage );
        $base_url = dokan_get_navigation_url( 'coupons' );

        $page_links = paginate_links( array(
            'base'      => $base_url. '%_%',
            'format'    => '?pagenum=%#%',
            'add_args'  => false,
            'prev_text' => __( '&laquo;', 'aag' ),
            'next_text' => __( '&raquo;', 'aag' ),
            'total' => $num_of_pages,
            'current' => $pagenum,
            'type' => 'array'
        ) );

        if ( $page_links ) {
            echo "<ul class='pagination'>\n\t<li>";
            echo join("</li>\n\t<li>", $page_links);
            echo "</li>\n</ul>\n";
            echo '</div>';
        }
    }

    /**
     * Get coupon product list
     *
     * @return object
     */
    function coupon_products_list() {
        global $wpdb;

        $user_id = dokan_get_current_user_id();

        $sql = "SELECT $wpdb->posts.* FROM $wpdb->posts
                WHERE $wpdb->posts.post_author IN ( $user_id )
                    AND $wpdb->posts.post_type = 'product'
                    AND ( ( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' OR $wpdb->posts.post_status = 'pending') )
                ORDER BY $wpdb->posts.post_date DESC";

        return $wpdb->get_results( $sql );
    }

    /**
     * Render Add Coupon Form
     *
     * @param object $validated
     *
     * @return void
     */
    function add_coupons_form( $validated ) {
        //intial time hide this function
        if ( !isset( $_GET['view'] ) ) {
            return;
        } else if ( $_GET['view'] != 'add_coupons' ) {
            return;
        }

        $button_name = __( 'Create Coupon', 'dokan' );

        if ( isset( $_GET['post'] ) && $_GET['action'] == 'edit' ) {

            $post                       = get_post( $_GET['post'] );
            $button_name                = __( 'Update Coupon', 'dokan' );

            $discount_type              = get_post_meta( $post->ID, 'discount_type', true );
            $amount                     = get_post_meta( $post->ID, 'coupon_amount', true );

            $products                   = get_post_meta( $post->ID, 'product_ids', true );
            $exclude_products           = get_post_meta( $post->ID, 'exclude_product_ids', true );
            $product_categories         = get_post_meta( $post->ID, 'product_categories', true );
            $exclude_product_categories = get_post_meta( $post->ID, 'exclude_product_categories', true );
            $usage_limit                = get_post_meta( $post->ID, 'usage_limit', true );
            $expire                     = get_post_meta( $post->ID, 'date_expires', true );
            $apply_before_tax           = get_post_meta( $post->ID, 'apply_before_tax', true );
            $exclide_sale_item          = get_post_meta( $post->ID, 'exclude_sale_items', true );
            $minimum_amount             = get_post_meta( $post->ID, 'minimum_amount', true );
            $customer_email             = get_post_meta( $post->ID, 'customer_email', true );
            $show_on_store              = get_post_meta( $post->ID, 'show_on_store', true );
        }

        $post_id     = isset( $post->ID ) ? $post->ID : '';
        $post_title  = isset( $post->post_title ) ? $post->post_title : '';
        $description = isset( $post->post_content ) ? $post->post_content : '';

        if ( !empty( $post_id ) && !dokan_is_valid_owner( $post_id, dokan_get_current_user_id() ) ) {
            wp_redirect( dokan_get_navigation_url( 'coupons' ) );
            exit();
        }

        $amount                     = isset( $amount ) ? $amount : '';
        $products                   = isset( $products ) ? $products : '';
        $exclude_products           = isset( $exclude_products ) ? $exclude_products : '';
        $product_categories         = !empty( $product_categories ) ? $product_categories : array();
        $exclude_product_categories = !empty( $exclude_product_categories ) ? $exclude_product_categories : array();

        $usage_limit      = isset( $usage_limit ) ? $usage_limit : '';

        if ( isset( $expire ) && ( (string) (int) $expire === $expire )
            && ( $expire <= PHP_INT_MAX )
            && ( $expire >= ~PHP_INT_MAX ) ) {
            $expire = date( 'Y-m-d', $expire );
        } else {
            $expire = !empty( $expire ) ? date( 'Y-m-d', strtotime( $expire ) ) : '';
        }

        $products_id = str_replace( ' ', '', $products );
        $products_id = explode( ',', $products_id );

        if ( isset( $apply_before_tax ) && $apply_before_tax == 'yes' ) {
            $apply_before_tax = 'checked';
        } else {
            $apply_before_tax = '';
        }

        if ( isset( $exclide_sale_item ) && $exclide_sale_item == 'yes' ) {
            $exclide_sale_item = 'checked';
        } else {
            $exclide_sale_item = '';
        }

        if ( isset( $show_on_store ) && $show_on_store == 'yes' ) {
            $show_on_store = 'checked';
        } else {
            $show_on_store = '';
        }

        $minimum_amount = isset( $minimum_amount ) ? $minimum_amount : '';
        $customer_email = isset( $customer_email ) ? implode( ',', $customer_email ) : '';

        if ( is_wp_error( self::$validated ) ) {

            $post_id       = $_POST['post_id'];
            $post_title    = $_POST['title'];
            $description   = $_POST['description'];
            $discount_type = $_POST['discount_type'];
            $amount        = $_POST['amount'];

            if ( isset( $_POST['product_drop_down'] ) ) {
                $products = implode( ',', array_filter( array_map( 'intval', (array) $_POST['product_drop_down'] ) ) );
            } else {
                $products = '';
            }

            if ( isset( $_POST['exclude_product_ids'] ) ) {
                $exclude_products = implode( ',', array_filter( array_map( 'intval', (array) $_POST['exclude_product_ids'] ) ) );
            } else {
                $exclude_products = '';
            }

            if ( isset( $_POST['product_categories'] ) ) {
                $product_categories = implode( ',', array_filter( array_map( 'intval', (array) $_POST['product_categories'] ) ) );
            } else {
                $product_categories = '';
            }

            if ( isset( $_POST['exclude_product_categories'] ) ) {
                $exclude_product_categories = implode( ',', array_filter( array_map( 'intval', (array) $_POST['exclude_product_categories'] ) ) );
            } else {
                $exclude_product_categories = '';
            }

            $usage_limit = $_POST['usage_limit'];
            $expire      = $_POST['expire'];

            if ( isset( $_POST['apply_before_tax'] ) && $_POST['apply_before_tax'] == 'yes' ) {
                $apply_before_tax = 'checked';
            } else {
                $apply_before_tax = '';
            }

            if ( isset( $_POST['exclude_sale_items'] ) && $_POST['exclude_sale_items'] == 'yes' ) {
                $exclide_sale_item = 'checked';
            } else {
                $exclide_sale_item = '';
            }

            if ( isset( $_POST['show_on_store'] ) && $_POST['show_on_store'] == 'yes' ) {
                $show_on_store = 'checked';
            } else {
                $show_on_store = '';
            }

            $minimum_amount = $_POST['minium_ammount'];
            $customer_email = $_POST['email_restrictions'];
        }

        $exclude_products = str_replace( ' ', '', $exclude_products );
        $exclude_products = explode( ',', $exclude_products );
        $discount_type    = ! empty( $_POST['discount_type'] ) ? $_POST['discount_type'] : '';

        if ( empty( $post_id ) && ! current_user_can( 'dokan_add_coupon' ) ) {
            dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to add coupon', 'dokan' ) ) );
        } elseif ( ! empty( $post_id ) && ! current_user_can( 'dokan_edit_coupon' ) ) {
            dokan_get_template_part('global/dokan-error', '', array( 'deleted' => false, 'message' => __( 'You have no permission to edit this coupon', 'dokan' ) ) );
        } else {
            dokan_get_template_part( 'coupon/form', '', array(
                'pro'                        => true,
                'post_id'                    => $post_id,
                'post_title'                 => $post_title,
                'discount_type'              => $discount_type,
                'description'                => $description,
                'amount'                     => $amount,
                'products'                   => $products,
                'exclude_products'           => $exclude_products,
                'product_categories'         => $product_categories,
                'exclude_product_categories' => $exclude_product_categories,
                'usage_limit'                => $usage_limit,
                'expire'                     => $expire,
                'minimum_amount'             => $minimum_amount,
                'customer_email'             => $customer_email,
                'button_name'                => $button_name,
                'exclide_sale_item'          => $exclide_sale_item,
                'show_on_store'              => $show_on_store,
                'all_products'               => $this->coupon_products_list(),
                'products_id'                => $products_id,
            ) );
        }
    }

    /**
     * Get coupon types
     *
     * @return array
     */
    public static function get_coupon_types() {
        return apply_filters( 'dokan_get_coupon_types', [
            'fixed_product'   => __( 'Product Discount', 'dokan' ),
            'percent_product' => __( 'Product % Discount', 'dokan' ),
            'booking_person'  => __( 'Booking Person Discount (Amount Off Per Person)', 'dokan' )
        ] );
    }
}