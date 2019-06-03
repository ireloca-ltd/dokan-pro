<?php
/**
 * Adds and controls pointers for contextual help/tutorials
 *
 * @author   weDevs
 *
 * @since 2.6.6
 *
 * @category Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dokan_Pro_Admin_Pointers Class.
 */
class Dokan_Pro_Admin_Pointers {

        private $screen_id;
        private $pointer;
	/**
	 * Constructor.
	 */
	public function __construct() {
            add_action( 'dokan_after_pointer_setup', array( $this, 'setup_pointers_for_screen' ), 10, 2 );
	}

	/**
	 * Setup pointers for screen.
	 */
	public function setup_pointers_for_screen( $screen, $dokan_pointers ) {

            if ( ! $screen = get_current_screen() ) {
                return;
            }

            $this->pointer = $dokan_pointers;
            $this->screen_id = $screen->id;

            switch ( $screen->id ) {
                case 'dokan_page_dokan-sellers' :
                        $this->all_vendors_tutorial();
                    break;
            }
	}

        /**
         *  Render pointers on Dashboard Page
         */
        public function all_vendors_tutorial() {

            if ( $this->pointer->is_dismissed( $this->screen_id ) ) {
                return;
            }

            $pointers = array(
                    'pointers' => array(
                        'all' => array(
                                'target'       => "li.all a",
                                'next'         => 'active',
                                'next_trigger' => array(
                                    'target' => '.next',
                                    'event'  => 'click',
                                ),
                                'options'      => array(
                                        'content'  => '<h3>' . esc_html__( 'All Vendors', 'dokan' ) . '</h3>' .
                                                      '<p>' . esc_html__( 'You can see all registered vendors of your marketplace with their details and status.', 'dokan' ) . '</p>',
                                        'position' => array(
                                                'edge'  => 'left',
                                                'align' => 'left',
                                        ),
                                ),
                                'next_button' => "<button class='next button button-primary right'>".__( 'Next', 'dokan' )."</button>"
                        ),
                        'active' => array(
                                    'target'       => "li.approved",
                                    'next'         => 'pending',
                                    'next_trigger' => array(
                                        'target' => '.next',
                                        'event'  => 'click',
                                    ),
                                    'options'      => array(
                                            'content'  => '<h3>' . esc_html__( 'Active Vendors', 'dokan' ) . '</h3>' .
                                                          '<p>' . esc_html__( 'All vendors who are actively selling are filtered within this list.', 'dokan' ) . '</p>',
                                            'position' => array(
                                                    'edge'  => 'left',
                                                    'align' => 'left',
                                            ),
                                    ),
                            'next_button' => "<button class='next button button-primary right'>".__( 'Next', 'dokan' )."</button>"
                            ),
                        'pending' => array(
                                    'target'       => "li.pending",
                                    'next'         => 'toggle',
                                    'next_trigger' => array(
                                        'target' => '.next',
                                        'event'  => 'click',
                                    ),
                                    'options'      => array(
                                            'content'  => '<h3>' . esc_html__( 'Pending Vendors', 'dokan' ) . '</h3>' .
                                                          '<p>' . esc_html__( 'This is a list of pending vendors who have registered in the marketplace but not active yet. Make them active to enable selling.', 'dokan' ) . '</p>',
                                            'position' => array(
                                                    'edge'  => 'left',
                                                    'align' => 'left',
                                            ),
                                    ),
                            'next_button' => "<button class='next button button-primary right'>".__( 'Next', 'dokan' )."</button>"
                            ),
                        'toggle' => array(
                                    'target'       => ".switch:first",
                                    'next'         => 'search',
                                    'next_trigger' => array(
                                        'target' => '.next',
                                        'event'  => 'click',
                                    ),
                                    'options'      => array(
                                            'content'  => '<h3>' . esc_html__( 'Toggle Status', 'dokan' ) . '</h3>' .
                                                          '<p>' . esc_html__( 'You can toggle vendor status simply by making them active or inactive from here.', 'dokan' ) . '</p>',
                                            'position' => array(
                                                    'edge'  => 'right',
                                                    'align' => 'right',
                                            ),
                                    ),
                            'next_button' => "<button class='next button button-primary right'>".__( 'Next', 'dokan' )."</button>"
                            ),
                        'search' => array(
                                    'target'       => "#search-input",
                                    'options'      => array(
                                            'content'  => '<h3>' . esc_html__( 'Search Vendors', 'dokan' ) . '</h3>' .
                                                          '<p>' . esc_html__( 'You can search vendors using Username, Shopname or Email.', 'dokan' ) . '</p>',
                                            'position' => array(
                                                    'edge'  => 'top',
                                                    'align' => 'left',
                                            ),
                                    ),
                            ),
                    ),
            );

            $this->pointer->enqueue_pointers( apply_filters( 'dokan_pointer_'.$this->screen_id, $pointers ) );

            $this->pointer->dismiss_screen( $this->screen_id );
        }
}

new Dokan_Pro_Admin_Pointers();
