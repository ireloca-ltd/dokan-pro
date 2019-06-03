<div id="bookings_persons" class="bookings_persons_tab dokan-edit-row dokan-clearfix advanced_text" style="display:none;">

    <div class="dokan-section-heading" data-togglehandler="bookings_persons">
        <h2><i class="fa fa-users" aria-hidden="true"></i> <?php _e( 'Persons', 'dokan' ) ?></h2>
        <p><?php _e( 'Set Person Options', 'dokan' ) ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>
    <div class="dokan-section-content dokan-clearfix">
        <div class="dokan-form-group content-half-part">
            <label for="_wc_booking_min_persons_group" class="form-label"><?php _e( 'Min persons', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'The minimum number of persons per booking.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_booking_min_persons_group', array( 'min' => '0', 'step' => '1', 'value' => max( absint( get_post_meta( $post_id, '_wc_booking_min_persons_group', true ) ), 1 ) ), 'number' ); ?>
            <div class="helper_text" style="display:none">
            <p><i>
            <?php _e('<strong>Min persons:</strong> The minimum number of persons per booking. If this is set to 2, then <strong>EVERY</strong> customer booking your product, must select at minimum 2 persons. If this is set higher, then <strong>EVERY</strong> customer has to choose at least this amount of persons, when booking your product. ', 'dokan-wc-booking' ); ?>
             </i></p>
            </div>
        </div>
        <div class="dokan-form-group content-half-part">
            <label for="_wc_booking_max_persons_group" class="form-label"><?php _e( 'Max persons', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'The maximum number of persons per booking.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php $max_persons_count = get_post_meta( $post_id, '_wc_booking_max_persons_group', true ); ?>
            <?php if ( empty( $max_persons_count )){ ?>
            <?php dokan_post_input_box( $post_id, '_wc_booking_max_persons_group', array( 'min' => '0', 'step' => '1', 'value' => '999', 'number' )); ?>
            <?php } else { ?>
            <?php dokan_post_input_box( $post_id, '_wc_booking_max_persons_group', array( 'min' => '0', 'step' => '1', 'value' => get_post_meta( $post_id, '_wc_booking_max_persons_group', true ) ), 'number' ); ?>
            <?php } ?>
            <div class="helper_text" style="display:none">
            <p><i>
            <?php _e('<strong>Max persons:</strong> The maximum number of persons per booking. If this is set to 2, then <strong>EVERY</strong> customer booking your product, can select/book at maximum 2 persons. If this is set higher, then customers can book at maximum this amount of persons, when booking your product.', 'dokan-wc-booking' ); ?>
             </i></p>
             <p><i>
             <?php _e('<strong>IMPORTANT! You need to set a value, otherwise it defaults to ‘0’ and no blocks will be available to book.</strong>', 'dokan-wc-booking' ); ?>
              </i></p>
              <p><i>
              <?php _e('<strong>Example 3:</strong> You have 2 boats and there are 16 customer places available. (The maximum bookings per block was set to 16). You should then set the max number of persons to ‘16’. This means you can have a maximum of 16 customers at the same time. Here you <strong>WOULD</strong> check the "count persons as bookings option".', 'dokan-wc-booking' ); ?>
               </i></p>
               <p><i>
               <?php _e('<strong>Example 4:</strong> You offer private ski groups, where only 1 customer (booking for multiple people) should book the group, but the group should have a minimum amount of persons, then you need to use both ‘Min persons’ and ‘Max persons’. If this group should have at least 3 people, but at most 6, then you set ‘Min persons’ to 3 and ‘Max persons’ to 6. This means <u>EVERY</u> customer who books this product has to add at least 3 and a maximum of 6 people. Here you <strong>WOULD NOT</strong> check the "count persons as bookings option". ', 'dokan-wc-booking' ); ?>
                </i></p>
            </div>
        </div>

        <div class="dokan-form-group">
            <label>
                <input name="_wc_booking_person_cost_multiplier" id="_wc_booking_person_cost_multiplier" value="yes" type="checkbox" <?php checked( get_post_meta( $post_id, '_wc_booking_person_cost_multiplier', true ), true ); ?> > <?php _e( 'Multiply all costs by person count', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Enable this to multiply the entire cost of the booking (block and base costs) by the person count.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <div class="helper_text" style="display:none">
            <p><i>
            <?php _e('<strong>Multiply all costs by person count:</strong> All costs are multiplied by the number of persons the customer defines. This means both the ‘base cost’ <strong>AND</strong> ‘block cost’ are multiplied. If you offer a set price per block (Example 4 above), then leave this unchecked. If not (Example 3 above), then you should check this checkbox.', 'dokan-wc-booking' ); ?>
             </i></p>
            </div>
        </div>
        <div class="dokan-form-group">
            <label>
                <input name="_wc_booking_person_qty_multiplier" id="_wc_booking_person_qty_multiplier" value="yes" type="checkbox" <?php checked( get_post_meta( $post_id, '_wc_booking_person_qty_multiplier', true ), true ); ?> > <?php _e( 'Count persons as bookings', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Enable this to multiply the entire cost of the booking (block and base costs) by the person count.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <div class="helper_text" style="display:none">
            <p><i>
            <?php _e('<strong>Count persons as bookings:</strong> If each customer is a booking, then check this checkbox. On the other hand, if your bookings represent groups, like in Example 4 above, then leave this unchecked. ', 'dokan-wc-booking' ); ?>
             </i></p>
            </div>
        </div>
        <div class="dokan-form-group" style="display:none;">
            <label>
                <input name="_wc_booking_has_person_types" id="_wc_booking_has_person_types" value="yes" type="checkbox" <?php checked( get_post_meta( $post_id, '_wc_booking_has_person_types', true ), true ); ?> > <?php _e( 'Enable person types', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Person types allow you to offer different booking costs for different types of individuals, for example, adults and children.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <div class="helper_text" style="display:none">
            <p><i>
            <?php _e('<strong>Enable person types:</strong> Once you tick this checkbox, you have the option to add multiple types as needed. It will open a new field where you can add these types and set different prices and availabilities for different kinds of persons. ', 'dokan-wc-booking' ); ?>
             </i></p>
            </div>
        </div>


        <div class="options_group" id="persons-types">

            <div class="toolbar">
                <h3><?php _e( 'Person types', 'dokan' ); ?></h3>
                <!--<span class="toolbar_links"><a href="#" class="close_all"><?php // _e( 'Close all', 'dokan' ); ?></a><a href="#" class="expand_all"><?php // _e( 'Expand all', 'dokan' ); ?></a></span>-->
            </div>

            <div class="woocommerce_bookable_persons wc-metaboxes">

                <?php
                $admin = get_user_by( 'email', get_option( 'admin_email' ) );
                $person_types = get_posts( array(
                    'post_type'      => 'bookable_person',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'asc',
                    'post_parent'    => $post_id,
                    'author__in'     => array( $admin->ID, get_current_user_id() ),
                ) );

                if ( sizeof( $person_types ) == 0 ) {
                    echo '<div id="message" class="inline woocommerce-message" style="margin: 1em 0;">';
                    echo '<div class="squeezer">';
                    echo '<h4>' . __( 'Person types allow you to offer different booking costs for different types of individuals, for example, adults and children.', 'dokan' ) . '</h4>';
                    echo '</div>';
                    echo '</div>';
                }

                if ( $person_types ) {
                    $loop = 0;

                    foreach ( $person_types as $person_type ) {
                        $person_type_id = absint( $person_type->ID );
                        include( 'html-booking-person.php' );
                        $loop++;
                    }
                }
                ?>
            </div>

            <p class="toolbar">
                <button type="button" class="button button-primary add_person dokan-btn dokan-btn-theme"><?php _e( 'Add Person Type', 'dokan' ); ?></button>
            </p>
            <div class="helper_text" style="display:none">
              <p><i>
              <?php _e('<strong>Person Type Name</strong>: This is the Title of the type of person. It is seen by you and also seen by the customer when selecting how many persons they want to add when booking your product. ', 'dokan-wc-booking' ); ?><br>
              <?php _e('<strong>Description</strong>: This can be left blank if the ‘Person Type Name’ explains it adequately. If however the customer will need more information, like ages you accept as children or that students required ID, then add that information here. This is also seen by the customer. ', 'dokan-wc-booking' ); ?><br>
              <?php _e('<strong>Base Cost and Block Cost</strong>: Same as the ‘Base Cost’ and ‘Block Cost’ from before. If you added in the previous step, then you can leave these on 0. If however you want to have different costs based on ‘Person types’ then add your costs here.  ', 'dokan-wc-booking' ); ?><br>
              <?php _e('<strong>Min and Max</strong>: This works like the ‘Min persons’ and ‘Max persons’ from the previous step. This can also be set to 0. You would use these settings if you needed to specify that each customer has to add a min or max of a specific ‘Person type’ per booking.', 'dokan-wc-booking' ); ?><br>
               </i></p>
               <img id="img_pers_type" src="<?php _e('https://storage.googleapis.com/gcs.ireloca.com/2018/05/perstypeexample.jpg', 'dokan-wc-booking' ); ?>" class="img_modal" alt="Attribute Value" style="width:100%;max-width:300px">
                           <div id="myModal_pers_type" class="modal">
                             <span class="img_close_pers_type img_close_cross">&times;</span>
                             <img class="modal-content" id="img_pers_type_modal">
                         </div>
               <p style="color:red">Test if costs are overriden or added!!</p>
              </div>
        </div>
    </div>
</div>
