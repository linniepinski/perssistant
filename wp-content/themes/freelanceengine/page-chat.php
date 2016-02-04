<?php
/**
 * The template for displaying all pages
 *
 * Template Name: Chat-room
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */
global $post;
get_header();
the_post();
?>
    <div class="container-fluid page-container">
        <!-- block control  -->
        <div class="row block-posts">
            <div class="col-md-12 col-sm-12 col-xs-12 posts-container" id="left_content">
                    <?php
                    the_content();
                    wp_link_pages( array(
                        'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', ET_DOMAIN ) . '</span>',
                        'after'       => '</div>',
                        'link_before' => '<span>',
                        'link_after'  => '</span>',
                    ) );
                    ?>
                    <div class="clearfix"></div>
                    <?php /*
		        <div class="latest-pages">
		        	<h4><?php _e("Similar Pages", ET_DOMAIN) ?></h4>
		        	<?php fre_latest_pages($post->ID) ?>
		        </div>
		        */ ?><!-- end latest page -->
                <!-- end page content -->
            </div>
        </div>
        <!--// block control  -->
    </div>
<div style="display: none">
<?php
get_footer();
?>
    </div>