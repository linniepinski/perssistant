<?php
error_reporting(E_ALL);
//ini_set('display_errors', 1);
//@ini_set('display_errors',1);

define("ET_UPDATE_PATH", "http://forum.enginethemes.com/?do=product-update");

define("ET_VERSION", '1.5.3');



if (!defined('ET_URL')) define('ET_URL', 'http://www.enginethemes.com/');



if (!defined('ET_CONTENT_DIR')) define('ET_CONTENT_DIR', WP_CONTENT_DIR . '/et-content/');



define('TEMPLATEURL', get_template_directory_uri() );



$theme_name = 'freelanceengine';



define('THEME_NAME', $theme_name);

@define('ET_DOMAIN', 'enginetheme');

define('MOBILE_PATH', TEMPLATEPATH . '/mobile/');



define('PROFILE', 'fre_profile');

define('PROJECT', 'project');

define('BID', 'bid');

define('PORTFOLIO', 'portfolio');



define('EMPLOYER', 'employer');

define('FREELANCER', 'freelancer');



define('PRICE', 'price');

define('CURRENCY', 'currency');



// define( 'ALLOW_UNFILTERED_UPLOADS', true );



if (!defined('THEME_CONTENT_DIR ')) define('THEME_CONTENT_DIR', WP_CONTENT_DIR . '/et-content' . '/' . $theme_name);

if (!defined('THEME_CONTENT_URL')) define('THEME_CONTENT_URL', content_url() . '/et-content' . '/' . $theme_name);



// theme language path

if (!defined('THEME_LANGUAGE_PATH')) define('THEME_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang/');



if (!defined('ET_LANGUAGE_PATH')) define('ET_LANGUAGE_PATH', THEME_CONTENT_DIR . '/lang');



if (!defined('ET_CSS_PATH')) define('ET_CSS_PATH', THEME_CONTENT_DIR . '/css');



if (!defined('USE_SOCIAL')) define('USE_SOCIAL', 1);



require_once dirname(__FILE__) . '/includes/index.php';

require_once TEMPLATEPATH . '/customizer/customizer.php';



if (!class_exists('AE_Base')) return;



require_once dirname(__FILE__) . '/mobile/functions.php';



class ET_FreelanceEngine extends AE_Base

{

    function __construct() {

        

        // disable admin bar if user can not manage options

        if (!current_user_can('manage_options') || et_load_mobile()) {

            show_admin_bar(false);

        };

        

        global $wp_roles;

        

        /**

         * register wp_role FREELANCER

         */

        if (!isset($wp_roles->roles[FREELANCER])) {

            

            //all new roles

            add_role(FREELANCER, __('Freelancer', 'functions') , array(

                'read' => true,

                

                // true allows this capability

                'edit_posts' => true,

                'delete_posts' => false,

                

                // Use false to explicitly deny

                

                

            ));

        }

        

        /**

         * add new role EMPLOYER

         */

        if (!isset($wp_roles->roles[EMPLOYER])) {

            add_role(EMPLOYER, __('Employer', 'functions') , array(

                'read' => true,

                

                // true allows this capability

                'edit_posts' => true,

                'delete_posts' => false,

                

                // Use false to explicitly deny

                

                

            ));

        }

        $this->add_action('init', 'fre_init');

        

        register_nav_menu('et_header', __("Fullscreen Header menu", 'functions'));

        register_nav_menu('et_header_standard', __("Standard Header menu", 'functions'));



        register_nav_menu('et_mobile', __("Mobile menu", 'functions'));

        register_nav_menu('et_footer', __("Footer menu", 'functions'));

        

        /**

         * filter post thumnail image, if not set use no image

         */

        

        // $this->add_filter('post_thumbnail_html', 'post_thumbnail_html', 10, 5);

        

        /**

         * Add role for themes

         */

        $this->add_filter('ae_social_auth_support_role','add_custom_role');



        /**

         * add query vars

         */

        $this->add_filter('query_vars', 'add_query_vars');

        

        /**

         * enqueue front end scripts

         */

        $this->add_action('wp_enqueue_scripts', 'on_add_scripts', 9);

        

        /**

         * enqueue front end styles

         */

        $this->add_action('wp_print_styles', 'on_add_styles', 10);

        

        /**

         * Filer query pre get post.

         */

        $this->add_action('pre_get_posts', 'pre_get_posts', 10);

        

        $this->add_filter('posts_orderby', 'order_by_post_status', 10, 2);

        

        /**

         * call new classes in footer

         */

        $this->add_action('wp_footer', 'script_in_footer', 100);

        

        /**

         * bundle some plugins

         */

        $this->add_action('tgmpa_register', 'ae_required_plugins');

        

        /**

         * add return url for user after register

         */

        $this->add_filter('ae_after_insert_user', 'filter_link_redirect_register');

        

        /**

         * add return url for user after login

         */

        $this->add_filter('ae_after_login_user', 'filter_link_redirect_login');

        

        /**

         * check role for user when register

         */

        $this->add_filter('ae_pre_insert_user', 'ae_check_role_user');

        

        /**

         * add user default value

         */

        $this->add_action('ae_insert_user', 'add_user_default_values');

        

        /**

         * update user profile title

         */

        $this->add_filter('ae_update_user', 'sync_profile_data');

        

        /**

         * check role for user when register

         */

        $this->add_filter('ae_convert_post', 'add_new_post_fields');

        

        /**

         * add users custom fields

         */

        $this->add_filter('ae_define_user_meta', 'add_user_meta_fields');

        

        /**

         * restrict pages

         */

        $this->add_action('template_redirect', 'restrict_pages');

        

        /**

         * redirect user to home after logout

         */

        //$this->add_filter('logout_url', 'logout_home', 10, 2);

        

        /**

         * filter profile link and replace by author post link

         */

        $this->add_filter('post_type_link', 'post_link', 10, 2);

        

        $this->add_filter('get_terms_orderby', 'order_terms', 10, 3);



        /**

         * add comment type filter dropdow

        */

        $this->add_filter('admin_comment_types_dropdown', 'admin_comment_types_dropdown');

        

        /**

         * add action admin menu prevent seller enter admin area

         */

        $this->add_action('admin_menu', 'redirect_seller');

        $this->add_action('login_init', 'redirect_login');

        

        // add theme support.

        add_theme_support('automatic-feed-links');

        

        //add new image size

        add_image_size('portfolio', 230, 170, true);

        

        /**

         * user front end control  : edit profile, update avatar

         */

        $this->user_action = new AE_User_Front_Actions(new AE_Users());

        

        /** 

         * init all action control project

         */

        $this->project_action = new Fre_ProjectAction();

        

        // init class bid action control bid

        $this->bid_action = new Fre_BidAction();



        // init action related to review

        $this->review_action = new Fre_ReviewAction();



        // init class control profile action

        $this->profile_action = new Fre_ProfileAction();

        // init class control portfolio update option

        $this->portfolio_action = new Fre_PortfolioAction();

        /**

         * init place meta post

         */

        new AE_Schedule(PROJECT);

        new AE_PostMeta(PROJECT);

    }

    

    /**

     * init theme

     * @since 1.0

     * @author Dakachi

     */

