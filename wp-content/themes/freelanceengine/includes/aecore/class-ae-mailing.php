<?php


/**
 * class Mailing control mail options
 *
 * @package  AE Mailing
 * @category mail
 *
 * @since  1.0
 * @author Dakachi
 */
class AE_Mailing extends AE_Base

{

    public static $instance;


    static function get_instance()
    {

        if (self::$instance == null) {

            self::$instance = new AE_Mailing();

        }


        return self::$instance;

    }


    function __construct()
    {

    }


    /**
     * send email to user after he successful confirm email
     * @param Int $user_id
     * @version 1.0
     */

    function confirmed_mail($user_id)
    {

        $user = new WP_User($user_id);

        $user_email = $user->user_email;


        $subject = __("Congratulations! Your account has been verified successfully.", 'aecore-class-ae-mailing-backend');

        $message = ae_get_option('confirmed_mail_template_'.ICL_LANGUAGE_CODE);


        $this->wp_mail($user_email, $subject, $message, array(

            'user_id' => $user_id

        ));

    }


    /**
     * send email to user after he successful confirm phone
     * @param Int $user_id
     * @version 1.0
     */

    function confirmed_phone($user_id)
    {

        $user = new WP_User($user_id);

        $user_email = $user->user_email;


        $subject = __("Congratulations! Your phone has been verified successfully.", 'aecore-class-ae-mailing-backend');

        $message = ae_get_option('confirmed_phone_template_'.ICL_LANGUAGE_CODE);

        $this->wp_mail($user_email, $subject, $message, array(

            'user_id' => $user_id

        ));

    }


    /**
     * mail to user when a user contact him
     * @param Object WP_User  $author
     * @param String $message
     * @author ThaiNT
     */

    function inbox_mail($author, $inbox_message)
    {

        global $current_user;


        // $headers = 'MIME-Version: 1.0' . "\r\n";

        // $headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";

        $headers = "From: $current_user->display_name " . "\r\n";

        $headers .= 'Reply-To: ' . $current_user->display_name . ' <' . $current_user->user_email . '>' . "\r\n";


        $subject = sprintf(__('[%s]New Private Message From %s', 'aecore-class-ae-mailing-backend'), get_bloginfo('blogname'), $current_user->display_name);

        $message = ae_get_option('inbox_mail_template_'.ICL_LANGUAGE_CODE);

        $inbox_message = stripslashes(str_replace("\n", "<br>", $inbox_message));

        $sender = get_author_posts_url($current_user->ID);


        /**
         * replace holder receive

         */

        $message = str_ireplace('[display_name]', $author->display_name, $message);


        /**
         *

         */

        $message = str_ireplace('[sender_link]', $sender, $message);

        $message = str_ireplace('[sender]', $current_user->display_name, $message);

        $message = str_ireplace('[message]', $inbox_message, $message);

        $message = str_ireplace('[blogname]', get_bloginfo('blogname'), $message);


        $this->wp_mail($author->user_email, $subject, $message);

    }


    /**
     * user forgot pass mail
     * @param Int $user_id
     * @param String $key Activate key
     */

    function forgot_mail($user_id, $key)
    {

        $user = new WP_User($user_id);

        $user_email = $user->user_email;

        $user_login = $user->user_login;


        $message = ae_get_option('forgotpass_mail_template_'.ICL_LANGUAGE_CODE);


        $activate_url = add_query_arg(array(

            'user_login' => $user_login,

            'key' => $key

        ), et_get_page_link('reset-pass'));


        $activate_url = '<a href="' . $activate_url . '">' . __("Activate Link", 'aecore-class-ae-mailing-backend') . '</a>';

        $message = str_ireplace('[activate_url]', $activate_url, $message);


        if (is_multisite()) $blogname = $GLOBALS['current_site']->site_name;

        else $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);


        $subject = sprintf(__('[%s] Password Reset', 'aecore-class-ae-mailing-backend'), $blogname);


        $subject = apply_filters('et_retrieve_password_title', $subject);


