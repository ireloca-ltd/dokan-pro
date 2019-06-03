<?php

/**
 * Update vendor and product geolocation data
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Update_Product_Location_Data extends Abstract_Dokan_Background_Processes {

    /**
     * Action
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $action = 'Dokan_Geolocation_Update_Product_Location_Data';

    /**
     * Perform updates
     *
     * @since 1.0.0
     *
     * @param mixed $item
     *
     * @return mixed
     */
    public function task( $item ) {
        if ( empty( $item ) ) {
            return false;
        }

        return $this->update_products( $item );
    }

    /**
     * Update products
     *
     * @since 1.0.0
     *
     * @param int $item
     *
     * @return array|bool
     */
    private function update_products( $item ) {
        $args = array(
            'post_type'      => 'product',
            'post_author'    => $item['vendor_id'],
            'posts_per_page' => 50,
            'post_status'    => 'any',
            'paged'          => $item['paged'],
        );

        $query = new WP_Query( $args );

        if ( empty( $query->posts ) ) {
            return false;

        } else {
            foreach ( $query->posts as $post ) {
                $use_store_settings = get_post_meta( $post->ID, '_dokan_geolocation_use_store_settings', true );

                if ( 'no' !== $use_store_settings ) {
                    $vendor_dokan_geo_latitude  = get_user_meta( $post->post_author, 'dokan_geo_latitude', true );
                    $vendor_dokan_geo_longitude = get_user_meta( $post->post_author, 'dokan_geo_longitude', true );
                    $vendor_dokan_geo_address   = get_user_meta( $post->post_author, 'dokan_geo_address', true );

                    if ( ! empty( $vendor_dokan_geo_latitude ) && ! empty( $vendor_dokan_geo_longitude ) ) {
                        update_post_meta( $post->ID, 'dokan_geo_latitude', $vendor_dokan_geo_latitude );
                        update_post_meta( $post->ID, 'dokan_geo_longitude', $vendor_dokan_geo_longitude );
                        update_post_meta( $post->ID, 'dokan_geo_public', 1 );
                        update_post_meta( $post->ID, 'dokan_geo_address', $vendor_dokan_geo_address );
                    }
                }
            }
        }

        return array(
            'vendor_id' => $item['vendor_id'],
            'paged'     => ++$item['paged'],
        );
    }
}
