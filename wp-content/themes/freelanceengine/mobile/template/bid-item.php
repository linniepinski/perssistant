<?php 
    global $wp_query, $ae_post_factory, $post,$project,$user_ID;
    $post_object    = $ae_post_factory->get( BID );
    $convert        = $post_object->convert($post);
    $bid_accept     = get_post_meta($project->ID, 'accepted', true);
    $project_status = $project->post_status;
    $role           = ae_user_role();
    $time = $convert->bid_time;    
    $type = $convert->type_time;
?>
<li class="info-bidding">
    <div class="info-author-bidders">
        <div class="avatar-proflie">
            <a href="<?php echo get_author_posts_url( $convert->post_author ); ?>"><span class="avatar-profile"> <?php echo $convert->et_avatar; ?></span></a>
        </div>
        <div class="user-proflie">
            <span class="name"><?php echo $convert->profile_display ;?></span>
            <span class="position"><?php echo $convert->et_professional_title ?></span>
        </div>
    </div>
    <ul class="wrapper-achivement">
        <li>          
            <div class="rate-it" data-score="<?php echo $convert->rating_score ; ?>"></div>      
        </li>
        <li><span><?php if(!empty($convert->experience)) echo '+ '.$convert->experience;  ?> </span></li>
    </ul>
    <div class="clearfix"></div>
    <div class="bid-price-wrapper">
        <div class="bid-price">
            <?php if(  in_array($project_status, array('complete','close') ) 
                        || ( $user_ID && $user_ID == $project->post_author ) 
                        || ( $user_ID && $user_ID == $convert->post_author ) 
                    ){ ?>
                <span class="number">
                    <?php echo $convert->bid_budget_text; ?></span> 
                    <?php echo $convert->bid_time_text; ?>
                </span>
            <?php }else { ?>
                <span class="number"><?php _e("In Process", ET_DOMAIN); ?></span>
            <?php } ?>
        </div>
        <p class="btn-warpper-bid col-md-3 number-price-project">
        <?php 
            
        /**
            *project accept button
            *only project owner can see & use this button
        */
        if( $user_ID == $project->post_author && $project_status == 'publish' ){ ?>
            <button href="#" id="<?php the_ID();?>" rel="<?php echo $project->ID;?>" class="btn-sumary btn-bid btn-accept-bid btn-bid-status">
                <?php _e('Accept',ET_DOMAIN) ; ?>
            </button>
            <span class="confirm"></span>
            <?php 

        } else if( $bid_accept && $project->accepted == $convert->ID && in_array($project_status, array('complete','close') ) ) { ?>
            <span class="ribbon"><i class="fa fa-trophy"></i></span>
            <?php
        }
        ?>
        </p>
    </div>
    <div class="clearfix"></div>
</li>