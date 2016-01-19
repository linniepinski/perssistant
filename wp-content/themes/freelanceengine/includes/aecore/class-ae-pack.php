<?php

/**
 * class AE_Pack control the way to act with post type pack
 * @author Dakachi
 * @package qaEngine
 * @version 1.0
 */
class AE_Pack extends AE_Posts
{
    static $instance;
    public $post_type;
    
    /**
     * return class $instance
     */
    public static function get_instance() {
        
        // if(self::$instance == null) {
        
        //  self::$instance =   new AE_Pack ();
        // }
        return self::$instance;
    }
    
    /**
     * construct instance, set post_type and meta data
     * @since 1.0
     */
    function __construct($post_type = '', $meta = array() , $localize = array()) {
        
        $this->post_type = ($post_type) ? $post_type : 'pack';
        $this->meta = (!empty($meta)) ? $meta : array(
            'qa_badge_point',
            'qa_badge_color',
            'option_name'
        );
        
        $this->localize = $localize;
        
        /**
         * add option name to control which option list post handle
         */
        $this->meta[] = 'option_name';
        
        /**
         * setup convert field of post data
         */
        $this->convert = array(
            'post_title',
            'post_name',
            'post_content',
            'ID',
            'post_type',
            'menu_order',
            'post_status'
        );
        self::$instance = $this;
    }
    
    /**
     * static function query badges
     * @param array $args query params, see more about this on WP_Query
     * @return object WP_Query
     * @since 1.0
     * @author Dakachi
     */
    public function query($args) {
        
        $args['post_type'] = self::$instance->post_type;
        $args['showposts'] = - 1;
        $args['meta_value'] = $args['option_name'];
        $args['meta_key'] = 'option_name';
        
        /**
         * construct WP_Query object
         */
        $post_query = new WP_Query($args);
        return $post_query;
    }
    
    /**
     * convert a post to pack object, which contain meta and tax data
     * @param $object Post
     * @return $object Post with meta data
     * @since 1.0
     * @author Dakachi
     */
    public static function qa_convert($post) {
        $instance = self::get_instance();
        $result = $instance->convert($post);
        return $result;
    }
    
    /**
     * fetch postdata from database, use function convert
     * @param array $args query options, see more WP_Query args
     * @return array of objects post
     * @author Dakachi
     * @since 1.0
     */
    public function fetch($option_name = '', $args = array()) {
        
        // $args = array();
        
        if ($option_name == '') {
            $option_name = $this->option_name;
        }
        
        // $args['meta_key'] = 'option_name';
        // $args['meta_value'] = $option_name;
        $args['post_type'] = $this->post_type;
        $args['showposts'] = - 1;
        
        $query = new WP_Query($args);
        $data = array();
        
        // loop post
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                global $post;
                
                // convert post data
                $data[] = $this->convert($post);
            }
        }
        /**
         * update pack list to options
         */
        $options = AE_Options::get_instance();
        $options->$option_name = $data;
        $options->save();
        // sort package
        if (isset($this->meta['order'])) {
            usort($data, array(
                $this,
                "et_package_cmp"
            ));
        }
        wp_reset_query();
        return $data;
    }
    
    public function et_package_cmp() {
        if ($a['menu_order'] == $b['menu_order']) {
            return 0;
        }
        return ($a['menu_order'] < $b['menu_order']) ? -1 : 1;
    }
    
    public function get_option() {
        return $this->option_name;
    }
}

/**
 * class AE_PackAction control all action do with object AE_Pack
 * @package qaengine
 * @version 1.0
 */
class AE_PackAction extends AE_Base
{
    function __construct(AE_Pack $pack) {
        
        $this->post = $pack;
        
        // add an action to catch ajax request sync pack
        $this->add_ajax('ae-pack-sync', 'pack_sync', true, false);
        
        $this->add_ajax('et_sort_payment_plan', 'et_ajax_payment_sorting');
        
        // add filter to order pack
        $this->add_filter('posts_orderby', 'ae_order_pack_by_menu_order', 10, 2);
        
        $this->options = AE_Options::get_instance();
    }
    
    /**
     * catch ajax request ae-pack-sync
     */
    public function pack_sync() {
        
        if (!current_user_can('manage_options')) return false;
        
        $request = $_REQUEST;
        unset($request['action']);
        
        extract($request);
        
        $request['post_status'] = 'publish';
        if (!isset($request['post_content'])) $request['post_content'] = __('content here', ET_DOMAIN);
        
        $option_name = $request['option_name'];
        
        /**
         * call instance sync
         */
        if ($option_name == 'payment_package') {
            $ae_pack = $this->post;
        } else {
            global $ae_post_factory;
            $ae_pack = $ae_post_factory->get($option_name);
        }
        
        $result = $ae_pack->sync($request);
        if ($result && !is_wp_error($result)) {
            
            /**
             * update badges options
             */
            $badges = $ae_pack->fetch($option_name);
            
            /**
             * update pack list to options
             */
            $this->options->$option_name = $badges;
            $this->options->save();
            
            wp_send_json(array(
                'success' => true,
                'data' => $result,
                'msg' => __("Sync success.", ET_DOMAIN)
            ));
        } else {
            
            // notice if false
            wp_send_json(array(
                'success' => false,
                'msg' => $result->get_error_message()
            ));
        }
    }
    
    /**
     * filter posts order by to order ae_field post by menu order
     * @param string $orderby The query orderby string
     * @param object $query Current wp_query object
     * @since 1.0
     * @author Dakachi
     */
    function ae_order_pack_by_menu_order($orderby, $query) {
        global $wpdb;
        if ($query->query_vars['post_type'] != 'pack') return $orderby;
        $orderby = "{$wpdb->posts}.menu_order ASC";
        return $orderby;
    }
    
    /**
     * Handle payment sorting request
     */
    function et_ajax_payment_sorting() {
        if (!current_user_can('manage_options')) return false;
        parse_str($_REQUEST['content']['order'], $sort_order);
        
        // update new order
        global $wpdb, $ae_post_factory;
        $sql = "UPDATE {$wpdb->posts} SET menu_order = CASE ID ";
        foreach ($sort_order['pack'] as $index => $id) {
            $sql.= " WHEN {$id} THEN {$index} ";
        }
        $sql.= " END WHERE ID IN (" . implode(',', $sort_order['pack']) . ")";
        
        $result = $wpdb->query($sql);
        

        // fetch list pack to refesh option
        $pack = get_post($id);
        $pack_object = $ae_post_factory->get($pack->post_type);
        if($pack->post_type != 'pack') {
            $pack_object->fetch( $pack->post_type );
        }
        // send json to client
        echo json_encode(array(
            'success' => $result,
            'msg' => __('Payment plans have been sorted', ET_DOMAIN)
        ));
        exit;
    }
}

