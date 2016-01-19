<?php
abstract class ET_SocialAuth extends AE_Base
{
    protected $social_option;
    
    abstract protected function send_created_mail($user_id);
    
    protected $social_id = false;
    
    public function __construct($type, $social_option, $labels = array()) {
        $this->social_type = $type;
        $this->social_option = $social_option;
        $this->auth_url = add_query_arg('type', $this->social_type, ae_get_option('social_connect'));
        $this->labels = $labels;
        $this->add_action('wp_enqueue_scripts', 'enqueue_scripts');
        $this->add_action('template_redirect', 'social_redirect');
        $this->add_ajax('et_authentication_' . $type, 'authenticate_user');
        $this->add_ajax('et_confirm_username_' . $type, 'confirm_username');
    }
    public function enqueue_scripts() { 
        global $current_user; ?>
		<script type="text/javascript" id="current_user">
            var currentUser = <?php if (isset($current_user->ID) && $current_user->ID != 0) echo json_encode($current_user);
            else echo json_encode(array(
                'id' => 0,
                'ID' => 0
            )); ?>;
            var is_mobile = <?php echo json_encode(et_load_mobile()); ?>
            </script>
        <?php   $this->add_script('et-authentication', ae_get_url() . '/social/js/authentication.js', array(
                'jquery',
                'underscore',
                'backbone'
            ));
        if( is_social_connect_page() ){
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            if (isset($_SESSION['et_auth_type'])) {
                wp_localize_script('et-authentication', 'ae_auth', array(
                    'action_auth' => 'et_authentication_' . $_SESSION['et_auth_type'],
                    'action_confirm' => 'et_confirm_username_' . $_SESSION['et_auth_type']
                ));
            } else {
                wp_redirect(home_url());
                exit();
            }
        }
        $this->register_style('social-connect-style', ae_get_url() . '/social/css/default.css');
    }
    
    public function social_redirect() {
        $flag = is_social_connect_page();
        if ( $flag && is_user_logged_in() ) {
            wp_redirect( home_url() );
            exit();
        }
        if ( $flag ) {
            global $et_data;
            if (isset($_GET['type']) && $_GET['type'] == $this->social_type) {
                $et_data['auth_labels'] = $this->labels;
            }
        }
    }
    protected function get_user($social_id) {
        $args = array(
            'meta_key' => $this->social_option,
            'meta_value' => trim($social_id) ,
            'number' => 1
        );
        $users = get_users($args);
        if (!empty($users) && is_array($users)) return $users[0];
        else return false;
    } 
    protected function logged_user_in($social_id) {
        $ae_user = $this->get_user($social_id);   
        if ($ae_user != false) {
            wp_set_auth_cookie($ae_user->ID);
            wp_set_current_user($ae_user->ID);
            return true;
        } else {
            return false;
        }
    }
    
