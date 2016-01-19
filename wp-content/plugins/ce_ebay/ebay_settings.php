<div id="ebay-settings" class="ebay-settings ebay-box">
	<?php
	$ap_id = get_option('ebay_app_id');
	$option = CE_Ebay::get_option();

	?>
	<div class="row">
		<div class="title font-quicksand"><?php _e("eBay API", ET_DOMAIN); ?></div>
		<div class="desc no-left">
			<?php _e("Configure your eBay API", ET_DOMAIN); ?>
		</div>
	</div>
	<form action="#" method="post" class="save-setting">
		<div class="row simply-ebay-form">

			<div class="form-item">
				<label><?php _e('Your APP ID on eBay.com',ET_DOMAIN);?>: </label>
				<input type="text" class="option-item bg-grey-input " placeholder="<?php _e('APP ID',ET_DOMAIN);?>" value="<?php echo $option['app_id'];?>" id="app_id" name="app_id" title="App ID">
 			</div>
 			<div class="form-item">
				<label><?php _e('Affiliate Custom ID',ET_DOMAIN);?>: </label>
				<input type="text" class="option-item bg-grey-input " placeholder="<?php _e('Custom ID',ET_DOMAIN);?>" value="<?php echo $option['custom_id'];?>" id="custom_id" name="custom_id" title="Custom ID"> 					
 			</div>
 			<div class="form-item">
				<label><?php _e('Select Network ID ',ET_DOMAIN);?>: </label>
				<?php
				$network = array(
					2 => 'Be Free',
					3 => 'Affilinet',
					4 => 'TradeDoubler',
					5 => 'Mediaplex',
					6 => 'DoubleClic',
					7 => 'Allye',
					8 => 'BJMT',
					9 => 'eBay Partner Network',
					);
				?>
				<div class="ce-ebay-network select-style et-button-select">
					<select name="network_id" id="network_id">
						<?php
						for($i=2; $i <= 9; $i++) { ?>
							<option  value ="<?php echo $i;?>" <?php if($i == $option['network_id']) echo "selected='selected' ";?> > <?php echo $network[$i]; ?> </option>
							<?php
						}
						?>
					</select>
				</div>
 			</div>
 			<div class="form-item">
				<label><?php _e('Your eBay Tracking Id',ET_DOMAIN);?>: </label>
				<input type="text" class="option-item bg-grey-input " placeholder="<?php _e('APP ID',ET_DOMAIN);?>" value="<?php echo $option['tracking_id'];?>" id="app_id" name="tracking_id" title="Tracking ID">
				<input type="hidden" name="action" value="ebay-save-setting" />
 			</div>
 			<div class="form-item form-item-checkbox">
				<label><?php _e('Use this info affiliate to import items',ET_DOMAIN);?>: </label>
				<input type="checkbox" class="" placeholder="<?php _e('use_affiliate',ET_DOMAIN);?>" value="1" <?php if($option['use_affiliate'] ==1) echo 'checked';?> id="use_affiliate" name="use_affiliate" > 					
 			</div>
			<?php wp_nonce_field('update_ebay_setting','ebay_setting'); ?>

		</div>
		<div class="row clearfix">
			<div class="header-more-info" style="width:166px;float:left;">
			<button class="et-button btn-button" id="save_setting">
				<?php _e("Save your setting", ET_DOMAIN); ?>
			</button>
			</div>

			<div class="header-more-info" style="width:166px;float:left;">
			<button class="et-button btn-button" id="test_setting" rel="<?php echo $option['app_id'];?>">
				<?php _e("Test connection", ET_DOMAIN); ?>
			</button>
			</div>
		</div>
	</form>

</div>