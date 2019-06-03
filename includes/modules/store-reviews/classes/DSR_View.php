<?php

/**
 * Description of DSR_View
 *
 * @author weDevs
 */
class DSR_View {

    public function __construct() {
        add_action( 'dokan_review_tab_before_comments', array( $this, 'add_or_edit_review' ) );
        add_action( 'dokan_after_load_script', array( $this, 'include_scripts' ) );
        add_action( 'dokan_enqueue_scripts', array( $this, 'include_scripts' ) );

        add_action( 'wp_ajax_dokan_store_rating_ajax_handler', array( $this, 'ajax_handler' ) );
        add_action( 'wp_ajax_nopriv_dokan_store_rating_ajax_handler', array( $this, 'ajax_handler' ) );
    }

    /**
     * Initializes the DSR_View() class
     *
     * Checks for an existing DSR_View() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new DSR_View();
        }

        return $instance;
    }

    /**
     * Hnadles all ajax requests
     *
     * @since 1.0
     *
     * @return void
     */
    function ajax_handler() {

        switch ( $_POST['data'] ) {

            case 'review_form':
                wp_send_json_success( $this->review_form() );
                break;

            case 'edit_review_form':
                wp_send_json_success( $this->edit_review_form() );
                break;

            case 'submit_review':
                    $this->submit_review();
                break;

            default:
                wp_send_json_success( '<div>Error!! try again!</div>' );
                break;
        }
    }

    /**
     * Render Review form
     *
     * @since 1.0
     *
     * @return string
     */
    function review_form(){
        ob_start();
        include_once DOKAN_SELLER_RATINGS_DIR.'/templates/add-review.php';
        return ob_get_clean();
    }

    /**
     * Render edit review form
     *
     * @since 1.0
     *
     * @return string
     */
    function edit_review_form() {
        ob_start();
        include_once DOKAN_SELLER_RATINGS_DIR.'/templates/edit-review.php';
        return ob_get_clean();
    }

    /**
     * Render Single Review Section
     *
     * @since 1.0
     *
     * @return string
     */
    function add_or_edit_review() {

        $seller_id = get_userdata( get_query_var( 'author' ) )->ID;

        //check if valid customer to proceed
        if ( !$this->check_if_valid_customer( $seller_id, get_current_user_id() ) ) {
            return;
        }
        //show add review or edit review

        $args = array(
            'post_type'   => 'dokan_store_reviews',
            'meta_key'    => 'store_id',
            'meta_value'  => $seller_id,
            'author'      => get_current_user_id(),
            'post_status' => 'publish'
        );

        $query = new WP_Query( $args );
        ob_start();

        if ( $query->posts ) {
        ?>
            <h3><?php _e( 'Your Review', 'dokan' ) ?></h3>
            <ol class="commentlist" id="dokan-store-review-single">
                <?php echo $this->render_review_list( $query->posts, __( 'No Reviews found', 'dokan' ) );?>
            </ol>
        <?php

        } else {
            $this->render_add_review_button( $seller_id );
        }

        ob_get_flush();
        wp_reset_postdata();
    }

    /**
     * Render add button for review
     *
     * @since 1.0
     *
     * @param type $seller_id
     *
     * @return string
     */
    function render_add_review_button( $seller_id ) {
        ?>
        <div class="dokan-review-wrapper" style="margin-bottom: 25px;">
            <button class='dokan-btn dokan-btn-sm dokan-btn-theme add-review-btn' data-store_id ='<?php echo $seller_id ?>' ><?php _e(' Write a Review ', 'dokan' ) ?></button>
        </div>
        <div class="dokan-clearfix"></div>

        <?php
    }

    /**
     * Render edit button for review
     *
     * @since 1.0
     *
     * @param type $seller_id
     *
     * @return string
     */
    function render_edit_review_button( $seller_id, $post_id ) {
        ?>
        <div class="dokan-review-wrapper" style="margin-bottom: 25px;">
            <button class='dokan-btn dokan-btn-sm dokan-btn-theme edit-review-btn' data-post_id='<?php echo $post_id ?>' data-store_id ='<?php echo $seller_id ?>' ><?php _e(' Edit', 'dokan' ) ?></button>
        </div>
        <div class="dokan-clearfix"></div>

        <?php
    }

    /**
     * Enqueue JS scripts
     *
     * @since 1.0
     *
     * @return void
     */
    function include_scripts() {

        if ( dokan_is_store_page() ) {
            wp_enqueue_style( 'dokan-magnific-popup' );
            wp_enqueue_script( 'dokan-popup' );
        }
    }

