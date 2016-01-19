<?php
/*
Plugin Name: JE Custom Fields
Plugin URI: www.enginethemes.com
Description: Allow admin insert additional information for posting job
Version: 2.3
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/

define('JEP_FIELD_PATH', dirname(__FILE__));
define('JEP_FIELD_URL', plugins_url( basename(dirname(__FILE__)) ));
require_once ( JEP_FIELD_PATH . '/inc/base.php' );
require_once ( JEP_FIELD_PATH . '/inc/field.php' );
require_once ( JEP_FIELD_PATH . '/init.php' );
require_once ( JEP_FIELD_PATH . '/admin.php' );
require_once ( JEP_FIELD_PATH . '/front.php' );
require_once ( JEP_FIELD_PATH . '/field_taxonomy.php' );

require_once ( JEP_FIELD_PATH . '/update.php' );


try {
	new JEP_Field();

	if (is_admin()){
		new JEP_Fields_Admin();
	}else {
		new JEP_Fields_Front();
	}
} catch (Exception $e) {

}
function je_resume_limit_file(){
	return apply_filters('je_resume_limit_file',3);
}