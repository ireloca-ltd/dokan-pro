<?php

class Dokan_Follow_Store_Cron {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_action( 'dokan_follow_store_send_updates', array( $this, 'send_updates' ) );

        if ( ! wp_next_scheduled( 'dokan_follow_store_send_updates' ) ) {
            wp_schedule_event( time(), 'daily', 'dokan_follow_store_send_updates' );
        }
    }

    /**
     * Unschedule cron
     *
     * Fires when module deactivate
     *
     * @since 1.0.0
     *
     * @return void
     */
    public static function unschedule_event() {
        $timestamp = wp_next_scheduled( 'dokan_follow_store_send_updates' );
        wp_unschedule_event( $timestamp, 'dokan_follow_store_send_updates' );
    }

    /**
     * Cron action hook method
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function send_updates() {
        global $wpdb;

        $processor_file = DOKAN_FOLLOW_STORE_INCLUDES . '/class-dokan-follow-store-send-updates.php';

        require_once $processor_file;

        $processor = new Dokan_Follow_Store_Send_Updates();
        $processor->cancel_process();

        $yesterday = date( 'Y-m-d', strtotime( '-24 hours', current_time( 'timestamp' ) ) );
        $from      = $yesterday . ' 00:00:00';
        $to        = $yesterday . ' 23:59:59';

        $args = array(
            'page'  => 1,
            'from'  => $from,
            'to'    => $to,
        );

        $processor->push_to_queue( $args )->dispatch_process( $processor_file );
    }
}
