<?php

	global $user_ID;
	
	$profile_id = get_user_meta($user_ID, 'user_profile_id', true);

?>
<div class="modal fade" id="cv_modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
				<i class="fa fa-times"></i>
				</button>
				<h4 class="modal-title"><?php _e("Add CV To Your Profile", ET_DOMAIN) ?></h4>
				<div class="loading-img" style="display:none; background-image: url(http://www.perssistant.com/wp-content/themes/freelanceengine/includes/aecore/assets/img//loading.gif); background-position: 50% 65%;"></div>
			</div>
			<div class="modal-body">
				<form id="create_cv" class="auth-form create_cv" enctype="multipart/form-data">
					<div id="portfolio_img_container">
						<input type="hidden" name="action" value="upLoadCvUser" />
						<input type="hidden" name="user_id" value="<?php echo $user_ID; ?>" />
						<input type="hidden" name="post_thumbnail" id="post_thumbnail" value="0" />
						<p class="browser-image btn btn-default browse_cont">Browse
							<input type="file" id="cv_img_browse_button" name="cv_upload" class="btn-submit upload_btn" value="Browse" required>
						</p>
						<div class="clearfix"></div>
						<br />
						<button type="submit" class="btn-submit btn-sumary btn-sub-create">
							<?php _e("Add CV", ET_DOMAIN) ?>
						</button>
					</div>
				</form>	
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->