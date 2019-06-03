<?php

/**
 * REST API Coupons controller
 *
 * Handles requests to the /coupons endpoint.
 *
 * @author   Dokan
 * @category API
 * @package  Dokan/API
 * @since    2.8
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

class Dokan_REST_Coupon_Controller extends Dokan_REST_Controller {

    /**
     * Endpoint namespace.
     *
     * @var string
     */
    protected $namespace = 'dokan/v1';

    /**
     * Route name
     *
     * @var string
     */
    protected $base = 'coupons';


    /**
     * Post type.
     *
     * @var string
     */
    protected $post_type = 'shop_coupon';

    /**
     * Post type.
     *
     * @var string
     */
    protected $post_status = array( 'publish' );

    /**
     * Register all routes related with coupons
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_items' ),
                'permission_callback' => array( $this, 'get_coupon_permission_check' ),
                'args'                => $this->get_collection_params(),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_item' ),
                'permission_callback' => array( $this, 'create_coupon_permission_check' ),
                'args'                => array_merge( $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ), array(
                    'code' => array(
                        'description' => __( 'Coupon code.', 'dokan' ),
                        'required'    => true,
                        'type'        => 'string',
                    ),
                ) ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)', array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the resource.', 'dokan' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_item' ),
                'permission_callback' => array( $this, 'get_coupon_permission_check' ),
                'args'                => array(
                    'context'         => $this->get_context_param( array( 'default' => 'view' ) ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_item' ),
                'permission_callback' => array( $this, 'edit_coupon_permission_check' ),
                'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::EDITABLE ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_item' ),
                'permission_callback' => array( $this, 'delete_coupon_permission_check' ),
            ),
            'schema' => array( $this, 'get_public_item_schema' ),
        ) );

    }

    /**
     * Get Coupon by id
     *
     * @param type $id
     *
     * @return WC_Coupon
     */
    protected function get_object( $data ) {

        if ( is_object( $data ) ) {
            return new WC_Coupon( $data->ID );
        }

        return new WC_Coupon( $data );
    }

      /**
     * validation_before_create_product
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function validation_before_create_item( $request ) {
        $store_id = dokan_get_current_user_id();

        if ( empty( $store_id ) ) {
            return new WP_Error( 'no_store_found', __( 'No seller found' ), array( 'status' => 404 ) );
        }

        if ( ! empty( $request['id'] ) ) {
            return new WP_Error( "dokan_rest_{$this->post_type}_exists", sprintf( __( 'Cannot create existing %s.', 'dokan' ), 'product' ), array( 'status' => 400 ) );
        }

        if ( empty( $request['code'] ) ) {
            return new WP_Error( "dokan_coupon_no_code_found", sprintf( __( 'Copoun code must be required', 'dokan' ), 'product' ), array( 'status' => 404 ) );
        }

        if ( empty( $request['amount'] ) ) {
            return new WP_Error( "dokan_coupon_amount", __( 'Coupon amount must be required', 'dokan' ), array( 'status' => 404 ) );
        }

        if ( empty( $request['product_ids'] ) ) {
            return new WP_Error( "dokan_coupon_product_ids", __( 'Please sepecific product ids', 'dokan' ), array( 'status' => 404 ) );
        }

        return true;
    }

    /**
     * Validation before update product
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function validation_before_update_item( $request ) {
        $store_id = dokan_get_current_user_id();

        if ( empty( $store_id ) ) {
            return new WP_Error( 'no_store_found', __( 'No seller found' ), array( 'status' => 404 ) );
        }

        $object = $this->get_object( (int) $request['id'] );

        if ( ! $object || 0 === $object->get_id() ) {
            return new WP_Error( "dokan_rest_{$this->post_type}_invalid_id", __( 'Invalid ID.', 'dokan' ), array( 'status' => 400 ) );
        }

        $product_author = get_post_field( 'post_author', $object->get_id() );

        if ( $store_id != $product_author ) {
            return new WP_Error( "dokan_rest_{$this->post_type}_invalid_id", __( 'Sorry, you have no permission to do this. Since it\'s not your product.', 'dokan' ), array( 'status' => 400 ) );
        }

        return true;
    }

    /**
     * validation_before_delete_item
     *
     * @since 2.8.0
     *
     * @return WP_Error|Boolean
     */
    public function validation_before_delete_item( $request ) {
        $store_id = dokan_get_current_user_id();
        $object   = $this->get_object( (int) $request['id'] );
        $result   = false;

        if ( ! $object || 0 === $object->get_id() ) {
            return new WP_Error( "dokan_rest_{$this->post_type}_invalid_id", __( 'Invalid ID.', 'dokan' ), array( 'status' => 404 ) );
        }

        $product_author = get_post_field( 'post_author', $object->get_id() );

        if ( $store_id != $product_author ) {
            return new WP_Error( "dokan_rest_{$this->post_type}_invalid_id", __( 'Sorry, you have no permission to do this. Since it\'s not your product.', 'dokan' ), array( 'status' => 400 ) );
        }

        return true;
    }

    /**
     * Get coupon permission check
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_coupon_permission_check() {
        return current_user_can( 'dokan_view_coupon_menu' );
    }

    /**
     * Create coupon permission check
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function create_coupon_permission_check() {
        return current_user_can( 'dokan_add_coupon' );
    }

    /**
     * Create coupon permission check
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function edit_coupon_permission_check() {
        return current_user_can( 'dokan_edit_coupon' );
    }

    /**
     * Create coupon permission check
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function delete_coupon_permission_check() {
        return current_user_can( 'dokan_delete_coupon' );
    }

    /**
     * Get item
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function get_item( $request ) {
        $validation = $this->validation_before_update_item( $request );

        if ( is_wp_error( $validation ) ) {
            return $validation;
        }

        return parent::get_item( $request );
    }

    /**
     * Prepare data for response
     *
     * @since 2.8.0
     *
     * @return void
     */
    public function prepare_data_for_response( $object, $request ) {
        $data     = $this->get_formatted_item_data( $object );
        $response      = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $object, $request ) );
        return apply_filters( "dokan_rest_prepare_{$this->post_type}_object", $response, $object, $request );
    }

    /**
     * Get formatted item data.
     *
     * @since  2.8.0
     *
     * @param  WC_Data $object WC_Data instance.
     *
     * @return array
     */
    protected function get_formatted_item_data( $object ) {
        $data = $object->get_data();

        $format_decimal = array( 'amount', 'minimum_amount', 'maximum_amount' );
        $format_date    = array( 'date_created', 'date_modified', 'date_expires' );
        $format_null    = array( 'usage_limit', 'usage_limit_per_user', 'limit_usage_to_x_items' );

        // Format decimal values.
        foreach ( $format_decimal as $key ) {
            $data[$key] = wc_format_decimal( $data[$key], 2 );
        }

        // Format date values.
        foreach ( $format_date as $key ) {
            $datetime            = $data[$key];
            $data[$key]          = wc_rest_prepare_date_response( $datetime, false );
            $data[$key . '_gmt'] = wc_rest_prepare_date_response( $datetime );
        }

        // Format null values.
        foreach ( $format_null as $key ) {
            $data[$key] = $data[$key] ? $data[$key] : null;
        }

        return array(
            'id'                          => $object->get_id(),
            'code'                        => $data['code'],
            'amount'                      => $data['amount'],
            'date_created'                => $data['date_created'],
            'date_created_gmt'            => $data['date_created_gmt'],
            'date_modified'               => $data['date_modified'],
            'date_modified_gmt'           => $data['date_modified_gmt'],
            'discount_type'               => $data['discount_type'],
            'description'                 => $data['description'],
            'date_expires'                => $data['date_expires'],
            'date_expires_gmt'            => $data['date_expires_gmt'],
            'usage_count'                 => $data['usage_count'],
            'individual_use'              => $data['individual_use'],
            'product_ids'                 => $data['product_ids'],
            'excluded_product_ids'        => $data['excluded_product_ids'],
            'usage_limit'                 => $data['usage_limit'],
            'usage_limit_per_user'        => $data['usage_limit_per_user'],
            'limit_usage_to_x_items'      => $data['limit_usage_to_x_items'],
            'free_shipping'               => $data['free_shipping'],
            'product_categories'          => $data['product_categories'],
            'excluded_product_categories' => $data['excluded_product_categories'],
            'exclude_sale_items'          => $data['exclude_sale_items'],
            'minimum_amount'              => $data['minimum_amount'],
            'maximum_amount'              => $data['maximum_amount'],
            'email_restrictions'          => $data['email_restrictions'],
            'used_by'                     => $data['used_by'],
            'meta_data'                   => $data['meta_data'],
        );
    }

    /**
     * Prepare a single coupon for create or update.
     *
     * @param  WP_REST_Request $request Request object.
     * @param  bool            $creating If is creating a new object.
     *
     * @return WP_Error|WC_Data
     */
    protected function prepare_object_for_database( $request, $creating = false ) {
        $id        = isset( $request['id'] ) ? absint( $request['id'] ) : 0;
        $coupon    = new WC_Coupon( $id );
        $schema    = $this->get_item_schema();
        $data_keys = array_keys( array_filter( $schema['properties'], array( $this, 'filter_writable_props' ) ) );

        // Validate required POST fields.
        if ( $creating && empty( $request['code'] ) ) {
            return new WP_Error( 'dokan_rest_empty_coupon_code', sprintf( __( 'The coupon code cannot be empty.', 'dokan' ), 'code' ), array( 'status' => 400 ) );
        }

        // Handle all writable props.
        foreach ( $data_keys as $key ) {
            $value = $request[ $key ];

            if ( ! is_null( $value ) ) {
                switch ( $key ) {
                    case 'code' :
                        $coupon_code  = wc_format_coupon_code( $value );
                        $id           = $coupon->get_id() ? $coupon->get_id() : 0;
                        $id_from_code = wc_get_coupon_id_by_code( $coupon_code, $id );

                        if ( $id_from_code ) {
                            return new WP_Error( 'woocommerce_rest_coupon_code_already_exists', __( 'The coupon code already exists', 'dokan' ), array( 'status' => 400 ) );
                        }

                        $coupon->set_code( $coupon_code );
                        break;
                    case 'meta_data' :
                        if ( is_array( $value ) ) {
                            foreach ( $value as $meta ) {
                                $coupon->update_meta_data( $meta['key'], $meta['value'], isset( $meta['id'] ) ? $meta['id'] : '' );
                            }
                        }
                        break;
                    case 'description' :
                        $coupon->set_description( wp_filter_post_kses( $value ) );
                        break;
                    default :
                        if ( is_callable( array( $coupon, "set_{$key}" ) ) ) {
                            $coupon->{"set_{$key}"}( $value );
                        }
                        break;
                }
            }
        }

        return apply_filters( "dokan_rest_pre_insert_{$this->post_type}_object", $coupon, $request, $creating );
    }

    /**
     * Prepare objects query.
     *
     * @since  2.8.0
     *
     * @param  WP_REST_Request $request Full details about the request.
     *
     * @return array
     */
    protected function prepare_objects_query( $request ) {
        $args = parent::prepare_objects_query( $request );

        if ( ! empty( $request['code'] ) ) {
            $id = wc_get_coupon_id_by_code( $request['code'] );
            $args['post__in'] = array( $id );
        }

        // Get only ids.
        $args['fields'] = 'ids';

        return $args;
    }

    /**
     * Only return writable props from schema.
     *
     * @param  array $schema
     * @return bool
     */
    protected function filter_writable_props( $schema ) {
        return empty( $schema['readonly'] );
    }

    /**
     * Prepare links for the request.
     *
     * @param WC_Data         $object  Object data.
     * @param WP_REST_Request $request Request object.
     *
     * @return array                   Links for the given post.
     */
    protected function prepare_links( $object, $request ) {
        $links = array(
            'self' => array(
                'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->base, $object->get_id() ) ),
            ),
            'collection' => array(
                'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->base ) ),
            ),
        );

        return $links;
    }


    /**
     * Get the Coupon's schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() {
        $schema = array(
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => $this->post_type,
            'type'       => 'object',
            'properties' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.', 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'code' => array(
                    'description' => __( 'Coupon code.', 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'amount' => array(
                    'description' => __( 'The amount of discount. Should always be numeric, even if setting a percentage.', 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'date_created' => array(
                    'description' => __( "The date the coupon was created, in the site's timezone.", 'dokan' ),
                    'type'        => 'date-time',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'date_created_gmt' => array(
                    'description' => __( 'The date the coupon was created, as GMT.', 'dokan' ),
                    'type'        => 'date-time',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'date_modified' => array(
                    'description' => __( "The date the coupon was last modified, in the site's timezone.", 'dokan' ),
                    'type'        => 'date-time',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'date_modified_gmt' => array(
                    'description' => __( 'The date the coupon was last modified, as GMT.', 'dokan' ),
                    'type'        => 'date-time',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'discount_type' => array(
                    'description' => __( 'Determines the type of discount that will be applied.', 'dokan' ),
                    'type'        => 'string',
                    'default'     => 'fixed_cart',
                    'enum'        => array_keys( wc_get_coupon_types() ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'description' => array(
                    'description' => __( 'Coupon description.', 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'date_expires' => array(
                    'description' => __( "The date the coupon expires, in the site's timezone.", 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'date_expires_gmt' => array(
                    'description' => __( "The date the coupon expires, as GMT.", 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'usage_count' => array(
                    'description' => __( 'Number of times the coupon has been used already.', 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'individual_use' => array(
                    'description' => __( 'If true, the coupon can only be used individually. Other applied coupons will be removed from the cart.', 'dokan' ),
                    'type'        => 'boolean',
                    'default'     => false,
                    'context'     => array( 'view', 'edit' ),
                ),
                'product_ids' => array(
                    'description' => __( "List of product IDs the coupon can be used on.", 'dokan' ),
                    'type'        => 'array',
                    'items'       => array(
                        'type'    => 'integer',
                    ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'excluded_product_ids' => array(
                    'description' => __( "List of product IDs the coupon cannot be used on.", 'dokan' ),
                    'type'        => 'array',
                    'items'       => array(
                        'type'    => 'integer',
                    ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'usage_limit' => array(
                    'description' => __( 'How many times the coupon can be used in total.', 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                ),
                'usage_limit_per_user' => array(
                    'description' => __( 'How many times the coupon can be used per customer.', 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                ),
                'limit_usage_to_x_items' => array(
                    'description' => __( 'Max number of items in the cart the coupon can be applied to.', 'dokan' ),
                    'type'        => 'integer',
                    'context'     => array( 'view', 'edit' ),
                ),
                'free_shipping' => array(
                    'description' => __( 'If true and if the free shipping method requires a coupon, this coupon will enable free shipping.', 'dokan' ),
                    'type'        => 'boolean',
                    'default'     => false,
                    'context'     => array( 'view', 'edit' ),
                ),
                'product_categories' => array(
                    'description' => __( "List of category IDs the coupon applies to.", 'dokan' ),
                    'type'        => 'array',
                    'items'       => array(
                        'type'    => 'integer',
                    ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'excluded_product_categories' => array(
                    'description' => __( "List of category IDs the coupon does not apply to.", 'dokan' ),
                    'type'        => 'array',
                    'items'       => array(
                        'type'    => 'integer',
                    ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'exclude_sale_items' => array(
                    'description' => __( 'If true, this coupon will not be applied to items that have sale prices.', 'dokan' ),
                    'type'        => 'boolean',
                    'default'     => false,
                    'context'     => array( 'view', 'edit' ),
                ),
                'minimum_amount' => array(
                    'description' => __( 'Minimum order amount that needs to be in the cart before coupon applies.', 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'maximum_amount' => array(
                    'description' => __( 'Maximum order amount allowed when using the coupon.', 'dokan' ),
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
                'email_restrictions' => array(
                    'description' => __( 'List of email addresses that can use this coupon.', 'dokan' ),
                    'type'        => 'array',
                    'items'       => array(
                        'type'    => 'string',
                    ),
                    'context'     => array( 'view', 'edit' ),
                ),
                'used_by' => array(
                    'description' => __( 'List of user IDs (or guest email addresses) that have used the coupon.', 'dokan' ),
                    'type'        => 'array',
                    'items'       => array(
                        'type'    => 'integer',
                    ),
                    'context'     => array( 'view', 'edit' ),
                    'readonly'    => true,
                ),
                'meta_data' => array(
                    'description' => __( 'Meta data.', 'dokan' ),
                    'type'        => 'array',
                    'context'     => array( 'view', 'edit' ),
                    'items'       => array(
                        'type'       => 'object',
                        'properties' => array(
                            'id' => array(
                                'description' => __( 'Meta ID.', 'dokan' ),
                                'type'        => 'integer',
                                'context'     => array( 'view', 'edit' ),
                                'readonly'    => true,
                            ),
                            'key' => array(
                                'description' => __( 'Meta key.', 'dokan' ),
                                'type'        => 'string',
                                'context'     => array( 'view', 'edit' ),
                            ),
                            'value' => array(
                                'description' => __( 'Meta value.', 'dokan' ),
                                'type'        => 'mixed',
                                'context'     => array( 'view', 'edit' ),
                            ),
                        ),
                    ),
                ),
            ),
        );
        return $this->add_additional_fields_schema( $schema );
    }

    /**
     * Get the query params for collections of attachments.
     *
     * @return array
     */
    public function get_collection_params() {
        $params = parent::get_collection_params();

        $params['code'] = array(
            'description'       => __( 'Limit result set to resources with a specific code.', 'dokan' ),
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'validate_callback' => 'rest_validate_request_arg',
        );

        return $params;
    }

}
