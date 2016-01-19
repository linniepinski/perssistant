<?php
/*
Plugin Name: CE AdMap
Plugin URI: www.enginethemes.com
Description: CE AdMap
Version: 1.5.1
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/

define('CE_MAP_VER', "1.5.1" );
define('CE_MAP_PATH',dirname(__FILE__));
define('CE_MAP_URL', plugins_url( basename(dirname(__FILE__)) ));
require_once dirname(__FILE__) . '/update.php';
require_once dirname(__FILE__) . '/widget.php';
require_once dirname(__FILE__) . '/shortcode.php';

add_action( 'widgets_init', 'map_register_widget');
function map_register_widget () {
    register_widget( 'AdMap_Widget' );
}

add_action ('after_setup_theme', 'ce_admap_init');
function ce_admap_init () {

	if(class_exists('ET_AdminMenuItem')) {
    	require_once dirname(__FILE__) . '/map_backend.php';
    	require_once dirname(__FILE__) . '/map_front.php';
    	new CE_AdMap ();
	}
}
if(!function_exists('et_is_tablet')) :
	function et_is_tablet(){
		global $et_mobile_detector;
		return $et_mobile_detector->isTablet();
	}
endif;

