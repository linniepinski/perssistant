<?php
set_theme_mod( 'je_show_active_company' , false );
class JEP_Fields_Front extends JEP_Fields_Init{

	const ACTION_POST_JOB = 'et_post_job_fields';
	const ACTION_EDIT_JOB = 'et_edit_job_fields';

	const ACTION_PRINT_SCRIPT 	= 'et_enqueue_scripts';
	const ACTION_PRINT_STYLE 	= 'wp_print_styles';
	const ACTION_SINGlE_JOB_FIELDS = 'je_single_job_fields';

	public function __construct(){
		parent::__construct();
		$this->add_action( self::ACTION_INIT, 'on_init');
		//$this->add_action('je_update_resume','je_insert_resume_field', 10000);
	}

	public function on_init(){
		$this->add_action( self::ACTION_POST_JOB, 'on_post_job_fields' );
		$this->add_action( self::ACTION_EDIT_JOB, 'on_edit_job_fields' );
		$this->add_action( self::ACTION_PRINT_SCRIPT, 'on_enqueue_scripts', 20 );
		$this->add_action( self::ACTION_PRINT_STYLE, 'on_enqueue_styles', 100);

		$this->add_action( self::ACTION_SINGlE_JOB_FIELDS, 'on_single_job_fields');

		// action in page-jobseeker-signup form. step 2
		$this->add_action( 'je_jobseeker_post_resume_form' , 'on_post_resume_fields' );

		// show resume fields on detail resume(single-resume.php).
		$this->add_action( 'je_jobseeker_edit_resume_form' , 'on_edit_resume_fields', 10, 3 );

		/*
		* Save radiooption for action create resume.
		*/

		//je_jobseeker_edit_info_field

		// update for mobile version.
		$this->add_action('et_mobile_head','et_mobile_head_fields');
		$this->add_action('et_mobile_footer','et_mobile_footer_fields',1);
		$this->add_action('je_mobile_job_post_form_fields','je_mobile_show_fields');

		// update meta field.
		$this->add_action('et_insert_job','et_insert_job_fields');

		// form register resume- step 2 of mobile.
		$this->add_action('je_resume_add_fields','je_resume_add_fields');
		//dissplay resume info in singl-resume.php file
		$this->add_action('je_resume_show_fields_on_detail','je_resume_show_fields_on_detail');
		// get list fields when edit_education
		$this->add_action('je_resume_edit_form','je_resume_edit_form');

		/*
		*/
		$this->add_action("wp_footer","je_field_footer_trigger", 12);
		$this->add_action("et_mobile_footer","je_field_footer_trigger", 12);

	}

	public function on_enqueue_scripts(){
		if (is_page_template( 'page-post-a-job.php' ) ||
			is_home() ||
			is_post_type_archive('job') ||
			is_tax('job_category') ||
			is_tax('job_type') ||
			is_singular( 'job' ) ||
			is_page_template( 'page-dashboard.php' ) ){

			//wp_enqueue_script('plupload-all');
			$this->add_existed_script( 'jquery-ui-datepicker' );
			$this->add_script( 'field-post-a-job', JEP_FIELD_URL . '/js/front.js', array('jquery', 'jquery-ui-datepicker') , '1.1.4');

			wp_localize_script( 'field-post-a-job', 'jep_field', array(
				'dateFormat' => $this->convert_php_date_format(get_option('date_format')),

				) );
		}

		if(is_singular( 'resume' ) ) {
			$this->add_script( 'resume-view', JEP_FIELD_URL . '/js/resume-view.js', array('jquery','et-underscore', 'et-backbone', 'job_engine' , 'jquery-ui-datepicker') , '1.1.4');
		}
		if(is_page_template("page-jobseeker-signup.php") || is_singular('resume') ){
			//wp_enqueue_script('plupload-all')
			$this->add_existed_script('jquery-ui-datepicker');
			$this->add_script( 'field-post-resume', JEP_FIELD_URL . '/js/file_upload.js', array('jquery','et-underscore', 'et-backbone', 'job_engine','plupload-all' ) );
			wp_localize_script('field-post-resume','reumse_field',array( 'file_maximum' 	=> __('File exceeds maximum allowed size of',ET_DOMAIN) ) );
			wp_enqueue_style('style-field-resume',JEP_FIELD_URL.'/css/resume.css');
		}
	}

	private function convert_php_date_format($df){
		$replace = array(
			'd' => 'dd', // two digi date
			'j' => 'd', // no leading zero date
			'm' => 'mm', // two digi month
			'n' => 'm', // no leading zero month
			'l' => 'DD', // date name long
			'D' => 'D', // date name short
			'F' => 'MM', // month name long
			'M' => 'M', // month name shỏrt
			'Y' => 'yy', // 4 digits year
			'y' => 'y',
		);
		$return = str_replace( array_keys($replace) , array_values($replace), $df);
		return $return;
	}

	public function on_enqueue_styles(){
	?>
		<style type="text/css">
			.form-item .input-date{
				position: relative;
			}
			.form-item .input-date .icon.icon-date{
				position: absolute;
				top: 13px;
				left: 10px;
				right: 0;
				width: 10px;
			}
			.form-item .input-date > input{
				padding-left: 30px;
				width: 250px !important;
			}
			.form-item .input-date .icon[data-icon="!"]{
				right: 200px;
			}

			.modal-job .form-item .error.input-date input{
				width: 240px;
			}
			.modal-job .form-item .error.input-date .icon[data-icon="!"]{
				right: 245px;
			}
			.post-a-job .form-item .error {
				width: 485px;
			}
			#resume_file_thumbnail{
				width: 100%;
				display: block;
				overflow: hidden;
			}
			span.file-item{
				padding: 3px;
				white-space:nowrap;
				text-overflow: ellipsis;
				float: left;
				position: relative;
			}
			span.thumb-item img{
				max-width: 150px;
				max-height: 150px;
			}

