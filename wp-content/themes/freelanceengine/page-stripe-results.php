<?php
/**
 * Template Name: Stripe Results Template
 *
 * */

$error_msg = '';

$settings_stripe_secret_key = '';
$settings_stripe_public_key = '';
$stripe_key = ae_get_option('stripe');
if(isset($stripe_key['publishable_key']) && !empty($stripe_key['publishable_key']) && isset($stripe_key['secret_key']) && !empty($stripe_key['secret_key'])){
	$settings_stripe_secret_key = get_option('settings_stripe_secret_key');
	$settings_stripe_public_key = get_option('settings_stripe_public_key');
}

if(empty($settings_stripe_secret_key) || empty($settings_stripe_public_key)){
	$error_msg = "Stripe settings error.";
} else {

	if(isset($_POST) && !empty($_POST) && isset($_POST['stripe_price']) && !empty($_POST['stripe_price'])){

		require_once(get_template_directory().'/inc/stripe/init.php');

		\Stripe\Stripe::setApiKey($settings_stripe_secret_key);

		$stripe_price = isset($_POST['stripe_price']) ? $_POST['stripe_price'] : '';
		$stripe_currency = isset($_POST['stripe_currency']) ? $_POST['stripe_currency'] : '';
		$bid_id = isset($_POST['bid_id']) ? $_POST['bid_id'] : '';
		$project_author_email = isset($_POST['project_author_email']) ? $_POST['project_author_email'] : '';
		$project_slug = isset($_POST['project_slug']) ? $_POST['project_slug'] : '';
		$project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
		$stripeToken = isset($_POST['stripeToken']) ? $_POST['stripeToken'] : '';
		$stripeTokenType = isset($_POST['stripeTokenType']) ? $_POST['stripeTokenType'] : '';
		$stripeEmail = isset($_POST['stripeEmail']) ? $_POST['stripeEmail'] : '';


		try {

			\Stripe\Stripe::setApiKey($stripe_key['secret_key']);

			$customer = \Stripe\Customer::create(array(
				'card' => $stripeToken,
				'description' => 'Customer from ' . home_url() ,
				'email' => $stripeEmail
			));

			$customer_id = $customer->id;

			$charge = \Stripe\Charge::create(array(
				'amount' => $stripe_price,
				'currency' => $stripe_currency,

				//'card' 		=> $token,
				'customer' => $customer_id
			));

			if($charge['status'] == 'succeeded') {

				wp_mail(get_site_option('admin_email'), 'Project payment', 'Hi admin,<br /><br />The project ('.get_the_title($project_id).'), project id: ('.$project_id.') is paid.');

				$project_paid_by_stripe = get_post_meta($project_id, 'project_paid_by_stripe', true);
				if(!empty($project_paid_by_stripe)){
					update_post_meta($project_id, 'project_paid_by_stripe', 'yes');
				} else {
					add_post_meta($project_id, 'project_paid_by_stripe', 'yes');
				}

				wp_redirect(get_home_url().'/project/'.$project_slug , 301); exit;
			}

		}
		catch(Exception $e) {
			$value  =   $e->getJsonBody();
			$error_msg =  $value['error']['message'];
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
