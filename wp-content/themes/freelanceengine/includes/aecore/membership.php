<?php
abstract class AE_Payment extends AE_Base
{
    
    /**
     * no private ajax
     */
    protected $no_priv_ajax = array();
    
    // private ajax
    protected $priv_ajax = array(
        'et-setup-payment'
    );
    
    function __construct() {
       
        $this->init_ajax();
    }
    
    // init ajax for payment
    function init_ajax() {
        foreach ($this->no_priv_ajax as $key => $value) {
            $function = str_replace('et-', '', $value);
            $function = str_replace('-', '_', $function);
            $this->add_ajax($value, $function);
        }
        
        foreach ($this->priv_ajax as $key => $value) {
            $function = str_replace('et-', '', $value);
            $function = str_replace('-', '_', $function);
            $this->add_ajax($value, $function, true, false);
        }
        
        // catch action ae_save_option to update payment api settings
        $this->add_action('ae_save_option', 'update_payment_settings', 10, 2);
        
        // process payment
        $this->add_action('ae_process_payment_action', 'process_payment', 10, 2);
    }
    
    /**
     * callback update option for payment api settings
     */
    public function update_payment_settings($name, $value) {
        
        // update paypal api settings
        if ($name == 'paypal') {
            ET_Paypal::set_api($value);
        }
        
        // update 2checkout api settings
        if ($name == '2checkout') {
            ET_2CO::set_api($value);
        }
        // update 2checkout api settings
        if ($name == 'cash') {
            ET_Cash::set_message($value['cash_message']);
        }
    }
    
    /*
     * get payment package for submit place
    */
    abstract public function get_plans();
    
    /**
     * catch action ce_payment_process and update ad data
     */
    function process_payment($payment_return, $data) {
        
        if(!isset($data['ad_id'])) return false;

        $options = AE_Options::get_instance();
        $ad_id = $data['ad_id'];
        
        extract($data);
        
        if(!$payment_return['ACK']) return ;

        /**
         * payment incomplete return false
         */
        // if (!$payment_return['ACK']) {
        //     if ($payment_return['payment_status'] != 'Completed') return '';
        // }

        $this->member_payment_process($payment_return, $data);

        /**
         * payment type is cash set pending post, and update unpaid
         */
	    /** @var string $payment_type */

	    if ( 'cash' == $payment_type ) {
            wp_update_post(array(
                'ID' => $ad_id,
                'post_status' => 'pending'
            ));
            
            // update unpaid payment
            update_post_meta($ad_id, 'et_paid', 0);
            return;
        }
        
        if ( 'usePackage' == $payment_type ) {
            return;
        }
        
        if ( 'free' == $payment_type ) {
            
            /**
             * sync Ad data
             */
            if ($options->use_pending) {
                 // pending ad
                wp_update_post(array(
                    'ID' => $ad_id,
                    'post_status' => 'pending'
                ));
            } else {
                wp_update_post(array(
                    'ID' => $ad_id,
                    'post_status' => 'publish'
                ));
            }
            
            // update free payment
            update_post_meta($ad_id, 'et_paid', 2);
            
            return $payment_return;
        }
        
        /**
         * payment succeed
         */
        if ($payment_return['payment_status'] != 'Pending') {
            if ($options->use_pending) {
                 // pending ad
                wp_update_post(array(
                    'ID' => $ad_id,
                    'post_status' => 'pending'
                ));
            } else {
                wp_update_post(array(
                    'ID' => $ad_id,
                    'post_status' => 'publish'
                ));
            }
            
            // paid
            update_post_meta($ad_id, 'et_paid', 1);
        } else {
            
            /**
             * in some case the payment will be pending
             */
            wp_update_post(array(
                'ID' => $ad_id,
                'post_status' => 'pending'
            ));
            
            // unpaid
            update_post_meta($ad_id, 'et_paid', 0);
        }
        
        // update post order id
        update_post_meta($ad_id, 'et_ad_order', $data['order_id']);
        
        
        
        return $payment_return;
    }
    
