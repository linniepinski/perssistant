<?php

/**
 * @package AppEngine Paymill
 */

/*
Plugin Name: AE Paymill
Plugin URI: http://enginethemes.com/
Description: Integrates the Paymill payment gateway to your DirectoryEngine, FreelanceEngine site
Version: 1.0
Author: enginethemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/

require_once dirname(__FILE__) . '/lib/Services/Paymill.php';
require_once dirname(__FILE__) . '/update.php';

add_filter('ae_admin_menu_pages', 'ae_paymill_add_settings');
function ae_paymill_add_settings($pages) {
    $sections = array();
    $options = AE_Options::get_instance();
    
    // $api_link = " <a class='find-out-more' target='_blank' href='https://dashboard.paymill.com/account/apikeys' >" . __("Find out more", ET_DOMAIN) . " <span class='icon' data-icon='i' ></span></a>";
    
    /**
     * ae fields settings
     */
    $sections = array(
        'args' => array(
            'title' => __("Paymill API", ET_DOMAIN) ,
            'id' => 'paymill_field',
            'icon' => 'F',
            'class' => ''
        ) ,
        
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Paymill API", ET_DOMAIN) ,
                    'id' => 'paymill-secret-key',
                    'class' => '',
                    'desc' => __('The Paymill API by providing one of your API keys in the request.', ET_DOMAIN) ,
                    'name' => 'paymill'
                ) ,
                'fields' => array(
                    array(
                        'id' => 'paymill_secret_key',
                        'type' => 'text',
                        'label' => __("Private Key", ET_DOMAIN) ,
                        'name' => 'private_key',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'paymill_publishable_key',
                        'type' => 'text',
                        'label' => __("Public Key", ET_DOMAIN) ,
                        'name' => 'public_key',
                        'class' => ''
                    )
                )
            )
        )
    );
    
    $temp = new AE_section($sections['args'], $sections['groups'], $options);
    
    $paymill_setting = new AE_container(array(
        'class' => 'field-settings',
        'id' => 'settings',
    ) , $temp, $options);
    
    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('Paymill', ET_DOMAIN) ,
            'menu_title' => __('PAYMILL', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'ae-paymill',
            'icon' => '$',
            'desc' => __("Integrate the Paymill payment gateway to your site", ET_DOMAIN)
        ) ,
        'container' => $paymill_setting
    );
    
    return $pages;
}

add_filter('ae_support_gateway', 'ae_paymill_add_support');
function ae_paymill_add_support($gateways) {
    $gateways['paymill'] = 'Paymill';
    return $gateways;
}

add_action('after_payment_list', 'ae_paymill_render_button');
function ae_paymill_render_button() {
    $paymill_key = ae_get_option('paymill');
    if (!$paymill_key['public_key'] || !$paymill_key['private_key']) return false;
?>
    <li>
        <span class="title-plan paymill-payment" data-type="paymill">
            <?php
    _e("Paymill", ET_DOMAIN); ?>
            <span><?php
    _e("Send your payment to our paymill account", ET_DOMAIN); ?></span>
        </span>
        <a href="#" class="btn btn-submit-price-plan other-payment" data-type="paymill"><?php
    _e("Select", ET_DOMAIN); ?></a>
    </li>
    
<?php
}

add_action('wp_print_scripts', 'ae_paymill_script');
function ae_paymill_script() {
    if (is_page_template('page-post-place.php') || is_page_template('page-submit-project.php')) {
        
        global $user_ID, $ae_post_factory;
        $ae_pack = $ae_post_factory->get('pack');
        $packs = $ae_pack->fetch();
        
        wp_enqueue_script('paymill', 'https://bridge.paymill.com/');
        wp_enqueue_script('ae_paymill', plugin_dir_url(__FILE__) . '/assets/paymill.js', array(
            'underscore',
            'backbone',
            'appengine'
        ) , '1.0', true);
        wp_enqueue_style('ae_paymill', plugin_dir_url(__FILE__) . '/assets/paymill.css', array() , '1.0');
        
        $paymill_key = ae_get_option('paymill');
        wp_localize_script('ae_paymill', 'ae_paymill', array(
            'public_key' => $paymill_key['public_key'],
            'currency' => ae_get_option('currency') ,
            'card_number_msg' => __('The Credit card number is invalid.', ET_DOMAIN) ,
            'name_card_msg' => __('The name on card is invalid.', ET_DOMAIN) ,
            'transaction_success' => __('The transaction completed successfull!.', ET_DOMAIN) ,
            'transaction_false' => __('The transaction was not completed successfull!.', ET_DOMAIN) ,
            'unknow_error' => __("An unknown error has been occur. Please contact admin for details.", ET_DOMAIN) ,
            'exp_msg' => __("Card is no longer valid or not anymore", ET_DOMAIN) ,
            'cvc_msg' => __(" Invalid checking number", ET_DOMAIN) ,
            'pack' => $packs
        ));
    }
}

