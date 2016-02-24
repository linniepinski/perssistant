<?php

// if(!defined('ET_DOMAIN')) {
//  wp_die('API NOT SUPPORT');
//}



/**
 * Class AE posts, control all action with post data
 * @author Dakachi
 * @version 1.0
 * @package AE
 * @since 1.0
 */
class AE_Posts
{
    static $instance;
    public $meta;
    
    /**
     * store instance current after converted post data
     */
    public $current_post;
    public $current_main_post;
    public $wp_query;
    
    /**
     * return class $instance
     */
    public static function get_instance() {
        if (self::$instance == null) {
            
            self::$instance = new AE_Posts('post');
        }
        return self::$instance;
    }
    
    /**
     * contruct a object post with meta data
     * @param string $post_type object post name
     * @param array $taxs array of tax name assigned to post type
     * @param array $meta_data all post meta data you want to control
     * @author Dakachi
     * @since 1.0
     */
    public function __construct($post_type = '', $taxs = array() , $meta_data = array() , $localize = array()) {
        
        $post_type = ($post_type) ? $post_type : 'post';
        if ($post_type == 'post' && empty($taxs)) {
            $taxs = array(
                'tag',
                'category'
            );
        }
        
        $this->post_type = $post_type;
        $this->taxs = apply_filters('ae_post_taxs', $taxs, $post_type);
        $defaults = array(
            'address',
            'avatar',
            'post_count',
            'comment_count',
            'et_featured',
            'et_expired_date'
        );
        $this->meta = apply_filters('ae_post_meta_fields', wp_parse_args($meta_data, $defaults) , $post_type);
        
        /**
         * setup convert field of post data
         */
        $this->convert = array(
            'post_parent',
            'post_title',
            'post_name',
            'post_content',
            'post_excerpt',
            'post_author',
            'post_status',
            'ID',
            'post_type',
            'comment_count'
        );
        
        $this->localize = $localize;
    }
    
    /**
     * convert post data to an object with meta data
     * @param object $post
     * @param string $thubnail Post thumbnail size
     * @param bool $excerpt convert excerpt
     * @param bool $singular convert in singular or a listing
     * @return post object after convert
     *         - wp_error object if post invalid
     * @author Dakachi
     * @since 1.0
     */
    public function convert($post_data, $thumbnail = 'medium_post_thumbnail', $excerpt = true, $singular = false) {
        $result = array();
        $post = (array)$post_data;
        
        /**
         * convert need post data
         */
        foreach ($this->convert as $key) {
            if (isset($post[$key])) $result[$key] = $post[$key];
        }
        
        // array statuses
        $status = array(
            'reject' => __("REJECTED", ET_DOMAIN) ,
            'archive' => __("ARCHIVED", ET_DOMAIN) ,
            'pending' => __("PENDING", ET_DOMAIN) ,
            'draft' => __("DRAFT", ET_DOMAIN) ,
            'publish' => __("ACTIVE", ET_DOMAIN) ,
            'trash' => __("TRASHED", ET_DOMAIN)
        );
        
        $result['status_text'] = isset($status[$result['post_status']]) ? $status[$result['post_status']] : '';
        
        $result['post_date'] = get_the_date('', $post['ID']);
        
        // generate post taxonomy
        if (!empty($this->taxs)) {
            
            foreach ($this->taxs as $name) {
                $terms = wp_get_object_terms($post['ID'], $name);
                $arr = array();
                if (is_wp_error($terms)) continue;
                
                foreach ($terms as $term) {
                    $arr[] = $term->term_id;
                }
                $result[$name] = $arr;
                $result['tax_input'][$name] = $terms;
            }
        }
        
        $meta = apply_filters('ae_' . $this->post_type . '_convert_metadata', $this->meta, $post, $singular);
        
        // generate meta data
        if (!empty($meta)) {
            foreach ($meta as $key) {
                $result[$key] = get_post_meta($post['ID'], $key, true);
            }
        }
        
        if (!empty($this->localize)) {
            foreach ($this->localize as $key => $localize) {
                $a = array();
                foreach ($localize['data'] as $loc) {
                    array_push($a, $result[$loc]);
                }
                
                $result[$key] = vsprintf($localize['text'], $a);
            }
        }
        
        unset($result['post_password']);
        $result['id'] = $post['ID'];
        $result['permalink'] = get_permalink($result['ID']);
        $result['unfiltered_content'] = $result['post_content'];
        
        /**
         * get post content in loop
         */
        ob_start();
        echo apply_filters('the_content', $result['post_content']);
        $the_content = ob_get_clean();
        
        $result['post_content'] = $the_content;
        
        /* set post excerpt */
        if (isset($result['post_excerpt']) && $result['post_excerpt'] == '') {
            $result['post_excerpt'] = wp_trim_words($the_content, 20);
        }
        
        /**
         * return post thumbnail url
         */
        if (has_post_thumbnail($result['ID'])) {
            $result['featured_image'] = get_post_thumbnail_id($result['ID']);
            $feature_image = wp_get_attachment_image_src($result['featured_image'], $thumbnail);
            $result['the_post_thumnail'] = $feature_image[0];
        } else {
            $result['the_post_thumnail'] = '';
            $result['featured_image'] = '';
        }
        $result['the_post_thumbnail'] = $result['the_post_thumnail'];
        /**
         * assign convert post to current post
         */
        $this->current_post = apply_filters('ae_convert_' . $this->post_type, (object)$result);
        
        return $this->current_post;
    }
    
