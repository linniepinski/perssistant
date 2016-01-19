(function($){
	console.log('123abc');

	Stripe.setPublishableKey (je_stripe.public_key);
	var mobile_stripe = {
		stripeResponseHandler : function ( status, response) {
			console.log('responsehandler');
			console.log(status);
			if(status !== 200 && response.error !== undefined ) {
				console.log (response);
				mobile_stripe.validateCard (response);
				$("#submit_stripe").removeAttr('disabled');
				alert(response.error.message);
				return false;
			} else
				mobile_stripe.submitPayment (response);
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
		},

		submitPayment : function (res) {
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
					jobID		: $('input[name="ad_id"]').val(),
					authorID	: $('input[name="post_author"]').val(),
					packageID	: $('input[name="et_payment_package"]').val(),
					paymentType	: 'stripe',
					token 		: res.id ,					
					coupon_code	: $('#coupon_code').val()
				},
				beforeSend : function () {
					$.mobile.showPageLoadingMsg();
					
				},
				success : function (res) {
					$.mobile.hidePageLoadingMsg();					
					if(res.success) {
						window.location = res.data.url;
					} else {
						alert(res.msg);
					}
				$("#submit_stripe").removeAttr('disabled');
				}
			});
		}

	};
	
	$(document).on('pageinit' , function () {

		$("input#submit_stripe").click(function(event){

			event.preventDefault();		
			$("#submit_stripe").attr('disabled','disabled');
			$("form#stripe_form").trigger("submit");
			return false;

		});

		$("form#stripe_form").submit(function (event) {
			event.preventDefault ();
			$("#submit_stripe").attr('disabled','disabled');		
			var $form = $(event.currentTarget);			
			Stripe.createToken($form, mobile_stripe.stripeResponseHandler);
			return false;
		});

	});
})(jQuery);