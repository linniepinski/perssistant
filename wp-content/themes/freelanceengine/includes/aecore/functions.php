<?php

if ( !function_exists('de_check_ajax_referer') ) :

/**

 * Verifies the AJAX request to prevent processing requests external of the blog.

 *

 * @since 2.0.3

 *

 * @param string $action Action nonce

 * @param string $query_arg where to look for nonce in $_REQUEST (since 2.5)

 */

function de_check_ajax_referer( $action = -1, $query_arg = false, $die = true ) {

    $nonce = '';



    if ( $query_arg && isset( $_REQUEST[ $query_arg ] ) )

        $nonce = $_REQUEST[ $query_arg ];

    elseif ( isset( $_REQUEST['_ajax_nonce'] ) )

        $nonce = $_REQUEST['_ajax_nonce'];

    elseif ( isset( $_REQUEST['_wpnonce'] ) )

        $nonce = $_REQUEST['_wpnonce'];



    $result = de_verify_nonce( $nonce, $action );



    if ( $die && false == $result ) {

        if ( defined( 'DOING_AJAX' ) && DOING_AJAX )

            wp_die( -1 );

        else

            die( '-1' );

    }



    /**

     * Fires once the AJAX request has been validated or not.

     *

     * @since 2.1.0

     *

     * @param string $action The AJAX nonce action.

     * @param bool   $result Whether the AJAX request nonce was validated.

     */

    do_action( 'check_ajax_referer', $action, $result );



    return $result;

}

endif;



if ( !function_exists('de_verify_nonce') ) :

/**

 * Verify that correct nonce was used with time limit.

 *

 * The user is given an amount of time to use the token, so therefore, since the

 * UID and $action remain the same, the independent variable is the time.

 *

 * @since 2.0.3

 *

 * @param string $nonce Nonce that was used in the form to verify

 * @param string|int $action Should give context to what is taking place and be the same when nonce was created.

 * @return bool Whether the nonce check passed or failed.

 */

function de_verify_nonce($nonce, $action = -1) {

    $user = wp_get_current_user();

    $uid = (int) $user->ID;

    if ( ! $uid ) {

        /**

         * Filter whether the user who generated the nonce is logged out.

         *

         * @since 3.5.0

         *

         * @param int    $uid    ID of the nonce-owning user.

         * @param string $action The nonce action.

         */

        $uid = apply_filters( 'nonce_user_logged_out', $uid, $action );

    }



    $i = wp_nonce_tick();



    // Nonce generated 0-12 hours ago

    if ( substr(wp_hash($i . $action . $uid, 'nonce'), -12, 10) === $nonce )

        return 1;

    // Nonce generated 12-24 hours ago

    if ( substr(wp_hash(($i - 1) . $action . $uid, 'nonce'), -12, 10) === $nonce )

        return 2;

    return false;

}

endif;



if ( !function_exists('de_create_nonce') ) :

/**

 * Creates a random, one time use token.

 *

 * @since 2.0.3

 *

 * @param string|int $action Scalar value to add context to the nonce.

 * @return string The one use form token

 */

function de_create_nonce($action = -1) {

    $user = wp_get_current_user();

    $uid = (int) $user->ID;

    if ( ! $uid ) {

        /** This filter is documented in wp-includes/pluggable.php */

        $uid = apply_filters( 'nonce_user_logged_out', $uid, $action );

    }



    $i = wp_nonce_tick();



    return substr(wp_hash($i . $action . $uid, 'nonce'), -12, 10);

}

endif;



/**

 * return core url

 */

if (!function_exists('ae_get_url')) {

    function ae_get_url() {

        return plugins_url('', __FILE__);

    }

}



/**

 * return core path

 */

if (!function_exists('ae_get_path')) {

    function ae_get_path() {

        return dirname(__FILE__);

    }

}



/**

 * ae get template part

 * @param $slug

 * @param String $name

 * @version 1.0

 */

if (!function_exists('ae_get_template_part')) {

    function ae_get_template_part($slug, $name) {

        $template = '';

        

        // Look in yourtheme/slug-name.php and yourtheme/woocommerce/slug-name.php

        if ($name) {

            $template = locate_template(array(

                "/includes/aecore/template/{$slug}-{$name}.php",

                "{$slug}-{$name}.php"

            ));

        }

        

        // Get default slug-name.php

        if (!$template && $name && file_exists(ae_get_path() . "/template/{$slug}-{$name}.php")) {

            $template = ae_get_path() . "/template/{$slug}-{$name}.php";

        }

        

        // If template file doesn't exist, look in yourtheme/slug.php and yourtheme/woocommerce/slug.php

        if (!$template) {

            $template = locate_template(array(

                "{$slug}.php",

                ae_get_path() . "{$slug}.php"

            ));

        }

        

        // Allow 3rd party plugin filter template file from their plugin

        $template = apply_filters('ae_get_template_part', $template, $slug, $name);

        

        if ($template) {

            load_template($template, false);

        }

    }

}



/**

 * check user capability

 * @param $cap user cap

 * @return bool

 * @author Dakachi

 */

function ae_user_can($cap) {

    return current_user_can($cap);

}

/**

 * get current user role  

 * @param int $user_ID The user ID you want to get role

 * @since v1.1

 * @return string $role current user role or null

 * @author Dakachi

 */

function ae_user_role($user_ID = '') {

    // get user id 's role

    if($user_ID != '') {

        $user_info = get_userdata($user_ID);

    }else{

        // get current user role

        global $current_user;

        $user_info = $current_user;

    }

    // if user exist

    if($user_info) {

        $roles = $user_info->roles;

        return array_pop($roles);    

    }

    // user not exist or not logged in

    return '';

}

