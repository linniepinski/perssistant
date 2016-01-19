<?php

require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('CE_Shop_Update')){
	class CE_Shop_Update extends CE_Plugin_Updater{
		const VERSION = '1.1';
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ce_shop.php' );
			$this->slug 			= 'ce_shop';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://www.enginethemes.com/forums/?do=product-update&product=ce_shop&type=plugin';

			parent::__construct();
		}
	}
}

new CE_Shop_Update();

?>