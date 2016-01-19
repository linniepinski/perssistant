<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('CE_AdRoll_Update')){
	class CE_AdRoll_Update extends CE_Plugin_Updater{
		const VERSION = '1.0.1';

		// setting up updater
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ce_adroll.php' );
			$this->slug 			= 'ce_adroll';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://www.enginethemes.com/forums/?do=product-update&product=ce_adroll&type=plugin';

			parent::__construct();
		}
	}
}

new CE_AdRoll_Update();

?>