<?php

do_action( 'woocommerce_account_navigation' ); ?>

<div class="woocommerce-MyAccount-content">
	<?php
		$dss      = Dokan_Store_Support::init();
		$topic_id = get_query_var( 'support-tickets' );

		if( is_numeric( $topic_id ) ) {
		    $topic = $dss->get_single_topic_by_customer( $topic_id, dokan_get_current_user_id() );
		}
	?>
	<div class="dokan-support-customer-listing dokan-support-topic-wrapper">
	    <?php
	        if ( empty( $topic ) || isset( $_GET['ticket_status'] ) ) {
	            $dss->support_topic_status_list( false );
	            $dss->print_support_topics_by_customer( dokan_get_current_user_id() );
	        } else {
	            $dss->print_single_topic( $topic );
	        }
	    ?>
	</div>

</div>
