<?php 
class JE_LINKEDIN_IMPORT_AJAX extends JE_LINKEDIN_IMPORT {
	function __construct () {
		parent::__construct();
		/*
		* ajax sync indeed job
		*/
		add_action ('wp_ajax_update_linked_setting', array($this, 'update_settings'));
		add_action ('wp_ajax_linkedin_connecting', array($this, 'test_connecting'));
		add_action ('wp_ajax_linkedin-search-job', array($this, 'linkedin_search_job'));
		add_action ('wp_ajax_linkedin-delete-jobs', array($this, 'delete_linkedin_jobs'));
		add_action ('wp_ajax_linkedin-change-page', array($this, 'linkedin_change_page'));
		add_action('wp_ajax_linkedin-save-imported-jobs', array($this,'linkedin_save_imported_jobs'));
		add_action('wp_ajax_linkedin-new-schedule', array($this,'linkedin_save_schedule_item'));
		add_action('wp_ajax_linkedin-toggle-schedule', array($this, 'ajax_toggle_schedule_item'));
		add_action('wp_ajax_linkedin-delete-schedule', array($this, 'ajax_delete_schedule_item'));
		add_action('wp_ajax_linkedin-schedule-recurrence', array($this, 'ajax_update_schedule_recurrence'));
		add_action('wp_ajax_linkedin-change-option', array($this, 'ajax_update_option'));
		add_action('wp_ajax_linkedin-delete-old-jobs', array($this, 'ajax_delete_old_jobs'));
	}

     /**
	 * ajax callback update setting
	*/

	function update_settings () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if ( empty($_POST) || !wp_verify_nonce($_POST['linkedin_setting'],'update_linked_setting') ){
			$response	=	array(
					'success'	=> false ,
					'msg'		=> __('Permission denied!', ET_DOMAIN)
				);
		}
		$settings	=	array();
		$settings['api_key']	=	$_POST['api_key'];
		$settings['secret_key']	=	$_POST['secret_key'];
		$settings['token']	=	$_POST['token'];
		$settings['token_secret']	=$_POST['token_secret'];

		$this->save_settings($settings);

		$response	=	array('success' => true);