    /**
     * request a payment process et-setup-payment
     */
    function setup_payment() {
        global $user_ID;
        
        // remember to check isset or empty here
        $adID = isset($_POST['ID']) ? $_POST['ID'] : '';
        $author = isset($_POST['author']) ? $_POST['author'] : $user_ID;
        $packageID = isset($_POST['packageID']) ? $_POST['packageID'] : '';
        $paymentType = isset($_POST['paymentType']) ? $_POST['paymentType'] : '';
        
        $job_error = '';
        $author_error = '';
        $package_error = '';
        $errors = array();
        
        // job id invalid
        
        // author does not authorize job
        $job = get_post($adID);
        
        if ($author != $job->post_author && !current_user_can('manage_options')) {
            $author_error = __("Post author information is incorrect!", 'aecore-membership-backend');
            $errors[] = $author_error;
        }
        
        $plans = $this->get_plans();
        
        if (empty($plans)) {
            wp_send_json($response);
        }
        
        // input data error
        if (!empty($errors)) {
            $response = array(
                'success' => false,
                'errors' => $errors
            );
            
            wp_send_json($response);
        }
        
        ////////////////////////////////////////////////
        ////////////// process payment//////////////////
        ////////////////////////////////////////////////
        
        $order_data = array(
            'payer' => $author,
            'total' => '',
            'status' => 'draft',
            'payment' => $paymentType,
            'paid_date' => '',
            'payment_plan' => $packageID,
            'post_parent' => $adID
        );
        
        foreach ($plans as $key => $value) {
            if ($value->sku == $packageID) {
                $plan = $value;
                break;
            }
        }
        
        $plan->ID = $adID;
        
        // $ship    =   array( 'street_address' => isset($company_location['full_location']) ? $company_location['full_location'] : __("No location", 'aecore-membership-backend'));
        // filter shipping
        $ship = apply_filters('ae_payment_ship', array());
        
        /**
         * filter order data
         */
        $order_data = apply_filters('ae_payment_order_data', $order_data);
        
        // insert order into database
        $order = new AE_Order($order_data, $ship);
        
        $order->add_product((array)$plan);
        
        $order_data = $order->generate_data_to_pay();
        
        et_write_session('order_id', $order_data['ID']);
        et_write_session('ad_id', $adID);
        
        $arg = apply_filters('ae_payment_links', array(
            'return' => et_get_page_link('process-payment') ,
            'cancel' => et_get_page_link('process-payment')
        ));
        
        /**
         * process payment
         */
        $paymentType = strtoupper($paymentType);
        
        /**
         * factory create payment visitor
         */
        $visitor = AE_Payment_Factory::createPaymentVisitor($paymentType, $order);
        
        $visitor->set_settings($arg);
        $nvp = $order->accept($visitor);
        if ($nvp['ACK']) {
            $response = array(
                'success' => $nvp['ACK'],
                'data' => $nvp,
                'paymentType' => $paymentType
            );
        } else {
            $response = array(
                'success' => false,
                'paymentType' => $paymentType,
                'msg' => __("Invalid payment gateway", 'aecore-membership-backend')
            );
        }
        
        $response = apply_filters('ae_setup_payment', $response, $paymentType, $order);
        
        wp_send_json($response);
    }
    
    /** 
     * action process payment update seller order data
     */
    public function member_payment_process($payment_return, $data) {
        extract($data);
        if (!$payment_return['ACK']) return false;
        if ($payment_type == 'free') return false;
        
        if ($payment_type == 'usePackage') {
            return false;
        }
        
        global $user_ID;
        $order_pay = $data['order']->get_order_data();
        
        self::update_current_order($order_pay['payer'], $order_pay['payment_package'], $data['order_id']);
        AE_Package::add_package_data($order_pay['payment_package'], $order_pay['payer']);
        
        /**
         * do action after process user order
         * @param $order_pay['payer'] the user id
         * @param $data The order data
        */
        do_action('ae_member_process_order' , $order_pay['payer'] , $order_pay );
        
    }
    
    /**
     * return the order id user paid for the package
     * @return Array
     */
    public static function get_current_order($user_id, $package_id = '') {
        $order = get_user_meta($user_id, 'ae_member_current_order', true);
        if ($package_id == '') return $order;
        else return (isset($order[$package_id]) ? $order[$package_id] : '');
    }
    /**
     * update user current order
     * @param $user_id the user pay id
     * @param $group array of order and package 'sku' => 'order_id'
    */
    public static function set_current_order($user_id , $group ) {
        update_user_meta($user_id, 'ae_member_current_order', $group);
    }
    
    /**
     *  update order id user paid for package
     */
    public static function update_current_order($user_id, $package, $order_id) {
        $group = self::get_current_order($user_id);
        
        $group[$package] = $order_id;
        
        return self::set_current_order($user_id, $group);
    }
}

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
        $post = get_post( $ad_id );
        if ($ad_id && !is_wp_error($post) ) {
            /**
             * get object by post type and convert
            */ 
            $post_obj = $ae_post_factory->get($post->post_type);
            $ad = $post_obj->convert($post);
            $ad_package = $ad->et_payment_package;

            // get package info
            $package_obj = $ae_post_factory->get('pack');
            $package = $package_obj->get($ad_package);
            
            if (is_wp_error($package) || $package->et_price > 0) { // check the price is 0?
                return array(
                    'ACK' => false,
                    'payment_type' => 'free',
                    'msg' => __("Invalid Payment package", 'aecore-membership-backend')
                );
            }
            
            if ( $user_ID == $ad->post_author || current_user_can('manage_options')) {
                
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
            'msg' => __("Invalid Post ID", 'aecore-membership-backend')
        );
    }
}

