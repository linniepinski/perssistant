<?php

/**
 * register post type fre_order to handle escrow order
 * @author Dakachi
 */
function fre_register_order() {
    register_post_type('fre_order', $args = array(
        'labels' => array(
            'name' => __('Fre Order', ET_DOMAIN) ,
            'singular_name' => __('Fre Order', ET_DOMAIN)
        ) ,
        'hierarchical' => true,
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_admin_bar' => true,
        'menu_position' => 5,
        'show_in_nav_menus' => true,
        'publicly_queryable' => true,
        'exclude_from_search' => false,
    ));
}
add_action('init', 'fre_register_order');

/**
 * enqueue script to open modal accept bid
 * @author Dakachi
 */
function fre_enqueue_escrow() {
    if (is_singular(PROJECT)) {
        wp_enqueue_script('escrow-accept', TEMPLATEURL . '/js/accept-bid.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine'
        ) , ET_VERSION, true);
    }
}
add_action('wp_print_scripts', 'fre_enqueue_escrow');

/**
 * ajax callback to setup bid info and send to client
 * @author Dakachi
 */
function fre_get_accept_bid_info() {
    $bid_id = $_GET['bid_id'];
    global $user_ID;
    $error = array(
        'success' => false,
        'msg' => __('Invalid bid', ET_DOMAIN)
    );
    if (!isset($_REQUEST['bid_id'])) {
        wp_send_json($error);
    }
    $bid_id = $_REQUEST['bid_id'];
    $bid = get_post($bid_id);
    
    // check bid is valid
    if (!$bid || is_wp_error($bid) || $bid->post_type != BID) {
        wp_send_json($error);
    }
    
    $bid_budget = get_post_meta($bid_id, 'bid_budget', true);
    
    // get commission settings
    $commission = ae_get_option('commission', 0);
    $commission_fee = $commission;
    
    // caculate commission fee by percent
    $commission_type = ae_get_option('commission_type');
    if ($commission_type != 'currency') {
        $commission_fee = ((float)($bid_budget * (float)$commission)) / 100;
    }
    
    $commission = fre_price_format($commission_fee);
    $payer_of_commission = ae_get_option('payer_of_commission', 'project_owner');
    if ($payer_of_commission == 'project_owner') {
        $total = (float)$bid_budget + (float)$commission_fee;
    } 
    else {
        $commission = 0;
        $total = $bid_budget;
    }
    
    wp_send_json(array(
        'success' => true,
        'data' => array(
            'budget' => fre_price_format($bid_budget) ,
            'commission' => $commission,
            'total' => fre_price_format($total)
        )
    ));
}
add_action('wp_ajax_ae-accept-bid-info', 'fre_get_accept_bid_info');

/**
 * ajax callback process bid escrow and send redirect url to client
 *
 * @author Dakachi
 */
