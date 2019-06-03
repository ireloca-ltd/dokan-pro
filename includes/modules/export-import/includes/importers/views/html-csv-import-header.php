<?php
/**
 * Admin View: Header
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
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
    ?>

    <div class="dokan-dashboard-content">

        <?php
        /**
         *  dokan_dashboard_content_before hook
         *
         *  @hooked show_seller_dashboard_notice
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_inside_before' );
        ?>
        <div class="dashboard-content-area wrap woocommerce">
            <h1 class="entry-title"><?php esc_html_e( 'Import Products', 'dokan' ); ?></h1>

            <div class="woocommerce-progress-form-wrapper">
