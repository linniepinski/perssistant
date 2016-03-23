<?php

class login_back
{
    public static function Init()
    {
        add_action('wp_head', array('login_back', 'temp_status_login'));
        add_action('wp_ajax_login_check', array('login_back', 'login_check_fn'));
        add_action('wp_ajax_nopriv_login_check', array('login_back', 'login_check_fn'));

    }

    public static function temp_status_login()
    {
        wp_enqueue_script('login_back_plugin', plugin_dir_url(__FILE__) . 'script.js', array('jquery'));
        wp_localize_script('login_back_plugin', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
        $current_user = wp_get_current_user();
        ?>
        <script type="text/javascript">
            var is_login =<?php if ($current_user->ID != 0) {
                echo 'true';
            } else {
                echo 'false';
            } ?>
        </script>
        <?php
    }

    public static function login_check_fn()
    {
        $current_user = wp_get_current_user();
        if ($current_user->ID != 0){
            $response = array(
                'is_login' => true
            );
            wp_send_json($response);
        }else{
            $response = array(
                'is_login' => false
            );
            wp_send_json($response);
        }
        wp_die();
    }
}