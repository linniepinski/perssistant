<?php
/*
Plugin Name: Perssistant Plus
Plugin URI: http://#
Description: Used by mi your Akismet configuration page, and save your API key.
Version: 0.1
Author: A
Author URI:
License: GPLv2 or later
*/
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('PERSSISTANT_PLUS__VERSION', '0.1');
define('PERSSISTANT_PLUS__MINIMUM_WP_VERSION', '3.2');
define('PERSSISTANT_PLUS__PLUGIN_URL', plugin_dir_url(__FILE__));
define('PERSSISTANT_PLUS__PLUGIN_DIR', plugin_dir_path(__FILE__));
//define( 'PERSSISTANT_PLUS_DELETE_LIMIT', 100000 );

//register_activation_hook( __FILE__, array( 'Akismet', 'plugin_activation' ) );
//register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );

require_once(PERSSISTANT_PLUS__PLUGIN_DIR . 'class.perssistant_plus.php');
//require_once( PERSSISTANT_PLUS__PLUGIN_DIR . 'class.akismet-widget.php' );

add_action('init', array('perssistant_plus', 'init'));
add_option('paymill_secret_key');
add_option('paymill_public_key');


if (is_admin()) {
    //require_once( AKISMET__PLUGIN_DIR . 'class.akismet-admin.php' );
    //add_action( 'init', array( 'Akismet_Admin', 'init' ) );
}

//add wrapper class around deprecated akismet functions that are referenced elsewhere
//require_once( AKISMET__PLUGIN_DIR . 'wrapper.php' );