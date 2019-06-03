<?php

namespace DokanPro\ReportAbuse;

class Rest {

    /**
     * Class constructor
     *
     * @since 2.9.8
     *
     * @return void
     */
    public function __construct() {
        add_filter( 'dokan_rest_api_class_map', [ $this, 'add_rest_controller' ] );
    }

    /**
     * Add module REST Controller
     *
     * @since 2.9.8
     *
     * @param array $class_map
     */
    public function add_rest_controller( $class_map ) {
        $class_map[ DOKAN_REPORT_ABUSE_INCLUDES . '/RestController.php' ] = "\\DokanPro\\ReportAbuse\\RestController";

        return $class_map;
    }
}
