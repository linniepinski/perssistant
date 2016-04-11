<?php
$currID = wp_get_current_user();
$user_id = $currID->ID;
$contact_with_user = $_POST['contact_with_user'];

$args = array(
    'post_type' => 'chat_message',
    'post_status' => 'draft',
    'posts_per_page' => 20, //20
    'meta_query' => array(
        array(
            'key' => 'receiver',
            'value' => array($user_id, $contact_with_user),
        ),
        array(
            'key' => 'sender',
            'value' => array($user_id, $contact_with_user),
        ),
    ),
    'orderby' => 'post_date',
    'order' => 'DESC', //DESC --> ASC
);

$chat_query = get_posts($args);
$response = array();
if ($chat_query) {
//    echo '<a id="loadprev" class="btn btn-default btn-sm btn-block">Load previous messages</a>';
    $response['status'] = true;
    $response['msg'] = __('No message here', '');
    $response['isPrevExist'] = false;
    foreach (array_reverse($chat_query) as $key => $post) {
        setup_postdata($post);
        $receiver = get_post_meta($post->ID, 'receiver');
        if ($receiver[0] == $user_id) {
            update_post_meta($post->ID, 'unread', 'false');
        }
        $id_post = $post->ID;

        $args = array(
            'post_parent' => $id_post,
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => 1,
        );

        $attachments = get_children($args, 'ARRAY_A');
        $html_link = '';
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
        $date1 = new DateTime($post->post_date);
        $date2 = new DateTime(date('F d,Y ', strtotime('-1 days')));

        $response['query'][$key]['id'] = $post->ID;
        $response['query'][$key]['avatar'] = get_avatar(get_the_author_meta('ID'), 50);
        $response['query'][$key]['display_name'] = get_the_author();
        $response['query'][$key]['content'] = get_the_content();
        $response['query'][$key]['link'] = $html_link;
        if ($date1 > $date2) {
            $response['query'][$key]['date1'] = get_the_date('', $post->ID);
            $response['query'][$key]['date2'] = get_the_time('', $post->ID);
        } else {
            $response['query'][$key]['date1'] = get_the_time('', $post->ID);
            $response['query'][$key]['date2'] = get_the_date('', $post->ID);
        }
    }
    wp_send_json($response);
    wp_reset_postdata();
} else {
    $response['status'] = false;
    $response['code_response'] = 'no_messages';
    $response['msg'] = __('No message here', 'chat-frontend');
    wp_send_json($response);
}
exit;