<?php
define('ET_SLIDER_PATH',dirname(__FILE__));
define('ET_SLIDER_URL', plugins_url( basename(dirname(__FILE__)) ));

class ET_Slider_Admin extends ET_Slider_Base{
	CONST ACTION_ADMIN_INIT 			= 'admin_init';
	const ACTION_AJAX_DEL_FIELD 		= 'wp_ajax_et_delete_slider';
	const ACTION_AJAX_SORT_ATTACHMENT 	= 'wp_ajax_et_sort_attachment';
	const ACTION_META_BOX 				= 'et_job_meta_box';

	const ACTION_AJAX_DEL_ATTACHMENT	= 'wp_ajax_on_del_attachment';
	const ACTION_AJAX_SYN_ATTACHMENT	= 'wp_ajax_et_sync_attachment';
	const ACTION_AJAX_SAVE_SLIDER		= 'wp_ajax_et_save_slider';

	const ACTION_AJAX_SYN_SLIDER		= 'wp_ajax_et_sync_slider';

	function __construct(){
		$this->inits();

	}
	function inits(){
		$this->add_action('et_admin_menu', 'add_admin_menu');
		$this->add_action('et_admin_enqueue_styles-et-slider', 'on_print_styles');
		$this->add_action('et_admin_enqueue_scripts-et-slider', 'on_print_scripts');
		$this->add_action(self::ACTION_AJAX_DEL_ATTACHMENT, 'on_delete_attachment');
		$this->add_action(self::ACTION_AJAX_SORT_ATTACHMENT,'on_sort_sliders');
		$this->add_action(self::ACTION_AJAX_SYN_ATTACHMENT,'et_sync_attachment');
		$this->add_action(self::ACTION_AJAX_SYN_SLIDER,'et_sync_slider');
		$this->add_action(self::ACTION_AJAX_SAVE_SLIDER,'et_save_slider');
		$this->add_action('wp_ajax_et_attachment_upload','et_attachment_upload');
	}

	public function on_print_styles(){
		$this->add_style('et-lider-style', ET_SLIDER_URL . '/css/admin.css' );
		$this->add_existed_style('admin_styles');
		// wp_enqueue_style('jquery-ui-style', 'http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css' );
	}

	public function on_print_scripts(){
		$this->add_existed_script('et_underscore');
		$this->add_existed_script('et_backbone');
		$this->add_existed_script('plupload-all');
		$this->add_existed_script('jquery-ui-sortable');
		$this->add_existed_script('jquery_validator');
		$this->add_script('et-slider-lib',ET_SLIDER_URL . '/js/et_lib.js', array('jquery', 'et_underscore' ,'et_backbone'));
		if(isset($_GET['action'])){
			$this->add_script('et-slider-admin',ET_SLIDER_URL . '/js/et_attachment.js', array('jquery', 'et_underscore' ,'et_backbone'));
		}
		else
			$this->add_script('et-slider-admin', ET_SLIDER_URL . '/js/et_slider_admin.js', array('jquery', 'et_underscore' ,'et_backbone'));
		wp_localize_script( 'et_backbone', 'et_globals', array(
			'ajaxURL' 			=> admin_url('admin-ajax.php'),
			'routerRoot' 		=> add_query_arg('page', 'engine-settings', admin_url('admin.php')),
			'tooltip' 			=> array(
				'deletePlan' 	=> __('Delete', ET_DOMAIN) ,
				'editPlan' 		=> __('Edit', ET_DOMAIN)
				),
			'imgURL'			=>	TEMPLATEURL.'/img/',
			'plupload_config'	=> array(
				'max_file_size' 		=> '3mb',
				'url' 					=> admin_url('admin-ajax.php'),
				'flash_swf_url' 		=> includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url'	=> includes_url('js/plupload/plupload.silverlight.xap'),
				'filters' 				=> array( array( 'title' => __('Image Files'), 'extensions' => 'jpg,jpeg,gif,png' ) ),
			),
			'loadingImg' 		=> '<img class="loading loading-wheel" src="'.TEMPLATEURL . '/img/loading.gif" alt="'.__('Loading...', ET_DOMAIN).'">',
			'loading' 		=> __('Loading', ET_DOMAIN)
		) );

		wp_localize_script( 'et_backbone', 'et_slider', array(
			'confirm_delete_slider' 	=> __('Are you sure you want to delete this slider?', ET_DOMAIN),
			'confirm_delete_slide' 		=> __('Are you sure you want to delete this slide?', ET_DOMAIN),
			'url_style_tinymce'			=> __(ET_SLIDER_URL . '/js/tiny_mce/content.css',ET_DOMAIN)

		));

		// wp_localize_script( 'job_engine', 'et_views', array(
		// 	'loadingImg' 		=> '<img class="loading loading-wheel" src="'.TEMPLATEURL . '/img/loading.gif" alt="'.__('Loading...', ET_DOMAIN).'">',
		// 	'loadingTxt' 		=> __('Loading...', ET_DOMAIN),
		// 	'loadingFinish' 	=> '<span class="icon loading" data-icon="3"></span>'
		// ) );
	}

