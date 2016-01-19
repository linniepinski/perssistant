<?php

/**

 * Template Name: Page Blog

 *

 * This is the template that displays all recent posts

 * @package WordPress

 * @subpackage FreelanceEngine

 * @since FreelanceEngine 1.0

 */



	get_header(); 

?>



<section class="blog-header-container">

	<div class="container">

		<!-- blog header -->

		<div class="row">

		    <div class="col-md-12 blog-classic-top">

		        <h2><?php the_title(); ?></h2>

		        <form id="search-bar" action="<?php echo home_url() ?>">

		            <i class="fa fa-search"></i>

		            <input type="text" name="s" placeholder="<?php _e("Search at blog",ET_DOMAIN) ?>">

		        </form>

		    </div>

		</div>      

		<!--// blog header  -->	

	</div>

</section>

<?php query_posts(array('post_type' => 'post', 'post_status' => 'publish')); ?>

<div class="container blog-container">

	<!-- block control  -->

	<div class="row block-posts" id="post-control">

		<div class="col-md-9 col-sm-12 col-xs-12 posts-container" id="posts_control">
			<div class="">
		<?php

			if(have_posts()){

				get_template_part( 'list', 'posts' );

			} else {

				echo '<h2>'.__( 'There is no posts yet', ET_DOMAIN ).'</h2>';

			}

		?>
		</div>
		</div><!-- LEFT CONTENT -->

		<div class="col-md-3 col-sm-12 col-xs-12 blog-sidebar" id="right_content">

			<?php get_sidebar('blog'); ?>

		</div><!-- RIGHT CONTENT -->

	</div>

	<!--// block control  -->

</div>

<?php

wp_reset_query();

	get_footer();

?>

