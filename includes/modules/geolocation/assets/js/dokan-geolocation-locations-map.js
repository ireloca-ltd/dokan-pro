( function ( $ ) {
    DokanGeo.LocationsMaps = {
        map: null,
        markers: [],
        marker_clusterer: null,
        info_window: null,

        init: function () {
            var self = this;

            var map_area = $( '#dokan-geolocation-locations-map' );

            self.map = new google.maps.Map( map_area.get(0), {
                zoom: parseInt( DokanGeo.map_zoom, 10 ),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            self.info_window = new google.maps.InfoWindow();

            self.setMarkers();

            self.map.addListener( 'clusterclick', function (cluster) {
                var bounds = cluster.getBounds(),
                    markers = cluster.getMarkers();

                // At first I was using if ( bounds.b.f === bounds.b.b ) condition,
                // then saw that bounds doesn't contain b any more. So, I'm trying to
                // use dynamic key properties here.

                var bounds_props = Object.keys( bounds ),
                    bound_1st_prop_props = Object.keys( bounds[ bounds_props[0] ] ),
                    f = bounds[ bounds_props[0] ][ bound_1st_prop_props[0] ],
                    b = bounds[ bounds_props[0] ][ bound_1st_prop_props[1] ];

                if ( f === b ) {
                    var html = '<div class="white-popup dokan-geo-map-info-windows-in-popup">';

                    markers.forEach( function ( marker ) {
                        html += self.getInfoWindowContent(marker.info);
                    } );

                    html += '</div>';

                    $.magnificPopup.open({
                        items: {
                            type: 'inline',
                            src: html
                        }
                    });
                }
            } );
        },

        setMarkers: function () {
            var items = $( '[name="dokan_geolocation[]"]' );

            if ( ! items.length ) {
                var search = window.location.search,
                    latitude = DokanGeo.default_geolocation.latitude,
                    longitude = DokanGeo.default_geolocation.longitude,
                    queries = search.replace( '?', '' ).split( '&' ),
                    i = 0;

                if ( queries.length ) {
                    var query = '',
                        param = '',
                        value = '';

                    for ( i = 0; i < queries.length; i++ ) {
                        query = queries[i].split( '=' );

                        param = query[0].toLowerCase();
                        value = query[1];

                        if ( 'latitude' === param ) {
                            latitude = value;
                        } else if ( 'longitude' === param ) {
                            longitude = value;
                        }
                    }
                }

                this.map.setCenter(
                    new google.maps.LatLng( latitude, longitude )
                );

                return;
            }

            var self = this,
                bound = new google.maps.LatLngBounds();

            var marker_icon = {
                url: DokanGeo.marker.image,
                scaledSize: new google.maps.Size(32, 32)
            };

            if ( self.marker_clusterer ) {
                self.marker_clusterer.clearMarkers();
            }

            self.markers = [];

            items.each( function () {
                var id = $( this ).val(),
                    latitude = $( this ).data( 'latitude' ),
                    longitude = $( this ).data( 'longitude' ),
                    info = $( this ).data( 'info' );

                var curpoint = new google.maps.LatLng( latitude, longitude );

                bound.extend( curpoint );

                var marker_options = {
                    position: curpoint,
                    map: self.map,
                    info: info
                };

                if ( DokanGeo.marker.image ) {
                    marker_options.icon = marker_icon;
                }

                var marker = new google.maps.Marker( marker_options );

                marker.addListener( 'click', function () {
                    if ( ! info ) {
                        return;
                    }

                    var info_window_content = self.getInfoWindowContent( info );

                    self.info_window.setContent( info_window_content );
                    self.info_window.open( self.map, marker );
                    self.map.panTo( curpoint );
                } );

                self.markers.push(marker);
            } );

            self.map.setCenter( bound.getCenter() );

            var i = 0, styles = [];

            for ( i = 0; i < 5; i++ ) {
                styles.push( {
                    url: DokanGeo.marker.clusterer,
                    height: 40,
                    width: 40,
                    textColor: '#fddace',
                    textSize: 13,
                    backgroundSize: '40px'
                } );
            }

            self.marker_clusterer = new MarkerClusterer( self.map, self.markers, {
                gridSize: 40,
                styles: styles
            } );
        },

        getInfoWindowContent: function ( info ) {
            var content = DokanGeo.info_window_template,
                infoProp;

            for ( infoProp in info ) {
                content = content.replace( '{' + infoProp + '}', info[infoProp] );
            }

            return content;
        }
    };

    if ( $( '#dokan-geolocation-locations-map' ).length ) {
        DokanGeo.LocationsMaps.init();
    }
} )( jQuery );
