<?php
class CE_Alert_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_alert', 'description' => __( "Show form subcribers") );
		parent::__construct('ce_alert', __('CE Alert'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		?>
		<div id="ce_alert" class="block-alert">
			<form id="frm_alert" class="frm_alert" action="#" method="POST">
				<div class="form-group">
					<label>
						<?php _e('Please select:',ET_DOMAIN);?>
					</label>
					<select id="chosen_cat" name="sub_category[]" data-placeholder="<?php _e('Choose a category...',ET_DOMAIN);?>" class="chosen-select chosen-cat  required" multiple style="width:100%;" tabindex="4">
						<option value=""></option>
						<?php
						$categories = ce_get_categories(array('hide_empty'=>false));//ET_AdCatergory::get_category_list();
						foreach ($categories as $key => $cat) {
							echo '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
						}

						?>
	                </select>
	            </div>

	            <div class="form-group">
				<select name="sub_location[]"  data-placeholder="<?php _e('Choose a location...',ET_DOMAIN);?>" class="chosen-select  required" multiple style="width:100%;" tabindex="4">
					<?php
         			$locations = ce_get_locations(array('hide_empty'=>false));//ET_AdCatergory::get_category_list();
						foreach ($locations as $key => $local) {
							echo '<option value="'.$local->term_id.'">'.$local->name.'</option>';
						}
					?>
	          	</select>
				</div>
				<div class="form-group">
					<label>
					<?php _e('Your email :',ET_DOMAIN);?>
					</label>
					<input type="text" name="email" class="subs-email" value="<?php _e('example@example.com',ET_DOMAIN);?>" />
					<input type="hidden" name="action" value="alert-add-subscriber"/>
				</div>
				<div class="form-group">
					<button class="btn  btn-primary"> <?php _e('SUBSCRIBE',ET_DOMAIN);?> </button>
				</div>
			</form>
		</div>
		<?php
		echo $after_widget;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = $instance['title'];
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, array( 'title' => ''));
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}

}
add_action('widgets_init', 'alert_widgets_init');
function alert_widgets_init(){
	register_widget('CE_Alert_Widget');
}
?>