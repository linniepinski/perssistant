<?php

/**
 * CE Fields Widgets
 *
 * @package WordPress
 * @subpackage Widgets
 */

/**
 * Taxonomy fillter widget class
 *
 * @since 2.8.0
 */
class CE_Fields_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_fillter_taxonomy', 'description' => __( 'A list of your site&#8217;s Pages.') );
		parent::__construct('widget_fillter_taxonomy', __('Taxonomy Fillter',ET_DOMAIN), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );

		$taxonomy 					= $instance['tax_select'];
		$argt 						= array();

		$title	=	isset($instance['title']) ? $instance['title'] : '';
		$argt['show_count'] 		= $instance['show_count'];
		$argt['taxonomy'] 			= $taxonomy;
		$argt['echo'] 				= 1;
	    $cat_args['title_li'] 		= __('Categories');
	    $argt['show_option_none'] 	= __('Taxonomy list is empty.',ET_DOMAIN);

	   	echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

	   	ce_list_term($argt);
		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		$taxs 		= CE_Fields::get_taxs();
		$instance 	= $old_instance;
		$fname 		= isset($tax[0]['tax_name']) ? $tax[0]['tax_name'] : '';
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['show_count'] = strip_tags($new_instance['show_count']);
		$instance['tax_select'] = isset($new_instance['tax_select']) ? $new_instance['tax_select'] : $fname;

		return $instance;
	}

	function form( $instance ) {
		$taxs 		= CE_Fields::get_taxs();
		$fname 		= isset($tax[0]['tax_name']) ? $tax[0]['tax_name'] : '';
		$instance 	= wp_parse_args( (array) $instance, array( 'tax_select' => $fname, 'title' => '','show_count' => false) );
		$title 	 	= esc_attr( $instance['title'] );

	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p>
			<label for="<?php echo $this->get_field_id('tax_select'); ?>"><?php _e( 'Select taxonomy:' ); ?></label>
			<select name="<?php echo $this->get_field_name('tax_select'); ?>" id="<?php echo $this->get_field_id('tax_select'); ?>" class="widefat">
				<?php
				foreach($taxs as $key=>$tax) { ?>
					<option value="<?php echo $tax['tax_name'];?>" <?php f_selected( $instance['tax_select'], $tax['tax_name'] ); ?> > <?php echo $tax['tax_label']; ?></option>
					<?php
				}
				?>
			</select>
		</p>
		<p>

			<input type="checkbox" <?php if($instance['show_count']) echo 'checked="checked"';?> id= "<?php echo $this->get_field_id('show_count'); ?>" name="<?php echo $this->get_field_name('show_count'); ?>" id="<?php echo $this->get_field_id('tax_select'); ?>" class="widefat" />
			<label for="<?php echo $this->get_field_id('show_count'); ?>"><?php _e( 'Show post count:' ); ?></label>

		</p>

	<?php
	}

}

function f_selected( $current, $check ){
	if( $current == $check ){
		echo 'selected = "selected" ';		
	}
}

add_action('widgets_init','ce_field_widget_init');
function ce_field_widget_init(){
	register_widget( 'CE_Fields_Widget' );
}
?>