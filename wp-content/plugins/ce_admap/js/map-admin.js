(function($){
	CE.map_admin = Backbone.View.extend({
		el:'#ce_map_settings',
		events :{
			'click .button-enable a' : 'save-map-settings'
		},
		initialize : function(){
			this.blockUI = new CE.Views.BlockUi();
		},
		'save-map-settings' : function(event){

			var target 	= $(event.currentTarget),
				view 	= this;

			var val = 0;
			if(target.hasClass('active'))
			 	val = 1;
			 $.ajax({
				    type: "POST",
				    url: et_globals.ajaxURL,
				   	data: {
				      	action : 'map-save-settings',
			      		name 	: target.attr('rel'),
			      		val 	: val,
				    },
				    beforeSend : function(){
				      	view.blockUI.block(target);
				    },
				    success: function(resp){
	                   	view.blockUI.unblock();
	                   	target.closest('.button-enable').find('a').removeClass('selected');
	                  	target.addClass('selected');
				    }
				});
			return false;
			}

	})
	jQuery(document).ready(function(){
		new CE.map_admin();
	});
}(jQuery))