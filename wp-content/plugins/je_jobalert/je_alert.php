<?php
/*
Plugin Name: JE Alert
Plugin URI: www.enginethemes.com
Description: Allow job seekers to subscribe for new jobs notification email.
Version: 2.2
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/
define('JEALERT_VERSION', "2.2");
require dirname(__FILE__).'/widgets.php';
require_once dirname(__FILE__) . '/update.php';

class JE_ALERT {

	function __construct(){
		/**
		 * print styles and scripts
		*/
		add_action('et_admin_enqueue_scripts-je-alert', array($this, 'plugin_scripts'));
		add_action('et_admin_enqueue_styles-je-alert', array($this, 'plugin_styles'));
		//add_action('wp_print_styles', array($this, 'plugin_frontend_styles'));

		add_action('et_admin_menu', array($this, 'register_menu'));
		/**
		 * register widget 
		*/
		add_action('widgets_init', array($this, 'register_widgets'));
		/**
		 * active plugin hook
		*/
		register_activation_hook(__FILE__, array($this,'activation'));
		register_deactivation_hook(__FILE__, array($this,'deactivation'));
		add_action('wp', array($this,'refesh_schedule_activation') );
		add_action('admin_init', array($this,'refesh_schedule_activation') );
		/**
		 * mail schedule
		*/
		add_action('et_mail_schedule_event', array($this, 'mail_schedule') );

		//single schedule event 
		// this action call for loop after main schedule run and have to turn off if main schedule finish.
	    add_action ('je_alert_mail_event', array($this, 'je_alert_mail_event'));


		add_action ('init', array($this, 'register_post_type_subscriber'));
		/**
		 * ajax action add new subscriber
		*/
		add_action ('wp_ajax_je-add-subscriber', array($this, 'je_add_subscriber'));
		add_action ('wp_ajax_nopriv_je-add-subscriber', array($this, 'je_add_subscriber'));

		/**
		 * ajax action remove subscriber
		*/
		add_action ('wp_ajax_je-remove-subscriber', array($this, 'je_remove_subscriber'));
		add_action ('wp_ajax_nopriv_je-remove-subscriber', array($this, 'je_remove_subscriber'));

		/**
		 * ajax action save job alert setting
		*/
		add_action('wp_ajax_update-job-alert-setting', array($this, 'update_job_alert_setting'));
		add_action('wp_ajax_subscriber-change-page', array($this, 'subscriber_change_page'));
		/**
		 * print styles and script in front-end when widget add subscriber is active
		*/
		if ( is_active_widget( false, false, 'je_alert', true ) ) {
			add_action('wp_head', array($this, 'je_alert_front_end_css')) ;
			add_action('wp_head', array($this, 'plugin_frontend_styles')) ;
			add_action('wp_footer', array($this, 'je_alert_front_end_js')) ;
			
		}
		// publish a job
		add_action ('transition_post_status', array($this, 'je_save_job') , 10 , 3);
		add_action ('transition_post_status',  array($this, 'je_save_job') , 10 , 3 );

		add_filter('cron_schedules', array($this, 'cron_add_interval'), 1000990);

		add_filter( 'et_get_translate_string', array( $this, 'add_translate_string') );

		add_filter( 'page_template', array($this, 'page_template_redirect') );

		// ajax save alert message
		add_action ('wp_ajax_et_mail_alert_message', array($this, 'save_alert_message'));
		//reset mail tempalte
		add_action ('wp_ajax_et_reset_mail_alert', array($this, 'reset_mail'));
		
	}

	function page_template_redirect($page_template){
		if(is_page("unsubscribe")){
			$page_template = dirname(__FILE__). '/page-unsubscribe.php';
		}	
		return $page_template;
	}

	function add_translate_string ($entries) {
		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/default.po',true );
		return	array_merge($entries, $pot->entries);
	}

	/**
	 * add custome interval for mail schedule
	*/
	function cron_add_interval( $schedules ) {
	 	// Adds once weekly to the existing schedules.
	 	$schedules['custom_mail_recurrence'] = array(
	 		'interval' =>  10 ,
	 		'display' => __( 'Custom Mail Schedule' )
	 	);
	 	$schedules['weekly'] = array(
	 		'interval' =>  7*3600*24 ,
	 		'display' => __( 'Custom Mail Schedule Weekly' )
	 	);
	 	
	 	return $schedules;
	}
	/**
	 * update subscriber group when have a new job published
	*/
	function je_save_job ( $new_status, $old_status, $post  ) {
		// return if post type is not job
		$job	=	$post;
		$job_id	=	$post->ID;
		if( $job->post_type != 'job') return ;

		//$job	=	get_post($job_id);
		if($new_status == $old_status) return ;
		if($new_status != 'publish') return;


		$location		=	et_get_post_field($job_id, 'location');
		
		$location_array =	explode(',', $location );
		array_push($location_array, 'Anywhere');
		$term			=	wp_get_post_terms( $job_id, 'job_category' );
		if ($term) {
			$slug = $term[0]->slug;
		} else {
			$slug = "";
		}
		foreach ($location_array as $key => $value) {
			$subcriber	=	new WP_Query (array(
				'post_type' 	=> 'subscriber',
				's'				=> $value,
				'posts_per_page'	=> -1,
				'meta_query' => array(
       					array(
							'key' => 'job_category',
							'value' => $slug,
							'compare' => 'LIKE',
						) 
					)
				
				)
			);
			while ($subcriber->have_posts()) { 
				$subcriber->the_post();
				update_post_meta( get_the_ID(), 'je_have_new_job', 1 );
			}
		
		}
		wp_reset_query();	
	}

	function je_alert_front_end_js () {
		
		wp_enqueue_script('je_alert_chosen_js', plugin_dir_url( __FILE__).'front-end/chosen.jquery.js', array('jquery')) ;
		wp_enqueue_script('je_alert_frontend_js', plugin_dir_url( __FILE__).'front-end/je_alert.js', array('jquery')) ;
		
	}

	function je_alert_front_end_css () {
	?>
		<style type="text/css">
			.widget-job-alert {
				padding: 20px;
			}
			.widget-job-alert .form-item {
			    border-bottom: 0;
			    font-size: 1em;
			    padding: 0;
			}
			.widget-job-alert .select-style,
			.widget-job-alert .btn-subscribe {
				width: 200px;
				height: 36px;
				margin: 0;
			}            
			.widget-job-alert .select-style { margin-bottom: 20px; }

			.widget-job-alert input {
				padding: 0 10px;
				width: 178px !important;
				height: 36px !important;
				line-height: 1;
				margin-bottom: 20px
			}
			.widget-job-alert .btn-subscribe {
				text-align: left;
				position: relative;
				padding: 0 10px;
				font-weight: bold;
				border: 0;
			}
			.widget-job-alert .btn-subscribe span {
			    line-height: 31px;
			    position: absolute;
			    right: 10px;
			    top: 0;
			}
			.widget-job-alert .form-item.error div {
				background: none;
				padding: 3px;
				margin: -20px 0px 5px;
				
				font-style: italic;
				font-size: 0.9em;
				font-weight: normal;
			}
		.widget-job-alert form .margin0{
	          margin-left:0px;			
			  display:block;
			  margin-bottom:15px;
		   	 border-radius: 3px;
		    	box-shadow: 0 1px 3px #E7E7E7 inset;
			
           }
           #container .margin0.btn-background{
	              border:none;
                 display: block;
           }
           .widget-job-alert form .form-item.margintop{
				margin-top:15px;
           }
			.widget-job-alert .form-item.error { color: #c67272; }
			.widget-job-alert .form-item.error input { border-color: #c67272; }
			.widget-job-alert .form-item input:invalid { background: #fff; }
			
		</style>
	<?php 
	}

	function subscriber_change_page () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		$data	=	$_REQUEST['content'];

		$query	= new WP_Query(array(
								'post_type' 	=> 'subscriber',
								'posts_per_page'=> 10,
								'paged' 		=> $data['page'],
								'post_status'	=> 'publish',
								'meta_key' 		=> 'et_subscriber_email',
		    					'meta_compare' 	=> '!=',
		    					'meta_value' 	=> ''
							));

 		foreach ($query->posts as $subscriber) {
 			$job_category	=	get_post_meta($subscriber->ID , 'job_category', true);
 			$email=get_post_meta($subscriber->ID , 'et_subscriber_email', true);
 			if(!$email) continue; 
 			if($job_category=='null') $job_category=' ';
 			$subscribers[] = array(
 				'ID'			=> $subscriber->ID,
 				'title' 		=> get_post_meta($subscriber->ID , 'et_subscriber_email', true),
 				'job_category'	=> isset($job_category) ? $job_category :'',
 				'location'	    => get_post_meta($subscriber->ID , 'job_location', true)
 			);
 		}
 		if ($query->post_count > 0)
 			$resp = array(
 				'success' 	=> true,
 				'msg' 		=> '',
 				'data' 		=> array(
 					'page' => $data['page'],
 					'pages_max' => $query->max_num_pages,
 					'subscribers' => $subscribers
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

	function update_job_alert_setting() {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );		
		
		unset($_REQUEST['action']);
		$this->set_job_alert_setting($_REQUEST);
		$this->activation();

		echo json_encode(array('success' => true));
		exit;
	}
	/**
	 *	set job alert setting
	 */
	function set_job_alert_setting ($arg) {
		$default	=	array(
			'mail' 		=> 100,
			'recurring'	=> 'daily',
			'job'		=> 5
		);
		$arg	=	wp_parse_args( $arg, $default );
		update_option('je_job_alert_setting', $arg);
	}
	/**
	 *	get job alert setting
	 */
	function get_job_alert_setting () {
		$default	=	array(
			'mail' 		=> 100,
			'recurring'	=> 'daily',
			'job'		=> 5
		);
		return get_option( 'je_job_alert_setting', $default);
	}
	/**
	 * ajax callback function: remove subscriber mail
	*/
	function je_remove_subscriber () {
		$mail		=	$_REQUEST['email'];
		$code		=	$_REQUEST['code'];
		$result = array();
		$args = array(
			'post_type' => 'subscriber',
			'posts_per_page' => 1,
			'meta_query' => array(
				array(
					'key' => 'et_subscriber_email',
					'value' => $mail,
					'compare' => 'LIKE'
				),
				array(
					'key' => 'je_subscriber_code_unsubscribe',
					'value' => $code,
					'compare' => 'LIKE'
				)
			)
		);
		$subscriber = get_posts($args);
		if($subscriber){
			wp_delete_post( $subscriber[0]->ID , true );
			delete_post_meta($subscriber[0]->ID, 'et_subscriber_email');
			delete_post_meta($subscriber[0]->ID, 'job_category');
			delete_post_meta($subscriber[0]->ID, 'job_location');
			$result = array(
				'success' => true,
				'msg' => __("Unsubscribed successfully! You won't receive new job alerts in your inbox anymore.", ET_DOMAIN)
			);
		} else {
			$result = array(
				'success' => false,
				'msg' => __("Unsubscribed failed! The email you have just entered is not found.",ET_DOMAIN)
			);
		}
		wp_send_json($result);
	}	
	/**
	 * ajax callback function: add subscriber mail
	*/
	function je_add_subscriber () {

		$mail		=	$_REQUEST['email'];
		$location	=	$_REQUEST['location'];
		$category	=	$_REQUEST['job_category'];
		
        if($category =='' || $category =='null'){
        		$cats=get_categories($arr =array('taxonomy'=>'job_category','hide_empty'=>0));
        		$job_cat=array();
        	    if($cats){
        	    	foreach ($cats as $item){
        	    		$job_cat[] =$item->slug;
        	    	}
        	    }
        	$category =implode(',', $job_cat);
        }
        
		if( !is_email( $mail ) )  { // check email input is valid

			$resp	=	array(
					'success' => false,
					'msg' => __('Your email address is invalid!', ET_DOMAIN)
				);

			echo json_encode($resp);
			exit;
		}
		
		$id	=$this->add_subscriber ( array('mail' => $mail, 'location' => $location , 'cat' => $category ));
		if($id) {
				
				if($id['id']==0){
						$resp	=	array(
								'success' => false,
								'msg'	  => __("This email is already subscribed.", ET_DOMAIN),
								
						);
				}else{
					$resp	=	array(
						'success' => true,
						'msg'	  => __("You have successfully subscribed to receive job alerts.", ET_DOMAIN),
						'data'		=> $id
					);
				}
			} else {
				$resp	=	array(
					'success' => false,
					'msg'	  => __("Error.", ET_DOMAIN)
				);
			}
			
		wp_send_json($resp);

	}
	/**
	 * add subscriber and set subscriber group
	*/
	function add_subscriber ( $args ) {
		//$term	=	wp_insert_term($args['cat'], 'subscriber_category');
		if($args['location'] == '') {
			$args['location']	=	'Anywhere';
		}
		$subscriber	=	new WP_Query (array( 
				'post_type' 	=> 'subscriber',
				//'s'				=> trim($args['location']),
				'posts_per_page'	=> 1,
				'meta_query' => array(
					array(
						'key' => 'et_subscriber_email',
						'value' => $args['mail'],
						'compare' => '='
					)),
				'post_status'	=> 'publish',
			)
		);
		


		if($subscriber->have_posts()) {
			  $subscriber->the_post();
			  $subscriberid=get_the_ID();
			   $cats=get_post_meta($subscriberid,'job_category',true);
			   $location =get_post_meta($subscriberid,'job_location',true);
			   if(trim($args['cat']) == $cats && $location==$args['location']){
			   	     return array('id'=>0);
			     }
                 $my_post = array(
					      'ID'           =>$subscriberid ,
					      'post_title' => $args['location']
					  );
                 wp_update_post( $my_post );
			     update_post_meta( $subscriberid, 'job_category', $args['cat']);

			     update_post_meta( $subscriberid, 'job_location', $args['location']);
			     return array('id'=>get_the_ID());
				

		} else {
			
			$id	=	wp_insert_post( array ('post_type' => 'subscriber', 'post_title' => $args['location'],'post_content' =>$args['mail']+ ' ' +$args['location']  ,'post_status'	=> 'publish'));
			$unsubscribe_key = wp_generate_password( 20, false );
			if(!is_wp_error( $id ) ) {
				update_post_meta( $id, 'job_location', $args['location']);
				update_post_meta( $id, 'job_category', $args['cat']);
				update_post_meta( $id, 'je_subscriber_code_unsubscribe', $unsubscribe_key);
				// wp set post term
				update_post_meta( $id, 'et_subscriber_email', $args['mail'] );
				return  array('id' => $id);
			}
			
		}
		return false;
	}

	function register_post_type_subscriber () {
		register_post_type('subscriber', array(
			'labels' => array(
					'name' => _x('Subscriber', 'post type general name',ET_DOMAIN),
					'singular_name' => _x('Subscriber', 'post type singular name', ET_DOMAIN),
					'add_new' => _x('Add New', 'application', ET_DOMAIN),
					'add_new_item' => __('Add New subscriber', ET_DOMAIN),
					'edit_item' => __('Edit subscriber', ET_DOMAIN),
					'new_item' => __('New subscriber', ET_DOMAIN),
					'all_items' => __('All subscriber', ET_DOMAIN),
					'view_item' => __('View subscriber', ET_DOMAIN),
					'search_items' => __('Search subscriber', ET_DOMAIN),
					'not_found' =>  __('No subscriber found', ET_DOMAIN),
					'not_found_in_trash' => __('No subscriber found in Trash', ET_DOMAIN), 
					'parent_item_colon' => '',
					'menu_name' => 'subscriber'
				),
				'public' => false,
				'publicly_queryable' => true,
				'show_ui' => true, 
				'show_in_menu' => true, 
				'query_var' => true,
				'rewrite' => true,
				'capability_type' => 'post',
				'has_archive' => true, 
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title', 'editor', 'author','custom-fields' )
			)
		) ;

		
	}
	/**
	 * function send mail alert
	 * @param $number_of_mail : number of email can be send
	 * @param $number_job     : number of job need send
	*/
	function mail_alert ( $number_of_mail, $number_job) {		
		$send_mail	=array(
				'post_type'	=> 'subscriber',
				'meta_key' => 'je_have_new_job',
				'meta_value'	=>1,
				'orderby' =>  'meta_value',
				'post_status'	=> 'publish',
				'posts_per_page' => $number_of_mail
			);
		$mails=get_posts($send_mail);
		$unsubscribe_link = '';			
		/**
		 * query all subcriber mail
		*/
		if($mails){
	         	foreach ($mails as $key) {

				$subscriber_id	=	$key->ID;
				$cats		=	get_post_meta( $subscriber_id, 'job_category', true);
				// retrieve mail message
				$message	=	$this->get_alert_message ($cats,$number_job);
				$subject	=	sprintf(__('New jobs alert from %s', ET_DOMAIN), get_option('blogname') );
				if($message != ''){
						$code = get_post_meta( $subscriber_id , 'je_subscriber_code_unsubscribe', true);
						if(!$code) {
							$code	=	wp_generate_password(20);
							update_post_meta( $subscriber_id, 'je_subscriber_code_unsubscribe', $code );
						}
						
						$email =get_post_meta($subscriber_id,'et_subscriber_email',true);
						$url = et_get_page_link("unsubscribe", array('email' => $email, 'code' => $code));
						$new_message = str_replace("[unsubscribe_link]", $url , $message);
						// echo $new_message;
						if($email){
						   wp_mail( $email, $subject , $new_message );
						}
				}
				// set group is out of new job
				update_post_meta( $subscriber_id, 'je_have_new_job', 0);
				
	 	    }

	  	} else {	  		
	  		// have turn off custom_mail_recurrence event.
	  		//je_log('do not any email, turn off je_alert_loop'); 
	  		wp_clear_scheduled_hook('je_alert_mail_event');
	  	}
		$number_of_mail	= $number_of_mail - count($mails);
		//delete_option('je_schedule');
		$schdule_list	=	get_option( 'je_schedule', array() );
		array_push($schdule_list, array(time(), $number_of_mail));
		update_option ('je_schedule', $schdule_list );
		/**
		 * update job cat and number of mail for next schedule
		*/
		update_option ('je_alert_recurren_number_mail', $number_of_mail);
		//update_option ('je_alert_recurren_job_category', $job_categories);
		
	}

	/*
	* call this function after main schedule run. when sent all email finish, this action have to turn off immediately
	* have to turn off if don't any email.
	*/

	function je_alert_mail_event () {		
		$subscriber_setting	=	$this->get_job_alert_setting();
		$number_of_mail	=	get_option( 'je_alert_recurren_number_mail',$subscriber_setting['mail'] );

		$number_job=$subscriber_setting['job'];
		//$job_categories	=	get_option ( 'je_alert_recurren_job_category', array() );
	    $this->mail_alert ($number_of_mail, $number_job);
	    
	}

	/**
	* This function run when main schdule run:
	* This function only run once for send mail and it trigger custom_mail_recurrence loop.
	*/
       
	function mail_schedule () {		
		$subscriber_setting	=	$this->get_job_alert_setting();
		$number_of_mail		=	$subscriber_setting['mail'];
		$this->mail_alert ($number_of_mail, $subscriber_setting['job'] );
		wp_schedule_event( time()+ 10 , 'custom_mail_recurrence' , 'je_alert_mail_event');
	}

	/**
	 *	check schedule exist or not
	*/
	function refesh_schedule_activation () {
		if(!wp_next_scheduled('et_mail_schedule_event')) {
			$this->activation();
		}
		$number_of_mail	=	get_option( 'je_alert_recurren_number_mail', 10 );
		
		//$job_categories	=	get_option ( 'je_alert_recurren_job_category', array() );
		if( $number_of_mail <=0) {
			wp_clear_scheduled_hook('je_alert_mail_event');
		}

	}

	function activation () {

		$setting	=	$this->get_job_alert_setting ();
		$time_stamp	=	date('d M y 00:00:00', time() + 3600*24);
		$time_stamp	=	strtotime( $time_stamp );		
		/**
		 * clear 2 schedule to schedule new
		*/
		wp_clear_scheduled_hook('et_mail_schedule_event');
		wp_clear_scheduled_hook('je_alert_mail_event');
		wp_schedule_event( $time_stamp, $setting['recurring'], 'et_mail_schedule_event');	
		
	
	}

	function deactivation () {
		wp_clear_scheduled_hook('et_mail_schedule_event');
	}
	public function plugin_scripts(){
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'et_underscore' );
		wp_enqueue_script( 'et_backbone' );
		wp_enqueue_script( 'job_engine' );
		wp_enqueue_script( 'admin_scripts');

		wp_enqueue_script('je_alert_js', plugin_dir_url( __FILE__).'/je_alert.js', array('jquery', 'et_underscore', 'et_backbone', 'job_engine')) ;
		wp_enqueue_script('je_alert_mail_js', plugin_dir_url( __FILE__).'/options-mails.js', array('jquery', 'et_underscore', 'et_backbone', 'job_engine')) ;
		if(is_page_template('page-unsubscribe.php')){
			wp_enqueue_script( 'jquery' );
			wp_dequeue_script( 'front' );
		}
	}

	public function plugin_styles(){
		wp_enqueue_style('admin_styles');
		wp_enqueue_style('je_alert_css', plugin_dir_url( __FILE__).'/je_alert.css');
	}

	public function plugin_frontend_styles(){
		wp_enqueue_style('css-chosen', plugin_dir_url( __FILE__).'css/chosen.css');
	}

	/**
	 * Register menu setting
	 */
	public function register_menu(){
		// register payment menu item
		et_register_menu_section('je_job_alert', array(
			'menu_title' 	=> __('JE Alert', ET_DOMAIN),
			'page_title' 	=> __('JE ALERT', ET_DOMAIN),
			'callback' 		=> array( $this, 'et_menu_callback' ),
			'slug'			=> 'je-alert',
			'page_subtitle'	=>	__('JE ALERT', ET_DOMAIN)
		));
	}

	function et_menu_callback ($args) {
		$setting	=	$this->get_job_alert_setting ();
		extract($setting);
		$time	=	array (
				'daily'		=> __( 'Daily', ET_DOMAIN),
				'weekly' 	=> __( 'Weekly', ET_DOMAIN)
			);
		?>
		<div id="je_alert">
			<div class="et-main-header">
				<div class="title font-quicksand"><?php _e("JE ALERT", ET_DOMAIN); ?> </div>
				<div class="desc"><?php _e("Set how frequent your subscribers will receive new job's alert in their inbox.", ET_DOMAIN); ?></div>
			</div>
			<div class="et-main-content">
				<div class="et-main-left">
					<ul class="et-menu-content">
						<li><a href="#je_alert_setting" class="active" ><span class="icon" data-icon="y"></span><?php _e("Settings", ET_DOMAIN); ?></a></li>
						<li><a href="#subscriber_list" ><span class="icon" data-icon="f"></span><?php _e("Subscribers", ET_DOMAIN); ?></a></li>
					    <li>
						<a href="#setting-mails" class="section-link">
							<span class="icon" data-icon="M"></span><?php _e("Mail Template",ET_DOMAIN);?>
						</a>
					</li>
					</ul>
				</div>
				<div class="et-main-main clearfix" id="je_alert_setting">
	    			<div class="module">
	        			<div class="title font-quicksand"><?php _e("Mailing settings", ET_DOMAIN); ?></div>
	        			<div class="desc no-left">
	        				<?php _e("Set up your Job Alerts", ET_DOMAIN); ?>
	        			</div>
	        		</div>

	        		<div class="module job-alert-form">
	        			<form action="#" method="post" id="job_alert_setting">
	        				<div class="form-item">
	        					<label><?php _e("Recurrence", ET_DOMAIN); ?><br/><span><?php _e("Choose frequency", ET_DOMAIN); ?></span></label>
	        					<div class="select-category select-style et-button-select">
	        						<select name="recurring">
	        						<?php foreach ($time as $key => $value) { ?>
	        							<option value="<?php echo $key ?>" <?php if($key == $recurring) echo "selected='selected'" ?> >
	        								<?php echo $value ?>
	        							</option>
	        						<?php } ?>
	        						</select>
	        					</div>
	        				</div>
	        				<div class="form-item">
	        					<label><?php _e("Next ", ET_DOMAIN); ?><br/><span><?php _e("The next time schedule run", ET_DOMAIN); ?></span></label>
	        					
	        						<?php echo gmdate ('Y-m-d H:i:s \G\M\T 0', wp_next_scheduled('et_mail_schedule_event')); ?>
	        						
	        					
	        				</div>
	        				
	        				<div class="form-item">
	        					<label><?php _e("Job Limit", ET_DOMAIN); ?> <br/><span><?php _e("Number of jobs per email", ET_DOMAIN); ?></span></label>
	        					<input type="text" name="job" value="<?php echo $job ?>"  class="required number" />
	        					<span class=""><?php _e("Jobs", ET_DOMAIN); ?></span>
	        				</div>
	        				<div class="form-item">
	        					<label><?php _e("Batch Email", ET_DOMAIN); ?> <br/><span><?php _e("Number of emails to send per batch", ET_DOMAIN); ?></span></label>
	        					<input type="text" name="mail" value="<?php echo $mail ?>" class="required number" />
	        					<span class=""><?php _e("Emails", ET_DOMAIN); ?></span>
	        				</div>	
	        				<input type="hidden" name="action" value="update-job-alert-setting" /> 	        				
	        			</form>
	        		</div>

	        		<div class="module form-schedule job-notice">
	        			<p>Alert emails are sent in batches (Batch Email) per your selected frequency (Recurrence). 
	        			For each batch, you can send a maximum of 100 emails. If the number of emails to be sent 
	        			at a given time exceeds the batch size you set, the remaining emails will be included in the next batch.	
	        			</p>
	        			<p>
	        			Each email contains the number of jobs defined in the "Job Limit" and only "published" jobs that match 
	        			the subscriber's alert criteria are included.
	        		</p>
	        		<p>
						The "Batch Email" is limited to 100 but some web hosts allow fewer than this value. 
						To avoid being blacklisted, you should contact your web host to know the right limit.
	        		</p>
	        		</div>

	        		<button class="et-button btn-button" id="save_setting">
	    				<?php _e("Save your setting", ET_DOMAIN); ?>
					</button>	        

	    		</div>
				<div class="et-main-main clearfix" id="subscriber_list" style="display:none;">
					<div class="module">
		    			<div class="title font-quicksand"><?php _e("Subscriber", ET_DOMAIN); ?></div>
		    			<div class="desc no-left">
		    				<?php _e("List of all subsribers", ET_DOMAIN); ?>
		    			</div>
		    		</div>
		    		<?php 
		    			$args	=	array(
				    					'post_type'		=> 'subscriber',
				    					'post_status'	=>	'publish',
				    					'posts_per_page'=> 10,
				    					'meta_key' 		=> 'et_subscriber_email',
				    					'meta_compare' 	=> '!=',
				    					'meta_value' 	=> ''
				    				);
		    			$subscribers	=	new WP_Query ($args);
		    		?>
		    		<div class="module job-alert-subscribe">
		    			<table class="list-job-alert">
		    				<tr>
		    					<th><?php _e('Email', ET_DOMAIN) ?></th>
		    					<th><?php _e('Job Category', ET_DOMAIN) ?></th>
		    					<th><?php _e('Location', ET_DOMAIN) ?></th>
							</tr>
							<?php while ($subscribers->have_posts()) { 
								$subscribers->the_post(); 
								$subscriber_id	=	get_the_ID();
								 $email =get_post_meta($subscriber_id,'et_subscriber_email',true);
								if($email){
								$job_categories	=	get_post_meta( $subscriber_id , 'job_category',true);
								$job_categories=explode(',', $job_categories);
								$list_cat=array();
								if($job_categories){
									  
									 foreach ($job_categories as $key){
									 	if(!$key) continue;
									 	 $job_category	=	get_term_by('slug', $key, 'job_category'  );
									 	 $list_cat[]=$job_category->name;
									 }
								}
 								
							?>
			    				<tr>
			    					<td><?php echo get_post_meta($subscriber_id,'et_subscriber_email',true); ?></td>
			    					<td><?php echo implode(', ',$list_cat); ?></td>
			    					<td><?php echo get_post_meta( $subscriber_id, 'job_location', true ); ?></td>
			    				</tr>
		    				<?php } 
		    			   }
		    				?>
		    			</table>
		    		</div>
		    		<div class="module pagination">
		    			<div class="subscriber-controls">
							<?php if ($subscribers->max_num_pages > 1) { ?>
							<div class="paginate">
								<span>1</span>
								<?php for ($i = 2; $i <= $subscribers->max_num_pages; $i++){ ?>
									<a href="#" class="pi"><?php echo $i ?></a>
								<?php } ?>
							</div>
							<?php } ?>							
						</div>
		    		</div>	 
					
				</div>
				<?php include_once "mail-template.php"; ?>
			</div>
		</div>
		<script id="subscriber_list_template" type="text/template">
			<tr>
				<td>{{title}}</td>
				<td>{{job_category}}</td>
				<td>{{location}}</td>
			</tr>
		</script>
	<?php 

	}

	/**
	 * generate mail message to send 
	 * return string
	*/
	function get_alert_message ($cats,$number_job) {
		$option			=	new ET_GeneralOptions ();
		// $site_logo	=	$option->get_website_logo ();
		$customize		=	$option->get_customization ();

		$job_content	=  '';

		$cats=explode(',', $cats);
		
		$jobs			=	new WP_Query (
										array(  'post_type' 		=> 'job',
											    'post_status' 		=> 'publish' ,
												'posts_per_page'	=>	$number_job,
												'tax_query' 		=> 	array(
													array(
														'taxonomy' 	=> 'job_category',
														'field' 	=> 'slug',
														'terms' 	=>	$cats
													),
												
												)));
		$found_post	=	$jobs->found_posts;
		$i = 0;
		while ( $jobs->have_posts() ) { $jobs->the_post ();	
			global $post;	
			$company		= 	et_create_companies_response( $post->post_author );
			$company_logo	= 	$company['user_logo'];
			
			if($i != 0 ) $job_content	.=	'<tr><td style="padding: 8px 10px 8px 0;">';

			$job_content	.=	'
										<a  style="display: block; padding: 5px; height: 70px; border-radius: 3px; -moz-border-radius: 3px; -webkit-border-radius: 3px; -moz-box-shadow: 0 3px 3px #E9E9E9; -webkit-box-shadow: 0 3px 3px #E9E9E9; box-shadow: 0 3px 3px #E9E9E9; border-bottom:2px solid #E9E9E9;">
										<img width="70" height="70" title="" alt="" src="'.$company_logo['company-logo'][0].'" /></a>
									</td>
									<td valign="top" style="padding: 10px 0;">
										<a href="'.get_permalink().'" style="font-size :15px; ;font-family :'.$customize['font-heading'].';text-decoration: none; display: block; color: #5c5c5c; font-weight: 300; margin-bottom: 10px; text-transform: uppercase;">'.get_the_title( ).'</a>
										<div style="color: #909090;font-family :'.$customize['font-text'].'; font-size :12px;">
											'.get_the_excerpt().'
										</div>'; 
			$i++;
			if($i < $found_post )  $job_content .= '</td></tr>';
			
		}

       	wp_reset_query();
        $alert_message=  get_option('et_mail_alert_message' ,$this->get_alert_message_template());
		$list_jobs 	=	str_ireplace('[list_jobs]',$job_content , $alert_message);
		$list_jobs  =   str_ireplace('[blogname]', get_bloginfo('name'), $list_jobs);
		$list_jobs  =	str_ireplace('[admin_email]', get_option('admin_email'), $list_jobs);
		$list_jobs  =   str_ireplace('[site_url]', get_bloginfo('url'), $list_jobs);
		$list_jobs  =   str_ireplace('[blogdescription]', get_option('blogdescription'), $list_jobs);
		$option		=	new ET_GeneralOptions ();
	    $site_logo	=	$option->get_website_logo ();
		$list_jobs  =   str_ireplace('[site_logo]', $site_logo[0], $list_jobs);
		$list_jobs  =   str_ireplace('[copyright]', $option->get_copyright (), $list_jobs);
		
		$header= '<html class="no-js" lang="en">
		<body style="margin: 0px; font-family:arial,sans-serif; font-size: 13px; line-height: 1.3;">';
		$footer='</body>
				</html>';
		if($job_content != '')	
			return $header.$list_jobs.$footer;
		else return '';
	}

	function register_widgets () {
		register_widget('JE_ALERT_WIDGET');
		//$this->get_alert_message();
	}
	
	
   function save_alert_message(){
   		$content=isset($_REQUEST['content']) ? $_REQUEST['content']  :"";
   		if($content && $content['name'] && $content['value']){
           $name= $content['name'];
           $value= $content['value'];
           update_option($name,stripcslashes($value));
           echo json_encode(array('success' => true, 'msg' => __('Options has been updated successfully!', ET_DOMAIN)));
		   exit;
   		}
   		die();  
   }

   public function get_alert_message_template(){

        $option		=	new ET_GeneralOptions ();
		$site_logo	=	$option->get_website_logo ();
		$customize	=	$option->get_customization ();
		
		$header	=	
		'<html class="no-js" lang="en">
			<body style="margin: 0px; font-family: '.$customize['font-text'].', sans-serif; font-size: 13px; line-height: 1.3;">
				<style>
					.job_list {
						color : #000;
					}
				</style>
				<div style="margin: 0px auto; width:660px; border: 1px solid '.$customize['background'].'">

					<table width="100%" cellspacing="0" cellpadding="0">
						<tr style="background-color: '.$customize['header'].'; display:block; padding:10px 0px; vertical-align: middle; box-shadow: 0 2px 0 2px #E3E3E3;">
							<td style="padding: 0 10px 0 20px; width: 0px;">
								<a href="[site_url]" target="_blank">
									<img title="" alt="[blogname]" src="[site_logo]" />
								</a>
							</td>
							<td style="padding-bottom: 3px;">
								<span style="text-shadow: 0 0 1px #151515; color: #b0b0b0;">[blogdescription]</span>
							</td>
							<td style="padding-right: 20px; width : 25%">
								<!-- see more button -->
								<a href="[site_url]" target="_blank" style="display: block; position: relative; box-shadow: 0 1px 2px #222; -moz-box-shadow: 0 1px 2px #222; -webkit-box-shadow: 0 1px 2px #222; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; text-decoration: none; padding: 0 10px; height: 40px; line-height: 40px; background-repeat: repeat-x; background: '.$customize['action'].';">
									<span style="color: #fff; font-size: 12px; font-weight: bold; text-shadow: 1px -1px 1px #9e4230;">
										'.__("VIEW MORE JOBS", ET_DOMAIN).'
									</span>						
								</a>
							</td>
						</tr>
						<tr style="height: 3px; background-color: '.$customize['background'].'"><td colspan="3"></td></tr>
						<tr>
							<td colspan="3" style="padding: 10px 20px">
								<table>
									<tr>
										<td colspan="2" style="line-height : 26px ;font-size : 24px; color: #5c5c5c; padding-bottom: 10px; font-weight: normal; font-family :'.$customize['font-heading'].';">
										Re-discover your potentials. Re-vision your future. Meet Success. Let [blogname] take you there!
										</td>
									</tr>
									<tr>
										<td style="padding: 8px 10px 8px 0;" class="job_list">
											[list_jobs]
										</td>
									</tr>
								';
             $footer	=	'</table>
							</td>
								</tr>
								<tr style="padding: 10px 20px; color: #909090; height: 89px; background-repeat: repeat-x; background-color:#f7f7f7;">
									<td colspan="3">
										<table width="100%" cellspacing="0" cellpadding="0">
											<tr>
												<td style="text-align: left; padding: 10px 20px; width:140px;">
													<a href="[site_url]" target="_blank" style="display: block; position: relative; box-shadow: 0 1px 2px #999; -moz-box-shadow: 0 1px 2px #999; -webkit-box-shadow: 0 1px 2px #999; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; text-decoration: none; padding: 0 10px; height: 40px; line-height: 40px; background-repeat: repeat-x; background: '.$customize['action'].';">
														<span style="color: #fff; font-size: 12px; font-weight: bold; text-shadow: 1px -1px 1px #9e4230;">
															'.__("VIEW MORE JOBS", ET_DOMAIN).'
														</span>						
													</a>
												</td>
												<td style="text-align: left; padding: 10px 20px;">
													<table>
														<tr>
															<td>[copyright]</td>
														</tr>
														<tr>
															<td style="text-align:center;"><a href="[unsubscribe_link]">Unsubscribe</a>
															</td>
														</tr>						
													</table>
												</td>
												<td style="text-align: right; padding: 10px 20px;">
													[admin_email]
												</td>
											</tr>
										</table>
									</td>
									
								</tr>
							</table>
							
						</div>
						
					</body>
					</html>';

			$mail_alert_message = $header.$footer;	
			return $mail_alert_message;	
   }

  public function reset_mail(){		
		try {
			if (empty($_POST['content']['mail']))
				throw new Exception( __("Mail template can't be found", ET_DOMAIN) );

			$name = $_POST['content']['mail'];
			$return = $this->get_alert_message_template();
			
			if ( $return == false ) 
				throw new Exception(__("Mail template can't be found", ET_DOMAIN));
			else 
			{
				update_option($name,stripcslashes($return));
				echo json_encode( array(
					'success' 	=> true,
					'data'		=> array(
						'name' 		=> $name,
						'template' 	=> $return
					)
				) );
			}

		} catch (Exception $e) {
			echo wp_send_json(array(
				'success' => false,
				'msg' 		=> $e->getMessage()
			));
		}
		exit;
	}



}
add_action('after_setup_theme','je_alert_init',12);
function je_alert_init(){
	new JE_ALERT();
}

/**
* this function help  developer testing and debug.
*/
if( !function_exists("je_log") ){
	function je_log($string){

		$file = fopen(ABSPATH."/wp-content/plugins/je_jobalert/je_log.txt","a");
		fwrite($file,date("Y-m-d H:i:s", current_time( "timestamp", true ) ).' - '.$string."\n");
		fclose($file); 
	}
}