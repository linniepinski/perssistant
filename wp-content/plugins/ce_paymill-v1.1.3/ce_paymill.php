<?php
/*
Plugin Name: CE Paymill
Plugin URI: http://www.enginethemes.com/
Description: A plugin to illustrate how to integrate paymill and ClassifiedEngine
Author: dakachi
Author URI: http://enginethemes.com/
Contributors: EngineThemes Team
Version: 1.1.3 

*/
define('CE_PAYMILL_PATH',dirname(__FILE__));
define('CE_PAYMILL_URL', plugins_url( basename(dirname(__FILE__)) ));

require_once dirname(__FILE__) . '/lib/Services/Paymill.php';
require_once dirname(__FILE__) . '/update.php';

class CE_PAYMILL {
	private static $api_endpoint = 'https://api.paymill.com/v2/' ;
	function __construct () { 

		$this->add_action ();
		register_deactivation_hook(__FILE__, array($this,'deactivation'));

	}

	private function  add_action () {

		add_action ('et_payment_settings_form', array ($this, 'paymill_setting'));
		add_action ('after_ce_payment_button',  array($this, 'paymill_payment_button'));

		

		//after_ce_mobile_payment_button
		add_filter( 'et_support_payment_gateway',array($this,'et_support_payment_gateway' ));
		//add_filter ('et_enable_gateway' , array($this, 'enable_gateway'));
		
		add_action('ce_on_add_scripts' , array($this, 'frontend_js'));
		add_action('ce_on_add_styles' , array($this, 'frontend_css'));

		add_action('wp_footer' , array($this, 'load_form_paymill'));
		


		add_filter ('et_update_payment_setting', array($this, 'set_settings' ), 10 ,3);
		
		// fillter return for ajax et_payment_process, run after submit form paymill.		
		add_filter ('et_payment_setup', array($this, 'setup_payment'), 10, 3);

		// filter for page-process-payement.php
		add_filter( 'et_payment_process', array($this, 'process_payment'), 10 ,3 );

		// set post_status publish add.
		//add_action('ce_payment_process',array($this,'ce_payment_process_paymill'),10,4);

		add_filter( 'et_get_translate_string', array($this, 'add_translate_string') );

		add_filter ('et_enable_gateway', array($this,'et_enable_paymill'), 10 , 2);

		//mobile 
		//add button payment by paymil in payment process when user using mobile.
		add_action('after_ce_mobile_payment_button',array($this,'paymill_payment_button_mobile'));
		add_action( 'et_mobile_header' ,  array ($this,'mobile_header' ) );
		add_action('et_mobile_footer', array($this,'frontend_js_mobile'));
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
		$paymill_api	=	$this->get_api();
		if($paymill_api['secret_key'] == '' ) return false;
		if($paymill_api['public_key'] == '' ) return false;
		return true;
	}

	function et_enable_paymill ($available , $gateway) {
		if($gateway == 'paymill') {
			if($this->is_enable ()) return true; 
			return false;
		}
		return $available;
	}
	/**
	 * get paymill api setting
	*/
	function get_api () {
		return 	get_option( 'ce_paymill_api', array('secret_key' => '', 'public_key' => '') );
	}
	/**
	 * update paymill api setting
	*/
	function set_api ( $api ) {
		update_option('ce_paymill_api', $api );
		if(!$this->is_enable()) {
			$gateways	=	ET_Payment::get_gateways();
			if(isset($gateways['paymill']['active']) && $gateways['paymill']['active'] != -1 ) {
				ET_Payment::disable_gateway('paymill');
				return __('Api setting invalid', ET_DOMAIN);
			}
		}
		return true;
	}
	/**
	 * ajax callback to update payment settings
	*/
	function set_settings ( $msg , $name, $value ) {
		$paymill_api	=	$this->get_api();
		switch ($name) {
			case 'PAYMILL-SECRET-KEY':
				$paymill_api['secret_key']	=	trim($value);
				$msg	=	$this->set_api( $paymill_api );
				break;
			case 'PAYMILL-PUBLIC-KEY':
				$paymill_api['public_key']	=	trim($value);
				$msg	=	$this->set_api( $paymill_api );
				break;
		}

		return $msg;
	}

