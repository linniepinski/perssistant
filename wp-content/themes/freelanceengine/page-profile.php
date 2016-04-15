<?php
/**
 * Template Name: Member Profile Page
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other 'pages' on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */

global $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
//convert current user
$ae_users = AE_Users::get_instance();
$user_data = $ae_users->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);
//convert current profile
$post_object = $ae_post_factory->get(PROFILE);

$profile_id = get_user_meta($user_ID, 'user_profile_id', true);
$user_mobile = get_user_meta($user_ID, 'phone', true);

#get country list
$country_list = ae_country_list();

$profile = array('id' => 0, 'ID' => 0);
if ($profile_id) {
    $profile_post = get_post($profile_id);
    if ($profile_post && !is_wp_error($profile_post)) {
        $profile = $post_object->convert($profile_post);
    }
}

//get profile skills
$current_skills = get_the_terms($profile, 'skill');

//define variables:
$skills = isset($profile->tax_input['skill']) ? $profile->tax_input['skill'] : array();
$job_title = isset($profile->et_professional_title) ? $profile->et_professional_title : '';
$hour_rate = isset($profile->hour_rate) ? $profile->hour_rate : '';
$currency = isset($profile->currency) ? $profile->currency : '';
$experience = isset($profile->et_experience) ? $profile->et_experience : '';
$hour_rate = isset($profile->hour_rate) ? $profile->hour_rate : '';
$about = isset($profile->post_content) ? $profile->post_content : '';
$display_name = $user_data->display_name;
$user_available = isset($user_data->user_available) && $user_data->user_available == "on" ? 'checked' : '';
$country = isset($profile->country) ? $profile->country : '';
$category = isset($profile->tax_input['project_category'][0]) ? $profile->tax_input['project_category'][0]->slug : '';
$intProfileCompletion = 0;
$totalPercent = 0;
if (ae_user_role() != FREELANCER) {
    if ($display_name) $intProfileCompletion += 20;
    if ($user_data->location) $intProfileCompletion += 20;
    if ($user_data->user_email) $intProfileCompletion += 20;
    if (get_user_meta($user_ID, 'paypal', true)) $intProfileCompletion += 40;
    //if ($user_mobile) $intProfileCompletion += 20;

    $totalPercent = ($intProfileCompletion * 0.01);
} else {
    if ($display_name) $intProfileCompletion += 10;
    if ($user_data->location) $intProfileCompletion += 10;
    if ($user_data->user_email) $intProfileCompletion += 10;
    if (get_user_meta($user_ID, 'paypal', true)) $intProfileCompletion += 20;
   // if ($user_mobile) $intProfileCompletion += 10;
    if ($job_title) $intProfileCompletion += 10;
    if ($hour_rate) $intProfileCompletion += 10;
    if (!empty($current_skills)) $intProfileCompletion += 10;
    //if (!empty($category)) $intProfileCompletion += 10;
    if (!empty($country)) $intProfileCompletion += 10;
    if (!empty($about)) $intProfileCompletion += 10;

    $totalPercent = ($intProfileCompletion * 0.01);
}
$totalPercent = ($totalPercent * 100);

