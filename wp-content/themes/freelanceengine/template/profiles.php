<?php

if(!class_exists('WPBakeryShortCode')) return;

class WPBakeryShortCode_fre_block_profile extends WPBakeryShortCode {



    protected function content($atts, $content = null) {



        $custom_css = $el_class = $title = $icon = $output = $s_content = $m_link = '';



        extract(shortcode_atts(array(

            'el_class'  => '',

            'showposts' => 200, 

            'orderby' => 'date',

            'paginate' => 'page',

            // 'query' => ''

        ), $atts));

        /* ================  Render Shortcodes ================ */

        ob_start();

        $query_args = array(   'post_type' => PROFILE , 

                                'post_status' => 'publish' , 

                                'posts_per_page' => $showposts

                            ) ;

        $key = '';

        if($orderby == 'rating') {

            $key = 'rating_score';

        }



        if($orderby == 'hourly_rate') {

            $key = 'hour_rate';

            $query_args['order'] = 'ASC';

        }



        if( $orderby != '' ) {

            $query_args['meta_key'] = $key;

            $query_args['orderby'] =  'meta_value_num date';

            $query_args['meta_query'] =  array(

                'relation' => 'OR',

                array( //check to see if et_featured has been filled out

                    'key' => $key,

                    'compare' => 'BETWEEN',

                    'value' => array(0,5)

                ),

                array( //if no et_featured has been added show these posts too

                    'key' => $key,

                    'value' => 1,

                    'compare' => 'NOT EXISTS'

                )

            );



        }

       

        ?>

        <!-- COUNTER -->

        <div class="section-wrapper section-project-home">

            <div class="list-profile-wrapper">

                

                <div class="tab-content-profile">

                    <!-- Tab panes -->

                    <div class="tab-content vc-block-profiles">

                        <!-- Tab panes -->

                        

                        <?php query_posts( $query_args); ?>



                        <div class="tab-pane fade in active tab-profile-home">

                            <div class="row">

                                <?php

                                /**

                                 * Template list profiles

                                */

                                global $wp_query, $ae_post_factory, $post;

                                $post_object = $ae_post_factory->get( PROFILE );

                                ?>

                                <div class="list-profile profile-list-container">

                                     <!-- block control  -->

                                    <?php 

                                    if(have_posts()) {

                                        $postdata = array();

                                        while(have_posts()) { the_post(); 

                                            $convert = $post_object->convert($post);
                                            
                                            echo $current_user = $convert->post_author; exit;
                                            $user = get_userdata( $current_user );
                                            $capabilities = $user->{$wpdb->prefix . 'capabilities'};
                                            
                                            $postdata[] = $convert;

                                            get_template_part('template/profile', 'item' );

                                        }

                                        /**

                                        * render post data for js

                                        */    

                                        echo '<script type="data/json" class="postdata" >'.json_encode($postdata).'</script>';

                                    }   

                                    ?>

                                </div>

                                <div class="clearfix"></div>

                                <!--// blog list  -->

                                <!-- pagination -->

                                <div class="col-md-12">

                                    <?php

                                        if($paginate == 'page' || $paginate == 'load_more') {

                                            echo '<div class="paginations-wrapper">';

                                            ae_pagination($wp_query, get_query_var('paged'), $paginate);

                                            echo '</div>';         

                                        }

                                    ?>

                                </div>

                            </div>

                        </div>

                        <?php wp_reset_query(); ?> 

                    </div>

                </div>

            </div>

        </div>

        <?php         

        $output = ob_get_clean();

        /* ================  Render Shortcodes ================ */

        return $output;

    }

}





vc_map( array(

    "base"      => "fre_block_profile",

    "name"      => __("List profiles", ET_DOMAIN),

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

            "heading"    => __("Orderby", ET_DOMAIN),

            "param_name" => "orderby",

            "value"      => array('Date' => 'date', 'Rating' => 'rating' , 'Hourly' => 'hourly_rate'),

        ),

        // array(

        //     "type"       => "dropdown",

        //     "class"      => "",

        //     "heading"    => __("Query", ET_DOMAIN),

        //     "param_name" => "query",

        //     "value"      => array('Featured Posts' => 'featured', 'Recent Posts' => 'recent'),

        // ), 

        array(

            "type"       => "dropdown",

            "class"      => "",

            "heading"    => __("Paginate", ET_DOMAIN),

            "param_name" => "paginate",

            "value"      => array('none' => '0', 'Page paginate' => 'page', 'Load More' => 'load_more'),

        )

        // , 

        // array(

        //     "type" => "checkbox",

        //     //"heading" => __("Enable featured list", ET_DOMAIN),

        //     "class" => "input-description",

        //     "param_name" => "featured",

        //      "value"      => Array(__('Featured', ET_DOMAIN) =>true )

        // )

    )

));



/*



chỗ project list, thì là "Looking for Professional Freelancers"

[6:05:41 PM] Việt Anh: profile list, thì là "Looking for Available Projects"



class WPBakeryShortCode_fre_list_profiles extends WPBakeryShortCode {



    protected function content($atts, $content = null) {



        $custom_css = $el_class = $title = $icon = $output = $s_content = $m_link = '';



        extract(shortcode_atts(array(

            'el_class'      => '',

            'title'         => __("Profiles", ET_DOMAIN),

            's_title'     => '',

            's_description'        => '',

            'showposts' => 10

        ), $atts));

        /* ================  Render Shortcodes ================ 

        ob_start();

        ?>

        <!-- COUNTER -->

        <?php

        global $wp_query;

        query_posts( array('post_type' => PROFILE , 'post_status' => 'publish', 'posts_per_page' => $showposts) );

        ?>

        

        <section class="section-wrapper section-profile">

            <div class="number-profile-wrapper">

                <div class="container">

                    <div class="row">

                        <div class="col-md-12">

                            <h2 class="number-profile"><?php printf(__("%d Profiles", ET_DOMAIN), $wp_query->found_posts ); ?></h2>

                            <div class="nav-tabs-profile">

                                <?php fre_profile_button(); ?>

                                

                                <!-- Nav tabs -->

                                <ul class="nav nav-tabs" role="tablist" id="myTab">

                                    <li class="active">

                                        <a href="#tab_lastest_profile" role="tab" data-toggle="tab">

                                            <?php _e("Lastest Profile", ET_DOMAIN); ?>

                                        </a>

                                    </li>

                                    <li>

                                        <a href="#tab_featured_profile" role="tab" data-toggle="tab">

                                            <?php _e("Featured Profile", ET_DOMAIN); ?>

                                        </a>

                                    </li>

                                </ul>

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

                                <div class="tab-content vc-block-profiles">

                                    

                                    <div class="tab-pane fade in active tab-profile-home" id="tab_lastest_profile">

                                        <div class="row">

                                            <?php get_template_part( 'list', 'profiles' ); ?>

                                        </div>

                                    </div>

                                    <div class="tab-pane fade tab-profile-home" id="tab_featured_profile">

                                        <div class="row">

                                        <?php 

                                            query_posts( array('post_type' => PROFILE , 'meta_key' => 'et_featured', 'meta_value' => 1, 'post_status' => 'publish', 'posts_per_page' => $showposts) );

                                            get_template_part( 'list', 'profiles' ); 

                                        ?>

                                        </div>

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

    "base"      => "fre_list_profiles",

    "name"      => __("List profiles", ET_DOMAIN),

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