<?php

/**
 * this file contain all ae core template function
 * List post
 * Template part function
 */

/**
 * list post with template
 * @param String $post_type The list of post type you want to render
 * @param Array $args   - WP_Query args query
 *                      - json data id
 */
function ae_list_post($post_type, $args = array()) {
    
    global $ae_post_factory;
    
    $object = $ae_post_factory->get($post_type);
    if ($object) {
        
        $query = $object->query($args);
        
        $json_data = array();
        
        if ($query->have_posts()) {
            
            // post list
            echo '<ul>';
            while ($query->have_posts()) {
                $query->the_post();
                global $post;
                
                $convert = $object->convert($post);
                $json_data[] = $convert;
                
                // get post type item template
                ae_post_template_part('template/loop', $post_type);
            }
            echo '</ul>';
            // print js template for post item
            ae_post_template_part('template-js/loop', $post_type);
            
            // print post array json data
            if (!isset($args['json_id'])) $args['json_id'] = 'ae-' . $post_type . '-json';
            echo '<script type="json/data" id="' . $args['json_id'] . '"> ' . json_encode($json_data) . '
                </script>';

        }
    }
}

/**
 * list post with template by default query
 * @since 1.0
 * @author Dakachi
 */
function ae_default_list_post() {
    
    global $ae_post_factory;
    if (have_posts()) {
        
        // post list
        echo '<ul>';
        while (have_posts()) {
            the_post();
            global $post;
            $object = $ae_post_factory->get($post->post_type);
            $convert = $object->convert($post);
            $json_data[] = $convert;
            
            // get post type item template
            ae_post_template_part('template/loop', $post->post_type);
        }
        echo '</ul>';
        
        // print js template for post item
        ae_post_template_part('template-js/loop', $post->post_type);
        
        // print post array json data
        if (!isset($args['json_id'])) $args['json_id'] = 'ae-' . $post->post_type . '-json';
        echo '<script type="json/data" id="' . $args['json_id'] . '"> ' . json_encode($json_data) . '
                </script>';
    }
}

/**
 * Load a post template part into a template
 *
 * Makes it easy for a theme to reuse sections of code in a easy to overload way
 * for child themes.
 *
 * Includes the named template part for a theme or if a name is specified then a
 * specialised part will be included. If the theme contains no {slug}.php file
 * then no template will be included.
 *
 * The template is included using require, not require_once, so you may include the
 * same template part multiple times.
 *
 * For the $name parameter, if the file is called "{slug}-special.php" then specify
 * "special".
 *
 * @since 1.0
 *
 * @uses locate_template()
 *
 * @param string $slug The slug name for the generic template.
 * @param string $post_type The name of the post type template.
 */

function ae_post_template_part($slug, $post_type) {
    $templates = array();
    if ('' !== $post_type) {
        $templates[] = "{$slug}-{$post_type}.php";
    }
    
    $templates[] = "{$slug}.php";
    $templates[] = "{$slug}-post.php";
    
    // $templates[] = "includes/aecore/{$slug}-item.php";
    
    locate_template($templates, true, false);
}

