<?php


class ET_Slider_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'ET_Slider_Widget', // Base ID
			'ET Slider', // Name
			array( 'description' => __( 'Drag this widget to any sidebar to display your images in slides.', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		extract( $args );
		$widget 	= explode('-',$args['widget_id']);
		$class 		= 'et_slider_widget_'.$widget[1];
		$speed 		= isset($instance['speed']) ? $instance['speed'] :5000;
		$slider_id 	= !empty($instance['slider_id']) ? $instance['slider_id'] : -1;
		$ws_height 	= !empty($instance['ws_height']) ? $instance['ws_height'] : '200';
		$hide_text 		= (isset($instance['hide_text']) && $instance['hide_text'] == 1 ) ? true : false;
		echo $before_widget;

		$et_slide = new ET_Slider_Front;
		echo $et_slide->et_slider_display($slider_id, $class, $speed, $hide_text, $ws_height );

		echo $after_widget;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
			//$type 	= 	isset($instance[ 'type' ]) ? $instance[ 'type' ] : '';
			$number 		= 	isset($instance['number']) ? $instance['number'] : 3;
			$speed 			=	isset($instance['speed']) ? $instance['speed'] : 5000;
			$hide_text 		=	isset($instance['hide_text']) ? $instance['hide_text'] : 0;
			$ws_height 		=	isset($instance['ws_height']) ? $instance['ws_height'] : 300;
			$slider_id 		=	isset($instance['slider_id']) ? $instance['slider_id'] : -1;

		?>

		<p>
		<label class="lb-slider-widget" for="<?php echo $this->get_field_name( 'slider_id' ); ?>"><?php _e( 'Select Slider:' ); ?></label> 
		<select name = "<?php echo $this->get_field_name( 'slider_id' ); ?>">
			<option value="-1">Select Slider</option>
			<?php
			$posts = get_posts('post_type=et_slider&post_status=publish&post_parent=0&numberposts=-1');
			foreach($posts as $post){
				$selected = $post->ID == $slider_id ? 'selected="selected"' : '';
				echo '<option value ="'.$post->ID.'" '.$selected.'>'.$post->post_title.'</option>';
			}
			?>
		</select>
		</p>
		<p>
			<label class="lb-slider-widget" for="<?php echo $this->get_field_name( 'speed'); ?>"><?php _e( 'Slide Speed' ); ?></label> :
			<input   class="wideat input-et-speed input-slider" id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>" type="text" value="<?php echo esc_attr( $speed ); ?>" />
		</p>
		<p>
			<label class="lb-slider-widget" for="<?php echo $this->get_field_name( 'ws_height'); ?>"><?php _e( 'Slide Height(px)' ); ?></label> :
			<input   class="wideat input-et-speed input-slider" id="<?php echo $this->get_field_id( 'ws_height' ); ?>" name="<?php echo $this->get_field_name( 'ws_height' ); ?>" type="text" value="<?php echo  $ws_height ; ?>" />
		</p>
		<p>
			<label class="lb-slider-widget" for="<?php echo $this->get_field_name( 'hide_text'); ?>"><?php _e( 'Hide text' ); ?></label> :
			<input <?php if($hide_text == 1) echo 'checked';?>  class="wideat" id="<?php echo $this->get_field_id( 'hide_text' ); ?>" name="<?php echo $this->get_field_name( 'hide_text' ); ?>" type="checkbox" />
		</p>

		<style>
		.lb-slider-widget{
			width: 100px; float: left;
		}
		.input-slider{width: 40px;}
		</style>	
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();		
		
		$instance['speed'] 			= (is_numeric( $new_instance['speed'] ) ) ? $new_instance['speed'] : 5000;	
		$instance['slider_id'] 		= (is_numeric( $new_instance['slider_id'] ) ) ? $new_instance['slider_id'] : 0;
		$instance['hide_text'] 		= ($new_instance['hide_text'] == 'on' ) ? 1 : 0;
		$instance['ws_height'] 		= $new_instance['ws_height'];		
		
		return $instance;
	}

} // class Foo_Widget
// php 5.3 use this function 
//add_action( 'widgets_init', function() { register_widget( 'ET_Slider_Widget' ); } );

// php 5.2 use this function;
add_action('widgets_init',   create_function('', 'return register_widget("ET_Slider_Widget");'));
?>