(function ($) {
$(document).ready(function(){
	//backendRoute.on('et:changeMenu', rss_on_menu_change);

	ET_RSS_Import	= Backbone.View.extend({
		el : 'div#rss_import',
		events : {
			'click #get_rss' 					: 'getRss',
			'change select[name=apply_cat]' 	: 'massChangeCat',
			'change select[name=apply_type]' 	: 'massChangeType',
			'change input[name=apply_location]'	: 'massChangeLocation',
			'change input.setall' 				: 'massCheck',
			'submit form#import' 				: 'saveImportation',
			'click .et-menu-content li a' 		: 'changeMenu',
			'click #delete_rss' 				: 'deleteRssJob',
			'click .paginate a' 				: 'paginate',
			'click #add-rss-schedule'			: 'showScheduleForm',
			'keypress #form-schedule input:text': 'noEnterSubmit',
			'click #form-schedule input:submit' : 'RSSUpdateSchedule',
			'click #schedule_list .edit'        : 'RSSEditSchedule',
			'click #schedule_list .delete'      : 'RSSDeleteSchedule',
			'click #schedule_list .power'       : 'RSSOffSchedule',
			'change #recurrence'				: 'updateRecurrenceTime',

			'change #rss_options .option' 		: 'updateJobLimitDate',
			'click #rss_options #delete_old_jobs': 'deleteOldJobs' 

		},

		initialize : function () {
			this.styleSelector(this.$el);
			_.templateSettings = {
			    evaluate    : /<#([\s\S]+?)#>/g,
				interpolate : /\{\{(.+?)\}\}/g,
				escape      : /<%-([\s\S]+?)%>/g
			};
		
			this.$('#search_author').autocomplete({
				source : JSON.parse($('#user_source').html()),
				select : function(event, ui){
					$('#import_author').val(ui.item.id);
				}
			});

			this.blockUi = new JobEngine.Views.BlockUi();

			this.schedule_list	=	JSON.parse($('#schedule_data').html());
		},

		noEnterSubmit : function (e) {
			if ( e.which == 13 ) e.preventDefault();
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

		getRss :  function () {
			var loadingButton = new JobEngine.Views.LoadingButton({el: '#get_rss'});
			var view = this;

			$('#indeed_error').remove();
			$.ajax ({
				url  : 'admin-ajax.php',
				data : {
					action : 'rss-import-job',
					link    : $('#rss_link').val()
				},
				type : 'post',
				beforeSend : function () {
					loadingButton.loading();
				},
				success : function (response) {
					loadingButton.finish();
					$('#rss_error').remove();
					var response	=	jQuery.parseJSON(response);
					if(!response.success) {
						$('#get_rss').parent().append('<div id="rss_error" class="error">'+response.msg[0]+'</div>');
						$('#import_search_result').hide();
						$('.import-tb-container > table').find('body').html('');
					} else {
						$('#rss_error').remove();
						fillDataToTableRss(response.data);
						view.styleSelector($('.import-tb-container > table'));
						$('#import_search_result').slideDown();
					}
				}
			});
		},

		changeMenu : function(event){
			console.log('change menu');
			event.preventDefault();
			var target = $(event.currentTarget),
				menu = target.attr('href');
			// 
			$('.et-menu-content li a').removeClass('active');
			target.addClass('active');
			// display content
			$('.setting-content .et-main-main').hide();
			$(menu).show();

			if(menu == '#rss-manage') {
				this.goToPage(1);
			}
		},

		deleteRssJob: function(event){
			event.preventDefault();
			// 
			if ($('#ijobs table td input:checked').length <= 0){
				return alert('Please choose as least one job');
			}

			// getting selected jobs
			var ids 	= [],
				paged 	= parseInt($.trim($('.rss-controls .paginate > span.current').html())),
				that = this;
			console.log(paged);
			$('#ijobs table td input:checked').each(function(){
				ids.push($(this).val());
			});

			// generate ajax params
			var params = {
				url 	: 'admin-ajax.php',
				type 	: 'post',
				data 	: {
					action : 'rss-delete-jobs',
					content : {
						ids : ids,
						page : paged,
						page_max : $('.paginate > *').length
					}
				},
				beforeSend: function(){
					$(event.currentTarget).addClass('disable');
					that.blockImportedJobs();
				},
				success : function(resp){
					that.unblockImportedJobs();
					if (resp.success){
						// refresh imported job
						//fillImportedRSSData(resp.data.jobs);
						// refresh pagination
						if(paged > 1)
							that.goToPage(parseInt(paged -1));
						else 
							that.goToPage(1);
						
						$('.setall').removeAttr('checked');
					}
				}
			};

			$.ajax(params);
		},

		massCheck : function(e){
			var isChecked 	= $(e.currentTarget).is(':checked');
			var table 		= $('.import-tb-container > table');
			if ( isChecked )
				table.find('input.allow').attr('checked','checked');
			else 
				table.find('input.allow').removeAttr('checked');
		},

		massChangeCat : function(e){
			var value 			= $(e.currentTarget).val();
			var table 			= $('.import-tb-container > table');
			var affected_rows 	= _.map( table.find('tr:not(:eq(0)) input.allow:checked'), function(element){
				return $(element).closest('tr').find('select.job_cat');
			});

			_.each(affected_rows, function(data, index){
				$(data).val(value);
				$(data).trigger('change');
			});
		},

		massChangeType : function(e){
			var value 			= $(e.currentTarget).val(),
				table 			= $('.import-tb-container > table'),
				affected_rows 	= _.map( table.find('tr:not(:eq(0)) input.allow:checked'), function(element){
					return $(element).closest('tr').find('select.job_type');
				});

			_.each(affected_rows, function(data, index){
				$(data).val(value);
				$(data).trigger('change');
			});
		},

		massChangeLocation : function (e) {
			var value 			= $(e.currentTarget).val(),
				table 			= $('.import-tb-container > table'),
				affected_rows 	= _.map( table.find('tr:not(:eq(0)) input.allow:checked'), function(element){
					return $(element).closest('tr').find('input.job_location');
				});
				//console.log (affected_rows);
			_.each(affected_rows, function(data, index){
				$(data).val(value);
			});

		},	

		saveImportation : function(e){
			e.preventDefault();
			var data = $('form#import').serialize(),
				loadingBtn = new JobEngine.Views.LoadingButton({el : $('form#import').find('button') });

			var params = {
				url : 'admin-ajax.php',
				type : 'post',
				data: {
					action 	: 'rss-save-imported-jobs',
					content : data
				},
				beforeSend: function(){
					loadingBtn.loading();
				},
				success : function(resp){
					loadingBtn.finish();
					alert(resp.msg);
				}
			}

			$.ajax(params);
		},

		paginate : function(event){
			event.preventDefault();

			var page = parseInt($.trim($(event.currentTarget).html()));
			this.goToPage(page);
		},

		goToPage : function(page){
			var view = this;
			var params = {
				url 	:'admin-ajax.php',
				type 	: 'post',
				data 	: {
					action : 'rss-change-page',
					content : {
						page : page
					}
				},
				beforeSend : function(){
					view.blockImportedJobs();
				},
				success : function(resp) {
					view.unblockImportedJobs();
					// console.log(resp);
					var html = '';
					if (resp.success){
						// refresh imported job
						fillImportedRSSData(resp.data.jobs);
						// refresh pagination
						
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

					} else {
						var table = $('#ijobs table');
						table.find('tr:not(:eq(0))').remove();
					}
					$('.rss-controls .paginate').html(html);
				}
			}

			return $.ajax(params);
		},

		showScheduleForm : function (event) {
			event.preventDefault();
			console.log ('show rss schedule form');
			var $currentTarget	=	$(event.currentTarget),
				parent			=	$currentTarget.parents('.module'),
				data			=	{
										'rss_link' 		: '', 
										'schedule_id' 	: '',
										'time'			: '',
										'author'		: '',
										'import_author' : '',
										'ON'			: 1 , 
										'job_location' : ''
									};
				this.displayScheduleForm(parent, data );
		},

		
		RSSEditSchedule : function (event) {
			event.preventDefault();
			var currentTarget	=	$(event.currentTarget),
				schedule_id		=	currentTarget.parents('td').find('.schedule_id').val(),
				data			=	this.schedule_list[schedule_id];
			this.displayScheduleForm(currentTarget.parents('.module'), data );
			
		},

		RSSUpdateSchedule : function (event) {
			event.preventDefault();
			var $currentTarget	=	$(event.currentTarget);
			var schedule_list	=	this.schedule_list;
			var update			=	$('#rss_schedule_form').find('.schedule_id').val();	
			var schedule	=	$('#rss_schedule_form').serialize(),
				view		=	this,
				block		=	this.blockUi;
				params = {
				url 	:'admin-ajax.php',
				type 	: 'post',
				data 	:	schedule ,

				beforeSend : function(){
					block.block($('#submit_schedule'));
				},

				success : function(resp){
					$('#rss_schedule_error').remove();
					block.unblock();
					var resp	=	jQuery.parseJSON(resp);
					if(!resp.success) {
						var error_msg = '', i = 0;
						for(i = 0 ; i < resp.msg.length ; i++)
							error_msg	+=	resp.msg[i]	;
						
						$currentTarget.parents('.form-button').append('<div id="rss_schedule_error" class="error">'+error_msg+'</div>');
					} else {
						var template	=	_.template($('#et_rss_list').html());
						var data		=	resp.data;

						fillPreviewRss(resp.rss);
						view.styleSelector($('.job-import-review > table'));
						$('.job-import-review').slideDown();

						schedule_list[resp.data.schedule_id]	=	data;
						if(update == '') {
							var tr = document.createElement("tr");
							tr.setAttribute ('id', 'schedule-'+resp.data.schedule_id);
							tr.innerHTML =	template(resp.data);
							$('#schedule_list').find('tbody').append(tr);
						}
						else 
							$('#schedule_list').find('#schedule-'+resp.data.schedule_id).html(template(resp.data));
					}
				}
			};
			$.ajax (params);
			
		}
		,
		RSSDeleteSchedule : function (event) {
			event.preventDefault();
			var $currentTarget	=	$(event.currentTarget);
			var block		=	this.blockUi,
				schedule	=	$currentTarget.parents('td').find('.schedule_id').val(),
				params = {
				url 	:'admin-ajax.php',
				type 	: 'post',
				data 	:	{action : 'rss-detele-schedule', schedule_id : schedule},

				beforeSend : function(){
					block.block($currentTarget.parents('tr'));
				},

				success : function(resp){
					block.unblock();
					if(resp.success) {
						$currentTarget.parents('tr').remove();
					}
				}
			};
			$.ajax (params);
		},

		RSSOffSchedule : function (event) {
			event.preventDefault();
			var $currentTarget	=	$(event.currentTarget);
			var block		=	this.blockUi,
				schedule	=	$currentTarget.parents('td').find('.schedule_id').val(),
				params = {
				url 	:'admin-ajax.php',
				type 	: 'post',
				data 	:	{action : 'rss-off-schedule', schedule_id : schedule},

				beforeSend : function(){
					block.block($currentTarget.parents('tr'));
				},
				success : function(resp){
					block.unblock();
					if(resp.success) {
						if(resp.icon == 'Q') {
							$currentTarget.parents('tr').removeClass('off');
							$currentTarget.attr('title', 'Turn off this schedule');
						}else  {
							$currentTarget.parents('tr').addClass('off');
							$currentTarget.attr('title', 'Turn on this schedule');
						}
					}
				}
			};
			$.ajax (params);
		}, 

		displayScheduleForm : function ( parents, data ) {
			$('#form-schedule').remove ();
			template		=	_.template($('#et_rss_schedule').html());
			parents.append(template(data));
			
			$("#job_category option[value="+data.job_category+"]").prop("selected", true);
			$("#job_type option[value="+data.job_type+"]").prop("selected", true);

			$('.job-import-review').hide();
			$('#rss_link_input').focus();
			this.$('#search_author_schedule').autocomplete({
				source : JSON.parse($('#user_source').html()),
				select : function(event, ui){
					$('#import_author_schedule').val(ui.item.id);
				}
			});
			
			jQuery(".select-style select").each(function(){
				var title = jQuery(this).attr('title');		
				var arrow = "";
				if (jQuery(".select-style select").attr('arrow') !== undefined) 
					arrow = " " + jQuery(".select-style select").attr('arrow');

				if( jQuery('option:selected', this).val() != ''  ) title = jQuery('option:selected',this).text() + arrow ;
				jQuery(this)
					.css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
					.after('<span class="select">' + title + arrow + '</span>')
					.change(function(){
						val = jQuery('option:selected',this).text() + arrow;
						jQuery(this).next().text(val);
					});
			});
		},
		updateRecurrenceTime : function (event) {
			var currentTarget	=	$(event.currentTarget),
				block		=	this.blockUi,
				params = {
					url 	:'admin-ajax.php',
					type 	: 'post',
					data 	:	{ 
						time : currentTarget.val() ,
						action : 'rss-update-recurrent-time'
					},

					beforeSend : function(){
						block.block($('#recurrence'));
					},

					success : function(resp){
						$('#rss_error').remove();
						block.unblock();
					}
				};
			$.ajax(params);
		},

		/**
		 * update job limit day to remove jobs add from limit days ago
		*/
		updateJobLimitDate : function (e) {
			var $target	=	$(e.currentTarget);
			var BlockUI	=	new JobEngine.Views.BlockUi ();

			$.ajax ({
				type : 'post',
				url  : 'admin-ajax.php',
				data : {days : $target.val(), action : 'rss-update-job-limit-date' },
				beforeSend : function () {
					BlockUI.block ($target);
				},
				success : function () {
					BlockUI.finish ();
				}				
			});
		},

		deleteOldJobs : function (e) {
			e.preventDefault ();
			var $target	=	$(e.currentTarget),
				that	=	this,
				BlockUI	=	new JobEngine.Views.BlockUi();
			$.ajax ({
				type : 'post',
				url  : 'admin-ajax.php',
				data : { action : 'rss-delete-old-jobs'},
				beforeSend : function () {
					//BlockUI.block($target);
				}, 
				success : function (resp) {
					//BlockUI.finish();
					that.goToPage (1);
				}

			});

		}, 


		blockImportedJobs : function(){
			this.blockUi.block($('#ijobs'));
		},

		unblockImportedJobs : function(){
			this.blockUi.unblock();
		} 
	});

	new ET_RSS_Import ();
});

function fillDataToTableRss(data){
	var table = $('#import_search_result .import-tb-container > table');

	// clear old data
	table.find('tr:not(:eq(0))').remove();
	var template = _.template($('#import_row_template').html());

	$.each(data, function(index, job){
		job.i = index;
		table.find('tbody').append(template(job));
	});
}

function fillImportedRSSData(data){
	var table = $('#ijobs table');

	// clear old data
	table.find('tr:not(:eq(0))').remove();
	var template = _.template($('#imported_template').html());

	$.each(data, function(index, job){
		job.i = index;
		table.find('tbody').append(template(job));
	});
}

function fillPreviewRss(data){
	var table = $('.job-import-review > table');

	// clear old data
	table.find('tr:not(:eq(0))').remove();
	var template = _.template($('#et_schedule_preview').html());

	$.each(data, function(index, job){
		job.i = index;
		table.find('tbody').append(template(job));
	});
}

})(jQuery);