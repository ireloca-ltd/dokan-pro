<?php

class Dokan_Follow_Store_Send_Updates extends Abstract_Dokan_Background_Processes {

    /**
     * Process action id
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $action = 'Dokan_Follow_Store_Send_Updates';

    /**
     * Following vendors
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $vendors = array();

    /**
     * Perform task
     *
     * @since 1.0.0
     *
     * @param array $args
     *
     * @return array
     */
    public function task( $args ) {
        $followers = $this->get_followers( $args['page'] );

        if ( empty( $followers ) ) {
            return false;
        }

        foreach ( $followers as $follower_id ) {
            $follower    = get_user_by( 'ID', $follower_id );
            $vendors_ids = $this->get_following_vendors( $follower_id );

            $vendors  = array();

            if ( ! empty( $vendors_ids ) ) {
                foreach ( $vendors_ids as $vendor_id ) {
                    if ( array_key_exists( $vendor_id , $this->vendors ) ) {
                        $vendor = $this->vendors[ $vendor_id ];
                    } else {
                        $vendor = dokan()->vendor->get( $vendor_id );
                        $this->vendors[ $vendor_id ] = $vendor;
                    }

                    if ( empty( $vendor->id ) ) {
                        continue;
                    }

                    $products = $this->get_vendor_new_products( $vendor_id, $args );
                    $coupons  = $this->get_vendor_new_coupons( $follower, $vendor_id, $args );

                    if ( ! empty( $products->posts ) || ! empty( $coupons ) ) {
                        $vendor->products = $products;
                        $vendor->coupons = $coupons;
                        $vendors[] = $vendor;
                    }
                }

                // Call WC_Emails once
                wc()->mailer();

                if ( ! empty( $vendors ) ) {
                    do_action( 'dokan_follow_store_send_update_email', $follower, $vendors );
                }
            }
        }

        ++$args['page'];

        return $args;
    }

    /**
     * Fetch active followers with pagination
     *
     * @since 1.0.0
     *
     * @param int $page
     *
     * @return array
     */
    public function get_followers( $page = 1 ) {
        global $wpdb;

        $limit = 1;
        $offset = $page * $limit;

        return $wpdb->get_col( $wpdb->prepare(
              "select follower_id"
            . " from {$wpdb->prefix}dokan_follow_store_followers"
            . " where unfollowed_at is null"
            . " group by follower_id"
            . " limit %d, %d",
            $offset,
            $limit
        ) );
    }

    /**
     * Get following stores for a single follower
     *
     * @since 1.0.0
     *
     * @param int $follower_id
     *
     * @return array
     */
    private function get_following_vendors( $follower_id ) {
        global $wpdb;

        return $wpdb->get_col( $wpdb->prepare(
              "select vendor_id"
            . " from {$wpdb->prefix}dokan_follow_store_followers"
            . " where unfollowed_at is null"
            . "     and follower_id = %d",
            $follower_id
        ) );
    }

    /**
     * Get vendor new products
     *
     * @since 1.0.0
     *
     * @param int $vendor_id
     * @param array $args
     *
     * @return WP_Query
     */
    private function get_vendor_new_products( $vendor_id, $args ) {
        $query_args = array(
            'post_type'   => 'product',
            'author'      => $vendor_id,
            'post_status' => 'publish',
            'posts_per_page' => 3,
            'date_query'  => array(
                'after'     => $args['from'],
                'before'    => $args['to'],
                'inclusive' => true,
            ),
        );

        return new WP_Query( $query_args );
    }

    /**
     * Get vendor coupons
     *
     * @since 1.0.0
     *
     * @param WP_User $follower
     * @param int     $vendor_id
     * @param array   $args
     *
     * @return array
     */
    private function get_vendor_new_coupons( $follower, $vendor_id, $args ) {
        $customer = new WC_Customer( $follower->ID );
        $customer_emails  = array_unique(
            array_filter(
                array_map(
                    'strtolower', array_map(
                        'sanitize_email', array(
                            $customer->get_billing_email(),
                            $follower->user_email,
                        )
                    )
                )
            )
        );

        $query_args = array(
            'post_type'   => 'shop_coupon',
            'author'      => $vendor_id,
            'post_status' => 'publish',
            'date_query'  => array(
                'after'     => $args['from'],
                'before'    => $args['to'],
                'inclusive' => true,
            ),
            'nopaging'    => true,
        );

        $new_coupons = new WP_Query( $query_args );

        $coupons = array();

        if ( $new_coupons->have_posts() ) {
            foreach ( $new_coupons->posts as $post ) {
                $coupon = new WC_Coupon( $post->ID );

                if ( dokan_follower_can_user_coupon( $customer_emails, $coupon ) ) {
                    $coupons[] = $coupon;
                }
            }
        }

        return $coupons;
    }
}