	public function add_admin_menu(){
		et_register_menu_section('et-slider',array(
			'menu_title' => __('ET Slider'),
			'page_title' => __('ET SLIDER'),
			'page_subtitle' => __("Manage list slider", ET_DOMAIN),
			'callback'   => array($this, 'et_slider_view'),
			'slug'          => 'et-slider',
			));

	}

	function et_save_slider(){
		$args = array(
			'ID' => $_POST['id'],
			'post_title' => $_POST['title']
			);
		$post_id =  wp_update_post($args);
		if ( !($post_id instanceof WP_Error) )
			$response = array(
			'success' => true,
			'code' => 200,
			'msg' => __('Updated successfull!', ET_DOMAIN)

			);//$res  = array('success' => 'true','msg' =>'Updated successfull');
		else
			$response = array('success' => 'false','msg' => 'Update false');
		header( 'HTTP/1.0 200 OK' );
		header( "Content-Type: application/json" );
		echo json_encode( $response );
		exit;
	}


	function et_attachment_upload(){

		header( 'HTTP/1.0 200 OK' );
		header( 'Content-type: text/html' );
		//header( "Content-Type: application/json" );
		$res	= array(
			'success'	=> false,
			'msg'		=> __('There is an error occurred', ET_DOMAIN ),
			'code'		=> 400,
		);
		// check fileID
		if(!isset($_POST['fileID']) || empty($_POST['fileID']) ){
			$res['msg']	= __('Missing image ID', ET_DOMAIN );
		}
		else {
			$fileID	= $_POST["fileID"];
				$author	= 1;
				// check ajax nonce
				if ( !check_ajax_referer( 'slider_thumb_et_uploader', '_ajax_nonce', false ) ){
					$res['msg']	= __('Security error!', ET_DOMAIN );
				}
				elseif(isset($_FILES[$fileID])){
					// handle file upload
					$attach_id	= et_process_file_upload( $_FILES[$fileID], $author, 0, array(
							'jpg|jpeg|jpe'	=> 'image/jpeg',
							'gif'			=> 'image/gif',
							'png'			=> 'image/png',
							'bmp'			=> 'image/bmp',
							'tif|tiff'		=> 'image/tiff'
						) );

					if ( !is_wp_error($attach_id) ){
						// Update the author meta with this logo
						try {
							$attach_img	= et_get_attachment_data($attach_id);
							$res	= array(
								'success'	=> true,
								'msg'		=> __('Company logo has been uploaded successfully!', ET_DOMAIN ),
								'data'		=> $attach_img
							);
						}
						catch (Exception $e) {
							$res['msg']	= __( 'Problem occurred while updating user field', ET_DOMAIN );
						}
					}
					else{
						$res['msg']	= $attach_id->get_error_message();
					}
				}
				else {
					$res['msg']	= __('Uploaded file not found', ET_DOMAIN);
				}
		}
		echo json_encode($res);	
		exit;
	}
	static function show_list_slider_parent($args){
        ?>
        <div class="et-main-header">
                <div class="title font-quicksand"><?php echo $args->menu_title ?></div>
                <div class="desc"><?php // echo $args->page_subtitle ?></div>
         </div>

        <script type="application/json" id="et_slider_data">
            <?php $sliders = et_refresh_slider(); echo json_encode( array_map('et_create_slider_response', array_values($sliders)) ) ?>
        </script>

        <div class="et-main-main et-main-full no-menu clearfix inner-content" id="job-fields-list">
            <div class="title font-quicksand">
                <?php _e("Sliders' List", ET_DOMAIN) ?>
            </div>
            <div class="desc">
                <?php
                echo '<div class="top-des">';
                _e('Default shortcode to insert slider to your post :',ET_DOMAIN);echo '<code><input type="text" readonly ="readonly" class="et-shortcode" id="shorrcode_demo" value= "[et_slider id=1 height=325 speed=5000]" /></code>'; 
                echo '</div>';
                $args = array(
                       'post_type'      => 'et_slider',
                       'numberposts'    => -1,
                       'post_status'    => 'publish',
                       'orderby'        => 'menu_order date',
                       'order'          => 'DESC',
                       'post_parent'    => 0
                      );
                $attachments = get_posts( $args );
             	echo '<div class="head-list-slider row"><div class = "col-title-slider col-slider-left col-slider "> <strong> Name </strong></div><div class="col-shortcode col-slider-right col-slider"> <strong> <span> Shortcode </strong> </span> </div>  </div>';
                echo'<ul class="ordered-list list_slider sortable form" id="list_slider">';

                if(count($attachments) >0)   {
                    if ( $attachments ) {

                        foreach ( $attachments as $attachment ) {
                            echo '<li class="item" data="'.$attachment->ID.'" id="slider_'.$attachment->ID.'">';
                                //echo '<div class="sort-handle"></div>';
                                //echo wp_get_attachment_image( $attachment->ID, 'thumbnail' );
                                echo '<a href="'. add_query_arg(array('action' => 'edit', 'id' => $attachment->ID )) .'" class="et-slider-title"><span  class = "slider-title" ><strong>'. $attachment->post_title . '</strong></span></a>';
                                echo '<input name="shortcode" class="et-shortcode" type="text" id="shortcode" value= " [et_slider id= '. $attachment->ID .']" class = "bg-grey-input not-empty" readonly ="readonly" /> ';
                                echo "<div class='title-hide form-item'><input type='text' name='title' id='title_slider' value = '". $attachment->post_title."'/></div>";
                                    echo '<div class="actions">
                                        <a data-icon="p" rel="'.$attachment->ID.'" class="icon act-edit act-edit-slider" title="Edit" href="#"> </a>
                                        <a data-icon="D" rel="'.$attachment->ID.'"  data="'.$attachment->ID.'" class="icon act-del" title="Delete" href="#"></a>
                                        </div>';
                            echo '</li>';
                            //
                          }
                    }
                } else{
                   // _e(" Slider is empty");
                } 
                echo '</ul>';
                ?>
                <form action="" class="edit-plan engine-payment-form add-slider" id="add-slider">
                    <input class="et_ajaxnonce" name="et_ajaxnonce" type="hidden" value="<?php echo wp_create_nonce('add_slider'); ?>">
                    <input type="hidden" name="id" value="">
                    <div class="form add-slider">
                        <div class="form-item">
                            <div class="label"><?php _e("Add a new slider",ET_DOMAIN);?></div>
                            <input class="bg-grey-input not-empty" name="title" type="text" value=""  placeholder = "<?php _e("Please enter the slider's name",ET_DOMAIN); ?>" />

                        </div>
                        <div class="submit">
                            <button id="save_resume_playment_plan" class="btn-button engine-submit-btn">
                                <span><?php _e("Save",ET_DOMAIN);?></span><span class="icon" data-icon="+"></span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <?php
    }

