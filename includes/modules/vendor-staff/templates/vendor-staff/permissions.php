<?php
/**
 *  Dokan Dashboard Stuffs Template
 *
 *  Load Stuffs related template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
?>
<?php
    $is_edit = ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' && ! empty( $_GET['staff_id'] ) ) ? $_GET['staff_id'] : 0;
?>

<?php do_action( 'dokan_dashboard_wrap_start' ); ?>

<div class="dokan-dashboard-wrap">

    <?php

        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked get_dashboard_side_navigation
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_before' );
        do_action( 'dokan_stuffs_content_before' );

    ?>

    <div class="dokan-dashboard-content dokan-stuffs-content">

        <?php

            /**
             *  dokan_stuffs_content_inside_before hook
             *
             *  @hooked show_seller_enable_message
             *
             *  @since 2.4
             */
            do_action( 'dokan_add_stuffs_content_inside_before' );
        ?>

        <header class="dokan-dashboard-header">
            <span class="left-header-content">
                <h1 class="entry-title">
                    <?php
                     _e( 'Manage Permissions', 'dokan' );
                    ?>

                    <span class="left-header-content dokan-right" style="font-size: 14px;">
                        <a href="<?php echo dokan_get_navigation_url( 'staffs' ); ?>" class="dokan-right"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> <?php _e( 'Back to all Staff', 'dokan' ); ?></a>
                    </span>
                </h1>
            </span>
            <div class="dokan-clearfix"></div>
        </header><!-- .entry-header -->

        <article class="dokan-stuffs-area">
            <?php
            $is_valid = true;
            $staff_id = ! empty( $_GET['staff_id'] ) ? $_GET['staff_id'] : 0;
            $stuff_user = new WP_User( $staff_id );

            $vendor_id = get_user_meta( $staff_id, '_vendor_id', true );
            if ( $staff_id && $vendor_id != get_current_user_id() ) {
                $is_valid = false;
            }
            ?>

            <?php
                if ( isset( $_GET['message'] ) && $_GET['message'] == 'success' ) {
                    dokan_get_template_part('global/dokan-success', '', array( 'deleted' => true, 'message' => __( 'Permission update successfully', 'dokan' ) ) );
                }
            ?>

            <?php if ( $is_valid ): ?>
                <form method="post">
                    <?php
                    $i = 1;
                    $selected_caps = $stuff_user->allcaps;
                    foreach ( dokan_get_all_caps() as $key => $value ) {
                        echo sprintf( '<div class="%s-permission dokan-w6"> <h3>%s</h3>', $key, ucwords( str_replace( '_', ' ', $key ) ) );
                        foreach ( $value as $key=>$cap ) {
                            $checked = ( isset( $selected_caps[$key] ) && $selected_caps[$key] ) ? 'checked' : '';
                            echo sprintf( '<div class="dokan-form-group"><label for="%1$s"><input type="checkbox" name="%1$s" value="1" id="%1$s" %3$s> &nbsp; %2$s</label></div>', $key, $cap, $checked );
                        }
                        echo '</div>';

                        if ( $i%2 == 0 ) {
                            echo '<div class="dokan-clearfix"></div>';
                        }

                        $i++;
                    }
                    ?>
                    <?php wp_nonce_field( 'dokan_manage_staff_permission', '_dokan_manage_staff_permission_nonce' ); ?>
                    <div class="dokan-clearfix"></div>
                    <hr>
                    <input type="submit" class="dokan-right dokan-btn dokan-btn-large dokan-btn-theme" name="update_staff_permission" value="<?php _e( 'Update permission', 'dokan' ) ?>">
                    <div class="dokan-clearfix"></div>
                </form>
            <?php else: ?>
                <div class="dokan-error">
                    <?php echo sprintf( '<p>%s <a href="%s">%s</a></p>', __( 'Invalid Staff. Please visit', 'dokan' ), esc_url( dokan_get_navigation_url( 'staffs' ) ), __( 'Back to list', 'dokan' ) ); ?>
                </div>
            <?php endif ?>

        </article>

        <?php

            /**
             *  dokan_Stuffs_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_Stuffs_content_inside_after' );
        ?>

    </div> <!-- #primary .content-area -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_Stuffs_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_Stuffs_content_after' );

    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>