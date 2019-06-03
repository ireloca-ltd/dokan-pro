<?php

global $user_login;

$seller_id = ( isset( $_POST['store_id'] ) ) ? $_POST['store_id'] : 0;
$post_id   = ( isset( $_POST['post_id'] ) ) ? $_POST['post_id'] : 0;

$post = get_post( $post_id );

if ( get_current_user_id() != $post->post_author ) {
    die();
}

$store_info = dokan_get_store_info( $seller_id );

wp_get_current_user();

?>

<div class="dokan-add-review-wrapper">
    <strong><?php printf( __( 'Hi, %s', 'dokan' ), $user_login ) ?></strong>

<div class="dokan-seller-rating-intro-text">
    <?php printf( __( "Update your Experience with <a href='%s' target='_blank'>%s</a>", 'dokan' ), dokan_get_store_url( $seller_id ), $store_info['store_name'] ) ?>
</div>
        <form class="dokan-form-container" id="dokan-add-review-form">

            <div id="dokan-seller-rating"></div>

            <div class="dokan-form-group">
                <label class="dokan-form-label" for="dokan-review-title"><?php _e( 'Title :', 'dokan' ) ?></label>
                <input required class="dokan-form-control" type="text" name='dokan-review-title' id='dokan-review-title' value="<?php echo $post->post_title ?>"/>
            </div>

            <div class="dokan-form-group">
                <label class="dokan-form-label" for="dokan-review-details"><?php _e( 'Your Review :', 'dokan' ) ?></label>
                <textarea required class="dokan-form-control" name='dokan-review-details' rows="5" id='dokan-review-details'><?php echo $post->post_content ?></textarea>
            </div>
            <input type="hidden" name='store_id' value="<?php echo $seller_id; ?>" />
            <input type="hidden" name='post_id' value="<?php echo $post->ID; ?>" />

            <?php wp_nonce_field( 'dokan-seller-rating-form-action', 'dokan-seller-rating-form-nonce' ); ?>
            <div class="dokan-form-group">
                <input id='support-submit-btn' type="submit" value="<?php _e( 'Submit', 'dokan' ) ?>" class="dokan-w5 dokan-btn dokan-btn-theme"/>
            </div>
        </form>
</div>
<div class="dokan-clearfix"></div>
<?php $rtl = is_rtl() ? 'true' : 'false' ?>
<script>
    (function($){

    var Dokan_Seller_Rating_Form = {

        init : function() {
            $("#dokan-seller-rating").rateYo({
                rating: <?php echo get_post_meta( $post->ID, 'rating', true) ?>,
                starWidth: "20px",
                fullStar: true,
                minValue: 1,
                rtl : <?php echo $rtl ?>
            });
        }
    };

    $(function() {
        Dokan_Seller_Rating_Form.init();
    });

})(jQuery);
</script>