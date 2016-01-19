<?php
/**

 * Template Name: Notification

 * The main template file
 
 * This is the most generic template file in a WordPress theme and one

 * of the two required files for a theme (the other being style.css).

 * It is used to display a page when nothing more specific matches a query,

 * e.g., it puts together the home page when no home.php file exists.

 *

 * @link http://codex.wordpress.org/Template_Hierarchy

 *

 * @package WordPress

 * @subpackage FreelanceEngine

 * @since FreelanceEngine 1.0

 */
global $wp_query, $ae_post_factory, $post, $user_ID;

get_header();

$notify_object = $ae_post_factory->get('notify');

$notifications = query_posts(array(

    'post_type' => 'notify',

    'post_status' => 'publish',

    'author' => $user_id,

    'showposts' => 10, 

    'paged' => $page

));

?>

<section class="section-wrapper  section-archive-project">  
    
    <div class=" blog-header-container" >
      <div class="container">
            <div class="row">
                <div class="col-md-12 blog-classic-top">

                <h2 class="title-search-form-top"><?php _e("NOTIFICATIONS", ET_DOMAIN); ?> </h2>

            </div>
              </div>  </div>  </div>
                <!-- projects container -->
                <div class="container page-container">

    <!-- block control  -->

    <div class="row block-posts block-page notification-fullscreen" id="notification_container" style="display: block;">

        <div id="left_content" class="col-md-12 col-sm-12 col-xs-12 posts-container">

            <div class="blog-content">
                    
                   

                    <?php fre_user_notification($user_ID); ?>            

                </div> </div> </div> </div>
                
          
      
    
</section>


<?php
get_footer();

