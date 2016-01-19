<?php

function register_interview()
{
    $labels = array(
        'name' => __('interview', ET_DOMAIN),
        'singular_name' => __('interview', ET_DOMAIN),
        'add_new' => _x('Add New interview', ET_DOMAIN, ET_DOMAIN),
        'add_new_item' => __('Add New interview', ET_DOMAIN),
        'edit_item' => __('Edit interview', ET_DOMAIN),
        'new_item' => __('New interview', ET_DOMAIN),
        'view_item' => __('View interview', ET_DOMAIN),
        'search_items' => __('Search interview', ET_DOMAIN),
        'not_found' => __('No interviews found', ET_DOMAIN),
        'not_found_in_trash' => __('No interviews found in Trash', ET_DOMAIN),
        'parent_item_colon' => __('Parent interview:', ET_DOMAIN),
        //'menu_name' => __('interviews', ET_DOMAIN) ,
    );

    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'public' => false,
        // 'show_ui' => true,
        // 'show_in_menu' => true,
        // 'show_in_admin_bar' => true,
        'menu_position' => 5,
        // 'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => 'interviews',
        'query_var' => true,
        'can_export' => true,
        'rewrite' => array(
            'slug' => 'interview'
        ),
        'capability_type' => 'post',
        'supports' => array()
    );
    register_post_type('interview', $args);

    global $ae_post_factory;
    $interview_tax = array();

    $interview_meta = array(
        'date_interview_1',
        'date_interview_2',
        'date_interview_3',
        'skype_id',
        'tel'


    );

    $ae_post_factory->set('interview', new AE_Posts('interview', $interview_tax, $interview_meta));
    //var_dump($ae_post_factory);
    //$ae_post_factory->set('interview', new AE_Posts('interview'));
}


add_action('admin_menu', 'mt_add_pages');
add_action('init', 'register_interview');

// action function for above hook
function mt_add_pages()
{
    // Add a new submenu under Settings:
    // add_options_page(__('Test Settings','menu-test'), __('Test Settings','menu-test'), 'manage_options', 'testsettings', 'mt_settings_page');

    // Add a new submenu under Tools:
    // add_management_page( __('Test Tools','menu-test'), __('Test Tools','menu-test'), 'manage_options', 'testtools', 'mt_tools_page');

    // Add a new top-level menu (ill-advised):
    add_menu_page(__('Interview', 'menu-test'), __('Interview', 'menu-test'), 'manage_options', 'mt-top-level-handle', 'mt_toplevel_page');

    // Add a submenu to the custom top-level menu:
  //  add_submenu_page('mt-top-level-handle', __('Expired interviews', 'menu-test'), __('Expired interviews', 'menu-test'), 'manage_options', 'sub-page', 'mt_sublevel_page');

    // Add a second submenu to the custom top-level menu:
  //  add_submenu_page('mt-top-level-handle', __('Future interviews', 'menu-test'), __('Future interviews', 'menu-test'), 'manage_options', 'sub-page2', 'mt_sublevel_page2');
}

// mt_settings_page() displays the page content for the Test settings submenu


// mt_tools_page() displays the page content for the Test Tools submenu
function mt_tools_page()
{
    echo "<h2>" . __('Test Tools', 'menu-test') . "</h2>";
}

// mt_toplevel_page() displays the page content for the custom Test Toplevel menu
function mt_toplevel_page()
{
    ?>

<!--    <script type="text/javascript" src="/wp-content/themes/freelanceengine/js/jquery.tablesorter.js"></script>-->
    <?php
    echo "<h2>" . __('Interviews', 'menu-test') . "</h2>";

    $tabs = array('today' => 'Interviews today', 'future' => 'Future interviews', 'expired' => 'Expired interviews');
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ($tabs as $tab => $name) {
        $class = ($tab == $_GET['tab']) ? ' nav-tab-active' : '';
        if (!isset ($_GET['tab']) && $tab == 'today') {
            $class = ' nav-tab-active';
        }
        echo "<a class='nav-tab$class' href='?page=mt-top-level-handle&tab=$tab'>$name</a>";

    }
    echo '</h2>';

    if (isset ($_GET['tab'])) {
        $tab = $_GET['tab'];
    }
    else $tab = 'today';

    echo '<table class="form-table">';
    switch ($tab) {
        case 'expired' :
            expired_page();
            break;
        case 'future' :
            future_page();
            break;
        case 'today' :
            today_page();
            break;
    }
}

