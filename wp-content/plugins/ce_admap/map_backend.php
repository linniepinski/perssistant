<?php

class CE_AdMap extends ET_AdminMenuItem{
    const CE_MAP_SLUG = 'ce-map';

    public function __construct($args = array() ){
        parent::__construct(self::CE_MAP_SLUG,  array(
            'menu_title'    => __('CE Map', ET_DOMAIN),
            'page_title'    => __('CE MAP', ET_DOMAIN),
            'callback'      => array($this, 'menu_view'),
            'slug'          => self::CE_MAP_SLUG,
            'page_subtitle' => __('CE Maps Overview', ET_DOMAIN),
            'pos'           => 55,
            'icon_class'    => 'icon-menu-overview'
        ));
            //add value to model;

            // add map to modal edit.

            $this->add_ajax('map-save-settings','map_save_settings');

          // for mobile version

    }

    /**
     * load script for backend ce_admap settings.
     * @return [type] [description]
     */
    public function on_add_scripts(){
        wp_enqueue_script('underscore');
        wp_enqueue_script('backbone');
        wp_enqueue_script('json');
        wp_enqueue_script( 'ce' );
        wp_enqueue_script('map-admin',plugin_dir_url(__FILE__).'js/map-admin.js',array('jquery','backbone','ce') );
    }
    /**
     * load style for backend ce_admap settings backend.
     * @return [type] [description]
     */
    public function on_add_styles(){
        wp_enqueue_style('admin.css');
        wp_enqueue_style('admin-map',plugin_dir_url(__FILE__).'css/admin-map.css');
    }

    /**
     * show view settings ce_map on backend.
     * @param  array $args array value of this menu.
     * @return show html on backend.
     */
    public function menu_view($args){ ?>
        <div class="et-main-header">
            <div class="title font-quicksand"><?php _e('CE Map',ET_DOMAIN);?></div>
            <div class="desc"><?php _e('Extension to add a map of ads in ClassifiedEngine.',ET_DOMAIN);?></div>
        </div>
        <div class="et-main-content" id="ce_map_settings">
            <div class="et-main-main clearfix inner-content" style="margin-left : 0;">
                <div  class="title font-quicksand map-setting-title">
                   <?php _e('Settings options',ET_DOMAIN);?>
                </div>
                <div class="desc">
                    <?php _e('Configuration management ce ad map extension',ET_DOMAIN);?>
                    <div class="inner">
                    <?php $options = self::get_map_options();  ?>
                        <div class="item">
                            <div class="payment">
                                 <label class="title font-quicksand" ><?php _e('Display map when insert or edit an ad',ET_DOMAIN); ?>  </label>
                                <div class="button-enable font-quicksand enable-email">
                                    <a class="deactive <?php if(!$options['show_map']) echo 'selected';?> " title="Disable" rel="show_map" href="#">
                                        <span><?php _e('Disable',ET_DOMAIN);?></span>
                                    </a>
                                    <a class="active <?php if($options['show_map']) echo 'selected';?>" title="Enable" rel="show_map" href="#">
                                        <span><?php _e('Enable',ET_DOMAIN);?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                       <div class="item" style="">
                            <div class="payment">
                                 <label class="title font-quicksand" ><?php _e('Display map on detail ad.',ET_DOMAIN); ?></label>
                                <div class="button-enable font-quicksand enable-email">
                                    <a class="deactive <?php if(!$options['show_single']) echo 'selected';?>" title="Disable" rel="show_single" href="#">
                                        <span><?php _e('Disable',ET_DOMAIN);?></span>
                                    </a>
                                    <a class="active <?php if($options['show_single']) echo 'selected';?>" title="Enable" rel="show_single" href="#">
                                        <span><?php _e('Enable',ET_DOMAIN);?></span>
                                    </a>
                                </div>
                            </div>

                        </div>

                        <div class="item">
                            <div class="payment">
                                <label class="title font-quicksand" > <?php _e('Use shortcode to display map in content: ',ET_DOMAIN); ?></label>
                                <input type='text' class="shortcode" readonly='readonlye' value='[ce_map address ="3 London Wall, London" zoom ="15" h="300px"]' />
                            </div>
                        </div>
                        <style type="text/css">
                        </style>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public static function get_map_options(){

        $args = array(
            'auto_save'     => 1,
            'show_single'   => 0,
            'show_map'      => 0
            );

        $options    = get_option('ce_map_options',array());
        $return     = wp_parse_args($options,$args);

        return $return;
    }
    public function save_map_options($options){
        update_option('ce_map_options',$options);
    }

    /**
     * save options settings in backend of ce_map extension.
     * @return [type] [description]
     */
    public function map_save_settings(){

        $name   = $_POST['name'];
        $val    = $_POST['val'];
        $_POST['val'] = 'deactive';
        if($val == 1){
            $_POST['val'] = 'active';
        }
        $options = self::get_map_options();
        $options[$name] = $val;
        $this->save_map_options($options);

        $resp = array('success' => true,'msg' => __('Save option success',ET_DOMAIN), 'data' => $_POST);
        wp_send_json($resp);
    }

}



?>