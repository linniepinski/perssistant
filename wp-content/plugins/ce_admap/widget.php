<?php

class AdMap_Widget extends WP_Widget {
    const name = 'ce_ad_map';
    /**
     * Constructor
     *
     * @return void
     **/
    function AdMap_Widget() {
        $widget_ops = array( 'classname' => self::name, 'description' => 'This widget works in all sidebars but its display suits best on top or bottom sidebar.' );
        $this->WP_Widget( self::name, __('CE AdMap',ET_DOMAIN), $widget_ops );
    }

    /**
     * Outputs the HTML for this widget.
     *
     * @param array  An array of standard parameters for widgets in this theme
     * @param array  An array of settings for this widget instance
     * @return void Echoes it's output
     **/
    function widget( $args, $instance ) {
        if( et_is_mobile() && !et_is_tablet() ){
            extract($instance);
            // use shorcode don't show full market.
            // if want to show market, have to fin solution implement map-mobile.js file tool
            echo do_shortcode('[ce_map address = "'.$center.'" zoom ="'.$zoom.'"  h="'.apply_filters('height_map_mobile',$height).'px" ]');
            return ;
        }
        if( is_singular( CE_AD_POSTTYPE )) {
            $instance['is_single_ad']  =   1;
        }

        if(!isset($instance['enable_zoom']))
            $instance['enable_zoom'] = 0;

        if(!isset($instance['auto_save']))
            $instance['auto_save'] = 0;

        $instance['heading']   =    __("<p>There are %s ads on this location:</p>", ET_DOMAIN);

        wp_parse_args( $instance, array('height' => '350' , 'width' => '420','enable_zoom' => false));

        wp_enqueue_script( 'ce_admap', plugin_dir_url( __FILE__).'/js/ce_admap.js',array('jquery','backbone','underscore','ce','front'),CE_VERSION, true);
        wp_localize_script( 'ce_admap', 'ce_admap_widget', $instance );


        extract( $args, EXTR_SKIP );

        echo $before_widget;

        if($instance['title'] != '') {
            echo $before_title;
            echo $instance['title']; // Can set this with a widget option, or omit altogether
            echo $after_title;
        }

        if( isset($instance['width']) &&  is_numeric( trim($instance['width']) ) )
           $instance['width'] = $instance['width'].'px';
        else
            $instance['width'] = '100%';

    ?>
        <div class="admap">

            <div id="ce_admap" class="ce_admap" style="width : <?php echo $instance['width'];?> ; height : <?php echo $instance['height']; ?>px">
            </div>
            <?php if( current_user_can( 'manage_options' ) && !et_is_mobile() ) { 
                $ajax_nonce = wp_create_nonce("save-sidebar-widgets");
            ?>

            <form style=" display:none; bottom: 5px !important;background : #dfdfdf; padding : 5px;">

                <input id="<?php echo $this->get_field_id('enable_zoom') ?>" name="<?php echo $this->get_field_name('enable_zoom')?>" type="hidden" value="<?php echo $instance['enable_zoom']; ?>">
                <input id="<?php echo $this->get_field_id('auto_save') ?>" name="<?php echo $this->get_field_name('auto_save')?>" type="hidden" value="<?php echo $instance['auto_save']; ?>">


                <input name="id_base" type="hidden" value="<?php echo $this->id_base ?>">
                <input name="widget-id" type="hidden" value="<?php echo $this->id ?>">
                <input name="savewidgets" type="hidden" value="<?php echo $ajax_nonce ?>">


                <!-- <input size="3" id="<?php echo $this->get_field_id('width') ?>" name="<?php echo $this->get_field_name('width'); ?>" type="hidden" value="<?php echo $instance['width'] ?>"> -->
                <input size="3" id="<?php echo $this->get_field_id('height') ?>" name="<?php echo $this->get_field_name('height'); ?>" type="hidden" value="<?php echo $instance['height'] ?>">
                <input class="widefat" id="<?php echo $this->get_field_id('title') ?>" name="<?php echo $this->get_field_name('title'); ?>" type="hidden" value="<?php echo $instance['title'] ?>">

                <input class="lat" id="<?php echo $this->get_field_id('lat'); ?>" name="<?php echo $this->get_field_name('lat'); ?>" type="hidden" value="<?php echo $instance['lat']; ?>" />
                <input class="lng"  id="<?php echo $this->get_field_id('lng'); ?>" name="<?php echo $this->get_field_name('lng'); ?>" type="hidden" value="<?php echo $instance['lng']; ?>" />
                <!-- Setting center -->
                <label for=""><?php _e("Center:", ET_DOMAIN); ?></label>
                <input title="<?php _e("Change your map center", ET_DOMAIN); ?>" class="widefat center" id="<?php echo $this->get_field_id('center') ?>" name="<?php echo $this->get_field_name('center'); ?>" type="text" value="<?php echo $instance['center'] ?>"> 
                <!-- Setting map zoom -->
                <label for=""><?php _e("Default Zoom:", ET_DOMAIN); ?></label>
                <input class="zoom widefat" id="<?php echo $this->get_field_id('zoom') ?>" name="<?php echo $this->get_field_name('zoom'); ?>" type="text" value="<?php echo $instance['zoom'] ?>">           

            </form>

            <?php } ?>
            <a style="display : none;" href="#" class="enlarge" title="<?php _e("Full Screen", ET_DOMAIN); ?>"><span  data-icon="`" class="map-icon" ></span></a>
        </div>

    <?php

        $this->template ();
               echo $after_widget;

    }
    function widget_mobile(){

    }

