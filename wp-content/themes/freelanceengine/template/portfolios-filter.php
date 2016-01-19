<?php 
/**
 * The template for displaying portfolio filter
 * @since 1.0
 * @category Template
 * @package FreelanceEngine
 */
global $wp_query, $ae_post_factory;
$post_object = $ae_post_factory->get(PROFILE);
$convert = $post_object->current_post;
?>
<h4 class="title-count-portfolio">
<?php 
    _e("Portfolio", ET_DOMAIN); echo ' ';
    if($wp_query->found_posts > 1) {
        printf(__("(%d items)", ET_DOMAIN), $wp_query->found_posts);    
    }else {
        printf(__("(%d item)", ET_DOMAIN), $wp_query->found_posts);
    }
    
?>
<div class="portfolio-filter">
    <select class="chosen-select" name="skill" data-chosen-width="200px" data-chosen-disable-search="" >
    <option value=""><?php _e( 'All skills' , ET_DOMAIN ); ?></option>
    <?php 
    if($convert->tax_input['skill']){
	  	foreach ($convert->tax_input['skill'] as $tax){ 
    ?>
        <option value="<?php echo $tax->slug; ?>"><?php echo $tax->name; ?></option>
    <?php 
		}
	}
	?>
    </select>
</div>
</h4>

