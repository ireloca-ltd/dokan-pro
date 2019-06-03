<div id="bookings_resources" class="bookings_resources_tab dokan-edit-row dokan-clearfix">
    <div class="dokan-section-heading" data-togglehandler="bookings_resources">
        <h2><i class="fa fa-plus-square" aria-hidden="true"></i> <?php _e( 'Additional Resources', 'dokan' ) ?></h2>
        <p><?php _e( 'Set Available Resources', 'dokan' ) ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>
    <div class="dokan-section-content dokan-clearfix">
        <div class="dokan-form-group" style="display:none">
            <label for="_wc_booking_resource_label" class="form-label"><?php _e( 'Label', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'The label shown on the frontend if the resource is customer defined.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_booking_resource_label', array( 'placeholder' => __( 'Exras', 'dokan' ), 'value' => $booking_resource_label ), 'text' ); ?>
        </div>
        <div id="resource_options" class="dokan-input-group options_group">
            <label for="_wc_booking_resources_assignment" class="form-label"><?php _e( 'Resources are...', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Customer selected resources allow customers to choose one from the booking form.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <select name="_wc_booking_resources_assignment" id="_wc_booking_resources_assignment" class="dokan-form-control short" style="width: auto; margin-right: 7px;">
                <option value="customer" <?php selected( $booking_resource_assignment, 'customer' ); ?>><?php _e( 'Customer selected', 'dokan' ); ?></option>
                <option value="automatic" <?php selected( $booking_resource_assignment, 'automatic' ); ?>><?php _e( 'Automatically assigned', 'dokan' ); ?></option>
            </select>
        </div>
 <div class="helper_text" style="display:none">
 <p><i>
 <?php _e('<strong>Resources are...</strong>', 'dokan-wc-booking' ); ?><br>
 <?php _e('<u>Customer selected</u>: A booking form shows a dropdown list of resources that the customer can select. ', 'dokan-wc-booking' ); ?><br>
  <?php _e('<u>Automatically assigned</u>: A resource is automatically assigned to a customer booking if available. The customer won’t see the resource on the product page, as it is added without the need for customers to do anything. If you want the customer to see theResource on the product page, but not be able to deselect it, add it as the only resource and set this setting, to ‘customer selected’.  ', 'dokan-wc-booking' ); ?><br>
    <?php _e('<strong>Important! If you are using customer selected resources, you must add a resource ‘none’ with an availability of 1000 to give the customer the option to book a resource by choice. If you do not add the ‘none’, the customer is not able to not select a resource.</strong>', 'dokan-wc-booking' ); ?>
  </i></p>
 </div>
        <div class="options_group">

            <div class="toolbar">
                <h3><?php _e( 'Resources', 'dokan' ); ?></h3>
                <!--<span class="toolbar_links"><a href="#" class="close_all"><?php // _e( 'Close all', 'dokan' );   ?></a><a href="#" class="expand_all"><?php // _e( 'Expand all', 'dokan' );   ?></a></span>-->
            </div>

            <div class="woocommerce_bookable_resources wc-metaboxes">

                <div id="message" class="inline woocommerce-message updated" style="margin: 1em 0;">
                    <p><?php _e( 'Resources are used if you have multiple bookable items, e.g. instructors or rental equipement. Availability for resources is global across all bookable products.<br><strong>Important! Don`t forget to save your product before you add/link a resource.</strong>', 'dokan-wc-booking' ); ?></p>
                </div>

                <?php
                global $wpdb;

                $all_resources = get_posts( array(
                    'post_type'      => 'bookable_resource',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'asc',
                    'author'         => dokan_get_current_user_id(),
                ) );

                if ( $post_id ) {
                    $product_resources = $wpdb->get_col( $wpdb->prepare( "SELECT resource_id FROM {$wpdb->prefix}wc_booking_relationships WHERE product_id = %d ORDER BY sort_order;", $post_id ) );
                    $loop              = 0;

                    if ( $product_resources ) {
                        $resource_base_costs  = get_post_meta( $post_id, '_resource_base_costs', true );
                        $resource_block_costs = get_post_meta( $post_id, '_resource_block_costs', true );

                        foreach ( $product_resources as $resource_id ) {
                            $resource            = get_post( $resource_id );
                            $resource_base_cost  = isset( $resource_base_costs[$resource_id] ) ? $resource_base_costs[$resource_id] : '';
                            $resource_block_cost = isset( $resource_block_costs[$resource_id] ) ? $resource_block_costs[$resource_id] : '';

                            include( DOKAN_WC_BOOKING_TEMPLATE_PATH.'booking/resources/html-booking-resource.php' );

                            $loop++;
                        }
                    }
                }
            ?>
            </div>
            <?php if( $all_resources ) {?>
            <p class="toolbar">
                <button type="button" class="button button-primary dokan-btn dokan-btn-theme add_resource"><?php _e( 'Add/link Resource', 'dokan' ); ?></button>
                <select name="add_resource_id" class="add_resource_id">
                    <!--<option value=""><?php // _e( 'New resource', 'dokan' ); ?></option>-->
                    <?php
                    if ( $all_resources ) {
                        foreach ( $all_resources as $resource ) {
                            echo '<option value="' . esc_attr( $resource->ID ) . '">#' . $resource->ID . ' - ' . esc_html( $resource->post_title ) . '</option>';
                        }
                    }
                    ?>
                </select>
                <!--<a href="<?php //echo admin_url( 'edit.php?post_type=bookable_resource' );   ?>" target="_blank"><?php _e( 'Manage Resources', 'dokan' ); ?></a>-->
            </p>
            <?php } else { ?>
            <div class="dokan-alert dokan-alert-warning">
                <?php
                $resource_add_link = dokan_get_navigation_url( 'booking' ).'resources/';

                echo sprintf( __( "No existing resource was found, please <a href='%s'>add resource</a>" ), $resource_add_link );
                ?>
            </div>
            <?php } ?>
            <div class="helper_text" style="display:none">
             <p><i>
             <?php _e('If you have Resources set up for other products, you will see the available Resources on the right. Select one to Link it to this Product. If you need to add a new Resource and none are set up, you will be prompted to ‘add resource’. (If you need to add another Resource, scroll to the top of the page and click the ‘Manage Resources’ tab, then continue the steps.) ', 'dokan-wc-booking' ); ?><br>
           </i></p>
           <p><i>
             <?php _e('After setting up your resources, you can now link your Resources to your product by selecting them and clicking ‘Add/Link Resource’. If you have set ‘customer selected resources’, the first Resource you add, will be the standard and first seen on the product page visible to the customer.', 'dokan-wc-booking' ); ?><br>
             <?php _e('<strong>Important!</strong> Link the Resources in the order they should show up to the customer', 'dokan-wc-booking' ); ?>
              </i></p>
              <p><i>
              <?php _e('Add extra costs to a resource, if needed:', 'dokan-wc-booking' ); ?><br>
              <?php _e('<strong>Base cost</strong> if you want to add one price for the whole booking no matter how long it takes.', 'dokan-wc-booking' ); ?><br>
              <?php _e('<strong>block cost</strong> if you want to multiply the amount for every block booked.', 'dokan-wc-booking' ); ?>
               </i></p>
                 <p><i><?php _e('<strong>Examples:</strong>', 'dokan-wc-booking' ); ?></i></p>
               <img id="img_resource_example_1" src="<?php _e('https://storage.googleapis.com/gcs.ireloca.com/2018/05/extracostexample.png', 'dokan-wc-booking' ); ?>" class="img_modal" alt="Attribute Value" style="width:100%;max-width:300px">
                   <div id="myModal_resource_example_1" class="modal">
                     <span class="img_close_resource_example_1 img_close_cross">&times;</span>
                     <img class="modal-content" id="img_resource_example_1_modal">
                 </div><br><br>
               <img id="img_resource_example_2" src="<?php _e('https://storage.googleapis.com/gcs.ireloca.com/2018/05/examplebikerental.jpg', 'dokan-wc-booking' ); ?>" class="img_modal" alt="Attribute Value" style="width:100%;max-width:300px">
                  <div id="myModal_resource_example_2" class="modal">
                    <span class="img_close_resource_example_2 img_close_cross">&times;</span>
                    <img class="modal-content" id="img_resource_example_2_modal">
                </div>
            </div>
        </div>
    </div>
</div>
