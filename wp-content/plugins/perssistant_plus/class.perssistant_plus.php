<?php

/**
 * Created by PhpStorm.
 * User: ANDREY
 * Date: 27.10.15
 * Time: 13:02
 */
class perssistant_plus
{
    public static function Init()
    {
        wp_enqueue_script('front_page_js', plugin_dir_url(__FILE__) . 'script.js', array('jquery'));
        wp_localize_script('front_page_js', 'MyAjax', array('ajaxurl' => admin_url('admin-ajax.php')));

        wp_register_style('perssistant_plus', plugin_dir_url(__FILE__) . 'style.css');
        wp_enqueue_style('perssistant_plus');

        add_action('perssistant_plus', array('perssistant_plus', 'pers_plus_post'));

        add_action('wp_footer', array('perssistant_plus', 'stripe_form'));
        add_action('perssistant_plus', array('perssistant_plus', 'display_perssistant_plus'));
        // add_action('admin_menu', 'perssistant_plus_menu');
        // add_action('admin_menu', array('perssistant_plus', 'perssistant_plus_menu'));
        //add_options_page( 'WP perssistant_plus', 'WP perssistant_plus', 'manage_options', 'class.perssistant_plus', 'wp_perssistant_plus_options_page' );

        add_filter('query_vars', array('perssistant_plus', 'add_query_vars_filter'), 10, 1);

        add_shortcode('perssistant_plus', array('perssistant_plus', 'display_perssistant_plus'));
        add_shortcode('paymill', array('perssistant_plus', 'display_paymill_btn'));
        add_shortcode('our_clients', array('perssistant_plus', 'our_clients_about_us'));
        add_shortcode('testimonial', array('perssistant_plus', 'testimonial_view'));
        $labels = array(
            'name' => _x('Perssistant Plus', 'post type general name', 'perssistant_plus'),
            'singular_name' => _x('perssistantplus2', 'post type singular name', 'perssistant_plus'),
            'menu_name' => _x('Perssistant Plus', 'admin menu', 'perssistant_plus'),
            'name_admin_bar' => _x('perssistantplus4', 'add new on admin bar', 'perssistant_plus'),
            'add_new' => _x('Add New', 'perssistantplus', 'perssistant_plus'),
            'add_new_item' => __('Add New perssistantplus5', 'perssistant_plus'),
            'new_item' => __('New perssistantplus7', 'perssistant_plus'),
            'edit_item' => __('Edit plan', 'perssistant_plus'),
            'view_item' => __('View Plans', 'perssistant_plus'),
            'all_items' => __('Perssistant Plus', 'perssistant_plus'),
            'search_items' => __('Search plans', 'perssistant_plus'),
            'parent_item_colon' => __('Parent perssistantplus12:', 'perssistant_plus'),
            'not_found' => __('No plans found.', 'perssistant_plus'),
            'not_found_in_trash' => __('No plans found in Trash.', 'perssistant_plus')
        );

        $args = array(
            'labels' => $labels,
            'description' => __('Description.', 'perssistant_plus'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'perssistantplus'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments')
        );

        //register_post_type('perssistantplus', $args);
        ///add_meta_box("credits_meta", "Design &amp; Build Credits", "credits_meta", "portfolio", "normal", "low");

    }


    public static function display_paymill_btn($atts, $content = null)
    {
        ?>
        <form action="/payment" class="payment_paymill" method="post">
            <script
                src="https://button.paymill.com/v1/"
                id="buttonPayment<?php echo $atts['id'] ?>"
                data-label="Buy"
                data-title="<?php echo $atts['title'] ?>"
                data-description=""
                data-submit-button="Pay <?php echo $atts['amount'] ?> EUR"
                data-amount="<?php echo $atts['amount'] * 100 ?>"
                data-currency="EUR"
                data-elv="false"
                data-public-key="<?php echo get_option('paymill_public_key'); ?>"
                data-inline="true"
                ></script>
            <input type="hidden" name="amount" value="<?php echo $atts['amount'] * 100 ?>">

        </form>
    <?php
    }

    public static function display_perssistant_plus($atts, $content = null)
    {

        $args = array(
            'post_type' => 'perssistantplus',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'caller_get_posts' => 1,
            'order' => 'DESC'
        );

        $result = null;
        $result = new WP_Query($args);
        //var_dump($result);
        ?>

        <div class="pricing-container">
        <a name="plans"></a>

        <div class="container">
            <div class="row">

                <?php
                $i = 0;
                if ($result->have_posts()) {
                    while ($result->have_posts()) : $result->the_post();
                        $i++;
                        $price = get_field('price', get_the_ID());
                        $no_of_hours = get_field('no_of_hours', get_the_ID());
                        $dedicated_va = get_field('dedicated_va', get_the_ID());
                        $working_hours = get_field('working_hours', get_the_ID());
                        $customer_support = get_field('customer_support', get_the_ID());
                        $same_day_turn_around = get_field('same_day_turn_around', get_the_ID());
                        $email_phone_support = get_field('email_phone_support', get_the_ID());
                        // $contacts_support = get_field('contacts_support', get_the_ID());
//var_dump($contacts_support);
                        $plan_description = '<p class="line-height-custom">';

                        if ($no_of_hours > 0) {
                            $plan_description .= $no_of_hours . ' hours of work<br>';
                        }
//var_dump($dedicated_va[0]);
                        if (get_the_title() == 'Entrepreneur plan' && $dedicated_va[0] == 'Yes') {
                            $plan_description .= 'a dedicated VA based in Europe<br>';
                        } elseif ($dedicated_va[0] == 'Yes') {
//                            $plan_description .= 'a dedicated VA based in Europe<br>';

                            $plan_description .= 'a dedicated VA Europe based ( or at extra charge your country based) <br>';
                        }

                        if ($working_hours > 0) {
                            $plan_description .= 'Working hours ' . $working_hours . '<br>';
                        }

                        if ($customer_support > 0) {
                            $plan_description .= 'customer support ' . $customer_support . '<br>';
                        }

                        if ($same_day_turn_around == 'Yes') {
                            $plan_description .= 'Same day turn around<br>';
                        }

                        if ($email_phone_support == 'Yes') {
                            $plan_description .= 'Email, SMS and Phone support <br>';
                        }

                        $plan_description .= '</p>';

                        ?>
                        <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
                            <div class="team-block">
                                <div class="team-img-wrapper">
                                    <div class="team-img out-div">
                                        <div class="pricing-number pricing-wrapper in-div">
                                            <h2 class="price"><?php echo get_the_title(); ?></h2>
                                        </div>
                                    </div>
                                    <div class="team-name">
                                        <?php echo $price . '<sup>&euro;</sup>'; ?>
                                    </div>
                                </div>
                                <div class="team-desc-wrapper">
                                    <div class="team-content">
                                        <?php echo $plan_description; ?>
                                    </div>
                                    <?php
                                    echo do_shortcode('[paymill id="' . $i . '" amount=' . $price . ' title="' . get_the_title() . '" ]');
                                    ?>
                                </div>
                            </div>
                        </div>

                    <?php

                    endwhile;
                    wp_reset_query();
                }
                ?>
            </div>
            <div class="col-xs-12 text-center" style="margin-top: 25px;">
                <!--                <img src="/wp-content/plugins/wp-stripe/images/types.png">-->
            </div>
        </div>


        <div class="container">
            <div class="row">
                <hr>
                <div class="col-xs-12">
                    <h4 style="text-align: center;"><strong>Recommend someone and get 50 eur on your account. The person
                            you recommended will get 25 euro on his/her account.</strong></h4>
                </div>
            </div>
            <div class="row">
                <hr>
                <div class="col-xs-12">
                    <h3 style="text-align: center;">Customized plan</h3>

                    <p style="text-align: center;"><a href="http://www.perssistant.com/contact/">Contact us for your
                            special wishes and needs.</a></p>

                    <p style="text-align: center;">Need a bigger plan? No problem. Get started and we’ll create a plan
                        for you based on your exact requirements.</p>

                    <p style="text-align: center;">Web development and design tasks are billed in addition to the plans
                        above. Please contact us for rate information.</p>

                    <img src="http://perssis.ai.ukrosoft.com.ua/wp-content/uploads/2015/12/8.png"
                         class="img-responsive img-center">
                </div>
            </div>
            <div class="row">
                <hr>
                <div class="col-xs-12">
                    <h3 style="text-align: center;"><strong>Start focusing on stream lining your work and your life
                            now!</strong></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 text-center">
                    <button style="height: initial;" class="btn btn-success btn-lg">Request A Consultation</button>
                </div>
            </div>
        </div>


    <?php

    }
}

function requestApi($action = '', $params = array(), $privateApiKey)
{
    $curlOpts = array(
        CURLOPT_URL => "https://api.paymill.com/v2/" . $action,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_USERAGENT => 'Paymill-php/0.0.2',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CAINFO => realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'paymill.crt',
    );

    $curlOpts[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
    $curlOpts[CURLOPT_USERPWD] = $privateApiKey . ':';

    $curl = curl_init();
    curl_setopt_array($curl, $curlOpts);
    $responseBody = curl_exec($curl);
    $responseInfo = curl_getinfo($curl);
    if ($responseBody === false) {
        $responseBody = array('error' => curl_error($curl));
    }
    curl_close($curl);

    if ('application/json' === $responseInfo['content_type']) {
        $responseBody = json_decode($responseBody, true);
    }

    return array(
        'header' => array(
            'status' => $responseInfo['http_code'],
            'reason' => null,
        ),
        'body' => $responseBody
    );
}

/**
 * Perform API and handle exceptions
 *
 * @param        $action
 * @param array $params
 * @param string $privateApiKey
 *
 * @return mixed
 */
function request($action, $params = array(), $privateApiKey)
{
    if (!is_array($params)) {
        $params = array();
    }

    $responseArray = requestApi($action, $params, $privateApiKey);
    $httpStatusCode = $responseArray['header']['status'];
    if ($httpStatusCode != 200) {
        $errorMessage = 'Client returned HTTP status code ' . $httpStatusCode;
        if (isset($responseArray['body']['error'])) {
            $errorMessage = $responseArray['body']['error'];
        }
        $responseCode = '';
        if (isset($responseArray['body']['data']['response_code'])) {
            $responseCode = $responseArray['body']['data']['response_code'];
        }

        return array("data" => array(
            "error" => $errorMessage,
            "response_code" => $responseCode,
            "http_status_code" => $httpStatusCode
        ));
    }

    return $responseArray['body']['data'];
}