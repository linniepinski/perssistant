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
                        'msg'     => __('Your invite has been sent!', ET_DOMAIN)
                    );
                }else {
                    $resp = array(
                        'success' => false,
                        'msg'     => __('Currently, you do not have any project available to invite this user.', ET_DOMAIN)
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
}
