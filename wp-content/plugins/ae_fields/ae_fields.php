<?php

/**
 * @package AppEngine Custom Fields
 */

/*
Plugin Name: AE Fields
Plugin URI: http://enginethemes.com/
Description: Easily add custom fields for your content, currently only for DirectoryEngine & FreelanceEngine
Version: 1.2
Author: EngineThemes
Author URI: https://www.enginethemes.com/
License: GPLv2 or later
Text Domain: enginetheme
*/

// return false;
define('AE_FIELD_PATH', dirname(__FILE__));
add_action('after_setup_theme', 'ae_field_require_lib');
function ae_field_require_lib() {
    if(!class_exists('AE_Base')) return ;
    // 
  //  require_once (AE_FIELD_PATH . '/html/index.php');
    require_once (AE_FIELD_PATH . '/form.php');
    require_once (AE_FIELD_PATH . '/class-field-list.php');
    require_once (AE_FIELD_PATH . '/functions.php');
    require_once (AE_FIELD_PATH . '/update.php');
}

add_filter('ae_admin_menu_pages', 'ae_fields_menu');
function ae_fields_menu($pages) {
    $sections = array();
    $options = AE_Options::get_instance();
    
    /**
     * ae fields settings
     */
    $sections[] = array(
        'args' => array(
            'title' => __("Meta Fields", ET_DOMAIN) ,
            'id' => 'meta_field',
            'icon' => 'F',
            'class' => ''
        ) ,
        
        'groups' => array(
            /**
             * package plan list
             */
            array(
                'type' => 'field_list',
                'args' => array(
                    'title' => __("Fields List", ET_DOMAIN) ,
                    'id' => 'list-field',
                    'class' => 'list-package',
                    'desc' => '',
                    'name' => 'ae_field',
                    'custom_field' => 'meta'
                ) ,
                
                'fields' => array(
                    'form' => plugin_dir_path(__FILE__) . '/settings-template/field-form.php',
                    'form_js' => plugin_dir_path(__FILE__) . '/settings-template/field-form-js.php',
                    'js_template' => plugin_dir_path(__FILE__) . '/settings-template/package-js-item.php',
                    'template' => plugin_dir_path(__FILE__) . '/settings-template/package-item.php'
                )
            )
        )
    );

    /**
     * ae fields settings
     */
    $sections[] = array(
        'args' => array(
            'title' => __("Taxonomies", ET_DOMAIN) ,
            'id' => 'tax_field',
            'icon' => 'z',
            'class' => ''
        ) ,
        
        'groups' => array(
            
            /**
             * package plan list
             */
            array(
                'type' => 'field_list',
                'args' => array(
                    'title' => __("Taxonomies Lists", ET_DOMAIN) ,
                    'id' => 'list-tax',
                    'class' => 'list-tax',
                    'desc' => '',
                    'name' => 'ae_field',
                    'custom_field' => 'tax'
                ) ,
                
                'fields' => array(
                    'form' => plugin_dir_path(__FILE__) . '/settings-template/tax-form.php',
                    'form_js' => plugin_dir_path(__FILE__) . '/settings-template/tax-form-js.php',
                    'js_template' => plugin_dir_path(__FILE__) . '/settings-template/package-js-item.php',
                    'template' => plugin_dir_path(__FILE__) . '/settings-template/package-item.php'
                )
            )
        )
    );
    
    foreach ($sections as $key => $section) {
        $temp[] = new AE_section($section['args'], $section['groups'], $options);
    }
    
    $orderlist = new AE_container(array(
        'class' => 'field-settings',
        'id' => 'settings',
    ) , $temp, $options);
    
    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('Custom Fields', ET_DOMAIN) ,
            'menu_title' => __('CUSTOM FIELDS', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'ae-fields',
            'icon' => 'x',
            'desc' => __("Easily add custom fields for your content", ET_DOMAIN)
        ) ,
        'container' => $orderlist
    );
    
    return $pages;
}


/**
 * hook to add translate string to plugins 
 * @param Array $entries Array of translate entries
 * @since 1.0
 * @author Dakachi
 */
add_filter( 'et_get_translate_string', 'ae_fields_add_translate_string' );
function ae_fields_add_translate_string ($entries) {
    $lang_path = dirname(__FILE__).'/lang/default.po';
    if(file_exists($lang_path)) {
        $pot        =   new PO();
        $pot->import_from_file($lang_path, true );
        
        return  array_merge($entries, $pot->entries);    
    }
    return $entries;
}

