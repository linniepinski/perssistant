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
global $wp_query, $ae_post_factory, $post, $queried_object;
$queried_object = $wp_query->queried_object;
$taxonomy = $queried_object->taxonomy;

get_header();
/**
 * get current tax support object type
 */
global $wp_taxonomies;
$tax = $wp_taxonomies[$taxonomy];
$object_type = $tax->object_type;
$object_type = array_pop($object_type);

// profile tax
if($object_type == PROFILE) {
    $post_object = $ae_post_factory->get( PROFILE );
    $count_posts = wp_count_posts(PROFILE); 
    ?>
    <section class="section-wrapper section-archive-profile">
        <?php
            // if(is_post_type_archive( PROFILE ) && !is_singular()){
                get_template_part('template/filter', 'profiles' ); 
            // }
        ?>
        <div class="number-profile-wrapper-archive">
            <div class="container">
                <div class="row">
                    <div class="col-md-3">                 
                    </div>
                    <div class="col-md-9">
                        <div class="text-right pos-related">
                            <?php fre_profile_button(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
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
}else {

// project tax
    $post_object = $ae_post_factory->get(PROJECT);
?>
    <section class="section-wrapper  section-archive-project">
<?php  
    // if(is_post_type_archive( 'project' ) && !is_singular()){
        get_template_part('template/filter', 'projects' ); 
    // }
 ?>
    <div class="number-project-wrapper-archive">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-xs-6 chosen-sort"> 
                    <select class="sort-order chosen-select" id="project_orderby" name="orderby" 
                        data-placeholder="<?php _e("Orderby", ET_DOMAIN); ?>" data-chosen-disable-search="1" data-chosen-width="90%" style="display: none;">
                        <option value="date"><?php _e('Newest Projects first',ET_DOMAIN);?></option>
                        <option value="et_featured"><?php _e('Featured Projects first',ET_DOMAIN);?></option>
                        <option value="et_budget"><?php _e('Budget Projects first',ET_DOMAIN);?></option>
                    </select>                 
                </div>
                <div class="col-md-9 col-xs-6">
                    <div class="text-right pos-related">
                        <?php fre_project_button() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="list-project-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="tab-content-project">
                        <div class="row title-tab-project">
                            <div class="col-md-5 col-sm-5 col-xs-7">
                                <span><?php _e("PROJECT TITLE", ET_DOMAIN); ?></span>
                            </div>
                            <div class="col-md-2 col-sm-3 hidden-xs">
                                <span><?php _e("BY", ET_DOMAIN); ?></span>
                            </div>
                            <div class="col-md-2 col-sm-2 hidden-sm hidden-xs">
                                <span><?php _e("POSTED DATE", ET_DOMAIN); ?></span>
                            </div>
                            <div class="col-md-1 col-sm-2 hidden-xs">
                                <span><?php _e("BUDGET", ET_DOMAIN); ?></span>
                            </div>
                        </div>
                        <!-- Tab panes -->
                        <div class="tab-content block-projects">
                            <div class="tab-pane fade in active" id="tab_lastest_projects">
                                <?php get_template_part( 'list', 'projects' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</section>
<?php
}

get_footer();


