<?php
if(!defined('ET_DOMAIN')) define ( 'ET_DOMAIN' , 'enginetheme');
class CE_Alert_Front{

	private $instance = NULL;

	private static $post_type 			= 'subscriber';

	public static $meta_field_category 	= 'ad_category';

	public static $meta_field_local 	= 'ad_location';



	function __construct(){

		$this->instance = CE_Alert_Option::get_instance();

		//if(is_active_widget('ce_alert'))

		if(is_active_widget( false, false,'ce_alert', true )){

			add_action('wp_ajax_alert-add-subscriber',array($this,'alert_add_subscriber'));

			add_action('wp_ajax_nopriv_alert-add-subscriber',array($this,'alert_add_subscriber'));

			add_filter('the_content',array($this,'unsubscriber_content'),11);

			add_action('wp_ajax_nopriv_remove-subscriber',array($this,'ce_unsubcriber'));

			add_action('wp_ajax_ce-remove-subscriber',array($this,'ce_unsubcriber'));

			add_action('ce_on_add_scripts',array($this,'subscriber_footer'),11 );

			add_action('ce_on_add_styles' , array($this, 'frontend_css'));

			add_filter('ce_minify_source_path', array ($this, 'minify_source_path') );

			//add_action('plugins_loaded', array( $this, 'alert_load_mo_file') );

			add_filter( 'et_get_translate_string', array($this, 'add_translate_string'),100 );


		}

	}
	function add_translate_string ($entries) {
		//create mofile;

		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/ce_alert.po',true );
		return	array_merge($entries, $pot->entries);
	}
	function alert_load_mo_file() {
	 	$mofile = ABSPATH.'/wp-content/plugins/ce_adalert/ce_alert.mo';
	 	if(!is_readable($mofile))
	 		chmod($mofile, 755);

		load_textdomain( ET_DOMAIN, $mofile );
	}
	function unsubscriber_content($content){

		if( isset($_GET['email']) && isset($_GET['code']) ){

			$form = '

			<div class="entry-blog tinymce-style" id="ce_alert">

		      	'.__('Are you sure you want to unsubscribe?',ET_DOMAIN).'

		      	<div style="padding:0" class="widget-job-alert">

					<div style="padding-top:20px;float:left;" class="unsubscribe">

						<form action="" method="POST" id="unsubscribe_form" class="form" novalidate="novalidate">

							<div class="form-item form-group">

								<input type="hidden" value="'.$_GET['code'].'" id="code" name="code">

								<input type="hidden" value="ce-remove-subscriber" id="action" name="action">

								<input type="text" value="'.$_GET['email'].'" id="email" name="email" placeholder="Email address" class="bg-default-input">

								&nbsp; &nbsp; &nbsp; <button class="btn  btn-primary" href="javascript:void(0);" id="unsubscribe_btn">'.__('Unsubscribe',ET_DOMAIN).'</button>

							</div>

						</form>

					</div>



			 	</div>

		 	</div>';

			return $form;
		}

		return $content;

	}


	function alert_add_subscriber(){

		$resp 		= array('success' => false, 'msg' => __('Insert subsciber false',ET_DOMAIN) ) ;

		$request 	= $_POST;

		$resp = $this->insert_alert($request);
		if(!is_wp_error($resp))
			wp_send_json($resp);

		wp_send_json(array('success' => false, 'msg' => $resp->get_error_message()));

	}



