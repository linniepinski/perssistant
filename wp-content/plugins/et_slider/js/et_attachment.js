(function($){
$(document).ready(function(){
	new optionMail();

});


var pubsub	= pubsub || {};
_.extend(pubsub, Backbone.Events);
var ajaxParams = {
	type        : 'POST',
	dataType    : 'json',
	url         : et_globals.ajaxURL,
	contentType : 'application/x-www-form-urlencoded;charset=UTF-8'
};
var ajaxParams = ajaxParams;

/* loading button */

// end loading effect
var ET_SliderUpload = Backbone.View.extend({

	initialize	: function(){
		_.bindAll(this,'onFileAdded', 'onFileUploaded','updateThumbnail','insertThumb','updateConfig','onFilesBeforeSend','onUploadComplete');

		this.uploaderID	= (this.options.uploaderID) ? this.options.uploaderID : 'et_uploader';

		this.config	= {
			runtimes			: 'gears,html5,flash,silverlight,browserplus,html4',
			multiple_queues		: true,
			multipart			: true,
			urlstream_upload	: true,
			multi_selection		: false,
			upload_later		: false,
			container			: this.uploaderID + '_container',
			browse_button		: this.uploaderID + '_browse_button',
			thumbnail			: this.uploaderID + '_thumbnail',
			thumbsize			: 'thumbnail',
			file_data_name		: this.uploaderID,
			max_file_size 		: '1mb',
			//chunk_size 			: '1mb',
			// this filters is an array so if we declare it when init Uploader View, this filters will be replaced instead of extend
			filters				: [
				{ title : 'Image Files', extensions : 'jpg,jpeg,gif,png' }
			],
			multipart_params	: {
				fileID		: this.uploaderID
			}
		};

		jQuery.extend( true, this.config, et_globals.plupload_config, this.options );

		this.controller	= new plupload.Uploader( this.config );
		this.controller.init();

		this.controller.bind( 'FileUploaded', this.onFileUploaded );
		this.controller.bind( 'FilesAdded', this.onFileAdded );
		this.controller.bind( 'BeforeUpload', this.onFilesBeforeSend );
		this.bind( 'UploadSuccessfully', this.onUploadComplete );

		if( typeof this.controller.settings.onProgress === 'function' ){
			this.controller.bind( 'UploadProgress', this.controller.settings.onProgress );
		}
		if( typeof this.controller.settings.onError === 'function' ){
			this.controller.bind( 'Error', this.controller.settings.onError );
		}
		if( typeof this.controller.settings.cbRemoved === 'function' ){
			this.controller.bind( 'FilesRemoved', this.controller.settings.cbRemoved );
		}

	},

	onFileAdded	: function(up, files){
		if( typeof this.controller.settings.cbAdded === 'function' ){
			this.controller.settings.cbAdded(up,files);
		}
		if(!this.controller.settings.upload_later){
			up.refresh();
			up.start();
			console.log('start');
		}
	},

	onFileUploaded	: function(up, file, res){
		res	= $.parseJSON(res.response);
		if( typeof this.controller.settings.cbUploaded === 'function' ){
			this.controller.settings.cbUploaded(up,file,res);
		}
		if (res.success){
			this.updateThumbnail(res.data);
			this.trigger('UploadSuccessfully', res);
		}
	},

	updateThumbnail	: function(res){
		var that		= this,
			$thumb_div	= this.$('#' + this.controller.settings['thumbnail']),
			$existing_imgs, thumbsize;

		if ($thumb_div.length>0){

			$existing_imgs	= $thumb_div.find('img'),
			thumbsize	= this.controller.settings['thumbsize'];

			if ($existing_imgs.length > 0){
				$existing_imgs.fadeOut(100, function(){
					$existing_imgs.remove();
					if( _.isArray(res[thumbsize]) ){
						that.insertThumb( res[thumbsize][0], $thumb_div );
					}
				});
			}
			else if( _.isArray(res[thumbsize]) ){
				this.insertThumb( res[thumbsize][0], $thumb_div );
			}
		}
	},

	insertThumb	: function(src,target){
		jQuery('<img>').attr({
				'id'	: this.uploaderID + '_thumb',
				'src'	: src
			})
			// .hide()
			.appendTo(target)
			.fadeIn(300);
	},

	updateConfig	: function(options){
		if ('updateThumbnail' in options && 'data' in options ){
			this.updateThumbnail(options.data);
		}
		$.extend( true, this.controller.settings, options );
		this.controller.refresh();
	},

	onFilesBeforeSend : function(){
		if('beforeSend' in this.options && typeof this.options.beforeSend === 'function'){
			this.options.beforeSend(this.$el);
		}
	},
	onUploadComplete : function(res){
		if('success' in this.options && typeof this.options.success === 'function'){
			this.options.success(res);
		}
	}

});


ET_Slider_Model = Backbone.Model.extend({
	initialize : function(){},
	parse : function(resp){			
		if ( resp.data ){
			return resp.data;
		}
	},
	remove : function(options){
		
		this.sync('delete', this, options);
	},
	add : function(options){
		this.sync('add', this, options);
	},
	sync	: function(method, model, options) {
		options	= options || {};
		var success	= options.success || function(resp){ };
		var beforeSend	= options.beforeSend || function(){ };
		var params		= _.extend(ajaxParams, options);
		var thisModel	= this;
		var action	= 'et_sync_attachment';

		

		if ( options.data ){
			params.data = options.data;
		}
		else {
			params.data = model.toJSON();		}
		
		params.success = function(resp) {		
		thisModel.set( thisModel.parse(resp) );		
			switch( method ){
				case 'add':
					if( resp.success ) {
						thisModel.set('id',resp.data.id );
						thisModel.set('attach_url',resp.data.attach_url);
						thisModel.set( thisModel.parse(resp.data) );									
						pubsub.trigger('je:setting:SliderAdded', thisModel, resp);

						$("#slider_thumb_container span#slider_thumb_thumbnail").html('');									
						//$("textarea#description").val('');
						tinyMCE.get('description').setContent('');

						$("form#et_slide_form").find('#attach_id').val('');
						$("form#et_slide_form input[name=et_link]").val('http://');					
						tinyMCE.get('description').setContent('');
						$('#description').html('');
					} else {						
						alert(resp.msg);
					}					
					break;
				case 'delete':					
					pubsub.trigger('je:setting:SliderRemoved', thisModel, resp);
					thisModel.trigger('remove');
					//thisModel.destroy();
					break;
				case 'update':
					if( resp.success ) {						
						thisModel.set('attach_url',resp.data.attach_url);
						thisModel.trigger('updated');
						pubsub.trigger('je:setting:SliderUpdated', thisModel, resp);
					} else {
						alert(resp.msg);
					}
					break;
				default :
					pubsub.trigger('je:setting:SliderSynced', thisModel, resp);
					break;
			}
			success(resp);
		};

		params.beforeSend = function(){
			beforeSend();
		};
		//params.method	= method;
		params.data = jQuery.param( {method : method, action : action, content : params.data });

		return jQuery.ajax(params);
	}
});


var optionMail = Backbone.View.extend({
	el: '#et-slider',
	events: {		
		//'click .toggle-button' 					: 'onToggleFeature',
		'submit form#et_slide_form'	: 'submitFormAddPlan',
		'blur #et_link'				: 'parseUrl'	
		
	},
	initialize: function(){
		
		this.setupView();
		var view = this;
		var appView =	this;
		this.initSlides();	
		this.loading = new ET_BlockLoad();
		$('.sortable').sortable({
			axis: 'y',
			handle: 'div.sort-handle'
		});
		$('ul.list-thumbnail').bind('sortupdate', function(e, ui){
			appView.updateSlideOrder();
		});
		this.add_slider	= this.$('form#et_slide_form').validate({
				rules	: {
					title		: "required",
					description : "required",
					attach_id   : "required",
					et_link		: "required"				
				 },
			    messages: {
			        attach_id: "Select an image.",
			    }
		});
		 // $.validator.addMethod('accept', function () { return true; });
	},
	initSlides : function(){
		// initilize payment plans
		var planCollection = new ET_Slider_View_Collection({el : 'ul.list-thumbnail' });
	},
	
		

	submitFormAddPlan : function(event){		
		// if(!this.add_slider.form()) return false;
		event.preventDefault();
		var form = $(event.target);
		var element 	= $(event.currentTarget);
		var container 	= element.parent();
		var button = form.find('.engine-submit-btn');	
		
		var model = new ET_Slider_Model({
			title 		: form.find('input[name=title]').val(),			
			et_link 	: form.find('input[name=et_link]').val(),
			read_more 	: form.find('input[name=read_more]').val(),					
			description : form.find('textarea[name=description]').val(),
			// description : tinyMCE.get('description').getContent(),
			parrent : $("form#fadd").attr('data'),
			attach_id : form.find('input[name=attach_id]').val(),			
			attach_url  :'',
			et_ajaxnonce : form.find('span.et_ajaxnonce_add').attr('id'),			
		

		}),
		loading = new ET_Slider_LoadingButton({el : button});		

		model.add({
			beforeSend : function() {
				loading.loading();
			},
			success : function( resp ){
				loading.finish();
				if(resp.success){
					form.find('input').val('');
					
				}
			}
		});
		
		return false;
	},
	parseUrl : function(event){
		var et_link = $("form#et_slide_form").find('#et_link');
		var url = et_link.val();
		if (url.toLowerCase().indexOf("http://") < 0 &&  url.toLowerCase().indexOf("https://") < 0 ){
			var new_url = 'http://' + url;
			et_link.val(new_url);
			et_link.focus();
		}	
		
	},

	setupView	: function(){

		// init logo upload
		var that		= this,
			$slider_thumb	= this.$('#slider_thumb_container');

		var blockUi = new ET_BlockLoad();
		this.logo_uploader	= new ET_SliderUpload({
			el					: $slider_thumb,
			uploaderID			: 'slider_thumb',
			thumbsize			: 'large',
			multipart_params	: {
				_ajax_nonce	: $slider_thumb.find('.et_ajaxnonce').attr('id'),
				action		: 'et_attachment_upload'
			},
			cbUploaded		: function(up,file,res){
				if(res.success){					
					var attach_id = res.data.attach_id;					
					$slider_thumb.find("input#attach_id").val(attach_id);

					//that.job.author.set('slider_thumb',res.data,{silent:true});
				} else {
					pubsub.trigger('je:notification',{
						msg	: res.msg,
						notice_type	: 'error'
					});				
				}
			},
			beforeSend	: function(element){
				blockUi.block($slider_thumb.find('.company-thumbs'));
			},
			success : function(){
				blockUi.unblock();

			}
		});
		// edit
	},

	updateSlideOrder : function(){
		var order = $('ul.list-thumbnail').sortable('serialize');

		var params = ajaxParams;
		params.data = {
			action: 'et_sort_attachment',
			content : {
				order: order
			}
		};
		params.before = function(){	}
		params.success = function(data){
		}
		$.ajax(params);
	},
});


ET_Slider_View_Collection = Backbone.View.extend({
	el : 'ul.list-thumbnail',
	initialize: function(){
		var view = this;
		view.views = [];
		view.collection = new ET_Slider_Collection( JSON.parse( $('#list_slide_data').html() ) );		
		view.$el.find('li').each(function(index){
			var $this = $(this);
			view.views.push( new ET_Slide_View_Item({
				model : view.collection.models[index],
				el : $this
			}) );
		});

		this.collection.bind('remove', this.removeView, this );
		this.collection.bind('add', this.addView, this );

		pubsub.on('je:setting:SliderAdded', this.addView, this);

	},
	add : function(model){

		this.collection.add(model);
	},
	removeView : function(model){
		
		var thisView = this;
		var viewToRemove = _.filter( thisView.views, function(vi){ 
			return vi.model.get('id') == model.get('id');
		})[0];

		_.without(thisView.views, viewToRemove);

		viewToRemove.fadeOut();
	},
	addView : function(model){

		//console.log(model);
		
		var view = new ET_Slide_View_Item({model: model});
		this.views.unshift( view );

		view.render().$el.hide().prependTo( this.$el ).fadeIn();
	}
});


ET_Slider_Collection = Backbone.Collection.extend({
	//model: JobEngine.Models.PaymentPlan,
	model: ET_Slider_Model,
	initialize: function(){ }
});

//	=============================================
//	View Payment Edit Form
//	=============================================
ET_Slider_EditForm = Backbone.View.extend({
	tagName : 'div',
	events : {
		'submit form.edit-attachment' : 'saveSlide',
		'click .cancel-edit' : 'cancel'
	},

	template : '', //_.template( $('#template_edit_form').html() ),
	render : function(){
		this.$el.html( this.template( this.model.toJSON() ) );
		return this;
	},
	initialize : function(options){
		_.templateSettings = {
			    evaluate    : /<#([\s\S]+?)#>/g,
				interpolate : /\{\{(.+?)\}\}/g,
				escape      : /<%-([\s\S]+?)%>/g
			};
		// apply template for view
		if ( $('#template_edit_form').length > 0 )
			this.template = _.template( $('#template_edit_form').html() );

		this.model.bind('update', this.closeForm, this);
		this.appear();
		this.add_slider	= this.$('form.edit-attachment').validate({
				rules	: {
					title		: "required",
					et_description : "required",
					et_link : {
						url: true,
						required: true
					}
				},
			    messages: {
			        attach_id: "Select an image.",
			    }
		});

	},

	appear : function(){
		this.render().$el.hide().appendTo( this.options.parent ).slideDown();
	},
	/* for update */

	saveSlide : function(event){
		
		event.preventDefault();
		var form = this.$el.find('form');
		var view = this;

		this.model.set({
			title: form.find('input[name=title]').val(),	
			attach_id: form.find('input[name=attach_id]').val(),					
			et_link: form.find('input[name=et_link]').val(),
			read_more: form.find('input[name=read_more]').val(),			
			description: form.find('textarea[name=description]').val()			
		});
		
		this.model.save(this.model.toJSON(), {
			beforeSend : function(){
				view.loading = new ET_Slider_LoadingButton({el : form.find('#save_resume_playment_plan') });
				view.loading.loading();
				

			},
			success : function(model, resp){
				console.log('resp success 0k');
				view.loading.finish();
				if(resp.success)
					view.closeForm(); 

			}
		});
		
	},
	cancel : function(event){
		event.preventDefault();
		this.closeForm();
	},
	closeForm : function(){
		
		this.$el.slideUp( 500, function(){ $(this).remove(); });
	}
});

ET_Slide_View_Item = Backbone.View.extend({
	tagName : 'li',
	className : 'item',
	events : {
		'click a.act-edit' : 'editPlans',
		'click a.act-del' : 'removeSlide',
		'click a.act-del-slider' : 'removeSlide',
		'click .slider-title': 'displayInputTitle',		
		'change #title_slider' : 'autoSaveTitle',
		'blur   #title_slider'	: 'close'
		
	},
	initialize: function(){
		console.log(this.model);
		this.model.bind('updated', this.render, this );
		this.model.bind('detroy', this.fadeOut, this);
		this.model.bind('remove', this.fadeOut, this);		
	},

	template : _.template("<div class='sort-handle'></div><span><%= title %></span>" +
		"<div class='actions'>" +
			"<a href='#' title='Edit' class='icon act-edit' rel='<%=id %>' data-icon='p'></a> " +
			"<a href='#' title='Delete' class='icon act-del' rel='<%= id %>' data-icon='D'></a>" +
		"</div>"),
	
	render : function(){
				
		this.$el.html( this.template(this.model.toJSON()) ).attr('data', this.model.id ).attr('id', 'slide_' + this.model.id);
		
		return this;
	},
	
	blockItem : function(){
		this.blockUi = new ET_BlockLoad();
		this.blockUi.block(this.$el);
	},

	unblockItem: function(){
		this.blockUi.unblock();
	},

	editPlans : function(event){		
		event.preventDefault();
		//console.log(this.$el);
		var et_form = $(".et-form");
		
		if ( this.editForm && this.$el.find('.engine-payment-form').length > 0 ){ // cai nay dang mo.			
			this.editForm.closeForm(event); 
			
			
		} else if( this.$el.find('.engine-payment-form').length < 1 ){
				if($(".et-form").find(".engine-payment-form").length > 0)
					$(".et-form").find(".engine-payment-form").remove();
				this.editForm = new ET_Slider_EditForm({ model: this.model, parent: this.$el });
		}
		// tinymce.PluginManager.load('myplugin', '/some/dir/someplugin/aaa.js');
		// tinymce.PluginManager.load('imagemanager', '/plugins/imagemanager/editor_plugin.js'); 
		// tinymce.PluginManager.load('filemanager', '/plugins/filemanager/editor_plugin.js');
		
		var $target = $(event.currentTarget);
		
		var selector = $target.closest('.item').find(".et_description");

		setTimeout(function(){
			// clear cache if has assigned tinymce for this element.
			tinymce.EditorManager.execCommand('mceRemoveEditor', false, selector.attr('id'));

		},100);
		
		setTimeout(function(){
			tinyMCE.execCommand("mceRepaint");
			tinyMCE.init({	     
	        selector:  '#'+selector.attr('id'),
    		theme: "modern",
	        autoresize_min_height: 200,	       
	        menubar : false,   
			autoresize_max_height: 350,
			menu : { // this is the complete default configuration
			        file   : {title : 'File'  , items : 'newdocument'},
			        edit   : {title : 'Edit'  , items : 'undo redo | cut copy paste pastetext | selectall'},
			        insert : {title : 'Insert', items : 'link  autolink media | template hr'},
			        view   : {title : 'View'  , items : 'visualaid'},
			        format : {title : 'Format', items : 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
			        table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
			        tools  : {title : 'Tools' , items : 'spellchecker code'}
			    },
			plugins : ['link','autolink','image'],
		
			toolbar: "bold underline italic link autolink| alignleft aligncenter alignright alignjustify | bullist numlist",
			

	    	});	
	    	// if don't has selector attribute we can use this line to add editor.
			//tinymce.EditorManager.execCommand('mceAddEditor', true, selector.attr('id'));
	    }, 	500);
		
	 	

		var $et_slider	= this.$('#et_slider_container');	

		var blockUi = new ET_BlockLoad();
		this.logo_uploader	= new ET_SliderUpload({
			el					: $et_slider,
			uploaderID			: 'et_slider',
			thumbsize			: 'large',
			multipart_params	: {
				_ajax_nonce	: $et_slider.find('.et_ajaxnonce').attr('id'),
				action		: 'et_attachment_upload'
			},
			cbUploaded		: function(up,file,res){
				if(res.success){					
					var attach_id = res.data.attach_id;					
					$et_slider.find("input#attach_id").val(attach_id);
					
				} else {
					pubsub.trigger('je:notification',{
						msg	: res.msg,
						notice_type	: 'error'
					});
				
				}
			},
			beforeSend	: function(element){
				blockUi.block($et_slider.find('.company-thumbs'));
			},
			success : function(){
				blockUi.unblock();

			}
		});
	},
	removeSlide : function(event){	
		
		// ask user if he really want to delete
		if ( !confirm(et_slider.confirm_delete_slide) ) return false;
		
		event.preventDefault();
		var view = this;
		this.model.remove({
			beforeSend: function(){
				view.blockItem();
			},
			success: function(resp){							
				view.unblockItem();				

			}
		});
	},
	displayInputTitle : function (event){    	
    	this.$el.addClass("editing");
    	this.$el.find('input').focus();

	},

	autoSaveTitle : function(event){
	
		var id = this.model.attributes.id;
		var that = this.model;

		var title = $("#slider_" + id + " #title_slider").val();		
		var params		= _.extend(ajaxParams);
		params.success = function(resp) {
			if(resp.success){
				$("#slider_" + id).find('.slider-title').html('<strong> ' + title  +' </strong>' );
				$("#slider_" + id).removeClass('editing');				
			
			}			
		}

		params.data = jQuery.param( {action : 'et_save_slider', title : title,id : id });
		return jQuery.ajax(params);

	},
	close: function(event) {	 	
    	this.$el.removeClass('editing');
    },


	fadeOut : function(){
		this.$el.fadeOut(function(){ $(this).remove(); });
	}
});

})(jQuery);