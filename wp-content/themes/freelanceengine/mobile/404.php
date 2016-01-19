<?php
	et_get_mobile_header();
?>
<div class="container">
	<!-- block control  -->
	<div class="row block-posts" id="post-control">
		<div class="col-md-12 posts-container" id="posts_control">
			<div class="blog-wrapper post-item single">
			    <div class="row">
			        <div class="col-md-12 col-xs-12">
			            <div class="blog-content">
			                <h2 class="title-blog">
			                	<?php _e("Not Found", ET_DOMAIN); ?>
			                </h2>
			            </div>
			        </div>
			    </div>
			</div>			
		</div><!-- SINGLE TITLE + CATEGORY -->

		<div class="clearfix"></div>

		<div class="col-md-12 col-xs-12 blog-content-wrapper">
			<div class="blog-content">
				<p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', ET_DOMAIN ); ?></p>
				<?php get_search_form(); ?>
			</div>
		</div><!-- SINGLE CONTENT -->

        <div class="clearfix"></div>
	</div>
	<!--// block control  -->
</div>
<?php
	et_get_mobile_footer();
?>