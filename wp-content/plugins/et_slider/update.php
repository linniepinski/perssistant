<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';

class ET_Slider_Update extends ET_Plugin_Updater{
	const VERSION = '1.3';

	// setting up updater
	public function __construct(){
		$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/et_slider.php' );
		$this->slug 			= 'et_slider';
		$this->license_key 		= get_option('et_license_key', '');
		$this->current_version 	= self::VERSION;
		$this->update_path 		= 'http://www.enginethemes.com/?do=product-update&product=et_slider&type=plugin';

		parent::__construct();
	}
}

new ET_Slider_Update();

?>