    /**
     * return instance current post data after convert
     * @since 1.0
     * @return object $post
     * @author Dakachi
     */
    public function get_current_post() {
        
        // if($this->wp_query && $this->wp_query->is_main_query()){
        //     return $this->current__main_post;
        // }
        return $this->current_post;
    }
    
    /**
     * insert postdata and post metadata to an database
     # used wp_insert_post
     # used update_post_meta
     # post AE_Posts function convert
     * @param   array $post data
     # wordpress post fields data
     # post custom meta data
     * @return  post object after insert
     # wp_error object if post data invalid
     * @author Dakachi
     * @since 1.0
     */
    public function insert($args) {
        global $current_user, $user_ID;
        
        // check user submit post too fast
        // if(!current_user_can( 'edit_others_posts' )) {
        //     $post = get_posts( array('post_author' => $user_ID, 'post_type' => $this->post_type, 'posts_per_page' => 1 ) );
        
        // }
        // strip tags
        foreach ($args as $key => $value) {
            if ((in_array($key, $this->meta) || in_array($key, $this->convert)) && is_string($args[$key]) && $key != 'post_content') {
                $args[$key] = strip_tags($args[$key]);
            }
        }
        
        // pre filter filter post args
        $args = apply_filters('ae_pre_insert_' . $this->post_type, $args);
        if (is_wp_error($args)) return $args;
        
        $args = wp_parse_args($args, array(
            'post_type' => $this->post_type
        ));
        
        /*if admin disable plan set status to pending or publish*/
        $pending = ae_get_option('use_pending', false);
        $pending = apply_filters( 'use_pending', $pending, $this->post_type );
        $disable_plan = ae_get_option('disable_plan', false);
        
        /*if admin disable plan set status to pending or publish*/
        if ($disable_plan) {
             // disable plan
            if ($pending) {
                 // pending post
                $args['post_status'] = 'pending';
            } else {
                 // disable pending post
                $args['post_status'] = 'publish';
            }
        }
        
        if (!isset($args['post_status'])) {
            $args['post_status'] = 'draft';
        }
        
        // could not create with an ID
        if (isset($args['ID'])) {
            return new WP_Error('invalid_data', __("The ID already existed!", ET_DOMAIN));
        }
        
        if (!isset($args['post_author']) || empty($args['post_author'])) $args['post_author'] = $current_user->ID;
        
        if ( empty( $args['post_author'] ) ) return new WP_Error('missing_author', __('You must login to submit listing.', ET_DOMAIN));
        
        // filter tax_input
        $args = $this->_filter_tax_input($args);
        
        // filter post content strip invalid tag
        $args['post_content'] = $this->filter_content($args['post_content']);
        
        /**
         * insert post by wordpress function
         */
        $result = wp_insert_post($args, true);
        
        /**
         * update custom field and tax
         */
        if ($result != false && !is_wp_error($result)) {
            $this->update_custom_field($result, $args);
            $args['ID'] = $result;
            $args['id'] = $result;
            
            /**
             * do action ae_insert_{$this->post_type}
             * @param Int $result Inserted post ID
             * @param Array $args The array of post data
             */
            do_action('ae_insert_' . $this->post_type, $result, $args);
            
            $result = (object)$args;
            
            /**
             * do action ae_insert_post
             * @param object $args The object of post data
             */
            do_action('ae_insert_post', $result);
            
            // localize text for js
            if (!empty($this->localize)) {
                foreach ($this->localize as $key => $localize) {
                    $a = array();
                    foreach ($localize['data'] as $loc) {
                        array_push($a, $result->$loc);
                    }
                    
                    $result->$key = vsprintf($localize['text'], $a);
                }
            }
            $result->permalink = get_permalink($result->ID);
            
            if (current_user_can('manage_options') || $result->post_author == $user_ID) {
                
                /**
                 * featured image not null and should be in carousels array data
                 */
                if (isset($args['featured_image'])) {
                    set_post_thumbnail($result->ID, $args['featured_image']);
                }
            }
        }
        
        return $result;
    }
    
