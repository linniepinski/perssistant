<?php 
    global $wp_query, $ae_post_factory, $post, $current_user;
    //convert current user
    $ae_users  = AE_Users::get_instance();
    $user_data = $ae_users->convert($current_user->data);
$user_data = $ae_users->convert($current_user->data);
$user_role = ae_user_role($current_user->ID);
//convert current profile
$post_object = $ae_post_factory->get(PROFILE);

$profile_id = get_user_meta($current_user->ID, 'user_profile_id', true);
$user_mobile = get_user_meta($current_user->ID, 'phone', true);

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
$experience = isset($profile->experience) ? explode(' ',$profile->experience)[0] : '';
$hour_rate = isset($profile->hour_rate) ? $profile->hour_rate : '';
$about = isset($profile->post_content) ? $profile->post_content : '';
$display_name = $user_data->display_name;
$user_available = isset($user_data->user_available) && $user_data->user_available == "on" ? 'checked' : '';
$country = isset($profile->tax_input['country'][0]) ? $profile->tax_input['country'][0]->name : '';
$category = isset($profile->tax_input['project_category'][0]) ? $profile->tax_input['project_category'][0]->slug : '';

et_get_mobile_header();
?>
<section class="section-wrapper section-user-profile">

	<div class="tabs-acc-details tab-profile" id="tab_account">
        <div class="user-profile-avatar" id="user_avatar_container">
            <span class="image" id="user_avatar_thumbnail">
                <?php echo get_avatar( $user_data->ID, 90 ); ?>
            </span>
            <a href="#" class="icon-edit-profile-user edit-avatar-user" id="user_avatar_browse_button">
                <i class="fa fa-pencil"></i>
            </a>
            <span class="et_ajaxnonce hidden" id="<?php echo de_create_nonce( 'user_avatar_et_uploader' ); ?>"></span>
        </div>
        <form class="form-mobile-wrapper form-user-profile" id="account_form">
            <div class="form-group-mobile">
                <label><?php _e("Your Fullname", 'page-profile-mobile') ?></label>
                <!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
                <input type="text" id="display_name" name="display_name" value="<?php echo $user_data->display_name ?>" placeholder="<?php _e("Full name", 'page-profile-mobile'); ?>">
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Location", 'page-profile-mobile') ?></label>
                <input type="text" id="location" name="location" value="<?php echo $user_data->location ?>" placeholder="<?php _e("Location", 'page-profile-mobile'); ?>">
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Email Address", 'page-profile-mobile') ?></label>
                <input type="text" id="user_email" value="<?php echo $user_data->user_email ?>" name="user_email" placeholder="<?php _e("Email", 'page-profile-mobile'); ?>">
            </div>
            <div class="form-group-mobile">
                <?php if (true) { //ae_get_option('use_escrow', false)?>
                    <div class="form-group">
                        <div class="form-group-control">
                            <label><?php _e('Your Paypal Account', 'page-profile-mobile') ?></label>
                            <input type="email" class="form-control" id="paypal"
                                   name="paypal"
                                   value="<?php echo get_user_meta($user_ID, 'paypal', true); ?>"
                                   placeholder="<?php _e('Enter your paypal email', 'page-profile-mobile') ?>">
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
                                <label><?php _e('Primary Account', 'page-profile-mobile') ?></label>
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
						                    <a href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=<?php echo $settings_stripe_client_id ?>&scope=read_write" class="btn btn-apply-project-item disable-new-window-opening" style="float: left; margin-bottom: 30px;"><?php _e('Reconnect stripe account', 'page-profile') ?></a>
					                    <?php } else { ?>
						                    <br />
						                    <a href="https://connect.stripe.com/oauth/authorize?response_type=code&client_id=<?php echo $settings_stripe_client_id ?>&scope=read_write" class="btn btn-apply-project-item disable-new-window-opening" style="float: left; margin-bottom: 30px;"><?php _e('Connect stripe account', 'page-profile') ?></a>
					                    <?php } ?>
				                    <?php } ?>
			                    </div>
		                    </div>
		                    <div class="clearfix"></div>

                        <div class="form-group">
                            <div class="form-group-control">
                                <label><?php _e('Primary Account', 'page-profile-mobile') ?></label>
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
            </div>
            <div class="form-group">
                <button type="submit" class="btn-submit btn-sumary"><?php _e('Save Details', 'page-profile-mobile') ?></button>
            </div>
        </form>
    </div>

    <div class="tabs-profile-details tab-profile collapse" id="tab_profile">
    	<form class="form-mobile-wrapper form-user-profile" id="profile_form">
            <div class="form-group-mobile edit-profile-title">
                <label><?php _e("Your Professional Title", 'page-profile-mobile') ?></label>
                <!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
                <input type="text" id="et_professional_title" value="<?php echo $job_title; ?>" name="et_professional_title" placeholder="<?php _e("Title", 'page-profile-mobile'); ?>">
            </div>            
            <div class="form-group-mobile">
                <div class="form-group">
                    <div class="form-group-control">
                        <label><?php _e('Your Hourly Rate', 'page-profile-mobile') ?></label>

                        <div class="row">
                            <div class="col-xs-8">
                                <input class="form-control" type="text" id="hour_rate"
                                       name="hour_rate"
                                       value="<?php echo $hour_rate ?>"
                                       placeholder="<?php _e('e.g:30', 'page-profile-mobile') ?>">
                            </div>
                            <div class="col-xs-4">
                                                        <span class="profile-exp-year">
                                                        <?php $currency = ae_get_option('content_currency');
                                                        if ($currency) {
                                                            echo $currency['code'];
                                                        } else {
                                                            _e('USD', 'page-profile-mobile');
                                                        } ?>
                                                        </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="form-group-mobile skill-profile-control">
            	
                <?php 
                $switch_skill = ae_get_option('switch_skill');
                if(!$switch_skill){
                    ?>
                    <div class="wrapper-skill">
                        <label><?php _e("Your Skills", 'page-profile-mobile') ?></label>
                        <a href="#" class="btn-sumary btn-add-skill add-skill"><?php _e("Add", 'page-profile-mobile') ?></a>
                        <input type="text" id="skill" class="skill" placeholder="<?php _e("Skills", 'page-profile-mobile'); ?>">
                    </div>
                    <div class="clearfix"></div>
                    <ul class="list-skill skills-list" id="skills_list"></ul>
                    <?php
                }else{
                    ?>
                    <div class="wrapper-skill">
                        <label><?php _e("Your Skills", 'page-profile-mobile') ?></label>
                    </div>
                    <?php
                    $c_skills = array();
                    if(!empty($current_skills)){
                        foreach ($current_skills as $key => $value) {
                            $c_skills[] = $value->term_id;
                        };
                    }
                    ae_tax_dropdown( 'skill' , array(  'attr' => 'data-chosen-width="95%" data-chosen-disable-search="" multiple data-placeholder="'.__(" Skills (max is 5)", 'page-profile-mobile').'"', 
                                        'class' => 'form-control required', 
                                        'hide_empty' => false, 
                                        'hierarchical' => true , 
                                        'id' => 'skill' , 
                                        'show_option_all' => false,
                                        'selected' =>$c_skills
                                        ) 
                    );
                }
                ?>
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Category", 'page-profile-mobile') ?></label>
                <?php 
                    ae_tax_dropdown( 'project_category' , 
                          array(  
                                'attr'            => 'data-chosen-width="95%" data-chosen-disable-search="" data-placeholder="'.__("Choose categories", 'page-profile-mobile').'"', 
                                'class'           => 'experience-form chosen multi-tax-item tax-item required', 
                                'hide_empty'      => false, 
                                'hierarchical'    => true , 
                                'id'              => 'project_category' , 
                                'selected'        => $category,
                                'show_option_all' => false
                              ) 
                    );
                ?>
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Country", 'page-profile-mobile') ?></label>
                <!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
                <input class="form-control" type="text" id="country" placeholder="<?php _e("Country", 'page-profile-mobile'); ?>" name="country" value="<?php if($country){echo $country;} ?>" autocomplete="off" class="country" spellcheck="false" >
            </div>                        
            <div class="form-group-mobile about-form">
                <label><?php _e("About You", 'page-profile-mobile') ?></label>
                <!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
                <textarea name="post_content" id="post_content" placeholder="<?php _e("About", 'page-profile-mobile'); ?>" rows="7"><?php echo trim(strip_tags($about)) ?></textarea>
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Your Experience", 'page-profile-mobile') ?></label>
                <!-- <a href="#" class="icon-edit-profile-user edit-info-user"><i class="fa fa-pencil"></i></a> -->
                 <div class="row">
                    <div class="col-xs-12">
                        <input class="form-control number is_number"
                               type="number" min="0" max="30"
                               name="et_experience" placeholder="<?php _e("year(s)", 'page-profile-mobile'); ?>"
                               value="<?php echo $experience; ?>"/>
                    </div>
                </div>
                <!--<input type="text" name="experience" value="<?php echo $experience; ?>" />-->
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Your Porfolio", 'page-profile-mobile') ?></label>
                <div class="edit-portfolio-container">
                    <?php
                    // list portfolio
                    query_posts( array(
                        'post_status' => 'publish',
                        'post_type'   => 'portfolio',
                        'author'      => $current_user->ID
                    ));
                    get_template_part( 'mobile/list', 'portfolios' );
                    wp_reset_query();
                    ?>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-submit btn-sumary"><?php _e('Save Details', 'page-profile-mobile') ?></button>
            </div>
        </form>            

    </div>

    <div class="tabs-project-details tab-profile collapse" id="tab_project">
    	<form class="form-mobile-wrapper form-user-profile">
            <div class="form-group-mobile edit-profile-title user-profile-history">
                <?php if($user_role == FREELANCER){ ?>
                <!-- BIDDING -->
                <label>
                    <?php _e("Your Bidding", 'page-profile-mobile') ?>
                </label>
                <?php 
                        query_posts( array(
                            'post_status' => 'publish', 
                            'post_type'   => 'bid', 
                            'author'      => $current_user->ID, 
                        ));
                        if(have_posts()){
                            get_template_part( 'mobile/list', 'user-bids' );
                        } else {
                            echo '<span class="no-results">';
                            _e( "You are not bidding any project.", 'page-profile-mobile' );
                            echo '</span>';
                        }
                        wp_reset_query();
                ?>
                <label>
                    <?php _e('Your Worked History and Reviews', 'page-profile-mobile') ?>
                </label>
                <?php
                    query_posts( array(  'post_status' => array('publish', 'complete'), 
                                'post_type' => BID, 
                                'author' => $current_user->ID, 
                                'accepted' => 1  
                            )
                        );
                    get_template_part('mobile/template/bid', 'history-list');
                    wp_reset_query();

                } else {
                    get_template_part('mobile/template/work', 'history');
                }
                ?>                
                <!-- / END BIDDING -->
            </div>      
        </form>
    </div>
    <!-- Notification -->
    <section class="notification-section tab-profile" id="tab_notification">
        <div class="container">
            <div class="notification-wrapper" id="notification_container">
                <?php fre_user_notification($user_ID); ?>
                
            </div>
        </div>
    </section>
    <!-- Notification / END -->

    <div class="tabs-acc-details tab-profile collapse" id="tab_change_pw">
        <form class="form-mobile-wrapper form-user-profile chane_pass_form" id="chane_pass_form">
            <div class="form-group-mobile edit-profile-title">
                <label><?php _e("Your Old Password", 'page-profile-mobile') ?></label>
                <input type="password" id="old_password" name="old_password" placeholder="<?php _e("Old password", 'page-profile-mobile'); ?>">
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Your New Password", 'page-profile-mobile') ?></label>
                <input type="password" id="new_password" name="new_password" placeholder="<?php _e("New password", 'page-profile-mobile'); ?>">
            </div>
            <div class="form-group-mobile">
                <label><?php _e("Retype New Password", 'page-profile-mobile') ?></label>
                <input type="password" id="renew_password" name="renew_password" placeholder="<?php _e("Retype again", 'page-profile-mobile'); ?>">
            </div>
            <p class="btn-warpper-bid">
                <input type="submit" class="btn-submit btn-sumary btn-bid" value="<?php _e("Change", 'page-profile-mobile') ?>" />
            </p>
        </form>
    </div>    
</section>

<!-- CURRENT PROFILE -->
<?php if(!empty($posts) && isset($posts[0])){ ?>
<script type="data/json" id="current_profile">
    <?php echo json_encode($profile) ?>
</script>
<?php } ?>
<!-- END / CURRENT PROFILE -->

<!-- CURRENT SKILLS -->
<?php if( !empty($current_skills) ){ ?>
<script type="data/json" id="current_skills">
    <?php echo json_encode($current_skills) ?>
</script>
<?php } ?>
<!-- END / CURRENT SKILLS -->

<?php
	et_get_mobile_footer();
?>
