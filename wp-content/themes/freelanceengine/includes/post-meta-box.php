<?php
/**
 * class AE_PostMeta
 * render and control post type metabox for this post type
 * @author Dakachi
 * @package AE
 * @version 1.0
 */
class AE_PostMeta extends AE_Base
{
    
    public function __construct($post_type = 'place') {
        $this->post_type = $post_type;
        $this->nonce = 'et_nonce_' . $post_type;
        
        /**
         * add places metabox
         */
        if (ae_user_can('edit_posts')) {
            add_action('add_meta_boxes', array(
                $this,
                'add_meta_boxes'
            ));

            $this->add_action('save_post', 'save_meta_fields');
            
            if ((basename($_SERVER['SCRIPT_FILENAME']) == 'post.php' && isset($_GET['action']) && $_GET['action'] == 'edit') 
                || (basename($_SERVER['SCRIPT_FILENAME']) == 'post-new.php' && (isset($_GET['post_type']) && $_GET['post_type'] == $this->post_type))
            ) {
                add_action('admin_head', array(
                    $this,
                    'add_meta_script'
                ));
                add_filter('wp_dropdown_users', array(
                    $this,
                    'wp_dropdown_users'
                ));
            }
        }
        
        
    }
    
    /**
     * All about meta boxes in backend
     */
    public function add_meta_boxes() {
        add_meta_box('place_info', __('Project Information', ET_DOMAIN) , array(
            $this,
            'meta_view'
        ) , $this->post_type, 'normal', 'high');
    }
    
    /**
     * add script for metabox
     * control address with map, date pick for date input
     * @author Dakachi
     * @since 1.0
     */
    public function add_meta_script() {
        
        global $wp_scripts, $post;
        $ui = $wp_scripts->query('jquery-ui-core');
        $url = "//code.jquery.com/ui/{$ui->ver}/themes/smoothness/jquery-ui.css";
        wp_enqueue_style('jquery-ui-redmond', $url, false, $ui->ver);
        
        wp_enqueue_script('jquery');
        
        // jquery auto complete for search users
        wp_enqueue_script('jquery-ui-autocomplete');
        
        // date pick for date input
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('jquery-ui-datepicker');
        
        // google map api
        wp_enqueue_script('et-googlemap-api');
        
        // gmap library
        $this->add_existed_script('gmap');
        
        wp_enqueue_script('edit-ad', TEMPLATEURL . '/js/edit-post.js', array(
            'jquery',
            'jquery-ui-autocomplete',
            'jquery-ui-datepicker',
            'gmap'
        ));
        
        $replace = array(
            'd' => 'dd',
            
            // two digi date
            'j' => 'd',
            
            // no leading zero date
            'm' => 'mm',
            
            // two digi month
            'n' => 'm',
            
            // no leading zero month
            'l' => 'DD',
            
            // date name long
            'D' => 'D',
            
            // date name short
            'F' => 'MM',
            
            // month name long
            'M' => 'M',
            
            // month name shỏrt
            'Y' => 'yy',
            
            // 4 digits year
            'y' => 'y',
        );
        $date_format = str_replace(array_keys($replace) , array_values($replace) , get_option('date_format'));
        
        wp_localize_script('edit-ad', 'edit_ad', array(
            'dateFormat' => $date_format
        ));
    }
    
    /**
     * filter wp dropdown users function
     */
    public function wp_dropdown_users($output) {
        global $user_ID;
        $post = false;
        if( isset($_REQUEST['post']) ) {
            $post_id = $_REQUEST['post'];
            $post = get_post($post_id);    
        }        
        /**
         * remove filter to prevent loop
         */
        remove_filter('wp_dropdown_users', array(
            $this,
            'wp_dropdown_users'
        ));
        
        $output = wp_dropdown_users(array(
            'who' => '',
            'name' => 'post_author_override',
            'selected' => ($post && isset($post->ID) ) ? $post->post_author : $user_ID,
            'include_selected' => true,
            'echo' => false
        ));
        
        return $output;
    }
    
