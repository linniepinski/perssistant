<?php

/**
 * Class render order list in engine themes backend
 * - list order
 * - search order
 * - load more order
 * @since 1.0
 * @author Dakachi
 */
class AE_OrderList
{
    
    /**
     * construct a user container
     */
    function __construct($args = array() , $roles = '') {
        $this->args = $args;
        $this->roles = $roles;
        
        add_action('wp_ajax_ae-fetch-orders', array(
            $this,
            'fetch_orders'
        ));
        add_action('wp_ajax_ae-sync-order', array(
            $this,
            'sync_order'
        ));
    }
    
    /**
     * render list of orders
     */
    function render() {
        $support_gateway = apply_filters('ae_support_gateway', array(
            'cash' => __("Cash", ET_DOMAIN) ,
            'paypal' => __("Paypal", ET_DOMAIN) ,
            '2checkout' => __("2Checkout", ET_DOMAIN) ,
        ));
        $orders = AE_Order::get_orders();
?>
	<div class="et-main-content order-container" id="">
		<div class="search-box et-member-search">
			<form action="">
				<span class="et-search-role">
					<select name="role" id="" class="et-input" >
						<option value="" ><?php
        _e("All", ET_DOMAIN); ?></option>
						<?php
        foreach ($support_gateway as $name => $label) {
            echo '<option value="' . $name . '" >' . $label . '</option>';
        } ?>
					</select>
				</span>
				<span class="et-search-input">
					<input type="text" class="et-input order-search" name="keyword" placeholder="<?php
        _e("Search post...", ET_DOMAIN); ?>">
					<span class="icon" data-icon="s"></span>
				</span>
			</form>				
		</div>
		<!-- // user search box -->

		<div class="et-main-main no-margin clearfix overview list">			
			<div class="title font-quicksand"><?php
        _e('All Orders', ET_DOMAIN) ?></div>
			<!-- order list  -->
			<ul class="list-inner list-payment users-list">
			<?php
        if ($orders->have_posts()) {
            while ($orders->have_posts()) {
                $orders->the_post();
                global $post;
                
                ae_get_template_part('order', 'item');
            }
        } else {
            _e('There are no payments yet.', ET_DOMAIN);
        }
?>
			</ul>
			<!--// order list  -->
			<!-- load more button -->
			<?php
        if ($orders->max_num_pages > 1) { ?>
				<button class="et-button btn-button load-more" >
					<?php
            _e('More Payments', ET_DOMAIN) ?>
				</button>
			<?php
        } ?>	        			
		</div>
		<!-- //user list -->
	</div>
	<script type="text/javascript">
		(function($){
			$(document).ready(function(){
				if (typeof AE.Views.OrderList !== 'undefined') {
		            var order_view = new AE.Views.OrderList({
		                el: $('.order-container'),
		                pages : parseInt('<?php
        echo $orders->max_num_pages; ?>')
		            });
		        }
			});
		})(jQuery);
	</script>
    <?php
    }
    
    //TODO: block control order
    function fetch_orders() {
        
        if (!current_user_can('edit_others_posts')) return;
        
        $request = $_REQUEST;
        if (isset($request['search']) && $request['search'] != '') {
            
            /**
             * search post with keyword
             */
            $posts = new WP_Query(array(
                's' => $request['search'],
                'meta_key' => 'et_ad_order',
                'showposts' => - 1,
                'post_status' => array(
                    'publish',
                    'pending',
                    'draft',
                    'archive',
                    'reject'
                )
            ));
            /**
             * build orders id param
             */
            $order_ids = array();
            while ($posts->have_posts()) {
                
                $posts->the_post();
                $order_id = get_post_meta(get_the_ID() , 'et_ad_order', true);
                if ($order_id) {
                    $order = get_post($order_id);
                    $order_ids = array_merge($order_ids, (array)$order->ID);
                }
            }
            
            // add args post__in to query order
            if (!empty($order_ids)) $request['post__in'] = $order_ids;
        }
        
        /**
         * get orders
         */
        $orders = AE_Order::get_orders($request);
        $content = '';
        ob_start();
        while ($orders->have_posts()) {
            $orders->the_post();
            global $post;
            
            ae_get_template_part('order', 'item');
        }
        
        $content = ob_get_clean();
        
        $response = array();
        $response['pages'] = $orders->max_num_pages;
        $response['page'] = $_REQUEST['paged'] + 1;
        $response['data'] = $content;
        
        if (!$orders->have_posts()) $response['msg'] = __("No order found by your query.", ET_DOMAIN);
        
        wp_send_json($response);
    }
    
    /**
     * catch ajax callback ae-sync-order to update order status and send json to clien
     * @return null
     *
     * @since  1.2
     * @author  Dakachi
     */
    function sync_order() {
        if (!current_user_can('manage_options')) {
        	wp_send_json(array(
	            'success' => false
	        ));
        }

        $order_status = $_REQUEST['status'];
        $order_id = $_REQUEST['ID'];
        
        $order = get_post($order_id);

        if($order->post_parent){
            $order_id = wp_update_post(array(
                'ID' => $order->post_parent,
                'post_status' => $order_status
            ));    
        }
        // update order status
        $order_id = wp_update_post(array(
            'ID' => $order_id,
            'post_status' => $order_status
	    ));	
        
        if ($order_id) {
            wp_send_json(array(
                'success' => true,
                'msg' => __("Update order successfull.", ET_DOMAIN)
            ));
        } else {
            wp_send_json(array(
                'success' => false
            ));
        }
    }
}
