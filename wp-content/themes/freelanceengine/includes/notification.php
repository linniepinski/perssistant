<?php

/**
 * This file contain functions related to user notification system
 * @since 1.1.1
 * @author Dakachi
 */

/**
 * register post notification to store user notification
 * @since 1.2
 * @author Dakachi
 */
function fre_register_notification() {
    $labels = array(
        'name' => __('Notifications', ET_DOMAIN) ,
        'singular_name' => __('Notification', ET_DOMAIN) ,
        'add_new' => _x('Add New notification', ET_DOMAIN, ET_DOMAIN) ,
        'add_new_item' => __('Add New notification', ET_DOMAIN) ,
        'edit_item' => __('Edit notification', ET_DOMAIN) ,
        'new_item' => __('New notification', ET_DOMAIN) ,
        'view_item' => __('View notification', ET_DOMAIN) ,
        'search_items' => __('Search notifications', ET_DOMAIN) ,
        'not_found' => __('No notifications found', ET_DOMAIN) ,
        'not_found_in_trash' => __('No notifications found in Trash', ET_DOMAIN) ,
        'parent_item_colon' => __('Parent notification:', ET_DOMAIN) ,
        'menu_name' => __('Notifications', ET_DOMAIN) ,
    );
    
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
        'has_archive' => 'notifications',
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array(
            'slug' => 'notification'
        ) ,
        'capability_type' => 'post',
        'supports' => array(
            'title',
            'editor',
            'author',
            'excerpt'
        )
    );
    register_post_type('notify', $args);
    
    // register notify object
    global $ae_post_factory;
    $ae_post_factory->set('notify', new AE_Posts('notify'));
}
add_action('init', 'fre_register_notification');

/**
 * class Fre_Notification
 * notify employer and freelancer when have any change on bid and project
 * @since 1.2
 * @author Dakachi
 */
class Fre_Notification extends AE_PostAction
{
    function __construct() {
        $this->post_type = 'notify';
        
        // init notify object
        $this->notify = new AE_Posts('notify');
        
        // catch action insert new bid to notify employer
        $this->add_action('ae_insert_bid', 'newBid', 10, 2);
        
        // catch action a bid accepted and notify freelancer
        $this->add_action('fre_delete_bid', 'bidDeleted');
        $this->add_action('fre_accept_bid', 'bidAccepted');

        // add action when employer complete project
        $this->add_action('fre_complete_project', 'completeProject', 10, 2);
        
        // add action review project owner
        $this->add_action('fre_freelancer_review_employer', 'reviewProjectOwner', 10, 2);
        // add a notification when have new message
        $this->add_action('fre_send_message', 'newMessage', 10, 3);

        $this->add_action('fre_new_invite', 'newInvite', 10, 3);
        
        $this->add_action('ae_update_user', 'clearNotify', 10, 2);

        $this->add_ajax('ae-fetch-notify', 'fetch_post');
        $this->add_action('ae_convert_notify', 'convert_notify');

        $this->add_action( 'wp_footer', 'render_template_js' );

        $this->add_action( 'template_redirect', 'mark_user_read_message');

        $this->add_ajax('ae-notify-sync', 'notify_sync');
    }
    
    /**
     * Notify employer when have new freelancer bid on his project
     * @param Object $bid object
     * @param Array $args
     * @since 1.2
     * @author Dakachi
     */
    function newBid($bid, $args) {
        $project = get_post($args['post_parent']);
        $content = 'type=new_bid&project=' . $args['post_parent'] . '&bid=' . $bid;
        
        // insert notification
        $notification = array(
            'post_type' => $this->post_type,
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_author' => $project->post_author,
            'post_title' => sprintf(__("New bid on %s", ET_DOMAIN) , get_the_title($project->ID)) ,
            'post_status' => 'publish',
            'post_parent' => $project->ID
        );
        return $this->insert($notification);
    }
    
