<div class="no-bid-found">
	<?php
		global $project, $user_ID;
		$role = ae_user_role();
		if($project->post_status == 'publish' ){
		 	if( (int) $project->post_author == $user_ID || $role != 'freelancer' ){
				_e('There are no bids yet.',ET_DOMAIN);
			} else if( $role == 'freelancer' || !$user_ID ) { ?>
				<div class="col-md-10">
				   <?php _e('There are no bids yet. Be the first one now!',ET_DOMAIN);?>
				   
				</div>
				<!-- <div class="col-md-2 btn-warpper-bid">
				   		<a href="#" class="btn btn-apply-project-item btn-bid-mobile btn-bid"><?php _e('Bid',ET_DOMAIN);?></a>
				</div> -->
			<?php }
		}  else {
			$status = 	array(	'pending' => __('This project is pending', ET_DOMAIN),
								'archive' => __('This project has been archived',ET_DOMAIN) ,
								'reject'  => __('This project has been rejected',ET_DOMAIN) );
			if(isset($status[$project->post_status]))
				printf($status[$project->post_status]);
		}
	?>
</div>