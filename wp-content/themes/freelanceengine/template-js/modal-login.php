<div class="modal fade" id="modal_login">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Welcome back!", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
				<form role="form" id="signin_form" class="auth-form signin_form">
					<div class="form-group">
						<label for="login_user_login"><?php _e('Your User Name or Email', ET_DOMAIN) ?></label>
						<input type="text" class="form-control" id="login_user_login" name="user_login" placeholder="<?php _e('Enter username', ET_DOMAIN) ?>">
					</div>
					<div class="form-group">
						<label for="login_user_pass"><?php _e('Your Password', ET_DOMAIN) ?></label>
						<input type="password" class="form-control" id="login_user_pass" name="user_pass" placeholder="<?php _e('Password', ET_DOMAIN) ?>">
					</div>		
                    <div class="clearfix"></div>
					<button type="submit" class="btn-submit btn-sumary btn-sub-create">
						<?php _e('Sign in', ET_DOMAIN) ?>
					</button>
                    <a class="show-forgot-form" href="#"><?php _e("Forgot Password?", ET_DOMAIN) ?></a>
                        <?php
			                if( function_exists('ae_render_social_button')){
			                    $before_string = __("You can also sign in by:", ET_DOMAIN);
			                    ae_render_social_button( array(), array(), $before_string ); 
			                }
			            ?>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog login -->
</div><!-- /.modal -->