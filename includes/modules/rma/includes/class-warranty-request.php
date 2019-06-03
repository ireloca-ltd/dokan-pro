<?php

/**
* Warranty Request class
*
* @package dokan
*
* @since 1.0.0
*/
class Dokan_RMA_Warranty_Request {

    use Dokan_RMA_Common;

    /**
     * Get all request
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function all( $data = [] ) {
        $results  = [];
        $requests = dokan_get_warranty_request( $data );

        if ( $requests ) {
            foreach ( $requests as $request ) {
                $results[] = $this->transform_warranty_requests( $request );
            }
        }

        return $results;
    }

    /**
     * Get a single request
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get( $id = 0 ) {
        if ( ! $id ) {
            return new WP_Error( 'no-id', __( 'No request id found', 'dokan' ) );
        }

        $results  = [];
        $request = dokan_get_warranty_request( ['id' => $id ] );

        if ( $request ) {
            $results = $this->transform_warranty_requests( $request );
        }

        return $results;
    }

    /**
     * Save warranty request data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function create( $data = [] ) {
        return dokan_save_warranty_request( $data );
    }

    /**
     * Update status
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update( $data = [] ) {
        return dokan_update_warranty_request( $data );
    }

}
