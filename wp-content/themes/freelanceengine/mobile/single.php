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
			        <div class="col-md-3 col-xs-3">
			            <div class="author-wrapper">
			                <span class="avatar-author">
			                    <?php echo get_avatar($post->post_author, 48); ?>
			                </span>
			            </div>
			        </div>
			        <div class="col-md-9 col-xs-9">
			            <div class="blog-content">
			                <span class="tag">
			                    <?php the_category( ' - ' ); ?>
			                </span>
			                <span class="cmt">
			                    <i class="fa fa-comments"></i>
			                    <?php comments_number( '0', '1', '%' ); ?>
			                </span>
			                <h2 class="title-blog">
			                	<a href="<?php the_permalink(); ?>">
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

        <div class="col-xs-12 comments-wrapper">
        	<?php comments_template('/mobile/comments.php'); ?>                
        </div><!-- SINGLE COMMENTS -->	
	</div>
	<!--// block control  -->
</div>
<?php
	et_get_mobile_footer();
?>