function fre_escrow_bid() {
    global $user_ID;
    $error = array(
        'success' => false,
        'msg' => __('Invalid bid', ET_DOMAIN)
    );
    if (!isset($_REQUEST['bid_id'])) {
        wp_send_json($error);
    }
    $bid_id = $_REQUEST['bid_id'];
    $bid = get_post($bid_id);
    
    // check bid is valid
    if (!$bid || is_wp_error($bid) || $bid->post_type != BID) {
        wp_send_json($error);
    }

    // currency settings
    $currency = ae_get_option('content_currency');
    $currency = $currency['code'];
    
    $bid_budget = get_post_meta($bid_id, 'bid_budget', true);
    
    // get commission settings
    $commission = ae_get_option('commission');
    $commission_fee = $commission;
    
    // caculate commission fee by percent
    $commission_type = ae_get_option('commission_type');
    
    if ($commission_type == 'percent') {
        $commission_fee = ($bid_budget * $commission) / 100;
    }
    
    $payer_of_commission = ae_get_option('payer_of_commission', 'project_owner');
    if ($payer_of_commission == 'project_owner') {
        $total = (float)$bid_budget + (float)$commission_fee;
    } 
    else {
        $total = $bid_budget;
        $bid_budget = (float)$total - (float)$commission_fee;
    }
    
    $receiver = get_user_meta($bid->post_author, 'paypal', true);

    // paypal adaptive process payment and send reponse to client
    $ppadaptive = AE_PPAdaptive::get_instance();
    // get paypal adaptive settings
    $ppadaptive_settings = ae_get_option('escrow_paypal');

    // the admin's paypal business account
    $primary = $ppadaptive_settings['business_mail'];
    
    // get from setting
    $feesPayer = $ppadaptive_settings['paypal_fee'];
    
    /**
     * paypal adaptive order data
    */
    $order_data = array(
        'actionType' => 'PAY_PRIMARY',
        'returnUrl' => et_get_page_link('process-accept-bid', array(
            'paymentType' => 'paypaladaptive'
        )) ,
        'cancelUrl' => et_get_page_link('process-accept-bid', array(
            'paymentType' => 'paypaladaptive'
        )) ,
        
        // 'maxAmountPerPayment' => '35.00',
        'currencyCode' => $currency,
        'feesPayer' => $feesPayer,
        'receiverList.receiver(0).amount' => $total,
        'receiverList.receiver(0).email' => $primary,
        'receiverList.receiver(0).primary' => true,
        
        // freelancer receiver
        'receiverList.receiver(1).amount' => $bid_budget,
        'receiverList.receiver(1).email' => $receiver,
        'receiverList.receiver(1).primary' => false,
        'requestEnvelope.errorLanguage' => 'en_US'
    );
    
    //dinhle1987-pers@yahoo.com
    // dinhle1987-pers2@yahoo.com
    
    $response = $ppadaptive->Pay($order_data);
  
    if (is_array($response) && isset($response['success']) && !$response['success']) {
        wp_send_json(array(
            'success' => false,
            'msg' => $response['msg']
        ));
    }
    
    // create order
    $order_post = array(
        'post_type' => 'fre_order',
        'post_status' => 'pending',
        'post_parent' => $bid_id,
        'post_author' => $user_ID,
        'post_title' => 'Pay for accept bid',
        'post_content' => 'Pay for accept bid ' . $bid_id
    );
    
    if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {
        $order_id = wp_insert_post($order_post);
        update_post_meta($order_id, 'fre_paykey', $response->payKey);
        update_post_meta($order_id, 'gateway', 'PPadaptive');
        
        update_post_meta($bid_id, 'fre_bid_order', $order_id);
        update_post_meta($bid_id, 'fre_paykey', $response->payKey);
        
        et_write_session('payKey', $response->payKey);
        et_write_session('order_id', $order_id);
        et_write_session('bid_id', $bid_id);
        et_write_session('ad_id', $bid->post_parent);
        
        $response->redirect_url = $ppadaptive->paypal_url . $response->payKey;
        wp_send_json($response);
    } 
    else {
        wp_send_json(array(
            'success' => false,
            'msg' => $response->error[0]->message
        ));
    }
}
add_action('wp_ajax_ae-escrow-bid', 'fre_escrow_bid');

/**
 * dispute process execute payment and send money to freelancer
 * @since 1.3
 * @author Dakachi
 */
function fre_execute_payment() {
    // only the admin or the user have manage_options cap can execute the dispute
    if (!current_user_can('manage_options')) {
        wp_send_json(array(
            'success' => false,
            'msg' => __("You do not have permission to do this action.", ET_DOMAIN)
        ));
    }
    $project_id = $_REQUEST['project_id'];
    $bid_accepted = get_post_meta($project_id, 'accepted', true);
    
    // cho nay co the dung action
    $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
    
    // execute payment and send money to freelancer
    $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
    if ($pay_key) {
        $ppadaptive = AE_PPAdaptive::get_instance();
        $response = $ppadaptive->executePayment($pay_key);
        
        if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {
            
            // success update order data
            $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
            if ($order) {
                wp_update_post(array(
                    'ID' => $order,
                    'post_status' => 'completed'
                ));
            }
            
            // success update project status
            wp_update_post(array(
                'ID' => $project_id,
                'post_status' => 'disputed'
            ));
            
            /**
             * do action after admin finish dispute and execute send payment to freelancer
             * @param int $project_id
             * @param int $bid_id_accepted
             * @param int $order
             * @since 1.3
             * @author Dakachi
             */
            do_action('fre_dispute_execute_payment', $project_id, $bid_id_accepted, $order);
            
            // send mail
            $mail = Fre_Mailing::get_instance();
            $mail->execute($project_id, $bid_id_accepted);
            
            wp_send_json(array(
                'success' => true,
                'msg' => __("Send payment successful.", ET_DOMAIN)
            ));
        } 
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => $response->error[0]->message
            ));
        }
    } 
    else {
        wp_send_json(array(
            'success' => false,
            'msg' => __("Invalid paykey.", ET_DOMAIN)
        ));
    }
}
add_action('wp_ajax_execute_payment', 'fre_execute_payment');

