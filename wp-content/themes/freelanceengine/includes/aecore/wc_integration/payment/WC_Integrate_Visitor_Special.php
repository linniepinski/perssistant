<?php
/**
 * @project classifiedengine_develop
 * @author  nguyenvanduocit
 * @date    02/12/2015
 */
function wc_ce_special_pre_checkout ($order)
{
    $payment_type = get_query_var( 'paymentType' );
    if ( $payment_type == "paytrail" ) {
        wc_ce_paytrail_pre_checkout($order);
    }
}

function wc_ce_paytrail_pre_checkout($order){
    if ( isset($_GET['ORDER_NUMBER'], $_GET['TIMESTAMP'], $_GET['PAID'], $_GET['METHOD'], $_GET['RETURN_AUTHCODE'] )) {
        $woocommerce_paytrail_merchant_secret = "6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ";
        $base = "{$_GET['ORDER_NUMBER']}|{$_GET['TIMESTAMP']}|{$_GET['PAID']}|{$_GET['METHOD']}|{$woocommerce_paytrail_merchant_secret}";
        if($_GET['RETURN_AUTHCODE'] == strtoupper(md5($base))){
            $order->set_status( 'publish' );
            $order->update_order();
        }
    }
}