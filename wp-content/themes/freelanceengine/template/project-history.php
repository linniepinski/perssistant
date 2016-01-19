<?php 
/**
 * The template for displaying block project history posted by employer
 * @since  1.0
 * @package FreelanceEngine
 * @category Template
 */
?>
<div class="profile-history project-history">
<?php 
	$author_id = get_query_var('author');
	if(is_page_template('page-profile.php')) {
        global $user_ID;
        $author_id = $user_ID;
    }
    query_posts( array(  'post_status' => array('publish','close', 'complete'), 'post_type' => PROJECT, 'author' => $author_id )); 
    $bid_posts   = $wp_query->found_posts;
?>

 <?php
    // list portfolio
    if(have_posts()):
        get_template_part( 'list', 'work-history' );     
    else :
    endif;
    //wp_reset_postdata();
    wp_reset_query();
 ?>
</div>