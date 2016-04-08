<?php
/**
 * Template Name: Stripe Results Template
 *
 * */

$error_msg = '';

$settings_stripe_secret_key = get_option('settings_stripe_secret_key');
$settings_stripe_public_key = get_option('settings_stripe_public_key');
$settings_stripe_client_id = get_option('settings_stripe_client_id');
$settings_company_fee_for_stripe = get_option('settings_company_fee_for_stripe');

if(empty($settings_stripe_secret_key) || empty($settings_stripe_public_key) || empty($settings_stripe_client_id)){
	$error_msg = "Stripe settings error.";
} else {

	if(isset($_POST) && !empty($_POST) && isset($_POST['user_stripe_acc']) && !empty($_POST['user_stripe_acc'])){

		require_once(get_template_directory().'/inc/stripe/init.php');

		\Stripe\Stripe::setApiKey($settings_stripe_secret_key);

		$stripe_price = isset($_POST['stripe_price']) ? $_POST['stripe_price'] : '';
		$user_stripe_acc = isset($_POST['user_stripe_acc']) ? $_POST['user_stripe_acc'] : '';
		$bid_id = isset($_POST['bid_id']) ? $_POST['bid_id'] : '';
		$project_slug = isset($_POST['project_slug']) ? $_POST['project_slug'] : '';
		$stripeToken = isset($_POST['stripeToken']) ? $_POST['stripeToken'] : '';
		$stripeTokenType = isset($_POST['stripeTokenType']) ? $_POST['stripeTokenType'] : '';
		$stripeEmail = isset($_POST['stripeEmail']) ? $_POST['stripeEmail'] : '';

		$comp_fee = ceil($stripe_price * $settings_company_fee_for_stripe / 100);

		$charge = \Stripe\Charge::create(
			array(
				"amount" => $stripe_price, // amount in cents
				"currency" => "usd",
				"source" => $stripeToken,
				"description" => "Perssistant payment",
				"application_fee" => $comp_fee // amount in cents
			),
			array("stripe_account" => $user_stripe_acc)
		);


		if($charge['status'] == 'succeeded') {
			$bid_paid_by_stripe = get_post_meta($bid_id, 'bid_paid_by_stripe', true);
			if(!empty($bid_paid_by_stripe)){
				update_post_meta($bid_id, 'bid_paid_by_stripe', 'yes');
			} else {
				add_post_meta($bid_id, 'bid_paid_by_stripe', 'yes');
			}
			$notification = Fre_Notification::bidPaid($bid_id);
			wp_redirect(get_home_url().'/project/'.$project_slug , 301); exit;
		}


//		$token_request_body = array(
//			'client_secret' => $settings_stripe_secret_key,
//			'client_id' => $settings_stripe_client_id,
//			'stripe_user_id' => 'acct_17tJ0yGQi1gCI7JI'
//		);
//
//		$response = wp_remote_post( 'https://connect.stripe.com/oauth/deauthorize', array(
//				'method' => 'POST',
//				'timeout' => 10,
//				'redirection' => 5,
//				'httpversion' => '1.0',
//				'blocking' => true,
//				'headers' => array('Content-Type: application/json'),
//				'body' => $token_request_body,
//				'cookies' => array()
//			)
//		);

	}

	if (isset($_GET['code'])) { // Redirect w/ code
		$code = $_GET['code'];

		$token_request_body = array(
			'grant_type' => 'authorization_code',
			'client_id' => $settings_stripe_client_id,
			'code' => $code,
			'client_secret' => $settings_stripe_secret_key
		);


		$response = wp_remote_post( 'https://connect.stripe.com/oauth/token', array(
				'method' => 'POST',
				'timeout' => 10,
				'redirection' => 5,
				'httpversion' => '1.0',
				'blocking' => true,
				'headers' => array('Content-Type: application/json'),
				'body' => $token_request_body,
				'cookies' => array()
			)
		);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			$error_msg = "Something went wrong: $error_message";
		} else {
			$resp_data = $response['body'] ? json_decode($response['body'], true) : '';
			if(!empty($resp_data) && isset($resp_data['stripe_user_id']) && !empty($resp_data['stripe_user_id'])){
				$current_user = wp_get_current_user();
				if(!empty($current_user)){
					$users_stripe_account_id = get_user_meta($current_user->ID, 'stripe_account_id', true);
					if(!empty($users_stripe_account_id)){
						update_user_meta($current_user->ID, 'stripe_account_id', $resp_data['stripe_user_id']);
					} else {
						add_user_meta($current_user->ID, 'stripe_account_id', $resp_data['stripe_user_id']);
					}
					wp_redirect(get_home_url().'/profile/' , 301); exit;
				}
			} else {
				$error_msg = "Something went wrong: no enough data.";
			}
		}
	} else if (isset($_GET['error'])) { // Error
		$error_msg = $_GET['error_description'];
	}
}

global $post;
get_header();
the_post();

?>

	<section class="blog-header-container">
		<div class="container">
			<!-- blog header -->
			<div class="row">
				<div class="col-md-12 blog-classic-top">
					<h2><?php the_title(); ?></h2>
				</div>
			</div>
			<!--// blog header  -->
		</div>
	</section>

	<div class="container page-container">
		<!-- block control  -->
		<div class="row block-posts block-page">
			<div class="col-md-12 col-sm-12 col-xs-12 posts-container">
				<div class="blog-content">
					<?php
					the_content();
					?>

					<div class="clearfix"></div>

					<div>
						<?php if(!empty($error_msg)){ ?>
							<?php echo $error_msg; ?>
						<?php } ?>
					</div>
				</div><!-- end page content -->
			</div><!-- LEFT CONTENT -->
		</div>
		<!--// block control  -->
	</div>
<?php
get_footer();
?>
