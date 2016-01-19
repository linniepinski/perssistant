<?php

/**
 * @author Tong Quang Dat
 * @copyright 2015
 * @package AppEngine Payment
 */

/*
Plugin Name: AE PayuMoney
Plugin URI: http://enginethemes.com/
Description: Integrates the PayuMoney payment gateway to your DirectoryEngine, FreelanceEngine site
Version: 1.0
Author: EngineThemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/

require_once dirname(__FILE__) . '/update.php';
//setup admin option
add_filter('ae_admin_menu_pages', 'ae_payu_add_settings');
function ae_payu_add_settings($pages) {
    $sections = array();
    $options = AE_Options::get_instance();
    
    // $api_link = " <a class='find-out-more' target='_blank' href='https://dashboard.paymill.com/account/apikeys' >" . __("Find out more", ET_DOMAIN) . " <span class='icon' data-icon='i' ></span></a>";
    
    
    
    /**
     * ae fields settings
     */
    $sections = array(
        'args' => array(
            'title' => __("PayuMoney API", ET_DOMAIN) ,
            'id' => 'payu_field',
            'icon' => 'F',
            'class' => ''
        ) ,
        
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("PayuMoney API", ET_DOMAIN) ,
                    'id' => 'payu-secret-key',
                    'class' => '',
                    'desc' => __('for test mode <b>Merchant ID: JBZaLc | SALT: GQs7yium</b>', ET_DOMAIN) ,
                    'name' => 'payu'
                ) ,
                'fields' => array(
                    array(
                        'id' => 'payu_Merchant_ID',
                        'type' => 'text',
                        'label' => __("Merchant ID PayuMoney", ET_DOMAIN) ,
                        'name' => 'payu_merchan_ID',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'payu_salt',
                        'type' => 'text',
                        'label' => __("Salt Key PayuMoney", ET_DOMAIN) ,
                        'name' => 'payu_salt',
                        'class' => ''
                    )
                )
            )
        )
    );
    
    $temp = new AE_section($sections['args'], $sections['groups'], $options);
    
    $payu_setting = new AE_container(array(
        'class' => 'field-settings',
        'id' => 'settings',
    ) , $temp, $options);
    
    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('PayuMoney', ET_DOMAIN) ,
            'menu_title' => __('PAYUMONEY', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'ae-payu',
            'icon' => '$',
            'icon_class' => 'fa fa-inr',
            'desc' => __("Integrate the PayU payment gateway to your site", ET_DOMAIN)
        ) ,
        'container' => $payu_setting
    );
    
    return $pages;
}

add_filter('ae_support_gateway', 'ae_payu_add_support');
function ae_payu_add_support($gateways) {
    $gateways['payu'] = 'PayuMoney';
    return $gateways;
}

//add button front end
add_action('after_payment_list', 'ae_payu_render_button');
function ae_payu_render_button() {
    $payu_key = ae_get_option('payu');
    
    // if (!$payu_key['payu_merchan_ID'] || !$payu_key['payu_salt']) return false;
    
?>
    <li>
        <span class="title-plan payu-payment" data-type="payu">
            <?php
    _e("PayU", ET_DOMAIN); ?>
            <span><?php
    _e("Send your payment to our PayUMoney account", ET_DOMAIN); ?></span>
        </span>
        <a href="#" id="" class="btn btn-submit-price-plan other-payment" data-type="payu"><?php
    _e("Select", ET_DOMAIN); ?></a>
    </li>
    
<?php
}

add_filter('ae_setup_payment', 'ae_payu_setup_payment', 10, 3);
function ae_payu_setup_payment($response, $paymentType, $order) {
    
    if ($paymentType == 'PAYU') {
        
        $order_pay = $order->generate_data_to_pay();
        $order_id = $order_pay['ID'];
        $payu_info = ae_get_option('payu');
        $productinfo = array_pop($order_pay['products']);
        $test_mode = ET_Payment::get_payment_test_mode();
        $payu_url = 'https://live.payu.in/_payment';
        if ($test_mode) {
            $payu_url = 'https://test.payu.in/_payment';
        }
        
        $hash_data['key'] =  $payu_info['payu_merchan_ID'];
        $hash_data['txnid'] = substr(hash('sha256', mt_rand() . microtime()) , 0, 20);
         // Unique alphanumeric Transaction ID
        $hash_data['amount'] = $productinfo['AMT'];
        $hash_data['productinfo'] = $productinfo['NAME'];
        $hash_data['firstname'] = $_POST['payu_firstname'];
        $hash_data['email'] = $_POST['payu_email'];
        $hash_data['phone'] = $_POST['payu_phone'];
        $hash_data['hash'] = ae_calculate_hash_before_transaction($hash_data);
        
        //$response = json_encode($hash_data);
        if ($hash_data['email'] != "" && $hash_data['firstname'] != "") {
            $response = array(
                'success' => true,
                'data' => array(
                    'url' => $payu_url,
                    'ACK' => true,
                    'hash' => $hash_data['hash'],
                    'txnid' => $hash_data['txnid'],
                    'key' => $hash_data['key'],
                    'amount' => $hash_data['amount'],
                    'phone' => $hash_data['phone'],
                    'firstname' => $hash_data['firstname'],
                    'productinfo' => $hash_data['productinfo'],
                    'email' => $hash_data['email'],
                    'surl' => et_get_page_link('process-payment', array(
                        'paymentType' => 'payu'
                    )) ,
                    'furl' => et_get_page_link('process-payment', array(
                        'paymentType' => 'payu'
                    )) ,
                    'salt' => $payu_info['payu_salt'],
                ) ,
                'paymentType' => 'PAYU'
            );
        } else {
            $response = array(
                'success' => false,
                'data' => array(
                    'url' => site_url('post-place') ,
                    'ACK' => false
                )
            );
        }
    }
    return $response;
}
add_action('wp_print_scripts', 'ae_payu_script');
function ae_payu_script() {
    if (is_page_template('page-post-place.php') || is_page_template('page-submit-project.php')) {
        wp_enqueue_script('ae_payu', plugin_dir_url(__FILE__) . 'assets/payu.js', array(
            'underscore',
            'backbone',
            'appengine'
        ) , '1.0', true);
        wp_enqueue_style('ae_payu', plugin_dir_url(__FILE__) . 'assets/payu.css', array() , '1.0');
        $test_mode = ET_Payment::get_payment_test_mode();
        $payu_url = 'https://live.payu.in/_payment';
        if ($test_mode) {
            $payu_url = 'https://test.payu.in/_payment';
        }
        wp_localize_script('ae_payu', 'ae_payu', array(
            'currency' => ae_get_option('currency') ,
            'empty_field' => __('This Field Cannot be empty', ET_DOMAIN) ,
            'email_error' => __('Email not correct please check again', ET_DOMAIN) ,
            /*'transaction_success' => __('The transaction completed successfull!.', ET_DOMAIN) ,
            'transaction_false' => __('The transaction was not completed successfull!.', ET_DOMAIN) ,
            'unknow_error' => __("An unknown error has been occur. Please contact admin for details.", ET_DOMAIN) ,
            'exp_msg' => __("Card is no longer valid or not anymore", ET_DOMAIN) ,
            'cvc_msg' => __(" Invalid checking number", ET_DOMAIN) ,
            'pack' => $packs*/
        ));
    }
}

