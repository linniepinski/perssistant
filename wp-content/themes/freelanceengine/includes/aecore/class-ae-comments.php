<?php
/**
 * AE_Comment abstract
 */
class AE_Comments
{
    public static $instance = null;
    /**
     * store instance current after converted post data
     */
    public $current_comment;

    public $post_arr;
    public $author_arr;
    public $duplicate;
    public $limit_time;

    public function __construct($comment_type, $meta_key = array() , $post_type = '') {
        $this->comment_type = $comment_type;
        $this->meta = array_merge($meta_key , array('et_rate'));

        $this->post_arr = array();
        $this->author_arr = array();

        $this->duplicate =  false;
        $this->limit_time =  120;
    }

    public function reset() {
        $this->post_arr = array();
        $this->author_arr = array();
    }
    /**
     * convert comments
    */
    function convert($comment, $thumb = 'thumbnail', $merge_post = true, $merge_author = true ){
        global $ae_post_factory;
        /**
         * add comment meta 
        */
        if(!empty($this->meta)) {
            foreach ($this->meta as $key => $value) {
                $comment->$value = get_comment_meta( $comment->comment_ID, $value, true );
            }
        }
        // comment link
        $comment->comment_link = get_comment_link($comment->comment_ID);
        $comment->ID = $comment->comment_ID;
        $comment->id = $comment->comment_ID;
        // caculate date ago
        $comment->date_ago = et_the_time(strtotime($comment->comment_date));

        if($merge_post) {
            /**
             * add post data to comment
            */
            if(!isset($this->post_arr[$comment->comment_post_ID])) {
                // check post exist or not
                $post = get_post($comment->comment_post_ID);
                if($post && !is_wp_error( $post )) {
                    // get register post object by post factory
                    $post_object = $ae_post_factory->get($post->post_type);
                    // if not null convert post
                    if($post_object) {
                        $comment->post_data = $post_object->convert( $post, $thumb , false );
                    }else{ // keep the simple post
                        $comment->post_data = $post;
                    }
                    // add post data to post_arr
                    $this->post_arr[$post->ID] = $comment->post_data;
                }
            }else {
                // post data already exist
                $comment->post_data = $this->post_arr[$comment->comment_post_ID];
            }
        }
        

        if($merge_author) {
            /**
             * add author data to comment
            */
            if(!isset($this->author_arr[$comment->user_id])) { // user_id not existed in author_arr
                $author = get_userdata( $comment->user_id );
                if($author) {
                    $users = AE_Users::get_instance();    
                    $comment->author_data = $users->convert($author);
                    // add author_data to author_arr
                    $this->author_arr[$comment->user_id] = $comment->author_data;
                }    
            }else {
                // author data already exist
                $comment->author_data = $this->author_arr[$comment->user_id];            
            }
        }
        

        $this->current_comment = $comment;
        return apply_filters('ae_convert_comment', $this->current_comment);
    }

    /**
     * fetch list of comments and convert them
    */
    public function fetch($args) {

        $args['type'] = $this->comment_type;
        $args['offset'] = $args['number'] * ($args['page']-1);
        
        $comments = get_comments( $args );
        $comment_data = array();
        /**
         * convert comment to build data
        */
        foreach ($comments as $key => $comment) {
            $comment_data[] = $this->convert($comment, 'review_post_thumbnail');
        }
        ob_start();
        ae_comments_pagination( $args['total'], $args['page'] , $args );
        $paginate =  ob_get_clean();

        // reset author and post arr
        $this->post_arr = array();
        $this->author_arr = array();
        // return array of comments
        return array(
            'data' => $comment_data,
            'query' => $args,
            'paginate' => $paginate
        );
    }
    // abstract static function get_instance();

    /**
     * insert comment with commnet type is class property $this->comment_type
     # @use wp_insert_comment
     # $args same with wp_insert_comment args
     * @return Object wp_error | comment object
    */
    public function insert($args) {
        global $current_user, $user_ID;
        
        // current user can not review for himself
        if (is_wp_error($post = get_post($args['comment_post_ID']))) return $post;
        
        if($this->limit_time) {
            $time = $this->limit_time;
            // review comment not too fast, should after 3 or 5 minute to post next review
            $comments = get_comments(array(
                'type' => '',
                'author_email' => $current_user->user_email,
                'number' => 1
            ));
            
            if (!empty($comments)) {
                 // check latest comment
                $comment = $comments[0];
                $date = $comment->comment_date_gmt;
                $ago = time() - strtotime($date);
                
                //return error if comment to fast
                if ($ago < ((int)$time)) return new WP_Error('fast_review', __("Please wait 2 minutes after each action submission.", ET_DOMAIN));
            }
        }
        

        // set comment type
        $args['type'] =  $this->comment_type;

        // donot allow duplication comment on a post
        if(!$this->duplicate) {
            $comments = get_comments(array(
                'post_id' => $post->ID,
                'type' => $args['type'],
                'author_email' => $current_user->user_email,
                'number' => 1
            ));

            if (!empty($comments)) {
                if(isset($args['type'])) {
                    return new WP_Error('duplication', __("Already added.", ET_DOMAIN));
                }else {
                    return new WP_Error('duplicationde', __("You have already comment on this.", ET_DOMAIN));
                }                
            }
        }
        
        unset($args['comment_author']);
        
        // try add review
        try {
            
            $browser = ae_getBrowser();
            $commentdata = wp_parse_args($args, array(
                'comment_post_ID' => $post->ID,
                'comment_author' => $current_user->user_login,
                'comment_author_email' => $current_user->user_email,
                'comment_author_url' => 'http://',
                'comment_content' => $args['comment_content'],
                'comment_type' => $args['type'],
                'comment_parent' => 0,
                'user_id' => $user_ID,
                'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                'comment_agent' => $browser['userAgent'],
                
                //'comment_date' => time(),
                'comment_approved' => isset($args['comment_approved']) ? $args['comment_approved'] : 0,
                'attitude' => 'pos'
            ));

            $commentdata = apply_filters( 'ae_filter_data_'.$args['type'], $commentdata );
            /**
             * insert review to database
             */
            $comment = wp_insert_comment($commentdata);
            
            if (!is_wp_error($comment)) {
                // update comment meta
                if(!empty($this->meta)) {
                    foreach ($this->meta as $key => $value) {
                        if( isset( $commentdata[$key] ) ) {
                            update_comment_meta($comment, $key, $commentdata[$key]);    
                        }                    
                    }
                }
                // do action after insert a comment with type args
                do_action( 'ae_after_insert_'.$args['type'] , $comment );
                // return comment data
                return $comment;

            } else {
                throw new Exception($comment->get_error_message());
            }
        }
        catch(Exception $e) {
            return new WP_Error('add_review_error', $e->getMessage());
        }
    }
}