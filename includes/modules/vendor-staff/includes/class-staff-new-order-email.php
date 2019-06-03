<?php
/**
 * Class Dokan_Staff_New_Order file
 *
 * @package WooCommerce\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Dokan_Staff_New_Order' ) ) :

    /**
     * New Order Email.
     *
     * An email sent to the admin when a new order is received/paid for.
     *
     * @class       Dokan_Staff_New_Order
     * @version     2.0.0
     * @package     WooCommerce/Classes/Emails
     * @extends     WC_Email
     */
    class Dokan_Staff_New_Order extends WC_Email {

        /**
         * Constructor.
         */
        public function __construct() {
            $this->id             = 'dokan_staff_new_order';
            $this->title          = __( 'Dokan Staff New Order', 'woocommerce' );
            $this->description    = __( 'New order emails are sent to chosen recipient(s) when a new order is received.', 'woocommerce' );
            $this->template_html  = 'emails/staff-new-order.php';
            $this->template_plain = 'emails/plain/staff-new-order.php';
            $this->template_base  = DOKAN_VENDOR_staff_DIR.'/templates/';

            $this->placeholders   = array(
                '{site_title}'   => $this->get_blogname(),
                '{order_date}'   => '',
                '{order_number}' => '',
            );

            // Triggers for this email.
            add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_pending_to_completed_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_failed_to_completed_notification', array( $this, 'trigger' ), 10, 2 );
            add_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $this, 'trigger' ), 10, 2 );

            // Call parent constructor.
            parent::__construct();

            // Other settings.
            $this->recipient = 'staff@email.com';
        }

        /**
         * Get email subject.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_subject() {
            return __( '[{site_title}] New customer order ({order_number}) - {order_date}', 'woocommerce' );
        }

        /**
         * Get email heading.
         *
         * @since  3.1.0
         * @return string
         */
        public function get_default_heading() {
            return __( 'New customer order', 'woocommerce' );
        }

        /**
         * Trigger the sending of this email.
         *
         * @param int            $order_id The order ID.
         * @param WC_Order|false $order Order object.
         */
        public function trigger( $order_id, $order = false ) {
            if ( ! $this->is_enabled() ) {
                return;
            }

            $this->setup_locale();

            if ( $order_id && ! is_a( $order, 'WC_Order' ) ) {
                $order = wc_get_order( $order_id );
            }

            if ( is_a( $order, 'WC_Order' ) ) {
                $this->object                         = $order;
                $this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
                $this->placeholders['{order_number}'] = $this->object->get_order_number();
            }

            $staff_ids  = dokan_get_staff_id_by_order( $order_id );

            if ( empty( $staff_ids ) ) {
                return;
            }

            foreach ( $staff_ids as $staff_id ) {
                if ( user_can( $staff_id, 'dokan_view_order' ) ) {
                    $staff            = get_userdata( $staff_id );
                    $email            = $staff->user_email;
                    $this->order_info = dokan_get_staff_order_details( $order_id, $staff_id );

                    $this->send( $email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
                }
            }

            $this->restore_locale();
        }

        /**
         * Get content html.
         *
         * @access public
         * @return string
         */
        public function get_content_html() {
            return wc_get_template_html(
                $this->template_html, array(
                    'order'         => $this->object,
                    'email_heading' => $this->get_heading(),
                    'sent_to_admin' => true,
                    'plain_text'    => false,
                    'email'         => $this,
                    'order_info'    => $this->order_info,
                ), 'dokan' ,$this->template_base
            );
        }

        /**
         * Get content plain.
         *
         * @access public
         * @return string
         */
        public function get_content_plain() {
            return wc_get_template_html(
                $this->template_plain, array(
                    'order'         => $this->object,
                    'email_heading' => $this->get_heading(),
                    'sent_to_admin' => true,
                    'plain_text'    => true,
                    'email'         => $this,
                    'order_info'    => $this->order_info,
                ), 'dokan/', $this->template_base
            );
        }

        /**
         * Initialise settings form fields.
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled'    => array(
                    'title'   => __( 'Enable/Disable', 'woocommerce' ),
                    'type'    => 'checkbox',
                    'label'   => __( 'Enable this email notification', 'woocommerce' ),
                    'default' => 'yes',
                ),
                'subject'    => array(
                    'title'       => __( 'Subject', 'woocommerce' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    /* translators: %s: list of placeholders */
                    'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
                    'placeholder' => $this->get_default_subject(),
                    'default'     => '',
                ),
                'heading'    => array(
                    'title'       => __( 'Email heading', 'woocommerce' ),
                    'type'        => 'text',
                    'desc_tip'    => true,
                    /* translators: %s: list of placeholders */
                    'description' => sprintf( __( 'Available placeholders: %s', 'woocommerce' ), '<code>{site_title}, {order_date}, {order_number}</code>' ),
                    'placeholder' => $this->get_default_heading(),
                    'default'     => '',
                ),
                'email_type' => array(
                    'title'       => __( 'Email type', 'woocommerce' ),
                    'type'        => 'select',
                    'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
                    'default'     => 'html',
                    'class'       => 'email_type wc-enhanced-select',
                    'options'     => $this->get_email_type_options(),
                    'desc_tip'    => true,
                ),
            );
        }
    }

endif;

return new Dokan_Staff_New_Order();
