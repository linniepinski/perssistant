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

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
    require_once( CHAT__PLUGIN_DIR . 'class.chat-admin.php' );
    add_action( 'init', array( 'chatAdmin', 'Init' ) );

}