function future_page(){
    $args_unconfirmed_users = array(
        'fields' => 'ID',
        'meta_query' => array(
            array(
                'key' => 'interview_status',
                'value' => 'unconfirm',
            ),
        ),
    );
    $unconfirmed_users = new WP_User_Query($args_unconfirmed_users);

    $args = array(
        'post_type' => 'interview',
        'author__in' => $unconfirmed_users->get_results(),
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'date_interview_1',
                'value' => strtotime('tomorrow'),
                'compare' => '>=',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => 'date_interview_2',
                'value' => strtotime('tomorrow'),
                'compare' => '>=',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => 'date_interview_3',
                'value' => strtotime('tomorrow'),
                'compare' => '>=',
                'type' => 'NUMERIC'
            ),
        ),
    );
    $interviews = new WP_Query($args);
    //wp-list-table widefat fixed striped posts
    ?>
    <table id="interview-table" class="wp-list-table widefat fixed striped posts">
        <tbody>
        <tr>
            <th width="5%">#</th>
            <th width="10%">Nickname</th>
            <th width="10%">Skype</th>
            <th width="10%">Tel</th>
            <th id="date_interview_1" width="15%">Date1</th>
            <th id="date_interview_2" width="15%">Date2</th>
            <th id="date_interview_3" width="15%">Date3</th>
            <th width="10%">Confirm</th>
        </tr>

        <?php

        //var_dump()
        foreach ($interviews->posts as $key => $post) {
            $post_meta = get_post_meta($post->ID);
            ?>
            <tr>
                <td><?php echo $key + 1 ?></td>
                <td><?php echo get_userdata($post->post_author)->display_name ?></td>
                <td><?php echo $post_meta['skype_id'][0]?></td>
                <td><?php echo $post_meta['tel'][0] ?></td>
                <td>
                    <?php

                    $str = $post_meta['date_interview_1'][0];
                    if ($str > time() && $str < strtotime('tomorrow')){
                        echo '<strong><u>'. date('m/d/Y g:i A',$str).'</strong></u>';
                    } elseif ($str < time()){
                        ?>
                        <label style="color: red">Expired</label>
                    <?php
                    }else{
                        echo date('m/d/Y g:i A',$str);
                    }
                    ?>
                </td>
                <td>
                    <?php

                    //01/05/2016 12:00 AM
                    $str = $post_meta['date_interview_2'][0];
                    if ($str > time() && $str < strtotime('tomorrow')){
                        echo '<strong><u>'. date('m/d/Y g:i A',$str).'</strong></u>';
                    } elseif ($str < time()){
                        ?>
                        <label style="color: red">Expired</label>
                    <?php
                    }else{
                        echo date('m/d/Y g:i A',$str);
                    }

                    //                    $date = new DateTime();
//                    $interview_date = DateTime::createFromFormat('m/d/Y g:i A',$str);
//                    $interval = $date->diff($interview_date);
//                    //                    echo '<pre>';
//                    //                    var_dump($interview_date);
//                    //                    echo '</pre>';
//                    if($interval->days == 0) {
//
//                        echo '<strong><u>'.$str.'</u></strong>';
//
//                    } elseif($interval->days > 1) {
//                        if($interval->invert == 0) {
//                            echo 'expired';
//                        } else {
//                            echo $str;
//                        }
//                    } else {
//                        //Sometime
//                    }
                    ?>
                </td>
                <td>
                    <?php

                    $str = $post_meta['date_interview_3'][0];
                    if ($str > time() && $str < strtotime('tomorrow')){
                        echo '<strong><u>'. date('m/d/Y g:i A',$str).'</strong></u>';
                    } elseif ($str < time()){
                        ?>
                        <label style="color: red">Expired</label>
                    <?php
                    }else{
                        echo date('m/d/Y g:i A',$str);
                    }


                    ?>
                </td>
                <td><button class="confirm-user" data-user-id="<?php echo $post->post_author; ?>">Confirm</button></td>
            </tr>

            <?php
            ////echo '<pre>';
            //var_dump($interviews->posts);
            // var_dump($post);

            //var_dump(date('Y-m-d',strtotime(get_post_meta($post->ID)['date_interview_1'][0])));
            // echo '</pre>';
        }

        ?>
        </tbody>
    </table>

