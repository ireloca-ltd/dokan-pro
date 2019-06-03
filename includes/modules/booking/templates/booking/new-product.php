<?php
global $post;

$post_id      = NULL;
$post_title   = '';
$post_content = '';
$post_excerpt = '';
$post_status  = 'pending';

if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
    $post_title     = $post->post_title;
    $post_content   = $post->post_content;
    $post_excerpt   = $post->post_excerpt;
    $post_status    = $post->post_status;
    $product        = wc_get_product( $post_id );

    $_visibility    = ( version_compare( WC_VERSION, '2.7', '>' ) ) ? $product->get_catalog_visibility() : get_post_meta( $post_id, '_visibility', true );
    $visibility_options = dokan_get_product_visibility_options();

    $_enable_reviews = $post->comment_status;
}
//
//$_downloadable   = get_post_meta( $post_id, '_downloadable', true );
//$_stock          = get_post_meta( $post_id, '_stock', true );
//$_stock_status   = get_post_meta( $post_id, '_stock_status', true );
$_virtual               = get_post_meta( $post_id, '_virtual', true );
$is_virtual             = ( 'yes' == $_virtual ) ? true : false;
$has_persons  = get_post_meta( $post_id, '_wc_booking_has_persons', true );
$has_resource = get_post_meta( $post_id, '_wc_booking_has_resources', true );

$template_args = array(
    'is_booking' => true,
    'post_id'    => $post_id
);
$user_id   = get_current_user_id();
$stripe_id = get_user_meta( $user_id, '_stripe_connect_access_key', true );
echo '<style>.menu-item-has-children{pointer-events:none!important;opacity:0.3;}</style>';
?>
<link rel="stylesheet" href="/wp-content/plugins/ireloca-dokan-plugin/css/new-product-tabs.css" type="text/css">
<script src="/wp-content/plugins/ireloca-dokan-plugin/js/bootstrap.min.js"></script>

<header class="dokan-dashboard-header dokan-clearfix">
    <h1 class="entry-title">
        <?php if ( !$post_id ): ?>
            <?php _e( 'Add product', 'dokan' ); ?>
        <?php else: ?>
            <?php _e( $title , 'dokan' ); ?>
            <span class="dokan-label <?php echo dokan_get_post_status_label_class( $post->post_status ); ?> dokan-product-status-label">
                <?php echo dokan_get_post_status( $post->post_status ); ?>
            </span>

            <?php if ( $post->post_status == 'publish' ) { ?>
                <span class="dokan-right">
                    <a class="view-product dokan-btn dokan-btn-sm" href="<?php echo get_permalink( $post->ID ); ?>" target="_blank"><?php _e( 'View Product', 'dokan' ); ?></a>
                </span>
            <?php } ?>

            <?php if ( $_visibility == 'hidden' ) { ?>
                <span class="dokan-right dokan-label dokan-label-default dokan-product-hidden-label"><i class="fa fa-eye-slash"></i> <?php _e( 'Hidden', 'dokan' ); ?></span>
            <?php } ?>

        <?php endif ?>
    </h1>
</header><!-- .entry-header -->

<?php
$wpml_is_original = wpml_is_original();
$is_original = $wpml_is_original['is_original'];
$original_id = $wpml_is_original['original_ID'];
$thePostID = $post->ID;

 if ( empty( $stripe_id ) && (current_user_can( 'seller' )) ) : ?>
  <div class="dokan-alert dokan-alert-danger">
 <a class="dokan-close" data-dismiss="alert">&times;</a>
           <?php  echo __( 'Your Stripe account isn\'t active yet. Please connect to stripe first!', 'dokan' ), '<br><a href='.dokan_get_navigation_url('settings/payment').'>', __( 'Connect to Stripe', 'dokan' ).'</a>';?>
</div>
   <?php else: ?>