    function fre_init() {

        

        // update database fix profile

        if(!get_option('change_profile_namess')) {

            global $wpdb;

            $wpdb->query("

                UPDATE $wpdb->posts 

                SET post_type = 'fre_profile'

                WHERE post_type = 'profile'

                "

            );

            update_option( 'change_profile_namess', 1 );

            echo 1;

        }

        if (!get_option('fix_interview_default')) {

            $users = new WP_User_Query(array('role' => 'freelancer', 'fields' => 'ID'));
            foreach ($users->get_results() as $item) {
                add_user_meta($item, 'interview_status', '', true);
            }
            update_option('fix_interview_default', 1);

            echo 'fixed db interview detail - reload page pls';

        }

        

        

        // register a post status: Reject (use when a project was rejected)

        register_post_status('reject', array(

            'label' => __('Reject', 'functions') ,

            'private' => true,

            'public' => false,

            'exclude_from_search' => false,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('Reject <span class="count">(%s)</span>', 'Reject <span class="count">(%s)</span>') ,

        ));

        

        /* a project after expired date will be changed to archive */

        register_post_status('archive', array(

            'label' => __('Archive', 'functions') ,

            'private' => false,

            'public' => true,

            'exclude_from_search' => true,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>') ,

        ));

        

        /* after finish a project, project and accepted bid will be changed to complete */

        register_post_status('complete', array(

            'label' => _x('complete', 'post') ,

            'public' => true,

            'exclude_from_search' => false,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('Complete <span class="count">(%s)</span>', 'Complete <span class="count">(%s)</span>') ,

        ));

        

        register_post_status('accept', array(

            'label' => _x('accepted', 'post') ,

            'public' => true,

            'exclude_from_search' => false,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('Accepted <span class="count">(%s)</span>', 'Accepted <span class="count">(%s)</span>') ,

        ));

        

        /** 

         * when a project was accept a bid, it will be change to close

         */

        register_post_status('close', array(

            'label' => _x('close', 'post') ,

            'public' => true,

            'exclude_from_search' => false,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('Close <span class="count">(%s)</span>', 'Close <span class="count">(%s)</span>') ,

        ));

        

        /** 

         * when employer close project or freelancer quit a project, it change to disputing

         */

        register_post_status('disputing', array(

            'label' => _x('disputing', 'post') ,

            'public' => true,

            'exclude_from_search' => false,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('Disputing <span class="count">(%s)</span>', 'Disputing <span class="count">(%s)</span>') ,

        ));

        register_post_status('opened_disput', array(

            'label' => _x('opened_disput', 'post') ,

            'public' => true,

            'exclude_from_search' => false,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('opened_disput <span class="count">(%s)</span>', 'opened_disput <span class="count">(%s)</span>') ,

        ));

        /** 

         * when admin resolve a disputing project, it's status change to disputed

         */

        register_post_status('disputed', array(

            'label' => _x('disputed', 'post') ,

            'public' => true,

            'exclude_from_search' => false,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('Disputed <span class="count">(%s)</span>', 'Disputed <span class="count">(%s)</span>') ,

        ));



        /**

         * when a user dont want employer hide/contact him,

         * he can change his profile to hide, so no one can contact him

         */

        register_post_status('hide', array(

            'label' => _x('hide', 'post') ,

            'public' => false,

            'exclude_from_search' => true,

            'show_in_admin_all_list' => true,

            'show_in_admin_status_list' => true,

            'label_count' => _n_noop('Hide <span class="count">(%s)</span>', 'Hide <span class="count">(%s)</span>') ,

        ));

        /**

         * set up social login

         */

        if(function_exists('init_social_login')){

            init_social_login();

        };

        /**

         * override author link

         */

        global $wp_rewrite;

        if ($wp_rewrite->using_permalinks()) {

            $wp_rewrite->author_base = ae_get_option('author_base', 'author');

            $wp_rewrite->author_structure = '/' . $wp_rewrite->author_base . '/%author%';

        }



        

    }

    

    /**

     * add custom role for theme

     */

    function add_custom_role(){

        $role = array( FREELANCER => FREELANCER, EMPLOYER=>EMPLOYER);

        return $role;

    }

    function add_user_default_values($result) {

        if (ae_user_role($result) == FREELANCER) {

            update_user_meta($result, 'user_available', 'on');

        }

    }

    

    public function sync_profile_data($result) {

        $user = get_user_by('id', $result);

        $ae_users = AE_Users::get_instance();

        $user_data = $ae_users->convert($user);

        $profile = get_post($user_data->user_profile_id);

        if (ae_user_role($result) == FREELANCER && !empty($profile) && $profile->post_type == "profile") {

            

            //sync profile title

            $args = array(

                'ID' => $user_data->user_profile_id,

                'post_title' => $user->display_name

            );

            wp_update_post($args);

            

            //sync profile post_status

            global $wpdb;

            

            if (!$profile = get_post($profile)) return;

            

            $new_status = isset($user_data->user_available) && $user_data->user_available == "on" ? "publish" : "hide";

            

            if ($new_status == $profile->post_status) return;

            

            $wpdb->update($wpdb->posts, array(

                'post_status' => $new_status

            ) , array(

                'ID' => $profile->ID

            ));

            

            clean_post_cache($profile->ID);

            

            $old_status = $profile->post_status;

            $profile->post_status = $new_status;

            wp_transition_post_status($new_status, $old_status, $profile);

        }

    }

    

    /**

     * filter redirect link after logout

     * @param string $logouturl

     * @param string $redir

     * @since 1.0

     * @author ThaiNt

     */

//    public function logout_home($logouturl, $redir) {
//
//        $redir = get_option('siteurl');
//
//        return $logouturl . '&amp;redirect_to=' . urlencode($redir);
//
//    }

    

    /**

     * add query var

     */

    function restrict_pages() {

        global $current_user;

        

        //if user is login and access to page auth

        if (is_user_logged_in() && is_page_template('page-auth.php')) {

            wp_redirect(home_url());

            exit();

        }

        

        //if user is not login and access to page profile

        if (!is_user_logged_in() && is_page_template('page-profile.php')) {

            wp_redirect(home_url());

            exit();

        }

        

        // prevent user enter single profile

        if (is_singular(PROFILE)) {

            global $post;

            wp_redirect(get_author_posts_url($post->post_author));

            exit;

        }

        // if( is_singular(PROJECT) ){

        //     global $post, $user_ID;

        //     if( $post->post_status != 'publish' ){

        //         if( $post->post_author != $user_ID && !current_user_can( 'manage_options ' ) ){

        //             wp_redirect( home_url() );

        //             exit; 

        //         }

        //     }

        // }

        // prevent freelancer post project

        if (is_page_template('page-submit-project.php')) {

            if (!fre_share_role() && ae_user_role() == FREELANCER) {

                wp_redirect(home_url());

                exit;

            }

        }

        

        /**

         * prevent user try to view a bid details

         # when user enter a link to bid redirect to home url

         */

        if (is_singular(BID)) {

            wp_redirect(home_url());

            exit;

        }

    }

    

    /**

     * filter profile link and change it to author posts link

     * @param String $url The post url

     * @param Object $post current post object

     */

    public function post_link($url, $post) {

        if ($post->post_type == PROFILE) {

            return get_author_posts_url($post->post_author);

        }

        return $url;

    }

    

    /**

     * Filter the ORDERBY clause of the terms query.

     *

     * @since 1.0

     *

     * @param string       $orderby    ORDERBY clause of the terms query.

     * @param array        $args       An array of terms query arguments.

     * @param string|array $taxonomies A taxonomy or array of taxonomies.

     * @author Dakachi

     */

    public function order_terms($orderby, $args, $taxonomies) {

        $taxonomy = array_pop($taxonomies);

        

        // get taxonomies sort from option

        switch ($taxonomy) {

            case 'project_category':

                $_orderby = ae_get_option('project_category_order', 'name');

                break;



            case 'project_type':

                $_orderby = ae_get_option('project_category_order', 'name');

                break;



            default:

                return $orderby;

        }

        

        // $_orderby = strtolower( $args['orderby'] );

        if ('count' == $_orderby) {

            $orderby = 'tt.count';

        } else if ('name' == $_orderby) {

            $orderby = 't.name';

        } else if ('slug' == $_orderby) {

            $orderby = 't.slug';

        } else if ('term_group' == $_orderby) {

            $orderby = 't.term_group';

        } else if ('none' == $_orderby) {

            $orderby = '';

        } elseif (empty($_orderby) || 'id' == $_orderby) {

            $orderby = 't.term_id';

        } else {

            $orderby = 't.name';

        }

        

        return $orderby;

    }



