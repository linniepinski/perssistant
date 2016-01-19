<?php
class CE_Field_Tax extends ET_TaxCategory
{
    static $instance    =   null;
    protected $_tax_name    = CE_AD_CAT;
    protected $_order       = 'et_ad_category_order';
    protected $_transient   = 'ad_category';
    protected $_tax_label   =  'Ad Category';
    // protected $_color       =  'job_available_colors';

    public function __construct($tax = array()){
        if ( !empty($tax) && !empty($tax['tax_name']) ){
            $this->_tax_name    = $tax['tax_name'];
            $this->_transient   = $tax['tax_name'];
            $this->_tax_label   = $tax['tax_name'];
            $this->_order       = 'et_'.$tax['tax_name'].'_order';
        }
    }
    public static function register ($tax = array(),$slug = '') {

        $slug   =   get_theme_mod($this->_tax_name  , 'ad_cat' );

        self::get_instance()->register_action ();


    }

    public static function get_instance () {
        if ( self::$instance == null){
            self::$instance = new CE_Field_Tax();
        }
        return self::$instance;
    }

    public static function slug() {
        return get_theme_mod( CE_Field_Tax , 'ad_cat123' );
    }

    /**
     * get term in ordered
    */
    function get_terms_in_order ($args = array()) {
        if ( get_transient( $this->_transient) == false ) {
           $this->refesh_terms();
        }

        $this->_term_in_order    =   get_transient($this->_transient);
        return $this->_term_in_order;
    }

    public   function get_term_list ($args=array()) {
        $terms  =   $this->get_terms_in_order ($args);
        if(empty($terms)) {
            $terms  =   get_terms(  $this->_tax_name , array ('hide_empty' => false ));
        }
        return $terms;
    }
    /**
     * register action do with ad category
    */
    public function register_action  () {

        add_action( 'delete_'.$this->_tax_name, array(&$this,'delete_transient' ) );
        add_action( 'created_'.$this->_tax_name,array(&$this, 'delete_transient' ));

        add_action( 'edited_'.$this->_tax_name,array(&$this, 'update_transient' ));

        add_action('save_post' , array(&$this, 'update_transient' ) );

    }

    function update_transient () {
        delete_transient( $this->_transient );
    }

    public function delete_transient ( $term_id ) {

        $order      = (array)get_option($this->_order);
        $term       = get_term_by( 'id', $term_id, $this->_tax_name );
        if($term && $term->parent) {
            $flag   =   0;
            foreach ($order as $key => $value) {
                if($value['item_id'] == $term->parent)  {
                    $flag = 1;
                    continue;
                }
                if($flag == 1 && $value['parent_id'] != $term->parent )  break;
            }
            array_splice( $order, $key , 0, array( array('item_id' => $term_id, 'parent_id' => $term->parent ) ) );
        }

        update_option( $this->_order, $order );

        delete_transient( $this->_transient );

    }

    function print_confirm_list () {

        if(!is_array($this->_term_in_order) ) $this->get_terms_in_order ();
    ?>
        <script type="text/template" id="temp_<?php echo $this->_tax_name ?>_delete_confirm">
            <div class="moved-tax">
                <span><?php _e('Move posts to', ET_DOMAIN) ?></span>
                <div class="select-style et-button-select">
                    <select name="move_<?php echo $this->_tax_name ?>" id="move_<?php  echo $this->_tax_name ?>">

                    <?php foreach ($this->_term_in_order as $term ) {  ?>
                            <option value="<?php echo $term->term_id ?>"><?php echo $term->name ?></option>
                    <?php } ?>

                    </select>
                </div>
                <button class="backend-button accept-btn" data = "<?php echo $this->_tax_name;?>"><?php _e("Accept", ET_DOMAIN); ?></button>
                <a class="icon cancel-del" data-icon="*"></a>
            </div>
        </script>
    <?php
    }

    /**
     * register ajax action synce with ad category
    */
    public function register_ajax () {

        add_action ('wp_ajax_et_sort_'.$this->_tax_name, array(&$this, 'sort_terms'));
        add_action ('wp_ajax_et_sync_'.$this->_tax_name, array(&$this, 'sync_term'));

    }

}

function ce_field_get_terms ( $args = array() ) {

    $tax = array('tax_name' => $args['taxonomy']);
    $class = new CE_Field_Tax($tax);
    return $class->get_term_list ($args);
}

