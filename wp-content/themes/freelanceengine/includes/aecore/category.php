<?php
/**
 * Terms & Taxonomy
 */
class AE_Term extends AE_Base{
	public $transient 		= '';
	public $taxonomy 		= '';
	public $label 			= '';
	public $order_option 	= '';

	public function __construct($args = array()){
		$args = wp_parse_args( $args, array('taxonomy' => 'place_category', 'label' => 'Place Category') );
		$this->taxonomy  	= $args['taxonomy'];
		$this->transient 	= $args['taxonomy'];
		$this->label 		= $args['label'];
		$this->order_option = 'et_order_' . $args['taxonomy'];
	}

	public function getAll($args = array()){
		$ordered    = array();
		$result 	= array();
        $args   	= wp_parse_args( $args,  array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
        $categories = get_terms($args['taxonomy'], $args);
        
        $order      = get_option($this->order_option);
        
        if ($order) {
            foreach ($order as $pos) {
                foreach ($categories as $key => $cat) {
                    if ($cat->term_id == $pos['item_id']){
                        $ordered[] = $cat;
                        unset($categories[$key]);
                    }
                }
            }
            if (count($categories) > 0)
                foreach ($categories as $cat) {
                    $ordered[] = $cat;
                }
            set_transient($this->transient, $ordered);
        }else {
            set_transient($this->transient, $categories);
            $ordered = $categories;
        }

        return $ordered;
	}

	public function create($term, $args = array()){
		$taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : 'category' ;
		unset($args['taxonomy']);

        $result = wp_insert_term( $term , $taxonomy, $args );
		do_action( 'et_insert_term' , $result, $args );
		do_action( 'et_insert_term_' . $this->taxonomy, $result, $args );

        return $result;
	}

	public function update($id, $args = array()){
		$taxonomy = isset($args['taxonomy']) ? $args['taxonomy'] : 'category' ;
		unset($args['taxonomy']);

        $result = wp_update_term( $id , $taxonomy, $args );
		do_action( 'et_update_term' , $args );
		do_action( 'et_update_term_' . $this->taxonomy, $args );

        return $result;
	}

	public function sort($data){

        update_option($this->order_option, $data);
	}

	public function delete($id, $taxonomy, $default = false){
		if ($default)
			$result = wp_delete_term($id, $taxonomy, array( 'default' => $default ));
		else 
			$result = wp_delete_term($id, $taxonomy );

		do_action( 'et_delete_term' , $result );
		do_action( 'et_delete_term_' . $taxonomy, $result );

		return $result;
	}
}
/**
 * Category manupilations
 */
class AE_Category extends AE_Term {
	public $color_option = '';

	public function __construct($args = array()){
		$args = wp_parse_args( $args, array('taxonomy' => 'place_category', 'label' => 'Place Category') );
		parent::__construct($args);
		$this->taxonomy 	= $args['taxonomy'];
		$this->color_option = 'et_color_'.$args['taxonomy'];
		$this->icon_option  = 'et_icon_'.$args['taxonomy'];
		$this->is_use_icon	= isset($args['use_icon']) ? $args['use_icon'] : 1;
		$this->is_use_color	= isset($args['use_color']) ? $args['use_color'] : 1;
		$this->hierarchical	= isset($args['hierarchical']) ? $args['hierarchical'] : 1;


		// $this->add_filter( 'get_term', 'get_term' );

	}

