<?php
class ET_Cash extends ET_Payment {
	private $_cash_message;
	
	public function __construct( $settings = array ( )) {
		$default_settings	=	array (
			'return'	=> 	'http://localhost',
			'cancel'	=>	'http://localhost'
		);
		$settings			=	wp_parse_args( $settings, $default_settings );	
		$this->_settings	=	$settings;
		$this->_cash_message=	self::get_message();
	}
	
	public static function set_message ( $message ) {
		update_option('et_cash_message', $message );
	}
	
	public static function get_message () {
		$msg	= get_option('et_cash_message');
		if(empty($msg)) 
			return __('Please send your payment to account number: XXXX. Once payment has been verified, admin will approve your post or request.', ET_DOMAIN);



		else return $msg;
	}	
	
	public function set_checkout ( ){
		$cash_url	=	$this->_settings['return'];
		return $cash_url;
	}
	
	public function do_checkout ( $order ) {
		do_action ('et_cash_checkout', $this->_cash_message, $order );
		return $this->_cash_message;
	}	
	public static function is_enable() {
		return true;
	}
}