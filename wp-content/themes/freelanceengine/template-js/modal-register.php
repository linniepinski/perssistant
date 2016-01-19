<div class="modal fade" id="modal_register">
	<input type="hidden" value="<?php _e("Work", ET_DOMAIN); ?>" class="work-text" name="worktext" />
	<input type="hidden" value="<?php _e("Hire", ET_DOMAIN); ?>" class="hide-text" name="hidetext" />

	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>				
				<h4 class="modal-title"><?php _e("Become our member!", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
				<form role="form" id="signup_form" class="auth-form signup_form">
                	<p class="user-type">
                		<?php _e("What are you looking for?", ET_DOMAIN) ?> 

                            <input type="checkbox" class="sign-up-switch" name="modal-check"/>
                            <span class="user-role text work">
                                <?php _e("Work", ET_DOMAIN); ?>
                            </span>
                	</p>
                	<input type="hidden" name="role" id="role" value="freelancer" />
					<div class="form-group">
						<label for="user_login"><?php _e('Username', ET_DOMAIN) ?></label>
						<input type="text" class="form-control" id="user_login" name="user_login" placeholder="<?php _e("Enter username", ET_DOMAIN) ?>">
					</div>
					<div class="form-group">
						<label for="register_user_email"><?php _e('Email address', ET_DOMAIN) ?></label>
						<input type="email" class="form-control" id="register_user_email" name="user_email" placeholder="<?php _e("Enter email", ET_DOMAIN) ?>">
					</div>
					<div class="form-group">
						<label for="register_user_pass"><?php _e('Password', ET_DOMAIN) ?></label>
						<input type="password" class="form-control" id="register_user_pass" name="user_pass" placeholder="<?php _e("Password", ET_DOMAIN) ?>">
					</div>
					<div class="form-group">
						<label for="repeat_pass"><?php _e('Retype Password', ET_DOMAIN) ?></label>
						<input type="password" class="form-control" id="repeat_pass" name="repeat_pass" placeholder="<?php _e("Retype password", ET_DOMAIN) ?>">
					</div>	
					<div class="clearfix"></div>
					<?php if(get_theme_mod( 'termofuse_checkbox', false )){ ?>
					<div class="form-group policy-agreement">
						<input name="agreement" id="agreement" type="checkbox" />
						<?php printf(__('I agree with the <a href="%s">Term of Use and Privacy policy</a>', ET_DOMAIN), et_get_page_link('tos') ); ?>
					</div>	
                    <div class="clearfix"></div>	
                    <?php } ?>
					<button type="submit" class="btn-submit btn-sumary btn-sub-create">
						<?php _e('Sign up', ET_DOMAIN) ?>
					</button>
					<?php if(!get_theme_mod( 'termofuse_checkbox', false )){ ?>
					<p class="text-term">
						<?php
		                /**
		                 * tos agreement
		                */
		                $tos = et_get_page_link('tos', array() ,false);
		                if($tos) { ?>
		                    <?php printf(__('By creating an account, you agree to our <a href="%s">Term of Use and Privacy policy</a>', ET_DOMAIN), et_get_page_link('tos') ); ?>
		                <?php } ?>
					</p>
					<?php } 
		                if( function_exists('ae_render_social_button')){
		                    $before_string = __("You can also sign in by:", ET_DOMAIN);
		                    ae_render_social_button( array(), array(), $before_string ); 
		                }
		            ?>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog register -->
</div><!-- /.modal -->