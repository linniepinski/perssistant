<?php 

require_once dirname(__FILE__) . '/lib/inc.update.php';

class JE_PPExpress_Update extends ET_Plugin_Updater{
	const VERSION = '1.0';

	// setting up updater
	public function __construct(){
		$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/je_ppexpress.php' );
		$this->slug 			= 'je_ppexpress';
		$this->license_key 		= get_option('et_license_key', '');
		$this->current_version 	= self::VERSION;
		$this->update_path 		= 'http://www.enginethemes.com/?do=product-update&product=je_ppexpress&type=plugin';

		parent::__construct();
	}
}
new JE_PPExpress_Update();

?>