<?php
/**
 * Template list all project
*/
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get(PROJECT);
?>
<ul class="list-project project-list-container">  
<?php 
    $postdata = array();
    while (have_posts()) { the_post();
        $convert = $post_object->convert($post);
        $postdata[] = $convert;
        get_template_part( 'mobile/template/project', 'item' );
    }?>
	
</ul>

<?php
    $wp_query->query = array_merge(  $wp_query->query ,array('is_archive_project' => is_post_type_archive(PROJECT) ) ) ;   
    echo '<div class="paginations-wrapper">';
    ae_pagination($wp_query, get_query_var('paged'), 'load');
    echo '</div>';         
/**
 * render post data for js
*/
echo '<script type="data/json" class="postdata" >'.json_encode($postdata).'</script>';
?>