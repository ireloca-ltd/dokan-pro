<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once ABSPATH . WPINC . '/formatting.php';

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<div id="dokan-report-abuse-email">
    <?php
        printf(
            '<p>%s <strong><a href="%s">%s</a></strong></p>',
            esc_html__( 'You have got a new abuse report for the product', 'dokan' ),
            esc_url( $data['product_link'] ),
            esc_html( $data['product_title'] )
        );

        printf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Reason', 'dokan' ), esc_html( $data['reason'] ) );

        if ( $data['description'] ) {
            printf( '<p><strong>%s:</strong> %s</p>', esc_html__( 'Description', 'dokan' ), esc_html( $data['description'] ) );
        }

        if ( $data['customer'] ) {
            $customer = $data['customer'];
            $customer_link = admin_url( sprintf( 'user-edit.php?user_id=%d', $customer->get_id() ) );
            printf(
                '<p><strong>%s:</strong> <a href="%s">%s</a></p>',
                esc_html__( 'Reported by', 'dokan' ),
                $customer_link,
                esc_html( $customer->get_username() )
            );
        } else {
            printf(
                '<p><strong>%s:</strong> %s &lt;%s&gt;</p>',
                esc_html__( 'Reported by', 'dokan' ),
                esc_html( $data['customer_name'] ),
                esc_html( $data['customer_email'] )
            );
        }

        printf(
            '<p><strong>%s:</strong> %s %s</p>',
            esc_html__( 'Reported At', 'dokan' ),
            date( get_option( 'date_format', 'F j, Y' ), $data['reported_at'] ),
            date( get_option( 'time_format', 'g:i a' ), $data['reported_at'] )
        );

        printf(
            '<p><strong>%s:</strong> <a href="%s">%s</a></p>',
            esc_html__( 'Product Vendor', 'dokan' ),
            esc_url( $data['vendor_link'] ),
            esc_html( $data['vendor_name'] )
        );

        printf(
            '<p>%s</p>',
            esc_html__( 'You can draft or remove the product or you can ignore this email if you think the product is OK.', 'dokan' )
        );
    ?>
</div>
<?php do_action( 'woocommerce_email_footer', $email );
