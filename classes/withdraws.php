<?php

/**
 * Withdraw Fnctionality class
 *
 * @since 2.4
 *
 * @author weDevs <info@wedevs.com>
 */
class Dokan_Pro_Withdraws {

	/**
     * Constructor for the Dokan_Pro_Withdraws class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @return void
     */
	function __construct() {
		add_filter( 'dokan_withdraw_methods', array( $this, 'load_withdraw_method' ), 10 );
	}

    /**
     * Singleton object
     *
     * @staticvar boolean $instance
     *
     * @return \self
     */
    public static function init() {

        static $instance = false;

        if ( !$instance ) {
            $instance = new Dokan_Pro_Withdraws();
        }

        return $instance;
    }

	/**
	 * Load withdraw method
	 *
	 * @since 2.4
	 *
	 * @param  array $methods
	 *
	 * @return array
	 */
	public function load_withdraw_method( $methods ) {
		$methods['skrill'] = array(
            'title'    => __( 'Skrill', 'dokan' ),
            'callback' => array( $this, 'dokan_withdraw_method_skrill' )
        );

        return $methods;
	}

	/**
	 * Callback for Skrill in store settings
	 *
	 * @since 2.4
	 *
	 * @global WP_User $current_user
	 *
	 * @param array $store_settings
	 *
	 * @return void
	 */
	public function dokan_withdraw_method_skrill( $store_settings ) {
	    global $current_user;

	    $email = isset( $store_settings['payment']['skrill']['email'] ) ? esc_attr( $store_settings['payment']['skrill']['email'] ) : $current_user->user_email ;
	    ?>
	    <div class="dokan-form-group">
	        <div class="dokan-w8">
	            <div class="dokan-input-group">
	                <span class="dokan-input-group-addon"><?php _e( 'E-mail', 'dokan' ); ?></span>
	                <input value="<?php echo $email; ?>" name="settings[skrill][email]" class="dokan-form-control email" placeholder="you@domain.com" type="text">
	            </div>
	        </div>
	    </div>
	    <?php
	}

}