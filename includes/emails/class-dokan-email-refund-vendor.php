<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Dokan_Email_Refund_Vendor' ) ) :

/**
 * Refund processed email
 *
 * An email sent to the vendor when a refund is processed by admin.
 *
 * @class       Dokan_Email_Refund_Vendor
 * @version     2.6.6
 * @author      weDevs
 * @extends     WC_Email
 */
class Dokan_Email_Refund_Vendor extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id               = 'dokan_vendor_refund';
		$this->title            = __( 'Dokan Refund Processed', 'dokan' );
		$this->description      = __( 'These emails are sent to vendor when a vendor refund request is processed', 'dokan' );
                $this->template_html    = 'emails/refund-seller-mail.php';
		$this->template_plain   = 'emails/plain/refund-seller-mail.php';
                $this->template_base    = DOKAN_PRO_DIR.'/templates/';
                
		// Triggers for this email
		add_action( 'dokan_refund_processed_notification', array( $this, 'trigger' ), 30, 5 );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = 'vendor@ofthe.product';
	}

	/**
	 * Get email subject.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_subject() {
            return __( '[{site_name}] Your refund request was {status}', 'dokan' );
	}

	/**
	 * Get email heading.
	 *
	 * @since  3.1.0
	 * @return string
	 */
	public function get_default_heading() {
            return __( 'Refund request for {order_id} is {status}', 'dokan' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int $product_id The product ID.
	 * @param array $postdata.
	 */
	public function trigger( $seller_mail, $order_id, $status, $refund_amount, $refund_reason ) {
            
            if ( ! $this->is_enabled() ) {
                return;
            }
            
            $seller                    = get_user_by( 'email', $seller_mail );
            $this->object              = $seller;
            $this->find['seller_name'] = '{seller_name}';
            $this->find['amount']      = '{amount}';
            $this->find['reason']      = '{reason}';
            $this->find['order_id']    = '{order_id}';
            $this->find['status']      = '{status}';
            $this->find['order_link']  = '{order_link}';
            $this->find['site_name']   = '{site_name}';
            $this->find['site_url']    = '{site_url}';

            $this->replace['seller_name'] = $seller->display_name;
            $this->replace['amount']      = wc_price( $refund_amount );
            $this->replace['reason']      = $refund_reason;
            $this->replace['order_id']    = $order_id;
            $this->replace['status']      = $status;
            $this->replace['order_link']  = dokan_get_navigation_url() . 'orders/?order_status=wc-refunded';
            $this->replace['site_name']   = $this->get_from_name();
            $this->replace['site_url']    = site_url();

            $this->setup_locale();
            $this->send( $seller_mail, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
                    'seller'        => $this->object,
                    'email_heading' => $this->get_heading(),
                    'sent_to_admin' => true,
                    'plain_text'    => false,
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
                    'seller'        => $this->object,
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
			'subject' => array(
				'title'         => __( 'Subject', 'dokan' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{site_name},{amount},{seller_name},{order_id},{status}</code>' ),
				'placeholder'   => $this->get_default_subject(),
				'default'       => '',
			),
			'heading' => array(
				'title'         => __( 'Email heading', 'dokan' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{site_name},{amount},{seller_name},{order_id},{status}</code>' ),
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

return new Dokan_Email_Refund_Vendor();
