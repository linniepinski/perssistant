<?php
/*
Plugin Name: JE Coupon
Plugin URI: www.enginethemes.com
Description: JE Coupon available on JE version 1.1.5
Version: 1.7
Author: Engine Themes team
Author URI: www.enginethemes.com
License: GPL2
*/

define('JE_COUPON_VERS', '1.6');

require_once dirname(__FILE__) . '/update.php';

class JE_Coupon {
	function __construct() {
		add_action('et_admin_enqueue_scripts-je-coupon', array($this, 'plugin_scripts'));
		add_action('et_admin_enqueue_styles-je-coupon', array($this, 'plugin_styles'));

		add_action('et_admin_menu', array($this, 'register_menu_import'));
		add_action( 'init', array($this,'register_post_type'));

		add_action('before_je_payment_button', array($this,'add_coupon_form'));

		add_action('wp_footer', array($this, 'front_end_script'),1);
		add_action('wp_head', array($this, 'front_end_styles'));

		add_filter( 'je_payment_setup_order_data', array($this,'setup_order_data') );
		add_filter( 'je_payment_setup', array($this,'je_payment_setup'), 50, 3 ) ;
		add_filter( 'je_payment_process', array($this, 'process_payment'), 10 ,3 );

		add_filter( 'et_get_translate_string', array($this, 'add_translate_string') );

		add_filter('the_title', array($this, 'payment_title'), 10, 2);
	}
	/**
	 * filter payment title to add coupon detail
	*/
	function payment_title ($title, $id) {
	
		if( (isset($_REQUEST['page']) && $_REQUEST['page'] == 'et-payments' ) 
				|| (isset($_REQUEST['action']) && $_REQUEST['action'] == 'et-filter-job-processor'))  {
			global $post;
			$order_id			=	$post->ID;

			$discount_method	=	get_post_meta( $order_id, 'et_order_discount_method', true );
			$discount_rate		=	get_post_meta( $order_id, 'et_order_discount_rate', true );
			$coupon_code		=	get_post_meta( $order_id, 'et_order_coupon_code', true );
			
			if($discount_method == 'percent') $discount_method = '%';
			if($discount_method == 'currency') $discount_method = get_post_meta( $order_id,'et_order_currency', true);
			if($discount_rate > 0)
				$title .= " (-$discount_rate $discount_method) ";
			
		}
		
		return $title;
	}

	function add_translate_string ($entries) {
		$pot		=	new PO();
		$pot->import_from_file(dirname(__FILE__).'/default.po',true );
		
		return	array_merge($entries, $pot->entries);
	}
	/**
	 * filter payment setup response if free return direct link to process payment
	*/
	function je_payment_setup ($response, $paymentType, $order) {

		$order_pay	=	$order->generate_data_to_pay();
		
		if($order_pay['total'] <= 0 ) {
			//$session	=	et_read_session();
			$job_id		=	$order_pay['product_id'];

			$response	=	array(
				'success' 		=> true,
				'data'	=> array ('ACK' => true, 'url' => et_get_page_link('process-payment' , array('paymentType' => 'coupon' , 'job_id' => $job_id)) )
			);

			update_post_meta( $order_pay['ID'], 'et_order_gateway', 'cash');

			et_update_post_field ($job_id, 'job_paid', 1);
			
			$o 	= et_get_post_field($job_id, 'job_order');

			if(empty($o)) {
				$o	=	array ();
			}
			$o[]	=	$order_pay['ID'];

			et_update_post_field ($job_id, 'job_order',$o );			

		}

		return $response;
	}

	function process_payment ( $payment_return, $order , $payment_type ) {
		if( $payment_type == 'coupon' ) {
			$order_pay	=	$order->generate_data_to_pay();
			if( $order_pay['total'] <= 0 ) {
				$payment_return	=	array (
					'ACK' 		=> true,
					'payment'	=>	'coupon'
				);
				$order->set_status ('publish');
				$order->update_order();
				
			}
		}	
		return $payment_return;
	}

