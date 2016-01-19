<?php
/**
 * Template list profiles
*/
global $wp_query, $ae_post_factory, $post;
$post_object = $ae_post_factory->get( PROFILE );
?>
<div class="list-profiles profile-list-container">
	 <!-- block control  -->
    <?php
        if(have_posts()) {
            $postdata = array();
            while(have_posts()) { the_post(); 
                $convert    = $post_object->convert($post);
                $postdata[] = $convert;
                get_template_part('mobile/template/profile', 'item' );
            }
            /**
            * render post data for js
            */    
            echo '<script type="data/json" class="postdata" >'.json_encode($postdata).'</script>';
        }
        wp_reset_query(); 
    ?>
</div>
<!--// blog list  -->
<!-- pagination -->

<?php
    echo '<div class="paginations-wrapper">';
    ae_pagination($wp_query, get_query_var('paged'), 'load');
    echo '</div>';             
?>
