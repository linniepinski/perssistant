<?php

class JEP_Fields_Admin extends JEP_Fields_Init{

	CONST ACTION_ADMIN_INIT 		= 'admin_init';
	const ACTION_AJAX_DEL_FIELD 	= 'wp_ajax_et_delete_field';
	const ACTION_AJAX_SORT_FIELDS 	= 'wp_ajax_et_sort_fields';

	const ACTION_SAVE_JOB 			= 'je_save_job';

	const ACTION_META_BOX 			= 'et_job_meta_box';
	const ACTION_SAVE_POST 			= 'save_post';


	public function __construct(){
		parent::__construct();

		$this->add_action('et_admin_menu', 'add_admin_menu');

		$this->add_action('et_admin_enqueue_styles-et-job-fields', 'on_print_styles');
		$this->add_action('et_admin_enqueue_scripts-et-job-fields', 'on_print_scripts');

		$this->add_action(self::ACTION_ADMIN_INIT, 'on_handle_post');
		$this->add_action(self::ACTION_AJAX_DEL_FIELD, 'on_delete_field');
		$this->add_action(self::ACTION_AJAX_SORT_FIELDS, 'on_sort_fields');

		$this->add_action( self::ACTION_SAVE_JOB, 'on_save_job');

		$this->add_action( self::ACTION_META_BOX, 'job_meta_box' );
		$this->add_action(self::ACTION_SAVE_POST, 'on_save_post');

		$this->add_action('et_resume_meta_box', 'resume_meta_box');

		add_action('wp_ajax_resume_upload_file', array($this,'resume_upload_file') );
		//remove file
		add_action('wp_ajax_resume_romove_attachment', array($this,'resume_romove_attachment') );
		add_action('wp_ajax_mobile_upload', array($this,'mobile_upload') );

	}

	public function on_print_styles(){
		$this->add_style('job-fields-style', JEP_FIELD_URL . '/css/admin.css' );
		$this->add_existed_style( 'admin_styles' );
	}

