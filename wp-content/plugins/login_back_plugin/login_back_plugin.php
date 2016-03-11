<?php
/*
Plugin Name: Login back Perssistant
Plugin URI: http://#
Description: t
Version: 0.1
Author: A
Author URI:
License: GPLv2 or later
*/
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('LOGIN_BACK_PLUGIN__VERSION', '0.1');
define('LOGIN_BACK_PLUGIN__MINIMUM_WP_VERSION', '3.2');
define('LOGIN_BACK_PLUGIN__PLUGIN_URL', plugin_dir_url(__FILE__));
define('LOGIN_BACK_PLUGIN__PLUGIN_DIR', plugin_dir_path(__FILE__));
//define( 'LOGIN_BACK_PLUGIN_DELETE_LIMIT', 100000 );


//register_activation_hook( __FILE__, array( 'Akismet', 'plugin_activation' ) );
//register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );

require_once(LOGIN_BACK_PLUGIN__PLUGIN_DIR . 'class.login_back_plugin.php');
//require_once( ACCOUNT_BALANCE_PLUGIN__PLUGIN_DIR . 'class.akismet-widget.php' );

add_action('init', array('login_back', 'init'));

if (is_admin()) {
    //require_once( AKISMET__PLUGIN_DIR . 'class.akismet-admin.php' );
    //add_action( 'init', array( 'Akismet_Admin', 'init' ) );
}
