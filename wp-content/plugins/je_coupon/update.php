<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';

class JE_Coupon_Update extends ET_Plugin_Updater{
	const VERSION = '1.7';

	// setting up updater
	public function __construct(){
		$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/je_coupon.php' );
		$this->slug 			= 'je_coupon';
		$this->license_key 		= get_option('et_license_key', '');
		$this->current_version 	= self::VERSION;
		$this->update_path 		= 'http://www.enginethemes.com/forums/?do=product-update&product=je_coupon&type=plugin';

		parent::__construct();
	}
}

new JE_Coupon_Update();

?>