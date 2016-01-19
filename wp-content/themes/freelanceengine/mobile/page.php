<?php
	et_get_mobile_header();
	the_post();
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
			                	<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
			                		<?php the_title() ?>
			                	</a>
			                </h2>
			            </div>
			        </div>
			    </div>
			</div>			
		</div><!-- SINGLE TITLE + CATEGORY -->

		<div class="clearfix"></div>

		<div class="col-md-12 col-xs-12 blog-content-wrapper">
			<div class="blog-content">
				<?php
                    the_content();
                    wp_link_pages( array(
                        'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
                        'after'       => '</div>',
                        'link_before' => '<span>',
                        'link_after'  => '</span>',
                    ) );                        
                ?>
			</div>
		</div><!-- SINGLE CONTENT -->

        <div class="clearfix"></div>
	</div>
	<!--// block control  -->
</div>
<?php
	et_get_mobile_footer();
?>