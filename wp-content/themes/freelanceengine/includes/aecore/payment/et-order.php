<?php
abstract class ET_Order
{
    
    /*
     * order id : identify an order
    */
    protected $_ID;
    
    /*
     * total money paid for order
    */
    protected $_total;
    protected $_total_before_discount;
    
    /*
     * currency code use in transaction or pay
    */
    protected $_currency;
    
    /*
     * buyer id : identify who pay for this order
    */
    protected $_payer;
    
    /*
     * payment type , identify kind of payment was used for this order
    */
    protected $_payment;
    
    /*
     * when payer pay success, payment gateway will return their code to us.
     * this field is use to store it. May be it can be used in refund, or check some info
     * when we get some complaints
    */
    protected $_payment_code;
    
    /*
     * list products of this order
     * 	- id
     *  - quantity
    */
    protected $_products;
    
    //single product
    protected $_product_id;
    
    /*
     * shipping infomation
     *  - ship option name
     *  - ship option label
     *  - ship option address
     *  - ship option amount
    */
    protected $_shipping;
    
    /*
     * order created date
    */
    protected $_created_date;
    
    /*
     * payer pay successful date
    */
    protected $_paid_date;
    
    /*
     * we use wp_posts table to store order data
     * so we need register post type order
    */
    protected $_stat;
    
    /*
     *  when pay order by paypal, paypal return we a payer id
     *  it's used in paypal when do express checkout
    */
    protected $_payer_id;
    
    protected $_setup_checkout;
    
    protected $_coupon_code;
    protected $_discount_rate;
    protected $_discount_method;
    
    protected $_post_parent;
    
    static function register_order_post_type() {
        $args = array(
            'labesls' => array(
                'name' => __('Order', ET_DOMAIN)
            ) ,
            'public' => true,
            'show_ui' => false,
            'publicly_queryable' => false,
            '_builtin' => true,
             /* internal use only. don't use this when registering your own post type. */
            '_edit_link' => 'post.php?post=%d',
             /* internal use only. don't use this when registering your own post type. */
            'capability_type' => 'post',
            'map_meta_cap' => true,
            'hierarchical' => true,
            'rewrite' => false,
            'query_var' => false,
            'supports' => array(
                'title',
                'author',
                'custom-fields'
            ) ,
        );
        register_post_type('order', $args);
    }
    
    /**
     * construct an object order
     * @param int | array $order :
     * @param array $product
     * @param array $ship
     */
    function __construct($order, $ship = array()) {
        $currency = ET_Payment::get_currency();
        if (!is_array($order)) {
            
            // construct a order by load data from database use order_id provided
            $order_id = intval($order);
            
            if ($order_id > 0) {
                $order = get_post($order_id, ARRAY_A);
                $this->_ID = $order_id;
                $this->_payer = $order['post_author'];
                
                //$this->_payment	=	$order['post_title'];
                $this->_created_date = $order['post_date'];
                $this->_stat = $order['post_status'];
                $this->_payment = get_post_meta($this->_ID, 'et_order_gateway', true);
                $this->_products = get_post_meta($this->_ID, 'et_order_products', true);
                $this->_shipping = get_post_meta($this->_ID, 'et_order_shipping', true);
                $this->_currency = get_post_meta($this->_ID, 'et_order_currency', true);
                $this->_payment_code = get_post_meta($this->_ID, 'et_order_payment_code', true);
                $this->_total = get_post_meta($this->_ID, 'et_order_total', true);
                $this->_paid_date = get_post_meta($this->_ID, 'et_order_paid_date', true);
                
                /**
                 * coupon
                 */
                $this->_coupon_code = get_post_meta($this->_ID, 'et_order_coupon_code', true);
                $this->_discount_rate = get_post_meta($this->_ID, 'et_order_discount_rate', true);
                $this->_discount_method = get_post_meta($this->_ID, 'et_order_discount_method', true);
                
                if ($this->_currency == '') $this->_currency = $currency['code'];
                
                $this->_setup_checkout = false;
                
                return $order;
            }
            return false;
        } else {
             // contruct an order by new order and insert into database
            
            $default_order = array(
                
                'payer' => '',
                'currency' => $currency['code'],
                
                //'products'			=>	 array(),
                'total' => '',
                'status' => 'pending',
                'payment' => 'cash',
                'create_date' => current_time('mysql') ,
                'paid_date' => '',
                'token' => '',
                'coupon_code' => '',
                'discount_rate' => '',
                'discount_method' => 'percent'
            );
            $order = wp_parse_args($order, $default_order);
            extract($order);
            
            $this->_payer = $payer;
            $this->_currency = $currency;
            $this->_total = $total;
            $this->_stat = $status;
            $this->_payment = $payment;
            $this->_created_date = $create_date;
            $this->_paid_date = $paid_date;
            $this->_payment_code = $token;
            
            // $this->_products		=	$products
            $this->set_shipping($ship);
            
            $this->_total = $total;
            $this->_total_before_discount = $total;
            
            /**
             * coupon
             */
            $this->_coupon_code = $coupon_code;
            $this->_discount_rate = $discount_rate;
            $this->_discount_method = $discount_method;
            
            // set order post parent
            if (isset($order['post_parent'])) {
                $this->_post_parent = $post_parent;
            }
            
            $this->update_order();
            
            $this->_setup_checkout = true;
            return $order;
        }
    }
    
