<?php
/**
 * new WordPress Widget format
 * Wordpress 2.8 and above
 * @see http://codex.wordpress.org/Widgets_API#Developing_Widgets
 */
class FRE_Social_Widget extends WP_Widget {

    /**
     * Constructor
     *
     * @return void
     **/
    function FRE_Social_Widget() {
        $widget_ops = array( 'classname' => 'fre-social', 'description' => __("FRE Social Widget", ET_DOMAIN) );
        $this->WP_Widget( 'fre-social', 'FRE Social', $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {
        extract( $args, EXTR_SKIP );
        echo $before_widget;
        echo $before_title;
        echo $instance['title']; // Can set this with a widget option, or omit altogether
        echo $after_title;
    ?>
        <ul class="social-list-footer">
            <?php if( ae_get_option('site_facebook') ) {
                echo '<li><a href="'. ae_get_option('site_facebook') .'"><span><i class="fa fa-facebook"></i></span>'.__("Facebook", ET_DOMAIN).'</a></li>' ;   
            } ?>
            <?php if( ae_get_option('site_twitter') ) {
                echo '<li><a href="'. ae_get_option('site_twitter') .'"><span><i class="fa fa-twitter"></i></span>'. __("Twitter", ET_DOMAIN).'</a></li>' ;   
            } ?>

            <?php if( ae_get_option('site_google') ) {
                echo '<li><a href="'. ae_get_option('site_google') .'"><span><i class="fa fa-google-plus"></i></span>'.__("Google+", ET_DOMAIN).'</a></li>' ;   
            } ?>
        </ul>
    <?php    
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
        $instance = wp_parse_args( (array) $instance, array( 'title' => __("Keep in touch", ET_DOMAIN) ) );
        extract($instance);
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:',ET_DOMAIN ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
    <?php
    }
}
/**
 * this file contain widgets support by DirectoryEngine
 * Widget_List_Categories
*/
add_action('widgets_init', 'fre_register_sidebars');
function fre_register_sidebars() {
     register_widget( 'FRE_Social_Widget' );

    /**
    * Creates a sidebar blog
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Blog Sidebar', ET_DOMAIN ),
        'id'            => 'sidebar-blog',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );

    /**
    * Creates a sidebar blog
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Page Sidebar', ET_DOMAIN ),
        'id'            => 'sidebar-page',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );

	/**
    * Creates a sidebar Footer 1
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Footer 1', ET_DOMAIN ),
        'id'            => 'fre-footer-1',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );

    /**
    * Creates a sidebar Footer 2
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Footer 2', ET_DOMAIN ),
        'id'            => 'fre-footer-2',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );
    

    /**
    * Creates a sidebar Footer 3
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Footer 3', ET_DOMAIN ),
        'id'            => 'fre-footer-3',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );


    /**
    * Creates a sidebar Footer 4
    * @param string|array  Builds Sidebar based off of 'name' and 'id' values.
    */
    $args = array(
        'name'          => __( 'Footer 4', ET_DOMAIN ),
        'id'            => 'fre-footer-4',
        'description'   => '',
        'class'         => '',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>'
    );

    register_sidebar( $args );

}