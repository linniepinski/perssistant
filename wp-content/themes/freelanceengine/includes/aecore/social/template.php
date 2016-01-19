<?php
/**
 * Generate social connect page template;
 */
function ae_page_social_connect(){

	global $wp_query, $wp_rewrite, $post, $et_data;
	if (!isset($_SESSION)) {
	    ob_start();
	    @session_start();
	}
	$labels = $et_data['auth_labels'];
	if(isset($_SESSION['et_auth']) && $_SESSION['et_auth'] != ''){
		$auth = unserialize($_SESSION['et_auth']);
	}
	else{
		wp_redirect(home_url());
	}
	$type = isset($_GET['type']) ? $_GET['type'] : '';
	?>
	<div class="twitter-auth social-auth social-auth-step1">
	<?php
	if($type == 'facebook'){ ?>
	    <p class="text-page-not social-big"><?php _e('SIGN IN WITH FACEBOOK',ET_DOMAIN);?></p>
	    <p class="social-small">
	    <?php
	         printf(__("This seems to be your first time signing in using your Facebook account. <br />If you already have an account with %s, please log in using the form below to link it to your Facebook account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.<br />You will only do this step ONCE. Next time, you'll get logged in right away.", ET_DOMAIN), get_bloginfo('name') );?>
	    </p>
	    <?php
	} else if ($type == 'twitter'){ ?>
	    <p class="text-page-not social-big"><?php _e('SIGN IN WITH TWITTER',ET_DOMAIN);?></p>
	    <p class="social-small">
	        <?php
	        printf(__("This seems to be your first time signing in using your Twitter account.<br />If you already have an account with %s, please log in using the form below to link it to your Twitter account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.<br > You will only do this step ONCE. Next time, you'll get logged in right away.</p>", ET_DOMAIN), get_bloginfo('name') );
	    ?>
	    </p>

	<?php } else if ($type == 'google'){    ?>
		<p class="text-page-not social-big"><?php _e('SIGN IN WITH GOOGLE+',ET_DOMAIN);?></p>
	    <p class="social-small">
	        <?php
	        printf(__("This seems to be your first time signing in using your Google+ account.If you already have an account, please log in using the form below to link it to your Google+  account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.You will only do this step ONCE. Next time, you'll get logged in right away.</p>", ET_DOMAIN), get_bloginfo('name') );
	    ?>
		</p>
	<?php } else if ($type == 'linkedin'){    ?>
		<p class="text-page-not social-big"><?php _e('SIGN IN WITH LINKEDIN',ET_DOMAIN);?></p>
	    <p class="social-small">
	        <?php
	        printf(__("This seems to be your first time signing in using your Linkedin account.If you already have an account, please log in using the form below to link it to your Linkedin  account. Otherwise, please enter an email address and a password on the form, and a username on the next page to create an account.You will only do this step ONCE. Next time, you'll get logged in right away.</p>", ET_DOMAIN), get_bloginfo('name') );
	    ?>
		</p>
	<?php } ?>
	    <form id="form_auth" method="post" action="">
	        <div class="social-form">
	            <input type="hidden" name="et_nonce" value="<?php echo wp_create_nonce( 'authentication' ) ?>">
	            <input type="text" name="user_email" value="<?php if(isset($_SESSION['user_email'])) echo $_SESSION['user_email']; ?>"  placeholder="<?php _e('Email', ET_DOMAIN) ?>">
	            <input type="password" name="user_pass"  placeholder="<?php _e('Password', ET_DOMAIN) ?>">
	            <input type="submit" value="Submit">
	        </div>
	    </form>
	</div>
	<div class="social-auth social-auth-step2">
	    <p class="text-page-not social-big"><?php echo $labels['title'] ?></p>
	    <p class="social-small"><?php _e('Please provide a username to continue',ET_DOMAIN);?></p>
	    <form id="form_username" method="post" action="">
	        <div class="social-form">
	            <input type="hidden" name="et_nonce" value="<?php echo wp_create_nonce( 'authentication' ) ?>">
	            <input type="text" name="user_login" value="<?php echo isset($auth['user_login']) ? $auth['user_login'] : "" ?>" placeholder="<?php _e('Username', ET_DOMAIN) ?>">
	            <?php $social_user_roles = ae_get_option('social_user_role', false); 
	            if( !$social_user_roles){
		            $social_user_roles = ae_get_social_login_user_roles_default();
	            }
	            if( $social_user_roles && count( $social_user_roles ) >= 1 ){?>
	            	  <select name="user_role" class="sc_user_role">
	            	  	<?php foreach ($social_user_roles as $key => $value) { ?>
		            	<option value="<?php echo $value ?>"><?php echo $value; ?></option>
		           		<?php } ?>
	            	  </select>
	            <?php } ?>
	            <input type="submit" value="Submit">
	        </div>
	    </form>
	</div>
<?php
}
/**
 *Generate short code phot social connect page
 */
