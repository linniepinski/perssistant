<?php
/**
 * Template list all project
*/
$args = array(
    'post_type' => PROJECT, 
    'showposts' => -1,
    'post_status' => 'pending'
);

$pending    =   new WP_Query($args);

global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get('project');
?>
<uel class="list-project project-list-container1">
<?php   
    $projecttdata = array();

        $post_arr   =   array();
        if($pending->have_posts()) {
            while ($pending->have_posts()) {
                $pending->the_post();
                global $post, $ae_post_factory;
                /**
                 * get ae post object and convert post data
                */
                $ae_post    =   $ae_post_factory->get('project');
                // convert post data
                $convert    =   $ae_post->convert($post, 'big_post_thumbnail');
                $projecttdata[] =   $convert;
                // get template render place details
                get_template_part( 'template/project', 'item-pending' );
            }
        } else {
            _e("No place found", ET_DOMAIN);
        }
      
    ?>
	
</uel>
<?php
         
/**
 * render post data for js
*/
echo '<script type="data/json" class="pendingdata" >'.json_encode($projecttdata).'</script>';
?>