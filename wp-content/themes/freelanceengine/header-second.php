<?php
/**
 * The Header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
global $current_user;
?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <?php global $user_ID; ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1 ,user-scalable=no">
	<title><?php wp_title( '|', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php ae_favicon(); ?>
	<?php 
    wp_head();
    if(function_exists('et_render_less_style')) {
        et_render_less_style();
    }
    ?>
</head>

<body <?php body_class(); ?>>

<!-- MENU DOOR -->
<div class="overlay overlay-scale">
	<div class="container">
    	<div class="row">
        	<div class="col-md-12">
            	<a href="javascript:void(0);" class="overlay-close"><i class="fa fa-times"></i></a>
            </div>
        </div>
    </div>
    <!-- MENU -->
	<?php
        if(has_nav_menu('et_header')) {
            /**
            * Displays a navigation menu
            * @param array $args Arguments
            */
            $args = array(
                'theme_location' => 'et_header',
                'menu' => '',
                'container' => 'nav',
                'container_class' => 'menu-fullscreen',
                'container_id' => '',
                'menu_class' => 'menu-main',
                'menu_id' => '',
                'echo' => true,
                'before' => '',
                'after' => '',
                'link_before' => '',
                'link_after' => ''
            );
      
          wp_nav_menu( $args );
        }
    ?>
    <!-- MENU / END -->
    <?php get_template_part('head/search'); ?>
    <?php get_template_part('head/notification'); ?>
    
</div>
<!-- MENU DOOR / END -->
<!-- HEADER -->
<!-- HEADER / END -->

<?php

if(is_page_template('page-home.php')){
    if(ae_get_option('header_youtube_id')) {
        get_template_part('head/video','youtube');
    }else{
        get_template_part('head/video','background');
    }
    
}
if(!is_user_logged_in()){
    get_template_part( 'template-js/header', 'login' );
}
global $user_ID;
if($user_ID) {
    echo '<script type="data/json"  id="user_id">'. json_encode(array('id' => $user_ID, 'ID'=> $user_ID) ) .'</script>';  
} 
