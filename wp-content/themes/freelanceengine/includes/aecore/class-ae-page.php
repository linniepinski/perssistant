<?php
class AE_Page extends AE_Base
{
    function __construct() {
        $this->add_action('admin_print_styles', 'print_styles');
        $this->add_action('admin_enqueue_scripts', 'print_scripts');
    }
    
    /**
     * add hook to register a men page , the hook admin_menu should be call in subclass
     */
    function add_menu_page() {
        $args = $this->args;
        
        add_menu_page($args['page_title'], $args['menu_title'], 'manage_options', $args['slug'], '', '', 4);
    }
    
    /**
     * add hook to add submen page , the hook admin_menu should be call in subclass
     */
    function sub_menu_page() {
        $args = $this->args;
        add_submenu_page($args['parent_slug'], $args['page_title'], $args['page_title'], 'manage_options', $args['slug'], array(
            $this,
            'render_frame'
        ));
    }
    
    public function print_styles() {
        $this->add_style('admin', ae_get_url() . '/assets/css/admin.css');
        $this->add_style('ae-colorpicker', ae_get_url() . '/assets/css/colorpicker.css');
        $this->add_style('fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css');
    }
    
    public function print_scripts() {
        
        if(!isset($_REQUEST['page'])) return ;

        $this->add_existed_script('jquery');
        $this->add_existed_script('plupload');
        //add de sort category
        $this->add_existed_script( 'jquery-ui-sortable' );
        $this->add_script( 'lib-nested-sortable',  ae_get_url() . '/assets/js/jquery.nestedSortable.js', array('jquery', 'jquery-ui-sortable') );
        // tam thoi add de xai
        $this->add_script('jquery-validator', ae_get_url() . '/assets/js/jquery.validate.min.js', 'jquery');
        
        $this->add_script('ae-colorpicker', ae_get_url() . '/assets/js/colorpicker.js', array(
            'jquery'
        ));
        
        $this->add_script('marionette', ae_get_url() . '/assets/js/marionette.js', array(
            'jquery',
            'underscore',
            'backbone'
        ));

        // control backend user list
        $this->add_script('gmap', ae_get_url() . '/assets/js/gmap.js', array(
            'et-googlemap-api'
        ));
        
        // ae core js appengine
        $this->add_script('appengine', ae_get_url() . '/assets/js/appengine.js', array(
            'jquery',
            'underscore',
            'backbone',
            'marionette',
            'plupload',
            'ae-colorpicker'
        ));
        
        // control backend user list
        $this->add_script('backend-user', ae_get_url() . '/assets/js/user-list.js', array(
            'appengine'
        ));

        // control backend order list
        $this->add_script('order-list', ae_get_url() . '/assets/js/payment-list.js', array(
            'appengine'
        ));
        
        //  option settings and save
        $this->add_script('option-view', ae_get_url() . '/assets/js/option-view.js', array(
            'appengine'
        ));
        
        // control option translate
        $this->add_script('language-view', ae_get_url() . '/assets/js/language-view.js', array(
            'appengine',
            'option-view'
        ));
        
        // control pack view add delete update pack
        $this->add_script('pack-view', ae_get_url() . '/assets/js/pack-view.js', array(
            'appengine',
            'option-view'
        ));
        
        // backend js it should be separate by theme
        $this->add_script('backend', ae_get_url() . '/assets/js/backend.js', array(
            'appengine'
        ));

        wp_localize_script('appengine', 'ae_globals', array(
            'ajaxURL'         => admin_url('admin-ajax.php') ,
            'imgURL'          => ae_get_url() . '/assets/img/',
            'jsURL'           => ae_get_url() . '/assets/js/',
            'themeImgURL'     => get_template_directory_uri(). '/img/',
            'loadingImg'      => '<img class="loading loading-wheel" src="' . ae_get_url() . '/assets/img/loading.gif" alt="' . __('Loading...', ET_DOMAIN) . '">',
            'loading'         => __('Loading', ET_DOMAIN) ,
            'texts'           => array('limit_category_level' => __("Categories' level is limited to 3", ET_DOMAIN)),
            'ae_is_mobile'    => et_load_mobile() ? 1 : 0,
            'plupload_config' => array(
                'max_file_size'       => '3mb',
                'url'                 => admin_url('admin-ajax.php') ,
                'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf') ,
                'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap') ,
                'filters'             => array(
                    array(
                        'title'      => __('Image Files', ET_DOMAIN) ,
                        'extensions' => 'jpg,jpeg,gif,png'
                    )
                )
            )
        ));
    }
    
    /**
     * render the frame with is used by all page in backend
     */
    function render_frame() {
?>
        <!-- ================================ -->
        <!-- Admin Frame                      -->
        <!-- ================================ -->
        <div class="wrap">
            <div class="et-body">
                <div class="et-header">
                    <div class="logo">
                        <a href="http://www.enginethemes.com/"> Powered by <img src="<?php
        echo ae_get_url(); ?>/assets/img/engine-logo.png" /> </a>
                    </div>
                    <div class="slogan"><span><?php
        _e('Administration', ET_DOMAIN) ?></span>. <?php
        _e('You are an admin. Here you administrate.', ET_DOMAIN) ?></div>          
                </div>
                <div class="et-wrapper clearfix">
                    <div class="et-left-column">                        
                            <?php
        $this->render_menu();
?>
                    </div>
                    <div id="engine_setting_content" class="et-main-column clearfix">
                        <?php
        
        // admin page html body
        $this->render();
        
        //if( current_user_can( 'manage_options' ) || is_page_template( 'page-account-listing.php' )){
        echo '<div class="hidden">';
        wp_editor('div_load_tiny', 'div_load_tiny', ae_editor_settings());
        echo '</div>';
        
        //}
        
        
?>
                    </div>
                </div>
                

                <div class="et-footer"></div>
                <!--
                <div class="et-footer">
                    If you have any troubles you can <a  href="#">watch a video about this page <span class="icon" data-icon="V"></span></a>or  <a href="#">send us a message <span class="icon" data-icon="M"></span></a>.
                </div>
                -->
            </div>
        </div><!-- wrap -->
        
        <?php
    }
    
    /**
     * render pages list menu title
     */
    public function render_menu() {
        
        if (!empty($this->pages)) {
            echo '<ul class="et-menu-items font-quicksand">';
            $active = '';
            foreach ($this->pages as $key => $page) {
                $args = $page['args'];
                if ($_REQUEST['page'] == $args['slug']) {
                    
                    // set current page active
                    $active = 'active';
                }

                if(isset($args['icon_class'])) {
                    $args['icon'] = $args['icon_class'];
                }
                
                if(!isset($args['icon']) || !$args['icon']) {
                    $args['icon'] = 'gear';
                }
                
                if(isset($args['icon_class'])) {
                    echo '<li>
                            <a class="engine-menu ' . $active . '" href="?page=' . $args['slug'] . '">
                                <div data-icon="' . $args['icon'] . '" class="' . $args['icon'] . '"></div>
                                <div class="">' . $args['menu_title'] . '</div>
                            </a>
                        </li>';
                }else {
                    echo '<li>
                            <a class="engine-menu ' . $active . '" href="?page=' . $args['slug'] . '">
                                <div data-icon="' . $args['icon'] . '" class="icon engine-menu-icon icon-' . $args['icon'] . '"></div>
                                <div class="">' . $args['menu_title'] . '</div>
                            </a>
                        </li>';
                }
                $active = '';
            }
            echo '</ul>';
        }
    }
    
    /**
     * render page container
     */
    public function render() {
        
        // admin page header
        if (isset($this->header)) {
            $this->header->render();
        } else {
            $this->header();
        }
        
        $this->container->render();
    }
    
    /**
     * render page header
     */
    public function header() {
?>
        <div class="et-main-header">
            <div class="title font-quicksand"><?php
        echo $this->args['menu_title']; ?></div>
            <?php
        if (isset($this->args['desc'])) { ?>
                <div class="desc"><?php
            echo $this->args['desc']; ?></div>
            <?php
        } ?>
        </div>
    <?php
    }
}

