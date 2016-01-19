<?php
class JE_Import_ScheduleEvent extends JE_Import {
	function __construct() {
		add_filter( 'cron_schedules', array($this, 'indeed_cron_schedules') );
		add_action('init', array($this, 'schedule_events'));
		add_action('et_auto_main_import_indeed', array($this, 'main_import_schedule'));
		add_action('et_auto_import_indeed', array($this, 'import_schedule'));
		add_action('et_auto_delete_indeed', array($this, 'event_delete_old_jobs'));
	}

	function event_delete_old_jobs(){
		$this->delete_old_jobs();
	}

	public function indeed_cron_schedules($schedules){
		$recurrence = get_option('et_indeed_recurrence', 7);
		$recurrence = 60*60*24*$recurrence;
		$schedules['indeed'] = array(
			'interval' => $recurrence,
			'display' => __('Indeed Recurrence')
			);
		$schedules['single_indeed_schedule'] = array(
	 		'interval' =>  30 ,
	 		'display' => __( 'Custom Single Indeed Schedule' )
	 	);
		return $schedules;
	}

	public function schedule_events() {
		// schedule importation
		if ( !wp_next_scheduled( 'et_auto_main_import_indeed') ){
			$this->schedule_activation();
		}

		// schedule delete
		if ( !wp_next_scheduled( 'et_auto_delete_indeed') ){
			$time = mktime(0, 0, 0, date('n'), date('j') + 1);
			wp_schedule_event( $time, 'daily', 'et_auto_delete_indeed');
		}
	}

	public function main_import_schedule () {
		$this->import_schedule(1);

		wp_schedule_event( time()+10, 'single_indeed_schedule', 'et_auto_import_indeed');
		update_option( 'je_indeed_used_schedule', array() );
	}

	function import_schedule($all=0){
		$used_schedule	=	get_option('je_indeed_used_schedule', array());
		$schedules = get_posts(array(
				'post_type' 	=> 'indeed_schedule',
				'post_status' 	=> array('draft','publish','pending'),
				'order' 		=> 'asc',
				'post__not_in'	=> $used_schedule,
				'numberposts'	=> ($all ? -1 : 2)
			));

		// fetch jobs from indeed
		$id 	= $this->get_indeed_publisher_id();
		$jobarr = array();
		$keys 	= array();
		foreach ($schedules as $i => $row) {
			array_push($used_schedule, $row->ID);
			$schedule = $this->build_schedule_item($row);
			if (!$schedule['active']) continue;

			$params = array(
				'q' 		=> $schedule['title'],
				'l' 		=> $schedule['location'],
				'co' 		=> $schedule['country'],
				'limit' 	=> $schedule['limit'],
				'fromage' 	=> get_option('et_indeed_recurrence', 7)
			);
			$resp = $this->indeed_job_sync(http_build_query($params), $id);

			// don't import if find no job
			if (empty($resp->results->result)) continue;
			$jobs 	= $resp->results->result;
			foreach ($jobs as $job) {
				$job = (array)$job;
				$jobarr[] = array(
					'jobtitle' => (string)$job['jobtitle'],
					'content' => (string)$job['snippet'],
					'date' => (string)$job['date'],
					'job_category' => $schedule['cat'],
					'job_type' => $schedule['type'],
					'formattedLocationFull' => (string)$job['formattedLocationFull'],
					'company' => (string)$job['company'],
					'url' => (string)$job['url'],
					'city' => (string)$job['city'],
					'country' => (string)$job['country'],
					'jobkey' => (string)$job['jobkey'],
				);
				$keys[] = $job['jobkey'];
			}
		}

		update_option( 'je_indeed_used_schedule', $used_schedule );

		// Importation
		// get the latest job in
		$latestjob = get_posts(array(
			'numberposts' 	=> -1,
			'post_type' 	=> 'job',
			'meta_query' 	=> array( array(
				'key' 		=> 'et_indeed_id',
				'value' 	=> $keys,
				'compare' 	=> 'IN'
				))
			));
		global $wpdb;
		$sql 		= "SELECT DISTINCT meta.meta_value FROM {$wpdb->postmeta} as meta WHERE meta.meta_key = 'et_indeed_id' AND meta.meta_value IN ('" . implode("','", $keys) ."') ";
		$results 	= $wpdb->get_results($sql, ARRAY_N);
		$dupkeys 	= array();
		foreach ($results as $row) {
			$dupkeys[] = $row[0];
		}

		foreach ($jobarr as $j => $item) {
			if (!in_array($item['jobkey'], $dupkeys))
				$this->save_indeed_jobs($item, $schedule['author']);
		}
	}

}