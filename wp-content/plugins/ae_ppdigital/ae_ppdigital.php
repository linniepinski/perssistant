<?php

/**
 * @package AppEngine Paymill
 */

/*
Plugin Name: AE PayPal Digital Goods
Plugin URI: http://enginethemes.com/
Description: Integrates the Paypal Digital Goods payment gateway to your DirectoryEngine, FreelanceEngine site
Version: 1.0
Author: enginethemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/

require_once dirname(__FILE__) . '/update.php';
require_once (dirname(__FILE__) . '/lib/paypal-digital-goods.class.php');
require_once (dirname(__FILE__) . '/lib/paypal-purchase.class.php');

require_once (dirname(__FILE__) . '/paypal-express.php');

add_filter('ae_admin_menu_pages', 'ae_ppdigital_add_settings');
function ae_ppdigital_add_settings($pages) {
    $sections = array();
    $options = AE_Options::get_instance();
    
    $api_link = " <a class='find-out-more' target='_blank' href='https://dashboard.ppdigital.com/account/apikeys' >" . __("Find out more", ET_DOMAIN) . " <span class='icon' data-icon='i' ></span></a>";
    
    /**
     * ae fields settings
     */
    $sections = array(
        'args' => array(
            'title' => __("Paypal Digital Goods API", ET_DOMAIN) ,
            'id' => 'meta_field',
            'icon' => 'F',
            'class' => ''
        ) ,
        
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Paypal Digital Goods API", ET_DOMAIN) ,
                    'id' => 'ppdigital-key',
                    'class' => '',
                    'desc' => __('The Paypal Digital Goods API by providing one of your API keys in the request.', ET_DOMAIN) ,
                    'name' => 'ppdigital'
                ) ,
                'fields' => array(
                    array(
                        'id' => 'Title',
                        'type' => 'text',
                        'label' => __("Title", ET_DOMAIN) ,
                        'name' => 'title',
                        'class' => '',
                        'default' => __("Papal DG", ET_DOMAIN)
                    ) ,
                    array(
                        'id' => 'Description',
                        'type' => 'text',
                        'label' => __("Description", ET_DOMAIN) ,
                        'name' => 'desc',
                        'class' => '', 
                        'default' => __("Send your payment to our Paypal account", ET_DOMAIN)
                    ) ,
                    array(
                        'id' => 'username',
                        'type' => 'text',
                        'label' => __("Username", ET_DOMAIN) ,
                        'name' => 'username',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'password',
                        'type' => 'text',
                        'label' => __("Password", ET_DOMAIN) ,
                        'name' => 'password',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'signature',
                        'type' => 'text',
                        'label' => __("Signature", ET_DOMAIN) ,
                        'name' => 'signature',
                        'class' => ''
                    )
                )
            )
        )
    );
    
    $temp = new AE_section($sections['args'], $sections['groups'], $options);
    
    $ppdigital_setting = new AE_container(array(
        'class' => 'field-settings',
        'id' => 'settings',
    ) , $temp, $options);
    
    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('Paypal Digital Goods Settings', ET_DOMAIN) ,
            'menu_title' => __('PAYPAL DIGITAL', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'ae-ppdigital',
            'icon' => '$',
            'desc' => __("Integrate the Paypal Digital Goods payment gateway to your site", ET_DOMAIN)
        ) ,
        'container' => $ppdigital_setting
    );
    
    return $pages;
}

add_filter('ae_support_gateway', 'ae_ppdigital_add');
function ae_ppdigital_add($gateways) {
    $gateways['ppdigital'] = 'PAYPAL DIGITAL';
    return $gateways;
}

add_action('after_payment_list', 'ae_ppdigital_render_button');
function ae_ppdigital_render_button() {
    $ppdigital_key = ae_get_option('ppdigital');
    if (!isset($ppdigital_key['username'])) return;
    if (!$ppdigital_key['username'] || !$ppdigital_key['password'] || !$ppdigital_key['signature']) return false;

    if(!isset($ppdigital_key['title']) || !$ppdigital_key['title'])  $ppdigital_key['title'] = __("Paypal DG", ET_DOMAIN);
    if(!isset($ppdigital_key['desc']) || !$ppdigital_key['desc'])  $ppdigital_key['desc'] = __("Send your payment to our Paypal account", ET_DOMAIN);
?>
    <li id="ppdigital-payment">
        <form action="" method="post" id="ppexpress_form" class="ppexpress-form" style="margin-top:0;">
            <span class="title-plan ppdigital-payment" data-type="ppdigital">
                <?php echo $ppdigital_key['title']; ?>
                <span><?php echo $ppdigital_key['desc']; ?></span>
            </span>
            <button href="#" class="btn btn-submit-price-plan other-payment" id="ppdigital-button" data-type="ppdigital"><?php _e("Select", ET_DOMAIN); ?></button>
        </form>
    </li>
    
<?php
}