if(!function_exists('ae_pagination')):
/**
 * render posts list pagination link
 * @param $wp_query The WP_Query object for post list
 * @param $current if use default query, you can skip it
 * @author Dakachi
*/
function ae_pagination( $query, $current = '', $type = 'page', $text = '') {
    
    $query_var  =   array();
    /**
     * posttype args
    */
    $query_var['post_type']     =   $query->query_vars['post_type'] != ''  ? $query->query_vars['post_type'] : 'post' ;
    $query_var['post_status']   =   isset( $query->query_vars['post_status'] ) ? $query->query_vars['post_status'] : 'publish';
    $query_var['orderby']       =   isset( $query->query_vars['orderby'] ) ? $query->query_vars['orderby'] : 'date';
    // taxonomy args
    $query_var['place_category']   =   isset( $query->query_vars['place_category'] ) ? $query->query_vars['place_category'] : '';
    $query_var['location']   =   isset( $query->query_vars['location'] ) ? $query->query_vars['location'] : '';
    $query_var['showposts']   =   isset( $query->query_vars['showposts'] ) ? $query->query_vars['showposts'] : '';
    /**
     * order
    */
    $query_var['order']         =   $query->query_vars['order'];
    
    if(!empty($query->query_vars['meta_key']))
        $query_var['meta_key']      =   isset( $query->query_vars['meta_key'] ) ? $query->query_vars['meta_key'] : 'rating_score';

    $query_var  =   array_merge($query_var, (array)$query->query );
    $query_var['paginate'] = $type;
    echo '<script type="application/json" class="ae_query">'. json_encode($query_var). '</script>';

    if( ($query->max_num_pages <= 1 && !et_load_mobile() ) || !$type ) return ;
    $style = '';
    if(et_load_mobile() && $query->max_num_pages <= 1) {
        $style ="style='display:none'";
    }

    echo '<div class="paginations" '.$style.'>';
    if($type === 'page') {
        $big = 999999999; // need an unlikely integer
        echo paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, ($current) ? $current : get_query_var('paged') ),
            'total'     => $query->max_num_pages,
            'next_text' => '<i class="fa fa-angle-double-right"></i>',
            'prev_text' => '<i class="fa fa-angle-double-left"></i>',     
        ) );    
    }else {
        if($text == '') {
            $text = __("Load more", ET_DOMAIN);
        }
        echo '<a id="'.$query_var['post_type'].'-inview" class="inview load-more-post" >'. $text .'</a>';
    }

    echo '</div>';
    
}
endif;


if(!function_exists('ae_comments_pagination')):
/**
 * render posts list pagination link
 * @param Int $total total pages of comment
 * @param Int $current The current page if use default query, you can skip it
 * @param Array $query_args the comment query args
 * @author Dakachi
*/
function ae_comments_pagination( $total, $current = '', $query_args = array()) {
    
    if(!empty($query_args)) {
        echo '<script type="application/json" class="ae_query">'. json_encode($query_args) . '</script>';
    }
    // don not use paginate or load more
    if(!isset($query_args['paginate']) || !$query_args['paginate'] ) return;
    // render paginate
    echo '<div class="paginations">';
    if( $query_args['paginate'] == 'page') { // paging
        
        $big = 999999999; // need an unlikely integer
        echo paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, ($current) ? $current : get_query_var('paged') ),
            'total'     => $total,
            'next_text' => '<i class="fa fa-angle-double-right"></i>',
            'prev_text' => '<i class="fa fa-angle-double-left"></i>',       
        ) );
    }else { // load more
        if($total > $current ) {
            $text = (isset($query_args['text'])) ? $query_args['text'] : '';
            if(!et_load_mobile() && !$text ){
                 $text = __("Load more", ET_DOMAIN);
            }
            echo '<a id="'.$query_args['type'].'-inview" class="inview load-more-post" >'. $text .'</a>';
        }
    }        
    
    echo '</div>';
}
endif;


if(!function_exists('ae_user_pagination')):
/**
 * render posts list pagination link
 * @param $total total pages of users list
 * @param $current if use default query, you can skip it
 * @author Dakachi
*/
function ae_user_pagination ($query_args = array(), $total, $current = '' ) {
    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; 
    if(!empty($query_args)) {
        if(isset($query_args['paged']) ){
            if($paged == 1){
                $paged = $query_args['paged'];
            }
            $query_args['offset'] = $total * ($paged - 1);
        }
        echo '<script type="application/json" class="ae_query">'. json_encode($query_args) . '</script>';
    }
    echo '<div class="paginations" >';
    if(!isset($query_args['paginate']) || (isset($query_args['paginate']) && $query_args['paginate'] == 'page')) {
        $big = 999999999; // need an unlikely integer
        echo paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, ($current) ? $current : $paged ),
            'total'     => $total,
            'next_text' => '<i class="fa fa-angle-double-right"></i>',
            'prev_text' => '<i class="fa fa-angle-double-left"></i>',     
        ) );    
    }else {
        if($total > $current ) {
            $text = 'load more';
            if(!et_load_mobile()) $text = (isset($query_args['text'])) ? $query_args['text'] : __("Load more", ET_DOMAIN);
            echo '<a id="'.$query_args['type'].'-inview" class="inview load-more-post" >'. $text .'</a>';
        }
    }     

    echo '</div>';
}
endif;

