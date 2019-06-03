(function($) {
    if ( ! $( '#dokan-geolocation-product-location' ).length ) {
        return;
    }

    var gmap, marker, address, geocoder;

    function initMap() {
        var lat      = $('[name="_dokan_geolocation_product_dokan_geo_latitude"]').val(),
            lng      = $('[name="_dokan_geolocation_product_dokan_geo_longitude"]').val(),
            map_area = $( '#dokan-geolocation-product-location-map' );

        address = $( '#_dokan_geolocation_product_location' );

        var curpoint = new google.maps.LatLng( lat, lng );

        gmap = new google.maps.Map( map_area.get(0), {
            center: curpoint,
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        marker = new google.maps.Marker( {
            position: curpoint,
            map: gmap,
            draggable: true
        } );

        geocoder = new google.maps.Geocoder;

        var autocomplete = new google.maps.places.Autocomplete( address.get(0) );

        autocomplete.addListener( 'place_changed', function () {
            var place = autocomplete.getPlace(),
                location = place.geometry.location;

            updateMap( location.lat(), location.lng(), place.formatted_address );
        } );

        gmap.addListener( 'click', function ( e ) {
            updateMap( e.latLng.lat(), e.latLng.lng() );
        });

        marker.addListener( 'dragend', function ( e ) {
            updateMap( e.latLng.lat(), e.latLng.lng() );
        } );
    }

    function updateMap( lat, lng, formatted_address ) {
        $('[name="_dokan_geolocation_product_dokan_geo_latitude"]').val( lat ),
        $('[name="_dokan_geolocation_product_dokan_geo_longitude"]').val( lng );

        var curpoint = new google.maps.LatLng( lat, lng )

        gmap.setCenter( curpoint );
        marker.setPosition( curpoint );

        if ( ! formatted_address ) {
            geocoder.geocode( {
                location: {
                    lat: lat,
                    lng: lng
                }
            }, function ( results, status ) {
                if ( 'OK' === status ) {
                    address.val( results[0].formatted_address );
                }
            } )
        }
    }

    $( '#_dokan_geolocation_use_store_settings' ).on( 'change', function () {
        $( '#dokan-geolocation-product-location-no-store-settings' ).toggleClass( 'dokan-hide' );
        $( '#dokan-geolocation-product-location' ).toggleClass( 'dokan-hide' );
    } );

    var locate_btn = $( '#dokan-geolocation-product-location' ).find( '.locate-icon' );

    if ( ! navigator.geolocation ) {
        locate_btn.addClass( 'dokan-hide' );
    } else {
        locate_btn.on( 'click', function () {
            navigator.geolocation.getCurrentPosition( function( position ) {
                updateMap( position.coords.latitude, position.coords.longitude );
            });
        });
    }

    initMap();
})(jQuery);