    /**
     * filter tax input args and check existed
     * @since 1.0
     * @author Dakachi
     * @return array
     */
    function _filter_tax_input($args) {
        $args['tax_input'] = array();
        if (!empty($this->taxs)) {
            foreach ($this->taxs as $tax_name) {
                if (is_taxonomy_hierarchical($tax_name)) {
                    if (isset($args[$tax_name]) && !empty($args[$tax_name])) {
                        
                        /**
                         * check term existed
                         */
                        if (is_array($args[$tax_name])) {
                            
                            // if tax input is array
                            foreach ($args[$tax_name] as $key => $value) {
                                $term = get_term_by('id', $value, $tax_name);
                                if (!$term) {
                                    unset($args[$tax_name][$key]);
                                }
                            }
                        } else {
                            
                            // if tax input ! is array
                            $term = get_term_by('id', $args[$tax_name], $tax_name);
                            if (!$term) {
                                unset($args[$tax_name]);
                            }
                        }
                        
                        // check term exist
                        
                        /**
                         * assign tax input
                         */
                        if (isset($args[$tax_name])) {
                            $args['tax_input'][$tax_name] = $args[$tax_name];
                        }
                    } else {
                        $args['tax_input'][$tax_name] = array();
                    }
                } else {
                    
                    /**
                     * assign tax input
                     */
                    if (isset($args[$tax_name])) {
                        if (is_array($args[$tax_name])) {
                            $temp = array();
                            foreach ($args[$tax_name] as $key => $value) {
                                if (isset($value['name'])) {
                                    $temp[] = $value['name'];
                                }
                            }
                            $args['tax_input'][$tax_name] = $temp;
                        } else {
                            $args['tax_input'][$tax_name] = $args[$tax_name];
                        }
                    } else {
                        $args['tax_input'][$tax_name] = array();
                    }
                }
            }
        }
        return $args;
    }
    
    /**
     * filter content insert and skip invalid tag
     * @param string $content the post content be filter
     * @return String the string filtered
     * @author Dakachi
     * @since 1.0
     */
    function filter_content($content) {
        $pattern = "/<[^\/>]*>(&nbsp;)*([\s]?)*<\/[^>]*>/";
        
        //use this pattern to remove any empty tag '<a target="_blank" rel="nofollow" href="$1">$3</a>'
        
        $content = preg_replace($pattern, '', $content);
        
        // $link_pattern = "/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>/";
        $content = str_replace('<a', '<a target="_blank" rel="nofollow"', $content);
        $content = strip_tags($content, '<p><a><ul><ol><li><h6><span><b><em><strong><br>');
        
        return $content;
    }
    
