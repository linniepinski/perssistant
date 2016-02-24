<?php
/**
 * The Template for displaying a user profile
 *
 * @package WordPress
 * @subpackage FreelanceEngine
 * @since FreelanceEngine 1.0
 */

global $wp_query, $ae_post_factory, $post, $user_ID;    
$post_object = $ae_post_factory->get(PROFILE);
$author_id 	= 	get_query_var( 'author' );
$author_name = get_the_author_meta('display_name', $author_id);
$author_available = get_user_meta($author_id, 'user_available', true);
$author_email_verified = (ae_get_option('user_confirm') && get_user_meta($author_id, 'register_status', true) == "unconfirm") ? false : true;
$author_phone_verified = (get_user_meta( $author_id, 'phone', true) != "") ? true : false;

// get user profile id
$profile_id = get_user_meta($author_id, 'user_profile_id', true);
// get post profile
$profile = get_post($profile_id);
$convert = '';

if( $profile && !is_wp_error($profile) ){    
    $convert = $post_object->convert( $profile );
}

// try to check and add profile up current user dont have profile
if(!$convert && ( fre_share_role() || ae_user_role($author_id) == FREELANCER) ) {
    $profile_post = get_posts(array('post_type' => PROFILE,'author' => $author_id));
    if(!empty($profile_post)) {
        $profile_post = $profile_post[0];
        $convert = $post_object->convert( $profile_post );
        $profile_id = $convert->ID;
        update_user_meta($author_id, 'user_profile_id', $profile_id);
    }else {
        $convert = $post_object->insert( array( 'post_status' => 'publish' , 
                                                'post_author' => $author_id , 
                                                'post_title' => $author_name , 
                                                'post_content' => '')
                                        );
        
        $convert = $post_object->convert( get_post($convert->ID) );
        $profile_id = $convert->ID;
    }
}

//  count author review number
$count_review = fre_count_reviews($author_id);
// $count_project = fre_count_user_posts_by_type($user_ID, PROJECT, 'publish');

get_header();
$next_post = false;
if($convert) {
    $next_post = ae_get_adjacent_post($convert->ID, false, '', true, 'skill');    
}

