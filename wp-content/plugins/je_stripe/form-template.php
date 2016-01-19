<?php
	$general_opts	= new ET_GeneralOptions();
	$website_logo	= $general_opts->get_website_logo();
?>
<div class="modal-job modal modal-login modal-stripe" id="stripe_modal">
	<div class="edit-job-inner">
		<?php if( !function_exists('et_is_mobile') || !et_is_mobile() ) { ?>
		<div class="stripe-header bg-main-header">
			<div class="logo"><img src="<?php echo $website_logo[0];?>" height="50" /></div>
			<div class="info slogan">
      			<span class="plan_name">{$plan_name}</span> <br/>
      			<span class="plan_desc">{$plan_description}</span>
    		</div>
		</div>
		<?php }?>
		<form class="modal-form" id="stripe_form" novalidate="novalidate" autocomplete="on">
			<div class="content clearfix">
				<div class="form-item">
					<div class="label">
						<?php _e('Card number:', ET_DOMAIN);?>
					</div>
					<div class="fld-wrap" id="">
						<input tabindex="20" id="stripe_number" type="text" size="20" data-stripe="number" class="bg-default-input not_empty" placeholder="&#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226;" />
					</div>
				</div>
				<div class="form-item form-item-right">
				  	<div class="label">
						<?php _e('Expiry date:', ET_DOMAIN);?>
				  	</div>
				 	<div class="fld-wrap stripe_date" id="">
					 	<input tabindex="21" type="text" size="2" data-stripe="exp-month" placeholder="MM" class="bg-default-input not_empty" id="exp_month"/>
				      	<span> / </span>
				      	<input tabindex="22" type="text" size="4" data-stripe="exp-year" placeholder="YY" class="bg-default-input not_empty" id="exp_year"/>
				 	</div>
				</div>

				<div class="form-item">
				  	<div class="label">
						<?php _e('Name on card:',ET_DOMAIN);?>
				  	</div>
				  	<div class="fld-wrap" id="">
						<input tabindex="23" name="" data-stripe="name" class="bg-default-input not_empty" type="text" />
				 	</div>
				</div>

				<div class="form-item form-item-right">
				  	<div class="label">
						<?php _e('Card code:', ET_DOMAIN);?>
				  	</div>
				 	<div class="fld-wrap" id="">
						<input tabindex="24" type="text" size="3" data-stripe="cvc" class="bg-default-input not_empty" placeholder="CVC" id="cvc" />
				  	</div>
				</div>
			</div>
			<div class="footer font-quicksand">
				<div class="button">
					<input type="submit" class="bg-btn-action border-radius" value="<?php _e('PAY THROUGH STRIPE',ET_DOMAIN);?>" id="submit_stripe">
				</div>
			</div>
		</form>
	</div>
	<div class="modal-close"></div>
</div>