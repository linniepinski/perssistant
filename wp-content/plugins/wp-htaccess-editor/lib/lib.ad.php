<?php
if (!defined('ABSPATH')) die('You do not have sufficient permissions to access this file.');

/*** funkce pro zobrazení odkazu na domovskou stránku pluginu ***/
function WPHE_PluginLink()
{
	echo '<a class="plugin-link" href="http://wordpress.org/extend/plugins/wp-htaccess-editor/" title="'.__('Plugin homepage','wphe').'" target="_blank">'.__('Plugin homepage','wphe').'</a>';
}

/*** funkce pro zobrazení odkazu na domovskou stránku autora ***/
function WPHE_AuthorLink()
{
	echo '<a class="author-link" href="http://wp-blog.cz/" target="_blank">WP-blog.cz</a>';
}


/*** funkce pro zobrazení odkazu na příspěvky autorovi ***/
function WPHE_DonateLink()
{
	echo '<a class="author-link" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NSKURYL8JEVU8" target="_blank">'.__('Donate', 'wphe').'</a>';
}
