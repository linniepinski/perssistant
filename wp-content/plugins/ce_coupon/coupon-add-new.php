<?php
	$plans			=	et_get_payment_plans();

?>
<div class="module row-top">
	<div class="title font-quicksand" style="border-bottom:0px;">
		<a class="f-right btn-new-coupon" href="#" title="<?php _e('New Coupon List',ET_DOMAIN);?>" id="add_new_coupon">
			<?php _e("Add New", ET_DOMAIN); ?><span class="icon" data-icon="+"></span>
		</a>
	</div>
</div>
<div id="add_coupon_form" style="display:none">

	<div class="title font-quicksand">
		<?php _e("Add new coupon", ET_DOMAIN); ?>
	</div>
	<div class="module ad-coupon-form">
		<form action="#" method="post" id="coupon_form">
			<input type="hidden" name="coupon_id" value="" />
			<input type="hidden" name="action" value="ce-update-coupon" />
			<div class="form-item">
				<label><?php _e("Coupon Usage Count <span>(How many times the coupon can be used)</span>", ET_DOMAIN); ?></label>
				<div class="">
					<input type="text" name="usage_count" id="usage_count" class="required number" placeholder="<?php _e("Enter a number for coupon usage", ET_DOMAIN); ?>" title="<?php _e("Enter a number for coupon usage", ET_DOMAIN); ?>" />
				</div>
			</div>
			<div class="form-item">
				<label><?php _e("Discount", ET_DOMAIN); ?></label>
				<div class="two-p50">
					<div class="">
						<input type="text" name="discount_rate" id="discount_rate" class="required number" placeholder="<?php _e("Enter a number for discount amount", ET_DOMAIN); ?>" title="<?php _e("Enter a number for discount amount", ET_DOMAIN); ?>" />
					</div>
					<div class="mright select-category select-style et-button-select">
						<select name="discount_type">
							<option value="percent">%</option>
							<option value="currency"><?php echo $currency['code']; ?></option>
						</select>
					</div>
				</div>
			</div>
			<div class="form-item">
				<label><?php _e("User Coupon Usage Count <span>(How many times a customer can use the coupon code)</span>", ET_DOMAIN); ?></label>
				<div class="">
					<input type="text" name="user_coupon_usage" id="user_coupon_usage" class="number" placeholder="<?php _e("Enter a number for user coupon usage", ET_DOMAIN); ?>" title="<?php _e("Enter a number for user coupon usage", ET_DOMAIN); ?>" />
					<span class=""><?php _e("Time(s)", ET_DOMAIN); ?></span>
				</div>
			</div>
			<div class="form-item">
				<label><?php _e("Dates", ET_DOMAIN); ?> <span><?php _e("(Validity period of the coupon)", ET_DOMAIN); ?></span>  </label>

				<div class="two-p50">
					<div class="date-limit select-category select-style et-button-select">
						<select name="date_limit" id="date_limit">
							<option  value="off"><?php _e("Lifetime", ET_DOMAIN); ?></option>
							<option value="on"><?php _e("Specify date range", ET_DOMAIN); ?></option>
						</select>
					</div>
					<!-- <div id="date_range" > -->
						<div class="date" style="display:none">
							<input type="text" name="start_date" id="start_date" class="sdate"  placeholder="<?php _e("Start date", ET_DOMAIN); ?>" title="<?php _e("Start date", ET_DOMAIN); ?>" />
							<span class="icon" data-icon="\"></span>
						</div>
						<div class="mright date" style="display:none">
							<input type="text" name="expired_date" id="expired_date" class="sdate" placeholder="<?php _e("Expiry date", ET_DOMAIN); ?>" title="<?php _e("Expiry date", ET_DOMAIN); ?>" />
							<span class="icon" data-icon="\"></span>
						</div>
					<!-- </div> -->
				</div>
			</div>
			<div class="form-item">
				<label><?php _e("Payment plans <span>(Coupons can be used on selected or all payment plans)</span>", ET_DOMAIN); ?></label>
				<div class="two-p50">
					<div class="select-category select-style et-button-select">
						<select  id="product" title="<?php _e("Select payment plans", ET_DOMAIN); ?>">
							<option value=""><?php _e("Select a payment plan", ET_DOMAIN); ?></option>
							<?php foreach ($plans as $key => $value) { ?>
							<option value="<?php echo $value['ID'] ?>"><?php printf(__("Ad plan - %s", ET_DOMAIN) , $value['post_title'])  ?></option>
							<?php } ?>
							
						</select>
					</div>
					<div class="mright list-coupon">
						<!-- product list here -->
					</div>
				</div>
			</div>
			<div class="form-item row-bottom">
				<button class="et-button btn-button" id="save_coupon"><?php _e('Save',ET_DOMAIN); ?></button>	
			</div>
	
		</form>
	</div>
	
