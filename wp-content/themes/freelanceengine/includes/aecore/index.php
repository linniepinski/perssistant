<?php
/**
 * return core url
 */
if(!function_exists('ae_get_url')) {
    function ae_get_url() {
        return TEMPLATEURL . '/includes/aecore';
    }
}
/**
 * get template part
 */
if(!function_exists('ae_get_template_part')) {
    function ae_get_template_part($slug, $name) {
        if ($slug) $slug = 'includes/aecore/template/' . $slug;
        get_template_part($slug, $name);
    }
}
/**
 * get core function
 */
if(file_exists( dirname( __FILE__ ).'/bootstrap.php' )) {
    require_once dirname( __FILE__ ).'/bootstrap.php';
}