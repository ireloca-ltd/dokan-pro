<?php
/**
 * Dokan Review Content Template
 *
 * @since 2.4
 *
 * @package dokan
 */
?>
<div class="dokan-comments-wrap">

    <?php

        /**
         * dokan_review_content_status_filter hook
         *
         * @hooked dokan_review_status_filter
         */
        do_action( 'dokan_review_content_status_filter', $post_type, $counts );


        /**
         * dokan_review_content_listing hook
         *
         * @hook dokan_review_content_listing
         */
        do_action( 'dokan_review_content_listing', $post_type, $counts );
    ?>

</div>