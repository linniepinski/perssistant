<?php

$current_user = wp_get_current_user();
$args_post_receiver = array(
    'post_type' => 'chat_message',
    'post_status' => 'draft',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'receiver',
            'value' => $current_user->ID,
        ),
    ),
    'orderby' => 'post_date',
    'order' => 'DESC',
);

$posts = new WP_Query($args_post_receiver);

if ($posts->have_posts()) {
    while ($posts->have_posts()) {
        $posts->the_post();
        if (!isset($contacts_with[get_the_author_meta('id')])) {
            $contacts_with[get_the_author_meta('id')] =  1;
        }
    }



$args = array(
    'include' => array_keys($contacts_with),
    'orderby' => 'include',
);
$user_query = new WP_User_Query($args);
if (!empty($user_query->results)) {
    foreach ($user_query->results as $user) {
        if ($user->ID !== $current_user->ID) {
            $time_last = get_user_meta($user->ID, 'online_was');

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
                        'key' => 'sender',
                        'value' => $user->ID,
                    ),
                    array(
                        'key' => 'unread',
                        'value' => 'true',
                    ),
                ),
            );
            $products = new WP_Query($args);

            if ($products->have_posts()) {
                while ($products->have_posts()) {
                    $products->the_post();
                    $i++;
                }
            }
            $count_miss = '<span class="badge">'.$i.'</span>';
            if (!empty($time_last)) {
                $time_diff = time() - (int)$time_last[0];
            }
            if ($time_diff == null OR $time_diff > 3600) {
                $class = '<span class="status-online pull-right icon-circle offline"></span>';
            } elseif ($time_diff > 600) {
                $class = '<span class="status-online pull-right icon-circle afk"></span>';
            } else $class = '<span class="status-online pull-right icon-ok-circled online"></span>';

            echo '<div class="item_contact" id_contact="' . $user->ID . '"><p>' . get_avatar($user->ID, 35) . '    ' . $user->display_name.' '.$count_miss.' ' . $class . '</p></div> ' . "\n";
            unset($time_diff);
            unset($i);
        }
    }
} else {
   //
}
} else {
   // echo 'No users found.';
    echo '<div class="no-users"><p>No users found.</p></div> ' . "\n";

}

/*$args = array(
    'exclude' => array_keys($contacts_with),
    'orderby' => 'diplay_name',
    'order'   => 'ASC'
);
$user_query = new WP_User_Query($args);
if (!empty($user_query->results)) {
    foreach ($user_query->results as $user) {
        if ($user->ID !== $current_user->ID) {
            $time_last = get_user_meta($user->ID, 'online_was');

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
                        'key' => 'sender',
                        'value' => $user->ID,
                    ),
                    array(
                        'key' => 'unread',
                        'value' => 'true',
                    ),
                ),
            );
            $products = new WP_Query($args);

            if ($products->have_posts()) {
                while ($products->have_posts()) {
                    $products->the_post();
                    $i++;
                }
            }
            $count_miss = '<span class="badge">'.$i.'</span>';
            if (!empty($time_last)) {
                $time_diff = time() - (int)$time_last[0];
            }
            if ($time_diff == null OR $time_diff > 3600) {
                $class = '<span class="pull-right icon-circle offline"></span>';
            } elseif ($time_diff > 600) {
                $class = '<span class="pull-right icon-circle afk"></span>';
            } else $class = '<span class="pull-right icon-ok-circled online"></span>';

            echo '<div class="item_contact" id_contact="' . $user->ID . '"><p>' . get_avatar($user->ID, 35) . '    ' . $user->display_name.' '.$count_miss.' ' . $class . '</p></div> ' . "\n";
            unset($time_diff);
            unset($i);
        }
    }
} else {
   // echo 'No users found.';
}*/