/**
 * Class AE_UsePackageVisitor
 * Process ad order when user submit by use package
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
            $ad = $post_obj ->convert($post);

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
                    if (is_wp_error($order)) {
                        return array(
                            'ACK' => false,
                            'payment_type' => 'usePackage',
                            'msg' => __("Invalid Order or Package", 'aecore-membership-backend')
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
                        $ad_data['et_paid'] = 1;
                        $options = new AE_Options();
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
                    update_post_meta($ad->ID, 'et_paid', $ad_data['et_paid'] );
                    // update post package order id
                    update_post_meta($ad->ID, 'et_ad_order', $ad_data['et_ad_order'] );
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
            'msg' => __("Invalid Ad ID", 'aecore-membership-backend')
        );
    }
}

/**
 * class CE_Payment_Factory
 * generate a payment visitor to process order by $paymentType
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

define('ET_SESSION_COOKIE', '_et_session');
class ET_Session
{
    protected $_session_id;
    protected $_expired_time;
    protected $_exp_variant;
    protected $_session_data;
    protected static $instance;
    
    public static function get_instance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    protected function __construct() {
        
        if (isset($_COOKIE[ET_SESSION_COOKIE])) {
            $cookie = stripslashes($_COOKIE[ET_SESSION_COOKIE]);
            $cookie_data = explode('||', $cookie);
            
            $this->_session_id = $cookie_data[0];
            $this->_expired_time = $cookie_data[1];
            
            // Update the session expiration if we're past the variant time
            if (time() > $this->_expired_time) {
                $this->set_expiration();
                $this->_session_id = $this->regenerate_id(true);
                update_option("_et_session_expires_{$this->_session_id}", $this->_expired_time);
            }
        } else {
            $this->_session_id = $this->generate_id();
            $this->set_expiration();
        }
        
        $this->read_data();
        
        $this->set_cookie();
    }
    
    public function read_data() {
        if (!get_option("_et_session_{$this->_session_id}", '')) return false;
        $this->_session_data = unserialize(get_option("_et_session_{$this->_session_id}", ''));
        return (array)$this->_session_data;
    }
    
    /**
     * Write the data from the current session to the data storage system.
     */
    public function write_data($key, $value) {
        $option_key = "_et_session_{$this->_session_id}";
        if (false === get_option($option_key)) {
            $this->_session_data = array(
                $key => $value
            );
            add_option("_et_session_{$this->_session_id}", serialize($this->_session_data) , '', 'no');
            add_option("_et_session_expires_{$this->_session_id}", $this->_expired_time, '', 'no');
        } else {
            $this->_session_data[$key] = $value;
            update_option("_et_session_{$this->_session_id}", serialize($this->_session_data));
        }
    }
    
    /**
     * set exprire time
     */
    protected function set_expiration() {
        $this->_exp_variant = time() + (int)apply_filters('et_session_expiration_variant', 24 * 60);
        $this->_expired_time = time() + (int)apply_filters('et_session_expiration', 20 * 60);
    }
    
    /**
     * Set the session cookie
     */
    protected function set_cookie() {
        setcookie(ET_SESSION_COOKIE, $this->_session_id . '||' . $this->_expired_time, $this->_expired_time, '/');
    }
    
    protected function generate_id() {
        require_once (ABSPATH . 'wp-includes/class-phpass.php');
        $hasher = new PasswordHash(8, false);
        
        return md5($hasher->get_random_bytes(32));
    }
    
    public function regenerate_id($delete_old = false) {
        if ($delete_old) {
            delete_option("_et_session_{$this->_session_id}");
        }
        
        $this->_session_id = $this->generate_id();
        
        $this->set_cookie();
    }
    
    public function unset_session($key = null) {
        delete_option("_et_session_{$this->_session_id}");
    }
}

/**
 * Clean up expired sessions by removing data and their expiration entries from
 * the WordPress options table.
 *
 * This method should never be called directly and should instead be triggered as part
 * of a scheduled task or cron ad.
 */