<?php
}

function expired_page(){
    $args_unconfirmed_users = array(
        'fields' => 'ID',
        'meta_query' => array(
            array(
                'key' => 'interview_status',
                'value' => 'unconfirm',
            ),
        ),
    );
    $unconfirmed_users = new WP_User_Query($args_unconfirmed_users);

    $args = array(
        'post_type' => 'interview',
        'author__in' => $unconfirmed_users->get_results(),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'date_interview_1',
                'value' => time(),
                'compare' => '<',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => 'date_interview_2',
                'value' => time(),
                'compare' => '<',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => 'date_interview_3',
                'value' =>time(),
                'compare' => '<',
                'type' => 'NUMERIC'
            ),
        ),
    );
    $interviews = new WP_Query($args);
    //wp-list-table widefat fixed striped posts
    ?>
    <table id="interview-table" class="wp-list-table widefat fixed striped posts">
        <tbody>
        <tr>
            <th width="5%">#</th>
            <th width="10%">Nickname</th>
            <th width="10%">Skype</th>
            <th width="10%">Tel</th>
            <th id="date_interview_1" width="15%">Date1</th>
            <th id="date_interview_2" width="15%">Date2</th>
            <th id="date_interview_3" width="15%">Date3</th>
            <th width="10%">Confirm</th>
        </tr>

        <?php

        //var_dump()
        foreach ($interviews->posts as $key => $post) {
            $post_meta = get_post_meta($post->ID);
            ?>
            <tr>
                <td><?php echo $key + 1 ?></td>
                <td><?php echo get_userdata($post->post_author)->display_name ?></td>
                <td><?php echo $post_meta['skype_id'][0]?></td>
                <td><?php echo $post_meta['tel'][0] ?></td>
                <td>
                    <?php

                    $str = $post_meta['date_interview_1'][0];
                    echo date('m/d/Y g:i A',$str);

                    ?>
                </td>
                <td>
                    <?php
                    $str = $post_meta['date_interview_2'][0];
                    echo date('m/d/Y g:i A',$str);

                    ?>
                </td>
                <td>
                    <?php

                    $str = $post_meta['date_interview_3'][0];
                    echo date('m/d/Y g:i A',$str);

                    ?>
                </td>
                <td><button class="confirm-user" data-user-id="<?php echo $post->post_author; ?>">Confirm</button></td>
            </tr>

            <?php
            ////echo '<pre>';
            //var_dump($interviews->posts);
            // var_dump($post);

            //var_dump(date('Y-m-d',strtotime(get_post_meta($post->ID)['date_interview_1'][0])));
            // echo '</pre>';
        }

        ?>
        </tbody>
    </table>

<?php
}

