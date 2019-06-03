<header class="dokan-dashboard-header">
    <h1 class="entry-title">
        <?php echo $title; ?>
    </h1>
</header><!-- .dokan-dashboard-header -->

<?php
$page = new Dokan_WC_Bookings_Calendar();
$page->output();

