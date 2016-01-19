(function($) {	
	var mobile_stripe = {

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

			alert( error.message );

			$("#submit_stripe").removeAttr("disabled");
		},

		stripeResponseHandler : function (status , response) {
			var view = this;
			if(status !== 200 && response.error !== undefined ) {				
				mobile_stripe.validateCard (response);
			} else {				
				$.ajax({
					url 	: et_globals.ajaxURL,
					type 	: 'post',
					contentType	: 'application/x-www-form-urlencoded;charset=UTF-8',
					data	: {
						action		: 'et_payment_process',
						ID			: $('input[name="ad_id"]').val(),
						author		: $('input[name="post_author"]').val(),
						packageID	: $('input[name="et_payment_package"]').val(),
						paymentType	: 'stripe',
						token 		: response.id ,
						coupon_code	: $('#coupon_code').val()
					},
					beforeSend : function() {
						$.mobile.showPageLoadingMsg();
					},
					success : function (response) {
						
						$.mobile.hidePageLoadingMsg();

						if(response.success) {
							window.location = response.data.url;
						} else {
							alert ( response.msg );
						}
						$("#submit_stripe").removeAttr("disabled");
					}
				});
			}
		}
		
	}


	$(document).on('pageinit' , function () { 
		if(typeof ce_stripe !== "undefined"){			
			Stripe.setPublishableKey (ce_stripe.public_key);
		}

		$('#stripe_form').submit(function (e) {e.preventDefault();});

		$('#submit_stripe').on('click' , function (event) {
			event.preventDefault();

			$(this).attr("disabled", "disabled");			
			
			var $form 		= $(event.currentTarget).parents('form');		

			var card_number = $form.find("#stripe_number").val();	
			var card_type   = $form.find("#name_card").val();	
			var card_vld = Stripe.card.validateCardNumber(card_number);

			if(!card_vld){
				alert ( ce_stripe.card_number_msg );

				$form.find("#stripe_number").addClass('error');
				$form.find("#stripe_number").focus();
				$("#submit_stripe").removeAttr("disabled");

				return false;
			}
			// validate Card type
			var type_vld = Stripe.card.cardType(card_number);
			
			if(type_vld.toLowerCase() != $.trim(card_type).toLowerCase() ){
				alert ( ce_stripe.name_card_msg );
				$form.find("#name_card").addClass('error');
				$form.find("#name_card").focus();
				$("#submit_stripe").removeAttr("disabled");
				return false;
			}
			// this.loading.loading();
			Stripe.createToken($form, mobile_stripe.stripeResponseHandler);
			///return false;
		});

	});
})(jQuery);