<?php
/*
Plugin Name: JE Indeed
Plugin URI: www.enginethemes.com
Description: Import Indeed jobs into your JobEngine-powered job board
Version: 3.7.1
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/
define('JE_INDEED_VER','3.7.1');

require_once dirname(__FILE__) . '/update.php';

class JE_Indeed {
	static $instance = null;
	private $settings;

	static public function get_instance(){
		if ( self::$instance == null){
			self::$instance = new JE_Indeed();
		}
		return self::$instance;
	}

	public function indeed_support_country () {
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
				'gb'	=> 'United Kingdom' ,		've' 	=> 'Venezuela' , 'tr' => 'Turkey'
		);

	}

	public function get_job_types () {
		return array (
			"fulltime" => __("Full-time", ET_DOMAIN),
			"parttime" => __("Part-time", ET_DOMAIN),
			"contract" => __("Contract", ET_DOMAIN),
			"internship" => __("Internship", ET_DOMAIN),
			"temporary"	=> __("Temporary", ET_DOMAIN)
		) ;
	}

	public function get_publisher_id () {
		$settings	=	get_option( 'je_ineed_settings' );
		// echo "<pre>";
		// print_r($settings) ;
		// echo "</pre>";
		return $settings['publisher'];
	}

	public function update_settings ($request) {
		update_option( 'je_ineed_settings' , $request );
	}

	public function get_settings () {
		if( empty($this->settings) ) {
			$default = array(
				'q' 			=> 'teacher',
				'l'				=> '',
				'limit' 		=> '25',
				'co'			=> 'us',
				'within' 		=> '',
				'publisher'		=> '',
				'display_label' => 1,
				'fromage'		=> 30,
				"fulltime" 		=> __("Full-time", ET_DOMAIN),
				"parttime" 		=> __("Part-time", ET_DOMAIN),
				"contract" 		=> __("Contract", ET_DOMAIN),
				"internship" 	=> __("Internship", ET_DOMAIN),
				"temporary"		=> __("Temporary", ET_DOMAIN),
				'author'		=> plugin_dir_url( __FILE__).'/default_logo.jpg'
			);
			$this->settings	=	get_option( 'je_ineed_settings', $default );
		}
		return $this->settings;
	}

	public function get_posts ( $query ) {
		$default = array(
			'q' 		=> 'teacher',
			'l'			=> '',
			'limit' 	=> '25',
			'co'		=> 'us',
			'within' 	=> ''
		);

		$params 	= wp_parse_args( $query, $this->settings );
		//var_dump($params);
		$qstring 	= http_build_query($params);

		$resp = $this->indeed_job_sync($qstring);
		$response = array();
		if($resp){
			$response = array(
				'query' 		=> (string)$resp->query,
				'location' 		=> (string)$resp->location,
				'total' 		=> (string)$resp->totalresults,
				'start' 		=> (string)$resp->start,
				'paged' 		=> ceil($resp->start / $params['limit']),
				'data' 			=> (array)$resp->results,
				'params'		=> $params ,
				'qstring'		=> $qstring
			);
		}
		return $response;
	}

	public function indeed_job_sync ($search_str) {

		$publisher	=	$this->get_publisher_id();

		if(function_exists( 'getBrowser' )) {
			$useragent	=	getBrowser();
			$useragent	=	$useragent['name'];
		}

		else
			$useragent	=	'';

		$userip		=	$_SERVER['REMOTE_ADDR'];

		$url		=	"http://api.indeed.com/ads/apisearch?publisher=$publisher&format=xml&v=2&userip=$userip&$search_str";
		// echo $url;
		$ch	=	curl_init();
		curl_setopt( $ch, CURLOPT_URL, $url );

		//return the transfer as a string
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_USERAGENT, $useragent );
		$response	=	curl_exec($ch);
		curl_close($ch);

		$job_data	=	simplexml_load_string($response);

		return $job_data;
	}

}

class JE_IndeedFront {
	private $indeed;
	function __construct() {
		$this->indeed	=	JE_Indeed::get_instance();
		//add_action('wp_footer' , array($this, 'wp_footer'));

		add_action ('wp_ajax_je_after_job_list' , array ($this, 'load_job') ) ;
		add_action ('wp_ajax_nopriv_je_after_job_list' , array ($this, 'load_job') ) ;

		//add_filter('je_ajax_fetch_job' , array ($this, 'filter_fetch_job_response'));

		add_action ('et_enqueue_scripts' , array ($this, 'add_script')  );
		add_action( 'wp_head' , array ($this, 'add_css') );
		add_action ('wp_footer' , array ($this, 'add_indeed_template')  );

		add_action( 'et_mobile_footer' , array ($this,'add_mobile_script' ) );

	}

	function load_job () {
		$request	=	wp_parse_args( $_REQUEST , array('paged' => 1 , 'content' => array()  ) );

		extract($request);
		//extract($content);

		global $wp_query;

		$settings	=	$this->indeed->get_settings();

		$post_per_page	=	get_option('posts_per_page');
		//if( $wp_query->found_posts <= $post_per_page ) {
		$query 	=	array('q' => $settings['q'] , 'l' => $settings['l'] , 'start' => ($paged - 1 ) * 10 , 'limit' => $post_per_page );

		if( isset( $content['job_type'] ) ) {
			if( is_array($content['job_type']) ) {
				$content['job_type']	=	array_pop($content['job_type']);
			}

			foreach ($settings as $key => $value) {
				if( $content['job_type'] == $value )	$query['jt']	=	$key;
			}
		}

		if( isset($content['job_category']) && $content['job_category'] != '' )  {

			if( is_array($content['job_category']) ) {
				$content['job_category']	=	array_pop($content['job_category']);
			}

			$query['q']	=	str_replace('-', ' ', $content['job_category']);
		}
		if( isset($content['s']) && $content['s'] != '' )  $query['q']	=	 $content['s'];
		if( isset($content['location']) && $content['location'] != '' )  $query['l']	=	 $content['location'];

		//$query =	wp_parse_args( $query, $settings );

		$jobs	=	$this->indeed->get_posts ($query);
		$jobs['query']	=	$query;
		//}
		wp_send_json( $jobs );
	}

	function add_script() {
		// homepage & single job & post job
		if ( is_home() || is_search() || is_post_type_archive('job') || is_tax('job_category') || is_tax('job_type') || apply_filters( 'je_is_index_enqueue_script', false ) ){
			wp_enqueue_script('indeed' , plugin_dir_url( __FILE__).'/js/indeed.js' , array ('job_engine'), JE_INDEED_VER ) ;
			wp_localize_script ('indeed' , 'indeed',  array ('post_per_page' => get_option('posts_per_page') ) ) ;
		}

	}

	function add_css () {
		//echo $_SERVER['REMOTE_ADDR'];
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
			.out {
				display: block !important;
			}
		</style>
		<script type="text/javascript" src="//gdc.indeed.com/ads/apiresults.js"></script>

	<?php
	}
	/**
	 * Insert custom js template
	 */
	public function add_indeed_template(){
		?>
		<script type="text/template" id="indeed_template">
			<?php echo $this->frontend_js_template() ?>
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
		$settings		=	$this->indeed->get_settings();
		if( isset( $settings['display_label']) && $settings['display_label'] ) {
				$template = '
			<li class="job-item" >
				<div class="thumb">
					<a onmousedown="{{onmousedown}}" href="{{ url }}" target="_blank" >
						<img src="'.$settings['author'].'"/>
					</a>
				</div>
				<div class="content">
					<a onmousedown="{{onmousedown}}" class="title-link" target="_blank" href="{{url}}""><h6 class="title">{{ jobtitle }}</h6></a>
					<div class="tech font-heading f-right actions">
						<span id=indeed_at><a href="http://www.indeed.com/">jobs</a> by <a
							href="http://www.indeed.com/" title="Job Search"><img
							src="http://www.indeed.com/p/jobsearch.gif" style="border: 0;
							vertical-align: middle;" alt="Indeed job search"></a>
						</span>
					</div>

					<div class="desc f-left-all">
						<div class="cat company_name">
							{{ company }}
						</div>
						<div><span class="icon" data-icon="@""></span><span class="job-location">{{ formattedLocationFull }}</span></div>
					</div>
				</div>
			</li>';
		} else {
			$template = '
			<li class="job-item" >
				<div class="thumb">
					<a onmousedown="{{ onmousedown }}" href="{{ url }}" target="_blank" >
						<img src="'.$settings['author'].'"/>
					</a>
				</div>
				<div class="content">
					<a onmousedown="{{ onmousedown }}" class="title-link" target="_blank" href="{{url}}><h6 class="title">{{jobtitle}}</h6></a>
					<div class="desc f-left-all">
						<div class="cat company_name">
							{{ company }}
						</div>
						<div><span class="icon" data-icon="@""></span><span class="job-location">{{formattedLocationFull}}</span></div>
					</div>
				</div>
			</li>';
		}

		return $template;
	}

	public function mobile_js_template($template = ''){
		$variables = array();
		$template = <<<TEMPLATE
		<li data-icon="false" class="list-item">
			<span class="arrow-right"></span>
			<a onmousedown="{{ onmousedown }}" href="{{url}}" target="_blank" >
				<p class="name">
					{{jobtitle }}
				</p>
				<p class="list-function">
					<span class="postions">{{company}}</span>
					<span class="locations"><span class="icon" data-icon="@"> </span>{{formattedLocationFull}}</span>
				</p>
			</a>
		</li>
TEMPLATE;
		return $template;
	}

	/**
	 * add script in mobile to get post
	*/
	public function add_mobile_script () {

		if ( is_home() || is_search() || is_post_type_archive('job') || is_tax('job_category') || is_tax('job_type') || apply_filters( 'je_is_index_enqueue_script', false ) ){
	?>
		<script type="text/javascript" src="//gdc.indeed.com/ads/apiresults.js"></script>
		<script type="text/template" id="template_mobile_indeed">
			<?php echo $this->mobile_js_template() ?>
		</script>

		<script type="text/javascript">
			(function ($) {
				// je_job_search_last
				$(document).ready(function () {

					var paged	=	1;

					$('.txt_search').on('keyup',function(){
						paged	=	1;
					});

					$('#et_search_cat').tap(function(){
						paged	=	1;
					});

					if( $('.list-item').length <= et_globals.numofpost ) {

						je_indeed_after_job ();
					}

					$(document).bind('je_job_search_last' , function () {

						je_indeed_after_job ();
					});

					function je_indeed_after_job  () {
						$('.ui-page-active').find('#lm_com_job').show();
						$('.ui-page-active').find('#et_loadmore').show();


						$('.no-result').hide();

						var query			=	_.clone(query_default);
						query.action		=	'je_after_job_list'	;
						query.content.paged =	paged;
						/*
						 * Fix mobile paging.
						 *@version 3.6
						**/
						query.paged 		= paged;
						// end 3.6

						$.ajax({
							url : et_globals.ajaxURL,
							type : 'post',
							data : query,
							beforeSend : function(){
								$.mobile.showPageLoadingMsg();
							},
							error : function(request){
								$.mobile.hidePageLoadingMsg();
							},
							success : function(res){
								$.mobile.hidePageLoadingMsg();
								//console.log(response);
								if( typeof res.data.result !== 'undefined' ) {
									_.templateSettings = {
									    evaluate    : /<#([\s\S]+?)#>/g,
										interpolate : /\{\{(.+?)\}\}/g,
										escape      : /<%-([\s\S]+?)%>/g
									};
									var container = $('div.ui-page-active ul.listview'),
										result		=	res.data.result,
										template = _.template($('#template_mobile_indeed').html());

									for ( var i=0 ; i < result.length ; i ++ ) {
										if(typeof result[i].company == 'object')
											result[i].company = '';
										var item	=	template(result[i]);
										//console.log(item);
										container.append( item );
									}
									paged++;
									container.listview('refresh');
								}else {
									$('#et_loadmore').hide();
								}
							}
						});
					}

				});
			})(jQuery)

		</script>
	<?php
		}
	}

}

