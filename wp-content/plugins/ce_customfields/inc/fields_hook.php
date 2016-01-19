<?php


class CE_Fields_Hook extends ET_Base  {
	public function __construct(){
		$this->add_filter('ce_get_terms','ce_get_terms',10,3);

		$this->add_action('ce_ad_post_form_fields','fields_add_post_form_fields',10,2);
		$this->add_action('ce_ad_edit_form_fields','fields_add_post_form_fields',10,2);
		$this->add_filter('ce_convert_ad','fields_add_meta');
		$this->add_action('ce_insert_ad','field_insert_add',10,2);
		$this->add_action('ce_update_ad','field_update_add',10,2);
		//$this->add_action('wp_footer','ce_field_footer', 20);
		//$this->add_action( 'wp_enqueue_scripts', 'enqueue_script' );
		$this->add_action('et_mobile_footer','fields_footer_mobile');

		$this->add_action('ce_on_add_scripts','ce_field_desktop_script',1000);
		$this->add_action('ce_after_footer_script','ce_field_desktop_footer_script',1000);

		$this->add_filter( 'pre_get_posts', 'pre_get_posts' );
		// show field in detail ad
		$this->add_action('ce_detail_ad_more','ce_field_show_after_content');
		// show taxonomies in settings of backend.
		$this->add_action('ce_settings_show_more_taxs','show_backend_taxonomy');
		$this->add_action('ce_mobile_ad_post_form_fields','fields_add_post_form_fields',10,2);

		// load script when minify;
		$this->add_filter('ce_minify_source_path','ce_minify_source_path',1000);
		$this->add_action('et_mobile_header','et_mobile_header');

		$this->add_action('ce_after_update_seller','save_seller_field',10,2);
		$this->add_action('ce_after_insert_seller','save_seller_field',10,2);

		$fields = CE_Fields::get_taxs();

		if ( is_array($fields) && !empty($fields) ){
			foreach($fields as $key=>$field){
				if(!$field['tax_status'])
					continue;
				$tax = new CE_Field_Tax($field);
				$tax->register_ajax();
				$tax->register_action();
			}
		}
		// seller field render
		$this->add_action("et_add_meta_seller_profile","render_seller_field");
		$this->add_action("ce_after_register_form","render_seller_field_register", 15);
		$this->add_action("ce_seller_add_info","ce_show_seller_field");
		$this->add_action('et_mobile_footer','ce_field_mobile_footer');
		$this->add_action('cm_after_seller_info','ce_show_seller_field');

	}
	public function pre_get_posts($query){

		global $wpdb,$et_global;
		if( !$query->is_main_query() && !is_search()  ) return $query;

		$c_str  =   ET_AdCatergory::slug();
        $l_str  =   ET_AdLocation::slug();

		$taxs = CE_Fields::get_taxs();
		if( is_array($taxs) && !empty($taxs)  ){
			foreach ($taxs as $key => $tax) {
				if( get_query_var($tax['tax_name']) ){
					$query->query_vars['tax_query'][] = array(
					                        'taxonomy'  => $tax['tax_name'],
					                        'field'     => 'slug',
					                        'terms'     => get_query_var($tax['tax_name'])
										);
					$query->query_vars['tax_query']['relation']	=	'AND';
				}
			}
		}
		return $query;
	}
	/**
	 * add file css to style for fields item when edit add, insert add.
	 * @return style front end.
	 */
	public function ce_field_desktop_script(){

		wp_enqueue_script('jquery-ui-datepicker');
		$mininfy = get_theme_mod( 'ce_minify', 0 );
        if( !$mininfy  ) {
        	wp_enqueue_style('ce-fields',CE_FIELDS_URL.'/css/ce-fields.css',array(),CE_FIELD_VER);
        	wp_enqueue_style('ce-fields-ui',CE_FIELDS_URL.'/css/jquery-ui.min.css', array(),CE_FIELD_VER);

        }

	}
	function ce_field_desktop_footer_script(){
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

		$date_format = str_replace( array_keys($replace) , array_values($replace), get_option('date_format'));
		$this->generate_field_template();
		echo '<script type="text/javascript" src="'.CE_FIELDS_URL . '/js/ce-fields-condition.js?ver='.CE_FIELD_VER.'"></script>';
		if(is_page_template( 'page-post-ad.php') || is_home() || is_singular(CE_AD_POSTTYPE) || is_page_template("page-account-profile.php") ) {
			?>
			<script type="text/javascript">
				(function ($) {
					$(document).ready(function(){
						if($(".ce_field_datepicker").length > 0) {
							$( ".ce_field_datepicker" ).datepicker({
								dateFormat: "<?php echo $date_format;?>"
								//defaultDate : new Date(jQuery('#field_current_date').val())
							});
							if($(".ce_field_datepicker").val() == ''){
								$(".ce_field_datepicker").val($('#field_current_date').val());
							}
						}
						if($(".csf-date-field").length > 0) {
							$( ".csf-date-field" ).datepicker({
								dateFormat: "<?php echo $date_format;?>"
								//defaultDate : new Date(jQuery('#field_current_date').val())
							});
							if($(".csf-date-field").val() == ''){
								$(".csf-date-field").val($('#field_current_date').val());
							}
						}
					});
				})(jQuery);
			</script>
			<?php
		}
	}