class  CE_Field_Walker_Category extends Walker_Category{
     /**
     * Starts the list before the elements are added.
     *
     * @see Walker::start_lvl()
     *
     * @since 2.1.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. Will only append content if style argument value is 'list'.
     *                       @see ce_list_categories()
     */

     function start_lvl( &$output, $depth = 0, $args = array() ) {        
       
        if ( 'list' != $args['style'] )
            return;
        if($depth == 0){
            $sclass = 'icon-next ';
            $uclass = 'menu-child';
        } else {
            // $sclass ='icon-next-third';
            $uclass = 'menu-third-child';
            $sclass ='';
            //$uclass ='';
        }
        
        $indent =   str_repeat("\t", $depth);
        //$indent = 
        //if($depth == 0 )
        $output .= "$indent <ul class='".$uclass."'>\n";

    }
     /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 2.1.0
     *
     * @param string $output   Passed by reference. Used to append additional content.
     * @param object $category Category data object.
     * @param int    $depth    Depth of category in reference to parents. Default 0.
     * @param array  $args     An array of arguments. @see wp_list_categories()
     * @param int    $id       ID of the current category.
     */
    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        extract($args);
        global $wp_query;
        
        $taxs = CE_Fields::get_taxs();
        
        $href   = esc_url(get_term_link( $category ));
        

        $field  =   $taxs[$category->taxonomy];

        /**
         * process url to filter
        */
        $cat_name   =   $category->name;
        $tax_slug   =   $field['tax_slug'];
        $ad_cat     =   isset( $_REQUEST[$tax_slug] ) ? $_REQUEST[$tax_slug] : ''; 
        
        if(!is_home() && !is_single() && !is_tax ($args['taxonomy']) ) {
            $href   =   add_query_arg(array( $field['tax_slug']  => $category->slug ));
        }

        if( !empty($ad_cat) && $ad_cat == $category->slug ) {
            $href   =   remove_query_arg( $field['tax_slug'], $href );
        }


        $link = '<a class="customize_text" href="' . $href . '" ';
        if ( $use_desc_for_title == 0 || empty($category->description) )
            $link .= 'title="' . esc_attr( sprintf(__( 'View all posts filed under %s' ), $cat_name) ) . '"';
        else
            $link .= 'title="' . esc_attr( strip_tags( apply_filters( 'category_description', $category->description, $category ) ) ) . '"';
        $link .= '>';
        $link .= $cat_name . '</a>';

        if ( !empty($feed_image) || !empty($feed) ) {
            $link .= ' ';

            if ( empty($feed_image) )
                $link .= '(';

            $link .= '<a href="' . esc_url( get_term_feed_link( $category->term_id, $category->taxonomy, $feed_type ) ) . '"';

            if ( empty($feed) ) {
                $alt = ' alt="' . sprintf(__( 'Feed for all posts filed under %s' ), $cat_name ) . '"';
            } else {
                $title = ' title="' . $feed . '"';
                $alt = ' alt="' . $feed . '"';
                $name = $feed;
                $link .= $title;
            }

            $link .= '>';

            if ( empty($feed_image) )
                $link .= $name;
            else
                $link .= "<img src='$feed_image'$alt$title" . ' />';

            $link .= '</a>';

            if ( empty($feed_image) )
                $link .= ')';
        }

        if ( !empty($show_count) )
            $link .= '<span class="cat-count"> (' . intval($category->count) . ')</span>';
        
        /**
         * check cat is root and has children add icon next
        */
        if( $category->has_child ){
            if($args['hierarchical'])
                $link   =   '<div class="border-bottom" >'.$link.'<i class="fa fa-arrow-right"></i></div>';
            else 
                $link   =   '<div class="border-bottom" >'.$link.'</div>';
        }
        else if($category->parent == 0)  {
            $link   =   '<div class="border-bottom" >'.$link.'</div>';
        }

        if ( 'list' == $args['style'] ) {
            $output .= "\t<li";
            $class = 'cat-item cat-item-' . $category->term_id;

            if( $category->slug == $ad_cat )
                $class .=  ' clicked active';

            if ( !empty($current_category) ) {
                $_current_category = get_term( $current_category, $category->taxonomy );
               
                if ( $category->term_id == $current_category )                    
                    $class .=  ' clicked active';                
                elseif ( $category->term_id == $_current_category->parent )
                    $class .=  ' clicked active';
            }
            // echo $class;
            $output .=  ' class="' . $class . '"';
            $output .= ">$link\n";
        } else {
            $output .= "\t$link<br />\n";
        }
    }

}



