<?php

function fre_register_bid() {

    

    // list args for bi post_type

    $bid_labels = array(

        'name' => __('Bids', 'bids-backend') ,

        'singular_name' => __('Bid', 'bids-backend') ,

        'add_new' => _x('Add New Bid', 'bids-backend', 'bids-backend') ,

        'add_new_item' => __('Add New Bid', 'bids-backend') ,

        'edit_item' => __('Edit Bid', 'bids-backend') ,

        'new_item' => __('New bid', 'bids-backend') ,

        'view_item' => __('View bid', 'bids-backend') ,

        'search_items' => __('Search Bids', 'bids-backend') ,

        'not_found' => __('No Bids found', 'bids-backend') ,

        'not_found_in_trash' => __('No Bids found in Trash', 'bids-backend') ,

        'parent_item_colon' => __('Parent bid:', 'bids-backend') ,

        'menu_name' => __('Bids', 'bids-backend') ,

    );

    

    $bid_args = array(

        'labels' => $bid_labels,

        'hierarchical' => false,

        'public' => true,

        'show_ui' => true,

        'show_in_menu' => true,

        'show_in_admin_bar' => true,

        'menu_position' => 10,

        'show_in_nav_menus' => true,

        'publicly_queryable' => true,

        'exclude_from_search' => false,

        'has_archive' => true,

        'query_var' => false,

        'can_export' => true,

        'rewrite' => array(

            'slug' => 'bid'

        ) ,

        

        // need fix to global

        'capability_type' => 'post',

        'supports' => array(

            'title',

            'editor',

            'author',

            'excerpt',

            'custom-fields',

            'page-attributes',

            'comments',

        )

    );

    register_post_type(BID, $bid_args);

    

    // need fix to global.

    global $ae_post_factory;

    $ae_post_factory->set(BID, new AE_Posts(BID, array() , array(

        

        // price enter when bid a project

        'bid_budget',
//        'bid_budget_per_hour',

        'dealine',

        'decide_later',

        // bid status set to 1 if project owner accept bid

        'accepted',

        

        // time dealine

        'bid_time',

        

        // time type (day,week,hour)

        'type_time'

    )));

}

add_action('init', 'fre_register_bid', 11);



/**

 * class control all action related to a bid object

 * @author Dan

 */

class Fre_BidAction extends AE_PostAction

{

    public static $instance;

    public static function get_instance() {

        if (self::$instance == null) {

            self::$instance = new Fre_BidAction();

        }

        return self::$instance;

    }

    public function __construct($post_type = BID) {

        

        // init mail instance to send mail

        $this->mail = Fre_Mailing::get_instance();

        

        $this->post_type = $post_type;

        

        /* add more data when convert a bid */

        $this->add_filter('ae_convert_bid', 'ae_convert_bid');

        

        // sync to update bid

        $this->add_ajax('ae-sync-bid', 'bid_sync');

        $this->add_ajax('ae-bid-sync', 'bid_sync');

        

        /* accept a bid */

        $this->add_ajax('ae-accept-bid', 'bid_accept');
        $this->add_ajax('ae-skip-bid', 'bid_skip');

        

        // request list bid

        $this->add_ajax('ae-fetch-bid', 'fetch_post');

        

        /*

         * check permission before insert bid.

        */

        $this->add_filter('ae_pre_insert_bid', 'fre_check_before_insert_bid', 12, 1);

        

        /*

         * update project and bid after bid success.

        */

        $this->add_action('ae_insert_bid', 'fre_update_after_bidding', 12, 1);

        

        /*

         * aftion after delete a bid

         * use this action insteated the action trashed_post

        */

        $this->add_action('et_delete_bid', 'fre_delete_bid', 12, 1);

        

        //$this->add_action('trashed_post','fre_delete_bid');

        

        /*

         * Filter for post title in back-end

        */

        $this->add_filter('the_title', 'the_title_bid', 10, 2);

        

        /*

         * Add column project tile in wrodpress

        */

        $this->add_filter('manage_bid_posts_columns', 'manage_bid_column_project', 1);

        $this->add_action('manage_bid_posts_custom_column', 'project_title_column_render', 2, 10);

        

        self::$instance = $this;

    }

    

    /**

     * Override filter_query_args for bid

     */

    public function filter_query_args($query_args) {

        

        if (isset($_REQUEST['query'])) {

            $query = $_REQUEST['query'];

            if (isset($query['post_parent']) && $query['post_parent'] != '') {

                $query_args['post_parent'] = $query['post_parent'];

            }

            

            // $query_args['post_status'] = array('complete','publish');

            // if(isset($query['is_single'])){

            $query_args['post_status'] = $query['post_status'];

            

            // }

            

            

        }

        return $query_args;

    }

    

