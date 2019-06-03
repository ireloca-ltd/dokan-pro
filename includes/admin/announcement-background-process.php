<?php

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WP_Async_Request', false ) ) {
    include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/wp-async-request.php';
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
    include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/wp-background-process.php';
}

if ( ! class_exists( 'Emogrifier', false ) ) {
    include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/class-emogrifier.php';
}

if ( ! class_exists( 'WC_Email', false ) ) {
    include_once dirname( WC_PLUGIN_FILE ) . '/includes/emails/class-wc-email.php';
}

class Dokan_Announcement_Background_Process extends WP_Background_Process {

    /**
     * The constructor
     *
     * @param License_Expiration_Emails $object
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * @var string
     */
    protected $action = 'dokan_announcement_emails';

    /**
     * Task
     *
     * Override this method to perform any actions required on each
     * queue item. Return the modified item for further processing
     * in the next pass through. Or, return false to remove the
     * item from the queue.
     *
     * @param mixed $item Queue item to iterate over
     *
     * @return mixed
     */
    protected function task( $payload ) {
        $seller_id = $payload['sender_id'];
        $post_id   = $payload['post_id'];

        $announcement_email = include( DOKAN_PRO_INC . '/emails/class-dokan-email-announcement.php' );

        if ( ! empty( $seller_id ) ) {
            $announcement_email->trigger( $seller_id, $post_id );
            dokan_log( sprintf( 'Mail send to %d', $seller_id ) );
        }

        return false;
    }

    /**
     * Complete
     *
     * Override if applicable, but ensure that the below actions are
     * performed, or, call parent::complete().
     */
    protected function complete() {
        parent::complete();
        dokan_log( 'Sending process completed' );
    }

}
