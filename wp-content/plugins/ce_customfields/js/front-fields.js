(function($) {  
$(document).ready(function(){
	var option  = {};
		option["d"] = 'dd'; // two digi date
		option["j"] = 'd';
		option["m"] = 'mm';
		option["n"] = 'm';
		option["l"] = 'DD';
		option["D"] = 'D';
		option["F"] = 'MM';
		option["M"] = 'M';
		option["Y"] = 'yy';
		option["y"] = 'y';
	for(var i in option)
		date_format=date_format.replace(i,option[i]);

	var n = $( ".ce_field_datepicker" ).length;

	if(n >0){

		$( ".ce_field_datepicker" ).datepicker({
			dateFormat: date_format
		});
		if($(".ce_field_datepicker").val() == ''){
			$(".ce_field_datepicker").val(jQuery('#field_current_date').val());
		}
	}
});
})(jQuery);
