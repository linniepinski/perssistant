<?php
if(!class_exists('WPBakeryShortCode')) return;
class WPBakeryShortCode_fre_testimonial_block extends WPBakeryShortCode {

    protected function content($atts, $content = null) {

        $custom_css = $el_class = $title = $icon = $output = $s_content = $number = '' ;

        extract(shortcode_atts(array(
            'el_class'      => '',
            // 'title'         => __('TESTIMONIALS BLOCK',ET_DOMAIN),
            's_content'     => '',
            'number'        => 3,
        ), $atts));

        $query = new WP_Query(array(
                'post_type' => 'testimonial',
                'showposts' => $number
            ));
        /* ================  Render Shortcodes ================ */
        ob_start();
        ?>
        <!-- TESTIMONIAL -->
        <section class="testimonial-wrapper <?php echo $el_class; ?>">
            <div class="container">
                <div class="row">
                    <?php 
                        global $post;
                        if($query->have_posts()){
                            while($query->have_posts()){
                                $query->the_post();
                    ?>
                    <div class="col-md-4">
                        <div class="testimonial">
                            <div class="test-content">
                                <?php the_content( ); ?>
                            </div>
                            <div class="test-info">
                                <span class="test-avatar">
                                    <?php the_post_thumbnail( 'thumbnail' ); ?>
                                </span>
                                <span class="test-name">
                                    <?php the_title() ?><span class="test-position"><?php echo get_post_meta( $post->ID, '_test_category', true ); ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php
                            }
                        }
                        wp_reset_query();
                    ?>
                </div>
                <!-- <div class="col-md-12">
                    <a href="#" class="view-all-test">
                        <?php _e("View All Testimonials", ET_DOMAIN) ?>&nbsp;&nbsp;&nbsp;<i class="fa fa-angle-double-right"></i>
                    </a>
                </div> -->
            </div>
        </section>
        <!-- TESTIMONIAL / END -->

        <?php         
        $output = ob_get_clean();
        /* ================  Render Shortcodes ================ */
        return $output;
    }
}


vc_map( array(
    "base"      => "fre_testimonial_block",
    "name"      => __("FrE Testimonial", ET_DOMAIN),
    "class"     => "",
    "icon"      => "icon-wpb-de_service",
    "category"  => __("FreelanceEngine", ET_DOMAIN),
    "params"    => array(
        array(
            "type" => "textfield",
            "heading" => __("Extra class name", ET_DOMAIN),
            "param_name" => "el_class",
            "description" => __("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", ET_DOMAIN)
        )
    )
));