get_header();
?>
    <section class="section-wrapper <?php if (ae_user_role() == FREELANCER) echo 'freelancer'; ?>">
        <div class="number-profile-wrapper">
            <div class="container">
                <div class="row">
                    <?php
                    if (get_user_meta($user_ID, 'interview_status', true) == 'unconfirm') {
                        ?>
                        <div class="col-md-12" style="margin-top: 25px">
                            <div class="alert alert-warning" role="alert">
                                <?php  _e('Your profile is not activated. To activate you profile you need to pass the interview - ','projects-page');?>
                                <a href="<?php echo ae_current_lang(); ?>/interview"><?php  _e('Interview details','projects-page');?></a>
                                <?php
                                if (get_option('interview_system') == 'false') {
                                    ?>
                                    <button id="activate_without_interview"
                                            class="btn btn-info"><?php  _e('Activate without interview','projects-page');?>
                                    </button>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <?php

                    }
                    ?>

                    <div class="col-md-12">
                        <?php
                        // auto_refresh_balance();
                        ?>
                        <?php /* <h2 class="number-profile"><?php printf(__(" %s's Profile ", 'page-profile'), $display_name ) ?></h2> */ ?>
                        <div class="nav-tabs-profile">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs responsive" role="tablist" id="myTab">

                                <li class="active">
                                    <a href="#tab_notification_details" role="tab" data-toggle="tab">
                                        <?php _e('Notification', 'page-profile') ?>
                                    </a>
                                </li>

                                <li>
                                    <a href="#tab_project_details" role="tab" data-toggle="tab">
                                        <?php _e('My Projects', 'page-profile') ?>
                                    </a>
                                </li>

                                <li>
                                    <a href="#tab_account_details" role="tab" data-toggle="tab">
                                        <?php _e('Account Details', 'page-profile') ?>
                                    </a>
                                </li>

                                <?php if (fre_share_role() || $user_role == FREELANCER) { ?>
                                    <li>
                                        <a href="#tab_profile_details" role="tab" data-toggle="tab">
                                            <?php _e('Profile Details', 'page-profile') ?>
                                        </a>
                                    </li>
                                    <!--
                            <li>
                                <a href="#tab_bank_details" role="tab" data-toggle="tab">
                                    <?php _e('Bank Details', 'page-profile') ?>
                                </a>
                            </li> -->
                                <?php } ?>

                                <?php /*if($user_role != FREELANCER){ ?>
                            <li>
                                <a href="#tab_finance_details" role="tab" data-toggle="tab">
                                    <?php _e('Authorize Credit Card', 'page-profile') ?>
                                </a>
                            </li>
                            <?php } */
                                ?>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-profile-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="tab-content-profile">
                            <!-- Tab panes -->
                            <div class="tab-content block-profiles responsive">
                                <!-- Tab Notification -->
                                <div class="tab-pane fade in active" id="tab_notification_details">
                                    <div class="">
                                        <div id="notification_container">
                                            <?php
                                            update_user_meta($user_ID, 'fre_new_notify', 0);
                                            //$notify_object = $ae_post_factory->get('notify');

                                            fre_user_notification($user_ID);
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Tab Notification -->

                                <!-- Tab account details -->
                                <div class="tab-pane fade" id="tab_account_details">
                                    <div class="row">
                                        <div class="avatar-profile-page col-md-3 col-xs-12" id="user_avatar_container">
                                        <span class="img-avatar image" id="user_avatar_thumbnail">
                                            <?php echo get_avatar($user_data->ID, 125) ?>
                                        </span>
                                            <a href="#" id="user_avatar_browse_button">
                                                <?php _e('Change', 'page-profile') ?>
                                            </a>
                                        <span class="et_ajaxnonce hidden"
                                              id="<?php echo de_create_nonce('user_avatar_et_uploader'); ?>">
                                        </span>
                                        </div>
                                        <div class="info-profile-page col-md-9 col-xs-12">
                                            <form class="form-info-basic" id="account_form">
                                                <div class="form-group">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Your Full Name', 'page-profile') ?></label>
                                                        <input type="text" class="form-control" id="display_name"
                                                               name="display_name"
                                                               value="<?php echo $user_data->display_name ?>"
                                                               placeholder="<?php _e('Enter Full Name', 'page-profile') ?>">
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>

                                                <div class="form-group">
                                                    <div class="form-group-control">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Location', 'page-profile') ?></label>
                                                                <select
                                                                    class="chosen multi-tax-item tax-item required cat_profile"
                                                                    id="location"
                                                                    name="location" data-placeholder="Choose country"
                                                                    data-chosen-disable-search=""
                                                                    data-chosen-width="95%">
                                                                    <?php
                                                                    if (!empty($country_list)) {
                                                                        foreach ($country_list as $key => $value) {
                                                                            if ($user_data->location != '') {
                                                                                if ($user_data->location == $value->country_name) {
                                                                                    $selected = 'selected="selected"';
                                                                                } else {
                                                                                    $selected = '';
                                                                                }
                                                                            } else if ($value->country_name == 'SZ') {
                                                                                $selected = 'selected="selected"';
                                                                            } else {
                                                                                $selected = '';
                                                                            }

                                                                            echo '<option value="' . $value->country_name . '" class=" ' . $value->country_name . '  level-0" ' . $selected . '>' . $value->country_name . '</option>';
                                                                        }

                                                                    }
                                                                    ?>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Email Address', 'page-profile') ?></label>
                                                        <input type="email" class="form-control" id="user_email"
                                                               name="user_email"
                                                               value="<?php echo $user_data->user_email ?>"
                                                               placeholder="<?php _e('Enter email', 'page-profile') ?>">
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <?php if (true) { //ae_get_option('use_escrow', false)?>
                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Your Paypal Account', 'page-profile') ?></label>
                                                            <input type="email" class="form-control" id="paypal"
                                                                   name="paypal"
                                                                   value="<?php echo get_user_meta($user_ID, 'paypal', true); ?>"
                                                                   placeholder="<?php _e('Enter your paypal email', 'page-profile') ?>">
                                                        </div>
                                                    </div>
                                                    <div class="clearfix"></div>

                                                    <?php
                                                    if (ae_user_role() != FREELANCER) {
                                                        $arrCCDetails = $wpdb->get_results('SELECT * FROM wp_user_cc_info WHERE user_id = ' . $user_ID);
                                                        $default_payment = get_user_meta($user_ID, 'default_payment_option', true);
                                                        $default_cc = get_user_meta($user_ID, 'active_cc', true);

                                                        $strPayPalSelected = '';
                                                        if ($default_payment == '1') {
                                                            $strPayPalSelected = 'selected="selected"';
                                                        }
                                                        ?>
                                                        <div class="form-group">
                                                            <div class="form-group-control">
                                                                <label><?php _e('Primary Account', 'page-profile') ?></label>
                                                                <select
                                                                    class="chosen multi-tax-item tax-item required cat_profile"
                                                                    id="default_payment_option"
                                                                    name="default_payment_option"
                                                                    data-placeholder="Choose Payment Option"
                                                                    data-chosen-disable-search=""
                                                                    data-chosen-width="95%">
                                                                    <option value="paypal"
                                                                            class="paypal level-0" <?php echo $strPayPalSelected; ?>>
                                                                        PayPal
                                                                    </option>
                                                                    <?php
                                                                    if (!empty($arrCCDetails)) {
                                                                        foreach ($arrCCDetails as $cc) {
                                                                            if ($cc->cc_id == $default_cc) {
                                                                                $strCCSelected = 'selected="selected"';
                                                                            } else {
                                                                                $strCCSelected = '';
                                                                            }
                                                                            echo '<option value="' . $cc->cc_id . '" class="' . $cc->cc_id . ' level-0" ' . $strCCSelected . '>' . $cc->mask_cc . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <?php
                                                    } else {
                                                        $default_payment = get_user_meta($user_ID, 'default_payment_option', true);

                                                        if ($default_payment == '1') {
                                                            $paypalSelected = 'selected="selected"';
                                                        } else {
                                                            $paypalSelected = '';
                                                        }

                                                        if ($default_payment == '2') {
                                                            $bankSelected = 'selected="selected"';
                                                        } else {
                                                            $bankSelected = '';
                                                        }
                                                        ?>

		                                                    <div class="form-group">
			                                                    <div class="form-group-control">
				                                                    <label><?php _e('Stripe Account', 'page-profile') ?></label>
			                                                      <?php $user_stripe_account_id = get_user_meta($current_user->ID, 'stripe_account_id', true); ?>
				                                                    <?php
				                                                    $settings_stripe_secret_key = get_option('settings_stripe_secret_key');
				                                                    $settings_stripe_public_key = get_option('settings_stripe_public_key');
				                                                    $settings_stripe_client_id = get_option('settings_stripe_client_id');
				                                                    ?>
				                                                    <?php if(!empty($settings_stripe_secret_key) && !empty($settings_stripe_public_key) && !empty($settings_stripe_client_id)) { ?>
				                                                      <?php if(!empty($user_stripe_account_id)) { ?>
					                                                      <input type="text" class="form-control" value="<?php echo $user_stripe_account_id; ?>" readonly/>
					                                                      <a href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=<?php echo $settings_stripe_client_id ?>&scope=read_write" class="btn btn-apply-project-item disable-new-window-opening" style="float: left; margin-bottom: 30px;"><?php _e('Reconect stripe account', 'page-profile') ?></a>
				                                                      <?php } else { ?>
					                                                      <br />
					                                                      <a href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=<?php echo $settings_stripe_client_id ?>&scope=read_write" class="btn btn-apply-project-item disable-new-window-opening" style="float: left; margin-bottom: 30px;"><?php _e('Conect stripe account', 'page-profile') ?></a>
					                                                    <?php } ?>
		                                                        <?php } ?>
			                                                    </div>
		                                                    </div>
		                                                    <div class="clearfix"></div>

                                                        <div class="form-group">
                                                            <div class="form-group-control">
                                                                <label><?php _e('Primary Account', 'page-profile') ?></label>
                                                                <select
                                                                    class="chosen multi-tax-item tax-item required cat_profile"
                                                                    id="default_payment_option"
                                                                    name="default_payment_option"
                                                                    data-placeholder="Choose country"
                                                                    data-chosen-disable-search=""
                                                                    data-chosen-width="95%">
                                                                    <option value="paypal"
                                                                            class="paypal level-0" <?php echo $paypalSelected; ?>>
                                                                        PayPal
                                                                    </option>
                                                                    <option value="bank_account"
                                                                            class="bank_account level-0" <?php echo $bankSelected; ?>>
                                                                        Bank Account
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="clearfix"></div>
                                                        <?php
                                                    }
                                                } ?>
                                                <div class="form-group">
                                                    <button type="submit" class="btn-submit btn-sumary"
                                                           ><?php _e('Save Details', 'page-profile') ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!--// END ACCOUNT DETAILS -->
                                <!-- Tab profile details -->
                                <?php if (fre_share_role() || ae_user_role() == FREELANCER) { ?>
                                    <div class="tab-pane fade" id="tab_profile_details">
                                        <div class="detail-profile-page">
                                            <form class="form-detail-profile-page" id="profile_form">

                                                <div class="form-group ">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Professional Title', 'page-profile') ?></label>
                                                        <input class="form-control" type="text"
                                                               id="et_professional_title"
                                                               name="et_professional_title"
                                                               value="<?php echo $job_title ?>"
                                                               placeholder="<?php _e("e.g: Wordpress Developer", 'page-profile') ?>">
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Your Hourly Rate', 'page-profile') ?></label>

                                                        <div class="row">
                                                            <div class="col-xs-8">
                                                                <input class="form-control" type="text" id="hour_rate"
                                                                       name="hour_rate"
                                                                       value="<?php echo $hour_rate ?>"
                                                                       placeholder="<?php _e('e.g:30', 'page-profile') ?>">
                                                            </div>
                                                            <div class="col-xs-4">
                                                        <span class="profile-exp-year">
                                                        <?php $currency = ae_get_option('content_currency');
                                                        if ($currency) {
                                                            echo $currency['code'];
                                                        } else {
                                                            _e('USD', 'page-profile');
                                                        } ?>
                                                        </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group">
                                                    <div class="skill-profile-control form-group-control">
                                                        <label><?php _e('Your Skills', 'page-profile') ?></label>
                                                        <?php
                                                        $switch_skill = ae_get_option('switch_skill');

                                                        if (!$switch_skill) {
                                                            ?>
                                                            <input class="form-control skill" type="text" id="skill"
                                                                   placeholder="<?php _e("Skills (max is 10)", 'page-profile'); ?>"
                                                                   name=""
                                                                   autocomplete="off" class="skill" spellcheck="false">
                                                            <ul class="skills-list" id="skills_list"></ul>
                                                            <?php
                                                        } else {
                                                            $c_skills = array();
                                                            if (!empty($current_skills)) {
                                                                foreach ($current_skills as $key => $value) {
                                                                    $c_skills[] = $value->term_id;
                                                                };
                                                            }
                                                            ae_tax_dropdown('skill', array('attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="' . __(" Skills (max is 10)", 'page-profile') . '"',
                                                                    'class' => 'sw_skill required',
                                                                    'hide_empty' => false,
                                                                    'hierarchical' => true,
                                                                    'id' => 'skill',
                                                                    'show_option_all' => false,
                                                                    'selected' => $c_skills
                                                                )
                                                            );
                                                        }

                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="skill-error error">
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group">
                                                    <div class="profile-category">
                                                        <label><?php _e('Category', 'page-profile') ?></label>
                                                        <?php
                                                        ae_tax_dropdown('project_category',
                                                            array(
                                                                'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" data-placeholder="' . __("Choose categories", 'page-profile') . '"',
                                                                'class' => 'chosen multi-tax-item tax-item required cat_profile',
                                                                'hide_empty' => false,
                                                                'hierarchical' => true,
                                                                'id' => 'project_category',
                                                                'selected' => $category,
                                                                'show_option_all' => false
                                                            )
                                                        );
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Your Country', 'page-profile') ?></label>

                                                        <?php /*<input class="form-control" type="text" id="country" placeholder="<?php _e("Country", 'page-profile'); ?>" name="country" value="<?php if($country){echo $country;} ?>" autocomplete="off" class="country" spellcheck="false" >*/ ?>
                                                        <select
                                                            class="chosen multi-tax-item tax-item required cat_profile"
                                                            id="country"
                                                            name="country" data-placeholder="Choose country"
                                                            data-chosen-disable-search=""
                                                            data-chosen-width="95%">
                                                            <?php
                                                            if (!empty($country_list)) {
                                                                foreach ($country_list as $key => $value) {
                                                                    if ($country != "") {
                                                                        if ($country == $value->country_name) {
                                                                            $selected = 'selected="selected"';
                                                                        } else {
                                                                            $selected = '';
                                                                        }
                                                                    } else if ($value->country_name == 'SZ') {
                                                                        $selected = 'selected="selected"';
                                                                    } else {
                                                                        $selected = '';
                                                                    }

                                                                    echo '<option value="' . $value->country_name . '" class=" ' . $value->country_name . '  level-0" ' . $selected . '>' . $value->country_name . '</option>';
                                                                }

                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group about-you">
                                                    <div class="form-group-control row-about-you">
                                                        <label><?php _e('About you', 'page-profile') ?></label>

                                                        <div class="clearfix"></div>
                            <textarea class="form-control" name="post_content" id="about_content" cols="30"
                                      rows="5"><?php echo trim($about) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="post-content-error error">
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group">
                                                    <div class="experience">
                                                        <label><?php _e('Your Experience', 'page-profile') ?></label>

                                                        <div class="row">
                                                            <div class="col-xs-3">
                                                                <input class="form-control number is_number"
                                                                       type="number" min="0" max="30"
                                                                       name="et_experience"
                                                                       value="<?php echo $experience; ?>"/>
                                                            </div>
                                                            <div class="col-xs-3">
                                                                <span
                                                                    class="profile-exp-year"><?php _e("year(s)", 'page-profile'); ?></span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <!--// project description -->
                                                <?php do_action('ae_edit_post_form', PROFILE, $profile); ?>
                                                <div class="form-group portfolios-wrapper">
                                                    <div class="form-group-control">
                                                        <label><?php _e('Your Portfolio', 'page-profile') ?></label>

                                                        <div class="edit-portfolio-container">
                                                            <?php
                                                            // list portfolio
                                                            query_posts(array(
                                                                'post_status' => 'publish',
                                                                'post_type' => 'portfolio',
                                                                'author' => $current_user->ID,
                                                                'posts_per_page' => -1
                                                            ));
                                                            get_template_part('list', 'portfolios');
                                                            wp_reset_query();
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="form-group">
                                                    <button type="submit" class="btn-submit btn-sumary"><?php _e('Save Details', 'page-profile') ?></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!--// END PROFILE DETAILS -->
                                <!-- tab project details -->
                                <div class="tab-pane fade" id="tab_project_details">
                                    <?php
                                    // list all freelancer current bid
                                    if (fre_share_role() || $user_role == FREELANCER) {
                                        ?>
                                        <div class="info-project-items">
                                            <h4 class="title-big-info-project-items">
                                                <?php _e("Your Bidding", 'page-profile') ?>
                                            </h4>
                                            <?php
                                            query_posts(array(
                                                'post_status' => array('publish', 'accept'),
                                                'post_type' => BID,
                                                'author' => $current_user->ID,
                                            ));

                                            get_template_part('list', 'user-bids');

                                            wp_reset_query();
                                            ?>
                                        </div>
                                        <?php
                                    }

                                    if (fre_share_role() || $user_role != FREELANCER) {
                                        // employer works history & reviews
                                        get_template_part('template/work', 'history');
                                    }

                                    if (fre_share_role() || $user_role == FREELANCER) {
                                        // freelancer bids history and reviews
                                        get_template_part('template/bid', 'history');
                                    }
                                    ?>
                                </div>
                                <!--// END PROJECT DETAILS -->

                                <!-- Tab profile details -->
                                <?php if (ae_user_role() != FREELANCER) { ?>
                                    <div class="tab-pane fade" id="tab_finance_details">
                                        <div class="row">
                                            <div class="col-md-3 col-xs-12">

                                            </div>
                                            <div class="detail-profile-page col-md-10 col-md-offset-1 saveCardw">
                                                <div class="saveCard">
                                                    <h5 style=" padding-bottom: 5px;border-bottom:1px solid #f4f4f4;">
                                                        Saved Cards</h5>
                                                    <?php
                                                    $arrCCDetails = $wpdb->get_results('SELECT * FROM wp_user_cc_info WHERE user_id = ' . $user_ID);
                                                    $default_payment = get_user_meta($user_ID, 'default_payment_option', true);
                                                    $default_cc = get_user_meta($user_ID, 'active_cc', true);

                                                    if (!empty($arrCCDetails)) {
                                                        ?>
                                                        <ol>
                                                            <?php
                                                            foreach ($arrCCDetails as $cc) {
                                                                ?>
                                                                <li>
                                                                    <?php echo $cc->mask_cc; ?>
                                                                    <a class="remove-cc" href="javascript:void(0)"
                                                                       onclick="remove_card('<?php echo $cc->cc_id; ?>');">Remove
                                                                        Card</a>
                                                                </li>
                                                                <?php
                                                            }
                                                            ?>
                                                        </ol>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>

                                                <form class="form-detail-profile-page" id="finance_form">

                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Card Type', 'page-profile') ?></label>
                                                            <select style="width:250px;"
                                                                    class="chosen multi-tax-item tax-item required cat_profile"
                                                                    id="card_type" name="card_type"
                                                                    data-placeholder="Choose Card Type"
                                                                    data-chosen-disable-search=""
                                                                    data-chosen-width="95%">
                                                                <option value="VISA" class=" VISA  level-0">Visa
                                                                </option>
                                                                <option value="MasterCard" class=" MasterCard  level-0">
                                                                    Master
                                                                </option>
                                                                <option value="Discover" class=" Discover  level-0">
                                                                    Discover
                                                                </option>
                                                                <option value="Amex" class=" Amex  level-0">Amex
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Card Number', 'page-profile') ?></label>
                                                            <input class="form-control" type="text" name="card_number"
                                                                   id="card_number" value=""
                                                                   placeholder="<?php _e("Card Number", 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('First Name', 'page-profile') ?></label>
                                                            <input class="form-control" type="text" name="first_name"
                                                                   id="first_name" value=""
                                                                   placeholder="<?php _e("First Name", 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Last Name', 'page-profile') ?></label>
                                                            <input class="form-control" type="text" name="last_name"
                                                                   id="last_name" value=""
                                                                   placeholder="<?php _e("Last Name", 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Expiration Date', 'page-profile') ?></label>

                                                            <p>
                                                                <input class="form-control" type="text" name="exp_month"
                                                                       id="exp_month" value=""
                                                                       placeholder="<?php _e("MM", 'page-profile') ?>"
                                                                       style="width:48%; float:left">
                                                                <input class="form-control" type="text" name="exp_year"
                                                                       id="exp_year" value=""
                                                                       placeholder="<?php _e("YY", 'page-profile') ?>"
                                                                       style="width:48%; float:right">
                                                            </p>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('CVV', 'page-profile') ?></label>
                                                            <input class="form-control" type="text" name="card_cvv"
                                                                   id="card_cvv" value=""
                                                                   placeholder="<?php _e("CVV", 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Street', 'page-profile') ?></label>
                                                            <input class="form-control" type="text" name="street"
                                                                   id="street" value=""
                                                                   placeholder="<?php _e("Street", 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('City', 'page-profile') ?></label>
                                                            <input class="form-control" type="text" name="city"
                                                                   id="city" value=""
                                                                   placeholder="<?php _e("City", 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('State', 'page-profile') ?></label>
                                                            <input class="form-control" type="text" name="state"
                                                                   id="state" value=""
                                                                   placeholder="<?php _e("State", 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Country', 'page-profile') ?></label>
                                                            <?php /*<input class="form-control"  type="text" name="country" id="country" value="" placeholder="<?php _e("Country", 'page-profile') ?>"> */ ?>
                                                            <select style="width:250px;"
                                                                    class="chosen multi-tax-item tax-item required cat_profile"
                                                                    id="user_country" name="user_country"
                                                                    data-placeholder="Choose Country"
                                                                    data-chosen-disable-search=""
                                                                    data-chosen-width="95%">

                                                                <?php
                                                                if (!empty($country_list)) {
                                                                    foreach ($country_list as $key => $value) {
                                                                        echo '<option value="' . $value->country_name . '" class=" ' . $value->country_name . '  level-0">' . $value->country_name . '</option>';
                                                                    }

                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Zip Code', 'page-profile') ?></label>
                                                            <input class="form-control" type="text" name="zip_code"
                                                                   id="zip_code" value=""
                                                                   placeholder="<?php _e("Zip Code", 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Make it default payment option', 'page-profile') ?></label>
                                                            <input class="form-control" type="checkbox"
                                                                   name="default_payment" id="default_payment"
                                                                   value="">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <input type="submit" class="btn-submit btn-sumary" name=""
                                                               value="<?php _e('Authorize Card', 'page-profile') ?>">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <!-- Tab bank details -->
                                <?php if (ae_user_role() == FREELANCER) { ?>
                                    <div class="tab-pane fade" id="tab_bank_details">
                                        <div class="row">
                                            <div class="avatar-profile-page col-md-3 col-xs-12"
                                                 id="user_avatar_container">

                                            </div>
                                            <div class="col-md-10 col-md-offset-1 saveCardw">
                                                <form class="form-info-basic" id="bank_form">
                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Bank Information', 'page-profile') ?></label>
                                                            <select
                                                                class="chosen multi-tax-item tax-item required cat_profile"
                                                                id="account_type"
                                                                name="account_type"
                                                                data-placeholder="Choose Account Type"
                                                                data-chosen-disable-search="" data-chosen-width="95%">
                                                                <option value="current_account"
                                                                        class=" current_account  level-0">
                                                                    Checking/Current
                                                                </option>
                                                                <option value="saving" class=" saving  level-0">Saving
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Bank Name', 'page-profile') ?></label>
                                                            <input type="text" class="form-control required"
                                                                   id="bank_name" name="bank_name"
                                                                   value="<?php echo get_user_meta($user_ID, 'bank_name', true); ?>"
                                                                   placeholder="<?php _e('Enter your bank name', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Bank Country', 'page-profile') ?></label>
                                                            <select
                                                                class="chosen multi-tax-item tax-item required cat_profile"
                                                                id="bank_country"
                                                                name="bank_country" data-placeholder="Choose country"
                                                                data-chosen-disable-search="" data-chosen-width="95%">
                                                                <?php
                                                                if (!empty($country_list)) {
                                                                    foreach ($country_list as $key => $value) {
                                                                        if ($user_data->country != '') {
                                                                            if ($user_data->country == $value->country_name) {
                                                                                $selected = 'selected="selected"';
                                                                            } else {
                                                                                $selected = '';
                                                                            }
                                                                        } else if ($value->country_name == 'SZ') {
                                                                            $selected = 'selected="selected"';
                                                                        } else {
                                                                            $selected = '';
                                                                        }
                                                                        echo '<option value="' . $value->country_name . '" class=" ' . $value->country_name . '  level-0" ' . $selected . '>' . $value->country_name . '</option>';
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('ABA Routing No', 'page-profile') ?></label>
                                                            <input type="text" class="form-control" id="routing_no"
                                                                   name="routing_no"
                                                                   value="<?php echo get_user_meta($user_ID, 'routing_no', true); ?>"
                                                                   placeholder="<?php _e('Enter ABA routing number', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Bank Address', 'page-profile') ?></label>
                                                            <input type="text" class="form-control" id="bank_address"
                                                                   name="bank_address"
                                                                   value="<?php echo get_user_meta($user_ID, 'bank_address', true); ?>"
                                                                   placeholder="<?php _e('Enter your bank address', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Bank City', 'page-profile') ?></label>
                                                            <input type="text" class="form-control" id="bank_city"
                                                                   name="bank_city"
                                                                   value="<?php echo get_user_meta($user_ID, 'bank_city', true); ?>"
                                                                   placeholder="<?php _e('Enter your bank city', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Bank State', 'page-profile') ?></label>
                                                            <input type="text" class="form-control" id="bank_state"
                                                                   name="bank_state"
                                                                   value="<?php echo get_user_meta($user_ID, 'bank_state', true); ?>"
                                                                   placeholder="<?php _e('Enter your bank state', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Bank Zip Code', 'page-profile') ?></label>
                                                            <input type="text" class="form-control" id="bank_zipcode"
                                                                   name="bank_zipcode"
                                                                   value="<?php echo get_user_meta($user_ID, 'bank_zipcode', true); ?>"
                                                                   placeholder="<?php _e('Enter your bank zip code', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Account Holder Currency', 'page-profile') ?></label>
                                                            <input type="text" class="form-control"
                                                                   id="account_holder_currency"
                                                                   name="account_holder_currency"
                                                                   value="<?php echo get_user_meta($user_ID, 'account_holder_currency', true); ?>"
                                                                   placeholder="<?php _e('Enter your currency code', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Account Holder Name', 'page-profile') ?></label>
                                                            <input type="text" class="form-control"
                                                                   id="account_holder_name"
                                                                   name="account_holder_name"
                                                                   value="<?php echo get_user_meta($user_ID, 'account_holder_name', true); ?>"
                                                                   placeholder="<?php _e('Enter your name', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Account No', 'page-profile') ?></label>
                                                            <input type="text" class="form-control" id="account_number"
                                                                   name="account_number"
                                                                   value="<?php echo get_user_meta($user_ID, 'account_number', true); ?>"
                                                                   placeholder="<?php _e('Enter your acc no', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Account Holder Address', 'page-profile') ?></label>
                                                            <input type="text" class="form-control"
                                                                   id="account_holder_address"
                                                                   name="account_holder_address"
                                                                   value="<?php echo get_user_meta($user_ID, 'account_holder_address', true); ?>"
                                                                   placeholder="<?php _e('Enter your address', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Account Holder City', 'page-profile') ?></label>
                                                            <input type="text" class="form-control"
                                                                   id="account_holder_city"
                                                                   name="account_holder_city"
                                                                   value="<?php echo get_user_meta($user_ID, 'account_holder_city', true); ?>"
                                                                   placeholder="<?php _e('Enter your city', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Account Holder State', 'page-profile') ?></label>
                                                            <input type="text" class="form-control"
                                                                   id="account_holder_state"
                                                                   name="account_holder_state"
                                                                   value="<?php echo get_user_meta($user_ID, 'account_holder_state', true); ?>"
                                                                   placeholder="<?php _e('Enter your state', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Account Holder Country', 'page-profile') ?></label>
                                                            <select
                                                                class="chosen multi-tax-item tax-item required cat_profile"
                                                                id="account_holder_country"
                                                                name="account_holder_country"
                                                                data-placeholder="Choose country"
                                                                data-chosen-disable-search=""
                                                                data-chosen-width="95%">
                                                                <?php
                                                                if (!empty($country_list)) {
                                                                    foreach ($country_list as $key => $value) {
                                                                        if ($user_data->account_holder_country == $value->country_name) {
                                                                            $selected = 'selected="selected"';
                                                                        } else {
                                                                            $selected = '';
                                                                        }
                                                                        echo '<option value="' . $value->country_name . '" class=" ' . $value->country_name . '  level-0" ' . $selected . '>' . $value->country_name . '</option>';
                                                                    }

                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <div class="form-group-control">
                                                            <label><?php _e('Account Holder Zip Code', 'page-profile') ?></label>
                                                            <input type="text" class="form-control"
                                                                   id="account_holder_zipcode"
                                                                   name="account_holder_zipcode"
                                                                   value="<?php echo get_user_meta($user_ID, 'account_holder_zipcode', true); ?>"
                                                                   placeholder="<?php _e('Enter your zip code', 'page-profile') ?>">
                                                        </div>
                                                    </div>


                                                    <div class="form-group">
                                                        <input type="submit" class="btn-submit btn-sumary" name=""
                                                               value="<?php _e('Save Bank Details', 'page-profile') ?>">
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                    <!-- profile left bar -->
                    <div class="col-md-4">
                        <?php
                        //echo fre_price_format(0.4166666);
                        //var_dump(strtotime('next monday +3 hours'));
                        //echo '<pre>'; var_dump( _get_cron_array() ); echo '</pre>';

//                        $account_balance = account_balance();
//                        if ($user_role == FREELANCER) {
//                            ?>
<!--                            <div class="account-balance">-->
<!--                                <label>--><?php //_e("Total earned:",'page-profile');?><!--</label>-->
<!--                                <span class="--><?php //echo $account_balance['style'] ?><!--">-->
<!--                                --><?php //echo $account_balance['string_balance'] ?>
<!--                                 </span>-->
<!--                            </div>-->
<!--                            --><?php
//                        } elseif ($user_role == EMPLOYER) {
                            ?>
                            <!--<div class="account-balance">
                                <label><?php //_e("Your account balance:",'page-profile');?></label>
                                <span class="<?php //echo $account_balance['style'] ?>">
                                <?php //echo $account_balance['string_balance'] ?>
                                </span>
                            </div>
                            <button type="button" style="margin-top: 9px;"
                                    class="btn btn-primary btn-block btn-custom-price" data-toggle="modal"
                                    data-target="#stripe_modal"
                                    data-title="<?php //_e("Custom amount",'account_balance_plugin');?>"><?php //_e("Account recharge",'page-profile');?>
                            </button>-->
                            <?php
                            //echo do_shortcode('[modal_paymill]');
//                        }
                        ?>
                        <div class="setting-profile-wrapper <?php echo $user_role; ?>">
<!--                            --><?php //if ($totalPercent != 100) { ?>
                                <div class="form-group">
                                    <div class="text-small">
                                        <label><?php _e('Profile Completion', 'page-profile') ?></label>

                                        <div class="profile-completion-status"><span
                                                style="width:<?php echo $totalPercent; ?>%"><?php echo $totalPercent; ?>
                                                %</span></div>
                                        <div id="description-profile-completion-status">

                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
<!--                            --><?php //} ?>

                            <?php if ($user_role == FREELANCER) { ?>
                                <div class="form-group <?php if ($totalPercent != 100) {
                                    echo 'confirm-request';
                                } ?>">
                            <span class="text-intro">
                                <?php _e("Available for hire?", 'page-profile') ?></span>
                            <span class="switch-for-hide tooltip-style" data-toggle="tooltip" data-placement="top"
                                  title='<?php _e('Turn on to display an "Invite me" button on your profile, allowing potential employers to suggest projects for you.', 'page-profile'); ?>'
                            >
                                <input type="checkbox" <?php echo $user_available; ?> class="js-switch user-available"
                                       name="user_available"/>
                                <span class="user-status-text text <?php echo $user_available ? 'yes' : 'no' ?>">
                                    <?php echo $user_available ? __('Yes', 'page-profile') : __('No', 'page-profile'); ?>
                                </span>
                            </span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="form-group">
                            <span class="text-small">
                                <?php _e('Select "Yes" to display a "Hire me" button on your profile allowing potential clients and employers to contact you.', 'page-profile') ?>
                            </span>
                                </div>
                                <div class="clearfix"></div>

                                <?php
                            }
                            // display a link for user to request a confirm email
                            if (!AE_Users::is_activate($user_ID)) {
                                ?>

                                <div class="form-group confirm-request">
                            <span class="text-small">
                                <?php
                                _e('You have not confirmed your email yet, please check out your mailbox.', 'page-profile');
                                echo '<br/>';
                                echo ' <a class="request-confirm" href="#">' . __('Request confirm email.', 'page-profile') . '</a>';
                                ?>
                            </span>
                                </div>
                            <?php } else { ?>

                                <div class="form-group confirm-request">
                            <span class="text-small verified">
                              <span class="text-small"><label for="user_mobile">
                                      <?php
                                      _e('Email Verified', 'page-profile');
                                      echo '</label></span>';
                                      echo '<br/>';
                                      echo '  <i class="fa fa-check"></i>  <a class="request-confirm" href="#">' . $user_data->user_email . '</a>';
                                      ?>
                            </span>
                                </div>

                            <?php } ?>

                            <form name="verify_user_phone" id="verify_user_phone" class="hidden">
                                <div class="form-group confirm-request"
                                     id="verify" <?php if ($user_mobile) { ?> style="display:none;" <?php } ?>>
                    <span class="text-small"><label
                            for="user_mobile"><?php _e('Verify your phone no', 'page-profile') ?></label></span><br/>
                                    <input type="text" name="user_mobile" id="user_mobile" value=""/>
                                    <button type="button" class="btn-submit"
                                            onclick="validatenumber();"><?php _e('Verify Phone', 'page-profile') ?></button>
                                </div>
                                <div class="form-group confirm-request verified"
                                     id="verified" <?php if (!$user_mobile) { ?> style="display:none;" <?php } ?>>

                    <span class="text-small"><label
                            for="user_mobile"><?php _e('Your phone no', 'page-profile') ?></label></span> <br/>
                                    <span id="user_verified_mobile"><i
                                            class="fa fa-check"></i> <?php echo $user_mobile; ?></span>
                                    <button type="button" class="btn-submit"
                                            onclick="editnumber();"><?php _e('Edit Phone', 'page-profile') ?></button>
                                </div>
                            </form>

                            <ul class="list-setting">
                                <?php if (fre_share_role() || $user_role != FREELANCER) { ?>
                                    <li>
                                        <a role="menuitem" tabindex="-1"
                                           href="<?php echo et_get_page_link("submit-project") ?>"
                                           class="display-name">
                                            <i class="fa fa-plus-circle"></i><?php _e("Post a Project", 'page-profile') ?>
                                        </a>
                                    </li>
                                <?php } ?>
                                <li>
                                    <a href="#" class="change-password">
                                        <i class="fa fa-key"></i>
                                        <?php _e("Change Password", 'page-profile') ?>
                                    </a>
                                </li>
                                <!-- <li>
                                <a href="#" class="creat-team-link"><i class="fa fa-users"></i><?php _e("Create Your Team", 'page-profile') ?></a>
                            </li> -->
                                <li>
                                    <a href="<?php echo wp_logout_url(home_url()); ?>" class="logout-link">
                                        <i class="fa fa-sign-out"></i>
                                        <?php _e("Log Out", 'page-profile') ?>
                                    </a>
                                </li>
                                <!-- HTML to write -->
                            </ul>
                            <?php /*
                        <div class="widget user_payment_status">
                        <?php ae_user_package_info($user_ID); ?>
                        </div>
                        
                         */
                            ?>
                        </div>
                    </div>
                    <!--// profile left bar -->
                </div>
            </div>
        </div>

    </section>

    <!-- CURRENT PROFILE -->
<?php if ($profile_id && $profile_post && !is_wp_error($profile_post)) { ?>
    <script type="data/json" id="current_profile">
        <?php echo json_encode($profile) ?>

    </script>
<?php } ?>
    <!-- END / CURRENT PROFILE -->

    <!-- CURRENT SKILLS -->
<?php if (!empty($current_skills)) { ?>
    <script type="data/json" id="current_skills">
        <?php echo json_encode($current_skills) ?>

    </script>
<?php } ?>
    <!-- END / CURRENT SKILLS -->

<?php
get_footer();
?>
