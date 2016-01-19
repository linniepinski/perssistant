(function ($) {
$(document).ready(function(){

	ET_SIMPLY_HIRED	= Backbone.View.extend({

		el : 'div#simply-hired',
		events : {
			'change select[name=apply_cat]' 			: 'massChangeCat',
			'change select[name=apply_type]' 			: 'massChangeType',
			'change input.setall' 						: 'massCheck',
			'click .et-menu-content li a' 				: 'changeMenu',
			'click  #save_setting'  					: 'updateSettings',
			'click .paginate a' 						: 'paginate',
			'click .et-menu-content li a' 				: 'changeMenu',
			'click #simplyhired_search'					: 'searchJobs',
			'submit form#import' 						: 'saveImportation'	,
			'click #delete_simplyhired'					: 'deleteSimplyhiredJobs',
			'change #simplyhired_options .option' 		: 'updateJobLimitDate',
			'click #simplyhired_options #delete_old_jobs': 'deleteOldJobs' ,
			// schedule
			'click #add-simplyhired-schedule'			: 'viewAddScheduleForm',
			'click #submit_schedule'					: 'updateSchedule',
			'click #schedule_list .delete'      		: 'deleteSchedule',
			'click #schedule_list .power'      			: 'OnOffSchedule',
			'click #schedule_list .edit'        		: 'editSchedule',
			'change #recurrence'						: 'updateRecurrenceTime'
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
			this.$('#search_author').autocomplete({
				source : JSON.parse($('#user_source').html()),
				select : function(event, ui){
					$('#import_author').val(ui.item.id);
				}
			});

			this.schedule_list	=	JSON.parse($('#schedule_data').html());
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
		/**
		 * update job limit day to remove jobs add from limit days ago
		*/
		updateJobLimitDate : function (e) {
			var $target	=	$(e.currentTarget);
			var BlockUI	=	new JobEngine.Views.BlockUi ();

			$.ajax ({
				type : 'post',
				url  : 'admin-ajax.php',
				data : {days : $target.val(), action : 'update-job-limit-date' },
				beforeSend : function () {
					BlockUI.block ($target);
				},
				success : function () {
					BlockUI.finish ();
				}				
			});
		},

		changeMenu : function(event){
			//console.log('change menu');
			event.preventDefault();
			var target = $(event.currentTarget),
				menu = target.attr('href');
			// 
			$('.et-menu-content li a').removeClass('active');
			target.addClass('active');
			// display content
			$('.setting-content .et-main-main').hide();
			$(menu).show();
			if(menu == '#simplyhired-manage') {
				this.goToPage(1);
			}
		},

		updateSettings : function (event) {
			event.preventDefault();
			var data	=	$('#simply_hired_setting form').serialize(),
				blockUI	=	new JobEngine.Views.BlockUi();
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
			
		},

		fillDataToTable : function (data) {
			var table = $('#import_search_result .import-tb-container > table');

			// clear old data
			table.find('tr:not(:eq(0))').remove();
			var template = _.template($('#simplyhired_row_template').html());

			$.each(data, function(index, job){
				job.i = index;
				table.find('tbody').append(template(job));
			});

		}
		,
		/**
		 * search job
		*/
		searchJobs : function (event) {
			var loadingButton = new JobEngine.Views.LoadingButton({el: '#simplyhired_search'});
			var view = this;

			$('#simplyhired_error').remove();
			$.ajax ({
				url  : 'admin-ajax.php',
				data : {
					action : 'simplyhired-search-job',
					data    : $('form#simplyhired_job').serialize ()
				},
				type : 'post',
				beforeSend : function () {
					loadingButton.loading();
				},
				success : function (response) {
					loadingButton.finish();
					if(!response.success) {
						$('#simplyhired_search').parent().append('<div id="simplyhired_error" class="error">'+response.msg+'</div>');
						$('#import_search_result').hide();
						$('.import-tb-container > table').find('body').html('');
					} else {						
						view.fillDataToTable(response.jobs);	
						view.styleSelector($('.import-tb-container > table'));
						$('#import_search_result').slideDown();
					}
				}
			});
		},

		saveImportation : function(e){
			e.preventDefault();
			var data = $('form#import').serialize(),
				loadingBtn = new JobEngine.Views.LoadingButton({el : $('form#import').find('button') });

			var params = {
				url : 'admin-ajax.php',
				type : 'post',
				data : data,
				
				beforeSend: function(){
					loadingBtn.loading();
				},
				success : function(resp){
					loadingBtn.finish();
					if(typeof resp.msg !== 'undefined')
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
			var BlockUi	=	new JobEngine.Views.BlockUi();
			var view = this,
				params = {
				url 	:'admin-ajax.php',
				type 	: 'post',
				data 	: {
					action : 'simplyhired-job-change-page',
					content : {
						page : page
					}
				},
				beforeSend : function(){
					BlockUi.block($('#ijobs'));
				},
				success : function(resp){
					// console.log(resp);
					BlockUi.finish();
					var html = '';
					if (resp.success) {
						// refresh imported job
						view.fillJobList(resp.data.jobs);
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
					$('.simplyhired-controls .paginate').html(html);
				}
			}
			return $.ajax(params)
		},

		deleteOldJobs : function (e) {
			e.preventDefault ();
			var $target	=	$(e.currentTarget),
				that	=	this,
				BlockUI	=	new JobEngine.Views.BlockUi();
			$.ajax ({
				type : 'post',
				url  : 'admin-ajax.php',
				data : {action : 'simplyhired-delete-old-jobs'},
				beforeSend : function () {
					//BlockUI.block($target);
				}, 
				success : function (resp) {
					//BlockUI.finish();
					that.goToPage (1);
				}

			});

		}, 

		deleteSimplyhiredJobs : function(event){
			event.preventDefault();
			// 
			if ($('#ijobs table td input:checked').length <= 0){
				return alert('Please choose at least one job');
			}

			// getting selected jobs
			var that	=	this;
			var paged 	= parseInt($.trim($('.simplyhired-controls .paginate > span.current').html()));
			var BlockUi	=	new JobEngine.Views.BlockUi();

			//console.log(paged);
			var ids = [];
			$('#ijobs table td input:checked').each(function(){
				ids.push($(this).val());
			});

			// generate ajax params
			var params = {
				url 	: 'admin-ajax.php',
				type 	: 'post',
				data 	: {
					action : 'simplyhired-delete-jobs',
					content : {
						ids : ids,
						page : paged,
						page_max : $('.paginate > *').length
					}
				},
				beforeSend: function(){
					$(event.currentTarget).addClass('disable');
					BlockUi.block($('#ijobs'));
				},
				success : function(resp){
					BlockUi.finish();
					if (resp.success){
						// unchecked the check box
						$('#ijobs table tr th input[type=checkbox]').removeAttr('checked');
						// refresh imported job
						that.fillJobList(resp.data.jobs);
						// refresh pagination
						if(paged > 1)
							that.goToPage(parseInt(paged -1));
						else 
							that.goToPage(1);
					}
				}
			};

			$.ajax(params);
		},

		updateSchedule : function (event) {
			event.preventDefault();

			var $currentTarget	=	$(event.currentTarget), 
				schedule_list	=	this.schedule_list,
				update			=	$('#schedule_form').find('.schedule_id').val(),
				schedule	=	$('#schedule_form').serialize(),
				view		=	this,
				block		=	new JobEngine.Views.BlockUi();
				params = {
					url 	:'admin-ajax.php',
					type 	: 'post',
					data 	:	schedule ,

					beforeSend : function(){
						block.block($('#submit_schedule'));
					},

					success : function(resp){
						$('#schedule_error').remove();
						block.unblock();
						if(!resp.success) {
							var error_msg = '', i = 0;
							for(i = 0 ; i < resp.msg.length ; i++)
								error_msg	+=	resp.msg[i]	;
							$currentTarget.parents('.form-button').append('<div id="schedule_error" class="error">'+error_msg+'</div>');

						} else {
							var template	=	_.template($('#et_schedule_list').html()),
								data		=	resp.data;

							// view.styleSelector($('.job-import-review > table'));
							//$('.job-import-review').slideDown();

							schedule_list[resp.data.schedule_id]	=	data;
							$('#form-schedule').fadeOut(500);
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

		},

		deleteSchedule : function (event) {
			event.preventDefault();
			var $currentTarget	=	$(event.currentTarget),
				block		=	new JobEngine.Views.BlockUi(),
				schedule	=	$currentTarget.parents('td').find('.schedule_id').val(),
				params = {
				url 	:'admin-ajax.php',
				type 	: 'post',
				data 	:	{ action : 'simplyhired-detele-schedule', schedule_id : schedule},

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

		OnOffSchedule : function (event) {
			event.preventDefault();
			var $currentTarget	=	$(event.currentTarget);
			var block		=	new JobEngine.Views.BlockUi(),
				schedule	=	$currentTarget.parents('td').find('.schedule_id').val(),
				params = {
				url 	: 	'admin-ajax.php',
				type 	: 	'post',
				data 	:	{action : 'simplyhired-off-schedule', schedule_id : schedule},

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

		editSchedule : function (event) {
			event.preventDefault();
			var currentTarget	=	$(event.currentTarget),
				schedule_id		=	currentTarget.parents('td').find('.schedule_id').val(),
				data			=	this.schedule_list[schedule_id];
			this.newScheduleForm(currentTarget.parents('.module'), data );
		},

		viewAddScheduleForm : function (event) {
			event.preventDefault();
			var $currentTarget	=	$(event.currentTarget),
				parent			=	$currentTarget.parents('.module'),
				data			=	{
										'q' 			: '', 
										'lz'			: '', 
										'ls' 			: '', 
										'lc' 			: '', 
										'ws' 			: '',
										'fjt'			: '', 

										'schedule_id' 	: '',
										'author'		: '',
										'import_author' : '',
										'ON'			: 1 ,
										'job_category'	: '',
										'job_type'		: ''
										
									};
			this.newScheduleForm( parent, data );
		},

		newScheduleForm : function ( parent , data) {
			$('#form-schedule').remove ();

			var template	=	_.template( $('#schedule_template').html() );

			parent.append(template(data));
			
			$("#job_category option[value="+data.job_category+"]").prop("selected", true);
			$("#job_type option[value="+data.job_type+"]").prop("selected", true);
			$("#schedule_fjt option[value="+data.fjt+"]").prop("selected", true);

			$('.job-import-review').hide();
			$('#schedule_jobtitle').focus();
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
				block		=	new JobEngine.Views.BlockUi(),
				params = {
					url 	:'admin-ajax.php',
					type 	: 'post',
					data 	:	{ 
						time : currentTarget.val() ,
						action : 'simplyhired-update-recurrent-time'
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

		fillJobList : function (data) {

			var table = $('#ijobs');

			// clear old data
			table.find('tr:not(:eq(0))').remove();
			var template = _.template($('#imported_template').html());

			$.each(data, function(index, job){
				job.i = index;
				table.find('tbody').append(template(job));
			});
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
			var value 			= $(e.currentTarget).val();
			var table 			= $('.import-tb-container > table');
			var affected_rows 	= _.map( table.find('tr:not(:eq(0)) input.allow:checked'), function(element){
				return $(element).closest('tr').find('select.job_type');
			});

			_.each(affected_rows, function(data, index){
				$(data).val(value);
				$(data).trigger('change');
			});
		},

	} );


	view = new ET_SIMPLY_HIRED ();
	
});

})(jQuery);


