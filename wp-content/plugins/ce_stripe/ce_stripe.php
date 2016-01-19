<?php
/*
Plugin Name: CE Stripe
Plugin URI: http://www.enginethemes.com/
Description: A plugin to illustrate how to integrate Stripe and ClassifiedEngine
Author: dakachi
Author URI: http://enginethemes.com/
Contributors: EngineThemes Team
Version: 1.2
*/
require_once dirname(__FILE__).'/lib/Stripe.php';
require_once dirname(__FILE__) . '/update.php';

class CE_STRIPE {

	function __construct () { 

		$this->add_action ();
		register_deactivation_hook(__FILE__, array($this,'deactivation'));

	}

	private function  add_action () {

		add_action ('et_payment_settings_form', array ($this, 'stripe_setting'));
		add_action ('after_ce_payment_button',  array($this, 'stripe_payment_button'));
		add_filter( 'et_support_payment_gateway',array($this,'et_support_payment_gateway' ));
		//add_filter ('et_enable_gateway' , array($this, 'enable_gateway'));
		

		add_action('ce_on_add_scripts' , array($this, 'frontend_js'));
		add_action('ce_on_add_styles' , array($this, 'frontend_css'));

		add_action('wp_footer' , array($this, 'stripe_form_html'));

		add_filter ('et_update_payment_setting', array($this, 'set_settings' ), 10 ,3);
		
		// fillter return for ajax et_payment_process, run after submit form stripe.		
		add_filter ('et_payment_setup', array($this, 'setup_payment'), 10, 3);

		// filter for page-process-payement.php
		add_filter( 'et_payment_process', array($this, 'process_payment'), 10 ,3 );

		// set post_status publish add.
		//add_action('ce_payment_process',array($this,'ce_payment_process_stripe'),10,4);

		add_filter( 'et_get_translate_string', array($this, 'add_translate_string') );

		add_filter ('et_enable_gateway', array($this,'et_enable_stripe'), 10 , 2);


		add_action( 'after_ce_mobile_payment_button' , array ($this, 'mobile_pay') );

		add_action( 'et_mobile_footer' ,  array ($this,'mobile_add_script' ) );
		add_action( 'et_mobile_header' ,  array ($this,'mobile_header' ) );
		

		add_filter( 'ce_minify_source_path', array ($this, 'minify_source_path') );

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
		if(is_page_template('page-post-ad.php') || is_page_template( 'page-upgrade-account.php' ) ) 
			wp_enqueue_style( 'stripe_css',plugin_dir_url( __FILE__).'/stripe.css' );
	}

	function frontend_js () {

		$general_opts	= CE_Options::get_instance();
		$website_logo	= $general_opts->get_website_logo();
		$stripe			= $this->get_api();

		if( is_page_template( 'page-post-ad.php') ) {

			wp_enqueue_script( 'stripe.checkout', 'https://checkout.stripe.com/v2/checkout.js' );
			wp_enqueue_script( 'stripe', 'https://js.stripe.com/v1/');
			wp_enqueue_script('stripe.modal', plugin_dir_url( __FILE__).'/stripe.js',array('jquery','backbone','underscore','ce'),CE_VERSION, true );
			wp_localize_script( 'stripe.modal', 'ce_stripe', array(
					'public_key' 			=> $stripe["public_key"],
					'currency'	 			=> ET_Payment::get_currency(),
					'card_number_msg' 		=> __('The Credit card number is invalid.',ET_DOMAIN),
					'name_card_msg' 		=> __('The name on card is invalid.',ET_DOMAIN),
					'transaction_success' 	=> __('The transaction completed successfull!.',ET_DOMAIN),
					'transaction_false' 	=> __('The transaction was not completed successfull!.',ET_DOMAIN),
				)
			);
		}

	}
	/**
	 * Load form html of stripe.
	 */
	
	 
	function stripe_form_html(){
		$is_mobile = et_is_mobile();
        if($is_mobile)
            return;

		include_once dirname(__FILE__).'/form-template.php';
	}
	

