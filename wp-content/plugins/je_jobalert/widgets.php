<?php
class JE_ALERT_WIDGET extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_je_alert', 'description' => __( 'A form to subscribe for new jobs notification emails.', ET_DOMAIN) );
		parent::__construct('je_alert', __('JE Job Alert', ET_DOMAIN), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$before_widget	=	str_replace('widget ', 'widget widget-select content-dot ', $before_widget);
		echo $before_widget;
		if( $instance['title'] != '')
			echo $before_title . $instance['title'] . $after_title; 
	?>
		<div   class="widget-job-alert bg-grey-widget">
			<form action="" menthod="post">
				<!-- <div class="form-item">
					<input type="text" placeholder="Job Title" class="bg-default-input" />
				</div> -->
				<div id="container">
				<div class="form-item">
			 	<?php 
						$job_cats	=	get_terms('job_category', array('orderby' => 'id', 'order' => 'ASC','hide_empty' => false ,'pad_counts' => true));
					?>
					<div class="margin0 btn-background border-radius">
						<?php 
							je_job_cat_select_alert ('job_category',__("Select Category", ET_DOMAIN), array('id' => ''));
						 ?>
						
					</div>
				</div>
				<div class="form-item margintop">
					<input type="text" placeholder="<?php _e("City, Country", ET_DOMAIN); ?>" class="bg-default-input" name="location" value="" />
				</div>
				<div class="form-item">
					<input type="text" placeholder="<?php _e("Email", ET_DOMAIN); ?>" class="bg-default-input email" name="email" required="required email" value=""/>
				</div>
				<div class="btn-submit">
					<button class="btn-subscribe bg-btn-action border-radius">
						<?php _e("Subscribe ", ET_DOMAIN); ?>
						<span class="icon" data-icon="f"></span>
					</button>
				</div>
				<input type="hidden" name="action" value="je-add-subscriber" /> 
				</div>
			</form>
		</div>
	<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title' => 'SUBSCRIBER') );
		$title = esc_attr( $instance['title'] );
		//$exclude = esc_attr( $instance['exclude'] );
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		
	<?php
	}
}


if(!function_exists('je_job_cat_children_options')) {
	function je_job_cat_children_options($tax, $cats = array(), $parent = false, $level = 0){
		// re get categories if it empty
		if (empty($cats))
			$cats = array();

		// echo 
		foreach ($cats as $cat) {
			if ( ($parent == false && !$cat->parent) || $parent == $cat->parent ){
				// seting spacing
				$space = '';
				for ($i = 0; $i < $level; $i++ )
					$space .= 'class="sub"';

				$current 	= get_query_var( $tax );
				$selected 	= $current == $cat->slug ? 'selected="selected"' : '';
				global $current_filter;
				if (empty($current_filter)) $current_filter = array();
				if ( $current == $cat->slug )
					$current_filter[$tax] = $cat->name;

				// display option tag
				echo '<option '.$space.' value="' . $cat->slug . '" '. $selected .' rel="' . $cat->name . '">' . $cat->name . '</option>';
				je_job_cat_children_options($tax, $cats, $cat->term_id, $level + 1);
			}
		} 
	}
}

if(!function_exists('je_job_cat_select_alert')) {
	function je_job_cat_select_alert($name, $label = 'Select Category', $args = array()){
		$cats = et_get_job_categories_in_order();
		$args = wp_parse_args( $args, array(
			'class' => '',
			'id' 	=> 'filter_cat',
			) );
		?>
		<select data-placeholder="<?php echo $label;?>" style="width:200px;margin-top:5px;" multiple name="<?php echo $name ?>" id="<?php echo $args['id'] ?>" class="<?php //echo $args['class'] ?>chosen-select">
			
			<?php je_job_cat_children_options('job_category', $cats); ?>
		</select>
		<?php
	}
}