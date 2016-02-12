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

if ($chat_query) {
    echo '<a id="loadprev" class="btn btn-default btn-sm btn-block">Load previous messages</a>';
    foreach (array_reverse($chat_query) as $post) {

        setup_postdata($post);

        $receiver = get_post_meta($post->ID, 'receiver');
        //var_dump($receiver[0]);
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

        ?>
        <div class="row" chat_id="<?php echo $post->ID ?>">
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
                echo $html_link ;
                ?>

            </div>


            <div class="col-xs-4 col-sm-3 col-md-2 the_time_chat">
                <?php

                /*echo get_the_date('',$post->ID);
                echo get_the_time('',$post->ID);*/
                //echo '<br>';
                /*$date_message = get_the_date('',$post->ID);
                $date = );*/

                $date1 = new DateTime(get_the_date('',$post->ID));
                $date2 = new DateTime(date('F d,Y ', strtotime('-1 days')));

                //var_dump($date1 == $date2);
               // var_dump($date1 < $date2);
                //var_dump($date1 > $date2);

                if ($date1 > $date2) {
                    ?>
                    <span class="time" data-toggle="tooltip" data-placement="top"
                          title="<?php echo get_the_date('', $post->ID) ?>"><?php echo get_the_time('', $post->ID) ?></span>

                <?php
                } else {?>
                    <span data-toggle="tooltip" data-placement="top"
                          title="<?php echo get_the_time('', $post->ID) ?>"><?php echo get_the_date('', $post->ID) ?></span>
                <?php
                }
                echo $date;
                ?>


            </div>

        </div>

   <?php


    }
    wp_reset_postdata();
} else {

    echo '<div class="panell panel-primary">
                    <div class="panel-body">
                        <h5 style="text-align: center">No message here</h5>
                    </div>
                </div>';
}
exit;