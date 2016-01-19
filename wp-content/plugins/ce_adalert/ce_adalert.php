<?php
/*
Plugin Name: CE AdAlert
Plugin URI: www.enginethemes.com
Description: CE AdAlert
Version: 1.3
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/


define('CE_ALERT_PATH',dirname(__FILE__));
define('CE_ALERT_URL', plugins_url( basename(dirname(__FILE__)) ));


//require_once dirname(__FILE__) . '/update.php';
require_once dirname(__FILE__) . '/inc/widget.php';
require_once dirname(__FILE__) . '/alert_option.php';
require_once dirname(__FILE__) . '/alert_front.php';

add_action ('after_setup_theme', 'ce_adlert_init');

function ce_adlert_init () {

	require_once dirname(__FILE__) . '/alert_backend.php';

	if(class_exists('CE_AdLert_Menu')){

		new CE_AdLert_Menu();
	}

}

class CE_Alert{
	private $option = null;
	private $page_unsub = 0;
	public function __construct(){

		$this->option = CE_Alert_Option::get_instance();

		add_filter('add_post_type_to_ad_cat', array($this,'add_post_type_to_ad_cat'));

		add_filter('add_post_type_to_ad_localtion', array($this,'add_post_type_to_ad_localtion'));

		register_activation_hook(__FILE__, array($this,'activation'));

		register_deactivation_hook(__FILE__, array($this,'deactivation'));

		add_action('init',array($this,'subscriber_register_post_type'),11 );

		add_filter('private_title_format', array($this,'title_format'));

		add_action('wp', array($this,'subsriber_setup_schedule') );

		add_action('et_mail_schedule_event_main', array($this, 'mail_schedule_main') );

		add_action('ce_alert_mail_event_trigger', array($this, 'ce_alert_mail_event_trigger'));

		add_filter('cron_schedules', array($this, 'cron_add_interval'),10000);

		add_action('save_post', array($this,'mark_subscriber_have_new_ad'));

		add_filter( 'excerpt_length', array($this, 'custom_excerpt_length'), 999 );

		add_action('wp_ajax_del-subscriber',array($this,'del_subscriber'));

	}


	function activation($first=0) {


		wp_clear_scheduled_hook('et_mail_schedule_event_main');

		wp_clear_scheduled_hook('ce_alert_mail_event_trigger');

		$option = $this->option->get_option();

		$time_default_schedule = $option['schedule'];

		// schedule main.
		wp_schedule_event( time() + 10  , $time_default_schedule , 'et_mail_schedule_event_main');

		global $wpdb;
		$unsub_page = $wpdb->get_results( "SELECT ID  FROM $wpdb->posts where post_type='page' AND post_name = 'unsubscriber'" );

		if ( !$unsub_page ){
		 	$unsub_id = wp_insert_post(array(
				'post_title' 		=> __('Unsubscriber', ET_DOMAIN),
				'post_content' 		=> __('Page unsubscriber for user 123', ET_DOMAIN),
				'post_name' 	=> 'unsubscriber',
				'post_type' 	=> 'page',
				'post_status' 	=> 'publish'
			));

			update_option('page_unsub',$unsub_id);

		}

	}
	function deactivation() {
		wp_clear_scheduled_hook('ce_mail_schedule_event');
		wp_clear_scheduled_hook('et_mail_schedule_event_main');

	}
	function title_format($content) {
		return '%s';
	}

	function custom_excerpt_length(){
		return 500;
	}
	function del_subscriber(){
		if( !current_user_can('delete_others_posts') ){
			wp_send_json(array('Success' => 'false', 'msg' => __('You don\'t have permission to remove subscriber',ET_DOMAIN)));
		}
		$sub_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
		if(empty($sub_id) || !is_numeric($sub_id))
			wp_send_json(array('success' => 'false', 'msg' => __(' Remove fail',ET_DOMAIN)));

		$result =wp_delete_post($sub_id,true);
		if(!is_wp_error($result))
			wp_send_json(array('success' => 'true', 'msg' => __(' Remove success',ET_DOMAIN)));
		else
			wp_send_json(array('success' => 'false', 'msg' => $result->get_error_message()) );
	}

	function subscriber_register_post_type () {

		$labels = array(
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
				);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'subscriber' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		register_post_type( 'subscriber', $args );
		register_taxonomy_for_object_type( 'ad_category', 'subscriber');
		register_taxonomy_for_object_type( 'ad_location', 'subscriber');
	}

	function subsriber_setup_schedule(){
		if(!wp_next_scheduled('et_mail_schedule_event_main')) {
			$this->activation(1);
		}
	}
	/**
	 * add custome interval for mail schedule
	*/
	function cron_add_interval( $schedules ) {
	 	// Adds once weekly to the existing schedules.
	 	$schedules['custom_mail_recurrence'] = array(
	 		'interval' => 10 ,
	 		'display' => __( 'Custom Mail Schedule' )
	 	);

	 	$schedules['weekly'] = array(
	 		'interval' =>  7*3600*24 ,
	 		'display' => __( 'Custom Mail Schedule Weekly')
	 	);


	 	return $schedules;
	}

	/*
	* this function only run after main schedule run.
	* have to destroy afer send email finish.
	*/
	function ce_alert_mail_event_trigger() {
		$option = $this->option->get_option();
		$number_mails	=	$option['number_emails'];
		$number_ads		=	$option['number_ads'];

		$this->sent_mail($number_mails, $number_ads);

	}

	/*
	* main schedule run each setting in dashboard.
	* Only run once time when to time schedule and triger the ce_alert_mail_event_trigger function .
	*/
	function mail_schedule_main () {

		$option = $this->option->get_option();
		$number_mails	=	$option['number_emails'];
		$number_ads		=	$option['number_ads'];
		//update_option('number_ad_wait_sent',$number_mails);// set any number max;

		$this->sent_mail ($number_mails, $number_ads );
		// schedule phu
		wp_schedule_event( time() + 10, 'custom_mail_recurrence', 'ce_alert_mail_event_trigger');
	}

	function sent_mail($number_mails,$number_ad){
		$unsub_page 	= get_option('page_unsub','unsubscriber');

		$args = array(
			'post_type' 		=> 'subscriber',
			'meta_key' 			=> 'ce_have_new_ad',
			'meta_value' 		=> 1,
			'post_status' 		=> 'private',
			'posts_per_page' 	=> $number_mails
		);

		$subscribers = new WP_Query($args);

		if( $subscribers->have_posts() ) {

			while($subscribers->have_posts()){

				$subscribers->the_post();
				$subscriber = new CE_Alert_Option();
				$sub_id 	= get_the_ID();
				$mail 		= get_the_title();

				$code 		= get_post_meta($sub_id,'ce_subscriber_code_unsubscribe',true);

				$unsub_link = et_get_page_link("unsubscribe", array('email' => $mail, 'code' => $code));

				$subject	= sprintf(__('New ads alert from %s', ET_DOMAIN), get_option('blogname') );

				$cats 		= wp_get_post_terms( $sub_id, 'ad_category', array('fields'=>'ids') );

				$locals 	= wp_get_post_terms( $sub_id, 'ad_location', array('fields'=>'ids') );


				$message 	= $this->get_alert_message($cats, $locals,$number_ad);

				$message 	= str_replace('[unsubscribe_link]', $unsub_link, $message);



				$status = wp_mail($mail, $subject, $message);

				update_post_meta($sub_id,'ce_have_new_ad', 0);
			}

		} else {

			wp_clear_scheduled_hook('ce_alert_mail_event_trigger');
		}
	}
	function get_alert_message ($cats, $locals, $number_ads) {

		$option			=	new CE_Options;

		$website_logo 	= 	$option->get_website_logo();

		$customize		=	$option->get_customization ();

		$logo 			= apply_filters('ce_alert_set_logo_url',$website_logo[0]);

		$header	=
		'<html class="no-js" lang="en">
			<body style="margin: 0px; font-family: '.$customize['font-text'].', sans-serif; font-size: 13px; line-height: 1.3;">
				<div style="margin: 0px auto; width:600px; border: 1px solid '.$customize['background'].'">

					<table width="100%" cellspacing="0" cellpadding="0">
						<tr style="background-color: '.$customize['header'].'; display:block; padding:10px 0px; vertical-align: middle; box-shadow: 0 2px 0 2px #E3E3E3;">
							<td style="padding: 0 10px 0 20px; width: 0px;">
								<a href="'.home_url().'" target="_blank">
									<img title="" alt="'.get_option('blogname').'" src="'.$logo.'" />
								</a>
							</td>
							<td style="padding-bottom: 3px;">
								<span style="text-shadow: 0 0 1px #151515; color: #b0b0b0;">'.get_option('blogdescription').'</span>
							</td>
							<td style="padding-right: 20px; width : 25%">
								<!-- see more button -->
								<a href="'.home_url().'" target="_blank" style="display: block; position: relative; box-shadow: 0 1px 2px #222; -moz-box-shadow: 0 1px 2px #222; -webkit-box-shadow: 0 1px 2px #222; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; text-decoration: none; padding: 0 10px; height: 40px; line-height: 40px; background-repeat: repeat-x; background: '.$customize['action'].';">
									<span style="color: #fff; font-size: 12px; font-weight: bold; text-shadow: 1px -1px 1px #9e4230;">
										'.__("VIEW MORE ADS", ET_DOMAIN).'
									</span>
								</a>
							</td>
						</tr>
						<tr style="height: 3px; background-color: '.$customize['background'].'"><td colspan="3"></td></tr>
						<tr>
							<td colspan="3" style="padding: 10px 20px">
								<table>
									<tr>
										<td colspan="2" style="line-height : 26px ;font-size : 18px; color: #5c5c5c; padding-bottom: 10px; font-weight: normal; font-family :'.$customize['font-heading'].';">
											'.sprintf(__('Re-discover your potentials. Re-vision your future. Meet Success. Let %s take you there!', ET_DOMAIN), get_option('blogname') ).'
										</td>
									</tr>';

			$ad_content	=	'';
			$ad_arg = array(
					'post_type' 		=> 'ad',
					'post_status' 		=> 'publish',
					'posts_per_page' 	=> $number_ads,

					);

			if($cats)
				$ad_arg['tax_query'][] = array(
											'taxonomy' => 'ad_category',
											'field' => 'id',
											'terms' => $cats,
											'operator' => 'IN'
										);
			if($locals)
				$ad_arg['tax_query'][] = array(
											'taxonomy' => 'ad_location',
											'field' => 'id',
											'terms' => $locals,
											'operator' => 'IN'
										);
			if($cats && $locals)
				$ad_arg['tax_query']['relation'] = 'AND';

				$ads = new WP_Query($ad_arg);

			while ($ads->have_posts()) { $ads->the_post ();

				global $post;

				$url = TEMPLATEURL.'/img/no_image.gif';

				if($website_logo)
					$url = $website_logo[0];

				$url = apply_filters('ce_alert_no_thumbnail_url',$url);


				if(has_post_thumbnail()){
					$thumb 	= wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'ad-thumbnail-grid' );
					$url 	= $thumb['0'];
				}
				$ad = CE_Ads::convert ($post);

				$ad_content	.=	'<tr>
							<td style="padding: 8px 10px 15px 0;">
								<a style="display: block; padding: 5px; text-decoration:none; height: 70px; border-radius: 3px; text-decoration: none; -moz-border-radius: 3px; -webkit-border-radius: 3px; -moz-box-shadow: 0 3px 3px #E9E9E9; -webkit-box-shadow: 0 3px 3px #E9E9E9; box-shadow: 0 3px 3px #E9E9E9; border-bottom:2px solid #E9E9E9;">
								<img width="auto" style= "max-width:153px; max-height:111px" height="auto" title="" alt="" src="'.$url.'" /></a>
							</td>
							<td valign="top" style="padding: 10px 0;">
								<a href="'.get_permalink().'" style="font-size :13px; color: #3399CC; font-family :'.$customize['font-heading'].'; text-decoration: none; display: block; font-weight: 700; margin-bottom: 10px; text-transform: uppercase;">'.get_the_title( ).'</a>
								<div style="color: #909090;font-family :'.$customize['font-text'].'; font-size :12px;">
									<span style="color='.$customize['action_2'].'" >'.$ad->price.'</span> <br />
									'.get_the_excerpt().'
								</div>
							</td>
						</tr>';
			}
			// end while
			wp_reset_query();
			$info 	=	apply_filters ('ce_mail_footer_contact_info' , get_option('blogname').' <br>'.get_option('admin_email').' <br>'
				);
			$footer			=	'</table>
							</td>
								</tr>
								<tr style="padding: 10px 20px; color: #909090; height: 89px; background-repeat: repeat-x; background-color:#f7f7f7; border-top:1px solid #eaeaea">
									<td colspan="3">
										<table width="100%" cellspacing="0" cellpadding="0" bo>
											<tr>
												<td style="text-align: left; padding: 10px 20px; width:123px;">
													<a href="'.home_url().'" target="_blank" style="display: block; position: relative; box-shadow: 0 1px 2px #999; -moz-box-shadow: 0 1px 2px #999; -webkit-box-shadow: 0 1px 2px #999; border-radius: 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; text-decoration: none; padding: 0 10px; height: 40px; line-height: 40px; background-repeat: repeat-x; background: '.$customize['action'].';">
														<span style="color: #fff; font-size: 12px; font-weight: bold; text-shadow: 1px -1px 1px #9e4230;">
															'.__("VIEW MORE ADS", ET_DOMAIN).'
														</span>
													</a>
												</td>
												<td style="text-align: left; padding: 10px 20px;">
													<table>
														<tr>
															<td>'.$option->get_copyright ().'</td>
														</tr>
														<tr>
															<td style="text-align:center;"><a class="link-unsubscriber" style="text-decoration:none" href="[unsubscribe_link]">'.__('Unsubscribe from this newsletter.',ET_DOMAIN).'</a> 
															</td>
														</tr>
													</table>
												</td>
												<td style="text-align: right; padding: 10px 20px;">
													'.$info.'
												</td>
											</tr>
										</table>
									</td>

								</tr>
							</table>

						</div>

					</body>
					</html>';



		if($ad_content != ''){
			$header_email = apply_filters("ce_alert_header_email",$header);
			$footer_email = apply_filters("ce_alert_footer_email", $footer);

			return $header_email.$ad_content.$footer_email;
		}
		else return '';
	}

	function mark_subscriber_have_new_ad($ad_id){

		if(get_post_type( $ad_id ) != 'ad') return ;
		$ad = get_post($ad_id);


		if($ad->post_status !='publish')
			return ;

		$categories =  	wp_get_post_terms( $ad_id, 'ad_category', array('fields'=>"ids") );
		$localtions =  	wp_get_post_terms( $ad_id, 'ad_location', array('fields'=>"ids") );
		$term		= 	wp_get_post_terms( $ad_id, 'ad_category' );
		$args = array( 	'post_type' 		=> 'subscriber',
						'post_status' 		=>'private',
						'posts_per_page' 	=> -1,
						'tax_query' 		=> 	array(array(
													'taxonomy' 	=> 'ad_location',
													'field' 	=> 'id',
													'terms' 	=> $localtions,
													'operator' 	=> 'IN'
												),
												array(
													'taxonomy' 	=> 'ad_category',
													'field' 	=> 'id',
													'terms' 	=> $categories,
													'operator' => 'IN'
												)));



		$subscribers = new WP_Query($args);

		if($subscribers->have_posts()):

			while($subscribers->have_posts()): $subscribers->the_post();
				update_post_meta(get_the_ID(),'ce_have_new_ad',1);
			endwhile;

		endif;
		/**
		* support subscriber all cateogires or all locations.
		* @since  1.1
		*/
		$subscriber_all_cats 	= new WP_Query(array('post_type'=> 'subscriber', 'post_status' =>'private', 'posts_per_page' => -1, 'meta_key' =>'subscriber_all_cat','meta_value' => 1));
		$subscriber_all_local 	= new WP_Query(array('post_type'=> 'subscriber', 'post_status' =>'private', 'posts_per_page' => -1, 'meta_key' =>'subscriber_all_cat','meta_value' => 1));


		if($subscriber_all_cats->have_posts()):

			while($subscribers->have_posts()): $subscriber_all_cats->the_post();
				update_post_meta(get_the_ID(),'ce_have_new_ad',1);
			endwhile;

		endif;

		if($subscriber_all_local->have_posts()):

			while($subscribers->have_posts()): $subscriber_all_local->the_post();
				update_post_meta(get_the_ID(),'ce_have_new_ad',1);
			endwhile;

		endif;

	}


}
new CE_Alert();
/*
* Log file to debug code .
*/

if( !function_exists("ce_log_alert") ){
	function ce_log_alert($string){
		$url   = CE_ALERT_PATH."/log_alert.log";
		$file = fopen($url,"a");
		fwrite($file,date("Y-m-d H:i:s", current_time( "timestamp", true ) ).' - '.$string."\n");
		fclose($file);
	}
}