    public function get_order_data() {
        return array(
            'payer' => $this->_payer,
            
            'created_date' => $this->_created_date,
            'status' => $this->_stat,
            'payment' => $this->_payment,
            'products' => $this->_products,
            
            'currency' => $this->_currency,
            'payment_code' => $this->_payment_code,
            'total' => $this->_total,
            'total_before_discount' => $this->_total_before_discount,
            'discount_rate' => $this->_discount_rate,
            'discount_method' => $this->_discount_method,
            'paid_date' => $this->_paid_date,
            'shipping' => $this->_shipping
        );
    }
    
    //convert order to an associate array
    public function generate_data_to_pay() {
        
        $order = array(
            
            'payment_code' => $this->_payment_code,
            'ID' => $this->_ID,
            'payer' => $this->_payer,
            'currencyCodeType' => $this->_currency,
            'products' => $this->_products,
            
            'total' => $this->_total,
            'total_before_discount' => $this->_total_before_discount,
            
            'ship' => $this->_shipping,
            'payment' => $this->_payment,
            'payer_id' => $this->_payer_id,
             // use in paypal
            'coupon_code' => $this->_coupon_code,
            'discount_rate' => $this->_discount_rate,
            'discount_method' => $this->_discount_method
        );
        
        return $order;
    }
    
    /*
     * update order infomation to database
    */
    function update_order() {
        
        if ($this->_total_before_discount == '' && $this->_total == '') return false;
        
        $postarr = array();
        $postarr['ID'] = $this->_ID;
        $postarr['post_author'] = $this->_payer;
        $postarr['post_title'] = $this->_payment;
        $postarr['post_content'] = 'product name';
        $postarr['post_date'] = $this->_created_date;
        $postarr['post_status'] = $this->_stat;
        $postarr['post_type'] = 'order';
        if ($this->_post_parent) $postarr['post_parent'] = $this->_post_parent;
        
        $postarr = apply_filters('et_save_order_data', $postarr);
        
        $this->_ID = wp_update_post($postarr);
        
        update_post_meta($this->_ID, 'et_order_products', $this->_products);
        update_post_meta($this->_ID, 'et_order_shipping', $this->_shipping);
        update_post_meta($this->_ID, 'et_order_currency', $this->_currency);
        update_post_meta($this->_ID, 'et_order_payment_code', $this->_payment_code);
        update_post_meta($this->_ID, 'et_order_total', $this->_total);
        update_post_meta($this->_ID, 'et_order_total_before_discount', $this->_total_before_discount);
        update_post_meta($this->_ID, 'et_order_paid_date', $this->_paid_date);
        update_post_meta($this->_ID, 'et_order_payer_id', $this->_payer_id);
        update_post_meta($this->_ID, 'et_order_gateway', $this->_payment);
        
        /**
         * coupon
         */
        update_post_meta($this->_ID, 'et_order_coupon_code', $this->_coupon_code);
        update_post_meta($this->_ID, 'et_order_discount_rate', $this->_discount_rate);
        update_post_meta($this->_ID, 'et_order_discount_method', $this->_discount_method);
        
        // et_refresh_revenue();
        
        do_action('et_save_order');
        
        return $this->_ID;
    }
    
