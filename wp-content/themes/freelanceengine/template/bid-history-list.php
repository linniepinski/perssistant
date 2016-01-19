<?php
/**
 * Template part for freelancer bid history 
 # this template is loaded in template/bid-history.php
 * @since 1.0	
 * @package FreelanceEngine
 */
global $wp_query, $ae_post_factory;
$author_id = get_query_var('author');

if(ae_user_role($author_id) == FREELANCER) {
	$post_object = $ae_post_factory->get(BID);
}else {
	$post_object = $ae_post_factory->get(PROJECT);
}

?>
<h4 class="title-big-info-project-items"><?php printf(__("Work history and Reviews (%d)", ET_DOMAIN), $wp_query->found_posts) ?></h4>
<ul class="list-history-profile">
	<?php
	$postdata = array();
	while (have_posts()) { the_post();
	    $convert = $post_object->convert($post,'thumbnail');
	    $postdata[] = $convert;
	    get_template_part( 'template/bid-history', 'item' );
    }
    ?>
</ul>
<?php
/**
 * render post data for js
*/
echo '<script type="data/json" class="postdata" >'.json_encode($postdata).'</script>';
?>
<!-- pagination -->
<?php
	$wp_query->query = array_merge(  $wp_query->query ,array('is_author' => true ) ) ;   
    echo '<div class="paginations-wrapper">';
    ae_pagination($wp_query, get_query_var('paged'), 'load');
    echo '</div>';             
?>
