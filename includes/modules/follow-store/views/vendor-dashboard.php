<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">
    <?php
        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 1.0.0
         */
        do_action( 'dokan_dashboard_content_before' );
    ?>
    <div class="dokan-dashboard-content">
        <h3 class="entry-title"><?php esc_html_e( 'Store Followers', 'dokan' ); ?></h3>

        <article class="dashboard-content-area">
            <table class="dokan-table dokan-table-striped product-listing-table dokan-inline-editable-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'dokan' ); ?></th>
                        <th><?php esc_html_e( 'Followed At', 'dokan' ); ?></th>
                    </tr>
                </thead>
                <?php if ( empty( $customers ) ): ?>
                    <tbody>
                        <tr>
                            <td colspan="2">
                                <?php _e( 'Your store does not have any follower.', 'dokan' ) ?>
                            </td>
                        </tr>
                    </tbody>
                <?php else: ?>
                    <tbody>
                        <?php foreach ( $customers as $customer ): ?>
                            <tr>
                                <td class="dokan-column-name-with-avatar">
                                    <?php echo get_avatar( $customer->ID, 32 ); ?>

                                    <?php
                                        $name = array(
                                            'first_name' => get_user_meta( $customer->ID, 'first_name', true ),
                                            'last_name'  => get_user_meta( $customer->ID, 'last_name', true ),
                                        );

                                        $name = implode( ' ', $name );
                                        $name = trim( $name );

                                        echo $name ? esc_html( $name ) : esc_html( sprintf( '(%s)', __( 'no name', 'dokan' ) ) );
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $follower = $followers[ $customer->ID ];
                                        $followed_at = $follower->followed_at;
                                        $diff = human_time_diff( strtotime($followed_at), current_time( 'timestamp' ) );

                                        echo esc_html( sprintf( '%s %s', $diff, __( 'ago', 'dokan' ) ) );
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>
            </table>
        </article>
    </div>
</div>

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>