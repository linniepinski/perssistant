<?php

/**
 * Project : classifiedengine
 * User: thuytien
 * Date: 11/28/2014
 * Time: 10:29 AM
 */
require_once dirname(__FILE__) . '/ET_WC_Order.php';
class WC_Integrate_Visitor extends ET_PaymentVisitor
{

    protected $_payment_type;
    private $gatewayType;
    private $gateway;

	/**
	 * @param \ET_Order $paymentType
	 */
	function __construct($paymentType)
    {
        $this->_payment_type = $paymentType;
        $this->gatewayType = $paymentType;
        $available_payment_gateways = WooCommerce::instance()->payment_gateways()->get_available_payment_gateways();
        $paymentType_lower = strtolower($paymentType);
        if (array_key_exists($paymentType_lower, $available_payment_gateways)) {
            $this->gateway = $available_payment_gateways[$paymentType_lower];
        }
    }

    //return url
    function setup_checkout(ET_Order $order)
    {
        $order->received_url = $this->_settings['return'];
        $order->cancel_url = $this->_settings['cancel'];
	    $inte_order = new ET_WC_Order($order);
        $payment_result = $this->gateway->process_payment($inte_order);
        if (is_array($payment_result)) {
            return array(
                'url' => $payment_result['redirect'],
                'ACK' => true,
                'extend' => false,
            );
        } else {
            return array(
                'ACK' => false,
                'msg' => __('Got error, please contact site administrator', ET_DOMAIN));
        }
    }

    //This function is useless
	function do_checkout( ET_Order $order ) {
		wc_ce_special_pre_checkout( $order );
		$order_datas   = $order->get_order_data();
		$paymentStatus = 'fraud';
		switch ( strtoupper( $order_datas[ 'status' ] ) ) {
			case 'COMPLETED':
			case 'PUBLISH':
				$paymentStatus = 'Completed';
				break;
			case 'PROCESSING':
			case 'PENDING':
				$paymentStatus = 'pending';
				break;
			case 'DRAFT':
				$paymentStatus = 'fraud';
				break;
			default:
				$paymentStatus = 'fraud';
				break;
		}

		return array(
			'ACK'            => in_array( strtoupper( $order_datas[ 'status' ] ), array(
				'COMPLETED',
				'PUBLISH',
				'PROCESSING',
				'PENDING'
			) ),
			'payment'        => $this->gatewayType,
			'payment_status' => $paymentStatus
		);
	}

}