    /*
     * remove an order from database
    */
    function delete_order() {
        
        // delete meta
        /*delete_post_meta($this->_ID, 'et_order_products');
        delete_post_meta($this->_ID, 'et_order_shipping');
        delete_post_meta($this->_ID, 'et_order_currency');
        delete_post_meta($this->_ID, 'et_order_payment_code');
        delete_post_meta($this->_ID, 'et_order_total');
        delete_post_meta($this->_ID, 'et_order_paid_date');*/
        
        // delete order post
        wp_delete_post($this->_ID, true);
        
        // destroy object
        $this->_ID = '';
        $this->_total = '';
    }
    
    protected function calculate_discount($total) {
        if ($this->_coupon_code && $this->_discount_rate) {
            if ($this->_discount_method == 'percent') {
                $total-= ($this->_discount_rate * $total) / 100;
            } else {
                $total-= $this->_discount_rate;
            }
        }
        if ($total < 0) $total = 0;
        return number_format($total, 2, '.', '');
    }
    
    /**
     * set up product for order
     * @param array $product :
     * 	-	$key : product ID
     * 	-	$value : product info
     */
    public function set_product($product = array()) {
        $arr = array();
        $total = 0;
        foreach ($product as $key => $value) {
            
            // get all payment plan : product
            // each product data
            $p = array(
                'ID' => $key,
                'NAME' => $value['title'],
                'AMT' => $value['price'],
                'QTY' => $value['qty'],
                'L_DESC' => $value['description']
            );
            $arr[$key] = $p;
            $total+= $value * $p['AMT'];
        }
        
        $this->_total_before_discount = $total;
        $this->_total = $this->calculate_discount($total);
        
        $this->_products = $arr;
        $this->update_order();
        return number_format($total, 2, '.', '');
    }
    
    /**
     * set up product for order
     * @param array $product :
     * 	-	$key : product ID
     * 	-	$value : product info
     */
    public function add_product($product, $number = 1) {
        
        $this->_products[$product['ID']] = array(
            'ID' => $product['ID'],
            'NAME' => $product['title'],
            'AMT' => $product['price'],
            'QTY' => $number,
            'L_DESC' => $product['description'],
            'SKU' => $product['sku']
        );
        $this->_total_before_discount+= number_format($product['price'] * $number, 2, '.', '');
        $this->_total = number_format($this->calculate_discount($this->_total_before_discount) , 2, '.', '');
        
        $this->_product_id = $product['ID'];
        $this->update_order();
    }
    
    public function get_products() {
        return $this->_products;
    }
    
    public function get_total() {
        return $this->_total;
    }
    
    /**
     * set up shipping infomation for order
     * @param array $ship
     * 	- name
     * 	- address
     *  - city
     *  - state
     *  - amount
     *  - country
     */
    public function set_shipping($ship = array()) {
        $this->_shipping = $ship;
    }
    
    /**
     * set order payment code
     * @param $string $code : token
     */
    public function set_payment_code($code) {
        $this->_payment_code = $code;
        update_post_meta($this->_ID, 'et_order_payment_code', $this->_payment_code);
    }
    public function get_payment_code() {
        return $this->_payment_code;
    }
    
    public function set_status($status) {
        $this->_stat = $status;
    }
    
    public function set_payer_id($id) {
        $this->_payer_id = $id;
    }
    
    public function get_payer_id() {
        return $this->_payer_id;
    }
    
    // function accept visitor
    function accept(ET_PaymentVisitor $visitor) {
        if ($this->_setup_checkout) return $visitor->setup_checkout($this);
        else return $visitor->do_checkout($this);
    }
}
class ET_NOPAYOrder extends ET_Order
{
    public function __construct() {
    }
}

add_action('init', 'et_add_order');
function et_add_order() {
    
    ET_Order::register_order_post_type();
}
