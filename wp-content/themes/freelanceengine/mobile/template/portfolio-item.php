<?php 
	global $wp_query, $ae_post_factory, $post;
	$post_object = $ae_post_factory->get( PORTFOLIO );
	$current     = $post_object->current_post;
	if(!$current){
	   return;
	}
?>
<li>
	<a href="<?php echo $current->the_post_thumbnail_full; ?>" title="<?php echo $current->post_title ?>" class="image-gallery" >
		<img src="<?php echo $current->the_post_thumbnail; ?>" >
	</a>
</li>