add_action('wp_footer', 'ae_paymill_form_template');
function ae_paymill_form_template() {
    $paymill_key = ae_get_option('paymill');
    include_once dirname(__FILE__) . '/form-template.php';
?>
    <script type="text/javascript"> var PAYMILL_PUBLIC_KEY ='<?php
    echo $paymill_key["public_key"]; ?>';</script>
    <?php
}

add_filter('ae_setup_payment', 'ae_paymill_setup_payment', 10, 3);
function ae_paymill_setup_payment($response, $paymentType, $order) {
    
    if ($paymentType == 'PAYMILL') {
        
        $order_pay = $order->generate_data_to_pay();
        $token = $_POST['token'];
        $job_id = $order_pay['product_id'];
        $paymill_api = ae_get_option('paymill');
        $description    = isset($_POST['description']) ? $_POST['description'] : '';

        global $user_email;
        
        try {
            
            $params = array(
                'token' => $token
            );
            $apiKey = $paymill_api['private_key'];
            $apiEndpoint = 'https://api.paymill.com/v2.1/';
            $paymill = new Services_Paymill_Transactions($apiKey, $apiEndpoint);
            
            //$creditcard = $paymentsObject->create($params);
            
            //$Paymill->update();
            //Paymill::setApiKey($paymill['secret_key']);
            $order_pay = $order->generate_data_to_pay();
            $charge = $paymill->create(array(
                'amount' => $order_pay['total'] * 100,
                'currency' => $order_pay['currencyCodeType'],
                'token' => $token,
                'description' => $description
            ));
            
            $returnURL = et_get_page_link('process-payment', array(
                'paymentType' => 'paymill'
            ));
            
            if (isset($charge['error']) || !isset($charge['id'])) {
                $response = array(
                    'success' => false,
                    'msg' => __('Transaction was not completed successfully!', ET_DOMAIN) ,
                    'data' => array(
                        'url' => $returnURL
                    ) ,
                    'paymentType' => 'paymill',
                    'charge' => $charge
                );
            } else if (isset($charge['id'])) {
                
                $id = $charge['id'];
                $token = md5($id);
                $order->set_payment_code($id);
                $order->set_payer_id($id);
                $order->update_order();
                
                $returnURL.= '&token=' . $token;
                
                $response = array(
                    'success' => true,
                    'data' => array(
                        'url' => $returnURL,
                        'msg' => __('Transaction completed successfull!', ET_DOMAIN)
                    ) ,
                    'paymentType' => 'paymill'
                );
            }
        }
        catch(Exception $e) {
            $value = $e->getJsonBody();
            
            $response = array(
                'success' => false,
                'msg' => $value['error']['message'],
                'paymentType' => 'paymill'
            );
        }
    }
    return $response;
}


add_filter( 'ae_process_payment', 'ae_paymill_process_payment', 10 ,2 );
function ae_paymill_process_payment ( $payment_return, $data) {
    $payment_type = $data['payment_type'];
    $order = $data['order'];
    if( $payment_type == 'paymill') {
        if( isset($_REQUEST['token']) &&  $_REQUEST['token'] == md5($order->get_payment_code()) ) {

            $payment_return =   array (
                'ACK'           => true,
                'payment'       =>  'paymill',
                'payment_status' =>'Completed'
            );
            $order->set_status ('publish');
            $order->update_order();
            
        } else {                
            $payment_return =   array (
                'ACK'           => false,
                'payment'       =>  'paymill',
                'payment_status' =>'fail',
                'msg'   => __('Paymill payment method false.', ET_DOMAIN)
                
            );
        }
    }  
    
    return $payment_return;

}


/**
 * hook to add translate string to plugins 
 * @param Array $entries Array of translate entries
 * @since 1.0
 * @author Dakachi
 */
add_filter( 'et_get_translate_string', 'ae_paymill_add_translate_string' );
function ae_paymill_add_translate_string ($entries) {
    $lang_path = dirname(__FILE__).'/lang/default.po';
    if(file_exists($lang_path)) {
        $pot        =   new PO();
        $pot->import_from_file($lang_path, true );
        
        return  array_merge($entries, $pot->entries);    
    }
    return $entries;
}