    /**
     * update postdata and post metadata to an database
     # used wp_update_post ,get_postdata
     # used update_post_meta
     # used AE_Users function convert
     * @param   array $post data
     # wordpress post fields data
     # post custom meta data
     * @return  post object after insert
     # wp_error object if post data invalid
     * @author Dakachi
     * @since tegory!
     )1.0
     */
    public function update($args) {
        global $current_user, $user_ID;

        // $args = wp_parse_args($args);
        // strip tags
        foreach ($args as $key => $value) {
            if ((in_array($key, $this->meta) || in_array($key, $this->convert)) && is_string($args[$key]) && $key != 'post_content') {
                $args[$key] = strip_tags($args[$key]);
            }
        }
        
        // unset post date
        if (isset($args['post_date'])) unset($args['post_date']);
        
        // filter args
        $args = apply_filters('ae_pre_update_' . $this->post_type, $args);
        if (is_wp_error($args)) return $args;
        
        // if missing ID, return errors
        if (empty($args['ID'])) return new WP_Error('ae_missing_ID', __('Post not found!', ET_DOMAIN));
        
        if (!ae_user_can('edit_others_posts')) {
            $post = get_post($args['ID']);
            if ($post->post_author != $user_ID) {
                return new WP_Error('permission_deny', __('You can not edit other posts!', ET_DOMAIN));
            }
            
            /**
             * check and prevent user publish post
             */
            if (isset($args['post_status']) && $args['post_status'] != $post->post_status && $args['post_status'] == 'publish') {
                unset($args['post_status']);
            }
            
            // unset($args['et_featured']);
            
        }
        // set post status to draft if renew
        if (isset($args['renew'])) {
            $args['post_status'] = 'draft';
            
            /*if admin disable plan set status to pending or publish*/
            $pending = ae_get_option('use_pending', false);
            $pending = apply_filters( 'use_pending', $pending, $this->post_type );
            $disable_plan = ae_get_option('disable_plan', false);
            
            if ($disable_plan) {
                 // disable plan
                if ($pending) {
                     // pending post
                    $args['post_status'] = 'pending';
                } else {
                     // disable pending post
                    $args['post_status'] = 'publish';
                }
            }
            
            /*if admin disable plan set status to pending or publish*/
        }
        
        $args = $this->_filter_tax_input($args);
        
        // filter post content strip invalid tag
        $args['post_content'] = $this->filter_content($args['post_content']);
        
        // update post data into database use wordpress function
        $result = wp_update_post($args, true);

        // catch event reject post
        if (isset($args['post_status']) && $args['post_status'] == 'reject' && isset($args['reject_message'])) {
            do_action('ae_reject_post', $args);
        }
        
        /**
         * update custom field and tax
         */
        
        if ($result != false && !is_wp_error($result)) {
            $this->update_custom_field($result, $args);
            
            $post = get_post($result);
            
            if (current_user_can('manage_options') || $post->post_author == $user_ID) {
                
                /**
                 * featured image not null and should be in carousels array data
                 */
                if (isset($args['featured_image'])) {
                    set_post_thumbnail($post->ID, $args['featured_image']);
                }
            }
            
            // make an action so develop can modify it
            do_action('ae_update_' . $this->post_type, $result, $args);
            $result = $this->convert($post);
        }
        
        return $result;
    }
    
    /**
     * update post meta and taxonomy
     * @param object $result post
     * @param array $data post data
     * @param array $args
     * @author Dakachi
     * @since version 1.0
     */
    public function update_custom_field($result, $args) {
        
        // update post meta
        if (!empty($this->meta)) {
            foreach ($this->meta as $key => $meta) {
                
                // do not update expired date
                if ($meta == 'et_expired_date') continue;
                
                if (isset($args[$meta])) {
                    if (!is_array($args[$meta])) {
                        $args[$meta] = esc_attr($args[$meta]);
                    }
                    update_post_meta($result, $meta, $args[$meta]);
                }
            }
        }
    }
    
    /**
     * delete post from site
     * @param int $ID post id want to delete
     * @param bool $force_delete defautl is false
     * @author Dakachi
     * @since version 1.0
     */
    public function delete($ID, $force_delete = false) {
        
        if (!ae_user_can('edit_others_posts')) {
            global $user_ID;
            $post = get_post($ID);
            if ($user_ID != $post->post_author) {
                return new WP_Error('permission_deny', __("You do not have permission to delete post.", ET_DOMAIN));
            }
        }
        
        if ($force_delete) {
            $result = wp_delete_post($ID, true);
        } else {
            $result = wp_trash_post($ID);
        }
        if ($result) do_action('et_delete_' . $this->post_type, $ID);
        
        return $this->convert($result);
    }
    
    /**
     * get postdata
     * @param int $ID post id want to get
     * @return object $post
     * @author Dakachi
     * @since version 1.0
     */
    public function get($ID) {
        $result = $this->convert(get_post($ID));
        return $result;
    }
    
    /**
     * sync request from client,
     * request should have attribute method to specify which action want to do
     * @param array $request
     * @author Dakachi
     * @since 1.0
     */
    function sync($request) {
        
        extract($request);
        //unset($request['method']);
        
        switch ($method) {
            case 'create':
                $result = $this->insert($request);
                break;

            case 'update':
                $result = $this->update($request);
                break;

            case 'remove':
                $result = $this->delete($request['ID']);
                break;

            case 'read':
                $result = $this->get($request['ID']);
                break;

            default:
                return new WP_Error('invalid_method', __("Invalid method", ET_DOMAIN));
        }
        
        return $result;
    }
    
