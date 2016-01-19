<?php 
class JE_Import_Ajax extends JE_Import {
	function __construct () {
		parent::__construct();
		/*
		* ajax sync indeed job
		*/
		add_action ('wp_ajax_update-publisher-id', array($this, 'update_publisher_id'));
		add_action ('wp_ajax_indeed-search-job', array($this, 'indeed_search_job'));
		add_action ('wp_ajax_indeed-delete-jobs', array($this, 'delete_indeed_jobs'));
		add_action ('wp_ajax_indeed-change-page', array($this, 'indeed_change_page'));
		add_action('wp_ajax_indeed-save-imported-jobs', array($this,'indeed_save_imported_jobs'));
		add_action('wp_ajax_indeed-new-schedule', array($this,'indeed_save_schedule_item'));
		add_action('wp_ajax_indeed-toggle-schedule', array($this, 'ajax_toggle_schedule_item'));
		add_action('wp_ajax_indeed-delete-schedule', array($this, 'ajax_delete_schedule_item'));
		add_action('wp_ajax_indeed-schedule-recurrence', array($this, 'ajax_update_schedule_recurrence'));
		add_action('wp_ajax_indeed-change-option', array($this, 'ajax_update_option'));
		add_action('wp_ajax_indeed-delete-old-jobs', array($this, 'ajax_delete_old_jobs'));
	}

	public function update_publisher_id () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}
		$this->set_indeed_publisher_id ($_REQUEST['publisher_id']);
		echo json_encode(array (
							'success'	=> true,
							'msg'		=> __('Success', ET_DOMAIN))
						);
		exit;
	}

	public function indeed_search_job (){

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );

		if(!current_user_can ('manage_options')) {
			return array (
						'success'	=> false,
						'msg'		=> __('Permission Denied', ET_DOMAIN)
					);
		}
		$data		=	$_REQUEST['data'];

		$job_data	=	$this->indeed_job_sync($data, $_REQUEST['publisher_id']);
		$this->set_search_string($data);
		$this->set_indeed_publisher_id ($_REQUEST['publisher_id']);


		if(isset($job_data->error) || $job_data->totalresults == 0) {
			$res	=	 array (
						'success'	=> false,
						'msg'		=> isset($job_data->error) ? $job_data->error : array( '0' =>__("There is no job found with your search string.", ET_DOMAIN))
				);

		} else {
 			$res		=	array (
					'success'	=> true,
					'msg'		=> __('Success', ET_DOMAIN),
					'data'		=> $job_data
				);
		}
		echo json_encode($res);
		exit;
	}

	public function delete_indeed_jobs(){
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
								'meta_value' 		=> 'indeed',
								'posts_per_page' 	=> 10,
								'paged' 			=> $page
							));
 		$jobs = array();
 		foreach ($query->posts as $job) {
 			$jobs[] = array(
 				'ID'		=> $job->ID,
 				'title' 	=> $job->post_title,
 				'creator' 	=> get_post_meta($job->ID, 'et_indeed_company', true),
 				'url' 		=> get_post_meta($job->ID, 'et_indeed_url', true),
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
	public function indeed_change_page(){
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
								'meta_value' 		=> 'indeed',
								'posts_per_page' 	=> 10,
								'paged' 			=> $data['page']
							));
 		foreach ($query->posts as $job) {
 			$jobs[] = array(
 				'ID'		=> $job->ID,
 				'title' 	=> $job->post_title,
 				'creator' 	=> get_post_meta($job->ID, 'et_indeed_company', true),
 				'url' 		=> get_post_meta($job->ID, 'et_indeed_url', true),
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
	public function indeed_save_schedule_item(){
		parse_str($_POST['content'], $data);
		//
		$id = wp_insert_post(array(
			'post_title' => $data['title'],
			'post_type' => 'indeed_schedule'
			));
		if ( $id ){
			update_post_meta($id, 'indeed_co', $data['co']);
			update_post_meta($id, 'indeed_loc', $data['loc']);
			update_post_meta($id, 'indeed_lim', $data['lim']);
			//update_post_meta($id, 'indeed_within', $data['within']);
			update_post_meta($id, 'indeed_cat', $data['cat']);
			update_post_meta($id, 'indeed_type', $data['type']);
			update_post_meta($id, 'indeed_author', $data['auth_id']);
			update_post_meta($id, 'indeed_active', 1);

			$resp = array(
				'success' 	=> true,
				'msg' 		=> '',
				'data' 		=> array(
					'schedule' 	=> $this->build_schedule_item($id)
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

			$active = get_post_meta( $_POST['content']['id'], 'indeed_active', true);
			if ($active)
				update_post_meta( $_POST['content']['id'], 'indeed_active', 0 );
			else
				update_post_meta( $_POST['content']['id'], 'indeed_active', 1 );

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

			update_option('et_indeed_recurrence', $recurrence);

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

			update_option( 'et_indeed_' . $data['name'], $data['value'] );

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

	function indeed_save_imported_jobs(){
		try {
			// validate user's permission
			if(!current_user_can ('manage_options'))
				throw new Exception(__('Permission Denied', ET_DOMAIN), '402');
			extract($_POST);
			$count 	= 0;

			foreach ((array)$import as $job) {
				if ( $job['allow'] == 1 ){
					$count += $this->save_indeed_jobs($job, $import_author);
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