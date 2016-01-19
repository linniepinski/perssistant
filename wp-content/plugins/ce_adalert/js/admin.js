(function($){
	CE.Views.AdAlert = Backbone.View.extend({
		el: 'div#ce_alert',
		events : {
		'submit form#ad_alert' : 'saveAlertOption',
		'click a.section-link ' : 'actionTabs',
		'click span.del-subscriber' : 'delSubscriber',
		},
		initialize : function(){
			this.styleSelector(this.$el);
			var url = window.location.href;
			var post = url.split("#");
			if(typeof post[1] !=='undefined'){

				$("div.et-main-left a").each(function(index,item){
					if($(this).attr('id') == post[1]){
						$("a[href='#"+post[1]+"']").closest('ul').find('a').removeClass('active');
						$("a[href='#"+post[1]+"']").addClass('active');
					}
					// else
					// 	$(this).addClass('hide');
				});
				$("div.tab-alert").addClass('hide');
				var full = "#"+post[1]+"_tab";
				$(full).removeClass('hide');
			}


		},
		saveAlertOption : function(event){
			var target 	= $(event.currentTarget),
				view 	= this,
				block =  new CE.Views.BlockUi();
			$.ajax({
				data : target.serialize(),
				type 	: 'post',
				url : et_globals.ajaxURL,
				beforeSend: function(event){
					block.block($(view.el).find('button'));
				},
				success : function(){
					block.finish();
				}
			});
			return false;
		},
		actionTabs : function(event){
			var target 	= $(event.currentTarget);
			target.closest('ul').find('a').removeClass('active');
			target.addClass('active');
			var id 		= target.attr('href');
			var full_id = id+"_tab";
			$("div.tab-alert").addClass('hide');
			$(full_id).removeClass('hide');
			$(full_id).show();
			location.hash = id;
			return false;

		},
		delSubscriber : function(event){
			var target 	= $(event.currentTarget),
				sub_id = target.attr('id'),
				view 	= this,
				block =  new CE.Views.BlockUi();
				var answer = confirm("Remove subscriber?")

			if (!answer){
				      return false;
				}
			$.ajax({
				data : {
					action :'del-subscriber',
					id : sub_id,
				},
				type 	: 'post',
				url : et_globals.ajaxURL,
				beforeSend: function(event){
					block.block(target.closest('tr'));
				},
				success : function(res){
					block.finish();
					if(res.success){
						target.closest('tr').remove();
					}

				}
			});
			return false;
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
	});
	jQuery(document).ready(function(){
		new CE.Views.AdAlert();
	});
})(jQuery);