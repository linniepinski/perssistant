<?php 
	$options = AE_Options::get_instance();
    // save this setting to theme options
    $website_logo = $options->site_logo;
?>

<div class="modal fade modal-paymill" id="paymill_modal" aria-hidden="true">
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
				
				<form class="modal-form" id="paymill_form" action="#" method="POST" novalidate="novalidate" autocomplete="on">
					<div class="content clearfix">		
						<div class="form-group">
							<div class="controls">
								<div class="form-item-left">
									<label>
										<?php _e('Card number:', ET_DOMAIN);?>
									</label>
									<div class="controls fld-wrap">
										<input  tabindex="20" id="paymill_number" type="text" size="20"  data-paymill="number" class="card-number bg-default-input not_empty" placeholder="&#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226; &nbsp; &nbsp; &nbsp; &#8226;&#8226;&#8226;&#8226;" />
									</div>
								</div>
								<div class="form-item-right">
								  	<label>
										<?php _e('Expiry date:', ET_DOMAIN);?>
								  	</label>
								 	<div class="paymill_date">
									 	<input tabindex="22" type="text" size="4" data-paymill="exp-year" placeholder="YYYY"  class="card-expiry-year bg-default-input not_empty" id="paymill_exp_year"/>
								      	<span> / </span>								      	
								      	<input tabindex="21" type="text" size="2" data-paymill="exp-month" placeholder="MM"  class="card-expiry-month bg-default-input not_empty" id="paymill_exp_month"/>
								 	</div>
								</div>
							</div>
						</div>
						

						<div class="form-group">

							<div class="form-item-left">
							  	<label>
									<?php _e('Name on card:',ET_DOMAIN);?>
							  	</label>
							  	<div class="controls name_card ">
									<input tabindex="23" name="paymill_card_holdername" id="paymill_card_holdername"  data-paymill="paymill_card_holdername" class=" bg-default-input not_empty" type="text" />
							 	</div>
							</div>

							<div class="form-item-right">
								<label>
									<?php _e('Card code:', ET_DOMAIN);?>
							  	</label>
							 	<div class="controls card-code">
									<input tabindex="24" type="text" size="3"  data-paymill="cvc" class="card-cvc bg-default-input not_empty input-cvc " placeholder="CVC" id="paymill_cvc" />
							  	</div>
							</div>	
						</div>	
					</div>
					<div class="footer form-group font-quicksand">
						<div class="button">  
							<button type="submit" class="btn  btn-primary" id="button_paymill"><?php _e('PAY THROUGH PAYMILL',ET_DOMAIN); ?> </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal-close"></div>
</div>