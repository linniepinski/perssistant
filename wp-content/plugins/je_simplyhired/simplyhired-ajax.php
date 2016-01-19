<?php
class JE_SimplyHired_Ajax extends JE_SimplyHired {

	function __construct () {
		parent::__construct() ;
		add_action( 'wp_ajax_update-simply-hired-setting', array($this, 'update_settings') ) ;
		add_action( 'wp_ajax_simplyhired-search-job', array($this, 'search_job') ) ;
		add_action( 'wp_ajax_simplyhired-save-jobs', array($this,'manual_import_job') );
		add_action ('wp_ajax_simplyhired-job-change-page', array($this, 'change_page'));
		add_action ('wp_ajax_simplyhired-delete-jobs', array($this, 'delete_simplyhired_jobs'));		
		add_action ('wp_ajax_update-job-limit-date' , array($this, 'update_job_limit_date'));
		add_action( 'wp_ajax_simplyhired-delete-old-jobs', array($this, 'delete_old_jobs') );
		/**
		 * schedule
		*/
		add_action('wp_ajax_update-simplyhired-import-schedule', array($this, 'update_schedule') );
		add_action ('wp_ajax_simplyhired-detele-schedule', array($this, 'delete_schedule'));
		add_action ('wp_ajax_simplyhired-off-schedule', array($this, 'on_off_schedule'));
		add_action ('wp_ajax_simplyhired-update-recurrent-time', array($this, 'update_recurrent_time'));

	}

	/**
	 * ajax callback update setting
	*/

	function update_settings () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if ( empty($_POST) || !wp_verify_nonce($_POST['simply_hired_setting'],'update_simply_hired_setting') ){
			$response	=	array(
					'success'	=> false ,
					'msg'		=> __('Permission denied!', ET_DOMAIN)
				);
		}

		$settings	=	array();
		$settings['pshid']	=	$_POST['pshid'];
		$settings['jbd']	=	$_POST['jbd'];
		$settings['ssty']	=	$_POST['ssty'];
		$settings['cflg']	=	$_POST['cflg'];

		$this->save_settings($settings);

		$response	=	array('success' => true);