    function template () {
        $temaplte   =   '<div class="admap-content"> <img src="{{ logo }}" /> <p> <a href="{{ permalink }}" > {{post_title}} </a> </p> <p> '.__("Location", ET_DOMAIN).': {{location}} </p></div>';
        echo '<script type="text/template" id="ce_admap_template">'. apply_filters( 'ce_admap_template', $temaplte ).'</script>';
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

        if(!isset($instance['enable_zoom']) )
            $instance['enable_zoom'] = 0;

        if(!isset($instance['auto_save']) )
            $instance['auto_save'] = 0;

        $instance = wp_parse_args( (array) $instance, array(
                            'title'         => __("Ad Map", ET_DOMAIN) ,
                            'width'         => '1050' ,
                            'height'        => '350' ,
                            'zoom'          => '8' ,
                            'center'        => 'Ho Chi Minh City',
                            'lat'           => '',
                            'lng'           => '',
                            'enable_zoom'   => 1,
                            'auto_save'     => 1
                        ));
        extract($instance);
    ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', ET_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('center'); ?>"><?php _e('Map Center:', ET_DOMAIN); ?></label>
            <input class="widefat"  id="<?php echo $this->get_field_id('center'); ?>" name="<?php echo $this->get_field_name('center'); ?>" type="text" value="<?php echo $center; ?>" /> 
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('zoom'); ?>"><?php _e('Default Zoom:', ET_DOMAIN); ?></label>
            <input class="widefat"  id="<?php echo $this->get_field_id('zoom'); ?>" name="<?php echo $this->get_field_name('zoom'); ?>" type="text" value="<?php echo $zoom; ?>" />
        </p>

         <p>
            <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', ET_DOMAIN); ?></label>
            <input size='3'  id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width; ?>" />px

            <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', ET_DOMAIN); ?></label>
            <input size='3'  id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height; ?>" />px
        </p>
        <p>
            <input class="widefat" <?php if($enable_zoom) echo 'checked="checked"';?>  id="<?php echo $this->get_field_id('enable_zoom'); ?>" name="<?php echo $this->get_field_name('enable_zoom'); ?>" type="checkbox" value="1" />
            <label for="<?php echo $this->get_field_id('enable_zoom'); ?>"><?php _e(' Enable Zoom', ET_DOMAIN); ?> &nbsp; </label>
        </p>
        <p style="display:none">
            <input class="widefat" <?php if($auto_save) echo 'checked="checked"';?>  id="<?php echo $this->get_field_id('auto_save'); ?>" name="<?php echo $this->get_field_name('auto_save'); ?>" type="checkbox" value="1" />
            <label for="<?php echo $this->get_field_id('auto_save'); ?>"><?php _e('Auto save map after drag end.', ET_DOMAIN); ?></label>
        </p>

        <input size='3'  id="<?php echo $this->get_field_id('lat'); ?>" name="<?php echo $this->get_field_name('lat'); ?>" type="hidden" value="<?php echo $lat; ?>" />
        <input size='3'  id="<?php echo $this->get_field_id('lng'); ?>" name="<?php echo $this->get_field_name('lng'); ?>" type="hidden" value="<?php echo $lng; ?>" />

    <?php
    }
}
/**
 * skip widget save lng ang lat of map by widget in widget.php.
 *
 */
add_action('widgets.php','map_control_save');
function map_control_save(){
    // update in widgets.php(dashboard wordpress)
    if(is_admin() && isset($_POST['add_new']) && $_POST['id_base'] == AdMap_Widget::name){
        $id_base        = $_POST['id_base']; // ce_ad_map
        $widget_id      = $_POST['widget-id']; //ce_ad_map-3
        $widget_number  = $_POST['widget_number'];
        $pre            = 'widget-'.$id_base;
        $name           = 'widget_'.AdMap_Widget::name;
        $map_widgets    = get_option($name);
        if( isset($map_widgets[$widget_number]) ){
            $cur_widget     = $map_widgets[$widget_number];
            $_POST[$pre][$widget_number]['lat'] = $cur_widget['lat'];
            $_POST[$pre][$widget_number]['lng'] = $cur_widget['lng'];
        }

    }
}



?>