    /**

     * convert bid

     */

    

    function ae_convert_bid($result) {

        global $user_ID;

        

        $result->et_avatar = get_avatar($result->post_author, 70);

        $result->author_url = get_author_posts_url($result->post_author);

        $profile_id = get_user_meta($result->post_author, 'user_profile_id', true);

        $result->rating_score = (float)get_post_meta($result->ID, 'rating_score', true);
if (get_post_meta($result->ID, 'comment_employer', true) != 0){
    $result->comment_employer = (int)get_post_meta($result->ID, 'comment_employer', true);
}
        if ((isset($_REQUEST) && isset($_REQUEST['query']) && isset($_REQUEST['query']['is_single'])) || is_singular(PROJECT)) {

            $result->rating_score = (float)get_post_meta($profile_id, 'rating_score', true);

        }

        

        if ($profile_id) {

            $result->et_professional_title = get_post_meta($profile_id, 'et_professional_title', true);

            $result->experience = get_post_meta($profile_id, 'experience', true);

            

            $result->profile_display = get_the_author_meta('display_name', $result->post_author);

        } else {

            $result->et_professional_title = '';

            $result->experience = __('Unknow', 'bids-backend');

            $result->profile_display = get_the_author_meta('display_name', $result->post_author);

        }



        if(!empty($result->type_time)){
            /*new convert*/
            if ($result->type_time == 'day') {

                if ($result->bid_time > 1) {

                    $result->bid_time_text = sprintf(__("in %d days", 'bids-backend') , $result->bid_time);

                } else {

                    $result->bid_time_text = sprintf(__("in %d day", 'bids-backend') , $result->bid_time);

                }

            }elseif ($result->type_time == 'week') {

                if ($result->bid_time > 1) {

                    $result->bid_time_text = sprintf(__("in %d weeks", 'bids-backend') , $result->bid_time);

                } else {

                    $result->bid_time_text =sprintf(__("in %d week", 'bids-backend') , $result->bid_time);

                }
            }else{
                if ($result->bid_time > 1) {

                    $result->bid_time_text = sprintf(__("in %d months", 'bids-backend') , $result->bid_time);

                } else {

                    $result->bid_time_text =sprintf(__("in %d month", 'bids-backend') , $result->bid_time);

                }
            }
        }else{
            /*old convert*/
            if ($result->type_time == 'day') {

                if ($result->bid_time > 1) {

                    $result->bid_time_text = sprintf(__("in %d days", 'bids-backend') , $result->bid_time);

                } else {

                    $result->bid_time_text = sprintf(__("in %d day", 'bids-backend') , $result->bid_time);

                }

            } else {

                if ($result->bid_time > 1) {

                    $result->bid_time_text = sprintf(__("in %d weeks", 'bids-backend') , $result->bid_time);

                } else {

                    $result->bid_time_text =sprintf(__("in %d week", 'bids-backend') , $result->bid_time);

                }

            }
        }


        

        $result->bid_budget_text = fre_price_format($result->bid_budget);

        //var_dump($result->bid_budget);
        

        //convert project infor to bid

        $project_post = get_post($result->post_parent);

        

        /**

         * add project data to bid

         */

        if ($project_post && !is_wp_error($project_post)) {

            $result->accepted = get_post_status($project_post->ID, 'accepted', true);

            $result->project_author_avatar = get_avatar($project_post->post_author, 30);

            $result->project_link = get_permalink($project_post->ID);

            $result->project_title = $project_post->post_title;

            $result->project_status = $project_post->post_status;

            $result->project_id = $project_post->ID;

            $result->project_author = (int)get_post_field('post_author', $project_post->ID);

            

            // check user role and project status to disable bid budget

            if (in_array($result->project_status, array(

                'complete',

                'close',

                'disputing'

            )) || ($user_ID && $user_ID == $project_post->post_author) || ($user_ID && $user_ID == $result->post_author)) {

                

                // dont do any thing

                

                

            } else {

                

                // hide bid text

                $result->bid_budget_text = __("In Progress", 'bids-backend');

                

                // hide bid time

                $result->bid_time_text = '';

                

                // hide bid budget

                $result->bid_budget = '';

            }

            

            if (!current_user_can('manage_options') && !(($user_ID && $user_ID == $project_post->post_author) || ($user_ID && $user_ID == $result->post_author))) {

                $result->post_content = '';

            }

            

            /* get bid review  */

            $result->project_comment = '';

            if ($result->project_status == 'complete') {

                

                // only project complete should have review

                $comment = get_comments(array(

                    'post_id' => $result->ID,

                    'type' => 'em_review'

                ));

                if ($comment) {

                    $result->project_comment = $comment['0']->comment_content;

                }

            }

            

            $result->project_post_date = $project_post->post_date;

            

            //$result->et_budget = $project_post->et_budget;

            $result->et_budget = fre_price_format(get_post_meta($project_post->ID, 'et_budget', true));

            

            // add new fields return @author ThaiNT

            $result->total_bids = $project_post->total_bids ? $project_post->total_bids : 0;

            $result->bid_average = $project_post->bid_average ? $project_post->bid_average : 0;

        } else {

            $result->accepted = '';

            $result->project_author_avatar = '';

            $result->project_link = '';

            $result->project_title = '';

            $result->project_status = '';

            $result->project_post_date = '';

            $result->et_budget = '';

            $result->project_id = '';

            $result->project_author = '';

        }

        $result->is_admin = current_user_can('manage_options') == TRUE ? 1 : 0;

        $result->current_user = $user_ID;

        return $result;

    }

    

