<?php 
	$options = AE_Options::get_instance();
    // save this setting to theme options
    $website_logo = $options->site_logo;
?>
<style type="text/css">
	#payu_modal .modal-header .close  {
		width: 30px;
		height: 30px;
        color:#fff;
	}
    .plan_desc{
        color:#428BCA;
    }
    #payu_email{
        height:41px;
        width:100%;
        padding:0 15px
    }
    
</style>
<div class="modal fade modal-payu form_modal_style" id="payu_modal" aria-hidden="true">
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
				
				<form class="modal-form" id="payu_form" action="#" method="POST" autocomplete="on">
                
					<div class="content clearfix">		
						<div class="form-group">
							<div class="controls">
								<div class="form-field">
									<label>
										<?php _e('First name:', ET_DOMAIN);?>
									</label>
									<div class="controls fld-wrap">
										<input  tabindex="20" id="payu_firstname" type="text" size="20"  required class="bg-default-input not_empty required" placeholder="Your first name" />
									</div>
								</div>
                                <div class="form-field">
									<label>
										<?php _e('Last name:', ET_DOMAIN);?>
									</label>
									<div class="controls fld-wrap">
										<input  tabindex="20" id="payu_lastname" type="text" size="20"  class="bg-default-input not_empty" placeholder="Your last name" />
									</div>
								</div>
                                <div class="form-field">
									<label>
										<?php _e('Email:', ET_DOMAIN);?>
									</label>
									<div class="controls fld-wrap">
										<input  tabindex="20" id="payu_email" type="email" size="20" required  class="bg-default-input not_empty" placeholder="e.g exemple@enginethemes.com" />
									</div>
								</div>
                                <div class="form-field">
									<label>
										<?php _e('Phone:', ET_DOMAIN);?>
									</label>
									<div class="controls fld-wrap">
										<input  tabindex="20" id="payu_phone" type="text" size="20" class="bg-default-input not_empty" placeholder="0123456789" />
									</div>
								</div>
							</div>
						</div>	
					</div>
					<div class="footer form-group font-quicksand">
						<div class="button">  
							<button type="submit" class="btn  btn-primary" id="button_payu"><?php _e('PAY NOW WITH PAYUMONEY',ET_DOMAIN); ?> </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="modal-close"></div>
</div>
</div>
<div style="display: none; height: 0; width:0;">
    <form method="post" action="#" id="payu_hidden_form">
        <input type="hidden" name="key" id="payu_key"/>
        <input type="hidden" name="hash" id="payu_hash" />
        <input type="hidden" name="txnid" id="payu_txnid"/>
        <input type="hidden" name="firstname" id="payu_firstname_h"/>
        <input type="hidden" name="productinfo" id="payu_productinfo"/>
        <input type="hidden" name="email" id="payu_email_h"/>
        <input type="hidden" name="phone" id="payu_phone_h"/>
        <input type="hidden" name="amount" id="payu_amount"/>
        <input type="hidden" name="surl" id="payu_surl"/>
        <input type="hidden" name="furl" id="payu_furl"/>
        <input type="hidden" name="curl" id="payu_curl"/>
        <input type="hidden" name="service_provider" value="payu_paisa"/>
        <button type="submit" class="btn  btn-primary" id="button_payu_h" >Submit </button>
</form>
</div>