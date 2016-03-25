<?php
class AppEngine extends AE_Base
{
    
    public function __construct() {
        /**
         * add script appengine
         */
        $this->add_action('wp_enqueue_scripts', 'print_scripts', 9);
        $this->add_action('admin_enqueue_scripts', 'print_scripts');
        
        $this->add_action('wp_footer', 'override_template_setting', 101);
        
        if (isset($_REQUEST['page'])) {
            $this->add_action('admin_print_footer_scripts', 'override_template_setting', 200);
        }
        
        /**
         * Create a nicely formatted and more specific title element text for output
         * in head of document, based on current view.
         */
        $this->add_filter('wp_title', 'ae_wp_title', 10, 2);
        
        /**
         * filter user avatar, replace by user upload avatar image
         */
        $this->add_filter('get_avatar', 'get_avatar', 10, 5);
        
        /**
         * add ajax when user request thumbnail form view carousels
         */
        $this->add_ajax('ae_request_thumb', 'request_thumb');
        
        /**
         * add ajax when user request delete an image from gallery
         */
        $this->add_ajax('ae_remove_carousel', 'remove_carousel');
         
        /**
          * hook to action reject post and send mail to post author 
          * @since 1.0
          * @author Dakachi
          */
        $this->add_action('ae_reject_post', 'reject_post');

        /**
         * hook to action ae insert post then send mail notify admin       
         * @since 1.0
         * @author Dakachi
         */
        $this->add_action('ae_process_payment_action', 'notify_admin', 10 ,2);

        
        add_theme_support('post-thumbnails');
        add_theme_support('automatic-feed-links');
        
        global $ae_post_factory;
        $ae_post_factory->set('post', new AE_Posts('post', array(
            'category'
        )));
        
        add_action('after_setup_theme', array(
            'AE_Language',
            'load_text_domain'
        ));
        
        $this->add_filter('nav_menu_css_class', 'special_nav_class', 10, 2);

        if (isset($_GET['close_notices'])) {
            update_option('option_sample_data', 1);
        }
    }

