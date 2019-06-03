<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Dokan_Email_Refund_Request' ) ) :

/**
 * New Product Email.
 *
 * An email sent to the admin when a new Product is created by vendor.
 *
 * @class       Dokan_Email_Refund_Request
 * @version     2.6.6
 * @author      weDevs
 * @extends     WC_Email
 */
class Dokan_Email_Refund_Request extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id               = 'dokan_refund_request';
		$this->title            = __( 'Dokan New Refund Request', 'dokan' );
		$this->description      = __( 'These emails are sent to chosen recipient(s) when a vendor send request for refund', 'dokan' );
                $this->template_html    = 'emails/refund_request.php';
		$this->template_plain   = 'emails/plain/refund_request.php';
                $this->template_base    = DOKAN_PRO_DIR.'/templates/';

		// Triggers for this email
		add_action( 'dokan_refund_request_notification', array( $this, 'trigger' ), 30 );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * Get email subject.
	 * @return string
	 */
	public function get_default_subject() {
            return __( '[{site_name}] A New refund request is made by {seller_name}', 'dokan' );
	}

	/**
	 * Get email heading.
	 * @return string
	 */
	public function get_default_heading() {
            return __( 'New Refund Request from - {seller_name}', 'dokan' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int $product_id The product ID.
	 * @param array $postdata.
	 */
	public function trigger( $order_id ) {

            if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
                return;
            }

            $order                     = wc_get_order( $order_id );
            $seller                    = get_user_by( 'id', dokan_get_current_user_id() );
            $this->object              = $order;
            $this->find['seller_name'] = '{seller_name}';
            $this->find['order_id']    = '{order_id}';
            $this->find['refund_url']  = '{refund_url}';
            $this->find['site_name']   = '{site_name}';
            $this->find['site_url']    = '{site_url}';

            $this->replace['seller_name'] = $seller->display_name;
            $this->replace['order_id']    = $order_id;
            $this->replace['refund_url']  = admin_url( 'admin.php?page=dokan#/refund?status=pending' );
            $this->replace['site_name']   = $this->get_from_name();
            $this->replace['site_url']    = site_url();

            $this->setup_locale();
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
            $this->restore_locale();

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
                    'order'         => $this->object,
                    'email_heading' => $this->get_heading(),
                    'sent_to_admin' => true,
                    'plain_text'    => false,
                    'email'         => $this,
                    'email'         => $this,
                    'data'          => $this->replace
                ), 'dokan/', $this->template_base );
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
                    'order'         => $this->object,
                    'email_heading' => $this->get_heading(),
                    'sent_to_admin' => true,
                    'plain_text'    => true,
                    'email'         => $this,
                    'data'          => $this->replace
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
			'recipient' => array(
				'title'         => __( 'Recipient(s)', 'dokan' ),
				'type'          => 'text',
				'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'dokan' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
				'placeholder'   => '',
				'default'       => '',
				'desc_tip'      => true,
			),
			'subject' => array(
				'title'         => __( 'Subject', 'dokan' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{site_name},{amount},{user_name}</code>' ),
				'placeholder'   => $this->get_default_subject(),
				'default'       => '',
			),
			'heading' => array(
				'title'         => __( 'Email heading', 'dokan' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{site_name},{amount},{user_name}</code>' ),
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

return new Dokan_Email_Refund_Request();
