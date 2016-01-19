<div class="et-main-main clearfix" id="simply_hired_setting">
	<?php
	$api	=	$this->get_settings(  );
	extract($api);
	?>
	<div class="module">
		<div class="title font-quicksand"><?php _e("SimplyHired API", ET_DOMAIN); ?></div>
		<div class="desc no-left">
			<?php _e("Configure your SimplyHired API", ET_DOMAIN); ?>
		</div>
	</div>

	<div class="module simply-hired-form">
		<form action="#" method="post">
			<div class="form-item">
				<label><?php _e("Publisher ID", ET_DOMAIN); ?></label>
				<input type="text" name="pshid" value="<?php echo $pshid ?>"  class="" />
			</div>
			<div class="form-item">
				<label><?php _e("Job-a-matic domain", ET_DOMAIN); ?></label>
				<input type="text" name="jbd" value="<?php echo $jbd ?>" class="" />
			</div>	
			<div class="form-item">
				<label><?php _e("Search Style", ET_DOMAIN); ?></label>
				<input type="text" name="ssty" value="<?php echo $ssty ?>" />
			</div>	

			<div class="form-item">
				<label><?php _e("Configuration Flag", ET_DOMAIN); ?></label>
				<input type="text" name="cflg" value="<?php echo $cflg ?>" />
			</div>	
			<?php wp_nonce_field('update_simply_hired_setting','simply_hired_setting'); ?>
			<input type="hidden" name="action" value="update-simply-hired-setting" /> 	        				
		</form>
	</div>
	<button class="et-button btn-button" id="save_setting">
		<?php _e("Save your setting", ET_DOMAIN); ?>
	</button>
</div>