	/**
	 * hook to filter order data and add coupon code if available
	*/
	function setup_order_data ($order_data) {
		if(isset($_REQUEST['coupon_code'])) {
			$coupon_code	=	trim($_REQUEST['coupon_code']);
			$coupon_data	=	$this->generate_coupon_response($coupon_code);		

			if(!is_user_logged_in()) return $order_data;
			/**
			 * check coupon
			*/
			global $user_ID;
			$is_available	=	$this->check_coupon($coupon_code, $user_ID);
			$ok 	=	0;
			if(!empty($is_available['added_product'])) {
				foreach ($is_available['added_product'] as $key => $value) {
					if($key == $_REQUEST['packageID']) {
						$ok	=	1;
					}
				}
			} else {
				$ok	=	1;
			}

			if(!isset($is_available['success']) && $ok) {
				/**
				 * add coupon to order
				*/
				$order_data['coupon_code']		=	$coupon_data['coupon_code'];
				$order_data['discount_rate']	=   $coupon_data['discount_rate'];
				$order_data['discount_method']  = 	$coupon_data['discount_type'];

				$pre	=	intval(get_user_meta( $user_ID, 'je_coupon_used_'.$coupon_code, true ));
				$pre++;
				update_user_meta( $user_ID, 'je_coupon_used_'.$coupon_code, $pre);

				$used_time	=	intval($coupon_data['have_been_used']);
				$used_time++;
				update_post_meta( $coupon_data['ID'], 'je_coupon_have_been_used', $used_time  );

			}
		}
		
		return $order_data;
	}

	/**
	 * add coupon form in post a job page
	*/
	function add_coupon_form () {
		if(is_page_template('page-post-a-job.php') || is_page_template( 'page-upgrade-account.php' )) {
	?>
	<li class="hidecoupon clearfix payment-coupon">
		<div id="coupon_form">
			<div class="coupon-bar" title="<?php _e("Use a coupon", ET_DOMAIN); ?>"><?php _e('COUPON', ET_DOMAIN );?></div>
			
			<div class="coupon-item" style="display:none;" >				
				<div class="coupon-input">
					<input type="text" class="bg-default-input" id="coupon_code" placeholder="<?php _e('ENTER COUPON CODE',ET_DOMAIN);?>" />
					<span class="total-title"><?php _e('Total price', ET_DOMAIN );?></span>
					<span class="job-price"></span>
					<span class="coupon-price"></span>
					<div class="btn-select f-right">
						<button data-gateway="cash" class="bg-btn-hyperlink border-radius select_payment disable-payment"><?php _e('Free Post',ET_DOMAIN);?> </button>
					</div>
				</div>
			</div>
			
		</div>
	</li>
	<?php 
		}
	}
	/**
	 * generate coupon data response
	*/
	function generate_coupon_response ($code) {
		$args	=	array(
				'post_type'		=> 'je_coupon',
				'post_status'	=> 'publish',
				'meta_key'		=> 'je_coupon_code',
				'meta_value'	=> $code
			);

		$coupons	=	new WP_Query ($args);
		
		if($coupons->have_posts()) {
			$coupon	=	$coupons->posts[0];
			
			$prefix	=	'je_coupon_';
			$key_maps	=	 array('usage_count','user_coupon_usage','discount_type','discount_rate','start_date','expired_date','date_limit', 'added_product', 'have_been_used');

			$coupon_data	=	array( 'ID' => $coupon->ID ,'coupon_code'	=> $code );
			foreach ($key_maps as $key => $value) {
				$coupon_data[$value]	=	get_post_meta( $coupon->ID, $prefix.$value, true ) ;
				//$coupon_data[$value]	=	$coupon_data[$value] ?  $coupon_data[$value] : 0;
			}
			
			return $coupon_data;

		} else {
			return false;
		}
	}
	/**
	 * check $coupon_code is available for $user_ID or not
	*/
	function check_coupon ( $code , $user_ID = 0 ) {
		$code	=	trim($code);
		if($code == '') 
			return array('success' => false, 'msg' => __("Coupon code is invalid!", ET_DOMAIN));

		$coupon_data	=	$this->generate_coupon_response ($code);
		/**
		 * code not found or draft
		*/
		if(!$coupon_data) {
			return array('success' => false, 'msg' => __("Coupon code is invalid!", ET_DOMAIN));
		}
		/**
		 * check coupon date limit
		*/
		if($coupon_data['date_limit'] == 'on') {
			$today			=	date('Y-m-d');

			$today_dt 		= new DateTime($today);
			$start_date 	= new DateTime($coupon_data['start_date']);
			$expired_date	= new DateTime($coupon_data['expired_date']);

			if ($start_date > $today_dt || $today_dt > $expired_date) {
				return array('success' => false, 'msg' => sprintf(__("This coupon is valid from (%s) to (%s).", ET_DOMAIN), $coupon_data['start_date'], $coupon_data['expired_date']) );
			}
		}

		if(!$user_ID && !is_user_logged_in())  {
			return $coupon_data;
		}

		if(!$user_ID) 
			global $user_ID;
		/**
		 * check user used coupon or not
		*/
		$used_coupon	=	intval(get_user_meta( $user_ID, 'je_coupon_used_'.$code , true ));
		
		if( $used_coupon >= $coupon_data['user_coupon_usage']) {
			return array('success' => false, 'msg' => __("You have used this coupon!", ET_DOMAIN));
		}

		if($coupon_data['have_been_used'] >= $coupon_data['usage_count']) {
			return array('success' => false, 'msg' => __("This coupon runs out of usage!", ET_DOMAIN));	
		}

		return $coupon_data;	
	}

