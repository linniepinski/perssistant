<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');

/***** Načtení systémových souborů WP pro kontrolu oprávnění uživatele */

if(!function_exists('wp_get_current_user')){
	if(file_exists(ABSPATH.'wp-includes/pluggable.php')){
		require_once ABSPATH.'wp-includes/pluggable.php';
	}else{
		wp_die(__('Plugin WP Htaccess Editor Error: File "/wp-includes/pluggable.php" does not exists!', 'wphe'));
	}
}

if(!function_exists('current_user_can')){
	if(file_exists(ABSPATH.'wp-includes/capabilities.php')){
		require_once ABSPATH.'wp-includes/capabilities.php';
	}else{
		wp_die(__('Plugin WP Htaccess Editor Error: File "/wp-includes/capabilities.php" does not exists!', 'wphe'));
	}
}
