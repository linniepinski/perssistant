<?php
class ET_2CO extends ET_Payment {
	/*
	 * 2checkout account number
	 * The vendor number is your numerical vendor/seller ID number.
	 */
	private $_sid;	
	
	/*
	 * secret word use to caculate md5
	 * The secret word is set by yourself on the Site Managment page. 
	 */
	private $_secret_word;
	
	/*
	 * An MD5 hash used to confirm the validity of a sale. 
	 * It is calculated based on a combination of your secret word, 
	 * seller identification number, the order number, and the sale total
	 */
	private $_md5;
	
	/*
	 * 2checkout purchase url
	 */
	private $_2CO_url;
	
	/*
	 * Y to enable demo mode, do not pass this in for live sales
	 */
	private $_demo;
	
	/*  This parameter will contain an integer value representing the type
		or classification of the ids used in the c_prod parameter(s). This value will
		apply universally per transaction. That is to say c_prod parameters must contain
		only assigned_product_id values or only vendor_product_id values. Current
		valid values for this parameter are defined as follows.
	 */
	private $_id_type;
	
	private $_mode;

	private $_api;

	private $_use_direct;

	public static $direct_script	=	'https://www.2checkout.com/static/checkout/javascript/direct.min.js';
	
	function __construct( ) {

		$demo	=	ET_Payment::get_payment_test_mode();
		//purchase url
		$this->_2CO_url		=	'https://www.2checkout.com/checkout/purchase?sid=';
		$api				=	self::get_api();
		$this->_api			=	$api;
		$this->_sid			=	trim($api['sid']);
		$this->_secret_word	=	trim($api['secret_key']);
		$this->_use_direct	=	false;
		$this->_mode		=	'2CO';
		
		if( $demo) {
			$this->_2CO_url		=	'https://sandbox.2checkout.com/checkout/purchase?sid=';
			$this->_demo	=	'Y';
		}
			
		
	}
	
	/**
	 * Function to perform the API call 3rd-Party Cart parameter 
	 * @param string  $nvpstr
	 * @param string  $payment_type
	 */
	function set_checkout ( $nvpstr, $payment_type ) {
		
		$payment_type	=	strtoupper( $payment_type );
		
		if($payment_type == 'SETEXPRESSCHECKOUT') {
			/*
			 * demo mode use to test : in demo mode, the money do not be charge
			*/
			$demo	=	'N';
			if($this->_demo = 'Y') {
				$demo	=	'&demo=Y';
			}
			
			//$return	=	"&x_receipt_link_url=".$this->_settings['return'];
			/*if ( $return == '' ) {
				return array (
					'ACK'		=>	 false,
					'error_msg'	=>	 'Return Url invalid'
				);
			}*/
			// 2checkout url for direct purchase link using the 3rd-Party Cart parameters
			$_2CO_url	=	$this->_2CO_url.$this->_sid."&id_type=".$this->_id_type.$demo.$nvpstr;
			
			return array('url' =>	$_2CO_url , 'data' => array( 'sid' => $this->_sid , 'id_type' => $this->_id_type , 'demo' => $this->_demo , 'mode' => '2CO' ) ) ; 
			
		}else {
			return false;
		}
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	function do_checkout ( ) {
		if( isset( $_REQUEST['key'])  && isset($_REQUEST['sid']) && $_REQUEST['sid'] == $this->_sid ) {
			
		}
	}
	/**
	 * MD5 hash is provided to help you verify the authenticity of a sale.
	 *  This is especially useful for vendors that sell downloadable products, or e-goods, 
	 *  as it can be used to verify whether sale actually came from 2Checkout and was a legitimate live sale. 
	 *  We intentionally break the hash code for demo orders 
	 *  so that you can compare the hash we provide with what it should be to determine whether or not to provide 
	 *  the customer with your goods.s
	 * @param int $oder_id : The order number is the order number for the sale
	 * @param double $total :  The total is the numerical value for the total amount of the sale
	 * @return md5 hashed sring
	 */
	function md5 ( $oder_id, $total ) {
		
		$string_to_hash =	$this->_secret_word;
		$string_to_hash	.=	$this->_sid;
		$string_to_hash	.=	$oder_id;
		$string_to_hash .=  $total;
		
		return strtoupper( md5( $string_to_hash ));
		
	}
	/**
	 * retrieve payment settings
	 */
	function get_settings ( ) {
		return $this->_settings ;
	}
	
	function get_mode () {
		return $this->_demo;
	}
	
	function use_direct() {
		return $this->_use_direct;
	}

	static function get_api (){ 
		$api	= (array)get_option('et_2checkout_api', true);
		
		if(!isset($api['sid'])) $api['sid']	=	'';
		if(!isset($api['secret_key'])) $api['secret_key']	=	'';
		if(!isset($api['use_direct'])) $api['use_direct']	=	'';
		return $api;
	}
	
	static function set_api ( $api = array () ) {
		update_option('et_2checkout_api', $api );
		if(!self::is_enable()) {
			ET_Payment::disable_gateway('2checkout');
			return __("Your 2Checkout was disabled because of invalid settings!",ET_DOMAIN);
		}
		return true;
	}
	
	public static function is_enable (){
		//$_Co	=	new ET_2CO(array (), true);
		$api	=	self::get_api();
	
		if(!isset($api['sid']) ||  $api['sid']	==	'') return false;
		if(!isset($api['secret_key']) || $api['secret_key']	==	'' ) return false;
		
		return true;
	}

}