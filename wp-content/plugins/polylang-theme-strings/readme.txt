=== Polylang Theme Strings ===
Contributors: modeewine
Donate link: http://modeewine.com/en-donation
Tags: extension, polylang, multilingual, translate, translation, language, multilanguage, international, localization
Requires at least: 3.8
Tested up to: 4.3
Stable tag: 2.1
License: GPL2

Extension for Polylang plugin

== Description ==

= What is «Polylang Theme Strings» and why is he needed?  =

This plugin gives additional features to the plugin Polylang. It automatically scans all templates files and scripts of the active WP theme for available strings that can be translated, for example:

* `pll__('...');`
* `pll_e('...');`

and adds them to the Polylang registery, after what you can manage the translation of finded strings through the administration panel. It will make your life easier for the development of multilanguage’s projects, because you will not need to enter the needed strings to translate manually – the plugin will do all the work for you.

= How works «Polylang Theme Strings»? =

You have to install the plugins «Polylang» and «Polylang Theme Strings» on your multilanguage WordPress CMS project and they have to be both active. When you are in the settings of plugin (Polylang) in the tab «Strings translation» the «Polylang Theme Strings» scans automatically the active theme of your project, find all the code strings that needed to be translated, adds them to the register and display them on that page to give the user the ability to translate these strings.

Like you can see, the «Polylang Theme Strings» is perfectly integrate with the «Polylang» plugin and works in automatically mode – it is comfortable, simple, and useful!

Learn more in <http://modeewine.com/en-polylang-theme-strings>.

== Installation ==

1. Make sure you are using WordPress 3.8 or later and that your server is running PHP 5.0 or later.
1. Install multilingual plugin «Polylang» and activate it.
1. Download the plugin «Polylang Theme Strings».
1. Extract all the files.
1. Upload everything (keeping the directory structure) to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the languages (Polylang) settings page.
1. When you are in the settings of plugin (Polylang) in the tab «Strings translation» the «Polylang Theme Strings» scans automatically the active theme of your project, find all the code strings that needed to be translated, adds them to the register and display them on that page to give the user the ability to translate these strings.
1. Learn more in <http://modeewine.com/en-polylang-theme-strings>.

== Screenshots ==

1. Screen of «Polylang» strings translate page settings and when «Polylang Theme Strings» in action.

== Changelog ==

= 2.1 (2015-09-01) =

* Absolute compatibility with WordPress 4.3.
* Partially improved code.

= 2.0 (2015-06-21) =

* Completely remade the search strings-translations logic in the themes.
* In the languages (Polylang) settings page: the search is performed on all themes in your project.
* Optimized initialization strings-translations for the active theme.
* Improved code.

= 1.1 (2015-06-12) =

* Fixed bug when removing the plugin from the admin panel.
* Improved code.

= 1.0 (2015-05-29) =

* First release.