/**

 * get theme option

 * @param $name the name of option

 * @return $option_value

 * @author Dakachi

 */

function ae_get_option($name, $default = false) {

    $option = AE_Options::get_instance();

    return ($option->$name != '') ? $option->$name : $default;

}



/**

 * update theme option, if option not exist create new

 * @param $name the name of option

 * @param $name the name of option

 * @return void

 * @author Dakachi

 */

function ae_update_option($name, $new_value) {

    if (!current_user_can('manage_options')) return;

    $option = AE_Options::get_instance();

    return $option->update_option($name, $new_value);

}

function ae_set_option($name, $new_value) {

    if (!current_user_can('manage_options')) return;

    $option = AE_Options::get_instance();

    return $option->update_option($name, $new_value);

}



/**

 * get site current currency sign (icon)

 * @param $echo bool

 * @author Dakachi

 */

function ae_currency_sign($echo = true) {

    

    // get option instance to retrieve option value

    $options = AE_Options::get_instance();

    if ($echo)

     // echo option

    echo $options->currency['icon'];

    else

    

    // return an option value

    return $options->currency['icon'];

}



/**

 * get site current currency sign (icon)

 * @param $echo bool

 * @author Dakachi

 */

function ae_currency_code($echo = true) {

    

    // get option instance to retrieve option value

    $options = AE_Options::get_instance();

    if ($echo)

     // echo code

    echo $options->currency['code'];

    else

    

    // return code

    return $options->currency['code'];

}



/**

 * print currency with a price format

 * @author Dakachi

 */

function ae_price($price, $echo = true) {

    $currency = '<sup>' . ae_currency_sign(false) . '</sup>';

    

    // if()

    $options = AE_Options::get_instance();

    $align = $options->currency['align'];

    

    if ($align) {

        echo $currency . $price;

    } else {

        echo $price . $currency;

    }

}



/**

 * get site current currency sign (icon)

 * @param $echo bool

 * @author Dakachi

 */

function ae_currency_align($echo = true) {

    

    // get option instance to retrieve option value

    $options = AE_Options::get_instance();

    if ($echo) echo $options->currency['align'];

    else return $options->currency['align'];

}



function ae_next_post($taxonomy, $excluded_terms = '', $in_same_terms = false) {

    return get_adjacent_post($in_same_terms, $excluded_terms, false, $taxonomy);

}



function ae_prev_post($taxonomy, $excluded_terms = '', $in_same_terms = false) {

    return get_adjacent_post($in_same_terms, $excluded_terms, true, $taxonomy);

}



/**

 * Retrieve adjacent post.

 *

 * Can either be next or previous post.

 *

 *

 * @param bool         $in_same_term   Optional. Whether post should be in a same taxonomy term.

 * @param array|string $excluded_terms Optional. Array or comma-separated list of excluded term IDs.

 * @param bool         $previous       Optional. Whether to retrieve previous post.

 * @param string       $taxonomy       Optional. Taxonomy, if $in_same_term is true. Default 'category'.

 * 

 * @return mixed       Post object if successful. Null if global $post is not set. Empty string if no corresponding post exists.

 *

 * @category ulti

 * 

 * @since 1.0

 * @author  Dakachi

 */

function ae_get_adjacent_post( $post_id = 0, $in_same_term = false, $excluded_terms = '', $previous = true, $taxonomy = 'category' ) {

    global $wpdb;



    $post = get_post($post_id);



    if ( ( ! $post ) || ! taxonomy_exists( $taxonomy ) )

        return null;



    $current_post_date = $post->post_date;



    $join = '';

    $where = '';



    if ( $in_same_term || ! empty( $excluded_terms ) ) {

        $join = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";

        $where = $wpdb->prepare( "AND tt.taxonomy = %s", $taxonomy );



        if ( ! empty( $excluded_terms ) && ! is_array( $excluded_terms ) ) {

            // back-compat, $excluded_terms used to be $excluded_terms with IDs separated by " and "

            if ( false !== strpos( $excluded_terms, ' and ' ) ) {

                _deprecated_argument( __FUNCTION__, '3.3', sprintf( __( 'Use commas instead of %s to separate excluded terms.' ), "'and'" ) );

                $excluded_terms = explode( ' and ', $excluded_terms );

            } else {

                $excluded_terms = explode( ',', $excluded_terms );

            }



            $excluded_terms = array_map( 'intval', $excluded_terms );

        }



        if ( $in_same_term ) {

            if ( ! is_object_in_taxonomy( $post->post_type, $taxonomy ) )

                return '';

            $term_array = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );



            // Remove any exclusions from the term array to include.

            $term_array = array_diff( $term_array, (array) $excluded_terms );

            $term_array = array_map( 'intval', $term_array );



            if ( ! $term_array || is_wp_error( $term_array ) )

                return '';



            $where .= " AND tt.term_id IN (" . implode( ',', $term_array ) . ")";

        }



        if ( ! empty( $excluded_terms ) ) {

            $where .= " AND p.ID NOT IN ( SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id IN (" . implode( $excluded_terms, ',' ) . ') )';

        }

    }



    $adjacent = $previous ? 'previous' : 'next';

    $op = $previous ? '<' : '>';

    $order = $previous ? 'DESC' : 'ASC';



    /**

     * Filter the JOIN clause in the SQL for an adjacent post query.

     *

     * The dynamic portion of the hook name, $adjacent, refers to the type

     * of adjacency, 'next' or 'previous'.

     *

     * @since 2.5.0

     *

     * @param string $join           The JOIN clause in the SQL.

     * @param bool   $in_same_term   Whether post should be in a same taxonomy term.

     * @param array  $excluded_terms Array of excluded term IDs.

     */

    $join  = apply_filters( "get_{$adjacent}_post_join", $join, $in_same_term, $excluded_terms );



    /**

     * Filter the WHERE clause in the SQL for an adjacent post query.

     *

     * The dynamic portion of the hook name, $adjacent, refers to the type

     * of adjacency, 'next' or 'previous'.

     *

     * @since 2.5.0

     *

     * @param string $where          The WHERE clause in the SQL.

     * @param bool   $in_same_term   Whether post should be in a same taxonomy term.

     * @param array  $excluded_terms Array of excluded term IDs.

     */

    $where = apply_filters( "get_{$adjacent}_post_where", $wpdb->prepare( "WHERE p.post_date $op %s AND p.post_type = %s AND p.post_status = 'publish' $where", $current_post_date, $post->post_type ), $in_same_term, $excluded_terms );



    /**

     * Filter the ORDER BY clause in the SQL for an adjacent post query.

     *

     * The dynamic portion of the hook name, $adjacent, refers to the type

     * of adjacency, 'next' or 'previous'.

     *

     * @since 2.5.0

     *

     * @param string $order_by The ORDER BY clause in the SQL.

     */

    $sort  = apply_filters( "get_{$adjacent}_post_sort", "ORDER BY p.post_date $order LIMIT 1" );



    $query = "SELECT p.ID FROM $wpdb->posts AS p $join $where $sort";

    $query_key = 'adjacent_post_' . md5( $query );

    $result = wp_cache_get( $query_key, 'counts' );

    if ( false !== $result ) {

        if ( $result )

            $result = get_post( $result );

        return $result;

    }



    $result = $wpdb->get_var( $query );

    if ( null === $result )

        $result = '';



    wp_cache_set( $query_key, $result, 'counts' );



    if ( $result )

        $result = get_post( $result );



    return $result;

}