	public function on_print_scripts(){

		//$this->add_existed_script('jquery');
		$this->add_existed_script('et_underscore');
		$this->add_existed_script('et_backbone');
		$this->add_existed_script('jquery-ui-sortable');

		if ( isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit') ){
			//$this->add_existed_script('jquery.validator' );
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'jquery_validator' );
			$this->add_script('job-field-script-add', JEP_FIELD_URL . '/js/admin_add.js', array('jquery' , 'et_underscore' , 'et_backbone' ) );
			wp_localize_script( 'job-field-script-add', 'et_fields', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'confirm_del' 	=> __('Are you sure you want to delete this option?', ET_DOMAIN)
			) );
		}
		else {
			$this->add_script('job-field-script', JEP_FIELD_URL . '/js/admin.js' , array('jquery' , 'et_underscore' , 'et_backbone' ));
			wp_localize_script( 'job-field-script', 'et_fields', array(
				'ajaxUrl' 		=> admin_url( 'admin-ajax.php' ) ,
				'nonceDelete' 	=> wp_create_nonce( 'delete_field' ),
				'nonceSort' 	=> wp_create_nonce( 'sort_field' )
			) );
		}
	}

	/**
	 * Add menu field
	 */
	public function add_admin_menu(){
		et_register_menu_section('et-job-fields',array(
			'menu_title' => __('JE Custom Fields',ET_DOMAIN),
			'page_title' => __('JE CUSTOM FIELDS',ET_DOMAIN),
			'page_subtitle' => __("Manage your job's additional information", ET_DOMAIN),
			'callback'   => array($this, 'menu_view'),
			'slug'          => 'et-job-fields',
			));
	}

	/**
	 * handle saving job
	 */
	public function on_save_job($job_id){
		if (empty($_REQUEST['content']['raw']) ) return;

		wp_parse_str( $_REQUEST['content']['raw'], $args);

		if (empty($args['cfield'])) return;
		$fields = $args['cfield'];
		JEP_Field::update_job_fields($job_id, $fields);

	}

	/**
	 * Display custom job fields in single job page
	 */
	public function on_single_job_fields($job){
		$fields = JEP_Field::get_all_fields();
		foreach ($fields as $field) {
			switch ($field->type) {
				case 'text':
				case 'date':
					echo '<p><strong>' . $field->name . ':</strong> ' . date(get_option("date_format"), get_post_meta( $job->ID, 'cfield-' . $field->ID, true ) ) . '</p>';
					break;
				case 'select':
					$option_id = (int)get_post_meta( $job->ID, 'cfield-' . $field->ID, true );
					$option = JEP_Field::get_field_option( $option_id );
					echo '<p><strong>' . $field->name . ':</strong> ' . $option->name . '</p>';
					break;
				default:
					break;
			}
		}
	}

	public function on_save_post($post_id){

		if(!isset($_POST['post_type']))
			return;
		$post_type = $_POST['post_type'];

		if($post_type == 'job'){
			if (!isset($_POST['_additional_nonce']) || !wp_verify_nonce( $_POST['_additional_nonce'], 'additional_fields' ))
				return;

			if (!current_user_can( 'manage_options' ))
				return;

			$fields = (array)$_POST['cfield'];
			JEP_Field::update_job_fields($post_id, $fields);
		} else if($post_type == 'resume'){

			if (!isset($_POST['_resume_additional_nonce']) || !wp_verify_nonce( $_POST['_resume_additional_nonce'], 'resume_additional_fields' ))
				return;
			$fields = (array)$_POST['resume_meta'];
			JEP_Field::update_resume_fields($post_id, $fields);
		}
	}

	/**
	 * [et_ajax_logo_upload description]
	 * upload thumbnail for resume field have type image
	 * @return [type] [description]
	 */
	function resume_upload_file(){

		global $user_ID;

		$res	= array(
			'success'	=> false,
			'msg'		=> __('There is an error occurred', ET_DOMAIN ),
			'code'		=> 400,
		);
		if(!isset($_POST['resume_id']) || !is_numeric($_POST['resume_id']) ){
			$post = get_posts( array('post_type' => 'resume','author' => $user_ID,'post_status' => 'any','posts_per_page'   => 1) );
			$_POST['resume_id'] = $post[0]->ID;
		}

		$this->check_permission_upload($_POST);
		// check fileID
		if(!isset($_POST['fileID']) || empty($_POST['fileID']) ){
			$res['msg']	= __('Missing image ID', ET_DOMAIN );
		} else {
			$fileID	= $_POST["fileID"];

			if(isset($_FILES[$fileID])){

				// for signup new resume
				$this->check_limit_files($_POST);

				$arg_type = $this->get_file_type_accept($_POST['field_type']);

				// handle file upload

				$attach_id	= et_process_file_upload( $_FILES[$fileID], $author=0, $_POST['resume_id'], $arg_type);

				if ( !is_wp_error($attach_id) ){

					$thumb			= et_get_attachment_data($attach_id);
					$thumb['id']	= $attach_id;
					$thumb['name'] = basename(wp_get_attachment_url( $attach_id));
					$thumb['url']  = wp_get_attachment_url( $attach_id);

					// Update the author meta with this logo
					update_post_meta($attach_id, $_POST['field_name'], 1);

					$res	= array(
						'success'	=> true,
						'msg'		=> __('Upload file has been uploaded successfully!', ET_DOMAIN ),
						'data'		=> $thumb
					);
				}
				else{
					$res['msg']	= $attach_id->get_error_message();
				}
			}
			else {
				$res['msg']	= __('Uploaded file not found', ET_DOMAIN);
			}

		}

		wp_send_json($res);
	}
	function mobile_upload(){
		global $user_ID;
		$resp = array('success' => false,'msg' => __('Upload file fail!',ET_DOMAIN) ,'data' => '');

		if(!isset($_GET['resume_id']) || !is_numeric($_GET['resume_id']) ){
			$post = get_posts( array('post_type' => 'resume','author' => $user_ID,'post_status' => 'any','posts_per_page'   => 1) );
			$_GET['resume_id'] = $post[0]->ID;
		}

		$this->check_permission_upload($_GET);

		if (isset($_FILES["file"]["error"]) && $_FILES["file"]["error"] > 0) {
		   wp_send_json(array('success'=>false, 'msg' => __('File size is too large!') ) );
		} else {

			// for signup new resume
		 	$this->check_limit_files($_GET);

		 	$arg_type = $this->get_file_type_accept($_GET['field_type']);

			$_FILES['files']['name'] 		= $_FILES['files']['name'][0];
			$_FILES['files']['type'] 		= $_FILES['files']['type'][0];
			$_FILES['files']['tmp_name'] 	= $_FILES['files']['tmp_name'][0];
			$_FILES['files']['size'] 		= $_FILES['files']['size'][0];
			$_FILES['files']['error'] 		= $_FILES['files']['error'][0];

		   	$attach_id	= et_process_file_upload( $_FILES['files'], $author=0, $_GET['resume_id'], $arg_type);

		   	if ( !is_wp_error($attach_id) ){

				update_post_meta($attach_id, $_GET['field_name'], 1);

			    $resp['success']		= true;
			   	$resp['id']			= $attach_id;
			   	$resp['temp'] 		= isset($_GET['field_type']) ? $_GET['field_type'] : 'file';
				$resp['name'] 		= basename(wp_get_attachment_url( $attach_id));
				$resp['url']  		= wp_get_attachment_url( $attach_id);

				$args_att 			= et_get_attachment_data($attach_id);
				$args_att['name'] 	= $resp['name'];

			   	$resp['files'][] 	= $args_att;
			   	$resp['msg'] 		=__('Upload file has successfully!',ET_DOMAIN);
			} else {
				// Size of file  upload > limit size.
				$resp['msg']	= $attach_id->get_error_message();
			}
	  }

		wp_send_json($resp);
	}

	function get_file_type_accept( $type ){

		$arg_type = array(
			'jpg|jpeg|jpe'	=> 'image/jpeg',
			'gif'			=> 'image/gif',
			'png'			=> 'image/png',
			'bmp'			=> 'image/bmp',
			'tif|tiff'		=> 'image/tiff'
		);

		if($type == 'file')
			$arg_type = array(
					'jpg|jpeg|jpe'	=> 'image/jpeg',
					'gif'			=> 'image/gif',
					'png'			=> 'image/png',
					'bmp'			=> 'image/bmp',
					'tif|tiff'		=> 'image/tiff',
					'RAR'			=> 'application/x-rar-compressed',
					'Zip' 			=> 'application/zip',
					'pdf' 			=> 'application/pdf',
					'docx' 			=> 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'doc' 			=> 'application/msword',
				);
		$arg_type = apply_filters('je_file_type_accept', $arg_type );

		return $arg_type;

	}

	function check_limit_files($args){

		$res 		= array();
		$limit_text = '';
		$attachs 	= get_posts( array('meta_key' => $args['field_name'],	'meta_value' => 1, 'post_parent'=>$args['resume_id'], 'post_status' => 'any', 'post_type' => 'attachment') );
		$limit_file = (int)get_post_meta( $args['field_id'],'limit_file',true);

		$limit_file = $limit_file == 0 ? 1 : $limit_file;
		if($limit_file == 1)
			$limit_text = $limit_file .' file.';
		else
			$limit_text = $limit_file .' files.';


		if(count($attachs) >=   $limit_file){
			$res['success'] = false;
			$res['limit_file'] = $limit_file;
			$resp['files'] 	= array();
			$res['msg'] 	= sprintf(__('Uploaded files are limited! You can’t upload more than %s!',ET_DOMAIN),$limit_text);
			wp_send_json($res);
		}

	}
	function check_permission_upload($args){
		global $user_ID;
		$post 		= get_post($args['resume_id']);
		$author_id 	= $post->post_author;
		if(!current_user_can('manage_options') && $user_ID != $post->post_author ){
			wp_send_json(array('success' => false,'msg'=>'You don\'t have permission to access file',ET_DOMAIN));
		}
		$user = get_user_by('id',$author_id);
		if( !in_array('jobseeker',$user->roles) ) {
			// in case user not a jobseeker- return false.
			wp_send_json(array('success'=> false,'msg' => __('Access denied',ET_DOMAIN) ) );
		}
	}

	/**
	 * [resume_romove_file description]
	 * remove file
	 * argument : _POST have key of array.
	 * @return [type] [description]
	 */
	public function resume_romove_attachment(){

		global $user_ID;
		$request 	= $_POST['content'];
		$post 		= get_post($request['resume_id']);

		$resp = array('succes' => false,'msg'=> __('Deleted file fail',ET_DOMAIN) );

		if( ($user_ID == $post->post_author) || current_user_can('manage_options') ) {
			wp_delete_post($request['id'],true);
			$resp = array('succes' => true,'msg'=> __('Deleted file success',ET_DOMAIN) );
		}
		wp_send_json($resp);
	}



	public function job_meta_box($post){
		//$this->add_style('jquery-ui', JEP_FIELD_URL . '/css/jquery-ui.css');
		?>
		<h4><?php _e('Additional information') ?>:</h4>
		<input type="hidden" name="_additional_nonce" value="<?php echo wp_create_nonce( 'additional_fields' ) ?>">
		<?php 
		$fields = JEP_Field::get_job_fields($post->ID) ;
		foreach ($fields as $field) {
			?>
			<p>
				<label for=""><strong><?php echo $field->name ?></strong></label> <br/>
				<?php 
				switch ($field->type) {
					default:
					case 'text':
						echo '<input type="text" name="cfield[' . $field->ID . ']" value="' . $field->value . '">';
						break;

					case 'date':
						if( $field->value === '' || strtotime($field->value) === 0 ) 
							$value = '';
						else
							$value = date( get_option('date_format'), strtotime($field->value) );

						echo '<input type="text" class="datepicker" name="cfield[' . $field->ID . ']" value="' . $value . '">';
						break;

					case 'select':
						$options = JEP_Field::get_options( $field->ID );
						echo '<select name="cfield[' . $field->ID . ']">';
						foreach ($options as $opt) {
							$selected = $opt->ID == $field->value ? 'selected="selected"' : '';
							echo '<option value="' . $opt->ID . '" '.$selected.'>' . $opt->name . '</option>';
						}
						echo '</select>';
						break;

				}
				?>
			</p>
			<?php
		}
		?>
		<?php
	}

	/**
	 * show meta box of resume when edit or add new a resume in back end.
	 * @param  array $resume has convert from function convert($post)
	 * @return display html in backend.
	 */
	function resume_meta_box($post){
		?>

		<h4><?php _e('Additional information Resume text') ?>:</h4>
		<input type="hidden" name="_resume_additional_nonce" value="<?php echo wp_create_nonce( 'resume_additional_fields' ) ?>">
		<?php
		$fields = JEP_Field::get_all_fields('resume'); ;
		foreach ($fields as $field) {

			$type = array('text','url','textarea');
			if(in_array($field->type, $type ) ){

				$content = get_post_meta( $post->ID, $field->name , true );
				if($field->type == 'url')
					$content = esc_url($content);
				echo '<p><label>'.$field->label.'</label> <br />';
				echo '<input type="text" name="resume_meta['.$field->name.']" value="' . $content . '">';
				echo '</p>';
			}

		}

	}

	/**
	 * Handle post
	 */
	public function on_handle_post(){
		$this->msg 				= isset($_COOKIE['et_field_msg']) ? $_COOKIE['et_field_msg'] : '';
		$this->current_section  = isset($_COOKIE['current_section']) ? $_COOKIE['current_section'] : 'job';
		setcookie("et_field_msg", "", time()-3600,'/');

		if (isset($_POST['_nonce']) && wp_verify_nonce( $_POST['_nonce'], 'add_field' ) ){
			$this->on_add_field();
		} else if ( isset($_POST['_nonce']) && wp_verify_nonce( $_POST['_nonce'], 'edit_field' ) ) {
			$this->on_edit_field();
		}
	}

	/**
	 * Handle post to add field
	 */
	protected function on_add_field(){
		// add field
		$args = array(
			'name' 			=> $_POST['name'],
			'type' 			=> $_POST['type'],
			'desc' 			=> $_POST['desc'],
			'options' 		=> $_POST['options'],
			'required' 		=> $_POST['required']
			);


		if(isset($_POST['resume_fields'])) {
			$args['name']			=	trim(strtolower($args['name']));
			$args['resume_fields']	=	1;
			$args['field_slug']		=	$_POST['field_slug'];
			$args['field_label']	=	$_POST['field_label'];
			$args['limit_file'] 	=	isset($_POST['limit_file']) ? $_POST['limit_file'] : 1;
		}
		if ($result = JEP_Field::insert_field($args)){
			if(!isset($_POST['resume_fields']))
				setcookie('et_field_msg', __('Your new field has been inserted to Post a Job form', ET_DOMAIN), time() + 3600, '/');
			else {
				setcookie('et_field_msg', __('Your new field has been inserted to Resume form', ET_DOMAIN), time() + 3600, '/');
				setcookie('current_section', 'resume' , time() + 3600, '/');
			}
			wp_redirect( remove_query_arg( array('action','id' , 'type') ));
		}
	}

	/**
	 * handle post to edit field
	 */
	protected function on_edit_field(){
		$args = array(
			'ID' 		=> $_POST['ID'],
			'name' 		=> $_POST['name'],
			'type' 		=> $_POST['type'],
			'desc' 		=> $_POST['desc'],
			'options' 	=> $_POST['options'],
			'required' 	=> $_POST['required'],

			);

		if(isset($_POST['resume_fields'])) {
			$args['resume_fields']	=	1;
			$args['field_slug']		=	$_POST['field_slug'];
			$args['field_label']	=	$_POST['field_label'];
			$args['limit_file'] 	=	isset($_POST['limit_file']) ? $_POST['limit_file'] : 1;
		}

		if ($result = JEP_Field::update_field($args['ID'], $args)){
			setcookie('et_field_msg', __('Field has been updated', ET_DOMAIN), time() + 3600, '/');

			if(isset($_REQUEST['resume_fields'] ) ) {
				setcookie('current_section', 'resume' , time() + 3600, '/');
			}

			wp_redirect( remove_query_arg( array('action','id' , 'type' ) ));
		}
	}

	/**
	 * Ajax handle: delete field
	 */
	public function on_delete_field(){
		$postData  	= $_POST['content'];
		try {
			if ( isset($postData['_nonce']) && wp_verify_nonce( $postData['_nonce'], 'delete_field' ) ){

				if ( !current_user_can( 'manage_options' ) ) throw new Exception(__('You dont have permission to perform this action', ET_DOMAIN));

				if ( isset($postData['id']) ){
					if ( JEP_Field::delete_field($postData['id']) ){
						$response = array('success' => true);
					} else {
						throw new Exception(__("Can't delete field", ET_DOMAIN));
					}
				} else {
					throw new Exception(__('Field id doesnt exist', ET_DOMAIN));
				}
			}
		} catch (Exception $e) {
			$response = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage()
			);
		}

		wp_send_json($response);

	}

	public function on_sort_fields(){
		$postData = $_POST['content'];
		try {
			if ( !current_user_can( 'manage_options' ) )
				throw new Exception(__("You don't have permission to perform this action", ET_DOMAIN));
			if ( isset($postData['data']) ){
				parse_str($postData['data'], $pos);
				if (JEP_Field::sort_fields( array_values($pos['item'])))
					$reponse = array(
						'success' => true
						);
				else
					throw new Exception(__('Cannot sort', ET_DOMAIN));
			}
		} catch (Exception $e) {
			$reponse = array(
				'success' => false,
				'msg' => $e->getMessage()
				);
		}
		wp_send_json( $response );
	}

	/**
	 * LIST VIEW
	 */
	public function menu_view($args){
		if ( isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit') ){
			$this->add_view($args);
			return;
		}
		?>
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->menu_title ?></div>
			<div class="desc"><?php // echo $args->page_subtitle ?></div>
		</div>
		<div class="et-main-content" id="fields-list" >
			<div class="et-main-left">
				<ul class="et-menu-content inner-menu">
					<li>
						<a href="#job-fields-list" menu-data="general" class="<?php if($this->current_section == 'job' ) echo 'active'; ?>" >
							<span class="icon" data-icon="l"></span><?php _e("Job", ET_DOMAIN); ?>						</a>
					</li>

					<li>
						<a href="#resume-fields-list" menu-data="general" class="<?php if($this->current_section == 'resume' ) echo 'active'; ?>" >
							<span class="icon" data-icon="N"></span><?php _e("Resume", ET_DOMAIN); ?>						</a>
					</li>

				</ul>
			</div>
			<div class="et-main-main  clearfix inner-content" id="job-fields-list" <?php if($this->current_section != 'job' ) echo 'style="display:none;"'; ?>>
				<?php if (!empty($this->msg ) && false ){
					echo '<div class="updated"><p>' . $this->msg . '</p></div>';
				} ?>
				<div class="title font-quicksand">
					<a href="<?php $link = add_query_arg('action', 'add'); echo remove_query_arg( 'type' , $link );  ?>" class="new-link"><?php _e('New', ET_DOMAIN)?></a>
					<?php _e('Job fields list', ET_DOMAIN) ?>
				</div>
				<div class="desc">
					<ul class="ordered-list sortable" id="lst_fields">
						<?php
						$fields = JEP_Field::get_all_fields('job');
						foreach ($fields as $field) { ?>
							<li class="item item-<?php echo $field->ID ?>" id="item_<?php echo $field->ID ?>">
								<div class="sort-handle"></div>
								<span><strong><?php echo $field->name ?></strong> <?php if ($field->required) echo '(' . __('required', ET_DOMAIN) . ')' ?></span>  
								<div class="actions">
									<a href="<?php echo add_query_arg(array('action' => 'edit', 'id' => $field->ID)) ?>" title="<?php _e('Edit', ET_DOMAIN) ?>" class="icon act-edit" data-icon="p"></a>
									<a href="#" title="<?php _e('Delete', ET_DOMAIN) ?>" data="<?php echo $field->ID?>" class="icon act-del" data-icon="D"></a>
								</div>
							</li>
						<?php } ?>

					</ul>
				</div>
			</div>
			<div class="et-main-main  clearfix inner-content" id="resume-fields-list" <?php if($this->current_section != 'resume' ) echo 'style="display:none;"'; ?>>
				<div class="title font-quicksand">
					<a href="<?php echo add_query_arg( array('action' => 'add' , 'type' => 'resume') ) ?>" class="new-link"><?php _e('New', ET_DOMAIN)?></a>
					<?php _e('Resume fields list', ET_DOMAIN) ?>
				</div>
				<div class="desc">
					<ul class="ordered-list sortable" id="resume_lst_fields">
						<?php
						$fields = JEP_Field::get_all_fields('resume');
						foreach ($fields as $field) { ?>
							<li class="item item-<?php echo $field->ID ?>" id="item_<?php echo $field->ID ?>">
								<div class="sort-handle"></div>
								<span><strong><?php echo $field->label ?></strong> <?php if ($field->required) echo '(' . __('required', ET_DOMAIN) . ')' ?></span>  
								<div class="actions">
									<a href="<?php echo add_query_arg(array('action' => 'edit', 'id' => $field->ID , 'type' => 'resume' )) ?>" title="<?php _e('Edit', ET_DOMAIN) ?>" class="icon act-edit" data-icon="p"></a>
									<a href="#" title="<?php _e('Delete', ET_DOMAIN) ?>" data="<?php echo $field->ID?>" class="icon act-del" data-icon="D"></a>
								</div>
							</li>
						<?php } ?>
						<!-- <li class="item" data="">
							<div class="sort-handle"></div>
							<span><strong>Soft Skill</strong> (required)</span>
							<div class="actions">
								<a href="#" title="<?php _e('Edit', ET_DOMAIN) ?>" class="icon act-edit" rel="<?php echo $plan['ID'] ?>" data-icon="p"></a>
								<a href="#" title="<?php _e('Delete', ET_DOMAIN) ?>" class="icon act-del" rel="<?php echo $plan['ID'] ?>" data-icon="D"></a>
							</div>
						</li> -->
					</ul>
				</div>
			</div>
		</div>
		<?php
	}

	public function add_view($args){
		if (isset($_GET['id']) )
			$field = JEP_Field::get_field($_GET['id']);
		else 
			$field = (object) array(
				'name' 		=> '',
				'desc' 		=> '',
				'type' 		=> 'text',
				'options' 	=> array(),
				'required' 	=> false ,
				'slug'		=> '',
				'label'		=> ''
			);

		?>
		<div class="et-main-header">
			<div class="title font-quicksand"><?php echo $args->menu_title ?></div>
			<?php if(!isset($_REQUEST['type'])) {
			?>
				<div class="desc"><?php _e("Add a new job information field in Post a Job page.", ET_DOMAIN) ?></div>
			<?php
			} else {
			?>
				<div class="desc"><?php _e("Add a new resume information field in Jobseeker Signup page.", ET_DOMAIN) ?></div>
			<?php
			} ?>

		</div>
		<div class="et-main-main et-main-full no-menu clearfix inner-content" id="job-fields-add">
			<div class="title font-quicksand">
				<a href="<?php echo remove_query_arg( array('action','id') ) ?>" class="back-link"> <span class="icon" data-icon="["></span>&nbsp;&nbsp;<?php _e('Back to custom fields list', ET_DOMAIN)?></a>
				<?php if (isset($_GET['id'])) {
					_e('Edit a field', ET_DOMAIN);
				} else {
					_e('Add a new field', ET_DOMAIN);
				}
				?>
			</div>
			<div class="desc">
				<form action="" method="post" id="fadd" class="et-form<?php if(isset($_GET['type'])) echo ' form-resume'; ?>">
					<?php
					if(isset($_REQUEST['type'])) {
						echo '<input type="hidden" name="resume_fields" value="1">';
					}
					if ( isset($field->ID) )
						echo '<input type="hidden" name="ID" value="' . $field->ID . '">';
					if ( $_GET['action'] == 'add' )
						echo '<input type="hidden" name="_nonce" value="' . wp_create_nonce( 'add_field' ) . '">';
					else if ($_GET['action'] == 'edit')
						echo '<input type="hidden" name="_nonce" value="' . wp_create_nonce( 'edit_field' ) . '">';

					//field for resume  -->
					if(isset($_REQUEST['type'])) {  ?>
						<div class="form-item">
							<label for="name"><?php _e("Field Label", ET_DOMAIN) ?></label>
							<input required type="text" class="field_label" name="field_label" placeholder="<?php _e('Enter a field label', ET_DOMAIN) ?>" value="<?php echo $field->label ?>">
						</div>
					<?php } ?>

					<div class="form-item">
						<label for="name"><?php _e("Field name", ET_DOMAIN) ?></label>
						<input id="field_name" <?php if ( isset($field->ID) && isset($_REQUEST['type'])  ) echo 'readonly="readonly"'; ?> required type="text" name="name" placeholder="<?php _e('Enter a field name', ET_DOMAIN) ?>" value="<?php echo $field->name ?>">
					</div>


					<?php if(isset($_REQUEST['type'])) {  ?>

						<div class="form-item">
							<label for="name"><?php _e("Field Slug", ET_DOMAIN) ?></label>
							<input required type="text" name="field_slug" placeholder="<?php _e('Enter a field slug', ET_DOMAIN) ?>" value="<?php echo $field->slug ?>">
						</div>
					<?php } ?>


					<div class="form-item">
						<label for="name"><?php _e("Field Description", ET_DOMAIN) ?></label>
						<textarea name="desc" placeholder="<?php _e("Enter field's description", ET_DOMAIN) ?>" cols="30" rows="10"><?php echo $field->desc ?></textarea>
					</div>
					<div class="form-item">
						<label for="name"><?php _e("Field type", ET_DOMAIN) ?></label>
						<span class="cb-field">
							<input id="field-text" type="radio" name="type" value="text" <?php echo $field->type == 'text' ? 'checked="checked"' : '' ?>>
							<label class="field-type field-text" for="field-text"><?php _e('Text',ET_DOMAIN);?></label>
						</span>

						<span class="cb-field">
							<input id="field-url" type="radio" name="type" value="url" <?php echo $field->type == 'url' ? 'checked="checked"' : '' ?>>
							<label for="field-url" class="field-type field-text"><?php _e('Url',ET_DOMAIN);?></label>
						</span>

						<!-- radio !-->
						<span class="cb-field">
							<input id="field-radio" type="radio" name="type" value="radio" <?php echo $field->type == 'radio' ? 'checked="checked"' : '' ?>>
							<label for="field-radio" class="field-type field-text"><?php _e('Radio',ET_DOMAIN);?></label>
						</span>
						<!-- end radio !-->
						<span class="cb-field">
							<input id ="field-drop" type="radio" name="type" value="select" <?php echo $field->type == 'select' ? 'checked="checked"' : '' ?>>
							<label for="field-drop" class="field-type field-text"><?php _e('Drop',ET_DOMAIN);?></label>
						</span>

						<?php if(isset($_REQUEST['type'])) {  ?>
						<span class="cb-field">
							<input id="field-checkbox" type="radio" name="type" value="checkbox" <?php echo $field->type == 'checkbox' ? 'checked="checked"' : '' ?>>
							<label for="field-checkbox" class="field-type field-text"><?php _e('Checkbox',ET_DOMAIN);?></label>
						</span>

						<span class="cb-field">
							<input id ="field-textarea" type="radio" name="type" value="textarea" <?php echo $field->type == 'textarea' ? 'checked="checked"' : '' ?>>
							<label for="field-textarea" class="field-type field-text"><?php _e('Textarea',ET_DOMAIN);?></label>
						</span>

						<span class="cb-field">
							<input id="field-multi" type="radio" name="type" value="multi-text" <?php echo $field->type == 'multi-text' ? 'checked="checked"' : '' ?>>
							<label for="field-multi" class="field-type field-text"><?php _e('Multivalue Text',ET_DOMAIN);?></label>
						</span>

						<span class="cb-field">
							<input id ="field-image" type="radio" name="type" value="image" <?php echo $field->type == 'image' ? 'checked="checked"' : '' ?>>
							<label for="field-image" class="field-type field-text"><?php _e('Image',ET_DOMAIN);?></label>
						</span>
						<span class="cb-field">
							<input id ="field-file" type="radio" name="type" value="file" <?php echo $field->type == 'file' ? 'checked="checked"' : '' ?>>
							<label for="field-file" class="field-type field-text"><?php _e('File',ET_DOMAIN);?></label>
						</span>
						<?php } ?>
						<span class="cb-field">
								<input id="field-date" type="radio" name="type" value="date" <?php echo $field->type == 'date' ? 'checked="checked"' : '' ?>>
								<label for="field-date" class="field-type field-text"><?php _e('Date',ET_DOMAIN);?></label>
						</span>

						<?php
						if(isset($_REQUEST['type'])  ){

							$class = 'hide';
							$limit_file = 1;
							if( $field->type == 'file' || $field->type =='image' ){
								$limit_file = get_post_meta($_GET['id'],'limit_file',true);
								$class = '';
							}
							?>
							<span class="cb-field file-limit <?php echo $class;?>">
								<span><?php _e('Limit number of files',ET_DOMAIN);?></span>
								<input id ="field-file" size="3" style="width:30px; height:20px;" value="<?php echo $limit_file;?>" placeholder="<?php echo $limit_file;?>" type="text" name="limit_file" >

							</span>
							<?php
						}
						?>

					</div>
					<?php

					if(!isset($_REQUEST['type'])) { ?>
					<div class="form-item form-drop" <?php if( !in_array( $field->type, array('select','checkbox','radio') ) ) 	echo 'style="display:none"'; ?>>
						<label for="option"></label>
						<ul class="form-options">
							<?php
							$count = 0;
							if ( $field->type == 'select' || $field->type == 'checkbox' || $field->type =='radio' )
								$options = JEP_Field::get_options($field->ID);
							else
								$options = array();

							if ( !empty($options) ) {
								foreach ($options as $option) {?>
									<li class="form-option">
										<input type="hidden" name="options[<?php echo $count ?>][id]" value="<?php echo $option->ID ?>">
										<input type="text" name="options[<?php echo $count ?>][name]" value="<?php echo $option->name ?>">
										<div class="controls controls-2">
											<a class="button act-open-form del-opt" rel="33" title="<?php _e('Delete this option') ?>">
												<span class="icon" data-icon="*"></span>
											</a>
										</div>
									</li>
									<?php
									$count++;
								}
							} ?>
							<li class="form-option">
								<input type="text" name="options[<?php echo $count ?>]">
								<div class="controls controls-2">
									<a class="button act-open-form del-opt" rel="33" title="<?php _e('Delete this option') ?>">
										<span class="icon" data-icon="*"></span>
									</a>
								</div>
							</li>
						</ul>
						<script type="text/template" id="tl_option">
							<li class="form-option">
								<input type="text" name="options[{{ id }}]">
								<div class="controls controls-2">
									<a class="button act-open-form" title="<?php _e('Delete this option') ?>">
										<span class="icon" data-icon="*"></span>
									</a>
								</div>
							</li>
						</script>
					</div>
					<div class="form-item">
						<label for="required"><?php _e('Required') ?></label>
						<input type="hidden" name="required" value="0">
						<input type="checkbox" name="required" value="1" id="required" <?php if( $field->required ) echo 'checked="checked"' ?> > 
						<label for="required" style="display:inline;" >
							<?php _e("Check this if field is required", ET_DOMAIN); ?>
						</label>
					</div>
					<?php } else { echo '<input type="hidden" value="" name="options" />'; } ?>
					<input type="submit" class="et-button btn-button load-more" value="<?php _e('Save',ET_DOMAIN);?>">
				</form>
			</div>
		</div>
		<?php
	}

};