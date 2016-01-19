<?php
/**
 * The template for displaying user bid item in page-profile.php
 */
$currency =  ae_get_option('content_currency',array('align' => 'left', 'code' => 'USD', 'icon' => '$'));
global $wp_query, $ae_post_factory, $post;
//get bid data
$bid_object     = $ae_post_factory->get( BID );
$bid            = $bid_object->current_post;
//get project data
$project        = get_post( $bid->post_parent );

if(!$project || is_wp_error($project) ) { 
    return false; 
}

$project_object = $ae_post_factory->get( PROJECT );
$project        = $project_object->convert($project);
//get all fields
$total_bids     = $project->total_bids ? $project->total_bids : 0;
$bid_average    = $project->bid_average ? $project->bid_average: 0;
$bid_budget     = $bid->bid_budget ? $bid->bid_budget : 0;
$bid_time       = $bid->bid_time ? $bid->bid_time : 0;
$type_time      = $bid->type_time ? $bid->type_time : 0;
$status_text    = $bid->status_text;

?>
<li <?php post_class( 'user-bid-item' ); ?>>

	<div class="row user-bid-item-list ">
        <div class="col-md-6">
            <a href="<?php echo get_author_posts_url( $project->post_author ); ?>" class="avatar-author-project-item">
                <?php echo get_avatar( $project->post_author, 35,true, $bid->project_title ); ?>
            </a>
            <a href="<?php echo get_permalink($project->ID);?>">
                <span class="content-title-project-item">
                    <?php echo $bid->project_title;?>
                </span>
            </a>
        </div>
    <?php if($project->post_status == "publish"){ ?>
        <div class="col-md-6">
        	<a href="<?php echo $project->permalink ?>" class="btn btn-apply-project-item">
                <?php _e("Cancel", ET_DOMAIN) ?>
            </a>
        </div>
        <!-- danng !-->
        <?php }  else if($project->post_status == 'complete' && ae_user_role() == 'freelancer' && $bid->ID == $project->accepted ){ ?>
        <div class="col-md-6">
            <a href="<?php echo add_query_arg('review',1,$project->permalink) ?>" class="btn btn-apply-project-item">
                <?php _e("Review job", ET_DOMAIN) ?>
            </a>
        </div>
        <?php  } else if($project->post_status == 'close' && ae_user_role() == 'freelancer' && $bid->ID == $project->accepted ){ ?>
        <div class="col-md-6">
            <a href="<?php echo add_query_arg(array('workspace' => 1), $project->permalink); ?>" class="btn btn-apply-project-item">
                <?php _e("Open Workspace", ET_DOMAIN) ?>
            </a>
        </div>
        <?php  } ?>
        <!-- danng end !-->
    </div>

    <div class="user-bid-item-info">
        <ul class="info-item">
            <li>
                <?php 
                if($total_bids > 1 || $total_bids == 0 ) {
                    printf(__('<span class="number-blue">%d</span> Bids', ET_DOMAIN), $total_bids);
                }else {
                    printf(__('<span class="number-blue">%d</span> Bid', ET_DOMAIN), $total_bids);
                }
                ?>
            </li>
            <li>
                <span class="number-blue">
                <?php 
                    $avg = 0;
                    if ($project->total_bids > 0) $avg = get_total_cost_bids($project->ID) / $project->total_bids;
                    echo fre_number_format($avg);
                ?>
                </span><?php printf( __('Avg Bid (%s)', ET_DOMAIN), $currency['code'] ) ?>
            </li>
            <li>
                <span class="number-blue">
                    <?php echo $status_text; ?> 
                </span>
            </li>
            <li>
                <span>
                    <?php _e("Bidding:", ET_DOMAIN) ?> 
                </span>
                <span class="number-blue"> 
                    <?php echo $bid->bid_budget_text; ?> 
                </span> <?php echo $bid->bid_time_text; ?>
            </li>
        </ul>
    </div>
</li>