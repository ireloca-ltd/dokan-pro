<div class="dokan-geolocation-options dokan-edit-row dokan-clearfix">
    <div class="dokan-section-heading" data-togglehandler="dokan_geolocation_options">
        <h2><i class="fa fa-map-marker" aria-hidden="true"></i> <?php _e( 'Geolocation', 'dokan' ); ?></h2>

        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
        </a>

        <div class="dokan-clearfix"></div>
    </div>

    <div class="dokan-section-content">
        <div class="dokan-form-group">
            <?php
                dokan_post_input_box(
                    $post_id,
                    '_dokan_geolocation_use_store_settings',
                    array(
                        'value' => $use_store_settings,
                        'label' => __( 'Same as store', 'dokan' ),
                    ),
                    'checkbox'
                );
            ?>
        </div>

        <?php if ( ! $store_has_settings ): ?>
            <div class="dokan-form-group<?php echo ( 'yes' !== $use_store_settings ) ? ' dokan-hide' : ''; ?>" id="dokan-geolocation-product-location-no-store-settings">
                <p class="dokan-error">
                    <?php printf( __( "Your store doesn't have geolocation settings. Please set it first in your <a href=\"%s\" target=\"_blank\">store settings</a>.", 'dokan' ), $store_settings_url ); ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="dokan-form-group<?php echo ( 'yes' === $use_store_settings ) ? ' dokan-hide' : '' ?>" id="dokan-geolocation-product-location">
            <label for="_dokan_geolocation_product_location" class="form-label">
                <?php _e( 'Product Location', 'dokan' ); ?>
            </label>

            <div class="dokan-geolocation-product-location-container">
                <input type="hidden" name="_dokan_geolocation_product_dokan_geo_latitude" value="<?php echo esc_attr( $dokan_geo_latitude ); ?>">
                <input type="hidden" name="_dokan_geolocation_product_dokan_geo_longitude" value="<?php echo esc_attr( $dokan_geo_longitude ); ?>">
                <input type="text" name="_dokan_geolocation_product_dokan_geo_address" value="<?php echo esc_attr( $dokan_geo_address ); ?>" class="dokan-form-control" id="_dokan_geolocation_product_location">

                <?php if ( is_ssl() || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ): ?>
                    <i class="locate-icon" style="background-image: url(<?php echo DOKAN_GEOLOCATION_URL . '/assets/images/locate.svg'; ?>)"></i>
                <?php endif; ?>
            </div>

            <div id="dokan-geolocation-product-location-map"></div>
        </div>
    </div>

    <div class="dokan-clearfix"></div>
</div><!-- .dokan-geolocation-options -->
