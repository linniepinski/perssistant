<?php
Class Map_Front{
    protected $options;
    protected $is_mobile;
	function __construct(){

        $this->options      = CE_AdMap::get_map_options();
        $this->is_mobile    = et_is_mobile();

        if ( is_active_widget( false, false, 'ce_ad_map', true ) ) {
            add_action( 'wp_ajax_ce_cemap_fetch_ads', array($this, 'fetch_ads') );
            add_action( 'wp_ajax_nopriv_ce_cemap_fetch_ads', array($this, 'fetch_ads') );
            add_action( 'wp_ajax_ce_admap_filter', array($this, 'filter_map') );
            add_action( 'wp_ajax_nopriv_ce_admap_filter', array($this, 'filter_map') );
            add_action( 'wp_footer', array($this, 'map_add_template') );
        }
        // default load backbone will load jquery and other library too;
		/*
		* render map into single-ad page
		*/
		add_action('display_right_ad',array($this,'show_map_in_ad_detail') );

        // add map top form when post  new ad.
        add_action('ce_ad_post_form_after_address', array( $this,'map_add_html_field') );

        add_filter('ce_convert_ad', array( $this, 'map_add_meta') );

        add_filter('ce_minify_source_path', array($this, 'ce_map_add_mininfy_path') );

        add_action( 'ce_on_add_scripts', array($this, 'map_enqueue_script') );

        add_action('ce_edit_ad_modal',array($this, 'map_add_html_field') );
        add_action('ce_insert_ad',array($this, 'save_option_field_map'), 10, 2 );
        add_action('ce_update_ad', array($this, 'save_option_field_map'), 10, 2);


        if($this->is_mobile){
            // render map in mobile.
            add_action('ce_display_tab_ad_content', array($this, 'ce_display_map_ad_content') );

             // rener icon map, click to display map.
            add_action('ce_display_tab_nav', array($this, 'ce_display_map_nav') );

            add_action('ce_on_add_scripts_mobile', array($this, 'ce_on_add_scripts_mobile') );

            add_action( 'et_mobile_header', array($this, 'et_mobile_header_map') );
            add_action( 'wp_enqueue_scripts', array($this, 'map_enqueue_script_footer_mobile') );
        }

	}


     function fetch_ads () {
        $ads   =   query_posts( array(
            'post_type'     => CE_AD_POSTTYPE,
            'post_status'   => 'publish',
            'showposts'     => '1000',
              'meta_query' => array(
                    array(
                        'key' => 'et_location_lat',
                        'value' => '',
                        'compare' => '!=',
                        )
                    )
        ) );

        $ad_data           =   array();
        $ad_data['count']  =   count($ads);
        $ad_data['data']   =   array();

        foreach ($ads as $ad) {
            $data   =   array();
            $data['post_title']     =   $ad->post_title;
            $data['post_excerpt']   =   empty($ad->post_excerpt) ? apply_filters('the_excerpt', $ad->post_content): $ad->post_excerpt ;
            $data['logo']           =   map_get_thumbnail($ad->ID);
            $data['lat']            =   get_post_meta( $ad->ID, 'et_location_lat', true );
            $data['lng']            =   get_post_meta( $ad->ID, 'et_location_lng', true );
            $data['location']       =   get_post_meta( $ad->ID, 'et_full_location', true );
            $data['permalink']      =   get_permalink( $ad->ID );
            array_push($ad_data['data'], $data );
        }

        header( 'HTTP/1.0 200 OK' );
        header( 'Content-type: application/json' );

        echo json_encode($ad_data);
        exit;

    }

    function filter_map () {
        $response = array();

        try {
            global $post, $et_global;
            // refine meta query
            $request = $_REQUEST;

            $request['meta_query']  =    array(
                    array(
                        'key' => 'et_location_lat',
                        'value' => '',
                        'compare' => '!=',
                        )
                    );
            if( isset($request['category']) && $request['category'] !='none' ){
                $request['tax_query'] =  array(
                                            array(
                                                'taxonomy' => CE_AD_CAT,
                                                'field' => 'slug',
                                                'terms' => $request['category']
                                            )
                                        );
                unset($request['category']);
            }
            if(isset($request['location'])){
                $request['meta_query'][]  = array(
                                            'key'       =>'et_full_location',
                                            'value'     => trim($request['location']),
                                            'compare'   => 'LIKE'
                                             );
            }

            $args = wp_parse_args( $request, array(
                'post_type' => CE_AD_POSTTYPE,
                'post_status' => array('publish'),
                'orderby' => 'post_date',
                'order' => 'DESC',
                'showposts' => 1000,
            ));

            $query      = new WP_Query( $args );
            $ads        = array();
            $authors    = array();

            $response   =   array ();
            if ($query->have_posts()) {
                $ad_data           =   array();
                $ad_data['count']  =   $query->found_posts;
                $ad_data['data']   =   array();

                while($query->have_posts()){
                    $query->the_post();
                    global $post;
                    $ad    =   $post;

                    $data   =   array();
                    $data['post_title']     =   $ad->post_title;
                    $data['post_excerpt']   =   empty($ad->post_excerpt) ? apply_filters('the_excerpt', $ad->post_content): $ad->post_excerpt ;
                    $data['logo']           =   map_get_thumbnail($ad->ID);
                    $data['lat']            =   get_post_meta( $ad->ID, 'et_location_lat', true );
                    $data['lng']            =   get_post_meta( $ad->ID, 'et_location_lng', true );
                    $data['location']       =   get_post_meta( $ad->ID, 'et_full_location', true );
                    $data['permalink']      =   get_permalink( $ad->ID );
                    array_push($ad_data['data'], $data );

                }

                $response = $ad_data;
            }

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'code'  => 400,
                'msg'   => __("An error has occurred!", ET_DOMAIN)
            );
        }
        wp_send_json($response);
    }


	function show_map_in_ad_detail($id){

            $options = $this->options;

            if($options['show_single']){
                echo '<div class="block-map right">';
                $lat        = get_post_meta( $id, 'et_location_lat',true);
                $lng        = get_post_meta( $id, 'et_location_lng',true);
                $address    = get_post_meta( $id, 'et_full_location',true);
                $zoom       = apply_filters( 'map_zoom_on_single', 12);

                if( empty($lat) || empty($lng)  )
                    echo do_shortcode('[ce_map address = "'.$address.'" zoom ="'.$zoom.'"  h="305px" is_single ="1" ]');
                else
                    echo do_shortcode('[ce_map lat = "'.$lat.'"  lng="'.$lng.'" zoom ="'.$zoom.'" h="305px" is_single ="1" ]');// is_single to check render for only single-ad.
                echo '</div>';
            }
    }
    function map_add_html_field($ad){
        $options        =  $this->options;
        $is_mobile      = et_is_mobile();
        $is_table       = et_is_table();
        $et_map_zoom    =  $zoom = 1;
        $height = 305;
        if($is_mobile){
            $zoom = 12;
            $height = 200;
        }
        $et_center_lat  = '';
        $et_center_lng  = '';
        $address        = '';

        if( isset($ad->ID) ){

            $et_map_zoom    = get_post_meta($ad->ID,'et_map_zoom',true);
            $et_center_lat  = get_post_meta($ad->ID,'et_center_lat',true);
            $et_center_lng  = get_post_meta($ad->ID,'et_center_lng',true);
            $address        = get_post_meta($ad->ID,'et_full_location', true);

        }   else if (is_user_logged_in()) {
            // get seller's address and assign to post ad
            global $current_user;
            $seller     = ET_Seller::convert($current_user);
            $address    = $seller->et_address;
        }

        if($options['show_map'] && (!$is_mobile || $is_table) ){  ?>
            <div class="form-group clearfix row-single-map">
                <!-- <label class="control-label customize_text">&nbsp; </label> -->
                <div class="controls">
                    <div id="single_map">
                        <div class="map-inner" id="map"></div>
                    </div>
                </div>

                <?php
                $et_map_zoom    = '';
                $et_center_lat  = '';
                $et_center_lng  = '';

                 ?>
                <input id="et_map_zoom" type="hidden" name="et_map_zoom" value="<?php echo $et_map_zoom;?>"/>
                <input id="et_center_lat" type="hidden" name="et_center_lat" value="<?php echo $et_center_lat;?>"/>
                <input id="et_center_lng" type="hidden" name="et_center_lng" value="<?php echo $et_center_lng;?>"/>
            </div>
            <?php
        } else if ( $is_mobile ) { ?>
            <div class="post-new-classified ui-field-contain ui-body ui-br" data-role="fieldcontain">
                <?php echo do_shortcode('[ce_map address = "'.$address.'" zoom ="'.$zoom.'" h="'.$height.'px" width = "100%"" ]'); ?>
            </div>
            <?php
        }

    }
     /**
     * add script when mininfy .
     * @param  [type] $mini_path [description]
     * @return [type]            [description]
     */
    function ce_map_add_mininfy_path($mini_path){
        $mininfy = get_theme_mod( 'ce_minify', 0 );
        $options =  $this->options;
        if( $mininfy ) {
            $mini_path['front'][]    = CE_MAP_PATH.'/js/marker-cluster.js';
            if($options['show_map']){
                $mini_path['front'][]   = CE_MAP_PATH.'/js/map-front.js';
            }
            $mini_path['mobile-js'][]   = THEME_CONTENT_DIR.'/js/lib/gmaps.js';
            $mini_path['mobile-js'][]   = CE_MAP_PATH.'/js/marker-cluster.js';
            $mini_path['mobile-js'][]   = CE_MAP_PATH.'/js/map-mobile.js';
        }
        return $mini_path;
    }
    /**
     * add htlm and script, style when use widget or  enable map on edit, insert ad.
     * @return [type] [description]
     */
    function map_enqueue_script() {
        wp_reset_query();
        $mininfy = get_theme_mod( 'ce_minify', 0 );
        //if(is_page_template('page-post-ad.php')){
            $options =  $this->options;
            wp_enqueue_style('map-style', plugin_dir_url( __FILE__).'/css/map-front.css', true , CE_MAP_VER);

            // if(!current_user_can('manage_options')){
            //     wp_enqueue_script( 'et-googlemap-api' );
            // }

            wp_enqueue_script( 'gmap', TEMPLATEURL.'/js/lib/gmaps.js', array ('et-googlemap-api') , CE_VERSION , true );
            // wp_enqueue_script('gmap');
            wp_enqueue_script( 'marker_cluster', plugin_dir_url( __FILE__).'/js/marker-cluster.js', array('gmap'), '1.0'  );
            if($options['show_map']){
                $address = '';
                $ad_id = isset($_GET['id']) ? $_GET['id'] : '';

                if( !empty($ad_id) ){

                    $address = get_post_meta($ad_id,'et_full_location', true);

                } else if ( is_user_logged_in() ){

                    global $current_user;
                    $seller             = ET_Seller::convert($current_user);

                    if (empty($ad_location_id))
                      $ad_location_id   = $seller->user_location_id;

                    if (empty($et_full_location))
                      $address   = $seller->et_address;
                }

                wp_enqueue_script('ce-map-front', plugin_dir_url( __FILE__).'/js/map-front.js',array('jquery','backbone','et-googlemap-api','gmap', 'ce'), CE_MAP_VER , true);
                wp_localize_script('ce-map-front','ce_map',array('auto_save' => $options['auto_save'], 'ad_address' => $address ) );
            }
        // }
    }

     /**
     * save meta fields relative map for ad.
     * @param  int $return
     * @param  array $data   list value of ad.
     * @return save post meta when edit, update da.
     */
    public function save_option_field_map($return,$data){

        //$args = array('et_map_zoom','et_center_lat','et_center_lng');

        if(isset($data['et_map_zoom']))
            update_post_meta($return,'et_map_zoom',$data['et_map_zoom']);

        if(isset($data['et_center_lat']))
            update_post_meta($return,'et_center_lat',$data['et_center_lat']);

        if(isset($data['et_center_lng']))
            update_post_meta($return,'et_center_lng',$data['et_center_lng']);
    }

    function map_add_template(){
        $is_mobile = et_is_mobile();
        if( et_is_mobile() && !et_is_table() ) 
            return;

        if ( !is_page_template('page-post-ad.php')) { ?>
            <div class="modal-map modal-dialog" id="modal_ad_map" style="" >
                <div class="header-filter">
                    <div class="main-center f-left-all">
                        <div class="location map-item">
                            <input type="text" name="ad_location" class="search-box ad-searchbox input-search-box border-radius" placeholder="Enter a location ..." value="">
                            <span class="icon" data-icon="@"></span>
                        </div>
                        <div class="form-item map-item">
                            <div class="select-style btn-background border-radius et-button-select">
                                <?php
                                $cats = ET_AdCatergory::get_category_list();
                                if($cats){
                                    echo '<select name="cateogory">';
                                    echo '<option value="none" >'.__('All Categories',ET_DOMAIN).'</option>';
                                    foreach($cats as $key =>$cat){
                                        echo '<option value = "'.$cat->slug.'">'.$cat->name.' </option>';
                                    }
                                    echo '</select>';
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
                <div  id="modal_map_inner" style="" ></div>
                <button class="close" type="button">&times;</button>
                <div id="map-overlay" style="display:none;" >
                    <div id="map-fadingBarsG" class="map-fadingBar">
                        <div id="map-fadingBarsG_1" class="fadingBarsG">
                        </div>
                        <div id="map-fadingBarsG_2" class="fadingBarsG">
                        </div>
                        <div id="map-fadingBarsG_3" class="fadingBarsG">
                        </div>
                        <div id="map-fadingBarsG_4" class="fadingBarsG">
                        </div>
                        <div id="map-fadingBarsG_5" class="fadingBarsG">
                        </div>
                        <div id="map-fadingBarsG_6" class="fadingBarsG">
                        </div>
                        <div id="map-fadingBarsG_7" class="fadingBarsG">
                        </div>
                        <div id="map-fadingBarsG_8" class="fadingBarsG">
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }

    public function map_add_meta($result){

        $result->et_map_zoom   =   get_post_meta($result->id,'et_map_zoom',true);
        $result->et_center_lat =   get_post_meta($result->id,'et_center_lat',true);
        $result->et_center_lng =   get_post_meta($result->id,'et_center_lng',true);
        return $result;
    }
    function ce_display_map_ad_content($id){

        $options =  $this->options;
        if($options['show_single']) {

            $lat        = get_post_meta( $id, 'et_location_lat',true);
            $lng        = get_post_meta( $id, 'et_location_lng',true);
            $address    = get_post_meta( $id, 'et_full_location',true);
            $zoom       = apply_filters( 'map_zoom_on_single', 12);
            echo ' <div class="tab-cont tab-map" id="tab_gmap">';
                if( empty($lat) || empty($lng)  )
                    echo do_shortcode('[ce_map address = "'.$address.'" zoom ="'.$zoom.'" h = "305px" w = "100%" is_single ="1" ]');
                else
                   echo do_shortcode('[ce_map lat = "'.$lat.'"  lng="'.$lng.'" zoom ="'.$zoom.'" h="305px" w="100%" is_single ="1" ]');
            echo '</div>';
        }
    }

    /*
    /Display link to tab mobile content.
    */
    function ce_display_map_nav(){
        $options =  $this->options;
        if($options['show_single']){?>
            <a class="ui-tabs ui-corner-left ui-link map"><i class="fa fa-map-marker"></i><span class="border-arrow"><span class="arrow"></span></span></a>
        <?php  }
    }

    function ce_on_add_scripts_mobile(){
        $mininfy = get_theme_mod( 'ce_minify', 0 );
        if(!$mininfy){
            wp_register_script( 'gmap', TEMPLATEURL.'/js/lib/gmaps.js', array ('et-googlemap-api') , CE_MAP_VER , true );
            wp_enqueue_script('gmap');
            wp_enqueue_script( 'marker_cluster', plugin_dir_url( __FILE__).'/js/marker-cluster.js', array('gmap'), CE_MAP_VER, true  );
        }
    }
      function et_mobile_header_map(){    ?>
        <style type="text/css">
            .admap-content img {
                float: left;
                padding: 5px;
                width: 88px;
            }
            .admap-content p {
                clear: right;
                font: italic 12px arial;
                margin: 0;
            }
            .admap-content p a {
                font: bold 13px arial !important;
                color: #428bca;
                text-decoration: none;
            }
            #ce_admap{
                max-width: 100% !important;
            }
            .gm-style-iw{
                 width: 259px !important;
            }
            .gm-style-iw > div{ overflow-x: hidden;}
        </style>
        <?php
    }
    function map_enqueue_script_footer_mobile(){

        $mininfy = get_theme_mod( 'ce_minify', 0 );

        if($mininfy){ 
            wp_enqueue_script('et-googlemap-api');
        /* ?>
            <script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=false"></script>
            <script type="text/javascript" src="<?php echo TEMPLATEURL.'/js/lib/gmaps.js';?>"></script>
            <script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__).'/js/marker-cluster.js';?>"></script> 
            <?php */
        }
    }
}

/**
 * get thumbnail of ad when show on marker.
 * @param  int $id post_id
 * @return [type]     [description]
 */
function map_get_thumbnail($id){
    $thumb            =   wp_get_attachment_image_src( get_post_thumbnail_id($id), 'thumbnail' );
    if($thumb)
        return $thumb[0];
    else return TEMPLATEURL.'/img/no_image.gif';
}


new Map_Front();
?>