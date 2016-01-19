<script type="text/template" id="template_edit_form">
	<form action="qa-update-badge" class="edit-plan engine-payment-form">
		<input type="hidden" name="id" value="<%= id %>">
		<input type="hidden" name="qa_point_text" value="<%= qa_point_text %>">
		<div class="form payment-plan">
			<div class="form-item">
				<div class="label"><?php _e("Enter a name for your badge", ET_DOMAIN); ?></div>
				<input value="<%= post_title %>" class="bg-grey-input not-empty required" name="post_title" type="text">
			</div>
			<div class="form-item f-left-all clearfix">
				<div class="width33p">
					<div class="label"><?php _e("Point", ET_DOMAIN); ?></div>
					<input value="<%= qa_badge_point %>" class="bg-grey-input width50p not-empty is-number required number" name="qa_badge_point" type="text" /> 
				</div>
				<div class="width33p">
					<div class="label"><?php _e("Color", ET_DOMAIN); ?></div>
					<input value="<%= qa_badge_color %>" class="color-picker bg-grey-input width50p not-empty is-number required" type="text" name="qa_badge_color" /> 							
				</div>
			</div>
			<div class="submit">
				<button  class="btn-button engine-submit-btn add_payment_plan">
					<span>Save Plan</span><span class="icon" data-icon="+"></span>
				</button>
				or <a href="#" class="cancel-edit">Cancel</a>
			</div>
		</div>
	</form>
</script>