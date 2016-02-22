<?php

/**

 * the template for displaying the freelancer work (bid success a project)

 # this template is loaded in template/bid-history-list.php

 * @since 1.0   

 * @package FreelanceEngine

 */

$author_id = get_query_var('author');

if(is_page_template('page-profile.php')) {

    global $user_ID;

    $author_id = $user_ID;

}



global $wp_query, $ae_post_factory, $post, $user_ID;



$post_object = $ae_post_factory->get( BID );

$current     = $post_object->current_post;

$project_author_id = $current->project_author;

#get project auther name
$authorProfile = get_userdata($project_author_id);

$ae_users = AE_Users::get_instance();

$user_data = $ae_users->convert($authorProfile);


if(!$current || !isset( $current->project_title )){

    return;

}



?>

<li class="bid-item">

    <div class="name-history">

       <a href="<?php echo get_author_posts_url( $current->post_author ); ?>"> <span class="avatar-bid-item"><?php echo $current->project_author_avatar;?></span>  </a>

        <div class="content-bid-item-history">


            <?php if($current->project_status == 'complete'){ ?>



                <h5><a href = "<?php echo $current->project_link; ?>"><?php echo $current->project_title; ?></a>

                    <div class="rate-it" data-score="<?php echo $current->rating_score?>"></div> by <a href="<?php echo $user_data->author_url;?>" target="_blank"><?php echo $user_data->user_nicename; ?></a> / Total Projects: <?php echo fre_count_user_posts_by_type($project_author_id, 'project', '"publish","complete","close" ', true);?>

                </h5>



                <?php if(isset($current->project_comment)){ ?>



                <span class="comment-author-history">

                    <?php echo $current->project_comment; ?>

                </span>



                <?php } else { ?>



                <span class="stt-in-process"><?php _e('Job is completed', ET_DOMAIN);?></span> 

                

                <?php } ?>



            <?php } else if($current->project_status == 'close'){ ?>



                <h5>

                    <a href = "<?php echo $current->project_link; ?>"><?php echo $current->project_title; ?></a>

                </h5>

                <span class="stt-in-process"><?php _e('Job is closed', ET_DOMAIN);?></span> 



            <?php } else { ?>



                <h5>

                    <a href = "<?php echo $current->project_link; ?>"><?php echo $current->project_title; ?></a>

                </h5>

                <span class="stt-in-process"><?php _e('Job in process', ET_DOMAIN);?></span> 



            <?php } ?>

        </div>

    </div>

    <ul class="info-history">

        <li><?php echo $current->project_post_date; ?></li>

        <li>

            <?php _e("Bid Budget", ET_DOMAIN); ?> : <span class="number-price-project-info"><?php echo $current->bid_budget_text; ?> </span>

        </li>

        <!-- <li><?php _e('Earned :', ET_DOMAIN) ;  echo $current->et_budget; ?></li> -->

    </ul>

    <div class="clearfix"></div>

</li>