	public function getAll($args = array()){
		$ordered    = array();
		$result 	= array();
		// check number of category
        $number = isset($args['number']) ?  $args['number'] : 0;
        $just_parent =   false;
        if(( isset($args['parent']) && $args['parent'] == 0 ) ){
        	$just_parent =   true;	
        	unset($args['parent']);
        }
        unset($args['number']);

        $args   	= wp_parse_args( $args,  array( 'order' => 'DESC','hide_empty' => false ,'pad_counts' => true));
        $categories = get_terms($this->taxonomy , $args);
        
        $order      = get_option($this->order_option);
        $colors = $this->get_color();
        $icon = $this->get_icon();

        if ( $order && !isset($args['orderby']) ) {
            foreach ($order as $id => $parent) {
                foreach ($categories as $key => $cat) {
                    if ($cat->term_id == $id ){
                    	
                    	if(isset($colors[$id])) {
                    		$cat->color = ($colors[$id]) ? $colors[$id] : '#aaa';
                    	}
                    	if(isset($icon[$id])) {
                    		$cat->icon = $icon[$id];
                    	}
                        $ordered[] = $cat;
                        unset($categories[$key]);
                    }
                }
            }
            if (count($categories) > 0)
                foreach ($categories as $cat) {
                	$id = $cat->term_id;
                	if(isset($colors[$id])) {
                		$cat->color = $colors[$id];
                	}
                	if(isset($icon[$id])) {
                		$cat->icon = $icon[$id];
                	}
                    $ordered[] = $cat;
                }
            set_transient($this->transient, $ordered);
        }else {
            set_transient($this->transient, $categories);
            $i = 0;
            foreach ($categories as $cat) {
            	// skip child term
            	if($just_parent && $cat->parent != 0 ) continue;
            	if($number && $i >= $number) break;
            	$id = $cat->term_id;
            	if(isset($colors[$id])) {
            		$cat->color = $colors[$id];
            	}
            	if(isset($icon[$id])) {
            		$cat->icon = $icon[$id];
            	}
                $ordered[] = $cat;
                $i++;
            }
        }

        return $ordered;
	}

	public function create($term, $args = array()){
		$result = parent::create($term, $args);

		// add color
		if ( !is_wp_error( $result ) && isset($args['color']) )
			$this->set_term_color( $result['term_id'], $args['color'], $args['taxonomy']);

		// add icon 
		if ( !is_wp_error( $result ) && isset($args['icon']) )
			$this->set_term_icon( $result['term_id'], $args['icon'], $args['taxonomy'] );	
				
		return $result;
	}

	public function update($id, $args = array()){
		$result = parent::update($id, $args);

		// add color
		if ( !is_wp_error( $result ) && isset($args['color']) )
			$this->set_term_color( $result['term_id'], $args['color'], $args['taxonomy'] );
		// add icon 
		if ( !is_wp_error( $result ) && isset($args['icon']) )
			$this->set_term_icon( $result['term_id'], $args['icon'], $args['taxonomy'] );			

		return $result;
	}

