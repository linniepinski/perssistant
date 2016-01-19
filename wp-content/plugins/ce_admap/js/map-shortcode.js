(function($) {
    $(document).ready(function() {
        CE_shortcode = Backbone.View.extend({
            el: 'body',
            events: {
                'keyup input#et_full_location': 'geocoding',
                'click .map': 'showMap'
            },
            initialize: function() {
                var view = this,
                    center = false,
                    zoom = 1;
                view.markers = [];
                view.marker = {
                    lat : 10, lng : 106
                };
                if (map_short_code.lat != '' && map_short_code.lng != '') {
                    lat = parseFloat(map_short_code.lat);
                    lng = parseFloat(map_short_code.lng);
                    center = new google.maps.LatLng(lat, lng);
                }
                if (map_short_code.zoom) zoom = parseInt(map_short_code.zoom);
                var mapOptions = {
                    zoom: zoom,
                    center: center
                };
                view.map = null;
                if (map_short_code.lat != '' && map_short_code.lng != '') {
                    /*
                     * render map
                     */
                    // view.map = new google.maps.Map(document.getElementById('map-shortcode'), mapOptions);
                    view.map = new GMaps({
                        div: '#map-shortcode',
                        lat: map_short_code.lat,
                        lng: map_short_code.lng,
                        zoom: zoom
                    });
                    var marker = new google.maps.Marker({
                        map: view.map,
                        position: mapOptions.center,
                    });
                    // marker.setMap(view.map);
                    view.map.addMarker(center);
                    view.marker = center;
                } else {
                    view.map = new GMaps({
                        div: '#map-shortcode',
                        lat: 10,
                        lng: 106,
                        zoom: zoom
                    });
                    /*
                     * render for gmap default(not for single-ad)
                     */
                    GMaps.geocode({
                        'address': map_short_code.address,
                        callback: function(results, status) {
                            if (status == 'OK') {
                                var latlng = results[0].geometry.location;
                                $('#et_location_lat').val(latlng.lat());
                                $('#et_location_lng').val(latlng.lng());
                                // set value to model
                                view.map.setZoom(zoom);
                                view.map.setCenter(latlng.lat(), latlng.lng());
                                view.map.removeMarkers();
                                view.map.addMarker({
                                    lat: latlng.lat(),
                                    lng: latlng.lng(),
                                    // draggable: true,
                                    dragend: function(e) {
                                        var location = e.latLng;
                                        $('#et_location_lat').val(location.lat());
                                        $('#et_location_lng').val(location.lng());
                                        view.model.set('et_location_lat', location.lat());
                                        view.model.set('et_location_lng', location.lng());
                                    }
                                });
                                view.marker = {
                                    lat: latlng.lat(),
                                    lng: latlng.lng()
                                };
                            }
                        }
                    });
                }
                // end initialize
            },
            geocoding: function(event) {
                var view = this;
                var mapOptions = {
                    zoom: 12,
                    center: false
                };
                var $target = $(event.currentTarget),
                    address = $target.val();
                GMaps.geocode({
                    'address': address,
                    callback: function(results, status) {
                        if (status == 'OK') {
                            var latlng = results[0].geometry.location;
                            mapOptions.center = new google.maps.LatLng(latlng.lat(), latlng.lng());
                            view.map.setCenter(latlng.lat(), latlng.lng());
                            if ($("input#et_location_lat").length > 0) $("input#et_location_lat").val(latlng.lat());
                            if ($("input#et_location_lng").length > 0) $("input#et_location_lng").val(latlng.lng());
                            var marker = new google.maps.Marker({
                                position: results[0].geometry.location,
                                title: address,
                            });
                            view.markers.push(marker);
                            // for (var i = 0; i < view.markers.length; i++) {
                            //     view.markers[i].setMap(null);
                            // }
                            view.map.addMarker(marker);
                            view.marker = marker;
                        }
                    }
                });
            },
            showMap: function(event) {
                var view = this;
                setTimeout(function() {
                    view.map.refresh();
                    view.map.addMarker(view.marker);
                    view.map.setCenter(view.marker.lat, view.marker.lng);
                }, 500);
            }
        });
        if (typeof map_short_code !== 'undefined') {
            new CE_shortcode();
        }
    });
}(jQuery));