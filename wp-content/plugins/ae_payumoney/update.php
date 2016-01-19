<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('AE_PayuMoney_Update')){
	class AE_PayuMoney_Update extends AE_Plugin_Updater{
		const VERSION = '1.0';

		// setting up updater
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ae_payumoney.php' );
			$this->slug 			= 'ae_payumoney';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://forum.enginethemes.com/?do=product-update&product=ae_payumoney&type=plugin';

			parent::__construct();
		}
	}
	new AE_PayuMoney_Update();
}


?>