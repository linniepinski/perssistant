<?php
/*
Plugin Name: JE Jobroll
Plugin URI: www.enginethemes.com
Description: JE Jobroll
Version: 1.2
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/
require_once dirname(__FILE__) . '/update.php';

define ('JOB_ROLL_VERSION', '1.2');

class JE_Jobroll {

	const ROLL_PAGE_NAME  	= 'jobroll';
	const ROLL_OPTION_NAME	= 'page_show_jobroll';
	private $page_jobroll_id;


	function __construct() {

		add_action('et_admin_enqueue_scripts-je-jobroll', array($this, 'plugin_scripts'));
		add_action('et_admin_enqueue_styles-je-jobroll', array($this, 'plugin_styles'));
		add_action('et_admin_menu', array($this, 'register_menu_jobroll'));

		//add_action('init', array($this,'check_jobroll_request') );
		// replace page template
		add_filter('page_template', array($this, 'redirect_jobroll_page'));

		add_action('wp_head', array($this, 'jobroll_style'));

		add_action('et_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_filter('et_localize_scripts', array($this, 'filter_localize_scripts'));

		register_activation_hook(__FILE__, array($this, 'install'));
		register_deactivation_hook(__FILE__, array($this, 'uninstall'));

		add_filter( 'et_get_translate_string', array($this, 'add_translate_string') );

		if(!get_option('je_jobroll_notice_board_hide' , 0 ))
			add_action('admin_notices', array($this, 'admin_notice') );
		add_action( 'wp_ajax_je_hide_jobroll_warning_board', array($this, 'je_hide_jobroll_warning') );

		//action save page template job-roll
		add_action( 'wp_ajax_je_save-page-jobroll', array($this, 'save_page_jobroll') );

	}

	function add_translate_string ($entries) {
		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/default.po',true );

		return	array_merge($entries, $pot->entries);
	}
	/**
	 * add menu jobroll in backend
	*/
	function register_menu_jobroll () {
		// register jobroll menu item
		et_register_menu_section('je_jobroll_board', array(
			'menu_title' 	=> __('JE Jobroll', ET_DOMAIN),
			'page_title' 	=> __('JE JOBROLL', ET_DOMAIN),
			'callback' 		=> array( $this, 'et_jobroll_callback' ),
			'slug'          => 'je-jobroll',
			'page_subtitle'	=>	__('Create a jobroll for publisher.', ET_DOMAIN)
		));
	}
	/**
	 * menu section callback function to render html
	*/
	function et_jobroll_callback ($args) {
	?>
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->page_title ?></div>
			<div class="desc"><?php echo $args->page_subtitle ?></div>
		</div>
		<div class="et-main-content" >

				<?php require_once 'backend-jobroll.php'; ?>

		</div>
	<?php
	}
	function plugin_scripts () {
		// wp_deregister_script( 'jquery' ); // deregisters the default WordPress jQuery & enqueue the new one later
		// wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'et_underscore' );
		wp_enqueue_script( 'et_backbone' );
		wp_enqueue_script( 'job_engine' );
		wp_enqueue_script( 'admin_scripts');

		wp_enqueue_script( 'je.iris', plugin_dir_url(__FILE__).'js/colorpicker.js');
		wp_enqueue_script( 'je.jobroll', plugin_dir_url(__FILE__).'js/jobroll.js',  array( 'jquery','et_underscore','et_backbone',  'job_engine' , 'jquery-ui-sortable'), JOB_ROLL_VERSION );

		$id 	= self::get_page_jobroll();
		$url 	= get_permalink($id);

		$url 	= add_query_arg(array('jobroll_request'=>1), $url);
		wp_localize_script( 'je.jobroll','je_jobroll', array('link'	=> $url ) ) ;

	}

	function plugin_styles () {
		wp_enqueue_style('admin_styles');
		wp_enqueue_style( 'backend_jobroll_style', plugin_dir_url( __FILE__).'/backend-jobroll.css');
	}

	function je_hide_jobroll_warning () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		echo update_option('je_jobroll_notice_board_hide' , 1 );
		exit;
	}

	/**
	 * save page job roll
	 * @return [type] [description]
	 */
	static function get_page_jobroll(){
		$default  = get_posts(array(
				'name' 			=> self::ROLL_PAGE_NAME,
				'post_type' 	=> 'page',
				'numberposts' 	=> 1
			));

		$id_default = isset($default[0]->ID) ? $default[0]->ID : '';

		return get_option(self::ROLL_OPTION_NAME,$id_default);
	}

	function set_page_jobroll($id){
		update_option(self::ROLL_OPTION_NAME,$id);
	}
	function save_page_jobroll(){

		if(isset($_REQUEST['id'])){
			$this->set_page_jobroll($_REQUEST['id']);
		}

		$id = self::get_page_jobroll();
		$_REQUEST['height']	=	$_REQUEST['number'] * 50 + 110;

		extract($_REQUEST);

		$width					=	$width + 50;
		$url 					= get_page_link($id);
		$_REQUEST['url_front'] 	= $url;

		$url 	= add_query_arg(array('jobroll_request'=>1,'number'=>$number,'height' =>$height, 'title' => $title, 'width' => $width,'color'=> $color ), $url);

		if(!empty($categories))
			$url = add_query_arg(array('categories'=> $categories), $url);
		if(!empty($job_types))
			$url = add_query_arg(array('job_types'=> $job_types), $url);

		$_REQUEST['url'] 		= $url;

		wp_send_json(array('success'=>true,'msg' => __('Save successfull',ET_DOMAIN), 'data' => $_REQUEST ));


		//wp_send_json(array('success' => false, 'msg' => __('Save false.',ET_DOMAIN) ));
	}

	// insert needed page when activate plugin
	function install(){

		$pages = get_posts(array(
			'name' => self::ROLL_PAGE_NAME,
			'post_type' => 'page',
			'numberposts' => 1
		));

		if ( empty($pages) ){
			$this->page_jobroll_id	= wp_insert_post(array(
				'post_title' 		=> __('Create a jobroll', ET_DOMAIN),
				'post_content' 		=> __('Jobroll', ET_DOMAIN),
				'post_name' 	=> self::ROLL_PAGE_NAME,
				'post_type' 	=> 'page',
				'post_status' 	=> 'publish'
			));
		} else {
			$this->page_jobroll_id	=	$pages[0]->ID;
		}

	}

	// remove plugin page template
	function uninstall(){
		$pages = get_posts(array(
			'name' => self::ROLL_PAGE_NAME,
			'post_type' => 'page',
			'numberposts' => 1
		));
		foreach ($pages as $page) {
			wp_delete_post($page->ID, true);
			break;
		}
	}

	public function admin_notice () {
		if(!get_option('je_jobroll_notice_board_hide' , 0 )) {
			$pages = get_posts(array(
				'name' => self::ROLL_PAGE_NAME,
				'post_type' => 'page',
				'numberposts' => 1
			));
			$id = self::get_page_jobroll();
			echo '<div class="updated">'
					.sprintf(__("You have just activated the JE Jobroll plugin. Go to <a href='%s' >this page</a> to create a jobroll to display your jobs in other websites.", ET_DOMAIN), 
							get_page_link($id ) ).'<a href="#" class="je_dismiss" title="Dismiss" > Dismiss</a></div>';
			?>
			<script type="text/javascript">
				(function ($) {
					$(document).ready(function() {
						$('.je_dismiss').click (function () {
							$(this).parents('.updated').hide ();
							$.ajax ({
								type : 'post',
								url : 'admin-ajax.php',
								data : {action : 'je_hide_jobroll_warning_board'},
								beforeSend : function () {},
								success: function (res) {}
							});
						});
					});
				})(jQuery);
			</script>
			<?php
		}

	}

	/**
	 * Automatically redirect jobroll page
	 * @since 1.0
	 */
	function redirect_jobroll_page( $page_template ){
		$id = self::get_page_jobroll();
		if ( is_page($id) ){
			 $page_template = dirname(__FILE__). '/jobroll.php';
		}

		return $page_template;
	}

	function jobroll_style () {
		wp_enqueue_style( 'jobroll_style', plugin_dir_url( __FILE__).'/jobroll.css', array(), JOB_ROLL_VERSION);
	}

	function enqueue_scripts () {
		$name = self::get_page_jobroll();
		if(is_page($name )) {
			wp_enqueue_script( 'je.iris', plugin_dir_url(__FILE__).'js/colorpicker.js');
			wp_enqueue_script( 'je.jobroll', plugin_dir_url(__FILE__).'js/jobroll.js',  array( 'backbone', 'underscore', 'job_engine' ,'jquery_validator' , 'jquery-ui-sortable', 'front'),JOB_ROLL_VERSION );
		}
	}

	function filter_localize_scripts ($localize) {
		$id = self::get_page_jobroll();
		$url = get_permalink($id);
		$url = add_query_arg(array('jobroll_request' => 1 ), $url);
		return array_merge($localize,
							array('je.jobroll' => array(
										'object_name'	=> 'je_jobroll',
										'data'			=> array(
										'link'			=> $url											)
										)
									)
							);
	}

}

