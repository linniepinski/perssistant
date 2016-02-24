<?php
/**
 * class AE_Package control and manage payment plan
 *
 * @package AE Package
 * @category payment
 *
 * @version 1.0
 * @author Dakachi
 */
class AE_Package extends AE_Pack
{
    static $instance;
    
    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new AE_Package();
        }
        return self::$instance;
    }
    
    function __construct($post_type = 'pack', $meta = array()) {
        $this->post_type = $post_type;
        $this->option_name = 'payment_package';
        
        // backend text
        $this->localize = array(
            'backend_text' => array(
                'text' => __('%s for %d days', 'aecore-class-ae-package-backend') ,
                'data' => array(
                    'et_price',
                    'et_number_posts'
                )
            )
        );
        
        $this->meta = array_merge($meta, array(
            'sku',
            'et_price',
            'et_number_posts',
            'order',
            'option_name',
            'et_duration',
            'et_featured',
            'et_not_duration'
        ));
        
        $this->convert = array(
            'post_title',
            'post_name',
            'post_content',
            'post_excerpt',
            'post_author',
            'post_status',
            'ID',
            'post_type'
        );
        
        self::$instance = $this;
    }
    
    /**
     * override convert function to update backend text
     */
    function convert($post, $thumbnail = '', $excerpt = false, $singular = false) {
        $result = parent::convert($post, $thumbnail);
        if ($result) {
            $currency = ae_currency_sign(false);
            $align = ae_currency_align(false);
            if ($align) {
                $result->backend_text = sprintf(__("(%s)%s for %d days", 'aecore-class-ae-package-backend') , $currency, $result->et_price, $result->et_duration);
            } else {
                $result->backend_text = sprintf(__("%s(%s) for %d days", 'aecore-class-ae-package-backend') , $result->et_price, $currency, $result->et_duration);
            }
        }
        return $result;
    }
    
    /**
     * get a package by sku
     */
    public function get($sku) {
        $options = AE_Options::get_instance();
        $option_name = $this->option_name;
        
        if ($options->$option_name) {
            $this->fetch();
        }
        
        if ($options->$option_name) {
            $packages = $options->$option_name;
            foreach ($packages as $key => $value) {
                if ($value->sku == $sku) {
                    return $value;
                }
            }
        }
        return false;
    }
    
    /**
     * check user are using a pakage or not
     * @param $sku the id of package
     * @param $user_id The user id
     * @return bool
     *
     * @since  1.0
     * @author Dakachi
     */
    public static function check_use_package($sku, $user_id = 0) {
        
        // if set user to current user id if not have input
        if (!$user_id) {
            global $user_ID;
            $user_id = $user_ID;
        }
        // 
        $orders = AE_Payment::get_current_order($user_id);
        // order not exists
        if(!isset($orders[$sku])) return false;
        $order = get_post($orders[$sku]);
        // invalid order, or order was trashed
        if(!$order || is_wp_error( $order ) || !in_array($order->post_status, array('pending', 'publish'))) return false;
        // get user package data
        $used_package = self::get_package_data($user_id);
        
        // if user use the package with qty greater than 0 return true ( has post left )
        if (isset($used_package[$sku]) && $used_package[$sku]['qty'] > 0) return true;
        return false;
    }
    
    /**
     * get all used package data
     */
    public static function get_package_data($user_id) {
        $used_package = get_user_meta($user_id, 'ae_member_packages_data', true);
        return $used_package;
    }
    
    public static function set_package_data($user_id, $used_package) {
        update_user_meta($user_id, 'ae_member_packages_data', $used_package);
    }
    
    /**
     * add user package data when purchase a package
     * @param String $sku The package stock keep unit
     * @param Integer $user_id The user ID
     *
     * @since  1.0
     * @author  Dakachi <ledd@youngworld.vn>
     */
    public static function add_package_data($sku, $user_id = 0) {
        
        if (!$user_id) {
            global $user_ID;
            $user_id = $user_ID;
        }
        
        $instance = self::get_instance();
        $packageObj = $instance->get($sku);
        // validate package object
        if (!$packageObj || is_wp_error($packageObj)) return $packageObj;
        $qty = (int)$packageObj->et_number_posts;
        
        $used_package = self::get_package_data($user_id); 
        $used_package[$sku] = array(
            'ID' => $sku,
            'qty' => $qty
        );
        
        self::set_package_data($user_id, $used_package);
        
        return true;
    }
    
    /**
     * update user package data after post an ad
     * @param string $pakage the package sku
     * @param int $user_id the user id
     *
     * @return null
     * @author  Dakachi
     */
    public static function update_package_data($package, $user_id = 0) {
        if (!$user_id) {
            global $user_ID;
            $user_id = $user_ID;
        }
        
        $used_package = self::get_package_data($user_id);
        
        if( !isset($used_package[$package]) ) return ;

        $qty = (int)($used_package[$package]['qty'] - 1);
        
        if ($qty == 0) {
            
            // remove user current order for the package
            $group = AE_Payment::get_current_order($user_id);
            unset($group[$package]);
            
            AE_Payment::set_current_order($user_id, $group);
        }
        
        $used_package[$package]['qty'] = $qty;
        self::set_package_data($user_id, $used_package);
    }
    
    /**
     * check user use package or use freepackage
     * @param  string $package_id The pacakge sku to identify package
     * @param  object $ad Current purchase post
     * @return array 
     *         'url' => string process-payment-url base on type free/usePackage,
     *         'success' => bool
     * @author Dakachi
     */
    public static function package_or_free($package_id, $ad) {
        
        $instance = self::get_instance();
        
        $response = array(
            'success' => false
        );
        
        $use_package = AE_Package::check_use_package($package_id);
        $package = $instance->get($package_id);
        
        if ($use_package) {
            et_write_session('ad_id', $ad->ID);
            $response['success'] = true;
            $response['url'] = et_get_page_link('process-payment', array(
                'paymentType' => 'usePackage'
            ));
            return $response;
        }
        
        if ($package->et_price == 0) {
            et_write_session('ad_id', $ad->ID);
            $response['success'] = true;
            $response['url'] = et_get_page_link('process-payment', array(
                'paymentType' => 'free'
            ));
            return $response;
        }

        return $response;
    }
    
    public static function limit_free_plan($package) {
        
        // check and limit seller user free plan
        $limit_free_plan = ae_get_option('limit_free_plan');
        
        $instance = self::get_instance();
        $package = $instance->get($package);
        
        $response = array(
            'success' => false
        );
        if ($package && $package->et_price == 0 && $limit_free_plan
        
        //&& !current_user_can( 'manage_options' )
        ) {
            
            /**
             * update number of free plan seller used
             */
            
            $number = self::update_used_free_plan();
            if ($number > $limit_free_plan) {
                
                $response['success'] = true;
                $response['msg'] = __("You have reached the maximum number of Free posts. Please select another plan.", 'aecore-class-ae-package-backend');
                
                return $response;
            }
        }
        return $response;
    }
    
    public static function update_used_free_plan($user_ID = '') {
        global $user_ID;
        if ($user_ID) {
            $number = self::get_used_free_plan();
            $number = $number + 1;
            update_user_meta($user_ID, 'ae_free_plan_used', $number);
            return $number;
        }
    }
    
    public static function get_used_free_plan($user_ID = '') {
        global $user_ID;
        return get_user_meta($user_ID, 'ae_free_plan_used', true);
    }
}