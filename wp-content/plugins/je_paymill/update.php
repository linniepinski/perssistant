<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';

class JE_paymill_Update extends ET_Plugin_Updater{
	const VERSION = '1.1';

	// setting up updater
	public function __construct(){
		$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/paymill.php' );
		$this->slug 			= 'je_paymill';
		$this->license_key 		= get_option('et_license_key', '');
		$this->current_version 	= self::VERSION;
		$this->update_path 		= 'http://www.enginethemes.com/forums/?do=product-update&product=je_paymill&type=plugin';

		parent::__construct();
	}
}
new JE_paymill_Update();

?>