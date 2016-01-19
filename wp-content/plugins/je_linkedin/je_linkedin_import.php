<?php
/*
Plugin Name: JE LinkedIn
Plugin URI: www.enginethemes.com
Description: Import LinkedIn jobs into your JobEngine-powered job board
Version: 1.0
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/
 require_once dirname(__FILE__) . '/update.php';
 class JE_LINKEDIN_IMPORT
 {
 	function __construct()
 	{
 		add_action('et_admin_enqueue_scripts-et-linkedin', array($this, 'plugin_scripts'));
 		add_action('et_admin_enqueue_styles-et-linkedin', array($this, 'plugin_styles'));
 		add_action('wp_print_styles', array($this, 'plugin_frontend_styles'));
 		add_action('et_admin_menu', array($this, 'register_menu_linkedin_import'));
 		register_deactivation_hook(__FILE__, array($this,'deactivation'));
 		add_filter('et_template_job', array($this, 'modify_job_template'));
 		add_filter('et_mobile_template_job', array($this, 'mobile_template'));
 		add_filter('et_jobs_ajax_response', array($this, 'jobs_ajax_response'));
 		add_filter('pre_get_posts', array($this, 'pre_get_posts'));
 		add_filter('wp', array($this, 'remove_filter_orderby'));
 		add_filter('et_get_job_count_where', array($this, 'modify_job_count'));
 		add_action('wp_footer', array($this, 'add_linkedin_template'));
 		add_action('et_mobile_footer', array($this, 'add_linkedin_mobile_template'));
 		add_action('et_companies_response', array($this, 'filter_ajax_company_respone'));
 		add_action('wp_footer', array($this,'custom_link_apply'));
 		add_action('wp_head',array($this,'custom_hide_sidebar_infor'));
 		// new post type
 		register_post_type('linkedin_schedule',
 				array(
 						'public' => false,
 						'publicly_queryable' => true,
 						'show_ui' => false,
 						'show_in_menu' => false,
 						'query_var' => true,
 						'rewrite' => false,
 						'capability_type' => 'post',
 						'has_archive' => false,
 						'hierarchical' => false,
 						'menu_position' => null
 				));
 		
 		// schedule
 	}
 	
 	function deactivation () {
 		global $wpdb;
 		$prefix		=	$wpdb->prefix;
 		$et_prefix	=	'et_';
 		$query	=	"update $wpdb->posts set post_status = 'draft'where ID IN(select post_id
				 	  from $wpdb->postmeta
				 	  where 	meta_key ='et_template_id'
				 	  and meta_value = 'linkedin') ";
 		$wpdb->query($query);
 		wp_clear_scheduled_hook('single_linkedin_schedule');
 		wp_clear_scheduled_hook('et_auto_delete_linkedin');
 		wp_clear_scheduled_hook('et_auto_main_import_linkedin');
 	}
 	/**
 	 * Replace display name of theme with display name linkedin 
 	 */
 	function filter_ajax_company_respone($company_respone)
 	{
 		global  $job;
 		 
  		if(is_single() && get_post_type()=='job')
  		{
  			$template=et_get_post_field( $job->ID,'template_id');
  			if($template =='linkedin'){
  		      $company_respone['display_name']=et_get_post_field($job->ID, 'linkedin_company');
  		      $company_respone['post_url']='#'; 
  			} 
  		}
  		return $company_respone;
 	}
 	
 	public function pre_get_posts($query){
 		if (is_author()){
 		add_filter('posts_where', array($this, 'remove_linkedin_job'));
 		}
 	}
 	
 	public function remove_linkedin_job($where){
 		global $wpdb;
 		remove_filter('posts_where', array($this, 'remove_linkedin_job'));
 		return $where . " AND {$wpdb->posts}.ID NOT IN (SELECT linkedin.post_id FROM {$wpdb->postmeta} as linkedin WHERE meta_key = 'et_linkedin_url')";
 	}
 	
 	public function modify_job_count($where){
 		global $wpdb;
 		return $where . " AND {$wpdb->posts}.ID NOT IN (SELECT linkedin.post_id FROM {$wpdb->postmeta} as linkedin WHERE meta_key = 'et_linkedin_url')";
 	}
 	
 	public function filter_orderby($order){
 		global $wpdb;
 		return "{$wpdb->postmeta}.meta_value DESC, {$wpdb->posts}.post_date DESC";
 	}
 	
 	public function remove_filter_orderby(){
 	remove_filter('posts_orderby', array(&$this, 'filter_orderby'));
 	}
 	//mobile
 	public function modify_job_template($template){
 		global $job;
 		if ($job['template_id'] == 'linkedin')
 			return dirname(__FILE__) . '/template-job.php';
 		else return $template;
 	}
 	public function mobile_template($template){
 		global $job;
 		if ($job['template_id'] == 'linkedin')
 			return dirname(__FILE__) . '/mobile-template-job.php';
 		else
 			return $template;
 	}
 	
 	public function jobs_ajax_response($response){
 		$opt 			= new ET_GeneralOptions();
 		$is_linkedin_job 	= et_get_post_field($response['id'], 'linkedin_url');
 		$linkedin_logo 	    = $opt->get_default_logo();
 		$url 			    = et_get_post_field($response['id'], 'linkedin_url');
 		$plus = array(
 				'is_linkedin_job'	 => et_get_post_field($response['id'], 'linkedin_url') != '' ? true : false,
 				'linkedin_url' 		 => et_get_post_field($response['id'], 'linkedin_url'),
 				'linkedin_logo'  	 => is_array($linkedin_logo) ? array_shift($linkedin_logo) : '',
 				'linkedin_company'   =>  et_get_post_field($response['id'], 'linkedin_company'),
 				'linkedin_ref_url' 	 => str_replace('viewjob', 'rc/clk', $url)
 		);
 		return $response + $plus;
 	}
 	
 	public function register_menu_linkedin_import(){
 		// register menu item
 		et_register_menu_section('linkedin', array(
 				'menu_title' 	=> __('JE LinkedIn', ET_DOMAIN),
 				'page_title' 	=> __('JE LINKEDIN', ET_DOMAIN),
 				'callback' 		=> array( $this, 'et_linkedin_import_callback' ),
 				'slug' 			=> 'et-linkedin',
 				'page_subtitle'	=>	__('Import LinkedIn jobs into your job board', ET_DOMAIN)
 		));
 	}
 	
 	public function linkedin_support_country () {
 		return array(  
 				'us' 	=> 'United States',	'ar'	=> 'Argentina', 	'au' => 'Australia', 			'at' => 'Austria',
 				'bh'	=> 'Bahrain' ,		'be' 	=> 'Belgium',		'br'	=> 'Brazil', 			'ca' => 'Canada', 		'cl' => 'Chile',
 				'cn'	=> 'China' ,		'co' 	=> 'Colombia',		'cz'	=> 'Czech Republic', 	'dk' => 'Denmark', 		'fi' => 'Finland',
 				'fr'	=> 'France' ,		'de' 	=> 'Germany',		'gr'	=> 'Greece', 			'hk' => 'Hong Kong', 	'hu' => 'Hungary',
 				'in'	=> 'India' ,		'id' 	=> 'Indonesia',		'ie'	=> 'Ireland', 			'il' => 'Israel', 		'it' => 'Italy',
 				'jp'	=> 'Japan' ,		'kr' 	=> 'Korea',			'kw'	=> 'Kuwait', 			'lu' => 'Luxembourg', 	'my' => 'Malaysia',
 				'mx'	=> 'Mexico' ,		'nl' 	=> 'Netherlands',	'no'	=> 'Norway', 			'om' => 'Oman', 		'pk' => 'Pakistan',
 				'pe'	=> 'Peru' ,			'ph' 	=> 'Philippines',	'pl'	=> 'Poland', 			'pt' => 'Portugal', 	'qa' => 'Qatar',
 				'ro'	=> 'Romania' ,		'ru' 	=> 'Russia',		'sa'	=> 'Saudi Arabia', 		'sg' => 'Singapore', 	'za' => 'South Africa',
 				'es'	=> 'Spain' ,		'se' 	=> 'Sweden',		'ch'	=> 'Switzerland', 		'tw' => 'Taiwan', 		'tr' => 'United Arab Emirates',
 				'gb'	=> 'United Kingdom' ,		've' 	=> 'Venezuela'
 		);
 	
 	}
 	
	function save_settings ( $settings	=	array() ) {
			update_option( 'je_linkedin_api_settings', $settings );
		}
	
		function get_settings () {
			$default	=	array(
					'api_key'	    => 'b97feo592l3r',
					'secret_key'	=> 'c6G7jq1tqQ8i2y5K',	
					'token'	        => '371dfc3d-6b1e-451a-a8f7-45515c983875',
					'token_secret'	=> 'f94c1465-5f4a-48f9-8171-e5ec4b9d92b1'
				);
			return get_option ('je_linkedin_api_settings', $default);
		}
 	
	 function set_search_string ($data) {
	 		$data	=	explode('&', urldecode($data));
	 		$s	=	array();
	 		foreach ($data as $key => $value) {
	 			$v	=	explode('=', $value);
	 			$s[$v[0]]	=	$v[1];
	 		}
	 		update_option( 'et_linkedin_search_string', $s );
	 
	 	}
	 	
	 function get_search_string () {
	 		$default	=	array(
	 				'co'				=> 'us',
	 				'keywords'			=>'',
	 				'job-title'			=> '',
	 				'industry'			=>'',
	 				'company-name'		=>'',
	 				'country-code'		=> 'us',
	 				'postal-code'		=>'',
	 				'start'			    =>0,
	 				'count'			    =>25
	 				
	 		);
	 		return get_option( 'et_linkedin_search_string', $default );
	 	}
 	
	 public function get_linkedin_api_key() {
	 		return get_option ('et_linkedin_api_key' );
	 	}
	 	
	 public function set_linkedin_api_key ( $id ) {
	 		return update_option ('et_linkedin_api_key', $id);
	 	}
	 	
 	/**
 	 *  get job from linkedin
 	 */
 	public function fetch_job($search_str,$author,$cat,$type){
 		
 		$api	=	$this->get_settings(  ); 	
 	    extract($api);
		$api_key		=	isset($api['api_key']) ? trim($api['api_key']) :'';
		$secret_key		=	isset($api['secret_key']) ? trim($api['secret_key']) :'';
		$token			=	isset($api['token']) ? trim($api['token']) :'';
		$token_secret	=	isset($api['token_secret']) ? trim($api['token_secret']) :'';	
 		$API_CONFIG 	= 	array(
				 				'appKey'       => $api_key,
				 				'appSecret'    => $secret_key,
				 				'callbackUrl'  => NULL
 							); 
 		$TOKEN_CONFIG	=	array('oauth_token'=>$token,'oauth_token_secret'=>$token_secret);
 		$linkedin 		= 	new WP_LinkedIn_LinkedIn($API_CONFIG);

 		$linkedin->setTokenAccess($TOKEN_CONFIG);
 		$linkedin->setResponseFormat(WP_LinkedIn_LinkedIn::_RESPONSE_XML);
 		$response 	= $linkedin->searchJobs(':(jobs:(id))?'.$search_str);
 		$data		= simplexml_load_string($response['linkedin']);	
 		if($response['success'] === TRUE)
 		{
 			$jobs=$data->jobs->job;
 			if($jobs)
 			{
 				$i=0;
 				$jobarr = array();
 				$keys 	= array();
 				foreach ($jobs as $item)
 				{
 					$jobid= $item->id;
 					try {
 						 $lk=new WP_LinkedIn_LinkedIn($API_CONFIG);
 						$response = $lk->job((string)$jobid,':(id,active,posting-date,company:(name),position:(title,location,job-functions,industries,job-type),skills-and-experience,description-snippet,description,salary,job-poster:(id,first-name,last-name,headline),referral-bonus,site-job-url,location-description)');
 						if($response['success'] === TRUE){
 							$job= simplexml_load_string($response['linkedin']);
 							$job =(array)$job;
 							$id =$job['id'];
 							$url=$job['site-job-url'];
 							$date=(array)$job['posting-date'];
 							$year=$date['year'];
 							$month=$date['month'];
 							$day=$date['day'];
 							$fulldate=$year .'-'.$month.'-'.$day;
 							$locatoin=(array)$job['position']->location;
 							$company=$job['company']->name;
 							$content=$job['description'];
 							$city='';
 							$country =(array)$locatoin['country'];
 							$title=$job['position']->title;
 							$state='';
 							 $i++;
 							$content =str_replace(array('\"'), '\'', $content);
 							$jobarr[] = array(
 									'jobtitle' => (string)$title,
 									'content' => (string)$content,
 									'date' => (string)$fulldate,
 									'job_category' =>$cat,
 									'job_type' =>$type,
 									'formattedLocationFull' => (string)$locatoin['name'],
 									'company' => (string)$company,
 									'url' => (string)$url,
 									'city' => (string)$city,
 									'country' => (string)$country['code'],
 									'jobkey' => (string)$id,
 							);
 							$keys[] = $id;
 						}
 						
 					} catch (Exception $e) {
 						
 						echo $e->getMessage();
 						exit();
 					}
 					
 				}	
 				// Importation
 				global $wpdb;
 				$sql 		= "SELECT DISTINCT meta.meta_value FROM {$wpdb->postmeta} as meta WHERE meta.meta_key = 'et_linkedin_id' AND meta.meta_value IN ('" . implode("','", $keys) ."') ";
 				$results 	= $wpdb->get_results($sql, ARRAY_N);
 				$dupkeys 	= array();
 				foreach ($results as $row) {
 					$dupkeys[] = $row[0];
 				}
 				
 				foreach ($jobarr as $j => $item) {
 					if (!in_array($item['jobkey'], $dupkeys))
 						
 					$this->save_linkedin_jobs($item, $author);
 				}	
 				
 			}
 			
 		}
 		
 	  
 	}
 	
 	function delete_old_jobs(){
 		global $wpdb;
 		$day_limit 	= intval(get_option('et_linkedin_delete_days', '30'));
 		$day_pos 	= date('Y-m-d 00:00:00', strtotime('-' . $day_limit . ' days'));
 		$sql 		= $wpdb->prepare("SELECT DISTINCT p.ID as ID FROM {$wpdb->posts} as p JOIN {$wpdb->postmeta} as mt ON mt.post_id = p.ID WHERE p.post_date <= %s AND p.post_type = 'job' AND mt.meta_key = 'et_linkedin_id' ", $day_pos);
 	
 		$results 	= $wpdb->get_results($sql);
 	
 		$count 		= 0;
 		foreach ($results as $result) {
 			if (wp_delete_post($result->ID, true )){
 				$count++;
 			}
 		}
 		return $count;
 	}

 	public function linkedin_job_sync ($search_str) {
 	
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
 		$response = $linkedin->searchJobs(':(jobs:(id))?'.$search_str.'&count=20');
 		$data= simplexml_load_string($response['linkedin']);
 		if($response['success'] === TRUE){
			  $xml=$this->create_xml_custom($data);
			  return $xml;
	 	}else {
	 		return array('error'=>$data->message,'total'=>0);
	 	}
 	}
 	
 	public  function create_xml_custom($data)
 	{
 		
 		 $total= (array)$data->jobs['total'];
 		 $start=$data->jobs['start'];
 		 $count= (array)$data->jobs['count'];
 		 $jobs=$data->jobs->job;
 		 $html ='';
 		 $xml='';
 		 if($jobs)
 		 {
 		 	$i=0;
 		 	foreach ($jobs as $item)
 		 	{
 		 		 $jobid= $item->id;
 		 		 $api=$this->get_settings(  );
 		 		 extract($api);
 		 		 $api_key=isset($api['api_key']) ? trim($api['api_key']) :'';
 		 		 $secret_key=isset($api['secret_key']) ? trim($api['secret_key']) :'';
 		 		 $API_CONFIG = array(
 				'appKey'       => $api_key,
 				'appSecret'    => $secret_key,
 				'callbackUrl'  => NULL
 		      ); 
 		 		 $linkedin = new WP_LinkedIn_LinkedIn($API_CONFIG);		 
 		 		 $response = $linkedin->job((string)$jobid,':(id,active,posting-date,company:(name),position:(title,location,job-functions,industries,job-type),skills-and-experience,description-snippet,description,salary,job-poster:(id,headline),site-job-url,location-description)');
 		 		 if($response['success'] === TRUE){
		 		 		 $job= simplexml_load_string($response['linkedin']);
		 		 		 $job =(array)$job;
		 		 		 $id =$job['id'];
		 		 		 $url=$job['site-job-url'];
		 		 		 $date=(array)$job['posting-date'];
		 		 		 $year=$date['year'];
		 		 		 $month=$date['month'];
		 		 		 $day=$date['day'];
		 		 		 $fulldate=$year .'-'.$month.'-'.$day;
		 		 		 $locatoin=(array)$job['position']->location;
		 		 		 $company=$job['company']->name;
		 		 		 $content=$job['description'];
		 		 		 $city='';
		 		 		 $country =(array)$locatoin['country'];
		 		 		 $title=$job['position']->title;
		 		 		 $job_type=(array)$job['position'];
		 		 		 $job_type=$job_type['job-type']->name;
		 		 		 $state='';
		 		 		 $i++;
		 		 		 $list=$this->et_get_job_type();
		 		 		 $cats=$this->et_job_categories_option_list(); 
		 		 		 $html [] ='<tr>
											   <td>
												<input type="hidden" name="import['. $i.'][allow]" value="0"/>
												<input type="checkbox" class="allow" name="import['. $i.'][allow]" value="1" checked="checked"/>
												<input type="hidden" name="import['. $i.'][url]" value="'. $url.'"/>
												<input type="hidden" name="import['. $i.'][date]" value="'.$fulldate.'"/>
												<input type="hidden" name="import['. $i.'][formattedLocationFull]" value="'.$locatoin['name'].'"/>
												<input type="hidden" name="import['. $i.'][company]" value="'.$company.'"/>
												<input type="hidden" name="import['. $i.'][jobkey]" value="'.$id.'"/>
											    <div style="display:none;"><input type="hidden" name="import['. $i.'][content]" value="'.$content.'"/></div>
												<input type="hidden" name="import['. $i.'][city]" value="'.$city.'"/>
												<input type="hidden" name="import['. $i.'][state]" value="'.$state.'"/>
												<input type="hidden" name="import['. $i.'][country]" value="'.$country['code'].'"/>
												<input type="hidden" name="import['. $i.'][jobtitle]" value="'.$title.'"/>
											</td>
											<td class="jobtitle">
												<a href="'.$url.'" target="_blank">'.$title.'</a>
												<div class="bubble-quote">
													<span>@ '.$company.'</span>
													<span class="icon" data-icon="@">'.$locatoin['name'].'</span>
													<div class="triangle"></div>
												</div>
											</td>
											<td class="jobtitle">'.$job_type.'</td>
											<td>
												<div class="select-style et-button-select">
													<select class="job_cat" name="import['.$i.'][job_category]" id="">
														 '.$cats.'
													</select>
												</div>
											</td>
											<td>
												<div class="select-style et-button-select">
													<select class="job_type" name="import['.$i.'][job_type]" id="">
														'.$list.'
													</select>
												</div>
											</td>
										</tr>';
		 		 	 }
		 		 	         $xml['total']=$total['0'];
		 		 	         $xml['count']=isset($count['0']) ? $count['0'] :$i;
		 		 	         $xml['jobs']=array('job'=>$html);  
		 		 	}
		 		 	
		 		 }else{
		 		 	$xml['total']=0;
		 		 	$xml['count']=0;
		 		 	$xml['jobs']=array('job'=>'');
		 		 }
		 		 return ($xml);
 	}
 	/**
 	 * Save sending job
 	 */
 	public function save_linkedin_jobs($job, $author = 1){
 		$prefix = 'et_';
 		$_POST += array('post_type' => 'job');
 		$result = wp_insert_post(array(
 				'post_author' 	=> $author,
 				'post_date'		=> date('Y-m-d h:i:s', strtotime($job['date'])) ,
 				'post_status' 	=> 'publish',
 				'post_content' 	=> $job['content'],
 				'post_type' 	=> 'job',
 				'post_title' 	=> $job['jobtitle'],
 				'tax_input' 	=> array(
 						'job_category' => array($job['job_category']),
 						'job_type' => array($job['job_type']),
 				),
 		));

 		// if insert fail, return false
 		if ( !$result )
 			return false;
 	
 		// otherwise, insert meta data and terms
 		wp_set_object_terms($result, $job['job_category'], 'job_category');
 		wp_set_object_terms($result, $job['job_type'], 'job_type');
 	
 		if(defined('ALTERNATE_WP_CRON')) {
 				
 			global $wpdb;
 			if($term_info = term_exists( $job['job_category'], 'job_category' ) ) {
 				$tt_id	=	$term_info['term_taxonomy_id'];
 				$wpdb->insert( $wpdb->term_relationships, array( 'object_id' => $result, 'term_taxonomy_id' => $tt_id ) );
 				wp_update_term_count( array($tt_id), 'job_category' );
 			}
 	
 			if( $term_info = term_exists( $job['job_type'], 'job_type') ) {
 				;
 				$tt_id	=	$term_info['term_taxonomy_id'];
 				$wpdb->insert( $wpdb->term_relationships, array( 'object_id' => $result, 'term_taxonomy_id' => $tt_id ) );
 				wp_update_term_count( array($tt_id), 'job_type' );
 			}
 		}
 	
 		$meta_maps = array(
 				'location' 			=> 'formattedLocationFull',
 				'full_location' 	=> 'formattedLocationFull',
 				'linkedin_company' 	=> 'company',
 				'linkedin_url' 		=> 'url',
 				'linkedin_city' 		=> 'city',
 				'linkedin_state' 		=> 'city',
 				'linkedin_country' 	=> 'country',
 				'linkedin_id' 		=> 'jobkey'
 		);
 	
 		foreach ($meta_maps as $key => $value) {
 			update_post_meta($result, $prefix . $key, $job[$value]);
 		}
 	
 		update_post_meta($result, $prefix . 'template_id', 'linkedin');
 	
 		return true;
 	}
 	public function schedule_activation () {
 		// update schedule event
 		wp_clear_scheduled_hook( 'et_auto_main_import_linkedin' );
 		$recurrence = get_option('et_linkedin_recurrence', 12);
 		$time = mktime($recurrence, 0, 0, date('n'), date('j'));
 		wp_schedule_event( $time, 'linkedin', 'et_auto_main_import_linkedin');
 	}
 	/**
 	 * Perform update action for schedule item
 	 * @param array data
 	 */
 	public function update_schedule_item($data){
 		$post = array(
 				'ID' => $data['id'],
 				'post_title' => $data['title']
 		);
 		wp_update_post( $post );
 		if (!empty($data['category'])) 	update_post_meta( $data['id'], 'category', $data['category'] );
 		if (!empty($data['countrycode'])) 	update_post_meta( $data['id'], 'countrycode', $data['countrycode'] );
 		if (!empty($data['postalcode'])) 	update_post_meta( $data['id'], 'postalcode', $data['postalcode'] );
 		if (!empty($data['keywords'])) 	update_post_meta( $data['id'], 'keywords', $data['keywords'] );
 		if (!empty($data['jobtitle'])) 	update_post_meta( $data['id'], 'jobtitle', $data['jobtitle'] );
 		if (!empty($data['companyname'])) 	update_post_meta( $data['id'], 'companyname', $data['companyname'] );
 		if (!empty($data['cat'])) 		update_post_meta( $data['id'], 'linkedin_cat', $data['cat'] );
 		if (!empty($data['type'])) 		update_post_meta( $data['id'], 'linkedintype', $data['type'] );
 		if (!empty($data['author'])) 	update_post_meta( $data['id'], 'linkedin_author', $data['author'] );
 		if (!empty($data['active'])) 	update_post_meta( $data['id'], 'ilinkedin_active', $data['active'] );
 	}
 	public function build_schedule_item($post){
 		
 		if (!is_object($post)){
 			$id = $post;
 			$post = get_post($post);
 		}
 	
 		if (!isset($post->ID))
 			return false;
 		   $indus='';
 	       $cat=get_post_meta( $post->ID, 'category', true);
 	       $in=$this->et_linkedin_industry();
 	       if($cat){
	 	         $category=str_replace('industry,', '', $cat);
	 	         $indus=$category;
	 	         $category =$in[$category];
 	       }else
 	       	 $category='';
 		return array(
 				'id' 		=> $post->ID,
 				'title' 	=> $post->post_title,
 				'industry'=>$indus,
 				'category' 	=> $category,
 				'postalcode' 	=> get_post_meta( $post->ID, 'postalcode', true),
 				'countrycode' 	=> get_post_meta( $post->ID, 'countrycode', true),
 				'companyname' 	=> get_post_meta( $post->ID, 'companyname', true),
 				'keywords' 	=> get_post_meta( $post->ID, 'keywords', true),
 				'jobtitle' 	=> get_post_meta( $post->ID, 'jobtitle', true),
 				'cat' 		=> get_post_meta( $post->ID, 'linkedin_cat', true),
 				'type' 		=> get_post_meta( $post->ID, 'linkedin_type', true),
 				'author'	=> get_post_meta( $post->ID, 'linkedin_author', true),
 				'active' 	=> get_post_meta( $post->ID, 'linkedin_active', true)
 		);
 	}
 	
 	public  function et_linkedin_industry()
 	{
 		$industry=array(
 				'47'=>'Accounting',
 				'94'=>'Airlines/Aviation',
 				'120'=>'Alternative Dispute Resolution',
 				'125'=>'Alternative Medicine',
 				'127'=>'Animation',
 				'19'=>'Apparel & Fashion',
 				'50'=>'Architecture & Planning',
 				'111'=>'Arts and Crafts',
 				'53'=>'Automotive',
 				'52'=>'Aviation & Aerospace',
 				'41'=>'Banking',
 				'12'=>'Biotechnology',
 				'36'=>'Broadcast Media',
 				'49'=>'Building Materials',
 				'138'=>'Business Supplies and Equipment',
 				'129'=>'Capital Markets',
 				'54'=>'Chemicals',
 				'90'=>'Civic & Social Organization',
 				'51'=>'Civil Engineering',
 				'128'=>'Commercial Real Estate',
 				'118'=>'Computer & Network Security',
 				'109'=>'Computer Games',
 				'3'=>'Computer Hardware',
 				'5'=>'Computer Networking',
 				'4'=>'Computer Software',
 				'48'=>'Construction',
 				'24'=>'Consumer Electronics',
 				'25'=>'Consumer Goods',
 				'91'=>'Consumer Services',
 				'18'=>'Cosmetics',
 				'65'=>'Dairy',
 				'1'=>'Defense & Space',
 				'99'=>'Design',
 				'69'=>'Education Management',
 				'132'=>'E-Learning',
 				'112'=>'Electrical/Electronic Manufacturing',
 				'28'=>'Entertainment',
 				'86'=>'Environmental Services',
 				'110'=>'Events Services',
 				'76'=>'Executive Office',
 				'122'=>'Facilities Services',
 				'63'=>'Farming',
 				'43'=>'Financial Services',
 				'38'=>'Fine Art',
 				'66'=>'Fishery',
 				'34'=>'Food & Beverages',
 				'23'=>'Food Production',
 				'101'=>'Fund-Raising',
 				'26'=>'Furniture',
 				'29'=>'Gambling & Casinos',
 				'145'=>'Glass, Ceramics & Concrete',
 				'75'=>'Government Administration',
 				'148'=>'Government Relations',
 				'140'=>'Graphic Design',
 				'124'=>'Health, Wellness and Fitness',
 				'68'=>'Higher Education',
 				'14'=>'Hospital & Health Care',
 				'31'=>'Hospitality',
 				'137'=>'Human Resources',
 				'134'=>'Import and Export',
 				'88'=>'Individual & Family Services',
 				'147'=>'Industrial Automation',
 				'84'=>'Information Services',
 				'96'=>'Information Technology and Services',
 				'42'=>'Insurance',
 				'74'=>'International Affairs',
 				'141'=>'International Trade and Development',
 				'6'=>'Internet',
 				'45'=>'Investment Banking',
 				'46'=>'Investment Management',
 				'73'=>'Judiciary',
 				'77'=>'Law Enforcement',
 				'9'=>'Law Practice',
 				'10'=>'Legal Services',
 				'72'=>'Legislative Office',
 				'30'=>'Leisure, Travel & Tourism',
 				'85'=>'Libraries',
 				'116'=>'Logistics and Supply Chain',
 				'143'=>'Luxury Goods & Jewelry',
 				'55'=>'Machinery',
 				'11'=>'Management Consulting',
 				'95'=>'Maritime',
 				'97'=>'Market Research',
 				'80'=>'Marketing and Advertising',
 				'135'=>'Mechanical or Industrial Engineering',
 				'126'=>'Media Production',
 				'17'=>'Medical Devices',
 				'13'=>'Medical Practice',
 				'139'=>'Mental Health Care',
 				'71'=>'Military',
 				'56'=>'Mining & Metals',
 				'35'=>'Motion Pictures and Film',
 				'37'=>'Museums and Institutions',
 				'115'=>'Music',
 				'114'=>'Nanotechnology',
 				'81'=>'Newspapers',
 				'100'=>'Non-Profit Organization Management',
 				'57'=>'Oil & Energy',
 				'113'=>'Online Media',
 				'123'=>'Outsourcing/Offshoring',
 				'87'=>'Package/Freight Delivery',
 				'146'=>'Packaging and Containers',
 				'61'=>'Paper & Forest Products',
 				'39'=>'Performing Arts',
 				'15'=>'Pharmaceuticals',
 				'131'=>'Philanthropy',
 				'136'=>'Photography',
 				'117'=>'Plastics',
 				'107'=>'Political Organization',
 				'67'=>'Primary/Secondary Education',
 				'83'=>'Printing',
 				'105'=>'Professional Training & Coaching',
 				'102'=>'Program Development',
 				'79'=>'Public Policy',
 				'98'=>'Public Relations and Communications',
 				'78'=>'Public Safety',
 				'82'=>'Publishing',
 				'62'=>'Railroad Manufacture',
 				'64'=>'Ranching',
 				'44'=>'Real Estate',
 				'40'=>'Recreational Facilities and Services',
 				'89'=>'Religious Institutions',
 				'144'=>'Renewables & Environment',
 				'70'=>'Research',
 				'32'=>'Restaurants',
 				'27'=>'Retail',
 				'121'=>'Security and Investigations',
 				'7'=>'Semiconductors',
 				'58'=>'Shipbuilding',
 				'20'=>'Sporting Goods',
 				'33'=>'Sports',
 				'104'=>'Staffing and Recruiting',
 				'22'=>'Supermarkets',
 				'8'=>'Telecommunications',
 				'60'=>'Textiles',
 				'130'=>'Think Tanks',
 				'21'=>'Tobacco',
 				'108'=>'Translation and Localization',
 				'92'=>'Transportation/Trucking/Railroad',
 				'59'=>'Utilities',
 				'106'=>'Venture Capital & Private Equity',
 				'16'=>'Veterinary',
 				'93'=>'Warehousing',
 				'133'=>'Wholesale',
 				'142'=>'Wine and Spirits',
 				'119'=>'Wireless',
 				'103'=>'Writing and Editing'
 		
 		);
 		return $industry;
 	}
 	
    public function  et_get_job_type()
    {
    	$list='';
    	$job_types		= et_get_job_types ();
    	foreach ($job_types as $type) {
    		$list .='<option value="'. $type->slug .'">'.$type->name.'</option>';
    	}
    	return $list;
    }	
    
    public  function et_job_categories_option_list ( $parent =	0, $level = 0 , $selected = '') {
    	$cats = et_get_job_categories(  array('parent' => $parent ));
    	$html='';
    	$delimeter	=	'';
    	for ($i=0; $i<$level; $i ++) {
    		$delimeter	.=	'&nbsp;&nbsp;';
    	}
    	if ( !empty($cats) ){
    		foreach ($cats as $cat) {
    			if( $cat->slug == $selected ) {
    				$selected = 'selected="selected"';
    			}
    		  $html .='<option value="' . $cat->slug . '" '.$selected.'>' .$delimeter. $cat->name . '</option>';
    			$this->et_job_categories_option_list ( $cat->term_id , $level + 1, $selected);
    		}
    		
    	}
    	return $html;
    }

 	public function plugin_frontend_styles(){
 	?>
	 	<style type="text/css">
			#indeed_at, #indeed_at *{
				text-transform: none !important;
				font-size: 1em;
			}

			.main-column .list-jobs li .desc .job-type span,
			.main-column .list-jobs li .desc .cat span{
				font-weight: bold;
			}
	 	</style>
 	<?php 
 	}
     //register js	
 	public function plugin_scripts(){
 		wp_register_script('je_linkedin', plugin_dir_url( __FILE__).'/je_linkedin.js', array('jquery', 'underscore' ,'backbone'));
 	
 		wp_enqueue_script( 'jquery' );
 		wp_enqueue_script( 'et_backbone' );
 		wp_enqueue_script( 'et_underscore' );
 		wp_enqueue_script( 'job_engine' );
 		wp_enqueue_script( 'admin_scripts' );
 		wp_enqueue_script( 'je_linkedin' );
 		wp_enqueue_script( 'jquery-ui-autocomplete' );
 	
 		wp_localize_script('je_linkedin', 'je_linkedin', array(
 				'ajax_url' 		=> admin_url('admin-ajax.php'),
 				'paginate_text' => __('Display ', ET_DOMAIN)
 		));
 	}
 	//register css
 	public function plugin_styles(){
 		wp_enqueue_style( 'admin_styles' );
 		wp_enqueue_style('je_linkedin_import_css', plugin_dir_url( __FILE__).'/je_linkedin.css');
 	}
 	function et_linkedin_import_callback($args){
 		$api_key	=$this->get_linkedin_api_key();
 		$sub_section = empty($_REQUEST['subSection']) ? '' : $_REQUEST['subSection'];
 		echo $sub_section;
 		//print_r($search_str);
 		?>
 		<div id="je_linkedin_import" >
 			<div class="et-main-header">
 				<div class="title font-quicksand"><?php echo $args->page_title ?></div>
 				<div class="desc"><?php echo $args->page_subtitle ?></div>
 				
 			</div>
 			<div class="et-main-content" >
 				<div class="et-main-left">
 					<ul class="et-menu-content inner-menu">
 					  <li>
 							<a href="#linkedin-settings" menu-data="linkedin" class="<?php if ( isset($sub_section) && ($sub_section == '' || $sub_section == 'linkedin')) echo 'active'  ?>">
 								<span class="icon" data-icon="y"></span><?php _e("API Setting",ET_DOMAIN);?>
 							</a>
 						</li>
 						<li>
 							<a href="#linkedin-import" menu-data="import">
 								<span class="icon" data-icon="W"></span><?php _e("Manual",ET_DOMAIN);?>
 							</a>
 						</li>
 						<li>
						<a href="#linkedin-schedule" menu-data="schedule" class="">
							<span class="icon" data-icon="t"></span><?php _e("Schedule",ET_DOMAIN);?>
						</a>
					</li>
 						<li>
						<a href="#linkedin-manage" menu-data="manage" class="">
							<span class="icon" data-icon="l"></span><?php _e("Manage",ET_DOMAIN);?>
						</a>
					</li>
 					</ul>
 				</div>
 				<div class="setting-content">
 					<?php
 					include dirname(__FILE__) . '/linkedin-settings.php';
 					include dirname(__FILE__) . '/import-linkedin.php';
 					include dirname(__FILE__) . '/schedule-linkedin.php';
 					include dirname(__FILE__) . '/manage-linkedin.php';
 					 ?>
 				</div>
 			</div>
 		</div>
 			
 			<script id="imported_template" type="text/template">
 				<tr>
 					<td><input class="allow" type="checkbox" name="" id="" value="<?php echo '<%= ID %>' ?>"></th>
 					<td><a target="_blank" href="<?php echo '<%= link %>' ?>"><?php echo '<%= title %>' ?></a></td>
                     <td><?php echo '<%= url %>' ?></td>
 					<td><?php echo '<%= creator %>' ?></td>
 					<td><?php echo '<%= date %>' ?></td>
 				</tr>
 			</script>
 			<?php
 		}
 		
 		/**
 		 * Insert custom js template
 		 */
 		public function add_linkedin_template(){
 			?>
 				<script type="text/template" id="template_linkedin">
 					<?php echo $this->frontend_js_template() ?>
 				</script>
 				<?php 
 			}
 		
 			public function add_linkedin_mobile_template(){
 				?>
 				<script type="text/template" id="template_mobile_linkedin">
 					<?php echo $this->mobile_js_template() ?>
 				</script>
 				<?php 
 			}
 			/**
 			 *
 			 */
 			public function frontend_js_template($template = ''){
 				$strings = array(
 						'edit' 		=> __('Edit', ET_DOMAIN),
 						'featured' 	=> __('Featured', ET_DOMAIN),
 						'approve' 	=> __('Approve', ET_DOMAIN),
 						'reject' 	=> __('Reject', ET_DOMAIN),
 						'archive' 	=> __('Archive', ET_DOMAIN),
 						'view_by' 	=> __("View jobs posted by ", ET_DOMAIN),
 						'remove_featured' => __('Remove Featured', ET_DOMAIN),
 						'set_featured' => __('Set Featured', ET_DOMAIN),
 						'paid' 		=> __('PAID', ET_DOMAIN),
 						'unpaid' 	=> __('UNPAID', ET_DOMAIN),
 						'free' 		=> __('FREE', ET_DOMAIN)
 				);
 				$plugins_url	=	plugin_dir_url(__FILE__);
 				if (get_option('et_linkedin_display_label')) {
 					$template = <<<TEMPLATE
	<div class='thumb'>
		<a href="<%= author_data['post_url'] %>">
			<img src="<%=linkedin_logo%>"/>
		</a>
	</div>
	<div class='content'>
		<a class='title-link' href='<%= permalink %>'><h6 class='title'><%= title %></h6></a>
		<div class='tech font-heading f-right actions'>
			<% if ( featured === "1"  && status !== 'pending' && status !== 'draft'){ %>
				<span class='feature font-heading'>{$strings['featured']}</span>
			<% } %>
			<span id='linkedin_at' class='font-links-style'><a target="_blank" href="http://www.linkedin.com/">jobs</a> by <a
				href="http://www.linkedin.com/" target="_blank" title="LinkedIn Job"><img
				src="{$plugins_url }/linkedin.png" style="border: 0;
				vertical-align: middle;" alt="LinkedIn job"></a>
			</span>
		</div>
 			
		<div class='desc f-left-all'>
			<div class='cat company_name'>
				<%= linkedin_company %>
			</div>
			<% if (typeof job_types[0] != 'undefined' ){ %>
			<div class='job-type color-<% if (typeof job_types[0].color != 'undefined') { %><%=job_types[0].color%> <% } %>'>
				<span class='flag'></span>
				<% _.each(job_types, function(type) { %>
					<%= type.name %>
				<% }); %>
			</div>
			<% } %>
			<div><span class='icon' data-icon='@'></span><span class='job-location'><%= location %></span></div>
		</div>
	</div>
TEMPLATE;
 				} else {
 					$template = <<<TEMPLATE
	<div class='thumb'>
		<a href="<%= author_data['post_url'] %>">
			<img src="<%=linkedin_logo%>"/>
		</a>
	</div>
	<div class='content'>
		<a class='title-link' href='<%= permalink %>'><h6 class='title'><%= title %></h6></a>
		<div class='tech font-heading f-right actions'>
			<% if ( featured === "1"  && status !== 'pending' && status !== 'draft'){ %>
				<span class='feature font-heading'>{$strings['featured']}</span>
			<% } %>
		</div>
 			
		<div class='desc f-left-all'>
			<div class='cat company_name'>
				<%= linkedin_company %>
			</div>
			<% if (typeof job_types[0] != 'undefined' ){ %>
			<div class='job-type color-<% if (typeof job_types[0].color != 'undefined') { %><%=job_types[0].color%> <% } %>'>
				<span class='flag'></span>
				<% _.each(job_types, function(type) { %>
					<%= type.name %>
				<% }); %>
			</div>
			<% } %>
			<div><span class='icon' data-icon='@'></span><span class='job-location'><%= location %></span></div>
		</div>
	</div>
TEMPLATE;
 				}
 				return $template;
 			}
 			
 			public function mobile_js_template($template = ''){
 				$variables = array();
 				$template = <<<TEMPLATE
		<li class="list-item">
			<a href="<%= permalink %>" data-transition="slide">
				<h2 class="list-title">
					<%= title %>
				</h2>
				<p class="list-subtitle">
					<span class="list-info job-loc"><%= linkedin_company %></span>
					<% if ( job_types.length > 0 ) { %>
						<span class="list-info job-title color-<% if (typeof job_types[0].color != 'undefined') { %><%= job_types[0].color %> <% } %>">
							<span class="icon-label flag"></span>
							<% _.each(job_types, function(type) { %>
								<%= type.name %>
							<% }); %>
						</span>
					<% } %>
					<% if ( location != '' ) { %>
						<span class="list-info job-loc icon" data-icon="@"><%= location %></span>
					<% } %>
				</p>
			</a>
			<div class="mblDomButtonGrayArrow arrow">
				<div></div>
			</div>
		</li>
TEMPLATE;
 				return $template;
 			} 	

 		function custom_link_apply(){
 			global  $job;
 			if(is_single() && get_post_type()=='job'){
 					$template=et_get_post_field( $job->ID,'template_id');
 				if($template=='linkedin'){
 				 	 $link=et_get_post_field($job->ID, 'linkedin_url');
 				  
 				  ?>
 				  
	 				  <script type="text/javascript">
				            jQuery('document').ready(function(){
				                  $('#how_to_apply .bg-btn-action').removeAttr('id'); 
				                  $('#how_to_apply .bg-btn-action').click(function(){
				                	     window.open('<?php echo $link;?>','_blank');
				                      }); 
				                });
				            </script> 
 				  <?php 
 				}
 			
 			}
 		}

 	 function custom_hide_sidebar_infor(){
 	 	global  $job;
 	 	if(is_single() && get_post_type()=='job'){
 	 		$template=et_get_post_field( $job->ID,'template_id');
 	 		if($template=='linkedin'){
 	 	?>
		 	 	<style type="text/css">
		         #sidebar-job-detail .company-profile{
			       display: none;
		         }   
		       </style>
 	 	<?php 
 	 		}
 	 	}
 	 }	
 		
 }
 
 require_once dirname(__FILE__) . '/je_linkedin_ajax.php';
 require_once dirname(__FILE__) . '/je_linkedin_event.php';
 new JE_Import_LinkedIn_ScheduleEvent();
 new JE_LINKEDIN_IMPORT_AJAX();	
 require_once dirname(__FILE__) . '/lib/linkedin_3.2.0.class.php';