<?php

if ( !isset( $_GET['booking_id'] ) ) {
    return;
}

$booking_id = $_GET['booking_id'];

$can_edit = false;
$user_id  = dokan_get_current_user_id();

$the_booking = get_wc_booking( $booking_id );
$order_id    = $the_booking->get_order_id();
$sub_orders  = dokan_get_suborder_ids_by( $order_id );

if ( $sub_orders ) {
    foreach ( $sub_orders as $sub_order ) {
        if ( dokan_get_seller_id_by_order( $sub_order->ID ) == $user_id ) {
            $order_id = $sub_order->ID;
            break;
        }
    }
}

if ( dokan_get_seller_id_by_order( $order_id ) == $user_id ) {
    $can_edit = true;
}

if ( !$can_edit ) {
    echo '<div class="dokan-alert dokan-alert-danger">' . __( 'This is not yours, I swear!', 'dokan' ) . '</div>';
    return;
}

$order_url = wp_nonce_url( add_query_arg( array( 'order_id' => $order_id ), dokan_get_navigation_url( 'orders' ) ), 'dokan_view_order' );
$product   = $the_booking->get_product();

$statuses  = array_unique( array_merge( get_wc_booking_statuses( 'user' ), get_wc_booking_statuses( 'cancel') ) );
?>

<header class="dokan-dashboard-header dokan-clearfix">
    <div class="all-orders">
      <a href="/dashboard/orders/"><?php _e( '&larr; Back to all Orders', 'woocommerce-bookings' ); ?></a>
    </div>
    <h4>
        <?php echo sprintf( __('Booking Number: #%d. Order Number:<a href="%s"> #%d </a>' ), $booking_id, $order_url, $order_id  ); ?>
    </h4>
</header><!-- .entry-header -->

