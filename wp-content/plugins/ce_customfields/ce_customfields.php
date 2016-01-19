<?php
/*
Plugin Name: CE Custom Fields
Plugin URI: www.enginethemes.com
Description: Support admin insert new taxonomy or new fields for ad.
Version: 2.3
Author: EngineThemes team
Author URI: www.enginethemes.com
License: GPL2
*/

define('CE_FIELDS_PATH',dirname(__FILE__));
define('CE_FIELDS_URL', plugins_url( basename(dirname(__FILE__)) ));
define ('CE_FIELD_VER','2.3');

require_once dirname(__FILE__) . '/update.php';

add_action('after_setup_theme', 'ce_fields_init');
function ce_fields_init () {

	// if ( !defined('CE_AD_POSTTYPE') ) {
	// 	define ('CE_AD_POSTTYPE','ad');
	// }

	if(class_exists('ET_AdminMenuItem')){

		require_once CE_FIELDS_PATH.'/inc/ce_base.php';
		require_once CE_FIELDS_PATH.'/inc/ce_ajax.php';
		require_once CE_FIELDS_PATH.'/inc/fields_hook.php';
		require_once CE_FIELDS_PATH.'/inc/meta_box.php';
		require_once CE_FIELDS_PATH.'/inc/class_tax.php';
		require_once CE_FIELDS_PATH.'/inc/widget.php';

		/**
		 * hook ce fields: add field to form locate at fields_hook.php
		*/
		new CE_Fields_Hook();
		/**
		 * init ce fields : register fields , register menu manageer, class locate ad ce_base.php
		*/
		new CE_Fields();
		/**
		 * ajax call
		*/
		new CE_Fields_Ajax();
	}

}

add_filter( 'ce_ad_validate_data', 'ce_field_validate' );
function ce_field_validate($data) {
	$fields = CE_Fields::get_fields();
	foreach ($fields as $key => $value) {
		
		if(!$value['field_required']) continue;
		if(isset($value['field_cats'])) {

			$field_cats = $value['field_cats'];
			$cat = $data['tax_input'][CE_AD_CAT];
			
			$intersec = array_intersect($field_cats, $cat );
			if(!empty($intersec)) {
				if(!isset($data[$value['field_name']]) || !$data[$value['field_name']] ) {
					return new WP_Error('ad_empty_content', sprintf(__("Please complete all required fields.", ET_DOMAIN), $value['field_label']));
				}
			}

		}
	}
	return $data;
}
?>