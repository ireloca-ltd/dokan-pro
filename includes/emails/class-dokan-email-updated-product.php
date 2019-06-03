<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dokan_Email_Updated_Product' ) ) :

/**
 * Update product to pending status email
 *
 * An email sent to the admin when a new Product is edited by vendor and set to pending status.
 *
 * @class       Dokan_Email_Updated_Product
 * @version     2.6.6
 * @author      weDevs
 * @extends     WC_Email
 */
class Dokan_Email_Updated_Product extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id               = 'updated_product_pending';
		$this->title            = __( 'Dokan Updated Pending Product', 'dokan' );
		$this->description      = __( 'Pending Product emails are sent to chosen recipient(s) when a published product is updated by vendors.', 'dokan' );
                $this->template_html    = 'emails/product-updated-pending.php';
		$this->template_plain   = 'emails/plain/product-updated-pending.php';
                $this->template_base    = DOKAN_PRO_DIR.'/templates/';
                
		// Triggers for this email
		add_action( 'dokan_edited_product_pending_notification', array( $this, 'trigger' ), 30, 3 );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
	}

	/**
	 * Get email subject.
	 *
	 * @return string
	 */
	public function get_default_subject() {
            return __( '[{site_title}] A product update is pending from ({seller_name}) - {product_title}', 'dokan' );
	}

	/**
	 * Get email heading.
	 *
	 * @return string
	 */
	public function get_default_heading() {
            return __( '{product_title} updated by Vendor {seller_name}', 'dokan' );
	}

	/**
	 * Trigger the sending of this email.
	 *
	 * @param int $product_id The product ID.
	 * @param array $postdata.
	 */
	public function trigger(  $product, $seller, $category ) {
            
            if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
                return;
            }
            $category_name = $category ? reset( $category ) : 'N/A';
            if ( is_a( $product, 'WC_Product' ) ) {
                $this->object                = $product;
                
                $this->find['product-title'] = '{product_title}';
                $this->find['price']         = '{price}';
                $this->find['seller-name']   = '{seller_name}';
                $this->find['seller_url']    = '{seller_url}';
                $this->find['category']      = '{category}';
                $this->find['product_link']  = '{product_link}';
                $this->find['site_name']     = '{site_name}';
                $this->find['site_url']      = '{site_url}';

                $this->replace['product-title'] = $product->get_title();
                $this->replace['price']         = $product->get_price();
                $this->replace['seller-name']   = $seller->display_name;
                $this->replace['seller_url']    = dokan_get_store_url( $seller->ID );
                $this->replace['category']      = $category_name;
                $this->replace['product_link']  = admin_url( 'post.php?action=edit&post=' . $product->get_id() );
                $this->replace['site_name']     = $this->get_from_name();
                $this->replace['site_url']      = site_url();
            }

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
                    'product'       => $this->object,
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
                    'product'       => $this->object,
                    'email_heading' => $this->get_heading(),
                    'sent_to_admin' => true,
                    'plain_text'    => true,
                    'email'         => $this,
                    'data'          => $this->replace
                ), 'dokan/', $this->template_base );
            return ob_get_clean();
	}

	/**
	 * Initialize settings form fields.
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
				'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{site_title}, {product_title}, {seller_name}</code>' ),
				'placeholder'   => $this->get_default_subject(),
				'default'       => '',
			),
			'heading' => array(
				'title'         => __( 'Email heading', 'dokan' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{site_title}, {product_title}, {seller_name}</code>' ),
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

return new Dokan_Email_Updated_Product();