    /**

     * hook to filter comment type dropdown and add review favorite to filter comment

     * @param Array $comment_types

    */

    function admin_comment_types_dropdown($comment_types) {

        $comment_types['fre_review']   = __("Freelancer Review", 'functions');

        $comment_types['em_review']   = __("Employer Review", 'functions');

        $comment_types['fre_report']   = __("Report", 'functions');

        $comment_types['fre_invite']   = __("Invite", 'functions');

        return $comment_types;

    }

    

    /**

     * redirect wp

     */

    function redirect_seller() {

        if (!(current_user_can('manage_options') || current_user_can('editor'))) {

            wp_redirect(home_url());

            exit;

        }

    }

    

    function redirect_login() {

        if (ae_get_option('login_init') && !is_user_logged_in()) {

            wp_redirect(home_url());

            exit;

        }

    }

    

    /**

     * add query var

     */

    function add_query_vars($vars) {

        array_push($vars, 'paymentType');

        return $vars;

    }

    

    //add new return custom fields for posts

    function add_new_post_fields($result) {

        

        //author name field

        if (!isset($result->author_name)) {

            $author = get_user_by('id', $result->post_author);

            $result->author_name = isset($author->display_name) ? $author->display_name : __('Unnamed', 'functions');

        }

        

        //comments field

        if (!isset($result->comment_number)) {

            $num_comments = get_comments_number($result->ID);

            if (et_load_mobile()) {

                $result->comment_number = $num_comments ? $num_comments : 0;

            } else {

                if (comments_open($result->ID)) {

                    if ($num_comments == 0) {

                        $comments = __('No Comments', 'functions');

                    } elseif ($num_comments > 1) {

                        $comments = $num_comments . __(' Comments', 'functions');

                    } else {

                        $comments = __('1 Comment', 'functions');

                    }

                    $write_comments = '<a href="' . get_comments_link() . '">' . $comments . '</a>';

                } else {

                    $write_comments = __('Comments are off for this post.', 'functions');

                }

                $result->comment_number = $write_comments;

            }

        }

        

        //post excerpt field

        if ($result->post_excerpt) {

            ob_start();

            echo apply_filters('the_excerpt', $result->post_excerpt);

            $post_excerpt = ob_get_clean();

            $result->post_excerpt = $post_excerpt;

        }

        

        //category field

        $categories = get_the_category();

        $separator = ' - ';

        $output = '';

        if ($categories) {

            foreach ($categories as $category) {

                $output.= '<a href="' . get_category_link($category->term_id) . '" title="' . esc_attr(sprintf(__("View all posts in %s", 'functions') , $category->name)) . '">' . $category->cat_name . '</a>' . $separator;

            }

            $result->category_name = trim($output, $separator);

        }

        

        //avatar field

        //if(!isset($result->avatar)){

        $result->avatar = get_avatar($result->post_author, 65);

        

        //}

        return $result;

    }

    

    //redirect user to url after login

    function filter_link_redirect_login($result) {

        $result->redirect_url = apply_filters('ae_after_login_link', home_url());

        $result->do = "login";

        return $result;

    }

    

    //redirect user to url after register

    function filter_link_redirect_register($result) {

        

        if (!is_wp_error($result)) {

            

            // $user_info = get_userdata($result->ID);

            $role = ae_user_role($result->ID);

        } else {

            $role = '';

        }

        

        $redirect_url = ($role == "employer" && AE_Users::is_activate($result->ID) ) ? et_get_page_link('submit-project') : et_get_page_link('profile');

        $result->redirect_url = apply_filters('ae_after_register_link', $redirect_url);

        $result->do = "register";

        

        return $result;

    }

    

    //prevent user add other roles

    function ae_check_role_user($user_data) {

        if ( isset($user_data['role'] ) && ($user_data['role'] != FREELANCER && $user_data['role'] != EMPLOYER)) {

            unset($user_data['role']);

        }

        return $user_data;

    }

    

    //add custom fields for user

    function add_user_meta_fields($default) {

        

        $default = wp_parse_args(array(

            'user_hour_rate',

            'user_profile_id',

            'user_currency',

            'user_skills',

            'user_available'

        ) , $default);



        if(ae_get_option('use_escrow')) {

            $default[] = 'paypal';

        }

        

        return $default;

    }

    function on_add_scripts() {

        

        global $user_ID;

        

        $this->add_existed_script('jquery');

        $this->add_existed_script('underscore');

        $this->add_existed_script('backbone');

        $this->add_existed_script('plupload');

        $this->add_existed_script('appengine');

        

        $this->add_existed_script('chosen');

        

        // add script validator

        $this->add_existed_script('jquery-validator');

        $this->add_existed_script('bootstrap');

        $this->add_script('modernizr', get_template_directory_uri() . '/js/modernizr.custom.js', array() , ET_VERSION, false);
        
        $this->add_script('customjs', get_template_directory_uri() . '/js/custom.js', array() , ET_VERSION, false);

         // $this->add_script('wow', get_template_directory_uri() . '/js/wow.js', array() , ET_VERSION, false);
         

        /**

         * bootstrap slider for search form

         */

        $this->add_existed_script('slider-bt');

        

        /**

         *  Add date picker js

         */

        

        //$this->add_existed_script('jquery-ui-datepicker');

        wp_enqueue_script('modernizr', get_template_directory_uri() . '/js/modernizr.min.js', array() , true, false);

        

        /*

         * Adds JavaScript to pages with the comment form to support

         * sites with threaded comments (when in use).

        */

        if (is_singular() && comments_open() && get_option('thread_comments')) $this->add_existed_script('comment-reply');

        

        $this->add_script('fre-lib', get_template_directory_uri() . '/js/fre-lib.js', array() , ET_VERSION, true);

        

        $this->add_script('front', get_template_directory_uri() . '/js/front.js', array(

            'jquery',

            'underscore',

            'backbone',

            'appengine',

            'fre-lib'

        ) , ET_VERSION, true);

        

        // add translatable texts

        wp_localize_script('front', 'fre_fronts', array(

            'portfolio_img' => __('Please select an image!', 'functions')

        ));

        

        /*

         * js for authenticate in page register & submit project

        */

        if (is_page_template('page-auth.php') || is_page_template('page-submit-project.php')) {

            $this->add_script('authenticate', get_template_directory_uri() . '/js/authenticate.js', array(

                'jquery',

                'underscore',

                'backbone',

                'appengine',

                'front'

            ) , ET_VERSION, true);

        }

        

        /*

         * script edit profile

        */

        if (is_page_template('page-profile.php')) {

            $this->add_script('profile', get_template_directory_uri() . '/js/profile.js', array(

                'jquery',

                'underscore',

                'backbone',

                'appengine',

                'front'

            ) , ET_VERSION, true);

        }

        

        /*

         * script reset pass

        */

        if (is_page_template('page-reset-pass.php')) {

            $this->add_script('reset-pass', get_template_directory_uri() . '/js/reset-pass.js', array(

                'jquery',

                'underscore',

                'backbone',

                'appengine',

                'front'

            ) , ET_VERSION, true);

        }

        

        // Add style css for mobile version.

        if (et_load_mobile()) {

            

            // $this->add_script('classie', get_template_directory_uri() . '/mobile/js/classie.js',

            //     array('jquery'), ET_VERSION, true

            // );

            

            $this->add_script('mobile-main', get_template_directory_uri() . '/mobile/js/main.js', array(

                'jquery',

                'underscore',

                'backbone',

                'appengine',

                'front'

            ) , ET_VERSION, true);

            

            /*

             * js working on single project

            */

            if (is_singular('project')) {

                $this->add_script('single-project', get_template_directory_uri() . '/js/single-project.js', array(

                    'jquery',

                    'underscore',

                    'backbone',

                    'appengine',

                    'front'

                ) , ET_VERSION, true);

                

                // add this for social like

                

                wp_localize_script('single-project', 'single_text', array(

                    'agree' => __('Agree', 'functions') ,

                    'accepted' => __('Accepted', 'functions') ,

                    'skip' => __('Skip', 'functions') ,

                    'working' => __('Working', 'functions') ,

                    'complete' => __('Complete', 'functions') ,

                    'completed' => __('Completed', 'functions') ,

                ));

                

                $this->add_script('workspace', get_template_directory_uri() . '/js/project-workspace.js', array(

                    'jquery',

                    'underscore',

                    'backbone',

                    'appengine',

                    'front'

                ) , ET_VERSION, true);

            }

            return;

        }

        

        /*

         * js working on single project

        */

        if (is_singular('project')) {

            $this->add_script('single-project', get_template_directory_uri() . '/js/single-project.js', array(

                'jquery',

                'underscore',

                'backbone',

                'appengine',

                'front'

            ) , ET_VERSION, true);

            

            $this->add_script('workspace', get_template_directory_uri() . '/js/project-workspace.js', array(

                'jquery',

                'underscore',

                'backbone',

                'appengine',

                'front'

            ) , ET_VERSION, true);



            // add this for social like

            $this->add_script('addthis-script', '//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-4ed5eb280d19b26b', array() , ET_VERSION, true);

            wp_localize_script('single-project', 'single_text', array(

                'agree' => __('Agree', 'functions') ,

                'accepted' => __('Accepted', 'functions') ,

                'skip' => __('Skip', 'functions') ,

                'working' => __('Working', 'functions') ,

                'complete' => __('Complete', 'functions') ,

                'completed' => __('Completed', 'functions') ,

            ));

        }

        

        // Adds Masonry to handle vertical alignment of footer widgets.

        if (is_active_sidebar('de-footer-1')) $this->add_existed_script('jquery-masonry');

        

        $this->add_script('index', get_template_directory_uri() . '/js/index.js', array(

            'jquery',

            'underscore',

            'backbone',

            'appengine',

            'front'

        ) , ET_VERSION, true);

        

        // if(is_page_template( 'page-submit-project.php' )){

        //     $this->add_script('post-project', get_template_directory_uri().'/js/post-project.js', array('jquery', 'underscore','backbone', 'appengine', 'front'), ET_VERSION, true);

        // }

        

        // author page js

        if (is_author()) {

            $this->add_script('author-page', get_template_directory_uri() . '/js/author.js', array(

                'jquery',

                'underscore',

                'backbone',

                'appengine',

                'front'

            ) , ET_VERSION, true);

        }

        

        // author profile js

        // if( is_singular( PROFILE ) || is_author() ){

        //     $this->add_script('single-profile', get_template_directory_uri().'/js/single-profile.js', array('jquery', 'underscore','backbone', 'appengine', 'front'), ET_VERSION, true);

        // }

        

        

    }

    

