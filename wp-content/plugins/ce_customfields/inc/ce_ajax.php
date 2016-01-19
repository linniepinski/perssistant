<?php

class CE_Fields_Ajax extends ET_Base{

	public function __construct(){
		// action for meta field.
		$this->add_ajax('ce-add-field','ce_add_field',true,false);
		$this->add_ajax('delete-field','ce_del_field',true,false);
		$this->add_ajax('sort-fields','ce_sort_fields',true,false);
		$this->add_ajax('sort-seller-fields','ce_sort_seller_fields',true,false);

		// action for taxonomy in admin.
		$this->add_ajax('ce-add-tax','ce_add_taxonomy',true,false);
		$this->add_ajax('delete-taxonomy','ce_del_taxonomy',true,false);
		$this->add_ajax('toggle-taxonomy','toggle_taxonomy',true,false);

		$this->add_ajax('ce-add-field-seller','ce_add_field_seller',true,false);
		$this->add_ajax('delete-sfield','ce_del_sfield',true,false);
	}
	/**
	 * add new a field to site.
	 * @return [type] [description]
	 */
	public function ce_add_field(){

		$resp 		= array();
		$request 	= $_POST;

		$request['field_des']  		= isset($request['field_des']) ? stripslashes(trim($request['field_des'])) : '';
		$request['field_label'] 	= isset($request['field_label']) ? stripslashes(trim($request['field_label']) ) : '';
		$request['field_pholder'] 	= isset($request['field_pholder']) ? stripslashes(trim($request['field_pholder']) ) : '';
		$request['field_type'] 		= isset($request['field_type']) ? trim($request['field_type'])  : 'text';
		$request['field_name'] 		= isset($request['field_name']) ? trim($request['field_name'])  : '';

		$fields  	= (array) CE_Fields::get_fields();
		$name 		= trim($request['field_name']);


		$required 	= isset($_POST['field_required']) ? 1 : 0;
		if( isset($request['field_cats'])  ){
			$request['field_cats'] = array_unique($request['field_cats']);
		}
		//get cat field name
		// assgin required to data, it for assign value to model.
		$request['field_required'] 	= $required;
		if(empty($name)){
			wp_send_json( array('success' => false, 'msg' => __('Field name empty.')) );
		}
		if( isset($fields[$name]) ){
			$fields[$name]  	= $request;
			$resp = array('success'	=> true, 'msg'=>__('Update field success!'),'data'=>$request);

		} else {
			$fields[$name]  = $request;
			$resp 			= array('success'=> true,'msg'=>__('Add a field success!'),'data'=>$request);

		}
		//Get category name, id and add to resp object
		if(isset($request["field_cats"])){
			$categories = ET_AdCatergory::get_category_list(array(
				'include'=> implode(',',$request['field_cats'])
			));
			//add property : id
			foreach($categories as $index=>$cat){
				$resp['data']['field_cats'][$index] = $cat;
				$resp['data']['field_cats'][$index]->id = $cat->term_id;
			}
			$resp['data']['field_cats'] = $categories;
		}

		CE_Fields::set_fields($fields);
		wp_send_json($resp);

	}
	/**
	 * action for delete a field item.
	 * @return [type] [description]
	 */
	public function ce_del_field(){

		$key 	= $_POST['key'];
		$fields = (array)CE_Fields::get_fields();

		if(isset($fields[$key])){
			unset($fields[$key]);
			$result = CE_Fields::set_fields($fields);
		}
		wp_send_json(array('success'=>true,'msg'=>__('Delete field success',ET_DOMAIN), 'key_del'=>$key));

	}

	public function ce_del_sfield(){

		$key 	= $_POST['key'];
		$fields = (array)CE_Fields::get_seller_fields();

		if(isset($fields[$key])){
			unset($fields[$key]);
			$result = CE_Fields::set_seller_fields($fields);
		} else {

			$pos = -1;
			foreach($fields as $index=>$value){

				if($key == $value['field_name']){
					$pos = $index;
					continue;
				}
			}
			unset($fields[$pos]);
			$result = CE_Fields::set_seller_fields($fields);
		}

		wp_send_json(array('success'=>true,'msg'=>__('Delete seller field success',ET_DOMAIN), 'key_del'=>$key));

	}

