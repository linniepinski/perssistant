<?php 
	$general_opts	= new ET_GeneralOptions();
	$website_logo	= $general_opts->get_website_logo();
if(et_is_mobile()){ ?>
	<style type="text/css">
		.modal-paymill .button{
			overflow: hidden;
			max-width: 100%;
			display: block;
			position: relative;
			clear: both;
		}
		.modal-paymill textarea{
			height: 70px !important;
			width: 235px !important
		}
		.modal-paymill input.error{
			color: #FA5858;
		}

	</style>
<?php 
}
?>

<div class="modal modal-job modal-login modal-paymill" id="paymill_modal">
	<div class="edit-job-inner">
		<?php if( !function_exists('et_is_mobile') || !et_is_mobile() ) { ?>
			<div class="paymill-header bg-main-header">
				<div class="logo"><img src="<?php echo $website_logo[0];?>" height="50" /></div>
				<div class="info slogan">
      				<span class="plan_name">{$plan_name}</span> <br/>
      				<span class="plan_desc">{$plan_description}</span>      
    			</div>
			</div>
		<?php }?>
		<div class="payment-errors"></div>
		<form class="modal-form" id="paymill_form" novalidate="novalidate" autocomplete="on">
			<div class="content clearfix">		
				<div class="form-item">
					<div class="label">
						<?php _e('Card number:', ET_DOMAIN);?>
					</div>
					<div class="fld-wrap">
						<input tabindex="20" id="paymill_number" name="number" type="text" size="20" data-paymill="number" class="bg-default-input not_empty" placeholder="&#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226;" />
					</div>
				</div>
				<div class="form-item form-item-right">
				  	<div class="label">
						<?php _e('Expiry date:', ET_DOMAIN);?>
				  	</div>
				 	<div class="fld-wrap paymill_date">
					 	<input tabindex="21" type="text" size="2" name="exp-month" data-paymill="exp-month" placeholder="MM" class="bg-default-input not_empty" id="paymill_exp_month"/>
				      	<span> / </span>
				      	<input tabindex="22" type="text" size="4" name="exp-year" data-paymill="exp-year" placeholder="YYYY" class="bg-default-input not_empty" id="paymill_exp_year"/>
				 	</div>
				</div>

				<div class="form-item">
				  	<div class="label">
						<?php _e('Name on card:',ET_DOMAIN);?>
				  	</div>
				  	<div class="fld-wrap">
						<input tabindex="23" name="cardholder" id="paymill-cardholder" data-paymill="name" class="bg-default-input not_empty" type="text" />
				 	</div>
				</div>	
				
				<div class="form-item form-item-right">
				  	<div class="label">
						<?php _e('Card code:', ET_DOMAIN);?>
				  	</div>
				 	<div class="fld-wrap">
						<input tabindex="24" type="text" size="3" id="paymill-cvc" data-paymill="cvc" class="bg-default-input not_empty" placeholder="CVC" id="paymill_cvc" />
				  	</div>
				  	
				</div>	
				
				<div class="expan" style="clear:both;float:left;">
				<div class="form-item form-item-right">
				  	<div class="label">
						<?php _e('Address:', ET_DOMAIN);?>
				  	</div>
				 	<div class="fld-wrap">
						<textarea tabindex="24" style="width:280px;height:50px;" id="paymill-des" data-paymill="des" class="bg-default-input not_empty" id="paymill_des" /></textarea>
				  	</div>
				  	
				</div>	
				
					
               </div>	

			</div>
			<div class="footer font-quicksand">
				<div class="button">				         
					<input type="submit" class="bg-btn-action border-radius" value="<?php _e('PAY THROUGH PAYMILL',ET_DOMAIN);?>" id="submit_paymill">
				</div>
			</div>
		</form>
	</div>
	<div class="modal-close"></div>
</div>