if(!function_exists('ae_pre_loading')) {
/**
 * render site preloading html from option
 * @author Dakachi
 * @return void
 */
function ae_pre_loading() {
    $img = get_template_directory_uri()."/img/preloading-logo.png";
    $options = AE_Options::get_instance();
    // save this setting to theme options
    $pre_loading = $options->pre_loading;
    if(!empty($pre_loading)) {
        $img = $pre_loading['thumbnail'][0];
    }
?>
    <!-- Preloading -->
    <div class="mask-color">
        <div id="preview-area">
            <div class="logo-image-preloading">
                <img src="<?php echo $img ?>" alt="<?php echo bloginfo( 'name' ) ?>">
            </div>
            <div class="spinner">
              <div class="bounce1"></div>
              <div class="bounce2"></div>
              <div class="bounce3"></div>
            </div>
        </div>
        <div class="page-main"></div>
    </div>
    <!-- Preloading / End -->
<?php
}
}


if(!function_exists('ae_logo')) {
/**
 * render site logo image get from option
 * @author Dakachi
 * @return void
 */
function ae_logo () {
    $img = get_template_directory_uri()."/img/logo-de.png";
    $options = AE_Options::get_instance();
    // save this setting to theme options
    $site_logo = $options->site_logo;
    if(!empty($site_logo)) {
        $img = $site_logo['large'][0];
    }
    echo '<img alt="'.$options->blogname.'" src="'. $img .'" />';
}
}

if(!function_exists('ae_mobile_logo')) {
/**
 * render mobile site logo image get from option
 * @author Dakachi
 * @return void
 */
function ae_mobile_logo () {
    $img = get_template_directory_uri()."/img/logo-mobile.png";
    $options = AE_Options::get_instance();
    // save this setting to theme options
    $mobile_logo = $options->mobile_logo;
    if(!empty($mobile_logo)) {
        $img = $mobile_logo['large'][0];
        echo '<img alt="'.$options->blogname.'" src="'. $img .'" />';
    }else {
        ae_logo();
    }
    
}
}

if(!function_exists('ae_favicon')) {
/**
 * render mobile icon, favicon image get from option
 * @author Dakachi
 * @return void
 */
function ae_favicon () {
    $img = get_template_directory_uri()."/img/favicon.png";
    $options = AE_Options::get_instance();
    // save this setting to theme options
    $mobile_icon = $options->mobile_icon;
    if(!empty($mobile_icon)) {
        $img = $mobile_icon['thumbnail'][0];
    }
    echo '<link href="'. $img .'" rel="shortcut icon" type="image/x-icon">';
    if(et_load_mobile()) {
        echo '<link href="'. $img .'" rel="apple-touch-icon" />';
    }
}
}


