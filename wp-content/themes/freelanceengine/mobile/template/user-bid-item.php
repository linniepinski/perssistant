<?php
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
$status_text    = $project->status_text;
?>


    <div class="info-single-project-wrapper">
        <div class="container">
            <div class="info-project-top">
                <div class="avatar-author-project">
                    <a href="<?php echo get_author_posts_url( $project->post_author ); ?>">
                        <?php echo get_avatar( $project->post_author, 35, true, $bid->project_title ); ?>
                    </a>
                </div>
                <h1 class="title-project">
                    <a href="<?php echo get_permalink($project->ID);?>">
                        <?php echo $bid->project_title; ?>
                    </a>
                </h1>
                <div class="clearfix"></div>
            </div>
            <div class="info-bottom">
                <span class="name-author"> 
                    <?php printf(__('Posted by %s', ET_DOMAIN), get_the_author_meta( 'display_name', $project->post_author )) ?>
                </span>
                <span class="price-project"><?php echo $currency['icon'].$bid->bid_budget ?></span>
            </div>
        </div>
    </div>

    <div class="info-bid-wrapper">
        <ul class="bid-top">
            <li>
            <?php 
                if($total_bids > 1 || $total_bids == 0) {
                    printf(__('<span class="number">%s</span> Bids', ET_DOMAIN), $total_bids);
                }else{
                    _e('<span class="number">1</span> Bid', ET_DOMAIN);
                }
            ?>
            </li>

            <li>
                <span class="number">
                    <?php echo $bid_average; ?>
                </span>
                <?php printf( __('Avg Bid (%s)', ET_DOMAIN), $currency['code'] ) ?>
            </li>

            <li class="clearfix"></li>

            <li class="stt-bid">
                <div class="time">
                    <span class="number">
                        <?php echo $status_text; ?>
                    </span><!-- 1 day, 12 hours left -->
                </div>
                <p class="btn-warpper-bid">
                    <?php if($project->post_status == "publish"){ ?>
                    <a href="<?php echo $project->permalink ?>" class="btn-sumary btn-bid">
                        <?php _e("CANCEL", ET_DOMAIN) ?>
                    </a>
                    <?php } ?>
                </p>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