    /**
     * notify freelancer when his bid was accepted by employer
     * @param int $bid_id the id of bid
     * @since 1.2
     * @author Dakachi
     */
    function bidAccepted($bid_id) {
        $bid = get_post($bid_id);
        if (!$bid || is_wp_error($bid)) return;
        
        $project_id = $bid->post_parent;
        $project = get_post($project_id);
        if (!$project || is_wp_error($project)) return;
        
        $content = 'type=bid_accept&project=' . $project_id;
        
        // insert notification
        $notification = array(
            'post_type' => $this->post_type,
            'post_parent' => $project_id,
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_status' => 'publish',
            'post_author' => $bid->post_author,
            'post_title' => sprintf(__("Bid on project %s was accepted", ET_DOMAIN) , get_the_title($project->ID))
        );
        return $this->insert($notification);
    }

    /**
     * notify freelancer when his bid was accepted by employer
     * @param int $bid_id the id of bid
     * @since 1.2
     * @author Dakachi
     */
    function bidDeleted($bid_id) {
        $bid = get_post($bid_id);
        if (!$bid || is_wp_error($bid)) return;

        $project_id = $bid->post_parent;
        $project = get_post($project_id);
        if (!$project || is_wp_error($project)) return;

        $content = 'type=bid_deleted&project=' . $project_id;

        // insert notification
        $notification = array(
            'post_type' => $this->post_type,
            'post_parent' => $project_id,
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_status' => 'publish',
            'post_author' => $bid->post_author,
            'post_title' => sprintf(__("Bid on project %s was declined", ET_DOMAIN) , get_the_title($project->ID))
        );
        return $this->insert($notification);
    }


    /**
     * notify freelancer after employer complete a project
     * @param int $project_id
     * @param Array $args
     * @since 1.2
     * @author Dakachi
     */
    function completeProject($project_id, $args) {
        
        $content = 'score=' . $args['score'] . '&type=complete_project&project=' . $project_id;
        $project = get_post($project_id);
        $bid_id = get_post_meta($project_id, 'accepted', true);
        $bid_author = get_post_field('post_author', $bid_id);
        
        // insert notification
        $notification = array(
            'post_type' => $this->post_type,
            'post_parent' => $project_id,
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_author' => $bid_author,
            'post_status' => 'publish',
            'post_title' => sprintf(__("Project %s was completed", ET_DOMAIN) , get_the_title($project->ID))
        );
        return $this->insert($notification);
    }
    
    /**
     * freelancer review project owner
     * @param int $project_id
     * @param Array $args request args
     #      - score
     #      - comment_content
     * @since 1.2
     * @author Dakachi
     */
    function reviewProjectOwner($project_id, $args) {
        global $user_ID;
        $content = 'score=' . $args['score'] . '&type=review_project&project=' . $project_id;
        $project = get_post($project_id);
        
        $project_title = get_the_title($project->ID);
        $bidder_name = get_the_author_meta('display_name', $user_ID);
        
        // insert notification
        $notification = array(
            'post_type' => $this->post_type,
            'post_parent' => $project_id,
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_author' => $project->post_author,
            'post_status' => 'publish',
            'post_title' => sprintf(__("%s reviewed project %s", ET_DOMAIN) , $bidder_name, $project_title)
        );
        return $this->insert($notification);
    }
    
    /**
     * notify when a project working on have new message
     * @param object $message
     * @param object $project
     * @param object $bid
     * @since 1.2
     * @author Dakachi
     */
    function newMessage($message, $project, $bid) {
        global $user_ID;

        $content = 'type=new_message&project=' . $project->ID . '&sender=' . $user_ID;
        $notification = array(
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_status' => 'publish',
            'post_type' => $this->post_type,
            'post_title' => sprintf(__("New message on project %s", ET_DOMAIN) , get_the_title($project->ID)) ,
            'post_parent' => $project->ID
        );
        
        // update notify for freelancer if current user is project owner
        if ($user_ID == $project->post_author) {
            $notification['post_author'] = $bid->post_author;
        }
        
        // update notify to employer if freelancer send message
        if ($user_ID == $bid->post_author) {
            $notification['post_author'] = $project->post_author;
        }

        $have_new = get_post_meta( $project->ID, $user_ID.'_new_message', true );
        
        if($have_new) return ;
        update_post_meta( $project->ID, $user_ID.'_new_message', true );

        $mail = Fre_Mailing::get_instance();
        $mail->new_message($notification['post_author'], $project, $message);

        return $this->insert($notification);
    }