	function ce_minify_source_path($mini_path){

		$mininfy = get_theme_mod( 'ce_minify', 1 );
        if( $mininfy  ) {

            $mini_path['mobile-js'][]   = CE_FIELDS_PATH.'//js/front-fields.js';
			$mini_path['mobile-js'][]   = CE_FIELDS_PATH.'//js/jquery.ui.core.min.js';
			$mini_path['mobile-js'][]   = CE_FIELDS_PATH.'//js/jquery.ui.datepicker.min.js';

			$mini_path['theme_css'][] 	= CE_FIELDS_PATH.'//css/ce-fields.css';
			$mini_path['theme_css'][] 	= CE_FIELDS_PATH.'//css/jquery-ui.min.css';
        }

        return $mini_path;
	}

	function et_mobile_header(){
		$option = get_option('date_format'); ?>
		<link rel="stylesheet" type="text/css" href="<?php echo CE_FIELDS_URL.'/css/jquery-ui.min.css';?>" />
		<script type ="text/javascript">
			var date_format = "<?php echo $option;?>";
		</script> <?php
		$this->generate_field_template();
	}

	public function fields_footer_mobile(){
		$use_minify = get_theme_mod( 'ce_minify', 0 );
		if( ( is_page_template('page-post-ad.php') || is_home() || is_singular(CE_AD_POSTTYPE) ) )  { ?>
			<script type="text/javascript" src="<?php echo CE_FIELDS_URL.'/js/ce-fields-condition-mobile.js';?>"></script>
			<script type="text/javascript" src="<?php echo CE_FIELDS_URL.'/js/jquery.ui.datepicker.min.js';?>"></script>
			<script type="text/javascript" src="<?php echo CE_FIELDS_URL.'/js/front-fields.js';?>"></script>
			<script type="text/javascript" src="<?php echo CE_FIELDS_URL.'/js/jquery.ui.core.min.js';?>"></script>
			<?php
		}

	}

	function save_seller_field($seller_id, $requet){
		$seller_felds = CE_Fields::get_seller_fields();
		if( !empty($seller_felds) && is_array($seller_felds) ){
			foreach ($seller_felds as $key => $field) {

				if(isset($requet[$key]) ){

					if($field['field_type'] == 'date'){
						if ( !empty($requet[$key]) ) {
							$date 	= DateTime::createFromFormat(get_option("date_format"), $requet[$key]);
							update_user_meta($seller_id,$key, $date->format('Y-m-d h:i:s'));
						} else {
							update_user_meta($seller_id,$key, '');
						}


					} else 	if( in_array($field['field_type'], array('select','radio') ) ) {

						update_user_meta($seller_id,$key, (int) $requet[$key]);

					} else if ( $field['field_type'] == 'checkbox' ) {

						$options = $requet[$key];

						$options = array_map('intval', $options);

						update_user_meta($seller_id,$key, $requet[$key]);


					} else {

						update_user_meta($seller_id,$key, $requet[$key]);

					}

				} else {
					update_user_meta($seller_id,$key, '');
				}
			}
		}
	}

	/**
	 * show taxonomy fields to form edit, add ad.
	 * @return html of taxonomy fields.
	 */
	public function fields_add_post_form_fields(){
		//Customfield container
		echo '<div id="customfield"></div>';
		$is_mobile = et_is_mobile();
		$fields = CE_Fields::get_fields();

		if($is_mobile){
			foreach ($fields as $key => $field) {
				show_field_template_mobile($field);
			}
		}else{
			foreach ($fields as $key => $field) {
				if (!isset($field['field_cats']) ) {
					show_field_html($field);
				}
			}
		}

		$taxs = CE_Fields::get_taxs();

		if(is_array($taxs) && !empty($taxs)){

			foreach ($taxs as $key => $tax) {
				if ($tax['tax_status']) {
					if (!$is_mobile)
						show_tax_html($tax);
					else
						show_tax_html_mobile($tax);
				}
			}
		}
		echo '<input type="hidden" id="field_current_date" value="'.date( get_option("date_format"),time() ).'" />';
	}

