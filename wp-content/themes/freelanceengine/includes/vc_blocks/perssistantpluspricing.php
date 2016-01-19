<?php

if(!class_exists('WPBakeryShortCode')) return;

class WPBakeryShortCode_perssistant_pricing_block extends WPBakeryShortCode {



    protected function content($atts, $content = null) {



        $custom_css = $el_class = $title = $icon = $output = $s_content = $m_link = $payment_plan_feature = $payment_plan = '';



        extract(shortcode_atts(array(

            'el_class'      => '',

            'perssistant_plan'     => '',

            'number_plan' => 4

            

        ), $atts));

        /* ================  Render Shortcodes ================ */

        ob_start();

        ?>

        <!-- PRICING -->

        <?php

        global $post;
        
        $args = array(
            'post_type' => 'perssistantplus',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'caller_get_posts'=> 1,
            'order' => 'ASC'
        );
        
        $result = null;
        $result = new WP_Query($args);

        $pack_currency = ae_get_option('currency');

        $i = 0;
        

        $col = 'col-md-3 col-sm-6 col-xs-6';

        $class_width='';

        if($number_plan == 3) {

            $col = 'col-md-4 col-sm-4 col-xs-6';

            $class_width='width-880';

        }



        if($number_plan == 2) {

            $col = 'col-md-6 col-sm-6 col-xs-6';

            $class_width='width-580';

        }





        ?>

        <div class="pricing-container">

            <div class="container  <?php echo $class_width ?> " >

                <div class="row">

                    <?php
                        if ($result->have_posts()) {
                            while ($result->have_posts()) : $result->the_post(); 
                            $price                  = get_field('price', get_the_ID());
                            $no_of_hours            = get_field('no_of_hours', get_the_ID());
                            $dedicated_va           = get_field('dedicated_va', get_the_ID());
                            $working_hours          = get_field('working_hours', get_the_ID());
                            $customer_support       = get_field('customer_support', get_the_ID());
                            $same_day_turn_around   = get_field('same_day_turn_around', get_the_ID());
                            $email_phone_support    = get_field('email_phone_support', get_the_ID());

                            $plan_description = '<p>';                           

                            if ($no_of_hours > 0 ) {
                                $plan_description .= $no_of_hours.' hours of work<br>';
                            }

                            if (get_the_title() == 'Entrepreneur plan' && $dedicated_va == 'Yes') {
                                $plan_description .= '<p>A dedicated VA based in Europe </p>';
                            } elseif ($dedicated_va == 'Yes') {
                                $plan_description .= 'Andedicated VA Europe based ( or at extra charge your country based)</br>';
                            }

                            if ($working_hours > 0) {
                                 $plan_description .= 'Working hours ' .$working_hours. '<br>';
                            }

                            if ($customer_support > 0) {
                                 $plan_description .= 'customer supportt '.$customer_support.'<br>';
                            }

                            if ($same_day_turn_around == 'Yes') {
                                 $plan_description .= 'Same day turn around<br>';
                            }

                            if ($email_phone_support == 'Yes') {
                                 $plan_description .= 'Email, SMS and Phone support <br>';
                            }
                            
                            $plan_description .= '</p>';

                        ?>                          

                                <div class="<?php echo $col ?> pricing-item">

                                    <div class="pricing">

                                        <div class="pricing-number pricing-wrapper">

                                            <h2 class="price"><?php echo ae_price($price); ?></h2>

                                            <span>
                                                
                                                <?php echo get_the_title(); ?>                                             

                                            </span>

                                        </div>

                                        <div class="pricing-content">

                                            <div class="pricing-detail">

                                                <?php echo $plan_description; ?>

                                            </div>

                                            <div class="submit-price">

                                                <a href="#" class="btn-sumary btn-price">

                                                    <?php 

                                                    if(!is_user_logged_in() ) { 

                                                        _e('Sign Up', ET_DOMAIN);

                                                    }else{ 

                                                        _e("Buy Plan", ET_DOMAIN);

                                                    }?>

                                                </a>

                                            </div>

                                        </div> 

                                    </div>

                                </div>

                        <?php
                            endwhile;
                            wp_reset_query();
                        }  
                    ?>
                </div>

            </div>

        </div>

        <?php

        $output = ob_get_clean();

        /* ================  Render Shortcodes ================ */

        return $output;

    }

}



// $packs = query_posts( 'post_type=pack' );

// $packs_arr= array();

// foreach ($packs as $key => $package){

//     $packs_arr[$package->post_title] = $package->ID;

// }

// wp_reset_query();                  

vc_map( array(

    "base"      => "perssistant_pricing_block",

    "name"      => __("Perssistant Pricing", ET_DOMAIN),

    "class"     => "",

    "icon"      => "icon-wpb-de_service",

    "category" => __("FreelanceEngine", ET_DOMAIN),

    "params"    => array(

        // array (

        //     "type"       => "textfield",

        //     "class"      => "",

        //     "heading"    => __("Tagline", ET_DOMAIN),

        //     "param_name" => "tagline",

        //     "value"      => 'per month'

        // ),

        array(

            "type"       => "dropdown",

            "class"      => "",

            "heading"    => __("Number of Plan", ET_DOMAIN),

            "param_name" => "number_plan",

            "value"      => array( '4' => '4', '3' => '3', '2' => '2' ),

        )

    )

));









