<?php
/*
Plugin Name: JE SimplyHired
Plugin URI: www.enginethemes.com
Description: Import SimplyHired jobs into your JobEngine-powered job board
Version: 1.1.2
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/
require_once dirname(__FILE__) . '/update.php';

define ('JE_Simply_VERSION', '1.1.1');

class JE_SimplyHired 
{
	function __construct () {

		add_action('et_admin_enqueue_scripts-je-simplyhired', array($this, 'plugin_scripts'));
		add_action('et_admin_enqueue_styles-je-simplyhired', array($this, 'plugin_styles'));
		/**
		 * register menu setting
		*/
		add_action('et_admin_menu', array($this, 'register_menu_simplyhired'));


		add_filter('et_template_job', array($this, 'modify_job_template'));
		add_filter('et_mobile_template_job', array($this, 'mobile_template'));
		//add_filter('et_mobile_job_template', array($this, 'mobile_js_template'));
		
		add_filter('et_jobs_ajax_response', array($this, 'jobs_ajax_response'));

		add_action('wp_footer', array($this, 'add_simplyhired_template'));
		add_action('et_mobile_footer', array($this, 'add_simplyhired_mobile_template'));

		register_activation_hook(__FILE__, array($this,'activation'));
		register_deactivation_hook(__FILE__, array($this,'deactivation'));
		

		add_action ('template_redirect', array($this, 'simplyhired_template_redirect'));

		add_filter('pre_get_posts', array($this, 'pre_get_posts'));
		add_filter('wp', array($this, 'remove_filter_orderby'));
		add_filter('et_get_job_count_where', array($this, 'modify_job_count'));

		add_action('admin_notices', array($this, 'admin_notice') );

		add_filter( 'et_get_translate_string', array($this, 'add_translate_string') );

		add_filter( 'cron_schedules', array($this, 'cron_add_weekly'));

	}
	
	function add_translate_string ($entries) {
		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/default.po',true );
		
		return	array_merge($entries, $pot->entries);
	}

	/**
	 * add custome interval for schedule
	*/
	function cron_add_weekly( $schedules ) {
	 	// Adds once weekly to the existing schedules.
	 	$time	=	get_option('et_simplyhired_recurrence',5);
	 	$schedules['custom_simplyhired_recurrence'] = array(
	 		'interval' =>  $time*3600*24 ,
	 		'display' => __( 'Custom SimplyHired Schedule' )
	 	);
	 	$schedules['custom_single_simplyhired_schedule'] = array(
	 		'interval' =>  30,
	 		'display' => __( 'Custom Single SimplyHired Schedule (10 minutes)' )
	 	);
	 	return $schedules;
	}

	/**
	 * deactivate plugin
	*/
	function deactivation () {
		global $wpdb;
		$prefix		=	$wpdb->prefix;
		$et_prefix	=	'et_';
		$query	=	"update $wpdb->posts set post_status = 'draft' 
							where ID IN 
								(select post_id 
									from $wpdb->postmeta 
									where 	meta_key ='et_template_id' 
										  	and meta_value = 'simplyhired') ";
		$wpdb->query($query);

		wp_clear_scheduled_hook('simplyhired_delete_job_event');
		wp_clear_scheduled_hook('simplyhired_import_schedule_event');
		wp_clear_scheduled_hook('simplyhired_import_single_schedule_event');
	}
	/**
	 * active main schedule
	*/
	function activate_schedule () {
		$time_stamp	=	date('d M y 00:00:00', time() + 3600*24);
		$time_stamp	=	strtotime( $time_stamp );
		
		wp_clear_scheduled_hook('simplyhired_import_schedule_event');
		wp_clear_scheduled_hook('simplyhired_import_single_schedule_event');

		wp_schedule_event( $time_stamp , 'custom_simplyhired_recurrence', 'simplyhired_import_schedule_event');	
	}
	/**
	 * activate plugin
	*/
	function activation () {
		$this->activate_schedule ();
		global $wpdb;
		$prefix		=	$wpdb->prefix;
		$et_prefix	=	'et_';
		$query		=	"update $wpdb->posts set post_status = 'publish' 
							where ID IN 
								(select post_id 
									from $wpdb->postmeta 
									where 	meta_key ='et_template_id' 
										  	and meta_value = 'simplyhired') ";
		$wpdb->query($query);

		
		$this->add_delete_job_event ();

	}

	function simplyhired_schedule ($schedule) {

		if(isset($schedule['ON']) && $schedule['ON'] == 0  ) return ;

		$search_str	=	'';
		foreach ($schedule as $key => $value) {
			if($key == 'job_category' || $key == 'job_type' || $key == 'author' 
			  || $key == 'import_author' || $key =='schedule_id' || $key == 'ON') 
				continue;

			if( $value != '' )
			$search_str	.=	'/'.$key.'-'.urlencode($value);
		} 

		$api_url	=	$this->get_api_url ();
		$api		=	$this->get_settings_str ();

		$response	=	$this->simplyhired_job_sync($api_url.$search_str.$api);
		
		if(!$response['success']) return;

		$jobs	=	$response['jobs'];
		
		foreach ($jobs as $key => $job) {

			$post	=	new WP_Query(
							array(
								'post_type' 	=> 'job', 
								'post_status' 	=> 'publish', 
								'meta_key' 		=> 'et_simplyhired_jobkey', 
								'meta_value' 	=> $job['jobkey']
							)
						);
			if($post->found_posts <=0) {
				foreach ($job as $key => $value) {
					if($key != 'location' && $key != 'jobkey')
						$job[$key]	=	$value[0];
				}
				$job['job_category']	=	$schedule['job_category'];
				$job['job_type']		=	$schedule['job_type'];
				$this->save_job( $job, $schedule['import_author'] );
			}
		}
	}

	public function admin_notice () {
		if(!self::check_curl()) {
			echo '<div class="error">'.__("JE SimplyHired requires CURL to be installed on your server. Please ask your hosting provider to install PHP CURL for you.", ET_DOMAIN).'</div>';
		} 
	}

	public static function check_curl () {
		if  (in_array  ('curl', get_loaded_extensions())) {
	        return true;
	    }
	    else{
	        return false;
	    }
	}

	/**
	 * 
	 */
	public function pre_get_posts($query){
		if (is_author()){
			// remove_all_filters('pre_get_posts');
			// $query->set('post_status', array('publish'));
			// add_filter('posts_orderby', array(&$this, 'filter_orderby'));
			add_filter('posts_where', array($this, 'remove_simplyhired_job'));
			// $query->set('meta_key', 'et_featured');
			// $query->set('orderby', 'date');
			// $query->set('order', 'DESC');
		}
	}

	public function remove_simplyhired_job($where){
		global $wpdb;
		remove_filter('posts_where', array($this, 'remove_simplyhired_job'));
		return $where . " AND {$wpdb->posts}.ID NOT IN (SELECT simply.post_id FROM {$wpdb->postmeta} as simply WHERE meta_key = 'et_simplyhired_url')";
	}

	public function modify_job_count($where){
		global $wpdb;
		return $where . " AND {$wpdb->posts}.ID NOT IN (SELECT simply.post_id FROM {$wpdb->postmeta} as simply WHERE meta_key = 'et_simplyhired_url')";
	}

	public function filter_orderby($order){
		global $wpdb;
		return "{$wpdb->postmeta}.meta_value DESC, {$wpdb->posts}.post_date DESC";
	}

	public function remove_filter_orderby(){
		remove_filter('posts_orderby', array(&$this, 'filter_orderby'));
	}

	/**
	 * redirect to indeed job if visitor access job by link
	*/
	public function simplyhired_template_redirect ( ) {
		if(is_single() && get_post_type() == 'job') {
			global $post;
			$template_id	=	get_post_meta( $post->ID,'et_template_id', true);
			if($template_id == 'simplyhired')
				wp_redirect( get_post_meta( $post->ID,'et_simplyhired_url', true));
		}
	}

	/**
	 * add event daily clean up simplyhired job
	*/
	public function add_delete_job_event () {
		$time_stamp	=	date('d M y 00:00:00', time() );
		$time_stamp	=	strtotime( $time_stamp );
		wp_clear_scheduled_hook('simplyhired_delete_job_event');

		$day	=	get_option( 'je_simplyhired_delete_days', '' );
		if($day != '')
			wp_schedule_event( $time_stamp , 'daily', 'simplyhired_delete_job_event');	
	}

	public function plugin_styles(){
		wp_enqueue_style('admin_styles');
		wp_enqueue_style('simply_hired_css', plugin_dir_url( __FILE__).'/simplyhired.css');
	}

	public function plugin_scripts () {
		// wp_deregister_script( 'jquery' ); // deregisters the default WordPress jQuery & enqueue the new one later
		// wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'et_underscore' );
		wp_enqueue_script( 'et_backbone' );
		wp_enqueue_script( 'job_engine' );
		wp_enqueue_script( 'admin_scripts');
		wp_enqueue_script( 'jquery-ui-autocomplete' );

		wp_enqueue_script('simply_hired_js', plugin_dir_url( __FILE__).'/simplyhired.js', array('jquery', 'underscore', 'backbone', 'job_engine')) ;
	}

	function get_api_url () {
		return 'http://api.simplyhired.com/a/jobs-api/xml-v2';
	}

	/**
	 * add schedule 
	*/
	function save_schedule ($schedule) {
		$schedule_list	=	$this->get_schedule_option ();
		if( $schedule['schedule_id'] != '' && isset($schedule_list[$schedule['schedule_id']]) )  {
			$schedule_list[$schedule['schedule_id']]	=	 $schedule;
		} else {
			if(!empty($schedule_list))
				$i	=	max(array_keys($schedule_list)) + 1 ;
			else $i	=	1;
			$schedule['schedule_id']	=	$i;
			$schedule_list[$i]	=	$schedule;
			
		}
		$this->update_schedule_option ($schedule_list);
		return $schedule;
	}	
	/**
	 * get schedule list
	*/
	function get_schedule_option () {
		return get_option ('je_simplyhired_schedule_option', array());
	}
	/**
	 * update schedule list
	*/
	function update_schedule_option ($schedule_list) {
		return update_option('je_simplyhired_schedule_option', $schedule_list );
	}
	

	function save_settings ( $settings	=	array() ) {
		update_option( 'je_simplyhired_api_settings', $settings );
	}

	function get_settings () {
		$default	=	array(
				'pshid'	=> '47735',
				'jbd'	=>	'dakachi.jobamatic.com',	
				'ssty'	=> 2,
				'cflg'	=> 'r'
			);
		return get_option ('je_simplyhired_api_settings', $default);
	}

	function get_settings_str () {
		$settings	=	$this->get_settings( );
		$api_str	=	'?';
		foreach ($settings as $key => $value) {
			$api_str	.=	$key.'='.$value.'&';
		}
		//$api_str	.=	'clip='.$_SERVER['HTTP_CLIENT_IP'];
		return $api_str;
	}

	function get_search_string () {
		//http://www.simplyhired.ca/a/jobs/list/t-developer/qa-developer/qe-developer/qo-developer/lc-sydney/ls-AB/lz-12/fjt-full-time/fex-3/fed-2/fdb-14/fln-en
		$default	=	array (
				'q'			=>	'Wordpress developer',
				'fjt'		=>	'',
				'lc'		=>  'Palo Alto',
				'ls'		=>  'CA',
				'lz'		=>  '94560',
				'ws'		=>  '20',
				'pn'		=>  '1'
			);
		return get_option ('je_simplyhired_search_string', $default );
	}
	
	function save_search_string ($search_str) {
		update_option( 'je_simplyhired_search_string', $search_str );
	}


	public function simplyhired_job_sync ($search_str) {

		$ch	=	curl_init();
		curl_setopt($ch, CURLOPT_URL, $search_str ); 

		//return the transfer as a string 
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
		$response	=	curl_exec($ch);
		curl_close($ch);
		$job_data	=	simplexml_load_string($response);
		
		if(isset($job_data->error)) {
			$response	=	array(
					'success'	=> false,
					'msg'		=> __("No results", ET_DOMAIN)
				);

		} else {
			$jobs	=	array();
			foreach ($job_data->rs->r as $key => $value) {
				$location	=	(array)$value->loc;			
				$company	=	(array)$value->cn[0];

				$key		=	$this->get_jobkey_by_url($value->src['url']);

				$jobs[]	=	array(
						'title'			=> (array)$value->jt,
						'company'		=> (array) (isset($company[0]) ? $company[0] : '' ),
						'url'			=> (array)$value->src['url'],
						'jobkey'		=> $key,
						'location'		=> $location[0],
						'job_type'		=> (array)$value->ty,
						'date'			=> (array)$value->dp,
						'description'	=> (array)$value->e
						//'exist'			=> $key[1]
					);
			}

			$response	=	array(
				'success'	=> true,
				'ws'		=> $job_data->rq->rpd,
				'total'		=> $job_data->rq->tr,
				'jobs'		=> $jobs
			);	
		}
	
		return $response;
	}

	/**
	 * find simplyhired job key from job url
	 * @param $url : string
	 * @return string job key
	*/
	function get_jobkey_by_url ($url) {
		
		$start_pos 	= 	strpos($url, 'jobkey-') + 7 ;
		$end_pos	=	strpos($url, '/rid');

		$jobkey		=	substr($url, $start_pos);
		$jobkey		=	explode('/rid', $jobkey);

		$jobkey		=	$jobkey[0];
		
		return $jobkey;

	}

	/**
	 * save simplyhired job
	*/
	function save_job ($job , $import_author) {
		
		$prefix = 'et_';
		
		$result = wp_insert_post(array(
			'post_author' 	=> ($import_author != '' ) ? $import_author : 1,
			'post_date'		=> date('Y-m-d 00:00:00',strtotime($job['date']) ),
			'post_status' 	=> 'publish',
			'post_content' 	=> apply_filters('et_job_content',$job['description'] ),
			'post_type' 	=> 'job',
			'post_title' 	=> $job['title'],
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
			if( $term_info = term_exists( $job['job_type'], 'job_type') ) { ;
				$tt_id	=	$term_info['term_taxonomy_id'];
				$wpdb->insert( $wpdb->term_relationships, array( 'object_id' => $result, 'term_taxonomy_id' => $tt_id ) );
				wp_update_term_count( array($tt_id), 'job_type' );
			}

		}

		update_post_meta($result, $prefix . 'simplyhired_url', $job['url']);
		update_post_meta($result, $prefix . 'template_id', 'simplyhired');
		update_post_meta($result, $prefix . 'simplyhired_creator', $job['company']);
		update_post_meta($result, $prefix . 'simplyhired_jobkey', $job['jobkey']);
		update_post_meta($result, $prefix . 'location', $job['location']);
		update_post_meta($result, $prefix . 'full_location', $job['location']);
		update_post_meta($result, $prefix . 'job_paid', 2);

		update_post_meta($result, $prefix . 'simplyhired_add_time', date('Y-m-d 00:00:00', time () ));

		return true;

	}

	function delete_job_from_date () {
		global $wpdb;
		$day_limit	=	intval(get_option('je_simplyhired_delete_days', 30));
		$day_pos 	= 	date('Y-m-d 00:00:00', strtotime('-' . $day_limit . ' days'));
		
		$sql 		= $wpdb->prepare("SELECT p.ID  
										FROM {$wpdb->posts} as p 
										WHERE p.ID in (select post_id 
														from $wpdb->postmeta 
														where 	meta_key ='et_simplyhired_add_time' 
																  	and meta_value <= %s )", $day_pos);
		
		$results 	= $wpdb->get_col($sql);
		
		$count 		= count ($results);
		
		$string		= implode(',', $results)	;
		
		$jobs_str	=	'('.$string.')';

		$wpdb->query ("DELETE FROM {$wpdb->posts} WHERE ID IN $jobs_str");
		$wpdb->query ("DELETE FROM {$wpdb->postmeta} WHERE post_id IN $jobs_str");

		return $count;

	}

	function query_page ($page) {
		$query = new WP_Query(array(
								'post_type' 		=> 'job',
								'meta_key' 			=> 'et_template_id',
								'meta_value' 		=> 'simplyhired',
								'posts_per_page' 	=> 10,
								'paged' 			=> $page
							));
 		foreach ($query->posts as $job) {
 			$jobs[] = array(
 				'ID'		=> $job->ID,
 				'title' 	=> $job->post_title,
 				'creator' 	=> get_post_meta($job->ID, 'et_simplyhired_creator', true),
 				'url' 		=> get_post_meta($job->ID, 'et_simplyhired_url', true),
 				'date' 		=> date('d-m-Y', strtotime($job->post_date))
 				);
 		}

 		if ($query->post_count > 0)
 			$resp = array(
 				'success' 	=> true,
 				'msg' 		=> '',
 				'data' 		=> array(
 					'page' => $page,
 					'pages_max' => $query->max_num_pages,
 					'jobs' => $jobs, 
 					'count' => $query->post_count
 				)
 			);
 		else 
 			$resp = array(
 				'success' 	=> false,
 				'msg' 		=> ''
 			);

 		return $resp;
	}

	/**
	 * 
	 */
	public function jobs_ajax_response($response){
		$opt 					= new ET_GeneralOptions();
		$simplyhired_logo 		= $opt->get_default_logo();

		$is_simplyhired_job 	= et_get_post_field($response['id'], 'simplyhired_url');
		$company				= et_create_companies_response( $response['author_id'] );
		
		$company_logo		= $company['user_logo'];
		$logo				=	'';
		if($company_logo['thumbnail'][0] && $company_logo['thumbnail'][0] != '' )
			$logo	=	$company_logo['thumbnail'][0];
		$url 					= et_get_post_field($response['id'], 'simplyhired_url');
		$plus 	= array(
				'is_simplyhired_job'	=> et_get_post_field($response['id'], 'simplyhired_url') != '' ? true : false,
				'simplyhired_url' 		=> et_get_post_field($response['id'], 'simplyhired_url'),
				'simplyhired_logo'  	=>  ($logo == '') ? array_shift($simplyhired_logo) : $logo,
				'simplyhired_company' 	=> et_get_post_field($response['id'], 'simplyhired_creator'),
		);
		return $response + $plus;
	}

	/**
	 * modify job template
	*/
	public function modify_job_template ($template) {
		global $job;
		if ($job['template_id'] == 'simplyhired')
			return dirname(__FILE__) . '/template-job.php';
		else 
			return $template;
	}
	/**
	 * modify job mobile template
	*/
	public function mobile_template ( $template ) {
		global $job;
		if ($job['template_id'] == 'simplyhired')
			return dirname(__FILE__) . '/mobile-template-job.php';
		else 
			return $template;
	}

	/**
	 * Insert custom js template
	 */
	public function add_simplyhired_template(){
		?>
		<script type="text/template" id="template_simplyhired">
			<?php echo $this->frontend_js_template() ?>
		</script>
		<?php 
	}
	/**
	 * insert mobile custom js template
	*/
	public function add_simplyhired_mobile_template(){
		?>
		<script type="text/template" id="template_mobile_simplyhired">
			<?php echo $this->mobile_js_template() ?>
		</script>
		<?php 
	}

	/**
	 * 
	 */
	public function frontend_js_template($template = ''){

		//$plugins_url	=	plugin_dir_url(__FILE__);
		$template = <<<TEMPLATE
			<div class='thumb'>
				<a href="<{{simplyhired_url}}">
					<img src="{{simplyhired_logo}}"/>
				</a>
			</div>
			<div class='content'>
				<a class='title-link' target="_blank" href='{{simplyhired_url}}'><h6 class='title'>{{ title }}</h6></a>
				<div class='desc f-left-all'>
					<div class='cat company_name'>
						{{ simplyhired_company }}
					</div>
					<# if (typeof job_types[0] != 'undefined' ){ #>
					<div class='job-type color-<# if (typeof job_types[0].color != 'undefined') { #>{{job_types[0].color}} <# } #>'>
						<span class='flag'></span>				
						<# _.each(job_types, function(type) { #>
							<a href="{{type.url}}" >{{type.name}} </a> 
						<# }); #>
					</div>
					<# } #>
					<div><span class='icon' data-icon='@'></span><span class='job-location'>{{location}}</span></div>
				</div>
			</div>
TEMPLATE;
	
		return $template;
	}

	public function mobile_js_template($template = ''){
		
		$template = <<<TEMPLATE
		<li class="list-item">
			<a href="{{simplyhired_url}}" target="_blank" data-transition="slide">
				<h2 class="list-title">
					{{title}}
				</h2>
				<p class="list-subtitle">
					<span class="list-info job-loc">{{simplyhired_company}}</span>
					<# if ( job_types.length > 0 ) { #>
						<span class="list-info job-title color-<# if (typeof job_types[0].color != 'undefined') { #>{{job_types[0].color}} <# } #>">
							<span class="icon-label flag"></span>
							<# _.each(job_types, function(type) { #>
								{{type.name}}
							<# }); #>
						</span>
					<# } #>
					<# if ( location != '' ) { #>
						<span class="list-info job-loc icon" data-icon="@">{{location}}</span>
					<# } #>
				</p>
			</a>
			<div class="mblDomButtonGrayArrow arrow">
				<div></div>
			</div>
		</li>
TEMPLATE;
		return $template;
	}


	function register_menu_simplyhired () {
		// register payment menu item
		et_register_menu_section('simplyhired', array(
			'menu_title' 	=> __('JE SimplyHired', ET_DOMAIN),
			'page_title' 	=> __('JE SIMPLYHIRED', ET_DOMAIN),
			'callback' 		=> array( $this, 'et_simplyhired_callback' ),
			'slug'			=> 'je-simplyhired',
			'page_subtitle'	=>	__('Import SimplyHired jobs into your job board', ET_DOMAIN)
		));
	}
	/**
	 * callback function to render menu
	*/
	function et_simplyhired_callback ($args) {
		$sub_section = empty($_REQUEST['subSection']) ? '' : $_REQUEST['subSection'];
	?>
	<div id="simply-hired" >
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->page_title ?></div>
			<div class="desc"><?php echo $args->page_subtitle ?></div>
		</div>
		<div class="et-main-content" >
			<div class="et-main-left">
				<ul class="et-menu-content inner-menu">
					<li>
						<a href="#simply_hired_setting" menu-data="simplyhired" class="<?php if ( isset($sub_section) && ($sub_section == '' || $sub_section == 'simplyhired')) echo 'active'  ?>">
							<span class="icon" data-icon="y"></span><?php _e("API Setting",ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#simplyhired-import" menu-data="import" class="">
							<span class="icon" data-icon="s"></span><?php _e("Search Jobs",ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#simplyhired-schedule" menu-data="schedule" class="">
							<span class="icon" data-icon="t"></span><?php _e("Schedule",ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#simplyhired-manage" menu-data="manage" class="">
							<span class="icon" data-icon="l"></span><?php _e("Manage Jobs",ET_DOMAIN);?>
						</a>
					</li>
				</ul>
			</div>
			<div class="setting-content">
				<?php
				include dirname(__FILE__) . '/settings.php';
				include dirname(__FILE__) . '/import.php';
				include dirname(__FILE__) . '/schedule.php';
				include dirname(__FILE__) . '/manage.php';
				 ?>
			</div>
		</div>
		<?php 
			$job_types	=	get_terms( 'job_type', array('hide_empty' => false ) );
		?>
		<script id="simplyhired_row_template" type="text/template">
			<tr>
				<td>
					<input type="hidden" name="import[{{ i }}][allow]" value="0">
					<input type="checkbox" class="allow" name="import[{{i}}][allow]" value="1" checked="checked">
					<input type="hidden" name="import[{{ i }}][url]" value="{{url}}">
					<input type="hidden" name="import[{{ i }}][date]" value="{{date}}">
					<input type="hidden" name="import[{{ i }}][location]" value="{{location}}">
					<input type="hidden" name="import[{{ i }}][company]" value="{{company}}">
					<input type="hidden" name="import[{{ i }}][jobkey]" value="{{jobkey}}">
					<input type="hidden" name="import[{{ i }}][description]" value="{{description}}">
					<input type="hidden" name="import[{{ i }}][title]" value="{{title}}">
				</td>
				<td class="jobtitle">
					<a href="{{url}}" target="_blank">{{title}}</a>
					<div class="bubble-quote">
						<span>@ {{company}}</span>
						<span class="icon" data-icon="@">{{location}}</span>
						<div class="triangle"></div>
					</div>
				</td>
				<td>
					<div class="select-style et-button-select">
						<select class="job_cat" name="import[{{i}}][job_category]">
							<?php echo et_job_categories_option_list(); ?>
						</select>
					</div>
				</td>
				<td>
					<div class="select-style et-button-select">
						<select class="job_type" name="import[{{i}}][job_type]">
							<?php foreach ($job_types as $type) {
								echo '<option value="'. $type->slug .'">'.$type->name.'</option>';
							} ?>
						</select>
					</div>
				</td>
			</tr>
		</script>
		<script id="imported_template" type="text/template">
			<tr>
				<td><input class="allow" type="checkbox" name="" value="{{ID}}"></th>
				<td><a target="_blank" href="{{url}}">{{title}}</a></td>
				<td>{{creator}}</td>
				<td>{{date}}</td>
			</tr>
		</script>
	</div>
	<?php
	}

}

require_once dirname(__FILE__) . '/simplyhired-ajax.php';

new JE_SimplyHired_Ajax ();
new JE_Simplyhired_Schedule ();