	/**
	 * Generate template for custom field, which assigned to category
	 */
	public function generate_field_template()
	{
		$is_mobile = et_is_mobile();
		// if(!$is_mobile) {
			echo '<script type="text/template" id="custom_field_template">';
			$fields = CE_Fields::get_fields();
			foreach ($fields as $key => $field) {
				if (isset($field['field_cats'])) {
					show_field_template($field);
				}
			}

			echo '</script>';
		// }
	}
	/**
	 * assign taxonomy of add to array, it use for assign collection.
	 * @param  array $result - array ad convert
	 * @return array with add fileds taxonomy value.
	 */
	public function fields_add_meta($result){

		$taxs = CE_Fields::get_taxs();
		if(is_array($taxs) && !empty($taxs) ) {
			foreach ($taxs as $key => $tax) {
				if(!$tax['tax_status'])
					continue;
				$terms = get_the_terms($result->id,$tax['tax_name']);
				if(!$terms)
					continue;
				if($tax['tax_type'] == 'radio' || $tax['tax_type'] == 'select'){
					$key = key($terms);
					$result->$tax['tax_name'] = $terms[$key]->term_id;
				} else {
					$temp = array();
					foreach($terms as $term){
						$temp[] = $term->term_id;
					}
					$result->$tax['tax_name'] = $temp;
				}
			}
		}
		$date_format = get_option('date_format');
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
		$date_format = str_replace( array_keys($replace) , array_values($replace), get_option('date_format'));

		$fields = CE_Fields::get_fields();
		if($fields){
			foreach($fields as $key => $field){

				if(!$field['field_name'])
					continue;

				if($field['field_type'] == 'date'){
					$meta_date 	= get_post_meta($result->id,$field['field_name'],true);
					if(!empty($meta_date))
						$result->$field['field_name'] = date(get_option('date_format'),strtotime($meta_date) );
					else
						$result->$field['field_name'] = date(get_option('date_format'),time());

				} else {

					$result->$field['field_name'] = get_post_meta($result->id, $field['field_name'], true);
				}

			}
		}
		return $result;
	}

	/**
	 * catch filter ce_get_terms in ce_get_terms function (ad_cat.php)
	 * return list terms of taxonomy register in the plugin.
	 * @return list term use for assign  value to select option mobile;
	 */
	function ce_get_terms($term, $taxonomy, $args){

		if($taxonomy == CE_AD_CAT || $taxonomy == 'ad_location'){

			return $term;
		}
		$terms = get_terms( $taxonomy, $args ) ;
		return $terms;
	}
	/**
	 * hook to insert fields taxonomy for ad. Use for save taxonomy of post..
	 * @param  int $return  post_id
	 * @param  array $data   current post data
	 * @return no return.
	 */
	public function field_insert_add($return, $data){

		$taxs = CE_Fields::get_taxs();
		if(is_array($taxs) && !empty($taxs) ) {
			foreach ($taxs as $key => $tax) {
				if(!isset($data[ $tax['tax_name']]))
					continue;
				if($tax['tax_type'] == 'select' || $tax['tax_type'] == 'radio' ){
					wp_set_object_terms($return,array(intval ($data[ $tax['tax_name']])), $tax['tax_name']);
				}else if ($tax['tax_type'] == 'checkbox'){
					$terms = $data[ $tax['tax_name']];
					wp_set_post_terms($return,(array)$terms,$tax['tax_name']);

				}
			}
		}

		$fields = CE_Fields::get_fields();
		if( is_array($fields) && !empty($fields) ){
			foreach ($fields as $key => $field) {

				if(isset($data[$field['field_name']])){
					if($field['field_type'] == 'date'){
						if(!empty($data[$field['field_name']])){
							$date   = DateTime::createFromFormat(get_option("date_format"), $data[$field['field_name']] );
							update_post_meta($return,$field['field_name'],$date->format('Y-m-d h:i:s'));
						}
					} else {
						update_post_meta($return,$field['field_name'],$data[$field['field_name']]);
					}
				}
			}
		}
	}

	/**
	 * hook to save fields taxonomy after update ad.
	 * @param  int $return post_id
	 * @param  array $data   current value of ad data.
	 * @return [type]         [description]
	 */
	public function field_update_add($return, $data){
		$taxs = CE_Fields::get_taxs();
		if( is_array($taxs) && !empty($taxs) ){
			foreach ($taxs as $key => $tax) {
				if(!isset($data[ $tax['tax_name']]) || !$tax['tax_status'])
					continue;
				if($tax['tax_type'] == 'select' || $tax['tax_type'] == 'radio' ){
					wp_set_object_terms($return,array(intval ($data[ $tax['tax_name']])), $tax['tax_name']);
				}else if ($tax['tax_type'] == 'checkbox'){
					$terms = $data[ $tax['tax_name']];
					wp_set_post_terms($return,(array)$terms,$tax['tax_name']);

				}
			}
		}
		$fields = CE_Fields::get_fields();
		if(is_array($fields) && !empty($fields) ){
			foreach ($fields as $key => $field) {

				if(isset($data[$field['field_name']])){

					if($field['field_type'] == 'date'){
						//igrone if valudate empty.
						if(!empty($data[$field['field_name']])){
							$date   = DateTime::createFromFormat(get_option("date_format"), $data[$field['field_name']] );
							update_post_meta($return,$field['field_name'],$date->format('Y-m-d h:i:s'));
						}
					} else {
						update_post_meta($return,$field['field_name'],$data[$field['field_name']]);
					}
				}
			}
		}
	}

