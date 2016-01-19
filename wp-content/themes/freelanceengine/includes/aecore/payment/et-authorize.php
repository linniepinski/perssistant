<?php
define ('AUTHORZIE_VERSION', 3.1);

class ET_Authorize extends ET_Payment {
	
	private $_login;
	private $_transaction_key;
	private $_md5_hash;
	/*
	 * The merchant’s website should post transaction requests by means of an HTML Form POST to the following payment gateway URL:
	 */
	private $_post_location;
	private $_x_test_request;
	
	private $_x_method ;
	private $_x_version;
	/*
	 * The type of link back to the merchant’s website from the hosted receipt page
	 * Format: LINK, POST, or GET
	 */
	private $_x_receipt_link_method;
	/*
	 * The URL of the link or button that directs the customer back to the merchant’s website
	 */
	private $_x_receipt_link_url;
	/*
	 * The text of the link or button that directs the customer back to the merchant’s website
	 */
	private $_x_receipt_link_text;
	/*
	 * Product or order description.
	 */
	private $_x_description;
	private $_x_line_item;
	private $_x_show_form;
	
	private $_nvp;
	
	
	function __construct( $settings = array (), $mode ){
		
		$this->_x_test_request	=	ET_Payment::get_payment_test_mode();


		$api					=	ET_Authorize::get_api ();
		if($this->_x_test_request)
			$this->_post_location	=	'https://test.authorize.net/gateway/transact.dll';
		else 
			$this->_post_location	=	'https://secure.authorize.net/gateway/transact.dll ';
		$this->_login			=	$api['x_login'];
		$this->_md5_hash		=	$api['x_MD5_hash'];
		$this->_transaction_key	=	$api['x_transaction_key'];	
		$this->_x_method		=	'cc';
		$this->_x_show_form		=	'payment_form';

		$default_settings	=	array (
			'return'	=> 	'http://localhost',
			'cancel'	=>	'http://localhost'
		);
		
		$settings			=	wp_parse_args( $settings, $default_settings );	
		
		$this->_x_receipt_link_url 	=	$settings['return'];
		
	}
	
	function set_checkout ( $extends , $amount) {
		
		$post_location		=	$this->_post_location;
		$currency			=	self::get_currency ();
		if( $currency['code'] != 'USD' || $currency['code'] != 'CAD' || $currency['code'] != 'GBP' ) {
			$currency	=	'USD';
		} else {
			$currency	=	$currency['code'];
		}

		$this->set_receipt_link();
		$extends		.=    $this->add_field('x_login', $this->_login);
		$extends		.=   $this->add_field('x_receipt_link_method', $this->_x_receipt_link_method);
		$extends		.=   $this->add_field('x_receipt_link_text', $this->_x_receipt_link_text);
		$extends		.=   $this->add_field('x_receipt_link_url', $this->_x_receipt_link_url);
		$extends		.=   $this->add_field('x_method', $this->_x_method);
		$extends		.=   $this->add_field('x_version', AUTHORZIE_VERSION);
		$extends		.=   $this->add_field('x_test_request', $this->_x_test_request);
		//$extends		.=   $this->add_field('x_amount', 54);
		$extends		.=   $this->add_field('x_type', 'AUTH_CAPTURE');
		$extends		.=   $this->add_field('x_show_form', $this->_x_show_form);

		//$extends		.=	 $this->add_field('x_currency_code', $currency );
		
		
		$fp_timestamp = time();
		$fp_sequence  = "123" . time(); // Enter an invoice or other unique number.
		
		$fingerprint = $this->get_finger_print($this->_login, $this->_transaction_key, $amount, $fp_sequence, $fp_timestamp);
		
		$extends		.=   $this->add_field('x_fp_sequence', $fp_sequence);
		$extends		.=   $this->add_field('x_fp_hash', $fingerprint);
		$extends		.=   $this->add_field('x_fp_timestamp', $fp_timestamp);
		
		$this->_nvp['url']				=	$post_location;
		$this->_nvp['extend_fields']	=	$extends;
		
		return $this->_nvp;				
	}
	