    protected function _create_user($params) {
        // insert user
        $ae_user = AE_Users::get_instance();
        $result = $ae_user->insert($params);
        if (!is_wp_error($result)) {
            // send email here
            $this->send_created_mail($result);
            // login
            $ae_user = wp_signon(array(
                'user_login' => $params['user_login'],
                'user_password' => $params['user_pass']
            ));
            if (is_wp_error($ae_user)) {
                return $ae_user;
            } else { 
                // Authenticate successfully
                return true;
            }
        } else {
            return $result;
        }
    }
    protected function connect_user($email, $password) {
        if ($this->social_id != false) {
            // get user first
            $ae_user = get_user_by('email', $email);
            if ($ae_user == false) return new WP_Error('et_password_not_matched', __("Username and password does not matched", ET_DOMAIN));
            // verify password
            if (wp_check_password($password, $ae_user->data->user_pass, $ae_user->ID)) {  
                // connect user
                update_user_meta($ae_user->ID, $this->social_option, $this->social_id);
                return true;
            } else {
                return new WP_Error('et_password_not_matched', __("Username and password does not matched", ET_DOMAIN));
            }
        } else {
            return new WP_Error('et_wrong_social_id', __("There is an error occurred", ET_DOMAIN));
        }
    }
    protected function social_connect_success() {
        wp_redirect(home_url());
        exit;
    }
    public function authenticate_user() {
        try {  
            // turn on session
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            $data = $_POST['content'];  
            // find user first
            if (empty($data['user_email']) || empty($data['user_pass'])) throw new Exception(__('Login information is missing', ET_DOMAIN));
            $pattern = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/';
            //if (!preg_match($pattern, $data['user_email']))
            //var_dump(filter_var($data['user_email'],FILTER_VALIDATE_EMAIL));
            if (filter_var($data['user_email'], FILTER_VALIDATE_EMAIL) === false) throw new Exception(__('Please provide a valid email', ET_DOMAIN)); 
            $email = $data['user_email'];
            $pass = $data['user_pass'];
            $ae_user = get_user_by('email', $email);
            $return = array();
            // if user doesn't exist, create one
            if ($ae_user == false) { 
                // save to session, waiting for username input
                if (isset($_SESSION['et_auth'])) $auth_info = unserialize($_SESSION['et_auth']);
                else $auth_info = array(); 
                $auth_info = wp_parse_args(array(
                    'user_email' => $email,
                    'user_pass' => $pass
                ) , $auth_info);
                $_SESSION['et_auth'] = serialize($auth_info);
                if (isset($auth_info['user_login'])) {
                    $auth_info['user_login'] = str_replace(' ', '', sanitize_user($auth_info['user_login']));
                    $ae_user = get_user_by('login', $auth_info['user_login']);
                    $ae_user = AE_Users::get_instance();
                    if (!$ae_user) {
                        $result = $ae_user->insert($auth_info);
                        if ($result == false || is_wp_error($result)) throw new Exception(__("Can not authenticate user", ET_DOMAIN));
                        else if (empty($_SESSION['et_social_id'])) {
                            throw new Exception(__("Can't find Social ID", ET_DOMAIN));
                        } else {  
                            update_user_meta($result, $this->social_option, $_SESSION['et_social_id']);
                            do_action('et_after_register', $result);
                            wp_set_auth_cookie($result, 1);
                            unset($_SESSION['et_auth']);
                        }
                        $return = array(
                            'status' => 'linked',
                            'user' => $ae_user,
                            'redirect_url' => home_url()
                        );
                    } else {
                        $return = array(
                            'status' => 'wait'
                        );
                    }
                } else {
                    $return = array(
                        'status' => 'wait'
                    );
                }
            }
            // if user does exist, connect them
            else { 
                // khi ti`m thay user bang email, kiem tra password
                // neu password dung thi dang nhap luon
                if (wp_check_password($pass, $ae_user->data->user_pass, $ae_user->ID)) {
                    // connect user
                    update_user_meta($ae_user->ID, $this->social_option, $_SESSION['et_social_id']);
                    //
                    wp_set_auth_cookie($ae_user->ID, 1);
                    unset($_SESSION['et_auth']);
                    $return = array(
                        'status' => 'linked',
                        'user' => $ae_user,
                        'redirect_url' => home_url()
                    );
                } else {
                    throw new Exception(__("This email is already existed. If you are the owner, please enter the right password", ET_DOMAIN));
                }
            }
            $resp = array(
                'success' => true,
                'msg' => '',
                'data' => $return
            );
        }
        catch(Exception $e) {
            $resp = array(
                'success' => false,
                'msg' => $e->getMessage()
            );
        }
        wp_send_json($resp);
    }
    public function confirm_username() {
        try {       
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            // get data
            $data = $_POST['content'];
            $auth_info = unserialize($_SESSION['et_auth']);
            $username = $data['user_login'];
            if (isset($data['user_role']) && $data['user_role'] != '') {
                $user_roles = ae_get_option('social_user_role', false);
                if( !$user_roles ){
                    $user_roles = ae_get_social_login_user_roles_default();
                }
                if ($user_roles && in_array($data['user_role'], $user_roles) && $data['user_role'] != 'Administrator') {
                    $auth_info['role'] = $data['user_role'];
                }
            }
            // verify username
            $ae_user = get_user_by('login', $username);
            $return = array();
            if ($ae_user != false) throw new Exception(__('Username is existed, please choose another one', ET_DOMAIN));
            else {
                $auth_info['user_login'] = $username; 
                // create user
                $ae_user = AE_Users::get_instance();
                $result = $ae_user->insert($auth_info);
                if (is_wp_error($result)) throw new Exception($result->get_error_message());
                else if (empty($_SESSION['et_social_id'])) {
                    throw new Exception(__("Can't find Social ID", ET_DOMAIN));
                } 
                else {
                    // creating user successfully
                    update_user_meta((int)$result->ID, $this->social_option, $_SESSION['et_social_id']);
                    do_action('et_after_register', $result);
                    wp_set_auth_cookie((int)$result->ID, 1);
                    unset($_SESSION['et_auth']);
                    $return = array(
                        'user_id' => $result,
                        'redirect_url' => home_url()
                    );
                }
            }
            $resp = array(
                'success' => true,
                'msg' => '',
                'data' => $return
            );
        }
        catch(Exception $e) {
            $resp = array(
                'success' => false,
                'msg' => $e->getMessage()
            );
        }
        wp_send_json($resp);
    }
}


class ET_TwitterAuth extends ET_SocialAuth
{
    