	public function ce_field_show_after_content($id){
		$fields = CE_Fields::get_fields();
		$taxs = CE_Fields::get_taxs();
		if($fields){
			foreach ($fields as $key => $field) {
				if(empty($field))
					continue;
				$value 	= get_post_meta($id,$field['field_name'],true);
					if(!empty($value)){
						if( $field['field_type'] == 'date' )
							$value = date(get_option('date_format'),strtotime($value));
						if($field['field_type'] == 'url')
							echo '<p class="ext-field"><label>'.$field['field_label'].': </label><span class="ext-field-value"><a target="_blank" href="'.esc_url($value).'" rel="nofollow">'.$value.'</a></span></p>';
						else
							echo '<p class="ext-field"><label>'.$field['field_label'].': </label><span class="ext-field-value">'.$value.'</span></p>';
					}
			}
		}
		if(!empty($taxs) && !is_wp_error($taxs)){

			foreach ($taxs as $key => $tax) {
				$ad_terms = wp_get_object_terms( $id,  $tax['tax_name'] );

				if ( ! empty( $ad_terms ) ) {
					if ( ! is_wp_error( $ad_terms ) ) {

						echo '<p class="ext-field ext-taxonomy"><label>'.$tax['tax_label']. ':</label>';
							foreach( $ad_terms as $key=>$term ) {
								if($key == count($ad_terms) - 1)
									echo $term->name. '.';
								else
									echo $term->name. ', ';
							}
						echo '</p>';

					}
				}
			}
		}
	}



	public function show_backend_taxonomy(){

		$fields = CE_Fields::get_taxs();

		echo '<div id="ce_fields_controls">';
		echo '<class="desc">';
			echo '<div class="title">';
				_e("Manager list taxonomies ad by CE custom fields extension.",ET_DOMAIN);
			echo '</div><br />';

		$taxonomies = array();
		foreach($fields as $key=>$field){
			if(!$field['tax_status'])
				continue;

			$taxonomies[] = $field['tax_name'];
			$tax 	= new CE_Field_Tax($field);
			?>
			<div class="title font-quicksand">
				<div title="" class="title-main" style="text-transform:uppercase;">
				<?php echo $field['tax_label']; ?>
				</div>

			</div>
			<div class="desc">
			<?php printf(__("Manager taxonomy %s.",ET_DOMAIN),$field['tax_name']);?> 
			<div class="types-list-container" id="tax-<?php echo $field['tax_name'];?>">
				<!-- <ul class="list-job-input list-tax jobtype-sortable tax-sortable"> -->
				<?php

					$tax->print_backend_terms();
					$tax->print_confirm_list($field);

				?>
			</div>
			</div>
			<?php


		}
		echo '</div>';
		wp_enqueue_script ('ce-fields-settings',CE_FIELDS_URL . '/js/ce-fields-settings.js',array('jquery','jquery-ui-sortable', 'underscore', 'backbone', 'ce'), CE_FIELD_VER );
		wp_localize_script('ce-fields-settings', 'ce_fields', array(
			'taxonomies' => $taxonomies
		));

	}

	function render_seller_field($seller){
		$fields = CE_Fields::get_seller_fields();
		if(!empty($fields)){
			foreach ($fields as $key => $field) {
				$this->render_field_edit($field, $seller);
			}
			echo '<input type="hidden" id="field_current_date" class="" value="'.date(get_option("date_format"),time()).'">';
		}
	}
	function render_seller_field_register(){
		$fields = CE_Fields::get_seller_fields();
		if(!empty($fields)){
			foreach ($fields as $key => $field) {
				$this->render_field_edit($field, false);
			}
		}
	}

	function ce_show_seller_field($seller){

		$fields = CE_Fields::get_seller_fields();
		if(!empty($fields)){
			foreach ($fields as $key => $field) {
				$this->render_field_front($field, $seller);
			}

		}
	}

