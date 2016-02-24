<?php

/**
 * this file contain all function related to places
 */
add_action('init', 'de_init_package');
function de_init_package() {
    
    register_post_type('pack', array(
        'labels' => array(
            'name' => __('Pack', 'packages-backend') ,
            'singular_name' => __('Pack', 'packages-backend') ,
            'add_new' => __('Add New', 'packages-backend') ,
            'add_new_item' => __('Add New Pack', 'packages-backend') ,
            'edit_item' => __('Edit Pack', 'packages-backend') ,
            'new_item' => __('New Pack', 'packages-backend') ,
            'all_items' => __('All Packs', 'packages-backend') ,
            'view_item' => __('View Pack', 'packages-backend') ,
            'search_items' => __('Search Packs', 'packages-backend') ,
            'not_found' => __('No Pack found', 'packages-backend') ,
            'not_found_in_trash' => __('NoPacks found in Trash', 'packages-backend') ,
            'parent_item_colon' => '',
            'menu_name' => __('Packs', 'packages-backend')
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

    $package = new AE_Package('pack', array('project_type'));
    $pack_action = new AE_PackAction($package);

    global $ae_post_factory;
    $ae_post_factory->set('pack', $package);
}

class FRE_Payment extends AE_Payment
{
    
    function __construct() {
        $this->no_priv_ajax = array();
        $this->priv_ajax = array(
            'et-setup-payment'
        );
        $this->init_ajax();
    }
    
    public function get_plans() {
        global $ae_post_factory;
        $pack = $ae_post_factory->get('pack');
        return $pack->fetch();
    }
}

new FRE_Payment();