<?php

/**
 * project review class
 */
class Fre_Review extends AE_Comments
{
    static $current_review;
    static $instance;
    
    /**
     * return class $instance
     */
    public static function get_instance($type = "em_review") {
        if (self::$instance == null) {
            
            self::$instance = new Fre_Review($type);
        }
        return self::$instance;
    }
    
    public function __construct($type = "em_review") {
        $this->comment_type = $type;
        $this->meta = array(
            'et_rate'
        );
        
        $this->post_arr = array();
        $this->author_arr = array();
        
        $this->duplicate = true;
        $this->limit_time = 120;
    }

    /**
     * The function retrieve employer rating score and review count 
     * @param Integer $employer_id The employer id
     * @since 1.4.1
     * @author Dakachi
     */
    public static function employer_rating_score($employer_id) {
        global $wpdb;
        $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                FROM $wpdb->posts as  p 
                    join $wpdb->comments as C 
                                ON p.ID = c.comment_post_ID 
                    join $wpdb->commentmeta as M 
                        ON C.comment_ID = M.comment_id
                WHERE 
                    p.post_author = $employer_id
                    AND p.post_status ='complete'
                    AND p.post_type ='" . PROJECT . "'                            
                    AND M.meta_key = 'et_rate'
                    AND C.comment_type ='fr_review'
                    AND C.comment_approved = 1";
        
        $results = $wpdb->get_results($sql);
        if($results) {
            return array('rating_score' => $results[0]->rate_point , 'review_count' => $results[0]->count );
        }else {
            return array('rating_score' => 0 , 'review_count' => 0 );           
        }
    }

    /**
     * The function retrieve freelancer rating score and review count 
     * @param Integer $freelancer_id The freelancer id
     * @since 1.4.1
     * @author Dakachi
     */
    public static function freelancer_rating_score($freelancer_id){
        global $wpdb;
        $bid = BID;
        $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(p.ID) as count
                from $wpdb->posts as  p                
                    join $wpdb->postmeta as M 
                        on M.post_id = p.ID
                Where p.post_author = $freelancer_id
                        and p.post_status ='complete'
                        and p.post_type = '" . BID . "'                         
                        and M.meta_key = 'rating_score'";                      
        $results = $wpdb->get_results($sql);
        if($results) {
            // update user meta
            update_user_meta( $freelancer_id, 'rating_score', $results[0]->rate_point);
            // return value
            return array('rating_score' => $results[0]->rate_point , 'review_count' => $results[0]->count );
        }else {
            return array('rating_score' => 0 , 'review_count' => 0 );           
        }
    }

}

/**
 * The class control all action related to freelancer and employer review
 * @since 1.0
 * @category FreelanceEngine
 * @version 1.0
 * @author Dakachi
 */
class Fre_ReviewAction extends AE_Base
{
    
    public function __construct() {
        
        $this->mail = Fre_Mailing::get_instance();
        
        //$this->add_action('preprocess_comment', 'process_review');
        
        // $this->add_action( 'comment_post' , 'update_rating');
        $this->init_ajax();
        
        /*
         * display review form for freelancer when project completed.
        */
        $this->add_action('wp_footer', 'fre_freelancer_review_form', 99);
    }
    
    /**
     * init ajax action 
     * @since 1.0
     * @author Dakachi
     */
    function init_ajax() {
        
        //$this->add_ajax('ae-fetch-comments', 'fetch_comments', true, true);
        $this->add_ajax('ae-employer-review', 'employer_review_action', true, false);
        $this->add_ajax('ae-freelancer-review', 'freelancer_review_action', true, false);
    }
    