add_action('wp_print_scripts', 'ae_ppdigital_script');
function ae_ppdigital_script() {
    if (is_page_template('page-post-place.php') || is_page_template('page-submit-project.php')) {
        wp_enqueue_script('ppexpress.checkout', '//www.paypalobjects.com/js/external/dg.js', array(
            'jquery'
        ));
        wp_enqueue_script('ppexpress', plugin_dir_url(__FILE__) . '/asset/ppexpress.js', array(
            'jquery',
            'appengine',
            'ppexpress.checkout'
        ));
    }
}

add_filter('ae_setup_payment', 'ae_ppdigital_setup_payment', 10, 3);
function ae_ppdigital_setup_payment($response, $paymentType, $order) {
    
    if ($paymentType == 'PPDIGITAL') {
        try {
            $order_pay = $order->generate_data_to_pay();
            
            if ($order_pay['total'] <= 0) return $response;
            
            $checkout = new AE_PPExpressVisitor();
            $response = $checkout->SetExpressCheckoutDG($order_pay);
            $ack = strtoupper($response['ACK']);
            if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
                $token = urldecode($response["TOKEN"]);
                $response['url'] = $checkout->payment->_dg_url . $token;
            }
        }
        catch(Exception $e) {
            $value = $e->getJsonBody();
            $response = array(
                'success' => false,
                'msg' => $value['error']['message'],
                'paymentType' => 'ppdigital'
            );
        }
    }
    return $response;
}

/**
 * hook to process payment and process order by pp digital good
 * @param Array $payment_return
 * @since 1.0
 * @author Dakachi
 */
add_filter('ae_process_payment', 'ae_ppdigital_process_payment', 10, 2);
function ae_ppdigital_process_payment($payment_return, $data) {
    $payment_type = $data['payment_type'];
    $order = $data['order'];
    if ($payment_type == 'ppdigital') {
        $ack = false;
        if (isset($_REQUEST['token']) && isset($_REQUEST['PayerID'])) {
            $token = $_REQUEST['token'];
            $payerID = $_REQUEST['PayerID'];
            
            $checkout = new AE_PPExpressVisitor();
            
            // generate order data
            $order_pay = $order->generate_data_to_pay();
            
            // confirm payment
            $response = $checkout->ConfirmPayment($token, $payerID, $order_pay);
            $ack = strtoupper($response['ACK']);
            if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
                $payment_return = array(
                    'ACK' => true,
                    'payment' => 'ppdigital',
                    'payment_status' => 'Completed'
                );
                
                // update order
                $order->set_payment_code($token);
                $order->set_payer_id($payerID);
                $order->set_status('publish');
                $order->update_order();
                
                $session = et_read_session();
                $link = get_permalink($session['ad_id']);
                
                echo '
                    <script type="text/javascript">
                        setTimeout (function () {
                            if (window.opener) {
                                window.opener.location.href = "' . $link . '";
                                window.close();
                            } }, 3000 );
                    </script>';
                $ack = true;
            }
        }
        
        if (!$ack) {
            echo '
                <script type="text/javascript">
                setTimeout (function () {
                    if (window.opener) {
                        window.opener.location.reload();
                        window.close();
                    } }, 3000 );
                </script>
            ';
        }
        echo '<style>
                .redirect-content {
                    position: absolute;
                    left : 100px;
                }
                .main-center {
                    margin: 0 auto;
                    width: auto !important;
                }

            </style>';
    }
    return $payment_return;
}

/**
 * hook to add translate string to plugins
 * @param Array $entries Array of translate entries
 * @since 1.0
 * @author Dakachi
 */
add_filter('et_get_translate_string', 'ae_ppdigital_add_translate_string');
function ae_ppdigital_add_translate_string($entries) {
    $lang_path = dirname(__FILE__) . '/lang/default.po';
    if (file_exists($lang_path)) {
        $pot = new PO();
        $pot->import_from_file($lang_path, true);
        
        return array_merge($entries, $pot->entries);
    }
    return $entries;
}
