<?php

/**
 * @package AppEngine Stripe
 */

/*
Plugin Name: AE Stripe
Plugin URI: http://enginethemes.com/
Description: Integrates the Stripe payment gateway to your Directory, Freelance site
Version: 1.1
Author: enginethemes
Author URI: http://enginethemes.com/
License: GPLv2 or later
Text Domain: enginetheme
*/

require_once dirname(__FILE__).'/lib/Stripe.php';
require_once dirname(__FILE__) . '/update.php';

add_filter('ae_admin_menu_pages', 'ae_stripe_add_settings');
function ae_stripe_add_settings($pages) {
    $sections = array();
    $options = AE_Options::get_instance();
    
    $api_link = " <a class='find-out-more' target='_blank' href='https://dashboard.stripe.com/account/apikeys' >" . __("Find out more", ET_DOMAIN) . " <span class='icon' data-icon='i' ></span></a>";
    
    /**
     * ae fields settings
     */
    $sections = array(
        'args' => array(
            'title' => __("Stripe API", ET_DOMAIN) ,
            'id' => 'meta_field',
            'icon' => 'F',
            'class' => ''
        ) ,
        
        'groups' => array(
            array(
                'args' => array(
                    'title' => __("Stripe API", ET_DOMAIN) ,
                    'id' => 'secret-key',
                    'class' => '',
                    'desc' => __('The Stripe API by providing one of your API keys in the request.', ET_DOMAIN) . $api_link,
                    'name' => 'stripe'
                ) ,
                'fields' => array(
                    array(
                        'id' => 'secret_key',
                        'type' => 'text',
                        'label' => __("Secret Key", ET_DOMAIN) ,
                        'name' => 'secret_key',
                        'class' => ''
                    ) ,
                    array(
                        'id' => 'publishable_key',
                        'type' => 'text',
                        'label' => __("Publishable Key", ET_DOMAIN) ,
                        'name' => 'publishable_key',
                        'class' => ''
                    )
                )
            )
            
            // array(
            //     'args' => array(
            //         'title' => __("Publishable Key", ET_DOMAIN) ,
            //         'id' => 'publishable-key',
            //         'class' => '',
            //         'desc' => ''
            //     ) ,
            //     'fields' => array(
            
            //     )
            // )
            
            
        )
    );
    
    $temp = new AE_section($sections['args'], $sections['groups'], $options);
    
    $stripe_setting = new AE_container(array(
        'class' => 'field-settings',
        'id' => 'settings',
    ) , $temp, $options);
    
    $pages[] = array(
        'args' => array(
            'parent_slug' => 'et-overview',
            'page_title' => __('Stripe', ET_DOMAIN) ,
            'menu_title' => __('STRIPE', ET_DOMAIN) ,
            'cap' => 'administrator',
            'slug' => 'ae-stripe',
            'icon' => '$',
            'desc' => __("Integrate the Stripe payment gateway to your site", ET_DOMAIN)
        ) ,
        'container' => $stripe_setting
    );
    
    return $pages;
}

add_filter( 'ae_support_gateway', 'ae_stripe_add' );
function ae_stripe_add($gateways){
	$gateways['stripe'] = 'Stripe';
	return $gateways;
}


add_action('after_payment_list', 'ae_stripe_render_button');
function ae_stripe_render_button() {
    $stripe_key = ae_get_option('stripe');
    if(!$stripe_key['publishable_key'] || !$stripe_key['secret_key']) return false;
?>
	<li>
        <span class="title-plan stripe-payment" data-type="stripe">
            <?php
    _e("Stripe", ET_DOMAIN); ?>
            <span><?php
    _e("Send your payment to our stripe account", ET_DOMAIN); ?></span>
        </span>
        <a href="#" class="btn btn-submit-price-plan other-payment" data-type="stripe"><?php
    _e("Select", ET_DOMAIN); ?></a>
    </li>
	
<?php
}

