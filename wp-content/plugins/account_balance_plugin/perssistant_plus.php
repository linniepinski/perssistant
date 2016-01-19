<?php
/*
Plugin Name: Plugin Virtual Assistants
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

define('ACCOUNT_BALANCE_PLUGIN__VERSION', '0.1');
define('ACCOUNT_BALANCE_PLUGIN__MINIMUM_WP_VERSION', '3.2');
define('ACCOUNT_BALANCE_PLUGIN__PLUGIN_URL', plugin_dir_url(__FILE__));
define('ACCOUNT_BALANCE_PLUGIN__PLUGIN_DIR', plugin_dir_path(__FILE__));
//define( 'ACCOUNT_BALANCE_PLUGIN_DELETE_LIMIT', 100000 );

//register_activation_hook( __FILE__, array( 'Akismet', 'plugin_activation' ) );
//register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );

require_once(ACCOUNT_BALANCE_PLUGIN__PLUGIN_DIR . 'class.account_balance.php');
//require_once( ACCOUNT_BALANCE_PLUGIN__PLUGIN_DIR . 'class.akismet-widget.php' );

add_action('init', array('account_balance', 'init'));
add_option('paymill_secret_key');
add_option('paymill_public_key');
if (is_admin()) {
    //require_once( AKISMET__PLUGIN_DIR . 'class.akismet-admin.php' );
    //add_action( 'init', array( 'Akismet_Admin', 'init' ) );
}

//add wrapper class around deprecated akismet functions that are referenced elsewhere
//require_once( AKISMET__PLUGIN_DIR . 'wrapper.php' );