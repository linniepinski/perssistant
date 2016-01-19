<?php
add_action('wp_enqueue_scripts', 'register_map_script');

function register_map_script() {
    $mininfy = get_theme_mod('ce_minify', 0);
    if (!$mininfy) {
        wp_register_script("only-backbone", "//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.2/backbone-min.js");
        wp_register_script('ce-map-shorcode', plugin_dir_url(__FILE__) . '/js/map-shortcode.js', array(
            'et-googlemap-api', 'backbone'
        ) , CE_MAP_VER, true);
    }else{
        wp_register_script('ce-map-shorcode', plugin_dir_url(__FILE__) . '/js/map-shortcode.js', array(
            'et-googlemap-api', 'backbone' , 'ce'
        ) , CE_MAP_VER, true);
    }
}
add_filter('ce_minify_source_path', 'map_shortcode_add_mininfy_path', 200);
function map_shortcode_add_mininfy_path($mini_path) {
    $mininfy = get_theme_mod('ce_minify', 0);    
    // $mini_path['mobile-js'][]   = '//cdnjs.cloudflare.com/ajax/libs/backbone.js/1.1.2/backbone-min.js';
    // $mini_path['mobile-js'][] = CE_MAP_PATH . '/js/map-shortcode.js';
    return $mini_path;
}
add_shortcode('ce_map', 'ce_map_shortcode');
function ce_map_shortcode($atts) {
    $address = 'New York';
    if (is_page_template("page-post-ad.php") && !isset($_GET['id']) && is_user_logged_in()) {
        global $user_ID;
        $address = get_user_meta($user_ID, 'et_address', true);
    }
    
    $default = array(
        'w' => '100%',
        'h' => '300px',
        'lat' => '',
        'lng' => '',
        'zoom' => 20,
        'address' => $address,
        'is_single' => 0
    );
    
    $atts = wp_parse_args($atts, $default);
    extract($atts);
    $mininfy = get_theme_mod('ce_minify', 0);
    
    // if(!$mininfy) {
        wp_enqueue_script('ce-map-shorcode');
        wp_localize_script('ce-map-shorcode', 'map_short_code', $atts);
    // }
    
    // if (!is_page_template('page-post-ad.php')) {
        
    //     wp_enqueue_script('gmap');
    //     wp_enqueue_script('marker_cluster', plugin_dir_url(__FILE__) . '/js/marker-cluster.js', array(
    //         'gmap'
    //     ) , '1.0');
    //     wp_enqueue_style('map-style', plugin_dir_url(__FILE__) . '/css/map-front.css', true, CE_MAP_VER);
    // }
    $html = '<div id="map-shortcode" class="map-shortcode" style="width : ' . $w . '; heigth: ' . $h . '; margin-top:0px;">';
    $html.= '</div>';
    
    return $html;
}
?>