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
                'title' => __("General", 'admin-backend') ,
                'id' => 'general-settings',
                'icon' => 'y',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Website Title", 'admin-backend') ,
                        'id' => 'site-name',
                        'class' => '',
                        'desc' => __("Enter your website title.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'blogname',
                            'type' => 'text',
                            'title' => __("Website Title", 'admin-backend') ,
                            'name' => 'blogname',
                            'class' => 'option-item bg-grey-input'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Website Description", 'admin-backend') ,
                        'id' => 'site-description',
                        'class' => '',
                        'desc' => __("Enter your website description", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'blogdescription',
                            'type' => 'text',
                            'title' => __("Website Title", 'admin-backend') ,
                            'name' => 'blogdescription',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Copyright", 'admin-backend') ,
                        'id' => 'site-copyright',
                        'class' => '',
                        'desc' => __("This copyright information will appear in the footer.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'copyright',
                            'type' => 'text',
                            'title' => __("Copyright", 'admin-backend') ,
                            'name' => 'copyright',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Google Analytics Script", 'admin-backend') ,
                        'id' => 'site-analytics',
                        'class' => '',
                        'desc' => __("Google analytics is a service offered by Google that generates detailed statistics about the visits to a website.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'textarea',
                            'title' => __("Google Analytics Script", 'admin-backend') ,
                            'name' => 'google_analytics',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Email Confirmation ", 'admin-backend') ,
                        'id' => 'user-confirm',
                        'class' => '',
                        'desc' => __("Enabling this will require users to confirm their email addresses after registration.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'user_confirm',
                            'type' => 'switch',
                            'title' => __("Email Confirmation", 'admin-backend') ,
                            'name' => 'user_confirm',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Login in admin panel", 'admin-backend') ,
                        'id' => 'login_init',
                        'class' => '',
                        'desc' => __("Prevent directly login to admin page.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'login-init',
                            'type' => 'switch',
                            'label' => __("Enable this option will prevent directly login to admin page.", 'admin-backend') ,
                            'name' => 'login_init',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Social Links", 'admin-backend') ,
                        'id' => 'Social-Links',
                        'class' => 'Social-Links',
                        'desc' => __("Social links are displayed in the footer and in your blog sidebar..", 'admin-backend') ,
                        
                        // 'name' => 'currency'
                        
                    ) ,
                    'fields' => array()
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Twitter URL", 'admin-backend') ,
                        'id' => 'site-twitter',
                        'class' => 'payment-gateway',
                        
                        //'desc' => __("Your twitter link .", 'admin-backend')
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'site-twitter',
                            'type' => 'text',
                            'title' => __("Twitter URL", 'admin-backend') ,
                            'name' => 'site_twitter',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Facebook URL", 'admin-backend') ,
                        'id' => 'site-facebook',
                        'class' => 'payment-gateway',
                        
                        //'desc' => __(".", 'admin-backend')
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'site-facebook',
                            'type' => 'text',
                            'title' => __("Copyright", 'admin-backend') ,
                            'name' => 'site_facebook',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Google Plus URL", 'admin-backend') ,
                        'id' => 'site-google',
                        'class' => 'payment-gateway',
                        
                        // 'desc' => __("This copyright information will appear in the footer.", 'admin-backend')
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'site-google',
                            'type' => 'text',
                            'title' => __("Google Plus URL", 'admin-backend') ,
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
                'title' => __("Branding", 'admin-backend') ,
                'id' => 'branding-settings',
                'icon' => 'b',
                'class' => ''
            ) ,
            
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Site logo ", 'admin-backend') ,
                        'id' => 'site-logo-black',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Site Logo", 'admin-backend') ,
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
                        'title' => __("Site logo in front page", 'admin-backend') ,
                        'id' => 'site-logo-while',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Site Logo while", 'admin-backend') ,
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
                        'title' => __("Mobile logo", 'admin-backend') ,
                        'id' => 'mobile-logo',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Mobile Logo", 'admin-backend') ,
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
                        'title' => __("Mobile Icon", 'admin-backend') ,
                        'id' => 'mobile-icon',
                        'class' => '',
                        'name' => '',
                        'desc' => __("This icon will be used as a launcher icon for iPhone and Android smartphones and also as the website favicon. The image dimensions should be 57x57px.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("Mobile Icon", 'admin-backend') ,
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
                        'title' => __("User default logo & avtar", 'admin-backend') ,
                        'id' => 'default-logo',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Your logo should be in PNG, GIF or JPG format, within 150x50px and less than 1500Kb.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'opt-ace-editor-js',
                            'type' => 'image',
                            'title' => __("User default logo & avtar", 'admin-backend') ,
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
                //         'title' => __("Pre Loading Icon", 'admin-backend') ,
                //         'id' => 'pre-loading-icon',
                //         'class' => '',
                //         'name' => '',
                //         'desc' => __("Preloading Image. The image dimensions should be 57x57px.", 'admin-backend')
                //     ) ,
                
                //     'fields' => array(
                //         array(
                //             'id' => 'opt-ace-editor-js',
                //             'type' => 'image',
                //             'title' => __("Mobile Icon", 'admin-backend') ,
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
                'title' => __("Content", 'admin-backend') ,
                'id' => 'content-settings',
                'icon' => 'l',
                'class' => ''
            ) ,
             //fre_share_role
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Sharing Role Capabilities", 'admin-backend') ,
                        'id' => 'fre-share-role',
                        'class' => 'fre-share-role',
                        'desc' => __("Enabling this will make employer and freelancer have the same capabilities.", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_share_role',
                            'type' => 'switch',
                            'title' => __("Shared Roles", 'admin-backend') ,
                            'name' => 'fre_share_role',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Currency", 'admin-backend') ,
                        'id' => 'content-payment-currency',
                        'class' => 'content-list-package',
                        'desc' => __("Enter currency code and sign ....", 'admin-backend') ,
                        'name' => 'content_currency'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'content-currency-code',
                            'type' => 'text',
                            'title' => __("Code", 'admin-backend') ,
                            'name' => 'code',
                            'placeholder' => __("Code", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'content-currency-code',
                            'type' => 'text',
                            'title' => __("Sign", 'admin-backend') ,
                            'name' => 'icon',
                            'placeholder' => __("Sign", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-align',
                            'type' => 'switch',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'align',
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Left", 'admin-backend') ,
                            'label_2' => __("Right", 'admin-backend') ,
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Budget limitation", 'admin-backend') ,
                        'id' => 'pending-post',
                        'class' => 'pending-post',
                        'desc' => __("Set up the limitation for the 'Budget' filter in 'Projects' page.", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre-slide-max-budget',
                            'type' => 'text',
                            'title' => __("Slide max budget", 'admin-backend') ,
                            'name' => 'fre_slide_max_budget',
                            'placeholder' => __("Slide max budget", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Pending Post", 'admin-backend') ,
                        'id' => 'pending-post',
                        'class' => 'pending-post',
                        'desc' => __("Enabling this will make every new project posted pending until you review and approve it manually.", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'use_pending',
                            'type' => 'switch',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'use_pending',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Maximum Number of Categories", 'admin-backend') ,
                        'id' => 'max-categories',
                        'class' => 'max-categories',
                        'desc' => __("Set a maximum number of categories a project can assign to", 'admin-backend')
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'max_cat',
                            'type' => 'text',
                            'title' => __("Max Number Of Project Categories", 'admin-backend') ,
                            'name' => 'max_cat',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Project Category Order", 'admin-backend') ,
                        'id' => 'unit_measurement',
                        'class' => '',
                        'desc' => __("Order list project categories by.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'order-project-category',
                            'type' => 'select',
                            'data' => array(
                                'name' => __("Name", 'admin-backend') ,
                                'slug' => __("Slug", 'admin-backend') ,
                                'id' => __("ID", 'admin-backend') ,
                                'count' => __("Count", 'admin-backend')
                            ) ,
                            'title' => __("Project Category Order", 'admin-backend') ,
                            'name' => 'project_category_order',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __("Project Category Order", 'admin-backend')
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Project Type Order", 'admin-backend') ,
                        'id' => 'unit_measurement',
                        'class' => '',
                        'desc' => __("Order list project types by.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'order-project-type',
                            'type' => 'select',
                            'data' => array(
                                'name' => __("Name", 'admin-backend') ,
                                'slug' => __("Slug", 'admin-backend') ,
                                'id' => __("ID", 'admin-backend') ,
                                'count' => __("Count", 'admin-backend')
                            ) ,
                            'title' => __("Project Type Order", 'admin-backend') ,
                            'name' => 'project_type_order',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __("Project Type Order", 'admin-backend')
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Disable Comment", 'admin-backend') ,
                        'id' => 'disable-project-comment',
                        'class' => '',
                        'desc' => __("Disable comment on project page.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'disable_project_comment',
                            'type' => 'switch',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'disable_project_comment',
                            
                            // 'label' => __("Code", 'admin-backend'),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Yes", 'admin-backend') ,
                            'label_2' => __("No", 'admin-backend') ,
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Invited To Bid", 'admin-backend') ,
                        'id' => 'invited-to-bid',
                        'class' => '',
                        'desc' => __("If you enable this option, freelancers have to be invited first before bidding a project.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'invited_to_bid',
                            'type' => 'switch',
                            'title' => __("Invited To Bid", 'admin-backend') ,
                            'name' => 'invited_to_bid',
                            
                            // 'label' => __("Code", 'admin-backend'),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Yes", 'admin-backend') ,
                            'label_2' => __("No", 'admin-backend') ,
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Select Skill From Predefined List", 'admin-backend') ,
                        'id' => 'switch-skill',
                        'class' => '',
                        'desc' => __("Enabling this will force user select skill from the predefined list.", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'switch_skill',
                            'type' => 'switch',
                            'title' => __("Switch Skill", 'admin-backend') ,
                            'name' => 'switch_skill',
                            
                            // 'label' => __("Code", 'admin-backend'),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Yes", 'admin-backend') ,
                            'label_2' => __("No", 'admin-backend') ,
                        )
                    )
                )
            )
        );

        // $sections['freelancer'] = array(
        //     'args' => array(
        //         'title' => __("Freelancer", 'admin-backend') ,
        //         'id' => 'freelancer-settings',
        //         'icon' => 'U',
        //         'class' => ''
        //     ) ,
        //      //fre_share_role
        //     'groups' => array(
        //         array(
        //             'args' => array(
        //                 'title' => __("Pay to Bid", 'admin-backend') ,
        //                 'id' => 'pay-to-bid',
        //                 'class' => 'pay-to-bid',
        //                 'desc' => __("Enabling this will require freelancer pay to bid.", 'admin-backend') ,
        //             ) ,
        //             'fields' => array(
        //                 array(
        //                     'id' => 'pay_to_bid',
        //                     'type' => 'switch',
        //                     'title' => __("Pay to Bid", 'admin-backend') ,
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
        //                 'title' => __("Bid Plans", 'admin-backend') ,
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
                'title' => __("Url slug", 'admin-backend') ,
                'id' => 'Url-Slug',
                'icon' => 'i',
                'class' => ''
            ) ,
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Project", 'admin-backend') ,
                        'id' => 'project-slug',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Single Project page", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_project_slug',
                            'type' => 'text',
                            'title' => __("Single Project page Slug", 'admin-backend') ,
                            'name' => 'fre_project_slug',
                            'placeholder' => __("Single Project page Slug", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'project'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Project Listing", 'admin-backend') ,
                        'id' => 'project-archive_slug',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Projects listing page", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_project_archive',
                            'type' => 'text',
                            'title' => __("Projects listing page Slug", 'admin-backend') ,
                            'name' => 'fre_project_archive',
                            'placeholder' => __("Projects listing page Slug", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'projects'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Project Category", 'admin-backend') ,
                        'id' => 'Project-Category',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Project Category page", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'project_category_slug',
                            'type' => 'text',
                            'title' => __("Project Category page Slug", 'admin-backend') ,
                            'name' => 'project_category_slug',
                            'placeholder' => __("Project Category page Slug", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'project_category',
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Project Type", 'admin-backend') ,
                        'id' => 'Project-Type',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Project Type page", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'project_type_slug',
                            'type' => 'text',
                            'title' => __("Project Type page Slug", 'admin-backend') ,
                            'name' => 'project_type_slug',
                            'placeholder' => __("Project Type page Slug", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'project_type'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Profile", 'admin-backend') ,
                        'id' => 'Profile-slug',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your User Profile page", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_profile_slug',
                            'type' => 'text',
                            'title' => __("User Profile page Slug", 'admin-backend') ,
                            'name' => 'author_base',
                            'placeholder' => __("User Profile page Slug", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'profile'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Profiles Listing", 'admin-backend') ,
                        'id' => 'profiles-archive_slug',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Profiles listing page", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_profile_archive',
                            'type' => 'text',
                            'title' => __(" Profiles listing page Slug", 'admin-backend') ,
                            'name' => 'fre_profile_archive',
                            'placeholder' => __("Profiles listing page Slug", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'profiles'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Country", 'admin-backend') ,
                        'id' => 'profile-Country',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Country tag page", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'country_slug',
                            'type' => 'text',
                            'title' => __("Country tag page Slug", 'admin-backend') ,
                            'name' => 'country_slug',
                            'placeholder' => __("Country tag page Slug", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input ',
                            'default' => 'country'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Skill", 'admin-backend') ,
                        'id' => 'profile-Skill',
                        'class' => 'list-package',
                        'desc' => __("Enter slug for your Skill tag page", 'admin-backend') ,
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'skill_slug',
                            'type' => 'text',
                            'title' => __("Skill tag page Slug", 'admin-backend') ,
                            'name' => 'skill_slug',
                            'placeholder' => __("Skill tag page Slug", 'admin-backend') ,
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
                'title' => __("Header Video", 'admin-backend') ,
                'id' => 'header-settings',
                'icon' => 'V',
                'class' => ''
            ) ,
            
            'groups' => array(
                
                array(
                    'args' => array(
                        'title' => __("Video Background Url", 'admin-backend') ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'desc' => __("Enter your video background url in page-home.php template (.mp4)", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'header-video',
                            'type' => 'text',
                            'title' => __("header video url", 'admin-backend') ,
                            'name' => 'header_video',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your header video url', 'admin-backend')
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Video Background Via Youtube ID", 'admin-backend') ,
                        'id' => 'header-youtube_id',
                        'class' => '',
                        'desc' => __("Enter youtube ID for background video instead of video url", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'youtube_id-video',
                            'type' => 'text',
                            'title' => __("header video url", 'admin-backend') ,
                            'name' => 'header_youtube_id',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter youtube video ID', 'admin-backend')
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Video Background Fallback", 'admin-backend') ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'desc' => __("Fallback image for video background when browser not support", 'admin-backend')
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'header-video',
                            'type' => 'text',
                            'title' => __("header video url", 'admin-backend') ,
                            'name' => 'header_video_fallback',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your header video fallback image url', 'admin-backend')
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Project Demonstration", 'admin-backend') ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'name' => 'project_demonstration'
                        
                        // 'desc' => __("Enter your header slider setting", 'admin-backend')
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'header-left-text',
                            'type' => 'text',
                            'title' => __("header left text", 'admin-backend') ,
                            'name' => 'home_page',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Project demonstration on header video background which can be view by employer', 'admin-backend')
                        ) ,
                        array(
                            'id' => 'header-right-text',
                            'type' => 'text',
                            'title' => __("header right text", 'admin-backend') ,
                            'name' => 'list_project',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Project demonstration on header video background which can be view by freelancer', 'admin-backend')
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Profile Demonstration", 'admin-backend') ,
                        'id' => 'header-slider-settings',
                        'class' => '',
                        'name' => 'profile_demonstration'
                        
                        // 'desc' => __("Enter your header slider setting", 'admin-backend')
                        
                    ) ,
                    
                    'fields' => array(
                        array(
                            'id' => 'header-left-text',
                            'type' => 'text',
                            'title' => __("header left text", 'admin-backend') ,
                            'name' => 'home_page',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Profile demonstration on header video background which can be view by freelancer', 'admin-backend')
                        ) ,
                        array(
                            'id' => 'header-right-text',
                            'type' => 'text',
                            'title' => __("header right text", 'admin-backend') ,
                            'name' => 'list_profile',
                            'class' => 'option-item bg-grey-input ',
                            'label' => __('Profiles demonstration on list profiles page which can be view by employer', 'admin-backend')
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Loop Header Video", 'admin-backend') ,
                        'id' => 'header-video-loop-option',
                        'class' => '',
                        'desc' => __(" Enabling this will make the video on the header automatically repeated.", 'admin-backend')
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'header-video-loop',
                            'type' => 'switch',
                            'title' => __("Select video loop", 'admin-backend') ,
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
                'title' => __("Payment", 'admin-backend') ,
                'id' => 'payment-settings',
                'icon' => '%',
                'class' => ''
            ) ,
            
            'groups' => array(
                
                array(
                    'args' => array(
                        'title' => __("Payment Currency", 'admin-backend') ,
                        'id' => 'payment-currency',
                        'class' => 'list-package',
                        'desc' => __("Enter currency code and sign ....", 'admin-backend') ,
                        'name' => 'currency'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'currency-code',
                            'type' => 'text',
                            'title' => __("Code", 'admin-backend') ,
                            'name' => 'code',
                            'placeholder' => __("Code", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-sign',
                            'type' => 'text',
                            'title' => __("Sign", 'admin-backend') ,
                            'name' => 'icon',
                            'placeholder' => __("Sign", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'currency-align',
                            'type' => 'switch',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'align',
                            
                            // 'label' => __("Code", 'admin-backend'),
                            'class' => 'option-item bg-grey-input ',
                            'label_1' => __("Left", 'admin-backend') ,
                            'label_2' => __("Right", 'admin-backend') ,
                        ) ,
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Number Format", 'admin-backend') ,
                        'id' => 'number-format',
                        'class' => 'list-package',
                        'desc' => __("Format a number with grouped thousands", 'admin-backend') ,
                        'name' => 'number_format'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'decimal-point',
                            'type' => 'text',
                            'title' => __("Decimal point", 'admin-backend') ,
                            'label' => __("Decimal point", 'admin-backend') ,
                            'name' => 'dec_point',
                            'placeholder' => __("Decimal point", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'thousand_sep',
                            'type' => 'text',
                            'label' => __("Thousand separator", 'admin-backend') ,
                            'title' => __("Thousand separator", 'admin-backend') ,
                            'name' => 'thousand_sep',
                            'placeholder' => __("Thousand separator", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input '
                        ),
                        array(
                            'id' => 'et_decimal',
                            'type' => 'text',
                            'label' => __("Number of decimal points", 'admin-backend') ,
                            'title' => __("Number of decimal points", 'admin-backend') ,
                            'name' => 'et_decimal',
                            'placeholder' => __("Sets the number of decimal points.", 'admin-backend') ,
                            'class' => 'option-item bg-grey-input positive_int',
                            'default' => 2
                        ),
                    )
                ),
                
                array(
                    'args' => array(
                        'title' => __("Free to submit listing", 'admin-backend') ,
                        'id' => 'free-to-submit-listing',
                        'class' => 'free-to-submit-listing',
                        'desc' => __("Enabling this will allow users to submit listing free.", 'admin-backend') ,
                        
                        // 'name' => 'currency'
                        
                        
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'disable-plan',
                            'type' => 'switch',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'disable_plan',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                
                /* payment test mode settings */
                array(
                    'args' => array(
                        'title' => __("Payment Test Mode", 'admin-backend') ,
                        'id' => 'payment-test-mode',
                        'class' => 'payment-test-mode',
                        'desc' => __("Enabling this will allow you to test payment without charging your account.", 'admin-backend') ,
                        
                        // 'name' => 'currency'
                        
                        
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'test-mode',
                            'type' => 'switch',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'test_mode',
                            'class' => 'option-item bg-grey-input '
                        )
                    )
                ) ,
                 // payment test mode
                
                /* payment gateways settings */
                array(
                    'args' => array(
                        'title' => __("Payment Gateways", 'admin-backend') ,
                        'id' => 'payment-gateways',
                        'class' => 'payment-gateways',
                        'desc' => __("Set payment plans your users can choose when posting new project.", 'admin-backend') ,
                        
                        // 'name' => 'currency'
                        
                    ) ,
                    'fields' => array()
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Paypal", 'admin-backend') ,
                        'id' => 'Paypal',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your users to pay through PayPal", 'admin-backend') ,
                        
                        'name' => 'paypal'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'paypal',
                            'type' => 'switch',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'paypal_mode',
                            'type' => 'text',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'api_username',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Enter your PayPal email address', 'admin-backend')
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("2Checkout", 'admin-backend') ,
                        'id' => '2Checkout',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your users to pay through 2Checkout", 'admin-backend') ,
                        
                        'name' => '2checkout'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => '2Checkout_mode',
                            'type' => 'switch',
                            'title' => __("2Checkout mode", 'admin-backend') ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'sid',
                            'type' => 'text',
                            'title' => __("Sid", 'admin-backend') ,
                            'name' => 'sid',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Your 2Checkout Seller ID', 'admin-backend')
                        ) ,
                        array(
                            'id' => 'secret_key',
                            'type' => 'text',
                            'title' => __("Secret Key", 'admin-backend') ,
                            'name' => 'secret_key',
                            'class' => 'option-item bg-grey-input ',
                            'placeholder' => __('Your 2Checkout Secret Key', 'admin-backend')
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Cash", 'admin-backend') ,
                        'id' => 'Cash',
                        'class' => 'payment-gateway',
                        'desc' => __("Enabling this will allow your user to send cash to your bank account.", 'admin-backend') ,
                        
                        'name' => 'cash'
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'cash_message_enable',
                            'type' => 'switch',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'enable',
                            'class' => 'option-item bg-grey-input '
                        ) ,
                        array(
                            'id' => 'cash_message',
                            'type' => 'editor',
                            'title' => __("Align", 'admin-backend') ,
                            'name' => 'cash_message',
                            'class' => 'option-item bg-grey-input ',
                            
                            // 'placeholder' => __('Enter your PayPal email address', 'admin-backend')
                            
                        )
                    )
                ) ,
                /**
                 * package plan list
                 */
                array(
                    'type' => 'list',
                    'args' => array(
                        'title' => __("Payment Plans", 'admin-backend') ,
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
                        'title' => __("Limit Free Plan Use", 'admin-backend') ,
                        'id' => 'limit_free_plan',
                        'class' => 'limit_free_plan',
                        'desc' => __("Enter the maximum number allowed for employers to use your Free plan", 'admin-backend')
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'cash_message_enable',
                            'type' => 'text',
                            'title' => __("Align", 'admin-backend') ,
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
                'title' => __("Mailing", 'admin-backend') ,
                'id' => 'mail-settings',
                'icon' => 'M',
                'class' => ''
            ) ,
            
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Authentication Mail Template", 'admin-backend') ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", 'admin-backend') ,
                            'text' => __("Email templates for authentication process. You can use placeholders to include some specific content.", 'admin-backend') . '<a class="icon btn-template-help payment" data-icon="?" href="#" title="View more details"></a>' . '<div class="cont-template-help payment-setting">
                                                    [user_login],[display_name],[user_email] : ' . __("user's details you want to send mail", 'admin-backend') . '<br />
                                                    [dashboard] : ' . __("member dashboard url ", 'admin-backend') . '<br />
                                                    [title], [link], [excerpt],[desc], [author] : ' . __("project title, link, details, author", 'admin-backend') . ' <br />
                                                    [activate_url] : ' . __("activate link is require for user to renew their pass", 'admin-backend') . ' <br />
                                                    [site_url],[blogname],[admin_email] : ' . __(" site info, admin email", 'admin-backend') . '
                                                    [project_list] : ' . __("list projects employer send to freelancer when invite him to join", 'admin-backend') . '

                                                </div>',
                            
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,

                array(
                    'args' => array(
                        'title' => __("Register Mail Template", 'admin-backend') ,
                        'id' => 'register-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when he register successfull.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'register_mail_template',
                            'type' => 'textarea',
                            'title' => __("Register Mail", 'admin-backend') ,
                            'name' => 'register_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'register_mail_template',
                            'type' => 'textarea',
                            'title' => __("Register Mail", 'admin-backend') ,
                            'name' => 'register_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Register Mail Template", 'admin-backend') ,
                        'id' => 'register-mail-freelancer',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user-freelancers when he register successfull.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'register_mail_template_freelancer',
                            'type' => 'textarea',
                            'title' => __("Register Mail", 'admin-backend') ,
                            'name' => 'register_mail_freelancer_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'register_mail_template_freelancer',
                            'type' => 'textarea',
                            'title' => __("Register Mail", 'admin-backend') ,
                            'name' => 'register_mail_freelancer_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Confirm Mail Template", 'admin-backend') ,
                        'id' => 'confirm-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users after he registered successfull when option confirm email is on.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirm_mail_template',
                            'type' => 'textarea',
                            'title' => __("Confirme Mail", 'admin-backend') ,
                            'name' => 'confirm_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'confirm_mail_template',
                            'type' => 'textarea',
                            'title' => __("Confirme Mail", 'admin-backend') ,
                            'name' => 'confirm_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Confirm Mail Freelancer Template", 'admin-backend') ,
                        'id' => 'confirm-freelancer-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users after he registered successfull when option confirm email is on.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirm_mail_freelancer_template',
                            'type' => 'textarea',
                            'title' => __("Confirmed Mail Freelancer", 'admin-backend') ,
                            'name' => 'confirm_mail_freelancer_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'confirm_mail_freelancer_template',
                            'type' => 'textarea',
                            'title' => __("Confirmed Mail Freelancer", 'admin-backend') ,
                            'name' => 'confirm_mail_freelancer_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Confirmed Mail Template", 'admin-backend') ,
                        'id' => 'confirmed-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users to notify that he was confirm email successfull.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirmed_mail_template',
                            'type' => 'textarea',
                            'title' => __("Confirmed Mail", 'admin-backend') ,
                            'name' => 'confirmed_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'confirmed_mail_template',
                            'type' => 'textarea',
                            'title' => __("Confirmed Mail", 'admin-backend') ,
                            'name' => 'confirmed_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    ),
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Confirmed Phone No Template", 'admin-backend') ,
                        'id' => 'confirmed-phone',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users to notify that he was confirm phone no successfull.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'confirmed_phone_template',
                            'type' => 'textarea',
                            'title' => __("Confirmed Phone", 'admin-backend') ,
                            'name' => 'confirmed_phone_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'confirmed_phone_template',
                            'type' => 'textarea',
                            'title' => __("Confirmed Phone", 'admin-backend') ,
                            'name' => 'confirmed_phone_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Forgotpass Mail Template", 'admin-backend') ,
                        'id' => 'forgotpass-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when he request resetpass.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'textarea',
                            'title' => __("Register Mail", 'admin-backend') ,
                            'name' => 'forgotpass_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'textarea',
                            'title' => __("Register Mail", 'admin-backend') ,
                            'name' => 'forgotpass_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Resetpass Mail Template", 'admin-backend') ,
                        'id' => 'resetpass-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user to notify him has resetpass successfully.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'resetpass_mail_template',
                            'type' => 'textarea',
                            'title' => __("Resetpassword Mail", 'admin-backend') ,
                            'name' => 'resetpass_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'resetpass_mail_template',
                            'type' => 'textarea',
                            'title' => __("Resetpassword Mail", 'admin-backend') ,
                            'name' => 'resetpass_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Project Related Mail Template", 'admin-backend') ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", 'admin-backend') ,
                            'text' => __("Email templates used for project-related event. You can use placeholders to include some specific content", 'admin-backend') ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("New Message Mail Template", 'admin-backend') ,
                        'id' => 'new-message-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when he have a new message on workspace.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'new_message_mail_template',
                            'type' => 'textarea',
                            'title' => __("Inbox Mail", 'admin-backend') ,
                            'name' => 'new_message_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'new_message_mail_template',
                            'type' => 'textarea',
                            'title' => __("Inbox Mail", 'admin-backend') ,
                            'name' => 'new_message_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Inbox Mail Template", 'admin-backend') ,
                        'id' => 'inbox-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when someone contact him.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'inbox_mail_template',
                            'type' => 'textarea',
                            'title' => __("Inbox Mail", 'admin-backend') ,
                            'name' => 'inbox_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'inbox_mail_template',
                            'type' => 'textarea',
                            'title' => __("Inbox Mail", 'admin-backend') ,
                            'name' => 'inbox_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Invite Mail Template", 'admin-backend') ,
                        'id' => 'invite-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when someone invite him join a project", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'invite_mail_template',
                            'type' => 'textarea',
                            'title' => __("Invite Mail", 'admin-backend') ,
                            'name' => 'invite_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'invite_mail_template',
                            'type' => 'textarea',
                            'title' => __("Invite Mail", 'admin-backend') ,
                            'name' => 'invite_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Cash Notification Mail Template", 'admin-backend') ,
                        'id' => 'cash-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user cash message when they pay by cash", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'cash_notification_mail',
                            'type' => 'textarea',
                            'title' => __("Cash Notification Mail", 'admin-backend') ,
                            'name' => 'cash_notification_mail_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'cash_notification_mail',
                            'type' => 'textarea',
                            'title' => __("Cash Notification Mail", 'admin-backend') ,
                            'name' => 'cash_notification_mail_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),

                array(
                    'args' => array(
                        'title' => __("Receipt Mail Template", 'admin-backend') ,
                        'id' => 'ae-receipt_mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'toggle' => true, 
                        'desc' => __("Send to users after they finish a payment", 'admin-backend')
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'ae_receipt_mail',
                            'type' => 'textarea',
                            'title' => __("Receipt Mail Template", 'admin-backend') ,
                            'name' => 'ae_receipt_mail_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'ae_receipt_mail',
                            'type' => 'textarea',
                            'title' => __("Receipt Mail Template", 'admin-backend') ,
                            'name' => 'ae_receipt_mail_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Disput Mail Template", 'admin-backend') ,
                        'id' => 'ae-receipt_mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'toggle' => true,
                        'desc' => __("Send to users after they finish a payment", 'admin-backend')
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'ae_receipt_mail',
                            'type' => 'textarea',
                            'title' => __("Disput Mail Template", 'admin-backend') ,
                            'name' => 'ae_disput_mail_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'ae_receipt_mail',
                            'type' => 'textarea',
                            'title' => __("Disput Mail Template", 'admin-backend') ,
                            'name' => 'ae_disput_mail_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                
                array(
                    'args' => array(
                        'title' => __("Publish Mail Template", 'admin-backend') ,
                        'id' => 'publish-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Sent to users to notify that one of their listing has been published.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'publish_mail_template',
                            'type' => 'textarea',
                            'title' => __("publish Mail", 'admin-backend') ,
                            'name' => 'publish_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'publish_mail_template',
                            'type' => 'textarea',
                            'title' => __("publish Mail", 'admin-backend') ,
                            'name' => 'publish_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Archive Mail Template", 'admin-backend') ,
                        'id' => 'archive-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Sent to users to notify that one of their listing has been archived due to expiration or manual administrative action.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'archive_mail_template',
                            'type' => 'textarea',
                            'title' => __("archive Mail", 'admin-backend') ,
                            'name' => 'archive_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'archive_mail_template',
                            'type' => 'textarea',
                            'title' => __("archive Mail", 'admin-backend') ,
                            'name' => 'archive_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Reject Mail Template", 'admin-backend') ,
                        'id' => 'reject-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Sent to users to notify that one of their listing has been rejected.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'reject_mail_template',
                            'type' => 'textarea',
                            'title' => __("reject Mail", 'admin-backend') ,
                            'name' => 'reject_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'reject_mail_template',
                            'type' => 'textarea',
                            'title' => __("reject Mail", 'admin-backend') ,
                            'name' => 'reject_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("New Bid Mail Template", 'admin-backend') ,
                        'id' => 'bid-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Sent to users when a candidate bid their projects.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'bid_mail_template',
                            'type' => 'textarea',
                            'title' => __("Bid Mail", 'admin-backend') ,
                            'name' => 'bid_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'bid_mail_template',
                            'type' => 'textarea',
                            'title' => __("Bid Mail", 'admin-backend') ,
                            'name' => 'bid_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ) ,
                
                array(
                    'args' => array(
                        'title' => __("Bid Accepted Mail Template", 'admin-backend') ,
                        'id' => 'bid_accepted_-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to freelancer when his bid was accepted.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'bid_accepted_template',
                            'type' => 'textarea',
                            'title' => __("Bid Accepted Mail", 'admin-backend') ,
                            'name' => 'bid_accepted_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'bid_accepted_template',
                            'type' => 'textarea',
                            'title' => __("Bid Accepted Mail", 'admin-backend') ,
                            'name' => 'bid_accepted_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Complete Project Mail Template", 'admin-backend') ,
                        'id' => 'complete-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when project he worked on was marked complete.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'complete_mail_template',
                            'type' => 'textarea',
                            'title' => __("Complete Mail", 'admin-backend') ,
                            'name' => 'complete_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'complete_mail_template',
                            'type' => 'textarea',
                            'title' => __("Complete Mail", 'admin-backend') ,
                            'name' => 'complete_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Project Report Mail Template", 'admin-backend') ,
                        'id' => 'mail-description-group',
                        'class' => '',
                        'name' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'mail-description',
                            'type' => 'desc',
                            'title' => __("Mail description here", 'admin-backend') ,
                            'text' => __("Email templates used for project-report event. You can use placeholders to include some specific content", 'admin-backend') ,
                            'class' => '',
                            'name' => 'mail_description'
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Project was Reported by Employer", 'admin-backend') ,
                        'id' => 'employer-report-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to freelancer when employer sends a report on the project.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'employer_report_mail_template',
                            'type' => 'textarea',
                            'title' => __("Employer Report  Mail", 'admin-backend') ,
                            'name' => 'employer_report_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'employer_report_mail_template',
                            'type' => 'textarea',
                            'title' => __("Employer Report  Mail", 'admin-backend') ,
                            'name' => 'employer_report_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Employer closed the project", 'admin-backend') ,
                        'id' => 'employer-close-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to freelancer when employer close project.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'employer_close_mail_template',
                            'type' => 'textarea',
                            'title' => __("Employer Report  Mail", 'admin-backend') ,
                            'name' => 'employer_close_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'employer_close_mail_template',
                            'type' => 'textarea',
                            'title' => __("Employer Report  Mail", 'admin-backend') ,
                            'name' => 'employer_close_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Project Reported by Freelancer", 'admin-backend') ,
                        'id' => 'freelancer-report-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to employer when freelancer send report on project.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'freelancer_report_mail_template',
                            'type' => 'textarea',
                            'title' => __("Freelancer Report  Mail", 'admin-backend') ,
                            'name' => 'freelancer_report_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'freelancer_report_mail_template',
                            'type' => 'textarea',
                            'title' => __("Freelancer Report  Mail", 'admin-backend') ,
                            'name' => 'freelancer_report_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Freelancer Quit The Project", 'admin-backend') ,
                        'id' => 'freelancer-quit-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to the employer when the freelancer quits the project", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'freelancer_quit_mail_template',
                            'type' => 'textarea',
                            'title' => __("Freelancer Quit  Mail", 'admin-backend') ,
                            'name' => 'freelancer_quit_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'freelancer_quit_mail_template',
                            'type' => 'textarea',
                            'title' => __("Freelancer Quit  Mail", 'admin-backend') ,
                            'name' => 'freelancer_quit_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                //admin_report_mail_template
                array(
                    'args' => array(
                        'title' => __("New Report was sent to Admin", 'admin-backend') ,
                        'id' => 'admin-new-report-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to admin when user sends a report.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'admin_report_mail_template',
                            'type' => 'textarea',
                            'title' => __("Admin New Report Mail", 'admin-backend') ,
                            'name' => 'admin_report_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'admin_report_mail_template',
                            'type' => 'textarea',
                            'title' => __("Admin New Report Mail", 'admin-backend') ,
                            'name' => 'admin_report_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Admin Refunded The Payment", 'admin-backend') ,
                        'id' => 'admin-refund-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to users when admin refunds the escrow payment to the project's owner.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_refund_mail_template',
                            'type' => 'textarea',
                            'title' => __("Admin Refund Payment", 'admin-backend') ,
                            'name' => 'fre_refund_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'fre_refund_mail_template',
                            'type' => 'textarea',
                            'title' => __("Admin Refund Payment", 'admin-backend') ,
                            'name' => 'fre_refund_mail_template_de',
                            'class' => '',
                            'reset' => 1
                        )
                    )
                ),
                array(
                    'args' => array(
                        'title' => __("Admin Executed The Payment", 'admin-backend') ,
                        'id' => 'admin-execute-payment-mail',
                        'class' => 'payment-gateway',
                        'name' => '',
                        'desc' => __("Send to user when admin executes the escrow payment and send to the freelancer.", 'admin-backend'),
                        'toggle' => true
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'fre_execute_mail_template',
                            'type' => 'textarea',
                            'title' => __("Admin Execute Payment Mail", 'admin-backend') ,
                            'name' => 'fre_execute_mail_template_en',
                            'class' => '',
                            'reset' => 1
                        ),
                        array(
                            'id' => 'fre_execute_mail_template',
                            'type' => 'textarea',
                            'title' => __("Admin Execute Payment Mail", 'admin-backend') ,
                            'name' => 'fre_execute_mail_template_de',
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
                'title' => __("Language", 'admin-backend') ,
                'id' => 'language-settings',
                'icon' => 'G',
                'class' => ''
            ) ,
            
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("Website Language", 'admin-backend') ,
                        'id' => 'website-language',
                        'class' => '',
                        'name' => '',
                        'desc' => __("Select the language you want to use for your website.", 'admin-backend')
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'forgotpass_mail_template',
                            'type' => 'language_list',
                            'title' => __("Register Mail", 'admin-backend') ,
                            'name' => 'website_language',
                            'class' => ''
                        )
                    )
                ) ,
                array(
                    'args' => array(
                        'title' => __("Translator", 'admin-backend') ,
                        'id' => 'translator',
                        'class' => '',
                        'name' => 'translator',
                        'desc' => __("Translate a language", 'admin-backend')
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'translator-field',
                            'type' => 'translator',
                            'title' => __("Register Mail", 'admin-backend') ,
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
                'title' => __("Update", 'admin-backend') ,
                'id' => 'update-settings',
                'icon' => '~',
                'class' => ''
            ) ,
            
            'groups' => array(
                array(
                    'args' => array(
                        'title' => __("License Key", 'admin-backend') ,
                        'id' => 'license-key',
                        'class' => '',
                        'desc' => ''
                    ) ,
                    'fields' => array(
                        array(
                            'id' => 'et_license_key',
                            'type' => 'text',
                            'title' => __("License Key", 'admin-backend') ,
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
        // $header      =   new AE_Head( array( 'page_title'    => __('Overview', 'admin-backend'),
        //                                  'menu_title'    => __('OVERVIEW', 'admin-backend'),
        //                                  'desc'          => __("Overview", 'admin-backend') ) );
        $pages['overview'] = array(
            'args' => array(
                'parent_slug' => 'et-overview',
                'page_title' => __('Overview', 'admin-backend') ,
                'menu_title' => __('OVERVIEW', 'admin-backend') ,
                'cap' => 'administrator',
                'slug' => 'et-overview',
                'icon' => 'L',
                'desc' => sprintf(__("%s overview", 'admin-backend') , $options->blogname)
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
                'page_title' => __('Settings', 'admin-backend') ,
                'menu_title' => __('SETTINGS', 'admin-backend') ,
                'cap' => 'administrator',
                'slug' => 'et-settings',
                'icon' => 'y',
                'desc' => __("Manage how your FreelanceEngine looks and feels", 'admin-backend')
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
                'page_title' => __('Members', 'admin-backend') ,
                'menu_title' => __('MEMBERS', 'admin-backend') ,
                'cap' => 'administrator',
                'slug' => 'et-users',
                'icon' => 'g',
                'desc' => __("Overview of registered members", 'admin-backend')
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
                'page_title' => __('Payments', 'admin-backend') ,
                'menu_title' => __('PAYMENTS', 'admin-backend') ,
                'cap' => 'administrator',
                'slug' => 'et-payments',
                'icon' => '%',
                'desc' => __("Overview of all payments", 'admin-backend')
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
        //         'page_title'  => __('Setup Wizard', 'admin-backend') ,
        //         'menu_title'  => __('Setup Wizard', 'admin-backend') ,
        //         'cap'         => 'administrator',
        //         'slug'        => 'et-wizard',
        //         'icon'        => 'help',
        //         'desc'        => __("Set up and manage every content of your site", 'admin-backend')
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

