(function($) {

	$(document).on('pageinit' , function () {
			var current_format = date_format, option  = {};
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
				current_format = current_format.replace(i,option[i]);

			$(".je_field_datepicker ").val($("#field_current_date").val());
			$(".je_field_datepicker").datepicker({
				dateFormat: current_format
			});

		var clickDel = function(event){
			var target 	= $(event.currentTarget),
				view 	= this,
				loading = '<img class="deleting" src="'+et_globals.imgURL+'/loading.gif">';

			var params = {
				url : et_globals.ajaxURL,
				type: 'post',
				data: {
					action: 'resume_romove_attachment',
					content: {
						id 	: target.attr('id'),
						resume_id : target.attr('rel'),
					}
				},
				beforeSend: function(){
					//view.loading.block(target.closest(".file-item"));

					target.closest(".field-upload").append(loading);
				},
				success: function(resp){
					//view.loading.unblock();
					target.closest(".field-upload").remove();

				}
			}
			$.ajax(params);

		}
		$(".delete a").on("click", clickDel);

		// init upload file jquery
		if($(".container-field-file").length > 0){
	    	$( ".fileupload" ).each(function( index ,element) {

	    		var target 			= $(element).closest(".container"),
	    			filediv 		= target.find("#files"),
	    			field_name 		= target.find("input.field_name").val(),
	    			field_id 		= target.find("input.field_id").val(),
	    			field_type 		= target.find("input.field_type").val(),
	    			progress_bar	= target.find(".progress-bar"),
	    			resume_id 		= $("input#resume_id").val(),
	    			file_type 		= target.hasClass('container-image') ? '/(\.|\/)(gif|jpe?g|png)$/i' : '/(\.|\/)(gif|jpe?g|png|pdf|doc|docx|rar|zip)$/i',
	    			max_size 		= 4 * 1024 * 1024;

			    $(element).fileupload({
			    	maxFileSize: max_size,
		            acceptFileTypes: file_type,
			        url: et_globals.ajaxURL+'?action=mobile_upload&resume_id=' + resume_id + '&field_name=' + field_name +'&field_type='+field_type + '&field_id=' + field_id ,
			        dataType: 'json',
			        type: "POST",
			        messave : {
			        	maxFileSize: 'File exceeds maximum allowed size of 4MB',
			        },

			        add : function (e,data){
			        	$.each(data.files, function (index, file) {
		        			if(file.size > max_size){
		        				alert('File exceeds maximum allowed size of 4MB');
		        				return false;
		        			} else{
		        				data.submit();
		        			}
			        	});
			        },

			        done: function (e, data) {
			        	var append  = target.find("div.files");
			        	if(data.result.success){
			        		$.each(data.result.files, function (index, file) {
					        	if(data.result.temp=='image'){
									append.append("<span class='field-upload'><img src='"+ file.thumbnail[0]+"' /><span class='delete'><a data-icon='#' rel='"+resume_id+"' id='"+file.attach_id+".' class='del-thumbnail ui-link'> x </a></span></span>");

					        	} else {
					        		append.append("<span class='field-upload'>"+file.name+"<span class='delete'><a data-icon='#' rel='"+resume_id+"' id='"+file.attach_id+".' class='del-thumbnail ui-link'> x </a></span></span>");
					        	}
					        	$(".delete a").bind("click", clickDel);
			        		});

			        	} else {
			        		alert(data.result.msg)
			        	}
			        },
			        progressall: function (e, data) {
			            var progress = parseInt(data.loaded / data.total * 100, 10);
			           progress_bar.css(
			                'width',
			                progress + '%'
			            );
			        }
			    }).prop('disabled', !$.support.fileInput)
			        .parent().addClass($.support.fileInput ? undefined : 'disabled');
			});
		}

	});
})(jQuery);