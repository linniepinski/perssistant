(function ($){
	plupload.addFileFilter('max_file_size', function(maxSize, file, cb) {

		var size = file.size/(1024*1024);
		  // Invalid file size
	  	if (size > parseInt(maxSize)) {
		  	alert(reumse_field.file_maximum +' '+ maxSize);
		    cb(false);
		  } else {
		    cb(true);
		  }
	});

	JobEngine.Views.ResumeFileUpload = Backbone.View.extend({
		el : 'body',
		resume : [],
		jobseeker : [],
		field : [],
		events : {
			'submit form' : 'saveFields' ,
			"click span.delete a" : 'deleteAttachment'
		},

		initialize : function (options) {

			var view = this,
				target = '#' + options.element,
				div_thumb = $(target).closest('.module').find('.field-thumb'),
				$file_upload = $(target);

			this.loading 	= new JobEngine.Views.BlockUi();
			this.field_file	= new JobEngine.Views.File_Uploader({
				el					: target,
				uploaderID			: options.button,
				thumbsize			: 'thumbnail',
				max_file_size 		: '4mb',
				filters				: [
					{ title : 'Resume Files', extensions : 'jpg,jpeg,gif,png,zip,rar,pdf,doc,docx,ppt,pptx' }
				],

				multipart_params	: {
					// _ajax_nonce	: $user_logo.find('.et_ajaxnonce').attr('id'),
					_ajax_nonce	: $file_upload.find('.et_ajaxnonce').attr('id'),
					action		: 'resume_upload_file',
					resume_id 	: $file_upload.find("input.resume_id").val(),
					field_id 	: $file_upload.find("input.field_id").val(),
					field_name 	: $file_upload.find("input.field_name").val(),
					field_type  : $file_upload.find('input.field_type').val(),
				},
				cbUploaded : function(up, file, res){
					view.loading.unblock();
					if (res.success){
						$file_upload.closest(".module").find(".field-thumb").append(view.templateFile(res.data));

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

		},
		templateFile: _.templateSettings = {
		    evaluate    : /<#([\s\S]+?)#>/g,
			interpolate : /\{\{(.+?)\}\}/g,
			escape      : /<%-([\s\S]+?)%>/g
		},

		templateFile : _.template('<span class="file-item"><a rel = "{{id}}" href="{{url}}" alt="{{name}}"> {{name}}</a> <span class="delete"><a class="" id="{{id}}" rel="{{id}}"> x </a></span></span>'),

		saveFields :function(event){

		},
		deleteAttachment : function(event){
			var target 	= $(event.currentTarget),
				view 	= this;

			var params = {
				url : et_globals.ajaxURL,
				type: 'post',
				data: {
					action: 'resume_romove_attachment',
					content: {
						id 	: target.attr('id'),
						resume_id : target.attr('rel'),
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

	});

JobEngine.Views.ResumeImgUpload = Backbone.View.extend({
		el 		: 'body',
		resume 	: [],
		jobseeker : [],
		field 	: [],
		events 	: {
			'submit form' : 'saveFields' ,
			'click a.del-thumbnail' : 	'deleteAttachment',
		},

		initialize : function (options) {

			var view 		= this,
				target 		= '#'+options.element,
				$field 		= $(target),
				div_thumb 	= $field.closest('.module').find('.field-thumb');

			this.loading 	= 	new JobEngine.Views.BlockUi();
			this.field_image	= new JobEngine.Views.File_Uploader({
				el					: target,
				uploaderID			: options.button,
				thumbsize			: 'thumbnail',
				max_file_size 		: '1mb',
				filters				: [
					{ title : 'Image Files', extensions : 'jpg,jpeg,gif,png' }
				],

				multipart_params	: {
					_ajax_nonce	: $field.find('.et_ajaxnonce').attr('id'),
					action		: 'resume_upload_file'	,
					field_name 	: $field.find('input.field_name').val(),
					resume_id	: $field.find('input.resume_id').val(),
					field_id	: $field.find('input.field_id').val(),
					field_type  : $field.find('input.field_type').val(),

				},
				cbUploaded : function(up, file, res){
					view.loading.unblock();
					if (res.success){
						div_thumb.append("<span class='thumb-item'> <a  target='_blank'  href='"+res.data.large[0]+"' href='_blank' ><img src='"+res.data.thumbnail[0]+"' /></a><span class='delete'> <a id ='"+res.data.id+"' class='del-thumbnail' rel='"+res.data.key+"'> X </a> </span></span>");

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

		},

		template: _.templateSettings = {
		    evaluate    : /<#([\s\S]+?)#>/g,
			interpolate : /\{\{(.+?)\}\}/g,
			escape      : /<%-([\s\S]+?)%>/g
		},

		template : _.template('<span class="img-item"><a  href="{{url}}" alt="{{name}}"> {{name}}</a> <span class="delete"><a class="" id="{{id}}" > x </a></span></span>" '),

		saveFields :function(event){

		},

		deleteAttachment : function(event){
			var target 	= $(event.currentTarget),
				view 	= this;

			var params = {
				url : et_globals.ajaxURL,
				type: 'post',
				data: {
					action: 'resume_romove_attachment',
					content: {
						id 	: target.attr('id'),
						resume_id : target.attr('rel'),
					}
				},
				beforeSend: function(){
					view.loading.block(target.closest(".thumb-item"));

				},
				success: function(resp){
					view.loading.unblock();
					target.closest(".thumb-item").remove();
				}
			}
			$.ajax(params);
		}

	});
	$(document).ready(function(){

		$(".btn-upload-image").each(function( index ,element) {
			var id 		=  $(element).attr('id').toString();
			var button  =  $(element).attr('rel').toString();
			new JobEngine.Views.ResumeImgUpload({element: id, button : button});
		});

		$(".btn-upload-file").each(function( index ,element) {
			var id 		=  $(element).attr('id').toString();
			var button  =  $(element).attr('rel').toString();
			new JobEngine.Views.ResumeFileUpload({element:id,button:button});
		});
	});

})(jQuery);
