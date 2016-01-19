<?php
	if(!is_author()) {
		global $post;
		$send_to = $post->post_author;	
	}else {
		$send_to = get_query_var('author');	
	}
	
?>
<div class="modal fade" id="modal_contact">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Private Message!", ET_DOMAIN) ?></h4>
			</div>
			<div class="modal-body">
				<form role="form" id="submit_contact" class="auth-form submit_contact">
					<input type="hidden" name="send_to" id="send_to" value="<?php echo $send_to ?>" />
					<div class="form-group textarea-form">
						<label for="user_login"><?php _e('Your message', ET_DOMAIN) ?></label>
						<p>
							<textarea id="message" name="message"></textarea>
						</p>
					</div>	
					<div class="clearfix"></div>
					<button type="submit" class="btn-submit btn-sumary btn-sub-create">
						<?php _e("Send", ET_DOMAIN) ?>
					</button>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->