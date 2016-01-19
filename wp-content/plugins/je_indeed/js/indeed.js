(function ($) {

	$(document).ready(function () {
		JobEngine.Views.Indeed	=	Backbone.View.extend({
			events : {
				'click .out' 							: 'loadMore',
				'keyup input.search-box'				: 'searchJob',
				'change .header-filter select, input'	: 'searchJob',
				'click ul.filter-joblist li a:not(".et_processing")' : 'filterHandler'
			},

			initialize : function () {

				this.paged	=	0;
				if( this.$('#latest_jobs_container').find('li').length < parseInt(indeed.post_per_page) ) {
					this.getPost();
				}
				pubsub.on('je:afterLoadJob' , this.indeedLoad , this );

			},

			searchJob : function () {
				this.paged = 0;
				$('.button-more').removeClass('out');
			},

			filterHandler : function () {
				this.paged = 0;
				$('.button-more').removeClass('out');
			},

			buildParams : function(){
				var params	= {},
					$activeLocation = 	$('ul#location_filter a.active'),
					$filterInput	= 	$('#header-filter').find('input,select');

				var $joblist_filter	=	$('ul.filter-joblist');

				_.each (this.$('.widget > ul.job-filter'), function (tax) {
					var etax		=	$(tax).attr('data-tax');

					if(etax) {
						var $activeTax 	=	 $(tax).find('a.active');
						var arg	=	$.map( $activeTax, function(item){
							return $(item).attr('data-slug');
						});
						arg	= arg.join(',');
						if(arg != '')
						params[etax] = arg;
					}
				});

				$.each( $filterInput, function(){
					var $this = jQuery(this),
						inputName	= $this.attr('name'),
						placeholder	= $this.attr('placeholder'),
						inputVal	= $this.val();

					if( inputVal !== '' && inputVal !== placeholder ){
						if( inputName === 'job_location' ){
							params.location	= inputVal;
						}
						else{
							params[inputName]	= inputVal;
						}
					}
				});

				return params;
			},

			indeedLoad : function (res, data) {
				if( !data.found_jobs ) {
					this.getPost();
					$('.button-more').addClass('out');
				}
			},

			getPost : function () {
				var view 	= this,
					params	= this.buildParams ();
				view.paged++;
				$('.no-job-found').hide();
				$.ajax ({
					url : et_globals.ajaxURL,
					type : 'get',
					data : { action : 'je_after_job_list' , content : params , paged : view.paged },
					beforSend : function () {
						$('.button-more').hide();
					},
					success : function (res) {
						if( typeof res.data.result !== 'undefined' && res.data.result.length > 0) {

							_.templateSettings = {
							    evaluate    : /<#([\s\S]+?)#>/g,
								interpolate : /\{\{(.+?)\}\}/g,
								escape      : /<%-([\s\S]+?)%>/g
							};
							var result		=	res.data.result,
								template	=	_.template( $('#indeed_template').html() );
							for ( var i=0 ; i < result.length ; i ++ ) {
								if(typeof result[i].company == 'object')
									result[i].company = '';
								var item	=	template(result[i]);
								$('.lastest-jobs').append (item);
							}

							if(view.paged == res.total) $('.button-more').removeClass('out');
							//if( !parseInt(res.total) )  $('.no-job-found').removeClass('hide-nojob');
							setTimeout(function(){
								$('.button-more').show().addClass('out').removeClass('et_processing');
							}, 50);
						}else {
							$('.button-more').removeClass('out');
							$('.no-job-found').show();
						}

					}
				}); 

				$('.button-more').removeClass('et_processing');

			},

			loadMore : function (event) {
				this.getPost();
			}

		});

		new JobEngine.Views.Indeed ({el : $('.wrapper')});

	});
})(jQuery);