if(!function_exists( 'ae_tax_dropdown' )) {
/**
 * Display or retrieve the HTML dropdown list of categories
 * The list of arguments is below:
 *     'show_option_all' (string) - Text to display for showing all categories.
 *     'show_option_none' (string) - Text to display for showing no categories.
 *     'orderby' (string) default is 'ID' - What column to use for ordering the
 * categories.
 *     'order' (string) default is 'ASC' - What direction to order categories.
 *     'show_count' (bool|int) default is 0 - Whether to show how many posts are
 * in the category.
 *     'hide_empty' (bool|int) default is 1 - Whether to hide categories that
 * don't have any posts attached to them.
 *     'child_of' (int) default is 0 - See {@link get_categories()}.
 *     'exclude' (string) - See {@link get_categories()}.
 *     'echo' (bool|int) default is 1 - Whether to display or retrieve content.
 *     'depth' (int) - The max depth.
 *     'tab_index' (int) - Tab index for select element.
 *     'name' (string) - The name attribute value for select element.
 *     'id' (string) - The ID attribute value for select element. Defaults to name if omitted.
 *     'class' (string) - The class attribute value for select element.
 *     'selected' (int) - Which category ID is selected.
 *     'taxonomy' (string) - The name of the taxonomy to retrieve. Defaults to category.
 *     'value' (string) - value in option is id or slug, name
 *
 * The 'hierarchical' argument, which is disabled by default, will override the
 * depth argument, unless it is true. When the argument is false, it will
 * display all of the categories. When it is enabled it will use the value in
 * the 'depth' argument.
 * @author Dakachi
*/
function ae_tax_dropdown( $tax, $args = array() ) {

    $defaults = array(
        'show_option_all' => '', 'show_option_none' => '',
        'orderby' => 'name', 'order' => 'ASC',
        'show_count' => 0,
        'hide_empty' => 1, 'child_of' => 0,
        'exclude' => '', 'echo' => 1,
        'selected' => 0, 'hierarchical' => 0,
        'name' => $tax, 'id' => '',
        'class' => 'postform', 'depth' => 0,
        'tab_index' => 0, 'taxonomy' => $tax,
        'hide_if_empty' => false , 'attr' => ''
    );

    $defaults['selected'] = ( is_tax( 'tax' ) ) ? get_query_var( $tax ) : 0;

    // Back compat.
    if ( isset( $args['type'] ) && 'link' == $args['type'] ) {
        _deprecated_argument( __FUNCTION__, '3.0', '' );
        $args['taxonomy'] = 'link_category';
    }

    $r = wp_parse_args( $args, $defaults );

    if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] ) {
        $r['pad_counts'] = true;
    }

    extract( $r );

    $tab_index_attribute = '';
    if ( (int) $tab_index > 0 )
        $tab_index_attribute = " tabindex=\"$tab_index\"";

    if(isset($r['name'])) unset($r['name']);
    $categories = get_terms( $taxonomy, $r );
    $name = esc_attr( $name );
    $class = esc_attr( $class );
    $id = $id ? "id='".esc_attr( $id )."'" : '';

    if ( ! $r['hide_if_empty'] || ! empty($categories) )
        $output = "<select $attr name='$name' $id class='$class' $tab_index_attribute>\n";
    else
        $output = '';

    if ( empty($categories) && ! $r['hide_if_empty'] && !empty($show_option_none) ) {

        /**
         * Filter a taxonomy drop-down display element.
         *
         * A variety of taxonomy drop-down display elements can be modified
         * just prior to display via this filter. Filterable arguments include
         * 'show_option_none', 'show_option_all', and various forms of the
         * term name.
         *
         * @since 1.2.0
         *
         * @see wp_dropdown_categories()
         *
         * @param string $element Taxonomy element to list.
         */
        $show_option_none = apply_filters( 'list_cats', $show_option_none );
        $output .= "\t<option value='-1' selected='selected'>$show_option_none</option>\n";
    }

    if ( ! empty( $categories ) ) {

        if ( $show_option_all ) {

            /** This filter is documented in wp-includes/category-template.php */
            $show_option_all = apply_filters( 'list_cats', $show_option_all );
            $selected = ( '0' === strval($r['selected']) ) ? " selected='selected'" : '';
            $output .= "\t<option value=''$selected>$show_option_all</option>\n";
        }

        if ( $show_option_none ) {

            /** This filter is documented in wp-includes/category-template.php */
            $show_option_none = apply_filters( 'list_cats', $show_option_none );
            $selected = ( '-1' === strval($r['selected']) ) ? " selected='selected'" : '';
            $output .= "\t<option value='-1'$selected>$show_option_none</option>\n";
        }

        if ( $hierarchical )
            $depth = $r['depth'];  // Walk the full depth.
        else
            $depth = -1; // Flat.

        $output .= ae_walk_tax_dropdown_tree( $categories, $depth, $r );
    }

    if ( ! $r['hide_if_empty'] || ! empty($categories) )
        $output .= "</select>\n";

    /**
     * Filter the taxonomy drop-down output.
     *
     * @since 2.1.0
     *
     * @param string $output HTML output.
     * @param array  $r      Arguments used to build the drop-down.
     */
    $output = apply_filters( 'ae_dropdown_tax', $output, $r );

    if ( $echo )
        echo $output;

    return $output;
}
}

/**
 * Retrieve HTML dropdown (select) content for category list.
 *
 * @uses Walker_CategoryDropdown to create HTML dropdown content.
 * @since 2.1.0
 * @see Walker_CategoryDropdown::walk() for parameters and return description.
 */
function ae_walk_tax_dropdown_tree() {
    $args = func_get_args();
    // the user's options are the third parameter
    if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
        $walker = new AE_Walker_TaxDropdown;
    else
        $walker = $args[2]['walker'];

    return call_user_func_array(array( &$walker, 'walk' ), $args );
}


