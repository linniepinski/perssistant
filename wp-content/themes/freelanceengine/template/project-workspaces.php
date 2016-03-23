<?php

/**

 * The template for displaying project message list, mesage form in single project

 */

global $wp_query, $post, $ae_post_factory, $user_ID;

$post_object    = $ae_post_factory->get(PROJECT);
$convert = $project = $post_object->current_post;
$bid_accepted           = $convert->accepted; 
$bid_accepted_author    = get_post_field( 'post_author', $bid_accepted);

$date_format = get_option('date_format');

$time_format = get_option('time_format');



$query_args = array('type' => 'message', 'post_id' => $post->ID , 'paginate' => 'load', 'order' => 'DESC', 'orderby' => 'date' );

/**

 * count all reivews

*/

$total_args = $query_args;

$all_cmts   = get_comments( $total_args );



/**

 * get page 1 reviews

*/

$query_args['number'] = 10;//get_option('posts_per_page');

$comments = get_comments( $query_args );



$total_messages = count($all_cmts);

$comment_pages  =   ceil( $total_messages/$query_args['number'] );

$query_args['total'] = $comment_pages;

$query_args['text'] = __("Load older message", 'project-workspaces');



$messagedata = array();

$message_object = Fre_Message::get_instance();



?>

<div class="project-workplace-details workplace-details">

    <div class="row">

        <div class="col-md-8 message-container">

        	<div class="work-place-wrapper">

                <?php if($post->post_status != 'complete') { ?>

            	<form class="form-group-work-place-wrapper form-message">

                	<div class="form-group-work-place file-container"  id="apply_docs_container">

                        <span class="et_ajaxnonce" id="<?php echo wp_create_nonce( 'file_et_uploader' ) ?>"></span>

                    	<a href="#" class="avatar-employer">

                            <?php echo get_avatar($user_ID, '33') ?>

                        </a>

                        <div class="content-chat-wrapper">

                        	<div class="triangle"></div>

                            <a href="#" class="attack attach-file" id="apply_docs_browse_button"><i class="fa fa-paperclip"></i></a>

                            <textarea name="comment_content" class="content-chat"></textarea>

                            <input type="submit" name="submit" value="<?php _e( "Send" , 'project-workspaces' ); ?>" class="submit-chat-content">

                            <input type="hidden" name="comment_post_ID" value="<?php echo $post->ID; ?>" />

                        </div>

                        <ul class="file-attack apply_docs_file_list" id="apply_docs_file_list">

                        </ul>

                    </div>

                </form>

                <?php } ?>

                <ul class="list-chat-work-place">

                    <?php 

                    foreach ($comments as $key => $message) { 

                        $convert = $message_object->convert($message);

                        $messagedata[] = $convert;

                    ?>

                	<li class="message-item" id="comment-<?php echo $message->comment_ID; ?>">

                    	<div class="form-group-work-place">

                            <a href="#" class="avatar-employer">

                                <?php echo $message->avatar; ?>

                            </a>

                            <div class="content-chat-wrapper">

                                <div class="triangle"></div>

                                <div class="content-chat fixed-chat">

                                <?php echo $convert->comment_content; ?>

                                <?php echo $convert->file_list; ?>

                                </div>

                                <div class="date-chat">

                                <?php 

                                    echo $message->message_time;

                                ?>

                                </div>

                            </div>

                        </div>

                    </li>

                    <?php } ?>

                </ul>

                <?php if($comment_pages > 1) { ?>

                <div class="paginations-wrapper" >

                    <?php ae_comments_pagination( $comment_pages, $paged ,$query_args );   ?>

                </div>

                <?php } ?>

                <?php echo '<script type="json/data" class="postdata" > ' . json_encode($messagedata) . '</script>'; ?>

            </div>

        </div>

        <?php if(!et_load_mobile()) { ?>

        <div class="col-md-4 workplace-project-details">

        	<div class="content-require-project">

                <?php 

                if(fre_access_workspace($post)) {

                    echo '<a style="font-weight:600;" href="'.get_permalink( $post->ID ).'">

                            <i class="fa fa-arrow-left"></i> '.__("Back To Project Page", 'project-workspaces').

                        '</a>';

                }

                ?>

                <h4><?php _e('Project description:','project-workspaces');?></h4>

                <?php the_content(); ?>



            </div>

        </div>

        <?php } ?>
       
       <?php /*
       <div class="col-md-4 workplace-project-details">

            <div class="info-company-wrapper">

                <div class="row">

                    <div class="col-md-12">                               
                        <?php
                            if (ae_user_role($user_ID) == FREELANCER) {
                                fre_display_user_info( $post->post_author );
                            } else {                                
                                $user = get_userdata($bid_accepted_author);

                                $ae_users = AE_Users::get_instance();

                                $user_data = $ae_users->convert($user);
                               
                                $author_email_verified = (ae_get_option('user_confirm') && get_user_meta($user_data->ID, 'register_status', true) == "unconfirm") ? false : true;
                                $author_phone_verified = (get_user_meta( $user_data->ID, 'phone', true) != "") ? true : false;                         
                                
                                $rating = Fre_Review::freelancer_rating_score($user_data->ID);
                                
                                $profile_id = get_user_meta($user_data->ID, 'user_profile_id', true); 
                                                            
                                $hourly_rate_price = get_post_meta($profile_id, 'hour_rate', true);
                                $experience = get_post_meta($profile_id, 'et_experience', true);
                                $country = get_user_meta($user_data->ID, 'location', true);
                                $currency = ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));
                                
                                #get country code
                                $arrCountry = get_country_name_by_country_code($country);
                                
                                #get bid detail
                                query_posts( array(  'post_status' => array('complete'), 'post_type' => BID, 'author' => $bid_accepted_author, 'accepted' => 1  )); 
                                $bid_posts   = $wp_query->found_posts;
                                
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
                                           <?php printf(__('Member Since %s', 'project-workspaces') , date(get_option('date_format') , strtotime($user_data->user_registered))); ?>
                                        </span>
                                    </div>
                                </div>
                        
                                <ul class="list-detail-info">
                                   
                                    <li>
                                        <i class="fa fa-envelope"></i>
                                        <span class="text"><?php _e('Email Verified:','project-workspaces');?></span>
                                        <span class="text-right verified"><?php if( $author_email_verified ) { echo "<i class='fa fa-check'></i> Verified"; } else { echo "Not Verified"; } ?></span>
                                    </li>

                                    <li>
                                        <i class="fa fa-phone"></i>
                                        <span class="text"><?php _e('Phone Verified:','project-workspaces');?></span>
                                        <span class="text-right verified"><?php if( $author_phone_verified ) { echo "<i class='fa fa-check'></i> Verified"; } else { echo "Not Verified"; } ?></span>
                                    </li>

                                    <li>
                                        <i class="fa fa-dollar"></i>
                                        <span class="text"><?php _e('Hourly Rate:','project-workspaces');?></span>
                                        <span class="text-right"><?php echo $hourly_rate_price . $currency['icon'] . '/h';  ?></span>
                                    </li>
                                    <li>
                                            <i class="fa fa-star"></i>
                                        <span class="text"><?php _e('Rating:','project-workspaces');?></span>
                                            <div class="rate-it" data-score="<?php echo $rating['rating_score']; ?>"></div>
                                    </li>
                                    <li>
                                        <i class="fa fa-pagelines"></i>
                                        <span class="text"><?php _e('Experience:','project-workspaces');?></span>
                                        <span class="text-right"><?php echo $experience . ' years'; ?></span>
                                    </li>
                                    <li>
                                        <i class="fa fa-briefcase"></i>
                                        <span class="text"><?php _e('Projects worked:','project-workspaces');?></span>
                                        <span class="text-right"><?php echo $bid_posts; ?></span>
                                    </li>

                                    <li>
                                        <i class="fa fa-money"></i>
                                        <span class="text"><?php _e('Total earned:','project-workspaces');?></span>
                                        <span class="text-right"><?php echo fre_price_format(fre_count_total_user_earned($user_data->ID)); ?></span>
                                    </li>

                                    <li>
                                        <i class="fa fa-map-marker"></i>
                                        <span class="text"><?php _e('Country:','project-workspaces');?></span>
                                        <span class="text-right">
                                            <?php 
                                                echo $arrCountry->country_name;
                                            ?>
                                        </span>
                                    </li>
                                </ul>
                        <?php
                            }
                        ?>
                    </div>

                </div>

            </div>

        </div> */?>

    </div>

</div>