    /**
     * notify user when have an invite to new project 
     * @param int $invited  
     * @param int $send_invite    
     * @since 1.3
     * @author Dakachi
     */
    function newInvite($invited , $send_invite, $list_project) {
        global $user_ID;
        $content = 'type=new_invite&send_invite=' . $send_invite;
        
        $author = get_the_author_meta( 'display_name', $invited );
        $send_author = get_the_author_meta( 'display_name', $send_invite );
        // insert notification
        $notification = array(
            'post_type' => $this->post_type,
            'post_content' => $content,
            'post_excerpt' => $content,
            'post_author' => $invited,
            'post_status' => 'publish',
            'post_title' => sprintf(__("%s have a new invite from %s", ET_DOMAIN) , $author, $send_author)
        );
        $notify = $this->insert($notification);
        update_post_meta( $notify, 'project_list', $list_project );
        foreach ($list_project as $item) {
            update_post_meta($item, "invited_{$invited}", true);
        }
        return $notify;
    }
    
    /**
     * clear notify flag, set user dont have any new notification
     * @param int $result user id
     * @param Array $data user submit data
     * @since 1.2
     * @author Dakachi
     */
    function clearNotify($user_id, $data) {
        global $user_ID;
        if ($user_ID != $user_id) {
            return $user_id = $user_ID;
        }
        if (isset($data['read_notify']) && $data['read_notify']) {
            delete_user_meta($user_id, 'fre_new_notify');
        }
        return $user_id;
    }
    
