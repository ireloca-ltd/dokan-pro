<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once ABSPATH . WPINC . '/formatting.php';

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<div id="dokan-follow-store">
    <h1 id="dokan-follow-store-title">
        <?php esc_html_e( 'Updates from your favorite stores', 'dokan' ); ?>
    </h1>

    <table style="width: 100%; border-collapse: collapse;">
        <tbody>
            <?php foreach ( $data['vendors'] as $vendor ): ?>
                <?php if ( $vendor->products->have_posts() || ! empty( $vendor->coupons ) ): ?>
                    <tr>
                        <td valign="middle">
                            <div class="content">
                                <h3>
                                    <a href="<?php echo esc_url( $vendor->get_shop_url() ); ?>">
                                        <?php echo esc_html( $vendor->get_shop_name() ); ?>
                                    </a>
                                </h3>

                                <?php if ( $vendor->products->have_posts() ): $products = $vendor->products->posts; ?>
                                    <p class="section-title"><strong><?php esc_html_e( 'New Products', 'dokan' ); ?></strong></p>

                                    <table class="vendor-products">
                                        <tbody>
                                            <tr>
                                                <?php foreach( $products as $i => $product ): $product = wc_get_product( $product ); ?>
                                                    <td>
                                                        <a href="<?php echo esc_url( $product->get_permalink() ); ?>">
                                                            <?php echo $product->get_image( 'thumbnail' ); ?>

                                                            <span class="product-name">
                                                                <?php echo esc_html( wp_trim_words( $product->get_name(), 4, '...' ) ); ?>
                                                            </span>
                                                        </a>
                                                    </td>
                                                <?php endforeach; ?>
                                                <td class="show-all-products">
                                                    <a href="<?php echo esc_url( $vendor->get_shop_url() ); ?>">
                                                        <?php esc_html_e( 'See all', 'dokan' ); ?>
                                                    </a>
                                                </td>

                                                <?php $total = count( $products ); if ( $total < 3 ): ?>
                                                    <?php for( $i = 0; $i < ( 3 - $total ); $i++ ): ?>
                                                    <td></td>
                                                    <?php endfor; ?>
                                                <?php endif; ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php endif; ?>

                                <?php if ( ! empty( $vendor->coupons ) ): $coupons = $vendor->coupons; ?>
                                    <p class="section-title"><strong><?php esc_html_e( 'Coupons', 'dokan' ); ?></strong></p>

                                    <ul class="vendor-coupons">
                                        <?php for ( $i = 0; $i < count( $coupons ); $i++ ): $coupon = $coupons[ $i ]; ?>
                                            <li>
                                                <span class="coupon-name">
                                                    <?php echo esc_html( $coupon->get_code() ); ?>
                                                </span>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<style type="text/css">
    #dokan-follow-store-title {
        text-align: center;
    }

    #dokan-follow-store table {
        width: 100%;
        border-collapse: collapse;
    }

    #dokan-follow-store .content {
        border: 1px solid #ddd;
        border-radius: 3px;
        height: 100%;
        padding: 20px;
    }

    #dokan-follow-store .content h3 {
        margin-top: 0;
    }

    #dokan-follow-store .content a {
        text-decoration: none;
        font-weight: bold;
    }

    #dokan-follow-store .content p.section-title {
        margin: 0 0 5px;
    }

    #dokan-follow-store .content .vendor-products td {
        width: 25%;
        padding: 0 10px 0 0;
    }

    #dokan-follow-store .content .vendor-products td.show-all-products {
        vertical-align: top;
        line-height: 100px;
        text-align: center;
    }

    #dokan-follow-store .content .vendor-products a {
        display: block;
        font-size: 13px;
        font-weight: 400;
    }

    #dokan-follow-store .content .vendor-products img {
        border: 1px solid #f1f1f1;
        width: 100%;
        margin: 0 0 10px;
        border-radius: 2px;
    }

    #dokan-follow-store .content .vendor-products .product-name {
        display: block;
        width: 100%;
        height: 55px;
    }

    #dokan-follow-store .content .vendor-coupons {
        padding: 0;
        list-style-type: none;
        display: inline-block;
        margin: 0;
    }

    #dokan-follow-store .content .vendor-coupons li {
        display: inline-block;
        background-color: #7d7d7d;
        border: 1px dashed #cacaca;
        color: #ffffff;
        padding: 10px;
        margin: 0 5px 7px 0;
        font-size: 13px;
        line-height: 1;
    }
</style>
<?php do_action( 'woocommerce_email_footer', $email );
