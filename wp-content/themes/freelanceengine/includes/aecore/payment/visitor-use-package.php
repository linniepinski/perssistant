<?php
/**
 * Class AE_UsePackageVisitor
 * Process ad order when user submit by use package
 *
 * @package AE Payment
 * @category paymentvisitor
 *
 * @since  1.0
 * @author  Dakachi
 *
 */
class AE_UsePackageVisitor extends ET_PaymentVisitor
{
    protected $_payment_type = 'use_package';
    
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
        
        if ($ad_id) {
            $post = get_post($ad_id);
            
            // ad id existed
            /**
             * get object by post type and convert
             */
            $post_obj = $ae_post_factory->get($post->post_type);
            $ad = $post_obj->convert($post);
            
            if (!is_wp_error($ad)) {
                
                /**
                 * check user is available to use selected package
                 */
                $available = AE_Package::check_use_package($ad->et_payment_package, $ad->post_author);
                
                if ($available) {
                    
                    // process order data
                    
                    $payment_return = array(
                        'ACK' => true,
                        'payment_type' => 'usePackage'
                    );
                    
                    /**
                     * get user current order for package
                     */
                    $current_order = AE_Payment::get_current_order($ad->post_author, $ad->et_payment_package);
                    
                    $order = get_post($current_order);
                    
                    if (!$order || is_wp_error($order)) {
                        return array(
                            'ACK' => false,
                            'payment_type' => 'usePackage',
                            'msg' => __("Invalid Order or Package", ET_DOMAIN)
                        );
                    }
                    
                    $ad_data = array();
                    
                    $ad_data['ID'] = $ad->ID;
                    
                    /**
                     * update ad order
                     */
                    $ad_data['et_ad_order'] = $current_order;
                    $ad_data['post_status'] = 'pending';
                    
                    if ($order->post_status == 'publish') {
                        $options = AE_Options::get_instance();
                        $ad_data['et_paid'] = 1;
                        if (!$options->use_pending) $ad_data['post_status'] = 'publish';
                    } else {
                        $ad_data['et_paid'] = 0;
                    }
                    
                    $ad_data['change_status'] = 'change_status';
                    $ad_data['method'] = 'update';
                    
                    /**
                     * sync Ad data
                     */
                    $return = wp_update_post($ad_data);
                    
                    // update post paid status
                    update_post_meta($ad->ID, 'et_paid', $ad_data['et_paid']);
                    
                    // update post package order id
                    update_post_meta($ad->ID, 'et_ad_order', $ad_data['et_ad_order']);
                    
                    /**
                     * update seller package quantity
                     */
                    AE_Package::update_package_data($ad->et_payment_package, $ad->post_author);
                    
                    return $payment_return;
                }
            }
        }
        
        return array(
            'ACK' => false,
            'payment_type' => 'usePackage',
            'msg' => __("Invalid Ad ID", ET_DOMAIN)
        );
    }
}
