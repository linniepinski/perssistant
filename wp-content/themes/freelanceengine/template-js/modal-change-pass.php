<div class="modal fade" id="modal_change_pass">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<!-- <span aria-hidden="true">&times;</span><span class="sr-only">
					<?php _e("Close", ET_DOMAIN) ?></span> -->
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Change your password.", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
				<form id="chane_pass_form" class="auth-form chane_pass_form">
					<div class="form-group">
						<label for="old_password"><?php _e('Old Password', ET_DOMAIN) ?></label>
						<input type="password" class="form-control" id="old_password" name="old_password" placeholder="<?php _e('Enter your old password', ET_DOMAIN) ?>">
					</div>
                    <div class="clearfix"></div>
					<div class="form-group">
						<label for="new_password"><?php _e('Your New Password', ET_DOMAIN) ?></label>
						<input type="password" class="form-control" id="new_password" name="new_password" placeholder="<?php _e('Enter your new password', ET_DOMAIN) ?>">
					</div>

                    <!-- Form validation password -->
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
                    <!-- / Form validation password -->

                    <div class="clearfix"></div>
					<div class="form-group">
						<label for="renew_password"><?php _e('Retype New Password', ET_DOMAIN) ?></label>
						<input type="password" class="form-control" id="renew_password" name="renew_password" placeholder="<?php _e('Retype your new password', ET_DOMAIN) ?>">
					</div>		
                    <div class="clearfix"></div>
					<button type="submit" class="btn-submit btn-sumary btn-sub-create">
						<?php _e('Change password', ET_DOMAIN) ?>
					</button>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->