	function setup_payment ( $response , $paymentType, $order ) {
		
		if( $paymentType == 'STRIPE') {

			$order_pay				=	$order->generate_data_to_pay (); 
			
			$token  = $_POST['token'];

			$job_id		=	$order_pay['product_id'];
			
			$stripe 	= $this->get_api();


			global $user_email;			
						
			try {
				
				Stripe::setApiKey($stripe['secret_key']);

				$customer 		= Stripe_Customer::create(array(
						  	'card' 			=> $token,
						  	'description' 	=> 'Customer from '.home_url(),
						  	'email'			=> $user_email
							)
						);

				$customer_id = $customer->id;


				$charge = Stripe_Charge::create(array(
					'amount'   	=> $order_pay['total']*100,
					'currency' 	=> $order_pay['currencyCodeType'],
					//'card' 		=> $token,
					'customer' 	=> $customer_id
					));

								
				$value	=	$charge->__toArray() ;
				$id		=	$value['id'];
				$token	=	md5($id);

				$order->set_payment_code ($token);
				$order->set_payer_id ($id);
				$order->update_order();

				$url	=	et_get_page_link('process-payment');

				global $wp_rewrite;
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

	function process_payment ( $payment_return, $order , $payment_type) {
		
		if( $payment_type == 'stripe') {

			if( isset($_REQUEST['token']) &&  $_REQUEST['token'] == $order->get_payment_code() ) {
				
				$payment_return	=	array (
					'ACK' 			=> true,
					'payment'		=>	'stripe',
					'payment_status' =>'Completed'
					
				);
				$order->set_status ('publish');
				$order->update_order();
				
			} else {				
				$payment_return	=	array (
					'ACK' 			=> false,
					'payment'		=>	'stripe',
					'payment_status' =>'Completed',
					'msg' 	=> __('Stripe payment method false.', ET_DOMAIN)
					
				);
			}
		}	
		return $payment_return;

	}
	// public function ce_payment_process_stripe($payment_return, $order, $payment_type, $session){
		
	// 	if( $payment_type == 'stripe' && $payment_return['ACK'] ){
	// 		$ad_id		=	$session['ad_id'];			
	// 		$ad = wp_update_post( array('ID' => $ad_id , 'post_status' => 'publish') );

	// 	}
	// }
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
		

		<li class="clearfix payment-item">
			<div class="f-left">
				<span class="name">
					<span class="fontsize17"><?php _e( 'Stripe', ET_DOMAIN )?> </span><br />
					<?php _e( 'Pay using your credit card through Stripe.', ET_DOMAIN )?>
					
				</span>
			</div>
			<div class="btn-select f-right">
				<button id="stripe_pay" class="bg-btn-hyperlink btn btn-primary" data-gateway="stripe" ><?php _e('Select', ET_DOMAIN );?></button>
			</div>
		</li>
		
	<?php
	}

	function mobile_pay ($payment_gateways) {
		if(!isset($payment_gateways['stripe']))  return;
			$stripe	=	$payment_gateways['stripe'];
			if( !isset($stripe['active']) || $stripe['active'] == -1) return ;
			
		?>	
			<style type="text/css">
				.post-new-classified.stripe a {
					color: #FFF;
					width: 100%;
					font-weight: 600;
					font-size: 16px;
					border: none;
					-moz-box-shadow: inset 0 -3px 3px #327dbd;
					-webkit-box-shadow: inset 0 -3px 3px #327DBD;
					box-shadow: inset 0 -3px 3px #327DBD;
					background-color: #3783C5;
					background-image: -moz-linear-gradient(bottom, #3783c5 0%, #3783c5 100%);
					background-image: -o-linear-gradient(bottom, #3783c5 0%, #3783c5 100%);
					background-image: -webkit-linear-gradient(bottom, #3783C5 0%, #3783C5 100%);
					background-image: linear-gradient(bottom, #3783c5 0%, #3783c5 100%);
					border-radius: 3px !important;
					-moz-border-radius: 3px !important;
					-webkit-border-radius: 3px !important;
					padding: 10px 0 10px 0;
					margin-left: 0;
					text-decoration: none;
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

	function mobile_header () {
		if(is_page_template( 'page-post-ad.php' ) ) {
			$stripe_api	= $this->get_api();
			$currency 	= ET_Payment::get_currency();
				
	?>	

		<script type="text/javascript">
	        var ce_stripe = {
	            'public_key' 		 	: "<?php echo $stripe_api["public_key"]; ?>",
				'currency'	 			: {<?php foreach($currency as $key=>$value){ echo '"'.$key.'":"'.$value.'",';}?>},
				'card_number_msg' 		: "<?php echo __('The Credit card number is invalid.',ET_DOMAIN); ?>",
				'name_card_msg' 		: "<?php echo __('The name on card is invalid.',ET_DOMAIN); ?>",
				'transaction_success' 	: "<?php echo __('The transaction completed successfull!.',ET_DOMAIN); ?>",
				'transaction_false' 	: "<?php echo __('The transaction was not completed successfull!.',ET_DOMAIN); ?>",
	        };
	    </script>
	    <script type="text/javascript" src="https://checkout.stripe.com/v2/checkout.js"></script>
		<script type="text/javascript" src="https://js.stripe.com/v1/"></script>

	<?php 
		}
	}

	function mobile_add_script () {
		if(is_page_template( 'page-post-ad.php' ) ) { 
			if( !get_theme_mod( 'ce_minify' , '' ) ) { ?> 
				<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__).'/mobile-stripe.js' ?>"></script>
			<?php }
		}
	}

	function minify_source_path ($minify_path) {
		array_push($minify_path['mobile-js'] , plugin_dir_path( __FILE__ ).'/mobile-stripe.js' ) ;
		return $minify_path;
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
					<input class="payment-item bg-grey-input <?php if($stripe_api['secret_key'] == '') echo 'color-error' ?>" name="stripe-secret-key" type="text" value="<?php echo $stripe_api['secret_key']  ?> " />
					<span class="icon <?php if($stripe_api['secret_key'] == '') echo 'color-error' ?>" data-icon="<?php  data_icon($stripe_api['secret_key']) ?>"></span>
				</div>
				<div class="form-item">
					<div class="label">
						<?php _e("Your stripe public key",ET_DOMAIN);?>
						
					</div>
					<input class="payment-item bg-grey-input <?php if($stripe_api['public_key'] == '') echo 'color-error' ?>" type="text" name="stripe-public-key" value="<?php echo $stripe_api['public_key'] ?> " />
					<span class="icon <?php if($stripe_api['public_key'] == '') echo 'color-error' ?>" data-icon="<?php  data_icon($stripe_api['public_key']) ?>"></span>
				</div>
			</div>
		</div>
	<?php 
	}

}

new CE_STRIPE ();