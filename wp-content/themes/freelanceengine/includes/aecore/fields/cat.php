<?php 
/**
 * class AE_cat
 * render list option and form to control list
 * @version 1.0
 * @package AE
 * @author Dakachi
*/
class AE_cat {

	public $item;
	public $data;
	/**
	 * contruct a list settings in backend
	 * @param array $args : 
	  - name : required  option name
	  - id
	  - title
	  - form param array contain form args
	 * @param $template 
	 	- template the item template path  (php render)
	 	- js_template js item template path (for js app)
	 	- form The form use to submit an item (php render)
	 	- form_js The js form template use to edit an item  (for js app)
	 * @param $data  pack list data
	 * @package AE
	 * @version 1.0
	*/
	function __construct( $args = array(), $template = array() , $data   ){

		$this->data		=	$data->$args['name'];
		$this->params	=	$args;
		if( !empty( $template ) ) {
			
			$this->template = $template['template'];
			$this->js_template = $template['js_template'];

			$this->form_template	=	$template['form'];
			$this->form_js_template	=	$template['form_js'];

		}else {
			$this->template	=	ae_get_path().'/template/category.php';
			$this->js_template	=	ae_get_path().'/template-js/cat-item.php';
			
			$this->form_js_template	=	ae_get_path().'/template-js/cat-item-form.php';
		}
		// call AJAX class
		//new AE_CategoryAjax( new AE_Category( array('taxonomy' => $this->params['taxonomy']) ) );						
	} // construct

	/**
	 * render html and template
	*/
	function render() {

		echo '<div class="title group-'. $this->params['id'] .'">'. $this->params['title'] .'</div>';
		echo '<div class="desc pack-control " id="control-'. $this->params['name'] .'" data-template="'. $this->params['name'] .'">';
		?>	
		<div class="cat-list-container" id="place-categories">
			<?php 
				$args = array(
						'taxonomy' 	 => $this->params['taxonomy'],
						'label'		 => $this->params['title'],
						'use_icon'  => isset($this->params['use_icon']) ? $this->params['use_icon'] : 1 ,
						'use_color' => isset($this->params['use_color']) ? $this->params['use_color'] : 1 ,
						'hierarchical' => isset($this->params['hierarchical']) ? $this->params['hierarchical'] : 1 ,
					);
				$category	=	new AE_BackendCategory ($args);
				$category->print_backend_terms();			
			?>
		</div>
		<?php 
			include ( $this->js_template );
			include ( $this->form_js_template );
		echo '</div>';
		?>
		<script type="text/javascript">
			(function ($) {
				$(document).ready(function(){
					if(typeof AE.Views.CategoryList != 'undefined') {
						new AE.Views.CategoryList({ el: $("#control-<?php echo $this->params['name'] ?>")});
					}
				});
			})(jQuery);
		</script>
		<?php
	} // render
}