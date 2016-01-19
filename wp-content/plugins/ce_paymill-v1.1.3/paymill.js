(function($) {

	// Modal paymill payment form
	CE.Views.Modal_Paymill_Payment_Form 	= CE.Views.Modal_Box.extend({
		el		: 'div#paymill_modal',
		ad		: [],
		events  : {
			
			'submit form#paymill_form' 		: 'submitPaymill'			
		
		},

		initialize	: function(){
			
			_.bindAll(this , 'paymillResponseHandler');

			CE.Views.Modal_Box.prototype.initialize.apply(this, arguments );
			this.loading		= new CE.Views.LoadingButton ({el : $('#button_paymill')})
			this.openPaymillModal ();
		
		},

		openPaymillModal	: function(){
			this.ad			=	CE.Post_Ad.ad;
			

			var packageID	=	this.ad.get('et_payment_package'),
				plans		=	JSON.parse($('#package_plans').html()),			
				currency	=	ce_paymill.currency.icon;
				

			var	ad_package	=	[];
			_.each(plans, function (element) {
				if(element.ID == packageID ) {
					ad_package	=	element ;
				}
			})

			var price		=	ad_package.et_price + currency ;
			if($('.coupon-price').html() !== '' && $('#coupon_code').val() != '' ) price	=	$('.coupon-price').html();
			
			this.$el.find('span.plan_name').html( ad_package.post_title + ' (' + price +')');
			this.$el.find('span.plan_desc').html(ad_package.post_content);

			this.openModal();

		},		

		validateCard : function (error) {				
			var type = error.apierror;
			var msg =  ' Unknow error';
			switch (type) {
				case 'field_invalid_card_number' :
					$('#paymill_number').addClass('error');
					$('#paymill_number').focus ();
					msg = ce_paymill.card_number_msg;					
					break;

				case 'field_invalid_card_exp' :
					$('#exp_year').addClass('error');
					$('#exp_year').focus ();
					msg = ce_paymill.exp_msg;					
					break;			
				
				case 'field_invalid_card_cvc' :
					$('#paymill_cvc').addClass('error');
					$('#paymill_cvc').focus ();
					msg = ce_paymill.cvc_msg;
					break;

				case 'invalid_public_key':
					msg = ce_paymill.public_key_msg
					break;

				default : 
					msg =  msg.unknow_error;
					break; 

			}
			return msg;
			
			
		},

		submitPaymill : function (event) {
			
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

		    var card_vld 	= paymill.validateCardNumber(data.number);
		   	var cvc_vld 	= paymill.validateCvc(data.cvc);
		 	var type 		= paymill.cardType(data.number);


		   	if(!card_vld){
		   		pubsub.trigger('ce:notification',{
    				msg	: ce_paymill.card_number_msg,
    				notice_type	: 'error'
    			});
		   		$("#button_paymill").removeAttr("disabled");
    			return false;
		   	}
		   	if(!cvc_vld){
		   		pubsub.trigger('ce:notification',{
    				msg	: ce_paymill.cvc_msg,
    				notice_type	: 'error'
    			});
    			$("#button_paymill").removeAttr("disabled");
    			return false;
		   	}
		   	var type_input = $.trim(data.cardholder).toLowerCase();
		   	

		   	if(type.toLowerCase()  != $.trim(data.cardholder).toLowerCase()){
		   		pubsub.trigger('ce:notification',{
    				msg	: ce_paymill.name_card_msg,
    				notice_type	: 'error'
    			});
    			
    			$("#button_paymill").removeAttr("disabled");
    			return false;
		   	}

		   	paymill.createToken(data,this.paymillResponseHandler);

		  	return false;
				
		},

		paymillResponseHandler : function ( error, response) {
			
			if (error) {
				
            	pubsub.trigger('ce:notification',{
    				msg	: this.validateCard(error),
    				notice_type	: 'error' 
    			});
    			$("#button_paymill").removeAttr("disabled");               	
               	return false;
            } else {
            	this.loading.loading();
              	$("#button_paymill").attr("disabled", "disabled");
                this.submitPayment (response);
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
					ID			: this.ad.id,
					authorID	: this.ad.get('post_author'),
					packageID	: this.ad.get('et_payment_package'),
					description	: $("#paymill-des").val(),
					paymentType	: 'paymill',
					token 		: res.token,
					coupon_code	: $('#coupon_code').val()
				},
				beforeSend : function () {
					
					//loading.loading();
				},
				success : function (res) {
					
					view.loading.finish();			
					
					if(res.success) {
						pubsub.trigger('ce:notification',{
							msg	: res.data.msg,
							notice_type	: 'success'
						});

						window.location = res.data.url;
					} else {
						pubsub.trigger('ce:notification',{
							msg	: res.msg,
							notice_type	: 'error'
						});
						$("#button_paymill").removeAttr("disabled");
					}
				}
			});
		}

	}); 

	jQuery(document).ready(function () {

		$('#paymill_pay').click(function(e){

			new CE.Views.Modal_Paymill_Payment_Form();			

	    });

	});

})(jQuery);