        $this->wp_mail($user_email, $subject, $message, array(

            'user_id' => $user_id

        ));

    }


    /**
     * user report mail
     * @param String $admin_email
     * @param Array $request
     */

    function report_mail($admin_email, $request)
    {


        $user = new WP_User($request['user_report']);

        $place = get_post($request['comment_post_ID']);

        $subject = sprintf(__("[%s]New report message from %s.", 'aecore-class-ae-mailing-backend'), get_option('blogname'), $user->display_name);


        $message = ae_get_option('ae_report_mail_'.ICL_LANGUAGE_CODE);

        $message = str_replace('[place_title]', $place->post_title, $message);

        $message = str_replace('[place_link]', get_permalink($place->ID), $message);

        $message = str_replace('[report_message]', wpautop($request['comment_content']), $message);

        $message = str_replace('[user_name]', $user->display_name, $message);

        $message = str_replace('[reports_link]', admin_url('edit-comments.php?comment_type=report'), $message);


        $this->wp_mail($admin_email, $subject, $message);

    }


    /**
     * user approve claim mail
     * @param String $user_email
     * @param Array $request
     */

    function approve_claim_mail($user_email, $request)
    {


        $user = new WP_User($request['user_request']);

        $place = get_post($request['place_id']);

        $subject = sprintf(__("[%s]Your claim request has been approved.", 'aecore-class-ae-mailing-backend'), get_option('blogname'));


        $message = ae_get_option('ae_approve_claim_mail_'.ICL_LANGUAGE_CODE);

        $message = str_replace('[place_title]', $place->post_title, $message);

        $message = str_replace('[place_link]', get_permalink($place->ID), $message);

        $message = str_replace('[display_name]', $user->display_name, $message);


        $this->wp_mail($user_email, $subject, $message);

    }


    /**
     * user reject claim mail
     * @param String $user_email
     * @param Array $request
     */

    function reject_claim_mail($user_email, $request)
    {


        $user = new WP_User($request['user_request']);

        $place = get_post($request['place_id']);

        $subject = sprintf(__("[%s]Your claim request has been rejected.", 'aecore-class-ae-mailing-backend'), get_option('blogname'));


        $message = ae_get_option('ae_reject_claim_mail_'.ICL_LANGUAGE_CODE);

        $message = str_replace('[place_title]', $place->post_title, $message);

        $message = str_replace('[display_name]', $user->display_name, $message);


        $this->wp_mail($user_email, $subject, $message);

    }


    /**
     * user claim mail
     * @param String $admin_email
     * @param Array $request
     */

    function claim_mail($admin_email, $request)
    {


        $user = new WP_User($request['user_request']);

        $place = get_post($request['place_id']);

        $subject = sprintf(__("[%s]New claim request from %s.", 'aecore-class-ae-mailing-backend'), get_option('blogname'), $user->display_name);


        $message = ae_get_option('ae_claim_mail_'.ICL_LANGUAGE_CODE);

        $message = str_replace('[place_title]', $place->post_title, $message);

        $message = str_replace('[place_link]', get_permalink($place->ID), $message);

        $message = str_replace('[claim_message]', wpautop($request['message']), $message);

        $message = str_replace('[user_name]', $user->display_name, $message);

        $message = str_replace('[claim_full_name]', $request['display_name'], $message);

        $message = str_replace('[claim_email]', $user->user_email, $message);

        $message = str_replace('[claim_phone]', $request['phone'], $message);

        $message = str_replace('[claim_address]', $request['location'], $message);

        $message = str_replace('[place_edit_link]', admin_url('post.php?post=' . $request['place_id']) . '&action=edit', $message);


        $this->wp_mail($admin_email, $subject, $message);

    }
    function send_freelancer_interview($user,$subject,$message)
    {
        $this->wp_mail($user->user_email, $subject, $message, array(

            'user_id' => $user->ID

        ));
    }

    /**
     *

     */

    function register_mail($user_id)
    {

        $user = new WP_User($user_id);

        $user_email = $user->user_email;


        if (ae_user_role($user_id) == FREELANCER) {
            $subject = sprintf(__("Congratulations! You have successfully registered to %s.", 'aecore-class-ae-mailing-backend'), get_option('blogname'));

            if (ae_get_option('user_confirm')) {

                $message = ae_get_option('confirm_mail_freelancer_template_'.ICL_LANGUAGE_CODE);

//                $message = ae_get_option('register_mail_freelancer_template');

            } else {

                $message = ae_get_option('register_mail_freelancer_template_'.ICL_LANGUAGE_CODE);

            }
        } else {
            $subject = sprintf(__("Congratulations! You have successfully registered to %s.", 'aecore-class-ae-mailing-backend'), get_option('blogname'));

            if (ae_get_option('user_confirm')) {

                $message = ae_get_option('confirm_mail_template_'.ICL_LANGUAGE_CODE);

            } else {

                $message = ae_get_option('register_mail_template_'.ICL_LANGUAGE_CODE);

            }
        }

//        if (ae_get_option('user_confirm')) {
//
////            $message = ae_get_option('confirm_mail_template');
//            $message = ae_user_role($user_id) . '1';
//
//        } else {
//            $message = ae_user_role($user_id) . '2';
//
//            //$message = ae_get_option('register_mail_template');
//
//        }

        $this->wp_mail($user_email, $subject, $message, array(

            'user_id' => $user_id

        ));

    }


    /* user request a new confirm email */

    function request_confirm_mail($user_id)
    {

        global $current_user;

        $user = $current_user;

        $user_email = $user->user_email;


        $subject = sprintf(__("You have request a confirm email from %s.", 'aecore-class-ae-mailing-backend'), get_option('blogname'));


        //if (ae_get_option('user_confirm')) {

        $message = ae_get_option('confirm_mail_template_'.ICL_LANGUAGE_CODE);


        // }

        return $this->wp_mail($user_email, $subject, $message, array(

            'user_id' => $user_id

        ));

    }


    /**
     *

     */

    function change_status($new_status, $old_status, $post)
    {


        if ($new_status != $old_status) {


            $authorid = $post->post_author;

            $user = get_userdata($authorid);

            $user_email = $user->user_email;


            switch ($new_status) {

                case 'publish':


                    // publish post mail

                    $subject = sprintf(__("Your post '%s' has been approved.", 'aecore-class-ae-mailing-backend'), get_the_title($post->ID));

                    $message = ae_get_option('publish_mail_template_'.ICL_LANGUAGE_CODE);


                    //send mail

                    $this->wp_mail($user_email, $subject, $message, array(

                        'user_id' => $authorid,

                        'post' => $post->ID

                    ), '');

                    break;


                case 'archive':


                    // archive post mail


                    $subject = sprintf(__('Your post "%s" has been archived', 'aecore-class-ae-mailing-backend'), get_the_title($post->ID));

                    $message = ae_get_option('archive_mail_template_'.ICL_LANGUAGE_CODE);


                    // send mail

                    $this->wp_mail($user_email, $subject, $message, array(

                        'user_id' => $authorid,

                        'post' => $post->ID

                    ), '');


                    break;


                default:


                    //code

                    break;

            }

        }

        return $new_status;

    }


    /**
     * send reject mail function
     * @author Tam
     * @version 1.0
     */

    function reject_post($data)
    {


        // get post author

        $user = get_user_by('id', $data['post_author']);

        $user_email = $user->user_email;


        // mail title

        $subject = sprintf(__("Your post '%s' has been rejected.", 'aecore-class-ae-mailing-backend'), get_the_title($data['ID']));


        // get reject mail template

        $message = ae_get_option('reject_mail_template_'.ICL_LANGUAGE_CODE);


        // filter reject message

        $message = str_replace('[reject_message]', $data['reject_message'], $message);


        // send reject mail

        $this->wp_mail($user_email, $subject, $message, array(

            'user_id' => $data['post_author'],

            'post' => $data['ID']

        ), '');

    }


    /**
     * new post alert to admin
     * @param Int $post
     * @since 1.1
     * @author Dakachi
     */

    function new_post_alert($post)
    {

        $mail = ae_get_option('new_post_alert', '') ? ae_get_option('new_post_alert', '') : get_option('admin_email');

        $subject = __("Have a new post on your site.", 'aecore-class-ae-mailing-backend');

        $message = sprintf(__("<p>Hi,</p><p> Have a new post on your site. You can review it here: %s </p>", 'aecore-class-ae-mailing-backend'), get_permalink($post));

        $this->wp_mail($mail, $subject, $message);

    }


    /**
     * send a cash notification mail to customer
     *
     * @param String $message Cash message
     * @param Integer $user_id The user 's id who purchase by cash
     * @param Integer $post_id The post id user pay for
     *
     * @author Dakachi
     * @version 1.1
     */

    public function send_cash_message($message, $user_id, $package, $post_id = '')
    {

        $user = get_userdata($user_id);

        if ($post_id) {

            $subject = sprintf(__("You submit a post by cash on '%s'", 'aecore-class-ae-mailing-backend'), ae_get_option('blogname'));

        } else {

            $subject = sprintf(__("You purchase successfully package '%s' by cash on '%s'", 'aecore-class-ae-mailing-backend'), $package['NAME'], ae_get_option('blogname'));

        }


        $mail_template = ae_get_option('cash_notification_mail_'.ICL_LANGUAGE_CODE);

        $message = str_replace('[cash_message]', $message, $mail_template);

        $this->wp_mail($user->user_email, $subject, $message, array(

            'user_id' => $user_id,

            'post' => $post_id

        ));

    }


    /**
     * send receipt when submit a payment successful payment email
     * @param Int $user_id user purchase id
     * @param Array $order Order data
     */

    public function send_receipt($user_id, $order)
    {


        $subject = __('Thank you for your payment!', 'aecore-class-ae-mailing-backend');


        $user = get_userdata($user_id);


        $content = ae_get_option('ae_receipt_mail_'.ICL_LANGUAGE_CODE);

        $products = $order['products'];


        $product = array_pop($products);

        $ad_id = $product['ID'];


        //$ad             =   get_post($ad_id);

        $ad_url = '<a href="' . get_permalink($ad_id) . '">' . get_the_title($ad_id) . '</a>';


        $content = str_ireplace('[link]', $ad_url, $content);

        $content = str_ireplace('[display_name]', $user->display_name, $content);

        $content = str_ireplace('[payment]', $order['payment'], $content);

        $content = str_ireplace('[invoice_id]', $order['ID'], $content);

        $content = str_ireplace('[date]', date(get_option('date_format'), time()), $content);

        $content = str_ireplace('[total]', $order['total'], $content);

        $content = str_ireplace('[currency]', $order['currency'], $content);


        return $this->wp_mail($user->user_email, $subject, $content, array(

            'user_id' => $user_id,

            'post' => $ad_id

        ));

    }

    public function disput_opened($user_id , $project_info_output)
    {
        $user = get_userdata($user_id);

        $subject = __('Project suspended', 'aecore-class-ae-mailing-backend');

        $content = ae_get_option('ae_disput_mail_'.ICL_LANGUAGE_CODE);

        if ($project_info_output['current_user_id'] == $user_id){
          $this->wp_mail($project_info_output['e_mail'], $subject, $content, array(
                'user_id' => $user_id,
                'post' => $project_info_output['ID']
            ));

        }else{
          $this->wp_mail($user->user_email, $subject, $content, array(
                'user_id' => $user_id,
                'post' => $project_info_output['ID']
            ));

        }
        Fre_Notification::project_suspended($user_id,$project_info_output['ID'],$project_info_output['current_user_id']);
        return true;
    }

    public function disput_opened_for_admin($project_info_output)
    {
//        $user = get_userdata($user_id);
$content = '';
        $subject = __('Disput form', 'aecore-class-ae-mailing-backend');

//        $output = print_r($project_info_output, true);
        $content .= "[main_content_body]";
        $content .= "<ul>";
        foreach ($project_info_output as $key => $item){
            $content .= '<li><b>'.$key.' </b> '.$item.'</li>';
        }
        $content .= "</ul>";

        $content .=  "[/main_content_body]";

//        $content = ae_get_option('ae_disput_mail_'.ICL_LANGUAGE_CODE);


        return $this->wp_mail('andrey02122@gmail.com', $subject, $content, array(

            'user_id' => 1,
//            'post' => $project_info_output['ID']
        ));


//        return $this->wp_mail($user->user_email, $subject, $content);

    }

    /**
     * send mail function
     * @param $to
     * @param $subject
     * @param $content
     * @param array $filter
     *  - post : the post id will be replace by placeholder in $content
     *  - user_id : the user_id will be replace by placeholder in $content
     * @param array $headers mail header
     * @author Dakachi <ledd@youngworld.vn>
     * @since 1.0
     */

    public function wp_mail($to, $subject, $content, $filter = array(), $headers = '')
    {


        if ($headers == '') {


            // $headers = 'MIME-Version: 1.0' . "\r\n";

            // $headers.= 'Content-type: text/html; charset=utf-8' . "\r\n";
            $headers .= "From: " . get_option('blogname') . " < " . get_option('admin_email') . "> \r\n";

        }


        /**
         * site info url, name, admin email

         */

        $content = str_ireplace('[site_url]', get_bloginfo('url'), $content);

        $content = str_ireplace('[blogname]', get_bloginfo('name'), $content);

        $content = str_ireplace('[admin_email]', get_option('admin_email'), $content);


        if (isset($filter['user_id'])) {

            $content = $this->filter_authentication_placeholder($content, $filter['user_id']);

        }

        if (isset($filter['post'])) {


            // filter post placeholder

            $content = $this->filter_post_placeholder($content, $filter['post']);

        }

        $content = html_entity_decode((string)$content, ENT_QUOTES, 'UTF-8');

        $subject = html_entity_decode((string)$subject, ENT_QUOTES, 'UTF-8');


        //$content    = $this->get_mail_header() . $content . $this->get_mail_footer() ;

        add_filter('wp_mail_content_type', array(

            $this,

            'set_html_content_type'

        ));

        $a = wp_mail($to, $subject, $this->get_mail_header() . $content . $this->get_mail_footer(), $headers);

        remove_filter('wp_mail_content_type', array(

            $this,

            'set_html_content_type'

        ));

        return $a;

    }


    function set_html_content_type()
    {

        return 'text/html';

    }


    /**
     * return mail header template

     */

    function get_mail_header()
    {




        $mail_header = apply_filters('ae_get_mail_header', '');

        if ($mail_header != '') return $mail_header;


        $logo_url = get_template_directory_uri() . "/img/logo-de.png";

        $options = AE_Options::get_instance();


        // save this setting to theme options

        $site_logo = $options->site_logo;

        if (!empty($site_logo)) {

            $logo_url = $site_logo['large'][0];

        }

        $logo_url = get_template_directory_uri() . "/img/logo-fre-white.png";

        //$logo_url = apply_filters('ae_mail_logo_url', $logo_url);

        $styles = '<style type="text/css">table.button:active td,table.button:hover td,table.button:visited td,table.large-button:hover td,table.medium-button:hover td,table.small-button:hover td,table.tiny-button:hover td{background:#2795b6!important}a:active,a:hover{color:#2795b6!important}a:visited{color:#2ba6cb!important}h1 a:active,h1 a:visited,h2 a:active,h2 a:visited,h3 a:active,h3 a:visited,h4 a:active,h4 a:visited,h5 a:active,h5 a:visited,h6 a:active,h6 a:visited{color:#3783c4!important}table.button td a:visited,table.button:active td a,table.button:hover td a,table.button:visited td a,table.large-button td a:visited,table.large-button:active td a,table.large-button:hover td a,table.medium-button td a:visited,table.medium-button:active td a,table.medium-button:hover td a,table.small-button td a:visited,table.small-button:active td a,table.small-button:hover td a,table.tiny-button td a:visited,table.tiny-button:active td a,table.tiny-button:hover td a{color:#fff!important}table.secondary:hover td{background:#d0d0d0!important;color:#555}table.secondary td a:visited,table.secondary:active td a,table.secondary:hover td a{color:#555!important}table.success:hover td{background:#457a1a!important}table.alert:hover td{background:#970b0e!important}@media only screen and (max-width:600px){table[class=body] img{width:auto!important;height:auto!important}table[class=body] center{min-width:0!important}table[class=body] .container{width:95%!important}table[class=body] .row{width:100%!important;display:block!important}table[class=body] .wrapper{display:block!important;padding-right:0!important}table[class=body] .column,table[class=body] .columns{table-layout:fixed!important;float:none!important;width:100%!important;padding-right:0!important;padding-left:0!important;display:block!important}table[class=body] .left-text-pad,table[class=body] .text-pad-left{padding-right:10px!important}table[class=body] .wrapper.first .column,table[class=body] .wrapper.first .columns{display:table!important}table[class=body] table.column td,table[class=body] table.columns td{width:100%!important}table[class=body] .column td.one,table[class=body] .columns td.one{width:8.333333%!important}table[class=body] .column td.two,table[class=body] .columns td.two{width:16.666666%!important}table[class=body] .column td.three,table[class=body] .columns td.three{width:25%!important}table[class=body] .column td.four,table[class=body] .columns td.four{width:33.333333%!important}table[class=body] .column td.five,table[class=body] .columns td.five{width:41.666666%!important}table[class=body] .column td.six,table[class=body] .columns td.six{width:50%!important}table[class=body] .column td.seven,table[class=body] .columns td.seven{width:58.333333%!important}table[class=body] .column td.eight,table[class=body] .columns td.eight{width:66.666666%!important}table[class=body] .column td.nine,table[class=body] .columns td.nine{width:75%!important}table[class=body] .column td.ten,table[class=body] .columns td.ten{width:83.333333%!important}table[class=body] .column td.eleven,table[class=body] .columns td.eleven{width:91.666666%!important}table[class=body] .column td.twelve,table[class=body] .columns td.twelve{width:100%!important}table[class=body] td.offset-by-eight,table[class=body] td.offset-by-eleven,table[class=body] td.offset-by-five,table[class=body] td.offset-by-four,table[class=body] td.offset-by-nine,table[class=body] td.offset-by-one,table[class=body] td.offset-by-seven,table[class=body] td.offset-by-six,table[class=body] td.offset-by-ten,table[class=body] td.offset-by-three,table[class=body] td.offset-by-two{padding-left:0!important}table[class=body] .right-text-pad,table[class=body] .text-pad-right{padding-left:10px!important}table[class=body] table.columns td.expander{width:1px!important}table[class=body] .hide-for-small,table[class=body] .show-for-desktop{display:none!important}table[class=body] .hide-for-desktop,table[class=body] .show-for-small{display:inherit!important}}</style>';
        $customize = et_get_customization();

        $mail_header = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width" />
    <title>Perssistant</title>
</head>

<body style="width: 100% !important; min-width: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0; padding: 0;">

'.$styles.'
    <table class="body" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; height: 100%; width: 100%; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;">
        <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
            <td class="center" align="center" valign="top" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: center; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;">
                <center style="width: 100%; min-width: 580px;">
                    <table class="row header" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 100%; position: relative; background: #54b3db; padding: 0px;" bgcolor="#54b3db">
                        <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                            <td class="center" align="center" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: center; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" valign="middle">
                                <center style="width: 100%; min-width: 580px;">
                                    <table class="container" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: inherit; width: 580px; margin: 0 auto; padding: 0;">
                                        <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                            <td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; position: relative; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 10px 0px 0px;" align="left" valign="middle">
                                                <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                                    <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                                        <td class="center" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: center; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="center" valign="middle">
                                                            <center style="width: 100%; min-width: 580px;">
                                                                <img class="center" src="'.$logo_url.'" alt="Perssistant" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; width: auto; max-width: 100%; float: none; clear: both; display: block; margin: 0 auto;" align="none" />
                                                            </center>
                                                        </td>
                                                        <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; visibility: hidden; width: 0px; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="middle"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </center>
                            </td>
                        </tr>
                    </table>
                    <table class="container" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: inherit; width: 580px; margin: 0 auto; padding: 0;">
                        <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                            <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="middle">
                               ';

        return $mail_header;

    }
