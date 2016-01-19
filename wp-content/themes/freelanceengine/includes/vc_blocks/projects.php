<?php
if(!class_exists('WPBakeryShortCode')) return;
class WPBakeryShortCode_fre_block_project extends WPBakeryShortCode {

    protected function content($atts, $content = null) {

        $custom_css = $el_class = $title = $icon = $output = $s_content = $m_link = '';

        extract(shortcode_atts(array(
            'el_class'  => '',
            'showposts' => 10, 
            'project_type' => '',
            'paginate' => 'page',
            'query' => ''
        ), $atts));
        /* ================  Render Shortcodes ================ */
        ob_start();
        $query_args = array(    'post_type' => PROJECT , 
                                'post_status' => 'publish' , 
                                'posts_per_page' => $showposts
                            ) ;
        if($project_type) {
            $query_args['project_type'] = $project_type;
        }
        if($query == 'featured') {
            $query_args['meta_key'] = 'et_featured';
            $query_args['meta_value'] = 1;
        }

        ?>
        <!-- COUNTER -->
        <section class="section-wrapper  section-project-home tab-project-home">
            <div class="list-project-wrapper">
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
                            
                            <?php query_posts( $query_args); ?>

                            <div class="tab-pane fade in active tab-project-home">
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
                                        get_template_part( 'template/project', 'item' );
                                    }?>
                                    
                                </ul>
                                
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php
                        if($paginate == 'page' || $paginate == 'load_more') {
                            echo '<div class="paginations-wrapper">';
                            ae_pagination($wp_query, get_query_var('paged'), $paginate);
                            echo '</div>';         
                            /**
                             * render post data for js
                            */
                            echo '<script type="data/json" class="postdata" >'.json_encode($postdata).'</script>';
                        }
                        ?>
                    </div>

                </div>
            </div>
        </section>
        <?php         
        $output = ob_get_clean();
        /* ================  Render Shortcodes ================ */
        return $output;
    }
}


// get all places & locations
global $wpdb;
// places
$query_places = "SELECT *
                        FROM
                            {$wpdb->terms} as t 
                        INNER JOIN 
                            {$wpdb->term_taxonomy} as tax 
                        ON 
                            tax.term_id = t.term_id
                        WHERE 
                            tax.taxonomy = 'project_type' AND tax.count > 0";                     
$places =  $wpdb->get_results($query_places);
$places_arr    = array(__('All', ET_DOMAIN) => '');

foreach ($places as $place) {
    $places_arr[$place->name] = $place->slug;
}


vc_map( array(
    "base"      => "fre_block_project",
    "name"      => __("List projects", ET_DOMAIN),
    "class"     => "",
    "icon"      => "",
    "category" => __("FreelanceEngine", ET_DOMAIN),
    "params"    => array(

        array(
            "type" => "textfield",
            "heading" => __("Number of posts", ET_DOMAIN),
            "class" => "input-title",
            "param_name" => "showposts",
            "value"     => '10'
        ),  
        array(
            "type"       => "dropdown",
            "class"      => "",
            "heading"    => __("Query", ET_DOMAIN),
            "param_name" => "query",
            "value"      => array('Featured Posts' => 'featured', 'Recent Posts' => 'recent'),
        ), 
        array(
            "type"       => "dropdown",
            "class"      => "",
            "heading"    => __("Project type", ET_DOMAIN),
            "param_name" => "project_type",
            "value"      => $places_arr,
        ),
        array(
            "type"       => "dropdown",
            "class"      => "",
            "heading"    => __("Paginate", ET_DOMAIN),
            "param_name" => "paginate",
            "value"      => array('none' => '0', 'Page paginate' => 'page', 'Load More' => 'load_more'),
        )
    )
));

/*
class WPBakeryShortCode_fre_list_projects extends WPBakeryShortCode {

    protected function content($atts, $content = null) {

        $custom_css = $el_class = $title = $icon = $output = $s_content = $m_link = '';

        extract(shortcode_atts(array(
            'el_class'      => '',
            'title'         => __("Projects", ET_DOMAIN),
            's_title'     => '',
            's_description'        => '',
            'showposts' => 10
        ), $atts));
        /* ================  Render Shortcodes ================ 
        ob_start();
        ?>
        <!-- COUNTER -->
        <?php
        $count_project = wp_count_posts(PROJECT);
        ?>
        
        <section class="section-wrapper section-project section-project-home">
            <div class="number-project-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <h2 class="number-project"><?php printf(__('%s Projects for you',ET_DOMAIN),$count_project->publish);?></h2>
                            <div class="nav-tabs-project">
                                <?php fre_project_button(); ?>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist" id="myTab">
                                    <li class="active">
                                        <a href="#tab_lastest_projects" role="tab" data-toggle="tab"><?php _e("Lastest Projects", ET_DOMAIN); ?></a>
                                    </li>
                                    <li>
                                        <a href="#tab_featured_projects" role="tab" data-toggle="tab"><?php _e("Featured Projects", ET_DOMAIN); ?></a>
                                    </li>
                                </ul>
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
                                    <div class="col-md-5">
                                        <h2><?php _e("PROJECT TITLE", ET_DOMAIN); ?></h2>
                                    </div>
                                    <div class="col-md-2">
                                        <h2><?php _e("BY", ET_DOMAIN); ?></h2>
                                    </div>
                                    <div class="col-md-2">
                                        <h2><?php _e("POSTED DATE", ET_DOMAIN); ?></h2>
                                    </div>
                                    <div class="col-md-1">
                                        <h2><?php _e("BUDGET", ET_DOMAIN); ?></h2>
                                    </div>
                                </div>
                                <!-- Tab panes -->
                                <div class="tab-content block-projects ">
                                    <?php query_posts( array('post_type' => PROJECT , 'post_status' => 'publish' , 'posts_per_page' => $showposts) ); ?>
                                    <div class="tab-pane fade in active tab-project-home" id="tab_lastest_projects">
                                        <?php get_template_part( 'list', 'projects' ); ?>
                                    </div>
                                    <?php query_posts( array('post_type' => PROJECT , 'meta_key' => 'et_featured', 'meta_value' => 1,  'posts_per_page' => $showposts , 'post_status' => 'publish') ); ?>
                                    <div class="tab-pane fade in tab-project-home " id="tab_featured_projects">
                                         <?php get_template_part( 'list', 'projects' ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php wp_reset_query(); ?>

        <?php         
        $output = ob_get_clean();
        /* ================  Render Shortcodes ================ 
        return $output;
    }
}


vc_map( array(
    "base"      => "fre_list_projects",
    "name"      => __("List projects", ET_DOMAIN),
    "class"     => "",
    "icon"      => "",
    "category" => __("FreelanceEngine", ET_DOMAIN),
    "params"    => array(
        array(
            "type" => "textfield",
            "heading" => __("Title", ET_DOMAIN),
            "class" => "input-title",
            "param_name" => "s_title",
            "value"     => 'THE FREELANCE MARKETPLACE WP THEME MADE BY ENGINETHEMES'
        ),
        array(
            "type" => "textfield",
            "heading" => __("Number of posts", ET_DOMAIN),
            "class" => "input-title",
            "param_name" => "showposts",
            "value"     => '10'
        ),        
        array(
            "type" => "checkbox",
            //"heading" => __("Enable featured list", ET_DOMAIN),
            "class" => "input-description",
            "param_name" => "featured",
             "value"      => Array(__('Use featured list', ET_DOMAIN) =>true )
        )
    )
));


*/



