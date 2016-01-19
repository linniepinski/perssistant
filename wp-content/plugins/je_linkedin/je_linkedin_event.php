<?php
class JE_Import_LinkedIn_ScheduleEvent extends JE_LINKEDIN_IMPORT {
	function __construct() {
		add_filter( 'cron_schedules', array($this, 'linkedin_cron_schedules') );
		add_action('init', array($this, 'schedule_events'));
		add_action('et_auto_main_import_linkedin', array($this, 'main_import_schedule'));
		add_action('et_auto_import_linkedin', array($this, 'import_schedule'));
		add_action('et_auto_delete_linkedin', array($this, 'event_delete_old_jobs'));
	}

	function event_delete_old_jobs(){
		$this->delete_old_jobs();
	}

	public function linkedin_cron_schedules($schedules){
		$recurrence = get_option('et_linkedin_recurrence', 24);
		$recurrence = 60* 60 * $recurrence;
		$schedules['linkedin'] = array(
			'interval' => $recurrence,
			'display' => __('LinkedIn Recurrence')
			);
		$schedules['single_linkedin_schedule'] = array(
	 		'interval' =>12 ,
	 		'display' => __( 'Custom Single LinkedIn Schedule' )
	 	);
		return $schedules;
	}

	public function schedule_events() {
		
		// schedule importation
		if (!wp_next_scheduled( 'et_auto_main_import_linkedin') ){
			
			$this->schedule_activation();
		}

		// schedule delete
		if ( !wp_next_scheduled( 'et_auto_delete_linkedin') ){
			$time = mktime(0, 0, 0, date('n'), date('j') + 1);
			wp_schedule_event( $time, 'daily', 'et_auto_delete_linkedin');
		}
	}

	public function main_import_schedule () {
		$this->import_schedule(1);
		$recurrence = get_option('et_linkedin_recurrence', 12);
		$recurrence = 60* 60 * $recurrence;
		wp_schedule_event( time()+ $recurrence, 'single_linkedin_schedule', 'et_auto_import_linkedin');
		update_option( 'je_linkedin_used_schedule', array() );
	}

	function import_schedule($all=0){
		// delete_option('je_linkedin_used_schedule');
		$used_schedule	=	get_option('je_linkedin_used_schedule', array());
		
		$schedules = get_posts(array(
				'post_type' 	=> 'linkedin_schedule',
				'post_status' 	=> array('draft','publish','pending'),
				'order' 		=> 'asc',
				'post__not_in'	=> $used_schedule,
				'numberposts'	=> ($all ? -1 : 2)
			));
		foreach ($schedules as $i => $row) {
			array_push($used_schedule, $row->ID);
			
			$schedule = $this->build_schedule_item($row);
			if (!$schedule['active']) continue;
			$params	=	array(
 				'keywords'			=>$schedule['keywords'],
 				'job-title'			=>$schedule['jobtitle'],
 				'company-name'		=>$schedule['companyname'],
 				'country-code'		=> $schedule['countrycode'],
 				'postal-code'		=>$schedule['postalcode'],
 		       );
			   if($schedule['category']) $params['facet'] ='industry,'.$schedule['industry'];
			 
			 $this->fetch_job(http_build_query($params),$schedule['author'],$schedule['cat'], $schedule['type']);

		}

		update_option( 'je_linkedin_used_schedule', $used_schedule );

	}

}