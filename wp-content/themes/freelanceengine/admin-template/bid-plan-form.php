<form action="de-add-bid-plans" class="engine-payment-form add-pack-form">
	<div class="form payment-plan">
		<div class="form-item f-left-all clearfix">
			<div class="width33p">
				<div class="label"><?php _e("SKU",ET_DOMAIN);?></div>
				<input class="bg-grey-input width50p not-empty  required" name="sku" type="text" /> 
			</div>
		</div>
		<div class="form-item">
			<div class="label"><?php _e("Enter a name for your plan",ET_DOMAIN);?></div>
			<input class="bg-grey-input not-empty required" name="post_title" type="text" />
		</div>
		<div class="form-item f-left-all clearfix">
			<div class="width33p">
				<div class="label"><?php _e("Price",ET_DOMAIN);?></div>
				<input class="bg-grey-input width50p not-empty is-number required number" name="et_price" type="text" /> 
				<?php 
				ae_currency_sign();
				?>
			</div>
			<div class="width33p">
				<div class="label"><?php _e("Number of project can post",ET_DOMAIN);?></div>
				<input class="bg-grey-input width50p not-empty is-number required number" type="text" name="et_number_posts" /> 
				<?php _e("posts",ET_DOMAIN);?>
			</div>
		</div>
		<div class="form-item">
			<div class="label"><?php _e("Short description about this package",ET_DOMAIN);?></div>
			<input class="bg-grey-input not-empty" name="post_content" type="text" />
		</div>
		<div class="form-item">
			<div class="label"><?php _e("Featured Project",ET_DOMAIN);?></div>
			<input type="checkbox" name="et_featured" value="1"/> <?php _e("This plan will be featured.",ET_DOMAIN);?>
		</div>
		<div class="submit">
			<button class="btn-button engine-submit-btn add_payment_plan">
				<span><?php _e("Save Plan",ET_DOMAIN);?></span><span class="icon" data-icon="+"></span>
			</button>
		</div>
	</div>
</form>