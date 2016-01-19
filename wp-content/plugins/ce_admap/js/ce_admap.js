/**
 * this file control map for widget
 * @param  {[type]} $ [description]
 * @return {[type]}   [description]
 */
(function($) {

	CE.Views.MapModal	=	CE.Views.Modal_Box.extend({

		el		: jQuery('div#modal_ad_map'),
		template : _.templateSettings = {
			    evaluate    : /<#([\s\S]+?)#>/g,
				interpolate : /\{\{(.+?)\}\}/g,
				escape      : /<#-([\s\S]+?)#>/g,
		},
		template : _.template($('#ce_admap_template').html()),

		events  : {

			'click button.close'			: 'close',

			'change select'					: 'changeCat',

			'keyup input.search-box'		: 'searchAd'

		},

		initialize	: function(){
			_.templateSettings = {
			    evaluate    : /<#([\s\S]+?)#>/g,
				interpolate : /\{\{(.+?)\}\}/g,
				escape      : /<#-([\s\S]+?)#>/g,
			};

			CE.Views.Modal_Box.prototype.initialize.apply(this, arguments );

			this.search_params	=	{};
			this.styleSelector(this.$el);
		},

		close : function (time, callback) {
			this.closeModal();

			return false;

		},

		openModalMap : function ( params ) {

			this.openModal();
			this.renderMap(params);

		},
		styleSelector : function(container){
	        // apply custom look for select box
	        $(container).find('.select-style select').each(function(){
	            var $this = jQuery(this),
	                title = $this.attr('title'),
	                selectedOpt = $this.find('option:selected');
	            if( selectedOpt.val() !== '' ){
	                title = selectedOpt.text();
	            }

	            $this.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
	                .after('<span class="select">' + $.trim(title) + '</span>')
	                .change(function(){
	                    var val = jQuery('option:selected',this).text();
	                    jQuery(this).next().text(val.trim());
	                });
	        });
	    },


		renderMap : function (params) {

			var view	=	this;

			view.center	=	params.center;

			view.map_options = {

				'zoom': parseInt(ce_admap_widget.zoom),

				'center': params.center,

				'mapTypeId': google.maps.MapTypeId.ROADMAP,

				'zoomControl' : true

			};



			view.markers		=	_.clone (params.markers);

			if(view.markerCluster)

				view.markerCluster.clearMarkers ();

			view.infoWindow 	= new google.maps.InfoWindow({maxWidth : 300});
			this.map = new google.maps.Map(document.getElementById("modal_map_inner"), view.map_options);
			view.markerCluster	=	new MarkerClusterer(view.map, view.markers);
			view.markerCluster.onClick  = function(icon) { return view.multiChoice(icon.cluster_); }

			if($('#modal_ad_map').find('.map-overlay').length > 0) {
				view.map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push( $('.map-overlay').get(0) );
			}else {
				var div =	document.createElement ('div');
				div.className  = 'map-overlay';
				div.innerHTML  = $('#map-overlay').html();
				view.map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push( div );
			}

		},

		clearMap : function () {
			for (var i = 0, marker; marker = this.markers[i]; i++) {

			    marker.setMap(null);

			}
		},

		changeCat : function (event) {

			event.preventDefault();

			this.filterMap ();

		},
		searchAd : function(event){

			event.preventDefault();
			this.filterMap();

		},

		filterMap : function () {

			var view 	=	this;

			var params	=	{

				'category' : this.$el.find('select').val(),

				'location' : this.$el.find('input[name=ad_location]').val(),

				's' : this.$el.find('input[name=s]').val()

			};

			view.search_params	=	_.extend (params, {action : 'ce_admap_filter'});

			$.ajax ({

				type 	: 'get',

				url 	: et_globals.ajaxURL,

				data 	: params ,

				beforeSend  : function () {
					$('.map-overlay').show();
				},

				success : function (resp) {

					view.clearMap();
					$('.map-overlay').hide();
					view.markerCluster.clearMarkers ();

					if(typeof resp.data !== 'undefined' && resp.data.length > 0 ) {
						var data	=	resp.data;
						view.markers = [];

						for (var i = 0; i < data.length; i++) {

							var content	=	view.template (data[i]);

							var latLng = new google.maps.LatLng(data[i].lat, data[i].lng);

							if (i == 0) {

								var center	=	latLng;

							}
							var marker = new google.maps.Marker({ 'position': latLng , title : data[i]['post_title'] });

							view.attachMarkerInfowindow ( marker , content );

							view.markers.push(marker);

						}
						view.map.setCenter(center);
						view.center = center;
						view.markerCluster = new MarkerClusterer(view.map, view.markers);

					}
				}

			});
		},

		clearMap : function () {

			for (var i = 0, marker; marker = this.markers[i]; i++) {

			    marker.setMap(null);

			}

		},
		attachMarkerInfowindow : function ( marker, content ) {

			var view	=	this;
			google.maps.event.addListener(marker,'click',function() {

				view.infoWindow.setContent(content);

				view.infoWindow.open(this.map,marker);

			});

		},

		multiChoice : function(clickedCluster) {

			if (clickedCluster.getMarkers().length > 1) {

				var markers		=	 clickedCluster.getMarkers();

				var data		=	ce_admap_widget.heading.replace('%s', markers.length);

				for(var i=0; i<markers.length;i++) {
					var d		=	markers[i].content;
				    data	+= d ;
				}

				var info = new google.maps.MVCObject;
				info.set('position', clickedCluster.center_);

				this.infoWindow.setContent('<div class="jobs-wrapper" style="width : 259px">'+data + '</div>');

				this.infoWindow.open( this.map, info);

			    return false;
			}

			return true;
		}

	});


/**
 * control event change position map, change zoom and save info of map widget.
 */

	CE.Views.AdMap	=	Backbone.View.extend({

		el : 'body' ,

		template : _.template($('#ce_admap_template').html()),

		map : null,

		map_options	: {},

		events : {

			'change .admap form input.center' : 'changeCenter',

			'change .admap form input.zoom' : 'changeZoom',

			'click .admap .enlarge'	: 'enlargeMap'

		},



		initialize : function () {

			pubsub.on('je:indexFilter', this.filterMap, this);

			pubsub.on('ce_admap:modalsearchAd', this.fillParams , this );

			var view	=	this;

			view.map_options = {

				'zoom': parseInt(ce_admap_widget.zoom),

			//	'center': view.center,

				'mapTypeId': google.maps.MapTypeId.ROADMAP,

				// 'scrollwheel': false

				'scrollwheel': ce_admap_widget.enable_zoom,

				'zoomControl' : true
			};
			view.markers		=	[];

			view.markerCluster	=	[];

			view.infoWindow 	= 	new google.maps.InfoWindow({autoPan : true ,maxWidth : 300});


			// Map for default save-widget
			//
			this.map = new google.maps.Map(document.getElementById("ce_admap"), view.map_options);

			google.maps.event.addListener(view.map, 'dragend', function(e) {

			});
			google.maps.event.addListener(view.map, 'zoom_changed', function(e) {

			});


			if($('.admap').find('form').length > 0) {
				view.map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push( $('.admap').find('form').get(0) );
			}

			if(ce_admap_widget.lng && ce_admap_widget.lat) {
				var latLng	=	new google.maps.LatLng(ce_admap_widget.lat, ce_admap_widget.lng );
				view.map.setCenter( latLng );
				// cannot delete.
				view.center = latLng;

			} else{

				GMaps.geocode({address: ce_admap_widget.center , callback: function(results, status) {

						if (status == 'OK') {
							view.center = results[0].geometry.location;
							view.map.setCenter(view.center);
						}

					}

				});

			}

			setTimeout ( function () {$('.admap').find('form').show() } , 2000 );
			var data	=	{action : 'ce_cemap_fetch_ads'}

			if( ce_admap_widget.is_single_job ) {

				data	= {action : 'ce_cemap_fetch_ads_insingle' , 'ad' : JSON.parse(this.$('#ad_data').html()) }

			}
			this.renderMap(data);

		},
		updateMap : function(){
			if($('.admap form').length > 0 ) {
				var data = $('.admap form').serialize();

				$.ajax({

					type : 'post',

					data : data + '&action=save-widget&sidebar=321',

					url : et_globals.ajaxURL,

					beforeSend : function () {},

					success : function () {}

				});

			}


		},
		renderMap : function (data) {
			var view	=	this;

			$.ajax ({

				type 	: 'get',

				url 	: et_globals.ajaxURL,

				data 	: data ,

				beforeSend  : function () {
				},

				success : function (resp) {

					if(typeof resp.data !== 'undefined') {

						var data	=	resp.data;

						view.markers = [];

						for (var i = 0; i < data.length; i++) {

							var content	=	view.template (data[i]);

							var latLng = new google.maps.LatLng(data[i].lat, data[i].lng);

							var marker = new google.maps.Marker({ 'position': latLng , title : data[i]['post_title'], content : content });
							view.attachMarkerInfowindow ( marker , content );
							view.markers.push(marker);
						}

						view.markerCluster = new MarkerClusterer(view.map, view.markers);
						view.markerCluster.onClick  = function(icon) { return view.multiChoice(icon.cluster_); }
						if(typeof resp.center != 'undefined') {

							var latLng	=	new google.maps.LatLng( resp.center.lat, resp.center.lng );

							view.map.setCenter( latLng );

							view.center = latLng;

						}

					}

					$('.enlarge').show();
				}
			});

		},

		fillParams : function (params) {

			if($('#header-filter').length > 0 ) {

				$('.filter-jobcat li a').removeClass('active');

				$('.filter-jobcat li a[data='+params.job_category).click();

				$('#header-filter').find('input[name=s]').val(params.s);

				$('#header-filter').find('input[name=job_location]').val(params.location);

			}

		},



		filterMap : function (params) {

			var view = this,
				param	=	_.clone(params);
			var param	=	_.extend (param, {action : 'ce_admap_filter'});

			$.ajax ({

				type 	: 'get',

				url 	: et_globals.ajaxURL,

				data 	: param ,

				beforeSend  : function () {
				},

				success : function (resp) {

					if(typeof resp.data !== 'undefined' && resp.data.length > 0 ) {

						view.clearMap();

						view.markerCluster.clearMarkers ();

						var data	=	resp.data;

						view.markers = [];

						//google.maps.event.trigger(view.map, 'resize');

						for (var i = 0; i < data.length; i++) {

							var content	=	view.template (data[i]);

							var latLng = new google.maps.LatLng(data[i].lat, data[i].lng);

							if (i == 0) {
								var center	=	latLng;
							}
							var marker = new google.maps.Marker({ 'position': latLng , title : data[i]['post_title'] });
							view.attachMarkerInfowindow ( marker , content );
							view.markers.push(marker);
						}
						view.map.setCenter(center);

						view.center = center;
						view.markerCluster = new MarkerClusterer(view.map, view.markers);
					}
				}
			});
		},
		clearMap : function () {
			for (var i = 0, marker; marker = this.markers[i]; i++) {
			    marker.setMap(null);
			}

		},
		enlargeMap : function (e) {

			e.preventDefault();

			if( typeof this.modal === 'undefined') {
				this.modal	=	new CE.Views.MapModal ();

			}
			this.modal.openModalMap( { markers : this.markers , center : this.center } );
		},
		updateWidget : function  (e) {

			if($('.admap form').length > 0 ) {

				$.ajax({

					type : 'post',

					data : $('.admap form').serialize () + '&action=save-widget&sidebar='+ $(e.currentTarget).parents('.ui-sortable').attr('id'),

					url : et_globals.ajaxURL,

					beforeSend : function () {},

					success : function () {}

				});

			}
		},

		changeCenter : function  (e) {
			var $target	=	$(e.currentTarget);

			var view	=	this;

			if( typeof this.map !== 'undefined' ) {

				GMaps.geocode({address: $target.val() , callback: function(results, status) {

					if (status == 'OK') {

						view.center = results[0].geometry.location;

						view.map.setCenter(view.center);

						$('.ce_admap form input.lat').val(view.center.lat());

						$('.ce_admap form input.lng').val(view.center.lng());

						view.markerCenter = new google.maps.Marker({

							map : view.map,

							position : results[0].geometry.location,

							draggable : true,

							title : 'Drag me to specify your correct center'

						});
						google.maps.event.addListener(view.markerCenter, 'dragend', function(event){

							var newLat = view.markerCenter.getPosition().lat();
 							var newLng = view.markerCenter.getPosition().lng();
 							$('.ce_admap form input.lat').val(newLat);
							$('.ce_admap form input.lng').val( newLng);

							view.updateWidget(event);
						});

						view.updateWidget(e);
					}
					}

				});

			}

		},
		changeZoom : function (e) {

			if( typeof this.map !== 'undefined' ) {

				this.map.setZoom (parseInt( $(e.currentTarget).val() ));

				this.updateWidget(e);

			}

		},
		attachMarkerInfowindow : function ( marker, content ) {

			var view	=	this;

			google.maps.event.addListener(marker,'click',function() {

				view.infoWindow.setContent(content);

				view.infoWindow.open(this.map,marker);

			});

		},
		multiChoice : function(clickedCluster) {

			if (clickedCluster.getMarkers().length > 1) {

				var markers		=	 clickedCluster.getMarkers();

				var data		=	ce_admap_widget.heading.replace('%s', markers.length);

				for(var i=0; i<markers.length;i++) {

					var d		=	markers[i].content;

				    data	+= d ;

				}
				this.infoWindow.close();
				var info = new google.maps.MVCObject;
				info.set('position', clickedCluster.center_);


				this.infoWindow.setContent('<div class="jobs-wrapper" style="width : 259px">'+data + '</div>');

				this.infoWindow.open( this.map, info);

			    return false;

			}
			return true;
		}

	});

	$(document).ready(function () {

		CE.Admap 	=	new CE.Views.AdMap();
		var fh 	 	= $( window ).height();
		var fw 		= $( window ).width();
		var bodyw 	= parseInt($(".main-center").css('width'));
		var h  		= 0.8*fh;
		var w 		= 0.8*fw;
		var mgl 	= parseInt( (fw-w)*0.5);
		if(fw < bodyw + 10)
			mgl = 0;
		if($("#modal_ad_map").length > 0)
			$("#modal_ad_map").css({'height': h, 'min-width': bodyw, 'width' : w, 'left' : mgl});
	});

} (jQuery));