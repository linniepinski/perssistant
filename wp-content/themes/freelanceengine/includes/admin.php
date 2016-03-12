<?php
define('ADMIN_PATH', TEMPLATEPATH . '/admin');

if (!class_exists('AE_Base')) return;

/**
 * Handle admin features
 * Adding admin menus
 */
class ET_Admin extends AE_Base
{
    function __construct() {
        
        /**
         * admin setup
         */
        $this->add_action('init', 'admin_setup');
        
        /**
         * update first options
         */
        $this->add_action('after_switch_theme', 'update_first_time');
        
        //declare ajax classes
        new AE_CategoryAjax(new AE_Category(array(
            'taxonomy' => 'project_category'
        )));
        new AE_CategoryAjax(new AE_Category(array(
            'taxonomy' => 'project_type'
        )));
        
        $this->add_ajax('ae-reset-option', 'reset_option');
        
        /* User Actions */
        $this->add_action('ae_upload_image', 'ae_upload_image', 10, 2);
        
        /**
         * set default options
         */
        $options = AE_Options::get_instance();
        if (!$options->init) $options->reset($this->get_default_options());
        
        // kick subscriber user
        if (!current_user_can('manage_options') && basename($_SERVER['SCRIPT_FILENAME']) != 'admin-ajax.php') {
            
            // wp_redirect( home_url(  ) );
            // exit;
            
        }
    }
    
    /**
     * update user avatar
     */
    public function ae_upload_image($attach_data, $data) {
        
        if (isset($data["method"]) && $data["method"] == "change_avatar") {
            if (!isset($data['author'])) return;
            
            $ae_users = AE_Users::get_instance();
            
            //update user avatar
            $user = $ae_users->update(array(
                'ID' => $data['author'],
                'et_avatar' => $attach_data['attach_id'],
                'et_avatar_url' => $attach_data['thumbnail'][0]
            ));
        }
        switch ($data) {
            case 'site_logo_black':
            case 'site_logo_white':
                $options = AE_Options::get_instance();
                
                // save this setting to theme options
                $options->$data = $attach_data;
                if ($data == 'site_logo_black') {
                    $options->site_logo = $attach_data;
                }
                $options->save();
                
                break;

            default:
                // code...
                break;
        }
    }
    
    /**
     * ajax function reset option
     */
    function reset_option() {
        
        $option_name = $_REQUEST['option_name'];
        $default_options = $this->get_default_options();
        
        if (isset($default_options[$option_name])) {
            $options = AE_Options::get_instance();
            $options->$option_name = $default_options[$option_name];
            wp_send_json(array(
                'msg' => $default_options[$option_name]
            ));
        }
    }
    
    function admin_custom_css() {
?>
        <style type="text/css">
        .custom-icon {
            margin: 10px;
        }
        .custom-icon input {
            width: 80%;
        }
        </style>
    <?php
    }
    
