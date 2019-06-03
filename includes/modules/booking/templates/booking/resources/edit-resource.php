
<header class="dokan-dashboard-header">
    <h1 class="entry-title"><?php _e( $title , 'dokan' ); ?>
    </h1>
</header><!-- .dokan-dashboard-header -->

    <?php
        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_inside_before' );

        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_inside_after' );

        $resource_id = isset( $_GET[ 'id' ] ) ? $_GET[ 'id' ] : '';
        $resource    = get_post( $resource_id );

    ?>
<?php if ( isset( $_GET['message'] ) && $_GET['message'] == 'success' ) { ?>
        <div class="dokan-message">
            <button type="button" class="dokan-close" data-dismiss="alert">&times;</button>
            <strong><?php _e( 'Success!', 'dokan' ); ?></strong> <?php _e( 'The Resource has been updated successfully.', 'dokan' ); ?>
        </div>

<?php } ?>

<div class="product-edit-new-container">
    <?php
    if ( empty( $resource ) ) {
        $error = __( 'Invalid Resource Id', 'dokan' );
    }

    if ( $resource->post_author != dokan_get_current_user_id() || $resource->post_type != 'bookable_resource' ) {
        $error = __( 'You do not have permission to edit this resource', 'dokan' );
    }

    if ( isset( $error ) ) {
    ?>
        <div class="dokan-alert dokan-alert-danger">
                <a class="dokan-close" data-dismiss="alert">&times;</a>
            <?php echo $error ?>
        </div>
</div>
    <?php
    }else{

    $available_quantity = get_post_meta( $resource_id, 'qty', true );
    ?>
    <form class="dokan-product-edit-form" role="form" method="post">
        <div class="dokan-form-top-area">
            <div class="dokan-form-group">
                <input type="hidden" name="resource_id" value="<?php echo $resource_id; ?>">
                <label for="post_title" class="form-label"><?php _e( 'Resource Title', 'dokan' ); ?></label>
                <?php dokan_post_input_box( $resource_id, 'post_title', array( 'placeholder' => __( 'Resource name..', 'dokan' ), 'value' => $resource->post_title ) ); ?>
            </div>
            <div class="dokan-form-group">
                <label for="_wc_booking_qty" class="form-label"><?php _e( 'Available Quantity', 'dokan' ); ?></label>
                <?php dokan_post_input_box( $resource_id, '_wc_booking_qty', array( 'value' => $available_quantity ), 'number' ); ?>
            </div>
           <div id="bookings_availability" class="bookings_availability availability_fields dokan-edit-row dokan-clearfix">
                        <div class="dokan-clearfix">
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
                                                    include( DOKAN_WC_BOOKING_TEMPLATE_PATH .'booking/html-booking-availability-fields.php' );
                                                    $html   = ob_get_clean();
                                                    echo esc_attr( $html );
                                                    ?>"><?php _e( 'Add Range', 'dokan' ); ?></a>
                                                <span class="description"><?php _e( 'Rules with lower numbers will execute first. Rules further down this table with the same priority will also execute first.', 'dokan' ); ?></span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                    <tbody id="availability_rows">
                                        <?php
                                        $values = get_post_meta( $resource_id, '_wc_booking_availability', true );
                                        if ( !empty( $values ) && is_array( $values ) ) {
                                            foreach ( $values as $availability ) {
                                                include( DOKAN_WC_BOOKING_TEMPLATE_PATH .'booking/html-booking-availability-fields.php' );
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <input type="hidden" name="dokan_booking_resource_update" value="<?php esc_attr_e( 'Save Resource', 'dokan' ); ?>"/>
                <input type="hidden" name="product-type" value="booking"/>
                <input type="submit" name="dokan_booking_resource_update" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" value="<?php esc_attr_e( 'Save Resource', 'dokan' ); ?>"/>
        </div>
    </form>

</div>

    <?php
    }

        /**
         *  dokan_dashboard_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
    ?>
<script type="text/javascript">
    ( function ( $ ) {

        $( document ).ready( function () {

        } );

    } )( jQuery );

</script>