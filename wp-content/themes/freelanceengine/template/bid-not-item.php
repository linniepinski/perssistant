<?php
/**
 * The template for displaying no bidding info in a project details page 
 * @since 1.0
 * @author Dakachi
 */
?>
<div class="no-bid-found">
	<div class="row">
	<?php
		global $wp_query, $ae_post_factory, $post,$user_ID;
		// get current project post data
	    $project_object = $ae_post_factory->get(PROJECT);;
	    $project = $project_object->current_post;

		$role = ae_user_role();
		if($project->post_status == 'publish' ){
		 	if( (int) $project->post_author == $user_ID || $role != FREELANCER ){
		 		echo '<div class="col-md-12">';
				_e('There are no bids yet.',ET_DOMAIN);
				echo '</div>';
			} else if( $role == 'freelancer' || !$user_ID ) { ?>
				<div class="col-md-10" style="line-height:26px;">
				   <?php _e('There are no bids yet. Be the first one now!',ET_DOMAIN);?>
				   
				</div>
				<div class="col-md-2">
				   		<a href="#" class="btn btn-apply-project-item" data-toggle="modal" data-target="#modal_bid">
				   			<?php _e("Bid", ET_DOMAIN); ?>
				   		</a>
				</div>
				<div class="clearfix"></div>
			<?php }
		}  else {
			echo '<div class="col-md-12" >';
			$status = 	array(	'pending' => __('This project is pending', ET_DOMAIN),
								'archive' => __('This project has been archived',ET_DOMAIN) ,
								'reject'  => __('This project has been rejected',ET_DOMAIN) );
			if(isset($status[$project->post_status]))
				printf($status[$project->post_status]);

			echo '</div>';
		}
	?>
	</div>
</div>