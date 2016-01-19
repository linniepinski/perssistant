<?php

/**
 * class Mailing control mail options
 */
Class Fre_Mailing extends AE_Mailing
{
    
    public static $instance;
    
    static function get_instance() {
        if (self::$instance == null) {
            self::$instance = new Fre_Mailing();
        }
        
        return self::$instance;
    }
    
    /**
     * bid_mail Mail to author's project know have a freelance has bided on their project.
     * @param  [type] $new_status [description]
     * @param  [type] $old_status [description]
     */
    
    /**
     * Email to author's project
     */
    function bid_mail($bid_id) {
        
        $project_id = get_post_field('post_parent', $bid_id);
        $post_author = get_post_field('post_author', $project_id);
        $author = get_userdata($post_author);
        if ($author) {
            $message = ae_get_option('bid_mail_template');
            $bid_msg = get_post_field('post_content', $bid_id);
            $message = str_replace('[message]', $bid_msg, $message);
            $subject = sprintf(__("Your project posted on %s has a new bid.", ET_DOMAIN) , get_option('blogname'));
            
            return $this->wp_mail($author->user_email, $subject, $message, array(
                'post' => $project_id,
                'user_id' => $post_author
            ) , '');
        }
        return false;
    }
    
    /**
     * employer complete a job and send mail to freelancer joined project
     * @param integer $project_id The project id
     * @since 1.0
     * @author Dan
     */
    function review_freelancer_email($project_id) {
        
        $message = ae_get_option('complete_mail_template');
        global $wp_rewrite;
        if ($wp_rewrite->using_permalinks()) {
            $replace = '?review=1';
        } else {
            $replace = '&review=1';
        }
        $message = str_replace('[review]', $replace, $message);
        
        $subject = __("Project you joined has a review.", ET_DOMAIN);
        $bid_id = get_post_meta($project_id, 'accepted', true);
        $freelancer_id = get_post_field('post_author', $bid_id);
        $author = get_userdata($freelancer_id);
        $this->wp_mail($author->user_email, $subject, $message, array(
            'post' => $project_id,
            'user_id' => $freelancer_id
        ) , '');
        return $author;
    }
    
    /**
     * employer complete a job and send mail to freelancer joined project
     * @param integer $project_id The project id
     * @since 1.0
     * @author Dan
     */
    function review_employer_email($project_id) {
        
        $message = ae_get_option('complete_mail_template');
        $message = str_replace('[review]', '', $message);
        
        $subject = __("Your posted project has a review.", ET_DOMAIN);
        
        // $bid_id = get_post_meta($project_id, 'accepted', true);
        $employer_id = get_post_field('post_author', $project_id);
        $author = get_userdata($employer_id);
        $this->wp_mail($author->user_email, $subject, $message, array(
            'post' => $project_id,
            'user_id' => $employer_id
        ) , '');
        return $author;
    }
    
    /**
     * invite a freelancer to work on current user project
     * @param int $user_id The user will be invite
     * @param int $project_id The project will be send
     * @since 1.0
     * @author Dakachi
     */
    function invite_mail($user_id, $project_id) {
        global $current_user, $user_ID;
        if ($user_id && $project_id) {
            
            // $user = new WP_User($user_id);
            // get user email
            $user_email = get_the_author_meta('user_email', $user_id);
            
            // mail subject
            $subject = sprintf(__("You have a new invitation to join project from %s.", ET_DOMAIN) , get_option('blogname'));
            
            // build list of project send to freelancer
            $project_info = '';
            foreach ($project_id as $key => $value) {
                // check invite this project or not
                if(fre_check_invited($user_id, $value)) continue;
                $project_link = get_permalink($value);
                $project_tile = get_the_title($value);
                // create a invite message
                fre_create_invite($user_id, $value);

                $project_info.= '<li><p>' . $project_tile . '</p><p>' . $project_link . '</p></li>';
            }

            if($project_info == '') return false;
            $project_info= '<ul>'.$project_info.'</ul>';
            
            // get mail template
            $message = '';
            if (ae_get_option('invite_mail_template')) {
                $message = ae_get_option('invite_mail_template');
            }
            
            // replace project list by placeholder
            $message = str_replace('[project_list]', $project_info, $message);
            
            // send mail
            return $this->wp_mail($user_email, $subject, $message, array(
                'user_id' => $user_id, 
                'post' => $value
            ));
        }
    }
    
    /**
     * send email to freelancer if his/her bid is accepted by employer
     * use mail template bid_accepted_template
     * @param int $freelancer_id
     * @param int $project_id
     * @since 1.1
     * @author Dakachi
     */
    function bid_accepted($freelancer_id, $project_id) {
        $user_email = get_the_author_meta('user_email', $freelancer_id);
        
        // mail subject
        $subject = sprintf(__("Your bid on project %s has been accepted.", ET_DOMAIN) , get_the_title($project_id));
        
        // get mail template
        $message = '';
        if (ae_get_option('bid_accepted_template')) {
            $message = ae_get_option('bid_accepted_template');
        }
        
        $workspace_link = add_query_arg(array(
            'workspace' => 1
        ) , get_permalink($project_id));

        $workspace_link = '<a href="' . $workspace_link . '">' . __("Workspace", ET_DOMAIN) . '</a>';
        $message = str_replace('[workspace]', $workspace_link, $message);
        
        return $this->wp_mail($user_email, $subject, $message, array(
            'user_id' => $freelancer_id,
            'post' => $project_id
        ));
    }
    
    /**
     * send email to employer when have new message
     * @param int $receiver the user will receive email
     * @param int $project the project id message send base on
     * @param string $message the message content
     * @since 1.2
     * @author Dakachi
     */
    function new_message($receiver, $project, $message) {
        $user_email = get_the_author_meta('user_email', $receiver);
        
        // mail subject
        $subject = sprintf(__("You have a new message on %s workspace.", ET_DOMAIN) , get_the_title($project));
        $workspace_link = add_query_arg(array(
            'workspace' => 1
        ) , get_permalink($project));
        
        $mail_template = ae_get_option('new_message_mail_template');
        
        // replace message content place holder
        $mail_template = str_replace('[message]', $message->comment_content, $mail_template);
        
        // replace workspace place holder
        $workspace_link = '<a href="' . $workspace_link . '">' . __("Workspace", ET_DOMAIN) . '</a>';
        $mail_template = str_replace('[workspace]', $workspace_link, $mail_template);
        
        // send mail
        return $this->wp_mail($user_email, $subject, $mail_template, array(
            'user_id' => $receiver,
            'post' => $project
        ));
    }
    
    /**
     * send email to 3 user admin, employer, or freelancer when have a new report ignore current user
     * @param Int $project_id The project id was reported
     * @param Object $report The object contain report content
     * @since 1.3
     * @author Dakachi
     */
    function new_report($project_id, $report) {
        global $user_ID;
        $project = get_post($project_id);
        
        // email subject
        $subject = sprintf(__("Have a new report on project %s.", ET_DOMAIN) , get_the_title($project_id));
        
        if ($project->post_author == $user_ID) {
            
            // mail to freelancer when project owner send a report
            $mail_template = ae_get_option('employer_report_mail_template');
            $mail_template = str_replace('[reporter]', get_the_author_meta('display_name', $user_ID) , $mail_template);
            
            $bid_id = get_post_meta($project_id, 'accepted', true);
            $bid_author = get_post_field('post_author', $bid_id);
            $user_email = get_the_author_meta('user_email', $bid_author);
            $receiver = $bid_author;
        } else {
            
            // mail to employer when freelancer working on project send a new report
            $mail_template = ae_get_option('freelancer_report_mail_template');
            $mail_template = str_replace('[reporter]', get_the_author_meta('display_name', $user_ID) , $mail_template);
            
            $user_email = get_the_author_meta('user_email', $project->post_author);
            $receiver = $project->post_author;
        }

        $workspace_link = add_query_arg(array(
            'workspace' => 1
        ) , get_permalink($project_id));

        $workspace_link = '<a href="' . $workspace_link . '">' . __("Workspace", ET_DOMAIN) . '</a>';
        // replace workspace place holder
        $mail_template = str_replace('[workspace]', $workspace_link, $mail_template);
        
        // mail to admin
        $admin_template = ae_get_option('admin_report_mail_template');
        $admin_template = str_replace('[reporter]', get_the_author_meta('display_name', $user_ID) , $admin_template);
        
        // send mail to freelancer / employer
        $this->wp_mail($user_email, $subject, $mail_template, array(
            'user_id' => $receiver,
            'post' => $project_id
        ));
        
        // send mail to admin
        $this->wp_mail(get_option('admin_email') , $subject, $admin_template, array(
            'user_id' => 1,
            'post' => $project_id
        ));
    }
    
    /**
     * send email to freelancer, admin when employer request close project
     * @param snippet
     * @since 1.3
     * @author Dakachi
     */
    function close_project($project_id, $message) {
        global $user_ID;
        $project = get_post($project_id);
        
        // email subject
        $subject = sprintf(__("Project %s was closed.", ET_DOMAIN) , get_the_title($project_id));
        
        // mail to freelancer when project owner send a report
        $mail_template = ae_get_option('employer_close_mail_template');
        $mail_template = str_replace('[reporter]', get_the_author_meta('display_name', $user_ID) , $mail_template);
        
        $bid_id = get_post_meta($project_id, 'accepted', true);
        $bid_author = get_post_field('post_author', $bid_id);
        $user_email = get_the_author_meta('user_email', $bid_author);

        $workspace_link = add_query_arg(array(
            'workspace' => 1
        ) , get_permalink($project_id));

        $workspace_link = '<a href="' . $workspace_link . '">' . __("Workspace", ET_DOMAIN) . '</a>';
        // replace workspace place holder
        $mail_template = str_replace('[workspace]', $workspace_link, $mail_template);
        
        // send mail to freelancer / employer
        $this->wp_mail($user_email, $subject, $mail_template, array(
            'user_id' => $bid_author,
            'post' => $project_id
        ));
        
        // mail to admin
        $admin_template = ae_get_option('admin_report_mail_template');
        $admin_template = str_replace('[reporter]', get_the_author_meta('display_name', $user_ID) , $admin_template);
        
        // send mail to admin
        $this->wp_mail(get_option('admin_email') , $subject, $admin_template, array(
            'user_id' => 1,
            'post' => $project_id
        ));
    }
    
    /**
     * send email to employer, admin when freelancer request close project
     * @param snippet
     * @since 1.3
     * @author Dakachi
     */
    function quit_project($project_id, $message) {
        global $user_ID;
        $project = get_post($project_id);
        
        // email subject
        $subject = sprintf(__("User quit your project %s.", ET_DOMAIN) , get_the_title($project_id));
        
        // mail to employer when freelancer working on project send a new report
        $mail_template = ae_get_option('freelancer_quit_mail_template');
        $mail_template = str_replace('[reporter]', get_the_author_meta('display_name', $user_ID) , $mail_template);
        
        $user_email = get_the_author_meta('user_email', $project->post_author);

        $workspace_link = add_query_arg(array(
            'workspace' => 1
        ) , get_permalink($project_id));

        $workspace_link = '<a href="' . $workspace_link . '">' . __("Workspace", ET_DOMAIN) . '</a>';

        // replace workspace place holder
        $mail_template = str_replace('[workspace]', $workspace_link, $mail_template);
        
        // send mail to freelancer / employer
        $this->wp_mail($user_email, $subject, $mail_template, array(
            'user_id' => $project->post_author,
            'post' => $project_id
        ));
        
        // mail to admin
        $admin_template = ae_get_option('admin_report_mail_template');
        $admin_template = str_replace('[reporter]', get_the_author_meta('display_name', $user_ID) , $admin_template);
        
        // send mail to admin
        $this->wp_mail(get_option('admin_email') , $subject, $admin_template, array(
            'user_id' => 1,
            'post' => $project_id
        ));
    }
    
    /**
     * send mail to employer, freelancer when admin decide dispute process
     * @param
     * @since 1.3
     * @author Dakachi
     */
    function refund($project_id, $bid_accepted) {
        $project_owner = get_post_field('post_author', $project_id);
        $bid_owner = get_post_field('post_author', $bid_accepted);
        
        $mail_template = ae_get_option('fre_refund_mail_template');
        if (!$mail_template) return;
        
        // mail to project owner
        $user_email = get_the_author_meta('user_email', $project_owner);
        $subject = sprintf(__("You have got a refund on project %s.", ET_DOMAIN) , get_the_title($project_id));

        $workspace_link = add_query_arg(array(
            'workspace' => 1
        ) , get_permalink($project_id));

        $workspace_link = '<a href="' . $workspace_link . '">' . __("Workspace", ET_DOMAIN) . '</a>';
        // replace workspace place holder
        $mail_template = str_replace('[workspace]', $workspace_link, $mail_template);

        $this->wp_mail($user_email, $subject, $mail_template, array(
            'user_id' => $project_owner,
            'post' => $project_id
        ));
        
        // mail to freelancer
        $user_email = get_the_author_meta('user_email', $bid_owner);
        $subject = sprintf(__("Project %s you worked on has refunded.", ET_DOMAIN) , get_the_title($project_id));
        $this->wp_mail($user_email, $subject, $mail_template, array(
            'user_id' => $bid_owner,
            'post' => $project_id
        ));
    }
    
    /**
     * send mail to employer, freelancer when admin execute payment
     * @param
     * @since 1.3
     * @author Dakachi
     */
    function execute($project_id, $bid_accepted) {
        $project_owner = get_post_field('post_author', $project_id);
        $bid_owner = get_post_field('post_author', $bid_accepted);
        
        $mail_template = ae_get_option('fre_execute_mail_template');
        if (!$mail_template) return;
        
        // mail to project owner
        $user_email = get_the_author_meta('user_email', $project_owner);
        $subject = sprintf(__("Your presend payment on project %s has been transfer.", ET_DOMAIN) , get_the_title($project_id));
        
        $workspace_link = add_query_arg(array(
            'workspace' => 1
        ) , get_permalink($project_id));

        $workspace_link = '<a href="' . $workspace_link . '">' . __("Workspace", ET_DOMAIN) . '</a>';
        // replace workspace place holder
        $mail_template = str_replace('[workspace]', $workspace_link, $mail_template);

        $this->wp_mail($user_email, $subject, $mail_template, array(
            'user_id' => $project_owner,
            'post' => $project_id
        ));
        
        // mail to freelancer
        $user_email = get_the_author_meta('user_email', $bid_owner);
        $subject = sprintf(__("You have been sent payment base on project %s.", ET_DOMAIN) , get_the_title($project_id));
        $this->wp_mail($user_email, $subject, $mail_template, array(
            'user_id' => $bid_owner,
            'post' => $project_id
        ));
    }
    
    /**
     * mail alert admin when project complete and transfer money to freelancer
     * @param int $project_id The project was completed
     * @param int $bid_accepted The bid accepted on project
     * @since 1.3
     * @author Dakachi
     */
    function alert_transfer_money($project_id, $bid_accepted) {
        if (!ae_get_option('manual_transfer')) {
            
            // mail to admin
            $subject = sprintf(__("Project %s has been completed and money has transfered.", ET_DOMAIN) , get_the_title($project_id));
            $admin_template = sprintf(__("Project %s has been completed and money has transfered. You can check workspace and project details", ET_DOMAIN) , get_the_title($project_id));
        } else {
            $subject = sprintf(__("Project %s has been completed and waiting your confirm.", ET_DOMAIN) , get_the_title($project_id));
            $admin_template = sprintf(__("Project %s has been completed. Please check it and transfer money to freelancer.", ET_DOMAIN) , get_the_title($project_id));
        }
        
        $admin_template.= get_permalink($project_id);
        
        // send mail to admin
        $this->wp_mail(get_option('admin_email') , $subject, $admin_template, array(
            'user_id' => 1,
            'post' => $project_id
        ));
    }
}
