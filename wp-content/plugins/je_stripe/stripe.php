<?php
/*
Plugin Name: JE Stripe
Plugin URI: http://enginethemes.com/
Description: A plugin to illustrate how to integrate Stripe and Jobengine
Author: dakachi
Author URI: http://enginethemes.com/
Contributors: EngineThemes Team
Version: 1.4.2
*/
require_once dirname(__FILE__).'/lib/Stripe.php';
require_once dirname(__FILE__) . '/update.php';

class JE_STRIPE {

	function __construct () {
		$this->add_action ();
		register_deactivation_hook(__FILE__, array($this,'deactivation'));
	}

	private function  add_action () {
		add_action ('je_payment_settings', array ($this, 'stripe_setting'));
		add_action ('after_je_payment_button', array($this, 'stripe_payment_button'));
		add_filter( 'et_support_payment_gateway',array($this,'et_support_payment_gateway' ));
		//add_filter ('et_enable_gateway' , array($this, 'enable_gateway'));
		add_action('wp_footer' , array($this, 'frontend_js'));
		add_action('wp_head' , array($this, 'frontend_css'));

		add_filter ('et_update_payment_setting', array($this, 'set_settings' ), 10 ,3);

		add_filter ('je_payment_setup', array($this, 'setup_payment'), 10, 3);

		add_filter( 'je_payment_process', array($this, 'process_payment'), 10 ,3 );

		add_filter( 'et_get_translate_string', array($this, 'add_translate_string') );

		add_filter ('et_enable_gateway', array($this,'et_enable_stripe'), 10 , 2);

		// update for mobile version 04/04/2014
		add_action('after_je_mobile_payment_button',array($this,'add_stripe_button_mobile'));
		add_action('et_mobile_head',array($this,'stripe_mobile_header'));
		add_action('et_mobile_footer',array($this,'stripe_mobile_footer'));

	}
	function add_translate_string ($entries) {
		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/default.po',true );
		return	array_merge($entries, $pot->entries);
	}
	/**
	 * check payment setting is enable or not
	*/
	function is_enable () {
		$stripe_api	=	$this->get_api();
		if($stripe_api['secret_key'] == '' ) return false;
		if($stripe_api['public_key'] == '' ) return false;
		return true;
	}

	function et_enable_stripe ($available , $gateway) {
		if($gateway == 'stripe') {
			if($this->is_enable ()) return true; 
			return false;
		}
		return $available;
	}
	/**
	 * get stripe api setting
	*/
	function get_api () {
		return 	get_option( 'et_stripe_api', array('secret_key' => '', 'public_key' => '') );
	}
	/**
	 * update stripe api setting
	*/
	function set_api ( $api ) {
		update_option('et_stripe_api', $api );
		if(!$this->is_enable()) {
			$gateways	=	ET_Payment::get_gateways();
			if(isset($gateways['stripe']['active']) && $gateways['stripe']['active'] != -1 ) {
				ET_Payment::disable_gateway('stripe');
				return __('Api setting invalid', ET_DOMAIN);
			}
		}
		return true;
	}
	/**
	 * ajax callback to update payment settings
	*/
	function set_settings ( $msg , $name, $value ) {
		$stripe_api	=	$this->get_api();
		switch ($name) {
			case 'STRIPE-SECRET-KEY':
				$stripe_api['secret_key']	=	trim($value);
				$msg	=	$this->set_api( $stripe_api );
				break;
			case 'STRIPE-PUBLIC-KEY':
				$stripe_api['public_key']	=	trim($value);
				$msg	=	$this->set_api( $stripe_api );
				break;
		}

		return $msg;
	}

	function frontend_css  () {
		if(is_page_template('page-post-a-job.php') || is_page_template( 'page-upgrade-account.php' ) ) 
			wp_enqueue_style( 'stripe_css',plugin_dir_url( __FILE__).'/stripe.css' );
	}

