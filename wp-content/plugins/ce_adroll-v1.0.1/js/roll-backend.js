(function($){
	CE.Views.AdRoll = Backbone.View.extend({
		el :'div#ce_adroll',
		events : {
		'change select.change' 		: 'changeSettingFront',
		'change select.page-roll' 	: 'savePageRollId',
		'change input' 				: 'changeInput',
		},

		initialize : function(){			
			this.styleSelector(this.$el);			
			this.blockUi = new CE.Views.BlockUi();
			var view = this;
			this.url = ce_adroll.link;			

			$('.bgcolor').ColorPicker({
					onSubmit: function(hsb, hex, rgb, el) {

						$(el).ColorPickerHide();
					},
					onChange : function(ev, hex) {						
						$('.bgcolor').val(hex);
						view.setupFrameSrc();
					},
					onBeforeShow: function () {
						$(this).ColorPickerSetColor(this.value);
					}
				})
				.bind('keyup', function(){
					$(this).ColorPickerSetColor(this.value);
				});

		},

		changeInput  : function (event) {
				 event.preventDefault();
				 this.setupFrameSrc();				 
		},

		autoChange : function(event){

		},
		savePageRollId : function(event){

			var target = $(event.currentTarget);
			var view = this;
			var form	=	$('#frm_roll').serialize();
			var width 	= 	$('#frm_roll #width').val();			
			var first_url = 'action=save-page-adroll&id='+target.val() +'&width=' + width +'&';			
			$.ajax ({
				type 	: 'get',
				url 	: et_globals.ajaxURL,
				data 	: first_url + form ,

				beforeSend  : function () {

					view.blockUi.block((view.$el.find("div.btn-effect"))) ;
					
				},

				success : function (resp) {
					

					if(resp.success){
						view.url = $("#select-page-roll").find(":selected").attr('data-url');
						view.setupFrameSrc();
						// var frame= view.template(resp.data);						
						// $('#frame_preview').attr('src',resp.data.url);
						// $('.code-content').val(frame);
						$("a#url_front").attr('href',resp.data.url_front);
						
					}
					view.blockUi.unblock();
				}
			});	

		},

		changeSettingFront  : function (event) {
				 event.preventDefault();
				 this.setupFrameSrc();
		},

		styleSelector : function(container){
        // apply custom look for select box
        	$(container).find('.select-style select').each(function(){
	            var $this = jQuery(this),
	                title = $this.attr('title'),
	                selectedOpt = $this.find('option:selected');
	            
	            if( selectedOpt.val() !== '' ){
	                title = selectedOpt.text();

	            } else if( selectedOpt.val() == '' ){

	               	selectedOpt = $this.closest('select').find("option").eq(0);	
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

	    setupFrameSrc : function () {
	    	
			var view 	= this;
	
			var form	=	$('#frm_roll').serialize(),
				width	=	parseInt($('#width').val()) ,
				height	=	parseInt($('.number').val()) * 78 + 77,
				frame	=	'<iframe id="frm_roll" src="'+ view.url +'&'+form+'" width="'+width+'" height="'+height+'" frameborder="0" allowtransparency="true" marginheight="0" marginwidth="0" style="border:0; overflow: hidden;"></iframe>';
				form = form.replace(/[^&]+=\.?(?:&|$)/g, '');				
			$('#frame_preview').attr('src', view.url +'&'+form).attr('width', width).attr('height', height);
			$('.code-content').val(frame.replace(/[^&]+=\.?(?:&|$)/g, ''));

			
		}, 

		htmlEntities : function (str) {
		    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
		},

		template : _.template('<iframe id="ce_adroll" src="<%= url %>" width="<%= width %>" height = "<%= height %>" frameborder="0" allowtransparency="true" marginheight="0" ')


	});
	jQuery(document).ready(function(){		
		new CE.Views.AdRoll;
	});

})(jQuery);