	function insert_alert($args){


		$cats  	= implode(",", $args['sub_category']);

		$locals = implode(",", $args['sub_location']);

		$title 	= trim($args['email']);

		if ( !is_email( $title) ) {
		    return new WP_Error( '201', __( "Email address is valid.", "ET_DOMAIN" ) );
		}

		global $wpdb;

		$exist = $wpdb->get_results("SELECT ID FROM $wpdb->posts	WHERE post_status = 'private' AND post_title ='$title' AND post_type='subscriber'");



        if($exist) {

        	$sub_id 	=$exist[0]->ID;

			$resp =  array('success' => true,'msg' => __('You have updated successfully.', ET_DOMAIN));

        } else {

			$default  		= array('post_status' => 'private','post_type'=>self::$post_type,'post_title' => $args['email']);

			$sub_id  = wp_insert_post($default);

			if(!is_wp_error($sub_id)){

				$resp =  array('success' => true,'msg' => __('You have successfully subscribed to receive ad alerts.', ET_DOMAIN));

				$unsubscribe_key = wp_generate_password( 20, false );

				update_post_meta( $sub_id, 'ce_subscriber_code_unsubscribe', $unsubscribe_key);

			}

		}


		$array_cats = (array) $args['sub_category'];
		if( in_array( 0, $array_cats) ){
			// in case subscriber follow all categories
			update_post_meta($sub_id, 'follow_all_cat', 1);

		} else {
			// follow one or specific  categories.
			foreach ($array_cats as $key => $var) {

			    $array[$key] = (int)$var;
			}

			wp_set_post_terms($sub_id, $array_cats,'ad_category');
		}



		$ids_local = (array) $args['sub_location'];

		if( in_array( 0, $ids_local) ){
			// in case subscriber follow all categories
			update_post_meta($sub_id, 'follow_all_local', 1);

		} else {
			// follow one or specific  locations.
			foreach ($ids_local as $key => $id) {

			    $ids_local[$key] = (int)$id;

			}

			wp_set_post_terms($sub_id, $ids_local,'ad_location');
		}


		return $resp;

	}

	function minify_source_path($mini_path){

        $mini_path['front'][]    	= CE_ALERT_PATH.'//js/chosen.jquery.js';

        $mini_path['front'][]    	= CE_ALERT_PATH.'//js/alert.js';

        $mini_path['theme_css'][] 	= CE_ALERT_PATH.'//css/chosen.css';

        return $mini_path;

	}



	function subscriber_footer(){	?>

		<style type="text/css">

			.chosen-container-multi .chosen-choices li.search-choice .search-choice-close {

			  background: url('<?php echo CE_ALERT_URL;?>/css/chosen-sprite.png') -42px 1px no-repeat;

			}

		</style>

		<?php

		$mininfy = get_theme_mod( 'ce_minify', 0 );

		if (!$mininfy && !is_page_template('page-post-ad.php') ) {

				wp_enqueue_style('chosen',CE_ALERT_URL.'/css/chosen.css');

				wp_enqueue_script('js.choosen',CE_ALERT_URL.'/js/chosen.jquery.js',array('jquery','backbone','underscore'),CE_VERSION, true);

				wp_enqueue_script('js.alert',CE_ALERT_URL.'/js/alert.js',array('jquery', 'js.choosen', 'backbone', 'underscore','ce'));



		}



		if(is_page('unsubscriber') && !$mininfy ){

			wp_enqueue_script('js.alert',CE_ALERT_URL.'/js/alert.js',array('jquery', 'backbone', 'underscore','ce'),CE_VERSION,true);

		}

	}

	function frontend_css(){

		$mininfy = get_theme_mod( 'ce_minify', 0 );

		if(!$mininfy)

			wp_enqueue_style('chosen',CE_ALERT_URL.'/css/chosen.css');

	}



	function ce_unsubcriber(){

		$request 	= $_POST;

		$mail 		= $request['email'];

		global $wpdb;

		$exist  	= $wpdb->get_results("SELECT ID FROM $wpdb->posts	WHERE post_status = 'private' AND post_title ='$mail' AND post_type='subscriber'");

		$resp 		= array('msg'=> __('Unsubscribe successfully',ET_DOMAIN),'success' => true);

		if($exist){

			$sub_id = $exist[0]->ID; ;

			$code 	= get_post_meta($sub_id,'ce_subscriber_code_unsubscribe',true);



			if($code == $request['code']){

				wp_delete_post( $sub_id , true );

				delete_post_meta($sub_id, 'ce_subscriber_code_unsubscribe');

				delete_post_meta($sub_id, 'ce_have_new_ad');

				$resp = array('msg'=> __('Unsubscribe successfully',ET_DOMAIN),'success' => true);

			} else {
				$resp = array('msg'=> __('The code is incorrect!',ET_DOMAIN),'success' => false);
			}

		} else {

			$resp = array('msg'=> __('The email don\'t exists',ET_DOMAIN),'success' => false);

		}

		wp_send_json($resp);

	}
}

new CE_Alert_Front();



?>