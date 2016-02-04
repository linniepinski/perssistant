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

        add_action('wp_footer', array('chat', 'popup_invate'));
        add_shortcode('chat', array('chat', 'chat_view'));

        wp_register_style('ChatCss', plugin_dir_url(__FILE__) . 'css/chat.css');
        wp_enqueue_style('ChatCss');

        if (is_user_logged_in()) {

            wp_enqueue_script('my-ajax-request', plugin_dir_url(__FILE__) . 'js/ajax_chat.js', array('jquery'));
            wp_localize_script('my-ajax-request', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

            wp_enqueue_script('jquery.form', plugin_dir_url(__FILE__) . 'js/jquery.form.js', array('jquery'));
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
            echo '<div style="margin-top:7%" class="col-xs-12 bs-example bs-example-standalone">
    <div class="alert alert-info" role="alert">' . 'You can\'t join the chat. Please login.' . '
            </div></div>';
            //get_footer();
        }

    }

    public static function popup_invate()
    {
        include 'templates/invate_freelancer.php';
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
            $response = array(
                'what' => 'stuff',
                'action' => 'delete_something',
                'id' => new WP_Error('oops', 'I had an accident.'),
                'data' => 'Whoops, there was a problem!'
            );
            $xmlResponse = new WP_Ajax_Response($response);
            $xmlResponse->send();
            exit;
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
        $respons = array(
            'what' => 'chat_message',
            'action' => 'send_message',
            'data' => '<div class="row" chat_id="' . $post_id . '">
    <div class="hidden-xs hidden-sm col-md-1 text-ellipsis the_author_chat">
         ' . get_avatar($user_id, 50) . '
    </div>
    <div class="col-xs-8 col-sm-9 col-md-9 the_content_chat">
        <p class="aut">' . $current_user->display_name . '</p>
        <p class="cont">' . $edit_message . '</p>' . $html_link . '
    </div>
    <div class="col-xs-4 col-sm-3 col-md-2 the_time_chat">
                                <span class="time" data-toggle="tooltip" data-placement="top"
                                      title="' . date('F d,Y ') . '">' . date('g:i a') . '</span>
    </div>
</div>',
            'supplemental' => array(
                //'data_message'=> 'sdfsdfdsf',
                'sender' => $user_id,
                'errors' => $html_errors,
            )

        );
        $Response = new WP_Ajax_Response($respons);
        $Response->send();
        unset($html_errors);
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
                ?>
                <div class="row" chat_id="<?php the_ID() ?>">
                    <div class="hidden-xs hidden-sm col-md-1 text-ellipsis the_author_chat">
                        <?php echo get_avatar(get_the_author_meta('ID'), 50); ?>
                    </div>
                    <div class="col-xs-8 col-sm-9 col-md-9 the_content_chat">
                        <p class="aut"><?php the_author() ?></p>
                        <?php

                        $phrase = get_the_content();
                        $phrase = apply_filters('the_content', $phrase);
                        $replace = '<p class="cont">';
                        echo str_replace('<p>', $replace, $phrase);
                        echo $html_link;
                        ?>

                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-2 the_time_chat">
                                <span class="time" data-toggle="tooltip" data-placement="top"
                                      title="<?php the_modified_date() ?>"><?php the_modified_time() ?></span>
                    </div>
                </div>
            <?php
            }
        } else {

//            echo '<div class="panell panel-primary">
//                    <div class="panel-body">
//                        <h5 style="text-align: center">No message here</h5>
//                    </div>
//                </div>';
            wp_send_json(array(
                'status' => true,
                'type' => 'empty',
                'msg' => 'No messages',
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
            $response['msg'] = __("Invitation sent successfully.", ET_DOMAIN);
            wp_send_json($response);
            exit;
        } else {
            $response['msg'] = __( 'An unknown error has occurred. Please try again later.' , ET_DOMAIN );
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
                    $message ="You've got an invitation from ".$author." to an interview on ".$invate_post->post_title.". Check messages!";
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
        //$UI_private_room = $contact_with_user . '_' . $user_id;
        //echo 'no data now';
        $args = array(

            'wpse_pid' => $last_chat_id, // Our custom post id argument
            'wpse_compare' => '>', // Out custom compare argument (<,>,<=,>=,!=,<>)
            //'post_name' => $UI_private_room,
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

        //var_dump($products);

        if ($products->have_posts()) {
            while ($products->have_posts()) {
                $products->the_post();
                if (get_post_meta(get_the_ID(), 'receiver') == $user_id) {
                    update_post_meta(get_the_ID(), 'unread', '');
                }
                $author = get_the_author();
                $ID_post = get_the_ID();
                $url_meta = get_post_meta(get_the_ID(), 'wp_custom_attachment');
                $type = get_post_meta(get_the_ID(), 'type');
//var_dump($type);
                $type_file = explode("/", $type[0]);
//var_dump($type_file);
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
                $responsed = array(

                    'what' => 'chat_message',
                    'action' => 'check_updates',
                    'id' => $contact_with_user . '_' . $user_id,
                    'data' => '<div class="row" chat_id="' . $ID_post . '">
    <div class="hidden-xs hidden-sm col-md-1 text-ellipsis the_author_chat">
        ' . get_avatar(get_the_author_meta('ID'), 50) . '
    </div>
    <div class="col-xs-8 col-sm-9 col-md-9 the_content_chat">
        <p class="aut">' . $author . '</p>
        <p class="cont">' . $message . '</p>' . $html_link . '
    </div>
    <div class="col-xs-4 col-sm-3 col-md-2 the_time_chat">
                                <span class="time" data-toggle="tooltip" data-placement="top"
                                      title="' . $dateH . '">' . $dateD . '</span>
    </div>
</div>',
                    'supplemental' => array(
                        'id_message' => $ID_post,
                        'message' => 'true',
                        //'sender' => $author,
                        //'message' => $message,
                        //'dateD' => $dateD,
                        //'dateH' => $dateH,
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


}