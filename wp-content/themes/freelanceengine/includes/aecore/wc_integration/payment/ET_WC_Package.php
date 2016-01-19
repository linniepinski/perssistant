<?php
if(class_exists("WooCommerce")) {
    class ET_WC_Package extends WC_Product
    {
        protected $etPaynentPackage;

        function __construct($product, $arg = array())
        {
            $this->product_type = 'pack';
            parent::__construct($product);
        }

        public function get_sku()
        {
            return $this->post->ID;
        }

        public function is_virtual()
        {
            return true;
        }

        public function needs_shipping()
        {
            return false;
        }

        public function get_permalink() {
            return get_permalink( $this->id );
        }

    }
}