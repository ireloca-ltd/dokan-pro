<div class="dokan-popup-content white-popup dokan-report-abuse-form-popup-wrapper" id="dokan-report-abuse-form-popup" style="width: 450px;">
    <h2 class="dokan-popup-title dokan-report-abuse-form-title">
        <?php esc_html_e( 'Report Abuse', 'dokan' ); ?>
    </h2>

    <?php
        dokan_report_abuse_report_form( [
            'id'                   => 'dokan-report-abuse-form-popup-form',
            'option_list_classes'  => 'dokan-popup-option-list',
            'option_label_classes' => 'dokan-popup-block-label',
        ], true );
    ?>
</div>