	/**
	 * action for auto sort item.
	 * @return [type] [description]
	 */
	public function ce_sort_fields(){

		$options 	= array();
		$request 	= array();
		$name 		= '';

		if(isset($_POST['field_key'])){

			$request 	= (array)$_POST['field_key'];
			$options 	= CE_Fields::get_fields();
			$temp 	= 	array();
			foreach ($request as $value) {
				if(isset($options[$value]))
					$temp[$value] = $options[$value];
			}

			CE_Fields::set_fields($temp);
			wp_send_json( array('success'=>true,'msg'=> __('Sort fields was successful!',ET_DOMAIN),'data' => $request ) );


		}	else if( isset($_POST['tax_key']) ){

			$request 	= (array)$_POST['tax_key'];
			$options 	= CE_Fields::get_taxs();
			$name 		= CE_Fields::CE_FIELD_TAX;

			$temp 	= 	array();

			foreach ($request as $key => $value) {

				if( !isset($options[$value])   )
					wp_send_json( array('success'=>true,'msg'=> __('Key do not exitst!',ET_DOMAIN) ) );

				$temp[$value] = $options[$value];

			}

			CE_Fields::set_taxs($temp);
			wp_send_json( array('success'=>true,'msg'=> __('Sort taxonomies was successful!',ET_DOMAIN),'data' => $request ) );
		}

		wp_send_json( array('success'=>false,'msg'=> __('Sort fields is false !',ET_DOMAIN),'data' => $request ) );
	}

	function ce_sort_seller_fields(){
		$options 	= array();
		$request 	= array();
		$name 		= '';

		if(isset($_POST['field_key'])){

			$request 	= (array)$_POST['field_key'];
			$options 	= CE_Fields::get_seller_fields();
			$temp 		= 	array();

			foreach ($request as $value) {
				$temp[$value] = $options[$value];

			}

			CE_Fields::set_seller_fields($temp);
			wp_send_json( array('success'=>true,'msg'=> __('Sort fields was successful !',ET_DOMAIN),'data' => $request,'db'=>$temp ) );


		}
		wp_send_json( array('success'=>false,'msg'=> __('Sort fields is false !',ET_DOMAIN),'data' => $request ) );
	}

	public function ce_add_taxonomy(){

		$request 	= $_POST;

		$request['tax_slug'] 	= isset($request['tax_slug']) ? stripslashes( trim($request['tax_slug']) ) : '';
		$request['tax_label'] 	= isset($request['tax_label']) ? stripslashes( trim($request['tax_label'])) : '';
		$request['tax_name'] 	= isset($request['tax_name']) ? strtolower(trim($request['tax_name'])) : '';
		$request['tax_type'] 	= isset($request['tax_type']) ? trim($request['tax_type']) : '';
		$request['tax_slug'] 	= isset($request['tax_slug']) ? strtolower(trim($request['tax_slug'])) : '';
		$tax_name 				= $request['tax_name'];

		if( empty($request['tax_slug']) || empty($request['tax_type']) || empty($tax_name) )
			wp_send_json(array('success' => false, 'msg' => __('Fields taxonomy do not allow empty',ET_DOMAIN)));

		// check taxonomy exists.
		if(taxonomy_exists($tax_name) && !isset($request['id']))
			wp_send_json(array('success' => false, 'msg' => __('Taxonomy is exists!',ET_DOMAIN)));

		$taxs 			= (array) CE_Fields::get_taxs();

		$request['key'] = $tax_name;

		if( isset($request['id']) ) {

			$id 					= 	$request['id'];
			$request['tax_status'] 	= 	isset($taxs[$id]['tax_status']) ? $taxs[$id]['tax_status'] : 1;
			// update

			$taxs[$tax_name] 	= $request;
			$resp 				= array('success' => true,'msg' => __('Updated taxonomy was successful',ET_DOMAIN),'data' =>  $request );

		} else {

			//insert
			$tax_exists = $this->check_taxonomy_exitst($tax_name, $request['tax_slug'], $request['key']);
			if($tax_exists)
				wp_send_json(array('success'  => false,'msg'=>__('Taxonomy name or taxonomy slug are exitsts!')));


			$request['id'] 			= 	$request['key'];
			$request['tax_status'] 	= 	1;
			$resp 					= 	array('success' => true,'msg' => __('Add new taxonomy successful',ET_DOMAIN),'data' =>  $request );

			$taxs[$tax_name] 		=  	$request;

		}

		CE_Fields::set_taxs($taxs);

		wp_send_json($resp);
	}
	public function toggle_taxonomy(){

		$resp 	= array('success' => false,'msg'=>' Active false');
		$tax 	= CE_Fields::get_taxs();
		$key 	= isset($_POST['key']) ? $_POST['key'] : 0;

		$toggle = ($tax[$key]['tax_status'] == 1) ? 0 : 1;

		$tax[$key]['tax_status'] = $toggle;
		CE_Fields::set_taxs($tax);

		wp_send_json(array('success'=>true,'msg'=>__('Update status taxonomy success',ET_DOMAIN), 'data'=>$toggle));
	}

