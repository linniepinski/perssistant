<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
get_header();
global $wp_query, $ae_post_factory, $post, $user_ID;
$post_object = $ae_post_factory->get(PROJECT);
$convert = $post_object->convert($post); 

if(have_posts()) { the_post();	
 	// get breacrumb of single project
    get_template_part('template/single-project','breadcrumb' ); 
    ?>
	   
    <div class="single-project-wrapper">
    	<div class="container">
        	<div class="row">
            	<?php get_template_part('template/project','detail' ); ?>
                <?php get_template_part('template/list','bids' ); ?>
            </div> <!-- end .row !-->
        </div>
    </div>
	<?php
    echo '<script type="data/json" id="project_data">'.json_encode($convert).'</script>';
}
get_footer();