	function frontend_css  () {
		if(is_page_template('page-post-ad.php') || is_page_template( 'page-upgrade-account.php' ) ) 
			wp_enqueue_style( 'paymill_css',plugin_dir_url( __FILE__).'/paymill.css' );
		$paymill		= $this->get_api();
		
		?>
	
		<script type="text/javascript"> var PAYMILL_PUBLIC_KEY ='<?php echo $paymill["public_key"];?>';</script>
		
		<?php 
		//wp_enqueue_script( 'paymill-root', 'https://bridge.paymill.com/',array('jquery') );
	}

	function frontend_js () {

		$general_opts	= CE_Options::get_instance();
		$website_logo	= $general_opts->get_website_logo();
		$paymill		= $this->get_api();

		if( is_page_template( 'page-post-ad.php') ) {
			
			wp_enqueue_script('paymill-root', 'https://bridge.paymill.com/');			
			wp_enqueue_script( 'paymill-modal', plugin_dir_url( __FILE__).'paymill.js', array('jquery','backbone', 'underscore', 'ce'),false,true );
			
			wp_localize_script( 'paymill-modal', 'ce_paymill', array(
					'public_key' 			=> $paymill["public_key"],
					'PAYMILL_PUBLIC_KEY' 	=> $paymill["public_key"],
					'currency'	 			=> ET_Payment::get_currency(),
					'card_number_msg' 		=>__('Card number is invalid.',ET_DOMAIN),
					'cvc_msg' 				=>__('Card code is invalid.',ET_DOMAIN),
					'exp_msg' 				=>__('The credit card expiration date is invalid .',ET_DOMAIN),
					'public_key_msg' 		=> __('Invalid public key.',ET_DOMAIN),
					'unknow_error' 			=> __('Unknow error',ET_DOMAIN),
					'name_card_msg' 		=> __('Name on card is invalid.',ET_DOMAIN),

				)
			);			

		}

	}

	function load_form_paymill(){

		$is_mobile = et_is_mobile();
        if($is_mobile)
            return;

       	include_once dirname(__FILE__).'/form-template.php';
    }

	function mobile_header(){
		
		
		$paymill		= $this->get_api();
		$currency 		= ET_Payment::get_currency(); 
		if( is_page_template( 'page-post-ad.php') ) {
			
			//wp_enqueue_script('paymill-root', 'https://bridge.paymill.com/');			
			//wp_enqueue_script( 'paymill-modal', plugin_dir_url( __FILE__).'paymill.js',array('paymill-root') );
			?>
			<script type="text/javascript"> var PAYMILL_PUBLIC_KEY ='<?php echo $paymill["public_key"];?>';</script>
			<script type="text/javascript">
			var ce_paymill= {
					'public_key' 			: "<?php echo $paymill["public_key"];?>",
					'PAYMILL_PUBLIC_KEY' 	: "<?php echo $paymill["public_key"]; ?>",
					'currency'	 			: {<?php foreach($currency as $key=>$value){ echo '"'.$key.'":"'.$value.'",';}?>},
					'card_number_msg' 		: "<?php _e('Card number is invalid.',ET_DOMAIN);?>",
					'cvc_msg' 				: "<?php _e('Card code is invalid.',ET_DOMAIN);?>",
					'exp_msg' 				: "<?php _e('The credit card expiration date is invalid .',ET_DOMAIN) ?>",
					'public_key_msg' 		: "<?php _e('Invalid public key.',ET_DOMAIN) ?>",
					'unknow_error' 			: "<?php _e('Unknow error',ET_DOMAIN)?>",
					'name_card_msg' 		: "<?php _e('Name on card is invalid.',ET_DOMAIN)?>",

			}
			</script>
			<?php 
		}

	}

