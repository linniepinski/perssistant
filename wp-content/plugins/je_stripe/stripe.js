(function($) {
	// Modal stripe payment form
	JobEngine.Views.Modal_Stripe_Payment_Form	= JobEngine.Views.Modal_Box.extend({
		el		: jQuery('div#stripe_modal'),
		job		: [],
		events  : {
			'click .modal-close' 			: 'close',
			'submit #stripe_form' 			: 'submit'
			//'click #submit_stripe'			: 'submit'
		},

		initialize	: function(){
			_.bindAll(this , 'stripeResponseHandler','validateCard');
			JobEngine.Views.Modal_Box.prototype.initialize.apply(this, arguments );
			this.openStripeModal ();
			this.job	=	JobEngine.post_job.job;
			Stripe.setPublishableKey (je_stripe.public_key);
		},

		openStripeModal	: function(){
			var job			=	JobEngine.post_job.job,
				packageID	=	job.get('job_package'),
				plans		=	JSON.parse($('#package_plans').html()),
				job_package	=	plans[packageID];
			var currency	=	je_stripe.currency.icon;
			var price		=	job_package.price + currency ;
			if($('.coupon-price').length >0 && $('#coupon_code').val() != '' ) price	=	$('.coupon-price').html();
			var string =  job_package.title + ' (' + price +')';
			this.$el.find('span.plan_name').html(string);
			this.$el.find('span.plan_desc').html(job_package.description);

			this.openModal();

		},

		close : function (event) {
			event.preventDefault();
			this.closeModal();
		},

		validateCard : function (response) {
			var error	=	response.error;
			switch (error.param) {
				case 'number' :
					$('#stripe_number').addClass('error');
					$('#stripe_number').focus ();
					break;
				case 'exp_year' :
					$('#exp_year').addClass('error');
					$('#exp_year').focus ();
					break;
				case 'exp_month' :
					$('#exp_month').addClass('error');
					$('#exp_month').focus ();
					break;
				case 'cvc' :
					$('#cvc').addClass('error');
					$('#cvc').focus ();
					break;
				default :
					break;
			}

			pubsub.trigger('je:notification',{
				msg	: error.message,
				notice_type	: 'error'
			});

			return false;
		},

		submit : function (event) {
			event.preventDefault ();
			var $form = $(event.currentTarget),
				loading		= new JobEngine.Views.LoadingButton ({el : $('#submit_stripe')});
			Stripe.createToken($form, this.stripeResponseHandler);
		},

		stripeResponseHandler : function ( status, response) {
			if(status !== 200 && response.error !== undefined ) {
				console.log (response);
				this.validateCard (response);
			} else
				this.submitPayment (response);
		},

		submitPayment : function (res) {
			var page_template	=	et_globals.page_template;
			var action	=	'et_payment_process'
			if(page_template == 'page-upgrade-account.php') {
				action	=	'resume_view_setup_payment';
			}
			var	loading		= new JobEngine.Views.LoadingButton ({el : $('#submit_stripe')});
			$.ajax ({
				type : 'post',
				url  : et_globals.ajaxURL,
				data : {
					action		: action,
					jobID		: this.job.id,
					authorID	: this.job.get('author_id'),
					packageID	: this.job.get('job_package'),
					paymentType	: 'stripe',
					token 		: res.id ,					
					coupon_code	: $('#coupon_code').val()
				},
				beforeSend : function () {
					loading.loading();
				},
				success : function (res) {
					loading.finish();
					if(res.success) {
						window.location = res.data.url;
					} else {
						pubsub.trigger('je:notification',{
							msg	: res.msg,
							notice_type	: 'error'
						});
					}

				}
			});
		}

	});

	$(document).ready(function () {
		$('#stripe_pay').click(function(e){
			e.preventDefault();
			var payment_form	= new JobEngine.Views.Modal_Stripe_Payment_Form();
			return false;
	    });
	});

})(jQuery);