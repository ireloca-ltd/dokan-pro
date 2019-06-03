<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Dokan_Email_Announcement' ) ) :

/**
 * Admin announcement Email to vendors
 *
 * @class       Dokan_Email_Announcement
 * @version     2.6.8
 * @author      weDevs
 * @extends     WC_Email
 */
class Dokan_Email_Announcement extends WC_Email {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id               = 'dokan_announcement';
		$this->title            = __( 'Dokan Announcement', 'dokan' );
		$this->description      = __( 'These emails are sent to a vendor(s) who is are selected in a annoucement ', 'dokan' );
        $this->template_html    = 'emails/announcement.php';
		$this->template_plain   = 'emails/plain/announcement.php';
        $this->template_base    = DOKAN_PRO_DIR.'/templates/';

		// Triggers for this email
		// add_action( 'dokan_after_announcement_saved', array( $this, 'trigger' ), 30, 2 );

		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = 'selecetedvendors@the.announcement';
	}

	/**
	 * Get email subject.
	 * @return string
	 */
	public function get_default_subject() {
        return __( 'A new announcement is made at - {site_name}', 'dokan' );
	}

	/**
	 * Get email heading.
	 * @return string
	 */
	public function get_default_heading() {
        return __( 'New Announcement - {title}', 'dokan' );
	}

	/**
	 * Trigger the this email.
	 */
	public function trigger( $seller_id, $post_id ) {

        if ( ! $this->is_enabled() ) {
            return;
        }

        $seller_info  = get_userdata( $seller_id );

        if ( ! $seller_info ) {
            return;
        }

        $email = $seller_info->user_email;

        $announcement_url = dokan_get_navigation_url( 'announcement/single-announcement' )."$post_id/";
        $post = get_post( $post_id );

        $this->find['title']            = '{title}';
        $this->find['message']          = '{message}';
        $this->find['announcement_url'] = '{announcement_url}';
        $this->find['site_name']        = '{site_name}';
        $this->find['site_url']         = '{site_url}';

        $this->replace['title']            = $post->post_title;
        $this->replace['message']          = $post->post_content;
        $this->replace['announcement_url'] = $announcement_url;
        $this->replace['site_name']        = $this->get_from_name();
        $this->replace['site_url']         = site_url();

        $this->setup_locale();

        $this->send( $email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
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
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
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

			'subject' => array(
				'title'         => __( 'Subject', 'dokan' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{title}, {message}, {site_name}</code>' ),
				'placeholder'   => $this->get_default_subject(),
				'default'       => '',
			),
			'heading' => array(
				'title'         => __( 'Email heading', 'dokan' ),
				'type'          => 'text',
				'desc_tip'      => true,
				/* translators: %s: list of placeholders */
				'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), '<code>{title}, {message}, {site_name}</code>' ),
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

return new Dokan_Email_Announcement();