	function frontend_js_mobile () { 
		if(is_page_template( 'page-post-ad.php' ) ) { ?>

			<script type="text/javascript" src="https://bridge.paymill.com/"></script> <?php 		
			if( !get_theme_mod( 'ce_minify' , '' ) ) { ?> 			
				<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__).'/mobile_paymill.js' ?>"></script><?php 
			}
			
		}
	}

	function setup_payment ( $response , $paymentType, $order ) {

		if( $paymentType == 'PAYMILL') {

			$order_pay	=	$order->generate_data_to_pay();

			$token  		= $_POST['token'];
			$job_id			= $order_pay['product_id'];	
			$description 	= isset($_POST['description']) ? $_POST['description'] : '';
			$paymill_api	= $this->get_api();
			
			try {
	
				$params = array(
						'token' =>$token
				);
				$apiKey         = $paymill_api['secret_key'];
				$apiEndpoint    = self::$api_endpoint;
				$paymill 		= new Services_Paymill_Transactions( $apiKey , $apiEndpoint );
		     	
				//$creditcard = $paymentsObject->create($params);	
				
				//$Paymill->update();
				//Paymill::setApiKey($paymill['secret_key']);
				$order_pay	=	$order->generate_data_to_pay ();
				$charge 	= 	$paymill->create(
									array(
										'amount'   	=> $order_pay['total'] * 100,
										'currency' 	=> $order_pay['currencyCodeType'],
										'token' 	=> $token,
										'description' => $description
							      	)
								);
				
				$returnURL	=	et_get_page_link('process-payment', array( 'paymentType' => 'paymill' /*, 'trans' => $charge['id']*/));

				if(isset($charge['error']) || !isset($charge['id'])){
					$response	=	array(
						'success'	=>	false,
						'msg'		=> __('Transaction was not completed successfully!',ET_DOMAIN),
						'data'		=>	array ('url' => $returnURL ),
						'paymentType'	=>	'paymill'
				
					);

				} else if(isset($charge['id'])) {

					
					$id		=	$charge['id'];
					$token	=	md5($id);
					$order->set_payment_code ($token);
					$order->set_payer_id ($id);
					$order->update_order();

					
					$returnURL .= '&token='.$id;
					
					$response	=	array(
						'success'	=>	true,
						'data'		=>	array ('url' => $returnURL , 'msg' 		=> __('Transaction completed successfull!', ET_DOMAIN) ),
						'paymentType'	=>	'paymill'
					);

				}				
				
			}  catch (Exception $e) {
				$value	=	$e->getJsonBody();
					
				$response	=	array(
						'success'	=>	false,
						'msg'	=> $value['error']['message'],
						'paymentType'	=>	'paymill'
	
				);
			}
	
		}
		return $response;
	}
	function minify_source_path($minify_path){
		array_push($minify_path['mobile-js'] , plugin_dir_path( __FILE__ ).'/mobile_paymill.js' ) ;
		return $minify_path;
	}

	function process_payment ( $payment_return, $order , $payment_type) {
	
		if( $payment_type == 'paymill') {
			$token = isset($_REQUEST['token']) ? md5($_REQUEST['token']) : '';
			if( $token == $order->get_payment_code() ) {
				
				$payment_return	=	array (
					'ACK' 			=> true,
					'payment'		=>	'paymill',
					'payment_status' =>'Completed'
					
				);
				$order->set_status ('publish');
				$order->update_order();
				
			} else {				
				$payment_return	=	array (
					'ACK' 			=> false,
					'payment'		=>	'paymill',
					'payment_status' =>'Completed',
					'msg' 	=> __('Paymill transaction was not completed.', ET_DOMAIN)
					
				);
			}
			
		}	
		return $payment_return;

	}
	public function ce_payment_process_paymill($payment_return, $order, $payment_type, $session){
		
		if($payment_type == 'paymill' && $payment_return['ACK']){
			$ad_id		=	$session['ad_id'];			
			$ad = wp_update_post( array('ID' => $ad_id , 'post_status' => 'publish') );

		}
	}
	/**
	 * deactive payment when deactive plugin
	*/
	function deactivation () {
		//ET_Payment::disable_gateway('paymill');
	}
	/**
	 * render paymill checkout button
	*/
	function paymill_payment_button ($payment_gateways) {
		if(!isset($payment_gateways['paymill']))  return;
		$paymill	=	$payment_gateways['paymill'];
		if( !isset($paymill['active']) || $paymill['active'] == -1) return ;
	?>
		<li class="clearfix payment-item">
			<div class="f-left">
				<span class="name">
					<span class="fontsize17"><?php _e( 'Paymill', ET_DOMAIN )?> </span><br />
					<?php _e( 'Pay using your credit card through Paymill.', ET_DOMAIN )?>
					
				</span>
			</div>
			<div class="btn-select f-right">
				<button id="paymill_pay" class="bg-btn-hyperlink btn btn-primary" data-gateway="paymill" ><?php _e('Select', ET_DOMAIN );?></button>
			</div>
		</li>
		
	<?php
	}
	

	/**
	 * render paymill checkout button for mobile os
	*/
	function paymill_payment_button_mobile ($payment_gateways) {
		if(!isset($payment_gateways['paymill']))  return;
		$paymill	=	$payment_gateways['paymill'];
		if( !isset($paymill['active']) || $paymill['active'] == -1) return ;
	?>
	<style type="text/css">
				.post-new-classified.paymill a {
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
	<div data-role="fieldcontain" class="post-new-classified paymill" >
		
		<?php _e( 'Pay using your credit card through Paymill.', ET_DOMAIN )?>
		<a href="#paymill-modal" data-rel="popup" data-position-to="window" class="ui-btn ui-corner-all ui-shadow ui-btn-inline" >
			<?php _e( 'Paymill', ET_DOMAIN ); ?>
		</a>
	</div>
	
	<div data-role="popup" id="paymill-modal">
			<?php include_once dirname(__FILE__).'/form-template.php'; ?>
		</div>
	<?php
	}

	// add paymill to je support payment
	function et_support_payment_gateway ( $gateway ) {
		$gateway['paymill']	=	array (
									'label' 		=> __("paymill",ET_DOMAIN),  
									'description'	=> __("Send your payment through paymill", ET_DOMAIN),
									'active' 		=> -1
									);
		return $gateway;
	}
	/**
	 * render paymill settings form
	*/
	function paymill_setting () {
		$paymill_api = $this->get_api();
	?>
		<div class="item">
			<div class="payment">
				<a class="icon" data-icon="y" href="#"></a>
				<div class="button-enable font-quicksand">
					<?php et_display_enable_disable_button('paymill', 'Paymill')?>
				</div>
				<span class="message"></span>
				<?php _e("Paymill",ET_DOMAIN);?>
			</div>
			<div class="form payment-setting">
				<div class="form-item">
					<div class="label">
						<?php _e("Your paymill secret key ",ET_DOMAIN);?> 
					</div>
					<input class="payment-item bg-grey-input <?php if($paymill_api['secret_key'] == '') echo 'color-error' ?>" name="paymill-secret-key" type="text" value="<?php echo $paymill_api['secret_key']  ?> " />
					<span class="icon <?php if($paymill_api['secret_key'] == '') echo 'color-error' ?>" data-icon="<?php  data_icon($paymill_api['secret_key']) ?>"></span>
				</div>
				<div class="form-item">
					<div class="label">
						<?php _e("Your paymill public key",ET_DOMAIN);?>
						
					</div>
					<input class="payment-item bg-grey-input <?php if($paymill_api['public_key'] == '') echo 'color-error' ?>" type="text" name="paymill-public-key" value="<?php echo $paymill_api['public_key'] ?> " />
					<span class="icon <?php if($paymill_api['public_key'] == '') echo 'color-error' ?>" data-icon="<?php  data_icon($paymill_api['public_key']) ?>"></span>
				</div>
			</div>
		</div>
	<?php 
	}

}

new CE_PAYMILL ();