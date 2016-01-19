(function($) {
    // for edit add.
    CE.map_edit = CE.Views.Modal_Box.extend({
        el: '#modal_edit_ad',
        events: {
            'keyup input#et_full_location': 'geocoding',
        },
        map: [],
        initialize: function() {
            pubsub.on('ce:ad:afterSetupFields', this.onEditAd, this);
            this.initMapEdit();
        },
        onEditAd: function(model) {
            var view = this;
            var et_lat = $("#modal_edit_ad").find('input#et_location_lat');
            var et_lng = $("#modal_edit_ad").find('input#et_location_lng');
            var et_lat_model = model.get('et_location_lat'); // get marker lat.
            var et_lng_model = model.get('et_location_lng'); // get marker lng.
            et_lat.val(et_lat_model);
            et_lng.val(et_lng_model);
            var zoom = 12;
            if (model.get('et_map_zoom')) {
                zoom = parseInt(model.get('et_map_zoom'));
            }
            var t = true;
            view.map.setZoom(zoom);
            var location = $.trim(model.get('et_full_location'));
            view.map.refresh();
            setTimeout(function() {
                GMaps.geocode({
                    address: location,
                    callback: function(results, status) {
                        if (status == 'OK') {
                            view.map.removeMarkers();
                            var latlng = results[0].geometry.location;
                            //view.map.setCenter(latlng.lat(), latlng.lng());
                            //
                            var center_lat = latlng.lat(),
                                center_lng = latlng.lng();
                            var marker_lat = latlng.lat(),
                                marker_lng = latlng.lng();
                            if (model.get('et_center_lat') && model.get('et_center_lng') && et_lat_model != '' && et_lng_model != '') {
                                center_lat = model.get('et_center_lat');
                                center_lng = model.get('et_center_lng');
                                marker_lat = et_lat_model;
                                marker_lng = et_lng_model;
                            }
                            view.map.setCenter(center_lat, center_lng);
                            view.map.addMarker({
                                lat: marker_lat,
                                lng: marker_lng,
                                draggable: true,
                                dragend: function(e) {
                                    //if(ce_map.auto_save){
                                    var mLat = this.getPosition().lat();
                                    var mLng = this.getPosition().lng();
                                    et_lat.val(mLat);
                                    et_lng.val(mLng);
                                    //}
                                }
                            });
                        }
                    }
                });
                // refresh only first time.
                //if(t)
                view.map.refresh();
            }, 500);
        },
        initMapEdit: function() {
            var view = this;
            this.map = new GMaps({
                div: '#map',
                lat: 44.623838782333564,
                lng: 62.2265625,
                zoom: 1,
                panControl: true,
                zoomControl: true,
                mapTypeControl: true,
                click: function(e) {
                    view.get_pos(e);
                },
                drag: function(e) {
                    //view.get_pos(e);
                },
                mousemove: function(e) {},
                center_changed: function(event) {
                    if (ce_map.auto_save) {
                        var location = event.getCenter();
                        var lat = location.lat();
                        var lng = location.lng();
                        //set value input in html.
                        var et_center_lat = $(view.el).find("#et_center_lat");
                        var et_center_lng = $(view.el).find("#et_center_lng");
                        et_center_lat.val(lat);
                        et_center_lng.val(lng);
                    }
                },
                zoom_changed: function(event) {
                    if (ce_map.auto_save == true) {
                        var location = event.getCenter();
                        var lat = location.lat();
                        var lng = location.lng();
                        view.$("#et_map_zoom").val(event.zoom);
                    }
                }
            });
            // setTimeout(function(){
            // 	if( typeof GMaps !== 'undefined' && typeof this.map.refresh === 'function' ){
            // 		this.map.refresh();
            // 	}
            // },500);
        },
        geocoding: function(event) {
            var that = this,
                $location = $(event.currentTarget);
            if (typeof this.t !== 'undefined') {
                clearTimeout(this.t);
            }
            this.t = setTimeout(function() {
                that.reloadMap({
                    address: $.trim($location.val())
                });
            }, 500);
        },
        reloadMap: function(args) {
            var that = this,
                params = _.extend({}, args),
                $locationtxt = this.$('#location');
            params.callback = function(results, status) {
                var latlng, location_lat, location_lng;
                if (status == 'OK') {
                    location_lat = that.$('#et_location_lat'),
                    location_lng = that.$('#et_location_lng');
                    latlng = results[0].geometry.location;
                    that.map.setZoom(12);
                    that.map.setCenter(latlng.lat(), latlng.lng());
                    that.map.removeMarkers();
                    that.map.addMarker({
                        lat: latlng.lat(),
                        lng: latlng.lng(),
                        draggable: true,
                        dragend: function(e) {
                            var mLat = this.getPosition().lat();
                            var mLng = this.getPosition().lng();
                            location_lat.val(mLat);
                            location_lng.val(mLng);
                        }
                    });
                    location_lat.val(latlng.lat());
                    location_lng.val(latlng.lng());
                }
                if (typeof args.callback === 'function') {
                    args.callback();
                }
            };
            GMaps.geocode(params);
        },
    });
    // for add new ad.
    CE.map = Backbone.View.extend({
        el: 'div#post-classified',
        events: {
            'click button.select-plan': 'selectPlan',
            'keyup input#et_full_location': 'geocoding',
        },
        initialize: function() {
            pubsub.on('et:response:auth', this.affterLogin, this);
            var view = this;
            //view.map = null;
            this.map_options = {
                'mapTypeId': "roadmap",
                'scrollwheel': false,
                'zoom': 8
            };
            if (typeof view.map == 'undefined' && $("#post-classified").length > 0) {
                view.initMapAdd();
                setTimeout(function() {
                    if (typeof GMaps !== 'undefined' && typeof this.map.refresh === 'function') {
                        view.map.refresh();
                    }
                }, 500);
            }
        },
        affterLogin: function(data) {
            var view = this;
            setTimeout(function() {
                if (typeof GMaps !== 'undefined' && typeof view.map.refresh === 'function') {
                    view.map.refresh();
                }
            }, 500);
        },
        initMapAdd: function() {
            view = this;
            view.map = new GMaps({
                div: '#map',
                lat: 44.623838782333564,
                lng: 62.2265625,
                zoom: 1,
                panControl: false,
                zoomControl: false,
                mapTypeControl: false,
                center_changed: function(event) {
                    //if(ce_map.auto_save){
                    var location = event.getCenter();
                    var lat = location.lat();
                    var lng = location.lng();
                    var et_center_lat = view.$("#et_center_lat");
                    var et_center_lng = view.$("#et_center_lng");
                    et_center_lat.val(lat);
                    et_center_lng.val(lng);
                    //}
                },
                zoom_changed: function(event) {
                    //if(ce_map.auto_save){
                    var location = event.getCenter();
                    var lat = location.lat();
                    var lng = location.lng();
                    view.$("#et_map_zoom").val(event.zoom);
                    //}
                }
            });
            if (ce_map.ad_address) {
                setTimeout(function() {
                    view.reloadMap({
                        address: ce_map.ad_address
                    });
                }, 500);
            }
        },
        geocoding: function(event) {
            event.preventDefault();
            var that = this,
                $location = $(event.currentTarget);
            if (typeof this.t !== 'undefined') {
                clearTimeout(this.t);
            }
            this.t = setTimeout(function() {
                that.reloadMap({
                    address: $.trim($location.val())
                });
            }, 500);
        },
        'get_pos': function(event) {
            var lat = event.latLng.d;
            var lng = event.latLng.e;
            $('#et_location_lat').val(lat);
            $('#et_location_lng').val(lng);
        },
        'selectPlan': function(event) {
            event.preventDefault();
            var view = this;
            view.initMapAdd();
            setTimeout(function() {
                if (typeof GMaps !== 'undefined' && typeof view.map.refresh === 'function') {
                    view.map.refresh();
                }
            }, 500);
        },
        reloadMap: function(args) {
            var that = this,
                params = _.extend({}, args),
                $locationtxt = this.$('#location');
            params.callback = function(results, status) {
                var latlng, location_lat, location_lng;
                if (status == 'OK') {
                    location_lat = that.$('#et_location_lat'),
                    location_lng = that.$('#et_location_lng');
                    latlng = results[0].geometry.location;
                    that.map.setZoom(12);
                    that.map.setCenter(latlng.lat(), latlng.lng());
                    that.map.removeMarkers();
                    that.map.addMarker({
                        lat: latlng.lat(),
                        lng: latlng.lng(),
                        draggable: true,
                        dragend: function(e) {
                            var mLat = this.getPosition().lat();
                            var mLng = this.getPosition().lng();
                            location_lat.val(mLat);
                            location_lng.val(mLng);
                        }
                    });
                    location_lat.val(latlng.lat());
                    location_lng.val(latlng.lng());
                }
                if (typeof args.callback === 'function') {
                    args.callback();
                }
            };
            GMaps.geocode(params);
        },
    });
    $(document).ready(function() {
        if ($("#post-classified").length > 0) new CE.map();
        if ($("#modal_edit_ad").length > 0) new CE.map_edit();
    });
}(jQuery))