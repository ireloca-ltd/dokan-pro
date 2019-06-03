<?php

defined( 'ABSPATH') || exit;

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p>
    <?php echo wp_kses_post( $message ); ?>
</p>

<p>
    <?php printf( __( 'Enquery Summery:', 'dokan' ) ); ?>
</p>

<ul class="dokan-product-enquery">
    <li><?php printf( __( 'From: %s - %s', 'dokan' ), $customer_name, $customer_email ); ?></li>
    <li><?php printf( __( 'Product: %s', 'dokan' ), $product->get_title() ); ?></li>
    <li><?php printf( __( 'Product URL: %s', 'dokan' ), $product->get_permalink() ); ?></li>
    <li><?php printf( __( 'IP: %s', 'dokan' ), $IP ); ?></li>
    <li><?php printf( __( 'User Agent: %s', 'dokan' ), $user_agent ); ?></li>
</ul>

<style type="text/css">
.dokan-product-enquery li {
    font-style: italic;
}
</style>

<?php do_action( 'woocommerce_email_footer', $email );