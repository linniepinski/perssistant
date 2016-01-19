<?php
error_reporting(E_ALL);  // turn on all errors, warnings and notices for easier debugging
if(class_exists("ET_AdminMenuItem")){
class CE_Ebay extends ET_AdminMenuItem {
	const 	URL_API_USER 		= 'http://open.api.ebay.com/shopping';  // Shopping
	const  	URL_API_SEARCH 		= 'http://svcs.ebay.com/services/search/FindingService/v1';  // Finding
	const  	TYPE_RESPONSE 		= 'XML';   // Format of the response
	const  	API_VERSION  		= '759';//667';   // Shopping API version number
	const  	API_FINDING_VERSION = '1.7.0';   // Finding API version number
	const  	APP_ID  			= 'YoungWor-48d3-4bfb-8435-5530470b03e1'; //replace this with your AppID
	public function __construct($args = array() ){
		parent::__construct('ebay-import',  array(
			'menu_title'	=> __('CE eBay Import', ET_DOMAIN),
			'page_title' 	=> __('CE eBay Import', ET_DOMAIN),
			'callback'   	=> array($this, 'menu_view'),
			'slug'			=> 'ebay-import',
			'page_subtitle'	=> __('ebay-import Overview', ET_DOMAIN),
			'pos' 			=> 45,
			'icon_class'	=> 'icon-menu-overview'
		));

		$this->add_action('et_admin_menu', 'add_option_page');
		$this->add_ajax('ebay-search-ad','ebay_search_ad');
		$this->add_ajax('ebay-save-imported-ads','ebay_save_imported_ads');
		$this->add_ajax('ebay-get-categories','ebay_get_categories');
		$this->add_ajax('ebay-save-setting','ebay_save_setting');
		$this->add_ajax('ebay-load-ads','ebay_load_ads');
		$this->add_ajax('ebay-delete-ads','ebay_delete_ads');
		$this->add_ajax('ebay_connecting','ebay_connecting');
		// 
		$this->add_ajax('update-ebay-schedule','updateSchedule');
		$this->add_ajax('ebay-set-days-run','ebay_set_number_days_run');
		$this->add_ajax('ebay-toggle-schedule','ebay_toggle_schedule');
		$this->add_ajax('ebay-delete-schedule','ebay_delete_schedule');



	}
	public static function get_option(){
		$default    =   array(
                    'app_id' 		=> 'ebaysjinternal',
                    'custom_id'  	=> '123456',
                    'tracking_id'   => '1234567890',
                    'network_id'    => '9',
                    'use_affiliate' => 1
                );
        $setting  =    get_option('ce_ebay_setting', array());
        return wp_parse_args( $setting, $default );
	}
	public static function get_schedule_option(){
		return get_option ('ce_ebay_schedule_option', array());
	}

	public function add_option_page(){
		// default args
		et_register_menu_section($this->menu_name, $this->menu_args);
	}
	function on_add_styles() {
		wp_enqueue_style( 'admin.css' );
		wp_enqueue_style('ebay-style',CE_IMPORT_URL.'/css/ce-import.css');
	}
	function on_add_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('underscore');
		wp_enqueue_script('backbone');
		wp_enqueue_script( 'ce' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script('ebay-script',CE_IMPORT_URL . '/js/ce-ebay.js',array('jquery', 'underscore', 'backbone', 'ce') );

		$options  	= self::get_option();
		wp_localize_script( 'ebay-script', 'ebay_script', array(
			'ajax_url' 			=> admin_url( 'admin-ajax.php' ),
			'select_cat' 		=> wp_dropdown_categories(array('echo'=>0,'id'=>CE_AD_CAT,'class'=>'set-cat', 'hide_empty'=>false,'exclude_tree'=>true, 'hierarchical'=>3, 'taxonomy'=>CE_AD_CAT,'name'=>'imports[{{ stt }}]['.CE_AD_CAT.']')),
			'select_location'	=> wp_dropdown_categories(array('echo'=>0,'id'=>'ad_location','class'=>'set-local','hide_empty'=>false,'exclude_tree'=>true, 'hierarchical'=>3, 'taxonomy'=>'ad_location','name'=>'imports[{{ stt }}][ad_location]')),
			'category_all' 		=> wp_dropdown_categories(array('show_option_all' => __('Set all to category',ET_DOMAIN), 'class'=>'set-all-cat','echo'=>0,'id'=>CE_AD_CAT,'hide_empty'=>false,'exclude_tree'=>true, 'hierarchical'=>3, 'taxonomy'=>CE_AD_CAT,'name'=>'cat_all')),
			'location_all'		=> wp_dropdown_categories(array('show_option_all' => __('Set all to location',ET_DOMAIN),'class'=>'set-all-local', 'echo'=>0,'id'=>'ad_location','hide_empty'=>false,'exclude_tree'=>true, 'hierarchical'=>3, 'taxonomy'=>'ad_location','name'=>'location_all')),
			'imgURL'			=> get_bloginfo("template_url").'/img',
			'loading'			=> __("Loading", ET_DOMAIN),
			'loadingImg' 		=> '<img class="loading loading-wheel" src="'.TEMPLATEURL . '/img/loading.gif" alt="'.__('Loading...', ET_DOMAIN).'">',
			'loadingTxt' 		=> __('Loading...', ET_DOMAIN),
			'loadingFinish' 	=> '<span class="icon loading" data-icon="3"></span>',
			'app_id' 			=> $options['app_id'],
			'tracking_id' 		=> $options['tracking_id'],
			'network_id' 		=> $options['network_id'],
			'custom_id' 		=> $options['custom_id'],
			'use_affiliate' 	=> $options['use_affiliate'],
			'url_search' 		=> 'http://svcs.ebay.com/services/search/FindingService/v1?OPERATION-NAME=findItemsAdvanced'
			//findCompletedItems findItemsAdvanced
			)
		);
	}

	function menu_view($args){ ?>
		<div class="et-main-header">
			<div class="title font-quicksand"><?php _e('Import eBay Ads',ET_DOMAIN);?></div>
			<div class="desc"><?php _e('Manage your imported eBay ads',ET_DOMAIN);?></div>
		</div>
		<div id= "ce_ebay_import" class="et-main-content wrap-ebay">
			<div class="et-main-left">
				<ul class="et-menu-content inner-menu">
					<li>
						<a class="active" menu-data="simplyhired" href="#ebay-settings">
							<span data-icon="y" class="icon"></span> <?php _e('API Setting',ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a class="" menu-data="import" href="#ebay-manual">
							<span data-icon="s" class="icon"></span><?php _e('Manual ',ET_DOMAIN);?>
						</a>
					</li>
					<li>
						<a class="first-click" id="ebay_manage_link" menu-data="manage" href="#ebay-manage">
						<span data-icon="l" class="icon"></span><?php _e('Manage Ads',ET_DOMAIN);?>	</a>
					</li>
				</ul>
			</div>

			<div class="settings-content et-main-main">
				<div class="desc">
					<?php
	 					include CR_IMPORT_PATH . '/ebay_settings.php';
	 					include CR_IMPORT_PATH . '/ebay_manual.php';
	 					include CR_IMPORT_PATH . '/ebay_schedule.php';
	 					include CR_IMPORT_PATH . '/ebay_manage_ads.php';
	 				?>
				</div>
			</div>
		</div>
	<?php
	}
	// action ajax: ebay-search-ad
	// send request to ebay.com and parse response.
	public function ebay_search_ad(){
		$request 	= $_POST;
		$resp 		= CE_Ebay_API::ebay_search($request);
		wp_send_json($resp);
	}

	// action ajax : ebay-save-imported-ads
	public function ebay_save_imported_ads(){
		if (!current_user_can( 'manage_options' ) )
			return ;
		$request 		= $_POST;
		//$cat_all 		= ($request['cat_all'] == 0) ?  false : $request['cat_all'];
		//$location_all 	= ($request['location_all'] == 0) ?  false : $request['location_all'];
		$imports 		= $request['imports'];
		$resp 			= array('success'=>false,'msg'=>__('Save success'));
		$items			= array();
		$username  		= isset($_POST['author']) ? $_POST['author'] : '';
		$author = array();
		if($username){
			$author = get_user_by( 'login', $username );
		}
		$author_id = isset($author->ID) ? $author->ID :'';

		foreach($imports as $key=>$item){
			if(isset($item['allow'])){
				$item['author'] = $author_id;
				$items[] 	= $this->import_item($item);
			}
			$resp = array('success'=>true,'msg'=>__('Save success'),'data'=> $items);
		}
       	wp_send_json($resp);
	}
	/*
	* import list items from  _POST form.
	*/
	public static function import_item($item){
		$ad_category 		= array('0' => '0');
		$ad_location 	= array('0' => '0');


		extract($item);

		$et_expired_date 		=  date('Y-m-d H:i:s',strtotime($end_time));
		$titled 				=  str_replace("''",'"',$title);
		$args = array(
			'guid' 			=> $viewItemURL,
			'post_title'	=> $titled,
			'post_status' 	=> 'publish',
			'post_content' 	=> '',
			'post_type' 	=> CE_AD_POSTTYPE,
			'post_author' 	=> $author,
			'tax_input' 	=> array('ad_location'=>$ad_location,CE_AD_CAT => $ad_category)
		);

		global $wpdb;	
		$item_id 		= $allow;
		$resp 			= array();
		$record 		= $wpdb->query( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s",'ce_ebay_item_id', $item_id));
		if( !$record ){
			$post_id 	= wp_insert_post($args,true);
			if(!is_wp_error($post_id) ) {

				if(isset($location))
					add_post_meta($post_id, 'et_full_location', $location , true);
				add_post_meta($post_id, 'ce_ebay_url', $viewItemURL, true);
				add_post_meta($post_id, 'ce_ebay_item_id', $item_id, true);

				//galleryURLThumb
				add_post_meta($post_id, 'ce_out_thumb', $galleryURL, true);

				if(empty($galleryURL))
					update_post_meta($post_id, 'ce_out_thumb', $galleryURLThumb );


				update_post_meta($post_id, CE_ET_PRICE, $currentPrice, true);
				update_post_meta($post_id, 'et_currency', $currencyId, true);
				update_post_meta($post_id, '_et_featured', 0 , true);
				update_post_meta($post_id, 'et_expired_date', $et_expired_date , true);
				$resp ['post'] = array('id'=> $item_id);
			}
		}  else {// end !record
			$resp['item'] 	= array('id'=> $item_id);
		}
		return $resp;
	}
	/*
		action : ebay-get-categories
		get categories from ebay site
	*/
	public function ebay_get_categories(){
		$site 		= isset($_POST['site']) ? $_POST['site'] : 0;
		$categories = ce_ebay_dropdow_categories(array('echo'=>false,'site'=>$site));
		setcookie("ebay_site", $site, time() + 30*24*3600);
		wp_send_json( array('success'=>true,'data'=> $categories) );
	}
	/*
		action : ebay-save-setting
		save ebay settings(app_id,affiliate info)
	*/
	public function ebay_save_setting(){
		if (!current_user_can( 'manage_options' ) )
			return ;

		$request = $_POST;
	 	if(!isset($_POST['use_affiliate']))
	 		$request['use_affiliate'] = 0;
		update_option('ce_ebay_setting',$request);
		$setting = get_option('ce_ebay_setting',array());
		wp_send_json(array('success'=> true, 'msg'=> __('Save app id success',ET_DOMAIN), 'data'=>$setting) );
	}

	/*
		action : ebay-load-ads
		auto load list item has imported  after click manage menu.

	*/
	function ebay_load_ads(){
		$paged 	= isset($_POST['paged']) ? $_POST['paged'] : 1;
		$args = array(
			'post_type' => CE_AD_POSTTYPE,
			'meta_key' 	=> 'ce_ebay_item_id',
			'paged' 	=> $paged,
		   	'meta_query' => array(
		       array(
		           'key' 	=> 'ce_ebay_item_id',
		           'value' 	=> 1,
		           'compare'=> '>=',
		       )
		));

		$ads 	= CE_Ads::query($args);
		$items 	= array();
		$paginator_html = ce_ebay_pagination($ads,$paged);
		$current = array(
			'EUR' => '&euro;',
			'USD' => '$',
			'CAD' => 'CA $',
			'GBP' => '&pound;',
			'SGD' => 'S$',
			'PHP' => '&#8369;',
			'AUD' => 'AU $',
			'PLN' => 'zł',
			'CHF' => 'CHF',
			'INR' => 'INR'
			);

		if($ads->have_posts()){
			$price_key = CE_ET_PRICE;
			while($ads->have_posts()) : $ads->the_post();
				global $post;
				$ad = CE_Ads::convert($post);
				if(isset($ad->location[0]))
				$ad->et_location = $ad->location[0]->name;
				$currentcy = get_post_meta($ad->ID,'et_currency',true);
				$nt = isset($current[$currentcy]) ? $current[$currentcy] : $currentcy;
				$ad->price = $nt.$ad->$price_key;
				$items[] = $ad;
			endwhile;
			$resp = array('success'=> true,'msg'=> __('List ads imported from ebay.com',ET_DOMAIN), 'data' => $items, 'paging' => $paginator_html);
		} else {
			$resp = array('success' => false,'msg'=> __('Your list is empty.'));
		}
		wp_send_json( $resp );
	}

	/*
	 	action : ebay-delete-ads
	 	delete ads from list id selected
	*/
	public function ebay_delete_ads(){
		if (!current_user_can( 'manage_options' ) )
			return ;

		$deleted 	= array();
		$ids 		= $_POST['id'];

		foreach ($ids as $key => $id) {
			$result  = wp_delete_post($id);
			if($result)
			$deleted[] = $id;
		}

		wp_send_json(array('success'=>true,'msg'=>__('deleted ads success',ET_DOMAIN),'data'=> $deleted));
	}

	// action : ebay_connecting
	// testing connect to ebay server.
	function ebay_connecting(){
		$app_id = $_POST['app_id'];
		$apicall 	= 'http://open.api.ebay.com/Shopping?callname=GetCategoryInfo';
		$apicall 	.='&appid='.$app_id;
		$apicall 	.='&version=677&siteid=0&CategoryID=-1';
		$result 		= simplexml_load_file($apicall);
		$resp 	= array();
		try{
			if($result->Ack == 'Success'){
				$resp = ('Connected!');
			} else {
				$resp =__('Application ID invalid');
			}
		} catch (Exception $e){
				$resp =$e->getMessage();
		}
		wp_send_json($resp);
	}

	/*
	// Schedlue controls.
	// Update schedule settings.
	*/
	function updateSchedule(){
		try {
			// validate user's permission
			if(!current_user_can ('manage_options') || !wp_verify_nonce($_POST['simplyhired_update_schedule'],'update_schedule' ))
				throw new Exception(__('Permission Denied', ET_DOMAIN), '402');
			unset($_POST['simplyhired_update_schedule']);
			unset($_POST['_wp_http_referer']);
			unset($_POST['action']);

			if($_POST['number'] > 25) $_POST['number']	=	25;
			if($_POST['keywords'] == '' )
				throw new Exception(__('Input Invalid!', ET_DOMAIN));
			if($_POST['import_author'] == '') {
				global $current_user;
				$_POST['import_author']	=	$current_user->ID;
				$_POST['author']	=	$current_user->user_login;
			}
			$schedule	=	$this->save_schedule ( $_POST);
			$response	=	array('success' => true , 'data' => $schedule,'msg'=>__('Add schedule successfull!'));

		} catch (Exception $e) {
			$response	=	array('success' => false , 'msg' => $e->getMessage());
		}
		wp_send_json($response);
	}

	/*
		// save schedule settings;
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
	function update_schedule_option ($schedule_list) {
		return update_option('ce_ebay_schedule_option', $schedule_list );
	}
	public function ebay_set_number_days_run(){
		$days = $_POST['number_days'];
		$resp =array('success'=>false,'msg' => __('Save false!'));
		if(is_numeric($days)){
			update_option('ce_ebay_days_run',$days);
			$resp =array('success'=>true,'msg' => __('Save successfull!'));
		}
		wp_send_json($resp);

	}

	// action : ebay-toggle-schedule
	// on or off a schedule item
	public function ebay_toggle_schedule(){

		$id 			=	$_POST['id'];
		$resp 			= 	array('success' => false,'msg' => __('Toggle success'));
		$schedule_list 	= 	$this->get_schedule_option ();
		foreach ($schedule_list as $key => $schedule) {
			if($schedule['schedule_id'] == $id){
				$status = ($schedule['ON'] == 1) ? 0 : 1;
				$schedule['ON'] = $status;
				$schedule_list[$key] = $schedule;
				$resp 			= 	array('success' => true,'msg' => __('Toggle success'));
			}
		}
		$this->update_schedule_option ($schedule_list);
		wp_send_json($resp);
	}

	// action : ebay-delete-schedule
	// delete schedule

	public function ebay_delete_schedule(){

		$id 			=	$_POST['id'];
		$resp 			= 	array('success' => false,'msg' => __('Delete false'));
		$schedule_list 	= 	$this->get_schedule_option ();
		foreach ($schedule_list as $key => $schedule) {
			if($schedule['schedule_id'] == $id){
				$status = ($schedule['ON'] == 1) ? 0 : 1;
				$schedule['ON'] = $status;
				unset($schedule_list[$key]);
				$resp = array('success' => true, 'msg' => __('Delete success',ET_DOMAIN), 'data'=>$id);
			}
		}
		$this->update_schedule_option ($schedule_list);
		wp_send_json($resp);
	}
}

}
?>
