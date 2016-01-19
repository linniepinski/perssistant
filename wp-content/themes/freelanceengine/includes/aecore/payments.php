<?php
define('ET_SESSION_COOKIE', '_et_session');
if (!function_exists('ae_cash_message')) {
    
    /**
     * send a cash notify to user after purchase by cash
     * @param String $cash_message
     * @return null
     *
     * @package AE Mail
     * @category mail
     *
     * @since 1.0
     * @author Dakachi
     */
    function ae_cash_message($cash_message, $order) {
        $products = $order['products'];
        
        $package = array_pop($products);
        $session = et_read_session();
        $ad_id = $session['ad_id'];
        
        $mail = AE_Mailing::get_instance();
        $mail->send_cash_message($cash_message, $order['payer'], $package, $ad_id);
    }
}
add_action('et_cash_checkout', 'ae_cash_message', 10, 2);

if (!function_exists('ae_member_process_order')) {
    
    /**
     * cach the function after user success an order and send the receipt
     * @param Integer $user_id The current user id
     * @param Object $order The order data
     * @return null
     *
     * @package AE Payment
     * @category payment
     *
     * @since 1.0
     * @author Dakachi
     */
    function ae_member_process_order($user_id, $order) {
        $mail = AE_Mailing::get_instance();
        $mail->send_receipt($user_id, $order);
    }
}
add_action('ae_member_process_order', 'ae_member_process_order', 10, 2);

/**
 * rennder user package info
 * @param Integer $user_ID the user_ID want to render
 *
 * @package AE Package
 * @category payment
 * 
 * @since 1.0
 * @author Dakachi
 */
function ae_user_package_info($user_ID) {
    if (!$user_ID) return;
    global $ae_post_factory;
    $ae_pack = $ae_post_factory->get('pack');
    $packs = $ae_pack->fetch();
    $orders = AE_Payment::get_current_order($user_ID);
    $package_data = AE_Package::get_package_data($user_ID);
    
    foreach ($packs as $package) {
        $number_of_post = $package->et_number_posts;
        $sku = $package->sku;
        $text = '';
        if (isset($package_data[$sku]) && $package_data[$sku]['qty'] > 0) {
            
            $order = get_post($orders[$sku]);
            if (!$order || is_wp_error($order) || !in_array($order->post_status, array('publish', 'pending'))) continue;
            /**
             * print text when company has job left in package
             */
?>
        <p>
        <?php
            $number_of_post = $package_data[$sku]['qty'];
            
            if ($order->post_status == 'publish') printf(__("You purchased package <strong>%s</strong> and have %d post/s left.", ET_DOMAIN) , $package->post_title, $number_of_post);
            if ($order->post_status == 'pending') printf(__("You purchased package <strong>%s</strong> and have %d post/s left. Your posted post is pending until payment.", ET_DOMAIN) , $package->post_title, $number_of_post);
?>
        </p>
    
    <?php
        }
    }
}


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
