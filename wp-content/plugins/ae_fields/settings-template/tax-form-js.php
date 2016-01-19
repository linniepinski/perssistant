<script type="text/template" id="template_edit_tax">
	<form action="qa-update-badge" class="edit-plan engine-payment-form">
		<input type="hidden" name="id" value="{{= id }}">
		<input type="hidden" name="menu_order" value="1">
		<div class="form payment-plan">
			<div class="form-item">
				<div class="label">
					<?php _e("Field name",ET_DOMAIN);?>
					<span style="display:block;font-size:14px;">
						<?php _e("A Field name should be unique, lowercase, do not leave spaces between the name.", ET_DOMAIN); ?>
					</span>
				</div>
				<input class="bg-grey-input not-empty required is_packname" name="post_title" value="{{=post_title}}" type="text" />
			</div>
			<div class="form-item">
				<div class="label"><?php _e("Field label",ET_DOMAIN);?></div>
				<input class="bg-grey-input not-empty required" name="label" value="{{=label}}" type="text" />
			</div>
			<div class="form-item">
				<div class="label"><?php _e("Placeholder",ET_DOMAIN);?></div>
				<input class="bg-grey-input" name="placeholder" type="text" value="{{=placeholder}}" />
			</div>
			<div class="form-item">
				<div class="label"><?php _e("Short description about this field",ET_DOMAIN);?></div>
				<input class="bg-grey-input not-empty required" name="post_content" value="{{=post_content}}" type="text" />
			</div>
			<div class="form-item">
				<div class="label"><?php _e("Post type",ET_DOMAIN);?></div>
				<?php 
				$post_types = apply_filters( 'ae_field_support_post_types', get_post_types(array('public' => true)) );
				?>
				<select name="field_for" id="field_for" class="chosen-single tax-item">
					<?php foreach ($post_types as $key => $post_type) {
						$obj = get_post_type_object($post_type);
					?>
					<option class=" fix  level-0" value="<?php echo $obj->name; ?>"><?php echo $obj->label; ?></option>
					<?php } ?>
				</select>
			</div>

			<div class="form-item">
				<div class="label"><?php _e("Field type",ET_DOMAIN);?></div>
				<select name="field_type" id="field_type" class="chosen-single tax-item">
					<option class=" fix  level-0" value="select"><?php _e("Select", ET_DOMAIN); ?></option>
					<option class=" fix  level-0" value="radio"><?php _e("Radio", ET_DOMAIN); ?></option>
					<option class=" fix  level-0" value="checkbox"><?php _e("Checkbox", ET_DOMAIN); ?></option>
					<option class=" fix  level-0" value="multi_select"><?php _e("Multi Select", ET_DOMAIN); ?></option>
				</select>
			</div>
			
			<div class="form-item">
				<div class="label"><?php _e("Requirable",ET_DOMAIN);?></div>
				<input type="checkbox" name="required" value="1" <# if (typeof required !== 'undefined' && required == 1 ) { #> checked="checked" <# } #> /> <?php _e("This field will be required.",ET_DOMAIN);?>
			</div>
			<div class="submit">
				<button class="btn-button engine-submit-btn add_payment_plan">
					<span><?php _e("Save Field",ET_DOMAIN);?></span><span class="icon" data-icon="+"></span>
				</button>
			</div>
		</div>
	</form>
</script>