	function et_sync_attachment(){
		$attach = $_POST['content'];
		$method = $_POST['method'];
		try{
			switch($method){
				case 'update':
					$args = array(
						'ID' => $attach['id'],
						'post_title' 	=> $attach['title'],
						'post_content' 	=> isset($attach['description']) ? $attach['description'] : '',
						);

					if(isset($attach['et_link']) && !empty($attach['et_link']))
						update_post_meta($attach['id'],'et_link',$attach['et_link']);
					if(isset($attach['attach_id']) && !empty($attach['attach_id']))
						update_post_meta($attach['id'],'_thumbnail_id',$attach['attach_id']);

					if(isset($attach['read_more']) && !empty($attach['read_more']))
						update_post_meta($attach['id'],'read_more',$attach['read_more']);

					$post_id = wp_update_post($args);

					if ( !($post_id instanceof WP_Error) )
						$response = array(
						'success' => true,
						'code' => 200,
						'msg' => __('Updated successfull!', ET_DOMAIN),
						'data' =>et_slider_response_return($post_id)

						);//$res  = array('success' => 'true','msg' =>'Updated successfull');
					else 
						$response = array('success' => 'false','msg' => 'Update false');

				break;
				case 'add':

					$args = array(
						'post_title' 	=> isset($attach['title']) ? $attach['title'] : '',
						'post_content' 	=> isset($attach['description']) ? $attach['description'] : '',
						'post_type'		=>'et_slider',
						'attach_id'		=> isset($attach['attach_id']) ? $attach['attach_id'] : 1,
						'et_link'		=> isset($attach['et_link']) ? $attach['et_link'] : 'http://',
						'read_more'		=> isset($attach['read_more']) ? $attach['read_more'] : __('Read more',ET_DOMAIN),
						'post_status'	=>'publish',
						'post_parent'	=> isset($attach['parrent']) ? $attach['parrent'] : 0
					);
					$response = array('success' => false,'msg' => __('Insert successfull!',ET_DOMAIN) );

					$slider = $this->et_insert_slider($args);
					if ( !($slider instanceof WP_Error) )
							$response = array(
							'success' => true,
							'code' => 200,
							'msg' => __('Insert attachment successfull!', ET_DOMAIN),
							'data'=>et_slider_response_return($slider)

							);//$res  = array('success' => 'true','msg' =>'Updated successfull');
					else
						$response = array('success' => false,'msg' => __('Insert false',ET_DOMAIN) );

				break;
				case 'delete' :
				wp_delete_post($attach['id']);
				$response = array(
					'success' 	=> true,
					'code' 		=> 200,
					'id_deleted'=>$attach['id'],
					'msg' 		=> __('Deleted successfull!', ET_DOMAIN));
				break;

				default:
					throw new Exception( __('An error has been occurred', ET_DOMAIN) );
					break;
			}

	} catch (Exception $e) {
		$response = array(
			'success' => false,
			'code' => 400,
			'msg' => $e->getMessage(),
			'data' => array()
		);
	}
		header( 'HTTP/1.0 200 OK' );
		header( "Content-Type: application/json" );
		echo json_encode( $response );
		exit;
	}