<div class="product-edit-new-container product-edit-container">
    <?php if ( Dokan_Template_Products::$errors ) { ?>
        <div class="dokan-alert dokan-alert-danger">
            <a class="dokan-close" data-dismiss="alert">&times;</a>

            <?php foreach ( Dokan_Template_Products::$errors as $error ) { ?>

                <strong><?php _e( 'Error!', 'dokan' ); ?></strong> <?php echo $error ?>.<br>

            <?php } ?>
        </div>
    <?php } ?>

    <?php if ( isset( $_GET['message'] ) && $_GET['message'] == 'success' ) { ?>
        <div class="dokan-message">
            <button type="button" class="dokan-close" data-dismiss="alert">&times;</button>
            <strong><?php _e( 'Success!', 'dokan' ); ?></strong> <?php _e( 'The product has been saved successfully.', 'dokan' ); ?>

            <?php if ( $post->post_status == 'publish' ) { ?>
                <a href="<?php echo get_permalink( $post_id ); ?>" target="_blank"><?php _e( 'View Product &rarr;', 'dokan' ); ?></a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php
    $can_sell = apply_filters( 'dokan_can_post', true );

    if ( $can_sell ) {

        if ( dokan_is_seller_enabled( get_current_user_id() ) ) {
            ?>
             <?php  if ( empty( $post_id ) || ($thePostID == $original_id)) { ?>
            <?php $user_id = get_current_user_id(); ?>
            <?php $posts   = count_user_posts( $user_id, $post_type = 'product', $public_only = false ); ?>
            <?php if ($posts < 1) { ?>
            <?php $current_user = wp_get_current_user(); ?>
            <?php $first_name = esc_html( $current_user->user_firstname ); ?>
            <div class="first_product"><span class="first_product_text"><?php printf( __( 'Hi there %s! If you need any help while adding or editing, just toggle the "Helper Mode" button on the right, to see a detailed explanation.', 'dokan-wc-booking' ), $first_name ); ?></span><button type="button" class="dokan-close" data-dismiss="alert">&times;</button></div>
          <?php } ?>
            <form class="form-horizontal dokan-product-edit-form" role="form" method="post">
                <?php
                if ( $post_id ):
                    do_action( 'dokan_product_data_panel_tabs' );
                endif;

                do_action( 'dokan_product_edit_before_main' );
                ?>
                <fieldset>
                  <div class="tabbable">
                    <div class="helper_button_field">
                    <span style="margin-left:15px;margin-right:5px;font-size:14px;vertical-align: sub;"><?php _e('Help', 'dokan-wc-booking' ); ?></span><label class="switch">
                      <input type="checkbox" id="helper_toggle">
                      <span class="slider round"></span>
                    </label></div>
                    <?php $advanced_mode = get_post_meta( $post_id, 'advanced_toggle', true ); ?>
                    <div class="advanced_button_field" style="float:right">
                    <span style="margin-right:5px;font-size:14px;vertical-align: sub;"><?php _e('Advanced', 'dokan-wc-booking' ); ?></span><label class="switch">
                      <?php if ( "advanced" === $advanced_mode ) {?>
                      <input type="checkbox" name="advanced_toggle" id="advanced_toggle" value="advanced" checked>
                      <span class="slider round"></span>
                    <?php } else {?>
                      <input type="checkbox" name="advanced_toggle" id="advanced_toggle" value="advanced">
                      <span class="slider round"></span>
                    <?php } ?>
                    </label></div>

                    <!-- Progress bar calculations -->
                    <?php if ( !empty( $post_title ) ){ $progress1 = "15";} else {$progress1 = "0";} ?>
                    <?php if ( has_post_thumbnail( $post_id ) ){ $progress2 = "15";} else {$progress2 = "0";} ?>
                    <?php if ( get_post_meta( $post_id, '_product_attributes', true ) ){ $progress4 = "15";} else {$progress4 = "0";} ?>
                    <?php if ( get_post_meta( $post_id, '_wc_booking_restricted_days', true ) ){ $progress7 = "25";} else {$progress7 = "0";} ?>
                    <?php if ( !empty( $post_excerpt ) ){ $progress8 = "15";} else {$progress8 = "0";} ?>
                    <?php if ( !empty( $post_content ) ){ $progress9 = "15";} else {$progress9 = "0";} ?>
                    <?php $progressbar = $progress1 + $progress2 + $progress4 + $progress7 + $progress8 + $progress9; ?>
                    <!-- Tab List -->
                    <ul class="nav nav-tabs" id="tab_bar">
                      <li><a href="#tab1" data-toggle="tab" class="active"><?php _e('Main', 'dokan-wc-booking' ); ?></a></li>
                      <?php if ( !empty( $post_id ) ) { ?>
                      <li><a href="#tab2" data-toggle="tab"><?php _e('Availability', 'dokan-wc-booking' ); ?></a></li>
                      <li><a href="#tab3" data-toggle="tab"><?php _e('Other Settings', 'dokan-wc-booking' ); ?></a></li>
                      <li><a href="#tab4" data-toggle="tab"><?php _e('Price', 'dokan-wc-booking' ); ?></a></li>
                      <li><a href="#tab5" data-toggle="tab"><?php _e('Location', 'dokan-wc-booking' ); ?></a></li>
                      <li><a href="#tab6" data-toggle="tab"><?php _e('Search Attributes', 'dokan-wc-booking' ); ?></a></li>
                      <li><a href="#tab7" data-toggle="tab"><?php _e('Status', 'dokan-wc-booking' ); ?></a></li>
                    <?php } else { ?>
                      <li class="disabledTab"><span><?php _e('Availability', 'dokan-wc-booking' ); ?></a></li>
                      <li class="disabledTab"><span><?php _e('Other Settings', 'dokan-wc-booking' ); ?></span></li>
                      <li class="disabledTab"><span><?php _e('Price', 'dokan-wc-booking' ); ?></span></li>
                      <li class="disabledTab"><span><?php _e('Location', 'dokan-wc-booking' ); ?></span></li>
                      <li class="disabledTab"><span><?php _e('Search Attributes', 'dokan-wc-booking' ); ?></span></li>
                      <li class="disabledTab"><span><?php _e('Status', 'dokan-wc-booking' ); ?></span></li>
                    <?php }?>
                    </ul>
                    <!-- Progress Bar -->
                    <?php if ( 0 != $progressbar ) { ?>
                    <div class="dokan-progress" style="max-width: 98%;">
                      <div class="dokan-progress-bar dokan-progress-bar-info dokan-progress-bar-striped" id="progressbar" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo $progressbar . __( '% of the product is complete', 'dokan' ); ?></div>
                    </div>
                    <script>
                    document.onload = document.getElementById('progressbar').style.width = '<?php echo $progressbar; ?>%';
                    </script>
                  <?php } else { ?>
                    <div class="dokan-progress" style="max-width: 98%;">
                      <div class="dokan-progress-bar dokan-progress-bar-info dokan-progress-bar-striped" id="progressbar" role="progressbar" style="width: 0%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"><?php echo '0%' ?></div><span style="display: inherit; text-align: center; margin-right: 3%; color: #666; line-height: 1.5;"><?php echo __( 'Begin by adding a title and clicking save, to start setting up your new product.', 'dokan' ); ?></span>
                    </div>
                    <script>
                    document.onload = document.getElementById('progressbar').style.width = '3%';
                    </script>
                  <?php  } ?>
                  <!--Add our save spinner -->
                  <?php
                  $spinner_message  = __(  'Saving your product:' , 'text-domain' );
                  $spinner_message2 = __(  'This page will refresh when it is done.' , 'text-domain' );
                  ?>
                  <div id="ireloca_spinner" style="display:none;z-index:999999!important;"></div>
                  <div id="ireloca_spinner_message" style="display:none;z-index:999999!important;"><img src="https://storage.googleapis.com/gcs.ireloca.com/2019/03/772381d4-ireloca_ball_spinner_new.gif"><br><br><b><?php echo $spinner_message ?><br><?php echo $spinner_message2 ?></b><br><br></div>
                  <!--End our save spinner -->

                  <div class="tab-content">

                  <!--Tab1-->
                  <div id="tab1" class="tab-pane active">
                    <div class="tab-buttons">
                    <div class="btn-group" style="float: left;">
                        <button class="disabled-tab btnprev" id="prevtab" type="button"><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<?php _e( 'Previous Tab', 'dokan' ); ?></button>
                    </div>
                    <?php if ( empty ( $post_id )){ ?>
                    <div class="btn-group" style="float: right;">
                        <button class="disabled-tab btnnext" id="nexttab" type="button"><?php _e( 'Next Tab', 'dokan' ); ?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></button>
                    </div>
                    <?php } else {?>
                    <div class="btn-group" style="float: right;">
                        <button class="btnnext" id="nexttab" type="button"><?php _e( 'Next Tab', 'dokan' ); ?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></button>
                    </div>
                    <?php } ?>
                  </div>
                  <div class="dokan-edit-row">
                  <div class="dokan-section-heading" data-togglehandler="dokan_product_shipping_tax">
                    <h2><i class="fa fa-cogs" aria-hidden="true"></i> <?php _e( 'Main', 'dokan' ); ?></h2>
                    <p><?php _e( 'Manage the main settings for this product', 'dokan' ); ?></p>
                    <div class="dokan-clearfix"></div>
                  </div>
                  <div class="dokan-section-content">
                <div class="dokan-form-top-area">
                    <div class="content-half-part">
                        <div class="dokan-form-group">
                            <input type="hidden" name="dokan_product_id" value="<?php echo $post_id; ?>">

                            <label for="post_title" class="form-label"><?php _e( 'Title', 'dokan' ); ?></label>
                            <div class="dokan-product-title-alert dokan-hide dokan-alert dokan-alert-danger">
                                <?php _e( 'Please choose a Name!', 'dokan' ); ?>
                            </div>
	                            <?php dokan_post_input_box( $post_id, 'post_title', array( 'placeholder' => __( 'Product name..', 'dokan-wc-booking' ), 'value' => $post_title ) ); ?>
	                        	<div class="helper_text" style="display:none"><p><i><?php _e( 'Here you type in the title of the Product you want to sell. This is the first thing that customers see in the search results and therefore very important to choose wisely. The title cannot be more than 10 words long and it should explain the product instantly.', 'dokan-wc-booking' ); ?></i></p></div>
                        </div>
                        <div class="dokan-form-group virtual-checkbox" style="display:none;">
                            <label>
                                <input type="checkbox" <?php checked( $is_virtual, true ); ?> class="_is_virtual" name="_virtual" id="_virtual"> <?php _e( 'Virtual', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Virtual products are intangible and aren\'t shipped.', 'dokan' ); ?>"></i>
                            </label>
                        </div>

                        <div class="dokan-form-group">
                            <label for="product_cat" class="form-label"><?php _e( 'Category', 'dokan' ); ?></label>
                            <?php
                            $term = array();
                            $term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

                            $multiple = dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ? '' : 'multiple';
                            $select_name = "multiple" == $multiple ? 'product_cat[]' : 'product_cat';

                            include_once DOKAN_LIB_DIR.'/class.taxonomy-walker.php';
                            $category_args = array(
                                'show_option_none' => __( '', 'dokan' ),
                                'hierarchical'     => 1,
                                'hide_empty'       => 0,
                                'name'             => $select_name,
                                'id'               => 'product_cat',
                                'taxonomy'         => 'product_cat',
                                'title_li'         => '',
                                'class'            => 'product_cat dokan-form-control dokan-select2',
                                'exclude'          => '',
                                'selected'         => $term,
                                'echo'             => 0,
                                'walker'           => new DokanTaxonomyWalker( $post_id )
                            );

                            $drop_down_category = wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
                            $replace_attrb      = "<select data-placeholder='".__( 'Select product category','dokan' )."' ". $multiple ;

                            echo str_replace( '<select', $replace_attrb, $drop_down_category );
                            ?>
                            <div class="helper_text" style="display:none"><p><i><?php _e( 'This is the main category where your product will show up. Customers should be able to find your activity when they search a specific category or when they land on a category page through google. You can select a category by simply clicking on the drop-down menu with categories for you to choose from. If you can’t find a suitable category, or are you not sure which one to choose, please contact us at <a href="mailto:supplier@ireloca.com">supplier@ireloca.com</a>. In the meantime, save it as "Other Sports & Entertainment". ', 'dokan-wc-booking' ); ?></i></p></div>
                        </div>

                        <div class="dokan-product-short-description">
                            <label for="post_excerpt" class="form-label"><?php _e( 'Description', 'dokan' ); ?></label>
                            <?php dokan_post_input_box( $post_excerpt, 'post_excerpt', array( 'value' => $post_excerpt, 'placeholder' => __( 'Add a description of your product here.', 'dokan' ) ), 'textarea' ); ?>
                            <div class="helper_text" style="display:none">
                        <p><i>
                        <?php _e('This is the full description of your product. Clearly describe what you are offering. Remember that customers don’t want to read long texts, so try and keep this as short as possible, whilst still outlining your product well. This text will show up in the search results and on the product page. ', 'dokan-wc-booking' ); ?>
                         </i></p>
                        </div>
                        </div>
                        <?php if ( !empty ( $post_id )){ ?>
                        <div class="dokan-form-group">
                            <label for="_purchase_note" class="form-label"><?php _e( 'Additional notes for customers (Booking Confirmation/Email)', 'dokan' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_purchase_note', array( 'placeholder' => __( 'Customer will get this info in their order email', 'dokan' ) ), 'textarea' ); ?>
                        </div>
                      <?php } else {?>
                        <div class="dokan-form-group">
                            <label for="_purchase_note2" class="form-label"><?php _e( 'Additional notes for customers (Booking Confirmation/Email)', 'dokan' ); ?></label>
                                <form>
                                  <textarea class="dokan-form-control" rows="4" type="text" name="_purchase_note2" placeholder="<?php echo __('Save the product first to add additional notes for customers.', 'dokan'); ?>" disabled></textarea>
                                </form>
                        </div>
                      <?php } ?>

                    </div><!-- .content-half-part -->

                    <div class="content-half-part featured-image">

                        <div class="dokan-new-product-featured-img dokan-feat-image-upload">
                            <?php
                            $wrap_class        = ' dokan-hide';
                            $instruction_class = '';
                            $feat_image_id     = 0;

                            if ( has_post_thumbnail( $post_id ) ) {
                                $wrap_class        = '';
                                $instruction_class = ' dokan-hide';
                                $feat_image_id     = get_post_thumbnail_id( $post_id );
                            }
                            ?>

                            <div class="instruction-inside<?php echo $instruction_class; ?>">
                                <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="<?php echo $feat_image_id; ?>">

                                <i class="fa fa-cloud-upload"></i>
                                <a href="#" class="dokan-feat-image-btn btn btn-sm"><?php _e( 'Upload a product cover image', 'dokan' ); ?></a>
                            </div>

                            <div class="image-wrap<?php echo $wrap_class; ?>">
                                <a class="close dokan-remove-feat-image">&times;</a>
                                <?php if ( $feat_image_id ) { ?>
                                    <?php echo get_the_post_thumbnail( $post_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array( 'height' => '', 'width' => '' ) ); ?>
                                <?php } else { ?>
                                    <img height="" width="" src="" alt="">
                                <?php } ?>
                            </div>
                        </div><!-- .dokan-feat-image-upload -->

                        <div class="dokan-product-gallery">
                            <div class="dokan-side-body" id="dokan-product-images">
                                <div id="product_images_container">
                                    <ul class="product_images dokan-clearfix">
                                        <?php
                                        $product_images = get_post_meta( $post_id, '_product_image_gallery', true );
                                        $gallery        = explode( ',', $product_images );

                                        if ( $gallery ) {
                                            foreach ( $gallery as $image_id ) {
                                                if ( empty( $image_id ) ) {
                                                    continue;
                                                }

                                                $attachment_image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                                                ?>
                                                <li class="image" data-attachment_id="<?php echo $image_id; ?>">
                                                    <img src="<?php echo $attachment_image[0]; ?>" alt="">
                                                    <a href="#" class="action-delete" title="<?php esc_attr_e( 'Delete image', 'dokan' ); ?>">&times;</a>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <li class="add-image add-product-images tips" data-title="<?php _e( 'Add gallery image', 'dokan' ); ?>">
                                            <a href="#" class="add-product-images"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                    <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_images ); ?>">
                                </div>
                            </div>
                            <div class="helper_text" style="display:none"><p><i><?php _e( 'To upload an Image you simply click on "Upload a product cover image". Your own media library will open. On the top left you can see the option to upload a file to this library and afterwards it will be available for selection. For SEO (google friendliness) purposes, you should fill in the caption (title), alt text (which is shown when the image cannot be loaded) and the image description (for example which product this is for). <br><br> The first image you select is the featured image and therefore the most important. It will show up on the search results and also be the first picture the customer sees when they come to your product page. Make sure that the featured image (cover image) is a clear description of the actual activity and is not your company logo.<br><br>Once a featured image has been selected, you can set so called ‘gallery images’, these extra images are available on the product pages for the customers to scroll through. Adding at least 2 or 3 (good quality) images per product will help customers choose your product.', 'dokan-wc-booking' ); ?></i></p></div>
                        </div> <!-- .product-gallery -->
                    </div><!-- .content-half-part -->
                </div><!-- .dokan-form-top-area -->
              </div>
                  <div class="dokan-form-group content-half-part" style="display:none;">
                      <label for="_visibility" class="form-label"><?php _e( 'Visibility', 'dokan' ); ?></label>
                      <select name="_visibility" id="_visibility" class="dokan-form-control">
                          <?php foreach ( $visibility_options as $name => $label ): ?>
                              <option value="<?php echo $name; ?>" <?php selected( $_visibility, $name ); ?>><?php echo $label; ?></option>
                          <?php endforeach ?>
                      </select>
                  </div>
                  <?php
                  if ( empty($post_id) ){?>
                  <div class="dokan-form-group" style="display:none;">
                      <label>
                          <input name="_wc_booking_has_persons" id="_wc_booking_has_persons" type="checkbox" checked<?php checked( $has_persons, true ); ?> class="dokan-booking-person"> <?php _e( 'Has persons', 'dokan' ); ?>
                          <input name="_wc_booking_person_cost_multiplier" id="_wc_booking_person_cost_multiplier" value="yes" type="checkbox" checked<?php checked( get_post_meta( $post_id, '_wc_booking_person_cost_multiplier', true ), true ); ?> > <?php _e( 'Multiply all costs by person count', 'dokan' ); ?>
                          <input name="_wc_booking_person_qty_multiplier" id="_wc_booking_person_qty_multiplier" value="yes" type="checkbox" checked<?php checked( get_post_meta( $post_id, '_wc_booking_person_qty_multiplier', true ), true ); ?> > <?php _e( 'Count persons as bookings', 'dokan' ); ?>
                      </label>
                  </div>
                <?php } ?>

                  <div class="dokan-clearfix"></div>

                  <div class="dokan-form-group" style="display:none">
                      <?php $_enable_reviews = ( $post->comment_status == 'open' ) ? 'yes' : 'no'; ?>
                      <?php dokan_post_input_box( $post_id, '_enable_reviews', array( 'value' => $_enable_reviews, 'label' => __( 'Enable product reviews', 'dokan' ) ), 'checkbox' ); ?>
                  </div>
          </div><!-- .dokan-other-options -->
            </div>
                    <?php
                    $duration_type = get_post_meta( $post_id, '_wc_booking_duration_type', true );
                    $duration      = max( absint( get_post_meta( $post_id, '_wc_booking_duration', true ) ), 1 );
                    $duration_unit = get_post_meta( $post_id, '_wc_booking_duration_unit', true );

                    //availability
                    $wc_booking_qty        = max( absint( get_post_meta( $post_id, '_wc_booking_qty', true ) ), 1 );
                    $booking_min_date_unit = get_post_meta( $post_id, '_wc_booking_min_date_unit', true );
                    $booking_max_date_unit = get_post_meta( $post_id, '_wc_booking_max_date_unit', true );


                    $booking_buffer_period  = absint( get_post_meta( $post_id, '_wc_booking_buffer_period', true ) );
                    $adjacent_buffer_period = get_post_meta( $post_id, '_wc_booking_apply_adjacent_buffer', true );

                    $booking_default_date_availability = get_post_meta( $post_id, '_wc_booking_default_date_availability', true );
                    $booking_check_availability = get_post_meta( $post_id, '_wc_booking_check_availability_against', true );

                    $booking_range_picker = get_post_meta( $post_id, '_wc_booking_enable_range_picker', true );
                    $booking_first_block  = get_post_meta( $post_id, '_wc_booking_first_block_time', true );

                    $booking_confirmation = get_post_meta( $post_id, '_wc_booking_requires_confirmation', true );
                    $booking_cancellation = get_post_meta( $post_id, '_wc_booking_user_can_cancel', true );
                    $cancellation_limit   = get_post_meta( $post_id, '_wc_booking_cancel_limit', true );
                    $cancellation_limit_unit = get_post_meta( $post_id, '_wc_booking_cancel_limit_unit', true );
                    //costs
                    //resources
                    $booking_resource_label      = get_post_meta( $post_id, '_wc_booking_resource_label', true );
                    $booking_resource_assignment = get_post_meta( $post_id, '_wc_booking_resources_assignment', true );

                    $calendar_display_mode = get_post_meta($post_id,'_wc_booking_calendar_display_mode',true);

                    $booking_product                   = new WC_Product_Booking( $post_id );
                    $booking_has_restricted_days       = $booking_product->has_restricted_days();
                    $booking_restricted_days           = $booking_product->get_restricted_days();

                    for ( $i = 0; $i < 7; $i++ ) {

                        if ( $booking_restricted_days && in_array( $i, $booking_restricted_days ) ) {
                            $restricted_days[ $i ] = $i;
                        } else {
                            $restricted_days[ $i ] = false;
                        }
                    }


                    if ( $post_id ) { ?>
                      <!-- TAB 2 -->
                    <div id="tab2" class="tab-pane">
                      <div class="tab-buttons">
                      <div class="btn-group" style="float: left;">
                          <button class="btnprev" id="prevtab" type="button"><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<?php _e( 'Previous Tab', 'dokan' ); ?></button>
                      </div>
                      <div class="btn-group" style="float: right;">
                          <button class="btnnext" id="nexttab" type="button"><?php _e( 'Next Tab', 'dokan' ); ?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></button>
                      </div>
                    </div>
                    <div id="bookings_availability" class="bookings_availability availability_fields dokan-edit-row dokan-clearfix">

                        <div class="dokan-section-heading" data-togglehandler="bookings_availability">
                            <h2><i class="fa fa-calendar" aria-hidden="true"></i> <?php _e( 'Availability' , 'dokan' ) ?></h2>
                            <p><?php _e( 'Set availabilities' , 'dokan' ) ?></p>
                            <a href="#" class="dokan-section-toggle">
                                <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
                            </a>
                            <div class="dokan-clearfix"></div>
                        </div>

                        <div class="dokan-section-content">

                            <div class="dokan-input-group content-half-part" style="display:none;">
                                <label for="_wc_booking_max_date" class="form-label"><?php _e( 'Maximum booking window ( into the future )', 'dokan' ); ?></label>
                                <input type="number" class="dokan-form-control" name="_wc_booking_max_date" id="_wc_booking_max_date" value="24" step="1" min="1" style="margin-right: 7px; width: 4em;">
                                <select name="_wc_booking_max_date_unit" id="_wc_booking_max_date_unit" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                                    <option value="month" <?php selected( $booking_max_date_unit, 'month' ); ?>><?php _e( 'Month(s)', 'dokan' ); ?></option>
                                    <option value="week" <?php selected( $booking_max_date_unit, 'week' ); ?>><?php _e( 'Week(s)', 'dokan' ); ?></option>
                                    <option value="day" <?php selected( $booking_max_date_unit, 'day' ); ?>><?php _e( 'Day(s)', 'dokan' ); ?></option>
                                    <option value="hour" <?php selected( $booking_max_date_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'dokan' ); ?></option>
                                </select>
                                <div class="helper_text" style="display:none">
                                  <p><i>
                                  <?php _e('Here you set up how far in advance customers can book your activity. This means if for example it is set to 1 month, even if you have availability, customers won’t be able to book it 3 months before the activity starts. The standard for this is set to 12 months, but you can change this to your needs. ', 'dokan-wc-booking' ); ?>
                                   </i></p>
                                  </div>
                            </div>

                            <div class="dokan-form-group" style="display:none;">
                                <label for="_wc_booking_buffer_period" class="form-label"><?php _e( 'Require a buffer period of ( ', 'dokan' ); ?><span id='_booking_binded_label'>minutes</span><?php _e( ' ) between bookings', 'dokan' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_wc_booking_buffer_period', array( 'step' => 1, 'value' => $booking_buffer_period ), 'number' ); ?>
                            </div>

                            <div class="dokan-form-group" style="display:none;">
                                <label class="form-label">
                                    <!--<input name="_wc_booking_apply_adjacent_buffer" id="_wc_booking_apply_adjacent_buffer" value="0" type="hidden" >-->
                                    <input name="_wc_booking_apply_adjacent_buffer" id="_wc_booking_apply_adjacent_buffer" value="1" type="checkbox" <?php checked( $adjacent_buffer_period ); ?> class="dokan-booking-adjacent-buffer"> <?php _e( 'Adjacent Buffering ?', 'dokan' ); ?>
                                    <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'By default buffer period applies forward into the future of a booking. Enabling this option will apply adjacently (before and after Bookings)', 'dokan' ) ?>">
                                        <i class="fa fa-question-circle"></i>
                                    </span>
                                </label>
                            </div>

                            <div class="dokan-form-group" style="display:none;">
                                <label for="_wc_booking_default_date_availability" class="form-label"><?php _e( 'All dates are...', 'dokan' ); ?></label>
                                <select name="_wc_booking_default_date_availability" id="_wc_booking_default_date_availability" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                                    <option value="non-available" <?php selected( $booking_default_date_availability, 'non-available' ); ?>><?php _e( 'not-available by default', 'dokan' ); ?></option>
                                      <option value="available" <?php selected( $booking_default_date_availability, 'available' ); ?>><?php _e( 'available by default', 'dokan' ); ?></option>
                                </select>
                                <span class="form-label"><i><?php _e( 'This option affects how you use the rules below.', 'dokan' ); ?></i></span>
                            </div>

                            <div class="dokan-form-group" style="display:none;">
                                <label for="_wc_booking_check_availability_against" class="form-label"><?php _e( 'Check rules against...', 'dokan' ); ?></label>
                                <select name="_wc_booking_check_availability_against" id="_wc_booking_check_availability_against" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                                    <option value="" <?php selected( $booking_check_availability, "" ); ?>><?php _e( 'All blocks being booked', 'dokan' ); ?></option>
                                    <option value="start" <?php selected( $booking_check_availability, true ); ?>><?php _e( 'The starting block only', 'dokan' ); ?></option>
                                </select>
                                <span class="form-label"><i><?php _e( 'This option affects how bookings are checked for availability.', 'dokan' ); ?></i></span>
                            </div>

                            <div class="dokan-form-group _wc_booking_first_block_time_field" style="display:none;">
                                <label for="_wc_booking_first_block_time" class="form-label" style="display:none;"><?php _e( 'First block starts at...', 'dokan' ); ?></label>
                                <input type="time" name="_wc_booking_first_block_time" id="_wc_booking_first_block_time" value="<?php echo $booking_first_block ?>" placeholder="HH:MM:" style="display:none;">
                            </div>

                            <div class="dokan-form-group dokan_booking_has_restricted_days_field" style="display:none;">
                                <label class="form-label">
                                    <input type="checkbox" name="_wc_booking_has_restricted_days" id="dokan_booking_has_restricted_days_field" value="yes" checked <?php checked( $booking_has_restricted_days, 'yes' ) ?>>
                                    <?php _e( 'Restrict start and end days?', 'dokan' ); ?>

                                    <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Restrict bookings so that they can only start on certain days of the week. Does not affect availability.', 'dokan' ) ?>">
                                        <i class="fa fa-question-circle"></i>
                                    </span>

                                </label>
                            </div>

                            <div class="dokan-booking-day-restriction">
                              <label for="_wc_booking_min_date" class="form-label"><?php _e( 'Bookable days', 'dokan' ); ?></label>
                                <table class="widefat">
                                    <tbody>
                                        <tr>
                                            <?php
                                                $weekdays = [
                                                    __( 'Sunday', 'dokan' ),
                                                    __( 'Monday', 'dokan' ),
                                                    __( 'Tuesday', 'dokan' ),
                                                    __( 'Wednesday', 'dokan' ),
                                                    __( 'Thursday', 'dokan' ),
                                                    __( 'Friday', 'dokan' ),
                                                    __( 'Saturday', 'dokan' ),
                                                ];

                                                for ( $i = 0; $i < 7; $i++ ) {
                                                    ?>
                                                        <td>
                                                            <input type="checkbox" class="checkbox" name="_wc_booking_restricted_days[<?php echo $i; ?>]" id="_wc_booking_restricted_days[<?php echo $i; ?>]" value="<?php echo $i; ?>" <?php checked( $restricted_days[ $i ], $i ); ?>>
                                                            <label class="checkbox" for="_wc_booking_restricted_days[<?php echo $i; ?>]"><?php echo $weekdays[ $i ]; ?>&nbsp;</label>
                                                        </td>
                                                    <?php
                                                }
                                            ?>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="helper_text" style="display:none">
                                  <p><i>
                                  <?php _e('<strong>Bookable days</strong> Set the which days can be booked. If a customer can book any day of the week except Saturday, check all boxes except Saturday. If the customer can only start you activity on a Tuesday (even if it goes for multiple days), then only check Tuesay.', 'dokan-wc-booking' ); ?>
                                   </i></p>
                                   </div>
                            </div>

                            <div class="table_grid dokan-booking-range-table" style="display:inline-block;">
                              <div class="dokan-form-group">
                                  <label style="display:none;" for="_wc_booking_range_availability" class="form-label"><?php _e( 'Set Availability Range :', 'dokan' ); ?></label>
                              </div>
                              <table class="widefat">
                                    <thead>
                                        <tr>
                                            <th class="sort" width="1%">&nbsp;</th>
                                            <th style="display:none;"><?php _e( 'Range type', 'dokan' ); ?></th>
                                            <th style="display:none;"><?php _e( 'Range', 'dokan' ); ?></th>
                                            <th></th>
                                            <th></th>
                                            <th style="display:none;">
                                                <?php _e( 'Bookable', 'dokan' ); ?>
                                                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'If not bookable, users won\'t be able to choose this block for their booking.', 'dokan' ); ?>">
                                                    <i class="fa fa-question-circle"></i>
                                                </span>
                                            </th>
                                            <th style="display:none;">

                                                <?php _e( 'Priority', 'dokan' ); ?>
                                                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'The lower the priority number, the earlier this rule gets applied. By default, global rules take priority over product rules which take priority over resource rules. By using priority numbers you can execute rules in different orders.', 'dokan' ); ?>">
                                                    <i class="fa fa-question-circle"></i>
                                                </span>
                                            </th>
                                            <th class="remove" width="1%">&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th class="advanced_text" colspan="8">
                                                <a href="#" id="add_span" class="button button-primary add_row dokan-btn dokan-btn-theme" data-row="<?php
                                                ob_start();
                                                include( DOKAN_WC_BOOKING_TEMPLATE_PATH.'booking/html-booking-availability-fields.php' );
                                                $html = ob_get_clean();
                                                echo esc_attr( $html );
                                                ?>"><?php _e( 'Add dates', 'dokan' ); ?></a>
                                                <span class="description"><?php _e( 'Dates further down this list will override dates higher up.', 'dokan' ); ?></span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                    <tbody id="availability_rows">
                                        <?php
                                        $values = get_post_meta( $post_id, '_wc_booking_availability', true );
                                        if ( empty( $values ) ) {
                                        include(  DOKAN_WC_BOOKING_TEMPLATE_PATH.'booking/html-booking-availability-fields.php' );
                                        }
                                        if ( !empty( $values ) && is_array( $values ) ) {
                                            foreach ( $values as $availability ) {
                                                include(  DOKAN_WC_BOOKING_TEMPLATE_PATH.'booking/html-booking-availability-fields.php' );
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                 <div class="helper_text" style="display:none">
                                   <p><i>
                                     <?php _e('<strong>Always presume that the booking system thinks that your products are unavailable unless you set a date your product CAN be booked on.</strong>', 'dokan-wc-booking' ); ?>
                                   </i></p>
                                   <p><i>
                                   <?php _e('<strong>Date</strong> Set the start and end dates you wish to make bookable (More options available in advanced mode).', 'dokan-wc-booking' ); ?>
                                    </i></p>
                                    <p><i>
                                    <?php _e('<strong>Time</strong> Set the start and end times for your dates (More options available in advanced mode).', 'dokan-wc-booking' ); ?>
                                     </i></p>
                                     <p><i>
                                     <?php _e('<strong>Bookable</strong> Here you have a choice between Yes and No. This tells the system if the dates you have set CAN or CANNOT be booked.', 'dokan-wc-booking' ); ?>
                                      </i></p>
                                    <div class="advanced_text" style="display:none;">
                                      <p><i>
                                      <?php _e('<strong>Advanced Mode</strong> Dates further down this list will override dates higher up. If for example, you want to have a week-off during your main dates you would set the week-off dates as bookable: "No" and have it below the main dates in the list. Be carefull when adding multiple rows as the bottom ones override all of the rows above.', 'dokan-wc-booking' ); ?>
                                       </i></p>
                                    </div>
                                    </div>
                                   </div>
                                 </div>
                           </div>
                        </div>
                    <!-- TAB 3 -->
                    <div id="tab3" class="tab-pane">
                      <div class="tab-buttons">
                      <div class="btn-group" style="float: left;">
                          <button class="btnprev" id="prevtab" type="button"><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<?php _e( 'Previous Tab', 'dokan' ); ?></button>
                      </div>
                      <div class="btn-group" style="float: right;">
                          <button class="btnnext" id="nexttab" type="button"><?php _e( 'Next Tab', 'dokan' ); ?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></button>
                      </div>
                    </div>
                    <div class="dokan-edit-row dokan-clearfix">
                    <div class="dokan-section-heading" data-togglehandler="dokan_product_shipping_tax">
                      <h2><i class="fa fa-hourglass" aria-hidden="true"></i> <?php _e( 'Other settings', 'dokan' ); ?></h2>
                      <p><?php _e( 'Add other settings for this product', 'dokan' ); ?></p>
                      <div class="dokan-clearfix"></div>
                    </div>
                    <div class="dokan-section-content">
                      <div class="dokan-input-group ireloca-minimum-booking">
                          <label for="_wc_booking_min_date" class="form-label"><?php _e( 'Minimum booking window ( into the future )', 'dokan' ); ?></label>
                          <input type="number" class="dokan-form-control" name="_wc_booking_min_date" id="_wc_booking_min_date" value="<?php echo max( absint( get_post_meta( $post_id, '_wc_booking_min_date', true ) ), 4 ); ?>" step="1" min="1" max="72" style="margin-right: 7px; width: 4em;">
                          <?php _e( 'Hour(s)', 'dokan' ); ?>
                          <select name="_wc_booking_min_date_unit" id="_wc_booking_min_date_unit" class="dokan-form-control short" style="display:none;">
                              <option value="hour" <?php selected( $booking_min_date_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'dokan' ); ?></option>
                          </select>
                          <div class="helper_text" style="display:none;margin-right:15px;">
                            <p><i>
                            <?php _e('Here you set up the minimum amount of time a customer has to book your product in advance. If you can take bookings up to one hour before your activity starts, great! If you need 24 hours’ notice, you should set it up here. The nearer the minimum time is to the start of the activity, the better. Customers often want to book something for the same day. Minimum setting on the website is 1 hour. ', 'dokan-wc-booking' ); ?>
                             </i></p>
                            </div>
                      </div>
                    <label for="_wc_booking_duration_type" class="form-label"><?php _e( 'Booking duration', 'dokan' ); ?></label>
                    <div class="dokan-input-group">
                        <select name="_wc_booking_duration_type" id="_wc_booking_duration_type" class="dokan-form-control" style="width: auto; margin-right: 7px;">
                            <option value="fixed" <?php selected( $duration_type, 'fixed' ); ?>><?php _e( 'Fixed blocks of', 'dokan' ); ?></option>
                            <option value="customer" <?php selected( $duration_type, 'customer' ); ?>><?php _e( 'Customer defined blocks of', 'dokan' ); ?></option>
                        </select>
                        <input type="number" class="dokan-form-control" name="_wc_booking_duration" id="_wc_booking_duration" value="<?php echo $duration; ?>" step="1" min="1" style="margin-right: 7px; width: 4em;">
                        <select name="_wc_booking_duration_unit" id="_wc_booking_duration_unit" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                            <option value="hour" <?php selected( $duration_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'dokan' ); ?></option>
                            <option value="minute" <?php selected( $duration_unit, 'minute' ); ?>><?php _e( 'Minutes(s)', 'dokan' ); ?></option>
                            <option value="month" <?php selected( $duration_unit, 'month' ); ?>><?php _e( 'Month(s)', 'dokan' ); ?></option>
                            <option value="day" <?php selected( $duration_unit, 'day' ); ?>><?php _e( 'Day(s)', 'dokan' ); ?></option>
                        </select>
                    </div>
                <div class="show_if_custom_block" style="display: none">
                    <div class="content-half-part">
                        <div class="dokan-form-group">
                            <label for="_wc_booking_min_duration" class="form-label"><?php _e( 'Minimum duration', 'dokan' ); ?></label>
                            <?php dokan_post_input_box( $post_id, '_wc_booking_min_duration', array( 'min' => '1', 'step' => 1, 'value' => max( absint( get_post_meta( $post_id, '_wc_booking_min_duration', true ) ), 1 ) ), 'number' ); ?>
                        </div>
                    </div>
                    <div class="content-half-part">
                        <div class="dokan-form-group">
                            <label for="_wc_booking_max_duration" class="form-label"><?php _e( 'Maximum duration', 'dokan' ); ?></label>
                            <?php dokan_post_input_box( $post_id, '_wc_booking_max_duration', array( 'min' => '1', 'step' => 1, 'value' => max( absint( get_post_meta( $post_id, '_wc_booking_max_duration', true ) ), 1 ) ), 'number' ); ?>
                        </div>
                    </div>
                </div>
                <div class="dokan-form-group" style="display:none">
                    <label for="_wc_booking_calendar_display_mode" class="form-label"><?php _e( 'Calendar display mode', 'dokan' );?></label>
                    <select name="_wc_booking_calendar_display_mode" id="_wc_booking_calendar_display_mode" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                        <option value="always_visible" <?php selected( $calendar_display_mode, 'always_visible' ); ?>><?php _e( 'Calendar always visible', 'dokan' ); ?></option>
                      </select>
                </div>

                <div class="dokan-form-group dokan-booking-enable-range-picker" style="display:none">
                    <label>
                        <!--<input name="_wc_booking_enable_range_picker" id="_wc_booking_enable_range_picker" value="0" type="hidden" >-->
                        <input name="_wc_booking_enable_range_picker" id="_wc_booking_enable_range_picker" value="1" type="checkbox" <?php checked( $booking_range_picker ); ?> class="dokan-booking-confirmation"> <?php _e( 'Enable Calendar Range Picker?', 'dokan' ); ?>
                        <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Lets the user select a start and end date on the calendar - duration will be calculated automatically.', 'dokan' ) ?>">
                            <i class="fa fa-question-circle"></i>
                        </span>
                    </label>
                </div>

                <div class="dokan-form-group dokan-booking-require-confirmation" style="display:none">
                    <label>
                        <!--<input name="_wc_booking_requires_confirmation" id="_wc_booking_requires_confirmation" value="0" type="hidden" >-->
                        <input name="_wc_booking_requires_confirmation" id="_wc_booking_requires_confirmation" value="1" type="checkbox" <?php checked( $booking_confirmation ); ?> class="dokan-booking-confirmation"> <?php _e( 'Requires Confirmation', 'dokan' ); ?>
                        <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Check this box if the booking requires YOUR approval/confirmation. Payment will not be taken during CHECKOUT.', 'dokan' ) ?>">
                            <i class="fa fa-question-circle"></i>
                        </span>
                    </label>
                </div>

                <div class="dokan-form-group"  style="display:none">

                    <label>

                        <!--<input name="_wc_booking_user_can_cancel" value="0" type="hidden">-->
                        <input name="_wc_booking_user_can_cancel" id="_wc_booking_user_can_cancel" value="1" type="checkbox" <?php checked( $booking_cancellation, true ); ?> class="dokan-booking-confirmation"> <?php _e( 'Can Be Cancelled ?', 'dokan' ); ?>
                        <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Check this box if the booking can be cancelled by the customer after it has been purchased. A refund will not be sent automatically.', 'dokan' ) ?>">
                            <i class="fa fa-question-circle"></i>
                        </span>

                    </label>
                </div>
                <div class="dokan-form-group form-field booking-cancel-limit">
                    <label for="_wc_booking_cancel_limit" class="form-label"><?php _e( 'Booking can be cancelled ', 'dokan' ); ?></label>
                    <input type="number" class="dokan-form-control" name="_wc_booking_cancel_limit" id="_wc_booking_cancel_limit" value="<?php echo max( absint( $cancellation_limit ), 1 ); ?>" step="1" min="1" style="display: inline-block; margin-right: 7px; width: 4em;">
                    <select name="_wc_booking_cancel_limit_unit" id="_wc_booking_cancel_limit_unit" class="dokan-form-control short" style="display: inline; width: auto; margin-right: 7px;">
                        <option value="month" <?php selected( $cancellation_limit_unit, 'month' ); ?>><?php _e( 'Month(s)', 'dokan' ); ?></option>
                        <option value="week" <?php selected( $cancellation_limit_unit, 'week' ); ?>><?php _e( 'Week(s)', 'dokan' ); ?></option>
                        <option value="day" <?php selected( $cancellation_limit_unit, 'day' ); ?>><?php _e( 'Day(s)', 'dokan' ); ?></option>
                        <option value="hour" <?php selected( $cancellation_limit_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'dokan' ); ?></option>
                    </select>
                    <span class="form-label"><?php _e( 'before start date', 'dokan' ); ?></span>
                </div>
                <div class="helper_text" style="display:none"><p><i><?php _e( 'A Block is one unit of the defined amount of time. A Block can have the following units: Month(s), Day(s), Hour(s), Minute(s)<br><br>This is the duration of the actual activity and can be 1 or more minutes/hours/days/months long. The setting of this block is needed to calculate availability and price later on.', 'dokan-wc-booking' ); ?>
                </i></p>
                <p><i><?php _e('<strong>Fixed blocks</strong> are used where the customer <u>cannot</u> choose the length of the product themselves. A customer can therefore only book an activity for a certain preset amount of time. For example, a 3-hour rafting course should be a fixed block of 3 hours.', 'dokan-wc-booking' ); ?>
                </i></p>
                <p><i><?php _e('<strong>Customer defined blocks</strong> are used where the customer <u>must</u> choose the length of the product themselves. A customer can choose how many blocks they want/need (within your minimum/maximum settings). For example, when ski lessons are available to book hourly, the customer can decide on how many 1-hour blocks they would like to book.', 'dokan-wc-booking' ); ?>
                </i></p>
                <p><i><?php _e('<strong>Example</strong> If a guided bike tour can be between 3 and 8 hours long, which the customer can choose, you should set the product up as <u>Customer defined blocks</u> with a <u>minimum duration</u> of ‘3’ and a <u>maximum duration</u> of ‘8’. The customer can then decide how long he wants this activity to last. Prices will be set later and can be calculated per block.', 'dokan-wc-booking' ); ?>
                </i></p>
              </div>
                                <div class="dokan-form-group">
                                  <label for="_wc_booking_qty" class="form-label"><?php _e( 'Max bookings per block', 'dokan' ); ?></label>
                                  <?php dokan_post_input_box( $post_id, '_wc_booking_qty', array( 'min' => '1', 'step' => 1, 'value' => $wc_booking_qty ), 'number' ); ?>
                                </div>
                                <div class="dokan-form-group" style="display:none;">
                                    <label>
                                        <input name="_wc_booking_has_resources" id="_wc_booking_has_resources" type="checkbox" <?php checked( $has_resource, true ); ?> class="dokan-booking-resource"> <?php _e( 'Has resources', 'dokan' ); ?>
                                    </label>
                                </div>
                       <div class="helper_text" style="display:none">
                      <p><i>
                        <?php _e('<strong>Max bookings per block:</strong> This option refers back to the ‘Booking duration’ set before. Here you type in the maximum number of bookings (or persons) that can be made at the same time per block.', 'dokan-wc-booking' ); ?>
                        </i></p>
                        <div class="advanced_text" style="display:none;">
                          <p><i>
                            <?php _e('<strong>Advanced Mode:</strong> When using advanced mode, this is not always the same as the number of customers you can have. If you offer a product, where different customers can join the same group, then this is the total number of customers possible per block. If you want each customer booking to be a separate group, then this is the amount of groups you can have at the same time. A customer booking can have multiple people.', 'dokan-wc-booking' ); ?>
                            </i></p>
                        <p><i>
                        <?php _e('<strong>Example 1:</strong> You provide rafting courses. If a tour has 8 places for customers available and these can come from any amount of booking, then the maximum bookings per block should be set to 8. If you have 2 boats and they can go out at the same time, then you offer a total of 16 places. Here the ‘Max bookings per block’ should be 16. In this example, the customer count sets the ‘Max bookings per block’. If the customer count sets the ‘Max bookings per block’, then you also need to check ‘Has persons’ and then ‘Count persons as bookings’.', 'dokan-wc-booking'); ?>
                       </i></p>
                       <p><i>
                       <?php _e('<strong>Example 2:</strong> You offer private closed ski school groups. You want each customer booking, to be a seperate private closed ski school group. This way, only people who know each other will be in the same group. If you can have 1 group at a time (per block), then set ‘Max bookings per block to 1. If you can have 4 private groups per block, then set it to 4. In this example the ‘Max bookings per block’ is set by the group count and results in closed groups, filled by 1 customer booking each. ', 'dokan-wc-booking'); ?>
                      </i></p>
                      </div>
                       </div>
                        </div>
                    </div>
                    <div class='dokan-clearfix'></div>
                    <div class="dokan-form-group advanced_text" style="display:none;">
                        <label>
                            <input name="_wc_booking_has_persons" id="_wc_booking_has_persons" type="checkbox" <?php checked( $has_persons, true ); ?> class="dokan-booking-person"> <?php _e( 'Has persons', 'dokan' ); ?>
                        </label>
                    </div>

                  <?php
                    if ( !empty( $post_id ) ) {
                      ?>
                      <div class="ireloca-persons advanced_text" style="display:none;"><?php
                       dokan_get_template_part( 'booking/persons/html-booking-persons', '', $template_args );

                       $resource_args = array(
                            'is_booking'                  => true,
                            'post_id'                     => $post_id,
                            'booking_resource_label'      => $booking_resource_label,
                            'booking_resource_assignment' => $booking_resource_assignment,
                        );
                        dokan_get_template_part( 'booking/resources/html-booking-resources', '', $resource_args );
                        ?></div><?php
                    }
                    ?>
                  </div>

              <?php } ?>
              <!-- TAB 4 -->
              <?php if ( !empty( $post_id ) ) { ?>
              <div id="tab4" class="tab-pane">
                <div class="tab-buttons">
                <div class="btn-group" style="float: left;">
                    <button class="btnprev" id="prevtab" type="button"><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<?php _e( 'Previous Tab', 'dokan' ); ?></button>
                </div>
                <div class="btn-group" style="float: right;">
                    <button class="btnnext" id="nexttab" type="button"><?php _e( 'Next Tab', 'dokan' ); ?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></button>
                </div>
              </div>
              <?php
              $user_id                 = dokan_get_current_user_id();
              $processing_time         = dokan_get_shipping_processing_times();
              $_required_tax           = get_post_meta( $post_id, '_required_tax', true );
              $_disable_shipping       = ( get_post_meta( $post_id, '_disable_shipping', true ) ) ? get_post_meta( $post_id, '_disable_shipping', true ) : 'no';
              $_additional_price       = get_post_meta( $post_id, '_additional_price', true );
              $_additional_qty         = get_post_meta( $post_id, '_additional_qty', true );
              $_processing_time        = get_post_meta( $post_id, '_dps_processing_time', true );
              $dps_shipping_type_price = get_user_meta( $user_id, '_dps_shipping_type_price', true );
              $dps_additional_qty      = get_user_meta( $user_id, '_dps_additional_qty', true );
              $dps_pt                  = get_user_meta( $user_id, '_dps_pt', true );
              $classes_options         = Dokan_Pro_Products::init()->get_tax_class_option();
              $porduct_shipping_pt     = ( $_processing_time ) ? $_processing_time : $dps_pt;

              dokan_get_template_part( 'booking/html-shipping-tax', '', array(
                  'is_booking'              => true,
                  'post_id'                 => $post_id,
                  'user_id'                 => $user_id,
                  'processing_time'         => $processing_time,
                  '_required_tax'           => $_required_tax,
                  '_disable_shipping'       => $_disable_shipping,
                  '_additional_price'       => $_additional_price,
                  '_additional_qty'         => $_additional_qty,
                  '_processing_time'        => $_processing_time,
                  'dps_shipping_type_price' => $dps_shipping_type_price,
                  'dps_additional_qty'      => $dps_additional_qty,
                  'dps_pt'                  => $dps_pt,
                  'classes_options'         => $classes_options,
                  'porduct_shipping_pt'     => $porduct_shipping_pt,
              ) );
          ?>
          <input type="hidden" name="dokan-edit-product-id" id="dokan-edit-product-id" value="<?php echo $post_id; ?>">
          <div class="helper_text" style="display:none;width:100%;">
            <p><i>
            <?php _e('Here you set the local tax rate (VAT) for your product. This tax rate will be paid by the customer and is visible on your invoice. You are responsible for selecting the correct tax rate and declaring it to the relevant authorities. ireloca ltd is not responsible for your taxes.', 'dokan-wc-booking' ); ?>
             </i></p>
            </div>
              <?php dokan_get_template_part( 'booking/html-dokan-booking-pricing', '', $template_args );?>
              </div>
              <?php } ?>
              <!-- TAB 5 -->
              <div id="tab5" class="tab-pane">
                <div class="tab-buttons">
                <div class="btn-group" style="float: left;">
                    <button class="btnprev" id="prevtab" type="button"><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<?php _e( 'Previous Tab', 'dokan' ); ?></button>
                </div>
                <div class="btn-group" style="float: right;">
                    <button class="btnnext" id="nexttab" type="button"><?php _e( 'Next Tab', 'dokan' ); ?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></button>
                </div>
              </div>
                <div class="dokan-edit-row">
                <div class="dokan-section-heading" data-togglehandler="dokan_product_shipping_tax">
                  <h2><i class="fa fa-map-marker" aria-hidden="true"></i> <?php _e( 'Location', 'dokan' ); ?></h2>
                  <p><?php _e( 'Manage the location settings for this product', 'dokan' ); ?></p>
                  <div class="dokan-clearfix"></div>
                </div>
                <div class="dokan-section-content">
                <div class="dokan-product-description">
                    <label for="post_content" class="form-label"><?php _e( 'Meeting Place', 'dokan' ); ?></label>
                    <?php dokan_post_input_box( $post_content, 'post_content', array( 'value' => $post_content, 'placeholder' => __( 'Enter the meeting place so that your customers can find the starting point for your activity.', 'dokan' ) ), 'textarea' ); ?>
                </div>
                <div class="helper_text" style="display:none">
                <p><i>
                <?php _e('Here you describe in <strong>words</strong> what the meeting place (not your office location, unless these are the same) will be, when the actual booking takes place. ', 'dokan-wc-booking' ); ?>
                 </i></p>
                 <p><i>
                 <?php _e('This line of text is visible in the tabs on the product page, but also on the booking confirmation sent to the customer. It is very important to make the meeting place clear, otherwise the customers might have trouble finding the starting point on the day of the booking. ', 'dokan-wc-booking' ); ?>
                  </i></p>
                  <p><i>
                  <?php _e('<strong>Example:</strong> Söll Ski area, middle station, directly after exiting the first gondola, turn right and move to the red flag clearly visible by now. Or, in front of the tourism office in Ellmau. ', 'dokan-wc-booking' ); ?>
                   </i></p>
                </div>
                <?php do_action( 'dokan_new_product_form', $post, $post_id ); ?>
                <div class="helper_text" style="display:none;width:100%;">
                  <p><i>
                  <?php _e('Here you select the <strong>exact</strong> position of the meeting place for your activity. This location should NOT always be the same as your office! ', 'dokan-wc-booking' ); ?>
                   </i></p>
                   <p><i>
                   <?php _e('This location is needed when the customer searches for activities nearby or for a specific place.', 'dokan-wc-booking' ); ?>
                  </i></p>
                  <p><i>
                  <?php _e('You have 5 ways of entering the correct location:', 'dokan-wc-booking' ); ?>
                   </i>
                   <ul>
                     <li style="display:list-item;list-style: decimal;"><?php _e('You can enter the address of the starting point in the entry field. Google willautomatically complete the address once you start typing.', 'dokan-wc-booking' ); ?></li>
                     <li style="display:list-item;list-style: decimal;"><?php _e('You click on the grey cross on the right to use your current location. Be careful, this might be a little off to the actual starting point', 'dokan-wc-booking' ); ?></li>
                    <li style="display:list-item;list-style: decimal;"><?php _e('You click on ‘address’ on the left menu and manually type in the address (afterwards please check on the map if correct). ', 'dokan-wc-booking' ); ?></li>
                    <li style="display:list-item;list-style: decimal;"><?php _e('You click on ‘coordinates’ on the left menu and type in the exact coordinates of the starting point. ', 'dokan-wc-booking' ); ?></li>
                    <li style="display:list-item;list-style: decimal;"><?php _e('You drag and drop the red pin until it lies directly on your starting point. You will see that the address will also change in the entry bar above. ', 'dokan-wc-booking' ); ?></li>
                   </ul></p>
                   <p><i>
                   <?php _e('If your location has been added correctly, you’ll see the words ‘location confirmed’ in a green box.', 'dokan-wc-booking' ); ?>
                  </i></p>
                  <p><i>
                  <?php _e('<strong>If you have not selected a Location, your Product will not be shown on the site!</strong>', 'dokan-wc-booking' ); ?>
                 </i></p>
                  </div>
              </div>
              </div>
              </div>

              <!-- TAB 6 -->
              <?php
                  if ( $post_id ) { ?>
                    <div id="tab6" class="tab-pane">
                      <div class="tab-buttons">
                      <div class="btn-group" style="float: left;">
                          <button class="btnprev" id="prevtab" type="button"><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<?php _e( 'Previous Tab', 'dokan' ); ?></button>
                      </div>
                      <div class="btn-group" style="float: right;">
                          <button class="btnnext" id="nexttab" type="button"><?php _e( 'Next Tab', 'dokan' ); ?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></button>
                      </div>
                    </div>
                      <?php
                      $product_attributes   = get_post_meta( $post_id, '_product_attributes', true );
                      $attribute_taxonomies = wc_get_attribute_taxonomies();
                      dokan_get_template_part( 'booking/html-attributes', '', array(
                          'is_booking'           => true,
                          'post_id'              => $post_id,
                          'product_attributes'   => $product_attributes,
                          'attribute_taxonomies' => $attribute_taxonomies,
                      ) ); ?>
                      <div class="helper_text" style="display:none"><p><i><?php _e('A ‘search attribute’ is what we call the values set to help customers filter their search results. These include: Family Friendly, Summer/Winter, Indoor/Outdoor and Time of Day. This way, customers can find an activity based on their desired needs. Deselect non relevant tags.', 'dokan-wc-booking' ); ?>
                      </i></p>
                    </div>
                    <div class="dokan-form-group" style="display:none">
                        <label for="product_tag" class="form-label"><?php _e( 'Tags', 'dokan' ); ?></label>
                        <?php
                        require_once DOKAN_LIB_DIR . '/class.taxonomy-walker.php';
                        $term           = wp_get_post_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
                        $selected       = ( $term ) ? $term : array();
                        $drop_down_tags = wp_dropdown_categories( array(
                            'show_option_none' => __( '', 'dokan' ),
                            'hierarchical'     => 1,
                            'hide_empty'       => 0,
                            'name'             => 'product_tag[]',
                            'id'               => 'product_tag',
                            'taxonomy'         => 'product_tag',
                            'title_li'         => '',
                            'class'            => ' dokan-select2 product_tags dokan-form-control chosen',
                            'exclude'          => '',
                            'selected'         => $selected,
                            'echo'             => 0,
                            'walker'           => new DokanTaxonomyWalker( $post_id )
                        ) );

                        echo str_replace( '<select', '<select data-placeholder="' . __( 'Select product tags', 'dokan' ) . '" multiple="multiple" ', $drop_down_tags );
                        ?>
                        <div class="helper_text" style="display:none"><p><i><?php _e( 'A tag is a keyword or term that describes your product in just a few words. This data is needed to be found more easily on our website, as well as on google. These tags will also be displayed on the product page. Ideally, you should have between 3 to 5 tags per product. To add a Tag, simply start writing and select the Tag, alternatively you can open the drop-down menu by clicking the Tag field. <br>If your tag is not availlable yet, then you can simply add it by typing the tag you want and press "enter".', 'dokan-wc-booking' ); ?></i></p></div>
                    </div>
                  </div>
                <?php } ?>

                <!-- TAB 7 -->
                <?php if ( !empty( $post_id ) ): ?>
                  <div id="tab7" class="tab-pane">
                    <div class="tab-buttons">
                    <div class="btn-group" style="float: left;">
                        <button class="btnprev" id="prevtab" type="button"><i class="fa fa-angle-double-left"></i>&nbsp;&nbsp;<?php _e( 'Previous Tab', 'dokan' ); ?></button>
                    </div>
                    <div class="btn-group" style="float: right;">
                        <button class="disabled-tab btnnext" id="nexttab" type="button"><?php _e( 'Next Tab', 'dokan' ); ?>&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i></button>
                    </div>
                  </div>
                    <div id="status" class="bookings_availability availability_fields dokan-edit-row dokan-clearfix">
                      <div class="dokan-section-heading">
                        <h2><i class="fa fa-list" aria-hidden="true"></i> <?php _e('Product Status', 'dokan' );?></h2>
                        <p><?php _e( 'Here you can toggle if you want your product to be online or offline', 'dokan' ); ?></p>
                        <div class='dokan-clearfix'></div>
                      </div>
                      <div class="dokan-section-content content-half-part">
                        <?php if ( $post_status != 'pending' ) { ?>
                            <?php
                            $post_statuses = apply_filters( 'dokan_post_status', array(
                                'publish' => __( 'Online', 'dokan' ),
                                'draft'   => __( 'Offline', 'dokan' )
                            ), $post );
                            ?>

                            <select id="post_status" class="dokan-form-control" name="post_status">
                                <?php foreach ( $post_statuses as $status => $label ) { ?>
                                    <option value="<?php echo $status; ?>"<?php selected( $post_status, $status ); ?>><?php echo $label; ?></option>
                            <?php } ?>
                            </select>
                        <?php } else { ?>
                            <?php $pending_class = $post_status == 'pending' ? '  dokan-label dokan-label-warning' : ''; ?>
                            <span class="dokan-toggle-selected-display<?php echo $pending_class; ?>"><?php echo dokan_get_post_status( $post_status ); ?></span>
                        <?php } ?>
                      </div>
                      <div class='dokan-clearfix'></div>
                      <div class="dokan-section-content">
                    <!--Checklist-->
                          <?php $no_title = __( 'Add a title', 'dokan' ); ?>
                          <?php $title = __( 'A title has been added', 'dokan' ); ?>
                          <?php $no_image = __( 'Add an image', 'dokan' ); ?>
                          <?php $image = __( 'An image has been added', 'dokan' ); ?>
                          <?php $no_attr = __('Add attributes', 'dokan' ); ?>
                          <?php $attr = __('Attributes have been added', 'dokan' ); ?>
                          <?php $no_avail = __('Add bookable days', 'dokan' ); ?>
                          <?php $avail = __('Bookable days have been added', 'dokan' ); ?>
                          <?php $no_desc = __('Add a description', 'dokan' ); ?>
                          <?php $desc = __('A description has been added', 'dokan' ); ?>
                          <?php $no_meet = __('Add the meeting place', 'dokan' ); ?>
                          <?php $meet = __('The meeting place has been added', 'dokan' ); ?>
                        <div class="dokan-section-heading">
                          <h2><i class="fa fa-list" aria-hidden="true"></i> <?php _e('Checklist', 'dokan' );?></h2>
                          <p><?php _e( 'Minimum checklist for an active product. Click save, to see your changes.', 'dokan' ); ?></p>
                          <div class='dokan-clearfix'></div>
                        </div>
                        <div class="checklist-ireloca" style="border:none!important;">
                              <ul>
                                <!--New Product-->
                            <?php if ( empty( $post_title ) ){ ?><li style="color:red"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_title; ?>
                              <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Fix this by going to the tab: Main', 'dokan' ) ?>">
                                  <i class="fa fa-question-circle"></i>
                              </span></li>
                              <?php } ?>
                              <?php if ( !empty( $post_title ) ){ ?><li style="color:#8fae1b"><i class="fa fa-check"></i><?php echo '&nbsp'; echo $title; ?></li><?php } ?>
                            <?php if ( empty( has_post_thumbnail( $post_id ) ) ){ ?><li style="color:red"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_image;?>
                            <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Fix this by going to the tab: Main', 'dokan' ) ?>">
                                <i class="fa fa-question-circle"></i>
                            </span></li>
                            <?php } ?>
                            <?php if ( !empty( has_post_thumbnail( $post_id ) ) ){ ?><li style="color:#8fae1b"><i class="fa fa-check"></i><?php echo '&nbsp'; echo $image; ?></li><?php } ?>
                            <?php if ( empty( $post_excerpt ) ){ ?><li style="color:red"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_desc; ?>
                              <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Fix this by going to the tab: Main', 'dokan' ) ?>">
                                  <i class="fa fa-question-circle"></i>
                              </span></li><?php } ?>
                            <?php if ( !empty( $post_excerpt ) ){ ?><li style="color:#8fae1b"><i class="fa fa-check"></i><?php echo '&nbsp'; echo $desc; ?></li><?php } ?>
                              <!--All Products-->
                            <?php if ( !empty( $post_id )) { ?>
                            <?php if ( empty( get_post_meta( $post_id, '_product_attributes', true ) ) ){ ?><li style="color:red"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_attr; ?>
                              <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Fix this by going to the tab: Search Attributes', 'dokan' ) ?>">
                                  <i class="fa fa-question-circle"></i>
                              </span></li><?php } ?>
                            <?php if ( !empty( get_post_meta( $post_id, '_product_attributes', true ) ) ){ ?><li style="color:#8fae1b"><i class="fa fa-check"></i><?php echo '&nbsp'; echo $attr; ?></li><?php } ?>
                            <?php if ( empty( get_post_meta( $post_id, '_wc_booking_restricted_days', true ) ) ){ ?><li style="color:red"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_avail; ?>
                              <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Fix this by going to the tab: Availability', 'dokan' ) ?>">
                                  <i class="fa fa-question-circle"></i>
                              </span></li><?php } ?>
                            <?php if ( !empty( get_post_meta( $post_id, '_wc_booking_restricted_days', true ) ) ){ ?><li style="color:#8fae1b"><i class="fa fa-check"></i><?php echo '&nbsp'; echo $avail; ?></li><?php } ?>
                            <?php if ( empty( $post_content ) ){ ?><li style="color:red"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_meet; ?>
                              <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Fix this by going to the tab: Location', 'dokan' ) ?>">
                                  <i class="fa fa-question-circle"></i>
                              </span></li><?php } ?>
                            <?php if ( !empty( $post_content ) ){ ?><li style="color:#8fae1b"><i class="fa fa-check"></i><?php echo '&nbsp'; echo $meet; ?></li><?php } ?>
                            <?php } else {?>
                              <!--Grey items on new product-->
                              <?php if ( empty( get_post_meta( $post_id, '_product_attributes', true ) ) ){ ?><li style="color:#ccc"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_attr; ?>
                                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'You must save the product first, before adding this', 'dokan' ) ?>">
                                    <i class="fa fa-question-circle"></i>
                                </span></li><?php } ?>
                              <?php if ( empty( get_post_meta( $post_id, '_wc_booking_restricted_days', true ) ) ){ ?><li style="color:#ccc"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_avail; ?>
                                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'You must save the product first, before adding this', 'dokan' ) ?>">
                                    <i class="fa fa-question-circle"></i>
                                </span></li><?php } ?>
                              <?php if ( empty( $post_content ) ){ ?><li style="color:#ccc"><i class="fa fa-times"></i><?php echo '&nbsp'; echo $no_meet; ?>
                                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'You must save the product first, before adding this', 'dokan' ) ?>">
                                    <i class="fa fa-question-circle"></i>
                                </span></li><?php } ?>
                              <?php } ?>
                          </ul>
                        </div>
                    </div>

              </div>
            </div>
            </div>
              <?php if ( !empty( $post_id ) ): ?>

              <?php endif;?>
                <?php

                    dokan_get_template_part( 'booking/persons/html-other-options', '', $template_args  );

                    do_action( 'dokan_product_edit_after_options' );

                    wp_nonce_field( 'dokan_edit_product', 'dokan_edit_product_nonce' );
                ?>

                    <input type="hidden" name="dokan_update_product" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
                    <div class="btn-group">
                          <input type="submit" onclick="return showslider();" name="dokan_update_product" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
                    </div>


                <?php else: ?>

                    <?php wp_nonce_field( 'dokan_add_new_product', 'dokan_add_new_product_nonce' ); ?>
                    <input type="hidden" name="add_product" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
                    <div class="btn-group">
                        <input type="submit" onclick="return showslider();" name="add_product" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
                    </div>

                <?php endif; ?>

                <!--hidden input for Firefox issue-->
                <input type="hidden" name="_stock_status" value="instock"/>
                <input type="hidden" name="_sku" value=""/>
                <input type="hidden" name="price" value=""/>
                <input type="hidden" name="product_type" value="booking"/>

                </fieldset>
            </form>
          <?php }
             else  {?>
              <?php
              echo '<style>.menu-item-has-children{display:none!important}</style>';
              function langcode_post_id($original_id){
                    global $wpdb;
                    $query = $wpdb->prepare('SELECT language_code FROM ' . $wpdb->prefix . 'icl_translations WHERE element_id="%d"', $original_id);
                    $query_exec = $wpdb->get_row($query);
                    return $query_exec->language_code;
                      }
              $dokan_original_language = langcode_post_id( $original_id );
              function langname_post_id($dokan_original_language){
                    global $wpdb;
                    $current_lang = apply_filters( 'wpml_current_language', NULL );
                    $query = $wpdb->prepare('SELECT name FROM ' . $wpdb->prefix . 'icl_languages_translations WHERE language_code="%s" AND display_language_code="' . $current_lang . '"', $dokan_original_language);
                    $query_exec = $wpdb->get_row($query);
                    return $query_exec->name;
                      }
                    $ireloca_lang = langname_post_id( $dokan_original_language );
                    $ireloca_dokan_redirect = ('/'.$dokan_original_language.'/dashboard/booking/edit/?product_id='.$original_id);
                    $ireloca_dokan_redirect_de = ('/dashboard/booking/edit/?product_id='.$original_id);
                      if ($dokan_original_language === 'de') {
                        if(function_exists('icl_get_languages')): ?>
                        <div class="dokan-error">
                        <?php _e( 'You are attempting to edit this product in the wrong language.', 'dokan' ); ?>
                        </div>
                         <div class="wrong-language" style="padding: 40px 20px 0;float: left;">
                         <h2><?php _e( 'You need to change language', 'dokan' ); ?></h2><br>
                         <?php _e( 'To avoid confusion, you can only edit a product in the language it was originally created in.', 'dokan' ); ?><br>
                         <?php _e( 'You are currently in:', 'dokan' ); ?>&nbsp;<strong> <?php echo esc_attr(ICL_LANGUAGE_NAME);?></strong><?php echo ', '; ?>
                         <?php _e( 'however this product was created in:', 'dokan' ); ?> &nbsp;<strong><?php echo $ireloca_lang;?></strong><br><br>
                         <br><br><a class="dokan-btn dokan-btn-theme" href="<?php echo $ireloca_dokan_redirect_de ?>"><?php printf( __('Click here to edit this product in: %s', 'dokan' ), $ireloca_lang ); ?></a>
                       </div>
                       <div class="wrong-language-image content-half-part">
                       <svg xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                       width="300" height="300"
                       viewBox="0 0 192 192"
                       style=" fill:#000000;"><g fill="none" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal"><path d="M0,192v-192h192v192z" fill="none"></path><g><g id="surface1"><path d="M156,24h-120c-6.625,0 -12,5.375 -12,12v132l34.21875,-32h97.78125c6.625,0 12,-5.375 12,-12v-88c0,-6.625 -5.375,-12 -12,-12z" fill="#2d9cf7"></path><path d="M80,24h-44c-6.625,0 -12,5.375 -12,12v132l34.21875,-32h49.78125z" fill="#0d4569"></path><path d="M112.25,96.89062c0.25,-0.125 19.98438,-12.875 31.75,-36.45312l7.15625,3.5625c-12.32813,25 -29.82813,37.17188 -34.03125,40z" fill="#ffffff"></path><path d="M148,104c0,0 -20,-10.32812 -31.07812,-27.98438l7.07812,-4.01562c9.32812,15.32812 27.92188,25.04688 27.92188,25.04688z" fill="#ffffff"></path><path d="M108,60h48v8h-48z" fill="#ffffff"></path><path d="M128,52h8v16h-8z" fill="#ffffff"></path><path d="M48,88h24v8h-24z" fill="#ffffff"></path><path d="M48.4375,104l11.5625,-36l11.625,36h8.4375l-16.0625,-48h-8l-16,48z" fill="#ffffff"></path></g></g></g></svg>
                        </div>
                         <?php endif;
                    }
                      else {
                        if(function_exists('icl_get_languages')): ?>
                           <div class="wrong-langauge">
                           <?php _e( 'To avoid confusion, you can only edit a product in the language it was originally created in.', 'dokan' ); ?><br>
                           <?php _e( 'You are seeing this message because you are attempting to edit in the wrong language.', 'dokan' ); ?><br><br>
                           <?php _e( 'You are currently in:', 'dokan' ); ?>&nbsp;<strong> <?php echo esc_attr(ICL_LANGUAGE_NAME);?></strong><?php echo ', '; ?>
                           <?php _e( 'however this product was created in:', 'dokan' ); ?> &nbsp;<strong><?php echo $ireloca_lang;?></strong>
                            </div>
                         <?php endif;
                      // header("Location: $ireloca_dokan_redirect", true, 301);
                      } ?>
              <?php }
               } else { ?>
                    <div class="dokan-alert dokan-alert">
                        <?php dokan_seller_not_enabled_notice() ?>
                    </div>
                <?php } ?>

    <?php } else { ?>

        <?php do_action( 'dokan_can_post_notice' ); ?>

        <?php
    }

    wp_reset_postdata();
    ?>
</div><?php endif ?> <!-- #primary .content-area -->
<script type="text/javascript">
        ( function ( $ ) {

            $( document ).ready( function () {
                var duration_type = $( 'select#_wc_booking_duration_type' );
                duration_type.on( 'change', function () {
                    if ( duration_type.val() == 'customer' ) {
                        $( '.show_if_custom_block' ).show();
                    } else {
                        $( '.show_if_custom_block' ).hide();
                    }
                } );

                var duration_unit = $( 'select#_wc_booking_duration_unit' );
                var duration_label = $( 'span#_booking_binded_label' );
                duration_unit.on( 'change', function () {
                    duration_label.html( duration_unit.val() + 's' );
                } );

                var restrict_field = $( '#dokan_booking_has_restricted_days_field' );

                restrict_field.on( 'change', function() {
                    var self = $(this);
                    var restricted_days = $( '.dokan-booking-day-restriction' );

                    if ( self.is( ':checked' ) ) {
                        restricted_days.show();
                    } else {
                        restricted_days.hide();
                    }
                } ).trigger( 'change' );

            } );

        } )( jQuery );
</script>
<script>
$('.btnnext').click(function() {
    $('.nav-tabs .active').parent().next('li').find('a').trigger('click');
  });
$('.btnprev').click(function() {
    $('.nav-tabs .active').parent().prev('li').find('a').trigger('click');
  });
</script>
<script>
jQuery(function(){
  var checkBox = document.getElementById("helper_toggle");
         jQuery('#helper_toggle').click(function(){
           if (checkBox.checked == true){
               jQuery('.helper_text').show()}
               else{
              jQuery('.helper_text').hide()
               };
        });
});
jQuery(function(){
  var checkBox = document.getElementById("advanced_toggle");
         jQuery('#advanced_toggle').click(function(){
           if (checkBox.checked == true){
               jQuery('.advanced_text').show()}
               else{
              jQuery('.advanced_text').hide()
               };
        });
});
jQuery(function(){
  var checkBox = document.getElementById("advanced_toggle");
  var addSpan = document.getElementById("add_span");
         jQuery('#add_span').click(function(){
           if (checkBox.checked == true){
               jQuery('.advanced_text').show()}
               else{
              jQuery('.advanced_text').hide()
               };
        });
});
var advancedcheckBox = document.getElementById("advanced_toggle");
if (advancedcheckBox.checked == true){
    jQuery('.advanced_text').show()}
    else{
   jQuery('.advanced_text').hide()
    };

function showslider(){
	document.getElementById("ireloca_spinner").style.display = "block";
  document.getElementById("ireloca_spinner_message").style.display = "block";
  return true;
}
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      localStorage.setItem('activeTab', $(e.target).attr('href'));
  });

  var activeTab = localStorage.getItem('activeTab');
  if(activeTab){
      $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
  }
 });