    /**
     * register base script
     */
    public function print_scripts() {
        
        $this->add_existed_script('jquery');
        
        $this->register_script('bootstrap', ae_get_url() . '/assets/js/bootstrap.min.js', array(
            'jquery'
        ) , ET_VERSION, true);
        
        /**
         * bootstrap slider for search form
         */
        $this->register_script('slider-bt', ae_get_url() . '/assets/js/slider-bt.js', array() , true);
        
        $this->register_script('et-googlemap-api', '//maps.googleapis.com/maps/api/js?sensor=false&signed_in=false', '3.0', true);
        
        $this->register_script('ae-colorpicker', ae_get_url() . '/assets/js/colorpicker.js', array(
            'jquery'
        ));
        
        $this->register_script('gmap', ae_get_url() . '/assets/js/gmap.js', array(
            'jquery',
            'et-googlemap-api'
        ));
        
        $this->register_script('marker', ae_get_url() . '/assets/js/marker.js', array(
            'gmap'
        ) , true);
        
        // tam thoi add de xai
        $this->register_script('jquery-validator', ae_get_url() . '/assets/js/jquery.validate.min.js', 'jquery');
        
        $this->register_script('chosen', ae_get_url() . '/assets/js/chosen.js', 'jquery');
        $this->register_script('jquery.cookie.js', ae_get_url() . '/assets/js/jquery.cookie.js', 'jquery');

        $this->register_script('marionette', ae_get_url() . '/assets/js/marionette.js', array(
            'jquery',
            'backbone',
            'underscore',
        ) , true);
        
        // ae core js appengine
        $this->register_script('appengine', ae_get_url() . '/assets/js/appengine.js', array(
            'jquery',
            'underscore',
            'backbone',
            'marionette', 
            'plupload',
        ) , true);
        
        wp_localize_script('chosen', 'raty', array(
            'hint' => array(
                __('bad', 'aecore-class-ae-framework-backend') ,
                __('poor', 'aecore-class-ae-framework-backend') ,
                __('nice', 'aecore-class-ae-framework-backend') ,
                __('good', 'aecore-class-ae-framework-backend') ,
                __('gorgeous', 'aecore-class-ae-framework-backend')
            )
        ));
        $adminurl = admin_url('admin-ajax.php');
        if(function_exists('icl_object_id')){
            $current = ICL_LANGUAGE_CODE;
            $adminurl = admin_url('admin-ajax.php?lang='.$current);
        }
        $variable = array(
            'ajaxURL' => $adminurl ,
            'imgURL' => ae_get_url() . '/assets/img/',
            'jsURL' => ae_get_url() . '/assets/js/',
            'loadingImg' => '<img class="loading loading-wheel" src="' . ae_get_url() . '/assets/img/loading.gif" alt="' . __('Loading...', 'aecore-class-ae-framework-backend') . '">',
            'loading' => __('Loading', 'aecore-class-ae-framework-backend') ,
            'ae_is_mobile'    => et_load_mobile() ? 1 : 0,
            'plupload_config' => array(
                'max_file_size' => '3mb',
                'url' => admin_url('admin-ajax.php') ,
                'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf') ,
                'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap') ,
                'filters' => array(
                    array(
                        'title' => __('Image Files', 'aecore-class-ae-framework-backend') ,
                        'extensions' => 'jpg,jpeg,gif,png'
                    )
                )
            ) ,
            'profile_completion' => array(
                'name'=> __("Fill your full name", 'aecore-class-ae-framework-backend') ,
                'location'=> __('Fill in the "Location"', 'aecore-class-ae-framework-backend') ,
                'e_mail'=> __('Fill in the "E-Mail"', 'aecore-class-ae-framework-backend') ,
                'paypal'=> __('Fill in the "Paypal Account"', 'aecore-class-ae-framework-backend') ,
                'phone_no'=> __('Fill in the "Phone no"', 'aecore-class-ae-framework-backend') ,
                'prof_title'=> __('Fill in the "Professional Title"', 'aecore-class-ae-framework-backend') ,
                'hourly_rate'=> __('Fill in the "Hourly Rate"', 'aecore-class-ae-framework-backend') ,
                'skills'=> __('Fill in the "Skills"', 'aecore-class-ae-framework-backend') ,
                'country'=> __('Fill in the "Country"', 'aecore-class-ae-framework-backend') ,
                'about'=> __('Fill in the "About"', 'aecore-class-ae-framework-backend') ,
            ),
            'homeURL' => home_url() ,
            'is_submit_post' => is_page_template('page-post-place.php') ? true : false,
            'is_submit_project' => is_page_template('page-submit-project.php') ? true : false,
            'is_single' => (!is_singular('page') && is_singular()) ? true : false,
            'max_images' => ae_get_option('max_carousel', 5) ,
            'user_confirm' => ae_get_option('user_confirm') ? 1 : 0,
            'max_cat' => ae_get_option('max_cat', 3) ,
            'confirm_delete_bid' => __("Are you sure you want to decline this bid?", 'aecore-class-ae-framework-backend') ,
            'confirm_message' => __("Are you sure to archive this?", 'aecore-class-ae-framework-backend') ,
            'confirm_message_delete' => __("Are you sure to delete this?", 'aecore-class-ae-framework-backend') ,
            'confirm_message_decline' => __("Are you sure to decline this?", 'aecore-class-ae-framework-backend') ,
            'map_zoom' => ae_get_option('map_zoom_default', 8) ,
            'map_center' => ae_get_option('map_center_default', array(
                'latitude' => 10,
                'longitude' => 106
            )) ,
            'fitbounds' => ae_get_option('fitbounds', ''),
            'limit_free_msg' => __("You have reached the maximum number of Free posts. Please select another plan.", 'aecore-class-ae-framework-backend') ,
            'error' => __("Please fill all require fields.", 'aecore-class-ae-framework-backend') ,
            'geolocation' => ae_get_option('geolocation', 0) ,
            'date_format' => get_option( 'date_format' ),
            'time_format' => get_option( 'time_format' ),
            'dates' => array(
                'days' => array(
                    __("Sunday", 'aecore-class-ae-framework-backend') ,
                    __("Monday", 'aecore-class-ae-framework-backend') ,
                    __("Tuesday", 'aecore-class-ae-framework-backend') ,
                    __("Wednesday", 'aecore-class-ae-framework-backend') ,
                    __("Thursday", 'aecore-class-ae-framework-backend') ,
                    __("Friday", 'aecore-class-ae-framework-backend') ,
                    __("Saturday", 'aecore-class-ae-framework-backend') ,
                    __("Sunday", 'aecore-class-ae-framework-backend')
                ) ,
                'daysShort' => array(
                    __("Sun", 'aecore-class-ae-framework-backend') ,
                    __("Mon", 'aecore-class-ae-framework-backend') ,
                    __("Tue", 'aecore-class-ae-framework-backend') ,
                    __("Wed", 'aecore-class-ae-framework-backend') ,
                    __("Thu", 'aecore-class-ae-framework-backend') ,
                    __("Fri", 'aecore-class-ae-framework-backend') ,
                    __("Sat", 'aecore-class-ae-framework-backend') ,
                    __("Sun", 'aecore-class-ae-framework-backend')
                ) ,
                'daysMin' => array(
                    __("Su", 'aecore-class-ae-framework-backend'),
                    __("Mo", 'aecore-class-ae-framework-backend'),
                    __("Tu", 'aecore-class-ae-framework-backend'),
                    __("We", 'aecore-class-ae-framework-backend'),
                    __("Th", 'aecore-class-ae-framework-backend'),
                    __("Fr", 'aecore-class-ae-framework-backend'),
                    __("Sa", 'aecore-class-ae-framework-backend'),
                    __("Su", 'aecore-class-ae-framework-backend')
                ) ,
                'months' => array(
                    __("January", 'aecore-class-ae-framework-backend') ,
                    __("February", 'aecore-class-ae-framework-backend') ,
                    __("March", 'aecore-class-ae-framework-backend') ,
                    __("April", 'aecore-class-ae-framework-backend') ,
                    __("May", 'aecore-class-ae-framework-backend') ,
                    __("June", 'aecore-class-ae-framework-backend') ,
                    __("July", 'aecore-class-ae-framework-backend') ,
                    __("August", 'aecore-class-ae-framework-backend') ,
                    __("September", 'aecore-class-ae-framework-backend') ,
                    __("October", 'aecore-class-ae-framework-backend') ,
                    __("November", 'aecore-class-ae-framework-backend') ,
                    __("December", 'aecore-class-ae-framework-backend')
                ) ,
                'monthsShort' => array(
                    __("Jan", 'aecore-class-ae-framework-backend'),
                    __("Feb", 'aecore-class-ae-framework-backend'),
                    __("Mar", 'aecore-class-ae-framework-backend'),
                    __("Apr", 'aecore-class-ae-framework-backend'),
                    __("May", 'aecore-class-ae-framework-backend'),
                    __("Jun", 'aecore-class-ae-framework-backend'),
                    __("Jul", 'aecore-class-ae-framework-backend'),
                    __("Aug", 'aecore-class-ae-framework-backend'),
                    __("Sep", 'aecore-class-ae-framework-backend'),
                    __("Oct", 'aecore-class-ae-framework-backend'),
                    __("Nov", 'aecore-class-ae-framework-backend'),
                    __("Dec", 'aecore-class-ae-framework-backend')
                )
            )
        );
        $variable['global_map_style'] = AE_Mapstyle::get_instance()->get_current_style();
        $variable = apply_filters('ae_globals', $variable);
        wp_localize_script('appengine', 'ae_globals', $variable);
        
        /**
         * html5
         */
        echo '<!--[if lt IE 9]>
                <script src="' . ae_get_url() . '/assets/js/html5.js"></script>
            <![endif]-->';
        
        // Loads the Internet Explorer specific stylesheet.
        if (!is_admin()) {
            $this->register_style('bootstrap', ae_get_url() . '/assets/css/bootstrap.min.css', array() , '3.0');
        }
    }
    
    /**
     * add script to footer override underscore templateSettings, localize validator message
     */
    function override_template_setting() {
?>
        <!-- override underscore template settings -->
        <script type="text/javascript">
            _.templateSettings = {
                evaluate: /\<\#(.+?)\#\>/g,
                interpolate: /\{\{=(.+?)\}\}/g,
                escape: /\{\{-(.+?)\}\}/g
            };
        </script>
        <!-- localize validator -->
        <script type="text/javascript">
            (function ($) {
                if(typeof $.validator !== 'undefined' ) {
                    $.extend($.validator.messages, {
                        required: "<?php
        _e("This field is required.", 'aecore-class-ae-framework-backend') ?>",
                        email: "<?php
        _e("Please enter a valid email address.", 'aecore-class-ae-framework-backend') ?>",
                        url: "<?php
        _e("Please enter a valid URL.", 'aecore-class-ae-framework-backend') ?>",
                        number: "<?php
        _e("Please enter a valid number.", 'aecore-class-ae-framework-backend') ?>",
                        digits: "<?php
        _e("Please enter only digits.", 'aecore-class-ae-framework-backend') ?>",
                        equalTo: "<?php
        _e("Please enter the same value again.", 'aecore-class-ae-framework-backend') ?>"
                    });
                }
                

            })(jQuery);
        </script>

    <?php
        // print google analytics code
        if(!is_admin()) {
            echo stripslashes(ae_get_option('google_analytics'));   
            // user confirm scripts 
            if (isset($_GET['act']) && $_GET['act'] == "confirm" && $_GET['key']) {
                $user_id = AE_Users::confirm($_GET['key']);
                if ($user_id) {
                    $mail = AE_Mailing::get_instance();
                    $mail->confirmed_mail($user_id);
            
                    ?>
                    <script type="text/javascript" id="user-confirm">
                        (function ($ , Views, Models, AE) {
                            $(document).ready(function(){
                                AE.pubsub.trigger('ae:notification', {
                                    msg: "<?php _e("Your account has been confirmed successfully!",'aecore-class-ae-framework-backend')  ?>",
                                    notice_type: 'success',
                                }); 
                                window.location.href = "<?php echo home_url(); ?>"                                        
                            });
                        })(jQuery, AE.Views, AE.Models, window.AE);
                    </script>   
                <?php }
            }  
        }   
    }
    
    /**
     * Create a nicely formatted and more specific title element text for output
     * in head of document, based on current view.
     *
     * @since AE 1.0
     *
     * @param string $title Default title text for current view.
     * @param string $sep Optional separator.
     * @return string The filtered title.
     */
    function ae_wp_title($title, $sep) {
        global $paged, $page;
        
        if (is_feed()) {
            return $title;
        }
        
        // Add the site name.
        $title.= get_bloginfo('name');
        
        // Add the site description for the home/front page.
        $site_description = get_bloginfo('description', 'display');
        if ($site_description && (is_home() || is_front_page())) {
            $title = "$title $sep $site_description";
        }
        
        // Add a page number if necessary.
        if ($paged >= 2 || $page >= 2) {
            $title = "$title $sep " . sprintf(__('Page %s', 'aecore-class-ae-framework-backend') , max($paged, $page));
        }
        
        return $title;
    }
    
    /**
     * filter wp avatar use AE_Users return a image tag with user setting avatar url
     * @param $avatar
     * @param $id_or_email
     * @param $size
     * @author Dakachi
     * @version 1.0
     */
    function get_avatar($avatar, $id_or_email, $size, $default, $alt) {
        
        $seller = AE_Users::get_instance();
        $profile_picture = $seller->get_avatar($id_or_email, $size);
        
        /**
         * overide $default by profile picture
         */
        if ($profile_picture != '') {
            $default = $profile_picture;
            if (false === $alt) $safe_alt = '';
            else $safe_alt = esc_attr($alt);
            
            $avatar = "<img alt='{$safe_alt}' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
        }        
        return $avatar;
    }
    
    /**
     * request carousel thumbnail image for edit ad form
     * send json back carousel js view
     * @author Dakachi
     * @version 1.0
     */
    function request_thumb() {
        $items = isset($_REQUEST['item']) ? $_REQUEST['item'] : array();
        $return = array();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $return[] = et_get_attachment_data($value, array(
                    'thumbnail'
                ));
            }
            wp_send_json(array(
                'success' => true,
                'data' => $return
            ));
        } else {
            wp_send_json(array(
                'success' => false
            ));
        }
    }
    
    /**
     * request remove image for edit ad form
     * send json back carousel js view
     * @author Dakachi
     * @version 1.0
     */
    function remove_carousel() {
        if (!current_user_can('manage_options')) {
            global $user_ID;
            $post = get_post($_REQUEST['id']);
            if ($user_ID != $post->post_author) wp_send_json(array(
                'success' => false,
                'msg' => __("Not owned this image!", 'aecore-class-ae-framework-backend')
            ));
        }
        wp_delete_post($_REQUEST['id'], true);
        wp_send_json(array(
            'success' => true
        ));
    }
    
    /**
     * reject post
     * @param $data
     */
    function reject_post($data) {
        $this->mail = AE_Mailing::get_instance();
        $this->mail->reject_post($data);
    }
    /**
     * send notify to admin 
     * @param Object $post Post data 
     * @since 1.1
     * @author Dakachi
     */
    function notify_admin ($payment_return, $data) {
        if(!isset($data['ad_id'])) return false;
        if(!$payment_return['ACK']) return ;           
        $this->mail = AE_Mailing::get_instance();
        $this->mail->new_post_alert($data['ad_id']);
    }

    function special_nav_class($classes, $item) {
        if (in_array('current-menu-item', $classes)) {
            $classes[] = 'active ';
        }
        return $classes;
    }
}
global $et_appengine;
$et_appengine = new AppEngine();
