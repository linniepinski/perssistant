<?php

require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('CE_Field_Update')){
	class CE_Field_Update extends CE_Plugin_Updater{
		const VERSION = '2.3';
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ce_customfields.php' );
			$this->slug 			= 'ce_customfields';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://forum.enginethemes.com/?do=product-update&product=ce_customfields&type=plugin';

			parent::__construct();
		}
	}
}

new CE_Field_Update();

?>