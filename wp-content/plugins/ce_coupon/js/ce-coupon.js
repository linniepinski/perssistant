(function ($) {
	$(document).ready(function () {
		new CE.Views.Coupon();
	});
	CE.Views.Coupon = Backbone.View.extend ({
		el : '#coupon-manage',
		events : {
			'change select#date_limit' 				: 'changeDateLimit',
			'change #product' 						: 'addProduct',
			'click .list-coupon .coupon a'			: 'removeProduct',
			'click #save_coupon'					: 'saveCoupon',
			'click #add_new_coupon'					: 'showNewCouponForm',
			'click #back_to_list'					: 'backToList',
			'click .ce-coupon-list .edit'  			: 'loadEditForm',
			'click .ce-coupon-list .delete'  		: 'deleteCoupon',
			'click .coupon-page-navigation li a'	: 'changePage',

		},
		/**
		 * initialize view
		*/
		initialize : function (flag) {
			var container = this.$el;
			if(typeof flag == 'undefined')
				flag = true;

			if(flag){
				this.added_product	=	[];

			}else{
				container = ".tr-edit";
			}
			this.styleSelector(container);
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

			if( !pro_id || $.inArray( pro_id , this.added_product ) > 0  ) return;

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
				$form	=	target.closest("form"),
				blockUI	=	new CE.Views.BlockUi();
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
							$(".tr-edit").remove();
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
			var table	=	$('.ce-coupon-list > table'),
				blockUI	=	new CE.Views.BlockUi();
			$.ajax({
				type 	: 'get',
				url		: et_globals.ajaxURL,
				data 	: {action : 'ce-coupon-get-page', paged : page} ,
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
			
		},

		showNewCouponForm : function (e) {
			e.preventDefault();

			//$('#coupon_list').hide();
			var status = $('#add_coupon_form').css('display');
			$(".tr-edit").remove();

			$(e.currentTarget).closest(".module").find("hide").removeClass(".hide");

			if(status == 'none'){
				$('#add_coupon_form').slideDown( "slow", function() {

				});
			} else {
			 	$('#add_coupon_form').slideUp( "slow", function() {

				});
			 }

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
				coupon_id	=	$target.closest('tr').attr('data-coupon');
				var next 	= $target.closest("tr").next();

			if( next.hasClass("tr-edit") ){
					next.slideUp("slow",function(){
					next.remove();
				});

				return false;
			} else {
				$target.closest("table").find(".tr-edit").remove()

				var coupon_data 	= [];
					coupon_data		= JSON.parse($('#coupon_'+coupon_id).html()),
					template 		=  _.template($('#coupon_edit_form').html()),
					form 			= template(coupon_data),
					added_product	= coupon_data.added_product,
					select 			= $target.closest("tr").find("select#date_limit"),
			 		tr 				= $target.closest("tr");

				$("<tr class='tr-edit'><td colspan='7'>" + form +"</td></tr>" ).insertAfter( tr );

				var	html	=	new _.template('<div class="coupon" >'+
		        									'<a href="#">[x]</a><span>{{ title }}</span>'+
		        									'<input type="hidden" name="added_product[{{ pro_id }}]" class="added_product" value="{{ title }}" />'+
		        								'</div>'
		        							);

				$( "select#date_limit option" ).each(function() {

					if($(this).attr('selected') == 'selected'){
						if($(this).val() == 'on'){
							$(this).closest(".two-p50").find(".date").show();
						}
					}
				});

				$('.list-coupon').html('');
				$.each(added_product, function(index, product){
					var product = { pro_id : index, title : product };
					view.added_product[index]	=	index;
					$('.list-coupon').append (html(product));
				});
				view.initialize(false);
			}

		},

		/**
		 * delete coupon
		*/
		deleteCoupon : function (e) {
			e.preventDefault();
			var view 		= this,
				$target		=	$(e.currentTarget),
				blockUI	=	new CE.Views.BlockUi(),
				$tr		=	$target.parents('tr'),
				coupon_id	=	$tr.attr('data-coupon');
			$.ajax({
				type : 'post',
				url  : et_globals.ajaxURL,
				data : {action : 'ce-delete-coupon', coupon_id : coupon_id},
				beforeSend : function () {
					blockUI.block($tr);
				},
				success : function (res) {
					blockUI.unblock();
					if(res.success) {
						$tr.remove();
						var table	=	$('.ce-coupon-list > table');
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