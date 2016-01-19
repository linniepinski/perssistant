<?php
/**
 * class AE_Payment_Factory
 * generate a payment visitor to process order by $paymentType
 *
 * @package AE Payment
 * @category payment
 *
 * @since  1.0
 * @author  Dakachi
 */
class AE_Payment_Factory extends ET_Payment_Factory
{
    function __construct() {
        
        // dont know what i can do here
        
        
    }
    
    public static function createPaymentVisitor($paymentType, $order) {
        
        switch ($paymentType) {
            case 'CASH':
                
                // return cash visitor
                $class = new ET_CashVisitor($order);
                break;

            case 'GOOGLE_CHECKOUT':
                $class = new ET_GoogleVisitor($order);
                break;

            case 'PAYPAL':
                $class = new ET_PaypalVisitor($order);
                break;

            case 'AUTHORIZE':
                $class = new ET_AuthorizeVisitor($order);
                break;

            case '2CHECKOUT':
                $class = new ET_2COVisitor($order);
                break;

            case 'FREE':
                return new AE_FreeVisitor($order);
                break;

            case 'USEPACKAGE':
                return new AE_UsePackageVisitor($order);
                break;

            default:
                $class = new ET_InvalidVisitor($order);
        }
        
        return apply_filters('et_build_payment_visitor', $class, $paymentType, $order);
    }
}
