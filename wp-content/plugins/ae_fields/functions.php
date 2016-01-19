<?php
/**
 * filter posts order by to order ae_field post by menu order 
 * @param string $orderby The query orderby string
 * @param object $query Current wp_query object
 * @since 1.0
 * @author Dakachi
 */
add_filter('posts_orderby', 'ae_order_field_by_menu_order', 10, 2);
function ae_order_field_by_menu_order($orderby, $query) {
    global $wpdb;
    if($query->query_vars['post_type'] != 'ae_field') return $orderby;
    $orderby = "{$wpdb->posts}.menu_order ASC";
    return $orderby;
}

/**
 * get post custom field 
 * @param string $field the field name
 * @param WP_Post|int $post the post want to show field
 * @since 1.0
 * @author Dakachi
 */
function et_get_field($field , $post = '') {
    if(!$post) {
        global $post, $ae_post_factory;
        $object = $ae_post_factory->get($post->post_type);
        return $object->current_post->$field;
    }
    if(is_int($post)) {
        return get_post_meta( $post, $field, true );    
    }
    return get_post_meta( $post->ID, $field, true );
}
/**
 * render post cutom field data 
 * @param string $field the field name
 * @param WP_Post|int $post the post want to show field
 * @since 1.0
 * @author Dakachi
 */
function et_the_field($field, $post = '') {
    if(!$post) {
        global $post, $ae_post_factory;
        $object = $ae_post_factory->get($post->post_type);
        echo $object->current_post->$field;
        return;
    }
    if(is_int($post)) {
        echo get_post_meta( $post, $field, true );  
        return;
    }
    echo get_post_meta( $post->ID, $field, true );
}

/**
 * render custom field data in single post 
 * @param object $post
 * @since 1.0
 * @author Dakachi
 */
function et_render_custom_field($post){
    et_render_custom_taxonomy($post);
    et_render_custom_meta($post);
}
/**
 * render post custome meta field 
 * @param object $post
 * @since 1.0
 * @author Dakachi
 */
function et_render_custom_meta($post){
    global $ae_post_factory;
    $post_type = $post->post_type;

    global $ae_post_factory;
    $post_type = $post->post_type;

    $field_object = $ae_post_factory->get('ae_field');
    $custom_fields = $field_object->fetch('ae_field', array(
        'meta_query' => array(
            array(
                'key' => 'field_for',
                'value' => $post_type,
                'compare' => '='
            )
        )
    ));

    if (!empty($custom_fields)) {
        foreach ($custom_fields as $key => $field) {
            $tax_list = '';
            $field_value = '';
            if($field->field_for != $post_type || $field->type == 'tax') continue;
            // check field value is empty or not
            $field_value = et_get_field($field->post_title, $post->ID);
            // change nl to br
            if($field->field_type == 'textarea') {
                $field_value = wpautop( $field_value );
            }
            if(!$field_value) continue;
            // render field
            echo '<div class="custom-field-wrapper '.$field->post_title.'-wrapper" >';
            echo '<span class="ae-field-title '.$field->post_title.'-title">'.$field->label.':</span>';
            
            echo '<span class="'.$field->post_title.'-title" >';
            echo $field_value;
            echo '</span>';
            
            echo '</div>';
        }
    }
}

function et_render_custom_taxonomy($post){
    global $ae_post_factory;
    $post_type = $post->post_type;

    $field_object = $ae_post_factory->get('ae_field');
    $custom_fields = $field_object->fetch('ae_field', array(
        'meta_query' => array(
            array(
                'key' => 'field_for',
                'value' => $post_type,
                'compare' => '='
            )
        )
    ));

    if (!empty($custom_fields)) {
        foreach ($custom_fields as $key => $field) {
            $tax_list = '';
            $field_value = '';
            if($field->field_for != $post_type || $field->type != 'tax') continue;
            // check tax value is empty or notif ($field->type == 'tax') {
            $tax_list = get_the_taxonomy_list( $field->post_title, $post );
            if($tax_list == '') continue;
          
            // render field
            echo '<div class="custom-field-wrapper '.$field->post_title.'-wrapper" >';
            echo '<span class="ae-field-title '.$field->post_title.'-title">'.$field->label.':</span>';
            
            echo $tax_list;
            
            echo '</div>';
        }
    }
}


if (!function_exists('ae_add_field_to_submit_form')) {
/**
 * function render form field to submit form
 * @param string $post_type the post type will be use the extra field
 * @param string $post Current post is be renew
 * @since 1.0
 * @author Dakachi
 */
function ae_add_field_to_submit_form($post_type, $post) {
    global $ae_post_factory;
    $field_object = $ae_post_factory->get('ae_field');
    $fields = $field_object->fetch('ae_field', array(
        'meta_query' => array(
            array(
                'key' => 'field_for',
                'value' => $post_type,
                'compare' => '='
            )
        )
    ));
    
    foreach($fields as $key => $field) {
        if($field->field_for != $post_type) continue;
        $field_object->convert($field);
        load_template( plugin_dir_path(__FILE__) . '/template/submit-field.php', false );
    }
}
add_action('ae_submit_post_form', 'ae_add_field_to_submit_form', 10, 2);
}


