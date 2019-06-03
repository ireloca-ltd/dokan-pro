<?php

/**
 * Include Dokan Follow Store template
 *
 * @since 1.0.0
 *
 * @param string $name
 * @param array  $args
 *
 * @return void
 */
function dokan_follow_store_get_template( $name, $args = [] ) {
    dokan_get_template( "$name.php", $args, DOKAN_FOLLOW_STORE_VIEWS, trailingslashit( DOKAN_FOLLOW_STORE_VIEWS ) );
}

/**
 * Follow button labels
 *
 * @since 1.0.0
 *
 * @return array
 */
function dokan_follow_store_button_labels() {
    /**
     * Filter to change the follow button label when not following
     *
     * @since 1.0.0
     *
     * @param $string
     */
    $follow = apply_filters( 'dokan_follow_store_button_label_follow', __( 'Follow', 'dokan' ) );

    /**
     * Filter to change the follow button label when following
     *
     * @since 1.0.0
     *
     * @param $string
     */
    $following = apply_filters( 'dokan_follow_store_button_label_following', __( 'Following', 'dokan' ) );

    /**
     * Filter to change the follow button label to unfollow
     *
     * @since 1.0.0
     *
     * @param $string
     */
    $unfollow = apply_filters( 'dokan_follow_store_button_label_unfollow', __( 'Unfollow', 'dokan' ) );

    return array(
        'follow'    => $follow,
        'following' => $following,
        'unfollow'  => $unfollow,
    );
}

/**
 * Toggle store follow status for a customer
 *
 * @since 1.0.0
 *
 * @param int $vendor_id   Vendor WP User ID
 * @param int $follower_id Follower WP User ID
 *
 * @return string Follow status
 */
function dokan_follow_store_toggle_status( $vendor_id, $follower_id ) {
    global $wpdb;

    $result = $wpdb->get_row( $wpdb->prepare(
          "select *"
        . " from {$wpdb->prefix}dokan_follow_store_followers"
        . " where vendor_id = %d and follower_id = %d"
        . " limit 1",
        $vendor_id,
        $follower_id
    ) );

    $current_time = current_time( 'mysql' );

    if ( empty( $result ) ) {
        $wpdb->insert(
            "{$wpdb->prefix}dokan_follow_store_followers",
            array(
                'vendor_id'   => $vendor_id,
                'follower_id' => $follower_id,
                'followed_at' => $current_time,
            ),
            array(
                '%d', '%d', '%s'
            )
        );

        $status = 'following';

    } else {
        if ( $result->unfollowed_at ) {
            $status = 'following';

            $data = array(
                'followed_at'   => $current_time,
                'unfollowed_at' => null
            );

            $format = array( '%s', '%s' );

        } else {
            $status = 'unfollowed';

            $data = array(
                'unfollowed_at' => $current_time,
            );

            $format = array( '%s' );
        }

        $wpdb->update(
            "{$wpdb->prefix}dokan_follow_store_followers",
            $data,
            array(
                'vendor_id'   => $vendor_id,
                'follower_id' => $follower_id,
            ),
            $format,
            array(
                '%d', '%d'
            )
        );
    }

    /**
     * Action hook after toggle follow status
     *
     * @since 1.0.0
     *
     * @param $vendor_id
     * @param $follower_id
     * @param $status
     * @param $current_time
     */
    do_action( 'dokan_follow_store_toggle_status', $vendor_id, $follower_id, $status, $current_time );

    return $status;
}

/**
 * Is customer following a store
 *
 * @since 1.0.0
 *
 * @param int $vendor_id
 * @param int $follower_id
 *
 * @return bool
 */
function dokan_follow_store_is_following_store( $vendor_id, $follower_id ) {
    global $wpdb;

    $following = $wpdb->get_var(
        $wpdb->prepare(
              "select count(*)"
            . " from {$wpdb->prefix}dokan_follow_store_followers"
            . " where vendor_id = %d"
            . "     and follower_id = %d"
            . "     and unfollowed_at is null"
            . " limit 1",
            $vendor_id,
            $follower_id
        )
    );

    return absint( $following ) ? true : false;
}

/**
 * Check if a follower can use coupon
 *
 * @since 1.0.0
 *
 * @param array     $follower_emails
 * @param WC_Coupon $coupon
 *
 * @return bool
 */
function dokan_follower_can_user_coupon( $follower_emails, $coupon ) {
    if ( ! class_exists( 'WC_Cart' ) ) {
        include_once WC_ABSPATH . 'includes/class-wc-cart.php';
    }

    $cart = new WC_Cart();

    $restrictions = $coupon->get_email_restrictions();

    if ( is_array( $restrictions ) && 0 < count( $restrictions ) && ! $cart->is_coupon_emails_allowed( $follower_emails, $restrictions ) ) {
        return false;
    }

    return true;
}

/**
 * Get arg values for Follow Store button
 *
 * @since 2.9.7
 *
 * @param WP_User $vendor
 * @param array   $button_classes
 *
 * @return array
 */
function dokan_follow_store_get_button_args( $vendor, $button_classes = array() ) {
    $btn_labels = dokan_follow_store_button_labels();

    $customer_id = get_current_user_id();

    $status = null;

    if ( dokan_follow_store_is_following_store( $vendor->ID, $customer_id ) ) {
        $label_current = $btn_labels['following'];
        $status = 'following';
    } else {
        $label_current = $btn_labels['follow'];
    }

    $button_classes = array_merge(
        array( 'dokan-btn', 'dokan-btn-theme', 'dokan-follow-store-button' ),
        $button_classes
    );

    $args = array(
        'label_current'  => $label_current,
        'label_unfollow' => $btn_labels['unfollow'],
        'vendor_id'      => $vendor->ID,
        'status'         => $status,
        'button_classes' => implode( ' ', $button_classes ),
        'is_logged_in'   => $customer_id,
    );

    return $args;
}
