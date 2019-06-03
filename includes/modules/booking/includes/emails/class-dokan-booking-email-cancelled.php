<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Dokan_Email_Booking_Cancelled' ) ) :

/**
 * New Product Published Email to vendor.
 *
 * An email sent to the vendor when a pending Product is published by admin.
 *
 * @class       Dokan_Email_Booking_Cancelled
 * @version     2.6.8
 * @author      weDevs
 * @extends     WC_Email
 */
class Dokan_Email_Booking_Cancelled extends WC_Email {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id               = 'Dokan_Email_Booking_Cancelled_NEW';
        $this->title            = __( 'Dokan Booking Cancelled by Customer', 'dokan' );
        $this->description      = __( 'This email is sent to admin and vendor when booking is cancelled by the customer', 'dokan' );

        $this->template_base    = DOKAN_WC_BOOKING_DIR . '/templates/';
        $this->template_html    = 'emails/customer-booking-cancelled.php';
        $this->template_plain   = 'emails/plain/customer-booking-cancelled.php';


        // Triggers for this email
        add_action( 'woocommerce_bookings_cancelled_booking', array( $this, 'trigger' ), 20, 1 );

        // Call parent constructor
        parent::__construct();

        $this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
    }

    /**
     * Get email subject.
     *
     * @since  3.1.0
     * @return string
     */
    public function get_default_subject() {
        return __( '"{product_title}" has been cancelled', 'dokan' );
    }

    /**
     * Get email heading.
     *
     * @since  3.1.0
     * @return string
     */
    public function get_default_heading() {
        return __( '{product_title} - is Cancelled', 'dokan' );
    }

    /**
     * trigger function.
     *
     * @access public
     * @return void
     */
    public function trigger( $booking_id ) {
        if ( $booking_id ) {

            // Only send the booking email for booking post types, not orders, etc
            if ( 'wc_booking' !== get_post_type( $booking_id ) ) {
                return;
            }

            $this->object = get_wc_booking( $booking_id );

            if ( ! is_object( $this->object ) || ! $this->object->get_order() ) {
                return;
            }

            foreach ( array( '{product_title}', '{order_date}', '{order_number}' ) as $key ) {
                $key = array_search( $key, $this->find );
                if ( false !== $key ) {
                    unset( $this->find[ $key ] );
                    unset( $this->replace[ $key ] );
                }
            }

            if ( $this->object->get_product() ) {
                $this->find[]    = '{product_title}';
                $this->replace[] = $this->object->get_product()->get_title();
            }

            $vendor_id    = dokan_get_seller_id_by_order( wp_get_post_parent_id( $booking_id ) );
            $vendor       = dokan()->vendor->get( $vendor_id );
            $vendor_email = $vendor->get_email();

            if ( $this->object->get_order() ) {
                if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
                    $billing_email = $this->object->get_order()->billing_email;
                    $order_date    = $this->object->get_order()->order_date;
                } else {
                    $billing_email = $this->object->get_order()->get_billing_email();
                    $order_date    = $this->object->get_order()->get_date_created() ? $this->object->get_order()->get_date_created()->date( 'Y-m-d H:i:s' ) : '';
                }

                $this->find[]    = '{order_date}';
                $this->replace[] = date_i18n( wc_date_format(), strtotime( $order_date ) );

                $this->find[]    = '{order_number}';
                $this->replace[] = $this->object->get_order()->get_order_number();

                $this->recipient = get_bloginfo( 'admin_email' ) . ',' . $vendor_email . ',' . $billing_email;
            } else {
                $this->find[]    = '{order_date}';
                $this->replace[] = date_i18n( wc_date_format(), strtotime( $this->object->booking_date ) );

                $this->find[]    = '{order_number}';
                $this->replace[] = __( 'N/A', 'woocommerce-bookings' );

                if ( $this->object->customer_id && ( $customer = get_user_by( 'id', $this->object->customer_id ) ) ) {
                    $this->recipient = get_bloginfo( 'admin_email' ) . ',' . $vendor_email . ',' . $customer->user_email;
                }
            }
        }

        if ( ! $this->is_enabled() || ! $this->get_recipient() )
            return;

        $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
    }


    /**
     * Get content html.
     *
     * @access public
     * @return string
     */
    public function get_content_html() {
        ob_start();
            wc_get_template( $this->template_html, array(
                'booking'       => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false
            ), 'dokan-wc-booking/', $this->template_base );

        return ob_get_clean();
    }

    /**
     * Get content plain.
     *
     * @access public
     * @return string
     */
    public function get_content_plain() {
        ob_start();
            wc_get_template( $this->template_html, array(
                'booking'       => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false
            ), 'dokan-wc-booking/', $this->template_base );

        return ob_get_clean();
    }

    /**
     * Initialise settings form fields.
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title'         => __( 'Enable/Disable', 'dokan' ),
                'type'          => 'checkbox',
                'label'         => __( 'Enable this email notification', 'dokan' ),
                'default'       => 'yes',
            ),

            'subject' => array(
                'title'         => __( 'Subject', 'dokan' ),
                'type'          => 'text',
                'desc_tip'      => true,
                /* translators: %s: list of placeholders */
                'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{blogname}</code>' ),
                'placeholder'   => $this->get_default_subject(),
                'default'       => '',
            ),
            'heading' => array(
                'title'         => __( 'Email heading', 'dokan' ),
                'type'          => 'text',
                'desc_tip'      => true,
                /* translators: %s: list of placeholders */
                'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{product_title}</code>' ),
                'placeholder'   => $this->get_default_heading(),
                'default'       => '',
            ),
            'email_type' => array(
                'title'         => __( 'Email type', 'dokan' ),
                'type'          => 'select',
                'description'   => __( 'Choose which format of email to send.', 'dokan' ),
                'default'       => 'html',
                'class'         => 'email_type wc-enhanced-select',
                'options'       => $this->get_email_type_options(),
                'desc_tip'      => true,
            ),
        );
    }
}

endif;

return new Dokan_Email_Booking_Cancelled();