	function render_field_front($field, $seller){
		if(empty($field))
			return '';
		$type 	= $field['field_type'];

		$value = '';

		if(!$seller || is_wp_error($seller))
			return '';

		$value 	= get_user_meta($seller->ID,$field['field_name'], true);

		$label 	= $field['field_label'];

		$type 	= $field['field_type'];
		$class  = et_is_mobile()  ? ' post-new-classified ' : '';
		switch ($type) {
			case 'checkbox':
				$values = get_option( 'ce_sf_'.$field['field_name'], array() );
				$value 	= get_user_meta($seller->ID,$field['field_name'], true); // make sure true(return default type)
				if(!empty($value) && is_array($value)){
					echo '<p class="text-csf-field"> <span class="colorgreen">'.$label.'</span><br />';
					$count = count($values);
					foreach ($value as $key) {
						if($key != end($value))
							echo $values[$key] .', ';
						else
							echo $values[$key] .'.';
					}
					echo '</p>';
				}
				break;

			case 'radio':
			case 'select':
				$values = get_option( 'ce_sf_'.$field['field_name'], array() );
				$value 	= get_user_meta($seller->ID,$field['field_name'], true);
				if(isset($values[$value]) && !empty($values[$value])) { ?>
					<p class="text-csf-field">    <span class="colorgreen"><?php echo $label;?></span><br><?php echo $values[$value];?></p>
					<?php
				}
				break;

			case 'date':
				if(!empty($value) ){
					?>
					<p class="text-csf-field">    <span class="colorgreen"><?php echo $label;?></span><br><?php echo date( get_option("date_format"), strtotime($value) );?></p>
					<?php
				}
				break;
			case 'url':
				if(!empty($value) ){
					?>
					<p class="text-csf-field">    <span class="colorgreen"><?php echo $label;?></span><br><a target="_blank" title="<?php echo $label;?>" href="<?php echo esc_url($value);?>"><?php echo $value;?></a></p>
					<?php
				}
				break;
			default:
				if(!empty($value) ){ ?>
					<p class="text-csf-field">    <span class="colorgreen"><?php echo $label;?></span><br><?php echo $value;?></p>
					<?php
				}
				# code...
				break;
		}
	}


