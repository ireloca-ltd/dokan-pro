<?php

/**
* Customer related functionality
*
* @since 2.9.5
*/
class Dokan_Wholesale_Customer {

    /**
     * Load automatically when class initiate
     *
     * @since 2.9.5
     */
    public function __construct() {
        add_action( 'dokan_customer_account_migration_list', [ $this, 'render_migration_html' ], 10 );
    }

    /**
     * Render migration html
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function render_migration_html() {
        $user_id            = get_current_user_id();
        $user               = get_user_by( 'id', $user_id );
        $wholesale_customer = metadata_exists( 'user', $user_id, '_is_dokan_wholesale_customer' );
        dokan_get_template_part( 'wholesale/migration', '', [ 'is_wholesale' => true, 'user' => $user, 'is_wholesale_customer' => $wholesale_customer, 'is_active_wholesale' => $user->has_cap( 'dokan_wholesale_customer' ) ] );
    }
}
