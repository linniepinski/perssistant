<div class="modal fade" id="modal_change_pass">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<!-- <span aria-hidden="true">&times;</span><span class="sr-only">
					<?php _e("Close", 'modal-change-password') ?></span> -->
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Change your password.", 'modal-change-password') ?></h4>
			</div>
			<div class="modal-body">
				<form id="chane_pass_form" class="auth-form chane_pass_form">
					<div class="form-group">
						<label for="old_password"><?php _e('Old Password', 'modal-change-password') ?></label>
						<input type="password" class="form-control" id="old_password" name="old_password" placeholder="<?php _e('Enter your old password', 'modal-change-password') ?>">
					</div>
                    <div class="clearfix"></div>
					<div class="form-group">
						<label for="new_password"><?php _e('Your New Password', 'modal-change-password') ?></label>
						<input type="password" class="form-control" id="new_password" name="new_password" placeholder="<?php _e('Enter your new password', 'modal-change-password') ?>">
					</div>
                    <!-- Form validation password -->
					<?php
					render_security_check_pass_info();
					?>
                    <!-- / Form validation password -->

                    <div class="clearfix"></div>
					<div class="form-group">
						<label for="renew_password"><?php _e('Retype New Password', 'modal-change-password') ?></label>
						<input type="password" class="form-control" id="renew_password" name="renew_password" placeholder="<?php _e('Retype your new password', 'modal-change-password') ?>">
					</div>		
                    <div class="clearfix"></div>
					<button type="submit" class="btn-submit btn-sumary btn-sub-create">
						<?php _e('Change password', 'modal-change-password') ?>
					</button>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->