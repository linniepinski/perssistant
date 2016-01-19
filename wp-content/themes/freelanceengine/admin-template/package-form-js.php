<script type="text/template" id="template_edit_form">
	<form action="qa-update-badge" class="edit-plan engine-payment-form">
		<input type="hidden" name="id" value="{{= id }}">
		
		<div class="form payment-plan">
			<div class="form-item f-left-all clearfix">
				<div class="width33p">
					<div class="label"><?php _e("SKU", ET_DOMAIN); ?></div>
					<input value="{{= sku }}" class="bg-grey-input width50p not-empty required" name="sku" type="text" /> 
				</div>
			</div>
			<div class="form-item">
				<div class="label"><?php _e("Package name", ET_DOMAIN); ?></div>
				<input value="{{= post_title }}" class="bg-grey-input not-empty required" name="post_title" type="text">
			</div>
			<div class="form-item f-left-all clearfix">
				<div class="width33p">
					<div class="label"><?php _e("Price", ET_DOMAIN); ?></div>
					<input value="{{= et_price }}" class="bg-grey-input width50p not-empty is-number required number" name="et_price" type="text" /> 
					<?php 
						ae_currency_sign();
					?>
				</div>
				<div class="width33p">
					<div class="label"><?php _e("Availability",ET_DOMAIN);?></div>
					<input value="{{= et_duration }}" class="bg-grey-input width50p not-empty is-number required number" type="text" name="et_duration" /> 
					<?php _e("days",ET_DOMAIN);?>
				</div>
				<div class="width33p">
					<div class="label"><?php _e("Number of project can post", ET_DOMAIN); ?></div>
					<input value="{{= et_number_posts }}" class=" bg-grey-input width50p not-empty is-number required" type="text" name="et_number_posts" /> 							
				</div>

			</div>
			<div class="form-item">
			<div class="label"><?php _e("Project type",ET_DOMAIN);?></div>
				<?php ae_tax_dropdown( 'project_type' , 
										array(  'class' => 'chosen-single tax-item', 
												'hide_empty' => false, 
												'hierarchical' => true , 
												'id' => 'project_type' , 
												'show_option_all' => __("Select project type", ET_DOMAIN) ,
												'value' => 'id',
												'name' => 'project_type'
											) 
										) ;?> 
			</div>
			<div class="form-item">
				<div class="label"><?php _e("Short description about this package",ET_DOMAIN);?></div>
				<input class="bg-grey-input not-empty" name="post_content" type="text" value="{{= post_content }}" />
			</div>

			<div class="form-item">
				<div class="label"><?php _e("Featured Project",ET_DOMAIN);?></div>
				<input type="checkbox" name="et_featured" value="1" <# if (typeof et_featured !== 'undefined' && et_featured == 1 ) { #> checked="checked" <# } #> 	/> 
				<?php _e("This plan will be featured.",ET_DOMAIN);?>
			</div>

			<div class="submit">
				<button  class="btn-button engine-submit-btn add_payment_plan">
					<span><?php _e( 'Save Package' , ET_DOMAIN ); ?></span><span class="icon" data-icon="+"></span>
				</button>
				or <a href="#" class="cancel-edit"><?php _e( "Cancel" , ET_DOMAIN ); ?></a>
			</div>
		</div>
	</form>
</script>