<?php

/**
 * Update vendor and product geolocation data
 *
 * @since 1.0.0
 */
class Dokan_Geolocation_Update_Location_Data extends Abstract_Dokan_Background_Processes {

    /**
     * Action
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $action = 'Dokan_Geolocation_Update_Location_Data';

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

        if ( 'vendors' === $item['updating'] ) {
            return $this->update_vendors( $item['paged'] );
        } else if ( 'products' === $item['updating'] ) {
            return $this->update_products( $item['paged'] );
        }

        return false;
    }

    /**
     * Update vendors
     *
     * @since 1.0.0
     *
     * @param int $paged
     *
     * @return array
     */
    private function update_vendors( $paged ) {
        $args = array(
            'role'   => 'seller',
            'number' => 50,
            'paged'  => $paged,
        );

        $query = new WP_User_Query( $args );

        $vendors = $query->get_results();

        if ( empty( $vendors ) ) {
            return array(
                'updating' => 'products',
                'paged'    => 1,
            );
        }

        foreach ( $vendors as $vendor ) {
            $dokan_geo_latitude = get_user_meta( $vendor->ID, 'dokan_geo_latitude', true );

            if ( ! empty( $dokan_geo_latitude ) ) {
                continue;
            }

            $profile_settings = get_user_meta( $vendor->ID, 'dokan_profile_settings', true );

            if ( ! empty( $profile_settings['location'] ) && ! empty( $profile_settings['find_address'] ) ) {
                $location = explode( ',', $profile_settings['location'] );

                if ( 2 !== count( $location ) ) {
                    continue;
                }

                update_user_meta( $vendor->ID, 'dokan_geo_latitude', $location[0] );
                update_user_meta( $vendor->ID, 'dokan_geo_longitude', $location[1] );
                update_user_meta( $vendor->ID, 'dokan_geo_public', 1 );
                update_user_meta( $vendor->ID, 'dokan_geo_address', $profile_settings['find_address'] );
            }
        }

        return array(
            'updating' => 'vendors',
            'paged'    => ++$paged,
        );
    }

    /**
     * Update products
     *
     * @since 1.0.0
     *
     * @param int $paged
     *
     * @return array|bool
     */
    private function update_products( $paged ) {
        $args = array(
            'post_type'      => 'product',
            'posts_per_page' => 50,
            'post_status'    => 'any',
            'paged'          => $paged,
        );

        $query = new WP_Query( $args );

        if ( empty( $query->posts ) ) {
            return false;

        } else {
            foreach ( $query->posts as $post ) {
                $dokan_geo_latitude = get_post_meta( $post->ID, 'dokan_geo_latitude', true );

                if ( empty( $dokan_geo_latitude ) ) {
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
            'updating' => 'products',
            'paged'    => ++$paged,
        );
    }
}
