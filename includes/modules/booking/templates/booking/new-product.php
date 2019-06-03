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
?>

<header class="dokan-dashboard-header dokan-clearfix">
    <h1 class="entry-title">
        <?php if ( !$post_id ): ?>
            <?php _e( 'Add Bookable product', 'dokan' ); ?>
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

            <form class="dokan-product-edit-form" role="form" method="post">

                <?php
                if ( $post_id ):
                    do_action( 'dokan_product_data_panel_tabs' );
                endif;

                do_action( 'dokan_product_edit_before_main' );
                ?>

                <div class="dokan-form-top-area">

                    <div class="content-half-part">

                        <div class="dokan-form-group">
                            <input type="hidden" name="dokan_product_id" value="<?php echo $post_id; ?>">

                            <label for="post_title" class="form-label"><?php _e( 'Title', 'dokan' ); ?></label>
                            <div class="dokan-product-title-alert dokan-hide dokan-alert dokan-alert-danger">
                                <?php _e( 'Please choose a Name !!!', 'dokan' ); ?>
                            </div>
                            <?php dokan_post_input_box( $post_id, 'post_title', array( 'placeholder' => __( 'Product name..', 'dokan' ), 'value' => $post_title ) ); ?>
                        </div>
                        <div class="dokan-form-group virtual-checkbox">
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
                        </div>

                        <div class="dokan-form-group">
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
                        </div>

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
                        </div> <!-- .product-gallery -->
                    </div><!-- .content-half-part -->
                </div><!-- .dokan-form-top-area -->
                <div class="booking_fields">
                    <?php
                    $duration_type                     = get_post_meta( $post_id, '_wc_booking_duration_type', true );
                    $duration                          = max( absint( get_post_meta( $post_id, '_wc_booking_duration', true ) ), 1 );
                    $duration_unit                     = get_post_meta( $post_id, '_wc_booking_duration_unit', true );

                    //availability
                    $wc_booking_qty                    = max( absint( get_post_meta( $post_id, '_wc_booking_qty', true ) ), 1 );
                    $booking_min_date_unit             = get_post_meta( $post_id, '_wc_booking_min_date_unit', true );
                    $booking_max_date_unit             = get_post_meta( $post_id, '_wc_booking_max_date_unit', true );


                    $booking_buffer_period             = absint( get_post_meta( $post_id, '_wc_booking_buffer_period', true ) );
                    $adjacent_buffer_period            = get_post_meta( $post_id, '_wc_booking_apply_adjacent_buffer', true );

                    $booking_default_date_availability = get_post_meta( $post_id, '_wc_booking_default_date_availability', true );
                    $booking_check_availability        = get_post_meta( $post_id, '_wc_booking_check_availability_against', true );

                    $booking_range_picker              = get_post_meta( $post_id, '_wc_booking_enable_range_picker', true );
                    $booking_first_block               = get_post_meta( $post_id, '_wc_booking_first_block_time', true );

                    $booking_confirmation              = get_post_meta( $post_id, '_wc_booking_requires_confirmation', true );
                    $booking_cancellation              = get_post_meta( $post_id, '_wc_booking_user_can_cancel', true );
                    $cancellation_limit                = get_post_meta( $post_id, '_wc_booking_cancel_limit', true );
                    $cancellation_limit_unit           = get_post_meta( $post_id, '_wc_booking_cancel_limit_unit', true );
                    //costs
                    //resources
                    $booking_resource_label            = get_post_meta( $post_id, '_wc_booking_resource_label', true );
                    $booking_resource_assignment       = get_post_meta( $post_id, '_wc_booking_resources_assignment', true );

                    $calendar_display_mode             = get_post_meta( $post_id,'_wc_booking_calendar_display_mode',true );

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

                    ?>

                    <div class="">
                        <label for="_wc_booking_duration_type" class="form-label"><?php _e( 'Booking duration', 'dokan' ); ?></label>
                        <div class="dokan-input-group">
                            <select name="_wc_booking_duration_type" id="_wc_booking_duration_type" class="dokan-form-control" style="width: auto; margin-right: 7px;">
                                <option value="fixed" <?php selected( $duration_type, 'fixed' ); ?>><?php _e( 'Fixed blocks of', 'dokan' ); ?></option>
                                <option value="customer" <?php selected( $duration_type, 'customer' ); ?>><?php _e( 'Customer defined blocks of', 'dokan' ); ?></option>
                            </select>
                            <input type="number" class="dokan-form-control" name="_wc_booking_duration" id="_wc_booking_duration" value="<?php echo $duration; ?>" step="1" min="1" style="margin-right: 7px; width: 4em;">
                            <select name="_wc_booking_duration_unit" id="_wc_booking_duration_unit" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                                <option value="month" <?php selected( $duration_unit, 'month' ); ?>><?php _e( 'Month(s)', 'dokan' ); ?></option>
                                <option value="day" <?php selected( $duration_unit, 'day' ); ?>><?php _e( 'Day(s)', 'dokan' ); ?></option>
                                <option value="hour" <?php selected( $duration_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'dokan' ); ?></option>
                                <option value="minute" <?php selected( $duration_unit, 'minute' ); ?>><?php _e( 'Minutes(s)', 'dokan' ); ?></option>
                            </select>
                        </div>
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
                    <div class="dokan-form-group">
                        <label for="_wc_booking_calendar_display_mode" class="form-label"><?php _e( 'Calendar display mode', 'dokan' );?></label>
                        <select name="_wc_booking_calendar_display_mode" id="_wc_booking_calendar_display_mode" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                            <option value="" <?php selected( $calendar_display_mode, '' ); ?>><?php _e( 'Display calendar on click', 'dokan' ); ?></option>
                            <option value="always_visible" <?php selected( $calendar_display_mode, 'always_visible' ); ?>><?php _e( 'Calendar always visible', 'dokan' ); ?></option>
                        </select>
                    </div>

                    <div class="dokan-form-group dokan-booking-enable-range-picker">
                        <label>
                            <!--<input name="_wc_booking_enable_range_picker" id="_wc_booking_enable_range_picker" value="0" type="hidden" >-->
                            <input name="_wc_booking_enable_range_picker" id="_wc_booking_enable_range_picker" value="1" type="checkbox" <?php checked( $booking_range_picker ); ?> class="dokan-booking-confirmation"> <?php _e( 'Enable Calendar Range Picker?', 'dokan' ); ?>
                            <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Lets the user select a start and end date on the calendar - duration will be calculated automatically.', 'dokan' ) ?>">
                                <i class="fa fa-question-circle"></i>
                            </span>
                        </label>
                    </div>

                    <div class="dokan-form-group dokan-booking-require-confirmation">
                        <label>
                            <!--<input name="_wc_booking_requires_confirmation" id="_wc_booking_requires_confirmation" value="0" type="hidden" >-->
                            <input name="_wc_booking_requires_confirmation" id="_wc_booking_requires_confirmation" value="1" type="checkbox" <?php checked( $booking_confirmation ); ?> class="dokan-booking-confirmation"> <?php _e( 'Requires Confirmation', 'dokan' ); ?>
                            <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Check this box if the booking requires YOUR approval/confirmation. Payment will not be taken during CHECKOUT.', 'dokan' ) ?>">
                                <i class="fa fa-question-circle"></i>
                            </span>
                        </label>
                    </div>

                    <div class="dokan-form-group">

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

                    <!--Attributes section-->
                    <?php
                        if ( $post_id ) {
                            $product_attributes   = get_post_meta( $post_id, '_product_attributes', true );
                            $attribute_taxonomies = wc_get_attribute_taxonomies();
                            dokan_get_template_part( 'booking/html-attributes', '', array(
                                'is_booking'           => true,
                                'post_id'              => $post_id,
                                'product_attributes'   => $product_attributes,
                                'attribute_taxonomies' => $attribute_taxonomies,
                            ) );
                        }
                    ?>
                    <!--shipping and tax section-->
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

                    <div id="bookings_availability" class="bookings_availability availability_fields dokan-edit-row dokan-clearfix">

                        <div class="dokan-section-heading" data-togglehandler="bookings_availability">
                            <h2><i class="fa fa-calendar" aria-hidden="true"></i> <?php _e( 'Availability' , 'dokan' ) ?></h2>
                            <p><?php _e( 'Set Availability options' , 'dokan' ) ?></p>
                            <a href="#" class="dokan-section-toggle">
                                <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
                            </a>
                            <div class="dokan-clearfix"></div>
                        </div>

                        <div class="dokan-section-content">
                            <div class="dokan-form-group">
                                <label for="_wc_booking_qty" class="form-label"><?php _e( 'Max bookings per block', 'dokan' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_wc_booking_qty', array( 'min' => '1', 'step' => 1, 'value' => $wc_booking_qty ), 'number' ); ?>
                            </div>
                            <div class="dokan-input-group content-half-part">
                                <label for="_wc_booking_min_date" class="form-label"><?php _e( 'Minimum booking window ( into the future )', 'dokan' ); ?></label>
                                <input type="number" class="dokan-form-control" name="_wc_booking_min_date" id="_wc_booking_min_date" value="<?php echo max( absint( get_post_meta( $post_id, '_wc_booking_min_date', true ) ), 1 ); ?>" step="1" min="1" style="margin-right: 7px; width: 4em;">
                                <select name="_wc_booking_min_date_unit" id="_wc_booking_min_date_unit" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                                    <option value="month" <?php selected( $booking_min_date_unit, 'month' ); ?>><?php _e( 'Month(s)', 'dokan' ); ?></option>
                                    <option value="week" <?php selected( $booking_min_date_unit, 'week' ); ?>><?php _e( 'Week(s)', 'dokan' ); ?></option>
                                    <option value="day" <?php selected( $booking_min_date_unit, 'day' ); ?>><?php _e( 'Day(s)', 'dokan' ); ?></option>
                                    <option value="hour" <?php selected( $booking_min_date_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'dokan' ); ?></option>
                                </select>
                            </div>


                            <div class="dokan-input-group content-half-part">
                                <label for="_wc_booking_max_date" class="form-label"><?php _e( 'Maximum booking window ( into the future )', 'dokan' ); ?></label>
                                <input type="number" class="dokan-form-control" name="_wc_booking_max_date" id="_wc_booking_max_date" value="<?php echo max( absint( get_post_meta( $post_id, '_wc_booking_max_date', true ) ), 1 ); ?>" step="1" min="1" style="margin-right: 7px; width: 4em;">
                                <select name="_wc_booking_max_date_unit" id="_wc_booking_max_date_unit" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                                    <option value="month" <?php selected( $booking_max_date_unit, 'month' ); ?>><?php _e( 'Month(s)', 'dokan' ); ?></option>
                                    <option value="week" <?php selected( $booking_max_date_unit, 'week' ); ?>><?php _e( 'Week(s)', 'dokan' ); ?></option>
                                    <option value="day" <?php selected( $booking_max_date_unit, 'day' ); ?>><?php _e( 'Day(s)', 'dokan' ); ?></option>
                                    <option value="hour" <?php selected( $booking_max_date_unit, 'hour' ); ?>><?php _e( 'Hour(s)', 'dokan' ); ?></option>
                                </select>
                            </div>

                            <div class="dokan-form-group">
                                <label for="_wc_booking_buffer_period" class="form-label"><?php _e( 'Require a buffer period of ( ', 'dokan' ); ?><span id='_booking_binded_label'>minutes</span><?php _e( ' ) between bookings', 'dokan' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_wc_booking_buffer_period', array( 'step' => 1, 'value' => $booking_buffer_period ), 'number' ); ?>
                            </div>

                            <div class="dokan-form-group">
                                <label class="form-label">
                                    <!--<input name="_wc_booking_apply_adjacent_buffer" id="_wc_booking_apply_adjacent_buffer" value="0" type="hidden" >-->
                                    <input name="_wc_booking_apply_adjacent_buffer" id="_wc_booking_apply_adjacent_buffer" value="1" type="checkbox" <?php checked( $adjacent_buffer_period ); ?> class="dokan-booking-adjacent-buffer"> <?php _e( 'Adjacent Buffering ?', 'dokan' ); ?>
                                    <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'By default buffer period applies forward into the future of a booking. Enabling this option will apply adjacently (before and after Bookings)', 'dokan' ) ?>">
                                        <i class="fa fa-question-circle"></i>
                                    </span>
                                </label>
                            </div>

                            <div class="dokan-form-group">
                                <label for="_wc_booking_default_date_availability" class="form-label"><?php _e( 'All dates are...', 'dokan' ); ?></label>
                                <select name="_wc_booking_default_date_availability" id="_wc_booking_default_date_availability" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                                    <option value="available" <?php selected( $booking_default_date_availability, 'available' ); ?>><?php _e( 'available by default', 'dokan' ); ?></option>
                                    <option value="non-available" <?php selected( $booking_default_date_availability, 'non-available' ); ?>><?php _e( 'not-available by default', 'dokan' ); ?></option>
                                </select>
                                <span class="form-label"><i><?php _e( 'This option affects how you use the rules below.', 'dokan' ); ?></i></span>
                            </div>

                            <div class="dokan-form-group">
                                <label for="_wc_booking_check_availability_against" class="form-label"><?php _e( 'Check rules against...', 'dokan' ); ?></label>
                                <select name="_wc_booking_check_availability_against" id="_wc_booking_check_availability_against" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                                    <option value="" <?php selected( $booking_check_availability, "" ); ?>><?php _e( 'All blocks being booked', 'dokan' ); ?></option>
                                    <option value="start" <?php selected( $booking_check_availability, true ); ?>><?php _e( 'The starting block only', 'dokan' ); ?></option>
                                </select>
                                <span class="form-label"><i><?php _e( 'This option affects how bookings are checked for availability.', 'dokan' ); ?></i></span>
                            </div>

                            <div class="dokan-form-group _wc_booking_first_block_time_field">
                                <label for="_wc_booking_first_block_time" class="form-label"><?php _e( 'First block starts at...', 'dokan' ); ?></label>
                                <input type="time" name="_wc_booking_first_block_time" id="_wc_booking_first_block_time" value="<?php echo $booking_first_block ?>" placeholder="HH:MM:">
                            </div>

                            <div class="dokan-form-group dokan_booking_has_restricted_days_field">
                                <label class="form-label">
                                    <input type="checkbox" name="_wc_booking_has_restricted_days" id="dokan_booking_has_restricted_days_field" value="yes" <?php checked( $booking_has_restricted_days, 'yes' ) ?>>
                                    <?php _e( 'Restrict start and end days?', 'dokan' ); ?>

                                    <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Restrict bookings so that they can only start on certain days of the week. Does not affect availability.', 'dokan' ) ?>">
                                        <i class="fa fa-question-circle"></i>
                                    </span>

                                </label>
                            </div>

                            <div class="dokan-booking-day-restriction">
                                <table class="widefat">
                                    <tbody>
                                        <tr>
                                            <td>&nbsp;</td>
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
                                                            <label class="checkbox" for="_wc_booking_restricted_days[<?php echo $i; ?>]"><?php echo $weekdays[ $i ]; ?>&nbsp;</label>
                                                            <input type="checkbox" class="checkbox" name="_wc_booking_restricted_days[<?php echo $i; ?>]" id="_wc_booking_restricted_days[<?php echo $i; ?>]" value="<?php echo $i; ?>" <?php checked( $restricted_days[ $i ], $i ); ?>>
                                                        </td>
                                                    <?php
                                                }
                                            ?>
                                            <td>&nbsp;</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="dokan-form-group">
                                <label for="_wc_booking_range_availability" class="form-label"><?php _e( 'Set Availability Range :', 'dokan' ); ?></label>
                            </div>

                            <div class="table_grid dokan-booking-range-table">
                                <table class="widefat">
                                    <thead>
                                        <tr>
                                            <th class="sort" width="1%">&nbsp;</th>
                                            <th><?php _e( 'Range type', 'dokan' ); ?></th>
                                            <th><?php _e( 'Range', 'dokan' ); ?></th>
                                            <th></th>
                                            <th></th>
                                            <th>
                                                <?php _e( 'Bookable', 'dokan' ); ?>
                                                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'If not bookable, users won\'t be able to choose this block for their booking.', 'dokan' ); ?>">
                                                    <i class="fa fa-question-circle"></i>
                                                </span>
                                            </th>
                                            <th>

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
                                            <th colspan="8">
                                                <a href="#" class="button button-primary add_row dokan-btn dokan-btn-theme" data-row="<?php
                                                ob_start();
                                                include( DOKAN_WC_BOOKING_TEMPLATE_PATH.'booking/html-booking-availability-fields.php' );
                                                $html = ob_get_clean();
                                                echo esc_attr( $html );
                                                ?>"><?php _e( 'Add Range', 'dokan' ); ?></a>
                                                <span class="description"><?php _e( 'Rules with lower numbers will execute first. Rules further down this table with the same priority will also execute first.', 'dokan' ); ?></span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                    <tbody id="availability_rows">
                                        <?php
                                        $values                      = get_post_meta( $post_id, '_wc_booking_availability', true );
                                        if ( !empty( $values ) && is_array( $values ) ) {
                                            foreach ( $values as $availability ) {
                                                include(  DOKAN_WC_BOOKING_TEMPLATE_PATH.'booking/html-booking-availability-fields.php' );
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <?php dokan_get_template_part( 'booking/html-dokan-booking-pricing', '', $template_args );?>
                    <div class='extra_options dokan-edit-row'>
                         <div class="dokan-section-heading" data-togglehandler="extra_options">
                            <h2><i class="fa fa-cubes" aria-hidden="true"></i> <?php _e( 'Extra Options' , 'dokan' ) ?></h2>
                            <p><?php _e( 'Set more options' , 'dokan' ) ?></p>
                            <a href="#" class="dokan-section-toggle">
                                <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
                            </a>
                            <div class="dokan-clearfix"></div>
                        </div>
                        <div class="dokan-section-content dokan-clearfix" >
                            <?php
                            if ( empty( $post_id ) ) {
                                _e( 'Please Save the Product to add extra options ( Persons or Resources )', 'dokan' );
                                $type = 'hidden';
                            } else {
                            ?>
                                <div class="dokan-form-group">
                                    <label>
                                        <input name="_wc_booking_has_persons" id="_wc_booking_has_persons" type="checkbox" <?php checked( $has_persons, true ); ?> class="dokan-booking-person"> <?php _e( 'Has persons', 'dokan' ); ?>
                                    </label>

                                </div>
                                <div class="dokan-form-group">
                                    <label>
                                        <input name="_wc_booking_has_resources" id="_wc_booking_has_resources" type="checkbox" <?php checked( $has_resource, true ); ?> class="dokan-booking-resource"> <?php _e( 'Has resources', 'dokan' ); ?>
                                    </label>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class='dokan-clearfix'></div>


                    <?php
                    if ( !empty( $post_id ) ) {
                       dokan_get_template_part( 'booking/persons/html-booking-persons', '', $template_args );

                       $resource_args = array(
                            'is_booking'                  => true,
                            'post_id'                     => $post_id,
                            'booking_resource_label'      => $booking_resource_label,
                            'booking_resource_assignment' => $booking_resource_assignment,
                        );
                        dokan_get_template_part( 'booking/resources/html-booking-resources', '', $resource_args );
                    }
                    ?>

                </div>

                <div class="dokan-product-short-description">
                    <label for="post_excerpt" class="form-label"><?php _e( 'Short Description', 'dokan' ); ?></label>
                    <?php wp_editor( $post_excerpt, 'post_excerpt', array( 'editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_excerpt' ) ); ?>
                </div>

                <div class="dokan-product-description">
                    <label for="post_content" class="form-label"><?php _e( 'Description', 'dokan' ); ?></label>
                    <?php wp_editor( $post_content, 'post_content', array( 'editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_content' ) ); ?>
                </div>

                <?php do_action( 'dokan_new_product_form', $post, $post_id ); ?>

                <?php if ( !empty( $post_id ) ): ?>

                <?php do_action( 'dokan_product_edit_after_main', $post, $post_id ); ?>

                <div class="dokan-other-options dokan-edit-row dokan-clearfix">
                    <div class="dokan-section-heading" data-togglehandler="dokan_other_options">
                        <h2><i class="fa fa-cog" aria-hidden="true"></i> <?php _e( 'Other Options', 'dokan' ); ?></h2>
                        <p><?php _e( 'Set your extra product options', 'dokan' ); ?></p>
                        <a href="#" class="dokan-section-toggle">
                            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
                        </a>
                        <div class="dokan-clearfix"></div>
                    </div>

                    <div class="dokan-section-content">
                        <div class="dokan-form-group content-half-part">
                            <label for="post_status" class="form-label"><?php _e( 'Product Status', 'dokan' ); ?></label>
                            <?php if ( $post_status != 'pending' ) { ?>
                                <?php
                                $post_statuses = apply_filters( 'dokan_post_status', array(
                                    'publish' => __( 'Online', 'dokan' ),
                                    'draft'   => __( 'Draft', 'dokan' )
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

                        <div class="dokan-form-group content-half-part">
                            <label for="_visibility" class="form-label"><?php _e( 'Visibility', 'dokan' ); ?></label>
                            <select name="_visibility" id="_visibility" class="dokan-form-control">
                                <?php foreach ( $visibility_options as $name => $label ): ?>
                                    <option value="<?php echo $name; ?>" <?php selected( $_visibility, $name ); ?>><?php echo $label; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="dokan-clearfix"></div>

                        <div class="dokan-form-group">
                            <label for="_purchase_note" class="form-label"><?php _e( 'Purchase Note', 'dokan' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_purchase_note', array( 'placeholder' => __( 'Customer will get this info in their order email', 'dokan' ) ), 'textarea' ); ?>
                        </div>

                        <div class="dokan-form-group">
                            <?php $_enable_reviews = ( $post->comment_status == 'open' ) ? 'yes' : 'no'; ?>
                            <?php dokan_post_input_box( $post_id, '_enable_reviews', array( 'value' => $_enable_reviews, 'label' => __( 'Enable product reviews', 'dokan' ) ), 'checkbox' ); ?>
                        </div>

                    </div>
                </div><!-- .dokan-other-options -->
                <?php

                    dokan_get_template_part( 'booking/persons/html-other-options', '', $template_args  );

                    do_action( 'dokan_product_edit_after_options' );

                    wp_nonce_field( 'dokan_edit_product', 'dokan_edit_product_nonce' );
                ?>

                    <input type="hidden" name="dokan_update_product" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
                    <input type="submit" name="dokan_update_product" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>

                <?php else: ?>

                    <?php wp_nonce_field( 'dokan_add_new_product', 'dokan_add_new_product_nonce' ); ?>
                    <input type="hidden" name="add_product" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
                    <input type="submit" name="add_product" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>

                <?php endif; ?>

                <!--hidden input for Firefox issue-->
                <input type="hidden" name="_stock_status" value="instock"/>
                <input type="hidden" name="_sku" value=""/>
                <input type="hidden" name="price" value=""/>
                <input type="hidden" name="product_type" value="booking"/>
            </form>

                <?php } else { ?>
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
</div> <!-- #primary .content-area -->
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
