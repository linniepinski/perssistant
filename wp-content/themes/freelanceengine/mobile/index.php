<?php
	et_get_mobile_header();
?>

<div class="container">
	<!-- block control  -->
	<div class="row block-posts" id="post-control">
		<div class="col-md-12 posts-container" id="posts_control">
		<?php
			if(have_posts()){
				get_template_part( 'mobile/list', 'posts' );
			} else {
				echo '<h2>'.__( 'There is no posts yet', ET_DOMAIN ).'</h2>';
			}
		?>
		</div><!-- LEFT CONTENT -->
	</div>
	<!--// block control  -->
</div>
<?php
	et_get_mobile_footer();
?>