<form role="search" method="get" class="dokan-geolocation-location-filters" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-scope="<?php echo esc_attr( $scope ); ?>" data-display="<?php echo esc_attr( $display ); ?>">
    <div class="dokan-geolocation-filters-loading" style="text-align: center;">
        <img src="<?php echo DOKAN_PLUGIN_ASSEST.'/images/ajax-loader.gif'; ?>" alt="" style="display: inline-block;">
    </div>

    <div class="dokan-row dokan-clearfix dokan-hide">
        <div class="dokan-geo-filters-column">
            <div class="<?php echo ! $scope ? 'dokan-input-group': ' no-dropdown'; ?>">
                <?php if ( 'vendor' === $scope ): ?>
                    <input type="text" class="dokan-form-control" name="dokan_seller_search" placeholder="<?php echo esc_attr( $placeholders['search_vendors'] ); ?>" value="<?php echo esc_attr( $seller_s ); ?>">
                <?php elseif ( 'product' === $scope ): ?>
                    <input type="text" class="dokan-form-control" name="s" placeholder="<?php echo esc_attr( $placeholders['search_products'] ); ?>" value="<?php echo esc_attr( $s ); ?>">
                <?php else : ?>
                    <input type="text" class="dokan-form-control" name="s" placeholder="<?php echo esc_attr( $placeholders['search_products'] ); ?>" value="<?php echo esc_attr( $s ); ?>">
                    <input type="text" class="dokan-form-control dokan-hide" name="dokan_seller_search" placeholder="<?php echo esc_attr( $placeholders['search_vendors'] ); ?>" value="<?php echo esc_attr( $seller_s ); ?>">
                <?php endif; ?>

                <?php if ( ! $scope ): ?>
                    <div class="dokan-input-group-btn">
                        <span class="dokan-geo-input-group-btn" data-toggle="dokan-geo-dropdown">
                            <span class="dokan-geo-filter-scope"><?php _e( 'Product', 'dokan' ); ?></span> <span class="dokan-geo-caret"></span>
                        </span>

                        <ul class="dokan-geo-dropdown-menu dropdown-menu-right dokan-geo-filter-scope-switch">
                          <li><a href="#" data-switch-scope="product"><?php _e( 'Product', 'dokan' ); ?></a></li>
                          <li><a href="#" data-switch-scope="vendor"><?php _e( 'Vendor', 'dokan' ); ?></a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dokan-geo-filters-column">
            <div class="location-address">
                <input type="text" placeholder="<?php echo esc_attr( $placeholders['location'] ); ?>" value="<?php echo esc_attr( $address ); ?>">

                <?php if ( is_ssl() || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ): ?>
                    <i class="locate-icon dokan-hides" style="background-image: url(<?php echo DOKAN_GEOLOCATION_URL . '/assets/images/locate.svg'; ?>)"></i>
                    <i class="locate-loader dokan-hide" style="background-image: url(<?php echo DOKAN_GEOLOCATION_URL . '/assets/images/spinner.svg'; ?>)"></i>
                <?php endif; ?>
            </div>
        </div>

        <?php if ( 'vendor' !== $scope ): ?>
            <div class="dokan-geo-filters-column dokan-geo-product-categories">
                <?php wc_product_dropdown_categories( $wc_categories_args ); ?>
            </div>
        <?php endif; ?>

        <?php if ( 'product' !== $scope && dokan_is_store_categories_feature_on() ): ?>
            <div class="dokan-geo-filters-column dokan-geo-store-categories">
                <select class="dropdown_product_cat" name="store_category" id="store-category-dropdown">
                    <option value=""><?php echo esc_html( __( 'Select a store category', 'dokan' ) ); ?></option>
                    <?php foreach ( $categories as $category ): ?>
                        <option value="<?php echo esc_attr( $category->slug ); ?>" <?php echo ( $category->slug === $store_category ) ? 'selected' : ''; ?>>
                            <?php echo esc_html( $category->name ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ( ! $scope ): ?>
            <div class="dokan-geo-filters-column">
                <button type="button" class="dokan-btn dokan-geo-filters-search-btn">
                    <?php _e( 'Search', 'dokan' ); ?>
                </button>
            </div>
        <?php endif; ?>

        <div class="dokan-w12 <?php echo ! $scope ? 'dokan-hide' : ''; ?>">
            <div class="range-slider-container dokan-clearfix">
                <span class="dokan-range-slider-value dokan-left">
                    <?php _e( 'Radius', 'dokan' ); ?> <span><?php echo $distance; ?></span><?php echo $slider['unit']; ?>
                </span>

                <input
                    class="dokan-range-slider dokan-left"
                    type="range"
                    value="<?php echo esc_attr( $distance ); ?>"
                    min="<?php echo esc_attr( $slider['min'] ); ?>"
                    max="<?php echo esc_attr( $slider['max'] ); ?>"
                >
            </div>
        </div>
    </div>

    <input type="hidden" name="latitude" value="<?php echo esc_attr( $latitude ); ?>">
    <input type="hidden" name="longitude" value="<?php echo esc_attr( $longitude ); ?>">
    <input type="hidden" name="wc_shop_page" value="<?php echo esc_attr( $wc_shop_page ); ?>">
    <input type="hidden" name="dokan_store_listing_page" value="<?php echo esc_attr( $store_listing_page ); ?>">
</form>
