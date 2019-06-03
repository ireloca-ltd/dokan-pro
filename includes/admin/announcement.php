<?php
if ( ! class_exists( 'Dokan_Announcement_Background_Process', false ) ) {
    include_once DOKAN_PRO_ADMIN_DIR . '/announcement-background-process.php';
}

/**
 *  Dokan Announcement class for Admin
 *
 *  Announcement for seller
 *
 *  @since 2.1
 *
 *  @author weDevs <info@wedevs.com>
 */
class Dokan_Announcement {

    private $post_type = 'dokan_announcement';

    protected $processor;

    /**
     *  Load automatically all actions
     */
    function __construct() {
        $this->processor = new Dokan_Announcement_Background_Process();

        add_action( 'init', array( $this, 'post_types' ), 20 );
    }

    /**
     * Trigger mail
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function trigger_mail( $sellers, $data ) {
        $announcement_data = $data->get_data();
        $sender_ids = array();

        if ( 'publish' !== $announcement_data['status'] ) {
            return;
        }

        $sender_type = get_post_meta( $announcement_data['id'], '_announcement_type', true );

        if ( 'all_seller' == $sender_type ) {
            $users   = new WP_User_Query( array( 'role' => 'seller' ) );
            $sellers = $users->get_results();

            if ( $sellers ) {
                foreach ( $sellers as $user ) {
                    $sender_ids[] = $user->ID;
                }
            }
        } else {
            $sender_ids = wp_list_pluck( $announcement_data['sender_ids'], 'id' );
        }

        $payload = array();

        foreach ( $sender_ids as $sender_id ) {
            $payload = array(
                'post_id'    => $announcement_data['id'],
                'sender_id' => $sender_id
            );

            $this->processor->push_to_queue( $payload );
        }

        $this->processor->save()->dispatch();
    }

    /**
     * Register Announcement post type
     *
     * @since 2.1
     *
     * @return void
     */
    function post_types() {
        register_post_type( $this->post_type, array(
            'label'           => __( 'Announcement', 'dokan' ),
            'description'     => '',
            'public'          => false,
            'show_ui'         => true,
            'show_in_menu'    => false,
            'capability_type' => 'post',
            'hierarchical'    => false,
            'rewrite'         => array('slug' => ''),
            'query_var'       => false,
            'supports'        => array( 'title', 'editor' ),
            'labels'          => array(
                'name'               => __( 'Announcement', 'dokan' ),
                'singular_name'      => __( 'Announcement', 'dokan' ),
                'menu_name'          => __( 'Dokan Announcement', 'dokan' ),
                'add_new'            => __( 'Add Announcement', 'dokan' ),
                'add_new_item'       => __( 'Add New Announcement', 'dokan' ),
                'edit'               => __( 'Edit', 'dokan' ),
                'edit_item'          => __( 'Edit Announcement', 'dokan' ),
                'new_item'           => __( 'New Announcement', 'dokan' ),
                'view'               => __( 'View Announcement', 'dokan' ),
                'view_item'          => __( 'View Announcement', 'dokan' ),
                'search_items'       => __( 'Search Announcement', 'dokan' ),
                'not_found'          => __( 'No Announcement Found', 'dokan' ),
                'not_found_in_trash' => __( 'No Announcement found in trash', 'dokan' ),
                'parent'             => __( 'Parent Announcement', 'dokan' )
            ),
        ) );
    }

    /**
     * Proce seller announcement data
     *
     * @since  2.1
     *
     * @param  array $announcement_seller
     * @param  integer $post_id
     *
     * @return void
     */
    function process_seller_announcement_data( $announcement_seller, $post_id ) {

        $inserted_seller_id = $this->get_assign_seller( $post_id );

        if ( !empty( $inserted_seller_id ) ) {
            foreach ( $inserted_seller_id as $key => $value) {
                $db[] = $value['user_id'];
            }
        } else {
            $db = array();
        }

        $sellers         = $announcement_seller;
        $existing_seller = $new_seller = $del_seller = array();

        foreach( $sellers as $seller ) {
            if ( in_array( $seller, $db ) ) {
                $existing_seller[] = $seller;
            } else {
                $new_seller[] = $seller;
            }
        }

        $del_seller = array_diff( $db, $existing_seller );

        if ( $del_seller ) {
            $this->delete_assign_seller( $del_seller, $post_id );
        }

        if ( $new_seller ) {
            $this->insert_assign_seller( $new_seller, $post_id );
        }
    }

    /**
     * Get assign seller
     *
     * @since  2.1
     *
     * @param  integer $post_id
     *
     * @return array
     */
    function get_assign_seller( $post_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix.'dokan_announcement';

        $sql = "SELECT `user_id` FROM {$table_name} WHERE `post_id`= $post_id";

        $results = $wpdb->get_results( $sql, ARRAY_A );

        if ( $results ) {
            return $results;
        } else {
            return array();
        }
    }

    /**
     * Insert assing seller
     *
     * @since 2.1
     *
     * @param  array $seller_array
     * @param  integer $post_id
     *
     * @return void
     */
    function insert_assign_seller( $seller_array, $post_id ) {
        global $wpdb;

        $values     = '';
        $table_name = $wpdb->prefix.'dokan_announcement';
        $i          = 0;

        foreach ( $seller_array as $key => $seller_id ) {
            $sep    = ( $i==0 ) ? '':',';
            $values .= sprintf( "%s ( %d, %d, '%s')", $sep, $seller_id, $post_id, 'unread' );

            $i++;
        }

        $sql = "INSERT INTO {$table_name} (`user_id`, `post_id`, `status` ) VALUES $values";
        $wpdb->query( $sql );
    }

    /**
     * Delete assign seller
     *
     * @since  2.1
     *
     * @param  array $seller_array
     * @param  integer $post_id
     *
     * @return void
     */
    function delete_assign_seller( $seller_array, $post_id ) {
        if ( ! is_array( $seller_array ) ) {
            return;
        }

        global $wpdb;

        $table_name = $wpdb->prefix.'dokan_announcement';
        $values     = '';
        $i          = 0;

        foreach ( $seller_array as $key => $seller_id ) {
            $sep    = ( $i == 0 ) ? '' : ',';
            $values .= sprintf( "%s( %d, %d )", $sep, $seller_id, $post_id );

            $i++;
        }

        // $sellers = implode( ',', $seller_array );
        $sql = "DELETE FROM {$table_name} WHERE (`user_id`, `post_id` ) IN ($values)";

        if ( $values ) {
            $wpdb->query( $sql );
        }
    }
}