	function frontend_js () {

		$general_opts	= new ET_GeneralOptions();
		$website_logo	= $general_opts->get_website_logo();
		$stripe			= $this->get_api();

		if(is_page_template('page-post-a-job.php') || is_page_template( 'page-upgrade-account.php' )) {
			wp_enqueue_script('stripe.checkout', 'https://checkout.stripe.com/v2/checkout.js', array('jquery'));
			wp_enqueue_script('stripe', 'https://js.stripe.com/v1/');
			wp_enqueue_script('stripe.modal', plugin_dir_url( __FILE__).'/stripe.js', array('jquery'));
			wp_localize_script( 'stripe.modal', 'je_stripe', array(
					'public_key' => $stripe["public_key"],
					'currency'	 => ET_Payment::get_currency()
				)
			);

			include_once dirname(__FILE__).'/form-template.php';

		}

	}

	function process_payment ( $payment_return, $order , $payment_type ) {
		if( $payment_type == 'stripe' ) {
			if( isset($_REQUEST['token']) &&  $_REQUEST['token'] == $order->get_payment_code() ) {
				$payment_return	=	array (
					'ACK' 		=> true,
					'payment'	=>	'stripe'
				);
				$order->set_status ('publish');
				$order->update_order();

			}
		}
		return $payment_return;

	}

	function setup_payment ( $response , $paymentType, $order ) {
		if( $paymentType == 'STRIPE') {

			$order_pay				=	$order->generate_data_to_pay ();
			// echo "<pre>";
			// print_r($order_pay) ;
			// echo "</pre>";
			$token  = $_POST['token'];

			$job_id		=	$order_pay['product_id'];
			$stripe = $this->get_api();
			global $user_email;

			try {

				Stripe::setApiKey($stripe['secret_key']);

				$customer 	= Stripe_Customer::create(array(
							  	'card' 			=> $token,
							  	'description' 	=> 'Customer from '.home_url(),
							  	'email'			=> $user_email
								)
							);

				$customer_id = $customer->id;


				$charge = Stripe_Charge::create(array(
					'amount'   => $order_pay['total']*100,
					'currency' => $order_pay['currencyCodeType'],
					//'card' => $token,
					'customer' 	=> $customer_id
				));
				$value	=	$charge->__toArray() ;
				$id		=	$value['id'];
				$token	=	md5($id);

				$order->set_payment_code ($token);
				$order->set_payer_id ($id);
				$order->update_order();

				$returnURL	=	et_get_page_link('process-payment', array( 'paymentType' => 'stripe', 'token' => $token));
				$response	=	array(
					'success'	=>	true,
					'data'		=>	array ('url' => $returnURL ),
					'paymentType'	=>	'stripe'
				);

			}  catch (Exception $e) {

				$value	=	$e->getJsonBody();
				$response	=	array(
					'success'	=>	false,
					'msg'	=> $value['error']['message'],
					'paymentType'	=>	'stripe'
				);
			}

		}
		return $response;
	}

	/**
	 * deactive payment when deactive plugin
	*/
	function deactivation () {
		//ET_Payment::disable_gateway('stripe');
	}
	/**
	 * render stripe checkout button
	*/
	function stripe_payment_button ($payment_gateways) {
		if(!isset($payment_gateways['stripe']))  return;
		$stripe	=	$payment_gateways['stripe'];
		if( !isset($stripe['active']) || $stripe['active'] == -1) return ;
	?>
		<li class="clearfix">
			<div class="f-left">
				<div class="title"><?php _e( 'Stripe', ET_DOMAIN )?></div>
				<div class="desc"><?php _e( 'Pay using your credit card through Stripe.', ET_DOMAIN )?></div>
			</div>
			<div class="btn-select f-right">
				<button id="stripe_pay" class="bg-btn-hyperlink border-radius" data-gateway="stripe" ><?php _e('Select', ET_DOMAIN );?></button>
			</div>
		</li>
	<?php
	}


