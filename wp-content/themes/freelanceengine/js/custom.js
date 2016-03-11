/**
 * This function is used for login
 * @param	action
 * @param	do
 * @param	read
 * @param	user_login
 * @param	user_pass
 */
var dologinuser = function () { 
	var action = 'ae-sync-user';
	var doaction = 'login';
	var method = 'read';
	var user_login = jQuery.trim(jQuery("#login_user_login").val());
	var user_pass = jQuery.trim(jQuery("#login_user_pass").val());
	var form = jQuery("#user_signin_form");

	jQuery.ajax({
		type : "post",
        dataType : "json",
        url : myAjax.ajaxurl,
        data : {action: action, do : doaction, method: method, user_login : user_login, user_pass : user_pass},
		beforeSend: function() {
			form.addClass('processing');			
		},
        success: function(status) { 
            form.removeClass('processing');
			if(status.success){
				AE.pubsub.trigger('ae:notification', {
					msg : status.msg,
					notice_type: 'success'
				});

				form.trigger('reset');
				window.location.href='/';

			} else {
				AE.pubsub.trigger('ae:notification', {
					msg : status.msg,
					notice_type: 'error'
				});
			}
        }
	});
}

var saveinterview = function() {
    var action = 'fetch-interview';
   //var doaction = 'save';
    var form = jQuery("form.interview_form");
    var post_id = jQuery('#post_id').val();
    var date1 = jQuery('#date_interview_1').val();
    var date2 = jQuery('#date_interview_2').val();
    var date3 = jQuery('#date_interview_3').val();
    var interview_tel = jQuery('#interview_tel').val();
    var interview_skype = jQuery('#interview_skype').val();
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : myAjax.ajaxurl,
        data : {
            ID : post_id,
            action: action,
           // doaction : doaction,
            date_interview_1 :date1,
            date_interview_2 :date2,
            date_interview_3 :date3,
            skype_id :interview_skype,
            tel :interview_tel
        },
        beforeSend: function() {
            form.addClass('processing');
        },
        success: function(status) {
            form.removeClass('processing');

            if(status.success){
                AE.pubsub.trigger('ae:notification', {
                    msg : status.msg,
                    notice_type: 'success'
                });

            } else {
                AE.pubsub.trigger('ae:notification', {
                    msg : status.msg,
                    notice_type: 'error'
                });
            }
        }
    });
};

var doSendPassword = function() {
	var action = 'ae-sync-user';
	var doaction = 'forgot';
	var method = 'read';
	var user_login = jQuery.trim(jQuery("#user_email").val());
	var form = jQuery("form.forgot_form");
	 var cptch_result = jQuery("input[name=cptch_result]").val();
    var cptch_time = jQuery("input[name=cptch_time]").val();
    var cptch_number = jQuery("#cptch_input").val();

	jQuery.ajax({
		type : "post",
        dataType : "json",
        url : myAjax.ajaxurl,
        data : {action: action, do : doaction, method: method, user_login : user_login,cptch_number:cptch_number,cptch_time:cptch_time,cptch_result:cptch_result},
		beforeSend: function() {
			form.addClass('processing');
		},
        success: function(status) { 
            form.removeClass('processing');
			
			if(status.success){
				AE.pubsub.trigger('ae:notification', {
					msg : status.msg,
					notice_type: 'success'
				});

				form.trigger('reset');
				
				window.location.href='/login';

			} else {
				AE.pubsub.trigger('ae:notification', {
					msg : status.msg,
					notice_type: 'error'
				});
			}
        }
	});
};