    /**
     * fetch postdata from database, use function convert
     * @param array $args query options, see more WP_Query args
     * @return array of objects post
     * @author Dakachi
     * @since 1.0
     */
    public function fetch($args) {

        $args['post_type'] = $this->post_type;
        if (isset($args['radius']) && $args['radius']) {
            $query = $this->nearbyPost($args);
        } else {
            $query = new WP_Query($args);
        }
        
        $data = array();
        
        $thumb = isset($args['thumbnail']) ? $args['thumbnail'] : 'thumbnail';
        
        // loop post
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                global $post;
                
                // convert post data
                $data[] = $this->convert($post, $thumb);
            }
        }
        
        if (!empty($data)) {
            
            /**
             * return array of data if success
             */
            return array(
                'posts' => $data,
                
                // post data
                'post_count' => $query->post_count,
                
                // total post count
                'max_num_pages' => $query->max_num_pages,
                
                // total pages
                'query' => $query
                
                // wp_query object
                
                
            );
        } else {
            return false;
        }
    }
    
    /**
     * query postdata from database, use function convert
     * @param array $args query options, see more WP_Query args
     * @return objects WP_Query
     * @author Dakachi
     * @since 1.0
     */
    public function query($args) {
        $args['post_type'] = $this->post_type;
        $this->wp_query = new WP_Query($args);
        return $this->wp_query;
    }
    
    /**
     * query postdata from database search nearby post with location latitude and location longitude
     * @param array $args query options, see more WP_Query args
     * @return objects WP_Query
     * @author Dakachi
     * @since 1.0
     */
    public function nearbyPost($args) {
        global $wpdb;
        
        $args['post_type'] = $this->post_type;
        
        // get nearby post id
        if (isset($args['near_lat']) && $args['near_lat'] && $args['radius']) {
            
            // 1.609344;
            if (ae_get_option('unit_measurement', 'mile') == 'km') {
                $args['radius'] = $args['radius'] / 1.609344;
            }
            
            $mile = $args['radius'];
            $near_latitude = $args['near_lat'];
            $near_longitude = $args['near_lng'];
            
            $calc = "acos(sin(A.meta_value * 0.0175) * sin( $near_latitude * 0.0175) 
                           + cos(A.meta_value * 0.0175) * cos( $near_latitude * 0.0175) * cos(( $near_longitude  * 0.0175) - (B.meta_value * 0.0175))) 
                           * 3959";
            $sql = "select ID , $calc as dis
                        from $wpdb->posts as P
                            join $wpdb->postmeta  as A
                                on A.post_id = P.ID and  A.meta_key= 'et_location_lat' and A.meta_value != ''
                            join $wpdb->postmeta as B
                                on B.post_id = P.ID and B.meta_key= 'et_location_lng' and B.meta_value != ''
                        where post_status = 'publish' and 
                        $calc <= $mile GROUP BY ID order by dis";
            
            /**
             * build a list of nearby id
             */
            $results = $wpdb->get_results($sql, ARRAY_N);
            if (!empty($results)) {
                $args['post__in'] = array();
                foreach ($results as $key => $value) {
                    array_push($args['post__in'], $value['0']);
                }
            } else {
                $args['meta_key'] = 'no_data_daaaaaa';
            }
        }
        
        // build wp_query object and return
        $query = new WP_Query($args);
        return $query;
    }
}

/**
 * class AE_PostFact
 * factory class to generate ae post object
 */
class AE_PostFact
{
    
    static $objects;
    
    /**
     * contruct init post type
     */
    function __construct() {
        self::$objects = array(
            'post' => AE_Posts::get_instance()
        );
    }
    
    /**
     * set a post type object to machine
     * @param String $post_type
     * @param AE_Post object $object
     */
    public function set($post_type, $object) {
        self::$objects[$post_type] = $object;
    }
    
    /**
     * get post type object in class object instance
     * @param String $post_type The post type want to use
     * @return Object
     */
    public function get($post_type) {
        if (isset(self::$objects[$post_type])) return self::$objects[$post_type];
        return null;
    }
}

/**
 * set a global object factory
 */
