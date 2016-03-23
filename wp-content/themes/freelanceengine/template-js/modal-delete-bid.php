<div class="modal modal-vcenter fade in" id="modal-deleting-bid">
	<div class="modal-dialog top-margin">
		<div class="modal-content" style="margin-top:150px; height:250px;">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close" aria-hidden="true"><span aria-hidden="true" style="font-size: 18px;">X</span></button>
				<h4 class="modal-title text-center text-color-popup" style="font-size:30px;"><?php  _e('Deleting Bid','modal-delete-bid');?></h4>
				<hr>
			</div>
			<div class="modal-body">
				<h4 class="text-center" style="font-size:25px; padding-top:10px;"><?php  _e('Are you sure, you want to delete the Bid?','modal-delete-bid');?></h4>
				<div style="padding-top:25px;">
					<a style="margin:10px;" href="#" class="btn btn-apply-project-item" data-dismiss="modal">
						<?php  _e('No','modal-delete-bid');?>
					</a>
					<a style="margin:10px;" rel="<?php echo $project->ID;?>" href="#" id="<?php echo $has_bid;?>" class="btn btn-apply-project-item btn-del-project" >
						<?php  _e('Delete','modal-delete-bid');?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>