	/**
	 * update coupon data
	*/
	function update_coupon ($extends = array()) {

		if(!current_user_can('manage_options')) return false;

		$args	=	array(
			'ID'				=> '',
			'post_type' 		=> 'je_coupon',
			'post_status'		=> 'publish'
		);
		/**
		 * setup coupon data
		*/
		$extends	=	wp_parse_args( $extends, array('added_product' => array(), 'usage_count'=> 100,	'user_coupon_usage'	=> 1,'discount_type'=> 'percent','discount_rate'=> 10,'start_date'=> date('d/m/Y', time()),'expired_date'=> '','date_limit'=> 'off') );
		if(trim($extends['user_coupon_usage']) == '' ) $extends['user_coupon_usage'] = 1;
		/**
		 * insert coupon and data 
		*/
		if(isset($extends['coupon_id']) && $extends['coupon_id'] ) {
			$args['ID']	=	$extends['coupon_id'];
			$id			=	wp_update_post( $args );
		}else {
			$coupon_code	=	strtoupper (substr(md5(time()), 0, 13) );
			$args['post_title']	=	$coupon_code;
			$id	=	wp_insert_post( $args );
			update_post_meta( $id, 'je_coupon_code', $coupon_code );
		}
		unset($extends['coupon_id']);
		foreach ($extends as $key => $value) {
			update_post_meta( $id, 'je_coupon_'.$key, $value);
		}

		if($id && !is_wp_error( $id )) {
			return array('id' => $id, 'code' => get_post_meta( $id, 'je_coupon_code', true ));
		} else {
			return false;
		}
	}

	function register_post_type () {
		register_post_type('je_coupon', array(
			'labels' => array(
					'name' => _x('Coupon', 'post type general name',ET_DOMAIN),
					'singular_name' => _x('Coupon', 'post type singular name', ET_DOMAIN),
					'add_new' => _x('Add New', 'application', ET_DOMAIN),
					'add_new_item' => __('Add New Coupon', ET_DOMAIN),
					'edit_item' => __('Edit Coupon', ET_DOMAIN),
					'new_item' => __('New Coupon', ET_DOMAIN),
					'all_items' => __('All Coupon', ET_DOMAIN),
					'view_item' => __('View Coupon', ET_DOMAIN),
					'search_items' => __('Search Coupon', ET_DOMAIN),
					'not_found' =>  __('No Coupon found', ET_DOMAIN),
					'not_found_in_trash' => __('No Coupon found in Trash', ET_DOMAIN), 
					'parent_item_colon' => '',
					'menu_name' => 'Coupon'
				),
				'public' => false,
				'publicly_queryable' => true,
				'show_ui' => false, 
				'show_in_menu' => false, 
				'query_var' => true,
				'rewrite' => true,
				'capability_type' => 'post',
				'has_archive' => true, 
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array( 'title',  'author','custom-fields' )
			)
		) ;
	}
	
	function front_end_script() {
		if(is_page_template('page-post-a-job.php') || is_page_template( 'page-upgrade-account.php' )) {
			wp_enqueue_script('je.coupon.frontend', plugin_dir_url(__FILE__).'/js/front-end.js');
			wp_localize_script( 'je.coupon.frontend','je_coupon', array('currency'	 => ET_Payment::get_currency(), 'free_text' => __('Free Post', ET_DOMAIN)) );
		}
	}

