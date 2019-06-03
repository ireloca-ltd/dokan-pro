<div id="bookings_persons" class="bookings_persons_tab dokan-edit-row dokan-clearfix">

    <div class="dokan-section-heading" data-togglehandler="bookings_persons">
        <h2><i class="fa fa-users" aria-hidden="true"></i> <?php _e( 'Persons', 'dokan' ) ?></h2>
        <p><?php _e( 'Set Person Options', 'dokan' ) ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>
    <div class="dokan-section-content dokan-clearfix">
        <div class="dokan-form-group">
            <label for="_wc_booking_min_persons_group" class="form-label"><?php _e( 'Min persons', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'The minimum number of persons per booking.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_booking_min_persons_group', array( 'min' => '0', 'step' => '1', 'value' => max( absint( get_post_meta( $post_id, '_wc_booking_min_persons_group', true ) ), 1 ) ), 'number' ); ?>
        </div>
        <div class="dokan-form-group">
            <label for="_wc_booking_max_persons_group" class="form-label"><?php _e( 'Max persons', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'The maximum number of persons per booking.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_booking_max_persons_group', array( 'min' => '0', 'step' => '1', 'value' => get_post_meta( $post_id, '_wc_booking_max_persons_group', true ) ), 'number' ); ?>
        </div>

        <div class="dokan-form-group">
            <label>
                <input name="_wc_booking_person_cost_multiplier" id="_wc_booking_person_cost_multiplier" value="yes" type="checkbox" <?php checked( get_post_meta( $post_id, '_wc_booking_person_cost_multiplier', true ), true ); ?> > <?php _e( 'Multiply all costs by person count', 'dokan' ); ?>
            </label>
            <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Enable this to multiply the entire cost of the booking (block and base costs) by the person count.', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
        </div>
        <div class="dokan-form-group">
            <label>
                <input name="_wc_booking_person_qty_multiplier" id="_wc_booking_person_qty_multiplier" value="yes" type="checkbox" <?php checked( get_post_meta( $post_id, '_wc_booking_person_qty_multiplier', true ), true ); ?> > <?php _e( 'Count persons as bookings', 'dokan' ); ?>
            </label>
            <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Enable this to multiply the entire cost of the booking (block and base costs) by the person count.', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
        </div>
        <div class="dokan-form-group">
            <label>
                <input name="_wc_booking_has_person_types" id="_wc_booking_has_person_types" value="yes" type="checkbox" <?php checked( get_post_meta( $post_id, '_wc_booking_has_person_types', true ), true ); ?> > <?php _e( 'Enable person types', 'dokan' ); ?>
            </label>
            <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Person types allow you to offer different booking costs for different types of individuals, for example, adults and children.', 'dokan' ); ?>">
                <i class="fa fa-question-circle"></i>
            </span>
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
        </div>
    </div>
</div>