    /**
     * insert user notification post
     * @param Array $notfication Notification post data
     * @since 1.2
     * @author Dakachi
     */
    function insert($notification) {
        $notify = wp_insert_post($notification);
        if ($notify) {
            $number = get_user_meta($notification['post_author'], 'fre_new_notify', true);
            $number = $number + 1;
            update_user_meta($notification['post_author'], 'fre_new_notify', $number);
        }
        return $notify;
    }
    /**
     * convert notification content 
     * @param object $notify The notification object
     * @since 1.2
     * @author Dakachi
     */
    function convert_notify($notify) {
        $notify->content = $this->fre_notify_item($notify);
        return $notify;
    }
    /**
     * build notification content 
     * @param object $notify The notification object
     * @since 1.2
     * @author Dakachi
     */
    function fre_notify_item($notify) {      
        // parse post excerpt to get data
        $post_excerpt = str_replace('&amp;', '&', $notify->post_excerpt);
        parse_str($post_excerpt);

        if (!isset($type) || !$type) return ;

        if( $type != 'new_invite' ) {
            if ( !isset($project) || !$project ) return;    
        }  
        // check project exists or deleted
        $project_post = get_post($project);
        if(!$project_post || is_wp_error( $project_post )) return ; 
        
        $project_link = '';
        if(isset($project)) {
            $project_link = '<a class="project-link" href="' . get_permalink($project) . '" >' . get_the_title($project) . '</a>';    
        }        
        $postdata[] = $notify;
        $content = '';
        switch ($type) {
                
                // notify employer when his project have new bid
            case 'new_bid':
                
                // get bid author
                $bid_author = get_post_field('post_author', $bid);
                
                // get bid author profile id
                $user_profile_id = get_user_meta($bid_author, 'user_profile_id', true);
                
                // render bid author data
                $author = '<a class="user-link" href="'. get_author_posts_url($bid_author) .'">
                        <span class="avatar-freelancer-notification">
                            ' . get_avatar($bid_author, 45) . '
                        </span>
                        <div class="profile">
                            <span class="name">
                                ' . get_the_author_meta('display_name', $bid_author) . '
                            </span>
                            <span class="postion-employer">
                                ' . get_post_meta($user_profile_id, 'et_professional_title', true) . '
                            </span>
                        </div>
                        </a>';
                
                $content.= sprintf(__("%s bid for your project %s", ET_DOMAIN) , $author, $project_link);
                break;
                
                // notify freelancer when his bid was accepted

            case 'bid_deleted':
                $project_author = get_post_field('post_author', $project);
                $author = '<a class="user-link" href="'. get_author_posts_url($project_author) .'" ><span class="avatar-notification">
                            ' . get_avatar($project_author, 45) . '
                        </span>&nbsp;&nbsp;
                        <span class="date-notification name">
                            ' . get_the_author_meta('display_name', $project_author) . '
                        </span>
                        </a>';
                $content.= sprintf(__("Your bid at %s was declined by %s", ET_DOMAIN) , $project_link, $author);
                break;

            // notify freelancer when employer complete a project
                
            case 'bid_accept':
                $project_author = get_post_field('post_author', $project);
                $author = '<a class="user-link" href="'. get_author_posts_url($project_author) .'" ><span class="avatar-notification">
                            ' . get_avatar($project_author, 45) . '
                        </span>&nbsp;&nbsp;
                        <span class="date-notification name">
                            ' . get_the_author_meta('display_name', $project_author) . '
                        </span>
                        </a>';
                $content.= sprintf(__("Your bid at %s was accepted by %s", ET_DOMAIN) , $project_link, $author);
                break;
                
                // notify freelancer when employer complete a project
                
                
            case 'complete_project':
                $project_owner = get_post_field('post_author', $project);
                $author = '<a class="user-link" href="'. get_author_posts_url($project_owner) .'" >
                            <span class="avatar-notification">
                            ' . get_avatar($project_owner, 45) . '
                        </span>&nbsp;&nbsp;
                        <span class="date-notification name">
                            ' . get_the_author_meta('display_name', $project_owner) . '
                        </span>
                        </a>';
                $content.= sprintf(__("%s you worked on was completed by %s", ET_DOMAIN) , $project_link, $author);
                break;

            case 'review_project':
                $accepted_bid = get_post_meta($project, 'accepted', true);
                $bid_author = get_post_field('post_author', $accepted_bid);
                
                $author = '<a class="user-link" href="'. get_author_posts_url($bid_author) .'" ><span class="avatar-notification">
                            ' . get_avatar($bid_author, 45) . '
                        </span>&nbsp;&nbsp;
                        <span class="date-notification name">
                            ' . get_the_author_meta('display_name', $bid_author) . '
                        </span>
                        </a>';
                $content.= sprintf(__("%s has reviewed on %s", ET_DOMAIN) , $author, $project_link);
                break;

            case 'new_message':
                if (!isset($sender)) $sender = 1;
                $author = '<a class="user-link"  href="'. get_author_posts_url($sender) .'" ><span class="avatar-notification">
                            ' . get_avatar($sender, 45) . '
                        </span>&nbsp;&nbsp;
                        <span class="date-notification name">
                            ' . get_the_author_meta('display_name', $sender) . '
                        </span>
                        </a>';
                
                $workspace_link = add_query_arg(array(
                    'workspace' => 1
                ) , get_permalink($project));
                
                $workspace_link = '<a href="' . $workspace_link . '" >' . get_the_title($project) . '</a>';
                
                $content.= sprintf(__("%s sent you a message on %s workspace", ET_DOMAIN) , $author, $workspace_link);
                
                break;
            case 'new_invite':
                if (!isset($send_invite)) $send_invite = 1;
                $author = '<a class="user-link"   href="'. get_author_posts_url($send_invite) .'"  ><span class="avatar-notification">
                            ' . get_avatar($send_invite, 45) . '
                        </span>&nbsp;&nbsp;
                        <span class="date-notification name">
                            ' . get_the_author_meta('display_name', $send_invite) . '
                        </span>
                        </a>';
                $project_list = get_post_meta( $notify->ID, 'project_list', true );
                if(!$project_list) {
                    $content.= sprintf(__("You have an invite from %s", ET_DOMAIN) , $author );
                }else{
                    $project = '';
                    foreach ($project_list as $key => $value) {
                        $project .= '<a href="'.get_permalink( $value) .'" >'. get_the_title($value) .'</a>';
                        $project .= ', ';
                    }
                    $project = trim($project, ', ');
                    $content.= sprintf(__("%s invited you to join project %s", ET_DOMAIN) , $author , $project );
                }
                break;

            default:
                break;
            }
            $content.= '&nbsp;';
            $content.= sprintf(__("at %s", ET_DOMAIN) , get_the_time('', $notify->ID));
            $content.= '&nbsp;';
            $content.= sprintf(__("on %s", ET_DOMAIN) , get_the_date('', $notify));
            $content.= '<a data-action="delete" class="action delete" href="#"><i class="fa fa-trash-o"></i></a>';
            // return notification content
            return $content;
    }