?>
	<section class="breadcrumb-wrapper">
		<div class="breadcrumb-single-site">
        	<div class="container">
    			<div class="row">
                	<div class="col-md-6 col-xs-8">
                    	<ol class="breadcrumb">
                            <li><a href="<?php echo home_url(); ?>"><?php _e("Home", 'author'); ?></a></li>
                            <li class="active"><?php printf(__("Profile of %s", 'author'), $author_name); ?></li>
                        </ol>
                    </div>

                    <?php /* if($next_post) { ?>
                        <div class="col-md-6 col-xs-4">
                        	<a title="<?php the_author_meta('display_name', $next_post->post_author) ?>" href="<?php echo get_author_posts_url($next_post->post_author);  ?>" class="prj-next-link"><?php _e('Next Profile', 'author');?> <i class="fa fa-angle-double-right"></i></a>
                        </div>
                    <?php }*/ ?>
                </div>
            </div>
        </div>
	</section>
    <div class="single-profile-wrapper">
    	<div class="container">
        	<div class="row">
            	<div class="col-md-8">
                	<div class="tab-content-single-profile">
                    	<!-- Title -->
                    	<div class="row title-tab-profile">
                            <div class="col-md-12">
                                <h2><?php printf(__('ABOUT %s', 'author'), strtoupper($author_name) ); ?></h2>
                            </div>
                        </div>
                        <!-- Title / End -->
                        <!-- Content project -->
                        <div class="single-profile-content">
                        	<div class="single-profile-top">
                                <ul class="single-profile">
                                    <li class="img-avatar"><span class="avatar-profile"><?php echo get_avatar($author_id, 70); ?></span></li>
                                    
                                    <li class="info-profile">
                                        <span class="name-profile"><?php echo $author_name; ?></span>
                                    <?php if($convert) { ?>
                                        <span class="position-profile"><?php echo $convert->et_professional_title; ?></span>
                                    <?php } ?> 

                                        <span class="number-review-profile"><?php if($count_review < 2) printf(__('%d review', 'author'), $count_review ); else printf(__('%d reviews', 'author'), $count_review );?></span>
                                    </li>
                                    
                                </ul>  
                                <?php if($convert && (fre_share_role() || ae_user_role($author_id) == FREELANCER ) ){ ?>
                                <div class="list-skill-profile">
                                    <ul>
                                    <?php if(isset($convert->tax_input['skill']) && $convert->tax_input['skill']){
                                            foreach ($convert->tax_input['skill'] as $tax){ ?>	
                                                <li><span class="skill-name-profile"><?php echo $tax->name; ?></span></li>
                                    <?php 	}
                                          } ?>        	
                                    </ul>
                                </div>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                            <div class="single-profile-bottom">
                                <?php if($convert) { ?>
                                <!-- overview -->
                                <div class="profile-overview">
                                	<h4 class="title-single-profile"><?php _e('Overview', 'author');?></h4>
                                    <p><?php echo $convert->post_content; ?></p>
                                    <?php 
                                    if(function_exists('et_the_field')) {
                                        et_render_custom_meta($convert);
                                        et_render_custom_taxonomy($convert);
                                    }
                                    ?>
                                </div>
                                <!--// overview -->
                                <?php }  
                                
                                if(fre_share_role() || ae_user_role($author_id) != FREELANCER ){
                                    get_template_part('template/work', 'history');
                                } 
                                if( fre_share_role() || ae_user_role($author_id) == FREELANCER ){ 
                                    get_template_part('template/bid', 'history');
                                    $bid_posts   = $wp_query->found_posts;
                                ?>
                                    <div class="portfolio-container">
                                    <?php 
                                        query_posts( array( 
                                                        // 'post_parent' => $convert->ID, 
                                                        'post_status' => 'publish', 
                                                        'post_type' => PORTFOLIO, 
                                                        'author' => $author_id ) 
                                                    );
                                        if(have_posts()):
                                            get_template_part('template/portfolios', 'filter' );   
                                            // list portfolio
                                            get_template_part( 'list', 'portfolios' );     
                                        else :
                                        endif;
                                        //wp_reset_postdata();
                                        wp_reset_query();
                                    ?>
                                    </div>
                                <?php }
                                ?>
                                </div>
                        </div>
                        <!-- Content project / End -->
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <!-- Title -->
                    <div class="row title-tab-profile">
                        <div class="col-md-12">
                            <h2><?php _e('INFO', 'author');?></h2>
                        </div>
                    </div>
                    <?php if( ae_user_role($author_id) == FREELANCER ){

                     ?>
                    	
                        <!-- Title / End -->
                        <!-- Content project -->
                        <div class="single-profile-content">
                        <?php if($author_available == 'on' || $author_available == '' ){ ?>
                            <div class="contact-link">
                                <a href="#" data-toggle="modal" class="invite-freelancer btn-sumary <?php if ( is_user_logged_in() ) { echo 'invite-open';}else{ echo 'login-btn';} ?>"  data-user="<?php echo $convert->post_author ?>">
                                    <?php _e("Invite me to join", 'author') ?>
                                </a> 
                                 <!--  <span><?php /*_e("Or", 'author'); */?></span>-->
                                <?php if ( is_user_logged_in() ){?>
                                <a href="<?php if ( is_user_logged_in() ) {echo '/chat-room?chat_contact='.$convert->post_author;} else{ echo '#" data-toggle="modal';} ?>" class="<?php if ( is_user_logged_in() ) {} else{ echo 'login-btn';} ?> btn-sumary contact-me-chat">
                                    <?php _e("Contact me", 'author') ?>
                                </a>
                                <?php } ?>
                            </div>
                        <?php } else {
                                echo '<h3 style="padding: 20px 25px;margin:0;">'.$author_name .'</h3>';
                            }

                            $rating = Fre_Review::freelancer_rating_score($author_id);
                        ?> 
                            <ul class="list-detail-info">
				<li>
                                    <i class="fa fa-envelope"></i>
                                    <span class="text"><?php _e('Email Verified:','author');?></span>
                                    <span class="text-right verified"><?php if( $author_email_verified ) { echo "<i class='fa fa-check'></i> Verified"; } else { echo "Not Verified"; } ?></span>
                                </li>
								
				<li>
                                    <i class="fa fa-phone"></i>
                                    <span class="text"><?php _e('Phone Verified:','author');?></span>
                                    <span class="text-right verified"><?php if( $author_phone_verified ) { echo "<i class='fa fa-check'></i> Verified"; } else { echo "Not Verified"; } ?></span>
                                </li>

                            	<li>
                                    <i class="fa fa-dollar"></i>
                                    <span class="text"><?php _e('Hourly Rate:','author');?></span>
                                    <span class="text-right"><?php echo $convert->hourly_rate_price;  ?></span>
                                </li>
                                <li>
                                	<i class="fa fa-star"></i>
                                    <span class="text"><?php _e('Rating:','author');?></span>
                                	<div class="rate-it" data-score="<?php echo $rating['rating_score']; ?>"></div>
                                </li>
                                <li>
                                    <i class="fa fa-pagelines"></i>
                                    <span class="text"><?php _e('Experience:','author');?></span>
                                    <span class="text-right"><?php echo $convert->experience; ?></span>
                                </li>
                                <li>
                                    <i class="fa fa-briefcase"></i>
                                    <span class="text"><?php _e('Projects worked:','author');?></span>
                                    <span class="text-right"><?php echo $bid_posts; ?></span>
                                </li>
                                
                                <li>
                                    <i class="fa fa-money"></i>
                                    <span class="text"><?php _e('Total earned:','author');?></span>
                                    <span class="text-right"><?php echo fre_price_format(fre_count_total_user_earned($author_id)); ?></span>
                                </li>
                                
                                <li>
                                    <i class="fa fa-map-marker"></i>
                                    <span class="text"><?php _e('Country:','author');?></span>
                                    <span class="text-right">
                                        <?php 
                                        if($convert->tax_input['country']){ 
                                            echo $convert->tax_input['country']['0']->name;
                                        } ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    <?php }else{ ?>
                        <div class="info-company-wrapper">
                            <div class="row">
                                <div class="col-md-12">
                                    <?php fre_display_user_info( $author_id ); ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- Content project / End -->
                </div>
            </div>
        </div>
    </div>

<?php

get_footer();