</script>
<script>
window.onload = function(){
// Add attribute
var modal = document.getElementById('myModal_add_attribute');
var img = document.getElementById('img_add_attribute');
var modalImg = document.getElementById("img_add_attribute_modal");
img.onclick = function(){
  modal.style.display = "block";
  modalImg.src = this.src;
}
var span = document.getElementsByClassName("img_close_add_attribute")[0];
span.onclick = function() {
  modal.style.display = "none";
}
// Attribute Value
var modal2 = document.getElementById('myModal_attribute_value');
var img2 = document.getElementById('img_attribute_value');
var modalImg2 = document.getElementById("img_attribute_value_modal");
img2.onclick = function(){
  modal2.style.display = "block";
  modalImg2.src = this.src;
}
var span2 = document.getElementsByClassName("img_close_attribute_value")[0];
span2.onclick = function() {
  modal2.style.display = "none";
}
// Availlability Example 1
var modal3 = document.getElementById('myModal_example_1');
var img3 = document.getElementById('img_example_1');
var modalImg3 = document.getElementById("img_example_1_modal");
img3.onclick = function(){
  modal3.style.display = "block";
  modalImg3.src = this.src;
}
var span3 = document.getElementsByClassName("img_close_example_1")[0];
span3.onclick = function() {
  modal3.style.display = "none";
}
// Cost Example 1
var modal4 = document.getElementById('myModal_example_cost_1');
var img4 = document.getElementById('img_example_cost_1');
var modalImg4 = document.getElementById("img_example_cost_1_modal");
img4.onclick = function(){
  modal4.style.display = "block";
  modalImg4.src = this.src;
}
var span4 = document.getElementsByClassName("img_close_example_cost_1")[0];
span4.onclick = function() {
  modal4.style.display = "none";
}
// Cost Example 2
var modal5 = document.getElementById('myModal_example_cost_2');
var img5 = document.getElementById('img_example_cost_2');
var modalImg5 = document.getElementById("img_example_cost_2_modal");
img5.onclick = function(){
  modal5.style.display = "block";
  modalImg5.src = this.src;
}
var span5 = document.getElementsByClassName("img_close_example_cost_2")[0];
span5.onclick = function() {
  modal5.style.display = "none";
}
// Person Type Example
var modal6 = document.getElementById('myModal_pers_type');
var img6 = document.getElementById('img_pers_type');
var modalImg6 = document.getElementById("img_pers_type_modal");
img6.onclick = function(){
  modal6.style.display = "block";
  modalImg6.src = this.src;
}
var span6 = document.getElementsByClassName("img_close_pers_type")[0];
span6.onclick = function() {
  modal6.style.display = "none";
}
// Resource Example 1
var modal7 = document.getElementById('myModal_resource_example_1');
var img7 = document.getElementById('img_resource_example_1');
var modalImg7 = document.getElementById("img_resource_example_1_modal");
img7.onclick = function(){
  modal7.style.display = "block";
  modalImg7.src = this.src;
}
var span7 = document.getElementsByClassName("img_close_resource_example_1")[0];
span7.onclick = function() {
  modal7.style.display = "none";
}
// Resource Example 2
var modal8 = document.getElementById('myModal_resource_example_2');
var img8 = document.getElementById('img_resource_example_2');
var modalImg8 = document.getElementById("img_resource_example_2_modal");
img8.onclick = function(){
  modal8.style.display = "block";
  modalImg8.src = this.src;
}
var span8 = document.getElementsByClassName("img_close_resource_example_2")[0];
span8.onclick = function() {
  modal8.style.display = "none";
}
};
</script>
