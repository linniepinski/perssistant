(function($) {
	$(document).ready( function () {
	// Modal stripe payment form
		CE.Views.PPExpress	= CE.Views.Modal_Box.extend({
			el		: $('li#ppexpress_checkout'),
			ad		: [],
			events  : {
				'click #ce_ppexpress' 			: 'submitPayment'			
			},

			initialize	: function(){				
				this.dg = new PAYPAL.apps.DGFlow( {
					trigger: 'ce_ppexpress',
					expType: 'instant'
					 //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
				});
			},

			submitPayment : function (event) {
				event.preventDefault();
				if($(event.currentTarget).closest("li").hasClass("disable-payment") )
					return false;
				var page_template	=	et_globals.page_template;
				var action	=	'et_payment_process'
				if(page_template == 'page-upgrade-account.php') {
					action	=	'resume_view_setup_payment';
				}
				this.ad	=	CE.Post_Ad.ad;
				
				$.ajax ({
					type : 'post',
					url  : et_globals.ajaxURL,
					data : {
						action		: action,
						ID		: this.ad.id,
						authorID	: this.ad.get('author_id'),
						packageID	: this.ad.get('et_payment_package'),
						paymentType	: 'ce_ppexpress',
						coupon_code	: $('#coupon_code').val()
					},
					beforeSend : function () {
						//loading.loading();
					},
					success : function (res) {
						var view	=	this;
						if(res.ACK === 'Success') {
							//$('#ppexpress_form').append ('<input type="hidden" name="token" value="'+res.TOKEN+'" />')
							$('#ppexpress_form').attr('action' , res.url);
							view.dg = new PAYPAL.apps.DGFlow( {
								trigger: 'ce_ppexpress',
								expType: 'instant'
								 //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
							});
							$('#ppexpress_form').trigger('click');
							$('#ppexpress_form').submit();
						}

						if( res.success ) {
							window.location.href = res.data.url;
						}

					}
				});
			}

		});

	
		new CE.Views.PPExpress();

	});

})(jQuery);