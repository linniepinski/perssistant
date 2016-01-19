(function($) {
	$(document).ready( function () {
	// Modal stripe payment form
	JobEngine.Views.PPExpress	= JobEngine.Views.Modal_Box.extend({
		el		: $('li#ppexpress_checkout'),
		job		: [],
		events  : {
			'click #je_ppexpress' 			: 'submitPayment'			
		},

		initialize	: function(){
			this.job	=	JobEngine.post_job.job;
			console.log('pp exxpress init');
		},

		submitPayment : function (event) {
			event.preventDefault();
			var page_template	=	et_globals.page_template;
			var action	=	'et_payment_process'
			if(page_template == 'page-upgrade-account.php') {
				action	=	'resume_view_setup_payment';
			}
			
			$.ajax ({
				type : 'post',
				url  : et_globals.ajaxURL,
				data : {
					action		: action,
					jobID		: this.job.id,
					authorID	: this.job.get('author_id'),
					packageID	: this.job.get('job_package'),
					paymentType	: 'je_ppexpress',
					coupon_code	: $('#coupon_code').val()
				},
				beforeSend : function () {
					//loading.loading();
				},
				success : function (res) {
					if(res.ACK === 'Success') {
						//$('#ppexpress_form').append ('<input type="hidden" name="token" value="'+res.TOKEN+'" />')
						$('#ppexpress_form').attr('action' , res.url);
						var dg = new PAYPAL.apps.DGFlow( {
							trigger: 'je_ppexpress',
							expType: 'instant'
							 //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
						});
						$('#ppexpress_form').trigger('click');
						$('#ppexpress_form').submit();
					}

				}
			});
		}

	});

	
		new JobEngine.Views.PPExpress();

		var dg = new PAYPAL.apps.DGFlow( {
			trigger: 'je_ppexpress',
			expType: 'instant'
			 //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
		});
	});

})(jQuery);