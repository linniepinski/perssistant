<div id="linkedin-settings" class="et-main-main" <?php if ( isset($sub_section) && ($sub_section != '' && $sub_section != 'linkedin')) echo 'style="display:none"' ?> >
	<?php
	$api	=	$this->get_settings(  );
	$api_key=isset($api['api_key']) ? trim($api['api_key']) :'';
	$secret_key=isset($api['secret_key']) ? trim($api['secret_key']) :'';
	$token=isset($api['token']) ? trim($api['token']) :'';
	$token_secret=isset($api['token_secret']) ? trim($api['token_secret']) :'';
	?>
	<div class="module">
		<div class="title font-quicksand"><?php _e("LinkedIn API", ET_DOMAIN); ?></div>
		<div class="desc no-left">
			<?php _e("Configure your LinkedIn API", ET_DOMAIN); ?>
		</div>
	</div>

	<div class="module simply-linkedin-form">
		<form action="#" method="post">
			<div class="form-item">
 					<label>Your API Key on LindkedIn.com: </label><input type="text" placeholder="API Key" value="<?php echo $api_key;?>" id="api_key" name="api_key" title="API Key">
 					
 				</div>
			<div class="form-item">
				<label>Your Secret Key on LindkedIn.com: </label><input type="text" placeholder="Secret Key" value="<?php echo $secret_key; ?>" id="secret_key" name="secret_key" title="Secret Key">
 					
			</div>	
			<div class="form-item">
			 <label>Your OAuth User Token on LindkedIn.com: </label><input type="text" placeholder="OAuth User Token" value="<?php echo $token; ?>" id="token" name="token" title="OAuth User Token">
 					
			</div>	
          <div class="form-item">
				<label>Your OAuth User Secret on LindkedIn.com: </label><input type="text" placeholder="OAuth User Secret" value="<?php echo $token_secret; ?>" id="token_secret" name="token_secret" title="OAuth User Secret">
 				
			</div>		
			<?php wp_nonce_field('update_linked_setting','linkedin_setting'); ?>
			<input type="hidden" name="action" value="update_linked_setting" /> 	        				
		</form>
	</div>
	<div class="header-more-info" style="width:200px;float:left;">
	<button class="et-button btn-button" id="save_setting">
		<?php _e("Save your setting", ET_DOMAIN); ?>
	</button>
	</div>
	<div class="header-more-info" style="width:200px;float:left;">
	<button class="et-button btn-button" id="test_setting">
		<?php _e("Test connection", ET_DOMAIN); ?>
	</button>
	</div>
</div>