    function on_add_styles() {

        $this->add_existed_style('bootstrap');

        

        // Font Awesome

        $this->add_style('font-icon', get_template_directory_uri() . '/css/font-awesome.min.css', array() , ET_VERSION);

        

        // GG Font

        $this->add_style('gg-font', '//fonts.googleapis.com/css?family=Raleway:400,300,500,600,700,800', array(

            'bootstrap'

        ) , ET_VERSION);

        

        // Chosen

        $this->add_style('chosen', get_template_directory_uri() . '/css/chosen.css', array(

            'bootstrap'

        ) , ET_VERSION);

        

        //iOS7 switch button

        $this->add_style('switchery', get_template_directory_uri() . '/css/switchery.css', array(

            'bootstrap'

        ) , ET_VERSION);

        

        // Add style css for mobile version.

        if (et_load_mobile()) {

            $this->add_style('mobile-style', get_template_directory_uri() . '/mobile/css/custom.css', array(

                'bootstrap'

            ) , ET_VERSION);


        }
        if (et_load_mobile()) {

            $this->add_style('mobile-style_2', get_template_directory_uri() . '/mobile/css/custom_2.css', array(

                'bootstrap'

            ) , ET_VERSION);

            return;

        }

        

        // theme custom.css

        $this->add_style('custom', get_template_directory_uri() . '/css/custom.css', array(

            'bootstrap'

        ) , ET_VERSION);

        // theme custom_2.css
        $this->add_style('custom_2', get_template_directory_uri() . '/css/custom_2.css', array(

            'bootstrap'

        ) , ET_VERSION);
        

        // style.css

        $this->add_style('freelanceengine-style', get_stylesheet_uri() , array(

            'bootstrap'

        ) , ET_VERSION);

        

        // style.css

       /* $this->add_style('animate', get_template_directory_uri() .'/css/animate.css' , array(

             'bootstrap'

        ) , ET_VERSION);*/

        

        

    }

    

    /*

     * custom query prev query post

    */

    function pre_get_posts($query) {

        if (is_tax('project_category') || is_tax('project_type') ) {

            if ($query->is_main_query()) {

                $query->set('post_type', PROJECT);

                $query->set('post_status', 'publish');

            }

        }



        if(is_tax('skill')) {

            if ($query->is_main_query()) {

                $query->set('post_type', PROJECT);

                $query->set('post_status', 'publish');

            }

        }

        

        if ((is_post_type_archive(PROJECT) || is_tax('project_category') || is_tax('project_type') || is_tax('skill')) && !is_admin()) {

            if (!$query->is_main_query()) return $query;

            

            if (current_user_can('manage_options')) {

                $query->set('post_status', array(

                    'pending',

                    'publish'

                ));

                

                //$query->set ('orderby', 'post_status');

                

                

            } else {

                $query->set('post_status', 'publish');

            }

        }

        

        if (is_author() && $query->is_main_query()) {

            $query->set('post_status', array(

                'publish',

                'close',

                'complete'

            ));

        }
        if (is_post_type_archive(PROFILE) && $query->is_main_query()) {
            $this->add_filter('posts_join', 'fre_join_select_confirmed_posts', 10, 2);

            $query->set('post_status', array(

                'publish',

                'draft'

            ));

        }


        return $query;

    }

    function fre_join_select_confirmed_posts($join, $query)
    {
        global $wpdb;

        $join .= " INNER JOIN $wpdb->usermeta AS s1 ON wp_posts.post_author = s1.user_id AND s1.meta_key = 'interview_status' AND (s1.meta_value ='confirmed' OR s1.meta_value = '' ) ";
        $join .= " INNER JOIN $wpdb->usermeta AS s2 ON wp_posts.post_author = s2.user_id AND s2.meta_key = 'user_available' AND s2.meta_value ='on' ";
        $join .= " INNER JOIN $wpdb->usermeta AS s3 ON wp_posts.post_author = s3.user_id AND s3.meta_value ='a:1:{s:10:\"freelancer\";b:1;}' ";

        return $join;

    }


    /*

     * custom order when admin view page-archive-projects

    */

    function order_by_post_status($orderby, $object) {

        global $user_ID;

        if ((is_post_type_archive(PROJECT) || is_tax('project_category') || is_tax('project_type') || is_tax('skill')) && !is_admin() && current_user_can('edit_others_posts')) {

            return self::order_by_post_pending($orderby, $object);

        }



        if (isset($object->query_vars['post_status']) && is_array($object->query_vars['post_status']) 

            && isset($object->query_vars['author']) && $user_ID == $object->query_vars['author']) {

            return self::order_by_post_pending($orderby, $object);

        }

        return $orderby;

    }


    

