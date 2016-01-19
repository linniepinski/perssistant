<?php
/**
 * Class AE_FreeVisitor is used to process when user submit a post by free package
 *
 * @package AE Payment
 * @category paymentvisitor
 *
 * @since 1.0
 * @author Dakachi
 */
class AE_FreeVisitor extends ET_PaymentVisitor
{
    protected $_payment_type = 'free';
    
    //function __construct () {}
    function setup_checkout(ET_Order $order) {
        
        /* do nothing  */
    }
    function do_checkout(ET_Order $order) {
        global $ae_post_factory, $user_ID;
        
        /**
         * check session
         */
        $session = et_read_session();
        $ad_id = isset($session['ad_id']) ? $session['ad_id'] : '';
        $post = get_post($ad_id);
        if ($ad_id && !is_wp_error($post)) {
            
            /**
             * get object by post type and convert
             */
            $post_obj = $ae_post_factory->get($post->post_type);
            $ad = $post_obj->convert($post);
            $ad_package = $ad->et_payment_package;
            
            // get package info
            $package_obj = $ae_post_factory->get('pack');
            $package = $package_obj->get($ad_package);
            
            if (is_wp_error($package) || $package->et_price > 0) {
                
                // check the price is 0?
                return array(
                    'ACK' => false,
                    'payment_type' => 'free',
                    'msg' => __("Invalid Payment package", ET_DOMAIN)
                );
            }
            
            if ($user_ID == $ad->post_author || current_user_can('manage_options')) {
                
                // check permission
                $payment_return = array(
                    'ACK' => true,
                    'payment_type' => 'free'
                );
                return $payment_return;
            }
        }
        
        return array(
            'ACK' => false,
            'payment_type' => 'free',
            'msg' => __("Invalid Post ID", ET_DOMAIN)
        );
    }
}