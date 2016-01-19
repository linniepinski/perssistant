//backendRoute.on('et:changeMenu', je_alert_on_menu_change);
(function($){
	$(document).ready(function(){
		new JE_ALERT ();		
	});

	JE_ALERT	= Backbone.View.extend({
		el : 'div#je_alert',
		events : {
			'click .et-menu-content li a' 		: 'changeMenu',
			'click  #save_setting'  			: 'updateSettings',
			'click .paginate a' 				: 'paginate'
			
		},
		changeMenu : function (event) {
			event.preventDefault();
			var target = $(event.currentTarget),
				menu = target.attr('href');
			// 
			$('.et-menu-content li a').removeClass('active');
			target.addClass('active');
			// display content
			$('.et-main-main').hide();
			$(menu).show();
		},
		initialize : function () {
			this.styleSelector(this.$el);
		},
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

		updateSettings : function (event) {
			
			event.preventDefault();
			var data	=	$('form#job_alert_setting').serialize(),
				blockUI	=	new JobEngine.Views.BlockUi();

			//if($('form#job_alert_setting')	.valid() ) { // valid form 
				// ajax update job alert setting
				$.ajax ({
					type : 'post',
					url : 'admin-ajax.php',
					data : data,
					beforeSend : function  () {
						blockUI.block($(event.currentTarget));
					},
					success : function () {	
						blockUI.unblock();
					}

				});
			//}
		},

		paginate : function(event){
			event.preventDefault();

			var page = parseInt($.trim($(event.currentTarget).html()));
			this.goToPage(page);
		},

		goToPage : function(page){
			var BlockUi	=	new JobEngine.Views.BlockUi();
			var view = this,
				params = {
				url 	:'admin-ajax.php',
				type 	: 'post',
				data 	: {
					action : 'subscriber-change-page',
					content : {
						page : page
					}
				},
				beforeSend : function(){
					BlockUi.block($('.job-alert-subscribe'));
				},
				success : function(resp){
					// console.log(resp);
					BlockUi.finish();
					if (resp.success){
						// refresh imported job
						view.fillSubsriberList(resp.data.subscribers);
						// refresh pagination
						var html = '';
						if(resp.data.pages_max <= 6 && resp.data.pages_max > 0 ){
							for($i = 1; $i <= resp.data.pages_max; $i++){
								if ($i == resp.data.page)
									html += '<span class="current">' + $i + '</span>';
								else 
									html += '<a href="#">' + $i + '</a>';
							}
						} else {
							if( resp.data.page > 4 ) {
								var prev	= resp.data.page - 2;
								html += '<a href="#">1</a>';
								html += '<span>...</span>';
								if(resp.data.page < (resp.data.pages_max - 2)) {
									var last	=	parseInt(resp.data.page) +2;
								} else {
									var last	=	resp.data.pages_max;
								}
								
								for($i = prev; $i <= last; $i++){
									if ($i == resp.data.page)
										html += '<span class="current">' + $i + '</span>';
									else 
										html += '<a href="#">' + $i + '</a>';
								}

							} else {
								for($i = 1; $i <= 4 + 1 ; $i++){
									if ($i == resp.data.page)
										html += '<span class="current">' + $i + '</span>';
									else 
										html += '<a href="#">' + $i + '</a>';
								}
							}

							if(resp.data.pages_max != resp.data.page &&  resp.data.pages_max > (parseInt(resp.data.page) +2) ) {
								html += '<span>...</span>';
								html += '<a href="#">' + resp.data.pages_max + '</a>';
								
							}
						}

						$('.subscriber-controls .paginate').html(html);
					}
				}
			}
			return $.ajax(params)
		},

		fillSubsriberList : function (data) {
			var table = $('.list-job-alert');

			// clear old data
			table.find('tr:not(:eq(0))').remove();
			_.templateSettings = {
			    evaluate    : /<#([\s\S]+?)#>/g,
				interpolate : /\{\{(.+?)\}\}/g,
				escape      : /<%-([\s\S]+?)%>/g
			};
		
			var template = _.template($('#subscriber_list_template').html());

			$.each(data, function(index, job){
				job.i = index;
				table.find('tbody').append(template(job));
			});
		}

	});

})(jQuery);




