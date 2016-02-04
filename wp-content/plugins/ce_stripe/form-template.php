<?php 
	$general_opts	= CE_Options::get_instance();
	$website_logo	= $general_opts->get_website_logo();
?>
<style type="text/css">
	.ui-popup .modal-body{
		padding: 20px;
	}

	.ui-popup #submit_stripe {
		color: #FFF;
		width: 100%;
		padding: 10px;
		font-weight: 600;
		font-size: 16px;
		border: none;
		-moz-box-shadow: inset 0 -3px 3px #327dbd;
		-webkit-box-shadow: inset 0 -3px 3px #327DBD;
		box-shadow: inset 0 -3px 3px #327DBD;
		background-color: #3783C5;
		background-image: -moz-linear-gradient(bottom, #3783c5 0%, #3783c5 100%);
		background-image: -o-linear-gradient(bottom, #3783c5 0%, #3783c5 100%);
		background-image: -webkit-linear-gradient(bottom, #3783C5 0%, #3783C5 100%);
		background-image: linear-gradient(bottom, #3783c5 0%, #3783c5 100%);
		border-radius: 3px !important;
		-moz-border-radius: 3px !important;
		-webkit-border-radius: 3px !important;
	}

</style>
<div class="modal fade modal-stripe" id="stripe_modal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
		<?php if( !function_exists('et_load_mobile') || !et_load_mobile() ) { ?>
			<div class="modal-header">
				<button  style="z-index:1000;" data-dismiss="modal" class="close">×</button>				
				<div class="info slogan">
	      			<h4 class="modal-title"><span class="plan_name">{$plan_name}</span></h4>
	      			<span class="plan_desc">{$plan_description}</span>      
	    		</div>
			</div>
		<?php } ?>
			<div class="modal-body">
				
				<form class="modal-form" id="stripe_form" novalidate="novalidate" autocomplete="on" data-ajax="false">
					<div class="content clearfix">		
						<div class="form-group">
							<div class="controls">
								<div class="form-item-left">
									<label>
										<?php _e('Card number:', ET_DOMAIN);?>
									</label>
									<div class="controls fld-wrap">
										<input tabindex="20" id="stripe_number" type="text" size="20"  data-stripe="number" class="bg-default-input not_empty" placeholder="&#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226;" />
									</div>
								</div>
								<div class="form-item-right">
								  	<label>
										<?php _e('Expiry date:', ET_DOMAIN);?>
								  	</label>
								 	<div class="stripe_date">
									 	<input tabindex="22" type="text" size="4" data-stripe="exp-year" placeholder="YY"  class="bg-default-input not_empty" id="exp_year"/>
								      	<span> / </span>								      	
								      	<input tabindex="21" type="text" size="2" data-stripe="exp-month" placeholder="MM"  class="bg-default-input not_empty" id="exp_month"/>
								 	</div>
								</div>
							</div>
						</div> 
						

						<div class="form-group">

							<div class="form-item-left">
							  	<label>
									<?php _e('Name on card:',ET_DOMAIN);?>
							  	</label>
							  	<div class="controls ">
									<input tabindex="23" name="name_card" id="name_card"  data-stripe="name" class="bg-default-input not_empty" type="text" />
							 	</div>
							</div>

							<div class="form-item-right">
								<label>
									<?php _e('Card code:', ET_DOMAIN);?>
							  	</label>
							 	<div class="controls">
									<input tabindex="24" type="text" size="3"  data-stripe="cvc" class="bg-default-input not_empty input-cvc " placeholder="CVC" id="cvc" />
							  	</div>
							</div>	
						</div>
						
					</div>
					<div class="footer form-group font-quicksand">
						<div class="button">  
							<button class="btn  btn-primary" type="submit"  id="submit_stripe"> <?php _e('PAY THROUGH STRIPE',ET_DOMAIN);?> </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal-close"></div>
</div>