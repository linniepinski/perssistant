(function($) {
	var g_tels = [];
	$(function() {
		var wpcf7_form = $('div.wpcf7 > form');
		wpcf7_form.find('input.wpcf7-tel')
			.intlTelInput()
			.each(function(tel) {
				$this = $(this);
				$this.attr('data-check', 1);
			});

		wpcf7_form.bind('form-submit-validate', null, onValidate);

		wpcf7_form.find('input.wpcf7-tel').change(function() {
			$(this).attr('data-check', 1);
		});

		function onValidate(e, a, f, o, veto) {
			g_tels = [];
			var mobiles = "";
			wpcf7_form.find('input.wpcf7-tel').each(function() {
				var tel = $(this);
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

			if (mobiles != "")
			{
				$.ajax({
						url: wpcf7_form.attr('action'),
						type: 'POST',
						data: {
							cf7_check_cognayls: 1,
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
								wrap = tel.parents('span.wpcf7-form-control-wrap');
								wrap.wpcf7NotValidTip(error_message);
								wrap.find('.wpcf7-form-control').addClass('wpcf7-not-valid');
								wrap.find('[aria-invalid]').attr('aria-invalid', 'true');

								wpcf7_form.find('img.ajax-loader').css({ visibility: 'hidden' });
							}
						}

						if (otp_starts != "")
						{
							var dialog = $('<div id="dialog-form" title="Enter OTP">' +
								'<p class="validateTips">Enter the last five digit of missed call you recieved from Cognalys</p>' +
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
												i = $(this).attr('data-index');
												tels[tels.length] = g_tels[i];
												otps[otps.length] = $(this).val();
											});
											check2(dialog, tels, otps);
										},
										Cancel: function() {
											dialog.dialog( "close" );
											dialog.find('.otp').each(function() {
												i = $(this).attr('data-index');
												g_tels[i].attr("data-check", 1);
											});

											wpcf7_form.find('img.ajax-loader').css({ visibility: 'hidden' });
										}
									},
									close: function() {
										wpcf7_form.find('img.ajax-loader').css({ visibility: 'hidden' });
									}
								});
						}

						
					});
				veto.veto = 1;
			}
		}

		function check2(dialog, tels, otps) {
			keymatch = "";
			otp = "";
			for (i = 0; i < tels.length; i ++)
			{
				if (keymatch != "") keymatch += ",";
				keymatch += tels[i].attr('data-keymatch');
				if (otp != "") otp += ",";
				otp += otps[i];
			}

			$.ajax({
				url: wpcf7_form.attr('action'),
				type: 'POST',
				data: {
					cf7_check_cognayls: 2,
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
					}
					else {
						tel.attr("data-check", 1);
						error_message = "OTP is Wrong";
						wrap = tel.parents('span.wpcf7-form-control-wrap');
						wrap.wpcf7NotValidTip(error_message);
						wrap.find('.wpcf7-form-control').addClass('wpcf7-not-valid');
						wrap.find('[aria-invalid]').attr('aria-invalid', 'true');

						wpcf7_form.find('img.ajax-loader').css({ visibility: 'hidden' });
					}
				}

				if (wpcf7_form.find('input.wpcf7-tel[data-check=1]').length == 0)
				{
					wpcf7_form.wpcf7ClearResponseOutput();
					wpcf7_form.submit();
				}

				dialog.dialog( "close" );
			});
		}
	});

})(jQuery);