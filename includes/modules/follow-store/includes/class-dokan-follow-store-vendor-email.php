<?php

class Dokan_Follow_Store_Vendor_Email extends WC_Email {

    /**
     * Store Follower
     *
     * @since 1.0.0
     *
     * @var null|WP_User
     */
    public $follower = null;

    /**
     * Following stores
     *
     * @since 1.0.0
     *
     * @var null|int
     */
    public $vendor = null;

    /**
     * Follow status
     *
     * @since 1.0.1
     *
     * @var null|string
     */
    public $status = null;

    /**
     * Constructor Method
     */
    public function __construct() {
        $this->id             = 'vendor_new_store_follower';
        $this->title          = __( 'Dokan Vendor New Store Follower', 'dokan' );
        $this->description    = __( 'Send email to vendor when there is a new store follower or someone unfollows a vendor.', 'dokan' );
        $this->template_html  = 'follow-store-vendor-email-html.php';
        $this->template_base  = DOKAN_FOLLOW_STORE_VIEWS . '/';
        $this->placeholders   = array(
            '{site_title}'    => $this->get_blogname(),
            '{follower_name}' => '',
        );

        // Call parent constructor
        parent::__construct();

        // Set the email content type text/html
        $this->email_type = 'html';
        $this->recipient  = 'vendor@ofthe.product';

        add_action( 'dokan_follow_store_toggle_status', array( $this, 'trigger' ), 15, 3 );
    }

    /**
     * Email settings
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function init_form_fields() {
        $available_placeholders = '{site_title}, {follower_name}';

        $this->form_fields = array(
            'enabled' => array(
                'title'         => __( 'Enable/Disable', 'dokan' ),
                'type'          => 'checkbox',
                'label'         => __( 'Enable this email', 'dokan' ),
                'default'       => 'yes',
            ),

            'subject' => array(
                'title'         => __( 'Subject', 'dokan' ),
                'type'          => 'text',
                'desc_tip'      => true,
                'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), $available_placeholders ),
                'placeholder'   => $this->get_default_subject(),
                'default'       => $this->get_default_subject(),
            ),

            'heading' => array(
                'title'         => __( 'Email heading', 'dokan' ),
                'type'          => 'text',
                'desc_tip'      => true,
                'description'   => sprintf( __( 'Available placeholders: %s', 'dokan' ), $available_placeholders ),
                'placeholder'   => $this->get_default_heading(),
                'default'       => $this->get_default_heading(),
            ),
        );
    }

    /**
     * Email default subject
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_default_subject() {
        return sprintf( __( '%s, see new updates from %s', 'dokan' ), '{follower_name}', '{site_title}' );
    }

    /**
     * Email default heading
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_default_heading() {
        return sprintf( __( 'Latest updates from %s', 'dokan' ), '{site_title}' );
    }

    /**
     * Send email
     *
     * @since 1.0.0
     *
     * @param WP_User $follower
     * @param array   $vendors
     *
     * @return void
     */
    public function trigger( $vendor_id, $follower_id, $status ) {
        $this->setup_locale();

        if ( ! $this->is_enabled() && ! $this->get_email_recipient() ) {
            return;
        }

        $this->follower = get_userdata( $follower_id );
        $this->vendor   = dokan()->vendor->get( $vendor_id );
        $this->status   = $status;

        $this->placeholders['{follower_name}'] = $this->follower->display_name;

        $this->send( $this->get_email_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

        $this->restore_locale();
    }

    /**
     * Follower email
     *
     * @since 1.0.0
     *
     * @return string|null
     */
    public function get_email_recipient() {
        if ( $this->vendor instanceof Dokan_Vendor && is_email( $this->vendor->get_email() ) ) {
            return $this->vendor->get_email();
        }

        return null;
    }

    /**
     * Email content
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_content() {
        ob_start();
        wc_get_template( $this->template_html, array(
            'email_heading' => $this->get_heading(),
            'sent_to_admin' => false,
            'plain_text'    => false,
            'email'         => $this,
            'data'          => array(
                'follower'  => $this->follower,
                'status'    => $this->status,
            ),
        ), 'dokan/', $this->template_base );
        return ob_get_clean();
    }
}
