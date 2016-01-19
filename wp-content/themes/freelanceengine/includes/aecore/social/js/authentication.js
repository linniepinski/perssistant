(function($){
$(document).ready(function(){
	var view = new AE.Views.SocialAuth();
});

AE.Views.SocialAuth = Backbone.View.extend({
	el: 'body',
	events: {
		'submit #form_auth' 	: 'authenticate',
		'submit #form_username' : 'confirm_username',
		'click .gplus_login_btn' : 'gplusDoLogin',
		'click .lkin' : 'lkinDoLogin'
	},
	initialize: function(){
		this.blockUi = new AE.Views.BlockUi();
	},
	gplusDoLogin: function (){
		var view = this;
		$.ajax({
			url : ae_globals.ajaxURL,
			type : "get",
			data :{
				action: "ae_gplus_auth",
			},
			beforeSend: function() {
                view.blockUi.block('.gplus');
            },
			success:function(resp){
				if( resp.success ){
					window.location.href = resp.redirect;
				}
				else{
					AE.pubsub.trigger('ae:notification', {
						msg: resp.msg,
						notice_type: 'error',
					});		
				}
			}
		});
	},
	lkinDoLogin: function(e){
		var view = this;
		$.ajax({
			url : ae_globals.ajaxURL,
			type : "get",
			data :{
				action: "ae_linked_auth",
				state : "click"
			},
			beforeSend: function() {
                view.blockUi.block('.lkin');
            },
			success:function(resp){
				if( resp.success ){
					window.location.href = resp.redirect;
				}
				else{
					AE.pubsub.trigger('ae:notification', {
						msg: resp.msg,
						notice_type: 'error',
					});		
				}
			}
		});
	},
	authenticate: function(event){
		event.preventDefault();
		var form = $(event.currentTarget);
		var view = this;

		var params = {
			url: 	ae_globals.ajaxURL,
			type: 	'post',
			data: {
				action: ae_auth.action_auth,
				content: form.serializeObject()
			},
			beforeSend: function(){
				//submit
				  var button = form.find('input[type=submit]')
				  view.blockUi.block(button);
			}, 
			success: function(resp){
				if ( resp.success ){
					if ( resp.data.status == 'wait' ){
						view.$('.social-auth-step1').fadeOut('fast', function(){
							view.$('.social-auth-step2').fadeIn();	
						});
					} else if ( resp.data.status == 'linked' ){
						//window.location = ae_globals.homeURL;
						window.location.reload();
					}
				}
				else{
					msg = 'ERROR!';
					if(resp != 0){
						msg = resp.msg;
					}
					AE.pubsub.trigger('ae:notification', {
						msg: msg,
						notice_type: 'error',
					});				
				}
			}, 
			complete: function(){
				view.blockUi.unblock();
			}
		}
		$.ajax(params);
	},
	
	confirm_username: function(event){
		event.preventDefault();
		var form = $(event.currentTarget);
		var view = this;

		var params = {
			url: 	ae_globals.ajaxURL,
			type: 	'post',
			data: {
				action: ae_auth.action_confirm,
				content: form.serializeObject()
			},
			beforeSend: function(){
				//form.find('input[type=submit]').loader('load');
				var button = form.find('input[type=submit]');
				view.blockUi.block(button);
			}, 
			success: function(resp){
				//console.log(resp);
				if ( resp.success == true ){
					window.location = ae_globals.homeURL;
				} else {
					alert(resp.msg);
				}
			}, 
			complete: function(){
				//form.find('input[type=submit]').loader('unload');
				view.blockUi.unblock();
			}
		}
		$.ajax(params);
	}
})
})(jQuery);