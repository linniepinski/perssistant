<?php
class CE_Import_Base {

	var $args;
	var $hookname;
	var $sections = array();
	const AJAX_PREFIX = 'wp_ajax_';
	const AJAX_NOPRIV_PREFIX = 'wp_ajax_nopriv_';

	const FILTER_SCRIPT = 'et_enqueue_script';
	const FILTER_STYLE = 'et_enqueue_style';

	public function __construct($args = array() ){
		$this->menu_args = wp_parse_args( $args, array(
			'menu_title' => 'Ebay',
			'page_title' => 'CE IMPORT',
			'slug' 			=> 'ebay-import',
			'callback' 	=> array($this, 'menu_view')
		) );
		$this->add_action('et_admin_menu', 'add_option_page');

	}
	protected function add_action($hook, $callback, $priority = 10, $accepted_args = 1){
		add_action($hook, array($this, $callback), $priority, $accepted_args);
	}
	protected function add_ajax($hook, $callback, $priv = true, $no_priv = true, $priority = 10, $accepted_args = 1){
		if ( $priv )
			$this->add_action( self::AJAX_PREFIX . $hook, $callback, $priority, $accepted_args );
		if ( $no_priv )
			$this->add_action( self::AJAX_NOPRIV_PREFIX . $hook, $callback, $priority, $accepted_args );
	}
	protected function add_filter($hook, $callback, $priority = 10, $accepted_args = 1){
		add_filter($hook, array($this, $callback), $priority, $accepted_args);
	}
	public function add_option_page(){
		// default args
		et_register_menu_section($this->menu_name, $this->menu_args);
	}

}

class Ebay_Schedule extends CE_Import_Base {
	function __construct(){

		register_activation_hook( __FILE__, array($this,'ebay_activation') );
		register_deactivation_hook( __FILE__, array($this,'ebay_deactivation') );
		$this->add_filter( 'cron_schedules', 'cron_custom_time',11,1 );
		$this->add_action( 'ce_custom_event_hook', 'ce_run_custom_event_hook' );
		$this->add_action('wp', 'ce_setup_schedule' );

	}
	public function ebay_activation(){
		wp_schedule_event( time(), 'custom', 'ce_custom_event_hook' );
	}
	public function ebay_deactivation(){
		wp_clear_scheduled_hook( 'ce_custom_event_hook' );
	}
	function cron_custom_time( $schedules ) {
 	// Adds once weekly to the existing schedules.
		$days = get_option('ce_ebay_days_run',5);
		$days = (float) 2*60*60;
	 	$schedules['custom'] = array(
	 		'interval' => $days,
	 		'display' => __( 'CE Import Schedule' )
	 	);
	 	return $schedules;
 	}
 	public function  ce_run_custom_event_hook(){

 		$schedules = CE_Ebay::get_schedule_option();
 		foreach ($schedules as $key => $option) {
 			if($option['ON'])
 			CE_Ebay_API::ebay_search_import($option);
 		}

 	}
 	function ce_setup_schedule() {
		if ( ! wp_next_scheduled( 'ce_custom_event_hook' ) ) {
			wp_schedule_event( time(), 'custom', 'ce_custom_event_hook');
		}
	}

}

?>
