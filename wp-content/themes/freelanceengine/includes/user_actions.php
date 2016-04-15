<?php
/**
 *
 * Class Handle User Actions
 * @param int $result: user id
 * @author Dakachi
 * @version 1.0
 * @copyright enginethemes.com team
 * @package white panda
 *
 *
 */
class AE_User_Front_Actions extends AE_Base
{
    function __construct(AE_Users $user) {
        $this->user = $user;
        $this->mail = AE_Mailing::get_instance();
        // $this->add_action('init', 'confirm');
        $this->add_action('ae_insert_user', 'after_register');
        $this->add_action('ae_user_forgot', 'user_forgot', 10, 2);
        $this->add_action('ae_user_inbox', 'user_inbox', 10, 2);
        $this->add_action('ae_upload_image', 'change_avatar', 10, 2);

        $this->add_action('save_post', 'update_user_profile_id' );

        //$this->add_ajax('ae_send_contact', 'ae_send_contact');
        $this->add_ajax('ae-send-invite','ae_send_invite');
        $this->add_ajax('ae-decline-invite','ae_decline_invite');

        $this->add_ajax('ae-open-dispute','ae_open_dispute');

    }
    
    /*
     * confirm user
    */
    function confirm() {
        
    }
    
    /*
     * send private message between 2 users
    */
    function user_inbox($author, $message) {
        $this->mail->inbox_mail($author, $message);
    }
    
    /*
     * send email forgot to user
    */
    function user_forgot($result, $key) {
        
        /* === Send Email Forgot === */
        $this->mail->forgot_mail($result, $key);
    }
    
    /*
     * check if confirm email is active
     * update user status
    */
    function after_register($result) {
        $user = new WP_User($result);
        
        // add key confirm for user
        if (ae_get_option('user_confirm')) {
            update_user_meta($result, 'register_status', 'unconfirm');
            update_user_meta($result, 'key_confirm', md5($user->user_email));
        }
        if (ae_user_role($user->ID) ==  FREELANCER) {
            update_user_meta($result, 'interview_status', 'unconfirm');
           // update_user_meta($result, 'key_confirm', md5($user->user_email));
        }
        
        /* === Send Email Register === */
        $this->mail->register_mail($result);
    }
    
    /**
     * update user avatar
     */
    public function change_avatar($attach_data, $data) {
        //if no author ID return false;
        if (!isset($data['author'])) return;
        //update user avatar only
        if($data['method'] == "change_avatar"){
            $ae_users = AE_Users::get_instance();
            //update user avatar
            $user = $ae_users->update(array(
                'ID'            => $data['author'],
                'et_avatar'     => $attach_data['attach_id'],
                'et_avatar_url' => $attach_data['thumbnail'][0]
            ));
        }
    }

    function update_user_profile_id($post_id){
        $post = get_post($post_id);
        if($post->post_type == PROFILE) {
            update_user_meta( $post->post_author, 'user_profile_id', $post->ID );    
        }
        
    }

