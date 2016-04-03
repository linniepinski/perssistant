<?php
if(!empty($_POST)) {

	if(isset($_POST['settings_stripe_secret_key'])) {
		update_option('settings_stripe_secret_key', $_POST['settings_stripe_secret_key']);
	} else {
		update_option('settings_stripe_secret_key', '');
	}
	if(isset($_POST['settings_stripe_public_key'])) {
		update_option('settings_stripe_public_key', $_POST['settings_stripe_public_key']);
	} else {
		update_option('settings_stripe_public_key', '');
	}
	if(isset($_POST['settings_stripe_client_id'])) {
		update_option('settings_stripe_client_id', $_POST['settings_stripe_client_id']);
	} else {
		update_option('settings_stripe_client_id', '');
	}
	if(isset($_POST['settings_company_fee_for_stripe'])) {
		update_option('settings_company_fee_for_stripe', $_POST['settings_company_fee_for_stripe']);
	} else {
		update_option('settings_company_fee_for_stripe', '');
	}

}
?>

<?php
$company_fees = array(7 => '7%', 8 => '8%', 9 => '9%', 10 => '10%', 11 => '11%', 12 => '12%');

$settings_stripe_secret_key = get_option('settings_stripe_secret_key');
$settings_stripe_public_key = get_option('settings_stripe_public_key');
$settings_stripe_client_id = get_option('settings_stripe_client_id');
$settings_company_fee_for_stripe = get_option('settings_company_fee_for_stripe');

?>

<style>
	.name_label {
		width: 150px;
		display: inline-block;
	}
	.input_100 {
		width: 100%;
	}
	.class_width_400 {
		width: 400px;
		display: inline-block;
	}
	.class_width_350 {
		width: 350px;
		display: inline-block;
	}
</style>

<div class="wrap">
	<h2>sStripe ettings</h2>
	<p style="clear: both; float: left;"></p>
	<form method="post" action="#" id="" style="clear: both;">

		<div>
			<p class="">Stripe secret key</p>
			<input class="class_width_400" name="settings_stripe_secret_key" value="<?php echo $settings_stripe_secret_key; ?>" />

			<p class="">Stripe public key </p>
			<input class="class_width_400" name="settings_stripe_public_key" value="<?php echo $settings_stripe_public_key; ?>" />

			<p class="">Stripe client id </p>
			<input class="class_width_400" name="settings_stripe_client_id" value="<?php echo $settings_stripe_client_id; ?>" />

		</div>
		<h4>Company fee</h4>
		<ul>
			<li>
				<span class="name_label">Select company fee </span>
				<select name="settings_company_fee_for_stripe">
					<?php foreach($company_fees as $item => $name) { ?>
						<option value="<?php echo $item; ?>" <?php echo ($settings_company_fee_for_stripe == $item) ? 'selected' : ''; ?>><?php echo $name; ?></option>
					<?php } ?>
				</select>
			</li>

		</ul>

		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php echo esc_attr_e('Save Changes') ?>" />
		</p>

	</form>
</div>