var doUserRegister = function() {
	var action = 'ae-sync-user';
	var doaction = 'register';
	var method = 'create';
	var role 		= jQuery.trim(jQuery("#role").val());
	var repeat_pass = jQuery.trim(jQuery("#repeat_pass").val());
	var user_email 	= jQuery.trim(jQuery("#register_user_email").val());
	var user_login 	= jQuery.trim(jQuery("#user_login").val());
	var user_pass 	= jQuery.trim(jQuery("#register_user_pass").val());
	var user_login  = jQuery.trim(jQuery("#user_login").val());
	var form 		= jQuery("#user_signup_form");
	
	jQuery.ajax({
		type : "post",
        dataType : "json",
        url : myAjax.ajaxurl,
        data : {
					action	: action, 
					do 		: doaction, 
					method	: method, 
					role 	: role, 
					repeat_pass : repeat_pass,
					user_email : user_email,
					user_login : user_login,
					user_pass : user_pass
				},
		beforeSend: function() {
			form.addClass('processing');
		},
        success: function(status) { 
            form.removeClass('processing');
			
			if(status.success){
				AE.pubsub.trigger('ae:notification', {
					msg : status.msg,
					notice_type: 'success'
				});

				form.trigger('reset');
				
				window.location.href='/login';

			} else {
				AE.pubsub.trigger('ae:notification', {
					msg : status.msg,
					notice_type: 'error'
				});
			}
        }
	});
};

jQuery(document).ready(function(){ 
	//Validate login form
	jQuery("#user_signin_form").validate({
		rules: {
			user_login: "required",
			user_pass: "required"
		},
		submitHandler: function(form){
			dologinuser();
		}

	});

	//Validate forgot password
	jQuery("form.forgot_form").validate({
		rules: {
			user_email: {
				required: true,
				email: true
			}
		},
		submitHandler: function(form){
			doSendPassword();
		}
	});

    jQuery("form.interview_form").validate({
        rules: {
            interview_tel: {
                number: true
            }
        },
        submitHandler: function(form){
            var count_dates = jQuery('input[name=date-interview]').filter(function() { return jQuery(this).val().trim() == ""; }).length;
            var count_contacts = 0;
            if (jQuery('#interview_skype').val().trim() == ''){count_contacts += 1;}
            if (jQuery('#interview_tel').val().trim() == ''){count_contacts += 1;}
            if (count_dates < 3 && count_contacts < 2 ){
                saveinterview();
            }else{
                if(count_dates == 3){
                    AE.pubsub.trigger('ae:notification', {
                        msg : 'Fill some dates',
                        notice_type: 'error'
                    });
                }
                if(count_contacts == 2){
                    AE.pubsub.trigger('ae:notification', {
                        msg : 'Fill some contacts',
                        notice_type: 'error'
                    });
                }
            }
        }
    });
	
	var clickCheckbox = document.querySelector('.sign-up-switch'), roleInput = jQuery("input#role");
	var hide_text = jQuery('.hide-text').val();
	var work_text = jQuery('.work-text').val();
	//alert(jQuery('.user-type span.text').text());

    if( jQuery('.sign-up-switch').length > 0 ){

        if(clickCheckbox.checked){
            roleInput.val("employer");
            jQuery('.user-type span.text').text(hide_text).removeClass('work').addClass('hire');
        } else {
            roleInput.val("freelancer");
            jQuery('.user-type span.text').text(work_text).removeClass('hire').addClass('work');
        }

        clickCheckbox.onchange = function() {
			if(clickCheckbox.checked){
				roleInput.val("employer");
				jQuery('.user-type span.text').text(hide_text).removeClass('work').addClass('hire');
			} else {
				roleInput.val("freelancer");
				jQuery('.user-type span.text').text(work_text).removeClass('hire').addClass('work');
			} 
		};

		//var moveIt = jQuery(".user-role").remove();

		//jQuery(".switchery").append(moveIt);
	}

    //Validate signup form
    jQuery('#register_user_pass , #new_password').keyup(function () {
        var pswd = jQuery(this).val();
        if (pswd.length < 8) {
            jQuery('#length').removeClass('valid').removeClass('required').addClass('invalid');
        } else {
            jQuery('#length').removeClass('invalid').addClass('valid').addClass('required');
        }
        //validate letter
        if (pswd.match(/[A-z]/)) {
            jQuery('#letter').removeClass('invalid').addClass('valid');
        } else {
            jQuery('#letter').removeClass('valid').addClass('invalid');
        }

//validate capital letter
        if (pswd.match(/[A-Z]/)) {
            jQuery('#capital').removeClass('invalid').addClass('valid');
        } else {
            jQuery('#capital').removeClass('valid').addClass('invalid');
        }

//validate number
        if (pswd.match(/\d/)) {
            jQuery('#number').removeClass('invalid').addClass('valid');
        } else {
            jQuery('#number').removeClass('valid').addClass('invalid');
        }
        var count_valid = jQuery('#pswd_info li.valid').length;
        if (count_valid && jQuery('#pswd_info li.required').length >= 1) {
            if (count_valid >= 2) {
                jQuery('.strong-level').text('minimum')
            }
            if (count_valid >= 3) {
                jQuery('.strong-level').text('medium')
            }
            if (count_valid >= 4) {
                jQuery('.strong-level').text('strong')
            }
        } else {
            jQuery('.strong-level').text('danger')
        }
    }).focus(function () {
        jQuery('#pswd_info').fadeIn('slow');
    }).blur(function () {
        jQuery('#pswd_info').fadeOut('fast');
    });

	jQuery("#user_signup_form").validate({
		rules: {
			user_login: "required",
			user_pass: {
                required: true
            },
			user_email: {
				required: true,
				email: true
			},
			repeat_pass: {
				required: true,
				equalTo: "#repeat_pass"
			}
		},
		submitHandler: function(form){
            if (jQuery('#pswd_info li.valid').length >= 2 && jQuery('#pswd_info li.required').length >= 1 ){
                doUserRegister();
            }else{
                jQuery('#pswd_info').fadeIn('slow');
                return false;
            }

        }
	});
    jQuery('.sw_skill').on('change',function(){
       console.log('changed')
        parent = jQuery(this).parent().find('.error');
        if(jQuery('.sw_skill > option:selected').length < 10){
            console.log('change2d');
            jQuery('.skill-error').html('');
        }else{
            console.log('cha2nge2d');
            jQuery('.skill-error').html('<span class="message"> You\'ve added maximum number of skills</span>');
        }
    });
});