	function render_field_edit($field, $seller){
		if(empty($field))
			return '';
		$type 	= $field['field_type'];
		$required = (isset($field['field_required']) && $field['field_required'] ==1) ? ' required' :'';
		$value = '';
		if($seller)
			$value 	= get_user_meta($seller->ID,$field['field_name'], true);

		$values = get_option( 'ce_sf_'.$field['field_name'], array() );
		$name = $field['field_name'];

		$class  = et_is_mobile()  ? ' post-new-classified ' : '';
		switch ($type) {

			case 'radio':
				if($seller)
					$value 	= get_user_meta($seller->ID,$field['field_name'], true); // make sure true to get defautl type

					if( !empty($values) ){?>
						<div class="control-group form-group ce-sf-item ce-sf-item ce-sf-item-<?php echo $type.$class;?>">
			          		<label class="control-label customize_text" for="email"><?php echo $field['field_label'];?> <br /> <span class="sub-title customize_text"> <?php echo $field['field_des'];?></span> </label>
			          		<div class="controls">
			          			<ul><?php
									foreach ($values as  $key=>$val) {
											$checked = ( $key == $value ) ? ' checked ="checked" ': '';
											echo '<li><input id="'.$name.$key.'"'.$checked.$required.' type="radio" name="'.$field['field_name'].'" value ="'.$key.'"><label for="'.$field['field_name'].$key.'">'.$val.'</label></li>';
									}
								echo '</ul>';
							echo '</div>';
						echo '</div>';
				}

			break;
			case 'checkbox':
				$value = array();
				if($seller)
					$value 	= get_user_meta($seller->ID,$field['field_name'], true);// make sure true to get defautl type
				if( !empty($values) ){ ?>
						<div class="control-group form-group ce-sf-item ce-sf-item-<?php echo $type.$class;?>">
			          		<label class="control-label customize_text" for="email"><?php echo $field['field_label'];?> <br /> <span class="sub-title customize_text"> <?php echo $field['field_des'];?></span></label>
			          		<div class="controls">
						      	<?php
						      	echo '<ul>';
								foreach ($values as  $key=>$val) {
									$checked = '';
									if(is_array($value))
										$checked = (in_array($key,$value)) ? ' checked ="checked"' :'';
									echo '<li><input id="'.$name.$key.'" '.$checked.$required.'  type="checkbox" name="'.$name.'" value ="'.$key.'"> &nbsp; &nbsp; <label for="'.$name.$key.'">'.$val.'</label> &nbsp;  &nbsp; &nbsp; </li>';
								}
								echo '<ul>';
							echo '</div>';
						echo '</div>';
				}
			break;

			case 'select':
				$value = '';
				if($seller)
					$value 	= get_user_meta($seller->ID,$field['field_name'], true);

				if( !empty($values) ){?>
					<div class="control-group form-group ce-sf-item ce-sf-item-<?php echo $type.$class;?>">
			          	<label class="control-label customize_text" for="email"><?php echo $field['field_label'];?> <br /> <span class="sub-title customize_text"> <?php echo $field['field_des'];?></span></label>
			          	<div class="controls">
				          	<?php
							echo '<select name="'.$name.'" class="sf-select">';
							foreach ($values as  $key=>$val) {
								echo '<option  '.selected( $value, $key,false).' name="'.$name.'" value ="'.$key.'">'.$val.'</option>';
							}
							echo '</select>';
						echo '</div>';
					echo '</div>';
				}
			# code...
			break;
			case 'date':

			if(empty($value)) $time =time();
			else $time = strtotime($value);
				?>

				<div class="control-group form-group ce-sf-item ce-sf-item-<?php echo $type.$class;?>">
		          	<label class="control-label customize_text" for="email"><?php echo $field['field_label'];?> <br /> <span class="sub-title customize_text"> <?php echo $field['field_des'];?></span></label>
		          	<div class="controls">
		            	<input type="text" id="<?php echo $field['field_name'];?>"  <?php echo $required;?> placeholder="<?php echo $field['field_pholder']; ?>" class="csf-date-field ce_field_datepicker" value="<?php echo date(get_option( 'date_format'), $time);?>" name="<?php echo $field['field_name'];?>" class="input-xlarge" >
		          	</div>
		        </div>
		        <?php
				break;

			default: ?>
				<div class="control-group form-group ce-sf-item ce-sf-item-<?php echo $type.$class;?>">
		          	<label class="control-label customize_text" for="email"><?php echo $field['field_label'];?> <br /> <span class="sub-title customize_text"> <?php echo $field['field_des'];?></span></label>
		          	<div class="controls">
		            	<input type="text" id="<?php echo $field['field_name'];?>"  <?php echo $required;?>  placeholder="<?php echo $field['field_pholder'];?>" value="<?php echo $value;?>" name="<?php echo $field['field_name'];?>" class="input-xlarge <?php echo $required ?>" >
		          	</div>
		        </div>
				<?php
				# code...
				break;
		}

	}
	function ce_field_mobile_footer(){
		?>
		<style type="text/css">
			.ce-sf-item ul li{
				list-style: none;
			}
			.ce-sf-item-select .ui-select .ui-btn-inner{
				border:none;
				text-shadow:none;
				box-shadow: none;
			}
			.ce-sf-item-select  .ui-select  .ui-btn,
			.ce-sf-item-select  .ui-select  .ui-btn:hover{
				background-color: #fff !important;
			}
			p.text-csf-field{
				text-align: left;
				font-size: 1em;
			}
			.content-listing .content-tabs p.text-csf-field{
				font-size: 1em;
			}
			.content-listing .content-tabs p.ext-field{
				font-size: 1em;
			}
		</style>
		<?php
	}
}
/**
 * show template a field in edit from ad, add form ad in front-end when user using desktop.
 * @param $field
 *
 */
function show_field_template($field)
{
	$type = isset($field['field_type']) ? $field['field_type'] : 'text';
	$fieldCats = isset($field['field_cats']) ? '[\'' . implode('\',\'', $field['field_cats']) . '\']' : '';
	?>
	<# if( _.contains(<?php echo $fieldCats ?>, catId) ) { #> <?php //Check if field assigned to catId ?>
	<div class="form-group  field-form-item clearfix" data-cats="<?php echo $fieldCats ?>" id="<?php echo $field['field_name']; ?>">
		<label for="<?php echo trim($field['field_name']) ?>" class="control-label"><?php _e($field['field_label'], ET_DOMAIN); ?> <br/>
			<span class="sub-title customize_text"><?php echo $field['field_des']; ?></span>
		</label>
		<?php
		$class = '';
		if ($field['field_required'])
			$class .= 'required ';

		if ($type == 'textarea') {
			?>
			<div class="controls">
				<textarea
					class="<?php echo $class; ?>" <?php if (isset($field['field_pholder'])) echo 'placeholder="' . $field['field_pholder'] . '"'; ?>
					rows="5" name="<?php echo $field['field_name']; ?>">{{ model.get("<?php echo $field['field_name'] ?>") }}</textarea>
			</div>
		<?php
		} else {
			if ($field['field_type'] == 'date') {
				$class .= 'ce_field_datepicker ';
			}
			?>
			<div class="controls">
				<input class="<?php echo $class; ?>" placeholder="<?php echo $field['field_pholder']; ?>" type="text"
					   name="<?php echo trim($field['field_name']) ?>" value="{{ model.get("<?php echo $field['field_name'] ?>") }}"/>
			</div> <?php
		}
		?>
	</div>
	<# } #>
	<?php
}
/**
 * show template a field in edit from ad, add form ad in front-end when user using mobile.
 * @return [type] [description]
 */
