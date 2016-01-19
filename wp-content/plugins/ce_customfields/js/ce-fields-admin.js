(function($){
	var Field = Backbone.Model.extend({
	    defaults: {
	        field_name 		: 'Field slug',
	        field_label 	: 'Field label',
	        field_pholder 	: 'Placeholder text',
	        field_des  		: 'Field description',
	        field_type 		: 'text',
	        field_required 	: 0

	    }
	});
	// A List of People
	var FieldCollection = Backbone.Collection.extend({
	    model: Field,
	});


	var Tax = Backbone.Model.extend({
	    defaults: {
	        tax_slug : 'Tax slug',
	        tax_name : 'Tax Name',
	        tax_label: 'Tax Label',
	        tax_des  : 'Tax description',
	        tax_type : '',
	    }
	});
	// A List of People
	var TaxCollection = Backbone.Collection.extend({
	    model: Tax,
	});

	CE.ext_fields = Backbone.View.extend({
		el : 'div#ce_fields_ext',
		events : {
			// for meta fields.
			'submit #fields_tab form.add-field' : 'add-field',
			'click .fields-list a.act-del'		: 'del-field',
			'click .fields-list a.act-edit'		: 'edit-field',
			'blur .add-field input#field_label'	: 'auto_set_name',
			'blur .add-tax input#tax_label'		: 'auto_set_name',
			'click #cat-list a.delete'			: 'del-cat',
			'change select#field_cats' 			: 'render_cats',
			// for taxonomy
			'submit #taxs_tab form.add-tax' 	: 'add-tax',
			'click .tax-list a.act-del'			: 'del-tax',
			'click .tax-list a.act-edit'		: 'edit-tax',
			'click .tax-list a.power' 			: 'toggle-status',
			//for seller field
			'submit #seller_tab form' 			: 'add-seller-field',
			'click .seller-fields-list a.act-del'	: 'del-seller-field',
			'click .seller-fields-list  a.act-edit'	: 'edit-seller-field',
			'blur #seller_tab input#field_label': 'auto_set_name',
			'click .seller-fields-list a.power' : 'toggle-status',
			'change #seller_tab select' 		: 'add-options',
			'keypress .list-options input'   	: 'keypress-options',
			'click span.del-opt'				: 'delete-input-option',
			'click span.add-opt'				: 'add-input-option',
		},

		initialize : function () {


			var url = window.location.href;
			var split = url.split("#");
			if(typeof split[1] != 'undefined'){
				$('div.tab').hide();
				$("a.section-link").removeClass("active");
				$("a."+split[1]).addClass("active")
				$("."+split[1]).show();
			}
			_.bindAll(this,'render_cats');
			this.blockUi 				= new CE.Views.BlockUi();
			this.collectionFields 		= new FieldCollection( JSON.parse( $('#list_field_data').html() ) );
			this.collectionTaxs 		= new TaxCollection( JSON.parse( $('#list_tax_data').html() ) );
			this.collectionSellerFields = new FieldCollection( JSON.parse( $('#list_seller_field_data').html() ) );

			$.validator.addMethod("noSpace", function(value, element) {

				if(value.length < 1)
					return false;
			  	return value.indexOf(" ") < 0 && value != "";
			}, ce_fields.text_no_space);
			this.styleSelector(this.$el);
			this.tabs_click();
			this.taxValidate('form.add-tax');
			this.fieldValidate('form.add-field');
			this.sfValidate('form.add-selser-field');

			$('.add-fields').sortable({
				axis: 'y',
				handle: 'div.sort-handle',

				update: function(event, ui) {

					var target,element;
					var info = $(this).sortable("serialize");

					var t =$(this).sortable("serialize");
					target = $(event.currentTarget);

					info 	= info +'&action=sort-fields';

					if($(this).hasClass('fields-list')){
						target 	= "ul.fields-list li";
						element	= "field_key_";
					}
					else if($(this).hasClass('tax-list')) {
						target 	= "ul.tax-list li";
						element 	 	= "tax_key_";
					}
					var load = this;
			       	blockUi 			= new CE.Views.BlockUi();
			        $.ajax({
			            type: "POST",
			            url: et_globals.ajaxURL,
			            data: info,
			            context: document.body,
			            beforeSend : function(){
			            	blockUi.block(load);
			            },
			            success: function(resp){
			            	if(element == 'field_key_'){
				                $(target).each(function(index,item){
				                	$(this).attr('id',element + index);
				                	$(this).attr('data',index);
				                });
				            } else if(element == 'tax_key_'){
				            	$.each(resp.data ,function(index,val){

				            	});
				            }
			                blockUi.finish();

			            }
			      });

				}
			});

			$('.seller-fields-list').sortable({
				axis: 'y',
				handle: 'div.sort-handle',

				update: function(event, ui) {

					var target,element;
					var info = $(this).sortable("serialize");

					var t =$(this).sortable("serialize");
					target = $(event.currentTarget);
					info 	= info +'&action=sort-seller-fields';

					if($(this).hasClass('seller-fields-list')){
						target 	= "ul.seller-fields-list li";
						element	= "field_key_";
					}
					var load = this;
			       	blockUi 			= new CE.Views.BlockUi();
			        $.ajax({
			            type: "POST",
			            url: et_globals.ajaxURL,
			            data: info,
			            context: document.body,
			            beforeSend : function(){
			            	blockUi.block(load);
			            },
			            success: function(resp){
			            	if(element == 'field_key_'){
				                $(target).each(function(index,item){
				                	$(this).attr('id',element + index);
				                	$(this).attr('data',index);
				                });
				            } else if(element == 'tax_key_'){
				            	$.each(resp.data ,function(index,val){

				            	});
				            }
			                blockUi.finish();

			            }
			      });

				}
			});
		},

		'tabs_click' : function(event){
			$("div.head-tabs a").click(function(){
				$("div.tab").hide();
				$("div.head-tabs a").removeClass('active');
				$(this).addClass('active');
				var div = $(this).attr('rel');
				$(div).show();
				return true;
			})
		},
		styleSelector : function(container){
	        // apply custom look for select box
	        $(container).find('.select-style select').each(function(){
	            var $this = jQuery(this),
	                title = $this.attr('title'),
	                selectedOpt = $this.find('option:selected');

	            if( selectedOpt.val() !== '' ){
	                title = selectedOpt.text();
	            }

	            $this.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
	                .after('<span class="select">' + title.trim() + '</span>')
	                .change(function(){
	                    var val = jQuery('option:selected',this).text();
	                    jQuery(this).next().text(val.trim());
	                });
	        });
	    },

	    'auto_set_name' : function (event){
	    	var target = event.currentTarget;
	    	var val = $(target).val().toLowerCase();
	    	val = val.trim();
	    	var res = val.split(" ");
	    	var tem = res[0];
	    	for(var i=1; i < res.length; i++){
	    		if(res[i])
	    			tem = tem + "_"+res[i];
	    	}
			tem = this.remove_alias(tem);
	    	var namefield 	= $(target).closest("form").find('input.auto-name');
	    	var name 		= namefield.val();
	    	if( name=='' || name == ' ' )
	    		namefield.val(tem);
	    },

		'del-cat' : function(event){
			var catLi = $(event.currentTarget).parent();
			catLi.remove();
			return false;
		},

	    render_cats : function(event){
			var catSelectEl = $(event.currentTarget),
				selectedOption = catSelectEl.find("option:selected"),
	    		text 	= selectedOption.text(),
	    		cat_id 	= selectedOption.val();
	    	var catList = catSelectEl.closest('.row-item').find('#cat-list');
	    	var t = catList.find('input[value='+cat_id+']').val();

	    	if( typeof t === 'undefined' && cat_id > 0) {
				catList.append('<li><a class="delete" href="#">	<span class="icon" data-icon="D"> </span> </a> <input type="hidden" value="'+cat_id+'" name="field_cats[]">'+text+'</li>');
	    	}
	    },

	    'add-field' : function(event){
			event.preventDefault();
			var target 		= $(event.currentTarget),
				loading		= new CE.Views.LoadingButton({el :target.find('button.btn-primary') }),
				data 		= target.serialize(),
				view 		= this;

			var that  = this;
			$.ajax({
	            url   : et_globals.ajaxURL,
	            type  : 'POST',
	            data : data,
	            beforeSend : function(){
	                loading.loading();
	            },
	            success: function(resp) {
	            	loading.finish();
	            	if(resp.success){
	            		var template = _.template($('#field_item_field').html());
	            		// action for after edit tax.
	            		if(typeof resp.data.field_name_edit !== 'undefined'){
	            			// render current tax after update.
	            			target.closest('li').find('form').slideUp( 500, function(){
	            				$(this).remove();
	            				$("li#field_key_"+resp.data.field_name).html(template(resp.data));
	            			});
	            			var model = that.collectionFields.where({field_name : resp.data.field_name})[0];
							model.set(resp.data);
	            		} else {
							//resp.data.field_cats = JSON.parse( resp.data.field_cats.toString());
	            			// append the tax has just added to ul.
	            			$("ul.fields-list li.no-result").remove();
	            			$("ul.fields-list").append('<li id="field_key_'+ resp.data.field_name+'" data="'+ resp.data.field_name+'">'+template(resp.data)+'</li>');
	            			view.collectionFields.push(resp.data);
	            		}
	            		$("form.add-field").trigger('reset');

	            	} else {
	            		target.append('<label class="error">'+resp.msg+'</label>');

	            	}
	         	}
	      	});
			return false;
		},

		'edit-field' : function (event) {

			event.preventDefault();
			var target = $(event.currentTarget);
			var display = target.closest('li').find('form').length;
			$(target).closest('li').find('ul.cat-list').html('');
			// if form edit exist.
			if(display > 0){
				target.closest('li').find('form').slideUp( 500, function(){ $(this).remove(); });
			} // append form edit
			else {
				var key 		= target.attr('rel');
				var template 	= _.template($('#template_add_field').html());

				var model 		= this.collectionFields.where({field_name : key})[0];
				console.log(this.collectionFields);
				$(template(model.attributes)).hide().appendTo($(target).closest('li')).slideDown('slow');
				var cats = model.attributes.field_cats;
				//TODO

				if(cats && cats.length > 0){
					$.each(cats , function(index, value){
				 	  	$(target).closest('li').find('ul.cat-list').append('<li><a class="delete" href="#">	<span class="icon" data-icon="D"> </span> </a> <input type="hidden" value="'+value.id+'" name="field_cats[]">'+value.name+'</li>');
					});
				} else {
					$(target).closest('li').find('ul.cat-list').html('');
				}

			}
			this.fieldValidate(target.closest("li").find('form.form-edit-field'));
			this.styleSelector(target.closest("li").find('form.form-edit-field'));

		},
		'edit-seller-field' : function(event){
			event.preventDefault();
			var target = $(event.currentTarget),
				li 	= target.closest("li");
			var display = target.closest('li').find('form').length;
			$(target).closest('li').find('ul.cat-list').html('');
			// if form edit exist.
			if(display > 0){
				target.closest('li').find('form').slideUp( 500, function(){ $(this).remove(); });
			} // append form edit
			else {
				var key 		= target.attr('rel');
				var template 	= _.template($('#template_add_seller_field').html());
				var model 		= this.collectionSellerFields.where({field_name : key})[0];

				$(template(model.attributes)).hide().appendTo($(target).closest('li')).slideDown('slow');

			}
			li.find(".list-options").html('');
			var list_type = ["checkbox", "select", "radio"]
			if( model.has("sf_options") && $.inArray( model.get("field_type"), list_type) >=0 ){
				var options = [];
				model.get("sf_options");

				options = model.get('sf_options');
				if(options.length > 0){
					$.each( options, function( key, value ) {
						li.find(".list-options").append("<li><input type='text' name='sf_options[]' value='"+value+"'><div class='act-option'><span data-icon='*' class='icon del-opt'></span><span data-icon='+' class='icon add-opt'></span></div></li>");
					});
				} else {
					li.find(".list-options").append("<li><input type='text' name='sf_options[]'><div class='act-option'><span data-icon='*' class='icon del-opt'></span><span data-icon='+' class='icon add-opt'></span></div></li>");
				}
			} else {

				li.find(".list-options").html('');
			}

			this.fieldValidate(target.closest("li").find('form.form-edit-field'));
			this.styleSelector(target.closest("li").find('form.form-edit-field'));

		},

		'del-field' : function (event){
			if (!confirm("Do you want to delete")){
			      return false;
			}
			var target 	= $(event.currentTarget),
				key 	= target.attr('rel'),
				view    = this;
 			$.ajax({
	            url   : et_globals.ajaxURL,
	            type  : 'POST',
	            data :{
	            	action : 'delete-field',
	            	key : key
	            },
	            beforeSend : function(){
	               view.blockUi.block(target);
	            },
	            success: function(resp) {
	            	view.blockUi.finish();
	            	if(resp.success){
	            		$("ul.fields-list li#field_key_"+resp.key_del).remove();
	            	} else {
	            		console.log(' False');
	            	}
	         	}
	      	});
		},
		'del-seller-field' : function(event){
			if (!confirm("Do you want to delete")){
			      return false;
			}
			var target 	= $(event.currentTarget),
				key 	= target.attr('rel'),
				view    = this;
 			$.ajax({
	            url   : et_globals.ajaxURL,
	            type  : 'POST',
	            data :{
	            	action : 'delete-sfield',
	            	key : key
	            },
	            beforeSend : function(){
	               view.blockUi.block(target);
	            },
	            success: function(resp) {
	            	view.blockUi.finish();
	            	if(resp.success){
	            		$("ul.seller-fields-list li#field_key_"+resp.key_del).remove();
	            	} else {
	            		console.log(' False');
	            	}
	         	}
	      	});
		},

		'add-tax' : function (event){
			event.preventDefault();
			var target 		= $(event.currentTarget),
				loading		=	new CE.Views.LoadingButton({el :target.find('button.btn-primary') }),
				data 		= target.serialize(),
				view 		= this;
			//isUpdate 	= data.indexOf('tax_name_edit'),
			$.ajax({
	            url   : et_globals.ajaxURL,
	            type  : 'POST',
	            data : data,
	            beforeSend : function(){
	                loading.loading();
	            },
	            success: function(resp) {
	            	loading.finish();
	            	if(resp.success){

	            		var template = _.template($('#field_item_tax').html());

	            		// action for after edit tax.
	            		if(typeof resp.data.tax_name_edit !== 'undefined'){

	            			target.closest('li').find('form').slideUp( 500, function(){
	            				$(this).remove();
	            				$("li#tax_key_"+resp.data.id).html(template(resp.data));
	            			});
	            			var model = view.collectionTaxs.get(resp.data.id);
							model.set(resp.data);
	            		} else {
	            			// append the tax has just added to ul.
	            			$("ul.tax-list li.no-result").remove();
	            			$("ul.tax-list").append('<li id="tax_key_'+ resp.data.id+'" data="'+ resp.data.id+'">'+template(resp.data)+'</li>');

	            			view.collectionTaxs.push(resp.data);
	            		}
	            		$("form.add-tax").trigger('reset');

	            	} else {
	            		target.append('<label class="error">'+resp.msg+'</label>');

	            	}
	         	}
	      	});
			return false;
		},

		'del-tax' : function (event){
			var target 	= $(event.currentTarget),
				key 	= target.attr('rel'),
				view    = this;
 			$.ajax({
	            url   : et_globals.ajaxURL,
	            type  : 'POST',
	            data :{
	            	action : 'delete-taxonomy',
	            	key : key
	            },
	            beforeSend : function(){
	               view.blockUi.block(target);
	            },
	            success: function(resp) {
	            	view.blockUi.finish();
	            	if(resp.success){
	            		$("ul.tax-list li#tax_key_"+resp.key_del).remove();

	            	} else {
	            		console.log(' False');
	            	}
	         	}
	      	});
		},

		'edit-tax' : function (event) {
			event.preventDefault();

			var target = $(event.currentTarget),
				view 	= this;
			var display = target.closest('li').find('form').length;
			// if form edit exist.
			if(display > 0){
				target.closest('li').find('form').slideUp( 500, function(){ $(this).remove(); });
			} // append form edit
			else {

				var key 		= target.attr('rel');
				var template 	= _.template($('#template_add_tax').html());
				//var model 		= this.collectionTaxs.get(key);

				var model 		= view.collectionTaxs.where({tax_name : key})[0];
				console.log(model);
				$(template(model.attributes)).hide().appendTo($(target).closest('li')).slideDown('slow');
			}
			this.taxValidate('form.form-edit-tax');

			this.styleSelector(target.closest("li").find('form.form-edit-tax'));


		},

		'toggle-status' : function(event){
			event.preventDefault();
			var target 	= $(event.currentTarget),
				key 	= target.attr('rel'),
				view 	= this;

			$.ajax({
	            url   : et_globals.ajaxURL,
	            type  : 'POST',
	            data :{
	            	action : 'toggle-taxonomy',
	            	key : key
	            },
	            beforeSend : function(){
	               view.blockUi.block(target);
	            },
	            success: function(resp) {
	            	view.blockUi.finish();
	            	if(resp.success){
	            		if(resp.data == 0){
	            			target.closest('li').addClass('off');
	            			target.attr('title','Turn on');
	            		} else {
	            			target.closest('li').removeClass('off');
	            			target.attr('title','Turn off');
	            		}
	            	} else {
	            		console.log(' False');
	            	}
	         	}
	      	});
		},

		'add-options' : function(event){
			var $target = $(event.currentTarget),
				value 	= $target.val();

			var array = ['checkbox','radio','select'];
			if($.inArray(value,array) != -1){
				$target.closest("form").find(".list-options").show();
				if( $target.closest("form").find(".list-options").html() == '' )
					$target.closest("form").find(".list-options").append("<li><input type='text' name='sf_options[]' value='"+value+"'><div class='act-option'><span data-icon='*' class='icon del-opt'></span><span data-icon='+' class='icon add-opt'></span></div></li>");
			} else {
				$target.closest("form").find(".list-options").hide();
			}

		},
		'keypress-options' : function(event){
			var $target = $(event.currentTarget);
			if(event.which == 13 ){
				if($target.val() == '')
					return false;

			 	$target.closest("form").find(".list-options").append("<li><input type='text' name='sf_options[]'><div class='act-option'><span data-icon='*' class='icon del-opt'></span><span data-icon='+' class='icon add-opt'></span></div></li>");
			 	$(event.currentTarget).closest("li").next().find("input").focus();
				return false;
			}
			return true;

		},
		'delete-input-option' :function(event){
			$(event.currentTarget).closest("li").remove();
		},

		'add-input-option' :function(event){
			var $target = $(event.currentTarget);
			var input = $target.closest("li").find("input").val();
			if ( input != '') {
				$target.closest(".list-options").append("<li><input type='text' name='sf_options[]' value=''><div class='act-option'><span data-icon='*' class='icon del-opt'></span><span data-icon='+' class='icon add-opt'></span></div></li>");
			}
		},

		'fieldValidate' : function(div){

			this.$(div).validate({
				rules: {
					// simple rule, converted to {required:true}
					field_label : {
						required : true
					},

					field_name : {
						required: true,
						noSpace : true

					},
					field_type : {
						required : true
					},
					// field_des : {
					// 	required : true
					// }
				}
			});
		},

		'sfValidate' : function(div){

			this.$(div).validate({
				rules: {
					// simple rule, converted to {required:true}
					field_label : {
						required : true
					},

					field_name : {
						required: true,
						noSpace : true

					},
					field_type : {
						required : true
					},
					field_des : {
						required : true
					}
				}
			});
		},

		'taxValidate' : function(div){

			this.$(div).validate({
				rules: {
					// simple rule, converted to {required:true}
					tax_label : {
						required : true
					},
					tax_slug : {
						required: true,
						noSpace : true

					},
					tax_name : {
						required: true,
						noSpace : true

					},
					tax_type : {
						required : true
					},
					tax_des : {
						required : true
					}
				}
			});
		},

		'add-seller-field' : function(event){
			event.preventDefault();
			var target 		= $(event.currentTarget),
				loading		= new CE.Views.LoadingButton({el :target.find('button.btn-primary') }),
				data 		= target.serialize(),
				view 		= this;
			var that  = this;
			$.ajax({
	            url   : et_globals.ajaxURL,
	            type  : 'POST',
	            data : data,
	            beforeSend : function(){
	                loading.loading();
	            },
	            success: function(resp) {
	            	loading.finish();
	            	if(resp.success){
	            		var template = _.template($('#field_item_field').html());
	            		// action for after edit tax.
	            		if(typeof resp.data.field_name_edit !== 'undefined'){
	            			target.closest('li').find('form').slideUp( 500, function(){
	            				$(this).remove();
	            				$("li#field_key_"+resp.data.field_name).html(template(resp.data));
	            			});
	            			var model = that.collectionSellerFields.where({field_name : resp.data.field_name})[0];
							model.set(resp.data);
	            		} else {
	            			$("ul.seller-fields-list li.no-result").remove();
	            			$("ul.seller-fields-list").append('<li id="field_key_'+ resp.data.field_name+'" data="'+ resp.data.field_name+'">'+template(resp.data)+'</li>');
	            			if( resp.data.method!= 'update' )
	            				view.collectionSellerFields.push(resp.data);
	            				target.trigger('reset');
	            		}

	            	} else {
	            		target.append('<label class="error">'+resp.msg+'</label>');

	            	}
	         	}
	      	});
			return false;
		},
		'remove_alias' :function ( alias ) {
			var str = alias;
			str = str.toLowerCase();
			str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ  |ặ|ẳ|ẵ/g, "a");
			str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
			str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
			str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ  |ợ|ở|ỡ/g, "o");
			str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
			str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
			str = str.replace(/đ/g, "d");
			str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|-/g, "_");
			/* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */
			str = str.replace(/_+_/g, "_"); //thay thế 2- thành 1-
			str = str.replace(/^\_+|\_+$/g, "");
			//cắt bỏ ký tự - ở đầu và cuối chuỗi
			return str;
		}

	});

	jQuery(document).ready(function(){
		new CE.ext_fields();
	});

})(jQuery);