<?php
/**
 * @package ukrosoft-chat
 */
/*
Plugin Name: trace
Description: Used to
Version: 0.0.9
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

define('TRACE_VERSION', '0.0.1');
define('TRACE__PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('TRACE__PLUGIN_DIR', plugin_dir_path( __FILE__ ));

require_once( TRACE__PLUGIN_DIR . 'class.trace.php' );
add_action( 'init', array( 'trace', 'Init' ) );
register_activation_hook(__FILE__,'trace_instal');

function trace_instal()
{
    global $wpdb;

    /*$sql =
        "CREATE TABLE IF NOT EXISTS `wp_trace` (
    `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
    `project_id` INT( 11 ) NOT NULL ,
    `freelancer_id` INT( 11 ) NOT NULL ,
    `employer_id` INT( 11 ) NOT NULL ,
    `image_url` VARCHAR( 255 ) NOT NULL ,
    `trace_time` BIGINT( 20 ) NOT NULL ,
    `clicks` BIGINT( 20 ) NOT NULL ,
    `timestamp` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ";
    $wpdb->query($sql);*/
}

if (is_admin()) {
    require_once( TRACE__PLUGIN_DIR . 'class.trace-admin.php' );
    add_action( 'init', array( 'traceAdmin', 'Init' ) );

}