function show_field_template_mobile($field){
	if(empty($field))
		return '';
	$type = isset($field['field_type']) ? $field['field_type'] : 'text';
	$fieldCats = isset($field['field_cats']) ? '["' . implode('","', $field['field_cats']) . '"]' : '';
	?>
	<div style="<?php if ($fieldCats != '') : ?> display: none <?php endif; ?>" class="post-new-classified <?php if ($fieldCats != '') : ?> hidden-custom-field <?php endif; ?> ui-field-contain ui-body ui-br form-group clearfix field-form-item" data-cats='<?php echo $fieldCats ?>' id="<?php echo $field['field_name']; ?>'>
		<label for="<?php echo trim($field['field_name']); ?>"
			   class="ui-input-text"><?php _e($field['field_label'], ET_DOMAIN); ?> <br/>
			<span class="subtitle"><?php echo $field['field_des'];  if($field['field_required']) echo '(*)'; ?></span>
		</label>
		<?php
		$class = '';
		$required = "";
		if ($field['field_required'])
			$required .= ' required="" ';

		if ($type == 'textarea') {
			?>
			<textarea <?php echo $required; ?>
				<?php if ($fieldCats != '') : ?>disabled<?php endif; ?>
				class="<?php echo $class; ?>" <?php if (isset($field['field_pholder'])) echo 'placeholder="' . $field['field_pholder'] . '"'; ?>
				rows="5" name="<?php echo $field['field_name']; ?>"></textarea>
		<?php
		} else {
			if ($field['field_type'] == 'date') {
				$class .= 'ce_field_datepicker ';
			}
			?>
			<input <?php echo $required; ?>
				class="<?php echo $class; ?> <?php echo $required; ?>"
				<?php if ($fieldCats != '') : ?>disabled<?php endif; ?>
				placeholder="<?php echo $field['field_pholder']; ?>" type="text"
				name="<?php echo trim($field['field_name']) ?>"
				value=""/>
		<?php
		}
		?>
	</div>
	<?php
}

/**
 * show html a field in edit from ad, add form ad in front-end when user using mobile.
 * @return [type] [description]
 */
function show_field_html_mobile($field){
	if(empty($field))
		return;
	$type = isset($field['field_type']) ? $field['field_type'] : 'text';
	?>
	<div class="post-new-classified ui-field-contain ui-body ui-br form-group clearfix field-form-item" id="<?php echo $field['field_name']; ?>">
		<label for="<?php echo trim($field['field_name']); ?>"
			   	class="ui-input-text">
			   	<?php 
			   	_e($field['field_label'], ET_DOMAIN); ;
			   	?> <br/>
			<span class="subtitle"><?php echo $field['field_des']; if($field['field_required']) echo '(*)'; ?></span>
		</label>
		<?php
		$class = '';
		$required = "";
		if ($field['field_required'])
			$required .= ' required="" ';

		if ($type == 'textarea') {
			?>
			<textarea <?php echo $required; ?>
				class="<?php echo $class; ?>" <?php if (isset($field['field_pholder'])) echo 'placeholder="' . $field['field_pholder'] . '"'; ?>
				rows="5" name="<?php echo $field['field_name']; ?>"></textarea>
		<?php
		} else {
			if ($field['field_type'] == 'date') {
				$class .= 'ce_field_datepicker ';
			}
			?>
			<input <?php echo $required; ?> class="<?php echo $class; ?> " id="<?php echo trim($field['field_name']) ?>"
											placeholder="<?php echo $field['field_pholder']; ?>" type="text"
											name="<?php echo trim($field['field_name']) ?>"/>
		<?php
		}
		?>
	</div>
<?php
}

/**
 * show html a field in edit from ad, add form ad in front-end when user using desktop.
 * @param $field
 */
function show_field_html($field){
	if(empty($field))
		return '';
	$type = isset($field['field_type']) ? $field['field_type'] : 'text';
	?>
	<div class="form-group  field-form-item clearfix modal-field-edit">
		<label for="<?php echo trim($field['field_name']) ?>" class="control-label customize_text"><?php _e($field['field_label'], ET_DOMAIN); ?> <br/>
			<span class="sub-title customize_text"><?php echo $field['field_des']; ?></span>
		</label>
		<?php
		$class = '';
		if ($field['field_required'])
			$class .= 'required ';

		if ($type == 'textarea') {
			?>
			<div class="controls">
				<textarea
					class="<?php echo $class; ?>" <?php if (isset($field['field_pholder'])) echo 'placeholder="' . $field['field_pholder'] . '"'; ?>
					rows="5" name="<?php echo $field['field_name']; ?>"></textarea>
			</div>
		<?php
		} else {
			if ($field['field_type'] == 'date') {
				$class .= 'ce_field_datepicker ';
			}
			?>
			<div class="controls">
				<input class="<?php echo $class; ?>" placeholder="<?php echo $field['field_pholder']; ?>" type="text"
					   name="<?php echo trim($field['field_name']) ?>" value=""/>
			</div> <?php
		}
		?>
	</div>
<?php
}
/**
 * show html a taxonomy in edit ad form,add ad form.
 * @param  [type] $tax [description]
 * @return [type]      [description]
 */
