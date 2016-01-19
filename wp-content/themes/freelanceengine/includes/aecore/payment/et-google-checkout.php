<?php
define ('GOOGLE_SCHEMA_URL', 'http://checkout.google.com/schema/2');
class ET_GoogleCheckout extends ET_Payment 
{
	private $_merchant_id;
	private $_merchant_key;
	
	private $_base_url;
	private $_schema_url;
	private $_server_url;
	
	
	function __construct( ) {
		$server_type	=	'';
		if( ET_Payment::get_payment_test_mode()) {
			$server_type	=	'sandbox';
		} 
		
		$api	=	$this->get_api();
		
		$this->_merchant_id = $api['merchant_id'];
		$this->_merchant_key = $api['merchant_key'];
		//$this->currency = $currency;
		
		if(strtolower($server_type) == "sandbox") {
			$this->_server_url = "https://sandbox.google.com/checkout/";
		} else {
			$this->_server_url=  "https://checkout.google.com/";  
		}
		
		$this->_base_url = $this->_server_url . "api/checkout/v2/"; 
		$this->_checkout_url = $this->_base_url . "checkout/Merchant/" . $this->_merchant_id;
		$this->_checkoutForm_url = $this->_base_url . "checkoutForm/Merchant/" . $this->_merchant_id;
		
		
		//$this->_settings	=	$setting;
	}	
	
	function set_checkout ($nvpstr , $payment_type = 'URL-Key') {
		
		$signature		=	base64_encode($this->generate_signature($nvpstr));
		//print_r ($signture);
		$cart			=	base64_encode($nvpstr);

		$extend_field	=	"<input type='hidden' name='cart' value='$cart' /><input type='hidden' name='signature' value='$signature' />";
		return array (
			'url'			=>	$this->_checkout_url,
			'extend_fields'	=>	$extend_field,
			'sign'			=>	$signature,
			'cart'			=>	$cart
		);
	}
	
	public function generate_signature ($data) {
		$key = trim($this->_merchant_key);
		$blocksize = 64;
		$hashfunc = 'sha1';
		if (strlen($key) > $blocksize) {
			$key = pack('H*', $hashfunc($key));
		}
		$key = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = pack(
                    'H*', $hashfunc(
                            ($key^$opad).pack(
                                    'H*', $hashfunc(
                                            ($key^$ipad).$data
                                    )
                            )
                    )
                );
        return  $hmac; 
	}
	/**
	 *  set digital product key to valid payment
	 *  @param ID : order id
	 *  @return string : md5 string 
	 */
	function get_digital_key ( $ID ) {
		return strtoupper(md5($this->_merchant_key.$ID));
	}
	
	/**
	 * save setting
	*/
	static function set_api ( $api = array () ) {
		update_option('et_google_checkout_api', $api );
		if(!self::is_enable()) {
			ET_Payment::disable_gateway('google_checkout');
			return __('Your Google Checkout was disabled because of invalid settings!', ET_DOMAIN);
		}		
		return true;
	}
	/**
	 * get google checkout api setting 
	*/
	static function get_api () {
		
		$api	= (array)get_option('et_google_checkout_api', true);
		if(!isset($api['merchant_id'])) $api['merchant_id']		=	'';
		if(!isset($api['merchant_key'])) $api['merchant_key']	=	'';
		return $api;
	}
	/**
	 * check api setting is ok and payment is available
	*/
	static function is_enable () {
		$api	=	self::get_api();
		if( isset($api['merchant_id']) && $api['merchant_id'] != '' 
		  && isset($api['merchant_key']) && $api['merchant_key'] != '' ) 
			return true;
		return false;
	}
	
	function accept(ET_PaymentVisitor $visitor) {
		
		$visitor->visitGoogleCO($this);
		
	}
	
}

/*add_filter ('et_enable_gateway', 'et_enable_googlecheckout',10,2);
function et_enable_googlecheckout ( $available, $gateway) {
	if( $gateway == 'google_checkout') {
		 if(!ET_GoogleCheckout::is_enable( ) )
			 return false ;
	} 
	return true;
}*/