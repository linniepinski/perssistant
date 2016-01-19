<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');

/***** Přidání stránek do menu v administraci *************************/
function WPHE_admin_menu()
{
    global $WPHE_dirname, $WPHE_dirurl;
    if(current_user_can('activate_plugins')){
		add_menu_page('WP Htaccess Editor', 'Htaccess', 'activate_plugins', $WPHE_dirname, 'WPHE_view_page', $WPHE_dirurl.'style/img/wphe-mini.png');
		WPHE_add_page('Htaccess Editor','Htaccess Editor', 'activate_plugins', $WPHE_dirname, 'WPHE_view_page');
		WPHE_add_page(__('Backup', 'wphe'),__('Backup', 'wphe'), 'activate_plugins', $WPHE_dirname.'_backup', 'WPHE_view_page');

		// přidání css stylu do administrace
		wp_enqueue_style('wphe-style', $WPHE_dirurl.'style/wphe-style.css');
	}
	unset($WPHE_dirname);
	unset($WPHE_dirurl);
}

/***** Zobrazení stránky podle požadavku ******************************/
function WPHE_view_page()
{
	global $WPHE_dirname, $WPHE_root, $WPHE_dirurl, $WPHE_version;

    switch (strip_tags(addslashes($_GET['page'])))
	{
		case $WPHE_dirname:
			if(file_exists($WPHE_root.'pages/wphe-dashboard.php')){
				require $WPHE_root.'pages/wphe-dashboard.php';
			}else{
				wp_die(__('Fatal error: Plugin <strong>WP Htaccess Editor</strong> is corrupted', 'wphe'));
			}
		break;
		case $WPHE_dirname.'_backup':
			if(file_exists($WPHE_root.'pages/wphe-backup.php')){
				require $WPHE_root.'pages/wphe-backup.php';
			}else{
				wp_die(__('Fatal error: Plugin <strong>WP Htaccess Editor</strong> is corrupted', 'wphe'));
			}
		break;
		default:
		    if(file_exists($WPHE_root.'pages/wphe-dashboard.php')){
				require $WPHE_root.'pages/wphe-dashboard.php';
			}else{
				wp_die(__('Fatal error: Plugin <strong>WP Htaccess Editor</strong> is corrupted', 'wphe'));
			}
		break;
	}

	unset($WPHE_dirname);
	unset($WPHE_root);
	unset($WPHE_dirurl);
	unset($WPHE_version);
}

/***** Pomocná funkce pro vytvoření menu ******************************/
function WPHE_add_page($page_title, $menu_title, $access_level, $file, $function = '')
{
	global $WPHE_dirname;
	add_submenu_page($WPHE_dirname, $page_title, $menu_title, $access_level, $file, $function);

	unset($WPHE_dirname);
	unset($page_title);
	unset($menu_title);
	unset($access_level);
	unset($file);
	unset($function);
}