add_filter('ae_process_payment', 'ae_payu_process_payment', 10, 2);
function ae_payu_process_payment($payment_return, $data) {
    $payment_type = $data['payment_type'];
    $order = $data['order'];
    
    $order_data = $order -> get_order_data();
    //$data_info = array_pop($order_data['products']);
    $payu_info =  ae_get_option('payu');
    //get data return from payu
    if ($payment_type == 'payu') {

        $status = $_REQUEST['status'];
        $email = $_REQUEST['email'];
        $firstname = $_REQUEST['firstname'];
        $productinfo = $_REQUEST['productinfo'];
        $txnid = $_REQUEST['txnid'];
        if($order_data['total'] == $_REQUEST['amount']){
            $amount = $_REQUEST['amount'];
            //echo "success"."<br>";
        }
        
        $hash_verify = ae_verify_payu_hash( $payu_info['payu_salt'], $status, $email, $firstname, $productinfo, $amount, $txnid, $payu_info['payu_merchan_ID']);
    
        if ($_REQUEST['status'] == "success" && $_REQUEST['hash'] == $hash_verify) {
            
            $payment_return = array(
                'ACK' => true,
                'payment' => 'payu',
                'payment_status' => 'Completed'
            );
            $order->set_status('publish');
            $order->update_order();
        } else {
            $payment_return = array(
                'ACK' => false,
                'payment' => 'payu',
                'payment_status' => 'fail',
                'msg' => __('Paymill payment method false.', ET_DOMAIN)
            );
        }
    }
    
    return $payment_return;
    
    // return $hash_verify;
    
    
}

add_action('wp_footer', 'ae_payu_form_template');
function ae_payu_form_template() {
    
    // $paymill_key = ae_get_option('paymill');
    include_once dirname(__FILE__) . '/form-template.php';
}

function ae_verify_payu_hash($salt, $status, $email, $firstname, $productinfo, $amount, $txnid, $key) {
    
    $retHashSeq = $salt . '|' . $status . '|||||||||||' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
    
    $hash = strtolower(hash("sha512", $retHashSeq));
    return $hash;
}

function ae_calculate_hash_before_transaction($hash_data) {
    
    $payu_info =  ae_get_option('payu');
    $hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
    $hashVarsSeq = explode('|', $hashSequence);
    $hash_string = '';
    foreach ($hashVarsSeq as $hash_var) {
        $hash_string.= isset($hash_data[$hash_var]) ? $hash_data[$hash_var] : '';
        $hash_string.= '|';
    }
    
    $hash_string.= $payu_info ['payu_salt'];
    
    $hash = strtolower(hash('sha512', $hash_string));
    return $hash;
    
    /*$hash_sequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";
    $hash_vars_seq = explode('|', $hash_sequence);
    
    
    
    $hash_string = '';
    
    foreach($hash_vars_seq as $hash_var) {
    $hash_string .= isset($hash_data[$hash_var]) ? $hash_data[$hash_var] : '';
    $hash_string .= '|';
    }
    
    $hash_string .= 'GQs7yium';//ae_get_option('payu')['payu_salt'];
    $hash_data['hash'] = strtolower(hash('sha512', $hash_string));
    return $hash_string."<------->".$hash_data['hash'];*/
    
    //return $hash_data['hash'];
    
    
}
 // End calculate_hash_before_transaction()


/**
 * hook to add translate string to plugins 
 * @param Array $entries Array of translate entries
 * @since 1.0
 * @author Dakachi
 */
add_filter( 'et_get_translate_string', 'ae_payu_add_translate_string' );
function ae_payu_add_translate_string ($entries) {
    $lang_path = dirname(__FILE__).'/lang/default.po';
    if(file_exists($lang_path)) {
        $pot        =   new PO();
        $pot->import_from_file($lang_path, true );
        
        return  array_merge($entries, $pot->entries);    
    }
    return $entries;
}