    /**
     * retrieve site default options
     */
    function get_default_options() {
        
        return array(
            'blogname' => get_option('blogname') ,
            'blogdescription' => get_option('blogdescription') ,
            'copyright' => '<span class="enginethemes"> © 2015 Perssistant - <a href="http://www.plugin.ag/">Plugin Initiative</a> </span>',
            
            'project_demonstration' => array(
                'home_page' => 'The best way to <br/>  find a professional',
                'list_project' => 'Find your project.<br/> Find it out!'
            ) ,
            'profile_demonstration' => array(
                'home_page' => 'Need a job? <br/> Tell us your story',
                'list_profile' => 'Need a job? <br/> Tell us your story'
            ) ,
            
            // default forgot passmail
            'forgotpass_mail_template' => '<p>Hello [display_name],</p><p>You have just sent a request to recover the password associated with your account in [blogname]. If you did not make this request, please ignore this email; otherwise, click the link below to create your new password:</p><p>[activate_url]</p><p>Regards,<br />[blogname]</p>',
            
            // default register mail template
            'register_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Thank you and welcome to [blogname].</p>',
            
            // default confirm mail template
            'password_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li><li>Password: [password]</li></ol><p>Thank you and welcome to [blogname].</p>',
            
            //  default reset pass mail template
            'resetpass_mail_template' => "<p>Hello [display_name],</p><p>You have successfully changed your password. Click this link &nbsp;[site_url] to login to your [blogname]'s account.</p><p>Sincerely,<br />[blogname]</p>",
            
            // default confirm mail template
            'confirm_mail_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Please click the link below to confirm your email address.</p><p>[confirm_link]</p><p>Thank you and welcome to [blogname].</p>',
            'confirm_mail_freelancer_template' => '<p>Hello [display_name],</p><p>You have successfully registered an account with &nbsp;&nbsp;[blogname].&nbsp;Here is your account information:</p><ol><li>Username: [user_login]</li><li>Email: [user_email]</li></ol><p>Please click the link below to confirm your email address.</p><p>[confirm_link]</p><p>Thank you and welcome to [blogname].</p>',

            // default confirmed mail template
            'confirmed_mail_template' => "<p>Hi [display_name],</p><p>Your email address has been successfully confirmed.</p><p>Thank you and welcome to [blogname].</p>",
            
            // default confirmed phone no template
            'confirmed_phone_template' => "<p>Hi [display_name],</p><p>Your phone number has been successfully confirmed.</p><p>Thank you and welcome to [blogname].</p>",
            
            //  default inbox mail template
            'inbox_mail_template' => "<p>Hello [display_name],</p><p>You have just received the following message from user: <a href=\"[sender_link]\">[sender]</a></p>
                                        <p>|--------------------------------------------------------------------------------------------------|</p>
                                        [message]
                                        <p>|--------------------------------------------------------------------------------------------------|</p>
                                        <p>You can answer the user by replying this email.</p><p>Sincerely,<br />[blogname]</p>",
            
            //  default inbox mail template
            'publish_mail_template' => "<p>Hello [display_name],</p>
                                        <p>Your listing: [title] in [blogname] is publish.</p>
                                        <p>You can follow this link: [link] to view your listing offer.</p>
                                        <p>Sincerely,<br />[blogname]</p>",
            
            'archive_mail_template' => "<p>Hello [display_name],</p>
                                        <p>Your listing: [title] in [blogname] has been archived due to expiration or manual administrative action.</p>
                                        <p>If you want to continue displaying this listing in our website, please go to your dashboard at [dashboard] to renew your listing offer.</p>
                                        <p>Sincerely,<br />[blogname]</p>",
            
            'reject_mail_template' => "<p>Hello [display_name],</p>
                                        <p>Your listing: [title] in [blogname] has been rejected due to expiration or manual administrative action.</p>
                                        <p>Please contact the administrators via [admin_email] for more information, or go to your dashboard at [dashboard] to edit your listing offer and post it again.</p>
                                        <p>Sincerely,<br />[blogname]</p>",
            'invite_mail_template' => "<p>Hello [display_name],</p>
                                        <p>You have a invitation  from  [blogname] to joint a project.</p>
                                        <p>You can view these project at link : [link]</p>
                                        <p>Sincerely,<br />[blogname]</p>",
            'bid_mail_template' => "<p>Hello [display_name],</p>
                                    <p>You have a new bid on the project : [title].</p>
                                    <p>Here is the freelancer's message : [message].</p>
                                    <p>You can have more details in : [link]</p>
                                    <p>Sincerely,</p>
                                    <p>[blogname]</p>",
            
            'complete_mail_template' => "<p>Hello [display_name],</p>
                                        <p>You have completed the project  [title].</p>
                                        <p>You can review the project in : [link][review]</p>
                                        <p>Sincerely,<br />[blogname]</p>",
            'bid_accepted_template' => "<p>Hello [display_name],</p>
                                        <p>Your bid on the project [link] has been accepted by [author]</p>
                                        <p>You now can contact the employer for further discussion.</p>
                                        <p>Sincerely,</p>
                                        <p>[blogname]</p>",
            'new_message_mail_template' => "<p>Hello [display_name],</p>
                                            <p>You have a new message on project [title]. Here is the message details:</p>
                                            <p>[message]</p>
                                            <p>You can view all message in [workspace]</p>
                                            <p>Sincerely,<br>[blogname]</p>",
            'cash_notification_mail' => "<p>Dear [display_name],</p>
                                        <p>[cash_message]</p>
                                        <p>Sincerely, <br/>[blogname].</p>",
            'ae_receipt_mail'   => '<p>Dear [display_name],</p>
                                    <p>Thank you for your payment.</p>
                                    <p>
                                        Here are the details of your transaction:<br />
                                        Detail:Submit post [link]<br />
                                    </p>
                                    <p>
                                        <strong> Customer info</strong>:<br /> 
                                        [display_name] <br /> 
                                        Email: [user_email]. <br />
                                    </p>
                                    <p> 
                                        <strong> Invoice</strong> <br />
                                        Invoice No: [invoice_id]  <br />
                                        Date: [date]. <br /> 
                                        Payment: [payment] <br /> 
                                        Total: [total] [currency]<br /> 
                                    </p>
                                    <p>Sincerely,<br />[blogname]</p>'  ,
            'employer_report_mail_template' => '<p>Hello [display_name],</p><p>Project [title] you’ve worked on has a new report.&nbsp;</p><p>You can review the project in : [link]</p><p>Sincerely,</p>',
            'employer_close_mail_template' => '<p>Hello [display_name],</p><p>Project [title] you’ve worked on has been closed by the owner. You should review and send your feedback in 36 hours.&nbsp;</p><p>You can review the project in : [link]</p><p>Sincerely,</p><p>[blogname]</p>',
            'freelancer_report_mail_template' => '<p>Hello [display_name],</p>
                                                <p>Your project has a report from [reporter].</p>
                                                <p>You can review the project in : [link]</p>
                                                <p>Sincerely, </br>
                                                [blogname]</p>',
            'freelancer_quit_mail_template' => '<p>Hello [display_name],</p>
                                                <p>[reporter] has quit your project [title]. You should review and feedback in 36 hours. If you don’t have any feedback, you will lose your fund.</p>
                                                <p>You can review the project in : [link]</p>
                                                <p>Sincerely, </br>
                                                [blogname]</p>',
            'admin_report_mail_template' => '<p>Hello [display_name],</p>
                                            <p>The disputing project [title] has a new report.</p>
                                            <p>You can review the project in : [link]</p>
                                            <p>Sincerely, </br>
                                            [blogname]</p>',
            'fre_refund_mail_template' => '<p>Hello [display_name],</p>
                                            <p>The disputing project [title] has been proceed by admin. </p>
                                            <p>The payment will be transferred back to project’s owner</p>
                                            <p>You can review the project in : [link]</p>
                                            <p>Sincerely, </br>
                                            [blogname]</p>',
            'fre_execute_mail_template' => '<p>Hello [display_name],
                                            <p>The disputing project [title] has been proceed by admin. </p>
                                            <p>The payment will be transfer to the freelancer</p>
                                            <p>You can review the project in : [link]</p>
                                            <p>Sincerely, </br>
                                            [blogname]</p>',
            'init' => 1
        );
    }
    
    function update_first_time() {
        update_option('de_first_time_install', 1);
        update_option('revslider-valid-notice', 'false');
    }
    