	public function et_insert_slider($args){

        $post_id = wp_insert_post($args);

        if ( is_wp_error($post_id) )
            return $post_id;
        if($post_id){
            update_post_meta($post_id,'et_link',$args['et_link']);
            update_post_meta($post_id,'_thumbnail_id',$args['attach_id']);
            update_post_meta($post_id,'read_more',$args['read_more']);

        }
        return $post_id;
    }

	/* view Slider Admin */

	public function et_slider_view($args){

		//$et_slider 	 = new ET_Slider();
		echo '<div id="et-slider">';
			if ( isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit') ){
				ET_Slider::add_view($args);
			} else 
				$this->show_list_slider_parent($args);
		echo'</div>';

	}
	/**
	 * Ajax sort attachment.
	 */	

	function on_sort_sliders(){
		parse_str( $_REQUEST['content']['order'] , $sort_order);

		// update new order
		global $wpdb;
		$sql = "UPDATE {$wpdb->posts} SET menu_order = CASE ID ";
		foreach ($sort_order['slide'] as $index => $id) {
			$sql .= " WHEN {$id} THEN {$index} ";
		}
		$sql .= " END WHERE ID IN (" . implode(',', $sort_order['slide']) . ")";
		$result = $wpdb->query( $sql );
		et_refresh_slider();

		echo json_encode(array(
			'success' 	=> $result,
			'msg' 		=> __('Slider have been sorted', ET_DOMAIN)
		));
		exit;
	}

}

function et_create_slider_response($plan){
	$plan = (array)$plan;
	return array(
		'id' 			=> $plan['ID'],
		'title' 		=> $plan['title'],
		'description' 	=> $plan['description'],
		'attach_url'	=> $plan['attach_url'],
		'et_link'		=> $plan['et_link'],
		'read_more'		=> $plan['read_more'],
		'backend_text' 	=> sprintf(__('to view jobseekers\' profiles in  days',ET_DOMAIN)),

	);
}

function et_refresh_slider(){
	global $et_global;

	$args = array(
	   'post_type' => 'et_slider',
	   'numberposts' => -1,
	   'post_status' => 'publish',
	   'orderby'	 => 'menu_order date',
	   'order'			=>'DESC',
	   'post_parent' => isset($_GET['id']) ? $_GET['id'] : 0
	  );

 	$attachments = get_posts( $args );


	$cache  	= array();

	foreach ($attachments as $att) {
		$et_link 	= get_post_meta($att->ID,'et_link',true);
		$read_more 	= get_post_meta($att->ID,'read_more',true);
		$att_id 	= get_post_meta($att->ID,'_thumbnail_id',true);
		$att_url 	= wp_get_attachment_image_src($att_id,'full');

		$new_att = new stdClass;
		$new_att->ID 			= $att->ID;
		$new_att->title 		= $att->post_title;
		$new_att->description 	= $att->post_content;
		$new_att->attach_url	= is_array($att_url) ? $att_url[0] : '';
		$new_att->read_more		= empty($read_more) ? __('Read more',ET_DOMAIN) : $read_more ;
		$new_att->et_link 		= !empty($et_link) ? esc_url($et_link) : 'http://';

		$cache[$att->ID] = (array)$new_att;
	}

	if ( !is_array($cache) ){
		$cache = false;
	}
	return $cache;
}

function et_slider_response_return($post_id){

	$att_id 	= get_post_meta($post_id,'_thumbnail_id',true);
	$att_url 	= wp_get_attachment_image_src($att_id,'full');
	$url_thumb 	= is_array($att_url) ? $att_url[0] : '';
	$et_link 	= get_post_meta($post_id,'et_link',true);
	$read_more 	= get_post_meta($post_id,'read_more',true);
	$post = get_post($post_id);
	return array(
		'id' 			=> $post_id,
		'title' 		=> $post -> post_title,
		'description' 	=> $post -> post_content,
		'et_link' 		=> $et_link,
		'parrent' 		=> $post->post_parent,
		'attach_id'	 	=> $att_id,
		'attach_url' 	=> $url_thumb,
		'read_more' 	=> $read_more
	);
}
?>