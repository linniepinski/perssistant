<?php

/**

 * The main template file

 *

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

global $wp_query, $ae_post_factory, $post;

$post_object = $ae_post_factory->get( PROFILE );

get_header();

$count_posts = wp_count_posts(PROFILE); 

?>

<section class="section-wrapper section-archive-profile">

    <?php

        // if(is_post_type_archive( PROFILE ) && !is_singular()){

            get_template_part('template/filter', 'profiles' ); 

        // }

    ?>
    <?php /*
	<div class="number-profile-wrapper-archive">

    	<div class="container">

            <div class="row">

                <div class="col-md-3">                 

                </div>

                <div class="col-md-9">

                    <div class="text-right pos-related">

                        <?php //fre_profile_button(); ?>

                    </div>

                </div>

            </div>

        </div>

    </div> 
<?php */?>
    <div class="list-profile-wrapper">

    	<div class="container">

        	<div class="row">

            	<div class="col-md-12">

                	<div class="tab-content-profile">

                        <!-- Tab panes -->

                        <div class="tab-content archive-block-profiles">

                            <div class="tab-pane fade in active" id="tab_lastest_profile">

                            	<div class="row">

                                    <?php get_template_part( 'list', 'profiles' ); ?>

                                </div>

                            </div>

                          <!--   <div class="tab-pane fade" id="tab_featured_profile">.sf sfd sf</div>

                            <div class="tab-pane fade" id="tab_featured_team">.sf sfd sf</div> -->

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    

</section>

<?php

get_footer();