class JE_Jobroll_Ajax extends JE_Jobroll {
	function __construct() {
		parent::__construct();
	}

}

new JE_Jobroll_Ajax ();

/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class JeJobroll_Publisher_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function JeJobroll_Publisher_Widget() {
        $widget_ops = array( 'classname' => 'jobroll_publisher', 'description' => 'Publisher' );
        $this->WP_Widget( 'jobroll_publisher', 'Jobroll Publisher', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {
    	$id = JE_Jobroll::get_page_jobroll();
    	
        extract( $args, EXTR_SKIP );
        echo $before_widget;
        echo $before_title;
        echo $instance['title']; // Can set this with a widget option, or omit altogether
        echo $after_title;
        echo '<p>';
        _e(" Add jobs to your site! ", ET_DOMAIN);
        $url 	= get_permalink($id);
       
        ?>
		<a href="<?php echo $url; ?>" ><?php _e("Click here", ET_DOMAIN); ?></a>
        <?php
        echo '</p>';
    //
    // Widget display logic goes here
    //

    echo $after_widget;
    }

    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    function update( $new_instance, $old_instance ) {
    
        // update logic goes here
        $updated_instance = $new_instance;
        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => 'Publisher' ) );
        extract($instance);
        ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
        <?php 
        // display field names here using:
        // $this->get_field_id( 'option_name' ) - the CSS ID
        // $this->get_field_name( 'option_name' ) - the HTML name
        // $instance['option_name'] - the option value
    }
}

add_action( 'widgets_init', 'je_jobroll_register_widget' );
function je_jobroll_register_widget () {
	register_widget( 'JeJobroll_Publisher_Widget' );
}