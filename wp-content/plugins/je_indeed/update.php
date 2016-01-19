<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';

class JE_Indeed_Update extends ET_Plugin_Updater{

	const VERSION = '3.7.1';

	// setting up updater
	public function __construct(){
		$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/je_indeed.php' );
		$this->slug 			= 'je_indeed';
		$this->license_key 		= get_option('et_license_key', '');
		$this->current_version 	= self::VERSION;
		$this->update_path 		= 'http://www.enginethemes.com/forums/?do=product-update&product=je_indeed&type=plugin';

		parent::__construct();
	}
}

new JE_Indeed_Update();

?>