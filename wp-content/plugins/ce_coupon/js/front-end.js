(function($) {
	$(document).ready(function () {

		$("form#ad_form").submit(function(){
			var ad			=	CE.Post_Ad.ad;
				packageID	=	ad.get('et_payment_package'),
				plans		=	JSON.parse($('#package_plans').html());

			var	ad_package	=	[];
				_.each(plans, function (element) {
					if(element.ID == packageID ) {
						ad_package	=	element ;
					}
				})
			$('.ad-price').html ('<sup>'+ce_coupon.currency.icon+'</sup>'+ ad_package.et_price);

		});


		$("button.select-plan").click(function(event){

			event.preventDefault();

			var $target 	= $(event.currentTarget),
				packageID 	= $target.attr('data-package'),
				plans		=	JSON.parse($('#package_plans').html());

			var	ad_package	=	[];
			_.each(plans, function (element) {
				if(element.ID == packageID ) {
					ad_package	=	element ;
				}
			})
			$('.ad-price').html ('<sup>'+ce_coupon.currency.icon+'</sup>'+ ad_package.et_price);

		});

		new CE.Views.CE_Coupon;

		$('button.select_plan').click (function () {
			$('.coupon-item').hide();
			$('.coupon-item').parents('li').addClass('hidecoupon');
		});

		$("li.payment-button").click(function(event){

				var $button_coupon = $(event.currentTarget).find("button");

				if( $(event.currentTarget).hasClass('disable-payment') || $button_coupon.hasClass("disable-payment")  ){
					return false;
				}

				return true;
		});

	});

	CE.Views.CE_Coupon = Backbone.View.extend ({
		el : '#coupon_form',
		ad		: [],
		events : {
			'click span.coupon-bar' 	: 'showCouponInput',
			'change input#coupon_code'	: 'checkCouponCode',
		},

		initialize : function (){
			//CE.Views.Modal_Box.prototype.initialize.apply(this, arguments );
			//this.ad	=	CE.Post_Ad.ad;
		},

		showCouponInput : function (event) {
			event.preventDefault();

			var ad			=	CE.Post_Ad.ad,
				packageID	=	ad.get('et_payment_package'),
				plans		=	JSON.parse($('#package_plans').html());

			var	ad_package	=	[];
			_.each(plans, function (element) {
				if(element.ID == packageID ) {
					ad_package	=	element ;
				}
			})

			$('.coupon-item').fadeToggle(200);
			$('.coupon-item').parents('li').toggleClass('hidecoupon');

			if(ce_coupon.currency.align == 'left') {
				$('.ad-price').html ('<sup>'+ce_coupon.currency.icon+'</sup>'+ ad_package.et_price);
			} else {
				$('.ad-price').html (ad_package.price + '<sup>'+ce_coupon.currency.icon+'</sup>');
			}
			$('.ad-price').css('text-decoration' ,'none');

			$('.coupon-price').html('');
			$('#coupon_code').val('');
		},

		checkCouponCode : function (e) {
			e.preventDefault();
			var $target	=	$(e.currentTarget),
				blockUi	=	new CE.Views.BlockUi(),
				code	=	$target.val(),
				ad			=	CE.Post_Ad.ad,
				packageID	=	ad.get('et_payment_package'),
				plans		=	JSON.parse($('#package_plans').html());

				var	ad_package	=	[];
				_.each(plans, function (element) {
					console.log(element);
					console.log(packageID);
					if(element.ID == packageID ) {
						ad_package	=	element ;
					}
				})


			$.ajax({
				type : 'POST',
				url : et_globals.ajaxURL,
				data : { coupon_code : code, action : 'ce-check-coupon-code', packageID : packageID, price : ad_package.et_price},
				beforeSend : function () {
					blockUi.block($target);
				},
				success : function (res) {
					blockUi.unblock();

					if(res.success) { // coupon code is valid, update new price
						$target.removeClass('error');

						if(ce_coupon.currency.align == 'left')
							var coupon_price	=	'<sup>'+ce_coupon.currency.icon+'</sup>' + res.coupon_price;
						else
							var coupon_price	=	res.coupon_price + '<sup>'+ce_coupon.currency.icon+'</sup>';

						$('.ad-price').css('text-decoration' ,'line-through');
						$('.coupon-price').html(coupon_price);

						/*
						* check if 100% or price =0
						*/
						if(res.coupon_price == 0 || res.coupon_price < 0){
							$(".coupon-item").find("button").removeClass("disable-payment");
							$("ul.post-step4 li.clearfix" ).each(function() {
								if(!$( this ).hasClass( "payment-coupon" ))
									$(this).addClass("disable-payment");
							});

						} else {
							$("li.payment-button").removeClass("hidden");
							$(".coupon-item").find("button").addClass("disable-payment");
							$("ul.post-step4 li.clearfix" ).each(function() {
								if(!$( this ).hasClass( "payment-coupon" ))
									$(this).removeClass("disable-payment");
							});

						}

					} else { // coupon code invalid, trigger a pubsub to show user the error message
						pubsub.trigger('ce:notification',{
							msg	: res.msg,
							notice_type	: 'error'
						});
						$target.addClass('error');
						$(".coupon-item").find("button").addClass("disable-payment");
						$("ul.post-step4 li.clearfix" ).each(function() {
								if(!$( this ).hasClass( "payment-coupon" ))
								$(this).removeClass("disable-payment");
							});

					}
				}
			});
		}
	});

})(jQuery);