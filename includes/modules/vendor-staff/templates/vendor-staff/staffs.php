<?php
/**
 *  Dokan Dashboard staffs Template
 *
 *  Load staffs related template
 *
 *  @since 2.4
 *
 *  @package dokan
 */
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
        do_action( 'dokan_staffs_content_before' );

    ?>

    <div class="dokan-dashboard-content dokan-staffs-content">

        <header class="dokan-dashboard-header">
            <span class="left-header-content">
                <h1 class="entry-title">
                    <?php _e( 'Staff', 'dokan' ); ?>

                    <span class="left-header-content dokan-right">
                        <a href="<?php echo add_query_arg( array( 'view' => 'add_staffs' ), dokan_get_navigation_url( 'staffs' ) ); ?>" class="dokan-btn dokan-btn-theme dokan-right"><i class="fa fa-user">&nbsp;</i> <?php _e( 'Add new staff', 'dokan' ); ?></a>
                    </span>
                </h1>
            </span>
            <div class="dokan-clearfix"></div>
        </header><!-- .entry-header -->

        <?php

            /**
             *  dokan_staffs_content_inside_before hook
             *
             *  @hooked show_seller_enable_message
             *
             *  @since 2.4
             */
            do_action( 'dokan_staffs_content_inside_before' );
        ?>


        <article class="dokan-staffs-area">

            <?php
                if ( isset( $_GET['message'] ) && $_GET['message'] == 'deleted' ) {
                    dokan_get_template_part('global/dokan-success', '', array( 'deleted' => true, 'message' => __( 'Staff deleted successfully', 'dokan' ) ) );
                }
            ?>

            <?php

                $seller_id    = get_current_user_id();
                $paged        = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
                $limit        = 10;
                $offset       = ( $paged - 1 ) * $limit;
                $staffs       = dokan_get_all_vendor_staffs( array( 'number' => $limit, 'offset' => $offset ) );

                if ( count( $staffs['staffs'] ) > 0 ) {
                    ?>
                    <table class="dokan-table dokan-table-striped vendor-staff-table">
                        <thead>
                            <tr>
                                <th><?php _e( 'Name', 'dokan' ); ?></th>
                                <th><?php _e( 'Email', 'dokan' ); ?></th>
                                <th><?php _e( 'Phone', 'dokan' ); ?></th>
                                <th><?php _e( 'Registered Date', 'dokan' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ( $staffs['staffs'] as $staff ) {
                                ?>
                                <tr >
                                    <td>
                                        <?php
                                            $delete_url =  wp_nonce_url( add_query_arg( array( 'action' => 'delete_staff', 'staff_id' => $staff->ID ), dokan_get_navigation_url( 'staffs' ) ), 'staff_delete_nonce', '_staff_delete_nonce' );
                                            $edit_url   = add_query_arg( array( 'view' => 'add_staffs', 'action' => 'edit', 'staff_id' => $staff->ID ), dokan_get_navigation_url( 'staffs' ) );
                                            $manage_permission_url = add_query_arg( array( 'view' => 'manage_permissions', 'action' => 'manage', 'staff_id' => $staff->ID ), dokan_get_navigation_url( 'staffs' ) );
                                        ?>

                                        <?php echo sprintf( '<a href="%s">%s</a>', esc_url( $edit_url ), $staff->display_name ); ?>
                                        <div class="row-actions">

                                            <?php if ( current_user_can( 'seller' ) ): ?>
                                                <span class="edit"><a href="<?php echo $edit_url; ?>"><?php _e( 'Edit', 'dokan' ); ?></a> | </span>
                                            <?php endif; ?>

                                            <?php if ( current_user_can( 'seller' ) ): ?>
                                                <span class="delete"><a  href="<?php echo $delete_url; ?>"  onclick="return confirm('<?php esc_attr_e( 'Are you sure want to delete', 'dokan' ); ?>');"><?php _e( 'Delete', 'dokan' ); ?></a> | </span>
                                            <?php endif ?>

                                            <?php if ( current_user_can( 'seller' ) ): ?>
                                                <span class="permission"><a  href="<?php echo $manage_permission_url; ?>" ><?php _e( 'Manage Permissions', 'dokan' ); ?></a></span>
                                            <?php endif ?>
                                        </div>
                                    </td>
                                    <td><?php echo $staff->user_email; ?></td>
                                    <td><?php echo get_user_meta( $staff->ID, '_staff_phone', true ); ?></td>
                                    <td><?php echo dokan_date_time_format( $staff->user_registered ); ?></td>
                                </tr>
                            <?php } ?>

                        </tbody>

                    </table>

                    <?php
                    $user_count = $staffs['total_users'];
                    $num_of_pages = ceil( $user_count / $limit );

                    $base_url  = dokan_get_navigation_url( 'staffs' );

                    if ( $num_of_pages > 1 ) {
                        echo '<div class="pagination-wrap">';
                        $page_links = paginate_links( array(
                            'current'   => $paged,
                            'total'     => $num_of_pages,
                            'base'      => $base_url. '%_%',
                            'format'    => '?pagenum=%#%',
                            'add_args'  => false,
                            'type'      => 'array',
                        ) );

                        echo "<ul class='pagination'>\n\t<li>";
                        echo join("</li>\n\t<li>", $page_links);
                        echo "</li>\n</ul>\n";
                        echo '</div>';
                    }
                    ?>

                <?php } else { ?>

                    <div class="dokan-error">
                        <?php _e( 'No staff found', 'dokan' ); ?>
                    </div>

                <?php } ?>

        </article>

        <style>

            table.vendor-staff-table tbody .row-actions {
                font-size: 12px;
            }
        </style>


        <?php

            /**
             *  dokan_staffs_content_inside_after hook
             *
             *  @since 2.4
             */
            do_action( 'dokan_staffs_content_inside_after' );
        ?>

    </div> <!-- #primary .content-area -->

    <?php

        /**
         *  dokan_dashboard_content_after hook
         *  dokan_staffs_content_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_after' );
        do_action( 'dokan_staffs_content_after' );

    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>

<style>
.vendor-staff-table tbody .row-actions {
   visibility: hidden;
   font-size: 12px;
   color: #ccc;
 }

.vendor-staff-table tbody .row-actions .delete a {
   color: #A05;
 }

 .vendor-staff-table tbody .row-actions .delete a:hover {
   color: red;
 }

.vendor-staff-table tbody tr:hover .row-actions {
   visibility: visible;
 }
</style>
