<?php 

require_once dirname(__FILE__) . '/inc/inc.update.php';
if (!class_exists('CE_Stripe_Update')){
	class CE_Stripe_Update extends CE_Plugin_Updater{
		const VERSION = '1.2';

		// setting up updater
		public function __construct(){
			$this->product_slug 	= plugin_basename( dirname(__FILE__) . '/ce_stripe.php' );
			$this->slug 			= 'ce_stripe';
			$this->license_key 		= get_option('et_license_key', '');
			$this->current_version 	= self::VERSION;
			$this->update_path 		= 'http://www.enginethemes.com/forums/?do=product-update&product=ce_stripe&type=plugin';

			parent::__construct();
		}
	}
}

new CE_Stripe_Update();

?>