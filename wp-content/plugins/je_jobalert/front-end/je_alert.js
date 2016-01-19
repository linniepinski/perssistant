(function ($) {
$(document).ready(function(){
	 var config = {
		      '.chosen-select'           : {},
		      '.chosen-select-deselect'  : {allow_single_deselect:true},
		      '.chosen-select-no-single' : {disable_search_threshold:10},
		      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
		      '.chosen-select-width'     : {width:"95%"},
		      'search_contains'			 :true,	
		    }
		    for (var selector in config) {
		      $(selector).chosen(config[selector]);
		    }
	
	ET_JE_Alert	= Backbone.View.extend({
		el : 'div.widget-job-alert',
		events : {
			'click .widget-job-alert .btn-subscribe' : 'subscribe',
			'click  a#unsubscribe_btn' : 'unsubscribe',
		},

		initialize : function() {
			$('.widget-job-alert form').validate({
				rules : { 
					email :{
					      required: true,
					      email: true
					    } 
				}
			});
		},
		unsubscribe: function(e){
			e.preventDefault();
			var blockUi	=	new JobEngine.Views.BlockUi(),
				data	=	$('.unsubscribe form#unsubscribe_form').serialize(),
				email 	= $('.unsubscribe form#unsubscribe_form').find('input#email').val(),
				code 	= $('.unsubscribe form#unsubscribe_form').find('input#code').val(),
				$target	=	$(e.currentTarget);
				$.ajax ({
					type : 'post',
					url : et_globals.ajaxURL,
					data : {
						action: 'je-remove-subscriber',
						email: email,
						code: code
					},
					beforeSend : function () {
						blockUi.block($target);
					},
					success : function (res) {
						blockUi.finish();
						if(res.success) {
							pubsub.trigger('je:notification',{
								msg	: res.msg,
								notice_type	: 'success'
							});
						} else {
							pubsub.trigger('je:notification',{
								msg	: res.msg,
								notice_type	: 'error'
							});
						}
						console.log (res);
					}			
				});						
		},
		subscribe : function (e) {
			e.preventDefault();
			var blockUi	=	new JobEngine.Views.BlockUi(),
				$target	=	$(e.currentTarget),
				data	=	$('.widget-job-alert form').serialize();
			   var value = $(".chosen-select").val();
			  
			   if(typeof value =='undefined' || typeof value =='' ){
				   value ='';
			   }
			   data =data +'&job_category='+value; 
			if($('.widget-job-alert form').valid()) {
				$.ajax ({
					type : 'post',
					url : et_globals.ajaxURL,
					data : data,
					beforeSend : function () {
						blockUi.block($target);
					},
					success : function (res) {
						blockUi.finish();
						if(res.success) {
							pubsub.trigger('je:notification',{
								msg	: res.msg,
								notice_type	: 'success'
							});
						} else {
							pubsub.trigger('je:notification',{
								msg	: res.msg,
								notice_type	: 'error'
							});
						}
						console.log (res);
					}			
				});
			}
		}

	});
	new ET_JE_Alert();
});
}) (jQuery);