	function plugin_scripts () {
		// wp_deregister_script( 'jquery' ); // deregisters the default WordPress jQuery & enqueue the new one later
		// wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'et_underscore' );
		wp_enqueue_script( 'et_backbone' );		
		wp_enqueue_script( 'job_engine' );
		wp_enqueue_script( 'admin_scripts' );
		wp_enqueue_script('jquery_validator');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script( 'je_coupon', plugin_dir_url(__FILE__).'/js/je-coupon.js'  );
	}
	
	function front_end_styles () {
		if(is_page_template('page-post-a-job.php') || is_page_template( 'page-upgrade-account.php' )) 
			wp_enqueue_style( 'je_coupon.frontend.style', plugin_dir_url(__FILE__).'/css/front-end.css' );
	}

	function plugin_styles () {
		wp_enqueue_style('admin_styles');
		wp_enqueue_style( 'je_coupon', plugin_dir_url(__FILE__).'/css/je-coupon.css', array('admin_styles') );
	}

	/**
	 * Register menu setting
	 */
	public function register_menu_import(){
		// register payment menu item
		et_register_menu_section('je-coupon', array(
			'menu_title' 	=> __('JE Coupon', ET_DOMAIN),
			'page_title' 	=> __('JE COUPON', ET_DOMAIN),
			'callback' 		=> array( $this, 'et_menu_callback' ),
			'slug' 			=> 'je-coupon',
			'page_subtitle'	=>	__('Manage your sales promotion', ET_DOMAIN)
		));
	}

	public function et_menu_callback ($args) {
	?>
    	<div class="et-main-header">
    		<div class="title font-quicksand"><?php _e("JE COUPON", ET_DOMAIN); ?></div>
    		<div class="desc"><?php _e("Create and manage your own discount promotions.", ET_DOMAIN); ?></div>
    	</div>
    	<div class="et-main-content" id="coupon-manage">
    		<div class="et-main-main one-column">
    		<?php
    		require_once 'coupon-list.php';
    		require_once 'coupon-add-new.php';
    		?>
    		</div>
    	</div>
	    
	<?php 
	}
}

class JE_Coupon_Ajax extends JE_Coupon {
	function __construct () {
		parent::__construct();
		add_action('wp_ajax_je-update-coupon', array($this, 'ajax_save_coupon'));
		add_action('wp_ajax_je-delete-coupon', array($this, 'ajax_delete_coupon'));
		add_action('wp_ajax_je-check-coupon-code', array($this, 'ajax_check_coupon_code'));
		add_action('wp_ajax_nopriv_je-check-coupon-code', array($this, 'ajax_check_coupon_code'));

		add_action('wp_ajax_je-coupon-get-page', array($this, 'load_page'));
	}
	
