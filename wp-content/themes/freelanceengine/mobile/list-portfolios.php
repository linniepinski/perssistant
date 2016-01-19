<?php 
	global $wp_query, $ae_post_factory, $post;
	$post_object = $ae_post_factory->get('portfolio');
?>
	<ul class="list-porfolio-author list-item-portfolio">
		<?php
	        $postdata = array();
	        while (have_posts()) { the_post();
				$convert    = $post_object->convert( $post, 'thumbnail' );
				$postdata[] = $convert;
	            get_template_part( 'mobile/template/portfolio', 'item' );
	        }
        ?>
	</ul>
<?php   
	/**
	 * render post data for js
	*/
	echo '<script type="data/json" class="postdata portfolios-data" >'.json_encode($postdata).'</script>';
	echo '<div class="paginations-wrapper">';
	ae_pagination($wp_query, get_query_var('paged'), 'load');
	echo '</div>';
	wp_reset_query();
	if(is_page_template( 'page-profile.php' )){
?>
<div class="add-porfolio-button">
	<a href="#" class="add-portfolio">
		<i class="fa fa-plus"></i>
		<?php _e("Add your work", ET_DOMAIN) ?>
	</a>
</div>
<?php } ?>