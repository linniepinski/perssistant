<?php
/*
Plugin Name: JE Paymill
Plugin URI: http://enginethemes.com/
Description: Integrate Paymill gateway into your Jobengine-powered job board
Author: EngineThemes Team
Author URI: http://enginethemes.com/
Contributors: EngineThemes Team
Version: 1.1
*/
require_once dirname(__FILE__) . '/lib/Services/Paymill.php';
require_once dirname(__FILE__) . '/update.php';

/**
 * render paymill settings form
 */
class JE_PAYMILL
{
	private static $api_endpoint = 'https://api.paymill.com/v2/' ;
	function __construct () {
		$this->add_action ();
		//register_deactivation_hook(__FILE__, array($this,'deactivation'));
	}
	private function  add_action () {
		add_action ('je_payment_settings', array ($this, 'paymill_setting'));
		add_action ('after_je_payment_button', array($this, 'paymill_payment_button'));
		add_filter( 'et_support_payment_gateway',array($this,'et_support_payment_gateway' ));
		add_action('wp_footer' , array($this, 'frontend_js'));
		add_action('wp_head' , array($this, 'frontend_css'));
		//add_action('wp_head' , array($this, 'add_paymill_bublic_key'));
		add_filter ('et_update_payment_setting', array($this, 'set_settings' ), 10 ,3);
	
		add_filter ('je_payment_setup', array($this, 'setup_payment'), 10, 3);
	
		add_filter( 'je_payment_process', array($this, 'process_payment'), 10 ,3 );
	
		add_filter( 'et_get_translate_string', array($this, 'add_translate_string') );
	
		add_filter ('et_enable_gateway', array($this,'et_enable_paymill'), 10 , 2);

		// update for mobile version 04/04/2014
		add_action('after_je_mobile_payment_button',array($this,'add_paymill_button_mobile'));
		add_action('et_mobile_head',array($this,'paymill_mobile_header'));
		add_action('et_mobile_footer',array($this,'paymill_mobile_footer'));
	
	}

	function add_translate_string ($entries) {
		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/default.po',true );
		
		return	array_merge($entries, $pot->entries);
	}
	
	function frontend_css  () {
		if(is_page_template('page-post-a-job.php') || is_page_template('page-upgrade-account.php') ) {
			wp_enqueue_style( 'paymill_css',plugin_dir_url( __FILE__).'/paymill.css' );
			$paypill			= $this->get_api();
		 	?>
		 	 <script type="text/javascript"> var PAYMILL_PUBLIC_KEY ='<?php echo $paypill["public_key"]; ?>';</script>
		 	<?php
		 }
		  
	}
	
	function frontend_js () {
	
		$general_opts	= new ET_GeneralOptions();
		$website_logo	= $general_opts->get_website_logo();
		$paypill		= $this->get_api();
	    
		if(is_page_template('page-post-a-job.php') || is_page_template('page-upgrade-account.php')) {
			wp_enqueue_script('paymill_checkout', 'https://bridge.paymill.com/', array('jquery'));
			//wp_enqueue_script('paypill', 'https://js.stripe.com/v1/');
			wp_enqueue_script('paymill_modal', plugin_dir_url( __FILE__).'/paymill.js', array('jquery'));
			wp_localize_script( 'paymill_modal', 'je_paymill', array(
					'PAYMILL_PUBLIC_KEY' => $paypill["public_key"],
					'currency'	 	=> ET_Payment::get_currency() ,
					'invalid_card'	=> __("Your card number is incorrect.", ET_DOMAIN) ,
					'invalid_date'	=> __("Your card's expiry date is invalid.", ET_DOMAIN),
					'invalid_cvc'	=> __("Your card's code is invalid.", ET_DOMAIN) ,
					'invalid_account_holder' => __("Account holder is invalid.", ET_DOMAIN),
					'unknow_error'	=> __("Unknown error!!!", ET_DOMAIN)
			)
			);
	
			include_once dirname(__FILE__).'/form-template.php';
	        
		}
	
	}
	
	/**
	 * check payment setting is enable or not
	 */
	//et_enable_gateway();

	function is_enable () {
		$paymill_api	=	$this->get_api();
		
		if($paymill_api['secret_key'] == '' ) return false;
		if($paymill_api['public_key'] == '' ) return false;
		return true;
	}
	
