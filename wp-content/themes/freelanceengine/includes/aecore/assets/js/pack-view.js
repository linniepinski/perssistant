/**
 * control AE Pack settings
 * use AE.Collections.Posts, AE.Models.Post
 */
(function(Models, Views, Collections, $, Backbone) {

	Models.Pack = Backbone.Model.extend({
		action: 'ae-pack-sync',

		initialize: function() {

		}
	});

	Collections.Packs = Backbone.Collection.extend({
		model: Models.Pack,
	});

	Views.PackItem = Backbone.View.extend({

		tagName: 'li',
		className: 'pack-item',
		template: '',

		events: {
			'click a.act-edit': 'editPlan',
			'click a.act-del': 'removePlan',
		},

		initialize: function(options) {
			_.bindAll(this, 'render', 'fadeOut');
			//console.log(this.$el);
			this.template_name = options.template_name || '';
			this.option_name = options.option_name || '';
			this.template = _.template($(options.template).html());

			this.model.set('option_name', this.option_name);
			this.model.set('template_name', this.template_name);

			this.model.bind('updated', this.render, this);
			this.model.on('change', this.render, this);
			this.model.bind('destroy', this.fadeOut, this);

			this.confirm_delete_pack = options.confirm_delete_pack;

		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON())).attr('data', this.model.id).attr('id', 'payment_' + this.model.id);
			return this;
		},

		blockItem: function() {
			this.blockUi = new Views.BlockUi();
			this.blockUi.block(this.$el);
		},

		unblockItem: function() {
			this.blockUi.unblock();
		},

		editPlan: function(event) {
			event.preventDefault();

			if (this.editForm && this.$el.find('.engine-payment-form').length > 0) {
				this.editForm.closeForm(event);
			} else {
				this.editForm = new Views.PackForm({
					model: this.model,
					parent: this.$el
				});
			}

		},

		removePlan: function(event) {
			// ask user if he really want to delete
			if (!confirm(this.confirm_delete_pack)) return false;

			event.preventDefault();
			var view = this;

			// call delete request
			this.model.destroy({
				beforeSend: function() {
					view.blockItem();
				},
				success: function(resp) {
					view.unblockItem();
				}
			});
		},
		fadeOut: function() {
			this.$el.fadeOut(function() {
				$(this).remove();
			});
		}
	});

	Views.PackForm = Backbone.View.extend({
		tagName: 'div',
		events: {
			'submit form.edit-plan': 'savePlan',
			'click .cancel-edit': 'cancel',
			'click form.edit-plan input[name=et_not_duration]' : 'duration',
		},
		template: '', //_.template( $('#template_edit_form').html() ),
		render: function() {
			var view = this;
			this.$el.html(this.template(this.model.toJSON()));

			this.$('select').each(function(){
				$(this).val(view.model.get($(this).attr('name')));
			});

			return this;
		},

		initialize: function(options) {

			_.bindAll(this, 'closeForm');
			var template_name = this.model.get('template_name');
			if($('#template_edit_'+template_name).length == 0) {
				// apply template for view
				console.log(template_name);
				if ($('#template_edit_form').length > 0) {
					this.template = _.template($('#template_edit_form').html());
				}
			}else {
				this.template = _.template($('#template_edit_'+template_name).html());
			}
			
			this.options = options;

			this.model.bind('update', this.closeForm, this);
			this.appear();

			this.blockUi = new Views.BlockUi();

			var $color = this.$('.color-picker');
			$color.ColorPicker({
				onChange: function(hsb, hex, rgb) {
					$color.val('#' + hex);
					//$this.css('color' , '#'+hex );
					$color.css('background', '#' + hex);
					// $this.ColorPickerHide();
				},
				onBeforeShow: function() {
					$(this).ColorPickerSetColor(this.value);
				}
			});
			var view = this;
			var isCheck = view.$el.find("input[name='et_not_duration']"),
				inputDuration = view.$el.find("input[name='et_duration']");;
			if(isCheck.val() == '1'){
				isCheck.attr("checked","checked");
				inputDuration.attr("disabled","disabled");
			}
		},

		appear: function() {
			this.render().$el.hide().appendTo(this.options.parent).slideDown();
		},

		savePlan: function(event) {
			event.preventDefault();
			var $form = $(event.currentTarget),
				button = $form.find('.engine-submit-btn'),
				view = this;

			if ($form.valid()) {
				/**
				 * get name value pair input set to model
				 */
				$form.find('input,textarea,select').each(function() {
					view.model.set($(this).attr('name'), $(this).val());
				});

				/**
	             * update input check box to model
	             */
	            $form.find('input[type=checkbox]').each(function() {

	            	if($(this).is(':checked')) {
	            		view.model.set($(this).attr('name'), $(this).val());	
	            	}else {
	            		view.model.set($(this).attr('name'), 0);
	            	}
	            });
	            /**
	             * update input radio to model
	             */
	            $form.find('input[type=radio]:checked').each(function() {
	                view.model.set($(this).attr('name'), $(this).val());
	            });
	            
				view.model.save('', '', {
					beforeSend: function() {
						view.blockUi.block($form);
					},
					success: function(result, status, xhr) {
						view.blockUi.unblock();
						view.closeForm();
					}
				});
			}
		},

		cancel: function(event) {
			event.preventDefault();
			this.closeForm();
		},
		closeForm: function() {
			this.$el.slideUp(500, function() {
				$(this).remove();
			});
		},
		duration : function(event){
			
			var view = this;
			var checkbox = view.$el.find("input[name='et_not_duration']"),
				inputDuration = view.$el.find("input[name='et_duration']");

			if(checkbox.is(":checked") == true){
				checkbox.val("1");
				inputDuration.removeClass('required').attr('DISABLED','DISABLED').val("");
			}else{
				checkbox.val("0");
				inputDuration.addClass('required').removeAttr('DISABLED');		
			}
		}
	});


	Views.Pack = Backbone.View.extend({

		events: {
			'submit form.add-pack-form': 'submitPaymentForm',
			'click form.add-pack-form input.not-duration' : 'duration',
		},

		initialize: function(options) {

			_.bindAll(this, 'addOne', 'addAll', 'render');

			var view = this;

			this.data_template = this.$el.attr('data-template');
			this.option_name = this.$el.attr('data-option-name');
			/**
			 * init collection data
			 */
			if ($('#ae_list_' + this.data_template).length > 0) {
				var packs = JSON.parse($('#ae_list_' + this.data_template).html());
				this.Packs = new Collections.Packs(packs);
			} else {
				this.Packs = new Collections.Packs();
			}

			this.pack_view = [];
			/**
			 * init UserItem view
			 */
			this.Packs.each(function(pack, index, col) {
				var el = view.$('li.pack-item').eq(index);
				view.pack_view.push(new Views.PackItem({
					el: el,
					model: pack,
					template: '#ae-template-' + view.data_template,
					template_name: view.data_template,
					option_name : view.option_name,
					confirm_delete_pack: view.$('#confirm_delete_' + view.data_template).val()
				}));
			});

			// bind event to collection users
			this.listenTo(this.Packs, 'add', this.addOne);
			this.listenTo(this.Packs, 'reset', this.addAll);
			this.listenTo(this.Packs, 'all', this.render);

			this.blockUi = new Views.BlockUi();

			// sort payment plan
			this.$('.sortable').sortable({
				axis: 'y',
				handle: 'div.sort-handle',
				placeholder: "ui-state-highlight"
			});

			this.$('ul.pay-plans-list').bind('sortupdate', function(e, ui){
				view.updatePackOrder(e, ui);
			});
			this.isCheck = $("input.not_duration");

		},

		updatePackOrder : function(e, ui){
			var order = this.$('ul.pay-plans-list').sortable('serialize');
			var params = {
	            type: 'POST',
	            dataType: 'json',
	            url: ae_globals.ajaxURL,
	            contentType: 'application/x-www-form-urlencoded;charset=UTF-8'
	        };
			params.data = {
				action: 'et_sort_payment_plan',
				content : {
					order: order
				}
			};
			params.beforeSend = function(){
				//console.log('a');
			}
			params.success = function(data){
				//console.log('finish');
			}
			$.ajax(params);
		},
		/**
		 * add one
		 */
		addOne: function(pack) {
			// console.log('add one');
			var Item = new Views.PackItem({
				model: pack,
				template: '#ae-template-' + this.data_template,
				template_name: this.data_template,
				option_name : this.option_name,
				confirm_delete_pack: this.$('#confirm_delete_' + this.data_template).val()
			});
			this.pack_view.push(Item);
			this.$('ul.pay-plans-list').append(Item.render().el);
		},

		/**
		 * add all
		 */
		addAll: function() {

			for (var i = 0; i < this.pack_view.length - 1; i++) {
				this.pack_view[i].remove();
			}

			this.$('ul').html('');
			this.pack_view = [];
			this.Packs.each(this.addOne, this);
		},

		// event handle: Submit Pack form
		submitPaymentForm: function(event) {
			event.preventDefault();

			var $form = $(event.currentTarget),
				button = $form.find('.engine-submit-btn'),
				view = this;

			if ($form.valid()) {
				var model = new Models.Pack();

				$form.find('input,textarea,select').each(function() {
					model.set($(this).attr('name'), $(this).val());
				});

	            /**
	             * update input check box to model
	             */
	            $form.find('input[type=checkbox]').each(function() {
	            	if($(this).is(':checked')) {
	            		model.set($(this).attr('name'), $(this).val());	
	            	}else {
	            		model.set($(this).attr('name'), 0);
	            	}
	            });
	            /**
	             * update input radio to model
	             */
	            $form.find('input[type=radio]:checked').each(function() {
	                view.model.set($(this).attr('name'), $(this).val());
	            });

				model.set('option_name', view.option_name);
				model.save('', '', {
					beforeSend: function() {
						view.blockUi.block($form);
					},
					success: function(result, status, xhr) {
						view.blockUi.unblock();
						$form.find('input[type=text],textarea').val('');
						view.Packs.add(result);
					}
				});

			}
		},
		duration: function (){
			var checkbox = $('form.add-pack-form  input.not-duration');
			var inputDuration = $('form.add-pack-form  input[name="et_duration"]');
			if(checkbox.is(":checked") == true)
			{	
				checkbox.val("1");
				inputDuration.removeClass('required').attr('READONLY','READONLY');
			}
			else{
				checkbox.val("0");
				inputDuration.addClass('required').removeAttr('READONLY');				
			}
		},
	});

	$(document).ready(function() {
		// modify validator: add new rule for username
        $.validator.addMethod("username", function(value, element) {
            var ck_username = /^[a-z0-9_]{1,20}$/;
            return ck_username.test(value);
        }, 'Enter lowercase, do not leave spaces between the name.');

        $.validator.addClassRules('is_packname', { required: true, username : true });

		$('.pack-control').each(function() {
			new Views.Pack({
				el: $('#' + $(this).attr('id'))
			});
		});
	});
})(window.AE.Models, window.AE.Views, window.AE.Collections, jQuery, Backbone);