    const OPT_CONSUMER_KEY = 'et_twitter_key';
    const OPT_CONSUMER_SECRET = 'et_twitter_secret';
    protected $consumer_key;
    protected $consumer_secret;
    protected $oath_callback;
    public function __construct() {
        parent::__construct('twitter', 'et_twitter_id', array(
            'title' => __("SIGN IN WITH TWITTER", ET_DOMAIN) ,
            'content' => __("This seems to be your first time signing in using your Twitter account.If you already have an account  , please log in using the form below to link it to your Twitter account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.You will only do this step ONCE. Next time, you'll get logged in right away.", ET_DOMAIN) ,
            'content_confirm' => __("Please provide a username to continue", ET_DOMAIN)
        ));
        $this->consumer_key = ae_get_option(self::OPT_CONSUMER_KEY, '');
         // 'H7ggzgE4rNubSq09SKQJGw';
        $this->consumer_secret = ae_get_option(self::OPT_CONSUMER_SECRET, '');
         //'zUrMVznhHvrMEKBE5LhipfvRODLlPsvEJLvYiaf4yqE';
        $this->oath_callback = add_query_arg('action', 'twitterauth_callback', home_url());
        
        // only run if options are given
        if (!empty($this->consumer_key) && !empty($this->consumer_secret) && !is_user_logged_in()) {
            
            //$this->add_action('init', 'redirect');
            $this->redirect();
        }
    }
    /**
     * Return if twitter auth are ready to run
     */
    public static function is_active() {
        $consumer_key = ae_get_option(self::OPT_CONSUMER_KEY, '');
        $consumer_secret = ae_get_option(self::OPT_CONSUMER_SECRET, '');
        
        return (!empty($consumer_key) && !empty($consumer_secret));
    }
    
    protected function send_created_mail($user_id) {
        do_action('et_after_register', $user_id);
    }
    /**
     * Redirect and auth twitter account
     */
    public function redirect() {
        if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'twitterauth') { 
            // request token
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            require_once dirname(__FILE__) . '/twitteroauth/twitteroauth.php';
            // create connection
            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret);
            // request token
            $request_token = $connection->getRequestToken($this->oath_callback);
            //
            if ($request_token) { 
                // var_dump($request_token);
                // exit;
                if( isset( $request_token['oauth_token'] ) && $request_token['oauth_token_secret'] ){
                    $token = $request_token['oauth_token'];
                    $_SESSION['oauth_token'] = $token;
                    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
                }
                // redirect to twitter
                switch ($connection->http_code) {
                    case 200:
                        $url = $connection->getAuthorizeURL($request_token);
                        //redirect to Twitter .
                        header('Location: ' . $url);
                        exit;
                        break;
                    default:
                        _e("Conection with twitter Failed", ET_DOMAIN);
                        exit;
                        break;
                }
            } else {
                echo __("Error Receiving Request Token", ET_DOMAIN);
                exit;
            }
        }
        // twitter auth callback
        else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'twitterauth_callback' && $_GET['oauth_token']) {  
            // request access token and
            // create account here
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            require_once dirname(__FILE__) . '/twitteroauth/twitteroauth.php';
            // create connection
            $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
            // request access token
            $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
            //
            if ($access_token && isset($access_token['oauth_token'])) {
                // recreate connection
                $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);  
                $account = $connection->get('account/verify_credentials');
                // create account
                if ($account && isset($account->screen_name) && isset($account->name)) {
                    // find
                    $users = get_users(array(
                        'meta_key' => 'et_twitter_id',
                        'meta_value' => $account->id
                    ));
                    if (!empty($users) && is_array($users)) {
                        $ae_user = $users[0];
                        wp_set_auth_cookie($ae_user->ID, 1);
                        wp_redirect(home_url());
                        exit;
                    }
                    $avatars = array();
                    $sizes = get_intermediate_image_sizes();
                    foreach ($sizes as $size) {
                        $avatars[$size] = array(
                            $account->profile_image_url
                        );
                    }
                    // save user info for saving later
                    $_SESSION['user_login'] = $account->screen_name;
                    $_SESSION['display_name'] = $account->name;
                    $_SESSION['et_twitter_id'] = $account->id;
                    $_SESSION['user_location'] = $account->location;
                    $_SESSION['description'] = $account->description;
                    $_SESSION['profile_image_url'] = $account->profile_image_url;
                    $_SESSION['et_auth'] = serialize(array(
                        'user_login' => $account->screen_name,
                        'display_name' => $account->name,
                        'user_location' => $account->location,
                        'description' => $account->description,
                        'et_avatar' => $avatars,
                    ));
                    $_SESSION['et_social_id'] = $account->id;
                    $_SESSION['et_auth_type'] = 'twitter';
                    wp_redirect($this->auth_url);
                    exit();
                }
            }
            exit();
        }
        // create user
        else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'twitterauth_login') {
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            if (!empty($_POST['user_email'])) {
                $password = wp_generate_password();
                $new_account = array(
                    'user_login' => $_SESSION['user_login'],
                    'display_name' => $_SESSION['display_name'],
                    'et_twitter_id' => $_SESSION['et_twitter_id'],
                    'user_location' => $_SESSION['user_location'],
                    'description' => $_SESSION['description'],
                    'user_email' => $_POST['user_email'],
                    'user_pass' => $password,
                    'et_avatar' => array(
                        'thumbnail' => array(
                            $_SESSION['profile_image_url']
                        )
                    )
                );
                $ae_user = get_user_by('login', $new_account['user_login']);
                if ($ae_user != false) {
                    $new_account['user_login'] = str_replace('@', '', $_POST['user_email']);
                }
                $ae_user = AE_Users::get_instance();
                $result = $ae_user->insert($new_account);
                if (!is_wp_error($result)) {
                    // send email here
                    //
                    do_action('et_after_register', $result);
                    // wp_mail( $_POST['user_email'],
                    // 	__("You have been logged in via Twitter", ET_DOMAIN),
                    // 	"Hi, <br/> your pasword on our site is {$password}");
                    // login
                    $ae_user = wp_signon(array(
                        'user_login' => $new_account['user_login'],
                        'user_password' => $new_account['user_pass']
                    ));
                    if (is_wp_error($ae_user)) {
                        global $et_error;
                        $et_error = $ae_user->get_error_message();
                        
                        //echo $user->get_error_message();
                    } else {
                        wp_redirect(home_url());
                        exit;
                    }
                } else {
                    global $et_error;
                    $et_error = $result->get_error_message();
                }
            }
            // ask people for password
            include TEMPLATEPATH . '/page-twitter-auth.php';
            exit;
        }
    }
}

