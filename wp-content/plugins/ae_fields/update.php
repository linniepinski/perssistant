<?php
if (!class_exists('AE_Fields_Update') && class_exists('AE_Plugin_Updater')){
    class AE_Fields_Update extends AE_Plugin_Updater{
        const VERSION = '1.2';

        // setting up updater
        public function __construct(){
            $this->product_slug     = plugin_basename( dirname(__FILE__) . '/ae_fields.php' );
            $this->slug             = 'ae_fields';
            $this->license_key      = get_option('et_license_key', '');
            $this->current_version  = self::VERSION;
            $this->update_path      = 'http://www.enginethemes.com/forums/?do=product-update&product=ae_fields&type=plugin';

            parent::__construct();
        }
    }
    new AE_Fields_Update();
}
