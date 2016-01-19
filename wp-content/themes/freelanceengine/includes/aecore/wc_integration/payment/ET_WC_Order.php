<?php if(class_exists("WC_Abstract_Order")) {
    class ET_WC_Order extends WC_Abstract_Order
    {
        protected $etOrder;
        protected $cancel_url = "";
        protected $received_url = "";

        public function __construct($order = '')
        {
            $this->post_type = 'order';
            $this->prices_include_tax = get_option('woocommerce_prices_include_tax') == 'yes' ? true : false;
            $this->tax_display_cart = get_option('woocommerce_tax_display_cart');
            $this->display_totals_ex_tax = $this->tax_display_cart == 'excl' ? true : false;
            $this->display_cart_ex_tax = $this->tax_display_cart == 'excl' ? true : false;
            $this->init($order);
        }

        public function get_refunds()
        {
            return array();
        }

        public function get_total_refunded()
        {
            return 0;
        }

        /**
         * Init
         *
         * @param int|object|WC_Order $order
         */
        protected function init($order)
        {
            if (is_numeric($order)) {
                $this->id = absint($order);
                $this->post = get_post($order);
                $this->get_order($this->id);
            } elseif ($order instanceof ET_WC_Order) {
                $this->id = absint($order->id);
                $this->ID = absint($order->id);
                $this->post = $order->post;
                $this->etOrder = $order->etOrder;
                $this->populate($order->etOrder);
            } elseif ($order instanceof AE_Order) {
                $payData = $order->generate_data_to_pay();
                $order->ID = $payData["ID"];
                $this->etOrder = $order;
                $this->populate($order);
                // Billing email cam default to user if set
                if (empty($this->billing_email) && !empty($this->customer_user)) {
                    $user = get_user_by('id', $this->customer_user);
                    $this->billing_email = $user->user_email;
                }
            } elseif ($order instanceof WP_Post || isset($order->ID)) {
                $this->id = absint($order->ID);
                $this->post = $order;
                $this->get_order($this->id);
            }
        }

        public function get_order($id = 0)
        {
            if (!$id) {
                return false;
            }
            $this->etOrder = new AE_Order($id);
            if ($this->etOrder) {
                $payData = $this->etOrder->generate_data_to_pay();
                $this->etOrder->ID = $payData["ID"];
                $this->populate($this->etOrder);
                return true;
            }

            return false;
        }

        /**
         * Populate the order, like convert method
         *
         * @param mixed $result
         */
        public function populate($result)
        {
            $orderData = $result->get_order_data();
            // Standard post data
            $this->id = $result->ID;
            $this->order_date = $orderData['created_date'];
            $this->modified_date = $orderData['created_date'];
            $this->customer_message = '';
            $this->customer_note = '';
            $this->post_status = $orderData['status'];
            // Billing email cam default to user if set
            if (empty($this->billing_email) && !empty($this->customer_user)) {
                $user = get_user_by('id', $this->customer_user);
                $this->billing_email = $user->user_email;
            }
            $this->cancel_url = et_get_page_link('process-payment', array('paymentType' => $orderData['payment']));
            $this->received_url = et_get_page_link('process-payment', array('paymentType' => $orderData['payment']));
        }

        public function get_shipping_address()
        {
            return "";
        }

        /**
         * Get item of order
         *
         * @param string $type
         * @return array
         */
        public function get_items($type = '')
        {
            if (empty($type)) {
                $type = array('line_item');
            }
            if (!is_array($type)) {
                $type = array($type);
            }
            $type = array_map('esc_attr', $type);
            $items = array();
            if (in_array('line_item', $type)) {
                $orderData = $this->etOrder->get_order_data();
                if (isset($orderData["products"])) {
                    $index = 0;
                    foreach ($orderData["products"] as $id => $product) {
                        $items[$index]['name'] = $product['NAME'];
                        $items[$index]['product_id'] = $product['ID'];
                        $items[$index]['type'] = "line_item";
                        $items[$index]['qty'] = $product['QTY'];
                        $items[$index]['tax_class'] = '';
                        $items[$index]['line_subtotal'] = $product['AMT'];
                        $items[$index]['line_subtotal_tax'] = '0';
                        $items[$index]['item_meta'] = array();
                        $index++;
                    }
                }
            }
            return $items;
        }

	    /**
	     * Get total of order
	     * @author : Nguyễn Văn Được
	     * @return mixed
	     */
        public function get_total()
        {
            $orderData = $this->etOrder->get_order_data();
            $total = $orderData["total"];
            return $total;
        }

	    /**
	     * Get order currency
	     * @author : Nguyễn Văn Được
	     * @return mixed
	     */
        public function get_order_currency()
        {
            $orderData = $this->etOrder->get_order_data();
            return $orderData["currency"];
        }

	    /**
	     * Get products from product item
	     * @author : Nguyễn Văn Được
	     *
	     * @param mixed $item
	     *
	     * @return bool|\ET_WC_Package
	     */
        public function get_product_from_item($item)
        {
            if (!empty($item['product_id'])) {
                $_product = new ET_WC_Package($item['product_id']);
            } else {
                $_product = false;
            }
            return $_product;
        }

        public function get_checkout_order_received_url()
        {
            return $this->received_url;
        }

        public function set_checkout_order_received_url($received_url)
        {
            $this->received_url = $received_url;
        }

        public function get_cancel_order_url($redirect = '')
        {
            return $this->cancel_url;
        }

        public function set_cancel_order_url($cancel_url)
        {
            $this->cancel_url = $cancel_url;
        }

        /**
         * Update order status
         *
         * @param string $new_status
         * @param string $note
         */
        public function update_status($new_status, $note = '')
        {
            if (!$this->id) {
                return;
            }

            // Standardise status names.
            $new_status = 'wc-' === substr($new_status, 0, 3) ? substr($new_status, 3) : $new_status;
            $old_status = $this->get_status();
            switch (strtoupper($new_status)) {
                case 'COMPLETED':
                case 'PUBLISH':
                    $this->post_status = 'publish';
                    break;
                case 'PROCESSING' :
                case 'ON-HOLD':
                $this->post_status = 'pending';
                    break;
                case 'CANCELLED' :
                    $this->post_status = 'draft';
                    break;
                default:
                    $this->post_status = 'draft';
                    break;
            }
            $this->etOrder->post_status = $this->post_status;
            $log = new WC_Logger();
            $log->add('paypal', "Our Debug : " . $this->post_status);
            wp_update_post(array('ID' => $this->etOrder->ID, 'post_status' => $this->post_status));
        }

        public function needs_shipping_address()
        {
            return false;
        }

        public function is_editable()
        {
            return parent::is_editable(); // TODO: Change the autogenerated stub
        }

        public function get_qty_refunded_for_item($item_id, $item_type = 'line_item')
        {
            $qty = 0;
            return $qty;
        }

        public function get_total_refunded_for_item($item_id, $item_type = 'line_item')
        {
            $total = 0;
            return $total * -1;
        }
    }
}