/**
 * dispute process refund payment to employer
 * @since 1.3
 * @author Dakachi
 */
function fre_refund_payment() {
    if (!current_user_can('manage_options')) {
        wp_send_json(array(
            'success' => false,
            'msg' => __("You do not have permission to do this action.", ET_DOMAIN)
        ));
    }
    $project_id = $_REQUEST['project_id'];
    
    // cho nay co the dung action
    $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
    
    // execute payment and send money to freelancer
    $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
    if ($pay_key) {
        $ppadaptive = AE_PPAdaptive::get_instance();
        $response = $ppadaptive->Refund($pay_key);
        
        if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {
            
            // success update order data
            $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
            if ($order) {
                wp_update_post(array(
                    'ID' => $order,
                    'post_status' => 'refund'
                ));
            }
            
            // success update project status
            wp_update_post(array(
                'ID' => $project_id,
                'post_status' => 'disputed'
            ));
            
            /**
             * do action after admin finish dispute and refund payment
             * @param int $project_id
             * @param int $bid_id_accepted
             * @param int $order
             * @since 1.3
             * @author Dakachi
             */
            do_action('fre_dispute_refund_payment', $project_id, $bid_id_accepted, $order);
            
            $mail = Fre_Mailing::get_instance();
            $mail->refund($project_id, $bid_id_accepted);
            
            // send json back
            wp_send_json(array(
                'success' => true,
                'msg' => __("Send payment successful.", ET_DOMAIN) ,
                'data' => $response
            ));
        } 
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => $response->error[0]->message
            ));
        }
    } 
    else {
        wp_send_json(array(
            'success' => false,
            'msg' => __("Invalid paykey.", ET_DOMAIN)
        ));
    }
}
add_action('wp_ajax_refund_payment', 'fre_refund_payment');

/**
 * ajax callback to transfer payment to freelancer
 * @since 1.3
 * @author Dakachi
 */
function fre_transfer_money() {
    if (current_user_can('manage_options')) {
        $project_id = $_REQUEST['project_id'];
        
        // cho nay co the dung action
        $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
        
        // execute payment and send money to freelancer
        $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
        if ($pay_key) {
            $ppadaptive = AE_PPAdaptive::get_instance();
            $response = $ppadaptive->executePayment($pay_key);
            if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {
                
                // success update order data
                $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                if ($order) {
                    wp_update_post(array(
                        'ID' => $order,
                        'post_status' => 'finish'
                    ));
                }
                
                // send mail
                $mail = Fre_Mailing::get_instance();
                $mail->execute($project_id, $bid_id_accepted);
                
                // send json back
                wp_send_json(array(
                    'success' => true,
                    'msg' => __("Payment refund successful.", ET_DOMAIN) ,
                    'data' => $response
                ));
            } 
            else {
                wp_send_json(array(
                    'success' => false,
                    'msg' => $response->error[0]->message
                ));
            }
        } 
        else {
            wp_send_json(array(
                'success' => false,
                'msg' => __("Invalid paykey.", ET_DOMAIN)
            ));
        }
    }
}
add_action('wp_ajax_transfer_money', 'fre_transfer_money');

/**
 * finish project, send money when freelancer review project
 * @param int $project_id
 * @since 1.3
 * @author Dakachi
 */
function fre_finish_escrow($project_id) {
    if (ae_get_option('use_escrow')) {
        $bid_id_accepted = get_post_meta($project_id, 'accepted', true);
        if (!ae_get_option('manual_transfer')) {
            
            // cho nay co the dung action
            
            // execute payment and send money to freelancer
            $pay_key = get_post_meta($bid_id_accepted, 'fre_paykey', true);
            if ($pay_key) {
                $ppadaptive = AE_PPAdaptive::get_instance();
                $response = $ppadaptive->executePayment($pay_key);
                if (strtoupper($response->responseEnvelope->ack) == 'SUCCESS') {
                    
                    // success update order data
                    $order = get_post_meta($bid_id_accepted, 'fre_bid_order', true);
                    if ($order) {
                        wp_update_post(array(
                            'ID' => $order,
                            'post_status' => 'finish'
                        ));
                        $mail = Fre_Mailing::get_instance();
                        $mail->alert_transfer_money($project_id, $bid_id_accepted);
                    }
                }
            }
        } 
        else {
            $mail = Fre_Mailing::get_instance();
            $mail->alert_transfer_money($project_id, $bid_id_accepted);
        }
    }
}
add_action('fre_freelancer_review_employer', 'fre_finish_escrow');
