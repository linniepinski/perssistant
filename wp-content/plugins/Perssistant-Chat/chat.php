<?php
/**
 * @package ukrosoft-chat
 */
/*
Plugin Name: Perssistant-Chat
Description: Used to Perssistant
Version: 0.9.9
Author: Ukrosoft
Author URI: http://www.ukrosoft.com.ua
License: GPLv2 or later
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('CHAT_VERSION', '0.0.1');
define('CHAT__PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('CHAT__PLUGIN_DIR', plugin_dir_path( __FILE__ ));

require_once( CHAT__PLUGIN_DIR . 'class.chat.php' );
add_action( 'init', array( 'chat', 'Init' ) );


if (is_admin()) {
//    require_once( CHAT__PLUGIN_DIR . 'class.chat-admin.php' );
//    add_action( 'init', array( 'chatAdmin', 'Init' ) );

}