    /**
     * render post type meta view
     * @author Dakachi
     * @since 1.0
     * @package AE
     */
    public function meta_view($post) {
        global $ae_post_factory;
        $ae_pack = $ae_post_factory->get('pack');

        $payment_package = $ae_pack->fetch();
        $currency = ae_get_option('currency');
        
        $place_obj = $ae_post_factory->get($this->post_type);
        $ad = (array)$place_obj->convert($post);
?> 
        <table class="form-table ad-info">
            <input type="hidden" name="_et_nonce" value="<?php echo wp_create_nonce($this->nonce) ?>">
            <tbody>
            <tr valign="top">
                <th scope="row"><label for=""><strong><?php _e("Packages:", ET_DOMAIN); ?></strong></label></th>
                <td>
                    <?php
                    if(!empty($payment_package)) {
                    foreach ($payment_package as $key => $plan) { ?>
                    <p>
                        <input data-duration="<?php echo $plan->et_duration ?>" class="ad-package" type="radio" id="et_ad_package_<?php
                            echo $plan->sku; ?>" name="et_payment_package" value="<?php
                            echo $plan->sku; ?>" <?php
                            checked($plan->sku, $ad['et_payment_package'], true); ?>
                        /> 
                        <label for="et_ad_package_<?php
                            echo $plan->sku; ?>"><strong><?php
                            echo $plan->post_title; ?>  <?php
                            echo $plan->et_price; ae_currency_sign(); ?></strong> - <?php
                            echo $plan->backend_text; ?>
                        </label>
                    </p>
                    <?php
                    }} ?>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row"><label for="et_featured"><strong><?php _e("Featured post:", ET_DOMAIN); ?></strong></label></th>
                <td>
                    <input type="hidden" value="0" name="et_featured" />
                    <input value="1"  name="et_featured" type="checkbox" id="et_featured" <?php checked(1, $ad['et_featured'], true); ?> >
                    <p class="description"><label for="et_featured" ><?php _e("Make this post featured in listing.", ET_DOMAIN); ?></label></p>
                </td>
                
            </tr>

            <tr valign="top">
                <th scope="row"><label for="et_expired_date"><strong><?php _e("Expired Date:", ET_DOMAIN); ?></strong></label></th>
                <td>
                    <input  name="et_expired_date" type="text" id="et_expired_date" value="<?php echo $ad['et_expired_date'] ?>" class="regular-text">
                    <p class="description"><?php _e("Specify a date when ad will be archived.", ET_DOMAIN); ?></p>
                </td>
                
            </tr>

            <?php do_action('et_meta_fields', $ad); ?>

            <!-- <tr valign="top">
                <?php $user = get_user_by('id', $ad['post_author']); ?> 
                <th scope="row"><label for="seller"><strong><?php _e("Assign to a author:", ET_DOMAIN); ?></strong> </label></th>
                <td>
                    <input name="seller" type="text" id="seller" value="<?php echo $user->display_name; ?>" class="regular-text ltr">
                    <input type="hidden" id="et_author" name="post_author_override" value="<?php echo $post->post_author ?>"> 
                    <p class="description"><?php _e("Choose a seller to make him become the author of this item.", ET_DOMAIN); ?></p>
                </td>
            </tr> -->
            </tbody>
        </table>
        <?php
        
        // print users list
        // $users = get_users();
        $template = array();
        // foreach ($users as $user) {
        //     $template[] = array(
        //         'value' => $user->ID,
        //         'label' => $user->display_name
        //     );
        // }
?>
        <script type="text/template" id="et_users">
            <?php echo json_encode($template); ?>
        </script>
    <?php
    }
    
    /**
     *
     */
    public function save_meta_fields($post_id) {
        
        if (!isset($_POST['_et_nonce']) || !wp_verify_nonce($_POST['_et_nonce'], $this->nonce)) return;
        unset($_POST['_et_nonce']);
        
        // cancel if current post isn't job
        if (!isset($_POST['post_type']) || $_POST['post_type'] != $this->post_type) return;
        
        global $ae_post_factory;
        $ce_ad = $ae_post_factory->get($this->post_type);
        
        /**
         * check expired date
         */
        if (isset($_POST['et_expired_date']) && $_POST['et_expired_date'] == '') {
            unset($_POST['et_expired_date']);
        } else {
            $_POST['et_expired_date'] = date('Y-m-d H:i:s', strtotime($_POST['et_expired_date']));
        }
        
        $_POST['ID'] = $_POST['post_ID'];
        $_POST['method'] = 'update';

        $request = $_POST;
        /**
         * sync post data
         */
        if(isset($request['et_expired_date'])) {
            update_post_meta($request['ID'], 'et_expired_date', $request['et_expired_date']);    
        }        
        // update place location
        // update_post_meta($request['ID'], 'et_full_location', $request['et_full_location']);
        // update_post_meta($request['ID'], 'et_location_lat', $request['et_location_lat']);
        // update_post_meta($request['ID'], 'et_location_lng', $request['et_location_lng']);
        if(isset($request['et_payment_package'])){
            // update payment package
            update_post_meta($request['ID'], 'et_payment_package', $request['et_payment_package']);    
        }
        
        // update featured
        update_post_meta($request['ID'], 'et_featured', $request['et_featured']);

        return;
        /**
         * check post order
         */
        $order = get_post_meta($post_id, 'et_ad_order', true);
        if ($order) {
            
            /**
             * update order status
             */
            wp_update_post(array(
                'ID' => $order,
                'post_status' => 'publish'
            ));
        }
    }
}
