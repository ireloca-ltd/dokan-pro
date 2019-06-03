<?php

/**
* Warranty request releated conversation
*
* @since 1.0.0
*
* @package dokan
*/
class Dokan_RMA_Conversation {

    use Dokan_RMA_Common;


    protected $table_name;

    /**
     * Construct functions
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        global $wpdb;

        $this->table_name = $wpdb->prefix . 'dokan_rma_conversations';
    }

    /**
     * Insert Conversations between two userd
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function insert( $data = [] ) {
        global $wpdb;

        $default = [
            'request_id' => 0,
            'from'       => 0,
            'to'         => 0,
            'message'    => '',
            'created_at' => current_time( 'mysql' )
        ];

        $data = dokan_parse_args( $data, $default );

        $conversation = $wpdb->insert(
            $this->table_name,
            [
                'request_id' => $data['request_id'],
                'from'       => $data['from'],
                'to'         => $data['to'],
                'message'    => $data['message'],
                'created_at' => $data['created_at'],
            ],
            [ '%d', '%d', '%d', '%s', '%s' ]
        );

        $conversation_id = $wpdb->insert_id;

        if ( ! $conversation ) {
            return new WP_Error( 'not-inserted', __( 'Conversation to saved', 'dokan' ) );
        }

        return $conversation_id;
    }

    /**
     * Get conv
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get( $data = [] ) {
        global $wpdb;

        $default = [
            'request_id' => 0,
            'from'       => 0,
            'to'         => 0
        ];

        $data          = dokan_parse_args( $data, $default );
        $conversations = [];

        if ( empty( $data['request_id'] ) ) {
            return new WP_Error( 'no-request-id', __( 'No request id found', 'dokan' ) );
        }

        $request_id = $data['request_id'];
        $results = $wpdb->get_results( "SELECT * FROM {$this->table_name} WHERE `request_id`='$request_id'", ARRAY_A );

        foreach ( $results as $result ) {
            $conversations[] = $this->transform_request_conversation( $result );
        }

        return $conversations;
    }
}
