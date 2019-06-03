<?php
/**
 * New Auction Product Email.
 *
 * An email sent to the admin when a new Product is created by vendor.
 *
 * @class       Dokan_Auction_Email
 * @version     2.7.1
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php _e( 'Hello,', 'dokan' ); ?></p>

<p><?php _e( 'A new auction product is submitted to your site', 'dokan' ); ?> <a href="<?php echo $data['site_url'] ?>" ><?php echo $data['site_name'] ?></a> </p>
<p><?php _e( 'Summary of the product:', 'dokan' ); ?></p>
<hr>
<ul>
    <li>
        <strong>
            <?php _e( 'Title :', 'dokan' ); ?>
        </strong>
        <?php printf( '<a href="%s">%s</a>', $data['product_link'], $data['product-title']  ); ?>
    </li>
    <li>
        <strong>
            <?php _e( 'Selling Price :', 'dokan' ); ?>
        </strong>
        <?php echo wc_price( $data['price'] ); ?>
    </li>
    <li>
        <strong>
            <?php _e( 'Vendor :', 'dokan' ); ?>
        </strong>
        <?php
        printf( '<a href="%s">%s</a>', $data['seller_url'], $data['seller-name']  ); ?>
    </li>
    <li>
        <strong>
            <?php _e( 'Category :', 'dokan' ); ?>
        </strong>
        <?php echo $data['category'] ?>
    </li>

</ul>
<p><?php _e( 'The product is currently in "publish" status. So, everyone can view the product.', 'dokan' ); ?></p>

<?php

do_action( 'woocommerce_email_footer', $email );
