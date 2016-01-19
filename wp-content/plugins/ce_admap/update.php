<?php

require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('CE_AdMap_Update')){
	class CE_AdMap_Update extends CE_Plugin_Updater{
		const VERSION = '1.5.1';
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ce_admap.php' );
			$this->slug 			= 'ce-map';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://forum.enginethemes.com/?do=product-update&product=ce_admap&type=plugin';

			parent::__construct();
		}
	}
}

new CE_AdMap_Update();

?>