if (!function_exists('ae_add_field_to_edit_form')) {
/**
 * function render form field to edit form
 * @param string $post_type the post type will be use the extra field
 * @param string $post Current post is be renew
 * @since 1.0
 * @author Dakachi
 */
function ae_add_field_to_edit_form($post_type, $post) {
    global $ae_post_factory;
    $field_object = $ae_post_factory->get('ae_field');
    $fields = $field_object->fetch('ae_field', array(
        'meta_query' => array(
            array(
                'key' => 'field_for',
                'value' => $post_type,
                'compare' => '='
            )
        )
    ));

    foreach($fields as $key => $field) {
        if($field->field_for != $post_type) continue;
        $field_object->convert($field);
        load_template( plugin_dir_path(__FILE__) . '/template/edit-field.php', false );
    }
}
add_action('ae_edit_post_form', 'ae_add_field_to_edit_form', 10, 2);
}


if(!function_exists('add_field_to_post_object')) {
/**
 * function insert custom meta field to post type object 
 * @param Array $fields The posttype current meta
 * @param String $post_type
 * @since 1.0
 * @author Dakachi
 */
function add_field_to_post_object($fields, $post_type){
    if($post_type == 'ae_field') return $fields;
    
    $custom_fields = get_posts(array(
        'post_type' => 'ae_field',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'field_for',
                'value' => $post_type,
                'compare' => '='
            )
        ),
        'showposts' => -1
    ));

    if(empty($custom_fields)) return $fields;
    // add meta to post object
    foreach($custom_fields as $key => $field) {

        $field_for = get_post_meta($field->ID, 'field_for', true);
        $type = get_post_meta($field->ID, 'type', true);

        if( $type == 'meta' )  {
            $fields[] = strtolower($field->post_title);
        }
    }  
    return $fields;

}
add_filter( 'ae_post_meta_fields', 'add_field_to_post_object' , 10, 2);
}

if(!function_exists('add_tax_to_post_object')) {
/**
 * function insert custom meta field to post type object 
 * @param Array $fields The posttype current meta
 * @param String $post_type
 * @since 1.0
 * @author Dakachi
 */
function add_taxs_to_post_object($fields, $post_type){
    if($post_type == 'ae_field') return $fields;

    $custom_fields = get_posts(array(
        'post_type' => 'ae_field',
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'field_for',
                'value' => $post_type,
                'compare' => '='
            )
        ), 
        'showposts' => -1
    ));
    
    if(empty($custom_fields)) return $fields;
    foreach($custom_fields as $key => $field) {
        $type = get_post_meta($field->ID, 'type', true);
        // add taxs to post object
        if( $type == 'tax' )  {
            $fields[] = strtolower($field->post_title);
        }
    } 
    return $fields;
}
add_filter( 'ae_post_taxs', 'add_taxs_to_post_object' , 10, 2);
}


add_action('ae_field_support_post_types' , 'ae_field_suport_post_type');
function ae_field_suport_post_type($post_types) {
    if(THEME_NAME == 'freelanceengine') {
        return array('fre_profile', 'project');
    }
    if(THEME_NAME == 'directoryengine') {
        return array('place');
    }
    return $post_types;
}


add_action('wp_head', 'custom_field_css');
function custom_field_css(){
?>
    <style type="text/css">
    .custom-field .chosen-container{
        display: block;
    }
    .custom-field .chosen-container .chosen-single{
        border-radius: 0;
        padding-left: 12px;
        height: 34px;
        line-height: 34px;
        background: transparent;
        border: 1px solid #d7d8da;
        box-shadow: none;
    }
    .custom-field .chosen-container-single .chosen-single div b {
        background: url(../img/chosen-sprite.png) no-repeat 0 6px;
    }
    .custom-field input[type=checkbox],
    .custom-field input[type=radio]{
        margin-right: 5px;
    }
    .custom-field.radio-field .fa-exclamation-triangle,
    .custom-field.checkbox-field .fa-exclamation-triangle{
        top:30px;
    }
    .checkbox-field.error  .fa-exclamation-triangle,
    .radio-field.error  .fa-exclamation-triangle {
        top : 5px;
    }
    .custom-field.radio-field .message,
    .custom-field.checkbox-field .message {
        margin-top: -10px;
    }
    .form_modal_style label {
        display: inline-block;
    }
    .form-group.custom-field  label.control-label {
        display: block;
    }
    textarea.form-control {
        padding-top : 10px;
    }

    .ae-field-title {
        color: #2c3d4f;
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 15px;
        display: block;
    }
    .custom-field-wrapper a {
        color: #5d5f5e;
    }
    .custom-field-wrapper li {
        padding: 5px 10px;
        -moz-border-radius: 3px;
        -webkit-border-radius: 3px;
        border-radius: 3px;
        -moz-background-clip: padding;
        -webkit-background-clip: padding-box;
        background-clip: padding-box;
        background-color: #f1f5f7;
        margin-right: 10px;
        margin-bottom: 10px;
        display: inline-block;
    }
    .custom-field-wrapper ul {
        padding: 0;
        margin: 0;
        list-style: none
    }
</style>
<?php 
}

//add_action('admin_print_footer_scripts', 'ae_field_backend_script');
function ae_field_backend_script() {
?>
    <script type="text/javascript">
    (function($){
        $(document).ready(function() {
            $.validator.addMethod("username", function(value, element) {
                var ck_username = /^[a-z0-9_]{1,20}$/;
                return ck_username.test(value);
            }, '<?php _e("Enter lowercase, do not leave spaces between the name.", ET_DOMAIN); ?>');

            $.validator.addClassRules('is_packname', { required: true, username : true });
        });
    })(jQuery);
    </script>
<?php
}