	public function sort( $data ,$args = array()){
		$taxonomy = $args['taxonomy'];
		unset($args['taxonomy']);
        $args  = wp_parse_args( $args,  array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
		$terms = get_terms( $taxonomy, $args );

		foreach ($terms as $key => $term) {
			foreach ($data as $id => $parent) {
				if ( $term->term_id == $id && !empty($parent) )
					wp_update_term( $term->term_id, $taxonomy, array('parent' => $parent) );
			}
		}

        update_option($this->order_option, $data);
	}

	protected function set_term_color($term_id, $color, $taxonomy){
		$colors = get_option( 'et_color_'.$taxonomy );

		if ( !is_array( $colors ) ) $colors = array();

		$colors[$term_id] = $color;
		update_option( 'et_color_'.$taxonomy, $colors );
	}

	protected function set_term_icon($term_id, $icon, $taxonomy){
		$icons = get_option( 'et_icon_'.$taxonomy );

		if ( !is_array( $icons ) ) $icons = array();

		$icons[$term_id] = $icon;
		update_option( 'et_icon_'.$taxonomy, $icons );
	}

	public function get_term_color($term_id, $taxonomy){
		$colors = get_option( 'et_color_'.$taxonomy );
		return isset($colors[$term_id]) ? $colors[$term_id] : 1;
	}

	public function get_term_icon($term_id, $taxonomy){
		$icons = get_option( 'et_icon_'.$taxonomy );
		return isset($icons[$term_id]) ? $icons[$term_id] : 'fa-map-marker';
	}

	function set_color ($colors) {
		//this function should be override if tax have color
		update_option( 'et_color_'.$this->taxonomy, $colors);
	}

	function set_icon ($icons) {
		//this function should be override if tax have color
		update_option( 'et_icon_'.$this->taxonomy, $icons);
	}

	function get_icon () {
		// this function should be override if tax have color
		return (array) get_option( 'et_icon_'.$this->taxonomy , array());
	}

	function get_color () {
		// this function should be override if tax have color
		return (array) get_option( 'et_color_'.$this->taxonomy , array());
	}

	function change_color(){
		$resp = array();
		if ( !empty($_REQUEST['content']['term_id']) && !empty($_REQUEST['content']['color']) ){
			$this->update_term_color($_REQUEST['content']['term_id'], $_REQUEST['content']['color']);
			$resp = array(
				'success'   => true,
				'msg'       => sprintf(__('%s color has been updated', ET_DOMAIN), $this->_tax_label )
				);
		}
		else {
			$resp = array(
				'success'   => false,
				'msg'       => __("An error has occurred!", ET_DOMAIN)
				);
		}
		return $resp;
	}

	function change_icon(){
		$resp = array();
		if ( !empty($_REQUEST['content']['term_id']) && !empty($_REQUEST['content']['icon']) ){
			$this->update_term_icon($_REQUEST['content']['term_id'], $_REQUEST['content']['icon']);
			$resp = array(
				'success'   => true,
				'msg'       => sprintf(__('%s icon has been updated', ET_DOMAIN), $this->_tax_label )
				);
		}
		else {
			$resp = array(
				'success'   => false,
				'msg'       => __("An error has occurred!", ET_DOMAIN)
				);
		}
		return $resp;
	}

	function update_term_color ($term_id, $color ) {
		$colors = $this->get_color($this->taxonomy);
	   
		$colors[$term_id] = $color;
		$this->set_color($colors);
	}

	function update_term_icon ($term_id, $icon ) {
		$icons = $this->get_icon($this->taxonomy);
	   
		$icons[$term_id] = $icon;
		$this->set_icon($icons);
	}

	static public function get_category_color($term_id, $taxonomy){
		$colors = get_option('et_color_'.$taxonomy);
		return !empty($colors[$term_id]) ? $colors[$term_id] : 0;
	}

	static public function get_category_icon($term_id, $taxonomy){
		$icons = get_option('et_icon_'.$taxonomy);
		return !empty($icons[$term_id]) ? $icons[$term_id] : 0;
	}

	static public function get_categories($args=array()){
		$handle = new AE_Category();
		return $handle->getAll($args);
	}

	public function get_term($_term){
		$_term->icon = self::get_category_icon($_term->term_id, $this->taxonomy);
		return $_term;
	}
}
class AE_BackendCategory extends AE_Category{

	function print_backend_terms ($parent = 0, $positions = false) {
			$class_no_icon = $this->is_use_icon ? '' : 'no-icon';
			$class_no_color = $this->is_use_color ? '' : 'no-color';
		?>
		<ul class="list-job-input list-tax category list-job-categories cat-sortable tax-sortable" id="place_cats" data-tax="<?php echo $this->taxonomy ?>">
		<?php 
			$this->print_backend_terms_li ($parent,$positions) ;
		?>
		</ul>
		<ul id="cat_create" class="list-job-input category add-category " data-tax="<?php echo $this->taxonomy; ?>">
			<li class="tax-item color-0">
				<div class="container">
					<div class="controls controls-2">
						<button class="button" type="submit"><span class="icon" data-icon="+"></span></button>
					</div>
					<div class="input-form input-form-1 color-default">
						<?php if($this->is_use_color){ ?>
						<div class="cursor" data="#000000">
							<span class="flag" style="background-color:#000000;"></span>
							<div class="color-panel" style="display:none;">
							<?php $arr_colors = array('#000000', '#1abc9c', '#3498db', '#be8cbc', '#a4bedf', '#fff146', '#e67e22', '#4e6c8a', '#9fd4a9', '#68d0f0', '#bdc3c7', '#16a085', '#2980b9', '#a286ba', '#8dbdd8', '#f5c506', '#d35400', '#34495e', '#60bf74', '#00b2d7', '#95a5a6', '#2ecc71', '#0078a0', '#9b59b6', '#8fd7d4', '#ec9e03', '#e74c3c', '#2c3e50', '#12a252', '#0090b0', '#7f8c8d', '#27ae60', '#004c7d', '#8e44ad', '#6ba5a3', '#f99138', '#c0392b', '#212f3d', '#24753c', '#004350'); 
							foreach ($arr_colors as $key => $value) {?>
								<div class="color-item" data="<?php echo $value; ?>">
									<span style="background-color:<?php echo $value ?>" class="flags"></span>
								</div>
							<?php } ?>		
								<div class="custom-color"><label>Set your custom color</label><input class="input-color color-picker " placeholder="e.g: #fbfbfb" value="" /> </div>
							</div>
						</div>
						<?php } ?>
						<?php if($this->is_use_icon){ ?>
						<div class="icon trigger" data="fa-map-marker"><i class="fa fa-map-marker"></i></div>
						<?php } ?>
						<input style="color:#000000;" class="bg-grey-input tax-name <?php echo $class_no_icon.' '.$class_no_color ?>" name="name" data-tax="<?php echo $this->taxonomy ?>" autocomplete="off" placeholder="<?php _e('Add a category', ET_DOMAIN) ?>" type="text" />
					</div>
				</div>
			</li>
		</ul>
		<?php 
	}

