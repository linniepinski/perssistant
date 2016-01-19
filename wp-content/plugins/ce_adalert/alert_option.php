<?php

Class CE_Alert_Option{
	private static $option 		= NULL;
	private static $instance 	= NULL;
	public static function get_instance() {
		if(NULL == self::$instance)
			self::$instance = new CE_Alert_Option();
		return self::$instance;
	}

	function __construct(){
		//add_action('wp_ajax_save-alert-option',array($this,'save_alert_option'));
	}
	/**
	 * get option adlert.
	 * @return array
	 */
	public static function get_option(){

		$default = array(
			'schedule' 		=> 'daily',
			'number_ads' 	=> 5,
			'number_emails' => 100);
		$options 	= get_option('ce_alert_option',array());
		$return 	= wp_parse_args($options,$default);

		return $return;
	}

	/**
	 * set option alert
	 * @param array
	 */
	function set_option($args){

		$old_option = self::get_option();
		$option 	= wp_parse_args($args,$old_option);
		update_option('ce_alert_option',$option);

	}

}

function ce_alert_pagination ($query,$paged, $url) {

	$big = 999999999; // need an unlikely integer
	$li = '';
	$class ='';
	$paged = (int) isset($_GET['paged']) ? $_GET['paged'] : 1;
	if($paged != 1){

		$prev =  ($paged - 2 > 1) ? '... ' :'';
		$li .='<a '.$class.' href="'.$url.'&paged=1"><i>First</i></a>'.$prev;
	}

	for($i=1; $i < $query->max_num_pages; $i++){

		$class = ($paged == $i)? "clas= 'page-item current-page' " : " 'page-item' ";

		if($paged == $i)
			$li .= '<span>'.$i.' </span>';
		else if($i - 3 < $paged && $i + 3 > $paged)
			$li .='<a '.$class.' href="'.$url.'&paged='.$i.'"> '.$i.'</a>';
	}
	if($paged != $query->max_num_pages){
		$cham =  ($query->max_num_pages - 3 > $paged) ? '... ' :'';

		$li .=$cham.'<a '.$class.' href="'.$url.'&paged='.$query->max_num_pages.'">'.$query->max_num_pages.' </a>';
	} else if($paged == $query->max_num_pages){
		$li .='<span>'.$query->max_num_pages.' </span>';
	}
	echo $li;
}

?>