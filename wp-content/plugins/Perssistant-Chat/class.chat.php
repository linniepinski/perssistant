<?php
/**
 * @package ukrosoft-chat
 */
class chat
{

    protected static $run = false;

    private function __construct()
    {
    }

    private function __clone()
    {
    }


    public static function Init()
    {
        //global $chat_require_capabilities;
        //var_dump(get_post_meta(1474,'unreadInvitation',true));
        //var_dump(get_post(get_post_meta(1481,'unreadInvitation',true)));
        include 'templates-js/local_language.php';
        add_action('wp_footer', array('chat', 'popup_invate'));
        add_action('wp_footer', array('chat', 'items_chat_js'));
        add_action('wp_footer', array('chat', 'localize_responses_js'));

        add_shortcode('chat', array('chat', 'chat_view'));

        wp_register_style('ChatCss', plugin_dir_url(__FILE__) . 'css/chat.css');
        wp_enqueue_style('ChatCss');

        if (is_user_logged_in()) {

            wp_register_script( 'Chat-js', plugin_dir_url(__FILE__) . 'js/ajax_chat.js' );
            wp_localize_script( 'Chat-js', 'chat_globals', $texts_chat_localized );
            wp_enqueue_script('Chat-js', plugin_dir_url(__FILE__) . 'js/ajax_chat.js', array('jquery'),true);

            wp_localize_script('Chat-js', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

            wp_enqueue_script('jquery.form', plugin_dir_url(__FILE__) . 'js/jquery.form.js', array('jquery'));
            wp_enqueue_script('jquery.tmpl', plugin_dir_url(__FILE__) . 'js/jquery.tmpl.js', array('jquery'));

            wp_enqueue_script('bootstrap.file-input.js', plugin_dir_url(__FILE__) . 'js/bootstrap.file-input.js', array('jquery'));

            wp_register_style('jquery.mCustomScrollbar', plugin_dir_url(__FILE__) . 'custom-scrollbar/jquery.mCustomScrollbar.css');
            wp_enqueue_style('jquery.mCustomScrollbar');
            wp_enqueue_script('jquery.mCustomScrollbar.concat.min', plugin_dir_url(__FILE__) . 'custom-scrollbar/jquery.mCustomScrollbar.concat.min.js', array('jquery'));


            add_action('wp_ajax_myajax_submit', array('chat', 'myajax_submit'));
            add_action('wp_ajax_nopriv_myajax_submit', array('chat', 'myajax_submit'));

            add_action('wp_ajax_myajax_refresh', array('chat', 'myajax_refresh'));
            add_action('wp_ajax_nopriv_myajax_refresh', array('chat', 'myajax_refresh'));

            add_action('wp_ajax_myajax_contact', array('chat', 'myajax_contact'));
            add_action('wp_ajax_nopriv_myajax_contact', array('chat', 'myajax_contact'));

            add_action('wp_ajax_myajax_check_online', array('chat', 'myajax_check_online'));
            add_action('wp_ajax_nopriv_myajax_check_online', array('chat', 'myajax_check_online'));

            add_action('wp_ajax_myajax_check_updates', array('chat', 'myajax_check_updates'));
            add_action('wp_ajax_nopriv_myajax_check_updates', array('chat', 'myajax_check_updates'));

            add_action('wp_ajax_myajax_count', array('chat', 'myajax_count'));
            add_action('wp_ajax_nopriv_myajax_count', array('chat', 'myajax_count'));

            add_action('wp_ajax_myajax_loadprev', array('chat', 'myajax_loadprev'));
            add_action('wp_ajax_nopriv_myajax_loadprev', array('chat', 'myajax_loadprev'));

            add_action('wp_ajax_invate_freelancer', array('chat', 'invate_freelancer'));
            add_action('wp_ajax_nopriv_invate_freelancer', array('chat', 'invate_freelancer'));

            add_action('wp_ajax_myajax_notifications_everywhere', array('chat', 'myajax_notifications_everywhere'));
            add_action('wp_ajax_nopriv_myajax_notifications_everywhere', array('chat', 'myajax_notifications_everywhere'));
        }

        $current_user = wp_get_current_user();

        update_user_meta($current_user->ID, 'online_was', time());

        if ($_POST['action'] == '') {
            $args = array(
                'post_type' => 'chat_message',
                'post_status' => 'draft',
                'posts_per_page' => 1,
                'meta_query' => array(
                    array(
                        'key' => 'receiver',
                        'value' => $current_user->ID,
                    ),
                ),
                'orderby' => 'post_date',
                'order' => 'DESC',
            );
            $last_post = new WP_Query($args);
            if ($last_post->have_posts()) {
                while ($last_post->have_posts()) {
                    $last_post->the_post();
                    $ID_post = get_the_ID();
                    update_user_meta($current_user->ID, 'last_check_chat_id', $ID_post);
                    //echo $ID_post;
                }
            }
        }

        add_filter('query_vars', array('chat', 'add_query_vars_filter_chat_contact'), 10, 1);

        add_filter('posts_where', function ($where, $q) {
            global $wpdb;

            if ($pid = $q->get('wpse_pid')) {
                // Get the compare input
                $cmp = $q->get('wpse_compare');
                // Only valid compare strings allowed:
                $cmp = in_array(
                    $cmp,
                    ['<', '>', '!=', '<>', '<=', '>=']
                )
                    ? $cmp
                    : '='; // default
                // SQL part
                $where .= $wpdb->prepare(" AND {$wpdb->posts}.ID {$cmp} %d ", $pid);
            }
            return $where;
        }, 10, 2);
    }

    function wpse119881_get_author($post_id = 0)
    {
        $post = get_post($post_id);
        return $post->post_author;
    }

    function add_query_vars_filter_chat_contact($vars)
    {
        $vars[] = "chat_contact";
        return $vars;
    }

    public static function chat_view($atts, $content = null)
    {
        global $chat_contact;
        $current_user = wp_get_current_user();

        if (current_user_can('edit_posts')) {
            $chat_contact = get_query_var('chat_contact');
            if ($chat_contact == '') {
                $current_user = wp_get_current_user();
                $usermetaarr = get_user_meta($current_user->ID, 'contact_with_user');
                $chat_contact = $usermetaarr[0];
            }
            require 'templates/main_temp.php';
        } else {
            ?>
            <div style="margin-top:7%" class="col-xs-12 bs-example bs-example-standalone">
            <div class="alert alert-info" role="alert">
                <?php __('You can\'t join the chat. Please login.','chat-frontend') ?>
            </div>
            </div>
            <?php
            //get_footer();
        }
    }

    public static function popup_invate()
    {
        include 'templates/invate_freelancer.php';
    }
    public static function localize_responses_js(){

//        $texts = apply_filters('chat_globals', $texts);
//      wp_localize_script('ajax_chat', 'chat_globals', $texts);
    }
    public static function items_chat_js()
    {
        include 'templates-js/item-message.php';
        include 'templates-js/list-item-contact.php';
    }

    public static function myajax_submit()
    {
        $html_link = '';
        $html_errors = '';
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $edit_message = $_POST['name_edit_message'];

        $contact_with_user = $_POST['contact_with'];
        if (isset($contact_with_user)) {
            update_user_meta($current_user->ID, 'contact_with_user', $contact_with_user);
        }
        if ($contact_with_user == null or trim($edit_message) == '') {
            //$html_errors = 'Fail send.Message is empty.';

//            $response = array(
//                'what' => 'stuff',
//                'action' => 'delete_something',
//                'id' => new WP_Error('oops', 'I had an accident.'),
//                'data' => 'Whoops, there was a problem!'
//            );
//            $xmlResponse = new WP_Ajax_Response($response);
//            $xmlResponse->send();
//            exit;
        } else {


            $post = array(
                'post_content' => $edit_message,
                'post_type' => 'chat_message',
                'post_author' => $user_id,
                'comment_status' => ['closed'],
            );
            $post_id = wp_insert_post($post);

            update_post_meta($post_id, 'sender', $user_id);
            update_post_meta($post_id, 'receiver', $contact_with_user);
            add_post_meta($post_id, 'unread', 'true');
            update_post_meta($post_id, 'unread', 'true');

            if (!wp_verify_nonce($_POST['wp_custom_attachment_nonce'], plugin_basename(__FILE__))) {
            }
            if (!empty($_FILES['wp_custom_attachment']['name'])) {

                $upload = wp_upload_bits($_FILES['wp_custom_attachment']['name'], null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));
                //var_dump($upload);

                $html_errors = $upload['error'];
                if ($upload['error'] == false) {
                    $filename = $upload['file'];
                    $arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));

                    $attachment = array(
                        'guid' => $upload['url'],
                        'post_mime_type' => $arr_file_type['type'],
                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename)),
                        'post_content' => '',
                        'post_status' => 'inherit'
                    );