class ET_FaceAuth extends ET_SocialAuth
{
    private $fb_secret_key;
    protected $fb_app_id;
    protected $fb_token_url;
    protected $fb_exchange_token;
    public function __construct() {
        parent::__construct('facebook', 'et_facebook_id', array(
            'title' => __('SIGN IN WITH FACEBOOK', ET_DOMAIN) ,
            'content' => __("This seems to be your first time signing in using your Facebook account.If you already have an account, please log in using the form below to link it to your Facebook account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.You will only do this step ONCE. Next time, you'll get logged in right away.", ET_DOMAIN) ,
            'content_confirm' => __("Please provide a username to continue", ET_DOMAIN)
        ));
        //$this->add_action('init', 'auth_facebook');
        $this->fb_app_id = ae_get_option('et_facebook_key', false);
        $this->fb_secret_key = ae_get_option('et_facebook_secret_key', false);
        $this->fb_token_url = 'https://graph.facebook.com/me';
        $this->fb_exchange_token = 'https://graph.facebook.com/oauth/access_token';
        $this->add_action('wp_enqueue_scripts', 'add_scripts', 20);
        $this->add_ajax('et_facebook_auth', 'auth_facebook');
    }
    public function add_scripts() {
        //$this->add_script('facebook_auth', '//connect.facebook.net/en_US/all.js', array(), false, true);
        $this->add_script('facebook_auth', ae_get_url() . '/social/js/facebookauth.js', array(
            'jquery'
        ) , false, true);
        wp_localize_script('facebook_auth', 'facebook_auth', array(
            'appID' => ae_get_option('et_facebook_key') ,
            'auth_url' => home_url('?action=authentication')
        ));
    }
    // implement abstract method
    protected function send_created_mail($user_id) {
        do_action('et_after_register', $user_id);
    }
    public function auth_facebook() {
        try {
            // turn on session
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            $fb_appID = ae_get_option('et_facebook_key', false);
            $fb_secret_key = ae_get_option('et_facebook_secret_key', false);
            if ( !$this->fb_app_id || !$this->fb_secret_key ) {
                $resp = array(
                    'success' => false,
                    'msg' => __('Social login is invalid. Please contact administrator for help.', ET_DOMAIN)
                );
                wp_send_json($resp);
                return;
            }
            if( !isset( $_POST['fb_token'] ) || $_POST['fb_token'] == '' ){
                   $resp = array(
                    'success' => false,
                    'msg' => __('Social login is invalid. Please contact administrator for help.', ET_DOMAIN)
                );
                wp_send_json($resp);
                return;
            }
            /**
             * check user id with a access token
             */
            $token_url = $this->fb_token_url;
            $token_url .= '?fields=id&access_token='.$_POST['fb_token'];
            $check_userid = wp_remote_get( $token_url );
            $check_userid = json_decode( $check_userid['body'] );
            if( !isset( $check_userid->id ) ||  $check_userid->id == '' ){
                $resp = array(
                    'success' => false,
                    'msg' => __('Social login is invalid. Please contact administrator for help.', ET_DOMAIN)
                );
                wp_send_json($resp);
                return;
            }
            $check_userid = $check_userid->id;
            /**
             * check user vefified app
             *
             */
            $fb_exchange_token = $this->fb_exchange_token;
            $fb_exchange_token .= '?grant_type=fb_exchange_token&';
            $fb_exchange_token .= 'client_id='.$this->fb_app_id.'&';
            $fb_exchange_token .= 'client_secret='.$this->fb_secret_key.'&';
            $fb_exchange_token .= 'fb_exchange_token='.$_POST['fb_token'];
            // $fb_app_token = wp_remote_get('https://graph.facebook.com/oauth/access_token?grant_type=fb_exchange_token&client_id='.$this->fb_app_id.'&client_secret='.$this->fb_secret_key.'&fb_exchange_token=' . $_POST['fb_token']);
            $fb_app_token = wp_remote_get( $fb_exchange_token );
            if( !isset( $_POST['content'] ) || empty( $_POST['content'] ) ){
                $resp = array(
                    'success' => false,
                    'msg' => __('Social login is invalid. Please contact administrator for help.', ET_DOMAIN)
                );
                wp_send_json($resp);
                return;
            }
            $data = $_POST['content'];
            if( !isset( $data['id'] ) || $data['id'] == '' ){
                $resp = array(
                    'success' => false,
                    'msg' => __('Social login is invalid. Please contact administrator for help.', ET_DOMAIN)
                );
                wp_send_json($resp);
                return;
            }
            if ( isset($fb_app_token['body']) && $fb_app_token['body'] != '' ) {
                $fb_app_token = explode('&', $fb_app_token['body']);
                $fb_token = explode('=', $fb_app_token['0']);
                if ($check_userid != $data['id'] || !isset($fb_token[1]) || $fb_token[1] == '') {
                    $fb_token = $fb_token['1'];
                    $resp = array(
                        'success' => false,
                        'msg' => __('Please login by using your Facebook account again!')
                    );
                    wp_send_json($resp);
                    return;
                }
            } else {
                $resp = array(
                    'success' => false,
                    'msg' => __('Please login by using your Facebook account again!')
                );
                wp_send_json($resp);
                return;
            }
            // find usser
            $return = array(
                'redirect_url' => home_url()
            );
            $user = $this->get_user($data['id']);
            // if user is already authenticated before
            if ( $user ) {
                $result = $this->logged_user_in($data['id']);
                $ae_user = AE_Users::get_instance();
                $userdata = $ae_user->convert($user);
                $nonce = array(
                    'reply_thread' => wp_create_nonce('insert_reply') ,
                    'upload_img' => wp_create_nonce('et_upload_images') ,
                );
                
                $return = array(
                    'user' => $userdata,
                    'nonce' => $nonce
                );
            }
            // if user never authenticated before
            else {
                // avatar
                $ava_response = wp_remote_get('http://graph.facebook.com/' . $data['id'] . '/picture?type=large&redirect=false');
                if (!is_wp_error($ava_response)) $ava_response = json_decode($ava_response['body']);
                else $ava_response = false;
                
                $sizes = get_intermediate_image_sizes();
                $avatars = array();
                if ($ava_response) {
                    foreach ($sizes as $size) {
                        $avatars[$size] = array(
                            $ava_response->data->url
                        );
                    }
                } else {
                    $avatars = false;
                }
                $data['name'] = str_replace(' ', '', sanitize_user($data['name']));
                $username = $data['name'];
                $params = array(
                    'user_login' => $username,
                    'user_email' => isset($data['email']) ? $data['email'] : false,
                    'description' => isset($data['bio']) ? $data['bio'] : false,
                    'user_location' => isset($data['location']) ? $data['location']['name'] : false,
                    'et_avatar' => $avatars,
                );
                //remove avatar if cant fetch avatar
                foreach ($params as $key => $param) {
                    if ($param == false) unset($params[$key]);
                }
                $_SESSION['et_auth'] = serialize($params);
                $_SESSION['et_social_id'] = $data['id'];
                $_SESSION['et_auth_type'] = 'facebook';
                $return['params'] = $params;
                $return['redirect_url'] = $this->auth_url;
            }
            $resp = array(
                'success'   => true,
                'msg'       => __('You have logged in successfully', ET_DOMAIN),
                'redirect'  => home_url(),
                'data'      => $return
            );
        }
        catch(Exception $e) {
            $resp = array(
                'success' => false,
                'msg' => $e->getMessage()
            );
        }
        wp_send_json($resp);
    }
}
class ET_GoogleAuth extends ET_SocialAuth
{
    private $state;
    private $gplus_secret_key;
    protected $gplus_client_id;
    protected $gplus_base_url;
    protected $gplus_exchange_url;
    protected $gplus_token_info_url;
    public function __construct() {
        parent::__construct('google', 'et_google_id', array(
            'title' => __('SIGN IN WITH GOOGLE+', ET_DOMAIN) ,
            'content' => __("This seems to be your first time signing in using your Google+ account.If you already have an account, please log in using the form below to link it to your Facebook account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.You will only do this step ONCE. Next time, you'll get logged in right away.", ET_DOMAIN) ,
            'content_confirm' => __("Please provide a username to continue", ET_DOMAIN)
        ));
        $this->add_ajax('ae_gplus_auth', 'ae_gplus_redirect');
        $this->gplus_client_id = ae_get_option('gplus_client_id');
        $this->gplus_secret_key = ae_get_option('gplus_secret_id');
        $this->gplus_base_url = 'https://accounts.google.com/o/oauth2/auth';
        $this->gplus_exchange_url = 'https://www.googleapis.com/oauth2/v3/token';
        $this->gplus_token_info_url = 'https://www.googleapis.com/oauth2/v1/userinfo';
        if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'gplus_auth_callback' ){
            if (!empty($this->gplus_client_id ) && !empty($this->gplus_secret_key) && !is_user_logged_in()){
                $this->auth_google();
            }
            else{
                _e( 'Please enter your Google client id and secret key in setting page!', ET_DOMAIN );
                exit();
            }
        }
    }
    // implement abstract method
    protected function send_created_mail($user_id) {
        do_action('et_after_register', $user_id);
    }
    public function ae_gplus_redirect(){
        try {
            // turn on session
            if (!isset($_SESSION)) {
                ob_start();
                @session_start();
            }
            $this->state = md5(uniqid());
            $redirect_uri = home_url('?action=gplus_auth_callback');
            $link = $this->gplus_base_url.'?';
            $link .= 'scope=https://www.googleapis.com/auth/plus.profile.emails.read  https://www.googleapis.com/auth/plus.login&';
            $link .= 'state='.$this->state.'&';
            $link .= 'redirect_uri='.$redirect_uri.'&';
            $link .= 'client_id='.$this->gplus_client_id.'&';
            $link .= 'response_type=code&';
            //$link .= 'access_type=online&';
            // $link .= 'approval_prompt=force';            
            $resp = array(
                'success'   => true,
                'msg'=> __( 'success', ET_DOMAIN ),
                'redirect'  => $link,
            );

        } catch (Exception $e) {

            $resp = array(
                'success'   => false,
                'msg'       => $e->getMessage()
            );

        }
        wp_send_json( $resp );
    }
    public function auth_google() {
        if( ( isset( $_REQUEST['code'] ) && !empty( $_REQUEST['code'] ) ) && ( isset( $_REQUEST['state'] ) || $_REQUEST['state'] == $this->state ) ){
            try {
                // turn on session
                if (!isset($_SESSION)) {
                    ob_start();
                    @session_start();
                }
                /**
                 * Exchange authorization code for tokens
                 */
                $redirect_uri = home_url('?action=gplus_auth_callback');
                $args = array(
                    'method' => 'POST',
                    'body' => array( 
                        'grant_type' => 'authorization_code', 
                        'code' => $_REQUEST['code'], 
                        'redirect_uri' =>  $redirect_uri,
                        'client_id' => $this->gplus_client_id,
                        'client_secret' => $this->gplus_secret_key
                        )
                    );
                $remote_post = wp_remote_post( $this->gplus_exchange_url, $args );                     
                if( isset( $remote_post ['body'] ) ){                    
                    $data = json_decode( $remote_post ['body'] );
                    if( isset($data->refresh_token) ){
                        $secure = ( 'https' === parse_url( site_url(), PHP_URL_SCHEME ) && 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
                        setcookie( 'refresh_token', $data->refresh_token, time() + 3600*24*7, SITECOOKIEPATH, COOKIE_DOMAIN, $secure );
                    }
                    if( isset( $data->error ) && $data->error == 'invalid_grant' ){
                        $args = array(
                            'method' => 'POST',
                            'body' => array( 
                                'grant_type' => 'refresh_token', 
                                'code' => $_REQUEST['code'], 
                                'redirect_uri' =>  $redirect_uri,
                                'client_id' => $this->gplus_client_id,
                                'client_secret' => $this->gplus_secret_key,
                                'refresh_token' => $_COOKIE['refresh_token']
                                )
                            );
                        $remote_post = wp_remote_post( $this->gplus_exchange_url, $args );  
                        $data = json_decode( $remote_post ['body'] ); 
                    }                    
                }
                else{                                       
                    _e( 'Error to connect to Google Server!', ET_DOMAIN );
                    exit();                    
                }
                /**
                 * Get user information
                 */  
                if( isset( $data->access_token ) ){
                    $userinfor = wp_remote_get( $this->gplus_token_info_url.'?access_token='.$data->access_token );
                    $userinfor = json_decode($userinfor['body']);
                }
                else{                   
                    _e( 'Error to connect to Google', ET_DOMAIN );
                    exit();
                }    
                if( !isset( $userinfor->id ) ||  empty( $userinfor->id ) ){                    
                    _e( 'Error to connect to Google Server!', ET_DOMAIN );
                    exit();
                }
                // if user is already authenticated before
                if ( $this->get_user( $userinfor->id ) ) {
                    $user = $this->get_user( $userinfor->id );
                    $result = $this->logged_user_in( $userinfor->id );
                    $ae_user = AE_Users::get_instance();
                    $userdata = $ae_user->convert( $user );
                    $nonce = array(
                        'reply_thread' => wp_create_nonce('insert_reply') ,
                        'upload_img' => wp_create_nonce('et_upload_images') ,
                    );
                }
                else {
                    // avatar
                    $ava_response = isset($userinfor->picture) ? $userinfor->picture : '';
                    $sizes = get_intermediate_image_sizes();
                    $avatars = array();
                    if ($ava_response) {
                        foreach ($sizes as $size) {
                            $avatars[$size] = array(
                                $ava_response
                            );
                        }
                    } else {
                        $avatars = false;
                    }  
                    $userinfor->name = str_replace(' ', '', sanitize_user( $userinfor->name ));
                    $username = $userinfor->name;
                    $params = array(
                        'user_login' => $username,
                        'user_email' => isset( $userinfor->email ) ? $userinfor->email : false,
                        'et_avatar' => $avatars
                    );
                    //remove avatar if cant fetch avatar
                    foreach ( $params as $key => $param ) {
                        if ( $param == false ){
                            unset($params[$key]);
                        }
                    }
                    $_SESSION['et_auth'] = serialize($params);
                    $_SESSION['et_social_id'] = $userinfor->id;
                    $_SESSION['et_auth_type'] = 'google';
                }
                header('Location: '.$this->auth_url);
                exit();
            }
            catch(Exception $e) {
                _e( 'Error to connect to Google Server', ET_DOMAIN );
                exit();
            }
        }
    }
}

