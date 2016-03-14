<?php 
/**
 * 
*/
global $user_ID;

if($user_ID) {

    // isset redirect url

    if(isset($_REQUEST['redirect'])) {

        wp_redirect($_REQUEST['redirect']);

        exit;

    }

    wp_redirect(home_url());

    exit;

}
	et_get_mobile_header('auth');
?>
<div class="container">
    <div class="row">
        <div class="col-md-12 mobile-logo-wrapper">
            <a href="<?php echo home_url(); ?>" class="logo-mobile">
                <?php ae_mobile_logo(); ?>
            </a>
        </div>
    </div>
</div>

<section class="section-wrapper section-register">
<!--    <form class="form-mobile-wrapper signup_form">-->
    <form id="user_signup_form" class="auth-form signup_form">
        <input type="hidden" value="<?php _e("Work", ET_DOMAIN); ?>" class="work-text" name="worktext" />
        <input type="hidden" value="<?php _e("Hire", ET_DOMAIN); ?>" class="hide-text" name="hidetext" />

    	<div class="container">
            <div class="row">
                <div class="col-xs-7">
                    <span class="text-choose">
                        <?php _e("What are you looking for?", ET_DOMAIN)?>
                    </span>
                </div>
                <div class="col-xs-5">
                    <span class="user-type">
                        <input type="hidden" name="role" id="role" value="freelancer" />
                        <input type="checkbox" class="sign-up-switch" name="modal-check" data-switchery="true" style="display: none;">
                        <span class="user-role text work">
                            <?php _e("Work", ET_DOMAIN); ?>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    
    	<div class="form-group-mobile">
        	<span class="icon-form-login icon-user"></span>
        	<input type="text" id="user_login" name="user_login" placeholder="<?php _e("Username", ET_DOMAIN); ?>">
        </div>
        <div class="form-group-mobile">
        	<span class="icon-form-login icon-email"></span>
        	<input type="email" id="register_user_email" name="user_email" placeholder="<?php _e("Your Email", ET_DOMAIN); ?>">
        </div>
        <div class="form-group-mobile">
        	<span class="icon-form-login icon-key"></span>
        	<input type="password" id="register_user_pass" name="user_pass" placeholder="<?php _e("Your Password", ET_DOMAIN); ?>">
        </div>
        <div class="form-group-mobile">
        <div id="pswd_info" style="display: none">
            <h4>Security level: <strong class="strong-level">danger</strong></h4>
            <h4>Password must meet the following requirements:</h4>
            <ul>
                <li id="letter" class="invalid">At least <strong>one letter</strong></li>
                <li id="capital" class="invalid">At least <strong>one capital letter</strong></li>
                <li id="number" class="invalid">At least <strong>one number</strong></li>
                <li id="length" class="invalid">Be at least <strong>8 characters</strong></li>
            </ul>
        </div>
            </div>
        <div class="form-group-mobile">
        	<span class="icon-form-login icon-key"></span>
        	<input type="password" id="repeat_pass" name="repeat_pass" placeholder="<?php _e("Retype Password", ET_DOMAIN); ?>">
        </div>
        <div class="form-group-mobile captcha">
            <?php if( function_exists( 'cptch_display_captcha' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha(); }?>
        </div>
        <?php if(get_theme_mod( 'termofuse_checkbox', false )){ ?>
        <div class="form-group policy-agreement">
            <input name="agreement" id="agreement" type="checkbox" />
            <?php printf(__('I agree with the <a href="%s">Term of Use and Privacy policy</a>', ET_DOMAIN), et_get_page_link('tos') ); ?>
        </div>  
        <?php } ?>
        <div class="clearfix"></div>    
        <div class="form-group-mobile form-submit-btn">
            <button class="btn-sumary btn-submit"><?php _e("SIGN UP", ET_DOMAIN); ?></button>
        </div>
    </form>
    <div class="container">
    	<div class="row">
        	<div class="col-md-12">
            <?php
                /**
                 * tos agreement
                */
                $tos = et_get_page_link('tos', array() ,false);
                if(!get_theme_mod( 'termofuse_checkbox', false ) && $tos) {
            ?>
            	<p class="text-policy">
                    <?php printf(__('By creating an account, you agree to our <a href="%s">Term of Use and Privacy policy</a>', ET_DOMAIN), et_get_page_link('tos') ); ?>
                </p>
            <?php 
                }
            ?>
                <a href="#" class="change-link-login">
                    <?php _e("You have account ? Click here !", ET_DOMAIN); ?>
                </a>
          <?php
                if( function_exists('ae_render_social_button')){
                    $before_string = __("You can also sign in by:", ET_DOMAIN);
                    ae_render_social_button( array(), array(), $before_string ); 
                }
            ?>
            </div>
        </div>
    </div>
</section>  

<section class="section-wrapper section-login">
    <form id="user_signin_form" class="auth-form signin_form">
    	<div class="container">
            <div class="row">
                <div class="col-md-12">
                    <span class="text-choose"></span>
                </div>
            </div>
        </div>
    
    	<div class="form-group-mobile">
        	<span class="icon-form-login icon-user"></span>
        	<input type="text" id="login_user_login" name="user_login" placeholder="<?php _e("Username", ET_DOMAIN); ?>">
        </div>
        <div class="form-group-mobile">
        	<span class="icon-form-login icon-key"></span>
        	<input type="password" id="login_user_pass" name="user_pass" placeholder="<?php _e("Your Password", ET_DOMAIN); ?>">
        </div>
        <div class="form-group-mobile captcha">
            <?php if( function_exists( 'cptch_display_captcha' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha(); }?>
        </div>
        <div class="form-group-mobile form-submit-btn">
            <a href="#" class="forgot-link change-link-forgot"><?php _e("Forgot your password?", ET_DOMAIN); ?></a>
            <div class="clearfix"></div>            
        	<button class="btn-sumary btn-submit"><?php _e("SIGN IN", ET_DOMAIN); ?></button>
        </div>
    </form>
    <div class="container">
    	<div class="row">
        	<div class="col-md-12 change-form">
            	<p class="text-policy"></p>
                <a href="#" class="change-link-register"><?php _e("New? Click here to become a member", ET_DOMAIN); ?></a>

		            <div class="socials-head"><?php _e("You can also sign in by:", ET_DOMAIN) ?></div>
		            <?php do_action( 'wordpress_social_login' ); ?>
				        <style>
					        .wp-social-login-connect-with{
						        display: none;
					        }
				        </style>
<!--                --><?php //
//                $use_facebook = ae_get_option('facebook_login');
//                $use_twitter = ae_get_option('twitter_login');
//                $gplus_login = ae_get_option('gplus_login');
//                $linkedin_login = ae_get_option('linkedin_login') ;
//                if($linkedin_login || $use_facebook || $use_twitter || $gplus_login) {
//                ?>
<!--                    <div class="socials-head">--><?php //_e("You can also sign in by:", ET_DOMAIN) ?><!--</div>-->
<!--                    <ul class="list-social-login">-->
<!--                        --><?php //if($use_facebook){ ?>
<!--                        <li>-->
<!--                            <a href="#" class="fb facebook_auth_btn">-->
<!--                                <i class="fa fa-facebook"></i>--><?php //_e("Facebook", ET_DOMAIN) ?>
<!--                            </a>-->
<!--                        </li>-->
<!--                        --><?php //} ?>
<!--                        --><?php //if($use_twitter){ ?>
<!--                        <li>-->
<!--                            <a href="--><?php //echo add_query_arg('action', 'twitterauth', home_url()) ?><!--" class="tw">-->
<!--                                <i class="fa fa-twitter"></i>--><?php //_e("Twitter", ET_DOMAIN) ?>
<!--                            </a>-->
<!--                        </li>-->
<!--                        --><?php //} ?>
<!--                        --><?php //if($gplus_login){ ?>
<!--                        <li>-->
<!--                            <a href="#" class="gplus gplus_login_btn">-->
<!--                                <i class="fa fa-google-plus"></i>--><?php //_e("Plus", ET_DOMAIN) ?>
<!--                            </a>-->
<!--                        </li>-->
<!--                        --><?php //} ?>
<!--                        --><?php //if($linkedin_login){ ?>
<!--                        <li>-->
<!--                            <a href="#" class="lkin">-->
<!--                                <i class="fa fa-linkedin"></i>--><?php //_e("Linkedin", ET_DOMAIN) ?>
<!--                            </a>-->
<!--                        </li>-->
<!--                        --><?php //} ?>
<!--                    </ul> -->
<!--                --><?php //} ?>
            </div>
        </div>
    </div>
</section>   

<section class="section-wrapper section-forgot collapse">
    <form class="form-mobile-wrapper forgot_form" id="forgot_form">
    	<div class="container">
            <div class="row">
                <div class="col-md-12">
                    <span class="text-choose"></span>
                </div>
            </div>
        </div>
    
    	<div class="form-group-mobile">
        	<span class="icon-form-login icon-email"></span>
            <input type="text" id="user_email" name="user_email" placeholder="<?php _e("Enter username or email", ET_DOMAIN) ?>">
        </div>
<div class="form-group-mobile captcha">
            <?php if( function_exists( 'cptch_display_captcha' ) ) { echo cptch_display_captcha(); }?>
        </div>
        <div class="form-group-mobile">
        	<a href="#" class="forgot-link change-link-login"><?php _e("Login Your Account", ET_DOMAIN); ?></a>
        </div>
        <div class="form-group-mobile form-submit-btn">
        	<button class="btn-sumary btn-submit"><?php _e("SUBMIT", ET_DOMAIN); ?></button>
        </div>
    </form>
    <div class="container">
    	<div class="row">
        	<div class="col-md-12">
            	<p class="text-policy"></p>
                <a href="#" class="change-link-register"><?php _e("New? Click here to become a member", ET_DOMAIN); ?></a>
            </div>
        </div>
    </div>
</section> 

<?php
	et_get_mobile_footer();
?>