function today_page(){
    $tomorrow = strtotime('tomorrow') - 1;
    //var_dump($tomorrow);
    $args_unconfirmed_users = array(
        'fields' => 'ID',
        'meta_query' => array(
            array(
                'key' => 'interview_status',
                'value' => 'unconfirm',
            ),
        ),
    );
    $unconfirmed_users = new WP_User_Query($args_unconfirmed_users);

    $args = array(
        'post_type' => 'interview',
        'author__in' => $unconfirmed_users->get_results(),
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'date_interview_1',
                'value' => array(time() , $tomorrow ),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'

            ),
            array(
                'key' => 'date_interview_2',
                'value' => array(time() , $tomorrow),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ),
            array(
                'key' => 'date_interview_3',
                'value' => array(time() , $tomorrow),
                'compare' => 'BETWEEN',
                'type' => 'NUMERIC'
            ),
        ),
    );
    $interviews = new WP_Query($args);
    //var_dump($interviews);
    //wp-list-table widefat fixed striped posts
    ?>
    <table id="interview-table" class="wp-list-table widefat fixed striped posts">
        <tbody>
        <tr>
            <th width="5%">#</th>
            <th width="10%">Nickname</th>
            <th width="10%">Skype</th>
            <th width="10%">Tel</th>
            <th id="date_interview_1" width="15%">Date1</th>
            <th id="date_interview_2" width="15%">Date2</th>
            <th id="date_interview_3" width="15%">Date3</th>
            <th width="10%">Confirm</th>
        </tr>

        <?php

        //var_dump()
        foreach ($interviews->posts as $key => $post) {
            $post_meta = get_post_meta($post->ID);
            ?>
            <tr>
                <td><?php echo $key + 1 ?></td>
                <td><?php echo get_userdata($post->post_author)->display_name ?></td>
                <td><?php echo $post_meta['skype_id'][0]?></td>
                <td><?php echo $post_meta['tel'][0] ?></td>
                <td>
                    <?php

                    $str = $post_meta['date_interview_1'][0];
                    if ($str > time() && $str < $tomorrow){
                        echo '<strong><u>'. date('m/d/Y g:i A',$str).'</strong></u>';
                    } elseif ($str < time()){
                        ?>
                        <label style="color: red">Expired</label>
                        <?php
                    }else{
                        echo date('m/d/Y g:i A',$str);
                    }


                    ?>
                </td>
                <td>
                    <?php

                    $str = $post_meta['date_interview_2'][0];
                    if ($str > time() && $str < $tomorrow){
                        echo '<strong><u>'. date('m/d/Y g:i A',$str).'</strong></u>';
                    } elseif ($str < time()){
                        ?>
                        <label style="color: red">Expired</label>
                    <?php
                    }else{
                        echo date('m/d/Y g:i A',$str);
                    }

                    ?>
                </td>
                <td>
                    <?php


                    $str = $post_meta['date_interview_3'][0];
                    if ($str > time() && $str < $tomorrow){
                        echo '<strong><u>'. date('m/d/Y g:i A',$str).'</strong></u>';
                    } elseif ($str < time()){
                        ?>
                        <label style="color: red">Expired</label>
                    <?php
                    }else{
                        echo date('m/d/Y g:i A',$str);
                    }

                    ?>
                </td>
                <td><button class="confirm-user" data-user-id="<?php echo $post->post_author; ?>">Confirm</button></td>
            </tr>

            <?php
        }

        ?>
        </tbody>
    </table>

<?php
}
// mt_sublevel_page() displays the page content for the first submenu
// of the custom Test Toplevel menu
//function mt_sublevel_page()
//{
//    echo "<h2>" . __('Test Sublevel', 'menu-test') . "</h2>";
//}
//
//// mt_sublevel_page2() displays the page content for the second submenu
//// of the custom Test Toplevel menu
//function mt_sublevel_page2()
//{
//    echo "<h2>" . __('Test Sublevel2', 'menu-test') . "</h2>";
//}


class interview extends AE_PostAction
{
    function __construct()
    {
        $this->post_type = 'interview';

        $this->interview = new AE_Posts('interview');

        $this->add_ajax('fetch-interview', 'fetch_interview');
        $this->add_ajax('confirm-interview', 'activite_user');


    }
 function activite_user(){


     if ( ae_user_role() == 'administrator'){
         $confirmed_user_id = $_POST['user_id'];
         $result = update_user_meta($confirmed_user_id,'interview_status','confirmed');
         if ($result){
             wp_send_json(array(
                 'status' => true,
                 'msg' => 'success'
             ));
         }
     }else{
         wp_send_json(array(
            'status' => false,
             'msg' => 'deny'
         ));
     }
     wp_die();
 }
    function fetch_interview()
    {

        $request = $_REQUEST;
        global $ae_post_factory, $user_ID;
        $place = $ae_post_factory->get($this->post_type);

        // var_dump($request['ID']);
        // sync place
        if ($request['ID'] == "") {
            $request['method'] = 'create';
            unset($request['ID']);
        } else {
            $request['method'] = 'update';
        }

        //var_dump(empty($request['id']));
        //var_dump($ae_post_factory);
        //wp_die('fadsfad');
        $request['date_interview_1'] = strtotime($request['date_interview_1']);
        $request['date_interview_2'] = strtotime($request['date_interview_2']);
        $request['date_interview_3'] = strtotime($request['date_interview_3']);
        //var_dump($request);
        $result = $place->sync($request);

        if (is_wp_error($result)) {

            wp_send_json(array(

                'success' => false,

                'msg' => $result->get_error_message()

            ));

        } else {

            $message = __("Update interview successful.", ET_DOMAIN);

            if ($request['method'] == 'create') {

                $message = __("Create interview successful.", ET_DOMAIN);

            }

            wp_send_json(array(

                'success' => true,

                'msg' => $message

            ));

        }


        // wp_die();
    }


}

new interview();

