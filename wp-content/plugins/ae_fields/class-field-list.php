<?php

/**
 * class AE_list
 * render list option and form to control list
 * @version 1.0
 * @package AE
 * @author Dakachi
 */
class AE_field_list extends AE_list
{
    
    public $item;
    public $data;
    
    /**
     * contruct a list settings in backend
     * @param array $args :
     - name : required  option name
     - id
     - title
     - form param array contain form args
     * @param $template
     - template the item template path  (php render)
     - js_template js item template path (for js app)
     - form The form use to submit an item (php render)
     - form_js The js form template use to edit an item  (for js app)
     * @param $data  pack list data
     * @package AE
     * @version 1.0
     */
    function __construct($args = array() , $template = array() , $data) {
        
        $this->data = $data;
        $this->params = $args;
        
        if (!isset($this->params['custom_field']) || !$this->params['custom_field']) {
            $this->params['custom_field'] = 'meta';
        }
        
        if (!empty($template)) {
            
            $this->template = ($template['template']);
            
            $this->js_template = ($template['js_template']);
            
            $this->form_template = ($template['form']);
            $this->form_js_template = ($template['form_js']);
        } else {
            $this->template = ae_get_path() . '/template/post-item.php';
            $this->js_template = ae_get_path() . '/template-js/post-item.php';
            
            $this->form_template = ae_get_path() . '/template/add-pack-form.php';
            $this->form_js_template = ae_get_path() . '/template-js/add-pack-form.php';
        }
    }
    
    // construct
    
    
    
    /**
     * render html and template
     */
    function render() {
        global $ae_post_factory;
        
        // get list data
        $ae_pack = $ae_post_factory->get('ae_field');
        $this->data = $ae_pack->fetch('ae_field', array(
            'meta_query' => array(
                array(
                    'key' => 'type',
                    'value' => $this->params['custom_field'],
                    'compare' => '='
                )
            )
        ));
        // return ;
        echo '<div class="title group-' . $this->params['id'] . '">' . $this->params['title'] . '</div>';
        echo '<div data-option-name="' . $this->params['name'] . '" class="desc pack-control " id="control-' . $this->params['custom_field'] . '" data-template="' . $this->params['custom_field'] . '">';
        
        echo '<ul class="pay-plans-list sortable" >';
        if (!empty($this->data)) {
            foreach ($this->data as $key => $item) {
                $this->item = $item;
                include ($this->template);
            }
        }
        echo '</ul>';
?>  
            <input id="confirm_delete_<?php
        echo $this->params['custom_field']; ?>" value="<?php
        _e("Are you sure you want to delete this field?", ET_DOMAIN); ?>" type="hidden" />
            <!-- add new item form -->
            <div class="item">
                <?php
        include ($this->form_template); ?>
            </div>
            <!-- edit item form template -->
            <?php
        load_template($this->form_js_template); ?>
            <!-- json data for pack view -->
            <script type="application/json" id="ae_list_<?php
        echo $this->params['custom_field']; ?>">
                <?php
        echo json_encode($this->data); ?>
            </script>
            <!-- js template for item view -->
            <script type="text/template" id="ae-template-<?php
        echo $this->params['custom_field']; ?>">
                <?php
        load_template($this->js_template, false); ?>
            </script>
    <?php
        echo '</div>';
    }
    
    // render
    
    
}

/**
 * this file contain all function related to places
 */
add_action('init', 'et_init_ae_field', 12);
function et_init_ae_field() {
    
    register_post_type('ae_field', array(
        'labels' => array(
            'name' => __('Custom fields', ET_DOMAIN) ,
            'singular_name' => __('Custom fields', ET_DOMAIN) ,
            'add_new' => __('Add New', ET_DOMAIN) ,
            'add_new_item' => __('Add New Field', ET_DOMAIN) ,
            'edit_item' => __('Edit Field', ET_DOMAIN) ,
            'new_item' => __('New Field', ET_DOMAIN) ,
            'all_items' => __('All Fields', ET_DOMAIN) ,
            'view_item' => __('View Field', ET_DOMAIN) ,
            'search_items' => __('Search Fields', ET_DOMAIN) ,
            'not_found' => __('No Field found', ET_DOMAIN) ,
            'not_found_in_trash' => __('NoFields found in Trash', ET_DOMAIN) ,
            'parent_item_colon' => '',
            'menu_name' => __('Custom Fields', ET_DOMAIN)
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
    
    global $ae_post_factory;
    $package = new AE_Pack('ae_field', array(
        'type',
        'label',
        'field_type',
        'field_for',
        'required',
        'placeholder'
    ));
    $ae_post_factory->set('ae_field', $package);
    
    /**
     * register custom taxonomy for post type
     * @author Dakachi
     */
    $custom_fields = get_posts(array(
        'post_type' => 'ae_field',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'type',
                'value' => 'tax',
                'compare' => '='
            )
        ), 
        'showposts' => -1
    ));

    if (!empty($custom_fields)) {
        foreach ($custom_fields as $key => $field) {
            $label = get_post_meta( $field->ID, 'label', true);
            $field_for = get_post_meta( $field->ID, 'field_for', true);
            register_taxonomy($field->post_title, '', array(
                'label' => $label,
                'hierarchical' => true
            ));
            register_taxonomy_for_object_type($field->post_title, $field_for);
        }
    }
}