function ce_list_term($args =''){
    $defaults = array(
        'show_option_all' => '', 'show_option_none' => __('Category list is empty.',ET_DOMAIN),
        'orderby' => 'name', 'order' => 'ASC',
        'style' => 'list',
        'show_count' => 0, 'hide_empty' => 0,
        'use_desc_for_title' => 1, 'child_of' => 0,
        'feed' => '', 'feed_type' => '',
        'feed_image' => '', 'exclude' => '',
        'exclude_tree' => '', 'current_category' => 0,
        'hierarchical' => true, 'title_li' => __( 'Categories', ET_DOMAIN ),
        'echo' => 1, 'depth' => 3,
        'taxonomy' => CE_AD_CAT
    );
    
    $r = wp_parse_args( $args, $defaults );
    

    if ( !isset( $r['pad_counts'] ) && $r['show_count'] && $r['hierarchical'] )
        $r['pad_counts'] = true;

    if ( true == $r['hierarchical'] ) {
        $r['exclude_tree'] = $r['exclude'];
        $r['exclude'] = '';
    }

    if ( !isset( $r['class'] ) )
        $r['class'] = ( 'category' == $r['taxonomy'] ) ? 'categories' : $r['taxonomy'];

    extract( $r );

    if ( !taxonomy_exists($taxonomy) ){       
        return false;

    }
    $categories = ce_field_get_terms($r);
    
      
    //$categories = get_categories( $r );
    
    foreach ( $categories as $key => $value ) {
        $value->has_child   =   0;
        if($value->parent != 0) continue;
        foreach ($categories as $key => $value_2) {
            if($value->term_id == $value_2->parent)
                $value->has_child   =   1;
        }
    }

    $output = '';
    if ( $title_li && 'list' == $style )
            $output ='<ul class="nav nav-list menu-left">';
           // $output = '<li title ="123" class="' . esc_attr( $class ) . '">' . $title_li . '<ul>';

    if ( empty( $categories ) ) {
        if ( ! empty( $show_option_none ) ) {
            if ( 'list' == $style )
                $output .= '<li>' . $show_option_none . '</li>';
            else
                $output .= $show_option_none;
        }
    } else {
        if ( ! empty( $show_option_all ) ) {
            $posts_page = ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
            $posts_page = esc_url( $posts_page );
            if ( 'list' == $style )
                $output .= "<li><a href='$posts_page'>$show_option_all</a></li>";
            else
                $output .= "<a href='$posts_page'>$show_option_all</a>";
        }

        if ( empty( $r['current_category'] ) && ( is_category() || is_tax() || is_tag() ) ) {
            $current_term_object = get_queried_object();
            if ( $current_term_object && $r['taxonomy'] === $current_term_object->taxonomy )
                $r['current_category'] = get_queried_object_id();
        }
        
        if ( $hierarchical )
            $depth = $r['depth'];
        else
            $depth = -1; // Flat.

        $output .= ce_field_walk_category_tree( $categories, $depth, $r );
    }

    if ( $title_li && 'list' == $style )
        $output .='</ul>';
        //$output .= '</ul></li>';

    $output = apply_filters( 'ce_list_categories', $output, $args );

    if ( $echo )
        echo $output;
    else
        return $output;


}


function ce_field_walk_category_tree() {
    $args = func_get_args();
    // the user's options are the third parameter
    if ( empty($args[2]['walker']) || !is_a($args[2]['walker'], 'Walker') )
        $walker = new CE_Field_Walker_Category;
    else
        $walker = $args[2]['walker'];

    return call_user_func_array(array( &$walker, 'walk' ), $args );
}
function get_slug_by_name($name){
    $taxs = CE_Fields::get_taxs();
    foreach ($taxs as $key => $tax) {
        if($tax['tax_name'] == $name)
            return $tax['tax_slug'];
    }
    return $name;
}
function get_slugs(){
    $taxs = CE_Fields::get_taxs();
    $slug = array();
    foreach ($taxs as $key => $tax) {
        $slug[$tax['tax_name']] = $tax['tax_slug'];
    }
    return $slug;
}
?>