/**

 * ae_process_payment function process payment return to check payment amount, update order

 * @use AE_Order , ET_NOPAYOrder, AE_Payment_Factory

 * @param string $payment_type the string of payment type such as paypal, 2checkout , stripe

 * @param Array $data

 *  -args $order_id : current order_id on process

 *  -args $ad_id : current ad id user submit

 * @return Array $payment_return

 *

 * @package AE Payment

 * @category payment

 *

 * @since 1.0

 * @author  Dakachi

 * 

 */

function ae_process_payment($payment_type, $data) {

    $payment_return = array(

        'ACK' => false

    );

    if ($payment_type) {

        

        // check order id

        if (isset($data['order_id'])) $order = new AE_Order($data['order_id']);

        else $order = new ET_NOPAYOrder();

        

        // call a visitor process order base on payment type

        $visitor = AE_Payment_Factory::createPaymentVisitor(strtoupper($payment_type) , $order);

        $payment_return = $order->accept($visitor);

        

        $data['order'] = $order;

        $data['payment_type'] = $payment_type;

        

        /**

         * filter payment return

         * @param Array $payment_return

         * @param Array $data -order : Order data, payment_type ...

         * @since 1.0

         */

        $payment_return = apply_filters('ae_process_payment', $payment_return, $data);

        $payment_return['order'] = $data['order'];

        

        /**

         * do an action after payment

         * @param Array $payment_return

         * @param Array $data -order : Order data, payment_type ...

         * @since 1.0

         */

        do_action('ae_process_payment_action', $payment_return, $data);

    }

    

    return $payment_return;

}



/**

 * count user comment by email

 * @param emal $email (required) The email of user you want to count comments

 * @version 1.0

 * 

 * @package AE Util

 * @category util

 *

 * @since  1.0

 * @author Dakachi

 */

function comment_count($email) {

    global $wpdb;

    $count = $wpdb->get_var('SELECT COUNT(comment_ID) FROM ' . $wpdb->comments . ' WHERE comment_author_email = "' . $email . '"');

    return $count;

}



/**

 * count user post by post type

 * @param (integer) $userID (required) The ID of the user to count posts for.

 * @param (string) $post_type The post type you want to count, Default is post.

 * @version 1.0

 * @author dakachi

 * @package AE

 * http://codex.wordpress.org/Function_Reference/count_user_posts

 */

function ae_count_user_posts_by_type($userid, $post_type = 'post') {

    global $wpdb;

    

    $where = get_posts_by_author_sql($post_type, true, $userid);

    

    $count = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts $where");

    

    return apply_filters('get_usernumposts', $count, $userid);

}



/*

 * return array wp_editor config

*/

function ae_editor_settings() {

    return apply_filters('ae_editor_settings', array(

        'quicktags' => false,

        'media_buttons' => false,

        'wpautop' => false,

        //'tabindex'    =>  '2',

        'teeny' => false,

        'tinymce' => array(

            'content_css' => ae_get_url() . '/assets/css/tinyeditor-content.css',

            'height' => 250,

            'editor_class' => 'input-item',

            'autoresize_min_height' => 250,

            'autoresize_max_height' => 550,

            'menu' => array(
                'edit' => array(
                    'title' => 'Edit', 'items' => 'undo redo | cut copy paste pastetext | selectall')
            ,

            ),



            'theme_advanced_buttons1' => 'bold,|,italic,|,underline,|,bullist,numlist,|,link,unlink,|,wp_fullscreen',

            'theme_advanced_buttons2' => '',

            'theme_advanced_buttons3' => '',

            'theme_advanced_statusbar_location' => 'none',

            'theme_advanced_resizing' => true,

            'paste_auto_cleanup_on_paste' => true,

            'setup' => "function(ed){

                ed.onChange.add(function(ed, l) {

                    var content = ed.getContent();

                    if(ed.isDirty() || content === '' ){

                        ed.save();

                        jQuery(ed.getElement()).blur(); // trigger change event for textarea

                    }



                });



                // We set a tabindex value to the iframe instead of the initial textarea

                ed.onInit.add(function() {

                    var editorId = ed.editorId,

                        textarea = jQuery('#'+editorId);

                    jQuery('#'+editorId+'_ifr').attr('tabindex', textarea.attr('tabindex'));

                    textarea.attr('tabindex', null);

                });

            }"

        )

    ));

}