add_action('wp_print_scripts', 'ae_stripe_script');
function ae_stripe_script() {
    if (is_page_template('page-post-place.php') || is_page_template('page-submit-project.php')) {
        
        global $user_ID, $ae_post_factory;
        $ae_pack = $ae_post_factory->get('pack');
        $packs = $ae_pack->fetch();

        wp_enqueue_script('stripe.checkout', 'https://checkout.stripe.com/v2/checkout.js');
        wp_enqueue_script('stripe', 'https://js.stripe.com/v1/');
        wp_enqueue_script('ae_stripe', plugin_dir_url(__FILE__) . '/assets/stripe.js', array(
            'underscore',
            'backbone',
            'appengine'
        ) , '1.0', true);
        wp_enqueue_style('ae_stripe', plugin_dir_url(__FILE__) . '/assets/stripe.css', array() , '1.0');
        
        $stripe_key = ae_get_option('stripe');
        wp_localize_script('ae_stripe', 'ae_stripe', array(
            'public_key' => $stripe_key['publishable_key'],
            'currency' => ae_get_option('currency') ,
            'card_number_msg' => __('The Credit card number is invalid.', ET_DOMAIN) ,
            'name_card_msg' => __('The name on card is invalid.', ET_DOMAIN) ,
            'transaction_success' => __('The transaction completed successfull!.', ET_DOMAIN) ,
            'transaction_false' => __('The transaction was not completed successfull!.', ET_DOMAIN) ,
            'pack' => $packs
        ));
    }
}

add_filter('ae_setup_payment', 'ae_stripe_setup_payment', 10, 3);
function ae_stripe_setup_payment($response, $paymentType, $order) {
    
    if ($paymentType == 'STRIPE') {
        
        $order_pay = $order->generate_data_to_pay();
        
        $token = $_POST['token'];
        
        $job_id = $order_pay['product_id'];
        
        $stripe_key = ae_get_option('stripe');
        
        global $user_email;
        
        try {
            
            Stripe::setApiKey($stripe_key['secret_key']);
            
            $customer = Stripe_Customer::create(array(
                'card' => $token,
                'description' => 'Customer from ' . home_url() ,
                'email' => $user_email
            ));
            
            $customer_id = $customer->id;
            
            $charge = Stripe_Charge::create(array(
                'amount' => $order_pay['total'] * 100,
                'currency' => $order_pay['currencyCodeType'],
                
                //'card' 		=> $token,
                'customer' => $customer_id
            ));
            
            $value = $charge->__toArray();
            $id = $value['id'];
            $token = md5($id);
            
            $order->set_payment_code($token);
            $order->set_payer_id($id);
            $order->update_order();
            
            $url = et_get_page_link('process-payment');
            
            global $wp_rewrite;
            $returnURL = et_get_page_link('process-payment', array(
                'paymentType' => 'stripe',
                'token' => $token
            ));
            
            $response = array(
                'success' => true,
                'data' => array(
                    'url' => $returnURL
                ) ,
                'paymentType' => 'stripe'
            );
        }
        catch(Exception $e) {
            $value  =   $e->getJsonBody();
            $response = array(
                'success' => false,
                'msg' => $value['error']['message'],
                'paymentType' => 'stripe'
            );
        }
    }
    return $response;
}


add_filter( 'ae_process_payment', 'ae_stripe_process_payment', 10 ,2 );
function ae_stripe_process_payment ( $payment_return, $data) {
	$payment_type = $data['payment_type'];
	$order = $data['order'];
	if( $payment_type == 'stripe') {
		if( isset($_REQUEST['token']) &&  $_REQUEST['token'] == $order->get_payment_code() ) {
			
			$payment_return	=	array (
				'ACK' 			=> true,
				'payment'		=>	'stripe',
				'payment_status' =>'Completed'
				
			);
			$order->set_status ('publish');
			$order->update_order();
			
		} else {				
			$payment_return	=	array (
				'ACK' 			=> false,
				'payment'		=>	'stripe',
				'payment_status' =>'Completed',
				'msg' 	=> __('Stripe payment method false.', ET_DOMAIN)
				
			);
		}
	}	
	return $payment_return;

}

add_action('wp_footer', 'ae_stripe_form_template');
function ae_stripe_form_template() {
    include_once dirname(__FILE__) . '/form-template.php';
}


/**
 * hook to add translate string to plugins 
 * @param Array $entries Array of translate entries
 * @since 1.0
 * @author Dakachi
 */
add_filter( 'et_get_translate_string', 'ae_stripe_add_translate_string' );
function ae_stripe_add_translate_string ($entries) {
    $lang_path = dirname(__FILE__).'/lang/default.po';
    if(file_exists($lang_path)) {
        $pot        =   new PO();
        $pot->import_from_file($lang_path, true );
        
        return  array_merge($entries, $pot->entries);    
    }
    return $entries;
}