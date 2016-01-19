(function($){
	var taxonomies = ce_fields.taxonomies || [];
	var TaxInit = [];
	FieldTaxView = [];
	CE.Fields_BackendTax = CE.Views.BackendTax.extend({
		'addTax' : function (event){

			event.preventDefault();
	        var 	form        = this.$el.find('form.new_tax'),
	                view        = this,
	                target 		= $(event.currentTarget) ,
	                loading = new CE.Views.LoadingEffect(),
	                container        =        view.$el.find('.list-tax');

	        // prevent user add category too many times
	        if (form.hasClass('disabled') || form.find('input[type=text]').val() == ''){
	                return false;
	        }

	        var model =        CE.TaxFactory.getTaxModel(form.attr('data-tax'), {
	                color : form.find('div.cursor').attr('data') ? form.find('div.cursor').attr('data') : 0,
	                name : form.find('input[type=text]').val(),
	                type : target.attr('data-tax')
	        });

	        model.save( model.toJSON(), {
	                beforeSend : function(){
	                        form.addClass('disabled');
	                        loading.render().$el.appendTo( form.find('.controls') );
	                },
	                success : function( model, resp){
	                        form.removeClass('disabled');
	                        loading.finish();
	                        //adding to list
	                        var view =  CE.TaxFactory.getTaxItem(form.attr('data-tax'),{model: model});
	                        $(view.render().el).hide().appendTo( container ).fadeIn();
	                        form.find('input[type=text]').val('');

	                        pubsub.trigger('ce:addTaxSucess');
	                }
	        } );
		}

	});
	if(taxonomies){
		for(var i = 0; i < taxonomies.length; i++){
			var view = this;
			var taxname = taxonomies[i];

			var TaxName = taxonomies[i];

			CE.Models.taxname	=	CE.Models.Tax.extend ({
				initialize : function () {
					if(typeof this.get('type') != 'undefined'){
					var id = this.get('id');
					var action = 'et_sync_';
					var name = jQuery("input[id="+id+"]").closest('ul').attr('data-tax');
						this.action = 'et_sync_'+this.get('type');
					}

				}
			});
			/**
			 * Resume Category view
			*/
			CE.Views.taxname =  CE.Views.TaxItem.extend ({
				initialize : function (){
					//CE.Views.TaxItem.prototype.initialize.call();

					this.confirm_html = 'temp_ad_location_delete_confirm';
					this.tax_name	  = taxname;

				},
				openForm : function(event){
		            var view = this;
		            var id = $(event.currentTarget).attr('rel');

		            if ( this.model.get('id') == id ){

		                    $html = this.sub_template({id : id,type : this.model.get('type')});
		                    if (view.$el.find('ul').length == 0)
		                            view.$el.append('<ul>');
		                    view.$el.children('ul').append($html);
		                    view.$el.children('ul').find('.new-tax').focus();
		            }
			    },

				addSubTax : function(event){
						event.stopPropagation();
			            event.preventDefault();
			            var view = this;
			            var formContainer = view.$el.children('ul').children('li.form-sub-tax');
			            var form = formContainer.find('form'),
			                    loadingView = new CE.Views.LoadingEffect();
			            if (form.find('input[name=name]').val() == '') return false;

			            var model    =  CE.TaxFactory.getTaxModel (form.attr('data-tax'), {
			                    parent  : form.find('input[name=parent]').val(),
			                    name    : form.find('input[name=name]').val(),
			                    type 	: form.attr('data-tax')
			            });

			            model.save(model.toJSON(), {
			                    beforeSend : function(){
			                            loadingView.render().$el.appendTo( formContainer.find('.controls') );
			                    },
			                    success: function(model, resp){
			                            if (resp.success){
			                                    loadingView.finish();
			                                    /**
			                                     * use factory to create object tax item
			                                    */
			                                    var subView = CE.TaxFactory.getTaxItem(form.attr('data-tax'),{model: model});
			                                    /**
			                                     * render tax item view
			                                    */
			                                    $(subView.render().el).insertBefore(view.$el.children('ul').find('li:last'));
			                                    formContainer.remove();
			                            }
			            }});
			    },
			    /**
			     * update tax name
			    */
			    updateName: function(event){
		            var current = $(event.currentTarget),
		                    id                = current.attr('rel'),
		                    val                = current.val(),
		                    loadingView = new CE.Views.LoadingEffect(),
		                    view = this;
		            // replace default action.
		            $.ajax({
					    type: "POST",
					    url: et_globals.ajaxURL,
					   	data: {
					      	action : this.model.action,
					      	method: 'update',
					      	content : {
					      		id 	 : this.model.get('id'),
					      		name : val,

					      	}
					    },
					    beforeSend : function(){
					      	loadingView.render().$el.appendTo( view.$el.children('.container').find('.controls') );
					    },
					    success: function(model,resp){
		                    loadingView.finish();
					    }
					});
			    },


			    displayReplaceList: function(event){
			    	var target = $(event.currentTarget);
			    	var tax_name = target.closest("ul").attr("data-tax");

        			var temp = '#temp_'+ tax_name + '_delete_confirm';

		            event.stopPropagation();
		            var $html                 = $($('#temp_'+ tax_name + '_delete_confirm').html()),
		                container          = this.$el.children('.container'),
		                view                 = this;
		            var tax_list        = this.$el.parents('ul.list-tax').find('li');
		            $html.find('select').html ('');
		            _.each(tax_list, function (element, index) {
		                    $html.find('select').append('<option value="' + $(element).find('input').attr('rel')+ '" >'+ $(element).find('input').val() +'</option>');
		            });

		            if (this.$el.find('ul > li').length > 0){
		                    if(this.$el.parents('ul.list-tax').attr('data-tax') == 'ad_category' || this.$el.parents('ul.list-tax').attr('data-tax') == 'product_cat')
		                        alert(et_setting.del_parent_cat_msg);
		                    else
		                        alert(et_setting.del_parent_location_msg);
		                    return false;
		            }

		            // hide the container
		            container.fadeOut('normal', function(){
		                    $html.insertAfter(container).hide().fadeIn('normal', function(){
		                            $html.find('button.accept-btn').bind('click', function(event){
		                                    var def = $html.find('select').val();
		                                    view.deleteTax(def);
		                            });
		                            $html.find('a.cancel-del').bind('click', function(event){
		                                    $html.fadeOut('normal', function(){
		                                            container.fadeIn();
		                                    });
		                            });
		                            $html.bind('keyup', function(event){
		                                    if (event.which == 27)
		                                            $html.fadeOut('normal', function(){
		                                                    container.fadeIn();
		                                            });
		                            });
		                    });
		                    // apply styling
		                    $html.find('option[value=' + view.model.get('id') + ']').remove();
		                    view.styleSelect();
		            });
		    },

				render : function(event){

					this.$el.append( this.template(this.model.toJSON()) ).addClass('category-item tax-item').attr('id', 'loc_' + this.model.get('id'));
					return this;
				},
				deleteTax : function(def){
		            var view = this,
		            blockUi = new CE.Views.BlockUi(),
		            loadingView = new CE.Views.LoadingEffect();
		           	// replace default action of model.
		       		$.ajax({
					    type: "POST",
					    url: et_globals.ajaxURL,
					   	data: {
					      	action : this.model.action,
					      	method: 'delete',
					      	content : {
					      		default_cat : def,
					      		id 			: this.model.get('id'),
					      		name 	: this.model.get('name'),

					      	}
					    },
					    beforeSend : function(){
					      	blockUi.block(view.$el.find('.moved-tax'));
					    },
					    success: function(data){
					           blockUi.unblock();
		                             blockUi.unblock();
		                            if ( data.success )
		                                view.$el.fadeOut('normal', function(){ $(this).remove(); });
					    }
					});
		    	},
				template: _.template('<div class="container"> \
									<div class="sort-handle"></div> \
								<div class="controls controls-2"> \
									<a class="button act-open-form" rel="{{ id }}" title=""> \
										<span class="icon" data-icon="+"></span> \
									</a> \
									<a class="button act-del" rel="{{ id }}" tax-name="{{ type }}" > \
										<span class="icon" data-icon="*"></span> \
									</a> \
								</div> \
								<div class="input-form input-form-2"> \
									<input class="bg-grey-input tax-name" rel="{{ id }}" type="text" value="{{ name }}"> \
								</div> \
							</div>'),

				sub_template : _.template('<li class="form-sub-tax disable-sort" id="tax_{{ id }}"> \
								<div class="container">\
									<!--	<div class="sort-handle"></div>  --> \
									<div class="controls controls-2">\
										<a class="button act-add-sub" title=""> \
											<span class="icon" data-icon="+"></span> \
										</a>\
									</div>\
									<div class="input-form input-form-2"> \
										<form action="" class="" data-tax="{{ type }}">\
											<input type="hidden" name="parent" value="{{id}}">\
											<input class="bg-grey-input new-tax" name="name" type="text" placeholder="Enter location name"> \
										</form> \
									</div> \
								</div>\
							</li>'),
			});
			//FieldTaxView
			FieldTaxView.taxname	=	CE.Fields_BackendTax.extend({
				initialize: function(model){
					this.initTax (model);
					this.initView (model);

				},

				initView : function () {
					var appView =	this,
						tax_type	=	this.$el.find('.list-job-input').attr('data-tax');
					$('div#tax-'+tax_type+' .tax-sortable').nestedSortable({
						handle: '.sort-handle',
						items: 'li',
						toleranceElement: '.sort-handle',
						listType : 'ul',
						placeholder : 'ui-sortable-placeholder',
						dropOnEmpty : false,
						cancel : '.disable-sort',
						update : function(event, ui){
			            	appView.sortTax(event, ui, 'et_sort_'+$(this).attr('data-tax'));
			            }
					});
				},

				initTax : function (action) {

					// this function should be override by children classs
					var tax_type	=	this.$el.find('.list-job-input').attr('data-tax'), view = this;
					_.each( this.$el.find('.list-job-input li.tax-item'), function(item){
						var $this = $(item);
						var jobLoc = {
							id : $this.find('.act-del').attr('rel'),
							name : $this.find('input[type=text]').val(),
							type : tax_type,
							action : action
						}


						//var itemView	=	view.factory(tax_type,jobLoc, item) ;
						var itemView = new CE.Views.taxname( {model : new CE.Models.taxname(jobLoc), el : item, confirm_html : 'temp_'+tax_type+'_delete_confirm' } );
					} );
				}
			});
		}
	}


	CE.Views.Tax = Backbone.View.extend ({
		el : 'div#classified_content',
		initialize : function () {
			var view = this;
			this.loading = new CE.Views.BlockUi();
			var taxonomies = ce_fields.taxonomies || [];
			var TaxInit = [];
			if(taxonomies){
				for(var i = 0; i < taxonomies.length; i++){
					var taxname = taxonomies[i];

					TaxInit[i] =	new FieldTaxView.taxname({el : $('div#tax-'+taxname) , action : 'et_sort_'+taxname });


					CE.TaxFactory.registerTaxModel(taxname, CE.Models.taxname);
					CE.TaxFactory.registerTaxItem(taxname, CE.Views.taxname);


				}
			}

		}
	});

	jQuery(document).ready(function(){
		new CE.Views.Tax();
	})

})(jQuery);