/**
 * this class just use to create an admin menu
 */
class AE_Menu extends AE_Page
{
    static $instance = null;
    function __construct($pages) {
        
        /**
         * add action to add menu
         * callback add_menu_page in parent class AE_Page
         */
        $this->add_action('admin_menu', 'add_menu_page');
        
        /**
         * ajax option sync
         */
        $this->add_action('wp_ajax_ae-option-sync', 'action_sync');
        
        /**
         * ajax upload image callback
         */
        $this->add_action('wp_ajax_et-upload-image', 'upload_image');
        // catch action after upload image success
        $this->add_action('ae_upload_image', 'update_site_branding', 10, 2);
        /**
         * add action to add menu to admin bar
         */
        $this->add_action('admin_bar_menu', "admin_bar_menu", 200);
        
        /**
         * ajax fetch users sync
         */
        
        // $this->add_action( 'wp_ajax_ae-fetch-users', 'fetch_user' );
        
        $this->args = array(
            'page_title' => __('Engine Settings', ET_DOMAIN) ,
            'menu_title' => __('Engine Settings', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'et-overview',
            'icon_url' => '',
            'pos' => 3
        );
        
        $this->pages = $pages;
        
        self::$instance = $this;
        
        $user_action = new AE_UserAction(new AE_Users());
        $language = new AE_Language();
        
        $this->add_action('updated_option', 'update_option', 10, 3);
    }
    
    public static function get_instance() {
        return self::$instance;
    }
    
    /**
     * option sync , catch ajax option-sync
     */
    function action_sync() {

        if(!current_user_can( 'manage_options' )) {
            wp_send_json( array('success' => false , 'msg' => __("You do not have permission to change option.", ET_DOMAIN)) );
        }

        
        $request = $_REQUEST;
        $name = $request['name'];
        
        $value = array();        
        if( is_string($request['value']) ){
            $request['value'] = stripslashes($request['value']);
        }
        if (isset($request['group']) && $request['group']) {
            parse_str($request['value'], $value);
        }else{
            $value = $request['value'];
        }

        /**
         * save option to database
         */
        $options = AE_Options::get_instance();
        $options->$name = $value;
        $options->save();
        
        if ($name == 'blogname' || $name == 'blogdescription' || $name == 'et_license_key'){
            update_option($name, stripslashes($value));
        } 
        
        do_action('ae_save_option' , $name, $value);

        /**
         * search index id in option array
         */
        $options_arr = $options->get_all_current_options();
        $id = array_search($name, array_keys($options_arr));
        $response = array(
            'success' => true,
            'data' => array(
                'ID' => $id
            ) ,
            'msg' => __("Update option successfully!", ET_DOMAIN)
        );
        wp_send_json($response);
    }
    
    /**
     * catch hook update option blog name
     */
    public function update_option($option, $old, $new) {
        
        /**
         * save option to database
         */
        if ($option == 'et_options') return;
        if ($option == 'blogname' || $option == 'blogdescription' || $option == 'et_license_key') {
            $options = AE_Options::get_instance();
            $options->$option = $new;
            $options->save();    
        }        
    }
    
    /**
     * update branding : logo, mobile icon
     */
    function upload_image() {
        global $user_ID;
        $res = array(
            'success' => false,
            'msg' => __('There is an error occurred', ET_DOMAIN) ,
            'code' => 400,
        );

        /**
         * User must login to upload image
         */
        if(!$user_ID) {
            $res['msg'] = __("You must login to upload image.", ET_DOMAIN);
            wp_send_json( $res );
        }
        
        // check fileID
        if (!isset($_POST['fileID']) || empty($_POST['fileID']) || !isset($_POST['imgType']) || empty($_POST['imgType'])) {
            $res['msg'] = __('Missing image ID', ET_DOMAIN);
        } else {
            $fileID = $_POST["fileID"];
            $imgType = $_POST['imgType'];
            // check ajax nonce
            if (!de_check_ajax_referer($imgType . '_et_uploader', false, false) && !check_ajax_referer($imgType . '_et_uploader', false, false) ) {
                $res['msg'] = __('Security error!', ET_DOMAIN);
            } elseif (isset($_FILES[$fileID])) {
                // 
                $upload_mimes = apply_filters('et_upload_file_upload_mimes', array(
                    'jpg|jpeg|jpe' => 'image/jpeg',
                    'gif' => 'image/gif',
                    'png' => 'image/png',
                    'bmp' => 'image/bmp',
                    'tif|tiff' => 'image/tiff',
                    // 'doc|docx' => 'application/msword' ,
                    // 'pdf' => 'application/pdf',
                    // 'zip' => 'multipart/x-zip'
                ));
                // handle file upload
                $attach_id = et_process_file_upload($_FILES[$fileID], 0, 0, $upload_mimes);
                
                if (!is_wp_error($attach_id)) {
                    
                    try {
                        $attach_data = et_get_attachment_data($attach_id);
                        
                        $options = AE_Options::get_instance();
                        
                        // save this setting to theme options
                        // $options->$imgType = $attach_data;
                        // $options->save();
                        /** 
                         * do action to control how to store data
                         * @param $attach_data the array of image data
                         * @param $request['data']
                         * @param $attach_id the uploaded file id
                        */
                        do_action('ae_upload_image' , $attach_data , $_POST['data'], $attach_id );

                        $res = array(
                            'success' => true,
                            'msg' => __('Branding image has been uploaded successfully', ET_DOMAIN) ,
                            'data' => $attach_data
                        );
                    }
                    catch(Exception $e) {
                        $res['msg'] = __('Error when updating settings.', ET_DOMAIN);
                    }
                } else {
                    $res['msg'] = $attach_id->get_error_message();
                }
            } else {
                $res['msg'] = __('Uploaded file not found', ET_DOMAIN);
            }
        }
        // send json to client
        wp_send_json($res);
    }
    /*
     * action hook ae_upload_image to setup site branding
    */
    function update_site_branding( $attach_data , $data ){

        switch ($data) {
            case 'site_logo':
            case 'mobile_icon':
            case 'mobile_logo' :
            case 'pre_loading' :
            case 'default_avatar' :
            case 'default_thumbnail_img':
                $options = AE_Options::get_instance();
                // save this setting to theme options
                $options->$data = $attach_data;
                $options->save();

                break;
            
            default:
                # code...
                break;
        }
    }
    
    /**
     * add add page to admin menu bar
     * @since 1.0
     * @package AE
     * @author Dakachi
     */
    public function admin_bar_menu() {
        global $et_admin_page, $wp_admin_bar;
        
        //
        //if ( !method_exists($et_admin_page, 'get_menu_items') ) return false;
        if (!current_user_can('manage_options') || !apply_filters('ae_admin_bar_menu', true)) return false;
        
        $parent = 'ae_menu';
        
        $wp_admin_bar->add_menu(array(
            'id' => $parent,
            'title' => __('Site Dashboard', ET_DOMAIN) ,
            'href' => false
        ));
        
        foreach ($this->pages as $key => $item) {
            $page_arg = $item['args'];
            $page = array(
                'parent' => $parent,
                'id' => $page_arg['slug'],
                'title' => $page_arg['page_title'],
                'href' => admin_url('/admin.php?page=' . $page_arg['slug'])
            );
            
            $wp_admin_bar->add_menu($page);
        }
    }
}

/**
 * register a admin submenu child of AE_Menu
 */
class AE_Submenu extends AE_Page
{
    static $instance = null;
    function __construct($args, $pages) {
        
        /**
         * add action to add submenu
         * callback sub_menu_page in parent class AE_Page
         */
        $this->add_action('admin_menu', 'sub_menu_page');
        
        $this->args = $args['args'];
        
        // page container
        $this->container = $args['container'];
        
        // page header
        if (isset($args['header'])) {
            $this->header = $args['header'];
        }
        
        // all pages list
        $this->pages = $pages;
        
        self::$instance = $this;
        parent::__construct();
    }
}