function show_tax_html($tax){
	$type = isset($tax['tax_type']) ? $tax['tax_type'] : 'select';
	$terms = get_terms( $tax['tax_name'],array('hide_empty' =>false));
	switch ($type) {
		case 'checkbox':
		case  'radio':
			if($terms){
				?>
				<div class="form-group clearfix field-form-item">
					<label for="title" class="control-label"><?php _e($tax['tax_label'],ET_DOMAIN);?><br>
						<span class="sub-title customize_text"><?php _e($tax['tax_des'],ET_DOMAIN);?>  </span>
					</label>
				<div class = "controls">
				<?php

				foreach($terms as $key=>$term){
					echo '<input id="'.$term->slug.'" type="'.$tax['tax_type'].'" name="'.$tax['tax_name'].'" value="'.$term->term_id.'" />';
					echo '<label for="'.$term->slug.'">'.$term->name.' </label>';
				}
				echo '</div> </div>';
			}
			break;
		default:
			if($terms){ ?>
				<div class="form-group field-form-item clearfix">
					<label for="title" class="control-label customize_text"><?php _e($tax['tax_label'],ET_DOMAIN);?><br>
						<span class="sub-title customize_text"><?php _e($tax['tax_des'],ET_DOMAIN);?>  </span>
					</label>
					<div class = "controls select-style">
						<?php
						echo '<select name="'.$tax['tax_name'].'">';
						//echo '<option> select '.$tax['tax_label'].' </option>';
						foreach($terms as $term){
							echo '<option value="'.$term->term_id.'">'.$term->name.' </option>';
						}
						echo '</select>';
					echo '</div></div>';
			}
			break;
	}
}
/**
 * show html a taxonomy in edit ad form,add ad form when user using mobile.
 * @param  [type] $tax [description]
 * @return [type]      [description]
 */
function show_tax_html_mobile($tax){
	$type 		= isset($tax['tax_type']) ? $tax['tax_type'] : 'select';
	$terms 		= get_terms( $tax['tax_name'],array('hide_empty' =>false));
	$last_fix 	= '';
 	if($tax['tax_type'] == 'checkbox')
		$last_fix = '[]';

	switch ($type) {
		case 'checkbox':
		case  'radio':
			if($terms){
				?>
				<div class="post-new-classified category ui-field-contain ui-body ui-br form-group clearfix field-form-item">
					<label for="<?php echo $tax['tax_name'];?>" class="control-label"><?php _e($tax['tax_label'],ET_DOMAIN);?><br>
						<span class="sub-title subtitle customize_text"><?php _e($tax['tax_des'],ET_DOMAIN);?>  </span>
					</label>
				<div class = "controls">
				<?php


				foreach($terms as $key=>$term){
					echo '<input id="'.$term->slug.'" type="'.$tax['tax_type'].'" name="'.$tax['tax_name'].$last_fix.'" value="'.$term->term_id.'" />';
					echo '<label for="'.$term->slug.'">'.$term->name.' </label>';
				}
				echo '</div> </div>';
			}
			break;
		default:
			if($terms){ ?>
				<div data-role="fieldcontain" class="post-new-classified category form-group clearfix field-form-item" data-ad="<?php echo CE_AD_CAT; ?>">
			        <label for="day">
			        	<?php _e($tax['tax_label'],ET_DOMAIN); ?>
			        	<span class="subtitle"><?php _e("Select yout area", ET_DOMAIN); ?></span>
			        </label>
			        <?php
			        	$current_tax = wp_get_post_terms(get_the_ID(),  $tax['tax_name'], array("fields" => "all"));
			        	$id_curent = isset($current_tax[0]->term_id) ? $current_tax[0]->term_id : -1;

			        	ce_dropdown_tax (	'color' ,
		        							array( 'show_option_all' => $tax['tax_label'],
				        							'name' 			=> $tax['tax_name'],
				        							'id' 			=> $tax['tax_name'],
				        							'taxonomy' 		=> $tax['tax_name'],
				        							'hide_empty'	=> false,
				        							'hierarchical' 	=> true,
				        							'selected'		=> $id_curent,
				        							'attr' 			=> array( 'data-native-menu' => 0 )
				        						)
		        							);
					        		?>
			    </div> <?php
			}
			break;
	}
}