/**
*	Linkedin authication
* 	@author Quang Ð?t
*/
class ET_LinkedInAuth extends ET_SocialAuth{
	private $state;
    private $linkedin_secret_key;
	protected $linkedin_api_key;
    protected $linkedin_base_url;
    protected $linkedin_token_url;
    protected $linkedin_people_url;
	public function __construct(){
		parent::__construct('linkedin', 'et_linkedin_id', array(
			'title'           => __('SIGN IN WITH LINKEDIN', ET_DOMAIN),
			'content'         => __("This seems to be your first time signing in using your LinkedIn account.If you already have an account, please log in using the form below to link it to your LinkedIn account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.You will only do this step ONCE. Next time, you'll get logged in right away.", ET_DOMAIN),
			'content_confirm' => __("Please provide a username to continue", ET_DOMAIN)
		));
        $this->state = md5( uniqid() );
		$this->add_ajax( 'ae_linked_auth', 'lkin_redirect' );
		$this->linkedin_api_key = ae_get_option('linkedin_api_key');
		$this->linkedin_secret_key = ae_get_option('linkedin_secret_key');
        $this->linkedin_base_url = 'https://www.linkedin.com/uas/oauth2/authorization';
        $this->linkedin_token_url = 'https://www.linkedin.com/uas/oauth2/accessToken';
        $this->linkedin_people_url = 'https://api.linkedin.com/v1/people/~:(id,location,picture-url,specialties,public-profile-url,email-address,formatted-name)?format=json';
        if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'linked_auth_callback' ){
            if ( !empty( $this->linkedin_api_key ) && !empty( $this->linkedin_secret_key ) && !is_user_logged_in() ){
                $this->linked_auth();
            }
            else{
                _e( 'Please enter your Linkedin App id and secret key!', ET_DOMAIN);
                exit();
            }   
        }
	}
	// implement abstract method
	protected function send_created_mail($user_id){
		do_action('et_after_register', $user_id);
	}
	/**
	 * When user click login button Linkedin, it will execution function bellow
	 * @return $link
	 */
	public function lkin_redirect(){
		try {
			// turn on session
			if (!isset($_SESSION)) {
			    ob_start();
			    @session_start();
			}
            /**
             * Step1: Request an Authorization Code
             */
			$redirect_uri = home_url('?action=linked_auth_callback');
			$link = $this->linkedin_base_url.'?';
            $link .= 'response_type=code&';
            $link .= 'client_id='.$this->linkedin_api_key.'&';
            $link .= 'redirect_uri='.$redirect_uri.'&';
            $link .= 'state='.$this->state.'&';
            /* $link .= 'scope=r_fullprofile r_emailaddress'; */			            $link .= 'scope=r_basicprofile r_emailaddress';
			// wp_set_auth_cookie($link);
			$resp = array(
				'success' 	=> true,
                'msg' => 'Success',
				'redirect' 	=> $link
			);
		} catch (Exception $e) {
			$resp = array(
				'success' 	=> false,
				'msg' 		=> $e->getMessage()
			);

		}
		wp_send_json($resp);
	}
	/**
	 * function handle after linkedin callback
	 */
	public function linked_auth(){
        if( ( isset( $_REQUEST['code'] ) && !empty( $_REQUEST['code'] ) ) && ( isset( $_REQUEST['state'] ) || $_REQUEST['state'] == $this->state ) ){
            try {
    			/**
    			 * Step2: Exchange Authorization Code for a Request Token
    			 */
    			$request = $_REQUEST;
    			$redirect_uri = home_url('?action=linked_auth_callback');
    			$args = array(
    				'method' => 'POST',
    				'timeout' => 45,
    				'redirection' => 5,
    				'httpversion' => '1.0',
    				'blocking' => true,
    				'headers' => array(),
    				'body' => array( 
    					'grant_type' => 'authorization_code', 
    					'code' => $request['code'], 
    					'redirect_uri' => $redirect_uri,
    					'client_id' => $this->linkedin_api_key,
    					'client_secret' => $this->linkedin_secret_key),
    				'cookies' => array()
    				);
    			$remote_post = wp_remote_post( $this->linkedin_token_url, $args );
                if( isset( $remote_post ['body'] ) && !empty( $remote_post ['body'] ) ){
        			$data = json_decode( $remote_post ['body'] );                  
                }
                else{
                    _e( 'Error to connect to Linkedin server!', ET_DOMAIN );
                    exit();
                }
                if( !isset( $data->access_token ) || empty( $data->access_token ) ){
                    _e( 'Can not get the access token from Linkedin server!', ET_DOMAIN );
                    exit();   
                }
    			/**
    			 * Step3: Make authenticated requests and get user's informations
    			 */
    			$args1 = array( 
    				'timeout' => 120, 
    				'httpversion' => '1.1', 
    				'headers'     => array(
    					'Authorization' => 'Bearer '.$data->access_token )
    				);
    			$remote_get = wp_remote_get( $this->linkedin_people_url, $args1 );
                if( isset( $remote_get['body'] ) && !empty( $remote_get['body'] ) ){
    			 $data_user = json_decode($remote_get['body']);
                }
                else{
                    _e( 'Error to connect to Linkedin server2!', ET_DOMAIN );
                    exit();
                }
                if( !isset( $data_user->id ) || empty( $data_user->id ) ){
                    _e( 'Can not get user information from Linkedin server!', ET_DOMAIN );
                    exit();   
                }
                // if user is already authenticated before
                if ( $this->get_user( $data_user->id ) ) {
                    $user = $this->get_user( $data_user->id );
                    $result = $this->logged_user_in( $data_user->id );
                    $ae_user = AE_Users::get_instance();
                    $userdata = $ae_user->convert( $user );
                    $nonce = array(
                        'reply_thread' => wp_create_nonce('insert_reply') ,
                        'upload_img' => wp_create_nonce('et_upload_images') ,
                    );
                }
                else {
                    // avatar
                    $ava_response = isset( $data_user->pictureUrl ) ? $data_user->pictureUrl : '';
                    $sizes = get_intermediate_image_sizes();
                    $avatars = array();
                    if ($ava_response) {
                        foreach ($sizes as $size) {
                            $avatars[$size] = array(
                                $ava_response
                            );
                        }
                    } else {
                        $avatars = false;
                    }  
                    $data_user->formattedName = str_replace(' ', '', sanitize_user( $data_user->formattedName ) );
                    $username = $data_user->formattedName;
                    $params = array(
                        'user_login' => $username,
                        'user_email' => isset( $data_user->emailAddress ) ? $data_user->emailAddress : false,
                        'et_avatar' => $avatars
                    );
                    //remove avatar if cant fetch avatar
                    foreach ( $params as $key => $param ) {
                        if ( $param == false ){
                            unset($params[$key]);
                        }
                    }
                    // turn on session
                    if (!isset($_SESSION)) {
                        ob_start();
                        @session_start();
                    }
                    /**
                     * set value into session for save later
                     *
                     */
                    $_SESSION['et_auth'] = serialize( $params );
                    $_SESSION['et_social_id'] = $data_user->id;
                    $_SESSION['et_auth_type'] = 'linkedin';
    			}
                header('Location: '.$this->auth_url);
                exit();
            }catch(Exception $e) {
                _e( 'Error to connect to Linkedin server', ET_DOMAIN );
                exit();
            }
        }
	}
}
?>