    static function order_by_post_pending($orderby, $object) {

        global $wpdb;

        $orderby = " case {$wpdb->posts}.post_status  

                            when 'disputing' then 0

                            when 'reject' then 1

                            when 'pending' then 2                             

                            when 'publish' then 3                           

                            when 'close' then 4

                            when 'complete' then 5 

                            when 'draft' then 6

                            when 'archive' then 7

                            end, 

                        {$wpdb->posts}.post_date DESC";

        return $orderby;

    }

    

    // load bundle plugin

    function ae_required_plugins() {

        $plugins = array(

            array(

                'name' => 'WPBakery Visual Composer Plugin',

                'slug' => 'js_composer',

                'source' => 'http://www.enginethemes.com/files/js_composer.zip',

                'required' => false,

                'version' => '4.3',

                'force_activation' => false,

                'force_deactivation' => true,

                'external_url' => 'http://www.enginethemes.com/files/js_composer.zip',

            ) ,

            array(

                'name' => 'Revolution Slider Plugin',

                'slug' => 'revslider',

                'source' => 'http://www.enginethemes.com/files/revslider.zip',

                'required' => false,

                'version' => '4.5.95',

                'force_activation' => false,

                'force_deactivation' => true,

                'external_url' => 'http://www.enginethemes.com/files/revslider.zip',

            )

        );

        

        // Change this to your theme text domain, used for internationalising strings

        $theme_text_domain = 'functions';

        

        $config = array(

            'domain' => $theme_text_domain,

            'default_path' => '',

            'parent_menu_slug' => 'themes.php',

            'parent_url_slug' => 'themes.php',

            'menu' => 'install-required-plugins',

            'has_notices' => true,

            'is_automatic' => false,

            'message' => '',

            'strings' => array(

                'page_title' => __('Install Required Plugins', $theme_text_domain) ,

                'menu_title' => __('Install Plugins', $theme_text_domain) ,

                'installing' => __('Installing Plugin: %s', $theme_text_domain) ,

                

                // %1$s                           = plugin name

                'oops' => __('Something went wrong with the plugin API.', $theme_text_domain) ,

                'notice_can_install_required' => _n_noop('This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.') ,

                

                // %1$s                           = plugin name(s)

                'notice_can_install_recommended' => _n_noop('This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.') ,

                

                // %1$s                           = plugin name(s)

                'notice_cannot_install' => _n_noop('Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.') ,

                

                // %1$s                           = plugin name(s)

                'notice_can_activate_required' => _n_noop('The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.') ,

                

                // %1$s                           = plugin name(s)

                'notice_can_activate_recommended' => _n_noop('The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.') ,

                

                // %1$s                           = plugin name(s)

                'notice_cannot_activate' => _n_noop('Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.') ,

                

                // %1$s                           = plugin name(s)

                'notice_ask_to_update' => _n_noop('The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.') ,

                

                // %1$s                           = plugin name(s)

                'notice_cannot_update' => _n_noop('Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.') ,

                

                // %1$s                           = plugin name(s)

                'install_link' => _n_noop('Begin installing plugin', 'Begin installing plugins') ,

                'activate_link' => _n_noop('Activate installed plugin', 'Activate installed plugins') ,

                'return' => __('Return to Required Plugins Installer', $theme_text_domain) ,

                'plugin_activated' => __('Plugin activated successfully.', $theme_text_domain) ,

                'complete' => __('All plugins installed and activated successfully. %s', $theme_text_domain)

                

                // %1$s = dashboard link

                

                

            )

        );

        

        tgmpa($plugins, $config);

    }

    

    function script_in_footer() {



        do_action('ae_before_render_script');

?>

        <script type="text/javascript" id="frontend_scripts">



        (function ($ , Views, Models, AE) {

            $(document).ready(function(){

                var currentUser;



                if($('#user_id').length > 0 ) {

                    currentUser = new Models.User( JSON.parse($('#user_id').html()) );

                    //currentUser.fetch();

                } else {

                    currentUser = new Models.User();

                }



                // init view front 

                if(typeof Views.Front !== 'undefined') {

                    AE.App = new Views.Front({model : currentUser});            

                }

                AE.App.user = currentUser; 

                // init view submit project

                if(typeof Views.SubmitProject !== 'undefined' && $('#post-place').length > 0) {

                    AE.Submit_Project = new Views.SubmitProject({

                        el: '#post-place',

                        user_login: currentUser.get('id'),

                        free_plan_used: 0,

                        limit_free_plan: false,

                        step: 2

                    });

                }



                new Views.SubmitBibPlan({el :"#upgrade-account" , user_login: currentUser.get('id'),

                    free_plan_used: 0,

                    limit_free_plan: false,

                    step: 2

                }); 

                // init view search form 

                if(typeof Views.SearchForm !== 'undefined') {

                    AE.search = new Views.SearchForm({

                        el: '#search_container'

                    });

                } 

                //create new auth view

                if(typeof Views.Auth !== 'undefined') {

                    new Views.Auth({ el: 'body' });

                }

                //create new user profile view

                if(typeof Views.Profile !== 'undefined') {

                    new Views.Profile();

                }

                //create new single profile view

                if(typeof Views.Single_Profile !== 'undefined') {

                    new Views.Single_Profile();

                }

                

            });

        })(jQuery, AE.Views, AE.Models, window.AE);



        </script>  

        <?php

        do_action('ae_after_render_script');

    }

}



global $et_freelance;

add_action('after_setup_theme', 'et_setup_theme');

function et_setup_theme() {

    global $et_freelance;

    $et_freelance = new ET_FreelanceEngine();

    if (is_admin() || current_user_can('manage_options')) {

        new ET_Admin();

    }

}





add_action('user_register' , 'de_new_user_alert');

function de_new_user_alert($user_id) {

     $display_name = get_the_author_meta( 'display_name', $user_id );

      $email = get_the_author_meta( 'user_email', $user_id );

     wp_mail(get_site_option('admin_email'), 'New user register ', 'There is a new user register on your site: "'.get_site_option('blogname').'" with id:' . $user_id . ' Name:' . $display_name .' E-mail:' . $email );

 }



/**

 * add custom status to wordpress post status

 */

