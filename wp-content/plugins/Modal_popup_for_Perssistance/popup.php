<?php
/*
Plugin Name: Popup-100% FOR FREE 3 months!
Description: Register now 100% FOR FREE the coming 3 months!
Version: 0.5
Author: Ukrosoft
Author URI: http://www.ukrosoft.com.ua
License: GPLv2 or later
*/

add_action('wp_footer', 'popup_window_free');
wp_register_style('PopupCss', plugin_dir_url(__FILE__) . 'popup.css');
wp_enqueue_style('PopupCss');
wp_enqueue_script('popup_ajax', plugin_dir_url(__FILE__) . 'popup_free3.js', array('jquery'));
wp_enqueue_script('jquery.cookie.js', plugin_dir_url(__FILE__) . 'jquery.cookie.js', array('jquery'));
wp_localize_script('popup_ajax', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

function popup_window_free()
{
    require 'popup-template.php';
}