add_filter('teeny_mce_buttons', 'ce_teeny_mce_buttons');

function ce_teeny_mce_buttons($buttons) {

    return array(

        'format',

        'bold',

        'italic',

        'underline',

        'bullist',

        'numlist',

        'link',

        'unlink'

    );

}



//function ce_tinymce_add_plugins($plugin_array) {
//
//
//
//    // $autoresize = get_template_directory_uri() . '/js/lib/tiny_mce/plugins/autoresize/editor_plugin.js';
//
//
//
//    // //$plugin_array['feimage'] = $feimage;
//
//    // if(!is_admin())
//
//    //     $plugin_array['autoresize'] = $autoresize;
//
//
//
//    //$plugin_array['etHeading']    = $et_heading;
//
//    //$plugin_array['wordcount']    = $wordcount;
//
//
//
//    return $plugin_array;
//
//}

//add_filter('mce_external_plugins', 'ce_tinymce_add_plugins');



/* add function */

add_filter('mce_external_plugins', 'ce_tinymce_add_plugins2');

function ce_tinymce_add_plugins2 () {
    $plugins = array('wordcount');

    $plugins_array = array();

    foreach ($plugins as $plugin ) {
        //$args[ $plugin ] = get_template_directory_uri().'/includes/aecore/tinymce/'. $plugin . '/plugin.min.js';
        $plugins_array[ $plugin ] = get_template_directory_uri().'/includes/aecore/tinymce/'. $plugin . '/editor_plugin.js';
    }


    return $plugins_array;
}

/**

 * process uploaded image: save to upload_dir & create multiple sizes & generate metadata

 * @param  [type]  $file     [the $_FILES['data_name'] in request]

 * @param  [type]  $author   [ID of the author of this attachment]

 * @param  integer $parent=0 [ID of the parent post of this attachment]

 * @param  array [$mimes] [array of supported file extensions]

 * @return [int/WP_Error]   [attachment ID if successful, or WP_Error if upload failed]

 * @author anhcv

 */

function et_process_file_upload($file, $author = 0, $parent = 0, $mimes = array()) {

    

    global $user_ID;

    $author = (0 == $author || !is_numeric($author)) ? $user_ID : $author;

    

    if (isset($file['name']) && $file['size'] > 0) {

        

        // setup the overrides

        $overrides['test_form'] = false;

        if (!empty($mimes) && is_array($mimes)) {

            $overrides['mimes'] = $mimes;

        }

        

        // this function also check the filetype & return errors if having any

        if (!function_exists('wp_handle_upload')) {

            require_once (ABSPATH . 'wp-admin/includes/file.php');

        }

        $uploaded_file = wp_handle_upload($file, $overrides);

        

        //if there was an error quit early

        if (isset($uploaded_file['error'])) {

            return new WP_Error('upload_error', $uploaded_file['error']);

        } elseif (isset($uploaded_file['file'])) {

            

            // The wp_insert_attachment function needs the literal system path, which was passed back from wp_handle_upload

            $file_name_and_location = $uploaded_file['file'];

            

            // Generate a title for the image that'll be used in the media library

            $file_title_for_media_library = sanitize_file_name($file['name']);

            

            $wp_upload_dir = wp_upload_dir();

            

            // Set up options array to add this file as an attachment

            $attachment = array(

                'guid' => $uploaded_file['url'],

                'post_mime_type' => $uploaded_file['type'],

                'post_title' => $file_title_for_media_library,

                'post_content' => '',

                'post_status' => 'inherit',

                'post_author' => $author

            );

            

            // Run the wp_insert_attachment function. This adds the file to the media library and generates the thumbnails. If you wanted to attch this image to a post, you could pass the post id as a third param and it'd magically happen.

            $attach_id = wp_insert_attachment($attachment, $file_name_and_location, $parent);

            require_once (ABSPATH . "wp-admin" . '/includes/image.php');

            $attach_data = wp_generate_attachment_metadata($attach_id, $file_name_and_location);

            wp_update_attachment_metadata($attach_id, $attach_data);

            return $attach_id;

        } else {

             // wp_handle_upload returned some kind of error. the return does contain error details, so you can use it here if you want.

            return new WP_Error('upload_error', __('There was a problem with your upload.', 'aecore-functions-backend'));

        }

    } else {

         // No file was passed

        return new WP_Error('upload_error', __('Where is the file?', 'aecore-functions-backend'));

    }

}



/**

 * handle file upload prefilter to tracking error

 */



//remove_filter( 'wp_handle_upload_prefilter','check_upload_size' );

add_filter('wp_handle_upload_prefilter', 'et_handle_upload_prefilter', 9);

