(function(Views, Models, $, Backbone) {
	Views.ResetPassModal = Backbone.View.extend({
		events: {
			'submit form#resetpass_form': 'doResetPassword',
		},
		initialize: function() {
			AE.Views.Modal_Box.prototype.initialize.call();
			this.blockUi = new AE.Views.BlockUi();
			this.LoadingButtonNew = new Views.LoadingButtonNew();
			this.user = new Models.User();
		},
		doResetPassword: function(event) {

			event.preventDefault();
			this.reset_validator = $("form#resetpass_form").validate({
				rules: {
					new_password: "required",
					re_new_password: {
						required: true,
						equalTo: "#new_password"
					}
				}
			});
			var form = $(event.currentTarget),
				username     = form.find('input#user_login').val(),
				user_key     = form.find('input#user_key').val(),
				new_password = form.find('input#new_password').val(),
				button       = form.find('.btn-submit'),
				view         = this;

			form.find('input').each(function(){
				view.user.set($(this).attr('name'), $(this).val());
			});
			if (jQuery('#pswd_info li.valid').length >= 2 && jQuery('#pswd_info li.required').length >= 1 ){
				if (this.reset_validator.form()) {
					this.user.resetpass({
						beforeSend: function() {
							view.LoadingButtonNew.loading(button);

						},
						success: function(user, status, jqXHR) {
							view.LoadingButtonNew.finish(button);

							if (status.success) {
								AE.pubsub.trigger('ae:notification', {
									msg: status.msg,
									notice_type: 'success',
								});
								window.location.href = ae_globals.homeURL;
								$('.authenticate').trigger('click');
							} else {
								AE.pubsub.trigger('ae:notification', {
									msg: status.msg,
									notice_type: 'error',
								});
							}
						}
					});
				}
			}else{
				jQuery('#pswd_info').fadeIn('slow');
				return false;
			}

		}
	});
	$(document).ready(function(){
		new Views.ResetPassModal({
			el : $('body')
		});
	});
})(AE.Views, AE.Models, jQuery, Backbone);