    /**
     * render js template for notification 
     * @since 1.2
     * @author Dakachi
     */
    function render_template_js(){
    ?>
        <script type="text/template" id="ae-notify-loop">
        {{= content }}
        </script>
    <?php 
    }

    /**
     * clear a flag when user read new message 
     * @param snippet
     * @since snippet.
     * @author Dakachi
     */
    function mark_user_read_message(){
        if( isset($_REQUEST['workspace']) && $_REQUEST['workspace'] ) {
            if(is_singular( PROJECT )){
                global $post, $user_ID;
                delete_post_meta( $post->ID, $user_ID.'_new_message' );
            }
        }
    }
    
    /**
     * user can sync notification ( delete ) 
     * @param snippet
     * @since snippet.
     * @author Dakachi
     */
    function notify_sync(){
        global $ae_post_factory, $user_ID;
        $request = $_REQUEST;
        // unset($request['post_content']);
        unset($request['post_excerpt']);
        if (isset($request['delete'])) {
            $request['post_status'] = 'trash';
        }

        $place = $ae_post_factory->get($this->post_type);
        // sync notify
        $result = $place->sync($request);

        wp_send_json(array(
            'success' => true,
            'data' => $result,
            'msg' => __("Update project successful!", ET_DOMAIN)
        ));

    }

}
new Fre_Notification();

/**
 * get user notification by
 * @param snippet
 * @since snippet.
 * @author Dakachi
  */

function fre_user_notification($user_id = 0, $page = 1) {
    if (!$user_id) {
        global $user_ID;
        $user_id = $user_ID;
    }
    global $post, $wp_query, $ae_post_factory;
    $notify_object = $ae_post_factory->get('notify');
    $notifications = query_posts(array(
        'post_type' => 'notify',
        'post_status' => 'publish',
        'author' => $user_id,
        'showposts' => 10, 
        'paged' => $page
    ));
    
    $postdata = array();
    if (have_posts()) {
        echo '<ul class="notification-list">';
        while (have_posts()) {
            the_post();
            $notify = $post;

            $project = get_post($post->post_parent);
            if (!$project || is_wp_error($project)) continue;
            $notify = $notify_object->convert($post);
            $postdata[] = $notify;
            echo '<li class="notify-item">';
            echo $notify->content;
            echo '</li>';
        }
        echo '</ul>';
    }else{
        echo '<ul class="notification-list">';
        echo '<li>';
        _e('You have no notifications' ,ET_DOMAIN);
        echo '</li>';
        echo '</ul>';
    }
    echo '<script type="data/json" class="postdata" >' . json_encode($postdata) . '</script>';
    
    // pagination
    
    echo '<div class="paginations-wrapper">';
    ae_pagination($wp_query, get_query_var('paged') , 'load');
    echo '</div>';
    wp_reset_query();
}
/**
 * function check user have new notifcation or not 
 * @since 1.3
 * @author Dakachi
 */
function fre_user_have_notify() {
    global $user_ID;
    return get_user_meta($user_ID, 'fre_new_notify', true);
}