		echo json_encode($response);
		exit;		
	}


	function search_job () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		$api_url	=	$this->get_api_url ();
		$api		=	$this->get_settings_str ();

		$s_data		=	$_REQUEST['data'];
		$s_data		=	explode('&', $s_data);

		$s		=	array();
		$s_str	=	'';
		foreach ($s_data as $key => $value) {
			$v	=	explode('=', $value);
			$s[$v[0]]	=	urldecode($v[1]);
			if( $v[1]  != '') {
				$nvp	=	str_replace('=', '-', $value);
				$s_str		.=	'/'.$nvp;
			}
		}

		$this->save_search_string ($s);

		$search_str	=	$api_url.$s_str.$api;
		
		$job_data	=	$this->simplyhired_job_sync ($search_str);

		echo json_encode($job_data);

		exit;
		
	}

	public function manual_import_job () {
		
		try {
			// validate user's permission
			if(!current_user_can ('manage_options') || !wp_verify_nonce($_POST['simplyhired_manual_import_job'],'save_import_job_manual' )) 
				throw new Exception(__('Permission Denied', ET_DOMAIN), '402');

			$data 	= $_POST;
			$count 	= 0;
			
			$import			=	$data['import'];
			$import_author	=	$data['import_author'];
			foreach ((array)$import as $job) {
				//print_r($job['allow']);
				if ( $job['allow'] == 1 ){
					$count += $this->save_job($job, $import_author);
				}
			}

			$response = array(
				'success' 	=> true,
				'msg' 		=> __('Jobs have been imported to your site.', ET_DOMAIN),
				'code' 		=> '200',
				'data' 		=> array(
					'count' => $count
					)
				);

		} catch (Exception $e) {
			$response = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage(),
				'code' 		=> $e->getCode()
				);
		}

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($response);
		exit;
	}

	public function delete_simplyhired_jobs(){
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}

		$data		=	$_REQUEST['content'];
				
 		$count 		= 0;
 		foreach ($data['ids'] as $id) {
 			if (wp_delete_post($id, true)){
 				$count++;
 			}
 		}
 		$page_max 	= $count >= 10 ? $data['page_max'] - 1 : $data['page_max'];
 		$page 		= min($data['page'], $page_max);
 		$resp		=	$this->query_page ($page);

 		if(count($data['ids']) == $count)
 			$resp['success'] = true;
 		
		wp_send_json( $resp );
	}	

	function change_page () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}

		$data		=	$_REQUEST['content'];
 		
 		$resp		=	$this->query_page ($data['page']);

		echo json_encode($resp);
		exit;
	}

	/**
	 * enable or disable schedule
	*/
	function on_off_schedule () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can ('manage_options')) {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Permission Denied', ET_DOMAIN) )
				)
			);
			exit;
		}
		$icon	=	'Q';
		$option	=	$this->get_schedule_option();

		if(isset($option[$_REQUEST['schedule_id']])) {
			$schedule	=	$option[$_REQUEST['schedule_id']];
			if(isset($schedule['ON']) && $schedule['ON'] == 0 ) {
				$schedule['ON']	=	1;
				$icon	=	'Q';
			} else {
				$schedule['ON']	=	0;
				$icon	=	'q';
			}
			$option[$_REQUEST['schedule_id']]	=	$schedule;
		}
		$this->update_schedule_option($option);

		echo json_encode (array (
					'success'	=> true,
					'icon'		=> $icon
				)
		);
		exit;
	}

	/**
	 * update recurrent time and schedule event
	*/
	function update_recurrent_time () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can ('manage_options')) {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Permission Denied', ET_DOMAIN) )
				)
			);
			exit;
		}
		update_option('et_simplyhired_recurrence', $_REQUEST['time']);
		$this->activate_schedule();

		echo json_encode(array('success' => true));
		exit;
	}

	/**
	 * ajax callback update schedule
	*/
	function update_schedule () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		try {
			// validate user's permission
			if(!current_user_can ('manage_options') || !wp_verify_nonce($_POST['simplyhired_update_schedule'],'update_schedule' )) 
				throw new Exception(__('Permission Denied', ET_DOMAIN), '402');
			unset($_POST['simplyhired_update_schedule']);
			unset($_POST['_wp_http_referer']);
			unset($_POST['action']);

			if($_POST['ws'] > 25) $_POST['ws']	=	25;
			if($_POST['q'] == '' && $_POST['lz'] == '' && $_POST['lc'] == '' && $_POST['ls'] == '')
				throw new Exception(__('Input Invalid!', ET_DOMAIN));
			if($_POST['import_author'] == '') {
				global $current_user;
				$_POST['import_author']	=	$current_user->ID;
				$_POST['author']	=	$current_user->user_login;
			}
			$schedule	=	$this->save_schedule ( $_POST);
			$response	=	array('success' => true , 'data' => $schedule);

		} catch (Exception $e) {
			$response	=	array('success' => false , 'msg' => $e->getMessage());
		}
		echo json_encode($response);
		exit;
	}

	/**
	 * delete simplyhired schedule setting
	*/
	function delete_schedule () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can ('manage_options')) {
			echo json_encode (array (
					'success'	=> false,
					'msg'		=> array('0' => __('Permission Denied', ET_DOMAIN) )
				)
			);
			exit;
		}
		$option	=	$this->get_schedule_option();
		if(isset($option[$_REQUEST['schedule_id']])) {
			unset($option[$_REQUEST['schedule_id']]);
		}
		$this->update_schedule_option($option);

		echo json_encode (array (
					'success'	=> true
				)
		);
		exit;
	}

	function delete_old_jobs () {
		
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}
		$count	=	$this->delete_job_from_date ();
		echo json_encode(array('success' => true, 'jobs_deteled' => $count ));
		exit;
	}

	function update_job_limit_date () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}

		update_option ('je_simplyhired_delete_days', $_POST['days']);
		//$this->add_delete_job_event ();
	}

}

class JE_Simplyhired_Schedule extends JE_SimplyHired {

	function __construct() {
		add_action('wp', array($this,'refesh_schedule_activation') );
		add_action('admin_init', array($this,'refesh_schedule_activation') );
		/**
		 * schedule event
		*/
		add_action('simplyhired_delete_job_event', array($this, 'delete_job_from_date'));
		add_action('simplyhired_import_schedule_event', array($this,'schedule_start') );
		add_action('simplyhired_import_single_schedule_event' , array($this, 'single_schedule_event'));
	}

	/**
	 * start the import schedule
	*/
	function schedule_start () {
		/**
		 * run all lost schedule
		*/
		$lost_schedule	=	get_option ('je_simplyhired_for_run_schedule_list', array());
		foreach ($lost_schedule as $key => $schedule) {
			$this->simplyhired_schedule ( $schedule );
		}

		$option	=	$this->get_schedule_option();
		update_option ('je_simplyhired_for_run_schedule_list', $option );
		/**
		 * add repeatly event to run each schedule after 30s
		*/
		wp_schedule_event( time() + 10 , 'custom_single_simplyhired_schedule' , 'simplyhired_import_single_schedule_event');
	}

	/**
	 * schedule function to run eache schedule
	*/
	function single_schedule_event () {
		$schedule_list	=	get_option ('je_simplyhired_for_run_schedule_list', array());
		
		if(!empty($schedule_list)) {
			$schedule	=	array_pop($schedule_list);
			$this->simplyhired_schedule ( $schedule );
		}
		update_option( 'je_simplyhired_for_run_schedule_list', $schedule_list );
	}

	function refesh_schedule_activation () {

		if(!wp_next_scheduled('simplyhired_delete_job_event')) {
			$this->add_delete_job_event ();
		}
		if(!wp_next_scheduled('simplyhired_import_schedule_event')) {
			$this->activate_schedule();
		}

		$schedule_list	=	get_option('je_simplyhired_for_run_schedule_list', array());
		if(empty($schedule_list)) {
			wp_clear_scheduled_hook('simplyhired_import_single_schedule_event');
		}
	}
}