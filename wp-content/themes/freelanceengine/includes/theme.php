<?php

if (!function_exists('fre_project_demonstration')) {

    

    /**

     * render project desmonstration settings in hompage

     * @param bool $home if true render home page desmonstration/ false render list project demonstration

     * @since v1.0

     * @author Dakachi

     */

    function fre_project_demonstration($home = false) {

        $project_demonstration = ae_get_option('project_demonstration');

        if ($home) {

            echo $project_demonstration['home_page'];

            return;

        }

        echo $project_demonstration['list_project'];

    }

}



if (!function_exists('fre_profile_demonstration')) {

    

    /**

     * render profile desmonstration settings in header

     * @param bool $home if true render home page desmonstration/ false render list project demonstration

     * @since v1.0

     * @author Dakachi

     */

    function fre_profile_demonstration($home = false) {

        $project_demonstration = ae_get_option('profile_demonstration');

        if ($home) {

            echo $project_demonstration['home_page'];

            return;

        }

        echo $project_demonstration['list_profile'];

    }

}



if (!function_exists('fre_logo')) {

    

    /**

     * render site logo image get from option

     * @author tam

     * @return void

     */

    function fre_logo($option_name = '') {

        if ($option_name == '') {

            if (is_front_page()) {

                $option_name = 'site_logo_white';

            } else {

                $option_name = 'site_logo_black';

            }

        }

        switch ($option_name) {

            case 'site_logo_black':

                $img = get_template_directory_uri() . "/img/logo-fre-black.png";

                break;



            case 'site_logo_white':

                $img = get_template_directory_uri() . "/img/logo-fre-white.png";

                break;



            default:

                $img = get_template_directory_uri() . "/img/logo-fre-black.png";

                break;

        }

        $options = AE_Options::get_instance();

        

        // save this setting to theme options

        $site_logo = $options->$option_name;

        if (!empty($site_logo)) {

            $img = $site_logo['large'][0];

        }

        echo '<img alt="' . $options->blogname . '" src="' . $img . '" />';

    }

}



/**

 * check site option shared role or not

 * @since 1.2

 * @author Dakachi

 */

function fre_share_role() {

    $options = AE_Options::get_instance();

    

    // save this setting to theme options

    return $options->fre_share_role;

}

function check_existing_post_name($title){
    global $wpdb;
    $postid = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $title . "'" );
    if ($postid){
        return true;
    }else{
        return false;
    }
}
/**

 * allow user to upload a video file

 * @author tam

 *

 */

add_filter('upload_mimes', 'fre_add_mime_types');

add_filter('et_upload_file_upload_mimes', 'fre_add_mime_types');

function fre_add_mime_types($mimes) {

    /**

     * admin can add more file extension

     */

    if (current_user_can('manage_options')) {

        return array_merge($mimes, array(

            'ac3' => 'audio/ac3',

            'mpa' => 'audio/MPA',

            'flv' => 'video/x-flv',

            'svg' => 'image/svg+xml',

            'mp4' => 'video/MP4',

            'doc|docx' => 'application/msword',

            'pdf' => 'application/pdf',

            'zip' => 'multipart/x-zip'

        ));

    }

    // if user is normal user

    $mimes = array_merge($mimes, array(

        'doc|docx' => 'application/msword',

        'pdf' => 'application/pdf',

        'zip' => 'multipart/x-zip'

    ));

    return $mimes;

}



/**

 * get content current currency sign (icon)

 * @param $echo bool

 * @author Dakachi

 */

function fre_currency_sign($echo = true) {

    

    // get option instance to retrieve option value

    $options = AE_Options::get_instance();

    if ($echo)

    

    // echo option

    echo $options->content_currency['icon'];

    else

    

    // return an option value

    return $options->content_currency['icon'];

}



function fre_price_format($amount, $style = '<sup>') {

    

    $currency = ae_get_option('content_currency', array(

        'align' => 'left',

        'code' => 'USD',

        'icon' => '$'

    ));

    

    $align = $currency['align'];

    

    // dafault = 0 == right;

    

    $currency = $currency['icon'];

    $price_format = get_theme_mod('decimal_point', 1);

    $format = '%1$s';

    

    switch ($style) {

        case 'sup':

            $format = '<sup>%s</sup>';

            break;



        case 'sub':

            $format = '<sub>%s</sub>';

            break;



        default:

            $format = '%s';

            break;

    }

    

    $number_format = ae_get_option('number_format');

    $decimal = (isset($number_format['et_decimal'])) ? $number_format['et_decimal'] : get_theme_mod('et_decimal', 2);

    $decimal_point = (isset($number_format['dec_point']) && $number_format['dec_point']) ? $number_format['dec_point'] : get_theme_mod('et_decimal_point', '.');

    $thousand_sep = (isset($number_format['thousand_sep']) && $number_format['thousand_sep']) ? $number_format['thousand_sep'] : get_theme_mod('et_thousand_sep', ',');

    

    if ($align != "0") {

        $format = $format . '%s';

        return sprintf($format, $currency, number_format((double)$amount, $decimal, $decimal_point, $thousand_sep));

    } else {

        $format = '%s' . $format;

        return sprintf($format, number_format((double)$amount, $decimal, $decimal_point, $thousand_sep) , $currency);

    }

}



