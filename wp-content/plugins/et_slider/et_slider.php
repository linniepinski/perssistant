<?php
/*
Plugin Name: ET Slider
Plugin URI: www.enginethemes.com
Description: A plugin from EngineThemes to include beautiful sliders in your website easily
Version: 1.3
Author: EngineThemes team
Author URI: www.enginethemes.com
License: GPL2
*/
define ('ET_SLIDER_VERSION', '1.3');
//require_once dirname(__FILE__) . '/register_post_type.php';
require_once dirname(__FILE__) . '/inc/base.php';
require_once dirname(__FILE__) . '/inc/et_slider.php';
require_once dirname(__FILE__) . '/front.php';
//require_once dirname(__FILE__) . '/inc/et_attachment.php';
require_once dirname(__FILE__) . '/admin.php';
require_once dirname(__FILE__) . '/inc/widget.php';
require_once dirname(__FILE__) . '/update.php';


if (is_admin()){
	new ET_Slider();
	new ET_Slider_Admin();
}


add_action( 'admin_init', 'my_tinymce_button' );

function my_tinymce_button() {
     if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
          add_filter( 'mce_buttons', 'my_register_tinymce_button' );
          add_filter( 'mce_external_plugins', 'my_add_tinymce_button' );
     }
}

function my_register_tinymce_button( $buttons ) {
     array_push( $buttons, "link","autolink" );
     return $buttons;
}

function my_add_tinymce_button( $plugin_array ) {
	 $plugins = array('link','autolink');
     $plugins_array = array();
	foreach ($plugins as $plugin ) {
	  $plugins_array[ $plugin ] = plugins_url('js/tiny_mce/plugins/', __FILE__) . $plugin . '/plugin.min.js';
	}
 return $plugins_array;

}