	// mobile
	function add_stripe_button_mobile($payment_gateways){
		if(!isset($payment_gateways['stripe']))  return;

		$stripe	=	$payment_gateways['stripe'];
		if( !isset($stripe['active']) || $stripe['active'] == -1) return ;
		?>
		<style type="text/css">
			.post-new-classified{
				padding: 20px 15px;
			}
			.post-new-classified.stripe a {
				background: -moz-linear-gradient(center top , #FEFEFE 0%, #FAFAFA 16%, #F0F0F0 85%, #E0E0E0 100%) repeat scroll 0 0 rgba(0, 0, 0, 0);
				border: 1px solid #BABABA;
				box-shadow: 1px 0 1px 0 #E3E3E3;
				color: #777777 !important;
				text-align: center;
				text-shadow: 0 -1px 0 #EFEFEF !important;

				display: block;
				clear: both;
				overflow: hidden;
				font-size: 16px;
				min-width: 0.75em;
				overflow: hidden;
				padding: 0.9em 20px;
				position: relative;
				text-overflow: ellipsis;
				white-space: nowrap;
				font-weight: bold;
				text-decoration: none;
				margin: 10px 0 0 0;
			}
		</style>
		<div data-role="fieldcontain" class="post-new-classified stripe" >

			<?php _e( 'Pay using your credit card through Stripe.', ET_DOMAIN )?>
			<a href="#stripe-modal" data-rel="popup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" >
				<?php _e( 'Stripe', ET_DOMAIN ); ?>
			</a>
		</div>

		<div data-role="popup" id="stripe-modal">
				<?php include_once dirname(__FILE__).'/form-template.php'; ?>
		</div>
		<?php

	}
	function stripe_mobile_header(){
		$stripe		= $this->get_api();
		$currency 	= ET_Payment::get_currency() ;

		if(is_page_template('page-post-a-job.php') || is_page_template( 'page-upgrade-account.php' )) { ?>
			<script type="text/javascript">
				var je_stripe = {
						'public_key' : "<?php echo $stripe["public_key"];?>",
						'currency'	 	: {<?php foreach($currency as $key=>$value){ echo '"'.$key.'":"'.$value.'",';}?>},
					}
			</script>
			<?php
		}

	}
	function stripe_mobile_footer(){
		if(is_page_template('page-post-a-job.php') || is_page_template( 'page-upgrade-account.php' )) {
			?>
			<script type ="text/javascript" src="https://checkout.stripe.com/v2/checkout.js"></script>
			<script type ="text/javascript" src="https://js.stripe.com/v1/"></script>
			<script type ="text/javascript" src="<?php echo plugin_dir_url( __FILE__).'/mobile_stripe.js'; ?>" /></script>
			<?php
		}
	}

	// add stripe to je support payment
	function et_support_payment_gateway ( $gateway ) {
		$gateway['stripe']	=	array (
									'label' 		=> __("Stripe",ET_DOMAIN),
									'description'	=> __("Send your payment through Stripe", ET_DOMAIN),
									'active' 		=> -1
									);
		return $gateway;
	}
	/**
	 * render stripe settings form
	*/
	function stripe_setting () {
		$stripe_api = $this->get_api();
	?>
		<div class="item">
			<div class="payment">
				<a class="icon" data-icon="y" href="#"></a>
				<div class="button-enable font-quicksand">
					<?php et_display_enable_disable_button('stripe', 'Stripe')?>
				</div>
				<span class="message"></span>
				<?php _e("Stripe",ET_DOMAIN);?>
			</div>
			<div class="form payment-setting">
				<div class="form-item">
					<div class="label">
						<?php _e("Your stripe secret key ",ET_DOMAIN);?> 
					</div>
					<input class="bg-grey-input <?php if($stripe_api['secret_key'] == '') echo 'color-error' ?>" name="stripe-secret-key" type="text" value="<?php echo $stripe_api['secret_key']  ?> " />
					<span class="icon <?php if($stripe_api['secret_key'] == '') echo 'color-error' ?>" data-icon="<?php  data_icon($stripe_api['secret_key']) ?>"></span>
				</div>
				<div class="form-item">
					<div class="label">
						<?php _e("Your stripe public key",ET_DOMAIN);?>
					</div>
					<input class="bg-grey-input <?php if($stripe_api['public_key'] == '') echo 'color-error' ?>" type="text" name="stripe-public-key" value="<?php echo $stripe_api['public_key'] ?> " />
					<span class="icon <?php if($stripe_api['public_key'] == '') echo 'color-error' ?>" data-icon="<?php  data_icon($stripe_api['public_key']) ?>"></span>
				</div>
			</div>
		</div>
	<?php
	}

}

new JE_STRIPE ();