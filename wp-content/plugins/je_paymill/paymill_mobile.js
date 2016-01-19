(function($){
	var mobile_paymill = {
		paymillResponseHandler	:	function (error, result) {
			
            if (error) {            
               	if(error.apierror == 'field_invalid_card_exp')
               		alert(je_paymill.invalid_date);
               	if(error.apierror == 'field_invalid_card_number'){
               		$('#paymill_number').focus();
               		alert(je_paymill.invalid_card);
               	}
               	if(error.apierror == 'field_invalid_card_cvc'){
               		$('#paymill-cvc').focus();
               		alert(je_paymill.invalid_cvc);
               	}

               	$("#submit_paymill").removeAttr('disabled');
               
               	
            } else {              	
                mobile_paymill.submitPayment (result);
            }

            
        },
        submitPayment : function (res) {
			var view = this;
			var page_template	=	et_globals.page_template;
			var action	=	'et_payment_process'
			if(page_template == 'page-upgrade-account.php') {
				action	=	'resume_view_setup_payment';
			}
			var packageID	= $('input[name="et_payment_package"]').val();
			console.log(packageID);
			$.ajax ({
				type : 'post',
				url  : et_globals.ajaxURL,
				data : {
					action		: action,
					jobID		: $('input[name="ad_id"]').val(),
					authorID	: $('input[name="post_author"]').val(),
					packageID	: $('input[name="et_payment_package"]').val(),
					description	: $("#paymill-des").val(),
					paymentType	: 'paymill',
					token 		: res.token,
					coupon_code	: $('#coupon_code').val()
				},
				beforeSend : function () {
					
					$.mobile.showPageLoadingMsg();
				},
				success : function (res) {
					
					if(res.success) {
						console.log(res);
						window.location = res.data.url;
					} else {
						alert(res.msg);
					}
					$.mobile.hidePageLoadingMsg();
					$("#submit_paymill").removeAttr('disabled');
				}
			});
		}

	};
	$(document).on('pageinit' , function () {

		$("#submit_paymill").click(function(){
			$("#submit_paymill").attr('disabled','disabled');
			$("form#paymill_form").trigger("submit");
			return false;
		});

		$("form#paymill_form").submit(function(event){
			
			event.preventDefault ();
			var data={
					  number:$('#paymill_number').val(),
					  exp_month: $('#paymill_exp_month').val(),   // required
				      exp_year: $('#paymill_exp_year').val(),
				      cvc: $('#paymill-cvc').val(),
				      description:$('#paymill-des').text(),
				      cardholder: $('#paymill-cardholder').val()
			   	};
			paymill.createToken(data, mobile_paymill.paymillResponseHandler);
			return false;
		});

	});
})(jQuery);