    function ae_send_invite(){
        global $user_ID;
        try {
            if($_POST['data']){
                $this->mail = Fre_Mailing::get_instance();
                $mail_success = $this->mail->invite_mail($_POST['user_id'], $_POST['data']['project_invites']);
                if($mail_success || true ) {
                    
                    $invited = $_POST['user_id'];
                    $send_invite = $user_ID;
                    $invite_project =  $_POST['data']['project_invites'];
                    /**
                     * do action when user have a new invite 
                     * @param int $invited invited user id
                     * @param int $send_invite user send invite
                     * @param Array $invite_project list of projects
                     * @since 1.3
                     * @author Dakachi
                     */
                    do_action( 'fre_new_invite', $invited, $send_invite, $invite_project);
                    
                    $resp = array(
                        'success' => true,
                        'msg'     => __('Your invite has been sent!', 'user-actions-backend')
                    );
                }else {
                    $resp = array(
                        'success' => false,
                        'msg'     => __('Currently, you do not have any project available to invite this user.', 'user-actions-backend')
                    );
                }
            }
        } catch (Exception $e) {
            $resp = array(
                'success' => false,
                'msg'     => $e->getMessage()
            );
        }
        wp_send_json( $resp );
    }
    function ae_decline_invite(){
        global $user_ID;
        $project_id = $_POST['id_project'];
        $IsInvitedToProject = (get_post_meta($project_id,"invited_{$user_ID}",true) == '1') ? true : false;
        if ($IsInvitedToProject) {
            $result = delete_post_meta($project_id,"invited_{$user_ID}");
            if($result){
                $resp = array(
                    'success' => true,
                    'msg' => __('Invitation has been declined!', 'user-actions-backend')
                );
            }else{
                $resp = array(
                    'success' => false,
                    'msg' => __('Try again later', 'user-actions-backend')
                );
            }
        } else {
            $resp = array(
                'success' => false,
                'msg' => __('Invitation has been already declined!', 'user-actions-backend')
            );
        }
        wp_send_json( $resp );
    }

    function ae_open_dispute()
    {
        $this->mail = Fre_Mailing::get_instance();

        $post = get_post($_POST['project_id']);
        $project_info_output = array();

        $project_info_output['current_user_id'] = $_POST['user_id'];
        $project_info_output['current_display_name'] = $_POST['current_display_name'];
        $project_info_output['ID'] = $post->ID;
        $project_info_output['e_mail'] = $_POST['e_mail'];
        $project_info_output['subject'] = $_POST['subject'];
        $project_info_output['amount'] = $_POST['amount'];
        $project_info_output['message'] = $_POST['message'];

        $project_info_output['post_status'] = $post->post_status;
        $project_info_output['post_author'] = $post->post_author;
        $project_info_output['display_name'] = get_userdata($post->post_author)->display_name;
        $project_info_output['post_date_gmt'] = $post->post_date_gmt;
        $project_info_output['post_title'] = $post->post_title;
        $project_info_output['guid'] = $post->guid;

        $project_meta = get_post_meta($post->ID);
        $project_info_output['et_budget'] = $project_meta['et_budget'][0];
        $project_info_output['et_featured'] = $project_meta['et_featured'][0];
        $project_info_output['hours_limit'] = $project_meta['hours_limit'][0];
        $project_info_output['type_budget'] = $project_meta['type_budget'][0];
        $project_info_output['accepted'] = $project_meta['accepted'][0];

        $bid_post_id = get_post_meta($post->ID)['accepted'][0];
        if ($bid_post_id) {
            $bid_post = get_post($bid_post_id);
            $project_info_output['bid_post_date_gmt'] = $bid_post->post_date_gmt;
            $project_info_output['bid_post_author'] = $bid_post->post_author;
            $project_info_output['bid_display_name'] = get_userdata($bid_post->post_author)->display_name;
            $project_bid_meta = get_post_meta($bid_post_id);
            $project_info_output['bid_budget'] = $project_bid_meta['bid_budget'][0];
            $project_info_output['bid_time'] = $project_bid_meta['bid_time'][0];
            $project_info_output['bid_type_time'] = $project_bid_meta['type_time'][0];
        }

        if ($project_info_output['post_status'] != 'opened_disput') {
            add_post_meta($project_info_output['ID'], 'post_status_before_disput', $project_info_output['post_status'], true);
            $my_post = array(
                'ID' => $_POST['project_id'],
                'post_status' => 'opened_disput'
            );
            wp_update_post($my_post);
        }

        $mail_employer = $this->mail->disput_opened($project_info_output['post_author'], $project_info_output);
        $mail_freelancer = $this->mail->disput_opened($project_info_output['bid_post_author'], $project_info_output);

        $mail_success = $this->mail->disput_opened_for_admin($project_info_output);
        $response = array();
        $response['msg'] = __('The dispute is open', 'user-actions-backend');
        $response['success'] = true;
        wp_send_json($response);
    }
}
