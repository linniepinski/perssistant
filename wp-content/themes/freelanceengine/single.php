<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme and one
 * of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query,
 * e.g., it puts together the home page when no home.php file exists.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
	global $post;
	get_header();
	the_post();
?>	
<section class="blog-header-container">
	<div class="container">
		<!-- blog header -->
		<div class="row">
		    <div class="col-md-12 blog-classic-top">
		        <h2><?php _e("Blog",ET_DOMAIN) ?></h2>
		        <form id="search-bar" action="<?php echo home_url() ?>">
		            <i class="fa fa-search"></i>
		            <input type="text" name="s" placeholder="<?php _e("Search at blog",ET_DOMAIN) ?>">
		        </form>
		    </div>
		</div>      
		<!--// blog header  -->	
	</div>
</section>

<div class="container">
	<!-- block control  -->
	<div class="row block-posts" id="post-control">
		<div class="col-md-9 col-sm-12 col-xs-12 posts-container">
			<div class="blog-wrapper">
	            <div class="row">
	                <div class="col-md-3 col-xs-3">
	                    <div class="author-wrapper">
	                        <span class="avatar-author">
	                            <?php echo get_avatar($post->post_author, 65); ?>
	                        </span>
	                        <span class="date">
	                            <?php the_author();?><br>
	                            <?php the_time('M j');  ?> <sup><?php the_time('S');?></sup>, <?php the_time('Y');?>
	                        </span>
	                    </div>
	                </div>
	                <div class="col-md-9 col-xs-9">
	                    <div class="blog-content">
	                        <span class="tag">
	                        	<?php the_category( '-' ); ?>
	                        </span><!-- end cat -->
	                        <span class="cmt">
	                        	<i class="fa fa-comments"></i><?php comments_number(); ?>
	                        </span><!-- end cmt count -->
	                        <h2 class="title-blog">
	                        	<a href="<?php the_permalink(); ?>"><?php the_title() ?></a>
	                        </h2><!-- end title -->
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
	                </div>
	            </div>
	        </div>                
	        <div class="clearfix"></div>
	        <?php comments_template(); ?>
		</div><!-- LEFT CONTENT -->
		<div class="col-md-3 col-sm-12 col-xs-12 blog-sidebar" id="right_content">
			<?php get_sidebar('blog'); ?>
		</div><!-- RIGHT CONTENT -->
	</div>
	<!--// block control  -->
</div>
<?php
	get_footer();
?>