(function($){
$(document).ready(function(){
	new optionMail_Alert();
});

var optionMail_Alert = Backbone.View.extend({
	el: '#setting-mails',
	events: {
		'click .mail-template .trigger-editor' 	: 'triggerEditor',
		'click .mail-template .reset-default' 	: 'onResetMail',
		'focusout .mail-input' 					: 'changeMail'
	},
	initialize: function(){
		var view = this;
		//$('.payment-setting').toggle(); 
		this.loading = new JobEngine.Views.BlockUi();
		$('.mail-control-btn').toggle();
		$('.btn-template-help').click(function(){
			$('.mail-control-btn').slideToggle(300);
			return false;
		});
		
	},

	changeMail : function (event) {
		var view		=	this,
			id 			= 	$(event.currentTarget).attr('id'),
			ed 			=	tinyMCE.get('et_mail_alert_message'),
			new_value	=	ed.getContent (),
			name 	= $(event.currentTarget).attr('data-name');	
		
		view.updateMail(name, new_value, {
			beforeSend: function(){
				view.loading.block($(event.currentTarget).parents ('.mail-template'));
			}, success: function(){
				view.loading.unblock();
			}
		}); 

		$(event.currentTarget).closest('.mail-template').find('a.trigger-editor').addClass ('activated');
	} ,

	
	updateMail : function(name, value, params){

		var params = $.extend( {
			url: ajaxurl,
			type: 'post',
			data: {
				action: 'et_mail_alert_message',
				content: {
					name: name,
					value: value,
				}
			},
			beforeSend: function(){},
			success: function(){}
		}, params );

		$.ajax(params);
	},
	
	triggerEditor : function (event) {
		event.preventDefault ();
		var $target 	=	$(event.currentTarget),
			$textarea 	=	$target.parents('.item').find('textarea');
		if($target.hasClass('activated')) {
			tinyMCE.execCommand('mceRemoveControl', false, $textarea.attr('id'));
			$target.removeClass('activated');
		}else {
			tinyMCE.execCommand('mceAddControl', false, $textarea.attr('id'));
			$target.addClass('activated');
		}
	},
	
	resetEmail : function(name, params){		
		var params = $.extend( {
			url: ajaxurl,
			type: 'post',
			data: {
				action: 'et_reset_mail_alert',
				content: {
					mail: name				
				}
			},
			beforeSend: function(){},
			success: function(){}
		}, params );

		$.ajax(params);

	},

	onResetMail : function (event) {		
		event.preventDefault ();

		var $target 	=	$(event.currentTarget),
			$textarea	=	$target.parents('.mail-template').find('textarea'),
			mail_type	=	$textarea.attr ('name');	

		this.resetEmail(mail_type, {
			beforeSend: function(){
				//view.loading.block(container);
			},
			success: function(resp){
				//view.loading.unblock();
	        
				if (resp.success){				
					//$textarea.val (response.msg);

					 var ed 			=	tinyMCE.get($textarea.attr('id'));
					 ed.setContent(resp.data.template);
				}
			}
		});		
			
			
	}
});
})(jQuery);