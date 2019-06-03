<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Dokan_Rma_Send_Warranty_Request' ) ) :

/**
 * New Product Published Email to vendor.
 *
 * An email sent to the vendor when a warranty request is made by customer.
 *
 * @class       Dokan_Rma_Send_Warranty_Request
 * @version     2.9.3
 * @author      weDevs
 * @extends     WC_Email
 */
class Dokan_Rma_Send_Warranty_Request extends WC_Email {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->id             = 'Dokan_Rma_Send_Warranty_Request';
        $this->title          = __( 'Dokan Send Refund Request to Vendor', 'dokan' );
        $this->description    = __( 'This email send to vendor once customer request for a refund', 'dokan' );

        $this->template_base  = DOKAN_RMA_DIR . '/templates/';
        $this->template_html  = 'emails/send-warranty-request.php';
        $this->template_plain = 'emails/plain/send-warranty-request.php';

        // Triggers for this email
        add_action( 'dokan_rma_send_warranty_request', [ $this, 'trigger' ], 30 );

        // Call parent constructor
        parent::__construct();

        $this->recipient = 'vendor@ofthe.product';
    }

    /**
     * Get email subject.
     *
     * @since  2.9.3
     *
     * @return string
     */
    public function get_default_subject() {
        return __( '[{site_name}] A new refund request is sent by ({customer_name})', 'dokan' );
    }

    /**
     * Get email heading.
     *
     * @since  2.9.3
     *
     * @return string
     */
    public function get_default_heading() {
        return __( 'Refund request is sent by ({customer_name})', 'dokan' );
    }

    /**
     * Trigger the sending of this email.
     *
     * @param array $data
     */
    public function trigger( $data ) {
        if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
            return;
        }

        $this->object = $data;

        $vendor_id = isset( $data['vendor_id'] ) ? $data['vendor_id'] : '';
        $vendor    = dokan()->vendor->get( $vendor_id );
        $email     = $vendor->get_email();

        if ( ! $email ) {
            return;
        }

        $order_id = isset( $data['order_id'] ) ? $data['order_id'] : 0;
        $order    = wc_get_order( $order_id );

        if ( ! $order ) {
            return;
        }

        $this->find['site_name']        = '{site_name}';
        $this->find['customer_name']    = '{customer_name}';
        $this->replace['site_name']     = $this->get_from_name();
        $this->replace['customer_name'] = $order->get_formatted_billing_full_name();

        $this->setup_locale();
        $this->send( $email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        $this->restore_locale();
    }


    /**
     * Get content html.
     *
     * @access public
     *
     * @return string
     */
    public function get_content_html() {
        ob_start();
        wc_get_template( $this->template_html, array(
            'data'          => $this->object,
            'email_heading' => $this->get_heading(),
            'plain_text'    => false,
            'email'         => $this,
            'replace'       => $this->replace
        ), 'dokan/', $this->template_base );
        return ob_get_clean();
    }

    /**
     * Get content plain.
     *
     * @access public
     *
     * @return string
     */
    public function get_content_plain() {
        ob_start();
        wc_get_template( $this->template_html, array(
            'data'          => $this->object,
            'email_heading' => $this->get_heading(),
            'plain_text'    => true,
            'email'         => $this,
            'replace'       => $this->replace
        ), 'dokan/', $this->template_base );
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
                'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{customer_name}</code>' ),
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

return new Dokan_Rma_Send_Warranty_Request();
