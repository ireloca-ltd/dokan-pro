<?php

class Dokan_REST_Store_Category_Controller extends WP_REST_Terms_Controller {

    /**
     * Endpoint namespace.
     *
     * @since 2.9.2
     *
     * @var string
     */
    protected $namespace = 'dokan/v1';

    /**
     * Route name
     *
     * @since 2.9.2
     *
     * @var string
     */
    protected $base = 'store-categories';

    /**
     * Taxonomy key.
     *
     * @since 2.9.2
     *
     * @var string
     */
    protected $taxonomy = 'store_category';

    /**
     * Class constructor
     *
     * @since 2.9.2
     *
     * @return void
     */
    public function __construct() {
        parent::__construct( $this->taxonomy );
        $this->namespace = 'dokan/v1';
        $this->rest_base = $this->base;
    }

    /**
     * Register routes
     *
     * @since 2.9.2
     *
     * @return void
     */
    public function register_routes() {
        parent::register_routes();

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/default-category', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_default_category' ),
                'permission_callback' => array( $this, 'get_items_permissions_check' ),
                'args'                => $this->get_collection_params(),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'set_default_category' ),
                'permission_callback' => array( $this, 'create_item_permissions_check' ),
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );
    }

    /**
     * Get Categories
     *
     * @since 2.9.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function get_items( $request ) {
        $response = parent::get_items( $request );

        $response->header( 'X-WP-Store-Category-Type', dokan_get_option( 'store_category_type', 'dokan_general', 'none' ) );
        $response->header( 'X-WP-Default-Category', dokan_get_default_store_category_id() );

        return $response;
    }

    /**
     * Get default store category
     *
     * @since 2.9.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function get_default_category( $request ) {
        $default_category = dokan_get_default_store_category_id();

        $term = $this->get_term( $default_category );

        $response = $this->prepare_item_for_response( $term, $request );

        return rest_ensure_response( $response );
    }

    /**
     * Set default store category
     *
     * @since 2.9.2
     *
     * @param WP_REST_Request $request
     *
     * @return WP_REST_Response
     */
    public function set_default_category( $request ) {
        $term_id = $request->get_param( 'id' );

        if ( empty( $term_id ) ) {
            return new WP_Error( 'missing_param', sprintf( __( 'Missing param %s', 'dokan' ), 'id' ), array( 'status' => 400 ) );
        }

        $term = $this->get_term( $term_id );

        if ( is_wp_error( $term ) ) {
            return $term;
        }

        dokan_set_default_store_category_id( $term->term_id );

        $response = $this->prepare_item_for_response( $term, $request );

        return rest_ensure_response( $response );
    }
}