function et_handle_upload_prefilter($file) {

    if (!is_multisite()) return $file;

    

    if (get_site_option('upload_space_check_disabled')) return $file;

    

    if ($file['error'] != '0')

    

    // there's already an error

    return $file;

    

    if (defined('WP_IMPORTING')) return $file;

    

    $space_allowed = 1048576 * get_space_allowed();

    $space_used = get_dirsize(BLOGUPLOADDIR);

    $space_left = $space_allowed - $space_used;

    $file_size = filesize($file['tmp_name']);

    if ($space_left < $file_size) $file['error'] = sprintf(__('Not enough space to upload. %1$s KB needed.', 'aecore-functions-backend') , number_format(($file_size - $space_left) / 1024));

    if ($file_size > (1024 * get_site_option('fileupload_maxk', 1500))) $file['error'] = sprintf(__('This file is too big. Files must be less than %1$s KB in size.', 'aecore-functions-backend') , get_site_option('fileupload_maxk', 1500));

    if (function_exists('upload_is_user_over_quota') && upload_is_user_over_quota(false)) {

        $file['error'] = __('You have used your space quota. Please delete files before uploading.', 'aecore-functions-backend');

    }

    

    // if ( $file['error'] != '0' && !isset($_POST['html-upload']) )

    //  wp_die( $file['error'] . ' <a href="javascript:history.go(-1)">' . __( 'Back' ) . '</a>' );

    return $file;

}



/**

 * Return all sizes of an attachment

 * @param   $attachment_id

 * @return  an array with [key] as the size name & [value] is an array of image data in that size

 *             e.g:

 *             array(

 *              'thumbnail' => array(

 *                  'src'   => [url],

 *                  'width' => [width],

 *                  'height'=> [height]

 *              )

 *             )

 * @since 1.0

 */

function et_get_attachment_data($attach_id, $size = array()) {

    

    // if invalid input, return false

    if (empty($attach_id) || !is_numeric($attach_id)) return false;

    

    $data = array(

        'attach_id' => $attach_id

    );

    

    if (!empty($size)) {

        $all_sizes = $size;

    } else {

        $all_sizes = get_intermediate_image_sizes();

    }

    

    $all_sizes[] = 'full';

    

    foreach ($all_sizes as $size) {

        $data[$size] = wp_get_attachment_image_src($attach_id, $size);

    }

    $data['src'] = wp_get_attachment_url( $attach_id );

    $data['name'] = get_the_title( $attach_id );

    return $data;

}



/**

 *

 * CONVERT POST_DATE INTO  HUMAN TIME

 * @param  int $timestamp

 * @author ToanNM

 * @since v1.0

 *

 *

 */

function ae_the_time($from) {

    

    //

    if (time() - $from > (7 * 24 * 60 * 60)) {

        return sprintf(__('on %s', 'aecore-functions-backend') , date_i18n(get_option('date_format') , $from, true));

    } else {

        return ae_human_time_diff($from) . ' ' . __('ago', 'aecore-functions-backend');

    }

}



function et_number_based($zero, $single, $plural, $num) {

    if ((int)$num <= 0) {

        return $zero;

    } else if ((int)$num == 1) {

        return $single;

    } else if ((int)$num > 1) {

        return $plural;

    }

}



/**

 * Determines the difference between two timestamps.

 *

 * The difference is returned in a human readable format such as "1 hour",

 * "5 mins", "2 days".

 *

 * @since 1.5.0

 *

 * @param int $from Unix timestamp from which the difference begins.

 * @param int $to Optional. Unix timestamp to end the time difference. Default becomes time() if not set.

 * @return string Human readable time difference.

 */

function ae_human_time_diff($from, $to = '') {

    if (empty($to)) $to = current_time('timestamp');

    

    $diff = (int)abs($to - $from);

    

    if ($diff < HOUR_IN_SECONDS) {

        $mins = round($diff / MINUTE_IN_SECONDS);

        if ($mins <= 1) $mins = 1;

        

        /* translators: min=minute */

        $since = sprintf(et_number_based(__('%s min', 'aecore-functions-backend') , __('%s min', 'aecore-functions-backend') , __('%s mins', 'aecore-functions-backend') , $mins) , $mins);

    } elseif ($diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        if ($hours <= 1) $hours = 1;

        $since = sprintf(et_number_based(__('%s hour', 'aecore-functions-backend') , __('%s hour', 'aecore-functions-backend') , __('%s hours', 'aecore-functions-backend') , $hours) , $hours);

    } elseif ($diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        $days = round($diff / DAY_IN_SECONDS);

        if ($days <= 1) $days = 1;

        $since = sprintf(et_number_based(__('%s day', 'aecore-functions-backend') , __('%s day', 'aecore-functions-backend') , __('%s days', 'aecore-functions-backend') , $days) , $days);

    } elseif ($diff < 30 * DAY_IN_SECONDS && $diff >= WEEK_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        $weeks = round($diff / WEEK_IN_SECONDS);

        if ($weeks <= 1) $weeks = 1;

        $since = sprintf(et_number_based(__('%s week', 'aecore-functions-backend') , __('%s week', 'aecore-functions-backend') , __('%s weeks', 'aecore-functions-backend') , $weeks) , $weeks);

    } elseif ($diff < YEAR_IN_SECONDS && $diff >= 30 * DAY_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        $months = round($diff / (30 * DAY_IN_SECONDS));

        if ($months <= 1) $months = 1;

        $since = sprintf(et_number_based(__('%s month', 'aecore-functions-backend') , __('%s month', 'aecore-functions-backend') , __('%s months', 'aecore-functions-backend') , $months) , $months);

    } elseif ($diff >= YEAR_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        $years = round($diff / YEAR_IN_SECONDS);

        if ($years <= 1) $years = 1;

        $since = sprintf(et_number_based(__('%s year', 'aecore-functions-backend') , __('%s year', 'aecore-functions-backend') , __('%s years', 'aecore-functions-backend') , $years) , $years);

    }

    

    return $since;

}