/**
 * Create HTML dropdown list of taxonomies.
 *
 * @package AE
 * @since 1.0
 * @uses Walker
 */
class AE_Walker_TaxDropdown extends Walker_CategoryDropdown{
    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     * @since 2.1.0
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category Category data object.
     * @param int    $depth    Depth of category. Used for padding.
     * @param array  $args     Uses 'selected' and 'show_count' keys, if they exist. @see wp_dropdown_categories()
     */
    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        $pad = '';

        /** This filter is documented in wp-includes/category-template.php */
        $cat_name = apply_filters( 'list_cats', $category->name, $category );

        if(isset($args['value']) && $args['value'] == 'slug' ) {
            $output .= "\t<option class=\" $category->slug  level-$depth\" value=\"".$category->slug."\"";    
        } else {
            $output .= "\t<option class=\" $category->slug  level-$depth\" value=\"".$category->term_id."\"";    
        }
        // if(isset($args['value'])  && $args['value'] == 'slug' ) {
        // for multi select
        if(is_array($args['selected'])) {
            
            if(in_array($category->slug , $args['selected']) || in_array($category->term_id , $args['selected']) ) {
                $output .= ' selected="selected"';
            }

        }else{ // single select or single value selected
            if ( $category->slug === $args['selected'] || $category->term_id === $args['selected'] ) {
                $output .= ' selected="selected"';
            }    
        }
            
        
        $output .= '>';
        $output .= $pad.$cat_name;
        if ( $args['show_count'] )
            $output .= '('. number_format_i18n( $category->count ) .')';
        $output .= "</option>\n";
    }
}


if(!function_exists('the_taxonomy_list')) {
/**
* Retrieve category list in either HTML list or custom format.
*
* @since 1.1
*
* @param string $separator Optional, default is empty string. Separator for between the categories.
* @param string $parents Optional. How to display the parents.
* @param int $post_id Optional. Post ID to retrieve categories.
* @return string
*/
function the_taxonomy_list( $taxonomy = 'category', $link_before = '', $link_after = '' ) {
    global $post;
    $product_terms = wp_get_object_terms($post->ID, $taxonomy);
    if(!empty($product_terms)){
        if(!is_wp_error( $product_terms )){
            echo '<ul>';
            foreach($product_terms as $term){
                echo '<li><a href="'.get_term_link($term->slug, $taxonomy).'"">'.$link_before.$term->name.$link_after.'</a></li>';
            }
            echo '</ul>';
        }
    }
}
}

if(!function_exists('get_the_taxonomy_list')) {
/**
* Retrieve category list in either HTML list or custom format.
*
* @since 1.1
*
* @param string $separator Optional, default is empty string. Separator for between the categories.
* @param string $parents Optional. How to display the parents.
* @param int $post_id Optional. Post ID to retrieve categories.
* @return string
*/
function get_the_taxonomy_list( $taxonomy = 'category', $post ='', $link_before = '', $link_after = '', $args = array() ) {
    if(!$post) {
        global $post;
    }
    $product_terms = wp_get_object_terms($post->ID, $taxonomy, $args);
    if(!empty($product_terms)){
        if(!is_wp_error( $product_terms )){
            ob_start();
            echo '<ul class="'.$taxonomy.'-wrapper tax-wrapper">';
            foreach($product_terms as $term){
                echo '<li><a href="'.get_term_link($term->slug, $taxonomy).'"">'.$link_before.$term->name.$link_after.'</a></li>';
            }
            echo '</ul>';
            $b = ob_get_clean();
            return $b;
        }
    }
    return '';
}
}

/**
 *selected for multi select box
 *@param array $selected
 *@param string $cureent 
 *@param $echo boolean
 *@return string
 */
function multi_selected( $selected, $current = true, $echo = true) {
    if(gettype($selected) === 'array'){
        if(in_array($current, $selected)){
            $result = " selected='selected'";
        }
        else{
            $result = '';
        }
    }
    else if ( (string) $selected === (string) $current )
        $result = " selected='selected'";
    else
        $result = '';

    if ( $echo )
        echo $result;

    return $result;
}
