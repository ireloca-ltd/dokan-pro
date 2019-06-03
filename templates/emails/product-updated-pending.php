<?php
/**
 * Pending Updated Product Email.
 *
 * An email sent to the admin when a new Product is updated by vendor and in pending status.
 *
 * @class       Dokan_Email_New_Product_Pending
 * @version     2.6.6
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?><?php _e( 'Hello there,', 'dokan' ); ?>

<p><?php _e( 'Hello,', 'dokan' ); ?></p>

<p><?php _e( 'A product has been updated in your site.', 'dokan' ); ?> <a href="<?php echo $data['site_url'] ?>" ><?php echo $data['site_name'] ?></a> </p>
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
            <?php _e( 'Price :', 'dokan' ); ?>
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
<p><?php _e( 'The product is currently in "pending" status.', 'dokan' ); ?></p>

<p>
    <?php
    echo sprintf( __( 'Moderate the product by clicking <a href="%s">here</a>', 'dokan' ), $data['product_link'] );
    ?>
</p>

<?php

do_action( 'woocommerce_email_footer', $email );
