<?php

/**

 * The template for displaying a bid info item, 

 * this template is used to display bid info in a project details, 

 * and called at template/list-bids.php 

 * @since 1.0

 * @author Dakachi

 */

    global $wp_query, $ae_post_factory, $post,$user_ID;



    $project_object = $ae_post_factory->get(PROJECT);;

    $project = $project_object->current_post;



    $post_object    = $ae_post_factory->get( BID );

    $convert        = $post_object->convert($post);



    $bid_accept     = get_post_meta($project->ID, 'accepted', true);

    $project_status = $project->post_status;



    $role           = ae_user_role();

    

?>



<div class="row list-bidding"> 

    <div class="info-bidding fade-out fade-in bid-item bid-<?php the_ID();?> bid-item-<?php echo $project_status;?>">

        <div class="col-md-4 col-xs-4">



        	<div class="avatar-freelancer-bidding"><a href="<?php echo get_author_posts_url( $convert->post_author ); ?>"><span class="avatar-profile"> <?php echo $convert->et_avatar; ?></span></a></div>

            <div class="info-profile-freelancer-bidding">

                <span class="name-profile"><?php echo $convert->profile_display ;?></span><br />

                <span class="position-profile"><?php echo $convert->et_professional_title ?></span>

            </div>

        </div>

        <div class="col-md-3 col-xs-3">

        	<div class="rate-exp-wrapper">

                <div class="rate-it" data-score="<?php echo $convert->rating_score ; ?>"></div>

                <span> <?php if(!empty($convert->experience)) echo '+ '.$convert->experience;  ?> </span>

            </div>

        </div>

        <div class="col-md-2 col-xs-2">

        <?php

            $time = $convert->bid_time;    

            $type = $convert->type_time;

        ?>



    	<span class="number-price-project">

            <?php 

            /**

             * user can view bid details 

             # when a project is complete 

             # when current user is project owner

             # when current user is bid owner

             */

            if( in_array($project_status, array('complete','close', 'disputing') ) 

                || ( $user_ID && $user_ID == $project->post_author ) 

                || ( $user_ID && $user_ID == $convert->post_author ) 

            ) { 

                 if ($post_object->current_post->bid_budget == 0){
                   
                    ?>
                    <span class="number-price">Decide later</span>
                    <?php
                }
                else {
                    if ($project->type_budget == 'hourly_rate'){
                        ?>
                        <span class="number-price"><?php echo $convert->bid_budget_text.'/h'; ?></span>
                    <?php
                    }else{
                        ?>
                        <span class="number-price"><?php echo $convert->bid_budget_text; ?></span>
                    <?php
                    }
                }

                ?>

                <span class="number-day">

                    <?php echo $convert->bid_time_text; ?>

                </span>

            <?php }else{ ?>

                <span class="number-price"><?php _e("In Process", ET_DOMAIN); ?></span> 

            <?php } 

            // end biding budget details

        

            /**

             * project accept button

             # only project owner can see & use this button

             */
            ?>   

        </span>



        </div>
        <div class="col-md-3 col-xs-3">
            <?php
                if( $user_ID == (int) $project->post_author && $project_status == 'publish' ){?>

                <button style="display:block !important" href="#" id="<?php the_ID();?>" rel="<?php echo $project->ID;?>" class="btn btn-apply-project-item">

                    <?php _e('Accept',ET_DOMAIN) ; ?>

                </button>
<?php
                    $invate_id = get_the_author_ID();
                    ?>
                    <button style="display:block !important" class="btn btn-success pull-right btn-invate-on-bid" onclick="invate_freelancer(<?php echo $invate_id.",".$project->post_author.",".$project->ID.",'".$project->author_name."'"?>);">
                        <?php _e('Invite to chat',ET_DOMAIN) ;
                        ?>
                    </button>
                <span class="confirm"></span>
				<?php 



            } else if( $bid_accept && $project->accepted == $convert->ID && in_array($project_status, array('complete','close', 'disputing') ) ) { ?>

                <span class="ribbon"><i class="fa fa-trophy"></i></span>

                <?php

            } 
            ?>
         </div>   
        <?php if($convert->post_content){ ?>

            <div class="col-md-12">

                <blockquote class="comment-author-history">

                    <?php echo $convert->post_content; ?>

                </blockquote>

            </div>

        <?php } ?>



        <div class="clearfix"></div>

    </div>



</div>