(function($) {
    var g_tels = [];
    jQuery(function() { 
		var mobile_form = jQuery('form#verify_user_phone');    
       
		mobile_form.find('input[name="user_mobile"]')
				.intlTelInput()
				.each(function(tel) {
						$this = jQuery(this);
						$this.attr('data-check', 1);
				});

		//mobile_form.bind('form-submit-validate', null, onValidate);

		mobile_form.find('input.user_mobile').change(function() {
				jQuery(this).attr('data-check', 1);
		});		
    });

})(jQuery);

function validatenumber() {
	g_tels = [];
	var mobiles = "";
	var mobile_form = jQuery('form#verify_user_phone');
	
	mobile_form.find('input[name="user_mobile"]').each(function() {
		var tel = jQuery(this);
		var mobile_number = tel.val();		
		var check = tel.attr('data-check');
		
		if (check != 1)
			return;

		if (mobile_number != "")
		{
			if (mobiles != "") mobiles += ",";
			mobiles += mobile_number;
			g_tels[g_tels.length] = tel;
		}
	});	
	
	if (mobiles != "") {
		jQuery.ajax({
			url: myAjax.ajaxurl,
			type: 'POST',
			data: {
				action: 'verify_user_phone',
				check_cognayls: 1,
				mobile: mobiles
			},
			dataType: 'json'
		}).done(function(rets) {
			var otp_starts = "";
			
			for (i = 0; i < rets.length; i ++)
			{
				ret = rets[i];
				var tel = g_tels[i];

				if (ret.status == "success")
				{
					tel.attr("data-check", 2);
					tel.attr("data-keymatch", ret.keymatch);
					tel.attr("data-otp_start", ret.otp_start);
					otp_starts += '<tr><td style="border:none">' + ret.mobile + ' : </td><td style="border:none">' + 
						'<input type="text" name="otp" value="' + ret.otp_start + '" data-index="' + i + 
						'" class="text ui-widget-content ui-corner-all otp"></td></tr>';
				}
				else {
					error_message = "Mobile number is not valid";							
				}
				
				if (otp_starts != "")
				{
					var dialog = jQuery('<div id="dialog-form" title="Enter OTP">' +
						'<p class="validateTips">Enter the last five digit of missed call you recieved</p>' +
						'<table style="border:none;">' +
						otp_starts +
						'</table></div>');
					dialog.dialog({ 
							height: 300,
							width: 400,
							modal: true,
							buttons: {
								"OK": function() {
									var tels = [], otps = [];
									dialog.find('.otp').each(function() {
										i = jQuery(this).attr('data-index');
										tels[tels.length] = g_tels[i];
										otps[otps.length] = jQuery(this).val();
									});
									verifyotp(dialog, tels, otps, mobiles);
								},
								Cancel: function() {
									dialog.dialog( "close" );
									dialog.find('.otp').each(function() {
										i = jQuery(this).attr('data-index');
										g_tels[i].attr("data-check", 1);
									});
								}
							},
							close: function() {
							
							}
						});
				}						
			}
		
		});
		//veto.veto = 1;		
	}
}

