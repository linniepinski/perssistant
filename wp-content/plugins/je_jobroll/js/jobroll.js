(function ($) {
	$(document).ready(function() {
		JobEngine.Views.Jobroll	=	Backbone.View.extend ({
			el : '#jobroll_info',

			events : {
				'change input' 	: 'changeSettingFront',
				'change .front-change' 	: 'changeSettingFront',
				// update jobroll setting
				'change select.auto-save' : 'changeSetting',
				// update jobroll page id
				'change select.save-page' : 'changePageRoll'
			},

			initialize : function () {
				var view	=	this;

				this.url = je_jobroll.link;

				$('#color').ColorPicker({
					onSubmit: function(hsb, hex, rgb, el) {
						$(el).ColorPickerHide();
					},
					onChange : function(ev, hex) {
						$('#color').val(hex);
						view.setupFrameSrc();
					},
					onBeforeShow: function () {
						$(this).ColorPickerSetColor(this.value);
					}
				})
				.bind('keyup', function(){
					$(this).ColorPickerSetColor(this.value);
				});

				// apply custom look for select box
				$(this.$el).find('.backend.select-style select').each(function(){
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
			changeSettingFront  : function (event) {
				 event.preventDefault();
				 this.setupFrameSrc();
			},
			changeSetting  : function (event) {
				// event.preventDefault();
				// this.setupFrameSrc();

				var view = this;
				var form	=	$('#jobroll_form').serialize();
				$.ajax ({

					type 	: 'get',

					url 	: et_globals.ajaxURL,

					data 	: form + '&action=je_save-page-jobroll',

					beforeSend  : function () {
					},

					success : function (resp) {
						if(resp.success){
						var frame= view.template(resp.data);
						$('#frame_preview').attr('src',resp.data.url);
						$('.code-content').html(view.htmlEntities(frame));
						$("a#url_front").attr('href',resp.data.url_front);
						}
					}
				});

			},
			changePageRoll : function (event){
				var view = this;
				var target = $(event.currentTarget);

				var id = target.val();
				console.log(id);
				view.url = $("#page_jobroll").find(":selected").attr('data-url');

				var form	=	$('#jobroll_form').serialize();
				$.ajax ({

					type 	: 'get',

					url 	: et_globals.ajaxURL,

					data 	: form + '&action=je_save-page-jobroll&id= '+target.val(),

					beforeSend  : function () {
					},

					success : function (resp) {
						if(resp.success){	
						var frame= view.template(resp.data);
						$('#frame_preview').attr('src',resp.data.url);
						$('.code-content').html(view.htmlEntities(frame));
						$("a#url_front").attr('href',resp.data.url_front);
						}
					}
				});

			},

			setupFrameSrc : function () {
				var view = this;

				var form		=	$('#jobroll_form').serialize(),
					width		=	parseInt($('#width').val()) +50,
					height		=	parseInt($('#number').val()) * 50 + 110,
					frame		=	'<iframe id="je_jobroll" src="' + view.url+'&'+form+'" width="'+width+'" height="'+height+'" frameborder="0" allowtransparency="true" marginheight="0" marginwidth="0" style="border:0; overflow: hidden;"></iframe>';

				$('#frame_preview').attr('src', view.url+'&'+form).attr('width', width).attr('height', height);
				$('.code-content').html(this.htmlEntities(frame));
			},

			htmlEntities : function (str) {
			    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
			},

			template : _.template('<iframe id="je_jobroll" src="{{ url }}" width="{{width}}" height = "{{height}}" frameborder="0" allowtransparency="true" marginheight="0" ')

		});

		new JobEngine.Views.Jobroll();

	}) ;
})(jQuery);

// Publishers
// Add Jobs to your website