function fre_append_post_status_list() {

    if(!isset($_REQUEST['post'])) return ;

    $post = get_post($_REQUEST['post']);   

    $complete = '';

    $closed = '';

    $disputing = '';

    $disputed = '';

    $label = '';

    

    if ($post && ($post->post_type == BID || $post->post_type == PROJECT) ) {

        if ($post->post_status == 'complete') {

            $complete = " selected='selected'";

            $label = '<span id="post-status-display">' . __("Completed", 'functions') . '</span>';

        }

        if ($post->post_status == 'close') {

            $closed = " selected='selected'";

            $label = '<span id="post-status-display">' . __("Close", 'functions') . '</span>';

        }

        if ($post->post_status == 'disputing') {

            $disputing = " selected='selected'";

            $label = '<span id="post-status-display">' . __("Disputing", 'functions') . '</span>';

        }

        if ($post->post_status == 'disputed') {

            $disputed = " selected='selected'";

            $label = '<span id="post-status-display">' . __("Disputed", 'functions') . '</span>';

        }

?>

          <script>

          jQuery(document).ready(function($){

               $("select#post_status").append("<option value='complete' <?php

        echo $complete; ?>>Completed</option><option value='close' <?php

        echo $closed; ?>>Close</option><option value='disputing' <?php

        echo $disputing; ?>>Disputing</option><option value='disputed' <?php

        echo $disputed; ?>>Disputed</option>");

               $(".misc-pub-section label").append('<?php

        echo $label; ?>');

          });

          </script>

          <?php

    }

}

add_action('admin_footer-post.php', 'fre_append_post_status_list');





add_filter( 'get_avatar', 'filter_user_avatar', 11, 5 );

function filter_user_avatar($avatar, $id_or_email, $size, $default, $alt){

    $default = '';

    $user = false;

    if (is_numeric($id_or_email)) {

        $id = (int)$id_or_email;

        $user = get_userdata($id);

    } elseif ( is_object($id_or_email) ) { 

        if ( ! empty( $id_or_email->user_id ) ) {

            $id = (int) $id_or_email->user_id;

            $user = get_userdata($id);

        }

    }else {

        $user =   get_user_by('email', $id_or_email );

        if(!$user){

            $user = false;

        }

        // $user = false;

    }

    if(!$user) return $avatar;



    $profile_id = get_user_meta($user->ID, 'user_profile_id', true);

    if(!$profile_id) return $avatar;

    if(!has_post_thumbnail( $profile_id )) return $avatar;

    $default = get_the_post_thumbnail( $profile_id );

    $avatar = "<img alt='{$safe_alt}' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";

    return $avatar;

}

/**

 * set default user roles for social login

 *

 *@author Tambh

 */

add_filter( 'ae_social_login_user_roles_default', 'fre_default_user_roles' );

if( !function_exists( 'fre_default_user_roles' ) ){

    function fre_default_user_roles( $default_role ){

        return array(

            FREELANCER,

            EMPLOYER

            );

    }

}

/**
 * added by Prawez to separate login, signup and forgot password page
 */
add_action( 'init', 'my_custom_script_enqueuer' );

function my_custom_script_enqueuer() {
	if ( ! is_admin() ) {
        wp_register_script( "my_custom_script", get_template_directory_uri().'/js/wow.js', array('jquery') );
	   wp_register_script( "my_custom_script", get_template_directory_uri().'/js/custom.js', array('jquery') );
	   wp_localize_script( 'my_custom_script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

	   wp_enqueue_script( 'jquery' );
	   wp_enqueue_script( 'my_custom_script' );   
	}
}

add_action("wp_ajax_verify_user_phone", "verify_user_phone");
add_action("wp_ajax_nopriv_verify_user_phone", "verify_user_phone");

if (!function_exists('verify_user_phone')) {
	function verify_user_phone() {
		$user_mobile = trim($_POST['mobile']);
		
		$settings 				= get_option('cf7_cognalys_addon');
		$cognalys_app_id 		= $settings['cognalys_app_id'];
		$cognalys_access_token 	= $settings['cognalys_access_token'];
		
		if($user_mobile != '') {
			$mobiles = split(',', $user_mobile);
			$json = "";
			
			foreach($mobiles as $mobile) {
				$url = 'https://www.cognalys.com/api/v1/otp/?app_id=' . $cognalys_app_id . '&access_token=' . $cognalys_access_token . '&mobile=' . $mobile;
				if ($json != "") {
					$json .= ",";
				}
				$json .= file_get_contents($url);
			}
			
			$json = "[" . $json . "]";
			
			echo $json;
			exit();
		}
	}	
}

add_action("wp_ajax_verify_user_otp", "verify_user_otp");
add_action("wp_ajax_nopriv_verify_user_otp", "verify_user_otp");

if (!function_exists('verify_user_otp')) {
	function verify_user_otp() {
	
		$keymatchs = trim($_POST['keymatch']);
		$otps = trim($_REQUEST['otp']);
		
		$settings 				= get_option('cf7_cognalys_addon');
		$cognalys_app_id 		= $settings['cognalys_app_id'];
		$cognalys_access_token 	= $settings['cognalys_access_token'];
		
		if ($keymatchs != "") {
			$keymatchs = split(',', $keymatchs);
			$otps = split(',', $otps);
			$json = "";

			for ($i = 0; $i < count($keymatchs); $i ++) {
				$keymatch = $keymatchs[$i];
				$otp = $otps[$i];
				$url = 'https://www.cognalys.com/api/v1/otp/confirm/?app_id=' . $cognalys_app_id . '&access_token=' . $cognalys_access_token . '&keymatch=' . $keymatch . '&otp=' . $otp;
				if ($json != "") {
					$json .= ",";
				}
				$json .= file_get_contents($url);
			}
			
			$json = "[" . $json . "]";	
			
			echo $json;
		}

		exit();
	}
}
add_action('wpml_custom_language_switcher', 'custom_language_switcher_perssistant');

function contains($str, array $arr)
{
    foreach($arr as $a) {
        if (stripos($a,$str) !== false) return false;
    }
    return true;
}
function custom_language_switcher_perssistant($args)
{
    $current_page_url = $temp_url = $_SERVER["REQUEST_URI"];
    $array = icl_get_languages('skip_missing=0&orderby=id&order=ASC');
    $exclude = array('/profiles/','/projects/','/project/','/author/');
    $isExclude = contains($current_page_url,$exclude);
    echo '<div class="language-selector-wpml-custom">';
    echo '<ul>';
    foreach ($array as $lang) {
//        if ($isExclude){
            if ($lang['language_code'] != ICL_LANGUAGE_CODE && $lang['language_code'] == 'en'){
                $temp_url = '/'.explode('/de/',$current_page_url)[1];
            }
            if ($lang['language_code'] == ICL_LANGUAGE_CODE && ICL_LANGUAGE_CODE != 'en'){
                $temp_url = '/'.ICL_LANGUAGE_CODE.'/'.explode('/de/',$current_page_url)[1];
            }
            if ($lang['language_code'] != ICL_LANGUAGE_CODE && $lang['language_code'] == 'de'){
                $temp_url = '/de' . $current_page_url;
            }
//        }else{
//            $temp_url = $lang['url'];
//        }
        if ($lang['active'] == '1' && $args['EscapeActive']) continue;
        echo '<li>';
        ?>
        <a href="<?php echo $temp_url ?>">
            <img class="<?php if ($lang['missing'] == 1 && $args['MissingTranslate'] && !$isExclude) echo 'missing'; ?>"
                 src="<?php echo $lang['country_flag_url'] ?>" title="<?php echo $lang['native_name'] ?>">
        </a>
        <?php
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
}

add_action("wp_ajax_update_user_mobile", "update_user_mobile");
add_action("wp_ajax_nopriv_update_user_mobile", "update_user_mobile");

if (!function_exists('update_user_mobile')) {
	function update_user_mobile() {
		$user_mobile = trim($_POST['mobile']);
		$user_id 	 = get_current_user_id();
		update_user_meta( $user_id, 'phone', $user_mobile );
		
                $mail = AE_Mailing::get_instance();
                
                $mail->confirmed_phone($user_id);
                
		echo "success";
	}
}

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );
function extra_user_profile_fields( $user ) {
?>
  <h3><?php _e("Extra profile information", "blank"); ?></h3>
  <table class="form-table">
    <tr>
      <th><label for="phone"><?php _e("Phone"); ?></label></th>
      <td>
        <input type="text" name="phone" id="phone" class="regular-text" 
            value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" /><br />
        <span class="description"><?php _e("Please enter your phone."); ?></span>
    </td>
    </tr>
  </table>
<?php
}

add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {
  $saved = false;
  if ( current_user_can( 'edit_user', $user_id ) ) {
    update_user_meta( $user_id, 'phone', $_POST['phone'] );
    $saved = true;
  }
  return true;
}

/**
 * Authorize credit card
 */
add_action("wp_ajax_authorizecard", "authorizecard");
add_action("wp_ajax_nopriv_authorizecard", "authorizecard");

if (!function_exists('authorizecard')) {
    function authorizecard() {
        
        global $wpdb;
        
        #get values
        $strCardType 	= $_POST['strCardType'];
        $strCardNumber 	= $_POST['strCardNumber'];
        $strFirstName 	= $_POST['strFirstName'];
        $strLastName 	= $_POST['strLastName'];
        $strCardMonth 	= $_POST['strCardMonth'];
        $strCardYear 	= $_POST['strCardYear'];
        $strCardCVV 	= $_POST['strCardCVV'];
        $strStreet       = $_POST['strStreet'];
        $strCity        = $_POST['strCity'];
        $strState       = $_POST['strState'];
        $strCountry     = $_POST['strCountry'];
        $strZip         = $_POST['strZipCode'];
        $strDefaultPayment = $_POST['strDefaultPayment'];
        $strExpDate     = $strCardMonth.$strCardYear;
        $strIPAddress   = $_SERVER['REMOTE_ADDR'];  
        
        if ($strDefaultPayment) {
            $strDefaultPayment = '1';
        } else {
            $strDefaultPayment = '0';
        }
        
        $api = ae_get_option('escrow_paypal_api');       

        $api_username = isset($api['username']) ? $api['username'] : 'dinhle1987-biz_api1.yahoo.com';

        $api_password = isset($api['password']) ? $api['password'] : '1362804968';

        $api_signature = isset($api['signature']) ? $api['signature'] : 'A6LFoneN6dpKOQkj2auJBwoVZBiLAE-QivfFWXkjxrvJZ6McADtMu8Pe';

        $appID = isset($api['appID']) ? $api['appID'] : 'APP-80W284485P519543T';
        
        $testmode = ae_get_option('test_mode');

        $endpoint = 'https://api-3t.paypal.com/nvp';

        // live mod is on

        if ($testmode) {

            $endpoint = 'https://api-3t.sandbox.paypal.com/nvp';

        }
        
        $currency = ae_get_option('content_currency');

        $currency = $currency['code'];
        
        $strPostField = 'USER='.$api_username.'&PWD='.$api_password.'&SIGNATURE='.$api_signature.'&METHOD=DoDirectPayment&PAYMENTACTION=Authorization&IPADDRESS='.$strIPAddress.'&VERSION=86&AMT=0.00&ACCT='.$strCardNumber.'&CREDITCARDTYPE='.$strCardType.'&CVV2='.$strCardCVV.'&FIRSTNAME='.$strFirstName.'&LASTNAME='.$strLastName.'&STREET='.$strStreet.'&CITY='.$strCity.'&STATE='.$strState.'&ZIP='.$strZip.'&COUNTRYCODE='.$strCountry.'&CURRENCYCODE='.$currency.'&EXPDATE='.$strExpDate;
        
        //Get length of post
        $postlength = strlen($strPostField);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL,$endpoint);
        curl_setopt($ch,CURLOPT_POST,$postlength);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$strPostField);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        //close connection
        curl_close($ch);
        
        if (!$response) {
          $status = "fail";
          $msg = "Credit card information is not valids."; 
        } else {
                       
            $httpParsedResponseAr = array();
            foreach ($httpResponseAr as $i => $value) {
                $tmpAr = explode("=", $value);
                if(sizeof($tmpAr) > 1) {
                    $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
                }
            }
            
            $httpParsedResponseAr['ACK'] = 'Success';
            if($httpParsedResponseAr['ACK'] == 'Success') {
                $status = "success";
                $msg = "You have successfully verified you credit card.";      
                
                #update credit card information
                $user_id        = get_current_user_id();
                $strCCNo        = cc_masking($strCardNumber);
                $strRefTransNo  = $httpParsedResponseAr['TRANSACTIONID'];
                $isActive       = $strDefaultPayment;
                $strAddedDate   = date('Y-m-d H:i:s');
                
                $dataArr = array(
                    'user_id' => $user_id,
                    'mask_cc' => $strCCNo,
                    'transaction_ref_no' => $strRefTransNo,
                    'date_time' => $strAddedDate
                );
                
                #insert information to database
                $wpdb->insert( 'wp_user_cc_info', $dataArr );
                $intCCId = $wpdb->insert_id; 
                
                #update user default payment option of users
                #1=>PayPal, 2=>CC
                if ($isActive) {
                    update_user_meta( $user_id, 'default_payment_option', 2 );
                    update_user_meta( $user_id, 'active_cc', $intCCId );     
                }
            } else {
                $status = "fail";
                $msg = "Credit card information is not valids."; 
            }
        }
       
        echo json_encode(array('status' => $status, 'msg' => $msg)); exit;
    }
}

/**
 * Mask credit card function
 */
function cc_masking($number) {
    return str_repeat("X", strlen($number) - 3) . substr($number, -4);
}

/**
 * Save VA bank account details
 */
add_action("wp_ajax_savebankdetails", "savebankdetails");
add_action("wp_ajax_nopriv_savebankdetails", "savebankdetails");

if (!function_exists('savebankdetails')) {
    function savebankdetails() {
        
        #get values
        $strAccType             = $_POST['strAccType'];
        $strBankName            = $_POST['strBankName'];
        $strBankCountry 	= $_POST['strBankCountry'];
        $strRoutingNo           = $_POST['strRoutingNo'];
        $strBankAdd             = $_POST['strBankAdd'];
        $strBankCity            = $_POST['strBankCity'];
        $strBankState           = $_POST['strBankState'];
        $strBankZip             = $_POST['strBankZip'];
        $strAccHolderName       = $_POST['strAccHolderName'];
        $strAccHolderCurr       = $_POST['strAccHolderCurr'];
        $strAccNo               = $_POST['strAccNo'];
        $strAccHolderAdd        = $_POST['strAccHolderAdd'];
        $strAccHolderCity       = $_POST['strAccHolderCity'];
        $strAccHolderCountry    = $_POST['strAccHolderCountry'];
        $strAccHolderState      = $_POST['strAccHolderState'];
        $strAccHolderZipCode    = $_POST['strAccHolderZipCode'];
        $user_id                = get_current_user_id();
        
        #update user meta
        update_user_meta( $user_id, 'account_type', $strAccType );
        update_user_meta( $user_id, 'bank_name', $strBankName );
        update_user_meta( $user_id, 'bank_country', $strBankCountry );
        update_user_meta( $user_id, 'routing_no', $strRoutingNo );
        update_user_meta( $user_id, 'bank_address', $strBankAdd );
        update_user_meta( $user_id, 'bank_city', $strBankCity );
        update_user_meta( $user_id, 'bank_state', $strBankState );
        update_user_meta( $user_id, 'bank_zipcode', $strBankZip );
        update_user_meta( $user_id, 'account_holder_currency', $strAccHolderCurr );
        update_user_meta( $user_id, 'account_holder_name', $strAccHolderName );
        update_user_meta( $user_id, 'account_number', $strAccNo );
        update_user_meta( $user_id, 'account_holder_address', $strAccHolderAdd );
        update_user_meta( $user_id, 'account_holder_city', $strAccHolderCity );
        update_user_meta( $user_id, 'account_holder_country', $strAccHolderCountry );
        update_user_meta( $user_id, 'account_holder_state', $strAccHolderState );
        update_user_meta( $user_id, 'account_holder_zipcode', $strAccHolderZipCode );
        
        #return resonse
        $arrRes = array('status' => 'success', 'msg' => 'Yor bank details has been saved successfully');
        
        echo json_encode($arrRes); exit;
    }
}

/* Get Current User Role */
/* function get_current_user_role() {
	global $wp_roles;
	$current_user = wp_get_current_user();
	$roles = $current_user->roles;
	$role = array_shift($roles);
	return isset($wp_roles->role_names[$role]) ? translate_user_role($wp_roles->role_names[$role] ) : false;
} */

function css_employer_freelancer(){
	global $wp_roles;
	$current_user = wp_get_current_user();
	$roles = $current_user->roles;
	$role = array_shift($roles);
	
	$css = '<style>';
	if($role == 'employer'){
		$css .= ".for_freelancer_only { display: none !important;}";
	}else{
		$css .= ".for_freelancer_only { display: inherit !important;}";
	}
	$css .= '</style>';
	echo $css;
}

/* Cv upload option */
add_action('ae_edit_post_form', 'add_cv_upload', 80);
function add_cv_upload(){
	global $user_ID;
	$cv_url  = get_user_meta($user_ID, 'uploaded_cv', true);
	$fcv_url =str_replace(' ', '/', $cv_url); ?>
	<style>.delete#del_cv{ display: block; position: absolute; height: 20px; width: 20px; float: right; background: rgba(0,0,0,0.2); bottom: 0; padding: 0px 2px; } .delete#del_cv i{ float: left; font-size: 20px; }</style>
	<div class="form-group portfolios-wrapper"> 
		<div class="form-group-control">
			<label><?php _e("Your CV", 'functions') ?></label>
			<div class="edit-portfolio-container">
				<div class="row">
					<ul class="list-item-cv"></ul>
					<div class="col-md-4 col-sm-4 col-xs-4 list-item-portfolio-last add-porfolio-CV-button"  style="position:relative;">
						<?php if($fcv_url != ''){ ?>
							<iframe src="http://docs.google.com/gview?url=<?php echo $fcv_url; ?>&embedded=true" class="cv_preview" id="iframe_cv" style="width:200px; height:200px;" frameborder="0"></iframe>
							<?php $displayDel = 'display: block;'; ?>
						<?php }else{ ?>
							<a href="#" class="add-cv" data-toggle="modal" data-target="#port_modal" style="position:absolute; top:2%;"><i class="fa fa-plus"></i><?php _e("Add your CV", 'functions') ?></a>
							<?php $displayDel = 'display: none;'; ?>

						<?php } ?>
							<a href="<?php echo ($fcv_url!='')?$fcv_url:'#'; ?>" target="_blank" class="preview_cv_link" style="<?php echo $displayDel; ?> position:absolute; top:0%;"><?php _e("Preview", 'functions') ?></a>
							<a href="#" class="delete" id="del_cv" style="<?php echo $displayDel; ?>"><i class="fa fa-trash-o fa-2"></i></a>
							<a href="#" class="add-cv_f" data-toggle="modal" data-target="#port_modal" style="display:none; position:absolute; top:0%;"><i class="fa fa-plus"></i><?php _e("Add your CV", 'functions') ?></a>
					</div>
				</div>
				<div class="error_cv" style="display: none;"></div>
			</div>
		</div>
	</div>
<?php }

add_action("wp_ajax_del_cv", "del_cv");
add_action("wp_ajax_nopriv_del_cv", "del_cv");
function del_cv(){
	global $user_ID;
	
	$meta_key = 'uploaded_cv';
	
	$meta_val= $get_user_meta = get_user_meta($user_ID, $meta_key, true);
	
	if($meta_val !=''){
		
	$homePath = get_home_path();
	
	$homeUrl = site_url();
	
	$filePath = $homePath . str_replace($homeUrl, '', $meta_val);
	
	$unlink= unlink($filePath);
	
		if($unlink){
			$update_meta = update_user_meta( $user_ID, $meta_key, '' );
			$return = ($update_meta)?__("CV Updated Successfully", 'functions'): __("Error Updating Values!", 'functions');
		}else{
			$return = __("Error Deleting File!", 'functions');
		}
	}
	echo $return;
	exit;
}
//__("Completed", 'functions')
add_action("wp_ajax_upLoadCvUser", "upLoadCvUser");
add_action("wp_ajax_nopriv_upLoadCvUser", "upLoadCvUser");

function upLoadCvUser(){

	$user_ID = $_POST['user_id'];
	$userdata = get_userdata( $user_ID );
	$username= $userdata->user_login;
	
	if ( ! function_exists( 'wp_handle_upload' ) ) { require_once( ABSPATH . 'wp-admin/includes/file.php' ); }

	$_FILES['cv_upload']['name'] = $username . '_' . $user_ID . '_' . $_FILES['cv_upload']['name'];
	
	$uploadedfile = $_FILES['cv_upload'];

	$upload_overrides = array( 'test_form' => false );

	$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

	if ( $movefile && !isset( $movefile['error'] ) ) {
		$userCv = $movefile['url'];
		$meta_key = 'uploaded_cv';
		$get_user_meta = get_user_meta($user_ID, $meta_key, true);
		/* ($get_user_meta == '')?add_user_meta( $user_ID, $meta_key, $userCv):update_user_meta( $user_ID, $meta_key, $userCv); */
		update_user_meta( $user_ID, $meta_key, $userCv);
		$output = $userCv;
	}else{
		$output = $movefile['error'];
	}
	echo $output;
	exit;
}

function custom_login_css() {
	echo '<style type="text/css">
        .login-action-login .wp-social-login-widget {display: none;}
    </style>';
}
add_action('login_head', 'custom_login_css');

include 'includes/price_range.php';


require_once('inc/admin-menu-extended.inc.php');

$page_args = array('name' => 'stripe-result-page', 'post_type' => 'page');
$stripe_result_page = get_posts($page_args);
if($stripe_result_page){
	$page_id = $stripe_result_page[0]->ID;
	if(get_post_meta($page_id, '_wp_page_template', true) != 'page-stripe-results.php'){
		update_post_meta($page_id, '_wp_page_template', 'page-stripe-results.php');
	}
} else {
	$new_page_id = wp_insert_post(
		array(
			'comment_status'	=>	'closed',
			'ping_status'		=>	'closed',
			'post_author'		=>	1,
			'post_name'		=>	'stripe-result-page',
			'post_title'		=>	'Stripe Result Page',
			'post_status'		=>	'publish',
			'post_type'		=>	'page'
		)
	);
	update_post_meta($new_page_id, '_wp_page_template', 'page-stripe-results.php');
}

function printStripePaymentForm($project_id, $project_author_id, $bid_id, $price, $project_slug, $string_currency) {

	$stripe_key = ae_get_option('stripe');
	if(isset($stripe_key['publishable_key']) && !empty($stripe_key['publishable_key']) && isset($stripe_key['secret_key']) && !empty($stripe_key['secret_key'])){

		$project_author_email = get_the_author_meta('user_email', $project_author_id);
		$settings_stripe_public_key = $stripe_key['publishable_key'];
		$stripePrice = $price * 100;

		$form = '';
		$form .= '<form action="'.site_url().'/stripe-result-page" class="made_stripe_payment_js" method="post" style="min-width: 130px;">';
		$form .= '<script src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button" data-key="'.$settings_stripe_public_key.'" data-amount="'.$stripePrice.'" data-description="Perssistant payment"></script>';
		$form .= '<input name="stripe_price" value="'.$stripePrice.'" type="hidden" />';
		$form .= '<input name="stripe_currency" value="'.$string_currency.'" type="hidden" />';
		$form .= '<input name="bid_id" value="'.$bid_id.'" type="hidden" />';
		$form .= '<input name="project_author_email" value="'.$project_author_email.'" type="hidden" />';
		$form .= '<input name="project_slug" value="'.$project_slug.'" type="hidden" />';
		$form .= '<input name="project_id" value="'.$project_id.'" type="hidden" />';
		$form .= '</form>';
	  echo $form;
	}
}

add_action("wp_ajax_ajax_logout", "ajax_logout");
add_action("wp_ajax_nopriv_ajax_logout", "ajax_logout");
function ajax_logout(){
    $response = array();
    wp_clear_auth_cookie();
    wp_logout();
    $response['status'] = true;
    $response['redirect'] = site_url().ae_current_lang();
    $response['msg'] = __("Logout success!", 'functions');
    wp_send_json($response);
}