	function print_backend_terms_li($parent = 0, $positions = false) {
		$colors = $this->get_color($this->taxonomy);
		$icons  = $this->get_icon($this->taxonomy);
		$class_no_icon = $this->is_use_icon ? '' : 'no-icon';
		$class_no_color = $this->is_use_color ? '' : 'no-color';
		$class_no_hierarchical = (isset($this->hierarchical) && $this->hierarchical == 0) ? 'no-hierarchical' : 'sort-handle';				
		if ( !$positions )
			$positions = $this->getAll(array( 'taxonomy' => $this->taxonomy ));
		foreach ($positions as $job_pos) {
			if ( $job_pos->parent == $parent ){
			?>
			<li class="tax-item" data-id="<?php echo $job_pos->term_id ?>" data-tax="<?php echo $this->taxonomy ?>" data-color="<?php echo isset($colors[$job_pos->term_id]) ? $colors[$job_pos->term_id] : '#000' ?>" data-icon="fa-map-marker" id="tax_<?php echo $job_pos->term_id ?>">
				<div class="container">
					<div class="<?php echo $class_no_hierarchical;?> "></div>
					<div class="controls controls-2">
						<a class="button act-open-form" rel="<?php echo $job_pos->term_id ?>"  title="<?php _e('Add sub tax for this tax', ET_DOMAIN) ?>">
							<span class="icon" data-icon="+"></span>
						</a>
						<a class="button act-del" rel="<?php echo $job_pos->term_id ?>">
							<span class="icon" data-icon="*"></span>
						</a>
					</div>
					<div class="input-form input-form-1" data-action="et_update_<?php echo $this->taxonomy ?>_color">
						<?php if($this->is_use_color){ ?>
						<div class="cursor">
							<span class="flag" style="background-color:<?php echo isset($colors[$job_pos->term_id]) ? $colors[$job_pos->term_id] : '#000' ?>;"></span>
							<div class="color-panel" style="display:none;">
							<?php $arr_colors = array('#000000', '#1abc9c', '#3498db', '#be8cbc', '#a4bedf', '#fff146', '#e67e22', '#4e6c8a', '#9fd4a9', '#68d0f0', '#bdc3c7', '#16a085', '#2980b9', '#a286ba', '#8dbdd8', '#f5c506', '#d35400', '#34495e', '#60bf74', '#00b2d7', '#95a5a6', '#2ecc71', '#0078a0', '#9b59b6', '#8fd7d4', '#ec9e03', '#e74c3c', '#2c3e50', '#12a252', '#0090b0', '#7f8c8d', '#27ae60', '#004c7d', '#8e44ad', '#6ba5a3', '#f99138', '#c0392b', '#212f3d', '#24753c', '#004350'); 
							foreach ($arr_colors as $key => $value) {?>
								<div class="color-item" data="<?php echo $value; ?>">
									<span style="background-color:<?php echo $value ?>" class="flags"></span>
								</div>
							<?php } ?>		
								<div class="custom-color"><label>Set your custom color</label><input class="input-color color-picker " placeholder="e.g: #fbfbfb" value="" /> </div>
							</div>
						</div>
						<?php } ?>
						<?php if($this->is_use_icon){ ?>
						<div class="icon trigger" data="<?php echo isset($icons[$job_pos->term_id]) ? $icons[$job_pos->term_id] : 'fa-map-marker'; ?>"><i class="fa <?php echo isset($icons[$job_pos->term_id]) ? $icons[$job_pos->term_id] : 'fa-map-marker'; ?>"></i>
						</div>
						<?php } ?>
						<input style="color:<?php echo isset($colors[$job_pos->term_id]) ? $colors[$job_pos->term_id] : '#000' ?>;" class="bg-grey-input tax-name <?php echo $class_no_icon.' '.$class_no_color ?>" name="name" autocomplete="off" rel="<?php echo $job_pos->term_id ?>" type="text" value="<?php echo $job_pos->name ?>">
					</div>
				</div>
				<ul>
					<?php $this->print_backend_terms_li($job_pos->term_id, $positions); ?>
				</ul>
			</li>
			<?php
			} // end if
		} // end foreach
	}

