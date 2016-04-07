<div class="modal fade" id="modal_forgot">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Forgot Password?", 'modal-forgot-password') ?></h4>
			</div>
			<div class="modal-body">
				<form id="forgot_form" class="auth-form forgot_form">
					<div class="form-group">
						<label for="forgot_user_email"><?php _e('Enter your email here', 'modal-forgot-password') ?></label>
						<input type="text" class="form-control" id="user_email" name="user_email" />
					</div>
					<div class="clearfix"></div>
					<?php if( function_exists( 'cptch_display_captcha' ) ) { echo "<input type='hidden' name='cntctfrm_contact_action' value='true' />"; echo cptch_display_captcha(); }?>
					<div class="clearfix"></div>
					<button type="submit" class="btn-submit btn-sumary btn-sub-create">
						<?php _e('Send', 'modal-forgot-password') ?>
					</button>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->