	/**
	* Add field for seller
	* @since 2.1
	*/

	public function ce_add_field_seller(){
		$resp = array('success' => false, 'msg'=>__("Add field fail",ET_DOMAIN));
		$resp 		= array();
		$request 	= $_POST;
		$request['method'] = '';
		$request['field_des']  		= isset($request['field_des']) ? stripslashes(trim($request['field_des'])) : '';
		$request['field_label'] 	= isset($request['field_label']) ? stripslashes(trim($request['field_label']) ) : '';
		$request['field_pholder'] 	= isset($request['field_pholder']) ? stripslashes(trim($request['field_pholder']) ) : '';
		$request['field_type'] 		= isset($request['field_type']) ? trim($request['field_type'])  : 'text';
		$request['field_name'] 		= isset($request['field_name']) ? trim($request['field_name'])  : '';

		$fields  	= (array) CE_Fields::get_seller_fields();
		$name 		= trim($request['field_name']);


		$required 	= isset($_POST['field_required']) ? 1 : 0;
		if( isset($request['field_cats'])  ){
			$request['field_cats'] = array_unique($request['field_cats']);
		}
		//get cat field name
		// assgin required to data, it for assign value to model.
		$request['field_required'] 	= $required;
		if(empty($name)){
			wp_send_json( array('success' => false, 'msg' => __('Field name empty.')) );
		}
		if( isset($fields[$name]) ){
			$fields[$name]  	= $request;
			$request['method'] = 'update';
			$resp = array('success'	=> true, 'msg'=>__('Update seller\'s field success!'),'data'=>$request);

		} else {
			$fields[$name]  = $request;
			$resp 			= array('success'=> true,'msg'=>__('Add a field success!'),'data'=>$request);

		}

		if(isset($request['sf_options'])){

			$options = $request['sf_options'];
			$list = array();
			foreach ($options as $key=>$option) {
				if(!empty($option))
					$list[$key] = $option;
			}
			update_option("ce_sf_".$name, $list);
			$field[$name]['all_values'] = (array)$list;
		}


		CE_Fields::set_seller_fields($fields);

		wp_send_json($resp);

	}

	public function ce_del_taxonomy(){
		$key 	= $_POST['key'];
		$tax 	= CE_Fields::get_taxs();
		unset($tax[$key]);
		CE_Fields::set_taxs($tax);

		wp_send_json(array('success'=>true,'msg'=>__('Delete taxonomy success',ET_DOMAIN), 'key_del'=>$key));
	}
	public function check_taxonomy_exitst($tax_name,$tax_slug){
		$taxs = CE_Fields::get_taxs();
		if( is_array($taxs) && !empty( $taxs) ){
			foreach ($taxs as $key => $tax) {
				if( ($tax['tax_name'] == $tax_name )|| ($tax['tax_slug'] == $tax_slug) )
					return true;
			}
		}
		return false;
	}
	public function check_field_exitst( $field_name, $pos=-1 ){
		$fields = CE_Fields::get_fields();
		if(is_array($fields) && empty($fields) ) {
			foreach ($fields as $key => $field) {
				if($key == $pos)
					continue;
				if( ($field['field_name'] == $field_name ))
					return true;
			}
		}
		return false;
	}
}
?>