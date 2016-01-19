(function($) {
	// Modal stripe payment form
	CE.Views.Modal_Stripe_Payment_Form	= CE.Views.Modal_Box.extend({
		el		: $('div#stripe_modal'),
		ad		: [],
		events  : {
			
			'submit form#stripe_form' 		: 'submitStripe'
			
		},

		initialize	: function(){
			
			_.bindAll(this , 'stripeResponseHandler');

			//CE.Views.Modal_Box.prototype.initialize.apply(this, arguments );
			this.openStripeModal ();
			this.ad	=	CE.Post_Ad.ad;
			Stripe.setPublishableKey (ce_stripe.public_key);
			this.loading		= new CE.Views.LoadingButton ({el : $('#submit_stripe')});
			this.count = 1;
		},

		openStripeModal	: function(){
			var ad			=	CE.Post_Ad.ad;
				packageID	=	ad.get('et_payment_package'),
				plans		=	JSON.parse($('#package_plans').html()),
				currency	=	ce_stripe.currency.icon;

			var	ad_package	=	[];
			_.each(plans, function (element) {
				if(element.ID == packageID ) {
					ad_package	=	element ;
				}
			})
				
			var price		=	ad_package.et_price + currency;
			if($('.coupon-price').html() !== '' && $('#coupon_code').val() != '' ) 
			price	=	$('.coupon-price').html();
			
			
			this.$el.find('span.plan_name').html( ad_package.post_title + ' (' + price +')');
			this.$el.find('span.plan_desc').html( ad_package.post_content );

			this.openModal();


		},		

		validateCard : function (response) {
			
			var error	=	response.error;
			switch (error.param) {
				case 'number' :
					//$('#stripe_number').addClass('error');
					$('#stripe_number').focus ();
					break;
				case 'exp_year' :
					//$('#exp_year').addClass('error');
					$('#exp_year').focus ();
					break;
				case 'exp_month' :
					//$('#exp_month').addClass('error');
					$('#exp_month').focus ();
					break;
				case 'cvc' :
					//$('#cvc').addClass('error');
					$('#cvc').focus ();
					break;
				default : 
					break;
				$("#submit_stripe").removeAttr("disabled"); 
			}

			pubsub.trigger('ce:notification',{
				msg	: error.message,
				notice_type	: 'error'
			});
			$("#submit_stripe").removeAttr("disabled", "disabled");

			
		},

		submitStripe : function (event) {
			event.preventDefault;
			var $form 		= $(event.currentTarget);

			if (this.beenSubmitted) {
				return false;
			}				
			
			if(this.count > 1) {
				return false;
			}
				
			this.count++;			
			this.loading.loading();
			
			Stripe.createToken($form, this.stripeResponseHandler);
			return false;
			
		},

		stripeResponseHandler : function ( status, response) {
			
			var view = this;
			if(status !== 200 && response.error !== undefined ) {				
				view.validateCard (response);
				view.loading.finish();
				view.closeModal();
				return false;
			} else {
				view.submitPayment (response);

			}
		},

		submitPayment : function (res) {			
			var view = this;
			var page_template	=	et_globals.page_template;
			var action			=	'et_payment_process'

			if(page_template == 'page-upgrade-account.php') {
				action	=	'resume_view_setup_payment';
			}

			var	loading		= new CE.Views.LoadingButton ({el : $('#submit_stripe')});
			$.ajax ({
				type : 'post',
				url  : et_globals.ajaxURL,
				data : {
					action		: action,
					ID			: this.ad.id,
					authorID	: this.ad.get('post_author'),
					packageID	: this.ad.get('et_payment_package'),
					paymentType	: 'stripe',
					token 		: res.id ,
					coupon_code	: $('#coupon_code').val()
				},
				beforeSend : function () {
					
					//$("#submit_stripe").removeAttr("disabled");
				},
				success : function (res) {
					view.loading.finish();
					if(res.success) {						
						window.location = res.data.url;
					} else {
						pubsub.trigger('ce:notification',{
							msg	: res.msg,
							notice_type	: 'error'
						});
					}
					
					view.count = 0;
					$("#submit_stripe").removeAttr("disabled");
					$("form#reset_stripe").trigger('click');
					view.closeModal();

				}
			});
		}

	});

	$(document).ready(function () {

		$('#stripe_pay').click(function(event){
			if( $(event.currentTarget).closest("li").hasClass("disable-payment") )
				return false;			
			new CE.Views.Modal_Stripe_Payment_Form();
			//return false;
	    });

	});

})(jQuery);