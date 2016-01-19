(function ($){

	JobEngine.Views.Resume_Text = Backbone.View.extend({
		resume : [],
		jobseeker : [],
		field : [],

		events : {
			'submit form' : 'saveFields' ,
		},

		initialize : function () {

			this.model.id 	= this.model.ID;
			this.resume		= new JobEngine.Models.Resume (this.model);
			this.loading 	= new JobEngine.Views.BlockUi();

		},

		saveFields : function (event) {
			event.preventDefault();
			event.preventDefault();
			var view 		= this,
				content 	= $( event.currentTarget ).find('textarea,input').val(),
				container 	= $( $( event.currentTarget ).closest('.module') ),
				field_name	= container.attr('data-resume');

			this.resume.set( field_name , content  );
			this.resume.save( '', '', {
					saveData: [field_name],
					beforeSend: function(){
						view.loading.block(container);
					},
					success : function(model, resp){
						view.loading.unblock();
						if ( resp.success ){
							// apply change content
							view.resume.set( resp.data.resume, {silent: true});
							container.find('.cnt').html(view.resume.get(field_name));
							view.toggleInlineEdit(container);
						}
					}
				});
		},
		toggleInlineEdit: function(element){
			var container = $(element);

			if (!container.hasClass('editing'))
				container.addClass('editing');
			else {
				container.removeClass('editing');
			}
		}
	});

	JobEngine.Views.Resume_Image = Backbone.View.extend({
		resume : [],
		jobseeker : [],
		field : [],
		events : {
			'submit form' : 'saveFields' ,
		},


		initialize : function () {

			this.model.id 	= this.model.ID;
			this.resume		= new JobEngine.Models.Resume (this.model);

			this.loading 	= new JobEngine.Views.BlockUi();
			//var blockUi = new JobEngine.Views.BlockUi();
			$field = $('#resume_image_container');
			if($field.length > 0 ){

				var view = this,
					div_thumb = $('#resume_image_container').closest('.module-edit').find('.field-thumb');
				this.field_image	= new JobEngine.Views.File_Uploader({
					el					: '#resume_image_container',
					uploaderID			: 'resume_image',
					thumbsize			: 'thumbnail',
					filters				: [
						{ title : 'Image Files', extensions : 'jpg,jpeg,gif,png' }
					],

					multipart_params	: {
						// _ajax_nonce	: $user_logo.find('.et_ajaxnonce').attr('id'),
						_ajax_nonce	: $field.find('.et_ajaxnonce').attr('id'),
						action		: 'resume_upload_image',
						author 		: $field.find('input[name=author]').val(),
						field_name  : $field.find('input.field_name').val(),
						resume_id   : $field.find('input.resume_id').val(),
					},
					cbUploaded : function(up, file, res){
						view.loading.unblock();
						if (res.success){

							div_thumb.html("<img src='"+res.data.thumbnail[0]+"' />");
							$field.find('input[name=attach_id]').val(res.data.attach_id);

						}else {
							pubsub.trigger('je:notification', {
								msg			: res.msg,
								notice_type	: 'error'
							});
						}
					},

					beforeSend		: function(element){
						view.loading.block(div_thumb);

					}
				});
			}
		},
		saveFields : function (event) {
			var target 	= $(event.currentTarget),
				view 	= this;
			var params 	= {
				url : et_globals.ajaxURL,
				type: 'post',
				data: {
					action: 'update_resume_field_image',
					content: {
						key 		: target.attr('rel'),
						resume_id 	: target.attr('id'),
					}
				},
				beforeSend: function(){
					//view.loading.block(target.closest(".file-item"));

				},
				success: function(resp){
					//view.loading.unblock();
					//target.closest(".file-item").remove();

				}
			}
			$.ajax(params);
			return false;

		},
		toggleInlineEdit: function(element){
			var container = $(element);

			if (!container.hasClass('editing'))
				container.addClass('editing');
			else {
				container.removeClass('editing');
			}
		}
	});


	JobEngine.Views.Resume_File = Backbone.View.extend({
		resume : [],
		jobseeker : [],
		field : [],
		events : {
			'submit form' : 'saveFields' ,
			'click span.delete a' : 'deleteFile'
		},

		initialize : function () {

			this.model.id 	= this.model.ID;
			this.resume		= new JobEngine.Models.Resume (this.model);

			this.loading 	= new JobEngine.Views.BlockUi();
			//var blockUi = new JobEngine.Views.BlockUi();
			//
			$file_upload = $('#resume_file_container');

			if($file_upload.length > 0 ){
				var view = this,
					div_thumb = $('#resume_file_container').closest('.module-edit').find('.field-thumb');
				this.field_file	= new JobEngine.Views.File_Uploader({
					el					: '#resume_file_container',
					uploaderID			: 'resume_file',
					thumbsize			: 'thumbnail',
					filters				: [
						{ title : 'Resume Files', extensions : 'jpg,jpeg,gif,png,zip,rar,pdf,doc,docx,ppt,pptx' }
					],

					multipart_params	: {
						// _ajax_nonce	: $user_logo.find('.et_ajaxnonce').attr('id'),
						_ajax_nonce	: $file_upload.find('.et_ajaxnonce').attr('id'),
						action		: 'resume_upload_file',
						resume_id 	: $file_upload.find("input.resume_id").val(),
						field_id 	: $file_upload.find("input.field_id").val(),
					},
					cbUploaded : function(up, file, res){
						view.loading.unblock();
						if (res.success){
							$file_upload.closest(".module-edit").find(".field-thumb").append(view.template(res.data));

							pubsub.trigger('je:notification', {
								msg			: res.msg,
								notice_type	: 'success'
							});

						}else {
							pubsub.trigger('je:notification', {
								msg			: res.msg,
								notice_type	: 'error'
							});
						}
					},

					beforeSend		: function(element){
						view.loading.block(div_thumb);

					}
				});
			}
		},
		templateFile: _.templateSettings = {
		    evaluate    : /<#([\s\S]+?)#>/g,
			interpolate : /\{\{(.+?)\}\}/g,
			escape      : /<%-([\s\S]+?)%>/g
		},
		//<img alt="<%=name%>" title="<%=name%>" src="<%=icon%>" /> <br />
		template : _.template('<span class="file-item"><a rel = "{{key}}" href="{{url}}" alt="{{name}}"> {{name}}</a> <span class="delete"><a class="" id="{{resume_id}}" rel="{{key}}"> x </a></span></span><input type="hidden"'),

		deleteFile : function(event){
			var target 	= $(event.currentTarget),
				view 	= this;

			var params = {
				url : et_globals.ajaxURL,
				type: 'post',
				data: {
					action: 'resume_romove_file',
					content: {
						key 		: target.attr('rel'),
						resume_id 	: target.attr('id'),
					}
				},
				beforeSend: function(){
					view.loading.block(target.closest(".file-item"));

				},
				success: function(resp){
					view.loading.unblock();
					target.closest(".file-item").remove();

				}
			}
			$.ajax(params);
		},

		saveFields : function (event) {

		},
		toggleInlineEdit: function(element){
			var container = $(element);

			if (!container.hasClass('editing'))
				container.addClass('editing');
			else {
				container.removeClass('editing');
			}
		}
	});


	JobEngine.Views.Resume_MultiText = Backbone.View.extend({
		resume : [],
		jobseeker : [],
		field : [],

		events : {
			'submit form' 		: 'saveFields' ,
			'keypress input' 	: 'onAddSkill',
		},

		initialize : function () {
			this.model.id 	= this.model.ID;
			this.resume		= new JobEngine.Models.Resume (this.model);
			this.loading 	= new JobEngine.Views.BlockUi();

			// resume category
			var view	  = this;
			var positions = JSON.parse( $( '#data_resume_'+this.$el.attr('data-resume') ).html() );
			view.positions_list = this.$el.find('ul.skill-list');

			if(positions) {
				_.each(positions, function(element, index){
					var taxView = new JobEngine.Views.EditedTaxonomyItem(element, $('#edit_skill_item').html());
					view.positions_list.append( taxView.render().$el );
				});
			}
		},

		onAddSkill: function(event){
			var $target		= $(event.currentTarget),
				val 		= $target.val();

			if ( event.which == 13 ){
				this.addSkill(val);
				$target.val('');
			}
			// var duplicates 	= this.skill_list.find('input[type=hidden][value="' + val + '"]');
			// if (duplicates.length > 0){ alert(et_resume_profile.duplicate_skills); };
			// // if "enter" is pressed, create new
			// if (event.which == 13 && val != '' && duplicates.length == 0){
			// 	var data = { 'name' : $(event.currentTarget).val() };
			// 	var taxView = new JobEngine.Views.EditedTaxonomyItem(data, $('#edit_skill_item').html());
			// 	this.skill_list.append( taxView.render().$el );
			// 	$(event.currentTarget).val('');
			// }
			return event.which != 13;
		},

		addSkill: function( skill , target ){
			var container	= this.$el.find('ul.skill-list'),
				duplicates 	= container.find('input[type=hidden][value="' + skill + '"]');

			if ( duplicates.length == 0 && skill != '' ){
				var data = { 'name' : skill };
				var taxView = new JobEngine.Views.EditedTaxonomyItem(data, $('#edit_skill_item').html());
				container.append( taxView.render().$el );
				// target.val('');
			}
		},

		saveFields : function (event) {
			event.preventDefault();

			// collect skills
			var container 	= $($(event.currentTarget).closest('.module')),
				view 		= this,
				field_name	= container.attr('data-resume'),
				skills 		= container.find('input[type=hidden]').filter(function(){ 
					return $(this).val() != ''; 
				}).map(function(){
					return $(this).val();
				}).get();

			if (skills.length == 0)
				skills = '';

			this.resume.set(field_name, skills, {silent: true});
			this.resume.save('' , '',{
				saveData: [field_name],
				beforeSend: function(){
					view.loading.block(container);
				},
				success:function( model, resp){
					// unloading effect
					view.loading.unblock();

					var content = container.find('.cnt');
					var html 	= '';

					$(view.resume.get(field_name)).each(function(index){
						html += '<div class="item"><div class="content">'+ this.name +'</div></div>';
					});
					content.html(html);

					// toggle view
					view.toggleInlineEdit(container);
				}
			});

		},

		toggleInlineEdit: function(element){
			var container = $(element);

			if (!container.hasClass('editing'))
				container.addClass('editing');
			else {
				container.removeClass('editing');
			}
		}

	});

	JobEngine.Views.Resume_Radio = Backbone.View.extend({
		resume : [],
		jobseeker : [],
		field : [],

		events : {
			'submit form' : 'saveFields'
		},

		initialize : function () {
			this.model.id 	= this.model.ID;
			this.resume		= new JobEngine.Models.Resume (this.model);
			this.loading 	= new JobEngine.Views.BlockUi();
		},

		saveFields : function (event) {
			event.preventDefault();

			// collect available
			var $target		= $(event.currentTarget),
				container 	= $($target.closest('.module')),
				field_name	= container.attr('data-resume'),
				view 		= this;

			var availables = $target.find('input:checked').map(function(){ return $(this).val(); }).get();
			var color_avai = $target.find('input:checked').map(function(){ return { name: $(this).attr('data-name') , color : $(this).attr('data-color') }; }).get();

			if (availables.length == 0)
				availables = '';

			this.resume.set(field_name, availables, {silent: true});
			this.resume.save(
				'' , '' ,{
				saveData : [field_name],
				beforeSend: function(){
					view.loading.block(container);
				},
				success:function(model, resp){
					// unloading effect
					view.loading.unblock();

					var content = container.find('.cnt');
					var html 	= '';

					$(color_avai).each(function(index){
						html += '<div class="item">' + 
								'<div class="job-type color-">' +
									'' + 
									  this.name + 
								'</div>' +
							'</div>';
					});
					content.html(html);

					// toggle view
					view.toggleInlineEdit(container);
				}
			});

		} ,
		toggleInlineEdit: function(element){
			var container = $(element);

			if (!container.hasClass('editing'))
				container.addClass('editing');
			else {
				container.removeClass('editing');
			}
		}
	});

	JobEngine.Views.Resume_Checkbox = Backbone.View.extend({
		resume : [],
		jobseeker : [],
		field : [],

		events : {
			'submit form' : 'saveFields' 
		},

		initialize : function () {
			this.model.id 	= this.model.ID;
			this.resume		= new JobEngine.Models.Resume (this.model);
			this.loading 	= new JobEngine.Views.BlockUi();
		},

		saveFields : function (event) {			
			event.preventDefault();

			// collect available
			var $target		= $(event.currentTarget),
				container 	= $($target.closest('.module')),
				field_name	= container.attr('data-resume'),
				view 		= this;

			var availables = $target.find('input:checked').map(function(){ return $(this).val(); }).get();
			var color_avai = $target.find('input:checked').map(function(){ return { name: $(this).attr('data-name') , color : $(this).attr('data-color') }; }).get();

			if (availables.length == 0)
				availables = '';

			this.resume.set(field_name, availables, {silent: true});
			this.resume.save(
				'' , '' ,{
				saveData : [field_name],
				beforeSend: function(){
					view.loading.block(container);
				},
				success:function(model, resp){
					// unloading effect
					view.loading.unblock();

					var content = container.find('.cnt');
					var html 	= '';

					$(color_avai).each(function(index){
						html += '<div class="item">' + 
								'<div class="job-type color-">' +
									'' + 
									  this.name + 
								'</div>' +
							'</div>';
					});
					content.html(html);

					// toggle view
					view.toggleInlineEdit(container);
				}
			});

		} ,
		toggleInlineEdit: function(element){
			var container = $(element);

			if (!container.hasClass('editing'))
				container.addClass('editing');
			else {
				container.removeClass('editing');
			}
		}
	});


	JobEngine.Views.Resume_Select = Backbone.View.extend({
		resume : [],
		jobseeker : [],
		field : [],
		events : {
			'submit form' 						: 'saveFields' ,
			'change .fields_select select' 		: 'addJobPosition'

		},

		initialize : function () {
			var view 		= this;

			this.model.id 	= this.model.ID;
			this.resume		= new JobEngine.Models.Resume (this.model);
			this.loading 	= new JobEngine.Views.BlockUi();
			// resume category
			var positions = JSON.parse( $( '#data_resume_'+this.$el.attr('data-resume') ).html() );
			view.positions_list = this.$el.find('ul.skill-list');

			if(positions) {
				_.each(positions, function(element, index){
					var taxView = new JobEngine.Views.EditedTaxonomyItem(element, $('#edit_position_item').html());
					view.positions_list.append( taxView.render().$el );
				});
			}

		},

		saveFields : function (event) {
			event.preventDefault();
			event.preventDefault();

			// collect resume category
			var container 	= $($(event.currentTarget).closest('.module')),
				field_name	= container.attr('data-resume'),
				view 		= this;

			//positions 	= unique(elements.get());
			var positions = container.find('input[type=hidden]').filter(function(){
				return $(this).val() != ''; 
			}).map(function(){
				return $(this).val();
			}).get();

			if (positions.length == 0)
				positions = '';

			this.resume.set( field_name , positions, {silent: true});
			this.resume.save('', '', {
				saveData : [field_name] ,
				beforeSend: function(){
					view.loading.block(container);
				},
				success:function(model, resp){
					// unloading effect
					view.loading.unblock();

					var content = container.find('.cnt');
					var html 	= '';
					var data 	=  view.resume.get(field_name);
					$(data).each(function(index){
						html += '<div class="item"><div class="content">'+ this.name +'</div></div>';
					});
					content.html(html);

					// toggle view
					view.toggleInlineEdit(container);
				}
			});
		},

		addJobPosition: function(event){
			var val 		= $(event.currentTarget).val();
			var duplicates 	= this.positions_list.find('input[type=hidden][value="' + val + '"]');
			if (duplicates.length > 0){ alert(et_resume.duplicate_resume_category); };

			if ( duplicates.length == 0 ){
				var tempName = $(event.currentTarget).find('option:selected').text(); 
				var data = { 'term_id' : val, 'name' : $.trim(tempName) };
				var taxView = new JobEngine.Views.EditedTaxonomyItem(data, $('#edit_position_item').html());
				this.positions_list.append( taxView.render().$el );
			}
		},

		toggleInlineEdit: function(element){
			var container = $(element);

			if (!container.hasClass('editing'))
				container.addClass('editing');
			else {
				container.removeClass('editing');
			}
		}


	});



	$(document).ready(function () {

		var resume  	= JSON.parse($('#data_resume').html());
		var jobseeker 	= JSON.parse($('#data_jobseeker').html());
		var fields  	= JSON.parse( $('#resume_custom_fields').html());
		//$(".je_field_datepicker").datepicker();

		if( $('#resume_custom_fields').length > 0 &&  fields.length > 0 )
		for(var i = 0 ; i < fields.length ; i++ ) {
			if(fields[i]['type'] == 'select') {
				new JobEngine.Views.Resume_Select( { el : $('#fields-'+fields[i]['name']) , model : resume , jobseeker : jobseeker , field : fields[i]});
			}

			if( fields[i]['type'] == 'text' || fields[i]['type'] == 'textarea' || fields[i]['type'] == 'url' || fields[i]['type'] == 'date'   ) {
				new JobEngine.Views.Resume_Text( {el : $('#fields-'+fields[i]['name']) , model : resume , jobseeker : jobseeker , field : fields[i]});
			}
			if(fields[i]['type'] == 'checkbox') {
				new JobEngine.Views.Resume_Checkbox( {el : $('#fields-'+fields[i]['name']) , model : resume , jobseeker : jobseeker , field : fields[i]});
			}

			if(fields[i]['type'] == 'multi-text') {
				new JobEngine.Views.Resume_MultiText( {el : $('#fields-'+fields[i]['name']) , model : resume , jobseeker : jobseeker , field : fields[i]});
			}
			/*
			* version 2.1.4
			 */
			if(fields[i]['type'] == 'radio') {
				new JobEngine.Views.Resume_Radio( {el : $('#fields-'+fields[i]['name']) , model : resume , jobseeker : jobseeker , field : fields[i]});
			}

		}

	});

})(jQuery);