function ae_block_ie($version, $page) {

    $info = ae_getBrowser();

    

    //if ( $info['name'] == 'Internet Explorer' && version_compare($version, $info['version'], '>=') && file_exists(TEMPLATEPATH . '/' . $page)){

    if (!is_page_template('page-unsupported.php')) {

        

        // find a template "unsupported"

        // If template doesn't existed, create it

        

        

?>

            <script type="text/javascript">

                var detectBrowser = function () {

                

                    var isOpera = this.check(/opera/);

                    var isIE = !isOpera && check(/msie/);

                    var isIE8 = isIE && check(/msie 8/);

                    var isIE7 = isIE && check(/msie 7/);

                    var isIE6 = isIE && check(/msie 6/);



                    if( ( isIE6 || isIE7 || isIE8 )  ) window.location   =   '<?php

        echo et_get_page_link("unsupported"); ?>';

                }



                var check  = function (r) {

                    var ua = navigator.userAgent.toLowerCase();

                    return r.test(ua);

                }

                detectBrowser ();



            </script>



            <?php

    }

}



/**

 * Detect user's browser and version

 * @return array browser info

 */

function ae_getBrowser() {

    $u_agent = $_SERVER['HTTP_USER_AGENT'];

    $bname = 'Unknown';

    $platform = 'Unknown';

    $version = "";

    $ub = "MSIE";

    

    //First get the platform?

    if (preg_match('/linux/i', $u_agent)) {

        $platform = 'linux';

    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {

        $platform = 'mac';

    } elseif (preg_match('/windows|win32/i', $u_agent)) {

        $platform = 'windows';

    }

    

    // Next get the name of the useragent yes separately and for good reason.

    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {

        $bname = 'Internet Explorer';

        $ub = "MSIE";

    } elseif (preg_match('/Firefox/i', $u_agent)) {

        $bname = 'Mozilla Firefox';

        $ub = "Firefox";

    } elseif (preg_match('/Chrome/i', $u_agent)) {

        $bname = 'Google Chrome';

        $ub = "Chrome";

    } elseif (preg_match('/Safari/i', $u_agent)) {

        $bname = 'Apple Safari';

        $ub = "Safari";

    } elseif (preg_match('/Opera/i', $u_agent)) {

        $bname = 'Opera';

        $ub = "Opera";

    } elseif (preg_match('/Netscape/i', $u_agent)) {

        $bname = 'Netscape';

        $ub = "Netscape";

    }

    

    // Finally get the correct version number.

    $known = array(

        'Version',

        $ub,

        'other'

    );

    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

    if (!preg_match_all($pattern, $u_agent, $matches)) {

        

        // we have no matching number just continue

        

    }

    

    // See how many we have.

    $i = count($matches['browser']);

    if (isset($matches['version'])) {

        if ($i != 1) {

            

            //we will have two since we are not using 'other' argument yet

            //see if version is before or after the name

            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {

                $version = isset($matches['version'][0]) ? $matches['version'][0] : '4.0';

            } else {

                $version = isset($matches['version'][1]) ? $matches['version'][1] : '4.0';

            }

        } else {

            $version = isset($matches['version'][0]) ? $matches['version'][0] : '4.0';

        }

    } else {

        $version = '4.0';

    }

    

    if ($ub == "MSIE") {

        preg_match('/(MSIE) [0-9.]*;/', $u_agent, $matches);

        $version = isset($matches[0]) ? $matches[0] : '1.0';

        $version = str_replace(array(

            'MSIE',

            ';',

            ' '

        ) , '', $version);

    }

    

    // Check if we have a number.

    if ($version == null || $version == "") {

        $version = "?";

    }

    

    return array(

        'userAgent' => $u_agent,

        'name' => $bname,

        'version' => $version,

        'platform' => $platform,

        'pattern' => $pattern

    );

}



/**

 * Handle mobile here

 */

add_filter('template_include', 'et_template_mobile');

function et_template_mobile($template) {

    global $user_ID, $wp_query, $wp_rewrite;

    $new_template = $template;

    

    // no need to redirect when in admin

    if (is_admin()) return $template;

    

    /***

     * Detect mobile and redirect to the correlative layout file

    */

    $filename = basename($template);

    if (et_load_mobile()) {       

        

        $child_path = get_stylesheet_directory() . '/mobile' . '/' . $filename;

        $parent_path = get_template_directory() . '/mobile' . '/' . $filename;

        

        if (file_exists($child_path)) {

            $new_template = $child_path;

        } else if (file_exists($parent_path)) {

            $new_template = $parent_path;

        } else {

            wp_redirect(home_url());

            // $new_template = get_template_directory() . '/mobile/unsupported.php';

        }

        if (!in_array($filename, array(

            'header.php',

            'footer.php'

        ))) {

            // if (is_page_template('page-login.php')) {

            //     $new_template = get_template_directory() . '/mobile/page-login.php';

            // } else if (is_page_template('page-register.php')) {

            //     $new_template = get_template_directory() . '/mobile/page-register.php';

            // }

        }

    }

    

    return apply_filters('et_template_mobile', $new_template , $template , $filename);

}



/**

 * detect mobile version

 * @return bool

 */

function et_load_mobile() {

    

    $detector = new AE_MobileDetect();

    $isMobile = apply_filters('ae_is_mobile', ($detector->isMobile() && !$detector->isTablet()) ? true : false);

    if ($isMobile

     /*&& (!isset($_COOKIE['mobile']) || md5('disable') != $_COOKIE['mobile'] )*/) {

        return true;

    } else {

        return false;

    }

}





/**

 * detect mobile version

 * @return bool

 */

function et_load_tablet() {

    

    $detector = AE_MobileDetect::get_instance();

    $isTablet = apply_filters('ae_is_tablet', $detector->isTablet() ? true : false);

    if ($isTablet

     /*&& (!isset($_COOKIE['mobile']) || md5('disable') != $_COOKIE['mobile'] )*/) {

        return true;

    } else {

        return false;

    }

}



/**

 * Get mobile version header template

 * @author toannm

 * @param name of the custom header template

 * @version 1.0

 * @copyright enginethemes.com team

 * @license enginethemes.com team

 */

function et_get_mobile_header($name = null) {

    //do_action('get_header', $name);

    

    //$templates = array();

    $templates = MOBILE_PATH . '/' . 'header.php';

    if (isset($name)) $templates = MOBILE_PATH . '/' . "header-{$name}.php";

    $templates = apply_filters('template_include', $templates);

    

    if ('' == locate_template($templates, true))

    

    //load_template( ABSPATH . WPINC . '/theme-compat/header.php');

    load_template($templates);

}



/**

 * Get mobile version header template

 * @author toannm

 * @param name of the custom header template

 * @version 1.0

 * @copyright enginethemes.com team

 * @license enginethemes.com team

 */

function et_get_mobile_footer($name = null) {

    

    do_action('get_footer', $name);

    

    //$templates = array();

    $templates = MOBILE_PATH . '/' . 'footer.php';

    if (isset($name)) $templates = MOBILE_PATH . '/' . "footer-{$name}.php";

    $templates = apply_filters('template_include', $templates);

    

    //$templates = apply_filters( 'template_include', $templates );

    // Backward compat code will be removed in a future release

    if ('' == locate_template($templates, true))

    

    //load_template( ABSPATH . WPINC . '/theme-compat/footer.php');

    load_template($templates);

}



/**

 *

 * Get the page template link

 * @param string $pages: login or register

 * @param array $params: array of query var

 * @return $link

 * @author Dakachi

 * @version 1.0

 * @copyright enginethemes.com team

 */

if( !function_exists( 'et_get_page_link' ) ){

    function et_get_page_link($pages, $params = array() , $create = true) {

        

        $page_args = array(

            'post_title' => '',

            'post_content' => __('Please fill out the form below ', 'aecore-functions-backend') ,

            'post_type' => 'page',

            'post_status' => 'publish'

        );

        

        if (is_array($pages)) {

            

            // page data is array (using this for insert page content purpose)

            $page_type = $pages['page_type'];

            $page_args = wp_parse_args($pages, $page_args);

        } else {

            

            // pages is page_type string (using this only insert a page template)

            $page_type = $pages;

            $page_args['post_title'] = $page_type;

        }        

        /**

         * get page template link option and will return if it not empty

         */

        $link = get_option($page_type, '');

        if ($link) {

            $return = add_query_arg($params, $link);

            return apply_filters('et_get_page_link', $return, $page_type, $params);

        }

        

        // find post template

        $pages = get_pages(array(

            'meta_key' => '_wp_page_template',

            'meta_value' => 'page-' . $page_type . '.php',

            'numberposts' => 1

        ));

        

        // page not existed

        if (empty($pages) || !is_array($pages)) {

            

            // return false if set create is false and doesnot generate page

            if (!$create) return false;

            if( !ae_get_option('auto_create_page', false) ){                    

                // insert page

                $id = wp_insert_post($page_args);

                

                if ($id) {

                    

                    // update page template option

                    update_post_meta($id, '_wp_page_template', 'page-' . $page_type . '.php');

                }

            }

            else{

                $id = -1;

            }

        } else {

            

            // page exists

            $page = array_shift($pages);

            $id = $page->ID;

        }

        if($id != -1 ){

            $return = get_permalink($id);

        }

        else{

            $return = home_url();

        }

        /**

         * update transient page link

         */

        update_option('page-' . $page_type . '.php', $return);

        

        if (!empty($params) && is_array($params)) {

            $return = add_query_arg($params, $return);

        }

        

        return apply_filters('et_get_page_link', $return, $page_type, $params);

    }

}



/**

 *

 * CONVERT POST_DATE INTO  HUMAN TIME

 * @param  int $timestamp

 * @author ToanNM

 * @since v1.0

 *

 *

 */

function et_the_time($from) {

    

    //

    if (time() - $from > (7 * 24 * 60 * 60)) {

        return sprintf(__('on %s', 'aecore-functions-backend') , date_i18n(get_option('date_format') , $from, true));

    } else {

        return et_human_time_diff($from) . ' ' . __('ago', 'aecore-functions-backend');

    }

}



/**

 * Determines the difference between two timestamps.

 *

 * The difference is returned in a human readable format such as "1 hour",

 * "5 mins", "2 days".

 *

 * @since 1.5.0

 *

 * @param int $from Unix timestamp from which the difference begins.

 * @param int $to Optional. Unix timestamp to end the time difference. Default becomes time() if not set.

 * @return string Human readable time difference.

 */

function et_human_time_diff($from, $to = '') {

    if (empty($to)) $to = current_time('timestamp');

    

    $diff = (int)abs($to - $from);

    

    if ($diff < HOUR_IN_SECONDS) {

        $mins = round($diff / MINUTE_IN_SECONDS);

        if ($mins <= 1) $mins = 1;

        

        /* translators: min=minute */

        $since = sprintf(et_number_based(__('%s min', 'aecore-functions-backend') , __('%s min', 'aecore-functions-backend') , __('%s mins', 'aecore-functions-backend') , $mins) , $mins);

    } elseif ($diff < DAY_IN_SECONDS && $diff >= HOUR_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        if ($hours <= 1) $hours = 1;

        $since = sprintf(et_number_based(__('%s hour', 'aecore-functions-backend') , __('%s hour', 'aecore-functions-backend') , __('%s hours', 'aecore-functions-backend') , $hours) , $hours);

    } elseif ($diff < WEEK_IN_SECONDS && $diff >= DAY_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        $days = round($diff / DAY_IN_SECONDS);

        if ($days <= 1) $days = 1;

        $since = sprintf(et_number_based(__('%s day', 'aecore-functions-backend') , __('%s day', 'aecore-functions-backend') , __('%s days', 'aecore-functions-backend') , $days) , $days);

    } elseif ($diff < 30 * DAY_IN_SECONDS && $diff >= WEEK_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        $weeks = round($diff / WEEK_IN_SECONDS);

        if ($weeks <= 1) $weeks = 1;

        $since = sprintf(et_number_based(__('%s week', 'aecore-functions-backend') , __('%s week', 'aecore-functions-backend') , __('%s weeks', 'aecore-functions-backend') , $weeks) , $weeks);

    } elseif ($diff < YEAR_IN_SECONDS && $diff >= 30 * DAY_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        $months = round($diff / (30 * DAY_IN_SECONDS));

        if ($months <= 1) $months = 1;

        $since = sprintf(et_number_based(__('%s month', 'aecore-functions-backend') , __('%s month', 'aecore-functions-backend') , __('%s months', 'aecore-functions-backend') , $months) , $months);

    } elseif ($diff >= YEAR_IN_SECONDS) {

        $hours = round($diff / HOUR_IN_SECONDS);

        $years = round($diff / YEAR_IN_SECONDS);

        if ($years <= 1) $years = 1;

        $since = sprintf(et_number_based(__('%s year', 'aecore-functions-backend') , __('%s year', 'aecore-functions-backend') , __('%s years', 'aecore-functions-backend') , $years) , $years);

    }

    

    return $since;

}





if(!function_exists('ae_order_by_post_status')) {

/**

 *

 * Function add filter orderby post status

 *

 *

 */

function ae_order_by_post_status($orderby) {

    global $wpdb;

    $orderby = " case {$wpdb->posts}.post_status

                         when 'reject' then 0 

                         when 'pending' then 1

                         when 'publish' then 2

                         when 'draft' then 3

                         when 'archive' then 4

                         end, 

            {$wpdb->posts}.post_date DESC";

    return $orderby;

}

}

/*

 *Function check is social connect page

 */

function is_social_connect_page(){

    if(!is_singular('page')) return false;

    $current_page_id = get_the_ID();

    $social_connect_page = ae_get_option('social_connect');

    $social_connect_page = str_replace('www.', '', $social_connect_page);

    $site_url = home_url('/');

    $site_url = str_replace('www.', '', $site_url);

    $social_connect_page = str_replace($site_url, '', $social_connect_page);

    $page_connect_id = get_page_by_path($social_connect_page, OBJECT);

    if($page_connect_id){

        $page_connect_id = (int)$page_connect_id->ID;

    }

    $flag = false;

    if($page_connect_id == $current_page_id){

        $flag = true;

    }

    return $flag;

}

/**

*get socail connect page link

*/

function ae_get_social_connect_page_link(){

    $page_link = ae_get_option('ae_social_connect_page_link', false);

    $check = ae_get_option('social_connect', false);

    if(!$check && !$page_link){

        $page_args = array(

            'post_title' => 'authentication',

            'post_content' => '[social_connect_page]' ,

            'post_type' => 'page',

            'post_status' => 'publish'

        );

        $id = wp_insert_post($page_args);

        $page_link = get_permalink($id);

        ae_update_option('ae_social_connect_page_link', $page_link);

        ae_update_option('social_connect', $page_link);

    }

    return apply_filters('ae_get_social_connect_page_link',$page_link);

}

/**

 * Get social default user roles

 *

 */

if( !function_exists( 'ae_get_social_login_user_roles_default' ) ){

    function ae_get_social_login_user_roles_default(){

        return apply_filters( 'ae_social_login_user_roles_default', array( 'author' ) );

    }

}

/**

 * Check cookie before send/resend activation code

 * @param snippet

 * @return true if user can send/resend activation code. False if neither.

 * @since 

 * @package Appengine

 * @category USER MANAGEMENT

 * @author Tambh

 */

if( !function_exists( 'ae_is_send_activation_code' ) ){

    function ae_is_send_activation_code(){

        if( isset( $_COOKIE['ae_sent_activation_code'] ) && $_COOKIE['ae_sent_activation_code'] == 1 ){

            return false;

        }

        else{

            return true;

        }

    }

}

/**
 * This function returns all country list
 */
function ae_country_list() {
    global $wpdb;
    
    $arrCountry = $wpdb->get_results( "SELECT id, country_code, country_name FROM wp_countries WHERE 1" );
    
    return $arrCountry;
}
function interview_is_profile_activated(){
    global $user_ID;
    $return = (get_user_meta($user_ID,'interview_status',true) == 'unconfirm');

    return $return;
}