<?php
/*
Plugin Name: Account Balance Perssistance
Plugin URI: http://#
Description: t
Version: 0.1
Author: A
Author URI:
License: GPLv2 or later
*/
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}

define('ACCOUNT_BALANCE_PLUGIN__VERSION', '0.1');
define('ACCOUNT_BALANCE_PLUGIN__MINIMUM_WP_VERSION', '3.2');
define('ACCOUNT_BALANCE_PLUGIN__PLUGIN_URL', plugin_dir_url(__FILE__));
define('ACCOUNT_BALANCE_PLUGIN__PLUGIN_DIR', plugin_dir_path(__FILE__));
//define( 'ACCOUNT_BALANCE_PLUGIN_DELETE_LIMIT', 100000 );
register_deactivation_hook(__FILE__, 'my_deactivation');
register_activation_hook(__FILE__, 'my_activation');

function my_deactivation()
{
    //wp_clear_scheduled_hook( 'refresh_balance_event' );
}

//wp_clear_scheduled_hook( 'refresh_balance_event' );

function my_activation()
{
    //wp_schedule_event(time(), 'hourly', array('account_balance','my_hourly_event') );
    add_action('init', 'register_refresh_balance_event');

    add_action('refresh_balance_event', 'auto_refresh_balance');
    add_filter('cron_schedules', 'cron_add_weekly');
}

add_action('init', 'register_refresh_balance_event');

add_action('refresh_balance_event', 'auto_refresh_balance');
add_filter('cron_schedules', 'cron_add_weekly');
add_filter('cron_schedules', 'cron_add_few_minute');

function cron_add_weekly($schedules)
{
    // Adds once weekly to the existing schedules.
    $schedules['weekly'] = array(
        'interval' => 604800,
        'display' => __('Once Weekly')
    );
    return $schedules;
}

function cron_add_few_minute($schedules)
{
    // Adds once weekly to the existing schedules.
    $schedules['wtf'] = array(
        'interval' => 15000,
        'display' => __('Once Wtf')
    );
    return $schedules;
}

function auto_refresh_balance()
{
    global $wpdb;
    //wp_die('123423432');

    $query = $wpdb->get_results(
        "SELECT id,project_id,employer_id,freelancer_id,trace_time

FROM wp_trace
INNER JOIN wp_postmeta
ON wp_trace.project_id = wp_postmeta.post_id
WHERE `image_url` != 'deleted'
AND `meta_key` = 'type_budget'
AND `status` IS NULL
"
    );

    foreach ($query as $item) {

        add_user_meta($item->freelancer_id,'account_cash_balance',0,true);
        add_user_meta($item->employer_id,'account_cash_balance',0,true);

        $project_bid = get_post_meta($item->project_id, 'accepted', true);

        echo '<pre>';
        var_dump($item);
        $bid_budget_amount_hour = (float)get_post_meta($project_bid, 'bid_budget', true);
        //var_dump($bid_budget_amount_hour);
        $payment_amount = (($bid_budget_amount_hour / 60 / 60) * $item->trace_time ) * 100;
        var_dump($payment_amount);
        echo '</pre>';

        $current_balance_freelancer = (float)get_user_meta($item->freelancer_id,'account_cash_balance',true);
        $current_balance_employer = (float)get_user_meta($item->employer_id,'account_cash_balance',true);

        var_dump($current_balance_freelancer);
        var_dump($current_balance_employer);

        $new_balance_freelancer = $current_balance_freelancer + $payment_amount;
        $new_balance_employer = $current_balance_employer - $payment_amount;

        var_dump($new_balance_freelancer);
        var_dump($new_balance_employer);

        $description_freelancer = '';
        $description_employer ='';

        $source = 'autotransaction';
        $response = 'success';
        $purpose = 'autotransaction';

        $wpdb->query('START TRANSACTION');

        $add_to_history_freelancer = wpdb_add_history_account_balance($item->freelancer_id, $item->employer_id, $current_balance_freelancer, $payment_amount, $new_balance_freelancer, $description_freelancer, $source, $response, $purpose, $item->project_id);
        $add_to_history_emloyer = wpdb_add_history_account_balance($item->employer_id, $item->freelancer_id, $current_balance_employer, (-$payment_amount), $new_balance_employer, $description_employer, $source, $response, $purpose, $item->project_id);

        $update_balance_freelancer = $wpdb->query(
            "UPDATE wp_usermeta SET meta_value = {$new_balance_freelancer}
WHERE user_id = {$item->freelancer_id} AND meta_key = 'account_cash_balance'
                       "
        );

        $update_balance_employer = $wpdb->query(
            "UPDATE wp_usermeta SET meta_value = {$new_balance_employer}
     WHERE user_id = {$item->employer_id}
AND meta_key = 'account_cash_balance'

      "  );
        $success_status = $wpdb->query("UPDATE wp_trace SET status = 'success'
 WHERE id = {$item->id}");

        if ($success_status && $update_balance_freelancer && $update_balance_employer && $add_to_history_freelancer && $add_to_history_emloyer ) {
            //$wpdb->query('ROLLBACK'); // // something went wrong, Rollback

            $wpdb->query('COMMIT'); // if you come here then well done
            var_dump('OK');
        } else {
            var_dump($update_balance_freelancer);
            var_dump($update_balance_employer);
            var_dump($add_to_history_freelancer);
            var_dump($add_to_history_emloyer);
            $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
            var_dump('ROLLBACK');

        }
        wp_cache_flush();
    }
   // wp_mail('andrey0214@gmail.com', 'Automatic email', 'Automatic scheduled email from WordPress.');


}

function auto_refresh_balance2()
{
    global $wpdb;
    //wp_die('123423432');
//    $query = $wpdb->get_results(
//        "SELECT *
//	                  FROM `wp_trace`
//	                  WHERE `image_url` != 'deleted'
//	                  AND `status` != null
//
//	                "
//    );
//    foreach ($query as $item){
//        echo '<pre>';
//        var_dump($item);
//        echo '</pre>';
//
//    }

    wp_mail('andrey0214@gmail.com', 'Automatic email', 'Automatic scheduled email from WordPress.');

//    $wpdb->query('START TRANSACTION');
//    $result1 = $wpdb->delete( $table, $where, $where_format = null );
//    $resul2 = $wpdb->delete( $table, $where, $where_format = null );
//    if($result1 && $result2) {
//        $wpdb->query('COMMIT'); // if you come here then well done
//    }
//    else {
//        $wpdb->query('ROLLBACK'); // // something went wrong, Rollback
//    }
}


function register_refresh_balance_event()
{

    if (!wp_next_scheduled('refresh_balance_event')) {

        wp_schedule_event(time(), 'hourly', 'refresh_balance_event');
    }
}


//register_activation_hook( __FILE__, array( 'Akismet', 'plugin_activation' ) );
//register_deactivation_hook( __FILE__, array( 'Akismet', 'plugin_deactivation' ) );

require_once(ACCOUNT_BALANCE_PLUGIN__PLUGIN_DIR . 'class.account_balance.php');
//require_once( ACCOUNT_BALANCE_PLUGIN__PLUGIN_DIR . 'class.akismet-widget.php' );

add_action('init', array('account_balance', 'init'));


add_option('paymill_secret_key');
add_option('paymill_public_key');
if (is_admin()) {
    //require_once( AKISMET__PLUGIN_DIR . 'class.akismet-admin.php' );
    //add_action( 'init', array( 'Akismet_Admin', 'init' ) );
}

//add wrapper class around deprecated akismet functions that are referenced elsewhere
//require_once( AKISMET__PLUGIN_DIR . 'wrapper.php' );