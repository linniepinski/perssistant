<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';

class JE_RSS_Update extends ET_Plugin_Updater{
	const VERSION = '2.3';

	// setting up updater
	public function __construct(){
		$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/rss_import.php' );
		$this->slug 			= 'rss_import';
		$this->license_key 		= get_option('et_license_key', '');
		$this->current_version 	= self::VERSION;
		$this->update_path 		= 'http://www.enginethemes.com/?do=product-update&product=rss_import&type=plugin';

		parent::__construct();
	}
}

new JE_RSS_Update();

?>