    /**
     * create a review by employer for a freelancer
     */
    function employer_review_action() {
        global $user_ID, $current_user;
        $args = $_POST;
        
        if (!isset($args['project_id'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __('Invalid project id.', ET_DOMAIN)
            ));
        }
        
        $project_id = $args['project_id'];
        
        $author_id = (int)get_post_field('post_author', $project_id);
        
        $result = array(
            'succes' => false,
            'msg' => __('You can\'t not access this action.', ET_DOMAIN)
        );
        
        $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
        
        $author_bid = get_post_field('post_author', $bid_id_accepted);
        
        $profile_id = get_user_meta($author_bid, 'user_profile_id', true);
        
        /*
         * validate data
        */
        if (!$bid_id_accepted) {
            $result = array(
                'succes' => false,
                'msg' => __('Please assign project before complete.', ET_DOMAIN)
            );
            wp_send_json($result);
        }
        
        if (!isset($args['score']) || empty($args['score'])) {
            $result = array(
                'succes' => false,
                'msg' => __('You have to rate for this profile.', ET_DOMAIN)
            );
            wp_send_json($result);
        }
        if (!isset($args['comment_content']) || empty($args['comment_content'])) {
            $result = array(
                'succes' => false,
                'msg' => __('Please post a review for this freelancer.', ET_DOMAIN)
            );
            wp_send_json($result);
        }
        
        /*
         * check permission for review action
        */
        
        if (!$user_ID || $user_ID !== $author_id) wp_send_json($result);
        
        $args['comment_post_ID'] = $bid_id_accepted;
        $args['comment_approved'] = 1;
        
        // insert review
        $review = Fre_Review::get_instance();
        $comment = $review->insert($args);
        
        if (!is_wp_error($comment)) {
            
            /**
             * fire an acction after project owner complete his project
             * @param int $project_id
             * @param Array $args
             * @since v1.2
             * @author Dakachi
             */
            do_action('fre_complete_project', $project_id, $args);
            
            /**
             * update project, bid, user rating scrore after review a project
             */
            $this->update_after_empoyer_review($project_id, $comment);
            
            $project_title = get_the_title($project_id);
            $freelancer_name = get_the_author_meta('display_name', $author_bid);
            wp_send_json(array(
                'success' => true,
                'msg' => sprintf(__("You have completed project %s and reviewed %s.", ET_DOMAIN) , $project_title, $freelancer_name)
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => $comment->get_error_message()
            ));
        }
    }
    
    /*
     * add review by freelancer.
    */
    function freelancer_review_action() {
        global $user_ID;
        $args = $_POST;
        $project_id = $args['project_id'];
        
        $status = get_post_status($project_id);
        
        $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
        
        $author_bid = (int)get_post_field('post_author', $bid_id_accepted);
        
        $freelancer_id = get_post_field('post_author', $bid_id_accepted);
        
        /*
         * validate data
        */
        if (!isset($args['score']) || empty($args['score'])) {
            $result = array(
                'succes' => false,
                'msg' => __('You have to rate this project.', ET_DOMAIN)
            );
            wp_send_json($result);
        }
        if (!isset($args['comment_content']) || empty($args['comment_content'])) {
            $result = array(
                'succes' => false,
                'msg' => __('Please post a review for this freelancer.', ET_DOMAIN)
            );
            wp_send_json($result);
        }
        
        /*
         * check permission
        */
        if ($user_ID !== $author_bid || !$user_ID) {
            wp_send_json(array(
                'succes' => false,
                'msg' => __('You don\'t have permission to review.', ET_DOMAIN)
            ));
        }
        
        /*
         *  check status of project
        */
        if ($status !== 'complete') {
            wp_send_json(array(
                'succes' => false,
                'msg' => __('You can\'t not reivew on this project.', ET_DOMAIN)
            ));
        }
        
        /**
         * check user reviewed project owner or not
         * @author Dan
         */
        $role = ae_user_role($user_ID);
        $type = 'em_review';
        if ($role == FREELANCER) {
            $type = 'fre_review';
        }
        
        $comment = get_comments(array(
            'status' => 'approve',
            'type' => $type,
            'post_id' => $project_id
        ));
        
        if (!empty($comment)) {
            wp_send_json(array(
                'succes' => false,
                'msg' => __('You have reviewed on this project.', ET_DOMAIN)
            ));
        }
        
        // end check user review project owner
        
        // add review
        $args['comment_post_ID'] = $project_id;
        $args['comment_approved'] = 1;
        $this->comment_type = 'fre_review';
        $review = Fre_Review::get_instance("fre_review");
        
        $comment = $review->insert($args);
        
        if (!is_wp_error($comment)) {
            
            /**
             * fire action after freelancer review employer base on project
             * @param int $int project id
             * @param Array $args submit args (rating score, comment)
             * @since 1.2
             * @author Dakachi
             */
            do_action('fre_freelancer_review_employer', $project_id, $args);
            
            //update project, bid, bid author, project author after review
            $this->update_after_fre_review($project_id, $comment);
            wp_send_json(array(
                'success' => true,
                'msg' => __("Your review has been submitted.", ET_DOMAIN)
            ));
        } else {
            
            // revert bid status
            wp_update_post(array(
                'ID' => $bid_id_accepted,
                'post_status' => 'publish'
            ));
            
            wp_send_json(array(
                'success' => false,
                'msg' => $comment->get_error_message()
            ));
        }
    }
    
