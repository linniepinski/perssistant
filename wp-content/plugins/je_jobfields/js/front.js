(function($){

	$(document).ready(function(){
		jobField.initDatepicker();
		var t = typeof $.validator ;
		if(typeof $.validator != 'undefined'){
			$.validator.addClassRules('input-url', {
		        url: true
		    });
		}
	});
	var jobFieldFile = {

	};
	var jobField = {

		initDatepicker : function(){
			this.dpPost();
			pubsub.on('je:job:modal_edit:afterSetupFields', this.dpEdit);
			pubsub.on('je:post:validate', this.postValidate);
			$( ".hasDatepicker" ).datepicker( $.datepicker.regional[ "fr" ] );
		},
		dpPost : function(){
			// init post job date picker
			var dateFields = $('#step_job .input-date input');
			dateFields.datepicker({
				dateFormat : jep_field.dateFormat,
				onClose: function(date, object){
					$(object.input).trigger('focusout');
				}
			});
			dateFields.datepicker('setDate', new Date());
		},
		dpEdit : function(model, $jobinfo){
			var inputFields = $('#modal_edit_job .input-date input');
			// fill all the fields value
			var fields = model.get('fields');

			_.each(fields, function(e, i, list){

				var field = $('#modal_edit_job .cfield-' + e.ID);
				var type = e.type
				if(e.value !== '' && e.value !== 0) {
					if(type == 'checkbox'){
						var arr = e.value;
						_.each(arr,function(key){
							var check_item = $('#modal_edit_job .checkbox-' + key);
							check_item.prop('checked', true);
						});

					} else if( type == 'radio'){
						$('#modal_edit_job .radio-' + e.ID).each(function(index,element){
							var val = $(this).val();
							if(val == e.value)
								$(this).prop("checked", true);
						});
						//field.prop('checked', true);
					}  else {
						field.val(e.value);
						// trigger change if field is select tag
						field.trigger('change');
					}
				}
			});
			var date_log = $(".input-date input").val();

			if(typeof date_log == 'undefined' || date_log == ''){
				var cur_date = $("input#current_date_field").val();
				$(".input-date input").attr('value',cur_date);
			}

			inputFields.datepicker({
				dateFormat : jep_field.dateFormat,
				onClose: function(date, object){
					$(object.input).trigger('change');
					$(object.input).trigger('focusout');
				}
			});

		},
		postValidate : function(){
			var fields 			= $('#modal_edit_job .input-field.input-required');
			var validateResult 	= true;

			_.each(fields, function(e, i, list){
				if (e.val() == ''){
					this.markError(e);
					validateResult = false;
				}
			});
			return false;
		},
		markError: function(e){
			var container = e.parent();
			container.addClass('error');
			container.append('<div for="title" generated="true" class="message" style="">this is is required</div><span class="icon" data-icon="!"></span>')
		}
	}

})(jQuery)