<?php 
/**
 * Template Name: Front Page Template
*/
global $wp_query, $ae_post_factory, $post;
get_header(); 
if(have_posts()) { 
the_post();

?>

<div class="fre_container">
<?php

the_content();

?>

</div>
<?php
}
get_footer();
