<?php
/*
Plugin Name: JE RSS Importer
Plugin URI: www.enginethemes.com
Description: Import jobs from RSS feeds into your JobEngine-powered job board
Version: 2.3
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/
require_once dirname(__FILE__) . '/update.php';

define('JERSSIMPORT_VERSION', "2.3");
class JE_RSS_Import {
	function __construct(){
		add_action('et_admin_enqueue_scripts-je-rss', array($this, 'plugin_scripts'));
		add_action('et_admin_enqueue_styles-je-rss', array($this, 'plugin_styles'));
		//add_action('wp_print_styles', array($this, 'plugin_frontend_styles'));

		add_action('et_admin_menu', array($this, 'register_menu_import'));
		add_filter('et_template_job', array($this, 'modify_job_template'));

		add_filter('wp_footer', array($this, 'add_rss_template'));
		
		add_filter('et_mobile_template_job', array($this, 'mobile_template'));
		add_action('et_mobile_footer', array($this, 'add_rss_mobile_template'));

		add_filter('et_jobs_ajax_response', array($this, 'ajax_jobs_response'));

		
		add_filter( 'cron_schedules', array($this, 'cron_add_weekly'));

		add_filter('single_template', array($this, 'redirect_single_rss'));
		add_filter('template_include', array($this, 'mobile_single_rss') , 20 );

		add_action( 'wp_print_scripts',array ($this,'deregister_javascript') );

 	 	add_filter( 'et_get_translate_string', array($this, 'add_translate_string') );

 	 	register_activation_hook(__FILE__, array($this,'schedule_activation'));
		register_deactivation_hook(__FILE__, array($this,'schedule_deactivation'));

	}
	

	function add_translate_string ($entries) {
		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/default.po',true );
		return	array_merge($entries, $pot->entries);
	}

	/**
	 * deregister js in single rss job
	*/
	function deregister_javascript() {
		if(is_single() && get_post_type() == 'job') {
			global $post;
			$template_id	=	get_post_meta( $post->ID,'et_template_id', true);
			if($template_id == 'rss') {
				wp_deregister_script('single_job');
			}
		}
	}
	/**
	 * add custome interval for schedule
	*/
	function cron_add_weekly( $schedules ) {
	 	// Adds once weekly to the existing schedules.
	 	$time	=	get_option('et_rss_recurrence',5);
	 	$schedules['custom_rss_recurrence'] = array(
	 		'interval' =>  $time*3600*24 ,
	 		'display' => __( 'Custom RSS Schedule' )
	 	);
	 	$schedules['custom_single_rss_schedule'] = array(
	 		'interval' =>  30 ,
	 		'display' => __( 'Custom Single RSS Schedule' )
	 	);
	 	return $schedules;
	}

	function schedule_deactivation () {
		wp_clear_scheduled_hook('rss_import_schedule_event');
		wp_clear_scheduled_hook('rss_import_single_schedule_event');
		global $wpdb;
		$prefix		=	$wpdb->prefix;
		$et_prefix	=	'et_';
		$query	=	"update $wpdb->posts set post_status = 'draft' 
							where ID IN 
								(select post_id 
									from $wpdb->postmeta 
									where 	meta_key ='et_template_id' 
										  	and meta_value = 'rss') ";
		$wpdb->query($query);
	}
	/**
	 * activate schedule
	*/
	function schedule_activation () {
		$time_stamp	=	date('d M y 00:00:00', time() + 3600*24 );
		$time_stamp	=	strtotime( $time_stamp );
		
		wp_clear_scheduled_hook('rss_import_schedule_event');
		wp_clear_scheduled_hook('rss_import_single_schedule_event');

		wp_schedule_event( $time_stamp , 'custom_rss_recurrence', 'rss_import_schedule_event');	
		
		global $wpdb;
		$prefix		=	$wpdb->prefix;
		$et_prefix	=	'et_';
		$query	=	"update $wpdb->posts set post_status = 'publish' 
							where ID IN 
								(select post_id 
									from $wpdb->postmeta 
									where 	meta_key ='et_template_id' 
										  	and meta_value = 'rss') ";
		$wpdb->query($query);
	}

	public function plugin_scripts(){

		// wp_deregister_script( 'jquery' ); // deregisters the default WordPress jQuery & enqueue the new one later
		// wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'et_underscore' );
		wp_enqueue_script( 'et_backbone' );
		wp_enqueue_script( 'job_engine' );
		wp_enqueue_script( 'admin_scripts');

		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('rss_import_js', plugin_dir_url( __FILE__).'/rss_import.js', array('jquery', 'underscore', 'backbone')) ;
	}

	public function plugin_styles(){
		wp_enqueue_style('admin_styles');
		wp_enqueue_style('rss_import_css', plugin_dir_url( __FILE__).'/rss_import.css');
	}

	public function plugin_frontend_styles(){
		wp_enqueue_style('rss_front_end_css', plugin_dir_url( __FILE__).'/frontend.css');
	}

	function update_schedule_option ( $value )	{
		update_option ('et_rss_schedule', $value);
	}

	function get_schedule_option () {
		$schedules	=	 get_option('et_rss_schedule', array()) ;
		foreach ($schedules as $key => $value) {
			$schedules[$key]	=	wp_parse_args( $value , array('job_location' => ''));
		}
		return $schedules;
	}
	/**
	 * get rss data
	 * @param  $rss_link string
	 * @param $check  bool true check rss link valid or not
	*/
	function get_rss_data ( $rss_link, $check = false) {
		if($rss_link == '') {
			return array (
					'success'	=> false,
					'msg'		=> array('0' => __('Your RSS feed is invalid.', ET_DOMAIN) )
				);
		}
		/**
		 * fetch feed from rss link use simple pie
		*/
		$feed = fetch_feed( $rss_link );

		
		
		$data	=	array();
		
		if(!is_wp_error( $feed )) {
			$max	=	0;
			if($check) $max	=	3;

			$maxitems = $feed->get_item_quantity($max);
			$rss_items = $feed->get_items(0, $maxitems); 
			
			foreach ($rss_items as $key => $item) {
				
				if(!$check) {
					$post	=	new WP_Query(
									array(
										'post_type' 	=> 'job', 
										'post_status' 	=> 'publish', 
										'meta_key' 		=> 'et_rss_url', 
										'meta_value' 	=> (string)$item->get_permalink(),
									)
								);

					if( $post->found_posts <= 0 ) 
						$data[]	= array(
							'title'			=> $item->get_title(),
							'link'			=> $item->get_permalink(),
							'content'		=> $item->get_content(),
							'pubDate'		=> $item->get_date('Y-m-d h:i:s'),
							'creator'		=> $feed->get_title()
						);
				} else {
					$data[]	= array(
						'title'			=> ($item->get_title()),
						'link'			=> ($item->get_permalink()),
						'content'		=> ($item->get_content()),
						'pubDate'		=> $item->get_date('Y-m-d h:i:s'),
						'creator'		=> ($feed->get_title())
					);
				}
			}
			wp_reset_query();
			
			if(!empty($data))
				return array (
						'success'	=> true,
						'data'		=> $data
					);
				
			else {
				if($check) 
					return array (
						'success'	=> true,
						'data'		=> $data 
					); 
				else 
					return array (
							'success'	=> false,
							'msg'		=> array('0' => __('There is no new job in this RSS feed. You may have already imported all of them.', ET_DOMAIN) )
						);
				
			}
		} else {
			return array (
					'success'	=> false,
					'msg'		=> array('0' => __('Your RSS feed is invalid.', ET_DOMAIN) )
				);
		}

	}
	
	/**
	 * save job
	*/
	function manual_rss_save_jobs ($job, $import_author) {
		
		
		$prefix = 'et_';
		$result = false;
		$flag = $this->job_rss_check($job['url']);
		if(!$flag)
		$result = wp_insert_post(array(
			'post_author' 	=> $import_author,
			//'post_date'		=> date('Y-m-d h:i:s', time()) ,
			'post_status' 	=> 'publish',
			'post_content' 	=> apply_filters('et_job_content',$job['jobdesc'] ),
			'post_type' 	=> 'job',
			'post_title' 	=> $job['jobtitle'],
			'tax_input' 	=> array(
				'job_category' => array((int)$job['job_category']),
				'job_type' => array((int)$job['job_type']),
				),
		));	

		// if insert fail, return false
		if ( !$result )
			return false;

		update_post_meta($result, 'et_rss_url', trim($job['url']) );
		if(isset($job['location'])){
			update_post_meta($result, $prefix . 'location', $job['location']);
			update_post_meta($result, $prefix . 'full_location', $job['location']);
		}
		update_post_meta($result, $prefix . 'template_id', 'rss');
		update_post_meta($result, $prefix . 'rss_creator', $job['creator']);

		return true;
	}

	function schedule_import_job($job, $import_author) {
		$prefix = 'et_';
		$result = false;
		$flag = $this->job_rss_check($job['url']);
		if(!$flag)
		$result = wp_insert_post(array(
			'post_author' 	=> $import_author,
			//'post_date'		=> date('Y-m-d h:i:s', time()) ,
			'post_status' 	=> 'publish',
			'post_content' 	=> apply_filters('et_job_content',$job['jobdesc'] ),
			'post_type' 	=> 'job',
			'post_title' 	=> $job['jobtitle']
		));	

		// if insert fail, return false
		if ( !$result )
			return false;
			// otherwise, insert meta data and terms
			wp_set_object_terms($result, $job['job_category'] , 'job_category');	
			wp_set_object_terms($result, $job['job_type'] , 'job_type');	

			// update_post_meta($result, $prefix . 'rss_job_category', $job['job_category']);
			// update_post_meta($result, $prefix . 'rss_job_type', $job['job_type']);

		if(defined('ALTERNATE_WP_CRON')) {
			//update_post_meta( $result, 'et_rss_fix_cron', 1 );
			global $wpdb;
			wp_set_object_terms($result, $job['job_category'] , 'job_category');	
			wp_set_object_terms($result, $job['job_type'] , 'job_type');	
			
		}

		update_post_meta($result, 'et_rss_url', trim($job['url']) );
		if(isset($job['location'])){
			update_post_meta($result, $prefix . 'location', $job['location']);
			update_post_meta($result, $prefix . 'full_location', $job['location']);
		}
		update_post_meta($result, $prefix . 'template_id', 'rss');
		update_post_meta($result, $prefix . 'rss_creator', $job['creator']);

		return true;
	}

	/**
	* check if job imported
	**/
	function job_rss_check($url){
		$url = trim($url);
		global $wpdb;
		$exist_url = $wpdb->get_row("SELECT * FROM $wpdb->postmeta WHERE meta_key  = 'et_rss_url' and meta_value = '$url'");
		if(!$exist_url)
			return false;
		return true;

	}
	/**
	 * delete job out of date by limit date setting
	*/
	function delete_job_from_date () {
		global $wpdb;
		$day_limit	=	intval(get_option('je_rss_delete_days', 30));
		$day_pos 	= 	date('Y-m-d 00:00:00', strtotime('-' . $day_limit . ' days'));
		
		$sql 		= $wpdb->prepare("SELECT p.ID  
										FROM {$wpdb->posts} as p 
										WHERE 	p.post_type = 'job' 
										 AND 	p.post_date <= %s 
										 AND 	p.ID in (select post_id 
														from $wpdb->postmeta 
														where 	meta_key ='et_template_id' 
																  	and meta_value = 'rss')", $day_pos);
		$results 	= $wpdb->get_col($sql);
		
		$count 		= count ($results);
		
		$string		= implode(',', $results)	;
		
		$jobs_str	=	'('.$string.')';

		$wpdb->query ("DELETE FROM {$wpdb->posts} WHERE ID IN $jobs_str");
		$wpdb->query ("DELETE FROM {$wpdb->postmeta} WHERE post_id IN $jobs_str");

		return $count;

	}

	/**
	 * Register menu setting
	 */
	public function register_menu_import(){
		// register payment menu item
		et_register_menu_section('rss_import', array(
			'menu_title' 	=> __('JE RSS', ET_DOMAIN),
			'page_title' 	=> __('JE RSS', ET_DOMAIN),
			'callback' 		=> array( $this, 'et_import_callback' ),
			'slug'          => 'je-rss',
			'page_subtitle'	=>	__('Import jobs from RSS feeds into your job board', ET_DOMAIN)
		));
	}

	public function redirect_single_rss( $page_template ) {
		global $isMobile;
		//$detector = new ET_MobileDetect();
		//$isMobile = apply_filters( 'et_is_mobile', ($detector->isMobile()) ? true : false );
		$isMobile = apply_filters( 'et_is_mobile', false );

		if(is_single() && get_post_type() == 'job' && !$isMobile) {
			global $post;
			$template_id	=	get_post_meta( $post->ID,'et_template_id', true);
			if($template_id == 'rss'){			
				$page_template = dirname(__FILE__). '/single-rss.php';	
			}
		}		
		return $page_template;
	}

	public function mobile_single_rss ($template) {
		global $wp_query, $post;
		
		if(!is_singular( 'job' )) return $template;
		
		$filename 		= basename($template);
		$template_id	=	get_post_meta( $post->ID, 'et_template_id', true );
		
	    if ( $filename == 'single-job.php' && $template_id == 'rss' ) {
	    	$detector = new ET_MobileDetect();
			$isMobile = apply_filters( 'ce_is_mobile', ( $detector->isIphone() || $detector->isAndroid() || $detector->isWindowsphone() ) ? true : false );	
	       	if ( $isMobile ) {
	       		$template =  dirname(__FILE__). '/mobile/single-rss.php';
	       	}
	    }

		return $template;
	}

	public function modify_job_template($template){
		global $job;
		if ( $job['template_id'] == 'rss')
			return dirname(__FILE__) . '/template-job.php';
		return $template;
	}

	public function mobile_template($template){
		global $job;
		
		if ($job['template_id'] == 'rss')
			return dirname(__FILE__) . '/mobile-template-job.php';
		return $template;
	}

	function et_import_callback ($args) {
		$sub_section = empty($_REQUEST['subSection']) ? '' : $_REQUEST['subSection'];
	?>	
		<div id="rss_import">
			<div class="et-main-header">
				<div class="title font-quicksand"><?php echo $args->page_title ?></div>
				<div class="desc"><?php echo $args->page_subtitle ?></div>
			</div>
			<div class="et-main-content" >
				<div class="et-main-left">
					<ul class="et-menu-content inner-menu">
						<li>
							<a href="#rss-import" menu-data="import" class="<?php if ( isset($sub_section) && ($sub_section == '' || $sub_section == 'import')) echo 'active'  ?>">
								<span class="icon" data-icon="W"></span><?php _e("Manual",ET_DOMAIN);?>
							</a>
						</li>
						<li>
							<a href="#rss-schedule" menu-data="schedule" class="<?php if ( isset($sub_section) && $sub_section == 'schedule') echo 'schedule' ?>">
								<span class="icon" data-icon="t"></span><?php _e("Schedule",ET_DOMAIN);?>
							</a>
						</li>
						<li>
							<a href="#rss-manage" menu-data="manage" class="<?php if ( isset($sub_section) && $sub_section == 'job') echo 'manage' ?>">
								<span class="icon" data-icon="l"></span><?php _e("Manage",ET_DOMAIN);?>
							</a>
						</li>
					</ul>
				</div>
				<div class="setting-content">
					<?php require_once dirname(__FILE__).'/manual.php' ?>
					<?php require_once dirname(__FILE__).'/schedule.php' ?>
					<?php require_once dirname(__FILE__).'/manage.php' ?>
				</div>
			</div>
		</div>
		<!-- template -->
		<script id="import_row_template" type="text/template">
		<?php echo '<tr>
				<td>
					<input type="hidden" name="import[{{ i }}][allow]" value="0">
					<input type="checkbox" class="allow" name="import[{{ i }}][allow]" value="1" checked="checked">
					<input type="hidden" name="import[{{ i }}][url]" value="{{ link }}">
					<input type="hidden" name="import[{{ i }}][date]" value="{{ pubDate }}">
					<input type="hidden" name="import[{{ i }}][jobtitle]" value="{{ title }}" >
					<input type="hidden" name="import[{{ i }}][creator]" value="{{ creator }}">
					<textarea style="display:none;" name="import[{{ i }}][jobdesc]">{{ content }}</textarea>
				</td>
				<td class="jobtitle">
					<a href="{{ link }}" target="_blank">{{ title }}</a>
				</td>
				<td>
					<div class="select-style et-button-select">
						<select class="job_cat" name="import[{{ i }}][job_category]">
					';
						 echo et_job_categories_option_list(); 
				echo '		</select>
					</div>
				</td>
				<td>
					<div class="select-style et-button-select">
						<select class="job_type" name="import[{{ i }}][job_type]">
				'; 			foreach ($job_types as $type) {
								echo '<option value="'. $type->term_id .'">'.$type->name.'</option>';
							}
					echo '</select>
					</div>
				</td>
				<td class="location">
					<input  type="text" name="import[{{ i }}][location]" value="" placeholder="Enter job location" class="job_location" />
				</td>
			</tr>';
			?>
		</script>
		<script id="imported_template" type="text/template">
		<?php echo '
			<tr>
				<td><input class="allow" type="checkbox" name="" value="{{ID}}"></th>
				<td><a target="_blank" href="{{permalink}}"> {{ title }} </a></td>
				<td>{{url}}</td>
				<td>{{creator}}</td>
				<td>{{date}}</td>
			</tr>';
		?>
		</script>
		<!-- template -->
	<?php
	}
	/**
	 * Insert custom js template
	 */
	public function add_rss_template(){
		?>
		<script type="text/template" id="template_rss">
			<?php echo $this->frontend_js_template() ?>
		</script>
		<?php 
	}

	public function add_rss_mobile_template(){
		?>
		<script type="text/template" id="template_mobile_rss">
			<?php echo $this->mobile_js_template() ?>
		</script>
		<?php 
	}

	function ajax_jobs_response ( $job ) {
		$opt 	= new ET_GeneralOptions();
		$company		= et_create_companies_response( $job['author_id'] );
		$company_logo	= $company['user_logo'];
		$logo			=	'';
		$default		=	$opt->get_default_logo();
		$default		=	$default[0];
		
		if($company_logo['thumbnail'][0] && $company_logo['thumbnail'][0] != '' )
			$logo	=	$company_logo['thumbnail'][0];
		$job	+=	array(
			'rss_url'	=> et_get_post_field($job['ID'], 'rss_url') ,
			'rss_logo'	=> ($logo == '') ? $default  : $logo
		) ;
		return $job;
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
		$template = <<<TEMPLATE
	<div class='thumb'>
		<a href="{{ author_url }}">
			<img src="{{rss_logo}}"/>
		</a>
	</div>
	<div class='content'>
		<a title="{{ title }}" class='title-link' href='{{ permalink }}'><h6 class='title'>{{ title }}</h6></a>
		<div class='desc f-left-all'>
			<div class='cat company_name'>
				<a title="{{author}}" href="{{author_url}}" > {{author}}</a>
			</div>
			<# if (typeof job_types[0] != 'undefined' ){ #>
			<div class='job-type color-<# if (typeof job_types[0].color != 'undefined') { #>{{job_types[0].color}} <# } #>'>
				<span class='flag'></span>				
				<# _.each(job_types, function(type) { #>
					<a href="{{type.url}}" > 
						{{type.name }} 
					</a>
				<# }); #>
			</div>
			<# } #>
			<# if ( location != '' ) { #>
			<div><span class='icon' data-icon='@'></span><span class='job-location'>{{ location }}</span></div>
			<# } #>
		</div>
	</div>
TEMPLATE;
		return apply_filters('je_rss_frontend_js_template', $template);
	}

	public function mobile_js_template(){
		$variables = array();
		$template = <<<TEMPLATE
		<li data-icon="false" class="list-item">
			<span class="arrow-right"></span>
			<a href="{{rss_url }}" data-transition="slide" data-ajax="false" >
				<h2 class="list-title">
					{{ title }}
				</h2>
				<p class="list-subtitle">
					<span class="list-info job-loc">{{ author }}</span>
					<# if ( job_types.length > 0 ) { #>
						<span class="list-info job-title color-<# if (typeof job_types[0].color != 'undefined') { #>{{job_types[0].color}} <# } #>
							<span class="icon-label flag"></span>
							<# _.each(job_types, function(type) { #>
								{{ type.name }}
							<# }); #>
						</span>
					<# } #>
					<# if ( location != '' ) { #>
						<span class="list-info job-loc icon" data-icon="@">{{ location }}</span>
					<# } #>
				</p>
			</a>
		</li>
TEMPLATE;
		return apply_filters('je_rss_mobile_js_template', $template);
	}
	
}


add_action('after_setup_theme','je_rss_import_init', 12);
function je_rss_import_init(){
	require_once dirname(__FILE__) . '/rss_ajax.php';
	new JE_RSS_Ajax ();
	new JE_RSS_Schedule ();
}