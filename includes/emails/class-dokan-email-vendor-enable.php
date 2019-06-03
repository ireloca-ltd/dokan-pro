<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Dokan_Email_Vendor_Enable' ) ) :

    /**
    * Admin announcement Email to vendors
    *
    * @class       Dokan_Email_Vendor_Enable
    * @version     2.7.6
    * @author      weDevs
    * @extends     WC_Email
    */
    class Dokan_Email_Vendor_Enable extends WC_Email {

        /**
        * Constructor.
        */
        public function __construct() {
            $this->id               = 'dokan_email_vendor_enable';
            $this->title            = __( 'Dokan Vendor Enable', 'dokan' );
            $this->description      = __( 'This email is set to a vendor when he enabled from admin settings', 'dokan' );
            $this->template_html    = 'emails/vendor-enabled.php';
            $this->template_plain   = 'emails/plain/vendor-enabled.php';
            $this->template_base    = DOKAN_PRO_DIR.'/templates/';

            // Triggers for this email
            add_action( 'dokan_vendor_enabled', array( $this, 'trigger' ) );

            // Call parent constructor
            parent::__construct();

            $this->recipient = 'vendor@ofthe.product';
        }

        /**
        * Get email subject.
        * @return string
        */
        public function get_default_subject() {
            return __( '[{site_name}] Your account is activated', 'dokan' );
        }

        /**
        * Get email heading.
        * @return string
        */
        public function get_default_heading() {
            return __( 'Your vendor account is activated', 'dokan' );
        }

        /**
        * Trigger the this email.
        */
        public function trigger( $seller_id ) {
            if ( ! $this->is_enabled() ) {
                return;
            }

            $this->setup_locale();

            $seller = get_user_by( 'ID', $seller_id );
            $seller_email = $seller->user_email;

            $this->find['site_name']        = '{site_name}';
            $this->find['first_name']       = '{first_name}';
            $this->find['last_name']        = '{last_name}';
            $this->find['display_name']     = '{display_name}';

            $this->replace['site_name']     = $this->get_from_name();
            $this->replace['first_name']    = $seller->first_name;
            $this->replace['last_name']     = $seller->last_name;
            $this->replace['display_name']  = $seller->display_name;

            $this->send( $seller_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

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

return new Dokan_Email_Vendor_Enable();
