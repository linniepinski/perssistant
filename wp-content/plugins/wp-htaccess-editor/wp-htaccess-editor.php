<?php
/*
Plugin Name: WP Htaccess Editor
Plugin URI: http://wp-blog.cz/101-plugin-wp-htaccess-editor/
Description: Simple editor htaccess file without using FTP client.
Version: 1.3.0
Text Domain: wphe
Domain Path: /lang/
Author: Lukenzi
Author URI: http://wp-blog.cz/
License: GPLv2 or later
*/

/*  Copyright 2011-2012, Lukenzi  (email : lukenzi@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');




/***** Základní nastavení cest a URL adres pluginu ********************/
if(!is_admin()){
   return;
}else{
	$WPHE_version = '1.3.0';

	if(!defined('WP_CONTENT_URL')){
		if(!defined('WP_SITEURL')){
			define('WP_SITEURL', get_option('url').'/');
		}
		define('WP_CONTENT_URL', WP_SITEURL.'wp-content');
	}
	if(!defined('WP_PLUGIN_URL')){
		define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
	}

	$WPHE_root = str_replace('\\', '/', dirname(__FILE__)).'/';
	$WPHE_lib = $WPHE_root.'lib/';
	$WPHE_dirname = str_replace('\\', '/', dirname(plugin_basename(__FILE__)));
	$WPHE_dirurl = WP_PLUGIN_URL.'/'.$WPHE_dirname.'/';



/***** Načtení překladu ***********************************************/
	$WPHE_Locale = get_locale();
	if(!empty($WPHE_Locale))
	{
		$WPHE_moFile = dirname(__FILE__) . '/lang/'.$WPHE_Locale.'.mo';
		if(@file_exists($WPHE_moFile) && is_readable($WPHE_moFile))
		{
			load_textdomain('wphe', $WPHE_moFile);
		}
		unset($WPHE_moFile);
	}
	unset($WPHE_Locale);



/***** Načtení souborů pluginu ****************************************/
	if(file_exists($WPHE_lib.'lib.wp-files.php')){
		require $WPHE_lib.'lib.wp-files.php';
	}else{ wp_die(__('Fatal error: Plugin <strong>WP Htaccess Editor</strong> is corrupted', 'wphe')); }

	if(file_exists($WPHE_lib.'lib.functions.php')){
		require $WPHE_lib.'lib.functions.php';
	}else{ wp_die(__('Fatal error: Plugin <strong>WP Htaccess Editor</strong> is corrupted', 'wphe')); }

	if(file_exists($WPHE_lib.'lib.ad.php')){
		require $WPHE_lib.'lib.ad.php';
	}else{ wp_die(__('Fatal error: Plugin <strong>WP Htaccess Editor</strong> is corrupted', 'wphe')); }


	if(file_exists($WPHE_lib.'lib.pages.php')){
		require $WPHE_lib.'lib.pages.php';
	}else{ wp_die(__('Fatal error: Plugin <strong>WP Htaccess Editor</strong> is corrupted', 'wphe')); }



/***** Vytvoření menu v administraci a spuštění pluginu ***************/
	if(function_exists('add_action')){
		add_action('admin_menu', 'WPHE_admin_menu');
	}else{
		unset($WPHE_root);
		unset($WPHE_lib);
		unset($WPHE_plugin);
		unset($WPHE_dirname);
		unset($WPHE_dirurl);
		return;
	}
}