function fre_number_format($amount, $echo = true){

    $number_format = ae_get_option('number_format');

    $decimal = (isset($number_format['et_decimal'])) ? $number_format['et_decimal'] : get_theme_mod('et_decimal', 2);

    $decimal_point = (isset($number_format['dec_point']) && $number_format['dec_point']) ? $number_format['dec_point'] : get_theme_mod('et_decimal_point', '.');

    $thousand_sep = (isset($number_format['thousand_sep']) && $number_format['thousand_sep']) ? $number_format['thousand_sep'] : get_theme_mod('et_thousand_sep', ',');

    if($echo) {

        echo number_format((double)$amount, $decimal, $decimal_point, $thousand_sep);

    }else{

        return number_format((double)$amount, $decimal, $decimal_point, $thousand_sep);

    }

}



/**

 *

 * Function add filter orderby post status

 *

 *

 */

function fre_order_by_bid_status($orderby) {

    global $wpdb;

    $orderby = " case {$wpdb->posts}.post_status

                         when 'complete' then 0 

                         when 'accept' then 1 

                         when 'publish' then 2

                         end, 

            {$wpdb->posts}.post_date DESC";

    return $orderby;

}



/**

 *

 * Function add filter orderby project post status

 *

 *

 */

function fre_order_by_project_status($orderby) {

    global $wpdb;

    $orderby = " case {$wpdb->posts}.post_status  

                            when 'disputing' then 0

                            when 'reject' then 1

                            when 'pending' then 2                             

                            when 'publish' then 3                           

                            when 'close' then 4

                            when 'complete' then 5 

                            when 'draft' then 6

                            when 'archive' then 7

                        end, 

                        {$wpdb->posts}.post_date DESC";

    return $orderby;

}



add_action('wp_ajax_ae_upload_files', 'fre_upload_file');

function fre_upload_file() {

    $res = array(

        'success' => false,

        'msg' => __('There is an error occurred', ET_DOMAIN) ,

        'code' => 400,

    );

    

    // check fileID

    if (!isset($_POST['fileID']) || empty($_POST['fileID'])) {

        $res['msg'] = __('Missing image ID', ET_DOMAIN);

    } else {

        $fileID = $_POST["fileID"];

        $imgType = $_POST['imgType'];

        

        // check ajax nonce

        if (!de_check_ajax_referer('file_et_uploader', false, false) && !check_ajax_referer('file_et_uploader', false, false)) {

            $res['msg'] = __('Security error!', ET_DOMAIN);

        } elseif (isset($_FILES[$fileID])) {

            

            // handle file upload

            $attach_id = et_process_file_upload($_FILES[$fileID], 0, 0, array(

                'jpg|jpeg|jpe' => 'image/jpeg',

                'gif' => 'image/gif',

                'png' => 'image/png',

                'bmp' => 'image/bmp',

                'tif|tiff' => 'image/tiff',

                'pdf' => 'application/pdf',

                'doc|docx' => 'application/msword',

                'odt' => 'application/vnd.oasis.opendocument.text',

                'zip' => 'application/zip',

                'rar' => 'application/rar'

            ));

            

            if (!is_wp_error($attach_id)) {

                

                try {

                    $attach_data = et_get_attachment_data($attach_id);

                    

                    $options = AE_Options::get_instance();

                    

                    // save this setting to theme options

                    // $options->$imgType = $attach_data;

                    // $options->save();

                    

                    

                    

                    /** 

                     * do action to control how to store data

                     * @param $attach_data the array of image data

                     * @param $request['data']

                     * @param $attach_id the uploaded file id

                     */

                    

                    //do_action('ae_upload_image' , $attach_data , $_POST['data'], $attach_id );

                    

                    $res = array(

                        'success' => true,

                        'msg' => __('File has been uploaded successfully', ET_DOMAIN) ,

                        'data' => $attach_data

                    );

                }

                catch(Exception $e) {

                    $res['msg'] = __('Error when updating settings.', ET_DOMAIN);

                }

            } else {

                $res['msg'] = $attach_id->get_error_message();

            }

        } else {

            $res['msg'] = __('Uploaded file not found', ET_DOMAIN);

        }

    }

    

    // send json to client

    wp_send_json($res);

}

/**

 * Check post type to use pending post 

 *

 * @since 1.5.2

 *

 * @author Tambh

 */

add_filter( 'use_pending', 'filter_post_type_use_pending', 10, 2 );

function filter_post_type_use_pending( $pending, $post_type ){

    if( $post_type == PROFILE ){

        $pending = false;

    }

    return $pending;

}

/**
 * Get country name using country code
 */
function get_country_name_by_country_code($countryCode) {
    global $wpdb;
  
    $results = $wpdb->get_results( "SELECT * FROM wp_countries WHERE country_code = '" . $countryCode . "'", OBJECT );
   
    return $results[0];
}