function ae_social_connect_page_shortcode() {
	return ae_page_social_connect();
}
add_shortcode( 'social_connect_page', 'ae_social_connect_page_shortcode' );
/**
 * init social login feature 
 */
function init_social_login(){
		if(ae_get_option('twitter_login', false))
			new ET_TwitterAuth();
		if(ae_get_option('facebook_login', false)){
			new ET_FaceAuth();
		}
		if(ae_get_option('gplus_login', false)){
			new ET_GoogleAuth();
		}
		if(ae_get_option('linkedin_login', false)){
			new ET_LinkedInAuth();
		}
}
/**
 *get user for social login
 */
function ae_social_auth_support_role() {
    $default = array('author'=> 'author', 'subscriber'=>'subscriber', 'editor'=>'editor', 'contributor'=>'contributor');
    return apply_filters('ae_social_auth_support_role', $default);
}
/**
 *Render the social login button
 *
 *@param array $icon_classes are css classes for displaying social buttons
 *@param array $button_classes are css classes for displaying social buttons
 *@param string $before_text are text display before social login buttons
 *@param string $after_text are text display after social login buttons
 *@since version 1.8.4 of DE
 *
 */
function ae_render_social_button( $icon_classes = array(), $button_classes = array(), $before_text = '', $after_text = '' ){ 
	/* check enable option*/
	$use_facebook = ae_get_option('facebook_login');
    $use_twitter = ae_get_option('twitter_login');
    $gplus_login = ae_get_option('gplus_login');
    $linkedin_login = ae_get_option('linkedin_login') ;
    if( $icon_classes == ''){
    	$icon_classes = 'fa fa-facebook-square';
    }
    $defaults_icon = array(
    	'fb' => 'fa fa-facebook',
    	'gplus' => 'fa fa-google-plus',
    	'tw' => 'fa fa-twitter',
    	'lkin' => 'fa fa-linkedin'
    	);
	$icon_classes = wp_parse_args( $icon_classes, $defaults_icon );
	$icon_classes = apply_filters('ae_social_icon_classes', $icon_classes );
	$defaults_btn = array(
    	'fb' => '',
    	'gplus' => '',
    	'tw' => '',
    	'lkin' => ''
    	);
	$button_classes = wp_parse_args( $button_classes, $defaults_btn );
	$button_classes = apply_filters('ae_social_button_classes', $button_classes );
	if( $use_facebook || $use_twitter || $gplus_login || $linkedin_login ){
		if( $before_text != '' ){ ?>
			<div class="socials-head"><?php echo $before_text ?></div>
		<?php } ?>
		<ul class="list-social-login">
			<?php if($use_facebook){ ?>
	    	<li>
	    		<a href="#" class="fb facebook_auth_btn <?php echo $button_classes['fb']; ?>">
	    			<i class="<?php echo $icon_classes['fb']; ?>"></i>
	    			<span class="social-text"><?php _e("Facebook", ET_DOMAIN) ?></span>
	    		</a>
	    	</li>
	    	<?php } ?>
	    	<?php if($gplus_login){ ?>
	        <li>
	        	<a href="#" class="gplus gplus_login_btn <?php echo $button_classes['gplus']; ?>" >
	        		<i class="<?php echo $icon_classes['gplus']; ?>"></i>
	        		<span class="social-text"><?php _e("Plus", ET_DOMAIN) ?></span>
	        	</a>
	        </li>
	        <?php } ?>
	    	<?php if($use_twitter){ ?>
	        <li>
	        	<a href="<?php echo add_query_arg('action', 'twitterauth', home_url()) ?>" class="tw <?php echo $button_classes['tw']; ?>">
	        		<i class="<?php echo $icon_classes['tw']; ?>"></i>
	        		<span class="social-text"><?php _e("Twitter", ET_DOMAIN) ?></span>
	        	</a>
	        </li>
	        <?php } ?>
	        <?php if($linkedin_login){ ?>
			<li>
	    		<a href="#" class="lkin <?php echo $button_classes['tw']; ?>">
	    			<i class="<?php echo $icon_classes['lkin']; ?>"></i>
	    			<span class="social-text"><?php _e("Linkedin", ET_DOMAIN) ?></span>
	    		</a>
	    	</li>
			<?php } ?>
	    </ul> 
	<?php 
		if( $after_text != '' ){ ?>
			<div class="socials-footer"><?php echo $after_text ?></div>
		<?php } 
	}
}