(function($) {
	// Modal paymill payment form
	JobEngine.Views.Modal_paymill_Payment_Form	= JobEngine.Views.Modal_Box.extend({
		el		: jQuery('div#paymill_modal'),
		job		: [],
		events  : {
			'click .modal-close' 			: 'close',
			'submit #paymill_form' 			: 'submit'
			//'click #submit_paymill'			: 'submit'
		},

		initialize	: function(){
			this.bind('waiting', this.waiting, this);
			this.bind('endWaiting', this.endWaiting, this);

			JobEngine.Views.Modal_Box.prototype.initialize.apply(this, arguments );
			this.openpaymillModal ();
			this.job	=	JobEngine.post_job.job;
			//paymill.setPublishableKey (je_paymill.public_key);
		},

		openpaymillModal	: function(){
			var job			=	JobEngine.post_job.job,
				packageID	=	job.get('job_package'),
				plans		=	JSON.parse($('#package_plans').html()),
				job_package	=	plans[packageID];
			var currency	=	je_paymill.currency.icon;
			var price		=	job_package.price + currency ;
			if($('.coupon-price').html() !== '' && $('#coupon_code').val() != '' ) price	=	$('.coupon-price').html();
			this.$el.find('span.plan_name').html( job_package.title + ' (' + price +')');
			this.$el.find('span.plan_desc').html(job_package.description);
			
			this.openModal();

		},

		close : function (event) {
			event.preventDefault();
			this.closeModal();
		},

		validateCard : function (response) {
			console.log (response)
			switch (response) {
				case 'field_invalid_card_number' :
					$('#paymill_number').addClass('error');
					$('#paymill_number').focus ();
					return je_paymill.invalid_card;
				break;
				case 'field_invalid_card_exp' :
					$('#paymill_exp_month').addClass('error');
					$('#paymill_exp_year').addClass('error');
					$('#paymill_exp_month').focus ();
					return je_paymill.invalid_date;
				break;
				case 'field_invalid_card_cvc' :
					$('#paymill_cvc').addClass('error');
					$('#paymill_cvc').focus ();
					return je_paymill.invalid_cvc;
				break;
				case  'field_invalid_account_holder' :
					$('#paymill-cardholder').addClass('error');
					$('#paymill-cardholder').focus ();
					return je_paymill.invalid_account_holder;
				default : 
					return je_paymill.unknow_error;
					break; 
			}
		},

		submit : function (event) {
			event.preventDefault ();
			var data={
					  number:$('#paymill_number').val(),
					  exp_month: $('#paymill_exp_month').val(),   // required
				      exp_year: $('#paymill_exp_year').val(),
				      cvc: $('#paymill-cvc').val(),
				      description:$('#paymill-des').text(),
				      cardholder: $('#paymill-cardholder').val()
			   	};
			this.trigger('waiting');
			paymill.createToken(data, this.paymillResponseHandler);
		
		},
		
		paymillResponseHandler	:	function (error, result) {
			
            if (error) {
            	pubsub.trigger('je:notification',{
    				msg	: this.validateCard(error.apierror),
    				notice_type	: 'error'
    			});
               	this.trigger('endWaiting');
            } else {
              
                this.submitPayment (result);
            }

            
        },
       
		submitPayment : function (res) {
			var view = this;
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
					description	: $("#paymill-des").val(),
					paymentType	: 'paymill',
					token 		: res.token,
					coupon_code	: $('#coupon_code').val()
				},
				beforeSend : function () {
					//loading.loading();
				},
				success : function (res) {
					//loading.finish();
					view.trigger('endWaiting');
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
		},
		waiting : function (e) {
			console.log('trigger');
			this.loading		= new JobEngine.Views.LoadingButton ({el : $('#submit_paymill')});
			this.loading.loading();
		},
		endWaiting : function () {
			this.loading.finish();
		}	

	});

	$(document).ready(function () {
		$('#paymill_pay').click(function(e){
			e.preventDefault();
			var payment_form	= new JobEngine.Views.Modal_paymill_Payment_Form();
			return false;
	    });
	});

})(jQuery);