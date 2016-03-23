<?php
$output = $title = $interval = $el_class = $tab_style = '';
/////
global $current_user;
$user_role = ae_user_role($current_user->ID);
/////
extract( shortcode_atts( array(
    'title'     => '',
    'interval'  => 0,
    'tab_style' => 'none',
    'el_class'  => ''
), $atts ) );
//render button
$button = '';
if( $tab_style == "profile" ){
    ob_start();
    fre_profile_button();
    $button = ob_get_clean();
}
if ( $tab_style == "project" ){
    ob_start();
    $button = fre_project_button();
    $button = ob_get_clean();
}

wp_enqueue_script( 'jquery-ui-tabs' );

$el_class = $this->getExtraClass( $el_class );

$element = 'wpb_tabs';
if ( 'vc_tour' == $this->shortcode ) $element = 'wpb_tour';

// Extract tab titles
preg_match_all( '/vc_tab([^\]]+)/i', $content, $matches, PREG_OFFSET_CAPTURE );
$tab_titles = array();
/**
 * vc_tabs
 *
 */
////////////////////// hide tab from user_{role}
if('hide_'.$user_role == trim($el_class)){
    goto end;
}
//////////////////////
if ( isset( $matches[1] ) ) {
	$tab_titles = $matches[1];
}

$tabs_nav = '';
$tabs_nav .= '<ul class="wpb_tabs_nav ui-tabs-nav vc_clearfix nav nav-tabs col-md-6 col-sm-8 col-xs-12 '.$tab_style.'">';
foreach ( $tab_titles as $tab ) {
	$tab_atts = shortcode_parse_atts($tab[0]);
	if(isset($tab_atts['title'])) {
		$tabs_nav .= '<li class="col-xs-6 col-sm-5 col-md-5"><a href="#tab-' . ( isset( $tab_atts['tab_id'] ) ? $tab_atts['tab_id'] : sanitize_title( $tab_atts['title'] ) ) . '">' . $tab_atts['title'] . '</a></li>';
	}
}
$tabs_nav .= '</ul>' . "\n";

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, trim( $element . ' wpb_content_element ' . $el_class ), $this->settings['base'], $atts );

$output .= "\n\t" . '<div class="' . $css_class . '" data-interval="' . $interval . '">';
$output .= "\n\t\t" . '<div class="wpb_wrapper wpb_tour_tabs_wrapper ui-tabs vc_clearfix">';
//modify from here
$output .= "\n\t\t" . '<div class="number-project-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12" style="padding-top:40px;">';

$output .= wpb_widget_title( array( 'title' => $title, 'extraclass' => $element . '_heading number-project' ) );
// modify here
$output .= "\n\t\t" . "<div class='nav-tabs-project'>"; 
$output .= "\n\t\t\t " . $button;
$output .= "\n\t\t\t" . $tabs_nav;
if($element == 'wpb_tour') { 
// tab content
$output .= "\n\t\t\t" . wpb_js_remove_wpautop( $content );

$output .= "\n\t\t\t" . '<div class="wpb_tour_next_prev_nav vc_clearfix"> <span class="wpb_prev_slide"><a href="#prev" title="' . __( 'Previous tab', 'vc_tabs' ) . '">' . __( 'Previous tab', 'vc_tabs' ) . '</a></span> <span class="wpb_next_slide"><a href="#next" title="' . __( 'Next tab', 'vc_tabs' ) . '">' . __( 'Next tab', 'vc_tabs' ) . '</a></span></div>';

}

// modify here
$output .= "\n\t\t" . "</div>";

$output .= '</div>
                    </div>
                </div>
            </div> ';
           // endheader
if($element != 'wpb_tour') {
$output .= '<div class="container">';
// tab content
$output .= "\n\t\t\t" . wpb_js_remove_wpautop( $content );

$output .= '</div>';

}





$output .= "\n\t\t" . '</div> ' . $this->endBlockComment( '.wpb_wrapper' );
$output .= "\n\t" . '</div> ' . $this->endBlockComment( $element );

//////////////
end:
/////////////////
echo $output;