	function print_confirm_list () {
		if(!is_array($this->_term_in_order) ) $this->getAll(array( 'taxonomy' => $this->taxonomy ));
	?>
		<script type="text/template" id="temp_<?php echo $this->taxonomy ?>_delete_confirm">
			<div class="moved-tax">
				<span><?php _e('Move jobs to', ET_DOMAIN) ?></span>
				<div class="select-style et-button-select">
					<select name="move_<?php echo $this->taxonomy ?>" id="move_<?php  echo $this->taxonomy ?>">
					
					<?php foreach ($this->_term_in_order as $term ) {  ?>
							<option value="<?php echo $term->term_id ?>"><?php echo $term->name ?></option>
					<?php } ?>
					
					</select>
				</div>
				<button class="backend-button accept-btn"><?php _e("Accept", ET_DOMAIN); ?></button>
				<a class="icon cancel-del" data-icon="*"></a>
			</div>
		</script>
	<?php 
	}
}

/**
 * Handle Ajax request about place category
 */
class AE_CategoryAjax extends AE_Base{
	public function __construct( AE_Category $tax ){
		$this->tax = $tax;
		$this->add_action('wp_ajax_et_'. $this->tax->taxonomy .'_sync',  'sync_term');
	}
	function sync_term () {
		try {
			// return false if request method is empty
			if ( empty($_REQUEST['method']) ) throw new Exception(__('There is an error occurred', ET_DOMAIN), 400);

			$method = empty( $_REQUEST['method'] ) ? '' : $_REQUEST['method'] ;
			$data   = $_REQUEST['content'];

			switch ($method) {
				case 'delete':
					$term  		= $_REQUEST['content']['id'];
					$default 	= isset($_REQUEST['content']['default']) ? $_REQUEST['content']['default'] : false;
					$taxonomy 	= $_REQUEST['content']['tax'];

					// check if category has children or not
					$children = get_terms($taxonomy, array('parent' => $term, 'hide_empty' => false));

					if ( !empty($children) )
						throw new Exception(__('You cannot delete a parent category. You need to delete its sub-categories first.', ET_DOMAIN));

					// delete
					$result   	= $this->tax->delete ( $term, $taxonomy, $default );

					if ( is_wp_error( $result ) ){
						throw new Exception($result->get_error_message());
					}
					else if ( $result ) {
						$resp = array(
							'success' => true
						);
					} else {
						throw new Exception(__("Can't delete category", ET_DOMAIN));
					}
					break;

				case 'create' :
					$term  		= $_POST['content']['name'];
					$args 		= array('color' => 0,'icon' => 'fa-map-marker');

					if ( empty($term) ) throw new Exception( __('Category name is required', ET_DOMAIN) );

					if ( isset($_REQUEST['content']['color']) ) $args['color'] = $_REQUEST['content']['color'];
					if ( isset($_REQUEST['content']['icon']) ) $args['icon'] = $_REQUEST['content']['icon'];
					if ( isset($_REQUEST['content']['parent']) ) $args['parent'] = $_REQUEST['content']['parent'];
					if ( isset($_REQUEST['content']['tax']) ) $args['taxonomy'] = $_REQUEST['content']['tax'];

					$result   	= $this->tax->create($term, $args);

					if ( is_wp_error( $result ) ){
						throw new Exception($result->get_error_message());
					}
					else {
						$data 			= get_term($result['term_id'], $args['taxonomy']);
						$data->color 	= $this->tax->get_term_color($result['term_id'], $args['taxonomy']);
						$data->icon 	= $this->tax->get_term_icon($result['term_id'], $args['taxonomy']);
						$resp = array(
							'success' 	=> true,
							'data' 		=> array(
								'term' 		=> $data,
							)
						);
					}
					break;

				case 'update' :
					$term  	= $_REQUEST['content']['id'];
					$args 	= array();

					if ( empty($term) ) throw new Exception( __("Cannot find category", ET_DOMAIN) );

					if ( isset($_REQUEST['content']['name']) ) $args['name'] 		= $_REQUEST['content']['name'];
					if ( isset($_REQUEST['content']['color']) ) $args['color'] 		= $_REQUEST['content']['color'];
					if ( isset($_REQUEST['content']['icon']) ) $args['icon'] 		= $_REQUEST['content']['icon'];
					if ( isset($_REQUEST['content']['parent']) ) $args['parent'] 	= $_REQUEST['content']['parent'];
					if ( isset($_REQUEST['content']['tax']) ) $args['taxonomy'] 	= $_REQUEST['content']['tax'];

					$result   = $this->tax->update($term, $args);
					if ( is_wp_error( $result ) ){
						throw new Exception($result->get_error_message());
					}
					else {
						$data = get_term($result['term_id'], $args['taxonomy']);
						$data->color 	= $this->tax->get_term_color($result['term_id'], $args['taxonomy']);
						$data->icon 	= $this->tax->get_term_icon($result['term_id'], $args['taxonomy']);
						$resp = array(
							'success' 	=> true,
							'data' 		=> array(
								'term' 		=> $data
							)
						);
					}
					break;

				case 'sort':
					$taxonomy = $_POST['content']['tax'];
					wp_parse_str( $_POST['content']['order'], $order );
					
					$order = $order['tax'];
					$handle = new AE_Category(array('taxonomy' => $taxonomy));
					$handle->sort($order, array('taxonomy' => $taxonomy) );

					$resp = array(
						'success' 	=> true,
						'data' 		=> array(
							'order' => $order
						)
					);
					break;

				case 'changeColor' :
					$resp   =   $this->tax->change_color($data);
					break;

				case 'changeIcon' :
					$resp   =   $this->tax->change_icon($data);
					break;

				default:
					throw new Exception(__('There is an error occurred', ET_DOMAIN), 400);
					break;
			}   
			// refresh sorted job categories
		} catch (Exception $e) {
			$resp = build_error_ajax_response(array(), $e->getMessage() );
		}
		wp_send_json( $resp );
	}
}

/**
 * Build general success response for ajax request
 * @param $data returned data 
 * @param $msg returned message
 * @param $code returned code
 * @since 1.0
 */
function build_success_ajax_response($data, $msg = '', $code = 200){
	return array(
		'success' 	=> true,
		'code' 		=> $code,
		'msg' 		=> $msg,
		'data' 		=> $data
		);
}

/**
 * Build general error response for ajax request
 * @param $data returned data 
 * @param $msg returned message
 * @param $code returned code
 * @since 1.0
 */
function build_error_ajax_response($data, $msg = '',$code = 400){
	return array(
		'success' 	=> false,
		'code' 		=> $code,
		'msg' 		=> $msg,
		'data' 		=> $data
		);
}