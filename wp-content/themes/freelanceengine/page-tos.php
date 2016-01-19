<?php
/**
 * Template Name: Term of Use
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
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
		        <h2><?php the_title(); ?></h2>
		    </div>
		</div>      
		<!--// blog header  -->	
	</div>
</section>

<div class="container page-container">
	<!-- block control  -->
	<div class="row block-posts block-page">
		<div class="col-md-9 col-sm-12 col-xs-12 posts-container" id="left_content">
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
                
				<div class="clearfix"></div>
				<?php /*
		        <div class="latest-pages">
		        	<h4><?php _e("Similar Pages", ET_DOMAIN) ?></h4>
		        	<?php fre_latest_pages($post->ID) ?>
		        </div>
		        */ ?><!-- end latest page -->                
            </div><!-- end page content -->
		</div><!-- LEFT CONTENT -->
		<div class="col-md-3 col-sm-12 col-xs-12 page-sidebar" id="right_content">
			<?php get_sidebar('page'); ?>
		</div><!-- RIGHT CONTENT -->
	</div>
	<!--// block control  -->
</div>
<?php
	get_footer();
?>