class JE_ImportView {
	private $indeed;
	function __construct () {

		$this->indeed	=	JE_Indeed::get_instance();

		add_action('et_admin_enqueue_scripts-et-indeed', array($this, 'plugin_scripts'));
		add_action('et_admin_enqueue_styles-et-indeed', array($this, 'plugin_styles'));
		// render admin menu view
		add_action('et_admin_menu', array($this, 'menu_view'));

		add_action('wp_ajax_indeed_update_settings', array($this,'update_indeed_settings') );
	}


	function update_indeed_settings () {
		if(!current_user_can( 'manage_options' ) ) wp_send_json( array('success' => false , 'msg' => __("Permission Denied!", ET_DOMAIN) ) );
		$request	=	array();
		parse_str( $_REQUEST['data'] , $request );

		$this->indeed->update_settings($request);
		wp_send_json( array('sucess' => true ) );
	}

	/**
	 * Register menu setting
	 */
	public function menu_view(){
		// register payment menu item
		et_register_menu_section('import', array(
			'menu_title' 	=> __('JE Indeed', ET_DOMAIN),
			'page_title' 	=> __('JE INDEED', ET_DOMAIN),
			'callback' 		=> array( $this, 'view_callback' ),
			'slug' 			=> 'et-indeed',
			'page_subtitle'	=>	__('Directly load Indeed jobs in your job board', ET_DOMAIN)
			));
	}

