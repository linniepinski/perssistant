(function($){
	function mapMobile() {
		var view = this;
		_.templateSettings = {
					    evaluate    : /<#([\s\S]+?)#>/g,
						interpolate : /\{\{(.+?)\}\}/g,
						escape      : /<#-([\s\S]+?)#>/g,
					};
		this.template = _.template($('#ce_admap_template').html());

		view.init = function(){
	    	var view	=	this;
			view.map_options = {

				'zoom': parseInt(ce_admap_mobile.zoom),

				'scrollwheel': ce_admap_mobile.enable_zoom,

				'zoomControl' : true
			};
			view.markers		=	[];

			view.markerCluster	=	[];

			view.infoWindow 	= 	new google.maps.InfoWindow({autoPan : true });


			// Map for default save-widget
			// 
			this.map = new google.maps.Map(document.getElementById("ce_admap"), view.map_options);
			if($('.admap').find('form').length > 0) {
				view.map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push( $('.admap').find('form').get(0) );
			}

			if(ce_admap_mobile.lng && ce_admap_mobile.lat) {
				var latLng	=	new google.maps.LatLng(ce_admap_mobile.lat, ce_admap_mobile.lng );
				view.map.setCenter( latLng );
				// cannot delete.
				view.center = latLng;

			} else{

				GMaps.geocode({address: ce_admap_mobile.center , callback: function(results, status) {

					if (status == 'OK') {
						view.center = results[0].geometry.location;
						view.map.setCenter(view.center);
					}

				}

			});

		}

			setTimeout ( function () {$('.admap').find('form').show() } , 2000 );
			var data	=	{action : 'ce_cemap_fetch_ads'}

			if( ce_admap_mobile.is_single_job ) {

				data	= {action : 'ce_cemap_fetch_ads_insingle' , 'ad' : JSON.parse(this.$('#ad_data').html()) }

			}
			this.renderMap(data);
	  	}
	  	// end init function

	  	view.renderMap = function (data) {

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

							//view.map.setCenter( latLng );

							view.center = latLng;

						}

					}
				}
			});

		}


		//End renderMap.

		view.attachMarkerInfowindow = function ( marker, content ) {

			var view	=	this;

			google.maps.event.addListener(marker,'click',function() {

				view.infoWindow.setContent(content);

				view.infoWindow.open(this.map,marker);

			});

		}
		view.multiChoice = function(clickedCluster) {

			if (clickedCluster.getMarkers().length > 1) {

				var markers		=	 clickedCluster.getMarkers();

				var data		=	ce_admap_mobile.heading.replace('%s', markers.length);

				for(var i=0; i<markers.length;i++) {


					var d		=	markers[i].content;

				    data	+= d ;

				}

				this.infoWindow.setContent('<div class="jobs-wrapper" style="width : 230px">'+data + '</div>');

				this.infoWindow.open( this.map, markers[0]);

			    return false;

			}
			return true;
		}

		// call init function
	  	view.init();
	};
	// $(document).ready(function(){
	// 	if(typeof ce_admap_mobile !== 'undefined')
	// 		new mapMobile();
	// })
})(jQuery);