    /**

     * bid_sync description

     *  create, udpate , delete a bid.

     * @return

     */

    function bid_sync() {

        global $ae_post_factory, $user_ID;

        $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';

        if ($method == 'remove') {

            $bid_id = $_REQUEST['ID'];

            $project_id = get_post_field('post_parent', $bid_id);

            $accepted = get_post_field('accepted', $project_id);

            if ((int)$accepted == (int)$bid_id) {

                wp_send_json(array(

                    'success' => false,

                    'msg' => __('Error. You have been accepted for this project', 'bids-backend')

                ));

            }

        }

        $post_data = (array)$_REQUEST;

        $title = isset($post_data['post_title']) ? $post_data['post_title'] : ' title biding';
$post_data['post_title'] = $title;
        

        // sync bid

        $bid = $ae_post_factory->get(BID);
//var_dump($bid);
       // var_dump($post_data);

        $result = $bid->sync($post_data);

       // var_dump($result);


        if (is_wp_error($result)) {

            

            // send error to client

            wp_send_json(array(

                'success' => false,

                'msg' => $result->get_error_message()

            ));

        } else {

            $message = __("Update bid successful.", 'bids-backend');

            if ($method == 'create') {

                $message = __("Create bid successful.", 'bids-backend');

            }

            wp_send_json(array(

                'success' => true,

                'msg' => $message

            ));

        }

    }

    function bid_skip() {
        global $current_user;
        $request =  $_POST;
        $bid_id = $request['bid_id'];
        $project_id = get_post_field('post_parent', $bid_id);
        $author_id = (int)get_post($project_id)->post_author;
        if($current_user->id === $author_id){
            do_action('fre_delete_bid', $bid_id);
            wp_delete_post( $bid_id, true );
            wp_send_json(array(
                'success' => true,
                'msg' => __("Decline bid successful.", 'bids-backend')
            ));

        }else{
            wp_send_json(array(
                'success' => false,
                'msg' => __("Error.", 'bids-backend')
            ));
        }

        wp_die();
    }

    /**

     * accept a bid for project

     * @author Dan

     */

    function bid_accept() {

        

        $request = $_POST;

        $bid_id = isset($request['bid_id']) ? $request['bid_id'] : '';

        $result = $this->assign_project($bid_id);

        

        if (!is_wp_error($result)) {

            

            /**

             * fire action fre_accept_bid after accept a bid

             * @param int $bid_id the id of accepted bid

             * @param Array $request

             * @since 1.2

             * @author Dakachi

             */

            do_action('fre_accept_bid', $bid_id);

            

            // send message to client

            wp_send_json(array(

                'success' => true,

                'msg' => __('Bid accepted successfully.', 'bids-backend')

            ));

        }

        

        wp_send_json(array(

            'success' => false,

            'msg' => $result->get_error_message()

        ));

    }

    

    /*

     *check perminsion before a freelancer bidding on a project.

    */