	function view_callback ( $args ) {
		$sub_section = empty($_REQUEST['subSection']) ? '' : $_REQUEST['subSection'];
		//extract($search_str);
	?>
	<div id="je_import" >
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->page_title ?></div>
			<div class="desc"><?php echo $args->page_subtitle ?></div>
			<!-- <div class="header-more-info">
				<input title="Publisher ID" type="text" name="publisher_id" id="publisher_id" value="<?php echo $publisher_id ?>" placeholder="<?php _e('Publisher ID') ?>">
				<span class="more-info-help" title="<?php _e('Your Publisher ID on Indeed.com', ET_DOMAIN) ?>">?</span>
			</div> -->
		</div>
		<div class="et-main-content" >
			<div class="et-main-left">
				<ul class="et-menu-content inner-menu">
					<li>
						<a href="#indeed-import" menu-data="import" class="<?php if ( isset($sub_section) && ($sub_section == '' || $sub_section == 'import')) echo 'active'  ?>">
							<span class="icon" data-icon="W"></span><?php _e("Settings",ET_DOMAIN);?>
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
				include dirname(__FILE__) . '/manage.php';
				 ?>
			</div>
		</div>
	</div>
	<?php
	}

	function plugin_scripts () {

		wp_register_script('je_indeed', plugin_dir_url( __FILE__).'/je_indeed.js', array('jquery', 'et_backbone', 'et_underscore'), JE_INDEED_VER );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'et_backbone' );
		wp_enqueue_script( 'et_underscore' );
		wp_enqueue_script( 'job_engine' );
		wp_enqueue_script( 'admin_scripts' );



		wp_enqueue_script( 'je_indeed' );
		//wp_enqueue_script( 'jquery-ui-autocomplete' );

		wp_localize_script('je_indeed', 'je_indeed', array(
			'ajax_url' 		=> admin_url('admin-ajax.php'),
			'paginate_text' => __('Display ', ET_DOMAIN)
			));

	}

	function plugin_styles () {
		wp_enqueue_style( 'admin_styles' );
		wp_enqueue_style('je_import_css', plugin_dir_url( __FILE__).'/je_indeed.css');
	}



}

new JE_ImportView ();
new JE_IndeedFront ();