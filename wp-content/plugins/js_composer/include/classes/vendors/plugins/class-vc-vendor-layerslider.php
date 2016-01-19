<?php
  /**
   * LayerSlider loader.
   *
   */
class Vc_Vendor_Layerslider implements Vc_Vendor_Interface {
	protected static $instanceIndex = 1;
	public function load() {
		add_action('vc_after_mapping', array(&$this, 'buildShortcode'));

	}
	public function buildShortcode() {
		if ( is_plugin_active( 'LayerSlider/layerslider.php' ) ) {
			global $wpdb;
			$ls = $wpdb->get_results(
				"
  SELECT id, name, date_c
  FROM " . $wpdb->prefix . "layerslider
  WHERE flag_hidden = '0' AND flag_deleted = '0'
  ORDER BY date_c ASC LIMIT 999
  "
			);
			$layer_sliders = array();
			if ( $ls ) {
				foreach ( $ls as $slider ) {
					$layer_sliders[$slider->name] = $slider->id;
				}
			} else {
				$layer_sliders[__( 'No sliders found', 'js_composer' )] = 0;
			}
			vc_map( array(
				'base' => 'layerslider_vc',
				'name' => __( 'Layer Slider', 'js_composer' ),
				'icon' => 'icon-wpb-layerslider',
				'category' => __( 'Content', 'js_composer' ),
				'description' => __( 'Place LayerSlider', 'js_composer' ),
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __( 'Widget title', 'js_composer' ),
						'param_name' => 'title',
						'description' => __( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'js_composer' )
					),
					array(
						'type' => 'dropdown',
						'heading' => __( 'LayerSlider ID', 'js_composer' ),
						'param_name' => 'id',
						'admin_label' => true,
						'value' => $layer_sliders,
						'description' => __( 'Select your LayerSlider.', 'js_composer' )
					),
					array(
						'type' => 'textfield',
						'heading' => __( 'Extra class name', 'js_composer' ),
						'param_name' => 'el_class',
						'description' => __( 'If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'js_composer' )
					)
				)
			) );
			// Load layer slider shortcode && change id
			if(vc_is_frontend_ajax() || vc_is_frontend_editor()) {
				include LS_ROOT_PATH.'/wp/shortcodes.php';
				add_filter('vc_layerslider_shortcode', array(&$this, 'setId'));
			}
		} // if layer slider plugin active
	}
	public function setId($output) {
		return preg_replace('/(layerslider_\d+)/', '$1_'.time().'_'.self::$instanceIndex++, $output);
	}
}