    /*
     * update profile and project after employer complete project and review a bid.
    */
    function update_after_empoyer_review($project_id, $comment_id) {
        global $wpdb;
        
        $rate = 0;
        
        $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
        
        $freelancer_id = get_post_field('post_author', $bid_id_accepted);
        
        $profile_id = get_user_meta($freelancer_id, 'user_profile_id', true);
        
        //update status for project
        wp_update_post(array(
            'ID' => $project_id,
            'post_status' => 'complete'
        ));
        
        //update rate for profile
        wp_update_post(array(
            'ID' => $bid_id_accepted,
            'post_status' => 'complete'
        ));
        
        if (isset($_POST['score']) && $_POST['score']) {
            $rate = (int)$_POST['score'];
            if ($rate > 5) $rate = 5;
            update_comment_meta($comment_id, 'et_rate', $rate);
            update_post_meta($bid_id_accepted, 'rating_score', $rate);
        }
        
        $sql = "select AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                from $wpdb->posts as  p               
                Join $wpdb->comments as C 
                    on p.ID = C.comment_post_ID 
                        join $wpdb->commentmeta as M 
                            on C.comment_ID = M.comment_id
                            Where p.post_author = $freelancer_id
                            and p.post_status ='complete'
                            and p.post_type ='" . BID . "'                           
                            and M.meta_key = 'et_rate'
                            and C.comment_type ='em_review'
                            and C.comment_approved = 1 ";
        
        $results = $wpdb->get_results($sql);
        
        // update post rating score
        if ($results) {
            wp_cache_set("reviews-{$freelancer_id}", $results[0]->count);
            update_post_meta($profile_id, 'rating_score', $results[0]->rate_point);
        } else {
            update_post_meta($profile_id, 'rating_score', $rate);
        }
        
        // send mail to freelancer.
        $this->mail->review_freelancer_email($project_id);
    }
    
    /*
     * action after freelancer review project .
    */
    
    function update_after_fre_review($project_id, $comment_id) {
        global $wpdb;
        if (isset($_POST['score']) && $_POST['score']) {
            $rate = (int)$_POST['score'];
            if ($rate > 5) $rate = 5;
            update_comment_meta($comment_id, 'et_rate', $rate);
            update_post_meta($project_id, 'rating_score', $rate);
        }
        $employer_id = (int)get_post_field('post_author', $project_id);
        $profile_id = get_user_meta($employer_id, 'user_profile_id', true);
        $sql = "SELECT AVG(M.meta_value)  as rate_point, COUNT(C.comment_ID) as count
                FROM $wpdb->posts as  p 
                    join $wpdb->comments as C 
                                ON p.ID = C.comment_post_ID 
                    join $wpdb->commentmeta as M 
                        ON C.comment_ID = M.comment_id
                WHERE 
                    p.post_author = $employer_id
                    AND p.post_status ='complete'
                    AND p.post_type ='" . PROJECT . "'                            
                    AND M.meta_key = 'et_rate'
                    AND C.comment_type ='fr_review'
                    AND C.comment_approved = 1";
        
        $results = $wpdb->get_results($sql);
        
        if ($results) {
            wp_cache_set("reviews-{$employer_id}", $results[0]->count);
            
            // update post rating score
            update_post_meta($profile_id, 'rating_score', $results[0]->rate_point);
        } else {
            update_post_meta($profile_id, 'rating_score', $rate);
        }

        // send mail to employer.
        $this->mail->review_employer_email($project_id);
    }
    