	function ajax_check_coupon_code () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(isset($_POST['coupon_code']))  {
			$is_available	=	$this->check_coupon ($_POST['coupon_code']);
		
			if(!isset($is_available['success'])) {
				extract($is_available);
				$i	=	0;

				if(empty($added_product)) {
					if($discount_type == 'percent')
						$coupon_price	=	$_POST['price'] - $_POST['price']*$discount_rate/100;
					else 
						$coupon_price	=	$_POST['price'] - $discount_rate;

					wp_send_json(array('success' => true , 'data' => $is_available['coupon_code'] , 'coupon_price' => max($coupon_price,0) ));
					
				}

				foreach ($added_product as $key => $value) {
					if( $key == $_POST['packageID'] ) {
						if($discount_type == 'percent')
							$coupon_price	=	$_POST['price'] - $_POST['price']*$discount_rate/100;
						else 
							$coupon_price	=	$_POST['price'] - $discount_rate;

						wp_send_json(array('success' => true , 'data' => $is_available['coupon_code'] , 'coupon_price' => max($coupon_price,0) ));						
					}
				}
				echo json_encode(array('success' => false , 'data' => $is_available['coupon_code'] , 'msg' => __("Your coupon code is not applicable for the selected payment plan.", ET_DOMAIN) ));
			}
			else {
				echo json_encode($is_available);
			}
			exit;
		}
	}	

	function ajax_delete_coupon () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can('manage_options')) {
			$response	=  array(
					'success' => false,
					'msg'	 => __("Permission denied!", ET_DOMAIN)
				);
		} else {
			$response	=	array('success' => true);
			try {
				wp_delete_post( $_POST['coupon_id'], true );
			} catch (Exception $e) {
				$response['success']	=	false;
				$response['msg']		=	$e->getMessage();
			}
		}
		echo json_encode($response);
		exit;
	}

	function ajax_save_coupon () {

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		if(!current_user_can('manage_options')) {
			echo json_encode ( array(
					'success' => false,
					'msg'	 => __("Permission denied!", ET_DOMAIN)
				));
			exit;
		}

		unset ($_POST['action']);
		
		if( $_POST['date_limit'] == 'on' ) {
			if(!$this->checkDate($_POST['start_date']) || !$this->checkDate($_POST['expired_date']) )   {
				echo json_encode ( array(
					'success' => false,
					'msg'	 => __("Invalid Date format!", ET_DOMAIN)
				));
				exit;
			}
		}

		$coupon_data	=	$this->update_coupon($_POST);
		echo json_encode ( array(
				'success' => true,
				'data'	 => $coupon_data
			));
		exit;
	}
	/**
	 * ajax return coupon list page
	*/
	function load_page () {
		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: application/json' );
		$paged	=	$_GET['paged'];
		$args	=	array(
			'post_type'		=> 'je_coupon',
			'post_status'	=> 'publish',
			'paged'			=> $paged
		);
		$coupon_list	=	new WP_Query ($args);
		$plans			=	et_get_payment_plans();

		
		$resume_plan	=	et_get_resume_plans ();

		$currency	=	ET_Payment::get_currency();
		$tr		=	'';
		while ($coupon_list->have_posts()) { $coupon_list->the_post();
			global $post;
			$coupon_data	=	$this->generate_coupon_response(get_the_title());
			$date_limit		=	$coupon_data['date_limit'];
			$added_product	=	$coupon_data['added_product'];

			if($date_limit == 'on') {
				$start_date		=	 $coupon_data['start_date']; 
				$expired_date	=	 $coupon_data['expired_date']; 
			}else {
				$start_date = __("Lifetime", ET_DOMAIN);
				$expired_date = __("Lifetime", ET_DOMAIN);
			}
			if($coupon_data['discount_type'] == 'percent') 
					$type	= ' (%)';
				else 
					$type	=	 $currency['code'];

			$products	=	'';
			if(empty($added_product)) {
				$products	=	__("All job packages", ET_DOMAIN);
			} else {
				$num	=	count($added_product);
				$i		=	0;
				foreach ($added_product as $key => $value) {
					$i++;
					$products	.=	 $value;
					if($i < $num) $products .= ', ';
				}
			}

			$tr	.='<tr id="coupon-'.$post->ID.'" data-coupon="'.$post->ID.'" >'.
				'<script id="coupon_'.$post->ID.'" type="text/data">'.json_encode($coupon_data).'</script>'.			
				'<td>'.$coupon_data['coupon_code'].'</td>'.
				'<td align="center">'.$start_date.'</td>'.
				'<td align="center">'.$expired_date.'</td>'.
				'<td>'.$coupon_data['discount_rate'].' '.$type.'</td>'.
				'<td>'.$products.'</td>'.
				'<td>'.intval($coupon_data['have_been_used']).'<span class="count">/'.$coupon_data['usage_count'].'</span></td>'.
				'<td align="center">'.
					'<a href="#" class="delete" title="'.__("Delete", ET_DOMAIN).'"><span class="icon" data-icon="-"></span></a>'.
					'<a href="#" class="edit" title="'.__("Edit", ET_DOMAIN).'"><span class="icon" data-icon="p"></span></a>'.
				'</td>'.
			'</tr>';
		}
		$paginate	=	'';
		if ($coupon_list->max_num_pages > 1) {
			for ($i = 1; $i <= $coupon_list->max_num_pages; $i++){
				if( $i != $paged)
					$paginate	.=	'<li data-page="'.$i.'" ><a  href="#" class="pi">'.$i.'</a></li>';
				else 
					$paginate	.=	'<li data-page="'.$i.'" ><span class="current">'.$i.'</span></li>';
			 }
		} 
		$response	=	array('success' => true, 'data' => $tr, 'paginate' => $paginate );
		echo json_encode($response);
		exit;

	}

	function checkDate($date){
	    
		if($date == date('Y-m-d',strtotime($date)) ) 
			return true; 
		return false;		
	} 

}

class JE_Coupon_Schedule extends JE_Coupon {

}

new JE_Coupon_Ajax ();