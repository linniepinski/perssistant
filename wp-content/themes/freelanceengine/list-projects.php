<?php
/**
 * Template list all project
*/
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('project');
?>
<ul class="list-project project-list-container">
<?php 
    $postdata = array();
    while (have_posts()) { the_post();
        $convert = $post_object->convert($post);
        $postdata[] = $convert;
        if($convert->post_status != 'pending')
            get_template_part( 'template/project', 'item' );
        else 
            get_template_part( 'template/project', 'item-pending' );
    }?>
	
</ul>

<?php
    $wp_query->query = array_merge(  $wp_query->query ,array('is_archive_project' => is_post_type_archive(PROJECT) ) ) ;   
    echo '<div class="paginations-wrapper">';
    ae_pagination($wp_query, get_query_var('paged'));
    echo '</div>';         
/**
 * render post data for js
*/
echo '<script type="data/json" class="postdata" >'.json_encode($postdata).'</script>';
?>
