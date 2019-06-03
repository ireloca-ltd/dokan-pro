<?php
$booking_cost         = get_post_meta( $post_id, '_wc_booking_cost', true );
$booking_block_cost   = get_post_meta( $post_id, '_wc_booking_block_cost', true );
$booking_display_cost = get_post_meta( $post_id, '_wc_display_cost', true )
?>

<div class="cost_fields dokan-edit-row dokan-clearfix">
    <div class="dokan-section-heading" data-togglehandler="cost_fields">
        <h2><i class="fa fa-money" aria-hidden="true"></i> <?php _e( 'Costs', 'dokan' ) ?></h2>
        <p><?php _e( 'Set Costs options', 'dokan' ) ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true" style="margin-top: 9px;"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>
    <div id="bookings_pricing" class="dokan-section-content">
        <div class="dokan-form-group content-half-part">
            <label for="_wc_booking_cost" class="form-label"><?php _e( 'Base cost', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'One-off cost for the booking as a whole.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_booking_cost', array( 'min' => '0', 'step' => '0.1', 'value' => $booking_cost ), 'number' ); ?>
        </div>
        <?php do_action( 'woocommerce_bookings_after_booking_base_cost', $post_id ); ?>

        <div class="dokan-form-group content-half-part">
            <label for="_wc_booking_block_cost" class="form-label"><?php _e( 'Block cost', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'This is the cost per block booked. All other costs (for resources and persons) are added to this.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_booking_block_cost', array( 'min' => '0', 'step' => '0.1', 'value' => $booking_block_cost ), 'number' ); ?>
        </div>
        <?php do_action( 'woocommerce_bookings_after_booking_block_cost', $post_id ); ?>

        <div class="dokan-form-group">
            <label for="_wc_display_cost" class="form-label"><?php _e( 'Display cost', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'The cost is displayed to the user on the frontend. Leave blank to have it calculated for you. If a booking has varying costs, this will be prefixed with the word "from:".', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_display_cost', array( 'min' => '0', 'step' => '0.1', 'value' => $booking_display_cost ), 'number' ); ?>
        </div>
        <?php do_action( 'woocommerce_bookings_after_display_cost', $post_id ); ?>

        <div class="table_grid dokan-booking-range-table">
            <table class="widefat dokan-booking-range-table">
                <thead>
                    <tr>
                        <th class="sort" width="1%">&nbsp;</th>
                        <th><?php _e( 'Range type', 'dokan' ); ?></th>
                        <th><?php _e( 'Range', 'dokan' ); ?></th>
                        <th></th>
                        <th></th>
                        <th><?php _e( 'Base cost', 'dokan' ); ?>&nbsp;
                            <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Enter a cost for this rule. Applied to the booking as a whole.', 'dokan' ); ?>">
                                <i class="fa fa-question-circle"></i>
                            </span>
                        <th><?php _e( 'Block cost', 'dokan' ); ?>&nbsp;
                            <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Enter a cost for this rule. Applied to each booking block.', 'dokan' ); ?>">
                                <i class="fa fa-question-circle"></i>
                            </span>
                            <!--<a class="tips" data-tip="<?php // _e( 'Enter a cost for this rule. Applied to each booking block.', 'dokan' );   ?>">[?]</a></th>-->
                        <th class="remove" width="1%">&nbsp;</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th colspan="8">
                            <a href="#" class="button button-primary add_row dokan-btn dokan-btn-theme" data-row="<?php
                            ob_start();
                            include( 'html-booking-pricing-fields.php' );
                            $html   = ob_get_clean();
                            echo esc_attr( $html );
                            ?>"><?php _e( 'Add Range', 'dokan' ); ?></a>
                            <span class="description"><?php _e( 'All matching rules will be applied to the booking.', 'dokan' ); ?></span>
                        </th>
                    </tr>
                </tfoot>
                <tbody id="pricing_rows">
                    <?php
                    $values = get_post_meta( $post_id, '_wc_booking_pricing', true );
                    if ( !empty( $values ) && is_array( $values ) ) {
                        foreach ( $values as $pricing ) {
                            include( 'html-booking-pricing-fields.php' );

                            /**
                             * Fired just after pricing fields are rendered.
                             *
                             * @since 1.7.4
                             *
                             * @param array		$pricing {
                             * 	The pricing details for bookings
                             *
                             * 	@type string $type	The booking range type
                             * 	@type string $from	The start value for the range
                             * 	@type string $to	The end value for the range
                             * 	@type string $modifier	The arithmetic modifier for block cost
                             * 	@type string $cost	The booking block cost
                             * 	@type string $base_modifier The arithmetic modifier for base cost
                             * 	@type string $base_cost	The base cost
                             * }
                             */
                            do_action( 'woocommerce_bookings_pricing_fields', $pricing );
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
