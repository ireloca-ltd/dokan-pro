<?php
    $dokan_geo_latitude  = dokan_geo_float_val( $dokan_geo_latitude );
    $dokan_geo_longitude = dokan_geo_float_val( $dokan_geo_longitude );
?>
<input
    type="hidden"
    name="dokan_geolocation[]"
    value="<?php echo esc_attr( $id ); ?>"
    data-latitude="<?php echo esc_attr( $dokan_geo_latitude ); ?>"
    data-longitude="<?php echo esc_attr( $dokan_geo_longitude ); ?>"
    data-address="<?php echo esc_attr( $dokan_geo_address ); ?>"
    data-info="<?php echo esc_attr( $info ); ?>"
>
