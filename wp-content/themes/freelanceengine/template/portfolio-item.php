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
//var_dump($current);
?>
<li class="portfolio-item col-md-4">
	<a data-description="<?php echo $current->post_content?>" href="<?php echo $current->the_post_thumbnail_full; ?>" title="<?php echo htmlspecialchars($current->post_title) ?>" class="image-gallery">
		<img src="<?php echo htmlspecialchars($current->the_post_thumbnail); ?>" >
	</a>
	<a href="#" class="delete">
		<i class="fa fa-trash-o"></i>
	</a>
    <a href="#" class="edit">
		<i class="fa fa-pencil"></i>
	</a>
<!--    <textarea>--><?php //echo $current->post_content?><!--</textarea>-->

</li>