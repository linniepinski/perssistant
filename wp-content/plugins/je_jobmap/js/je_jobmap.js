(function($) {
	
	

	JobEngine.Views.MapModal	=	JobEngine.Views.Modal_Box.extend({ 
		el		: jQuery('div#modal_job_map'),
		template : _.template($('#je_jobmap_template').html()),
		events  : {
			'click div.modal-close'			: 'closeModal',
			'change select'					: 'changeCat',
			'change input.search-box'		: 'searchJob',
		},
		initialize	: function(){
			JobEngine.Views.Modal_Box.prototype.initialize.apply(this, arguments );
			this.search_params	=	{};
		},

		closeModal : function (time, callback) {
			pubsub.trigger('je_jobmap:modalSearchJob', this.search_params );
			var modal = this;
			time = time || 200,
			this.$overlay.fadeOut(200, function(){
				modal.$el.hide();
				if (typeof callback === 'function'){
					callback();
				}
			});
			return false;
		},

		openModalMap : function ( params ) {
			this.openModal();
			this.renderMap(params);
		},

		renderMap : function (params) {
			var view	=	this;
			view.center	=	params.center;

			view.map_options = {
				'zoom': parseInt(je_jobmap.zoom),
				'center': params.center,
				'mapTypeId': google.maps.MapTypeId.ROADMAP
			};

			view.markers		=	_.clone (params.markers);
			if(view.markerCluster)
				view.markerCluster.clearMarkers ();

			view.infoWindow 	= new google.maps.InfoWindow({maxWidth : 300});

			this.map = new google.maps.Map(document.getElementById("modal_map_inner"), view.map_options);

			view.markerCluster	=	new MarkerClusterer(view.map, view.markers);

			view.markerCluster.onClick  = function(icon) { return view.multiChoice(icon.cluster_); }

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

		searchJob : function(event){
			event.preventDefault();

			this.filterMap();
		},

		filterMap : function () {
			var view 	=	this;
			var params	=	{
				'job_category' : this.$el.find('select').val(),
				'location' : this.$el.find('input[name=job_location]').val(),
				's' : this.$el.find('input[name=s]').val()
			};

			view.search_params	=	_.extend (params, {action : 'je_jobmap_filter'});

			//

			$.ajax ({
				type 	: 'get',
				url 	: et_globals.ajaxURL,
				data 	: params ,
				beforeSend  : function () {

				},
				success : function (resp) {		
					view.clearMap();
					view.markerCluster.clearMarkers ();				
					if(typeof resp.data !== 'undefined' && resp.data.length > 0 ) {						
						//console.log(resp.data);
						var data	=	resp.data;
						view.markers = [];				
						//google.maps.event.trigger(view.map, 'resize');
						for (var i = 0; i < data.length; i++) {
							var content	=	view.template (data[i]);
							// console.log(content);
							
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
				var data		=	je_jobmap.heading.replace('%s', markers.length);
				for(var i=0; i<markers.length;i++) {
					
					var d		=	markers[i].content;
				    data	+= d ;
					
				}		   	
				this.infoWindow.close();
				this.infoWindow.setContent('<div class="jobs-wrapper" style="width : 239px">'+data + '</div>');				
				var info = new google.maps.MVCObject;
				info.set('position', clickedCluster.center_);				
				this.infoWindow.setContent(data);
				this.infoWindow.open( this.map, info);


			    return false;
			}

			return true;
		}


	});

	JobEngine.Views.JobMap	=	Backbone.View.extend({
		el : 'div.wrapper' ,
		template : _.template($('#je_jobmap_template').html()),
		map : null,
		map_options	: {},
		events : {
			'change .jobmap form input.center' : 'changeCenter',
			'change .jobmap form input.zoom' : 'changeZoom',
			'click .jobmap .enlarge'	: 'enlargeMap'
		},

		initialize : function () {

			pubsub.on('je:indexFilter', this.filterMap, this);
			pubsub.on('je_jobmap:modalSearchJob', this.fillParams , this );
			
			var view	=	this;
			view.map_options = {
				'zoom': parseInt(je_jobmap.zoom),
			//	'center': view.center,
				'mapTypeId': google.maps.MapTypeId.ROADMAP,
				// 'scrollwheel': false 
				'scrollwheel': false
			};

			view.markers		=	[];
			view.markerCluster	=	[];
			view.infoWindow 	= 	new google.maps.InfoWindow({autoPan : true ,maxWidth : 300});

			this.map = new google.maps.Map(document.getElementById("je_jobmap"), view.map_options);

			if($('.jobmap').find('form').length > 0) {
				view.map.controls[google.maps.ControlPosition.BOTTOM_LEFT].push( $('.jobmap').find('form').get(0) );
			}

			if(!je_jobmap.lat) {
				GMaps.geocode({address: je_jobmap.center , callback: function(results, status) {	
						if (status == 'OK') {
							view.center = results[0].geometry.location;
							view.map.setCenter(view.center);
							//view.map_options.center =	view.center;
						}	
					}
				});
			} else {
				var latLng	=	new google.maps.LatLng(je_jobmap.lat, je_jobmap.lng );
				view.map.setCenter( latLng );
				view.center = latLng;
			}

			setTimeout ( function () {$('.jobmap').find('form').show() } , 2000 );

			var data	=	{action : 'je_jobmap_fetch_jobs'}
			if( je_jobmap.is_single_job ) {
				data	= {action : 'je_jobmap_fetch_jobs_insingle' , 'job' : JSON.parse(this.$('#job_data').html()) }
			}

			this.renderMap(data);
			
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
						//console.log(resp.data);
						var data	=	resp.data;
						view.markers = [];				
						
						for (var i = 0; i < data.length; i++) {
							var content	=	view.template (data[i]);
							// console.log(content);
							var latLng = new google.maps.LatLng(data[i].lat, data[i].lng);
							var marker = new google.maps.Marker({ 'position': latLng , title : data[i]['post_title'], content : content });

							view.attachMarkerInfowindow ( marker , content );

							view.markers.push(marker);
							
						}				
						view.markerCluster = new MarkerClusterer(view.map, view.markers);
						view.markerCluster.onClick  = function(icon) { return view.multiChoiceMain(icon.cluster_); }

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
			var param	=	_.extend (param, {action : 'je_jobmap_filter'});
			
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
						//console.log(resp.data);
						var data	=	resp.data;
						view.markers = [];				
						//google.maps.event.trigger(view.map, 'resize');
						for (var i = 0; i < data.length; i++) {
							var content	=	view.template (data[i]);
							// console.log(content);
							
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
				this.modal	=	new JobEngine.Views.MapModal ();
			}
			this.modal.openModalMap( { markers : this.markers , center : this.center } );
		},

		updateWidget : function  (e) {
			if($('.jobmap form').length > 0 ) {
				$.ajax({
					type : 'post',
					data : $('.jobmap form').serialize () + '&action=save-widget&sidebar='+ $(e.currentTarget).parents('.ui-sortable').attr('id'),
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
						$('.jobmap input.lat').val(view.center.lat());
						$('.jobmap input.lng').val(view.center.lng());
						view.markerCenter = new google.maps.Marker({
							map : view.map,
							position : results[0].geometry.location,
							draggable : true,
							title : 'Drag me to specify your correct center'
						});

						google.maps.event.addListener(view.markerCenter,'dragend',function (e) {
							$('.jobmap input.lat').val(this.position.lat());
							$('.jobmap input.lng').val(this.position.lng());

							view.updateWidget(e);
							// console.log(e.position);
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

		multiChoiceMain : function(clickedCluster) {
			if (clickedCluster.getMarkers().length > 1) {
				var markers		=	 clickedCluster.getMarkers();
				var data		=	je_jobmap.heading.replace('%s', markers.length);
				for(var i=0; i<markers.length;i++) {					
					var d		=	markers[i].content;				    
				    data	+= d ;
					
				}		   	
				this.infoWindow.close();
				this.infoWindow.setContent('<div class="jobs-wrapper" style="width : 259px">'+data + '</div>');
				//this.infoWindow.open( this.map, markers[0]);
				var info = new google.maps.MVCObject;
				info.set('position', clickedCluster.center_);
				//this.infoWindow.setOptions({maxWidth:299});
				this.infoWindow.setContent(data);
				
				this.infoWindow.open( this.map, info);

			    return false;
			}

			return true;
		}
	});

	$(document).ready(function () {
		
		JobEngine.Jobmap	=	new JobEngine.Views.JobMap();
		
	});
} (jQuery));