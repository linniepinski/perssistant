<?php
/**
 * this file contain all function related to places
 */
add_action('init', 'fre_init_bid_plan');
function fre_init_bid_plan() {
    
    register_post_type('bid_plan', array(
        'labels' => array(
            'name' => __('Bid plan', 'bid-plans-backend') ,
            'singular_name' => __('Bid plan', 'bid-plans-backend') ,
            'add_new' => __('Add New', 'bid-plans-backend') ,
            'add_new_item' => __('Add New Bid plan', 'bid-plans-backend') ,
            'edit_item' => __('Edit Bid plan', 'bid-plans-backend') ,
            'new_item' => __('New Bid plan', 'bid-plans-backend') ,
            'all_items' => __('All Bid plans', 'bid-plans-backend') ,
            'view_item' => __('View Bid plan', 'bid-plans-backend') ,
            'search_items' => __('Search Bid plans', 'bid-plans-backend') ,
            'not_found' => __('No Bid plan found', 'bid-plans-backend') ,
            'not_found_in_trash' => __('No Bid plans found in Trash', 'bid-plans-backend') ,
            'parent_item_colon' => '',
            'menu_name' => __('Bid plans', 'bid-plans-backend')
        ) ,
        'public' => false,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => true,
        
        'capability_type' => 'post',
        // 'capabilities' => array(
        //     'manage_options'
        // ) ,
        'has_archive' => 'packs',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array(
            'title',
            'editor',
            'author',
            'custom-fields'
        )
    ));

    $package = new AE_Pack('bid_plan',array(
            'sku',
            'et_price',
            'et_number_posts',
            'order',
            'et_featured'
        ),
        array(
            'backend_text' => array(
                'text' => __('%s for %d days', 'bid-plans-backend') ,
                'data' => array(
                    'et_price',
                    'et_number_posts'
                )
            )
        ));
    global $ae_post_factory;
    $ae_post_factory->set('bid_plan', $package);
}



if(!function_exists('fre_order_bid_plan_by_menu_order')) {
    /**
     * filter posts order by to order bid_plan post by menu order
     * @param string $orderby The query orderby string
     * @param object $query Current wp_query object
     * @since 1.4
     * @author Dakachi
     */
    function fre_order_bid_plan_by_menu_order($orderby, $query){
        global $wpdb;
        if ($query->query_vars['post_type'] != 'bid_plan') return $orderby;
        $orderby = "{$wpdb->posts}.menu_order ASC";
        return $orderby;
    }
    add_filter('posts_orderby', 'fre_order_bid_plan_by_menu_order', 10, 2);
}