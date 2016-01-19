<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';

class JE_JobMap_Update extends ET_Plugin_Updater{
	const VERSION = '1.2';

	// setting up updater
	public function __construct(){
		$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/job_map.php' );
		$this->slug 			= 'je_jobmap';
		$this->license_key 		= get_option('et_license_key', '');
		$this->current_version 	= self::VERSION;
		$this->update_path 		= 'http://www.enginethemes.com/?do=product-update&product=je_jobmap&type=plugin';

		parent::__construct();
	}
}

new JE_JobMap_Update();

?>