    /**
     * update admin setup
     */
    function admin_setup() {
        // disable admin bar for all users except admin
        if (!current_user_can('administrator') && !is_admin()) {
            show_admin_bar(false);
        }
        
        $sections = array();
        
        /**
         * general settings section
         */
        $sections['general-settings'] = array(
            'args' => array(
                'title' => __("General", ET_DOMAIN) ,
                'id' => 'general-settings',
                'icon' => 'y',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Website Title", ET_DOMAIN) ,
                        'id' => 'site-name',
                        'class' => '',
                        'desc' => __("Enter your website title.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'blogname',
                            'type' => 'text',
                            'title' => __("Website Title", ET_DOMAIN) ,
                            'name' => 'blogname',
                            'class' => 'option-item bg-grey-input'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Website Description", ET_DOMAIN) ,
                        'id' => 'site-description',
                        'class' => '',
                        'desc' => __("Enter your website description", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'blogdescription',
                            'type' => 'text',
                            'title' => __("Website Title", ET_DOMAIN) ,
                            'name' => 'blogdescription',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Copyright", ET_DOMAIN) ,
                        'id' => 'site-copyright',
                        'class' => '',
                        'desc' => __("This copyright information will appear in the footer.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'copyright',
                            'type' => 'text',
                            'title' => __("Copyright", ET_DOMAIN) ,
                            'name' => 'copyright',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Google Analytics Script", ET_DOMAIN) ,
                        'id' => 'site-analytics',
                        'class' => '',
                        'desc' => __("Google analytics is a service offered by Google that generates detailed statistics about the visits to a website.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'textarea',
                            'title' => __("Google Analytics Script", ET_DOMAIN) ,
                            'name' => 'google_analytics',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Email Confirmation ", ET_DOMAIN) ,
                        'id' => 'user-confirm',
                        'class' => '',
                        'desc' => __("Enabling this will require users to confirm their email addresses after registration.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'user_confirm',
                            'type' => 'switch',
                            'title' => __("Email Confirmation", ET_DOMAIN) ,
                            'name' => 'user_confirm',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Login in admin panel", ET_DOMAIN) ,
                        'id' => 'login_init',
                        'class' => '',
                        'desc' => __("Prevent directly login to admin page.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'login-init',
                            'type' => 'switch',
                            'label' => __("Enable this option will prevent directly login to admin page.", ET_DOMAIN) ,
                            'name' => 'login_init',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Social Links", ET_DOMAIN) ,
                        'id' => 'Social-Links',
                        'class' => 'Social-Links',
                        'desc' => __("Social links are displayed in the footer and in your blog sidebar..", ET_DOMAIN) ,
                        
                        // 'name' => 'currency'
                        
                    ) ,
                    'fields' => array()
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Twitter URL", ET_DOMAIN) ,
                        'id' => 'site-twitter',
                        'class' => 'payment-gateway',
                        
                        //'desc' => __("Your twitter link .", ET_DOMAIN)
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'site-twitter',
                            'type' => 'text',
                            'title' => __("Twitter URL", ET_DOMAIN) ,
                            'name' => 'site_twitter',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Facebook URL", ET_DOMAIN) ,
                        'id' => 'site-facebook',
                        'class' => 'payment-gateway',
                        
                        //'desc' => __(".", ET_DOMAIN)
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'site-facebook',
                            'type' => 'text',
                            'title' => __("Copyright", ET_DOMAIN) ,
                            'name' => 'site_facebook',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Google Plus URL", ET_DOMAIN) ,
                        'id' => 'site-google',
                        'class' => 'payment-gateway',
                        
                        // 'desc' => __("This copyright information will appear in the footer.", ET_DOMAIN)
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'site-google',
                            'type' => 'text',
                            'title' => __("Google Plus URL", ET_DOMAIN) ,
                            'name' => 'site_google',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                )
            )
        );
        
        /**
         * branding section
         */
        $sections['branding'] = array(
            
            'args' => array(
                'title' => __("Branding", ET_DOMAIN) ,
                'id' => 'branding-settings',
                'icon' => 'b',
                'class' => ''
            ) ,
            
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Site logo ", ET_DOMAIN) ,
                        'id' => 'site-logo-black',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Site Logo", ET_DOMAIN) ,
                            'name' => 'site_logo_black',
                            'class' => '',
                            'size' => array(
                                '143',
                                '29'
                            )
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Site logo in front page", ET_DOMAIN) ,
                        'id' => 'site-logo-while',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Site Logo while", ET_DOMAIN) ,
                            'name' => 'site_logo_white',
                            'class' => '',
                            'size' => array(
                                '143',
                                '29'
                            )
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Mobile logo", ET_DOMAIN) ,
                        'id' => 'mobile-logo',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Mobile Logo", ET_DOMAIN) ,
                            'name' => 'mobile_logo',
                            'class' => '',
                            'size' => array(
                                '150',
                                '50'
                            )
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Mobile Icon", ET_DOMAIN) ,
                        'id' => 'mobile-icon',
                        'class' => '',
                        'name' => '',
                        'desc' => __("This icon will be used as a launcher icon for iPhone and Android smartphones and also as the website favicon. The image dimensions should be 57x57px.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Mobile Icon", ET_DOMAIN) ,
                            'name' => 'mobile_icon',
                            'class' => '',
                            'size' => array(
                                '57',
                                '57'
                            )
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("User default logo & avtar", ET_DOMAIN) ,
                        'id' => 'default-logo',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("User default logo & avtar", ET_DOMAIN) ,
                            'name' => 'default_avatar',
                            'class' => '',
                            'size' => array(
                                '150',
                                '150'
                            )
                        )
                    )
                )
                
                // array(
                //     'args' => array(
                //         'title' => __("Pre Loading Icon", ET_DOMAIN) ,
                //         'id' => 'pre-loading-icon',
                //         'class' => '',
                //         'name' => '',
                //         'desc' => __("Preloading Image. The image dimensions should be 57x57px.", ET_DOMAIN)
                //     ) ,
                
                //     'fields' => array(
                //         array(
                //             'id' => 'opt-ace-editor-js',
                //             'type' => 'image',
                //             'title' => __("Mobile Icon", ET_DOMAIN) ,
                //             'name' => 'pre_loading',
                //             'class' => '',
                //             'size' => array(
                //                 '57',
                //                 '57'
                //             )
                //         )
                //     )
                // )
                
            )
        );
        
        $sections['content'] = array(
            'args' => array(
                'title' => __("Content", ET_DOMAIN) ,
                'id' => 'content-settings',
                'icon' => 'l',
                'class' => ''
            ) ,
             //fre_share_role
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Sharing Role Capabilities", ET_DOMAIN) ,
                        'id' => 'fre-share-role',
                        'class' => 'fre-share-role',
                        'desc' => __("Enabling this will make employer and freelancer have the same capabilities.", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_share_role',
                            'type' => 'switch',
                            'title' => __("Shared Roles", ET_DOMAIN) ,
                            'name' => 'fre_share_role',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Currency", ET_DOMAIN) ,
                        'id' => 'content-payment-currency',
                        'class' => 'content-list-package',
                        'desc' => __("Enter currency code and sign ....", ET_DOMAIN) ,
                        'name' => 'content_currency'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'content-currency-code',
                            'type' => 'text',
                            'title' => __("Code", ET_DOMAIN) ,
                            'name' => 'code',
                            'placeholder' => __("Code", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'content-currency-code',
                            'type' => 'text',
                            'title' => __("Sign", ET_DOMAIN) ,
                            'name' => 'icon',
                            'placeholder' => __("Sign", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-align',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'align',
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Left", ET_DOMAIN) ,
                            'label_2' => __("Right", ET_DOMAIN) ,
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Budget limitation", ET_DOMAIN) ,
                        'id' => 'pending-post',
                        'class' => 'pending-post',
                        'desc' => __("Set up the limitation for the 'Budget' filter in 'Projects' page.", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre-slide-max-budget',
                            'type' => 'text',
                            'title' => __("Slide max budget", ET_DOMAIN) ,
                            'name' => 'fre_slide_max_budget',
                            'placeholder' => __("Slide max budget", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Pending Post", ET_DOMAIN) ,
                        'id' => 'pending-post',
                        'class' => 'pending-post',
                        'desc' => __("Enabling this will make every new project posted pending until you review and approve it manually.", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'use_pending',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'use_pending',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Maximum Number of Categories", ET_DOMAIN) ,
                        'id' => 'max-categories',
                        'class' => 'max-categories',
                        'desc' => __("Set a maximum number of categories a project can assign to", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'max_cat',
                            'type' => 'text',
                            'title' => __("Max Number Of Project Categories", ET_DOMAIN) ,
                            'name' => 'max_cat',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Project Category Order", ET_DOMAIN) ,
                        'id' => 'unit_measurement',
                        'class' => '',
                        'desc' => __("Order list project categories by.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'order-project-category',
                            'type' => 'select',
                            'data' => array(
                                'name' => __("Name", ET_DOMAIN) ,
                                'slug' => __("Slug", ET_DOMAIN) ,
                                'id' => __("ID", ET_DOMAIN) ,
                                'count' => __("Count", ET_DOMAIN)
                            ) ,
                            'title' => __("Project Category Order", ET_DOMAIN) ,
                            'name' => 'project_category_order',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __("Project Category Order", ET_DOMAIN)
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Project Type Order", ET_DOMAIN) ,
                        'id' => 'unit_measurement',
                        'class' => '',
                        'desc' => __("Order list project types by.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'order-project-type',
                            'type' => 'select',
                            'data' => array(
                                'name' => __("Name", ET_DOMAIN) ,
                                'slug' => __("Slug", ET_DOMAIN) ,
                                'id' => __("ID", ET_DOMAIN) ,
                                'count' => __("Count", ET_DOMAIN)
                            ) ,
                            'title' => __("Project Type Order", ET_DOMAIN) ,
                            'name' => 'project_type_order',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __("Project Type Order", ET_DOMAIN)
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Disable Comment", ET_DOMAIN) ,
                        'id' => 'disable-project-comment',
                        'class' => '',
                        'desc' => __("Disable comment on project page.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'disable_project_comment',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'disable_project_comment',
                            
                            // 'label' => __("Code", ET_DOMAIN),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Yes", ET_DOMAIN) ,
                            'label_2' => __("No", ET_DOMAIN) ,
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Invited To Bid", ET_DOMAIN) ,
                        'id' => 'invited-to-bid',
                        'class' => '',
                        'desc' => __("If you enable this option, freelancers have to be invited first before bidding a project.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'invited_to_bid',
                            'type' => 'switch',
                            'title' => __("Invited To Bid", ET_DOMAIN) ,
                            'name' => 'invited_to_bid',
                            
                            // 'label' => __("Code", ET_DOMAIN),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Yes", ET_DOMAIN) ,
                            'label_2' => __("No", ET_DOMAIN) ,
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Select Skill From Predefined List", ET_DOMAIN) ,
                        'id' => 'switch-skill',
                        'class' => '',
                        'desc' => __("Enabling this will force user select skill from the predefined list.", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'switch_skill',
                            'type' => 'switch',
                            'title' => __("Switch Skill", ET_DOMAIN) ,
                            'name' => 'switch_skill',
                            
                            // 'label' => __("Code", ET_DOMAIN),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Yes", ET_DOMAIN) ,
                            'label_2' => __("No", ET_DOMAIN) ,
                        )
                    )
                )
            )
        );

        // $sections['freelancer'] = array(
        //     'args' => array(
        //         'title' => __("Freelancer", ET_DOMAIN) ,
        //         'id' => 'freelancer-settings',
        //         'icon' => 'U',
        //         'class' => ''
        //     ) ,
        //      //fre_share_role
        //     'groups' => array(
        //         array(
        //             'args' => array(
        //                 'title' => __("Pay to Bid", ET_DOMAIN) ,
        //                 'id' => 'pay-to-bid',
        //                 'class' => 'pay-to-bid',
        //                 'desc' => __("Enabling this will require freelancer pay to bid.", ET_DOMAIN) ,
        //             ) ,
        //             'fields' => array(
        //                 array(
        //                     'id' => 'pay_to_bid',
        //                     'type' => 'switch',
        //                     'title' => __("Pay to Bid", ET_DOMAIN) ,
        //                     'name' => 'pay_to_bid',
        //                     'class' => 'option-item bg-grey-input '
        //                 )
        //             )
        //         ),
        //         /**
        //          * package plan list
        //          */
        //         array(
        //             'type' => 'list',
        //             'args' => array(
        //                 'title' => __("Bid Plans", ET_DOMAIN) ,
        //                 'id' => 'list-package',
        //                 'class' => 'list-package',
        //                 'desc' => '',
        //                 'name' => 'bid_plan',
        //                 'custom_field' => 'bid_plan'
        //             ) ,
                    
        //             'fields' => array(
        //                 'form' => '/admin-template/bid-plan-form.php',
        //                 'form_js' => '/admin-template/bid-plan-form-js.php',
        //                 'js_template' => '/admin-template/bid-plan-js-item.php',
        //                 'template' => '/admin-template/bid-plan-item.php'
        //             )
        //         ) ,
        //     )
        // );
        
        /**
         * slug settings
         */
        $sections['url_slug'] = array(
            'args' => array(
                'title' => __("Url slug", ET_DOMAIN) ,
                'id' => 'Url-Slug',
                'icon' => 'i',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Project", ET_DOMAIN) ,
                        'id' => 'project-slug',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Single Project page", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_project_slug',
                            'type' => 'text',
                            'title' => __("Single Project page Slug", ET_DOMAIN) ,
                            'name' => 'fre_project_slug',
                            'placeholder' => __("Single Project page Slug", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'project'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Project Listing", ET_DOMAIN) ,
                        'id' => 'project-archive_slug',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Projects listing page", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_project_archive',
                            'type' => 'text',
                            'title' => __("Projects listing page Slug", ET_DOMAIN) ,
                            'name' => 'fre_project_archive',
                            'placeholder' => __("Projects listing page Slug", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'projects'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Project Category", ET_DOMAIN) ,
                        'id' => 'Project-Category',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Project Category page", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'project_category_slug',
                            'type' => 'text',
                            'title' => __("Project Category page Slug", ET_DOMAIN) ,
                            'name' => 'project_category_slug',
                            'placeholder' => __("Project Category page Slug", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'project_category',
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Project Type", ET_DOMAIN) ,
                        'id' => 'Project-Type',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Project Type page", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'project_type_slug',
                            'type' => 'text',
                            'title' => __("Project Type page Slug", ET_DOMAIN) ,
                            'name' => 'project_type_slug',
                            'placeholder' => __("Project Type page Slug", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'project_type'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Profile", ET_DOMAIN) ,
                        'id' => 'Profile-slug',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your User Profile page", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_profile_slug',
                            'type' => 'text',
                            'title' => __("User Profile page Slug", ET_DOMAIN) ,
                            'name' => 'author_base',
                            'placeholder' => __("User Profile page Slug", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'profile'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Profiles Listing", ET_DOMAIN) ,
                        'id' => 'profiles-archive_slug',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Profiles listing page", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_profile_archive',
                            'type' => 'text',
                            'title' => __(" Profiles listing page Slug", ET_DOMAIN) ,
                            'name' => 'fre_profile_archive',
                            'placeholder' => __("Profiles listing page Slug", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'profiles'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Country", ET_DOMAIN) ,
                        'id' => 'profile-Country',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Country tag page", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'country_slug',
                            'type' => 'text',
                            'title' => __("Country tag page Slug", ET_DOMAIN) ,
                            'name' => 'country_slug',
                            'placeholder' => __("Country tag page Slug", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'country'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Skill", ET_DOMAIN) ,
                        'id' => 'profile-Skill',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Skill tag page", ET_DOMAIN) ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'skill_slug',
                            'type' => 'text',
                            'title' => __("Skill tag page Slug", ET_DOMAIN) ,
                            'name' => 'skill_slug',
                            'placeholder' => __("Skill tag page Slug", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'skill'
                        )
                    )
                )
            )
        );
        
        /**
         * video background settings section
         */
        $sections['header_video'] = array(
            
            'args' => array(
                'title' => __("Header Video", ET_DOMAIN) ,
                'id' => 'header-settings',
                'icon' => 'V',
                'class' => ''
            ) ,
            
            'groups' => array(
                
                array(
                    'args' => array(
                        'title' => __("Video Background Url", ET_DOMAIN) ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'desc' => __("Enter your video background url in page-home.php template (.mp4)", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'header-video',
                            'type' => 'text',
                            'title' => __("header video url", ET_DOMAIN) ,
                            'name' => 'header_video',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your header video url', ET_DOMAIN)
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Video Background Via Youtube ID", ET_DOMAIN) ,
                        'id' => 'header-youtube_id',
                        'class' => '',
                        'desc' => __("Enter youtube ID for background video instead of video url", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'youtube_id-video',
                            'type' => 'text',
                            'title' => __("header video url", ET_DOMAIN) ,
                            'name' => 'header_youtube_id',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter youtube video ID', ET_DOMAIN)
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Video Background Fallback", ET_DOMAIN) ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'desc' => __("Fallback image for video background when browser not support", ET_DOMAIN)
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'header-video',
                            'type' => 'text',
                            'title' => __("header video url", ET_DOMAIN) ,
                            'name' => 'header_video_fallback',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your header video fallback image url', ET_DOMAIN)
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Project Demonstration", ET_DOMAIN) ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'name' => 'project_demonstration'
                        
                        // 'desc' => __("Enter your header slider setting", ET_DOMAIN)
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'header-left-text',
                            'type' => 'text',
                            'title' => __("header left text", ET_DOMAIN) ,
                            'name' => 'home_page',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Project demonstration on header video background which can be view by employer', ET_DOMAIN)
                        ) ,
                        array(
                            'id' => 'header-right-text',
                            'type' => 'text',
                            'title' => __("header right text", ET_DOMAIN) ,
                            'name' => 'list_project',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Project demonstration on header video background which can be view by freelancer', ET_DOMAIN)
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Profile Demonstration", ET_DOMAIN) ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'name' => 'profile_demonstration'
                        
                        // 'desc' => __("Enter your header slider setting", ET_DOMAIN)
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'header-left-text',
                            'type' => 'text',
                            'title' => __("header left text", ET_DOMAIN) ,
                            'name' => 'home_page',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Profile demonstration on header video background which can be view by freelancer', ET_DOMAIN)
                        ) ,
                        array(
                            'id' => 'header-right-text',
                            'type' => 'text',
                            'title' => __("header right text", ET_DOMAIN) ,
                            'name' => 'list_profile',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Profiles demonstration on list profiles page which can be view by employer', ET_DOMAIN)
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Loop Header Video", ET_DOMAIN) ,
                        'id' => 'header-video-loop-option',
                        'class' => '',
                        'desc' => __(" Enabling this will make the video on the header automatically repeated.", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'header-video-loop',
                            'type' => 'switch',
                            'title' => __("Select video loop", ET_DOMAIN) ,
                            'name' => 'header_video_loop',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                )
            )
        );
        
        /**
         * Payment settings
         */
        $sections['payment_settings'] = array(
            'args' => array(
                'title' => __("Payment", ET_DOMAIN) ,
                'id' => 'payment-settings',
                'icon' => '%',
                'class' => ''
            ) ,
            
            'groups' => array(
                
                array(
                    'args' => array(
                        'title' => __("Payment Currency", ET_DOMAIN) ,
                        'id' => 'payment-currency',
                        'class' => 'list-package',
                        'desc' => __("Enter currency code and sign ....", ET_DOMAIN) ,
                        'name' => 'currency'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'currency-code',
                            'type' => 'text',
                            'title' => __("Code", ET_DOMAIN) ,
                            'name' => 'code',
                            'placeholder' => __("Code", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-sign',
                            'type' => 'text',
                            'title' => __("Sign", ET_DOMAIN) ,
                            'name' => 'icon',
                            'placeholder' => __("Sign", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-align',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'align',
                            
                            // 'label' => __("Code", ET_DOMAIN),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Left", ET_DOMAIN) ,
                            'label_2' => __("Right", ET_DOMAIN) ,
                        ) ,
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Number Format", ET_DOMAIN) ,
                        'id' => 'number-format',
                        'class' => 'list-package',
                        'desc' => __("Format a number with grouped thousands", ET_DOMAIN) ,
                        'name' => 'number_format'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'decimal-point',
                            'type' => 'text',
                            'title' => __("Decimal point", ET_DOMAIN) ,
                            'label' => __("Decimal point", ET_DOMAIN) ,
                            'name' => 'dec_point',
                            'placeholder' => __("Decimal point", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'thousand_sep',
                            'type' => 'text',
                            'label' => __("Thousand separator", ET_DOMAIN) ,
                            'title' => __("Thousand separator", ET_DOMAIN) ,
                            'name' => 'thousand_sep',
                            'placeholder' => __("Thousand separator", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'et_decimal',
                            'type' => 'text',
                            'label' => __("Number of decimal points", ET_DOMAIN) ,
                            'title' => __("Number of decimal points", ET_DOMAIN) ,
                            'name' => 'et_decimal',
                            'placeholder' => __("Sets the number of decimal points.", ET_DOMAIN) ,
                            'class' => 'option-item bg-grey-input positive_int',
                            'default' => 2
                        ),
                    )
                ),
                
                array(
                    'args' => array(
                        'title' => __("Free to submit listing", ET_DOMAIN) ,
                        'id' => 'free-to-submit-listing',
                        'class' => 'free-to-submit-listing',
                        'desc' => __("Enabling this will allow users to submit listing free.", ET_DOMAIN) ,
                        
                        // 'name' => 'currency'
                        
                        
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'disable-plan',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'disable_plan',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                
                /* payment test mode settings */
                array(
                    'args' => array(
                        'title' => __("Payment Test Mode", ET_DOMAIN) ,
                        'id' => 'payment-test-mode',
                        'class' => 'payment-test-mode',
                        'desc' => __("Enabling this will allow you to test payment without charging your account.", ET_DOMAIN) ,
                        
                        // 'name' => 'currency'
                        
                        
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'test-mode',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'test_mode',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                 // payment test mode
                
                /* payment gateways settings */
                array(
                    'args' => array(
                        'title' => __("Payment Gateways", ET_DOMAIN) ,
                        'id' => 'payment-gateways',
                        'class' => 'payment-gateways',
                        'desc' => __("Set payment plans your users can choose when posting new project.", ET_DOMAIN) ,
                        
                        // 'name' => 'currency'
                        
                    ) ,
                    'fields' => array()
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Paypal", ET_DOMAIN) ,
                        'id' => 'Paypal',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your users to pay through PayPal", ET_DOMAIN) ,
                        
                        'name' => 'paypal'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'paypal',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'paypal_mode',
                            'type' => 'text',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'api_username',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your PayPal email address', ET_DOMAIN)
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("2Checkout", ET_DOMAIN) ,
                        'id' => '2Checkout',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your users to pay through 2Checkout", ET_DOMAIN) ,
                        
                        'name' => '2checkout'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => '2Checkout_mode',
                            'type' => 'switch',
                            'title' => __("2Checkout mode", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'sid',
                            'type' => 'text',
                            'title' => __("Sid", ET_DOMAIN) ,
                            'name' => 'sid',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Your 2Checkout Seller ID', ET_DOMAIN)
                        ) ,
                        array(
                            'id' => 'secret_key',
                            'type' => 'text',
                            'title' => __("Secret Key", ET_DOMAIN) ,
                            'name' => 'secret_key',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Your 2Checkout Secret Key', ET_DOMAIN)
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Cash", ET_DOMAIN) ,
                        'id' => 'Cash',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your user to send cash to your bank account.", ET_DOMAIN) ,
                        
                        'name' => 'cash'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'cash_message_enable',
                            'type' => 'switch',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'cash_message',
                            'type' => 'editor',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'cash_message',
                            'class' => 'option-item bg-grey-input ',
                            
                            // 'placeholder' => __('Enter your PayPal email address', ET_DOMAIN)
                            
                        )
                    )
                ) ,
                /**
                 * package plan list
                 */
                array(
                    'type' => 'list',
                    'args' => array(
                        'title' => __("Payment Plans", ET_DOMAIN) ,
                        'id' => 'list-package',
                        'class' => 'list-package',
                        'desc' => '',
                        'name' => 'pack',
                    ) ,
                    
                    'fields' => array(
                        'form' => '/admin-template/package-form.php',
                        'form_js' => '/admin-template/package-form-js.php',
                        'js_template' => '/admin-template/package-js-item.php',
                        'template' => '/admin-template/package-item.php'
                    )
                ) ,
                
                // limit_free_plan
                array(
                    'args' => array(
                        'title' => __("Limit Free Plan Use", ET_DOMAIN) ,
                        'id' => 'limit_free_plan',
                        'class' => 'limit_free_plan',
                        'desc' => __("Enter the maximum number allowed for employers to use your Free plan", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'cash_message_enable',
                            'type' => 'text',
                            'title' => __("Align", ET_DOMAIN) ,
                            'name' => 'limit_free_plan',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
            )
        );
        
        /**
         * mail template settings section
         */
        $sections['mailing'] = array(
            'args' => array(
                'title' => __("Mailing", ET_DOMAIN) ,
                'id' => 'mail-settings',
                'icon' => 'M',
                'class' => ''
            ) ,
            
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Authentication Mail Template", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates for authentication process. You can use placeholders to include some specific content.", ET_DOMAIN) . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help payment-setting">
                                                    [user_login],[display_name],[user_email] : ' . __("user's details you want to send mail", ET_DOMAIN) . '<br />
                                                    [dashboard] : ' . __("member dashboard url ", ET_DOMAIN) . '<br />
                                                    [title], [link], [excerpt],[desc], [author] : ' . __("project title, link, details, author", ET_DOMAIN) . ' <br />
                                                    [activate_url] : ' . __("activate link is require for user to renew their pass", ET_DOMAIN) . ' <br />
                                                    [site_url],[blogname],[admin_email] : ' . __(" site info, admin email", ET_DOMAIN) . '
                                                    [project_list] : ' . __("list projects employer send to freelancer when invite him to join", ET_DOMAIN) . '

                                                </div>',
                            
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,

                array(
                    'args' => array(
                        'title' => __("Register Mail Template", ET_DOMAIN) ,
                        'id' => 'register-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when he register successfull.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'register_mail_template',
                            'type' => 'editor',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'register_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Register Mail Template", ET_DOMAIN) ,
                        'id' => 'register-mail-freelancer',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user-freelancers when he register successfull.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'register_mail_template_freelancer',
                            'type' => 'editor',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'register_mail_freelancer_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Confirm Mail Template", ET_DOMAIN) ,
                        'id' => 'confirm-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users after he registered successfull when option confirm email is on.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirm_mail_template',
                            'type' => 'editor',
                            'title' => __("Confirme Mail", ET_DOMAIN) ,
                            'name' => 'confirm_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Confirm Mail Freelancer Template", ET_DOMAIN) ,
                        'id' => 'confirm-freelancer-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users after he registered successfull when option confirm email is on.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirm_mail_freelancer_template',
                            'type' => 'editor',
                            'title' => __("Confirmed Mail Freelancer", ET_DOMAIN) ,
                            'name' => 'confirm_mail_freelancer_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Confirmed Mail Template", ET_DOMAIN) ,
                        'id' => 'confirmed-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users to notify that he was confirm email successfull.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirmed_mail_template',
                            'type' => 'editor',
                            'title' => __("Confirmed Mail", ET_DOMAIN) ,
                            'name' => 'confirmed_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Confirmed Phone No Template", ET_DOMAIN) ,
                        'id' => 'confirmed-phone',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users to notify that he was confirm phone no successfull.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirmed_phone_template',
                            'type' => 'editor',
                            'title' => __("Confirmed Phone", ET_DOMAIN) ,
                            'name' => 'confirmed_phone_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Forgotpass Mail Template", ET_DOMAIN) ,
                        'id' => 'forgotpass-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when he request resetpass.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'editor',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'forgotpass_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Resetpass Mail Template", ET_DOMAIN) ,
                        'id' => 'resetpass-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user to notify him has resetpass successfully.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'resetpass_mail_template',
                            'type' => 'editor',
                            'title' => __("Resetpassword Mail", ET_DOMAIN) ,
                            'name' => 'resetpass_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Project Related Mail Template", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates used for project-related event. You can use placeholders to include some specific content", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("New Message Mail Template", ET_DOMAIN) ,
                        'id' => 'new-message-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when he have a new message on workspace.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'new_message_mail_template',
                            'type' => 'editor',
                            'title' => __("Inbox Mail", ET_DOMAIN) ,
                            'name' => 'new_message_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Inbox Mail Template", ET_DOMAIN) ,
                        'id' => 'inbox-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when someone contact him.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'inbox_mail_template',
                            'type' => 'editor',
                            'title' => __("Inbox Mail", ET_DOMAIN) ,
                            'name' => 'inbox_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Invite Mail Template", ET_DOMAIN) ,
                        'id' => 'invite-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when someone invite him join a project", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'invite_mail_template',
                            'type' => 'editor',
                            'title' => __("Invite Mail", ET_DOMAIN) ,
                            'name' => 'invite_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Cash Notification Mail Template", ET_DOMAIN) ,
                        'id' => 'cash-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user cash message when they pay by cash", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'cash_notification_mail',
                            'type' => 'editor',
                            'title' => __("Cash Notification Mail", ET_DOMAIN) ,
                            'name' => 'cash_notification_mail',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Receipt Mail Template", ET_DOMAIN) ,
                        'id' => 'ae-receipt_mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'toggle' => true, 
                        'desc' => __("Send to users after they finish a payment", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'ae_receipt_mail',
                            'type' => 'editor',
                            'title' => __("Receipt Mail Template", ET_DOMAIN) ,
                            'name' => 'ae_receipt_mail',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                
                array(
                    'args' => array(
                        'title' => __("Publish Mail Template", ET_DOMAIN) ,
                        'id' => 'publish-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Sent to users to notify that one of their listing has been published.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'publish_mail_template',
                            'type' => 'editor',
                            'title' => __("publish Mail", ET_DOMAIN) ,
                            'name' => 'publish_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Archive Mail Template", ET_DOMAIN) ,
                        'id' => 'archive-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Sent to users to notify that one of their listing has been archived due to expiration or manual administrative action.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'archive_mail_template',
                            'type' => 'editor',
                            'title' => __("archive Mail", ET_DOMAIN) ,
                            'name' => 'archive_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Reject Mail Template", ET_DOMAIN) ,
                        'id' => 'reject-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Sent to users to notify that one of their listing has been rejected.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'reject_mail_template',
                            'type' => 'editor',
                            'title' => __("reject Mail", ET_DOMAIN) ,
                            'name' => 'reject_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("New Bid Mail Template", ET_DOMAIN) ,
                        'id' => 'bid-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Sent to users when a candidate bid their projects.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'bid_mail_template',
                            'type' => 'editor',
                            'title' => __("Bid Mail", ET_DOMAIN) ,
                            'name' => 'bid_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Bid Accepted Mail Template", ET_DOMAIN) ,
                        'id' => 'bid_accepted_-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to freelancer when his bid was accepted.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'bid_accepted_template',
                            'type' => 'editor',
                            'title' => __("Bid Accepted Mail", ET_DOMAIN) ,
                            'name' => 'bid_accepted_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Complete Project Mail Template", ET_DOMAIN) ,
                        'id' => 'complete-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when project he worked on was marked complete.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'complete_mail_template',
                            'type' => 'editor',
                            'title' => __("Complete Mail", ET_DOMAIN) ,
                            'name' => 'complete_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Project Report Mail Template", ET_DOMAIN) ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", ET_DOMAIN) ,
                            'text' => __("Email templates used for project-report event. You can use placeholders to include some specific content", ET_DOMAIN) ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Project was Reported by Employer", ET_DOMAIN) ,
                        'id' => 'employer-report-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to freelancer when employer sends a report on the project.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'employer_report_mail_template',
                            'type' => 'editor',
                            'title' => __("Employer Report  Mail", ET_DOMAIN) ,
                            'name' => 'employer_report_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Employer closed the project", ET_DOMAIN) ,
                        'id' => 'employer-close-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to freelancer when employer close project.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'employer_close_mail_template',
                            'type' => 'editor',
                            'title' => __("Employer Report  Mail", ET_DOMAIN) ,
                            'name' => 'employer_close_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Project Reported by Freelancer", ET_DOMAIN) ,
                        'id' => 'freelancer-report-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to employer when freelancer send report on project.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'freelancer_report_mail_template',
                            'type' => 'editor',
                            'title' => __("Freelancer Report  Mail", ET_DOMAIN) ,
                            'name' => 'freelancer_report_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Freelancer Quit The Project", ET_DOMAIN) ,
                        'id' => 'freelancer-quit-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to the employer when the freelancer quits the project", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'freelancer_quit_mail_template',
                            'type' => 'editor',
                            'title' => __("Freelancer Quit  Mail", ET_DOMAIN) ,
                            'name' => 'freelancer_quit_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                //admin_report_mail_template
                array(
                    'args' => array(
                        'title' => __("New Report was sent to Admin", ET_DOMAIN) ,
                        'id' => 'admin-new-report-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to admin when user sends a report.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'admin_report_mail_template',
                            'type' => 'editor',
                            'title' => __("Admin New Report Mail", ET_DOMAIN) ,
                            'name' => 'admin_report_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Admin Refunded The Payment", ET_DOMAIN) ,
                        'id' => 'admin-refund-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when admin refunds the escrow payment to the project's owner.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_refund_mail_template',
                            'type' => 'editor',
                            'title' => __("Admin Refund Payment", ET_DOMAIN) ,
                            'name' => 'fre_refund_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Admin Executed The Payment", ET_DOMAIN) ,
                        'id' => 'admin-execute-payment-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when admin executes the escrow payment and send to the freelancer.", ET_DOMAIN),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_execute_mail_template',
                            'type' => 'editor',
                            'title' => __("Admin Execute Payment Mail", ET_DOMAIN) ,
                            'name' => 'fre_execute_mail_template',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                )

            )
        );
        
        /**
         * language settings
         */
        $sections['language'] = array(
            'args' => array(
                'title' => __("Language", ET_DOMAIN) ,
                'id' => 'language-settings',
                'icon' => 'G',
                'class' => ''
            ) ,
            
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Website Language", ET_DOMAIN) ,
                        'id' => 'website-language',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Select the language you want to use for your website.", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'language_list',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'website_language',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Translator", ET_DOMAIN) ,
                        'id' => 'translator',
                        'class' => '',
                        'name' => 'translator',
                        'desc' => __("Translate a language", ET_DOMAIN)
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'translator-field',
                            'type' => 'translator',
                            'title' => __("Register Mail", ET_DOMAIN) ,
                            'name' => 'translate',
                            'class' => ''
                        )
                    )
                )
            )
        );
        
        /**
         * license key settings
         */
        $sections['update'] = array(
            'args' => array(
                'title' => __("Update", ET_DOMAIN) ,
                'id' => 'update-settings',
                'icon' => '~',
                'class' => ''
            ) ,
            
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("License Key", ET_DOMAIN) ,
                        'id' => 'license-key',
                        'class' => '',
                        'desc' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'et_license_key',
                            'type' => 'text',
                            'title' => __("License Key", ET_DOMAIN) ,
                            'name' => 'et_license_key',
                            'class' => ''
                        )
                    )
                )
            )
        );
        
        $temp = array();
        $options = AE_Options::get_instance();
        foreach ($sections as $key => $section) {
            $temp[] = new AE_section($section['args'], $section['groups'], $options);
        }
        
        $pages = array();
        
        /**
         * overview container
         */
        $container = new AE_Overview(array(
            PROFILE,
            PROJECT
        ) , true);
        
        //$statics      =   array();
        // $header      =   new AE_Head( array( 'page_title'    => __('Overview', ET_DOMAIN),
        //                                  'menu_title'    => __('OVERVIEW', ET_DOMAIN),
        //                                  'desc'          => __("Overview", ET_DOMAIN) ) );
        $pages['overview'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Overview', ET_DOMAIN) ,
                'menu_title' => __('OVERVIEW', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-overview',
                'icon' => 'L',
                'desc' => sprintf(__("%s overview", ET_DOMAIN) , $options->blogname)
            ) ,
            'container' => $container,
            
            // 'header' => $header
            
            
        );
        
        /**
         * setting view
         */
        $container = new AE_Container(array(
            'class' => '',
            'id' => 'settings'
        ) , $temp, '');
        $pages['settings'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Settings', ET_DOMAIN) ,
                'menu_title' => __('SETTINGS', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-settings',
                'icon' => 'y',
                'desc' => __("Manage how your FreelanceEngine looks and feels", ET_DOMAIN)
            ) ,
            'container' => $container
        );
        
        /**
         * user list view
         */
        
        $container = new AE_UsersContainer(array(
            'filter' => array(
                'moderate'
            )
        ));
        $pages['members'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Members', ET_DOMAIN) ,
                'menu_title' => __('MEMBERS', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-users',
                'icon' => 'g',
                'desc' => __("Overview of registered members", ET_DOMAIN)
            ) ,
            'container' => $container
        );
        
        /**
         * order list view
         */
        $orderlist = new AE_OrderList(array());
        $pages['payments'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Payments', ET_DOMAIN) ,
                'menu_title' => __('PAYMENTS', ET_DOMAIN) ,
                'cap' => 'administrator',
                'slug' => 'et-payments',
                'icon' => '%',
                'desc' => __("Overview of all payments", ET_DOMAIN)
            ) ,
            'container' => $orderlist
        );
        
        /**
         * setup wizard view
         */
        
        // $container = new AE_Wizard();
        // $pages[] = array(
        //     'args' => array(
        //         'parent_slug' => 'et-overview',
        //         'page_title'  => __('Setup Wizard', ET_DOMAIN) ,
        //         'menu_title'  => __('Setup Wizard', ET_DOMAIN) ,
        //         'cap'         => 'administrator',
        //         'slug'        => 'et-wizard',
        //         'icon'        => 'help',
        //         'desc'        => __("Set up and manage every content of your site", ET_DOMAIN)
        //     ) ,
        //     'container' => $container
        // );
        
        
        /**
         *  filter pages config params so user can hook to here
         */
        $pages = apply_filters('ae_admin_menu_pages', $pages);
        
        /**
         * add menu page
         */
        $this->admin_menu = new AE_Menu($pages);
        
        /**
         * add sub menu page
         */
        foreach ($pages as $key => $page) {
            new AE_Submenu($page, $pages);
        }
    }
}