function et_session_cleanup() {
    global $wpdb;
    
    if (defined('WP_SETUP_CONFIG')) {
        return;
    }
    
    if (!defined('WP_INSTALLING')) {
        $expiration_keys = $wpdb->get_results("SELECT option_name, option_value FROM $wpdb->options WHERE option_name LIKE '_et_session_expires_%'");
        
        $now = time();
        $expired_sessions = array();
        
        foreach ($expiration_keys as $expiration) {
            
            // If the session has expired
            if ($now > intval($expiration->option_value)) {
                
                // Get the session ID by parsing the option_name
                $session_id = substr($expiration->option_name, 20);
                
                $expired_sessions[] = $expiration->option_name;
                $expired_sessions[] = "_et_session_$session_id";
            }
        }
        
        // Delete all expired sessions in a single query
        if (!empty($expired_sessions)) {
            $option_names = implode("','", $expired_sessions);
            $wpdb->query("DELETE FROM $wpdb->options WHERE option_name IN ('$option_names')");
        }
    }
    
    // Allow other plugins to hook in to the garbage collection process.
    do_action('et_session_cleanup');
}
add_action('et_session_garbage_collection', 'et_session_cleanup');

/**
 * Register the garbage collector as a twice daily event.
 */
function et_session_register_garbage_collection() {
    if (!wp_next_scheduled('et_session_garbage_collection')) {
        wp_schedule_event(time() , 'twicedaily', 'et_session_garbage_collection');
    }
}
add_action('wp', 'et_session_register_garbage_collection');

function et_write_session($key, $value) {
    $et_session = ET_Session::get_instance();
    return $et_session->write_data($key, $value);
}

/**
 * get a session details
*/
function et_read_session() {
    $et_session = ET_Session::get_instance();
    return $et_session->read_data();
}
/**
 * destroy a session
*/
function et_destroy_session($key = null) {
    $et_session = ET_Session::get_instance();
    $et_session->unset_session($key);
}


/**
 * class AE_Package
 * control and manage de payment plan
 * @version 1.0
 * @package DirectoryEngine
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
                'text' => __('%s for %d days', 'aecore-membership-backend') ,
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
            'et_featured'
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
    function convert($post , $thumbnail = '', $excerpt = false) {
        $result = parent::convert($post , $thumbnail);
        if($result){
            $currency = ae_currency_sign(false);
            $align = ae_currency_align(false);
            
            if($align) {
                $result->backend_text = sprintf(__("(%s)%s for %d days", 'aecore-membership-backend'), $currency, $result->et_price,  $result->et_duration);
            }else {
                $result->backend_text = sprintf(__("%s(%s) for %d days", 'aecore-membership-backend'), $result->et_price, $currency, $result->et_duration);    
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
        
        if($options->$option_name) {
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
    */
    public static function check_use_package($sku, $user_id = 0) {
        // if set user to current user id if not have input
        if (!$user_id) {
            global $user_ID;
            $user_id = $user_ID;
        }
        // get user package data
        $used_package = self::get_package_data($user_id);
        
        // if user use the package with qty greater than 0 return true ( has post left )
        if (isset( $used_package[$sku] ) && $used_package[$sku]['qty'] > 0) return true;
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
     * add user package data when post ad
     */
    public static function add_package_data($sku, $user_id = 0) {
        
        if (!$user_id) {
            global $user_ID;
            $user_id = $user_ID;
        }
        
        $instance = self::get_instance();
        
        $used_package = self::get_package_data($user_id);
        
        $packageObj = $instance->get($sku);
        if (is_wp_error($packageObj)) return $packageObj;

        $qty = (int)$packageObj->et_number_posts - 1;
        
        $used_package[$sku] = array(
            'ID' => $sku,
            'qty' => $qty
        );
        
        self::set_package_data($user_id ,$used_package );
        
        return true;
    }
    
    /**
     * update user package data after post an ad
     */
    public static function update_package_data($package, $user_id = 0) {
        if (!$user_id) {
            global $user_ID;
            $user_id = $user_ID;
        }
        
        $used_package = self::get_package_data($user_id);
        
        $qty = (int)$used_package[$package]['qty'] - 1;
        
        if ($qty == 0) {
            
            // remove user current order for the package
            $group = AE_Payment::get_current_order($user_id);
            unset($group[$package]);

            AE_Payment::set_current_order($user_id, $group);
            
        }
        
        $used_package[$package]['qty'] = $qty;
        self::set_package_data($user_id ,$used_package );
    }
    
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
                $response['msg'] = __("You have reached the maximum number of Free posts. Please select another plan.", 'aecore-membership-backend');
                
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



if(!function_exists('ae_cash_message')){
    function ae_cash_message ( $cash_message, $order ) {
        $products       =   $order['products'];

        $args           =   array_pop($products);
        $ad_id          =   $args['ID'];

        $mail = AE_Mailing::get_instance();
        $mail->send_cash_message( $cash_message , $order['payer'] ,  $ad_id );
    }
}
add_action ('et_cash_checkout', 'ae_cash_message', 10, 2);


add_action('ae_member_process_order', 'ae_member_process_order', 10, 2);
function ae_member_process_order($user_id, $order) {
    $mail = AE_Mailing::get_instance();
    $mail->send_receipt( $user_id, $order );
}
