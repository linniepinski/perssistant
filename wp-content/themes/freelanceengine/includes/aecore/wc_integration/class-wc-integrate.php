<?php
/**
 * Project : classifiedengine
 * User: thuytien
 * Date: 11/28/2014
 * Time: 9:16 AM
 */

/**
 * Class WC_Integrate
 * Use for integrate CE with WC\
 */

class WC_Integrate
{
    /**
     * @return static
     */
	static $instance = null;
    public static function getInstance()
    {
        if (null === self::$instance) {
	        self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add integration hook, Call when action
     */
    public function add_hook()
    {
        add_filter('woocommerce_order_class', array(self::getInstance(), 'wc_woocommerce_order_class'), 10, 3);
        add_filter('woocommerce_product_class', array(self::getInstance(), 'wc_woocommerce_product_class'), 10, 4);
        add_filter('woocommerce_valid_order_statuses_for_payment_complete', array(self::getInstance(), 'woocommerce_valid_order_statuses_for_payment_complete'), 10, 2);

        add_filter('woocommerce_payment_complete_order_status', array(self::getInstance(), 'woocommerce_payment_complete_order_status'), 10, 2);
        add_filter('woocommerce_order_item_needs_processing', array(self::getInstance(), 'woocommerce_order_item_needs_processing'), 10, 3);

        //hide product menu
        add_filter('woocommerce_register_post_type_product', array(self::getInstance(), 'woocommerce_register_post_type_product'));
        add_filter('woocommerce_register_post_type_shop_order', array(self::getInstance(), 'woocommerce_register_post_type_shop_order'));
        add_filter('woocommerce_register_post_type_shop_coupon', array(self::getInstance(), 'woocommerce_register_post_type_shop_coupon'));

        add_filter('et_build_payment_visitor', array(self::getInstance(), 'build_payment_visitor'), 10, 3);

        add_action('after_payment_list', array(self::getInstance(), 'add_wc_payment'));
    }

	/**
	 * Register posttyle to Woocomerce
	 *
	 * @author: nguyenvanduocit
	 */
	function register_post_type_to_wc(){
		global $wc_order_types;
		$args = array(
			'exclude_from_orders_screen'       => false,
			'add_order_meta_boxes'             => true,
			'exclude_from_order_count'         => false,
			'exclude_from_order_views'         => false,
			'exclude_from_order_reports'       => false,
			'exclude_from_order_sales_reports' => false,
			'class_name'                       => 'ET_WC_Order'
		);
		$wc_order_types[ 'order' ] = $args;
	}

    function add_wc_payment()
    {
        $available_payment_gateways = WooCommerce::instance()->payment_gateways()->get_available_payment_gateways();
        foreach ($available_payment_gateways as $key => $gateway):
            ?>
            <li>
                <span class="title-plan" data-type="<?php echo $key ?>">
                    <?php echo $gateway->get_title() ?>
                    <span><?php echo $gateway->get_description() ?></span>
                </span>
                <a href="#" class="btn btn-submit-price-plan select-payment"
                   data-type="<?php echo $key ?>"><?php _e("Select", ET_DOMAIN); ?></a>
            </li>
        <?php
        endforeach;
    }

	/**
	 *
	 * Build the visistor
	 *
	 * @author : Nguyễn Văn Được
	 *
	 * @param $class
	 * @param $paymentType
	 * @param $order
	 *
	 * @return \WC_Integrate_Visitor
	 */
    function build_payment_visitor($class, $paymentType, $order)
    {
        if ($class instanceof ET_InvalidVisitor) {

            if (class_exists("WooCommerce")) {
                $available_payment_gateways = WooCommerce::instance()->payment_gateways()->get_available_payment_gateways();
                $paymentType_lower = strtolower($paymentType);
                if (array_key_exists($paymentType_lower, $available_payment_gateways)) {
                    $class = new WC_Integrate_Visitor($paymentType);
                }
            }
        }
        return $class;
    }

	/**
	 *
	 * Hide coupon menu
	 *
	 * @author : Nguyễn Văn Được
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
    function woocommerce_register_post_type_shop_coupon($args)
    {
        // $args['show_ui'] = false;
        // $args['public'] = false;

        return $args;
    }

	/**
	 * Hide wc's posttype menu
	 * @author : Nguyễn Văn Được
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
    function woocommerce_register_post_type_shop_order($args)
    {
        // $args['show_ui'] = false;
        // $args['public'] = false;

        return $args;
    }

	/**
	 * Hide product menu
	 * @author : Nguyễn Văn Được
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
    function woocommerce_register_post_type_product($args)
    {

        // $args['show_ui'] = false;
        // $args['public'] = false;

        return $args;
    }

    /**
     * @param $is_isneed
     * @param $_product
     * @param $id
     * @return bool
     *
     * If product is need processing step
     */
    function woocommerce_order_item_needs_processing($is_isneed, $_product, $id)
    {
        if ($_product instanceof ET_WC_Package) {
            return false;
        }
        return $is_isneed;
    }

    /**
     * @param $status
     * @param $order
     * @return string
     *
     * Return status for complete order
     */
    function woocommerce_payment_complete_order_status($status, $order)
    {
        if ($order instanceof ET_WC_Order) {
            return 'publish';
        }
        return $status;
    }

    /**
     * @param $status
     * @param $order
     * @return array
     *
     * Return array of valid order status for process payment complete
     */
    function woocommerce_valid_order_statuses_for_payment_complete($status, $order)
    {
        if ($order instanceof ET_WC_Order) {
            return array('pending', 'draft');
        }
        return $status;
    }

    /**
     * @param $classname
     * @param $product_type
     * @param $post_type
     * @param $product_id
     * @return string
     *
     * Return class to wrap product data
     */
    function wc_woocommerce_product_class($classname, $product_type, $post_type, $product_id)
    {
        if ($classname == false && (in_array($post_type, array('pack')))) {
            $classname = "ET_WC_Package";
        }
        return $classname;
    }

    /**
     * @param $classname
     * @param $post_type
     * @param $order_id
     * @return string
     *
     * Return class to wrap Order data
     * TODO : Add arg $order when wc update new API for commit
     */
    function wc_woocommerce_order_class($classname, $post_type, $order_id)
    {
	    //var_dump($order);
        if ( "AE_Order" == $classname ) {
            $classname = "ET_WC_Order";
        }
        return $classname;
    }

}

/**
 * Add action on Woocommerce init
 */
add_action('woocommerce_init', 'ae_load_integrate');
function ae_load_integrate(){
	/*
	 * Ony use it when run appengine as plugin, because of file require order.
	 */
	require_once dirname(__FILE__) . '/payment/ET_WC_Order.php';
	require_once dirname(__FILE__) . '/payment/ET_WC_Package.php';
	require_once dirname(__FILE__) . '/payment/WC_Integrate_Visitor.php';
	require_once dirname(__FILE__) . '/payment/WC_Integrate_Visitor_Special.php';

	WC_Integrate::getInstance()->register_post_type_to_wc();
    WC_Integrate::getInstance()->add_hook();
}

/**
 * Handle on payment post data back.
 */
add_action('init', 'ae_payment_postback_handle');
function ae_payment_postback_handle() {
    /**
     * only paypal using this, and skip if wooCommerce actived
     */
    if (!empty($_GET['paypalListener']) && ('paypal_appengine_IPN' == $_GET['paypalListener']) ) {
	    if(class_exists("ET_Paypal")) {
		    $paypal = new ET_Paypal();
		    $paypal->check_ipn_response();
	    }
    }
}