	function add_field	($name, $value) {
		$this->_nvp[$name]		=	$value;
		return '<input type="hidden" name="'.$name.'" value="'.$value.'" />';
	}
	/**
	 * set receipt link to relay response
	 * @param unknown_type $args
	 */
	function  set_receipt_link ( $args	=	 array ('method' => 'POST', 'text' => 'Return to comfirm your order')) {
		extract($args);
		$this->_x_receipt_link_method 	= 	$method;
		$this->_x_receipt_link_text	  	=	$text;
		//$this->_x_receipt_link_url		=	$url;
	}
	/**
     * Generates a fingerprint needed for a hosted order form or DPM.
     *
     * @param string $api_login_id    Login ID.
     * @param string $transaction_key API key.
     * @param string $amount          Amount of transaction.
     * @param string $fp_sequence     An invoice number or random number.
     * @param string $fp_timestamp    Timestamp.
     *
     * @return string The fingerprint.
     */
    public  function get_finger_print($api_login_id, $transaction_key, $amount, $fp_sequence, $fp_timestamp){
    	
        $api_login_id = ($api_login_id ? $api_login_id : (defined('AUTHORIZENET_API_LOGIN_ID') ? AUTHORIZENET_API_LOGIN_ID : ""));
        $transaction_key = ($transaction_key ? $transaction_key : (defined('AUTHORIZENET_TRANSACTION_KEY') ? AUTHORIZENET_TRANSACTION_KEY : ""));
        if (function_exists('hash_hmac')) {
            return hash_hmac("md5", $api_login_id . "^" . $fp_sequence . "^" . $fp_timestamp . "^" . $amount . "^", $transaction_key); 
        }
        return bin2hex(mhash(MHASH_MD5, $api_login_id . "^" . $fp_sequence . "^" . $fp_timestamp . "^" . $amount . "^", $transaction_key));
    }
    
	public function generate_hash( $amount , $trans_id){
        $_md5_hash=	 strtoupper(md5(trim($this->_md5_hash) . $this->_login . $trans_id . $amount));
        return $_md5_hash;
    }
    /**
     * save api setting
    */
	static function set_api ( $api = array () ) {
		update_option('et_authorize_api', $api );
		if(!self::is_enable()) {
			$gateways	=	self::get_gateways();
			if(isset($gateways['authorize']['active']) && $gateways['authorize']['active'] != -1 ) {
				ET_Payment::disable_gateway('authorize');
				return __('Your Authorize.Net was disabled because of invalid settings!', ET_DOMAIN);
			}
		}
		return true;
	}
	/**
	 * get api setting
	*/
    public static function get_api () {
    	$api	= get_option('et_authorize_api', array() );
    	if(!isset($api['x_login'])) 			$api['x_login']	=	'';
		if(!isset($api['x_transaction_key'])) 	$api['x_transaction_key']	=	'';
		if(!isset($api['x_MD5_hash'])) 			$api['x_MD5_hash']	=	'';
		return $api;
    }

    /**
	 * check authorize api setting available or not
	 */
	public static function is_enable() {
		$api	=	self::get_api();
		if( !isset($api['x_login']) || $api['x_login'] == '') 
			return false;
		if( !isset($api['x_transaction_key']) || $api['x_transaction_key'] == '') 
			return false;
		if( !isset($api['x_MD5_hash']) || $api['x_MD5_hash'] == '') 
			return false;
		return true;
	}

}


add_filter ('et_update_payment_setting', 'et_update_authorize_setting', 10 ,3);
function et_update_authorize_setting ( $msg , $name, $value) {
	$authorize	=	ET_Authorize::get_api ();
	switch ($name) {
		case 'X_LOGIN':
			$authorize['x_login']	=	$value;
			$msg	=	ET_Authorize::set_api( $authorize );
			break;
		case 'X_TRANSACTION_KEY':
			$authorize['x_transaction_key']	=	$value;
			$msg	=	ET_Authorize::set_api( $authorize );
			break;
		case 'X_MD5_HASH':
			$authorize['x_MD5_hash']	=	$value;
			$msg	=	ET_Authorize::set_api( $authorize );
			break;
	}

	return $msg;
}

//add_filter ('et_enable_gateway', 'et_enable_authorize', 10 , 2);
function et_enable_authorize ($available,  $gateway) {
	if( $gateway == 'authorize') {
		 if(!ET_Authorize::is_enable( ) )
			return false ;
		else 
			return true;
	}
	
	return $available;
}

//add_filter ('et_support_payment_gateway', 'et_add_authorize_gateway') ;
function et_add_authorize_gateway ( $support) {
	$support['authorize']		= array ('label' => 'Authorize.Net', 'active' => -1,
									'description' => __("Send your payment via Authorize.Net",ET_DOMAIN));
	return $support;
}
