<?php 
/**
 * The template for displaying user porfolio in profile details, edit profiles 
 * @since 1.0
 * @package FreelanceEngine
 * @category Template
 */
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get( PORTFOLIO );
$current     = $post_object->current_post;
if(!$current){
   return;
}
?>
<li class="portfolio-item col-md-4">
	<a href="<?php echo $current->the_post_thumbnail_full; ?>" title="<?php echo $current->post_title ?>" class="image-gallery">
		<img src="<?php echo $current->the_post_thumbnail; ?>" >
	</a>
	<a href="#" class="delete">
		<i class="fa fa-trash-o"></i>
	</a>
</li>