    function fre_check_before_insert_bid($args) {

        global $user_ID;

        /**

         * add filter to filter bid required field

         * @param Array

         * @since 1.4

         * @author Dakachi

         */

        $bid_required_field = apply_filters('fre_bid_required_field', array(

            'decide_later',
            'bid_budget',

            'bid_time',

            'bid_content'

        ));

        

        if (is_wp_error($args)) return $args;

        

        if(in_array('bid_content', $args) && !isset($args['bid_content'])) {

            return new WP_Error('empty_content', __('Please enter your bid message.', 'bids-backend'));

        }
        if(get_user_meta($user_ID,'interview_status',true) == 'unconfirm') {

            return new WP_Error('interview_unconfirm', __('You are not able to bid on the projects, your profile is not activated.', 'bids-backend'));

        }


        $args['post_content'] = $args['bid_content'];

        $project_id = isset($args['post_parent']) ? $args['post_parent'] : '';

        $args['post_status'] = 'publish';

        // $request = $_POST;

        

        /*

         * validate data

        */

        if (in_array('bid_budget', $bid_required_field) && (!isset($args['bid_budget'])) && $args['decide_later'] == false) {

            return new WP_Error('empty_bid', __('You have to set the bid budget.' , 'bids-backend'));

        }

        

        if (in_array('bid_time', $bid_required_field) && (!isset($args['bid_time']) || empty($args['bid_time'])) ) {

            return new WP_Error('empty_time', __('You have to set the time to finish project.', 'bids-backend'));

        }

        

        if ((in_array('bid_budget', $bid_required_field) && $args['bid_budget'] < 0) && $args['decide_later'] == false) {

            return new WP_Error('budget_less_than_zero', __("Your budget have to greater than zero!", 'bids-backend'));

        }

        
        //  || (in_array('bid_time', $bid_required_field) && !is_numeric($args['bid_time']))
        if ((in_array('bid_budget', $bid_required_field) && !is_numeric($args['bid_budget']) ) && $args['decide_later'] == false) {

            return new WP_Error('invalid_input', __('Please enter a valid number in budget or bid time', 'bids-backend'));

        }

        

        if (!$user_ID) return new WP_Error('no_permission', __('Please login to bid a project', 'bids-backend'));

        

        if (get_post_status($project_id) != 'publish') return new WP_Error('invalid_input', __('This project is not publish.', 'bids-backend'));

        

        // $accepted = get_post_meta($project_id,'accepted', true);

        

        // if($accepted || 'complete' ==  get_post_status($project_id) )

        //    return new  WP_Error (200 ,__('The project has been accepted', 'bids-backend'));

        

        if (fre_has_bid($project_id)) return new WP_Error(200, __('You have bid on this project', 'bids-backend'));

        

        $post_author = (int)get_post_field('post_author', $project_id, 'display');

        

        if ($user_ID == $post_author) {

            return new WP_Error(200, __('You can\'t bid on your project', 'bids-backend'));

        }

        

        // check role to bid project

        $role = ae_user_role();

        if (!fre_share_role() && $role != FREELANCER) return new WP_Error(200, __('You have to be a freelancer to bid a project.', 'bids-backend'));

        

        /*

         * check profile has set?

        */

        $profile_id = get_user_meta($user_ID, 'user_profile_id', true);

        $profile = get_post($profile_id);

        

        // user have to complete profile to bid a project

        if (!$profile || !is_numeric($profile_id)) {

            return new WP_Error(200, __('You must complete your profile to bid on a project.', 'bids-backend'));

        }

        

        /* when using escrow, freelancer must setup an paypal account */

        if (ae_get_option('use_escrow')) {

            $paypal_account = get_user_meta($user_ID, 'paypal', true);

            if (!$paypal_account) {

                return new WP_Error('dont_have_paypal', __('You must setup your paypal account in profile to receive money.', 'bids-backend'));

            }

        }


        return $args;

    }

    

    /*

     * update project and bid after have a bid succesfull.

    */

    function fre_update_after_bidding($bid_id) {

        

        if ('publish' != get_post_status($bid_id)) wp_update_post(array(

            'ID' => $bid_id,

            'post_status' => 'publish'

        ));

        

        $project_id = get_post_field('post_parent', $bid_id);

        

        //update avg bids for project

        $total_bids = get_number_bids($project_id);

        $avg = get_post_meta($bid_id, 'bid_average', true);

        if ($total_bids > 0) $avg = get_total_cost_bids($project_id) / $total_bids;

        

        update_post_meta($project_id, 'bid_average', number_format($avg, 2));

        

        update_post_meta($project_id, 'total_bids', $total_bids);

        

        $this->mail->bid_mail($bid_id);

        wp_send_json(array(

            'success' => true,

            'msg' => __('You are bid successful', 'bids-backend')

        ));

    }

    

    /*

     * current force delete = false,

     * remove currenbid one again with force = true

    */

    

