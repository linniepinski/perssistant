<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('CE_Coupon_Update')){
	class CE_Coupon_Update extends CE_Plugin_Updater{
		const VERSION = '1.3';

		// setting up updater
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ce_coupon.php' );
			$this->slug 			= 'ce_coupon';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://www.enginethemes.com/forums/?do=product-update&product=ce_coupon&type=plugin';

			parent::__construct();
		}
	}
}

new CE_Coupon_Update();

?>