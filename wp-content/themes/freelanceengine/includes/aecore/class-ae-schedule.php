<?php
class AE_Schedule extends AE_Base {

	static protected $cron_time	;
	static protected $cron_name	=	'ce_arhive_ad';
	protected $cron_hook	=	'ce_ads_archived_expireds';

	// static $post_type			=	'ad';
	/**
	 * init a schedule and set post type will be expire 
	 * @param string $post_type
	 * @since 1.3
	 * @author Dakachi
	 */
	function __construct( $post_type = 'ad' ) {
		$this->post_type = $post_type;
		$this->add_filter( 'cron_schedules',  'add_cron_time');
		$this->add_action('init', 'schedule_events', 100);

		$this->cron_hook = $this->post_type.'_archived_expireds';
		$this->add_action( $this->cron_hook,'archive_ad' );

		self::$cron_time	=	3600*4;
	}

	/**
	 * register a cron for run schedule archive expired ads
	*/
	function add_cron_time () {
		$schedules[self::$cron_name] = array(
	 		'interval' =>  self::$cron_time ,
	 		'display' => 'AE Archive Expired Ad cron time'
	 	);
	 	return $schedules;
	}

    /**
     * Schedule event
     */
	function schedule_events () {
		wp_clear_scheduled_hook($this->cron_hook);
		if ( !wp_next_scheduled( $this->cron_hook ) ){
			$tomorrow = strtotime( date( 'Y-m-d 00:00:00', strtotime('now')) );
			wp_schedule_event( time() , self::$cron_name, $this->cron_hook );
		}
	}	

	/**
	 * archive expired ad
	*/
	public  function archive_ad () {
		global $wpdb, $et_global, $post;
		$post_type = $this->post_type;
		$current = date('Y-m-d H:i:s', current_time('timestamp') );
		$sql = "SELECT DISTINCT ID FROM {$wpdb->posts} as p 
				INNER JOIN {$wpdb->postmeta} as mt ON mt.post_id = p.ID AND mt.meta_key = 'et_expired_date' 
				WHERE 	(p.post_type = '{$post_type}') 	AND 
						(p.post_status = 'publish') 			AND
						(mt.meta_value < '{$current}')			AND 
						(mt.meta_value != '' ) " ;

		$archived_ads = $wpdb->get_results($sql);
		
		$count = 0;
		update_option('cron_time', time());
		$ar	=	array();
		foreach ($archived_ads as $key =>  $ad) {
			// perform approval for found job
			// $return	=	CE_Ads::update( array( 'ID' => $ad->ID , 'post_status' => 'archive', 'change_status' => 'change_status' ));
			wp_update_post( array( 'ID' => $ad->ID , 'post_status' => 'archive') );
			$count++;
		}
		//update_option ('je_schedule_log', $current );
		return $count;
	}

}