</div>
<script type="text/template" id="coupon_edit_form">
	<div id="edit_coupon_form">	
		<div class="module ad-coupon-form">
			<div class="title font-quicksand">
				<?php _e('Edit coupon',ET_DOMAIN);?>
			</div>
			<form action="#" method="post" id="coupon_form">
				<input type="hidden" name="coupon_id" value="{{ID}}" />
				<input type="hidden" name="action" value="ce-update-coupon" />
				<div class="form-item">
					<label><?php _e("Coupon Usage Count <span>(How many times the coupon can be used)</span>", ET_DOMAIN); ?></label>
					<div class="">
						<input type="text" name="usage_count" value ="{{ usage_count }}" id="usage_count" class="required number" placeholder="<?php _e("Enter a number for coupon usage", ET_DOMAIN); ?>" title="<?php _e("Enter a number for coupon usage", ET_DOMAIN); ?>" />
					</div>
				</div>
				<div class="form-item">
					<label><?php _e("Discount", ET_DOMAIN); ?></label>
					<div class="two-p50">
						<div class="">
							<input type="text" name="discount_rate" id="discount_rate" value= "{{  discount_rate }}" class="required number" placeholder="<?php _e("Enter a number for discount amount", ET_DOMAIN); ?>" title="<?php _e("Enter a number for discount amount", ET_DOMAIN); ?>" />
						</div>
						<div class="mright select-category select-style et-button-select">
							<select name="discount_type">
								<option <# if(discount_type == 'percent') { #> selected="selected" <# } #> value="percent">%</option>
								<option <# if(discount_type == 'currency') { #> selected="selected" <# } #>  value="currency"><?php echo $currency['code']; ?></option>
							</select>
						</div>
					</div>
				</div> 
				<div class="form-item">
					<label><?php _e("User Coupon Usage Count <span>(How many times a customer can use the coupon code)</span>", ET_DOMAIN); ?></label>
					<div class="">
						<input type="text" name="user_coupon_usage" id="user_coupon_usage" value="{{ user_coupon_usage }}" class="number" placeholder="<?php _e("Enter a number for user coupon usage", ET_DOMAIN); ?>" title="<?php _e("Enter a number for user coupon usage", ET_DOMAIN); ?>" />
						<span class=""><?php _e("Time(s)", ET_DOMAIN); ?></span>
					</div>
				</div>	
				<div class="form-item">
					<label><?php _e("Dates", ET_DOMAIN); ?> <span><?php _e("(Validity period of the coupon)", ET_DOMAIN); ?></span>  </label>

					<div class="two-p50">
						<div class="date-limit select-category select-style et-button-select">
							<select name="date_limit" class="date_limit" id="date_limit">
								<option <# if(date_limit =='off') { #> selected ="selected" <#}#> value="off"><?php _e("Lifetime", ET_DOMAIN); ?></option>
								<option <# if(date_limit =='on') { #> selected ="selected" <#}#>  value="on"><?php _e("Specify date range", ET_DOMAIN); ?></option>
							</select>
						</div>
				
							<div class="date" style="display:none">
								<input type="text" name="start_date" id="start_date" value ="{{ start_date }}" class="sdate"  placeholder="<?php _e("Start date", ET_DOMAIN); ?>" title="<?php _e("Start date", ET_DOMAIN); ?>" />
								<span class="icon" data-icon=""></span>
							</div>
							<div class="mright date" style="display:none">
								<input type="text" name="expired_date" id="expired_date" value ="{{ expired_date }}" class="sdate" placeholder="<?php _e("Expiry date", ET_DOMAIN); ?>" title="<?php _e("Expiry date", ET_DOMAIN); ?>" />
								<span class="icon" data-icon=""></span>
							</div>
					
					</div>
				</div>
				<div class="form-item">
					<label><?php _e("Payment plans <span>(Coupons can be used on selected or all payment plans)</span>", ET_DOMAIN); ?></label>
					<div class="two-p50">
						<div class="select-category select-style et-button-select">
							<select  id="product" title="<?php _e("Select payment plans", ET_DOMAIN); ?>">
								<option value=""><?php _e("Select a payment plan", ET_DOMAIN); ?></option>
								<?php foreach ($plans as $key => $value) { ?>
								<option value="<?php echo $value['ID'] ?>"><?php printf(__("Ad plan - %s", ET_DOMAIN) , $value['post_title'])  ?></option>
								<?php } ?>
								
							</select>
						</div>
						<div class="mright list-coupon">
							<!-- product list here -->
						</div>
					</div>
				</div>
				<div class="form-item row-bottom">
					<button id="save_coupon" class="et-button btn-button"><?php _e('Save',ET_DOMAIN);?></button>
				</div>
		
			</form>
		</div>
	</div>
</script>