global $ae_post_factory;
$ae_post_factory = new AE_PostFact();
$ae_post_factory->set('post', new AE_Posts('post'));

/**
 * class with all action releated to post
 */
class AE_PostAction extends AE_Base
{
    
    /**
     * catch event publish a post and set up order
     * @param int $ad_id
     * @since 1.1
     * @author Dakachi
     */
    function publish_post_action($ad_id) {
        if (get_post_type($ad_id) != $this->post_type) return;
        
        $order = get_post_meta($ad_id, 'et_ad_order', true);
        if ($order) {
            
            /**
             * update order status
             */
            //if (!isset($_POST['_et_nonce'])) {
                wp_update_post(array(
                    'ID' => $order,
                    'post_status' => 'publish'
                ));
            //}
                
            $ads = new WP_Query(array(
                'post_type' => $this->post_type,
                'post_status' => array(
                    'pending'
                ) ,
                'meta_value' => $order,
                'meta_key' => 'et_ad_order',
                'posts_per_page' => - 1,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'post__not_in' => array(
                    $ad_id
                )
            ));
            
            if (!$ads->have_posts()) return;
            
            /**
             * update ads in same package
             */
            $use_pending = ae_get_option('use_pending');
            
            if ($use_pending) {
                foreach ((array)$ads->posts as $ad) {
                    wp_update_post(array(
                        'ID' => $ad->ID,
                        'post_status' => 'pending'
                    ));
                    update_post_meta($ad->ID, 'et_paid', 1);
                }
            } else {
                foreach ((array)$ads->posts as $ad) {
                    wp_update_post(array(
                        'ID' => $ad->ID,
                        'post_status' => 'publish'
                    ));
                    update_post_meta($ad->ID, 'et_paid', 1);
                }
            }
        }
    }
    
    /**
     * construct function
     */
    function __construct($post_type = 'post') {
        $this->post_type = $post_type;
        $this->add_ajax('ae-fetch-blogs', 'fetch_post');
    }
    
    protected function query_post() {
    }
    
    /**
     * fetch data
     */
    function fetch_post() {
        global $ae_post_factory;
        $post = $ae_post_factory->get($this->post_type);
        
        $page = $_REQUEST['page'];
        extract($_REQUEST);
        
        $thumb = isset($_REQUEST['thumbnail']) ? $_REQUEST['thumbnail'] : 'thumbnail';
        
        $query_args = array(
            'paged' => $page,
            'thumbnail' => $thumb,
            'post_status' => 'publish',
            'post_type' => $this->post_type
        );
        
        // check args showposts
        if (isset($query['showposts']) && $query['showposts']) $query_args['showposts'] = $query['showposts'];
        if (isset($query['posts_per_page']) && $query['posts_per_page']) $query_args['posts_per_page'] = $query['posts_per_page'];
        
        $query_args = $this->filter_query_args($query_args);
        /**
         * filter fetch post query args 
         * @param Array $query_args
         * @param object $this
         * @since 1.2
         * @author Dakachi
         */
        $query_args = apply_filters( 'ae_fetch_'.$this->post_type . '_args' , $query_args, $this );
        
        if (isset($query['category_name']) && $query['category_name']) $query_args['category_name'] = $query['category_name'];
        
        //check query post parent
        if (isset($query['post_parent']) && $query['post_parent'] != '') {
            $query_args['post_parent'] = $query['post_parent'];
        }
        
        //check query author
        if (isset($query['author']) && $query['author'] != '') {
            $query_args['author'] = $query['author'];
        }
        if (isset($query['s']) && $query['s']) {
            $query_args['s'] = $query['s'];
        }
        
        /**
         * fetch data
         */
        $data = $post->fetch($query_args);
        
        ob_start();
        ae_pagination($data['query'], $page, $_REQUEST['paginate']);
        $paginate = ob_get_clean();
        
        /**
         * send data to client
         */
        if (!empty($data)) {
            wp_send_json(array(
                'data' => $data['posts'],
                'paginate' => $paginate,
                'msg' => __("Successs", ET_DOMAIN) ,
                'success' => true,
                'max_num_pages' => $data['max_num_pages'],
                'total' => $data['query']->found_posts
            ));
        } else {
            wp_send_json(array(
                'success' => false,
                'data' => array()
            ));
        }
    }
    
    function filter_query_args($query_args) {
        return $query_args;
    }
}

new AE_PostAction();
