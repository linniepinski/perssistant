<?php

/**
 * @package ukrosoft-chat
 */
class trace
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

        wp_register_style('TraceCss', plugin_dir_url(__FILE__) . 'css/trace.css');
        wp_enqueue_style('TraceCss');

//        if (is_user_logged_in()) {

        wp_enqueue_script('ajax_trace', plugin_dir_url(__FILE__) . 'js/ajax_trace.js', array('jquery'));
        wp_localize_script('ajax_trace', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));


        add_action('wp_ajax_check_project_list', array('trace', 'check_project_list'));
        add_action('wp_ajax_nopriv_check_project_list', array('trace', 'check_project_list'));

        add_action('wp_ajax_delete_items', array('trace', 'delete_items'));
        add_action('wp_ajax_nopriv_delete_items', array('trace', 'delete_items'));

        add_action('wp_ajax_auth_trace', array('trace', 'auth_trace'));
        add_action('wp_ajax_nopriv_auth_trace', array('trace', 'auth_trace'));

        add_action('wp_ajax_get_trace_data', array('trace', 'get_trace_data'));
        add_action('wp_ajax_nopriv_get_trace_data', array('trace', 'get_trace_data'));

        add_action('wp_ajax_get_view_data', array('trace', 'get_view_data'));
        add_action('wp_ajax_nopriv_get_view_data', array('trace', 'get_view_data'));

        add_action('wp_ajax_get_tracker_time_of_week', array('trace', 'get_tracker_time_of_week'));
        add_action('wp_ajax_nopriv_get_tracker_time_of_week', array('trace', 'get_tracker_time_of_week'));

        add_shortcode('diary', array('trace', 'diary_view'));
        add_shortcode('trace', array('trace', 'trace_view'));
        add_filter('query_vars', array('trace', 'add_query_vars_filter_diary'), 10, 1);

    }

    function add_query_vars_filter_diary($vars)
    {
        $vars[] = "project_id";
        $vars[] = "freelancer_id";
        return $vars;
    }

    public static function diary_view($atts, $content = null)
    {
        $user_role = ae_user_role();
        if ($user_role == 'freelancer') {
            $project_id = get_query_var('project_id');
            $current_user = wp_get_current_user();
            $newdate = date_start_week();

            ?>
            <div id="diary_section" class="container">
                <br>
                <?php
                $current_project_post = get_post($project_id);
                include 'template/diary-control-row-freelancer.php';
                $project_query = diary_query('freelancer_id', $project_id, $current_user->ID, $newdate);
                $row = 0;
                foreach ($project_query as $key => $query) {

                    if ($key % 6 == 0) {
                        $row++;
                        ?>
                        <div class="row <?php echo 'data-line-' . $row ?>">
                        <div class="col-xs-1">
                            <div class="time-block">
                                <span class="center-ver">
                                    <input type="checkbox" class="row-time-item" data-target="<?php echo $row; ?>">
                                    <?php echo date("g a", strtotime($query->timestamp)); ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-11">
                        <?php
                        $memoquery = memo_query('freelancer_id', $project_id, $current_user->ID, $query->id);
                        echo print_memo_line($memoquery);
                        ?>
                        <div class="row image-line" data-parent="row-<?php echo $row; ?>">
                    <?php
                    }
                    include 'template/diary-imageline-item.php';
                    if ($key % 6 == 5) {
                        ?>
                        </div>
                        </div>
                        </div>
                    <?php
                    }

                }



                ?>
            </div>
            </div>
            </div>
            </div>


        <?php

        } elseif ($user_role == 'employer') {
            $project_id = get_query_var('project_id');
            $freelancer_id = get_query_var('freelancer_id');

            $newdate = date_start_week();

            ?>
            <div id="diary_section" class="container">
                <br>
                <?php
                $current_project_post = get_post($project_id);
                $i = 0;
                include 'template/diary-control-row-employer.php';
                $project_query = diary_query('freelancer_id', $project_id, $freelancer_id, $newdate);

                $row = 0;
                foreach ($project_query as $key => $query) {

                if ($key % 6 == 0) {
                $row++;
                ?>
                <div class="row <?php echo 'data-line-' . $row ?>">
                    <div class="col-xs-1">
                        <div class="time-block">
                                <span class="center-ver">
                                    <input type="checkbox" class="row-time-item" data-target="<?php echo $row; ?>">
                                    <?php echo date("g a", strtotime($query->timestamp)); ?>
                                </span>
                        </div>
                    </div>
                    <div class="col-xs-11">
                        <?php
                        $memoquery = memo_query('freelancer_id', $project_id, $freelancer_id, $query->id);
                        echo print_memo_line($memoquery);
                        ?>
                        <div class="row image-line" data-parent="row-<?php echo $row; ?>">
                            <?php
                            }
                            include 'template/diary-imageline-item.php';

                            if ($key % 6 == 5) {
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                            }
                            }
                            ?>
                        </div>
                    </div>

                </div>
            </div>

        <?php


        }
    }

    public static function trace_view($atts, $content = null)
    {
        $user_role = ae_user_role();
        if ($user_role == 'freelancer') {
            ?>
            <div class="container">
                <?php
                $current_user = wp_get_current_user();
                $list = get_list_freelancer_bids($current_user->ID);

                if ($list != false) {
                    foreach ($list as $item) {
                        include 'template/trace-freelancer-item.php';
                    }
                }
                ?>
            </div>
        <?php

        } elseif ($user_role == 'employer') {
            ?>
            <div class="container">
                <?php
                $current_user = wp_get_current_user();
                $list = get_list_employer_projects($current_user->ID);
                if ($list != false) {
                    foreach ($list as $item) {
                        include 'template/trace-employer-item.php';
                    }
                }
                ?>
            </div>
        <?php

        } else {
            ?>
            <div class="container">
                <?php
                echo 'user role: ';
                var_dump($user_role);
                ?>
            </div>
        <?php
        }

    }


    public static function delete_items()
    {
        global $wpdb;
        $current_user = $_POST['current_user'];
        $project_id = $_POST['project_id'];

        $user = wp_get_current_user();
        // var_dump($user->ID);

        $array = explode(',', $_POST['array']);
        //var_dump($array);
        $prepare_sql = '';
        foreach ($array as $item) {
            if ($item != '') {
                $prepare_sql .= $item . ',';
            }
        }
        $prepare_sql = substr($prepare_sql, 0, -1);
        $delete_images = $wpdb->get_results(
            "SELECT `image_url`
	             FROM `wp_trace`
	             WHERE `freelancer_id` = {$user->ID}
	            AND `id` IN ({$prepare_sql})
	            "
        );
        $abspath = ABSPATH;
        $abspath = substr($abspath, 0, -1);

        foreach ($delete_images as $image) {
            if (is_file("{$abspath}{$image->image_url}")) {
                if (unlink("{$abspath}{$image->image_url}")) {
                } else {
                }
            } else {
            }
        }
        $result = $wpdb->query("UPDATE `wp_trace` SET `memo_string` = '',
`image_url` = 'deleted',
`trace_time` = '0',
`clicks_mouse` = '0',
`clicks_keyboard` = '0' WHERE `id` IN ({$prepare_sql})");
        if ($result > 0) {
            $array_memo_starts = explode(',', $_POST['memo_starts']);
            $memo_refresh_output = array();
            foreach (array_unique($array_memo_starts) as $item) {
                if ($item != '') {
                    $memoquery = $wpdb->get_results(
                        "SELECT `id`,`memo_string`,`image_url`
	             FROM `wp_trace`
	             WHERE `project_id` = {$project_id}
	             AND `freelancer_id` = {$current_user}
	             AND `id` >= {$item}

                LIMIT 6
	            ");
                    $memo_refresh_output[$item]['memo_line'] = print_memo_line($memoquery);
                }
            }
            $notify = array();
            $notify['status'] = 'success';
            $notify['msg'] = 'success';
            $notify['count'] = $result;
            $notify['memo'] = $memo_refresh_output;
            wp_send_json($notify);
            wp_die();
        } elseif ($result === false) {
            $notify = array();
            $notify['status'] = 'error';
            $notify['msg'] = 'error';
            $notify['result'] = $result;
            $notify['error'] = 'SQL_FAIL';
            wp_send_json($notify);
            wp_die();
        }
        wp_die();
    }

    public static function get_tracker_time_of_week()
    {
        global $wpdb;
        $today = date('Y-m-d', time());
        //$start_week = date('w', time()) - 1;
        $user_id = $_POST['id'];
        $project_id = $_POST['project_id'];
        $hash = $_POST['hash'];
        $status = check_session_hash($user_id, $hash);
        if ($status['status']) {
            $newdate = date_start_week();

            $time_trace_query = $wpdb->get_results(
                "SELECT `trace_time`
	             FROM `wp_trace`
	             WHERE `freelancer_id` = {$user_id}
	             AND `project_id` = {$project_id}
	             AND `timestamp` > '{$newdate}'
	            "
            );
            $count = 0;
            foreach ($time_trace_query as $trace_time) {
                $count += $trace_time->trace_time;
            }
            $time_trace_query_today = $wpdb->get_results(
                "SELECT `trace_time`
	             FROM `wp_trace`
	             WHERE `freelancer_id` = {$user_id}
	             AND `project_id` = {$project_id}
	             AND `timestamp` > '{$today}'
	            "
            );
            $count_today = 0;
            foreach ($time_trace_query_today as $trace_time) {
                $count_today += $trace_time->trace_time;
            }

            $notify = array();
            $notify['message'] = 'success';
            $notify['time']['time_today'] = $count_today;
            $notify['time']['time_of_week'] = $count;
            wp_send_json($notify);
            wp_die();
        } else {
            $notify = array();
            $notify['message'] = 'error';
            $notify['error'] = 'FAIL_LOGIN';
            wp_send_json($notify);
            wp_die();
        }
    }

    public static function get_view_data()
    {


    }


    public static function get_trace_data()
    {
        global $wpdb;
        $user_id = (int)$_POST['id'];
        $project_id = (int)$_POST['project_id'];
        $employer_id = (int)$_POST['employer_id'];
        $hash = $_POST['hash'];
        $trace_time = (int)$_POST['seconds'];
        $memo_string = $_POST['memo_string'];
        $clicks_mouse = (int)$_POST['mouse_clicks'];
        $clicks_keyboard = (int)$_POST['keyboard_clicks'];

        $status = check_session_hash($user_id, $hash);
        if ($status['status']) {

            if (!empty($_FILES['image']['name'])) {
                $time = date('Y_m_d_H_i_s', time());
                $upload = wp_upload_bits($_FILES['image']['name'], null, file_get_contents($_FILES['image']['tmp_name']));

                if ($upload['error'] == false) {
                    $arr_file_type = wp_check_filetype(basename($_FILES['image']['name']));
                    $abspath = ABSPATH;
                    // $d = date ( 'd' , time());
                    //$m = date ( 'm' , time());
                    // $y = date('Y', time());

                    //$dir_path = "{$abspath}wp-content/uploads/traced_images/{$y}/{$employer_id}/{$project_id}/{$user_id}/{$m}/{$d}/";
                    $dir_path = "{$abspath}wp-content/uploads/traced_images/{$employer_id}/{$project_id}/{$user_id}"; // /{$m}/{$d}/";

                    if (mkdir($dir_path, 0777, true) OR file_exists($dir_path)) {
                        $name = $time . '.' . $arr_file_type['ext'];
                        //$name = $_FILES['image']['name'];
                        $filepath = "{$dir_path}/{$name}";
                        //var_dump(rename($upload['file'], $filepath));
                        if (rename($upload['file'], $filepath)) {
                            $base_url = get_site_url();
                            $http_url = "/wp-content/uploads/traced_images/{$employer_id}/{$project_id}/{$user_id}/{$name}";

                            $result = $wpdb->insert("wp_trace", array(
                                "employer_id" => $employer_id,
                                "project_id" => $project_id,
                                "freelancer_id" => $user_id,
                                "trace_time" => $trace_time,
                                "image_url" => $http_url,
                                "memo_string" => $memo_string,
                                "clicks_mouse" => $clicks_mouse,
                                "clicks_keyboard" => $clicks_keyboard
                            ));
                            $notify = array();
                            $notify['message'] = 'inserted';
                            $notify['status'] = 'success';
                            $notify['data'] = $result;
                            $notify['id'] = $wpdb->insert_id;
                            $notify['url'] = "{$base_url}{$http_url}";
                            wp_send_json($notify);
                            wp_die();
                        } else {
                            //fail move and rename
                            $notify = array();
                            $notify['status'] = 'error';
                            $notify['message'] = 'failed move and rename file';
                            $notify['error'] = 'FAIL_MOVE_FILE';

                            wp_send_json($notify);
                            wp_die();
                        }

                    } else {
                        //fail create dir
                        $notify = array();
                        $notify['status'] = 'error';
                        $notify['message'] = '';
                        $notify['error'] = 'CREATING_DIR_FAILED';
                        wp_send_json($notify);
                        wp_die();
                    }

                }
            } else {
                //no image
                $notify = array();
                $notify['status'] = 'error';
                $notify['message'] = '';
                $notify['error'] = 'NO_UPLOAD_IMAGE';
                wp_send_json($notify);
                wp_die();
            }
        } else {
            //fail session
            $notify = array();
            $notify['status'] = 'error';
            $notify['message'] = '';
            $notify['error'] = 'FAIL_LOGIN';
            wp_send_json($notify);
            wp_die();
        }
        wp_die('asdfg');
    }

    public static function auth_trace()
    {
        $response = array();
        $auth_data = array();
        $auth_data['method'] = $_POST['method'];

        if ($auth_data['method'] == 'login') {
            $creds = array();
            $creds['user_login'] = $_POST['login'];
            $creds['user_password'] = $_POST['password'];
            $creds['remember'] = false;
            $user = wp_signon($creds, true);
            if (is_wp_error($user)) {
                $response['status'] = 'error';
                $response['error'] = 'ERROR_LOGIN';
                $response['message'] = $user->get_error_message();
            } else {
                $response['status'] = 'success';
                //$response['message']= '';
                $user_sessions = get_user_meta($user->ID, 'session_tokens');
//                var_dump(end($user_sessions[0]));
//                var_dump(end(array_keys($user_sessions[0])));


                $response['auth_data'] = end($user_sessions[0]);
                $response['auth_data']['hash'] = end(array_keys($user_sessions[0]));
                $response['auth_data']['ID'] = $user->ID;
                wp_send_json($response);
                wp_die();
            }
        }
        /*if ($auth_data['method'] == 'is_valid') {
            $auth_data['ID'] = $_POST['ID'];
            $auth_data['hash'] = $_POST['hash'];
            $user_sessions = get_user_meta($auth_data['ID'], 'session_tokens');
            if (array_key_exists($auth_data['hash'], $user_sessions[0])) {
                $response['auth_data'] = end($user_sessions[0]);
                $response['auth_data']['hash'] = end(array_keys($user_sessions[0]));

            }
        }*/
    }


    public static function check_project_list()
    {
        //var_dump($_POST);
        $user_id = (int)$_POST['id'];

        $status = check_session_hash($_POST['id'], $_POST['hash']);
        if ($status['status']) {
            $args = array(
                'post_status' => 'accept',
                'post_type' => 'bid',
                'author' => $user_id,
                'posts_per_page' => -1,

            );
            $list_bids = new WP_Query($args);
            $id_posts = array();
//var_dump($list_bids);
            if ($list_bids->have_posts()) {
                while ($list_bids->have_posts()) {
                    $list_bids->the_post();
                    // var_dump(get_the_author());
                    //var_dump(wp_get_post_parent_id(get_the_ID()));
                    $id_posts[wp_get_post_parent_id(get_the_ID())] = 1;

                }
            } else {

            }
//var_dump($id_posts);
            if ($id_posts) {
                $args2 = array(
                    'post__in' => array_keys($id_posts),
                    'post_type' => 'project',
                    'posts_per_page' => -1,

                    //'post_status' => 'close',
                );
                $list = array();
                $list_projects = new WP_Query($args2);
                if ($list_projects->have_posts()) {
                    while ($list_projects->have_posts()) {
                        $list_projects->the_post();
                        $item['ID'] = get_the_ID();
                        $item['guid'] = get_the_guid();
                        $item['project_author'] = get_the_author();
                        $item['author_id'] = get_the_author_meta('id');
                        $item['project_title'] = get_the_title();

                        //var_dump($list);
                        array_push($list, $item);


                    }
                } else {
                    $notify = array();
                    $notify['message'] = 'no projects';
                    $notify['error'] = 'EMPTY_LIST';
                    wp_send_json($notify);
                    wp_die();
                }
                //var_dump($list);
                //   echo json_encode($list);
                wp_send_json($list);
                unset($list_projects);
                unset($list_bids);
                wp_die();

            } else {
                $notify = array();
                $notify['message'] = 'no projects';
                $notify['error'] = 'EMPTY_LIST';
                wp_send_json($notify);
                wp_die();
            }
        } else {
            $notify = array();
            $notify['message'] = 'wrong hash';
            $notify['error'] = 'INVALID_HASH';
            wp_send_json($notify);
            wp_die();
        }

    }
}

function check_session_hash($id, $hash)
{
    $user_sessions = get_user_meta($id, 'session_tokens');
    if (array_key_exists($hash, $user_sessions[0])) {
        $response['status'] = true;
        $response['auth_data'] = end($user_sessions[0]);
        $response['auth_data']['hash'] = end(array_keys($user_sessions[0]));
        return $response;
    }

    return $response['status'] = false;
}

function get_list_freelancer_bids($user_id)
{


    $args = array(
        'post_status' => 'accept',
        'post_type' => 'bid',
        'author' => $user_id,
        'posts_per_page' => -1,

    );
    $list_bids = new WP_Query($args);
    $id_posts = array();
    if ($list_bids->have_posts()) {
        while ($list_bids->have_posts()) {
            $list_bids->the_post();
            $id_posts[wp_get_post_parent_id(get_the_ID())] = 1;

        }
    } else {
        //return 'Empty list';
        echo '<div style="margin-top: 20px">Empty</div>';
        return false;
    }
    if ($id_posts) {
        $args2 = array(
            'post__in' => array_keys($id_posts),
            'post_type' => 'project',
            'posts_per_page' => -1,
        );
        $list = array();
        $list_projects = new WP_Query($args2);
        if ($list_projects->have_posts()) {
            while ($list_projects->have_posts()) {
                $list_projects->the_post();
                $item['ID'] = get_the_ID();
                $item['guid'] = get_the_guid();
                $item['project_author'] = get_the_author();
                $item['author_id'] = get_the_author_meta('id');
                $item['project_title'] = get_the_title();

                array_push($list, $item);


            }
        } else {
            return false;
        }
        return $list;
    }
    return false;
}

function get_list_employer_projects($user_id)
{
    $args = array(
        'post_status' => array('publish', 'close'),
        'post_type' => 'project',
        'author' => $user_id,
        'posts_per_page' => -1,

    );
    $id_posts = array();
    $list_projects = new WP_Query($args);
    if ($list_projects->have_posts()) {
        while ($list_projects->have_posts()) {
            $list_projects->the_post();

            $id_posts[get_the_ID()] = 1;

        }
        //var_dump($list_bids);
    } else {
        echo '<div style="margin-top: 20px">Empty</div>';
        return false;
    }
    $list = array();
    if ($id_posts) {
        $args2 = array(
            'post_parent__in' => array_keys($id_posts),
            'post_type' => 'bid',
            'posts_per_page' => -1,

            'post_status' => 'accept',
        );
        $list_bids = new WP_Query($args2);

        if ($list_bids->have_posts()) {
            while ($list_bids->have_posts()) {
                $list_bids->the_post();
                $item['ID'] = get_the_ID();
                $item['post_parent'] = wp_get_post_parent_id(get_the_ID());
                $item['title'] = get_the_title(get_post(wp_get_post_parent_id(get_the_ID())));
                $item['guid'] = get_the_guid(get_post(wp_get_post_parent_id(get_the_ID())));
                $item['bid_author'] = get_the_author();
                $item['author_id'] = get_the_author_meta('id');
                //$item['project_title'] = get_the_title();

                //var_dump($list);
                array_push($list, $item);


            }
            return $list;
        } else {
            // wp_die('error2');
            echo '<div style="margin-top: 20px">Empty</div>';
            return false;

        }
    }
    return false;
}

function diary_query($user_role = '', $project_id, $user_id, $newdate)
{
    global $wpdb;
    $query = $wpdb->get_results(
        "SELECT *
	                  FROM `wp_trace`
	                  WHERE `project_id` = {$project_id}
	                  AND `{$user_role}` = {$user_id}
                      AND `timestamp` > '{$newdate}'
	                "
    );
    return $query;
}

function memo_query($user_role = '', $project_id, $user_id, $id)
{
    global $wpdb;
    $query = $wpdb->get_results(
        "SELECT `id`,`memo_string`,`image_url`
	             FROM `wp_trace`
	             WHERE `project_id` = {$project_id}
	             AND `{$user_role}` = {$user_id}
	             AND `id` >= {$id}

                LIMIT 6
	            ");
    return $query;
}

function print_memo_line($memoquery)
{
    $html_memo_string = '';
    $html_memo_string .= '<div class="row memo-line first-id-' . $memoquery[0]->id . '">';
    $array_memo = array();
    //var_dump($memoquery[0]->id);
    foreach ($memoquery as $key2 => $memo_item) {
        if ($memo_item->image_url == 'deleted') {
            $array_memo[] = 'deleted';
        } else {
            $array_memo[] = $memo_item->memo_string;
        }
    }

    $output = array();
    $prev_memo = array_shift($array_memo);
    $count_memo = 1;
    $count_memo_2 = count($array_memo);
    foreach ($array_memo as $count_memo_key => $item) {
        if ($prev_memo == $item) {
            $count_memo++;
        } else {
            $output[] = array(
                'memo' => $prev_memo,
                'count' => $count_memo
            );

            $count_memo = 1;
            $prev_memo = $item;
        }
        if ($count_memo_key % $count_memo_2 == $count_memo_2 - 1) {
            $output[] = array(
                'memo' => $item,
                'count' => $count_memo
            );
        }

    }
    unset($array_memo);
    foreach ($output as $item) {
        $style2 = '';
        $style = '';
        //var_dump($item);
        if ($item['memo'] != '' && $item['memo'] != 'deleted') {
            $style = 'with-memo';
        }
        if ($item['memo'] == '') {
            $style = 'empty-memo';
            //$item['memo'] = '&nbsp;';
        }
        if ($item['memo'] == 'deleted') {
            $style2 = 'deleted-memo';
            $item['memo'] = '';
        }


        $html_memo_string .= '<div class="col-xs-' . $item['count'] * 2 . ' no-padding-memo ' . $style . ' wtf2">';
        $html_memo_string .= '<p class="memostring ' . $style2 . '" >' . substr($item['memo'], 0, 20) . '</p>';
        $html_memo_string .= '</div>';
    }
    unset($output);
    $html_memo_string .= '</div>';
    return $html_memo_string;
}

function get_trace_activity($mouse_clicks, $keyboard_clicks, $time)
{
    $activity = $mouse_clicks + $keyboard_clicks;
    $one_click_per__sec = 2;
    $activity_perfect = $time / $one_click_per__sec ;

//    $perc_activity = round(((($temp) / ($time)) * 100));
    $perc_activity = round(($activity / $activity_perfect) * 100 );
//    var_dump($activity_perfect);
//    var_dump($activity);
//var_dump($perc_activity);

    if ($perc_activity > 100) {
        $perc_activity = 100;
    }
    return $perc_activity;
}

function date_start_week()
{
    $time = date('w', time()) - 1;
    //var_dump($time);
    if ($time >= 1) {
        $newdate = date('Y-m-d', strtotime("- {$time} days"));

    } else {
        $newdate = date('Y-m-d', time());
    }
    return $newdate;
}

function get_tracker_time_of_week($id, $project_id)
{
    global $wpdb;
    $newdate = date_start_week();
    $time_trace_query = $wpdb->get_results(
        "SELECT `trace_time`
	             FROM `wp_trace`
	             WHERE `freelancer_id` = {$id}
	             AND `project_id` = {$project_id}
	             AND `timestamp` > '{$newdate}'
	            "
    );
    //var_dump($time_trace_query);
    //var_dump($GLOBALS['wpdb']->last_query);
    $count = 0;
    foreach ($time_trace_query as $trace_time) {
        $count += $trace_time->trace_time;
    }
    //var_dump($count);
    //echo gmdate("H:i", $count);
    if ($count > 86399) {
        $seconds = $count;
        $H = floor($seconds / 3600);
        $i = ($seconds / 60) % 60;
        //$s = $seconds % 60;
        $time_string = sprintf("%02dh %02dm", $H, $i);
    } else {
        $time_string = gmdate("H\h i\m", $count);
    }

    return $time_string;
}