                    $attach_id = wp_insert_attachment($attachment, $filename, $post_id);

                    require_once(ABSPATH . 'wp-admin/includes/image.php');

                    $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                    wp_update_attachment_metadata($attach_id, $attach_data);

                    $args = array(
                        'post_parent' => $post_id,
                        'post_type' => 'attachment',
                        'post_status' => 'inherit',
                        'posts_per_page' => 1,

                    );
                    $attachments = get_children($args, 'ARRAY_A');
                    if ($attachments) {
                        foreach ($attachments as $attachment) {
                            //echo $attachment['guid'];
                            $type_file = explode("/", $attachment['post_mime_type']);
                            //echo $type_file[0];
                            //var_dump($attachment);
                            if ($upload['error'] == false) {
                                if ($type_file[0] == 'image') {
                                    $html_link = '<a href="' . $attachment['guid'] . '" download>' . wp_get_attachment_image($attachment['ID'], array(320, 320)) . '</a>';
                                } else {
                                    $html_link = '<a href="' . $attachment['guid'] . '" download>' . end(explode("/", $attachment['guid'])) . '</a>';

                                }
                            }
                        }
                    }
                    unset($attachments);
                }
            }
        }
        $edit_message = preg_replace("/(\r\n){2,}/", "<br/><br/>", $edit_message); //если 2 и более подряд
        $edit_message = preg_replace("/(\r\n)/", "<br/>", $edit_message);

        $response['status'] = true;
        $response['message']['id'] = $post_id;
        $response['message']['avatar'] = get_avatar($user_id, 50);
        $response['message']['display_name'] = $current_user->display_name;
        $response['message']['content'] = $edit_message;
        $response['message']['link'] = $html_link;
        $response['message']['date1'] = date('F d,Y ');
        $response['message']['date2'] = date('g:i a');
        $response['html_errors'] = $html_errors;
        unset($html_errors);
        wp_send_json($response);
        exit;
    }

    public static function myajax_refresh()
    {
        include 'templates/item_chat.php';
        exit;
    }

    public static function myajax_contact()
    {
        include 'templates/contact.php';
        exit;
    }

    public static function myajax_check_online()
    {
        include 'templates/contact_list.php';
        exit;
    }

    public static function myajax_count()
    {
        $current_user = wp_get_current_user();
        $args = array(

            'post_type' => 'chat_message',
            'post_status' => 'draft',
            'posts_per_page' => -1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'receiver',
                    'value' => $current_user->ID,
                ),
                array(
                    'key' => 'unread',
                    'value' => 'true',
                ),
            ),
        );
        $posts = new WP_Query($args);
        if ($posts->post_count !== 0) {
            echo $posts->post_count;
        }
        exit;
    }

    public static function myajax_loadprev()
    {
        $html_link = '';
        $first_chat_id = $_POST['prev_chat_id'];
        $contact_with_user = $_POST['contact_with_user'];
        $current_user = wp_get_current_user();
        $args = array(
            'wpse_pid' => $first_chat_id, // Our custom post id argument
            'wpse_compare' => '<', // Out custom compare argument (<,>,<=,>=,!=,<>)
            'post_type' => 'chat_message',
            'post_status' => 'draft',
            'posts_per_page' => 20, //20
            'meta_query' => array(
                array(
                    'key' => 'receiver',
                    'value' => array($current_user->ID, $contact_with_user),
                ),
                array(
                    'key' => 'sender',
                    'value' => array($current_user->ID, $contact_with_user),
                ),
            ),
            'orderby' => 'post_date',
            'order' => 'DESC', //DESC --> ASC
        );
        $posts = new WP_Query($args);
        $array_rev = array_reverse($posts->posts);
        $posts->posts = $array_rev;
        $count = 0;
        $response = array();
        if ($posts->have_posts()) {
            while ($posts->have_posts()) {
                $posts->the_post();
                $receiver = get_post_meta(get_the_ID(), 'receiver');
                if ($receiver[0] == $current_user->ID) {
                    update_post_meta(get_the_ID(), 'unread', 'false');
                }
                $id_post = get_the_ID();
                $args = array(
                    'post_parent' => $id_post,
                    'post_type' => 'attachment',
                    //'numberposts' => 1,
                    'post_status' => 'inherit',
                    'posts_per_page' => 1,

                );
                $attachments = get_children($args, 'ARRAY_A');

                if ($attachments) {
                    foreach ($attachments as $attachment) {

                        $type_file = explode("/", $attachment['post_mime_type']);

                        if ($type_file[0] == 'image') {
                            $html_link = '<a href="' . $attachment['guid'] . '" download>' . wp_get_attachment_image($attachment['ID'], array(320, 320)) . '</a>';
                        } else {
                            $html_link = '<a href="' . $attachment['guid'] . '" download>' . end(explode("/", $attachment['guid'])) . '</a>';
                        }

                    }
                }
                unset($attachments);
                $date1 = new DateTime(get_the_date('', $id_post));
                $date2 = new DateTime(date('F d,Y ', strtotime('-1 days')));

                $response['query'][$count]['id'] = $id_post;
                $response['query'][$count]['avatar'] = get_avatar(get_the_author_meta('ID'), 50);
                $response['query'][$count]['display_name'] = get_the_author();
                $response['query'][$count]['content'] = get_the_content();
                $response['query'][$count]['link'] = $html_link;
                if ($date1 > $date2) {
                    $response['query'][$count]['date1'] = get_the_date('', $id_post);
                    $response['query'][$count]['date2'] = get_the_time('', $id_post);
                } else {
                    $response['query'][$count]['date1'] = get_the_time('', $id_post);
                    $response['query'][$count]['date2'] = get_the_date('', $id_post);
                }
                $count++;
            }
            wp_send_json($response);

        } else {
            wp_send_json(array(
                'status' => true,
                'type' => 'empty',
                'msg' =>  __("No messages", 'chat-frontend'),
            ));
        }
        exit;
    }

    public static function invate_freelancer()
    {
        $response = array(
            'success' => false
        );
        $edit_message = $_POST['invate_message'].'<p><a href="'.$_POST['guid'].'">'.$_POST['title'].'</a></p>';
        $sender_id = $_POST['sender_id'];
        $contact_with_user = $_POST['reciever_id'];
        $project_ID = $_POST['project_id_invate'];

        if (isset($contact_with_user)) {
            update_user_meta($sender_id, 'contact_with_user', $contact_with_user);
        }
        if ($contact_with_user !== null or trim($edit_message) !== '') {
            $post = array(
                'post_content' => $edit_message,
                'post_type' => 'chat_message',
                'post_author' => $sender_id,
                'comment_status' => ['closed'],
            );
            $post_id = wp_insert_post($post);

            update_post_meta($post_id, 'sender', $sender_id);
            update_post_meta($post_id, 'receiver', $contact_with_user);
            add_post_meta($post_id, 'unread', 'true');
            update_post_meta($post_id, 'unread', 'true');
            update_post_meta($post_id, 'unreadInvitation', $project_ID);

            $response['success'] = true;
            $response['msg'] = __("Invitation sent successfully.", 'chat-frontend');
            wp_send_json($response);
            exit;
        } else {
            $response['msg'] = __( 'An unknown error has occurred. Please try again later.' , 'chat-frontend' );
            $response['success'] = false;
            wp_send_json($response);
            exit;
        }
    }

    public static function myajax_notifications_everywhere()
    {
        $current_user = wp_get_current_user();
        $last_check = get_user_meta($current_user->ID, 'last_check_chat_id');
        if (!isset($last_check)) {
            $last_check = 0;
        }

        $args = array(

            'wpse_pid' => $last_check, //$last_check , // Our custom post id argument
            'wpse_compare' => '>', // Out custom compare argument (<,>,<=,>=,!=,<>)
            'post_type' => 'chat_message',
            'post_status' => 'draft',
            'posts_per_page' => 1,
            'meta_query' => array(
                array(
                    'key' => 'receiver',
                    'value' => $current_user->ID,
                ),
            ),
            'orderby' => 'post_date',
            'order' => 'ASC',
        );
        $last_post = new WP_Query($args);

        if ($last_post->have_posts()) {
            while ($last_post->have_posts()) {
                $last_post->the_post();
                $author = get_the_author();
                $ID_post = get_the_ID();
                update_user_meta($current_user->ID, 'last_check_chat_id', $ID_post);
                //$img_url = get_avatar_data();
                if (get_post_meta($ID_post,'unreadInvitation',true)!==''){
                    $invate_post = get_post(get_post_meta($ID_post,'unreadInvitation',true));
//                    $message ="You've got an invitation from ".$author." to an interview on ".$invate_post->post_title.". Check messages!";
                    $message = printf("You've got an invitation from %s to an interview on %s. Check messages!",$author,$invate_post->post_title);
                }else
                {
                    $message = get_the_content();
                }
                //var_dump(get_avatar_url());
                $responsed = array(
                    'what' => 'chat_message',
                    'action' => 'notifications_everywhere',
                    'supplemental' => array(
                        'idmessage' => $ID_post,
                        'sender' => $author,
                        'message' => $message,
                        'status' => 'success',
                        // 'img' => $img_url['url'],
                    )
                );
                $Response = new WP_Ajax_Response($responsed);
                $Response->send();
                exit;
            }
        } else {
            echo 'No new message here';
        }
        exit;

    }

    public static function myajax_check_updates()
    {
        $html_link = '';
        $last_chat_id = $_POST['last_chat_id'];
        $contact_with_user = $_POST['contact_with_user'];
        //$chat_id_count = $_POST['chat_id_count'];
        //echo 'last id : ' .$last_chat_id_getpost . ' contact:'. $contact_with_user;
        //if ($last_chat_id_getpost == '') $last_chat_id_getpost = 0;
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $args = array(

            'wpse_pid' => $last_chat_id, // Our custom post id argument
            'wpse_compare' => '>', // Out custom compare argument (<,>,<=,>=,!=,<>)
            'post_type' => 'chat_message',
            'post_status' => 'draft',
            'posts_per_page' => 1,
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'receiver',
                    'value' => $user_id,
                ),
                array(
                    'key' => 'sender',
                    'value' => $contact_with_user,
                ),
            ),
            'orderby' => 'post_date',
            'order' => 'ASC',
        );
        $products = new WP_Query($args);

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                if (get_post_meta(get_the_ID(), 'receiver') == $user_id) {
                    update_post_meta(get_the_ID(), 'unread', '');
                }
                $url_meta = get_post_meta(get_the_ID(), 'wp_custom_attachment');
                $type = get_post_meta(get_the_ID(), 'type');
                $type_file = explode("/", $type[0]);
                if ($type_file[0] == 'image') {
                    $html_link = '<a href="' . $url_meta[0]['url'] . '"><img class="img-responsive" src="' . $url_meta[0]['url'] . '"></a>';
                } else {
                    $html_link = '<a href="' . $url_meta[0]['url'] . '">' . end(explode("/", $url_meta[0]['file'])) . '</a>';
                }

                $message = get_the_content();
                $dateH = get_the_modified_date();
                $dateD = get_the_modified_time();
                $message = preg_replace("/(\r\n){2,}/", "<br/><br/>", $message); //если 2 и более подряд

                $message = preg_replace("/(\r\n)/", "<br/>", $message);

                $response['status'] = true;
                $response['message']['id'] = get_the_ID();;
                $response['message']['avatar'] = get_avatar(get_the_author_meta('ID'), 50);
                $response['message']['display_name'] = get_the_author();
                $response['message']['content'] = $message;
                $response['message']['link'] = $html_link;
                $response['message']['date1'] = $dateH;
                $response['message']['date2'] = $dateD;
                unset($html_errors);
                wp_send_json($response);

            }
        } else {
//            $response['status'] = true;
//            wp_send_json($response);
            echo 'No new message here';
        }
        exit;
    }


}