    /**
     * Submit or Edit new review
     *
     * @since 1.0
     *
     * @return JSON Success | Error
     */
    function submit_review() {

        parse_str( $_POST['form_data'], $postdata );

        if ( !wp_verify_nonce( $postdata['dokan-seller-rating-form-nonce'], 'dokan-seller-rating-form-action' ) ) {
            wp_send_json( array(
                'success' => false,
                'msg'     => __( 'Sorry, something went wrong!.', 'dokan' ),
            ) );
        }

        //check if valid customer to proceed
        if ( !$this->check_if_valid_customer( $postdata['store_id'], get_current_user_id() ) ) {
            wp_send_json( array(
                'success' => false,
                'msg'     => __( 'Sorry, something went wrong!.', 'dokan' ),
            ) );
        }

        $rating = intval ( $_POST['rating'] );

        $my_post = array(
            'post_title'     => sanitize_text_field( $postdata['dokan-review-title'] ),
            'post_content'   => wp_kses_post( $postdata['dokan-review-details'] ),
            'author'         => get_current_user_id(),
            'post_type'      => 'dokan_store_reviews',
            'post_status'    => 'publish'
        );

        if ( isset( $postdata[ 'post_id' ] ) ) {
            $post_id = intval( $postdata[ 'post_id' ] );
            $post    = get_post( $post_id );

            if ( get_current_user_id() == $post->post_author ) {
                $my_post[ 'ID' ] = $post->ID;
                $post_id = wp_update_post( $my_post );
            } else {
                $post_id = 0;
            }

        } else {
            $post_id = wp_insert_post( $my_post );
        }

        if ( $post_id ) {
            update_post_meta( $post_id, 'store_id', $postdata['store_id'] );
            update_post_meta( $post_id, 'rating', $rating );

            wp_send_json( array(
                'success' => true,
                'msg'     => __( 'Thanks for your review', 'dokan' ),
            ) );
        } else {
            wp_send_json( array(
                'success' => false,
                'msg'     => __( 'Sorry, something went wrong!.', 'dokan' ),
            ) );
        }
    }

    /**
     * Render review list for store by all customer
     *
     * @since 1.0
     *
     * @param object $posts
     *
     * @return String List of reviews
     */
    function render_review_list( $posts, $msg ) {

        if ( count( $posts ) == 0 ) {
            echo '<span colspan="5">' . __( $msg, 'dokan' ) . '</span>';
            return;
        }
        foreach ( $posts as $review ) {

            $review_date       = get_the_time( 'l, F jS, Y \a\t g:i a', $review );
            $user_info         = get_userdata( $review->post_author );
            $review_author_img = get_avatar( $user_info->user_email, 180 );
            $permalink         = '';

            $rating = get_post_meta( $review->ID, 'rating', true );
            ?>
            <li itemtype="http://schema.org/Review" itemscope="" itemprop="reviews">
                        <div class="review_comment_container">
                            <div class="dokan-review-author-img"><?php echo $review_author_img; ?></div>
                            <div class="comment-text">
                                <a href="<?php echo $permalink; ?>">
                                        <div class="dokan-rating">
                                            <div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf( __( 'Rated %d out of 5', 'dokan' ), $rating ) ?>">
                                                <span style="width:<?php echo ( intval( $rating ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo $rating; ?></strong> <?php _e( 'out of 5', 'dokan' ); ?></span>
                                            </div>
                                        </div>
                                </a>
                                <p>
                                    <strong itemprop="author"><?php echo $user_info->user_nicename ?></strong>
                                    <em class="verified"><?php //echo $single_comment->user_id == 0 ? '(Guest)' : ''; ?></em>
                                    –
                                    <a href="<?php echo $permalink; ?>">
                                        <time datetime="<?php echo date( 'c', strtotime( $review_date ) ); ?>" itemprop="datePublished"><?php echo $review_date; ?></time>
                                    </a>
                                </p>
                                <div class="description" itemprop="description">
                                    <h4><?php echo $review->post_title ?></h4>
                                    <p><?php echo $review->post_content ?></p>
                                </div>
                                <?php
                                    if ( get_current_user_id() == $review->post_author ) {
                                        $seller_id = get_post_meta( $review->ID, 'store_id', true );
                                        ob_start();
                                        $this->render_edit_review_button( $seller_id, $review->ID );
                                        ob_get_flush();
                                    }
                                ?>
                            </div>
                        </div>
                    </li>
            <?php
        }
    }

    /**
     * Check if Customer has bought any product for this seller
     *
     * @since 1.0
     *
     * @param int $seller_id
     *
     * @param int $customer_id
     *
     * @return boolean
     */
    function check_if_valid_customer( $seller_id, $customer_id ) {

        if ( !is_user_logged_in() ) {
            return false;
        }

        if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' ) {
            return true;
        }

        $args = array(
            'post_type'           => 'shop_order',
            'author'              => $seller_id,
            'meta_key'            => '_customer_user',
            'meta_value'          => $customer_id,
            'post_status'         => 'wc-completed'
        );

        $query = new WP_Query( $args );

        if( $query->posts ) {
            return true;
        }

        return false;
    }
}

$dsr_view = DSR_View::init();
