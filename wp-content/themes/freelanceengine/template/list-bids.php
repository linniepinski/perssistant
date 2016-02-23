<?php

/**
 * The template for display a list bids of a project
 * @since 1.0
 * @author Dakachi
 */

global $wp_query, $ae_post_factory, $post, $user_ID;

$post_object = $ae_post_factory->get(PROJECT);


$project = $post_object->current_post;


$number_bids = (int)get_number_bids(get_the_ID());

//$sum            = (float) get_total_cost_bids( get_the_ID() );

add_filter('posts_orderby', 'fre_order_by_bid_status');

$q_bid = new WP_Query(array('post_type' => BID,

        'post_parent' => get_the_ID(),

        'post_status' => array('publish', 'complete', 'accept')

    )

);

remove_filter('posts_orderby', 'fre_order_by_bid_status');

$biddata = array();

?>

    <div class="col-xs-12 col-md-8">


        <div class="row title-tab-project <?php if ($q_bid->found_posts < 1) echo 'display-none' ?>">

            <div class="col-xs-4 col-md-4">

                <span><?php printf(__('FREELANCER BIDDING (%s)', ET_DOMAIN), $number_bids); ?></span>

            </div>

            <div class="col-xs-3 col-md-3">

                <span><?php _e('REPUTATION', ET_DOMAIN); ?></span>

            </div>

            <div class="col-xs-5 col-md-5">

                <span><?php _e('BID', ET_DOMAIN); ?></span>

            </div>

        </div>

        <div class="info-bidding-wrapper project-<?php echo $project->post_status; ?>">

            <?php


            if ($q_bid->have_posts()):


                global $wp_query, $ae_post_factory, $post;

                $post_object = $ae_post_factory->get(BID);


                while ($q_bid->have_posts()) :$q_bid->the_post();

                    $convert = $post_object->convert($post);

                    $biddata[] = $convert;

                    get_template_part('template/bid', 'item');

                endwhile;


                echo '<div class="paginations-wrapper">';

                $q_bid->query = array_merge($q_bid->query, array('is_single' => 1));

                ae_pagination($q_bid, get_query_var('paged'), 'load');

                echo '</div>';


            else :

                get_template_part('template/bid', 'not-item');

            endif;

            ?>

        </div>

    </div>

    <input type="hidden" id="project_id" name="<?php echo $project->ID; ?>" value="<?php echo $project->ID; ?>"/>

    <div class="col-md-4">

        <?php
        $project_status = $project->post_status;
        $bid_accept = get_post_meta($project->ID, 'accepted', true);

        if ($bid_accept && $project_status == 'close') {
            ?>
            <div class="row title-tab-project">

                <div class="col-md-12">
                    <?php if ((ae_user_role($user_ID) == FREELANCER) || !is_user_logged_in()) { ?>
                        <span><?php _e('ABOUT EMPLOYER', ET_DOMAIN); ?></span>
                    <?php } else { ?>
                        <span><?php _e('ABOUT VIRTUAL ASSISTANT', ET_DOMAIN); ?></span>
                    <?php } ?>

                </div>

            </div>
            <?php
        }

        if ($bid_accept && $project_status == 'close') {
            ?>

            <div class="info-company-wrapper">

            <div class="row">

            <div class="col-md-12">
            <?php


            if ((ae_user_role($user_ID) == FREELANCER) || !is_user_logged_in()) {
                fre_display_user_info($project->post_author);
            } else {
                $bid_accepted = $project->accepted;
                $bid_accepted_author = get_post_field('post_author', $bid_accepted);

                $user = get_userdata($bid_accepted_author);

                $ae_users = AE_Users::get_instance();

                $user_data = $ae_users->convert($user);

                $author_email_verified = (ae_get_option('user_confirm') && get_user_meta($user_data->ID, 'register_status', true) == "unconfirm") ? false : true;
                $author_phone_verified = (get_user_meta($user_data->ID, 'phone', true) != "") ? true : false;

                $rating = Fre_Review::freelancer_rating_score($user_data->ID);

                $profile_id = get_user_meta($user_data->ID, 'user_profile_id', true);

                $hourly_rate_price = get_post_meta($profile_id, 'hour_rate', true);
                $experience = get_post_meta($profile_id, 'et_experience', true);
                $country = get_user_meta($user_data->ID, 'location', true);
                $currency = ae_get_option('content_currency', array('align' => 'left', 'code' => 'USD', 'icon' => '$'));

                #get country code
                $arrCountry = get_country_name_by_country_code($country);

                #get bid detail
                query_posts(array('post_status' => array('complete'), 'post_type' => BID, 'author' => $bid_accepted_author, 'accepted' => 1));
                $bid_posts = $wp_query->found_posts;
                ?>
                <div class="info-company-avatar">
                    <a href="<?php echo get_author_posts_url($user_data->ID); ?>">
                        <span class="info-avatar">
                            <?php
                            echo get_avatar($user_data->ID, 35);
                            ?>
                        </span>
                    </a>
                    <div class="info-company">
                        <h3 class="info-company-name"><?php echo $user_data->display_name; ?></h3>
                        <span class="time-since">
                           <?php printf(__('Member Since %s', ET_DOMAIN), date(get_option('date_format'), strtotime($user_data->user_registered))); ?>
                        </span>
                    </div>
                </div>

                <ul class="list-detail-info">

                    <li>
                        <i class="fa fa-envelope"></i>
                        <span class="text"><?php _e('Email Verified:', ET_DOMAIN); ?></span>
                        <span class="text-right verified"><?php if ($author_email_verified) {
                                echo "<i class='fa fa-check'></i> Verified";
                            } else {
                                echo "Not Verified";
                            } ?></span>
                    </li>

                    <li>
                        <i class="fa fa-phone"></i>
                        <span class="text"><?php _e('Phone Verified:', ET_DOMAIN); ?></span>
                        <span class="text-right verified"><?php if ($author_phone_verified) {
                                echo "<i class='fa fa-check'></i> Verified";
                            } else {
                                echo "Not Verified";
                            } ?></span>
                    </li>

                    <li>
                        <i class="fa fa-dollar"></i>
                        <span class="text"><?php _e('Hourly Rate:', ET_DOMAIN); ?></span>
                        <span class="text-right"><?php echo $hourly_rate_price . $currency['icon'] . '/h'; ?></span>
                    </li>
                    <li>
                        <i class="fa fa-star"></i>
                        <span class="text"><?php _e('Rating:', ET_DOMAIN); ?></span>
                        <div class="rate-it" data-score="<?php echo $rating['rating_score']; ?>"></div>
                    </li>
                    <li>
                        <i class="fa fa-pagelines"></i>
                        <span class="text"><?php _e('Experience:', ET_DOMAIN); ?></span>
                        <span class="text-right"><?php echo $experience . ' years'; ?></span>
                    </li>
                    <li>
                        <i class="fa fa-briefcase"></i>
                        <span class="text"><?php _e('Projects worked:', ET_DOMAIN); ?></span>
                        <span class="text-right"><?php echo $bid_posts; ?></span>
                    </li>

                    <li>
                        <i class="fa fa-money"></i>
                        <span class="text"><?php _e('Total earned:', ET_DOMAIN); ?></span>
                        <span
                            class="text-right"><?php echo fre_price_format(fre_count_total_user_earned($user_data->ID)); ?></span>
                    </li>

                    <li>
                        <i class="fa fa-map-marker"></i>
                        <span class="text"><?php _e('Country:', ET_DOMAIN); ?></span>
                        <span class="text-right">
                            <?php
                            echo $arrCountry->country_name;
                            ?>
                        </span>
                    </li>
                </ul>


                </div>

                </div>

                </div>
                <?php
            }
        }
        ?>
    </div>
</div>


<?php

if (!empty($biddata)) {

    echo '<script type="data/json" class="biddata" >' . json_encode($biddata) . '</script>';

}

