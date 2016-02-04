<?php
/**
 * Template Name: Authenticate Template
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */

	global $post;
	get_header();
	the_post();
?>

<section class="blog-header-container">
	<div class="container">
		<!-- blog header -->
		<div class="row">
		    <div class="col-md-12 blog-classic-top">
		        <h2><?php the_title(); ?></h2>
		    </div>
		</div>      
		<!--// blog header  -->	
	</div>
</section>

<div class="container">
	<!-- block control  -->
	<div class="row block-posts block-page">
		<div class="col-md-9 col-sm-12 col-xs-12 posts-container" id="left_content">
			<div class="step-content-wrapper content page-authenticate">
			    <div class="tab-content">
			        <div class="tab-pane fade " id="signup">
			        	<div class="text-intro-acc">
			        		<?php _e('Already have an account?', ET_DOMAIN) ?>&nbsp;&nbsp;<a href="#signin" role="tab" data-toggle="tab"><?php _e('Login', ET_DOMAIN); ?></a>
			            </div>
                        <form id="user_signup_form" class="auth-form signup_form">
				            <input type="hidden" value="<?php _e("Work", ET_DOMAIN); ?>" class="work-text" name="worktext" />
							<input type="hidden" value="<?php _e("Hire", ET_DOMAIN); ?>" class="hide-text" name="hidetext" />

			                <div class="form-group">
			                	<div class="row">
			                    	<div class="col-md-4">
			                        	<label class="control-label title-plan" for="user_login"><?php _e('Username', ET_DOMAIN) ?><span><?php _e('Enter username', ET_DOMAIN) ?></span></label>
			                        </div>
			                        <div class="col-sm-8">
			                            <input type="text" class="form-control text-field" id="user_login" name="user_login" placeholder="<?php _e("Enter username", ET_DOMAIN); ?>">
			                        </div>
			                    </div>
			                </div>
                            <div class="clearfix"></div>
			                <div class="form-group">
			                	<div class="row">
			                    	<div class="col-md-4">
			                        	<label class="control-label title-plan" for="register_user_email"><?php _e('Email address', ET_DOMAIN) ?><span><?php _e('Enter a email', ET_DOMAIN) ?></span></label>
			                        </div>
			                        <div class="col-sm-8">
			                            <input type="email" class="form-control text-field" id="register_user_email" name="user_email" placeholder="<?php _e("Your email address", ET_DOMAIN); ?>">
			                        </div>
			                    </div>
			                </div>
                            <div class="clearfix"></div>
			                <div class="form-group">
			                	<div class="row">
			                    	<div class="col-md-4">
			                        	<label class="control-label title-plan" for="register_user_email"><?php _e('I\'m looking to:', ET_DOMAIN) ?><span><?php _e('Choose type account', ET_DOMAIN) ?></span></label>
			                        </div>
			                        <div class="col-sm-8">
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
                            <div class="clearfix"></div>
			                <div class="form-group">
			                	<div class="row">
			                    	<div class="col-md-4">
			                        	<label class="control-label title-plan" for="register_user_pass"><?php _e('Password', ET_DOMAIN) ?><span><?php _e('Enter password', ET_DOMAIN) ?></span></label>
			                        </div>
			                        <div class="col-sm-8">
			                            <input type="password" class="form-control text-field" id="register_user_pass" name="user_pass" placeholder="<?php _e('Password', ET_DOMAIN);?>">
			                        </div>
			                    </div>
			                </div>
                            <div class="clearfix"></div>
			                <div class="form-group">
			                	<div class="row">
			                    	<div class="col-md-4">
			                        	<label class="control-label title-plan" for="repeat_pass"><?php _e('Retype Password', ET_DOMAIN) ?><span><?php _e('Retype password', ET_DOMAIN) ?></span></label>
			                        </div>
			                        <div class="col-sm-8">
			                            <input type="password" class="form-control text-field" id="repeat_pass" name="repeat_pass" placeholder="<?php _e('Password', ET_DOMAIN);?>">
			                        </div>
			                    </div>
			                </div>    
                            <div class="clearfix"></div>
                            <?php if(get_theme_mod( 'termofuse_checkbox', false )){ ?>
							<div class="form-group policy-agreement">
		                        <div class="row">
		                            <div class="col-md-offset-4 col-md-8" >
		                                <input name="agreement" id="agreement" type="checkbox" />
		                                <?php printf(__('I agree with the <a href="%s">Term of Use and Privacy policy</a>', ET_DOMAIN), et_get_page_link('tos') ); ?>
		                            </div>
		                        </div>
		                    </div>  
		                    <div class="clearfix"></div>	
		                    <?php } ?>
			                <div class="form-group">
			                	<div class="row">
			                        <div class="col-md-4">
			                        </div>
			                        <div class="col-sm-8">
			                            <button type="submit" class="btn btn-submit btn-submit-login-form">
			                                <?php _e('Create account', ET_DOMAIN) ?>
			                            </button>
			                            <p class="text-term">
											<?php
							                /**
							                 * tos agreement
							                */
							                $tos = et_get_page_link('tos', array() ,false);
							                if(!get_theme_mod( 'termofuse_checkbox', false ) && $tos) { ?>
							                    <?php 
							                        printf(__('By creating an account, you agree to our <a href="%s">Term of Use and Privacy policy</a>', ET_DOMAIN), et_get_page_link('tos') );
							                    ?>
							                <?php } ?>
										</p>
			                        </div>
			                    </div>
			                </div>       
			            </form> 
			        </div>
			        <div class="tab-pane fade in active" id="signin">
			            <div class="text-intro-acc">
			        		<?php _e('You do not have an account?', ET_DOMAIN) ?>&nbsp;&nbsp;<a href="#signup" role="tab" data-toggle="tab"><?php _e('Register', ET_DOMAIN) ?></a>
			            </div>
                        <form id="user_signin_form" class="auth-form signin_form">
			                <div class="form-group">
			                	<div class="row">
			                    	<div class="col-md-4">
			                        	<label class="control-label title-plan" for="user_login"><?php _e('Username', ET_DOMAIN) ?><span><?php _e('Enter Username', ET_DOMAIN) ?></span></label>
			                        </div>
			                        <div class="col-sm-8">
			                            <input type="text" class="form-control text-field" id="login_user_login" name="user_login" placeholder="<?php _e('Enter username', ET_DOMAIN);?>">
			                        </div>
			                    </div>
			                </div>
                            <div class="clearfix"></div>
			                <div class="form-group">
			                	<div class="row">
			                    	<div class="col-md-4">
			                        	<label class="control-label title-plan" for="user_pass"><?php _e('Password', ET_DOMAIN) ?><span><?php _e('Enter Password', ET_DOMAIN) ?></span></label>
			                        </div>
			                        <div class="col-sm-8">
			                            <input type="password" class="form-control text-field" id="login_user_pass" name="user_pass" placeholder="<?php _e('Password', ET_DOMAIN);?>">
			                        </div>
			                    </div>
			                </div>
                            <div class="clearfix"></div>  
			                <div class="form-group">
			                	<div class="row">
			                    	<div class="col-md-4">
			                        </div>
			                        <div class="col-sm-8">
			                            <button type="submit" class="btn btn-submit btn-submit-login-form">
			                                <?php _e('Submit', ET_DOMAIN) ?>
			                            </button>
						                    <?php
					                            if( function_exists('ae_render_social_button')){
					                            	$before_string = __("You can also sign in by:", ET_DOMAIN);
					                                ae_render_social_button( array(), array(), $before_string ); 
					                            }
						                    ?>
			                        </div>
			                    </div>
			                </div>    
			            </form> 
			        </div>
			    </div>
			</div>
		</div><!-- LEFT CONTENT -->
		<div class="col-md-3 col-sm-12 col-xs-12 page-sidebar" id="right_content">
			<?php get_sidebar('page'); ?>
		</div><!-- RIGHT CONTENT -->
	</div>
	<!--// block control  -->
</div>
<?php
	get_footer();
?>