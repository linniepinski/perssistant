(function($){
	CE.AdAlert = Backbone.View.extend({
		el : "#ce_alert",
		events : {
			'submit form#frm_alert' : 'addSubscriber',
			//'change .chosen-select' : 'chooseOption',
			'submit form#unsubscribe_form' :'unSubscriber',
			'focus  input.subs-email'  : 'resetEmail',
 		},
 		initialize : function(){
			$('.chosen-select').removeClass('hide');
			if($("#ce_alert").length > 0){
				$('.chosen-select').chosen();
				jQuery.validator.addMethod("chosen_select", function(value, element) {
					var val = $(element).val();
					return $.isNumeric(val) ||  $.isArray(val);
					}, "Please select a option.");
				this.validator	= this.$('form.frm_alert').validate({
					ignore: [],
					rules: {
						email	: {
							required: true,
							email : true
						},
					},
					errorPlacement: function(error, element) {
				     if (element.hasClass('chosen-select')) {
				        var show = $(element).closest(".form-group").find(".chosen-container");
				        error.appendTo(show);
				     } else {
				         error.insertAfter(element);
				     }
				   }
				});
			}


 		},
 		chooseOption : function(event){
 			event.preventDefault();
 			var target = $(event.currentTarget);
 			var val = target.val();
 			if($.isNumeric(val) || $.isArray(val)){
 				var item = target.closest('.form-group');
 				item.find('label.error').remove();
 			}
 		},

 		addSubscriber : function(event){
 			event.preventDefault();
 			var view = this;
 			var block =  new CE.Views.BlockUi();
 			var target = $(event.currentTarget);
 			var form = target.serialize();

 			$.ajax({
 				type : 'post',
 				data : form,
 				url : et_globals.ajaxURL,
 				beforeSend: function(event){
 					block.block($(view.el).find('button'));

 				},
 				success : function(resp){
 					block.finish();
 					if(resp.success){
 						pubsub.trigger('ce:notification', {notice_type : 'success' , msg : resp.msg});
 					} else {
 						pubsub.trigger('ce:notification', {notice_type : 'error' , msg : resp.msg});
 					}
 				}
 			})
 			return false;
 		},

 		unSubscriber : function(event){
 			var target 	= $(event.currentTarget);
 			var data  	= target.serialize();

 			$.ajax({
 				url : et_globals.ajaxURL,
 				type : 'post',
 				data :data,
 				beforeSend : function(){
 				},
 				success : function(resp){
 					if(resp.success){
 						pubsub.trigger('ce:notification', {notice_type : 'success' , msg : resp.msg});
 					} else
 						pubsub.trigger('ce:notification', {notice_type : 'error' , msg : resp.msg});
 				}
 			});
 			return false;
 		},
 		resetEmail : function(event){
 			var target = $(event.currentTarget),
 				email = target.val();
 			if(email == 'example@example.com')
 				target.val('');
 		},

	});
	$(document).ready(function(){
		new CE.AdAlert();
	});
})(jQuery);