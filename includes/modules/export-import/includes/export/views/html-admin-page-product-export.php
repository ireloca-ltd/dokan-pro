<?php
/**
 * Admin View: Product Export
 */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

wp_enqueue_script( 'dokan-select2' );
wp_enqueue_script( 'wc-product-export' );

$exporter        = new WC_Product_CSV_Exporter();
$product_count   = wp_count_posts( 'product' );
$variation_count = wp_count_posts( 'product' );
$total_rows      = $product_count->publish + $product_count->private + $variation_count->publish + $variation_count->private;
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
            <!--<h1 class="entry-title"><?php esc_html_e( 'Export Products', 'dokan' ); ?></h1>-->

            <div class="woocommerce-exporter-wrapper">
                <form class="woocommerce-exporter">
                    <header>
                        <span class="spinner is-active"></span>
                        <h2><?php esc_html_e( 'Export products to a CSV file', 'dokan' ); ?></h2>
                        <p><?php esc_html_e( 'This tool allows you to generate and download a CSV file containing a list of all products.', 'dokan' ); ?></p>
                    </header>
                    <section>
                        <table class="form-table woocommerce-exporter-options">
                            <tbody>
                                <tr>
                                    <th scope="row">
                                        <label for="woocommerce-exporter-columns"><?php esc_html_e( 'Which columns should be exported?', 'dokan' ); ?></label>
                                    </th>
                                    <td>
                                        <select id="woocommerce-exporter-columns" class="woocommerce-exporter-columns dokan-select2" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all columns', 'dokan' ); ?>">
                                            <?php
                                            foreach ( $exporter->get_default_column_names() as $column_id => $column_name ) {
                                                echo '<option value="' . esc_attr( $column_id ) . '">' . esc_html( $column_name ) . '</option>';
                                            }
                                            ?>
                                            <option value="downloads"><?php esc_html_e( 'Downloads', 'dokan' ); ?></option>
                                            <option value="attributes"><?php esc_html_e( 'Attributes', 'dokan' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="woocommerce-exporter-types"><?php esc_html_e( 'Which product types should be exported?', 'dokan' ); ?></label>
                                    </th>
                                    <td>
                                        <select id="woocommerce-exporter-types" class="woocommerce-exporter-types dokan-select2" style="width:100%;" multiple data-placeholder="<?php esc_attr_e( 'Export all products', 'dokan' ); ?>">
                                            <?php
                                            foreach ( wc_get_product_types() as $value => $label ) {
                                                echo '<option value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
                                            }
                                            ?>
                                            <option value="variation"><?php esc_html_e( 'Product variations', 'dokan' ); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="woocommerce-exporter-meta"><?php esc_html_e( 'Export custom meta?', 'dokan' ); ?></label>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="woocommerce-exporter-meta" value="1" />
                                        <label for="woocommerce-exporter-meta"><?php esc_html_e( 'Yes, export all custom meta', 'dokan' ); ?></label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <progress class="woocommerce-exporter-progress" max="100" value="0"></progress>
                    </section>
                    <div class="wc-actions">
                        <input type="submit" class="woocommerce-exporter-button button button-primary" value="<?php esc_attr_e( 'Generate CSV', 'dokan' ); ?>" />
                    </div>
                </form>
            </div>
        </div>
        <?php
        /**
         *  dokan_dashboard_content_inside_after hook
         *
         *  @since 2.4
         */
        do_action( 'dokan_dashboard_content_inside_after' );
        ?>


    </div><!-- .dokan-dashboard-content -->

    <?php
    /**
     *  dokan_dashboard_content_after hook
     *
     *  @since 2.4
     */
    do_action( 'dokan_dashboard_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->

<?php do_action( 'dokan_dashboard_wrap_end' ); ?>

<script>
    window.ajaxurl = dokan.ajaxurl;
</script>
