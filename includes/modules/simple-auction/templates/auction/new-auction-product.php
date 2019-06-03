<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">
    <?php
    do_action( 'dokan_dashboard_content_before' );
    do_action( 'dokan_new_auction_product_content_before' );
    ?>

    <div class="dokan-dashboard-content">
        <?php

            /**
             *  dokan_auction_content_inside_before hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_auction_content_inside_before' );
        ?>
        <header class="dokan-dashboard-header dokan-clearfix">
            <h1 class="entry-title">
                <?php _e( 'Add New Auction Product', 'dokan' ); ?>
            </h1>
        </header><!-- .entry-header -->

        <div class="dokan-new-product-area">
            <?php if ( Dokan_Template_Auction::$errors ) { ?>
                <div class="dokan-alert dokan-alert-danger">
                    <a class="dokan-close" data-dismiss="alert">&times;</a>
                    <?php foreach ( Dokan_Template_Auction::$errors as $error) { ?>
                        <strong><?php _e( 'Error!', 'dokan' ); ?></strong> <?php echo $error ?>.<br>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php

            $can_sell = apply_filters( 'dokan_can_post', true );

            if ( $can_sell ) {

                if ( dokan_is_seller_enabled( get_current_user_id() ) ) { ?>

                    <form class="dokan-form-container dokan-auction-product-form" method="post">

                        <div class="product-edit-container dokan-clearfix">
                            <div class="content-half-part featured-image">
                                <div class="featured-image">
                                    <div class="dokan-feat-image-upload">
                                        <div class="instruction-inside">
                                            <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="0">
                                            <i class="fa fa-cloud-upload"></i>
                                            <a href="#" class="dokan-feat-image-btn dokan-btn"><?php _e( 'Upload Product Image', 'dokan' ); ?></a>
                                        </div>

                                        <div class="image-wrap dokan-hide">
                                            <a class="close dokan-remove-feat-image">&times;</a>
                                                <img src="" alt="">
                                        </div>
                                    </div>
                                </div>

                                <div class="dokan-product-gallery">
                                    <div class="dokan-side-body" id="dokan-product-images">
                                        <div id="product_images_container">
                                            <ul class="product_images dokan-clearfix">
                                                <li class="add-image add-product-images tips" data-title="<?php _e( 'Add gallery image', 'dokan' ); ?>">
                                                    <a href="#" class="add-product-images"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                </li>
                                            </ul>
                                            <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="">
                                        </div>
                                    </div>
                                </div> <!-- .product-gallery -->
                            </div>

                            <div class="content-half-part dokan-product-meta">
                                <div class="dokan-form-group dokan-auction-post-title">
                                    <input class="dokan-form-control" name="post_title" id="post-title" type="text" placeholder="<?php esc_attr_e( 'Product name..', 'dokan' ); ?>" value="<?php echo dokan_posted_input( 'post_title' ); ?>">
                                </div>

                                <div class="dokan-form-group dokan-auction-post-excerpt">
                                    <textarea name="post_excerpt" id="post-excerpt" rows="5" class="dokan-form-control" placeholder="<?php esc_attr_e( 'Short description about the product...', 'dokan' ); ?>"><?php echo dokan_posted_textarea( 'post_excerpt' ); ?></textarea>
                                </div>

                                <?php if ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ): ?>
                                    <div class="dokan-form-group dokan-auction-category">

                                        <?php
                                        $category_args =  array(
                                            'show_option_none' => __( '- Select a category -', 'dokan' ),
                                            'hierarchical'     => 1,
                                            'hide_empty'       => 0,
                                            'name'             => 'product_cat',
                                            'id'               => 'product_cat',
                                            'taxonomy'         => 'product_cat',
                                            'title_li'         => '',
                                            'class'            => 'product_cat dokan-form-control dokan-select2',
                                            'exclude'          => '',
                                            'selected'         => Dokan_Template_Products::$product_cat,
                                        );

                                        wp_dropdown_categories( apply_filters( 'dokan_product_cat_dropdown_args', $category_args ) );
                                        ?>
                                    </div>
                                <?php elseif ( dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'multiple' ): ?>
                                    <div class="dokan-form-group dokan-auction-category">
                                        <?php
                                        $term = array();
                                        include_once DOKAN_LIB_DIR.'/class.taxonomy-walker.php';
                                        $drop_down_category = wp_dropdown_categories( array(
                                            'show_option_none' => __( '', 'dokan' ),
                                            'hierarchical'     => 1,
                                            'hide_empty'       => 0,
                                            'name'             => 'product_cat[]',
                                            'id'               => 'product_cat',
                                            'taxonomy'         => 'product_cat',
                                            'title_li'         => '',
                                            'class'            => 'product_cat dokan-form-control dokan-select2',
                                            'exclude'          => '',
                                            'selected'         => $term,
                                            'echo'             => 0,
                                            'walker'           => new DokanTaxonomyWalker()
                                        ) );

                                        echo str_replace( '<select', '<select data-placeholder="'.__( 'Select product category', 'dokan' ).'" multiple="multiple" ', $drop_down_category );
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <div class="dokan-form-group dokan-auction-tags">
                                    <?php
                                    require_once DOKAN_LIB_DIR.'/class.taxonomy-walker.php';
                                    $drop_down_tags = wp_dropdown_categories( array(
                                        'show_option_none' => __( '', 'dokan' ),
                                        'hierarchical'     => 1,
                                        'hide_empty'       => 0,
                                        'name'             => 'product_tag[]',
                                        'id'               => 'product_tag',
                                        'taxonomy'         => 'product_tag',
                                        'title_li'         => '',
                                        'class'            => 'product_tags dokan-form-control dokan-select2',
                                        'exclude'          => '',
                                        'selected'         => array(),
                                        'echo'             => 0,
                                        'walker'           => new DokanTaxonomyWalker()
                                    ) );

                                    echo str_replace( '<select', '<select data-placeholder="'.__( 'Select product tags', 'dokan' ).'" multiple="multiple" ', $drop_down_tags );
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="product-edit-new-container">
                            <div class="dokan-edit-row dokan-auction-general-sections dokan-clearfix">

                                <div class="dokan-section-heading" data-togglehandler="dokan_product_inventory">
                                    <h2><i class="fa fa-cubes" aria-hidden="true"></i> <?php _e( 'General Options', 'dokan' ) ?></h2>
                                    <p><?php _e( 'Manage your auction product data', 'dokan' ); ?></p>
                                    <div class="dokan-clearfix"></div>
                                </div>

                                <div class="dokan-section-content">
                                    <div class="content-half-part dokan-auction-item-condition">
                                        <label class="dokan-control-label" for="_auction_item_condition"><?php _e( 'Item condition', 'dokan' ); ?></label>
                                        <div class="dokan-form-group">
                                            <select name="_auction_item_condition" class="dokan-form-control" id="_auction_item_condition">
                                                <option value="new"><?php _e( 'New', 'dokan' ) ?></option>
                                                <option value="used"><?php _e( 'Used', 'dokan' ) ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="content-half-part dokan-auction-type">
                                        <label class="dokan-control-label" for="_auction_type"><?php _e( 'Auction type', 'dokan' ); ?></label>
                                        <div class="dokan-form-group">
                                            <select name="_auction_type" class="dokan-form-control" id="_auction_type">
                                                <option value="normal"><?php _e( 'Normal', 'dokan' ) ?></option>
                                                <option value="reverse"><?php _e( 'Reverse', 'dokan' ) ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="dokan-clearfix"></div>

                                    <div class="dokan-form-group dokan-auction-proxy-bid">
                                        <div class="checkbox">
                                            <label for="_auction_proxy">
                                                <input type="checkbox" name="_auction_proxy" id="_auction_proxy" value="yes">
                                                <?php _e( 'Enable proxy bidding for this auction product', 'dokan' );?>
                                            </label>
                                        </div>
                                    </div>

                                    <?php if( get_option( 'simple_auctions_sealed_on', 'no' ) == 'yes') : ?>
                                        <div class="dokan-form-group dokan-auction-sealed-bid">
                                            <div class="checkbox">
                                                <label for="_auction_sealed">
                                                    <input type="checkbox" name="_auction_sealed" value="yes" id="_auction_sealed">
                                                    <?php _e( 'Enable sealed bidding for this auction product', 'dokan' );?>
                                                    <i class="fa fa-question-circle tips" data-title="<?php _e( 'In this type of auction all bidders simultaneously submit sealed bids so that no bidder knows the bid of any other participant. The highest bidder pays the price they submitted. If two bids with same value are placed for auction the one which was placed first wins the auction.', 'dokan' ); ?>"></i>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="content-half-part dokan-auction-start-price">
                                        <label class="dokan-control-label" for="_auction_start_price"><?php _e( 'Start Price', 'dokan' ); ?></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <input class="dokan-form-control" name="_auction_start_price" id="_auction_start_price" type="number" placeholder="9.99" value="" step="any" min="0" style="width: 97%;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="content-half-part dokan-auction-bid-increment">
                                        <label class="dokan-control-label" for="_auction_bid_increment"><?php _e( 'Bid increment', 'dokan' ); ?></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                               <input class="dokan-form-control" name="_auction_bid_increment" id="_auction_bid_increment" type="number" placeholder="9.99" value="" step="any" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dokan-clearfix"></div>

                                    <div class="content-half-part dokan-auction-reserved-price">
                                        <label class="dokan-control-label" for="_auction_reserved_price"><?php _e( 'Reserved price', 'dokan' ); ?></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <input class="dokan-form-control" name="_auction_reserved_price" id="_auction_reserved_price" type="number" placeholder="9.99" value="" step="any" min="0" style="width: 97%;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="content-half-part dokan-auction-regular-price">
                                        <label class="dokan-control-label" for="_regular_price"><?php _e( 'Buy it now price', 'dokan' ); ?></label>
                                        <div class="dokan-form-group">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <input class="dokan-form-control" name="_regular_price" id="_regular_price" type="number" placeholder="9.99" value="" step="any" min="0">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dokan-clearfix"></div>

                                    <div class="content-half-part dokan-auction-dates-from">
                                        <label class="dokan-control-label" for="_auction_dates_from"><?php _e( 'Auction Start date', 'dokan' ); ?></label>
                                        <div class="dokan-form-group">
                                            <input class="dokan-form-control auction-datepicker" name="_auction_dates_from" id="_auction_dates_from" type="text" value="" style="width: 97%;">
                                        </div>
                                    </div>

                                    <div class="content-half-part dokan-auction-dates-to">
                                        <label class="dokan-control-label" for="_auction_dates_to"><?php _e( 'Auction End date', 'dokan' ); ?></label>
                                        <div class="dokan-form-group">
                                            <input class="dokan-form-control auction-datepicker" name="_auction_dates_to" id="_auction_dates_to" type="text" value="">
                                        </div>
                                    </div>

                                    <div class="dokan-clearfix"></div>

                                    <div class="auction_relist_section">
                                        <div class="dokan-form-group dokan-auction-automatic-relist">
                                            <div class="dokan-text-left">
                                                <div class="checkbox">
                                                    <label for="_auction_automatic_relist">
                                                        <input type="hidden" name="_auction_automatic_relist" value="no">
                                                        <input type="checkbox" name="_auction_automatic_relist" id="_auction_automatic_relist" value="yes">
                                                        <?php _e( 'Enable automatic relisting for this auction', 'dokan' );?>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relist_options" style="display: none">
                                            <div class="dokan-w3 dokan-auction-relist-fail-time">
                                                <label class="dokan-control-label" for="_auction_relist_fail_time"><?php _e( 'Relist if fail after n hours', 'dokan' ); ?></label>
                                                <div class="dokan-form-group">
                                                    <input class="dokan-form-control" name="_auction_relist_fail_time" id="_auction_relist_fail_time" type="number">
                                                </div>
                                            </div>
                                            <div class="dokan-w3 dokan-auction-relist-not-paid-time">
                                                <label class="dokan-control-label" for="_auction_relist_not_paid_time"><?php _e( 'Relist if not paid after n hours', 'dokan' ); ?></label>
                                                <div class="dokan-form-group">
                                                    <input class="dokan-form-control" name="_auction_relist_not_paid_time" id="_auction_relist_not_paid_time" type="number">
                                                </div>
                                            </div>
                                            <div class="dokan-w3 dokan-auction-relist-duration">
                                                <label class="dokan-control-label" for="_auction_relist_duration"><?php _e( 'Relist auction duration in h', 'dokan' ); ?></label>
                                                <div class="dokan-form-group">
                                                    <input class="dokan-form-control" name="_auction_relist_duration" id="_auction_relist_duration" type="number">
                                                </div>
                                            </div>
                                            <div class="dokan-clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dokan-form-group dokan-auction-post-content">
                            <?php wp_editor( Dokan_Template_Auction::$post_content, 'post_content', array('editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_content') ); ?>
                        </div>

                        <?php do_action( 'dokan_new_auction_product_form' ); ?>

                        <div class="dokan-form-group">
                            <input type="hidden" name="product-type" value="auction">
                            <?php wp_nonce_field( 'dokan_add_new_auction_product', 'dokan_add_new_auction_product_nonce' ); ?>
                            <input type="submit" name="add_auction_product" class="dokan-btn dokan-btn-theme dokan-btn-lg dokan-right" value="<?php esc_attr_e( 'Add auction Product', 'dokan' ); ?>"/>
                        </div>

                    </form>

                <?php } else { ?>

                    <?php dokan_seller_not_enabled_notice(); ?>

                <?php } ?>

            <?php } else { ?>

                <?php do_action( 'dokan_can_post_notice' ); ?>

            <?php } ?>
        <?php

            /**
             *  dokan_auction_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_auction_content_inside_after' );
        ?>
    </div> <!-- #primary .content-area -->

     <?php
        /**
         *  dokan_dashboard_content_after hook
         *  dokan_withdraw_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_new_auction_product_content_after' );
    ?>
</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>

<script>
    ;(function($){
        $(document).ready(function(){
            $('.auction-datepicker').datetimepicker({
                dateFormat : 'yy-mm-dd',
                currentText: dokan.datepicker.now,
                closeText: dokan.datepicker.done,
                timeText: dokan.datepicker.time,
                hourText: dokan.datepicker.hour,
                minuteText: dokan.datepicker.minute
            });
            $('#_auction_automatic_relist').on( 'click', function(){
              if($(this).prop('checked')){
                  $('.relist_options').show();
              }else{
                  $('.relist_options').hide();
              }
            });

            $('.dokan-auction-proxy-bid').on('change', 'input#_auction_proxy', function() {
                if( $(this).prop('checked') ) {
                    $('.dokan-auction-sealed-bid').hide();
                } else {
                    $('.dokan-auction-sealed-bid').show();
                }
            });

            $('.dokan-auction-sealed-bid').on('change', 'input#_auction_sealed', function() {
                if ( $(this).prop('checked') ) {
                    $('.dokan-auction-proxy-bid').hide();
                } else {
                    $('.dokan-auction-proxy-bid').show();
                }
            });
            $('input#_auction_proxy').trigger('change');
            $('input#_auction_sealed').trigger('change');
        });
    })(jQuery)

</script>
