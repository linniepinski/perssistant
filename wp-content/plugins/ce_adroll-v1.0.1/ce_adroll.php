<?php 
/*
Plugin Name: CE AdRoll
Plugin URI: www.enginethemes.com
Description: CE AdRoll
Version: 1.0.1
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
define('CE_ROLL_PATH',dirname(__FILE__));

define('CE_ROLL_URL', plugins_url( basename(dirname(__FILE__)) ));



require_once dirname(__FILE__) . '/update.php';
require_once dirname(__FILE__) . '/widget.php';
require_once dirname(__FILE__) . '/roll_base.php';
new CE_AddRoll();

function ce_adroll_install() {
		
	$pages = get_posts(array(
		'name' => CE_AddRoll::CE_ROLL_PAGE_NAME,
		'post_type' => 'page',			
		'numberposts' => 1
	));
	
	if ( empty($pages) ){
	 	$id = wp_insert_post(array(
			'post_title' 		=> __('Create an Adroll', ET_DOMAIN),
			'post_content' 		=> __('Adroll', ET_DOMAIN),
			'post_name' 	=> CE_AddRoll::CE_ROLL_PAGE_NAME,
			'post_type' 	=> 'page',
			'post_status' 	=> 'publish'
		));
		update_post_meta($id,'_wp_page_template','');
		update_option(CE_AddRoll::CE_ROLL_OPTION_NAME,$id);
	}

}

function ce_adroll_unstall() { 
	$pages = get_posts(array(
		'name' => CE_AddRoll::CE_ROLL_PAGE_NAME,
		'post_type' => 'page',
		'numberposts' => 1
	));
	foreach ($pages as $page) {
		wp_delete_post($page->ID, true);
		break;
	} 	
}
register_activation_hook( __FILE__, 'ce_adroll_install' );
register_deactivation_hook(__FILE__,'ce_adroll_unstall');



add_action ('after_setup_theme', 'ce_adroll_init');
function ce_adroll_init () {
	require_once dirname(__FILE__) . '/roll_menu.php';
	if(class_exists('CE_AdRoll_Menu'))
		new CE_AdRoll_Menu();

}

function ce_log_file($error){
	$handle = fopen(CE_ROLL_PATH."\log.txt", "a+");
	fwrite($handle, $error);
}