			#resume_file_thumbnail span.file-url{
				position: relative;
			}
			.inline-edit .form-item-field div{
				margin-left: 0;
			}
			span.file-item:hover span{
				opacity: 1 !important;
			}
			span.delete{

				opacity: 0;
				height: 15px;
				z-index: 10;
				display: inline-block;

			}
			span.delete:hover{
				background: #ccc;
				opacity: 0.8;
			}

			span.delete a{
				cursor: pointer;
				position: relative;
				color: # 000!important;
				background: #ccc;
				padding:0 3px;
			}
			span.delete  a:hover{
				opacity: 1 !important;
			}
			span.delete .icon:before{
				color: #000 !important;
				content: "#" !important;
			}
			span.thumb-item{
				position: relative;
				display: inline-block;
				margin-right: 10px;
			}
			span.thumb-item a.del-thumbnail{

				position: absolute;
				top:-10px;
				cursor: pointer;
				right:-10px;
				z-index: 10;
			}
			span.thumb-item:hover span.delete,
			span.thumb-item:hover a.del-thumbnail{
				opacity: 1 !important;
			}
			.form-item-field .input-file .button-upload {
			    cursor: pointer;
			    display: block;
			    font-weight: bold;
			    height: 20px;
			    margin-top: 5px;
			    padding: 10px 40px 10px 15px;
			    width: 85px !important;
			}
			.field-thumb{
				overflow: hidden;
				padding-top: 20px;
				display: block;
			}
			.row-checkbox {
				position: relative;
				padding-bottom: 25px;
			}
			.row-checkbox .message{
				position: absolute;
				bottom: -15px;
			}
			.post-a-job .form-item .row-checkbox   input{
				width: 30px;
			}
			.post-a-job .form-item .row-checkbox   input,
			.post-a-job .form-item .row-checkbox   label{
				display: inline;
				padding: 0 5px;
			}
			.post-a-job  .form-item .row-checkbox span.icon{
				bottom: -5px;
				top: auto;
				display: none;
			}
			.je-field-radio .jse-input{
				width: 100px;
			}
			.jse-radio input[type=radio]{
				width: 20px !important;
			}
			.je-radio-value{ font-weight: bold; padding-top: 15px;}
			.post-a-job .je-field-radio .error input{
				width: 10px;
			}
			.post-a-job .je-field-radio .error .message{
				position: absolute;
			}
		</style>
	<?php
		if (is_page_template( 'page-post-a-job.php' ) || 

			is_home() ||
			is_post_type_archive('job') ||
			is_tax('job_category') ||
			is_tax('job_type') ||
			is_singular( 'job' ) ||
			is_page_template( 'page-dashboard.php' ) ){
			global $wp_scripts, $post;
				$home_url	=	home_url();
				$http		=	substr($home_url, 0,5);
				if($http != 'https') {
					$http	=	'http';
				}
				$ui = $wp_scripts->query('jquery-ui-core');
		        $url = $http."://code.jquery.com/ui/{$ui->ver}/themes/smoothness/jquery-ui.theme.css";
		       // wp_enqueue_style('jquery-ui-redmond', $url, false, $ui->ver);
			}
	}

	public function on_single_job_fields($job){
		$fields = JEP_Field::get_all_fields();

		foreach ($fields as $field) {

			$label = $field->name;
			$value = get_post_meta( $job->ID, 'cfield-'. $field->ID, true );
			switch ($field->type) {
				case 'select':
					$options = JEP_Field::get_options($field->ID);
					if(empty($options)) break;
					foreach ($options as $option) {
						if ($option->ID == $value){
							echo '<p><strong>' . $label . '</strong>: ' . $option->name . '</p>';
							break;
						}
					}
					break;
				/*
				* radio from version 2.1.4
				*/
				case 'radio':
					$options = JEP_Field::get_options($field->ID);
					if(empty($options)) break;
					foreach ($options as $option) {
						if ($option->ID == $value){
							echo '<p><strong>' . $label . '</strong>: ' . $option->name . '</p>';
							break;
						}
					}
					break;

				case 'date':
					if(strtotime($value) == 0 ) break;
					$date = date( get_option('date_format'), strtotime($value));
					echo '<p><strong>' . $label . '</strong>:  ' .  $date . '</p>';
					break;
				case 'url' :
					if($value == '') break;
					echo '<p><strong>' . $label . '</strong>: <a href="'.esc_url($value).'" target="_blank" rel="nofollow" title="'.$label.'"> ' . $value . '</a></p>';
					break;
				case 'text':
				default:
					if($value == '') break;
					echo '<p><strong>' . $label . '</strong>: ' . $value . '</p>';
					break;
			}

		}
	}

	/**
	 * Display field on page post job.
	 */
	public function on_post_job_fields(){
		$job_id	=	get_query_var( 'job_id' );

		$fields = JEP_Field::get_all_fields();
		foreach ($fields as $field) {
			$name 		= 'cfield['.$field->ID.']';
			$required 	= $field->required ? 'input-required required' : ''; ?>
			<div class="form-item je-field-item je-field-<?php echo $field->type;?>">
				<div class="label">
					<h6 class="font-quicksand"><?php echo $field->name ?></h6>
					<?php echo $field->desc ?>
				</div>
				<?php 
				if($job_id ) {
					$value = get_post_meta( $job_id, 'cfield-'.$field->ID , true);
				} else $value = '';
				switch ($field->type) {
					case 'select':
						?>
						<div class="select-style btn-background border-radius">
							<select class="input-field <?php echo $required ?>" name="cfield[<?php echo $field->ID ?>]" >
								<?php
								$options = JEP_Field::get_options($field->ID);
								foreach ($options as $option) {
									if($option->ID == $value ) $checked = 'selected="selected"';
									else $checked	=	'';
									echo '<option '.$checked.' value="' . $option->ID . '">' . $option->name . '</option>';
								} ?>
							</select>
						</div>
						<?php 
						break;

					case 'checkbox':

						$options = JEP_Field::get_options($field->ID);

						if(!empty($options)){
								echo '<div class="row row-checkbox">';
								foreach ($options as $option) {
									$checked = ($option->ID == $value) ? 'checked="checked"' : '';
									echo '<input name="cfield['.$field->ID.'][]" class="'.$required.'" '.$checked.' type="checkbox" id = "'.$option->ID.'" value="' . $option->ID . '" /><label for ="'.$option->ID.'">' . $option->name. '</label>';
								}
							echo '</div>';
						}
						break;
					/*
					* version 2.3.4
					 */
					case 'radio':
						$options = JEP_Field::get_options($field->ID);
						if(!empty($options))
							echo '<div class="row row-radio">';
							foreach ($options as $option) {
								$checked = ($option->ID == $value) ? 'checked="checked"' : '';
								echo '<input name="cfield['.$field->ID.']" class="'.$required.'" '.$checked.' type="radio" id = "'.$option->ID.'" value="' . $option->ID . '" /><label for ="'.$option->ID.'">' . $option->name. '</label> &nbsp; ';
							}
						echo '</div>';
						break;


					case 'text' :
						?>
						<div>
							<input type="text" class="bg-default-input input-field <?php echo $required ?>" name="cfield[<?php echo $field->ID ?>]" value="<?php echo $value ?>" />
						</div>
						<?php
						break;

					case 'url' :
						?>
						<div>
							<input type="text" class="bg-default-input input-field input-url <?php echo $required ?>" name="cfield[<?php echo $field->ID ?>]" value="<?php echo $value ?>" />
						</div>
						<?php
						break;

					case 'date' :
						if( $value != '' ) {
						?>
							<div class="input-date">
								<div class="icon icon-date" data-icon="\"></div>
								<input type="text" class="bg-default-input input-field <?php echo $required ?>" name="cfield[<?php echo $field->ID ?>]" value="<?php echo date( get_option('date_format'), strtotime($value)); ?>" />
							</div>
						<?php 
						} else {
						?>
							<div class="input-date">
								<div class="icon icon-date" data-icon="\"></div>
								<input type="text" class="bg-default-input input-field <?php echo $required ?>" name="cfield[<?php echo $field->ID ?>]" value="<?php echo $value; ?>" />
							</div>
						<?php
						}

						break;

					default:
						# code...
						break;
				}
				?>
			</div>
			<input type="hidden" id="current_date_field" value="<?php echo date( get_option('date_format'),time());?>" />
		<?php } ?>
		<?php
	}

	// popup job edit.
	public function on_edit_job_fields(){
		$fields = JEP_Field::get_all_fields();
		foreach ($fields as $field) {
			$name 		= 'cfield['.$field->ID.']';
			$required 	= $field->required ? 'input-required required' : '';
			?>
			<div class="form-item">
				<div class="label">
					<h6><?php echo $field->name ?></h6>
				</div>
				<?php switch ($field->type) {
					case 'text': ?>
						<div><input type="text" class="bg-default-input input-field <?php echo $required ?> cfield-<?php echo $field->ID ?>" name="<?php echo $name ?>"></div>
					<?php
						break;
					case 'url': ?>
						<div><input type="text" class="bg-default-input input-field input-url <?php echo $required ?> cfield-<?php echo $field->ID ?>" name="<?php echo $name ?>"></div>
					<?php
						break;

					case 'date': ?>
						<div class="input-date">
							<div class="icon icon-date" data-icon="\"></div>
							<input type="text" class="bg-default-input input-field cfield-<?php echo $field->ID ?> <?php echo $required ?>" name="<?php echo $name ?>">
						</div>
					<?php
						break;

					case 'select':
						$options = JEP_Field::get_options($field->ID);
						?>
						<div class="select-style btn-background border-radius">
							<select class="input-field <?php echo $required ?> cfield-<?php echo $field->ID ?>" name="<?php echo $name ?>">
								<?php foreach ($options as $option) {
									echo '<option value="' . $option->ID . '">'. $option->name .'</option>';
								} ?>
							</select>
						</div>
					<?php
						break;
					/*
					* version 2.1.4
					 */
					case 'radio':
						$options = JEP_Field::get_options($field->ID);
						if(!empty($options)){
							foreach ($options as $option) {
										echo '<input name="'.$name.'" id="radio-'.$option->ID.'" class="cfield-'.$field->ID.' radio-'.$field->ID.'" type ="radio" value="' . $option->ID . '"/><label for="radio-'.$option->ID.'">'. $option->name.'</label> &nbsp; ' ;
							}
						}
						break;


					// case 'checkbox':

					// 	$options = JEP_Field::get_options($field->ID);
					// 	foreach ($options as $option) {

					// 		echo '<input  name="'.$name.'" id="checkbox-'.$option->ID.'" class="cfield-'.$field->ID.' checkbox-'.$field->ID.'" type="checkbox" value="'. $option->ID .'"/><label for="radio-'.$option->ID.'">'. $option->name .'</label>';
					// 	}
					// 	break;
					default:
						# code...
						break;
				} ?>
			</div>
		<?php
		} ?>
		<input type="hidden" id="current_date_field" value="<?php echo date( get_option('date_format'),time());?>" />
		<?php

	}

	public function on_post_resume_fields () {
		$fields = JEP_Field::get_all_fields('resume');
		global $current_user;
	?>
		<script type="text/data" id="resume_custom_fields">
			<?php echo json_encode($fields); ?>
		</script>
	<?php
		echo '<input type="hidden" name="currentdate" id= "current_date" value"'.date(get_option("date_format"), time()).'" />';
		foreach ($fields as $key => $field) {
			$terms	=	get_terms($field->name, array ('hide_empty' => false ));
			$required 	= $field->required ? 'input-required required' : '';
			switch ($field->type ) {

				case 'multi-text':
					?>
					<div class="module skill" data-resume="<?php echo $field->name; ?>">
						<div class="title">
							<?php echo $field->label; ?>
						</div>
						<form action="" id="form_<?php echo $field->name; ?>">
							<div id="inline_skills" class="edu-form skill auto-add" data-resume="<?php echo $field->name; ?>" >

								<div class="jse-input" style="width:100%;">
									<span>
										<input type="text" class="bg-default-input skill-input" value="" placeholder="<?php printf (__("Type your %s", ET_DOMAIN), strtolower($field->label) ) ; ?>" />
									</span>
									<?php printf(__('Press Enter to keep adding %s', ET_DOMAIN), strtolower( $field->label ) ); ?>
								</div>
							</div>
							<ul class="skill-list clearfix">

							</ul>
						</form>

					</div>
					<?php // end skill

					break;

				case 'text':
				case 'textarea':
				case 'url' 	:
				case 'date':
				?>
					<div class="module position custom-fields" data-resume="<?php echo $field->name; ?>">
						<div class="title">
							<?php echo  $field->label; ?>
						</div>
						<form action="" id="form_resume_<?php echo $field->name; ?>">
							<div class="inline-edit " style="display:block;">
								<form action="" id="form_<?php echo $field->name; ?>">
									<div class="jse-input">
										<?php if($field->type == 'textarea') { ?>
											<textarea class="bg-default-input content-about <?php echo $required;?>" data-resume="<?php echo $field->name; ?>" ></textarea>
										<?php } else if($field->type=='url'){  ?>
												<input placeholder="<?php echo $field->label; ?>" data-resume="<?php echo $field->name; ?>" style="width: 430px;padding-left: 10px;" class="bg-default-input skill-input jobfield-url <?php echo $required;?>" value="" />
										<?php } else if($field->type=='date'){  ?>
												<input data-resume="<?php echo $field->name; ?>" style="width: 430px;padding-left: 10px;" class="je_field_datepicker bg-default-input skill-input date-resume <?php echo $required;?>" value="" />
										<?php }else { ?>
											<input placeholder="<?php echo $field->label; ?>" data-resume="<?php echo $field->name; ?>" style="width: 430px;padding-left: 10px;" class="<?php echo $required;?> bg-default-input skill-input " value="" />
										<?php } ?>
									</div>
								</form>
							</div>

						</form>

					</div>
				<?php

				break;
				case 'select' :

					if(!empty($terms)) {
					?>
					<div class="module position" data-resume="<?php echo $field->name; ?>">
						<div class="title">
							<?php echo  $field->label; ?>
						</div>
						<form action="" id="form_resume_<?php echo $field->name; ?>">
						<div class="inline-edit jobposition" style="display:block;">

								<div class="edu-form">

									<div class="jse-multi-select jobpos_select">
										<div class="select-style job-pos-sel btn-background border-radius">
											<select id="<?php echo $field->name ?>" class="<?php echo $required;?>">
												<option value=""><?php _e("Select your matches", ET_DOMAIN); ?></option>
												<?php
													foreach ($terms as $key => $tax) {
														echo '<option value="'.$tax->term_id.'">'.$tax->name .'</option>';
													}
												?>
											</select>
										</div>
									</div>
								</div>

						</div>
						<ul class="skill-list clearfix">
						</ul>
						</form>

					</div>
				<?php }
				break;

				case 'image' :
				case 'file' :	?>

					<div class="module position custom-fields">
					<div class="title">
							<?php
							//resume_image_et_uploader
							$uploaderID = 'resume_file_'.$field->name;
							if($field->type=='image'){
								$uploaderID = 'resume_image_'.$field->name;
							}
							echo $field->label; ?>
							<?php //_e('Categories', ET_DOMAIN) ?>
								<div class="btn-edit"><a href="#" class="icon" data-icon="p">&nbsp;&nbsp;&nbsp;</a></div>
						</div>
						<span class="field-thumb " id="<?php echo $uploaderID;?>_thumbnail"></span>
						<form action="" id="form_<?php echo $field->name; ?>" >

								<div class="form-item-field btn-upload-<?php echo $field->type;?>" id="<?php echo $uploaderID;?>_container" rel="<?php echo $uploaderID;?>">
									<div class="input-file clearfix et_uploader">
										<span class=" border-radius button-upload thumb btn-background" id="<?php echo $uploaderID;?>_browse_button" tabindex="8" >
											<?php _e('Browse...', ET_DOMAIN );?>
											<span class="icon" data-icon="o"></span>
										</span>
										<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'resume_image_et_uploader' ); ?>"></span>
									    <div class="clearfix"></div>
									    <div class="filelist"></div>
									    <input type="hidden" class="field_name"  value="<?php echo $field->name;?>">
									    <input type="hidden" class="field_id" name="field_id" value="<?php echo $field->ID;?>">
									    <input type="hidden" class="resume_id" value="<?php if(isset($post->ID)) echo $post->ID;?>">
									    <input type="hidden" class="field_type" name="field_type" value="<?php echo $field->type ?>">
									</div>
								</div>
						</form>
					</div>
					<?php
					break;
				case 'radio':

				if(!empty($terms)) {
					?>
					<div class="module position custom-fields je-custom-field je-custom-radio" data-resume="<?php echo $field->name; ?>">
						<div class="title">
							<?php echo  $field->label; ?>
						</div>

						<div class=" edu-form job-type">
							<?php
							// show the all the available
							foreach ($terms as $avail) {?>
								<div class="jse-input">
									<div class="jse-radio">
										<input class="<?php echo $required;?>" id="<?php echo $field->name; ?>-<?php echo $avail->name ?>" type="radio" name="<?php echo $field->name; ?>" data-resume="<?php echo $field->name; ?>" value="<?php echo $avail->term_id ?>" />
									</div>
									<div class="job-type ">
										<!-- <span class="flag"></span> -->
										<label style="margin-left:0px;" for="<?php echo $field->name; ?>-<?php echo $avail->name ?>" href="#"><?php echo $avail->name ?></label>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php }
				break;

				case 'checkbox' :

					if(!empty($terms)) {
					?>
					<div class="module jobtype available" data-resume="<?php echo $field->name; ?>">
						<div class="title">
							<?php echo  $field->label; ?>
						</div>

						<div class="edu-form job-type">
							<?php
							// show the all the available
							foreach ($terms as $avail) { ?>
								<div class="jse-input">
									<div class="jse-checkbox">
										<input type="hidden" name="" value="0">
										<input class="<?php echo $required;?>" id="<?php echo $field->name; ?>-<?php echo $avail->name ?>" type="checkbox" name="" value="<?php echo $avail->term_id ?>" />
									</div>
									<div class="job-type ">
										<!-- <span class="flag"></span> -->
										<label style="margin-left:0px;" for="<?php echo $field->name; ?>-<?php echo $avail->name ?>" href="#"><?php echo $avail->name ?></label>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php
				}
				default:
				break;
			}
		}
	}

	public function on_edit_resume_fields ($post, $jobseeker, $authorise) {
		$fields = JEP_Field::get_all_fields('resume');
		global $current_user;
	?>
		<script type="text/data" id="resume_custom_fields">
			<?php echo json_encode($fields); ?>
		</script>
	<?php
		foreach ($fields as $key => $field) {

			$required 	= $field->required ? 'input-required required' : '';
			switch ($field->type ) {
				case 'multi-text':
					$terms = wp_get_object_terms($post->ID , $field->name );
					if(!empty($terms) || $authorise ) {
					?>
					<div class="module module-edit education" data-resume="<?php echo $field->name; ?>" id="fields-<?php echo $field->name ?>" >
						<div class="title">
							<?php echo $field->label; ?>
							<div class="btn-edit"><a href="#" class="icon" data-icon="p">&nbsp;&nbsp;&nbsp;</a></div>
						</div>

						<div class="edu-skill cnt">
							<?php
							if(!empty($terms) || $current_user->ID != $jobseeker->ID )
							foreach ($terms as $term) { ?>
								<div class="item">
									<div class="content">
										<?php echo $term->name ?>
									</div>
								</div>
							<?php } else {
								_e("Oops! You're missing something here...", ET_DOMAIN);
							} ?>
						</div>
						<?php if ( $authorise ) { ?>
						<div class="inline-edit skill">
							<form action="" id="form_<?php echo $field->name; ?>">
								<script type="text/data" id="data_resume_<?php echo $field->name; ?>">
									<?php echo json_encode($terms); ?>
								</script>
								<div id="inline_skills" class="auto-add">
									<div class="jse-input" style="width:100%;">
										<span>
											<input type="text" class="bg-default-input skill-input <?php echo $required;?>" value="" placeholder="<?php printf (__("Type your %s", ET_DOMAIN), strtolower( $field->label ) ) ; ?>" />
										</span>
										<?php printf(__('Press Enter to keep adding %s', ET_DOMAIN), strtolower( $field->label ) ); ?>
									</div>
								</div>
								<ul class="skill-list clearfix">
								</ul>

								<div class="edu-form btn-save">
									<input rel="#edit_education" class="save-edit button" type="submit" value="<?php _e('SAVE', ET_DOMAIN) ?>">
									<a class="cancel-edit close" href="#">
										<?php _e('Cancel', ET_DOMAIN); ?>
										<span data-icon="D" class="icon"></span><span class="line-bottom"></span>
									</a>
								</div>
							</form>
						</div>
						<?php } // end if authorise skills?>
					</div>
					<?php } // end skill

					break;
				case 'text':
				case 'textarea':
				case 'url' :
				case 'date':

					$content = get_post_meta( $post->ID, $field->name , true );

					if($content != '' || $authorise ) { ?>
					<div class="module module-edit education info-resume jf-input-text" data-resume="<?php echo $field->name; ?>" id="fields-<?php echo $field->name; ?>">
						<div class="title">
							<?php echo $field->label; ?>
							<div class="btn-edit"><a href="#" class="icon" data-icon="p">&nbsp;&nbsp;&nbsp;</a></div>
						</div>
						<div class="edu-content cnt" id="<?php echo $field->name; ?>-content">
							<?php
								if( $content != '' || $current_user->ID != $jobseeker->ID ){
									if($field->type == 'url'){
										echo '<a rel="nofollow" target="_blank" href="'.esc_url($content).'">'.esc_url($content).'</a>';
									} else if($field->type =="date"){
										echo date(get_option("date_format"),strtotime($content));
									} else {
										echo $content;
									}
								}
								else {
									echo "<div class='edu-module' >";
									_e("Oops! You're missing something here...", ET_DOMAIN);
									echo '</div>';
								}
							?>
						</div>
						<?php if ($authorise) {  ?>
						<div class="inline-edit">
							<form action="" id="form_<?php echo $field->name; ?>">
								<div class="jse-input">
									<?php if($field->type == 'textarea') { ?>
										<textarea class="bg-default-input content-about <?php echo $required;?>"><?php echo $content ?></textarea>
									<?php } else if($field->type == 'url'){ ?>
										<input style="width: 400px;padding-left: 10px;"   class="bg-default-input skill-input  ui-autocomplete-input <?php echo $required;?>"  value="<?php echo esc_url($content); ?>" />
									<?php } else if($field->type == 'date'){ ?>
										<input style="width: 400px;padding-left: 10px;"   class="je_field_datepicker bg-default-input skill-input  <?php echo $required;?>"  value="<?php echo date(get_option("date_format"),strtotime($content) ); ?>" />
									<?php }else { ?>
										<input style="width: 400px;padding-left: 10px;" class="bg-default-input skill-input <?php echo $required;?>" value="<?php echo $content ?>" />
									<?php } ?>
								</div>
								<div class="btn-save">
									<input class="save-edit button" type="submit" value="<?php _e('SAVE', ET_DOMAIN) ?>"> <a class="cancel-edit close" href="#"><?php _e('Cancel', ET_DOMAIN) ?><span data-icon="D" class="icon"></span><span class="line-bottom"></span></a>
								</div>
							</form>
						</div>
						<?php } // end if authorise?>
					</div>
					<?php
					} // end about me
					break;

				case 'file' :
				case 'image' :
					// html for file field.
					$attachs = get_posts( array('meta_key' => $field->name,	'meta_value' => 1, 'post_parent'=>$post->ID,'post_status' => 'any', 'post_type' => 'attachment', 'posts_per_page' => 10) );

    				if(count($attachs) > 0 || $authorise ) { ?>
					<div class="module module-edit education info-resume" data-resume="<?php echo $field->name; ?>" id="fields-<?php echo $field->name; ?>">

						<div class="title">
							<?php
							$uploaderID = 'resume_file_'.$field->name;


							if($field->type=='image'){

								$uploaderID = 'resume_image_'.$field->name;
							}
							echo $field->label; ?>
								<?php //_e('Categories', ET_DOMAIN) ?>
								<div class="btn-edit"><a href="#" class="icon" data-icon="p">&nbsp;&nbsp;&nbsp;</a></div>
						</div>
						<span class="field-thumb " id="<?php echo $uploaderID;?>_thumbnail">
							<?php

								$attachs = get_posts( array('meta_key' => $field->name,	'meta_value' => 1, 'post_parent'=>$post->ID,'post_status' => 'any', 'post_type' => 'attachment', 'posts_per_page' => 10) );

								if(is_array($attachs)) {
									foreach($attachs as $attach){
										setup_postdata( $attach );
										if($field->type == 'file'){
											?>
											<span class="file-item">
												<a   rel = "<?php the_ID();?>" class="file-url" alt="<?php echo $attach->post_title; ?>" title="<?php echo $attach->post_title;?>" href="<?php echo $attach->guid; ?>">
													<?php echo basename($attach->guid); ?>
												</a>
												<?php
												if($authorise){ ?>
													<span class="delete" ><a rel="<?php echo $post->ID;?>"  id="<?php echo $attach->ID;?>" data-icon="#" > x </a></span>
												<?php
												}
												?>
											</span>
										<?php
										} else { ?>

											<span class="thumb-item">
												<a target = "_blank" rel = "<?php the_ID();?>" class="file-url" alt="<?php echo $attach->post_title;?>" title="<?php echo $attach->post_title;?>" href="<?php echo $attach->guid; ?>">
													<?php echo wp_get_attachment_image($attach->ID,'thumbnail',false,array('width' => '50','height' => '50'));?>
												</a>
												<?php if($authorise){ ?>
												<span class="delete" ><a class="del-thumbnail" id="<?php echo $attach->ID;?>" rel="<?php echo $post->ID;?>" data-icon="#" > x </a></span>
												<?php }?>
											</span>

											<?php

										}
									}
								}
							?>
						</span>
						<div class="inline-edit">
							<form action="" id="form_<?php echo $field->name; ?>" >

								<div class="form-item-field btn-upload-<?php echo $field->type;?>"  id="<?php echo $uploaderID?>_container" rel="<?php echo $uploaderID;?>">
									<div class="input-file clearfix et_uploader">
										<span class="btn-background border-radius button-upload thumb" id="<?php echo $uploaderID;?>_browse_button" tabindex="8" >
											<?php _e('Browse...', ET_DOMAIN );?>
											<span class="icon" data-icon="o"></span>
										</span>
										<span class="et_ajaxnonce" id="<?php echo wp_create_nonce( $uploaderID . '_file_uploader' ); ?>"></span>
									    <div class="clearfix"></div>
									    <div class="filelist"></div>
									    <input type="hidden" class="field_name"  value="<?php echo $field->name;?>">
									    <input type="hidden" class="field_id" name="field_id" value="<?php echo $field->ID;?>">
									    <input type="hidden" class="resume_id"   value="<?php echo $post->ID;?>">
									    <input type="hidden" name="author" value="<?php echo $jobseeker->ID ?>">
									    <input type="hidden" class="field_type" name="field_type" value="<?php echo $field->type ?>">
									</div>
								</div>
							</form>

						</div>
					</div>
					<?php
					}
						// end html for file field.
					break;

				case 'select' :
					/**
					 * Resume category
					*/
					$terms = wp_get_object_terms($post->ID , $field->name );

					if(!empty($terms) || $authorise ) {
					?>
					<div class="module module-edit education" data-resume="<?php echo $field->name; ?>" id="fields-<?php echo $field->name ?>" >
						<div class="title">
							<?php

								$taxs	=	get_terms ($field->name , array('hide_empty' => false ));
								echo $field->label;
							?>
								<?php //_e('Categories', ET_DOMAIN) ?>
								<div class="btn-edit"><a href="#" class="icon" data-icon="p">&nbsp;&nbsp;&nbsp;</a></div>
							</div>
							<div class="edu-skill cnt">

								<?php
								if(!empty($terms) || $current_user->ID != $jobseeker->ID )
								foreach ($terms as $term) { ?>
									<div class="item">
										<div class="content">
											<?php echo $term->name ?>
										</div>
									</div>
								<?php }
								 else _e("Oops! You're missing something here...", ET_DOMAIN);
								 ?>
							</div>
							<?php if ( $authorise  ) { ?>
							<div class="inline-edit jobposition">
								<form action="" id="form_<?php echo $field->name; ?>" class="form-select">
									<script type="text/data" id="data_resume_<?php echo $field->name;  ?>">
										<?php echo json_encode($terms); ?>
									</script>
									<div class="edu-form first">
										<div class="jse-multi-select  123 jobpos_select fields_select">
											<div class="select-style job-pos-sel btn-background border-radius">
												<select id="<?php echo $field->name ?>" class="<?php echo $required;?>">
													<option value=""><?php _e("Select your matches", ET_DOMAIN); ?></option>
												<?php 
													foreach ($taxs as $key => $tax) {
														echo '<option value="'.$tax->term_id.'">'.$tax->name .'</option>';
													}
												?>
											</select>
											</div>
										</div>
									</div>
									<ul class="skill-list clearfix">
									</ul>

									<div class="edu-form btn-save">
										<input rel="#edit_education" class="save-edit button" type="submit" value="<?php _e('SAVE' , ET_DOMAIN) ?>"> <a class="cancel-edit close" href="#"><?php _e('Cancel' , ET_DOMAIN) ?><span data-icon="D" class="icon"></span><span class="line-bottom"></span></a>
									</div>
								</form>
							</div>
							<?php } // end if authorise Resume category ?>
						</div>
					<?php } // end resume category

					break;

				case 'radio' :


				 	$availables 	= 	get_terms( $field->name, array ('hide_empty' => false ) );

					$terms 			= 	wp_get_object_terms( $post->ID, $field->name );

					if( (!empty($terms) || $authorise ) && !empty($availables) ) {
					?>

					<div class="module module-edit je-custom-radio  jobtype" data-resume="<?php echo $field->name; ?>" id="fields-<?php echo $field->name; ?>">
						<div class="title">
							<?php
							//$job_vailable = new JE_Jobseeker_Available;
							// $title_available  = $available_tax->get_title();
							echo $field->label;
							 ?>
							<?php //_e('Available for', ET_DOMAIN) ?>
							<div class="btn-edit"><a href="#" class="icon" data-icon="p">&nbsp;&nbsp;&nbsp;</a></div>
						</div>
						<div class="job-type cnt field-radio">
							<?php

							$term_ids 	= array();
							if(!empty($terms) || $current_user->ID != $jobseeker->ID )
							foreach ($terms as $term) { $term_ids[] = $term->term_id; ?>
								<div class="item">
									<div class="job-type color- je-radio-value" >
										<!-- <span class="flag"></span> -->
										<?php echo $term->name ?>
									</div>
								</div>
							<?php } else
								_e("Oops! You're missing something here...", ET_DOMAIN);
							?>

						</div>
						<?php if ($authorise) {

							?>
						<div class="inline-edit edu-form job-type je-custom-radio">
							<form action="" id="form_<?php echo $field->name ?>" class="je-field-radio">
								<?php
								// show the all the available
								foreach ($availables as $avail) {
									$checked = in_array($avail->term_id, $term_ids) ? 'checked="checked"' : ''; ?>
									<div class="jse-input">
										<div class="jse-radio">
											<input id="<?php echo $field->name; ?>-<?php echo $avail->term_id; ?>" class="<?php echo $required;?>" type="radio" data-color="0" name="<?php echo $field->name;?>" data-name="<?php echo $avail->name ?>" value="<?php echo $avail->term_id ?>" <?php echo $checked ?>>
										</div>
										<div class="job-type color-">
											<!-- <span class="flag"></span> -->
											<label style="margin-left: 0;" for="<?php echo $field->name; ?>-<?php echo $avail->term_id; ?>" ><?php echo $avail->name ?></label>
										</div>
									</div>
								<?php } ?>
								<div class="edu-form btn-save">
									<input class="save-edit button" type="submit" value="<?php _e('SAVE', ET_DOMAIN) ?>">
									<a class="cancel-edit close" href="#"><?php _e('Cancel', ET_DOMAIN) ?><span data-icon="D" class="icon"></span><span class="line-bottom"></span></a>
								</div>
							</form>
						</div>
						<?php } // end if authorise available ?>
					</div>
					<?php } // end available
				break;
				case 'checkbox':

					// $available_tax  =   wp_get_object_terms( $post->ID, $field->name );
		            $availables 	= 	get_terms( $field->name, array ('hide_empty' => false ) );

					$terms 			= 	wp_get_object_terms( $post->ID, $field->name );
					if( (!empty($terms) || $authorise ) && !empty($availables) ) {
					?>

					<div class="module module-edit education jobtype" data-resume="<?php echo $field->name; ?>" id="fields-<?php echo $field->name; ?>">
						<div class="title">
							<?php
							//$job_vailable = new JE_Jobseeker_Available;
							// $title_available  = $available_tax->get_title();
							echo $field->label;
							 ?>
							<?php //_e('Available for', ET_DOMAIN) ?>
							<div class="btn-edit"><a href="#" class="icon" data-icon="p">&nbsp;&nbsp;&nbsp;</a></div>
						</div>
						<div class="edu-skill job-type cnt jf-test">
							<?php

							$term_ids 	= array();
							if(!empty($terms) || $current_user->ID != $jobseeker->ID )
							foreach ($terms as $term) { $term_ids[] = $term->term_id; ?>
								<div class="item">
									<div class="job-type color-" >
										<!-- <span class="flag"></span> -->
										<?php echo $term->name ?>
									</div>
								</div>
							<?php } else
								_e("Oops! You're missing something here...", ET_DOMAIN);
							?>

						</div>
						<?php if ($authorise) {

							?>
						<div class="inline-edit edu-form job-type">
							<form action="" id="form_<?php echo $field->name ?>">
								<?php
								// show the all the available
								foreach ($availables as $avail) { $checked = in_array($avail->term_id, $term_ids) ? 'checked="checked"' : ''; ?>
									<div class="jse-input">
										<div class="jse-checkbox">
											<input class= "<?php echo $required;?>" id="<?php echo $field->name; ?>-<?php echo $avail->term_id; ?>" type="checkbox" data-color="0" name="" data-name="<?php echo $avail->name ?>" value="<?php echo $avail->term_id ?>" <?php echo $checked ?>>
										</div>
										<div class="job-type color-">
											<!-- <span class="flag"></span> -->
											<label style="margin-left: 0;" for="<?php echo $field->name; ?>-<?php echo $avail->term_id; ?>" ><?php echo $avail->name ?></label>
										</div>
									</div>
								<?php } ?>
								<div class="edu-form btn-save">
									<input class="save-edit button" type="submit" value="<?php _e('SAVE', ET_DOMAIN) ?>">
									<a class="cancel-edit close" href="#"><?php _e('Cancel', ET_DOMAIN) ?><span data-icon="D" class="icon"></span><span class="line-bottom"></span></a>
								</div>
							</form>
						</div>
						<?php } // end if authorise available ?>
					</div>
					<?php } // end available
					break;
				default:

					break;
			}

		}
	}

	function je_mobile_show_fields(){
		$fields = JEP_Field::get_all_fields();
		$request = isset($_POST['cfield']) ? $_POST['cfield'] : array();
		foreach($fields as $key=>$field){
			$this->show_field_on_mobile($field,$request);
		}
		echo '<input type="hidden" id="date_format" value="'.get_option("date_format").'" />';
		echo '<input type="hidden" id="field_current_date" value="'.date( get_option("date_format"),time() ).'" />';
	}

	function et_insert_job_fields($job_id){

		if (empty($_REQUEST['cfield'])) return;
			$fields = $_REQUEST['cfield'];
		JEP_Field::update_job_fields($job_id, $fields);

	}
	function et_mobile_head_fields(){
	if(is_page_template( 'page-jobseeker-signup.php' ) ||  is_singular('resume') ) { ?>
		<script type="text/javascript">
			var et_resume = {
				'date_range_invalid' : "<?php _e('End date is invalid.', ET_DOMAIN) ?>",
				'position_invalid'	 : "<?php _e(' Please enter your job title.', ET_DOMAIN) ?>",
				'from_date_invalid'  : "<?php _e(' Please select start date.', ET_DOMAIN) ?>",
				'to_date_invalid'	 : "<?php _e(' Please select end date.', ET_DOMAIN) ?>",
				'school_name_invalid' : "<?php _e('Please enter your school name.', ET_DOMAIN) ?>",
				'company_name_invalid' : "<?php _e(' Please enter your company name.', ET_DOMAIN) ?>"
			};
			</script>

			<style type="text/css">
			.progress {
			    background-color: #F5F5F5;
			    border-radius: 4px;
			    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset;
			    height: 20px;
			    margin-bottom: 20px;
			    overflow: hidden;
			}

			.progress-bar {
			    background-color: #428BCA;
			    box-shadow: 0 -1px 0 rgba(0, 0, 0, 0.15) inset;
			    color: #FFFFFF;
			    float: left;
			    font-size: 12px;
			    height: 100%;
			    line-height: 20px;
			    text-align: center;
			    transition: width 0.6s ease 0s;
			    width: 0;
			}
			.progress-bar-success {
			    background-color: #5CB85C;
			}
			.delete a{
				cursor: pointer;
			}
			img.deleting{
				position: absolute;
				top:8px;
				right: 5px;
			}
			span.field-upload{
				position: relative;
				display: inline-block;
				margin-right: 8px;

			}
			span.field-upload a{
				text-decoration: none;
			}
			span.field-upload img{
				max-width: 79px;
				max-height: 79px;
			}
			div.container-file span.field-upload{
				display: block;
    			padding-bottom: 10px;
    			padding-right: 20px;

			}
			span.delete{
				position: absolute;
			    right: -12px;
			    text-align: center;
			    top: -12px;
			    width: 13px;
			}
			span.delete a{
				display: block;
				position: relative;
			}
			div.container-field-file a{
				text-decoration: none;
			}
			div.container-field-file{
				padding-top: 20px;
			}
			div.container-file .field-upload a{
				text-overflow: ellipsis;
    			white-space: nowrap;
    			display: inline-block;
    			width: 96%;
    			overflow: hidden;
			}
			div.container-file .field-upload .delete{
				position: relative;
				top:-17px;
				float: right;
				left: 0;
			}
			</style>

			</head>

			<?php
		}
	}

	function et_mobile_footer_fields(){ ?>
	<script type="text/javascript">
	var date_format = '<?php echo get_option("date_format");?>';
	</script>
	<?php

		if(is_page_template("page-post-a-job.php")) {
			?>
			<script type="text/javascript" src="<?php echo  plugins_url( basename(dirname(__FILE__)) ).'/js/jquery.ui.core.min.js';?>"></script>
			<script type="text/javascript" src="<?php echo  plugins_url( basename(dirname(__FILE__)) ).'/js/jquery.ui.datepicker.min.js';?>"></script>
			<script type="text/javascript" src="<?php echo  plugins_url( basename(dirname(__FILE__)) ).'/js/mobile_fields.js';?>"></script>
			<link type="text/css" href="<?php echo plugins_url( basename(dirname(__FILE__)) ).'/css/jquery-ui.css';?>" rel="stylesheet" />

			<?php
		}
		if(is_singular('resume') || is_page_template('page-jobseeker-signup.php')){?>
		<link type="text/css" href="<?php echo plugins_url( basename(dirname(__FILE__)) ).'/css/jquery-ui-datepicker.css';?>" rel="stylesheet" />
		<link type="text/css" href="<?php echo plugins_url( basename(dirname(__FILE__)) ).'/css/resume.css';?>" rel="stylesheet" />
					<!--
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
			<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
			<script src="<?php echo JEP_FIELD_URL;?>/js-upload/vendor/jquery.ui.widget.js"></script>
			<script type="text/javascript" src="<?php echo  plugins_url( basename(dirname(__FILE__)) ).'/js/jquery.ui.datepicker.min.js';?>"></script>
			<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
			<script src="<?php echo JEP_FIELD_URL;?>/js-upload/jquery.iframe-transport.js"></script>
			<!-- The basic File Upload plugin -->
			<script src="<?php echo JEP_FIELD_URL;?>/js-upload/jquery.fileupload.js"></script>
			<script type="text/javascript" src="<?php echo  plugins_url( basename(dirname(__FILE__)) ).'/js/mobile_fields.js';?>"></script>

		<?php }
	}
	function show_field_on_mobile( $field, $request ){

		$type 		= $field->type;
		$field_id 	= $field->ID;
		$name 		= 'cfield['.$field->ID.']';
		//$value 		= get_post_meta( $job->ID, 'cfield-'. $field->ID, true );
		//$value 		= get_post_meta( $job_id, 'cfield-'.$field->ID , true);
		$value 		= isset($request[$field->ID]) ? $request[$field->ID] : '';
		$checked 	= '';
		$required 	= $field->required ? ' required ="required" ' : '';
		?>
		<div data-role="fieldcontain" class="post-new-job">
			<label for="full_location">
				<?php printf(__("%s",ET_DOMAIN),$field->name); ?>
				<span class="subtitle"><?php printf(__("%s", ET_DOMAIN),$field->desc); ?></span>
			</label>
		<?php

		switch($type) {
			case "textarea":
				echo "<textarea name='".$name."' class='".$required."'> </textarea>";
				break;
			case "select" : ?>
				<div class="select-style btn-background border-radius">
					<select class="input-field "  <?php echo $required ?> name="<?php echo $name;?>" >
						<?php
						$options = JEP_Field::get_options($field->ID);
						foreach ($options as $option) {
							$check = ($option->ID == $value ) ? 'selected' : '';
							echo '<option '.$checked.'  value="' . $option->ID . '">' . $option->name . '</option>';
						} ?>
					</select>
				</div>
			<?php
			break;
			case "date" :
				echo '<input type="text"  class="je_field_datepicker" '.$required.' value="$value" name="'.$name.'" />';
			break;
			case "radio":
				$options = JEP_Field::get_options($field->ID);
						foreach ($options as $option) {
							$checked = ($option->ID == $value ) ? 'checked' : '';
							echo '<input name="'.$name.'" '.$checked.'  type="radio" '.$required.' value="' . $option->ID . '" id="raido- '. $option->ID . '"><label for="raido- '. $option->ID . '">' . $option->name . '</label>';
						}
				break;

			default :
					$class = ($type =='url') ? 'input-url' : '';
				echo '<input type="text"  '.$required.' class="'.$class.'"  value ="'.$value.'" name="'.$name.'" />';
				break;
		}
		echo '</div>';

	}

	function je_resume_add_fields(){
		$fields = JEP_Field::get_all_fields('resume');
		global $current_user;
		foreach ($fields as $key => $field) {
			//$terms	=	get_terms($field->name, array ('hide_empty' => false ));
			$this->show_resume_field_form_add_mobile($field);
		}
	}
	function je_resume_show_fields_on_detail($resume){


		$fields = JEP_Field::get_all_fields('resume');
		foreach ($fields as $key => $field) {
			$this->show_resume_field_on_mobile($resume,$field);

		}
	}

	function je_resume_edit_form($resume){
		$fields = JEP_Field::get_all_fields('resume');
		if($fields){
			foreach ($fields as $key => $field) {
				$this->show_resume_field_on_mobile($resume,$field);
			}
			echo '<input type="hidden" id="field_current_date" value="'.date( get_option("date_format"),time() ).'" />';
		}
	}
	function je_field_footer_trigger(){
		if(is_page_template("page-jobseeker-signup.php") || is_singular("resume")){ ?>
			<script type="text/javascript">
			(function ($){
				 $(document).ready(function($) {

				 	var date_format = '<?php echo get_option("date_format");?>', option  = {};

						option["d"] = 'dd'; // two digi date
						option["j"] = 'd';
						option["m"] = 'mm';
						option["n"] = 'm';
						option["l"] = 'DD';
						option["D"] = 'D';
						option["F"] = 'MM';
						option["M"] = 'M';
						option["Y"] = 'yy';
						option["y"] = 'y';

						for(var i in option)
							date_format = date_format.replace(i,option[i]);
				 	$('.je_field_datepicker').datepicker({
				 		dateFormat: date_format,
				 	});

				});
			})(jQuery);
			</script>
			<?php
		}
	}

	function show_resume_field_on_mobile($resume,$field) {
		if(!isset($resume->ID)) return;
		global $user_ID, $current_user;

		$id = $resume->ID;
		$type 		= $field->type;
		$value 		= get_post_meta( $id, $field->name , true );
		$id_edit 	= isset($_GET['edit']) ? intval($_GET['edit']) : -1;
		$authorise 	= (current_user_can( 'manage_options' ) || $resume->post_author == $user_ID) ? true : false;

		$edit = false;
		if($id_edit == $id && $authorise)
			$edit = true;

		switch ($type) {

			case 'text' :
			case 'textarea' :
			case 'url' :

			if(empty($value) && !$authorise )
				continue;
			?>
				<div class="content-info content-text textarea je-mobile-field" >
					<h1 class="line"><?php echo $field->label;?></h1>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<?php
					$role = '';
					$class = '';
					if($edit){
						if($type != 'textarea')
							echo '<input type="text" '.$role.' role="'.$type.'" data-inline="true" name= "'.$field->name.'"  id= "'.$field->name.'" class="'.$class.'" placeholder ="'.$field->desc.'" value="'.$value.'" data-resume = "'.$field->name.'"  />';
						else
							echo '<textarea data-resume = "'.$field->name.'"  name= "'.$field->name.'"   placeholder ="'.$field->desc.'" id= "'.$field->name.'" >'.$value.'</textarea>';
					} else {
						if($type != 'url'){
							echo $value;
						} else {
							echo '<a target ="_blank" rel="nofollow" href="'.esc_url($value).'">'.$value.'</a>';
						}
					}
				echo '</div>';
				break;
			case 'date' :
				if(empty($value) && !$authorise )
				continue;	?>
				<div class="content-info content-text textarea je-mobile-field jf-mobile-date" >
					<h1 class="line"><?php echo $field->label;?></h1>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<?php

					$role = ' data-role="date"';
					$class = 'je_field_datepicker';


					if($edit){
							echo '<input class="je_field_datepicker" area data-resume = "'.$field->name.'"  name= "'.$field->name.'"   placeholder ="'.$field->desc.'" id= "'.$field->name.'" value="'.date(get_option("date_format"),strtotime($value) ).'"/>';
					} else {
						echo date(get_option("date_format"),strtotime($value) );

					}
				echo '</div>';
				break;
			case 'multi-text':

				$terms = wp_get_object_terms($id , $field->name );
				if(!$terms && !$authorise)
    				continue;

				?>
				<div class="content-info content-text skill-container multi-field je-mobile-field" data-resume="<?php echo $field->name;?>">
		            <h1 class="line"><?php echo $field->label; ?></h1>
		            <div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
		            <div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
		            <?php if($edit){ ?>
		            <div class="date-select option-signup">
		                <input type="text" name="<?php $field->name;?>" value="" class="skill" />
		            </div>
		            <?php }?>

		            <ul class="select-option-signup skill-list">
		                <?php foreach ($terms as $key => $term) { ?>
		                    <li class="element" ><?php  if($edit){ ?> <span class="icon icon-track" data-icon="#"> <?php } ?></span><span class="text"><?php echo $term->name ?></span><input class="skill" type="hidden" value="<?php echo $term->name ?>" ></li>
		                <?php } ?>
		            </ul>

		    	</div>
    		<?php
				break;

			case 'select' :
				//$terms = wp_get_object_terms($id , $field->name );
				$terms	=	get_terms($field->name, array ('hide_empty' => false ));
				$selected = wp_get_object_terms($id , $field->name,array('fields'=>'ids') );

				if(!$terms && !$authorise)
    				continue;

				?>
				<div class="content-info content-text category je-mobile-field" data-resume="<?php echo $field->name;?>" >
		           	<h1 class="line"><?php echo $field->label; ?></h1>
		           	<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>

		            <?php if($edit && !empty($terms)){ ?>
			            <div class="date-select option-signup">
			                <select name="<?php echo $field->name;?>" id="<?php echo $field->name;?>" multiple ="multiple" class="month" data-native-menu="0" >

			                	<?php
			                	foreach($terms as $key=>$term){
			                		$select = '';
			                		if(in_array($term->term_id, $selected))
			                			$select='selected ="selected" ';

			                		echo '<option '.$select.' rel="'.$term->name.'" value="'.$term->term_id.'">'.$term->name.'</option>'	;
			                	}?>

			                </select>
			                <?php //JE_Helper::jobPositionSelectTemplate('position[]', $selected_cat, array('class' => 'month' , 'attr' => array('multiple' => 'multiple', 'data-native-menu'=> 0 , 'data-defaults' => "true") ) ); ?>
			            </div>

		            <?php
		            } else {
		            	$selected = wp_get_object_terms($id , $field->name,array('fields'=>'all') );
		            	foreach ($selected as $key => $term) {
		            		echo '<span>'.$term->name.'</span> &nbsp ';
		            	}
		            }
		            ?>

		    	</div>
    		<?php
    		break;

    		case 'checkbox':

    			$terms	=	get_terms($field->name, array ('hide_empty' => false ));
    			if(!$terms && !$authorise)
    				continue;
    			$terms		= get_terms($field->name, array ('hide_empty' => false ));

    			?>
    			<div class="content-info content-text  available je-mobile-field je-mobile-checkbox" data-resume="<?php echo $field->name;?>">
					<h1 class="line"><?php echo  $field->label; ?></h1>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>

					<?php
					$selected  	= wp_get_post_terms($id , $field->name,array('fields'=>'ids'));
					if($terms && $edit){

						foreach ($terms as $term) {

								$check = '';
								if(in_array($term->term_id,$selected))
		    					$check =' checked ="checked"';

								?>
					        	<div class="ui-checkbox signup">
					                <label for="<?php echo $term->slug; ?>" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off ">
					                    <?php echo $term->name; ?>
					                </label>
					                <input <?php echo $check;?> class="" id="<?php echo $term->slug; ?>" type="checkbox" name="<?php echo $field->name;?>" value="<?php echo $term->term_id ?>" >
					            </div> <?php
				        	} ?>
				     	<input  style="display:none" checked="checked" type="checkbox" name="<?php echo $field->name;?>[]" value=""> 
				    <?php

				    } else if(!$edit && $selected){

			        	$selected  	= wp_get_post_terms($id , $field->name,array('fields'=>'all'));

				        foreach ($selected as $key => $term) {
				        	echo '<span>'.$term->name.'</span> &nbsp; ';
				        }

			        } else if(!$selected){
			        	_e('Please set this values!',ET_DOMAIN);
			        }

					?>

				</div>
				<?php
    			break;

    		case 'radio':

    			$terms	=	get_terms($field->name, array ('hide_empty' => false ));
    			if(!$terms && !$authorise)
    				continue;
    			$terms		= get_terms($field->name, array ('hide_empty' => false ));

    			?>
    				<div class="content-info content-text  available jf-mobile-radio" data-resume="<?php echo $field->name;?>">
					<h1 class="line"><?php echo  $field->label; ?></h1>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<?php
					$selected  	= wp_get_post_terms($id , $field->name,array('fields'=>'ids'));
					if($terms && $edit){
						echo '<div data-role="fieldcontain" >';
						echo '<fieldset data-role="controlgroup">';
						foreach ($terms as $term) {

								$check = '';
								if(in_array($term->term_id,$selected))
		    					$check =' checked ="checked"';

								?>

					                <input <?php echo $check;?> class="ui-radio-on" id="<?php echo $term->slug; ?>" type="radio" name="<?php echo $field->name;?>" value="<?php echo $term->term_id ?>" >
					                <label for="<?php echo $term->slug; ?>" class="ui-radio-on">  <?php echo $term->name; ?></label>

					            <?php
				        	} ?>
				        </fieldset>
				        </div>
				    <?php

				    } else if(!$edit && $selected){

			        	$selected  	= wp_get_post_terms($id , $field->name,array('fields'=>'all'));

				        foreach ($selected as $key => $term) {
				        	echo '<span>'.$term->name.'</span> &nbsp; ';
				        }

			        } else if(!$selected){
			        	_e('Please set this values!',ET_DOMAIN);
			        }

					?>

				</div>
				<?php
    			break;

    		case 'file':
    		case 'image':

    			$attachs = get_posts( array('meta_key' => $field->name,	'meta_value' => 1, 'post_parent'=>$id,'post_status' => 'any', 'post_type' => 'attachment', 'posts_per_page' => 100) );
    			if(count($attachs) < 1  && !$edit)
    				continue;
    			?>
    			<div class="content-info content-text " data-resume="<?php echo $field->name;?>">
    				<h1 class="line"><?php echo  $field->label; ?></h1>

	    			<div class="container container-<?php echo $field->type;?> container-field-file">

	    			  	<input id="resume_id" class="resume_id" type="hidden" name="resume_id" value="<?php echo $id?>" />
	    			  	<input type="hidden" name="field_name" class="field_name" value="<?php echo $field->name; ?>" />
	    			  	<input type="hidden" name="field_type" class="field_type" value="<?php echo $field->type; ?>" />
		   			  	<input type="hidden" name="field_id" class="field_id" value="<?php echo $field->ID; ?>" />
		   			  	<div <?php if(!$edit){?> style="display:none" <?php }?>>
				   			  	<input id="fileupload" class="fileupload" type="file" name="files[]" <?php if($field->type=='image') echo 'accept="image/*"';?> multiple value="Upload file">
							   	</span>
							   	<!-- The global progress bar -->
							    <div id="progress" class="progress">
							        <div class="progress-bar progress-bar-success"></div>
							    </div>

		   			  	</div>

					    <!-- The container for the uploaded files -->
					    <div id="files" class="files">
					    <?php

							if($attachs != '' || $authorise ) {
								foreach ($attachs as $attach) {
									if($field->type =='image'){ ?>
										<span class="thumb-item field-upload">

											<a target = "_blank" rel = "<?php the_ID();?>" class="file-url" alt="<?php echo $attach->post_title;?>" title="<?php echo $attach->post_title;?>" href="<?php echo $attach->guid; ?>">
												<?php echo wp_get_attachment_image($attach->ID,'thumbnail');?>
											</a>
											<?php if($edit){ ?>
											<span class="delete" ><a class="del-thumbnail" id="<?php echo $attach->ID;?>" rel="<?php echo $id;?>" data-icon="#" > x </a></span>
											<?php }?>
										</span> <?php
									} else { ?>
										<span class="file-item field-upload">
											<a   rel = "<?php the_ID();?>" class="file-url" alt="<?php echo $attach->post_title; ?>" title="<?php echo $attach->post_title;?>" href="<?php echo $attach->guid; ?>">
												<?php echo basename($attach->guid); ?>
											</a>
											<?php if($edit){ ?><span class="delete" ><a rel="<?php echo $id;?>"  id="<?php echo $attach->ID;?>" data-icon="#" > x </a></span><?php }	?>
										</span>
										<?php
									}
								}
							}
						?>

					    </div>
					    <br>
					</div>
				</div>
    			<?php
    			break;

			default:

				echo $value;
			break;
		}

	}
	function show_resume_field_form_add_mobile($field){

		global $current_user;
		$type 		= $field->type;
		switch ($type) {

			case 'text' :
			case 'textarea' :
			case 'url' : ?>
				<div class="content-info content-text textarea" data-resume="'.$field->name.'" >
				<h1 class=""><?php echo $field->label;?></h1>
				<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
				<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
				<?php
				$role = '';
				$class = '';
				if($type == "date"){
					$role = ' data-role="date"';
					$class = 'je_field_datepicker';
				}
				if($type !='textarea')
				echo '<input type="text"  '.$role.'   name= "'.$field->name.'"  id= "'.$field->name.'" placeholder ="'.$field->desc.'" class="'.$class.'"  data-resume="'.$field->name.'"/>';
				else
					echo '<textarea name= "'.$field->name.'"  id= "'.$field->name.'"   data-resume="'.$field->name.'" placeholder ="'.$field->desc.'" > </textarea>';

				echo '</div>';
				break;

			case 'date' : ?>
				<div class="content-info content-text textarea" data-resume="'.$field->name.'" >
				<h1 class=""><?php echo $field->label;?></h1>
				<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
				<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
				<?php
					$role = ' data-role="date"';
					$class = 'je_field_datepicker';
					echo '<input type="text"  '.$role.'   name= "'.$field->name.'"  id= "'.$field->name.'" placeholder ="'.$field->desc.'" class="'.$class.'"  data-resume="'.$field->name.'"/>';

				echo '</div>';
				break;

			case 'multi-text':
				$terms	=	get_terms($field->name, array ('hide_empty' => false ));
				?>
				<div class="content-info content-text  skill-container multi-field" data-resume="<?php echo $field->name;?>">
		            <h1 class=""><?php echo $field->label; ?></h1>
		            <div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
		            <div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>

		            <div class="date-select option-signup">
		                <input type="text" name="<?php $field->name;?>" value="" class="skill" />
		            </div>
		            <ul class="select-option-signup skill-list">

        			</ul>

		    	</div>
    		<?php
				break;
			case 'file':
			case 'image':

    			?>
    			<div class="content-info content-text container-field-file" data-resume="<?php echo $field->name;?>">
    				<h1 class="line"><?php echo  $field->label; ?></h1>

	    			<div class="container container-<?php echo $field->type;?> container-field-file">

	    			  	<input id="resume_id" class="resume_id" type="hidden" name="resume_id" value="" />
	    			  	<input type="hidden" name="field_name" class="field_name" value="<?php echo $field->name; ?>" />
	    			  	<input type="hidden" name="field_type" class="field_type" value="<?php echo $field->type; ?>" />
		   			  	<input type="hidden" name="field_id" class="field_id" value="<?php echo $field->ID; ?>" />

		   			  		<span>
			   			  	<input id="fileupload" class="fileupload" type="file" name="files[]" multiple value="Upload file">
						   	</span>
						   	<!-- The global progress bar -->
						    <div id="progress" class="progress">
						        <div class="progress-bar progress-bar-success"></div>
						    </div>

					    <!-- The container for the uploaded files -->
					    <div id="files" class="files">


					    </div>
					    <br>
					</div>
				</div>
    			<?php
    			break;

			case 'select' :
				$terms		=	get_terms($field->name, array ('hide_empty' => false ));
				?>
				<div class="content-info content-text  category" data-resume="<?php echo $field->name;?>" >
		           	<h1 class=""><?php echo $field->label; ?></h1>
		           	<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>

		            <div class="date-select option-signup">
		            <?php if($terms){ ?>
		                <select name="<?php echo $field->name;?>" id="<?php echo $field->name;?>" multiple ="multiple" class="month" data-native-menu="0" data-defaults="true" >
		                	<option data-placeholder="true" value=""><?php echo $field->desc;?></option>
		                	<?php
		                	foreach($terms as $key=>$term){
		                		echo '<option  rel="'.$term->name.'" value="'.$term->term_id.'">'.$term->name.'</option>'	;
		                	}?>
		                </select>

		               <?php
		               }
		               ?>
		            </div>
		    	</div>
    		<?php
    		break;
    		case 'checkbox':
    			$terms		= get_terms($field->name, array ('hide_empty' => false ));
    			?>
    			<div class="content-info content-text available" data-resume="<?php echo $field->name;?>">
					<h1 class=""><?php echo  $field->label; ?></h1>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>

					<?php
					if($terms ){

						foreach ($terms as $term) { ?>
				        	<div class="ui-checkbox signup">
				                <label for="<?php echo $term->slug; ?>" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off ">
				                    <?php echo $term->name; ?>
				                </label>
				               	<input class="" id="<?php echo $term->slug; ?>" type="checkbox" name="" value="<?php echo $term->term_id;?>" data-enhanced="true">
				            </div> <?php
			        	}
				    }
					?>
				</div>
				<?php
				break;
			case 'radio':
    			$terms		= get_terms($field->name, array ('hide_empty' => false ));
    			?>
    			<div class="content-info content-text available" data-resume="<?php echo $field->name;?>">
					<h1 class=""><?php echo  $field->label; ?></h1>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>
					<div class="clear" style="clear:both; height:5px; overflow:hidden;"></div>

					<?php
					if($terms ){

						foreach ($terms as $term) { ?>
				        	<div class="ui-radio signup">
				                <label for="<?php echo $term->slug; ?>" class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-checkbox-off ">
				                    <?php echo $term->name; ?>
				                </label>
				               	<input class="" id="<?php echo $term->slug; ?>" type="radio" name="" value="<?php echo $term->term_id;?>" data-enhanced="true">
				            </div> <?php
			        	}
				    }
					?>
				</div>
				<?php
				break;

			default:

				break;
		}

	}


}

/*
* only affect when register(not update)
*/
add_action('je_insert_resume','je_update_resume_field', 1000);
add_action('je_update_resume','je_update_resume_field', 1000);
function je_update_resume_field($id){
	$fields = JEP_Field::get_all_fields('resume');
	foreach ($fields as $key => $field) {
		if($field->type == "radio"){
			$value = isset($_REQUEST["content"][$field->name]) ? $_REQUEST["content"][$field->name] :'';
			if(!empty($value) && !is_array($value)){
				wp_set_post_terms( $id, array($value), $field->name );
			}
		} else if ( $field->type == "date"){
			$value = isset($_REQUEST["content"][$field->name]) ? $_REQUEST["content"][$field->name] :'';
			if ( !empty($value) ) {
				$date 	= DateTime::createFromFormat(get_option("date_format"), $value);
				update_post_meta($id,$field->name, $date->format('Y-m-d H:i:s'));
			} else {
				update_user_meta($id, $field->name, '' );
			}
		}
	}


}


?>