<?php

require_once dirname(__FILE__) . '/update.php';
class JE_Import {
	function __construct() {
		add_action('et_admin_enqueue_scripts-et-indeed', array($this, 'plugin_scripts'));
		add_action('et_admin_enqueue_styles-et-indeed', array($this, 'plugin_styles'));
		add_action('wp_print_styles', array($this, 'plugin_frontend_styles'));
		add_action('wp_enqueue_scripts', array($this, 'plugin_frontend_scripts'));

		add_action('et_admin_menu', array($this, 'register_menu_import'));
		add_filter('et_template_job', array($this, 'modify_job_template'));
		//add_filter('et_mobile_job_template', array($this, 'mobile_js_template'));
		add_filter('et_mobile_template_job', array($this, 'mobile_template'));
		add_filter('et_jobs_ajax_response', array($this, 'jobs_ajax_response'));

		add_filter('pre_get_posts', array($this, 'pre_get_posts'));
		add_filter('wp', array($this, 'remove_filter_orderby'));
		add_filter('et_get_job_count_where', array($this, 'modify_job_count'));
		add_action('wp_footer', array($this, 'add_indeed_template'));
		add_action('et_mobile_footer', array($this, 'add_indeed_mobile_template'));

		add_action('admin_notices', array($this, 'admin_notice') );
		// new post type
		register_post_type('indeed_schedule',
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
		add_action ('template_redirect', array($this, 'indeed_template_redirect'));
	}

	private function indeed_support_country () {
		return array(  'us' 	=> 'United States',	'ar'	=> 'Argentina', 	'au' => 'Australia', 			'at' => 'Austria',
				'bh'	=> 'Bahrain' ,		'be' 	=> 'Belgium',		'br'	=> 'Brazil', 			'ca' => 'Canada', 		'cl' => 'Chile',
				'cn'	=> 'China' ,		'co' 	=> 'Colombia',		'cz'	=> 'Czech Republic', 	'dk' => 'Denmark', 		'fi' => 'Finland',
				'fr'	=> 'France' ,		'de' 	=> 'Germany',		'gr'	=> 'Greece', 			'hk' => 'Hong Kong', 	'hu' => 'Hungary',
				'in'	=> 'India' ,		'id' 	=> 'Indonesia',		'ie'	=> 'Ireland', 			'il' => 'Israel', 		'it' => 'Italy',
				'jp'	=> 'Japan' ,		'kr' 	=> 'Korea',			'kw'	=> 'Kuwait', 			'lu' => 'Luxembourg', 	'my' => 'Malaysia',
				'mx'	=> 'Mexico' ,		'nl' 	=> 'Netherlands',	'no'	=> 'Norway', 			'om' => 'Oman', 		'pk' => 'Pakistan',
				'pe'	=> 'Peru' ,			'ph' 	=> 'Philippines',	'pl'	=> 'Poland', 			'pt' => 'Portugal', 	'qa' => 'Qatar',
				'ro'	=> 'Romania' ,		'ru' 	=> 'Russia',		'sa'	=> 'Saudi Arabia', 		'sg' => 'Singapore', 	'za' => 'South Africa',
				'es'	=> 'Spain' ,		'se' 	=> 'Sweden',		'ch'	=> 'Switzerland', 		'tw' => 'Taiwan', 		'ae' => 'United Arab Emirates',
				'gb'	=> 'United Kingdom' ,		've' 	=> 'Venezuela' , 	'tr' => 'Turkey'
			);
	}
	/**
	 * redirect to indeed job if visitor access job by link
	*/
	public function indeed_template_redirect ( ) {
		if(is_single() && get_post_type() == 'job') {
			global $post;
			$template_id	=	get_post_meta( $post->ID,'et_template_id', true);
			if($template_id == 'indeed')
				wp_redirect( get_post_meta( $post->ID,'et_indeed_url', true));
		}
	}

	public function admin_notice () {
		if(!self::check_curl()) {
			echo '<div class="error">'.__("JE Indeed requires CURL to be installed on your server. Please ask your hosting provider to install PHP CURL for you.", ET_DOMAIN).'</div>';
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

	function delete_old_jobs(){
		global $wpdb;
		$day_limit 	= intval(get_option('et_indeed_delete_days', '30'));
		$day_pos 	= date('Y-m-d 00:00:00', strtotime('-' . $day_limit . ' days'));
		$sql 		= $wpdb->prepare("SELECT DISTINCT p.ID as ID FROM {$wpdb->posts} as p JOIN {$wpdb->postmeta} as mt ON mt.post_id = p.ID WHERE  p.post_type = 'job' AND mt.meta_key = 'et_indeed_id' ", $day_pos);

		$results 	= $wpdb->get_results($sql);
		$count 		= 0;
		foreach ($results as $result) {
			if (wp_delete_post($result->ID, true )){
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Hook into posts where to find out which job is outdate
	 */
	function posts_where_old_jobs($where){
		return $where;
	}

	function auto_import_job () {
		$jobdata	=	$this->indeed_job_sync ('q=wordpress+developer&l=New+York&limit=30&fromage=30', $this->get_indeed_publisher_id());
		$results	=	$jobdata->results->result;
		if(!empty( $results )) {
			foreach ($results as $value) {
				wp_insert_post(array('post_title' => $value->jobtitle, 'post_content' => $value->snippet, 'post_type' => 'post', 'post_status' => 'publish'));
			}
		}
	}

	public function plugin_scripts(){
		wp_register_script('je_indeed', plugin_dir_url( __FILE__).'/je_indeed.js', array('jquery', 'backbone', 'underscore'), JE_INDEED_VER );

		// wp_deregister_script( 'jquery' ); // deregisters the default WordPress jQuery & enqueue the new one later
		// wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'et_backbone' );
		wp_enqueue_script( 'et_underscore' );
		wp_enqueue_script( 'job_engine' );
		wp_enqueue_script( 'admin_scripts' );
		wp_enqueue_script( 'je_indeed' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );

		wp_localize_script('je_indeed', 'je_indeed', array(
			'ajax_url' 		=> admin_url('admin-ajax.php'),
			'paginate_text' => __('Display ', ET_DOMAIN)
			));
	}


	public function schedule_activation () {
		// update schedule event
		wp_clear_scheduled_hook( 'et_auto_main_import_indeed' );
		$time = mktime(0, 0, 0, date('n'), date('j') + 1);
		wp_schedule_event( $time, 'indeed', 'et_auto_main_import_indeed');
	}

	public function plugin_styles(){
		wp_enqueue_style( 'admin_styles' );
		wp_enqueue_style('je_import_css', plugin_dir_url( __FILE__).'/je_indeed.css');
	}

	public function plugin_frontend_styles(){
		wp_enqueue_style('je_import_css', plugin_dir_url( __FILE__).'/frontend.css');
	}

	public function plugin_frontend_scripts () {
		wp_enqueue_script( 'indeed-apiresults', 'http://www.indeed.com/ads/apiresults.js', array(), '3.3' , false );
	}

	/**
	 * Register menu setting
	 */
	public function register_menu_import(){
		// register payment menu item
		et_register_menu_section('import', array(
			'menu_title' 	=> __('JE Indeed', ET_DOMAIN),
			'page_title' 	=> __('JE INDEED', ET_DOMAIN),
			'callback' 		=> array( $this, 'et_import_callback' ),
			'slug' 			=> 'et-indeed',
			'page_subtitle'	=>	__('Import Indeed jobs into your job board', ET_DOMAIN)
			));
	}

	/**
	 *
	 */
	public function pre_get_posts($query){
		if (is_author()){
			// remove_all_filters('pre_get_posts');
			// $query->set('post_status', array('publish'));
			// add_filter('posts_orderby', array(&$this, 'filter_orderby'));
			add_filter('posts_where', array($this, 'remove_indeed_job'));
			// $query->set('meta_key', 'et_featured');
			// $query->set('orderby', 'date');
			// $query->set('order', 'DESC');
		}
	}

	public function remove_indeed_job($where){
		global $wpdb;
		remove_filter('posts_where', array($this, 'remove_indeed_job'));
		return $where . " AND {$wpdb->posts}.ID NOT IN (SELECT indeed.post_id FROM {$wpdb->postmeta} as indeed WHERE meta_key = 'et_indeed_url')";
	}

	public function modify_job_count($where){
		global $wpdb;
		return $where . " AND {$wpdb->posts}.ID NOT IN (SELECT indeed.post_id FROM {$wpdb->postmeta} as indeed WHERE meta_key = 'et_indeed_url')";
	}

	public function filter_orderby($order){
		global $wpdb;
		return "{$wpdb->postmeta}.meta_value DESC, {$wpdb->posts}.post_date DESC";
	}

	public function remove_filter_orderby(){
		remove_filter('posts_orderby', array(&$this, 'filter_orderby'));
	}

	public function modify_job_template($template){
		global $job;
		if ($job['template_id'] == 'indeed')
			return dirname(__FILE__) . '/template-job.php';
		else return $template;
	}

	public function mobile_template($template){
		global $job;
		if ($job['template_id'] == 'indeed')
			return dirname(__FILE__) . '/mobile-template-job.php';
		else
			return $template;
	}

	function set_search_string ($data) {
		$data	=	explode('&', urldecode($data)); 
		$s	=	array();
		foreach ($data as $key => $value) {
			$v	=	explode('=', $value);
			$s[$v[0]]	=	$v[1];
		}
		update_option( 'et_indeed_search_string', $s );
	}

	function get_search_string () {
		$default	=	array(
			'co'		=> 'us',
			'q'			=>	'',
			'l'			=> '',
			'limit'		=> '',
			'fromage'	=> ''
		);
		return get_option( 'et_indeed_search_string', $default );
	}


	/**
	 *
	 */
	public function fetch_job( $publisher, $params = array()){
		$default = array(
			'q' 		=> '',
			'l'			=> '',
			'limit' 	=> 25,
			'co'		=> 'us',
			'within' 	=> '30',
		);
		$params 	= wp_parse_args( $params, $default );
		$qstring 	= 'http://api.indeed.com/ads/apisearch' . http_build_query($params);

		$resp = $this->indeed_job_sync($qstring, $publisher);
		$response = array(
			'query' 		=> (string)$resp->query,
			'location' 		=> (string)$resp->location,
			'total' 		=> (string)$resp->totalresults,
			'start' 		=> (string)$resp->start,
			'paged' 		=> ceil($resp->start / $params['limit']),
			'data' 			=> (array)$resp->results->result
		);
		return $response;
	}

	public function indeed_job_sync ($search_str, $publisher) {

		$publisher	=	$publisher;
		$ch	=	curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://api.indeed.com/ads/apisearch?publisher=$publisher&format=xml&v=2&$search_str");

		//return the transfer as a string
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
		$response	=	curl_exec($ch);
		curl_close($ch);

		$job_data	=	simplexml_load_string($response);

		return $job_data;
	}


	/**
	 * Save sending job
	 */
	function save_indeed_jobs($job, $author = 1){
		$prefix = 'et_';
		$_POST += array('post_type' => 'job');
		$result = wp_insert_post(array(
			'post_author' 	=> $author,
			'post_date'		=> date('Y-m-d h:i:s', time() ) ,
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

			if( $term_info = term_exists( $job['job_type'], 'job_type') ) { ;
				$tt_id	=	$term_info['term_taxonomy_id'];
				$wpdb->insert( $wpdb->term_relationships, array( 'object_id' => $result, 'term_taxonomy_id' => $tt_id ) );
				wp_update_term_count( array($tt_id), 'job_type' );
			}
		}

		$meta_maps = array(
			'location' 			=> 'formattedLocationFull',
			'full_location' 	=> 'formattedLocationFull',
			'indeed_company' 	=> 'company',
			'indeed_url' 		=> 'url',
			'indeed_city' 		=> 'city',
			'indeed_state' 		=> 'city',
			'indeed_country' 	=> 'country',
			'indeed_id' 		=> 'jobkey'
		);

		foreach ($meta_maps as $key => $value) {
			update_post_meta($result, $prefix . $key, $job[$value]);
		}

		update_post_meta($result, $prefix . 'template_id', 'indeed');

		return true;
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
		if (!empty($data['location'])) 	update_post_meta( $data['id'], 'indeed_loc', $data['location'] );
		if (!empty($data['country'])) 	update_post_meta( $data['id'], 'indeed_co', $data['country'] );
		if (!empty($data['limit'])) 	update_post_meta( $data['id'], 'indeed_lim', $data['limit'] );
		//if (!empty($data['within'])) 	update_post_meta( $data['id'], 'indeed_within', $data['within'] );
		if (!empty($data['cat'])) 		update_post_meta( $data['id'], 'indeed_cat', $data['cat'] );
		if (!empty($data['type'])) 		update_post_meta( $data['id'], 'indeed_type', $data['type'] );
		if (!empty($data['author'])) 	update_post_meta( $data['id'], 'indeed_author', $data['author'] );
		if (!empty($data['active'])) 	update_post_meta( $data['id'], 'indeed_active', $data['active'] );
	}

	public function build_schedule_item($post){
		if (!is_object($post)){
			$id = $post;
			$post = get_post($post);
		}

		if (!isset($post->ID))
			return false;

		return array(
			'id' 		=> $post->ID,
			'title' 	=> $post->post_title,
			'location' 	=> get_post_meta( $post->ID, 'indeed_loc', true),
			'country' 	=> get_post_meta( $post->ID, 'indeed_co', true),
			'limit' 	=> get_post_meta( $post->ID, 'indeed_lim', true),
			//'within' 	=> get_post_meta( $post->ID, 'indeed_within', true),
			'cat' 		=> get_post_meta( $post->ID, 'indeed_cat', true),
			'type' 		=> get_post_meta( $post->ID, 'indeed_type', true),
			'author'	=> get_post_meta( $post->ID, 'indeed_author', true),
			'active' 	=> get_post_meta( $post->ID, 'indeed_active', true)
		);
	}

	/**
	 * 
	 */
	public function jobs_ajax_response($response){
		$opt 			= new ET_GeneralOptions();
		$is_indeed_job 	= et_get_post_field($response['id'], 'indeed_url');
		$indeed_logo 	= $opt->get_default_logo();
		$url 			= et_get_post_field($response['id'], 'indeed_url');
		$plus = array(
			'is_indeed_job' => et_get_post_field($response['id'], 'indeed_url') != '' ? true : false,
			'indeed_url' 	=> et_get_post_field($response['id'], 'indeed_url'),
			'indeed_logo'  	=> is_array($indeed_logo) ? array_shift($indeed_logo) : '',
			'indeed_company' =>  et_get_post_field($response['id'], 'indeed_company'),
			'indeed_ref_url' 	=> str_replace('viewjob', 'rc/clk', $url)
		);
		return $response + $plus;
	}

	public function get_indeed_publisher_id () {
		return get_option ('et_indeed_publisher_id' );
	}

	public function set_indeed_publisher_id ( $id ) {
		return update_option ('et_indeed_publisher_id', $id);
	}

	/**
	 * Schedule
	 */


	/**
	 * Insert custom js template
	 */
	public function add_indeed_template(){
		?>
		<script type="text/template" id="template_indeed">
			<?php echo $this->frontend_js_template() ?>
		</script>
		<?php
	}

	public function add_indeed_mobile_template(){
		?>
		<script type="text/template" id="template_mobile_indeed">
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
		if (get_option('et_indeed_display_label')) {
		$template = <<<TEMPLATE
	<div class='thumb'>
		<a href="{{ indeed_ref_url }}">
			<img src="{{indeed_logo}}"/>
		</a>
	</div>
	<div class='content'>
		<a class='title-link' target="_blank" href='{{ indeed_ref_url }}'><h6 class='title'>{{title }}</h6></a>
		<div class='tech font-heading f-right actions'>
			<# if ( featured === "1"  && status !== 'pending' && status !== 'draft'){ #>
				<span class='feature font-heading'>{$strings['featured']}</span>
			<# } #>
			<span id=indeed_at><a href="http://www.indeed.com/">jobs</a> by <a
				href="http://www.indeed.com/" title="Job Search"><img
				src="http://www.indeed.com/p/jobsearch.gif" style="border: 0;
				vertical-align: middle;" alt="Indeed job search"></a>
			</span>
		</div>

		<div class='desc f-left-all'>
			<div class='cat company_name'>
				{{ indeed_company }}
			</div>
			<# if (typeof job_types[0] != 'undefined' ){ #>
			<div class='job-type color-<# if (typeof job_types[0].color != 'undefined') { #>{{job_types[0].color}} <# } #>'>
				<span class='flag'></span>
				<# _.each(job_types, function(type) { #>
					{{ type.name }}
				<# }); #>
			</div>
			<# } #>
			<div><span class='icon' data-icon='@'></span><span class='job-location'>{{location}}</span></div>
		</div>
	</div>
TEMPLATE;
	} else {
		$template = <<<TEMPLATE
	<div class='thumb'>
		<a href="{{ indeed_ref_url }}">
			<img src="{{indeed_logo}}"/>
		</a>
	</div>
	<div class='content'>
		<a class='title-link' target="_blank" href='{{indeed_ref_url}}'><h6 class='title'>{{title}}</h6></a>
		<div class='tech font-heading f-right actions'>
			<# if ( featured === "1"  && status !== 'pending' && status !== 'draft'){ #>
				<span class='feature font-heading'>{$strings['featured']}</span>
			<# } #>
		</div>

		<div class='desc f-left-all'>
			<div class='cat company_name'>
				{{ indeed_company }}
			</div>
			<# if (typeof job_types[0] != 'undefined' ){ #>
			<div class='job-type color-<# if (typeof job_types[0].color != 'undefined') { #>{{job_types[0].color}} <# } #>'>
				<span class='flag'></span>
				<# _.each(job_types, function(type) { #>
					{{ type.name }}
				<# }); #>
			</div>
			<# } #>
			<div><span class='icon' data-icon='@'></span><span class='job-location'>{{ location }}</span></div>
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
			<a href="{{ indeed_ref_url }}" target="_blank" data-transition="slide">
				<h2 class="list-title">
					{{ title }}
				</h2>
				<p class="list-subtitle">
					<span class="list-info job-loc"> {{ indeed_company }}</span>
					<# if ( job_types.length > 0 ) { #>
						<span class="list-info job-title color-<# if (typeof job_types[0].color != 'undefined') { #>{{job_types[0].color}} <# } #>">
							<span class="icon-label flag"></span>
							<# _.each(job_types, function(type) { #>
								{{ type.name }}
							<# }); #}
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

	function et_import_callback($args){
		$publisher_id	=	$this->get_indeed_publisher_id ( );
		$sub_section = empty($_REQUEST['subSection']) ? '' : $_REQUEST['subSection'];
		$search_str	=	$this->get_search_string();
		extract($search_str);
	?>
	<div id="je_import" >
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->page_title ?></div>
			<div class="desc"><?php echo $args->page_subtitle ?></div>
			<div class="header-more-info">
				<input title="Publisher ID" type="text" name="publisher_id" id="publisher_id" value="<?php echo $publisher_id ?>" placeholder="<?php _e('Publisher ID') ?>">
				<span class="more-info-help" title="<?php _e('Your Publisher ID on Indeed.com', ET_DOMAIN) ?>">?</span>
			</div>
		</div>
		<div class="et-main-content" >
			<div class="et-main-left">
				<ul class="et-menu-content inner-menu">
					<li>
						<a href="#indeed-import" menu-data="import" class="<?php if ( isset($sub_section) && ($sub_section == '' || $sub_section == 'import')) echo 'active'  ?>">
							<span class="icon" data-icon="W"></span><?php _e("Manual",ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#indeed-schedule" menu-data="schedule" class="">
							<span class="icon" data-icon="t"></span><?php _e("Schedule",ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a href="#indeed-manage" menu-data="manage" class="">
							<span class="icon" data-icon="l"></span><?php _e("Manage",ET_DOMAIN);?>
						</a>
					</li>
				</ul>
			</div>
			<div class="setting-content">
				<?php
				include dirname(__FILE__) . '/import.php';
				include dirname(__FILE__) . '/schedule.php';
				include dirname(__FILE__) . '/manage.php';
				 ?>
			</div>
		</div>
	</div>
		<script id="import_row_template" type="text/template">
			<tr>
				<td>
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][allow]" value="0">
					<input type="checkbox" class="allow" name="import[<?php echo '{{ i }}' ?>][allow]" value="1" checked="checked">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][url]" value="<?php echo '{{  url  }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][date]" value="<?php echo '{{  date  }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][formattedLocationFull]" value="<?php echo '{{  formattedLocationFull }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][company]" value="<?php echo '{{  company  }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][jobkey]" value="<?php echo '{{  jobkey  }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][content]" value="<?php echo '{{ snippet  }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][city]" value="<?php echo '{{  city  }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][state]" value="<?php echo '{{  state  }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][country]" value="<?php echo '{{  country }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][jobtitle]" value="<?php echo '{{  jobtitle  }}' ?>">
					<input type="hidden" name="import[<?php echo '{{ i }}' ?>][onmousedown]" value="<?php echo '{{ onmousedown }}' ?>">
				</td>
				<td class="jobtitle">
					<a href="<?php echo '{{ url }}' ?>" target="_blank"><?php echo '{{  jobtitle  }}' ?></a>
					<div class="bubble-quote">
						<span>@ <?php echo '{{company }}' ?></span>
						<span class="icon" data-icon="@"><?php echo '{{ formattedLocationFull }}' ?></span>
						<div class="triangle"></div>
					</div>
				</td>
				<td>
					<div class="select-style et-button-select">
						<select class="job_cat" name="import[<?php echo '{{  i  }}' ?>][job_category]">
							<?php echo et_job_categories_option_list(); ?>
						</select>
					</div>
				</td>
				<td>
					<div class="select-style et-button-select">
						<select class="job_type" name="import[<?php echo '{{ i }}' ?>][job_type]">
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
				<td><input class="allow" type="checkbox" name="" value="<?php echo '{{ ID }}' ?>"></th>
				<td><a target="_blank" href="<?php echo '{{url}}' ?>"><?php echo '{{ title }}' ?></a></td>
				<td><?php echo '{{creator }}' ?></td>
				<td><?php echo '{{date }}' ?></td>
			</tr>
		</script>
		<?php
	}
}

require_once dirname(__FILE__) . '/je_indeed_event.php';
require_once dirname(__FILE__) . '/je_indeed_ajax.php';

add_action  ('after_setup_theme', 'je_init_indeed') ;
function je_init_indeed () {
	// trigger the pluggin class
	// new JE_Import_ScheduleEvent();
	// new JE_Import_Ajax();
}

?>