function verifyotp(dialog, tels, otps, mobiles)
{
	keymatch = "";
	otp = "";
	for (i = 0; i < tels.length; i ++)
	{
		if (keymatch != "") keymatch += ",";
		keymatch += tels[i].attr('data-keymatch');
		if (otp != "") otp += ",";
		otp += otps[i];
	}
	
	jQuery.ajax({
		url: myAjax.ajaxurl,
		type: 'POST',
		data: {
			action: 'verify_user_otp',
			check_cognayls: 2,
			keymatch: keymatch,
			otp: otp
		},
		dataType: 'json'
	}).done(function(rets) {
		for (i = 0; i < rets.length; i ++)
		{
			ret = rets[i];
			tel = tels[i];
			if (ret.status == "success")
			{
				tel.attr("data-check", 3);
				
				jQuery('#verify').hide();
				jQuery('#user_verified_mobile').text(mobiles);
				jQuery('#verified').show();
				jQuery('form#verify_user_phone').trigger('reset');
				
				dialog.dialog( "close" );
				
				AE.pubsub.trigger('ae:notification', {
					msg : 'Your Phone number had been verified.',
					notice_type: 'success'
				});
				
				update_user_phone(mobiles);
			}
			else {
				tel.attr("data-check", 1);
				dialog.dialog( "close" );
				AE.pubsub.trigger('ae:notification', {
					msg : 'OTP is Wrong',
					notice_type: 'error'
				});				
			}
		}
	});
}

var editnumber = function () {
	jQuery('#verified').hide();
	jQuery('#verify').show();
}

var update_user_phone = function(mobile) {
	jQuery.ajax({
		url: myAjax.ajaxurl,
		type: 'POST',
		data: {
			action: 'update_user_mobile',
			mobile: mobile
		},
		//dataType: 'json',
		success: function(response) {
		
		}
		
	});
}

jQuery(document).ready(function () {
    var div_for_type_budget = jQuery('.for_type_budget');
    jQuery('select[name="type_budget"]').change(function () {

        console.log(jQuery(this).val());
        if ( jQuery(this).val() == 'hourly_rate'){
            jQuery('#hours_limit').removeAttr('disabled');
            div_for_type_budget.fadeIn();
        }else{
            jQuery('#hours_limit').attr('disabled','disabled');
            div_for_type_budget.fadeOut();

        }
    });
	jQuery('input[type="number"]').keypress(function (e) {
		//if the letter is not digit then display error and don't type anything
		console.log(e.which)
		if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46) {
			return false;
		}
	});
});