    /**
     * fetch comment
     */
    function fetch_comments() {
        
        global $ae_post_factory;
        $review_object = $ae_post_factory->get('de_review');
        
        // get review object
        
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 2;
        $query = $_REQUEST['query'];
        
        $map = array(
            'status' => 'approve',
            'meta_key' => 'et_rate',
            'type' => 'review',
            'post_type' => 'place',
            'number' => '4',
            'total' => '10'
        );
        
        $query['page'] = $page;
        
        $data = $review_object->fetch($query);
        if (!empty($data)) {
            $data['success'] = true;
            wp_send_json($data);
        } else {
            wp_send_json(array(
                'success' => false,
                'data' => $data
            ));
        }
    }
    
    /**
     * display form for freelancer review employer  after complete project.
     * @since  1.0
     * @author Dan
     */
    function fre_freelancer_review_form() {
        wp_reset_query();
        global $user_ID;
        $status = get_post_status(get_the_ID());
        $bid_accepted = get_post_field('accepted', get_the_ID());
        $freelan_id = (int)get_post_field('post_author', $bid_accepted);
        $comment = get_comments(array(
            'status' => 'approve',
            'post_id' => get_the_ID() ,
            'type' => 'fre_review'
        ));
        $review = isset($_GET['review']) ? (int)$_GET['review'] : 0;
        $status = get_post_status(get_the_ID());
        
        if (empty($comment) && $user_ID == $freelan_id && $review && $status == 'complete') { ?>
            <script type="text/javascript">
            (function($, Views, Models, Collections) {
                $(document).ready(function(){
                    this.modal_review       = new AE.Views.Modal_Review();
                    this.modal_review.openModal();
                });
            })(jQuery, AE.Views, AE.Models, AE.Collections);
            </script>

            <?php
        }
    }
}


/**
 * Retrieve total review for employer or freelancer
 *
 *
 * @param int $user_id required. User ID.
 * @return object review stats.
 */
function fre_count_reviews($user_id = 0) {
    
    global $wpdb;
    
    $user_id = (int)$user_id;
    $role = ae_user_role($user_id);
    $count = wp_cache_get("reviews-{$user_id}");
    
    if (false !== $count) return $count;
    
    $sql = '';
    if ($role != 'freelancer') {
        $sql = "SELECT distinct  COUNT(C.comment_ID) as count
                    from $wpdb->posts as  p                
                    Join $wpdb->comments as C 
                        on p.ID = C.comment_post_ID
                        where p.post_author = $user_id                       
                              and p.post_status ='complete'
                              and p.post_type ='" . PROJECT . "'
                              and C.comment_type ='fre_review'
                              and C.comment_approved = 1 ";
    } elseif ($role == 'freelancer') {
        $sql = "SELECT COUNT(C.comment_ID) as count
                from $wpdb->posts as  p                
                left join $wpdb->comments as C 
                    on p.ID = C.comment_post_ID 
                where   p.post_status ='complete'
                        and p.post_author = $user_id
                        and p.post_type ='" . BID . "'
                        and C.comment_type ='em_review'
                        and C.comment_approved = 1 ";
    }

    $result = $wpdb->get_results($sql);    
    if ($result) {
        $count = $result[0]->count;
        
        // $count = $wpdb->get_var($sql);
        
        
    } else {
        $count = 0;
    }
    wp_cache_set("reviews-{$user_id}", $count);
    return (int)$count;
}
?>