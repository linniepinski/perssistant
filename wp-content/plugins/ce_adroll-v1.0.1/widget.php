<?php

class AdRoll_Widget extends WP_Widget {    
 
    function AdRoll_Widget() {
        $widget_ops = array( 'classname' => 'jobroll_publisher', 'description' => 'Publisher' );
        $this->WP_Widget( 'jobroll_publisher', __('Adroll Publisher',ET_DOMAIN), $widget_ops );
    }
   
    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {
        $id = CE_AddRoll::get_page_adroll();
        
        extract( $args, EXTR_SKIP );
        echo $before_widget;
        echo $before_title;
        echo $instance['title']; // Can set this with a widget option, or omit altogether
        echo $after_title;
        echo '<p>';
        _e(" Add ads to your site! ", ET_DOMAIN);
        $url    = get_permalink($id);
       
        ?>
        <a href="<?php echo $url; ?>" ><?php _e("Click here", ET_DOMAIN); ?></a>
        <?php
        echo '</p>';
    //
    // Widget display logic goes here
    //

    echo $after_widget;
    }
 
    /**
     * Deals with the settings when they are saved by the admin. Here is
     * where any validation should be dealt with.
     *
     * @param array  An array of new settings as submitted by the admin
     * @param array  An array of the previous settings
     * @return array The validated and (if necessary) amended settings
     **/
    function update( $new_instance, $old_instance ) {
    
        // update logic goes here
        $updated_instance = $new_instance;
        return $updated_instance;
    }

    /**
     * Displays the form for this widget on the Widgets page of the WP Admin area.
     *
     * @param array  An array of the current settings for this widget
     * @return void Echoes it's output
     **/
    function form( $instance ) {
        
     $instance = wp_parse_args( (array) $instance, array( 'title' => 'Publisher' ) );
        extract($instance);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
        
    }
}

?>