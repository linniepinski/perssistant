<?php

if(!class_exists('WPBakeryShortCode')) return;

class WPBakeryShortCode_fre_block_profile extends WPBakeryShortCode {



    protected function content($atts, $content = null) {


        $custom_css = $el_class = $title = $icon = $output = $s_content = $m_link = '';


        extract(shortcode_atts(array(

            'el_class'  => '',

            'showposts' => 10, 

            'orderby' => 'date',

            'paginate' => 'page',

            // 'query' => ''

        ), $atts));

        /* ================  Render Shortcodes ================ */
        $select_available_users = array(
                'role' => 'freelancer',
                'number' => 100,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key'     => 'user_available',
                        'value'   => 'on',
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'interview_status',
                        'value'   => array('confirmed' ),
                        'compare' => 'OR'
                    ),
                ),

                'fields' => 'ID'

            );
            $result = new WP_User_Query($select_available_users);
        ob_start();
        $query_args = array(   'post_type' => PROFILE ,

                                'post_status' => array('draft','publish') ,

                                'posts_per_page' => 10,
                                'author__in' => $result->get_results()

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
                                            $current_user = $convert->post_author;
                                            $user = get_userdata( $current_user );
                                            $capabilities = $user->roles[0];
//SELECT * FROM wp_posts INNER JOIN wp_usermeta ON wp_posts.post_author=wp_usermeta.user_id WHERE post_type = 'fre_profile' AND meta_key = 'unconfirm'
//                                            if ($capabilities == 'freelancer' && get_user_meta($current_user,'interview_status',true) != 'unconfirm' ) {
                                                $postdata[] = $convert;

                                                get_template_part('template/profile', 'item' );                                            
//                                            }
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

    "name"      => __("List profiles", 'vc_blocks-profiles-backend'),

    "class"     => "",

    "icon"      => "",

    "category" => __("FreelanceEngine", 'vc_blocks-profiles-backend'),

    "params"    => array(



        array(

            "type" => "textfield",

            "heading" => __("Number of posts", 'vc_blocks-profiles-backend'),

            "class" => "input-title",

            "param_name" => "showposts",

            "value"     => '10'

        ),  

        array(

            "type"       => "dropdown",

            "class"      => "",

            "heading"    => __("Orderby", 'vc_blocks-profiles-backend'),

            "param_name" => "orderby",

            "value"      => array('Date' => 'date', 'Rating' => 'rating' , 'Hourly' => 'hourly_rate'),

        ),

        // array(

        //     "type"       => "dropdown",

        //     "class"      => "",

        //     "heading"    => __("Query", 'vc_blocks-profiles-backend'),

        //     "param_name" => "query",

        //     "value"      => array('Featured Posts' => 'featured', 'Recent Posts' => 'recent'),

        // ), 

        array(

            "type"       => "dropdown",

            "class"      => "",

            "heading"    => __("Paginate", 'vc_blocks-profiles-backend'),

            "param_name" => "paginate",

            "value"      => array('none' => '0', 'Page paginate' => 'page', 'Load More' => 'load_more'),

        )

        // , 

        // array(

        //     "type" => "checkbox",

        //     //"heading" => __("Enable featured list", 'vc_blocks-profiles-backend'),

        //     "class" => "input-description",

        //     "param_name" => "featured",

        //      "value"      => Array(__('Featured', 'vc_blocks-profiles-backend') =>true )

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

            'title'         => __("Profiles", 'vc_blocks-profiles-backend'),

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

                            <h2 class="number-profile"><?php printf(__("%d Profiles", 'vc_blocks-profiles-backend'), $wp_query->found_posts ); ?></h2>

                            <div class="nav-tabs-profile">

                                <?php fre_profile_button(); ?>

                                

                                <!-- Nav tabs -->

                                <ul class="nav nav-tabs" role="tablist" id="myTab">

                                    <li class="active">

                                        <a href="#tab_lastest_profile" role="tab" data-toggle="tab">

                                            <?php _e("Lastest Profile", 'vc_blocks-profiles-backend'); ?>

                                        </a>

                                    </li>

                                    <li>

                                        <a href="#tab_featured_profile" role="tab" data-toggle="tab">

                                            <?php _e("Featured Profile", 'vc_blocks-profiles-backend'); ?>

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

    "name"      => __("List profiles", 'vc_blocks-profiles-backend'),

    "class"     => "",

    "icon"      => "",

    "category" => __("FreelanceEngine", 'vc_blocks-profiles-backend'),

    "params"    => array(

        array(

            "type" => "textfield",

            "heading" => __("Title", 'vc_blocks-profiles-backend'),

            "class" => "input-title",

            "param_name" => "s_title",

            "value"     => 'THE FREELANCE MARKETPLACE WP THEME MADE BY ENGINETHEMES'

        ),

        array(

            "type" => "textfield",

            "heading" => __("Number of posts", 'vc_blocks-profiles-backend'),

            "class" => "input-title",

            "param_name" => "showposts",

            "value"     => '10'

        ),

        array(

            "type" => "checkbox",

            //"heading" => __("Enable featured list", 'vc_blocks-profiles-backend'),

            "class" => "input-description",

            "param_name" => "featured",

             "value"      => Array(__('Use featured list', 'vc_blocks-profiles-backend') =>true )

        )

    )

));

*/