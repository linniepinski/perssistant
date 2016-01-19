//(function($) {	
	
	var mobile_paymill = {
		paymillResponseHandler : function ( error, response) {
			
			if (error) {
            	
            	if(error.apierror == "field_invalid_card_exp")
            		alert(ce_paymill.exp_msg);
            	if(error.apierror == "field_invalid_card_number")
            		alert(ce_paymill.card_number_msg);
            	if(error.apierror == "field_invalid_card_cvc")
            		alert(ce_paymill.cvc_msg);

            	$("#button_paymill").removeAttr("disabled");
            	return false;
            	

            } else {
            	console.log(response);
            	//this.loading.loading();
              	//$("#button_paymill").attr("disabled", "disabled");
                mobile_paymill.submitPayment (response);
            }

		},
		submitPayment : function (res) {			
			
			var view = this;
			var page_template	=	et_globals.page_template;
			//var loading			= 	new CE.Views.LoadingButton ({el : $('#button_paymill')})
			var action			=	'et_payment_process'
			if(page_template == 'page-upgrade-account.php') {
				action	=	'resume_view_setup_payment';
			}

			$.ajax ({
				type : 'post',
				url  : et_globals.ajaxURL,
				data : {
					action		: action,
					ID			: $('input[name="ad_id"]').val(),
					author		: $('input[name="post_author"]').val(),
					packageID	: $('input[name="et_payment_package"]').val(),
					description	: $("#paymill-des").val(),
					paymentType	: 'paymill',
					token 		: res.token,
					coupon_code	: $('#coupon_code').val()
				},
				beforeSend : function () {
					$.mobile.showPageLoadingMsg();
					//loading.loading();
				},
				success : function (res) {
					$.mobile.hidePageLoadingMsg();					
					
					if(res.success) {
						console.log('successful payment');					

						window.location = res.data.url;
					} else {						
						
						alert ( res.msg );
					}
					$("#button_paymill").removeAttr("disabled");
				}
			});
		}

	};
	$(document).on('pageinit' , function () { 

		$('form#paymill_form').submit(function(event){
			
			event.preventDefault();
			
			$("#button_paymill").attr("disabled", "disabled");
						
			var $form 		= $(event.currentTarget),
				view 		= this;		    
		    var data =  {
				number: 	$('#paymill_number').val(),  // required, ohne Leerzeichen und Bindestriche
				exp_month: 	$('#paymill_exp_month').val(),   // required
				exp_year: 	$('#paymill_exp_year').val(),     // required, vierstellig z.B. "2016"
				cvc: 		$('#paymill_cvc').val(),
				cardholder: $('#paymill_card_holdername').val(), // optional
		    };                   // Info dazu weiter unten

		    //var card_vld 	= paymill.validateCardNumber(data.number);		    
		   	//var cvc_vld 	= paymill.validateCvc(data.cvc);
		 	//var type 		= paymill.cardType(data.number);		   		   	
		   	//var type_input = $.trim(data.cardholder).toLowerCase();
		   
		   	// if(!type_input){
		    // 	$('#paymill_card_holdername').addClass('error');
		    // 	$('#paymill_card_holdername').focus();
		    // 	return false;
		    // }		   
		   	
		   	paymill.createToken(data,mobile_paymill.paymillResponseHandler);

		  	return false;
				
		});

	});
//});