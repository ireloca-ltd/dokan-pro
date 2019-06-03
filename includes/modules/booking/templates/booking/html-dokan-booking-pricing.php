<?php
$booking_cost         = get_post_meta( $post_id, '_wc_booking_cost', true );
$booking_block_cost   = get_post_meta( $post_id, '_wc_booking_block_cost', true );
$booking_display_cost = get_post_meta( $post_id, '_wc_display_cost', true );
$current_display_name = wp_get_current_user()->display_name;
$id = get_the_ID();
$product = wc_get_product( $id );
$product_name = $product->get_name();

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
                <?php
                //Base Currency Switcher
                $currency_codes = array();
                $currency_codes[ get_woocommerce_currency() ] = get_woocommerce_currency();
                $total_number = apply_filters( 'booster_option', 1, get_option( 'wcj_multicurrency_base_price_total_number', 1 ) );
                for ( $i = 1; $i <= $total_number; $i++ ) {
                  $currency_codes[ get_option( 'wcj_multicurrency_base_price_currency_' . $i ) ] = get_option( 'wcj_multicurrency_base_price_currency_' . $i );
                }
                ?>
                <label for="base_currency" class="form-label"><?php _e( 'Base currency', 'dokan' ); ?>
                  <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'Original currency of this product.', 'dokan' ); ?>">
                      <i class="fa fa-question-circle"></i>
                  </span>
                </label>
                      <?php
                      dokan_post_input_box( $post_id, '_wcj_multicurrency_base_price_currency', array( 'options' => $currency_codes ), 'select' ); ?>
                </div>
                <div class="helper_text" style="display:none">
                <p><i>
                <?php _e('<strong>Base currency</strong> is the currency you originally operate this product in.', 'dokan-wc-booking' ); ?><br>
                <?php _e('<u>Example:</u> You operate in the UK and this product is based in the UK, then you would select: <strong>‘GBP’ as the base currency</strong>.', 'dokan-wc-booking' ); ?>
                </i></p>
                <p><i>
                 <?php _e('<u>Example 2:</u> You operate in the EU, but this product is based in Switzerland, then you would select: <strong>‘CHF’ as the base currency</strong>.', 'dokan-wc-booking' ); ?>
                  </i></p>
                  <p><i>
                  <?php _e('Entering the base currency for your product is needed, so that we can use exchange rates to convert your prices into other currencies.. The currency only gets converted, if the customer does not want to pay in the same currency as your product. ', 'dokan-wc-booking' ); ?>
                   </i></p>
                </div>
              <div class="dokan-clearfix"></div>

              <div class="dokan-form-group content-half-part">
                  <label for="_wc_booking_block_cost" class="form-label"><?php _e( 'Block cost', 'dokan' ); ?>
                      <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'This is the cost per block booked. All other costs (for resources and persons) are added to this.', 'dokan' ); ?>">
                          <i class="fa fa-question-circle"></i>
                      </span>
                  </label>
                  <?php dokan_post_input_box( $post_id, '_wc_booking_block_cost', array( 'min' => '0', 'step' => '0.1', 'value' => $booking_block_cost ), 'number' ); ?>
              </div>
              <?php do_action( 'woocommerce_bookings_after_booking_block_cost', $post_id ); ?>

        <div class="dokan-form-group content-half-part advanced_text">
            <label for="_wc_booking_cost" class="form-label"><?php _e( 'Base cost', 'dokan' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'One-off cost for the booking as a whole.', 'dokan' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_booking_cost', array( 'min' => '0', 'step' => '0.1', 'value' => $booking_cost ), 'number' ); ?>
        </div>
        <?php do_action( 'woocommerce_bookings_after_booking_base_cost', $post_id ); ?>

 <div class="helper_text" style="display:none">
  <p><i>
  <?php _e('<strong>Block cost:</strong> In the tab "Other Settings" you assigned a booking duration as a block. Here you now give that block a price. These costs are multiplied by the number of blocks booked (group or persons).', 'dokan-wc-booking' ); ?><br>
  <?php _e('<u>Example:</u> You offer mountain-biking. Each person pays €20 per hour. In this case you would fill in <strong>‘20’ in the block costs</strong>', 'dokan-wc-booking' ); ?>
  </i></p>
  <div class="advanced_text" style="display:none:">
  <p><i>
  <?php _e('<u>Advanced Example:</u> You offer private ski groups (Example 4 from the "Other Settings" tab) for €50 per hour. In this case you would fill in <strong>‘50’ in the block costs</strong> and the system will multiply this €50 for every hour booked by the customer. ', 'dokan-wc-booking' ); ?>
  </i></p>
   <p><i>
   <?php _e('<strong>Base cost</strong> is applied regardless of a customer’s choices on the booking duration.', 'dokan-wc-booking' ); ?><br>
   <?php _e('<u>Example:</u> You offer snow shoe walks in the winter. You need €30 for your guide to reach the starting point and after that the activity costs €25 per hour. In this case you fill in <strong>‘30’ as base costs</strong> and ‘25’ as block costs.', 'dokan-wc-booking' ); ?>
    </i></p>
       </div>
 </div>
         <div class="dokan-form-group" style="display:none;">
            <label for="_wc_display_cost" class="form-label"><?php _e( 'Display cost', 'dokan-wc-booking' ); ?>
                <span class="dokan-tooltips-help tips" title="" data-original-title="<?php _e( 'The cost is displayed to the user on the frontend. Leave blank to have it calculated for you. If a booking has varying costs, this will be prefixed with the word "from:".', 'dokan-wc-booking' ); ?>">
                    <i class="fa fa-question-circle"></i>
                </span>
            </label>
            <?php dokan_post_input_box( $post_id, '_wc_display_cost', array( 'min' => '0', 'step' => '0.1', 'value' => $booking_display_cost ), 'number' ); ?>
        </div>
        <?php do_action( 'woocommerce_bookings_after_display_cost', $post_id ); ?>

        <div class="table_grid dokan-booking-range-table" style="display:none;">
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