    function fre_delete_bid($bid_id) {

        global $wpdb;

        //$wpdb->query( $wpdb->prepare("DELETE FROM $wpdb->posts WHERE post_id = %d AND post_type = %s  ", $bid_id, BID  ) );

        // current bid status = trash.

        $project_id = get_post_field('post_parent', $bid_id);

        $result = $wpdb->get_results("SELECT ID FROM `wp_posts` WHERE `post_content` LIKE '%bid=".$bid_id."%'");

        $bid_budget = (float)get_post_meta($bid_id, 'bid_budget', true);

        

        $total_bids = (int)get_post_meta($project_id, 'total_bids', true) - 1;

        

        $total_bids = max($total_bids, 0);

        


        

        $new_avg = 0;

        if ($total_bids > 0) {

            

            $total_cost_bid = (float)get_total_cost_bids($project_id) / $total_bids;

            $new_avg = $total_cost_bid / $total_bids;

        }

        

        // update avg and total bid;

        update_post_meta($project_id, 'total_bids', $total_bids);

        

        update_post_meta($project_id, 'bid_average', number_format($new_avg, 2));


        wp_delete_post($bid_id, true);
        wp_delete_post($result[0]->ID, true); //delete empty notify

        wp_send_json(array(

            'success' => true,

            'msg' => __('Bid has been deleted successful', 'bids-backend')

        ));

    }

    

    /**

     *Assign a job for a freelancer.

     */

    function assign_project($bid_id) {

        

        global $user_ID;

        $project_id = get_post_field('post_parent', $bid_id);

        $project = get_post($project_id);

        

        $result = new WP_Error($code = '200', $message = __('You don\'t have perminsion to accept this project.', 'bids-backend') , array());

        

        // check authenticate

        if (!$user_ID) return new WP_Error($code = '200', $message == __(' You must login to accept bid.', 'bids-backend'));

        

        if ($project->post_status != 'publish') {

            

            // a project have to published when bidding

            return new WP_Error($code = '200', $message = __('Your project was not pubished. You can not accept a bid!', 'bids-backend'));

        }

        

        if ((int)$project->post_author == $user_ID) {

            

            // add accepted bid id to project meta

            update_post_meta($project->ID, 'accepted', $bid_id);

            

            // change project status to close so mark it to on working

            wp_update_post(array(

                'ID' => $project->ID,

                'post_status' => 'close'

            ));

            

            // change a bid to be accepted

            wp_update_post(array(

                'ID' => $bid_id,

                'post_status' => 'accept'

            ));

            

            /**

             * fire action fre_assign_project after accept a bid

             * @param Object $project the project was assigned

             * @param int $bid_id the id of accepted bid

             * @since 1.2

             * @author Dakachi

             */

            do_action('fre_assign_project', $project, $bid_id);

            

            // send mail to freelancer if he won a project

            $freelancer_id = get_post_field('post_author', $bid_id);

            $this->mail->bid_accepted($freelancer_id, $project->ID);

            

            return true;

        }

        

        return $result;

    }

    

    /*

     * filter title bid in back-end

    */

    function the_title_bid($title, $bid_id = 0) {

        if (is_admin() && is_post_type_archive(BID) && get_post_field('post_type', $bid_id) == 'bid') {

            $post_author = get_post_field('post_author', $bid_id);

            $project_id = get_post_field('post_parent', $bid_id);

            $author = get_the_author_meta('display_name', $post_author);

            

            // change title

            $title = sprintf(__('%s bid for project "%s"', 'bids-backend') , $author, get_the_title($project_id));

        }

        return $title;

    }

    

    function manage_bid_column_project($columns) {

        $column_thumbnail = array(

            'avatar' => 'Avatar'

        );

        $columns = array_slice($columns, 0, 1, true) + $column_thumbnail + array_slice($columns, 1, NULL, true);

        return array_merge($columns, array(

            'project_title' => __('Project', 'bids-backend')

        ));

    }

    

    function project_title_column_render($column, $bid_id) {

        

        if ($column == 'project_title') {

            

            $project_id = get_post_field('post_parent', $bid_id);

            echo '<a href="' . get_permalink($project_id) . '"> ' . get_the_title($project_id) . '</a>';

        }

        

        if ($column == 'avatar') {

            echo get_avatar(get_post_field('post_author', $bid_id) , '50');

        }

    }

}



/*

 * check an user has bid on a project yet?

 * int $project_id,

 * $user_id default NULL

*/

if (!function_exists('fre_has_bid')):

    

    function fre_has_bid($project_id, $user_id = false) {

        global $wpdb;

        if (!$user_id) {

            global $user_ID;

            $user_id = $user_ID;

        }

        $bided = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE post_author = $user_id AND post_type = '" . BID . "'  AND post_parent  = $project_id", ARRAY_N);

        if ($bided) return (float)$bided[0];

        return false;

    }

endif;