	function et_enable_paymill ($available , $gateway) {
		// echo $this->alert($gateway);
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
		return 	get_option( 'et_paymill_api', array('secret_key' => '', 'public_key' => '') );
	}
	/**
	 * update paymill api setting
	 */
	function set_api ( $api ) {
		update_option('et_paymill_api', $api );
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

	function process_payment ( $payment_return, $order , $payment_type) {

		$paymill_api	= $this->get_api();		
		$apiKey         = $paymill_api['secret_key'];
		$apiEndpoint    = self::$api_endpoint;

		//$paymill = new Services_Paymill_Transactions( $apiKey , $apiEndpoint );
		//$trans_id = (isset($_REQUEST['trans'])) ? $_REQUEST['trans'] : '';
		//$info = $paymill->getOne($trans_id);	
		if($payment_type == 'paymill' ) {
			if( isset($_REQUEST['token']) && $_REQUEST['token'] == $order->get_payment_code() ) {

				$payment_return	=	array (
						'ACK' 		=> true,
						'payment'	=>	'paymill',
						//'info'		=> $info
				);
				$order->set_status ('publish');
				$order->update_order();
	
			}
		}
		return $payment_return;
	
	}
	
	function setup_payment ( $response , $paymentType, $order ) {
		//$this->alert('ok');
		
		if( $paymentType == 'PAYMILL') {

			$order_pay	=	$order->generate_data_to_pay();

			$token  = $_POST['token'];
			$job_id	= $order_pay['product_id'];	
			$description = $_POST['description'];
			$paymill_api	=	$this->get_api();
			
			try {
	
				$params = array(
						'token' =>$token
				);
				$apiKey         = $paymill_api['secret_key'];
				$apiEndpoint    = self::$api_endpoint;
				$paymill = new Services_Paymill_Transactions( $apiKey , $apiEndpoint );
		     	
				//$creditcard = $paymentsObject->create($params);	
				
				//$Paymill->update();
				//Paymill::setApiKey($paymill['secret_key']);
				$order_pay				=	$order->generate_data_to_pay ();
				$charge = $paymill->create(
						array(
						'amount'   => $order_pay['total'] * 100,
						'currency' => $order_pay['currencyCodeType'],
						'token' 	=> $token,
						'description' => $description
			      	)
				);
				$response	=	array(
						'success'	=>	false,
						'msg'	=> $charge,
						'paymentType'	=>	'paymill'
				
				);
				//return $response;
				$id		=	$charge['id'];
				$token	=	md5($id);
				$order->set_payment_code ($token);
				$order->set_payer_id ($id);
				$order->update_order();			

				$returnURL	=	et_get_page_link('process-payment', array( 'paymentType' => 'paymill','token' => $token));
					
				$response	=	array(
						'success'	=>	true,
						'data'		=>	array ('url' => $returnURL ),
						'paymentType'	=>	'paymill'
				);

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
	/**
	 * render paymill checkout button
	 */
	function paymill_payment_button ($payment_gateways) {
		if(!isset($payment_gateways['paymill']))  return;
		$stripe	=	$payment_gateways['paymill'];
		if( !isset($stripe['active']) || $stripe['active'] == -1) return ;
		?>
			<li class="clearfix">
				<div class="f-left">
					<div class="title"><?php _e( 'Paymill', ET_DOMAIN )?></div>
					<div class="desc"><?php _e( 'Pay using your credit card through Paymill.', ET_DOMAIN )?></div>
				</div>
				<div class="btn-select f-right">
					<button id="paymill_pay" class="bg-btn-hyperlink border-radius" data-gateway="paymill" ><?php _e('Select', ET_DOMAIN );?></button>
				</div>
			</li>
		<?php
	}

	function add_paymill_button_mobile($payment_gateways){
		if(!isset($payment_gateways['paymill']))  return;
		$paymill	=	$payment_gateways['paymill'];
		if( !isset($paymill['active']) || $paymill['active'] == -1) return ;
		?>
		<style type="text/css">
			.post-new-classified{
				padding: 20px 15px;
			}
					.post-new-classified.paymill a {
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
	function paymill_mobile_header(){
		
		$paypill		= $this->get_api();
	    $currency 	= ET_Payment::get_currency() ;
		if(is_page_template('page-post-a-job.php') || is_page_template('page-upgrade-account.php')) { ?>
			<script type="text/javascript"> var PAYMILL_PUBLIC_KEY ='<?php echo $paypill["public_key"]; ?>';</script>
			<script type="text/javascript">
			var  je_paymill = {
					"PAYMILL_PUBLIC_KEY" : "<?php  $paypill["public_key"];?>",
					'currency'	 	: {<?php foreach($currency as $key=>$value){ echo '"'.$key.'":"'.$value.'",';}?>},
					'invalid_card'	: "<?php _e('Your card number is incorrect.', ET_DOMAIN);?>" ,
					'invalid_date'	: "<?php _e('Your card\'s expiry date is invalid.', ET_DOMAIN)?>",
					'invalid_cvc'	: "<?php _e('Your card\'s code is invalid.', ET_DOMAIN);?>" ,
					'invalid_account_holder' : "<?php _e('Account holder is invalid.', ET_DOMAIN)?>",
					'unknow_error'	: "<?php _e('Unknown error!!!', ET_DOMAIN);?>"
				}
			
			</script>
			<?php		
	        
		}

	}
	function paymill_mobile_footer(){
		if(is_page_template('page-post-a-job.php') || is_page_template('page-upgrade-account.php')) { 
			?>
			<script type="text/javascript" src="https://bridge.paymill.com/"></script>
			<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__).'/paymill_mobile.js';?>"></script>
			<?php 
		}
	}
	
	// add stripe to je support payment
	function et_support_payment_gateway ( $gateway ) {
		$gateway['paymill']	=	array (
									'label' 		=> __("Paymill",ET_DOMAIN),  
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
		//print_r($paymill_api);
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
						<input class="bg-grey-input <?php if($paymill_api['secret_key'] == '') echo 'color-error' ?>" name="paymill-secret-key" type="text" value="<?php echo $paymill_api['secret_key']  ?> " />
						<span class="icon <?php if($paymill_api['secret_key'] == '') echo 'color-error' ?>" data-icon="<?php  data_icon($paymill_api['secret_key']) ?>"></span>
					</div>
					<div class="form-item">
						<div class="label">
							<?php _e("Your paymill public key",ET_DOMAIN);?>
							
						</div>
						<input class="bg-grey-input <?php if($paymill_api['public_key'] == '') echo 'color-error' ?>" type="text" name="paymill-public-key" value="<?php echo $paymill_api['public_key'] ?> " />
						<span class="icon <?php if($paymill_api['public_key'] == '') echo 'color-error' ?>" data-icon="<?php  data_icon($paymill_api['public_key']) ?>"></span>
					</div>
				</div>
			</div>
		<?php 
	}
	
}

new JE_PAYMILL();

