<?php
/*
Plugin Name: CE Ebay Import
Plugin URI: www.enginethemes.com
Description: Import items from eBay.com into your site (requires: CE 1.1).
Version: 1.1.1
Author: EngineThemes team
Author URI: www.enginethemes.com
License: GPL2
*/

define('CR_IMPORT_PATH',dirname(__FILE__));
define('CE_IMPORT_URL', plugins_url( basename(dirname(__FILE__)) ));

require_once dirname(__FILE__) . '/update.php';

add_action('after_setup_theme', 'ce_setup_plugins');
function ce_setup_plugins () {
	require_once dirname(__FILE__) . '/inc/index.php';
	if(class_exists("CE_Ebay"))
		new CE_Ebay();
}

function ce_ebay_dropdow_site($args = array()){
	$sited = (isset($_COOKIE['ebay_site'])) ? $_COOKIE['ebay_site'] : 0;
	$default 		= array(
		'echo' 		=> true,
		'selected' 	=> $sited,
		'class' 	=> ''
	);
	$args 	= wp_parse_args($args,$default);
	extract($args);
	$locations = array(
					0 	=> array('EBAY-US' 	,'eBay United States - USD'),
					2 	=> array('EBAY-ENCA','eBay Canada (English)- CAD'),
					3 	=> array('EBAY-GB','eBay United Kingdom - GBP'),
					15 	=> array('EBAY-AU','eBay Australia - AUD'),
					16 	=> array('EBAY-AT','eBay Austria - EUR'),
					23 	=> array('EBAY-FRBE','eBay Belgium (French) - EUR'),
					71 	=> array('EBAY-FR','eBay France - EUR'),
					77 	=> array('EBAY-DE','eBay Germany - EUR'),
					//100 => array('EBAY-MOTOR','eBay Motors 	'),
					101 => array('EBAY-IT','eBay  Italy - EUR'),
					123 => array('EBAY-NLBE','eBay Belgium (Dutch) - EUR'),
					146 => array('EBAY-NL','eBay Netherlands - EUR'),
					186 => array('EBAY-ES','eBay Spain - EUR'),
					193 => array('EBAY-CH','eBay Switzerland - CHF'),
					201 => array('EBAY-HK','eBay Hong Kong - HKD'),
					203 => array('EBAY-IN','eBay India - INR'),
					205 => array('EBAY-IE', 'eBay Ireland - EUR'),
					207 => array('EBAY-MY','eBay Malaysia - MYR'),
					210 => array('EBAY-FRCA','eBay Canada (French)  CAD/USD'),
					211 => array('EBAY-PH','eBay Philippines - PHP'),
					212 => array('EBAY-PL','eBay Poland - PLN'),
					216 => array('EBAY-SG','eBay Singapore - SGD')
		);
	$html 	='';
	$html 	.='<div class="ce-ebay-select select-style et-button-select '.$class.'" id="wrap-site" >';
		$html 	.='<select name="site" class="ebay_site" title=" '.__("ALL CATEGORIES", ET_DOMAIN).'">';

			foreach($locations as $key=>$value){
				$select = '';
				if($selected == $key)
					$select = 'selected = "selected"';
				$html .='<option rel ="'.$key.'" '.$select.' value="'.$value[0].'">';
				$html .= $value[1];
				$html .='</option>';
			}
		$html .='</select>';
		// if(!$echoselect		// 	$html.='<span class="select">'.__('United States - EBAY-US - USD','ET_DOMAIN').'</span>';
	$html .='</div>';
	if($echo)
		echo $html;
	else
		return $html;
}

function ce_ebay_dropdow_categories($args = array()){

	$sited 		= (isset($_COOKIE['ebay_site'])) ? $_COOKIE['ebay_site'] : 0;
	$default 	= array('echo' => true,'site' => $sited ,'class'=> '');
	$args 		= wp_parse_args($args,$default);
	extract($args);
	$categories = CE_Ebay_API::get_categories($site);
	$html ='';
	if (is_array($categories)){
		$html .='<div class="ce-ebay-select select-style et-button-select '.$class.'" id="wrap-cat" >';
			$html.='<select id="category"  name="category" class="ebay_category" title="'.__("ALL CATEGORIES", ET_DOMAIN).'">';
				$html.='<option class=""  value="-1">'.__("All Categories", ET_DOMAIN).'</option>';

				foreach($categories as $cat){
					$select = '';
					if($site == $cat[0]->CategoryID)
						$select = 'selected = "selected"';
					if($cat[0]->CategoryID == -1)
					continue;
					$html.= '<option value="'.$cat[0]->CategoryID.'" '.$select.'>';
					$html.= $cat[0]->CategoryName;
					$html.= '</option>';
			}
			$html.='</select>';
			if(!$echo)
				$html.='<span class="select">'.__('All Categories','ET_DOMAIN').'</span>';
		$html.='</div>';
	}
	if($echo)
	echo $html;
	else
	return $html;
}


function ce_ebay_pagination($query = '',$curent_paged){
	if($query == '') {
		global $wp_query;
		$query	=	$wp_query;
	}
	$big = 999999999; // need an unlikely integer

	$paginate	=	 paginate_links( array(
		'base' 			=> str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format' 		=> '?paged=%#%',
		'current' 		=> max( 1, $curent_paged ),
		'total' 		=> $query->max_num_pages,
		'type'			=> 'array',
		'prev_text'    	=> 'Prev',
		'next_text'    	=> 'Next',
		'end_size' 		=> 	2,
		'mid_size' 		=> 	1
	) );
	$html 	= '';
	if($paginate) {
		$html.= ' <ul class="pagination">';
		foreach ($paginate as $key => $value) {
			$html.= '<li>'.$value. '</li>'	;
		}
		$html.='</ul>';
	}
	return $html;
}

//date_default_timezone_set('GMT');

function getPrettyTimeFromEbayTime($eBayTimeString){
    // Input is of form 'PT12M25S'
    $matchAry = array(); // initialize array which will be filled in preg_match
    $pattern = "#P([0-9]{0,3}D)?T([0-9]?[0-9]H)?([0-9]?[0-9]M)?([0-9]?[0-9]S)#msiU";
    preg_match($pattern, $eBayTimeString, $matchAry);

    $days  = (int) $matchAry[1];
    $hours = (int) $matchAry[2];
    $min   = (int) $matchAry[3];    // $matchAry[3] is of form 55M - cast to int
    $sec   = (int) $matchAry[4];

    $retnStr = '';
    if ($days)  { $retnStr .= "$days day"    . pluralS($days);  }
    if ($hours) { $retnStr .= " $hours hour" . pluralS($hours); }
    if ($min)   { $retnStr .= " $min minute" . pluralS($min);   }
    if ($sec)   { $retnStr .= " $sec second" . pluralS($sec);   }
    $seconds = $days*24*60*60 + $hours*60*60 + $min*60 + $sec;
    return $seconds;
} // function

function pluralS($intIn) {
    // if $intIn > 1 return an 's', else return null string
    if ($intIn > 1) {
        return 's';
    } else {
        return '';
    }
} // function



?>