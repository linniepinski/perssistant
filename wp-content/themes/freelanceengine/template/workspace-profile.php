<?php 
/**
 * The template for displaying profile in a loop
 * @since  1.0
 * @package FreelanceEngine
 * @category Template
 */
global $wp_query, $post, $user_ID;
$post_object = $ae_post_factory->get( PROFILE );
$current = $post_object->current_post;

if(!$current){
    return;
}
?>

<div class="col-md-4 workplace-project-details">

	<div class="info-company-wrapper">

		<div class="row">

			<div class="col-md-12">                               
				<?php
					fre_display_user_info( $user_ID );
				?>
			</div>

		</div>

	</div>

</div>