<div>
    <article>
        <div class="dokan-clearfix">
            <div class="dokan-w8" style="margin-right:3%;">

                <div class="dokan-clearfix">
                    <div class="" style="width:100%">
                        <div class="dokan-panel dokan-panel-default">
                            <div class="dokan-panel-heading"><strong><?php _e( 'Details' , 'dokan' ) ?></strong></div>
                            <div class="dokan-panel-body">
                                <div class="dokan-booking-general-details">
                                    <ul class="list-unstyled booking-status">

                                        <li>
                                            <span class="dokan-booking-label"><?php _e( 'Booking Status:', 'dokan' ); ?></span>
                                            <label class="dokan-label dokan-booking-label-<?php echo $the_booking->get_status(); ?>"><?php echo get_post_status_object( $the_booking->get_status() )->label ?></label>
                                        </li>

                                        <li class="dokan-hide">
                                            <form id="dokan-booking-status-form" action="" method="post">

                                                <select id="booking_order_status" name="booking_order_status" class="form-control">
                                                    <option value="paid"><?php _e( 'Paid', 'dokan-wc-booking' ); ?></option>
                                                    <option value="cancelled"><?php _e( 'Cancelled', 'dokan-wc-booking' ); ?></option>
                                                </select>

                                                <input type="hidden" name="booking_id" value="<?php echo $the_booking->get_id(); ?>">
                                                <input type="hidden" name="action" value="dokan_wc_booking_change_status">
                                                <input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'dokan_wc_booking_change_status' ); ?>">
                                                <input type="submit" class="dokan-btn dokan-btn-success dokan-btn-sm" name="dokan_change_status" value="<?php _e( 'Update', 'dokan' ); ?>">
                                            </form>
                                        </li>

                                        <li>
                                            <span class="dokan-booking-label"><?php _e( 'Order Date :', 'dokan' ); ?></span>
                                            <?php
                                            $created_date = new WC_DateTime( "@{$the_booking->get_date_created()}", new DateTimeZone( 'UTC' ) );
                                            echo wc_format_datetime( $created_date, get_option( 'date_format' ) . ', ' . get_option( 'time_format' ) );
                                            ?>
                                        </li>

                                        <li>
                                            <span class="dokan-booking-label"><?php _e( 'Booked Product: ', 'dokan' ); ?></span>
                                            <?php
                                            $product_url = add_query_arg( 'product_id',$the_booking->get_product_id(), dokan_get_navigation_url( 'booking/edit' ) );
                                            ?>
                                            <?php echo "<a href='".$product_url."'>".$product->get_title()."</a>";?>
                                        </li>

                                        <?php if ( $the_booking->has_resources() ) :
                                        $resource = $the_booking->get_resource();
                                        $resource_label = get_post_meta( $the_booking->get_product_id(), '_wc_booking_resource_label', true );
                                        ?>
                                        <li><span class="dokan-booking-label"><?php _e( 'Resource(s):', 'dokan') ?></span></li>
                                        <li>
                                            <span class="dokan-booking-label" style="margin-left:20px"><?php echo $resource_label.' :'?></span>
                                            <?php echo  $resource->post_title  ?>
                                        </li>
                                    <?php endif; ?>

                                    <?php if ( $the_booking->has_persons() ) :

                                    $saved_persons = get_post_meta( $the_booking->get_id(), '_booking_persons', true );

                                    if ( ! empty ( $product ) ) {
                                        $person_types = $product->get_person_types();
                                        if ( ! empty( $person_types ) && is_array( $person_types ) ) {
                                            echo '<li><span class="dokan-booking-label">' . __( 'Person(s):', 'dokan' ) . '</span></li>';

                                            foreach ( $person_types as $person_type ) {
                                                $person_count = ( isset( $saved_persons[ $person_type->ID ] ) ? $saved_persons[ $person_type->ID ] : 0 );
                                                echo '<li><span class="dokan-booking-label" style="margin-left:20px">'.$person_type->post_title. ' : </span>'.$person_count. '</li>';
                                            }
                                        } else if ( empty( $person_types ) && ! empty( $saved_persons ) && is_array( $saved_persons ) ) {
                                            foreach ( $saved_persons as $person_id => $person_count ) {
                                                echo '<li><span class="dokan-booking-label"">'. __( 'Person(s): ', 'dokan-wc-booking' ) . '</span></strong>'.$person_count.'</li>';
                                            }
                                        }
                                    }
                                    endif;
                                    ?>
                                    <li>
                                        <span class="dokan-booking-label"><?php _e( 'Booking Start Date:', 'dokan' ); ?></span>
                                        <?php echo $the_booking->get_start_date() ?>
                                    </li>

                                    <li>
                                        <span class="dokan-booking-label"><?php _e( 'Booking End Date:', 'dokan' ); ?></span>
                                        <?php echo $the_booking->get_end_date() ?>
                                    </li>

                                    <li>
                                        <span class="dokan-booking-label"><?php _e( 'Duration:', 'dokan' ); ?></span>
                                        <?php echo $product->get_duration().' '.$product->get_duration_unit(); ?>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="dokan-left">

                </div>

                <div class="clear"></div>


            </div>
        </div>

        <div class="dokan-w4">
            <div class="row dokan-clearfix">
                <div class="" style="width:100%">
                    <div class="dokan-panel dokan-panel-default">
                        <div class="dokan-panel-heading"><strong><?php _e( 'Customer Details', 'dokan' ) ?></strong></div>
                        <div class="dokan-panel-body general-details">
                            <?php
                            $customer_id = get_post_meta( $the_booking->get_id(), '_booking_customer_id', true );
                            $has_data    = false;

                            echo '<table class="booking-customer-details">';

                            if ( $customer_id && ( $user = get_user_by( 'id', $customer_id ) ) ) {
                             echo '<tr>';
                             echo '<th>' . __( 'Name:', 'dokan' ) . '</th>';
                             echo '<td>';
                             if ( $user->last_name && $user->first_name ) {
                               $order = wc_get_order($order_id);
                               $customer_user = absint( get_post_meta( dokan_get_prop( $order, 'id' ), '_customer_user', true ) );
                               $display_name = get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_first_name', true ). ' '. get_post_meta( dokan_get_prop( $order, 'id' ), '_billing_last_name', true );
                               echo $display_name;
                           } else {
                               echo '-';
                           }
                           echo '</td>';
                           echo '</tr>';
                           $order = wc_get_order($order_id);
                           $customer_user = absint( get_post_meta( dokan_get_prop( $order, 'id' ), '_customer_user', true ) );
                           $user_meta = get_userdata($customer_user);
                           $user_roles = $user_meta->roles;
                           if (in_array("dokandar", $user_roles, true) || in_array("vendor_staff", $user_roles, true) || in_array("seller", $user_roles, true)){
                           echo '<tr>';
                           echo '<th>' . __( 'Booking Agent:', 'dokan-wc-booking' ) . '</th>';
                           echo '<td>';
                           $user_id = $order->get_user_id();
                           $meta_key3 = '_staff_phone';
                           $custom_field3 = get_user_meta( $user_id, $meta_key3, true );
                           echo $custom_field3;
                           echo '</td>';
                           echo '</tr>';
                           $has_data = true;
                       }
                     }

                       if ( $order_id && ( $order = wc_get_order( $order_id ) ) ) {
                         echo '<tr>';
                         echo '<th>' . __( 'Address:', 'dokan' ) . '</th>';
                         echo '<td>';
                         if ( $order->get_formatted_billing_address() ) {
                           echo wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) );
                       } else {
                           echo __( 'No billing address set.', 'dokan' );
                       }
                       echo '</td>';
                       echo '</tr>';
                       echo '<tr>';
                       echo '<th>' . __( 'Email:', 'dokan' ) . '</th>';
                       echo '<td>';
                       echo '<a href="mailto:' . esc_attr( $order->get_billing_email() ) . '">' . esc_html( $order->get_billing_email() ) . '</a>';
                       echo '</td>';
                       echo '</tr>';
                       echo '<tr>';
                       echo '<th>' . __( 'Phone:', 'dokan' ) . '</th>';
                       echo '<td>';
                       echo esc_html( $order->get_billing_phone() );
                       echo '</td>';
                       echo '</tr>';
                       echo '<tr class="view">';
                       echo '<th>&nbsp;</th>';
                       echo '<td>';
                       echo '<a class="dokan-btn dokan-btn-sm dokan-btn-theme" target="_blank" href="' . $order_url . '">' . __( 'View Order', 'dokan' ) . '</a>';
                       echo '</td>';
                       echo '</tr>';

                       $has_data = true;
                   }

                   if ( ! $has_data ) {
                     echo '<tr>';
                     echo '<td colspan="2">' . __( 'N/A', 'dokan' ) . '</td>';
                     echo '</tr>';
                 }

                 echo '</table>';
                 ?>

             </div>
         </div>
     </div>

 </div> <!-- .row -->
</div> <!-- .col-md-4 -->
</div>
</article>
</div>
