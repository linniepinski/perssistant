<form id="" action="qa-add-bage" class="engine-payment-form add-pack-form">
	<div class="form payment-plan">
		<div class="form-item">
			<div class="label"><?php _e("Enter a name for your badge", 'aecore-other-backend'); ?></div>
			<input class="bg-grey-input not-empty required" name="post_title" type="text">
		</div>
		<div class="form-item f-left-all clearfix">
			<div class="width33p">
				<div class="label"><?php _e("Point", 'aecore-other-backend'); ?></div>
				<input class="bg-grey-input width50p not-empty is-number required number" name="qa_badge_point" type="text" /> 
			</div>
			<div class="width33p">
				<div class="label"><?php _e("Color", 'aecore-other-backend'); ?></div>
				<input class="color-picker bg-grey-input width50p not-empty is-number required" type="text" name="qa_badge_color" /> 							
			</div>
		</div>
		
		<div class="submit">
			<button class="btn-button engine-submit-btn add_payment_plan">
				<span><?php _e("Add badge", 'aecore-other-backend'); ?></span><span class="icon" data-icon="+"></span>
			</button>
		</div>
	</div>
</form>