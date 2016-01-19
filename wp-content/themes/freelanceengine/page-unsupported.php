<?php 
/**
 * Template Name: Unsupported Browsers
 */
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<link rel="stylesheet" href="<?php echo TEMPLATEURL ?>/css/unsupported.css">
	<title><?php _e( 'Unsupported Browsers' , ET_DOMAIN ); ?> | <?php echo bloginfo( 'name' ) ?></title>
</head>
<body <?php body_class('browser-unsupported') ?>>
	<?php
	the_post();
	$img = TEMPLATEURL . '/img/warning.png';
	$content =  <<<HTML
	<img src="{$img}" alt="warning">
		<h1>Whoa!</h1>
		<p>You're using an ancient browser. It has known security flaws <br/> and may not display all features of this and other websites.<br/>
	Please update your browser by clicking on icons below:</p>
		<div class="browser-links">
			<a href="http://windows.microsoft.com/en-US/internet-explorer/download-ie" rel="nofollow" target="_blank" class="icon-ie"><span>Internet Explorer</span></a>
			<a href="http://support.apple.com/downloads/#safari" rel="nofollow" target="_blank" class="icon-safari"><span>Apple Safari</span></a>
			<a href="http://www.mozilla.org/en-US/firefox/new/" rel="nofollow" target="_blank" class="icon-firefox"><span>Mozilla Firefox</span></a>
			<a href="https://www.google.com/intl/en/chrome/browser/" rel="nofollow" target="_blank" class="icon-chrome"><span>Google Chrome</span></a>
			<a href="http://www.opera.com/browser/download/" rel="nofollow" target="_blank" class="icon-opera"><span>Opera</span></a>
		<div>
HTML;

	$post_content	=	 '';
	if($post_content != '') { the_content();} else echo $content;
	?>
</body>
</html>