		echo json_encode($response);
		exit;		
	}
	//test connecting
	function test_connecting () {

	   $key=$_POST['key'];
		if($key)
		{
			$api=$this->get_settings(  );
			extract($api);
			$api_key=isset($api['api_key']) ? trim($api['api_key']) :'';
			$secret_key=isset($api['secret_key']) ? trim($api['secret_key']) :'';
			$token=isset($api['token']) ? trim($api['token']) :'';
			$token_secret=isset($api['token_secret']) ? trim($api['token_secret']) :'';
			$API_CONFIG = array(
					'appKey'       => $api_key,
					'appSecret'    => $secret_key,
					'callbackUrl'  => NULL
			);
			$TOKEN_CONFIG=array('oauth_token'=>$token,'oauth_token_secret'=>$token_secret);
			$linkedin = new WP_LinkedIn_LinkedIn($API_CONFIG);
			$linkedin->setTokenAccess($TOKEN_CONFIG);
			$linkedin->setResponseFormat(WP_LinkedIn_LinkedIn::_RESPONSE_XML);
			$response=$linkedin->searchJobs('?keywords=quality&count=1');
			if($response['success']===true)
			{
				 echo 'Connected successfully. Now you can import linkedin jobs into your job board.';
			}else 
			{
				$err=simplexml_load_string($response['linkedin']);
				echo 'Erorr: '.$err->message . '. Please check your LinkedIn API ';
			}
		}
		
		//echo json_encode($response);
		die();
	}

	public function linkedin_search_job (){
		
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}
		$data		=	$_REQUEST['data'];
		$this->set_search_string($data);
		$string=$this->get_search_string();
		if($string['facet']=='')
		 $data =str_replace('facet=&','', $data);
		$job_data	=	$this->linkedin_job_sync(trim($data));
		if(isset($job_data['error']) && $job_data['total'] == 0) {
			$res	=array (
						'success'	=> false,
						'msg'		=> isset($job_data['error']) ? $job_data['error'] : array( '0' =>__("There is no job found with your search string.", ET_DOMAIN))
				);

		} else {
 			$res	=array (
					'success'	=> true,
					'msg'		=> __('Success', ET_DOMAIN),
					'data'		=> $job_data
				);
		}
		echo json_encode($res);
		exit;
	}

	public function delete_linkedin_jobs(){
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
 			if (wp_delete_post($id)){
 				$count++;
 			}
 		}
 		$page_max 	= $count >= 10 ? $data['page_max'] - 1 : $data['page_max'];
 		$page 		= min($data['page'], $page_max);

 		$query = new WP_Query(array(
								'post_type' 		=> 'job',
								'meta_key' 			=> 'et_template_id',
								'meta_value' 		=> 'linkedin',
								'posts_per_page' 	=> 10,
								'paged' 			=> $page
							));
 		$jobs = array();
 		foreach ($query->posts as $job) {
 			$jobs[] = array(
 				'ID'		=> $job->ID,
 				'link'		=> get_permalink($job->ID),
 				'title' 	=> $job->post_title,
 				'creator' 	=> get_post_meta($job->ID, 'et_linkedin_company', true),
 				'url' 		=> get_post_meta($job->ID, 'et_linkedin_url', true),
 				'date' 		=> date('d-m-Y', strtotime($job->post_date))
 				);
 		}
 		if ($count > 0)
 			$resp = array(
 				'success' 	=> true,
 				'msg' 		=> $count == 1 ? __('1 job has been deleted', ET_DOMAIN) : sprintf(__('%d jobs have been deleted', ET_DOMAIN), $count),
 				'data' 		=> array(
 					'count' => $count,
 					'page' 	=> $page,
 					'pages_max' => $query->max_num_pages,
 					'jobs' => $jobs
 				)
 			);
 		else 
 			$resp = array(
 				'success' 	=> false,
 				'msg' 		=> __('No job has been deleted', ET_DOMAIN)
 			);

		echo json_encode($resp);
		exit;
	}

	/**
	 * 
	 */
	public function linkedin_change_page(){
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}

		$data		=	$_REQUEST['content'];
 		$query = new WP_Query(array(
								'post_type' 		=> 'job',
								'meta_key' 			=> 'et_template_id',
								'meta_value' 		=> 'linkedin',
								'posts_per_page' 	=> 10,
								'paged' 			=> $data['page']
							));
 		foreach ($query->posts as $job) {
 			$jobs[] = array(
 				'ID'		=> $job->ID,
 				'title' 	=> $job->post_title,
 				'link'		=> get_permalink($job->ID),
 				'creator' 	=> get_post_meta($job->ID, 'et_linkedin_company', true),
 				'url' 		=> get_post_meta($job->ID, 'et_linkedin_url', true),
 				'date' 		=> date('d-m-Y', strtotime($job->post_date))
 				);
 		}
 		if ($query->post_count > 0)
 			$resp = array(
 				'success' 	=> true,
 				'msg' 		=> '',
 				'data' 		=> array(
 					'page' => $data['page'],
 					'pages_max' => $query->max_num_pages,
 					'jobs' => $jobs
 				)
 			);
 		else 
 			$resp = array(
 				'success' 	=> false,
 				'msg' 		=> ''
 			);

		echo json_encode($resp);
		exit;
	}

	/**
	 * Handle ajax: save new schedule item
	 */
	public function linkedin_save_schedule_item(){
		
		
		parse_str($_POST['content'], $data);
		//
		$id = wp_insert_post(array(
			'post_title' => $data['jobtitle'],
			'post_type' => 'linkedin_schedule'
			));
		if ( $id ){
			update_post_meta($id, 'category', $data['category']);
			update_post_meta($id, 'countrycode', $data['countrycode']);
			update_post_meta($id, 'jobtitle', $data['jobtitle']);
			update_post_meta($id, 'postalcode', $data['postalcode']);
			update_post_meta($id, 'keywords', $data['keywords']);
			update_post_meta($id, 'companyname', $data['companyname']);
			update_post_meta($id, 'linkedin_cat', $data['cat']);
			update_post_meta($id, 'linkedin_type', $data['type']);
			update_post_meta($id, 'linkedin_author', $data['auth_id']);
			update_post_meta($id, 'linkedin_active', 1);
			$sche = $this->build_schedule_item($id);
			$ctr=$sche['countrycode'];
			
			$support_co	=$this->linkedin_support_country();
			if($ctr)
				$country=$support_co[$ctr];
			else $country='';
			$sche['countrycode']=$country;
			$resp = array(
				'success' 	=> true,
				'msg' 		=> '',
				'data' 		=> array(
					'schedule' 	=>$sche
					)
				);
		}else {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> __('Could not save schedule, please try again!', ET_DOMAIN)
			);
		}

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($resp);
		exit;
	}

	public function ajax_toggle_schedule_item(){
		try {
			if (empty($_POST['content']['id']))
				throw new Exception(__('Cannot active/deactive schedule, please try again!', ET_DOMAIN));

			$active = get_post_meta( $_POST['content']['id'], 'linkedin_active', true);
			if ($active)
				update_post_meta( $_POST['content']['id'], 'linkedin_active', 0 );
			else 
				update_post_meta( $_POST['content']['id'], 'linkedin_active', 1 );

			$resp = array(
				'success' 	=> true
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage()
			);
		}

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($resp);
		exit;
	}

	public function ajax_delete_schedule_item(){
		if(is_multisite())
		{
			$blogid=get_current_blog_id();
			switch_to_blog($blogid);
		}
		try {
			if (empty($_POST['content']['id']))
				throw new Exception(__('Cannot delete item, please try again!', ET_DOMAIN));

			wp_delete_post( $_POST['content']['id'], true );

			$resp = array(
				'success' 	=> true
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage()
			);
		}

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($resp);
		exit;
	}

	public function ajax_update_schedule_recurrence(){
		try {
			if (empty($_POST['content']['recurrence'])) 
				throw new Exception(__('There is an error occurred, please try again later!', ET_DOMAIN));

			if (!is_numeric($_POST['content']['recurrence']) || $_POST['content']['recurrence'] <= 0)
				throw new Exception(__('Input value must be a positive number!', ET_DOMAIN));

			$recurrence = intval($_POST['content']['recurrence']);

			update_option('et_linkedin_recurrence', $recurrence);

			$this->schedule_activation();

			$resp = array('success' => true);
		} catch (Exception $e) {
			$resp = array('success' => false, 'msg' => $e->getMessage());
		}
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($resp);
		exit;
	}

	public function ajax_delete_old_jobs(){
		try {
			if ( !current_user_can( 'manage_options' ) )
				throw new Exception(__("You don't have permission to perform this action", ET_DOMAIN));

			$count = (int)$this->delete_old_jobs();

			$resp = array(
				'success' 	=> true,
				'msg' 		=> sprintf( __('%d job(s) have been deleted', ET_DOMAIN ), $count)
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage()
			);
		}

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($resp);
		exit;
	}

	public function ajax_update_option(){
		try {
			if ( !isset($_POST['content']['name']) || !isset($_POST['content']['value']) )
				throw new Exception('Input is invalid', ET_DOMAIN);

			if ( !current_user_can( 'manage_options' ) )
				throw new Exception(__("You don't have permission to perform this action", ET_DOMAIN));

			$data = array(
				'name' 		=> $_POST['content']['name'],
				'value' 	=> $_POST['content']['value']
			);

			update_option( 'et_linkedin_' . $data['name'], $data['value'] );

			$resp = array(
				'success' 	=> true,
				'msg' 		=> ''
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> __("Can't save option", ET_DOMAIN)
			);
		}
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($resp);
		exit;
	}

	function linkedin_save_imported_jobs(){
		try {
			// validate user's permission
			if(!current_user_can ('manage_options')) 
				throw new Exception(__('Permission Denied', ET_DOMAIN), '402');

			$data 	= parse_str( ($_POST['content']) );
			$count 	= 0;
			
			foreach ((array)$import as $job) {
				if ( $job['allow'] == 1 ){
					$count += $this->save_linkedin_jobs($job, $import_author);
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
				'code' 		=> $e->getCode(),
				'data' 		=> array(
					'count' => $count
					)
				);
		}

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo json_encode($response);
		exit;
	}
}
