<?php
class Fre_Message extends AE_Comments
{
    public static $instance;
    
    /**
     * return class $instance
     */
    public static function get_instance() {
        if (self::$instance == null) {
            
            self::$instance = new Fre_Message();
        }
        return self::$instance;
    }
    
    public function __construct() {
        $this->comment_type = 'message';
        $this->meta = array();
        
        $this->post_arr = array();
        $this->author_arr = array();
        
        $this->duplicate = true;
        $this->limit_time = '';
    }
    
    function convert($comment, $thumb = 'thumbnail', $merge_post = false, $merge_author = false) {
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        /**
         * add comment meta
         */
        if (!empty($this->meta)) {
            foreach ($this->meta as $key => $value) {
                $comment->$value = get_comment_meta($comment->comment_ID, $value, true);
            }
        }
        
        $comment->comment_content = wpautop(esc_attr($comment->comment_content));
        
        // comment link
        $comment->comment_link = get_permalink( $comment->comment_post_ID );

        $comment->ID = $comment->comment_ID;
        $comment->id = $comment->comment_ID;
        $comment->avatar = get_avatar($comment->user_id, '33');

        unset($comment->comment_author_email);
        
        $comment->message_time = sprintf(__('on %s', ET_DOMAIN) , get_comment_date($date_format, $comment)) . '&nbsp;' . sprintf(__('at %s', ET_DOMAIN) , get_comment_date($time_format, $comment));

        $file_arr = get_comment_meta( $comment->comment_ID, 'fre_comment_file', true );
        $comment->file_list = '';
        if(!empty($file_arr)) {
            $attachment = get_posts(array('post_type' => 'attachment', 'post__in' => $file_arr));
            ob_start();
            echo '<ul class="list-file-attack">';
            foreach ($attachment as $key => $file) {
                echo '<li><a target="_blank" href="'.$file->guid.'" class="attack-file"><i class="fa fa-paperclip"></i> '.$file->post_title.'</a></li>';
            }
            echo '</ul>';
            $message_file = ob_get_clean();
            $comment->file_list = $message_file;
        }        
        
        return $comment;
    }
}

class Fre_MessageAction extends AE_Base
{
    function __construct() {
        
        // send message
        $this->add_ajax('ae-sync-message', 'sendMessage');
        
        // get older message
        $this->add_ajax('ae-fetch-messages', 'fetchMessage');

        $this->add_action( 'template_redirect', 'preventAccessWorkspace' );

        $this->add_action( 'after_sidebar_single_project', 'addWorkSpaceLink' );
        
        // init message object
        $this->comment = Fre_Message::get_instance();
    }
    
    /**
     * ajax callback sync message
     * $request ajax from client with comment_post_ID, comment_content
     */
    function sendMessage() {
        global $user_ID;
        
        // check projec id is associate with current user
        $args = array();
        /**
         * validate data
         */
        if (empty($_REQUEST['comment_post_ID'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Error! Can not specify which project you are working on.", ET_DOMAIN)
            ));
        }
        
        if (empty($_REQUEST['comment_content'])) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("You cannot send an empty message.", ET_DOMAIN)
            ));
        }
        
        if (!$user_ID) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("You have to login.", ET_DOMAIN)
            ));
        }
        
        $comment_post_ID = $_REQUEST['comment_post_ID'];
        
        // check project owner
        $project = get_post($comment_post_ID);
        
        // check freelancer was accepted on project
        $bid_id = get_post_meta($comment_post_ID, "accepted", true);
        $bid = get_post($bid_id);
        
        // current user is not project owner, or working on
        if ($user_ID != $project->post_author && $user_ID != $bid->post_author) {
            wp_send_json(array(
                'success' => false,
                'msg' => __("You are not working on this project.", ET_DOMAIN)
            ));
        }
        
        /**
         * set message data
         */
        $_REQUEST['comment_approved'] = 1;
        $_REQUEST['type'] = 'message';
        
        $comment = $this->comment->insert($_REQUEST);
        
        if (!is_wp_error($comment)) {
            
            // get comment data
            $comment = get_comment($comment);
            if(isset($_REQUEST['fileID'])) {
                $file_arr = array();
                foreach ((array)$_REQUEST['fileID'] as $key => $file) {
                    $file_arr[] = $file['attach_id'];
                }
                update_comment_meta( $comment->comment_ID, 'fre_comment_file', $file_arr );
            }            
            /**
             * fire an action fre_send_message after send message
             * @param object $comment
             * @param object $project
             * @param object $bid
             * @author Dakachi
             */
            do_action('fre_send_message', $comment, $project, $bid);
            
            // send json data to  client
            wp_send_json(array(
                'success' => true,
                'data' => $this->comment->convert($comment)
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'msg' => $comment->get_error_message()
            ));
        }
    }
    
    /**
     * ajax callback get message collection
     * @author Dakachi
     */
    function fetchMessage() {
        global $user_ID;
        $review_object = $this->comment;
        
        // get review object
        
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 2;
        $query = $_REQUEST['query'];
        
        $map = array(
            'status' => 'approve',
            'type' => 'message',
            'number' => '4',
            'total' => '10',
            'order' => 'DESC',
            'orderby' => 'date'
        );
        
        $query['page'] = $page;
        
        //add_filter( 'comments_clauses' , array($this, 'groupby') );
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
     * prevent user access workspace  
     * @since 1.2
     * @author Dakachi
     */
    function preventAccessWorkspace(){
        if( isset($_REQUEST['workspace']) && $_REQUEST['workspace'] &&  !current_user_can( 'manage_options' ) ) {
            if(is_singular( PROJECT )){
                global $post, $user_ID;
                // check project owner
                $project = $post;
                
                // check freelancer was accepted on project
                $bid_id = get_post_meta($project->ID, "accepted", true);
                $bid = get_post($bid_id);
                // current user is not project owner, or working on
                if (!$bid_id || ($user_ID != $project->post_author && $user_ID != $bid->post_author) ) {
                    wp_redirect( get_permalink( $post->ID ) );
                    exit;
                }
            }
        }
    }

    function addWorkSpaceLink($project){
        $permission = fre_access_workspace($project);
        $project_link = get_permalink( $project->ID );
        if($permission){
            echo '<a style="font-weight:600;" href='.add_query_arg(array('workspace' => 1), $project_link).'>'.__("Open Workspace", ET_DOMAIN).' <i class="fa fa-arrow-right"></i></a>';
        }
    }

}

new Fre_MessageAction();
/**
 * function check user can access project workspace or not 
 * @param object $project The project user want to access workspace
 * @since 1.2
 * @author Dakachi
 */
function fre_access_workspace($project){
    global $user_ID;
    // check freelancer was accepted on project
    $bid_id = get_post_meta($project->ID, "accepted", true);
    $bid = get_post($bid_id);
    
    // current user is not project owner, or working on
    if (!$bid_id || ($user_ID != $project->post_author && $user_ID != $bid->post_author) ) {
        return false;
    }
    return true;
}