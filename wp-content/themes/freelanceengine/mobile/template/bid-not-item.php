<div class="no-bid-found">
	<?php
		global $project, $user_ID;
		$role = ae_user_role();
		if($project->post_status == 'publish' ){
		 	if( (int) $project->post_author == $user_ID || $role != 'freelancer' ){
				_e('There are no bids yet.','bid-not-item-mobile');
			} else if( $role == 'freelancer' || !$user_ID ) { ?>
				<div class="col-md-10">
				   <?php _e('There are no bids yet. Be the first one now!','bid-not-item-mobile');?>
				   
				</div>
				<!-- <div class="col-md-2 btn-warpper-bid">
				   		<a href="#" class="btn btn-apply-project-item btn-bid-mobile btn-bid"><?php _e('Bid','bid-not-item-mobile');?></a>
				</div> -->
			<?php }
		}  else {
			$status = 	array(	'pending' => __('This project is pending', 'bid-not-item-mobile'),
								'archive' => __('This project has been archived','bid-not-item-mobile') ,
								'reject'  => __('This project has been rejected','bid-not-item-mobile') );
			if(isset($status[$project->post_status]))
				printf($status[$project->post_status]);
		}
	?>
</div>