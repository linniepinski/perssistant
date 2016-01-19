<?php
/**
 * Template list all freelancer current bid
 # This template is load page-profile.php
 * @since 1.0
*/
global $wp_query, $ae_post_factory;
$post_object = $ae_post_factory->get( BID );
?>
<!-- <ul class="bid-list-container"> -->
    <?php 
        $postdata = array();
        if(have_posts()){
            while (have_posts()) { the_post();
                $convert    = $post_object->convert($post);
                $postdata[] = $convert;
                get_template_part( 'mobile/template/user', 'bid-item' );
            }
        } else {
            echo '<li><span class="no-results">'.__('You are not bidding any project.', ET_DOMAIN).'</span></li>';
        }
    ?>
<!-- </ul> -->
<?php
    echo '<div class="paginations-wrapper">';
    ae_pagination($wp_query, get_query_var('paged'));
    echo '</div>';          
/**
 * render post data for js
*/
echo '<script type="data/json" class="postdata" >'.json_encode($postdata).'</script>';
?>