//
//<td style="padding: 10px 20px 10px 5px">
//
//<span style="text-shadow: 0 0 1px #151515; color: #b0b0b0;">' . get_option('blogdescription') . '</span>
//
//</td>
    /**
     * return mail footer html template

     */

    function get_mail_footer()
    {


        $mail_footer = apply_filters('ae_get_mail_footer', '');

        if ($mail_footer != '') return $mail_footer;


        $info = apply_filters('ae_mail_footer_contact_info', get_option('blogname') . ' <br>

                        ' . get_option('admin_email') . ' <br>');



        $customize = et_get_customization();

        $copyright = apply_filters('get_copyright', ae_get_option('copyright'));


        $mail_footer = '<table class="row footer" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                    <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                        <td class="wrapper" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; position: relative; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 10px 20px 0px 0px;" align="left" valign="middle">
                                            <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                                <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                                    <td class="six sub-columns" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; min-width: 0px; width: 50%; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 10px 10px 0px;" align="left" valign="middle">
                                                        <p style="color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 1.1; font-size: 12px; margin: 0 0 10px; padding: 0;" align="left">
                                                            <br class="hide-for-small" /> © 2016 Perssistant - Plugin Initiative</p>
                                                    </td>
                                                    <td class="six sub-columns last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; min-width: 0px; width: 50%; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="middle">
                                                        <p class="text-right" style="color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; text-align: right; line-height: 1.1; font-size: 12px; margin: 0 0 10px; padding: 0;" align="right">Persisstant
                                                            <br /><a href="mailto:info@persisstant.com" style="color: #737373; text-decoration: none;">info@persisstant.com</a>
                                                        </p>
                                                    </td>
                                                    <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; visibility: hidden; width: 0px; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="middle"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </center>
            </td>
        </tr>
    </table>
</body>

</html>';

        return $mail_footer;

    }


    /**
     * mail filter placeholder function
     * @author Dakachi
     * @since 1.0
     */

    function filter_authentication_placeholder($content, $user_id)
    {

        $user = new WP_User($user_id);


        /**
         * member user login, username

         */

        $content = str_ireplace('[user_login]', $user->user_login, $content);

        $content = str_ireplace('[user_name]', $user->user_login, $content);


        // user nicename plaholder

        $content = str_ireplace('[user_nicename]', ucfirst($user->user_nicename), $content);


        //member email

        $content = str_ireplace('[user_email]', $user->user_email, $content);


        /**
         * member display name

         */

        $content = str_ireplace('[display_name]', ucfirst($user->display_name), $content);

        $user_profile_link = '<a href="'.home_url().'/profile'.'">'.$user->display_name.'</a>';
        $content = str_ireplace('[display_name_with_profile_link]', $user_profile_link, $content);

        $profile_link = '<a href="'.home_url().'/profile#tab_project_details'.'">Dashboard</a>';
        $content = str_ireplace('[dashboard]', $user_profile_link, $content);

        $content = str_ireplace('[member]', ucfirst($user->display_name), $content);

        $avatar_style='border-radius: 50%;
width:70px;
margin-right: 30px;
vertical-align: middle;';
        $avatar = '<img style="'.$avatar_style.'" src="'.get_avatar_url($user_id,70).'">';
        $content = str_ireplace('[avatar]', $avatar, $content);


        $greeting = '
        <table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                    <td class="wrapper last space-up" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; position: relative; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 40px 0px 0px;" align="left" valign="middle">
                                        <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                            <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                                <td class="three sub-columns" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; min-width: 0px; width: 25%; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 10px 10px 0px;" align="left" valign="middle">
                                                    <img src="'.get_avatar_url($user_id,150).'" alt="Name" class="circle" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; width: 70px; max-width: 100%; float: left; clear: both; display: block; height: auto; -webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;" align="left" />
                                                </td>
                                                <td class="nine sub-columns last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; min-width: 0px; width: 75%; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="middle">
                                                    <p style="color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">'.__('Hi','aecore-class-ae-mailing-backend') . ' <a href="'.home_url().'/profile'.'" target="blank" style="color: #3783c4; text-decoration: none;">'.$user->display_name.'</a>'.__(', welcome to Perssistant!','aecore-class-ae-mailing-backend') . '</p>
                                                </td>
                                                <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; visibility: hidden; width: 0px; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="middle"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
        ';


        $content = str_ireplace('[greeting]', $greeting, $content);

        $hello = '
        <table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                    <td class="wrapper last space-up" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; position: relative; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 40px 0px 0px;" align="left" valign="middle">
                                        <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                            <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                                <td class="three sub-columns" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; min-width: 0px; width: 25%; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 10px 10px 0px;" align="left" valign="middle">
                                                    <img src="'.get_avatar_url($user_id,150).'" alt="Name" class="circle" style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; width: 70px; max-width: 100%; float: left; clear: both; display: block; height: auto; -webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;" align="left" />
                                                </td>
                                                <td class="nine sub-columns last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; min-width: 0px; width: 75%; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="middle">
                                                    <p style="color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">'.__('Hello','aecore-class-ae-mailing-backend') . ' <a href="'.home_url().'/profile'.'" target="blank" style="color: #3783c4; text-decoration: none;">'.$user->display_name.'</a></p>
                                                </td>
                                                <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; visibility: hidden; width: 0px; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="middle"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
        ';


        $content = str_ireplace('[hello]', $hello, $content);

        $main_content_body_start= '<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                        <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                            <td class="wrapper last space-up" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; position: relative; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 40px 0px 0px;" align="left" valign="middle">
                                                <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                                    <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                                        <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="middle">
                                                            <p style="color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">';

        $main_content_body_end = '                          </p>
                                                        </td>
                                                        <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; visibility: hidden; width: 0px; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="middle"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                   </table>';
        $content = str_ireplace('[main_content_body]', $main_content_body_start, $content);
        $content = str_ireplace('[/main_content_body]', $main_content_body_end, $content);


        $before_footer_body_start= '<table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                    <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                        <td class="wrapper last" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; position: relative; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 10px 0px 0px;" align="left" valign="middle">
                                            <table class="twelve columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                                <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                                    <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="middle">
                                                        <p style="color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; text-align: left; line-height: 24px; font-size: 14px; margin: 0 0 10px; padding: 0;" align="left">';

        $before_footer_body_end = '                     </p>
                                                    </td>
                                                    <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; visibility: hidden; width: 0px; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="middle"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>';
        $content = str_ireplace('[before_footer_body]', $before_footer_body_start, $content);
        $content = str_ireplace('[/before_footer_body]', $before_footer_body_end, $content);
        /**
         * author posts link

         */

        $author_link = '<a href="' . get_author_posts_url($user_id) . '" >' . __("Author's Posts", 'aecore-class-ae-mailing-backend') . '</a>';

        $content = str_ireplace('[author_link]', $author_link, $content);


        $confirm_link = add_query_arg(array(

            'act' => 'confirm',

            'key' => md5($user->user_email)

        ), home_url());

        $content = str_ireplace('[confirm_link]', $confirm_link, $content);

        $block_button_confirm = '
                                <table class="row" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                    <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                        <td class="wrapper offset-by-four" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; position: relative; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 10px 20px 0px 200px;" align="left" valign="middle">
                                            <table class="four columns" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 180px; margin: 0 auto; padding: 0;">
                                                <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                                    <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0px 0px 10px;" align="left" valign="middle">
                                                        <table class="button" style="border-spacing: 0; border-collapse: collapse; vertical-align: middle; text-align: left; width: 100%; overflow: hidden; padding: 0;">
                                                            <tr style="vertical-align: middle; text-align: left; padding: 0;" align="left">
                                                                <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: center; color: #ffffff; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; display: block; width: auto !important; border-radius: 5px; -webkit-box-shadow: 0 2px 0 #124251; -moz-box-shadow: 0 2px 0 #124251; box-shadow: 0 2px 0 #124251; background: #3783c4; margin: 0 0 2px; padding: 8px 0; border: none;" align="center" bgcolor="#3783c4" valign="middle">
                                                                    <a href="' . $confirm_link . '" target="_blank" style="color: #ffffff; text-decoration: none; font-weight: bold; font-family: Arial, sans-serif; font-size: 14px;">' . __("Click verification link", 'aecore-class-ae-mailing-backend') . '</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td class="expander" style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: middle; text-align: left; visibility: hidden; width: 0px; color: #737373; font-family: \'Arial\', sans-serif; font-weight: normal; line-height: 24px; font-size: 14px; margin: 0; padding: 0;" align="left" valign="middle"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
        ';

        /**
         * confirm link

         */

        $content = str_ireplace('[confirm_button]', $block_button_confirm, $content);


        /**
         * filter mail content et_filter_auth_email
         * @param String $content mail content will be filter
         * @param id $user_id The user id who the email will be sent to
         */

        $content = apply_filters('ae_filter_auth_email', $content, $user_id);


        return $content;

    }


    /**
     * filter mail content with post place holder
     * @author Dakachi
     * @since 1.0
     */

    function filter_post_placeholder($content, $post_id = '')
    {

        if (!$post_id) return $content;

        $post = get_post($post_id);


        if (!$post || is_wp_error($post)) return $content;


        $title = apply_filters('the_title', $post->post_title);


        /**
         * post content

         */

        $content = str_ireplace('[title]', $title, $content);

        $content = str_ireplace('[desc]', apply_filters('the_content', $post->post_content), $content);

        $content = str_ireplace('[excerpt]', apply_filters('the_excerpt', $post->post_excerpt), $content);

        $content = str_ireplace('[author]', get_the_author_meta('display_name', $post->post_author), $content);


        /**
         * post link

         */

        $post_link = '<a href="' . get_permalink($post_id) . '" >' . $title . '</a>';

        $content = str_ireplace('[link]', $post_link, $content);


        /**
         * author posts link

         */

        $author_link = '<a href="' . get_author_posts_url($post->post_author) . '" >' . __("Author's Posts", 'aecore-class-ae-mailing-backend') . '</a>';

        $content = str_ireplace('[author_link]', $author_link, $content);


        /**
         * filter mail content et_filter_ad_email
         * @param String $content mail content will be filter
         * @param id $user_id The post id which the email is related to
         */

        $content = apply_filters('ae_filter_post_email', $content, $post_id);


        return $content;

    }

}

