<?php
// Add taxonomy for job.
//register taxonomy
Class JE_Add_Tax{

	const ACTION_POST_JOB 	= 'et_post_job_fields';
	const ACTION_EDIT_JOB 	= 'et_edit_job_fields';
	const ACTION_SAVE_JOB 	= 'je_save_job';
	const TAX_NAME  		= 'skills';
	const ACTION_SINGlE_JOB_FIELDS = 'je_single_job_fields';



	public function __construct(){

		add_action('init',array($this,'je_register_taxonomy'));

		// show this taxonomy on post job form.
		add_action(self::ACTION_POST_JOB,array($this,'je_show_tax_on_job_fields'));
		add_action(self::ACTION_EDIT_JOB,array($this,'je_show_tax_on_job_fields'));

		// add taxonomy value to model
		add_filter('et_jobs_ajax_response',array($this,'et_jobs_update_taxonomy_field'), 100);

		// save taxonomy for job.
		add_action(self::ACTION_SAVE_JOB,array($this,'je_save_job_taxonomy'));

		// show skill in single-job detail
		add_action( self::ACTION_SINGlE_JOB_FIELDS, array( $this,'on_single_job_fields'),15,1 );

	}

	public function je_register_taxonomy(){
		$labels = array(
			'name'              => _x( 'skills', 'taxonomy skills name' ),
			'singular_name'     => _x( 'skills', 'taxonomy skills name' ),
			'search_items'      => __( 'Search skills',ET_DOMAIN ),
			'all_items'         => __( 'All skills',ET_DOMAIN ),
			'parent_item'       => __( 'Parent skills',ET_DOMAIN ),
			'parent_item_colon' => __( 'Parent skills',ET_DOMAIN ),
			'edit_item'         => __( 'Edit skills',ET_DOMAIN ),
			'update_item'       => __( 'Update skills',ET_DOMAIN ),
			'add_new_item'      => __( 'Add New skills',ET_DOMAIN ),
			'new_item_name'     => __( 'New Genre skills',ET_DOMAIN ),
			'menu_name'         => __( 'Skills',ET_DOMAIN ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'skills' ),
		);

		register_taxonomy( self::TAX_NAME, array( 'job' ), $args );

	}
	public function je_show_tax_on_job_fields(){

		$terms  = get_terms( self::TAX_NAME, array('hide_empty'=>false) );
		if($terms && !is_wp_error($terms)){?>
			<div class="form-item">
				<div class="label">
					<h6><?php _e('Select skills',ET_DOMAIN);;?></h6>
				</div>
				<div>
				<?php

				foreach($terms as $key=>$term){
					echo '<input type="checkbox" id="'.$term->term_id.'" class="checkbox-'.self::TAX_NAME.' checkbox-'.$term->term_id.'"  name="'.self::TAX_NAME.'[]" value="'.$term->term_id.'"> <label  for="'.$term->term_id.'">'.$term->name.' </label> &nbsp; ';
				} ?>
				</div>
			</div> <?php

		}
	}


	function et_jobs_update_taxonomy_field($job){
		$terms  = get_terms( self::TAX_NAME, array('hide_empty'=>false) );
		$return = array();
		if($terms){
			$term_list = wp_get_post_terms($job['ID'], self::TAX_NAME, array("fields" => "ids"));
			$job['fields'][] = array('ID'=>self::TAX_NAME,'value'=>$term_list,'type' => 'checkbox');
		}

		return $job;
	}


	function je_save_job_taxonomy($job_id){
		if (empty($_REQUEST['content']['raw']) ) return;

		$url = $_REQUEST['content']['raw'];
		parse_str($url,$request);
		$terms = array();
		if(isset($request[self::TAX_NAME])) {
			$terms = $request[self::TAX_NAME];
		}
		wp_set_post_terms( $job_id, $terms, self::TAX_NAME );
	}
	/*
	* display skill  in single-job detail
	*/
	function on_single_job_fields($job){
		$terms = wp_get_post_terms($job->ID, self::TAX_NAME);
		if(!is_wp_error($terms) && !empty($terms)){
			echo '<strong>'.__('Skills',ET_DOMAIN).'</strong>: ';
			$count = count($terms);

			foreach ($terms as $key => $term) {
				if($count-1 > $key)
					echo $term->name .',';
				else
					echo $term->name .'.';

			}
		}

	}
}

function je_field_get_checkbox($job_id, $term_name = ''){
	if(empty($term_name))
		$term_name = JE_Add_Tax::TAX_NAME;

	$term_list = wp_get_post_terms($job_id, $term_name);

	if(!is_wp_error($term_list) && !empty($term_list)){

		$count = count($term_list);

		foreach ($term_list as $key => $term) {
			if($key < $count -1 )
				echo $term->name.',';
			else
				echo $term->name.'.';

		}
	}
}

//new JE_Add_Tax();

?>