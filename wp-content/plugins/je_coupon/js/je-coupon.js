(function ($) {
	$(document).ready(function () {
		new JobEngine.Views.Coupon();
	});
	JobEngine.Views.Coupon = Backbone.View.extend ({
		el : '#coupon-manage',
		events : {
			'change select#date_limit' 				: 'changeDateLimit',
			'change #product' 						: 'addProduct',
			'click .list-coupon .coupon a'			: 'removeProduct',
			'click #save_coupon'					: 'saveCoupon',
			'click #add_new_coupon'					: 'showNewCouponForm',
			'click #back_to_list'					: 'backToList',
			'click .job-coupon-list .edit'  		: 'loadEditForm',
			'click .job-coupon-list .delete'  		: 'deleteCoupon',
			'click .coupon-page-navigation li a'	: 'changePage'
		},
		/**
		 * initialize view
		*/
		initialize : function () {
			this.added_product	=	[];
			this.styleSelector(this.$el);
			$('input.sdate').datepicker({dateFormat : 'yy-mm-dd'});
			$('form#coupon_form').validate ({
				errorPlacement: function(error, element) {
					//element.addClass('color-error');
				}

			});
		},
		/**
		 * style the selector
		*/
		styleSelector : function(container){
			// apply custom look for select box
			$(container).find('.select-style select').each(function(){
				var $this = jQuery(this),
					title = $this.attr('title'),
					selectedOpt	= $this.find('option:selected');
				
				if( selectedOpt.val() !== '' ){
					title = selectedOpt.text();
				}

				$this.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
				.after('<span class="select">' + title + '</span>')
				.change(function(){
					var val = jQuery('option:selected',this).text();
					jQuery(this).next().text(val);
				});
			});
		},

		changeDateLimit : function (e) {
			$target	=	$(e.currentTarget);
			//console.log ($target);
			if($target.val() == 'on') {
				$target.parents('.form-item').css('height', '120px');
				$('.date').show(500);
			} else {
				$target.parents('.form-item').css('height', '65px');
				$('.date').hide(500);
			}
		},
		/**
		 * add payment plan to list of product which coupon will be used on
		*/
		addProduct : function (e) {
			e.preventDefault();
			var $target	=	$(e.currentTarget),
				title	=	$target.find(":selected").text(),
				pro_id	=	$target.val();

			if( !pro_id || $.inArray( pro_id , this.added_product ) > 0 /* this.added_product.indexOf(pro_id) >= 0*/ ) return;
			_.templateSettings = {
			    evaluate    : /<#([\s\S]+?)#>/g,
				interpolate : /\{\{(.+?)\}\}/g,
				escape      : /<%-([\s\S]+?)%>/g
			};

			var	html	=	new _.template('<div class="coupon" >'+
	        									'<a href="#">[x]</a><span>{{ title }}</span>'+
	        									'<input  data-id="{{ pro_id }}" type="hidden" name="added_product[{{ pro_id }}]" class="added_product" value="{{ title }}" />'+
	        								'</div>'
	        							);
			
			this.added_product[pro_id]	=	pro_id;
			
			var product	=	{title : title, pro_id : pro_id };
			$('.list-coupon').append (html(product));



		},
		/**
		 * remove payment plan from list of product which coupon will be used on.
		*/
		removeProduct : function (e) {
			e.preventDefault();
			var $target	=	$(e.currentTarget),
				pro_id	=	$target.parents('.coupon').find('input').attr('data-id');

			if( $.inArray (pro_id , this.added_product ) ) {
				var index	=	$.inArray (pro_id , this.added_product );
				this.added_product.splice(index, 1);
			}
			$target.parents('.coupon').remove();
			
		},
		/**
		 * update coupon
		*/
		saveCoupon : function (e) {
			e.preventDefault();
			var view	=	this,
				target	=	$(e.currentTarget),
				$form	=	$('form#coupon_form'),
				blockUI	=	new JobEngine.Views.BlockUi();
			if($form.valid()) {
				$.ajax({
					type : 'post',
					url	: et_globals.ajaxURL,
					data : $form.serialize(),
					beforeSend : function () {
						blockUI.block(target);
					},
					success : function (res) {
						blockUI.unblock();
						if(!res.success) {
							alert (res.msg);
						} else {
							view.loadPage(1);
						}						
					}
				});
			}
		},

		changePage : function (e) {
			e.preventDefault();
			var $target	=	$(e.currentTarget),
				paged	=	$target.parents('li').attr('data-page');

			this.loadPage(parseInt(paged));
		},

		loadPage : function (page) {
			var table	=	$('.job-coupon-list > table'),
				blockUI	=	new JobEngine.Views.BlockUi();
			$.ajax({
				type 	: 'get',
				url		: et_globals.ajaxURL,
				data 	: {action : 'je-coupon-get-page', paged : page} ,
				beforeSend :  function () {
					blockUI.block(table);
				},
				success : function (res) {
					blockUI.unblock();
					table.find('tr:not(:eq(0))').remove();
					table.append(res.data);
					$('.coupon-page-navigation ul').html(res.paginate);
				}
			});
			$('#add_coupon_form').hide();
			$('#coupon_list').fadeIn(500);
		},

		showNewCouponForm : function (e) {
			e.preventDefault();
			$('#coupon_list').hide();
			$('#add_coupon_form').fadeIn(500);
			$('#add_coupon_form').find('input[name=coupon_id]').val('');
			$('#add_coupon_form').find('.form-item input').val('');
			$('#add_coupon_form').find('.form-item .list-coupon').html('');
		},
		backToList : function (e) {
			e.preventDefault();
			$('#add_coupon_form').hide();
			$('#coupon_list').fadeIn(500);
		},
		/**
		 * load edit coupon form
		*/
		loadEditForm :	function (e) {
			e.preventDefault();
			var view 		= this,
				$target		=	$(e.currentTarget),
				coupon_id	=	$target.parents('tr').attr('data-coupon'),
				coupon_data	=	JSON.parse($('#coupon_'+coupon_id).html());
			$('#add_coupon_form').find('input[name=coupon_id]').val(coupon_id).end()
								.find('input[name=usage_count]').val(coupon_data.usage_count).end()
								.find('input[name=discount_rate]').val(coupon_data.discount_rate).end()
								.find('input[name=user_coupon_usage]').val(coupon_data.user_coupon_usage).end()

								.find('input[name=start_date]').val(coupon_data.start_date).end()
								.find('input[name=expired_date]').val(coupon_data.expired_date).end()

								.find('select[name=discount_type]').val(coupon_data.discount_type).change().end()
								.find('select[name=date_limit]').val(coupon_data.date_limit).change().end();
			
			var added_product	=	coupon_data.added_product;
			_.templateSettings = {
			    evaluate    : /<#([\s\S]+?)#>/g,
				interpolate : /\{\{(.+?)\}\}/g,
				escape      : /<%-([\s\S]+?)%>/g
			};
		
			var	html	=	new _.template('<div class="coupon" >'+
	        									'<a href="#">[x]</a><span>{{ title }}</span>'+
	        									'<input type="hidden" name="added_product[{{ pro_id }}]" class="added_product" value="{{title}}" />'+
	        								'</div>'
	        							);
			view.added_product	=	[];
			$('.list-coupon').html('');
			$.each(added_product, function(index, product){
				var product = { pro_id : index, title : product };
				view.added_product[index]	=	index;
				$('.list-coupon').append (html(product));
			});

			$('#coupon_list').hide();
			$('#add_coupon_form').fadeIn(500);

		},
		/**
		 * delete coupon
		*/
		deleteCoupon : function (e) {
			e.preventDefault();
			var view 		= this,
				$target		=	$(e.currentTarget),
				blockUI	=	new JobEngine.Views.BlockUi(),
				$tr		=	$target.parents('tr'),
				coupon_id	=	$tr.attr('data-coupon');
			$.ajax({
				type : 'post',
				url  : et_globals.ajaxURL,
				data : {action : 'je-delete-coupon', coupon_id : coupon_id},
				beforeSend : function () {
					blockUI.block($tr);
				},
				success : function (res) {
					blockUI.unblock();
					if(res.success) {
						$tr.remove();
						var table	=	$('.job-coupon-list > table');
						if(table.length < 2) {
							var paged	=	$('.coupon-page-navigation').find('span').parents('li').attr('data-page');
							view.loadPage (parseInt(paged));
						}

					} else {
						alert(res.msg);
					}
				}
			});

		}

	});
})(jQuery);