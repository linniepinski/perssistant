(function($, Views) {
    Views.StripeForm = Views.Modal_Box.extend({
        el: jQuery('div#stripe_modal'),
        events: {
            'submit form#stripe_form': 'submitStripe'
        },
        initialize: function(options) {
            Views.Modal_Box.prototype.initialize.apply(this, arguments);
            // bind event to modal
            _.bindAll(this, 'stripeResponseHandler', 'setupData');
            Stripe.setPublishableKey(ae_stripe.public_key);
            this.blockUi = new Views.BlockUi();
            // catch event select extend gateway
            AE.pubsub.on('ae:submitPost:extendGateway', this.setupData);
        },
        // callback when user select stripe, set data and open modal
        setupData: function(data) {
            if (data.paymentType == 'stripe') {
                this.openModal();
                this.data = data,
                plans = JSON.parse($('#package_plans').html());

                var packages	=	[];
                _.each(plans, function (element) {
					if(element.sku == data.packageID ) {
						packages	=	element ;
					}
				})
				var align = parseInt(ae_stripe.currency.align);
                if(align) {
                    var price       =   ae_stripe.currency.icon + packages.et_price; 
                }else {
                    var price       =   packages.et_price + ae_stripe.currency.icon;     
                }
				

				//if($('.coupon-price').html() !== '' && $('#coupon_code').val() != '' ) price	=	$('.coupon-price').html();
				
				
				this.$el.find('span.plan_name').html( packages.post_title + ' (' + price +')');
				this.$el.find('span.plan_desc').html( packages.post_content );
            }
        },
        submitStripe: function(event) {
            event.preventDefault();
            var $form = $(event.currentTarget);
            this.blockUi.block($form);
            Stripe.createToken($form, this.stripeResponseHandler);
        },
        validateCard: function(response) {
            var error = response.error;
            switch (error.param) {
                case 'number':
                    //$('#stripe_number').addClass('error');
                    $('#stripe_number').focus();
                    break;
                case 'exp_year':
                    //$('#exp_year').addClass('error');
                    $('#exp_year').focus();
                    break;
                case 'exp_month':
                    //$('#exp_month').addClass('error');
                    $('#exp_month').focus();
                    break;
                case 'cvc':
                    //$('#cvc').addClass('error');
                    $('#cvc').focus();
                    break;
                default:
                    break;
                    $("#submit_stripe").removeAttr("disabled");
            }
            AE.pubsub.trigger('ae:notification', {
                msg: error.message,
                notice_type: 'error'
            });
            $("#submit_stripe").removeAttr("disabled", "disabled");
        },
        stripeResponseHandler: function(status, response) {
            var view = this;
            if (status !== 200 && response.error !== undefined) {
                view.validateCard(response);
                view.blockUi.unblock();
                // view.closeModal();
                return false;
            } else {
                // console.log('Before all submit payment');
                view.submitPayment(response);
            }
        },
        submitPayment : function (res) {
			var view = this;
			view.data.token = res.id;
			$.ajax ({
				type : 'post',
				url  : ae_globals.ajaxURL,
				data : view.data,
				beforeSend : function () {
					//$("#submit_stripe").removeAttr("disabled");
				},
				success : function (res) {
					view.blockUi.unblock();
					if(res.success) {						
						view.closeModal();
						window.location = res.data.url;
					} else {
						AE.pubsub.trigger('ae:notification',{
							msg	: res.msg,
							notice_type	: 'error'
						});						
					}
					// view.count = 0;
					// $("#submit_stripe").removeAttr("disabled");
					// $("form#reset_stripe").trigger('click');
				}
			});
		}
    });
    // init stripe form
    $(document).ready(function() {
        new Views.StripeForm();
    });
})(jQuery, AE.Views);